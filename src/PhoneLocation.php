<?php
// +----------------------------------------------------------------------
// | 功能介绍 手机号码归属地查询
// +----------------------------------------------------------------------
// | @author PanWenHao
// +----------------------------------------------------------------------
// | @copyright PanWenHao Inc.
// +----------------------------------------------------------------------
// array (
//   'province' => '浙江',
//   'city' => '嘉兴',
//   'zip_code' => '314000',
//   'area_code' => '0573',
//   'type' => 1,
//   'type_str' => '移动',
//   'info' => '浙江|嘉兴|314000|0573',
// )

namespace Pangongzi\Phone;

class PhoneLocation
{
  private const DATA_FILE = __DIR__ . '/data/phone.dat';

  private const TYPE_LISTS = [
    0 => '未知',
    1 => '移动',
    2 => '联通',
    3 => '电信',
    4 => '电信虚拟运营商',
    5 => '联通虚拟运营商',
    6 => '移动虚拟运营商',
    7 => '中国广电',
    8 => '中国广电虚拟运营商',
  ];

  // 文件句柄
  private $_fileHandle = null;

  // 使用 mmap 提高文件读取效率
  // 你目前用 fseek() + fread() 查找数据，
  // 但 mmap() 可以直接映射文件到内存，提高查询速度：
  private $_mmap = null;

  // dat 数据文件的大小
  private  $_fileSize = 0;

  // 归属地信息
  private  $_item = null;

  // 使用 file_get_contents() 替代 mmap()
  // file_get_contents() 会一次性读取整个文件到内存，
  // 可以避免 fseek() 反复访问磁盘，提高查询效率。
  private string $_data;

  // 读取索引起始位置（前 4 个字节）
  private int $_indexBegin;

  // 当前查询的手机号码
  private string $_phone;

  // 单列模式
  private static  $instance = null;



  public function __construct()
  {
    // 判断存在
    if (!file_exists(self::DATA_FILE)) {
      throw new \Exception("Data file not found: " . self::DATA_FILE);
    }

    // 方式1、打开文件 discard
    // $this->_fileHandle = fopen(self::DATA_FILE, 'r');
    // if ($this->_fileHandle === false) {
    //   throw new \Exception("Failed to open data file: " . self::DATA_FILE);
    // }


    // 方式2、直接读取整个文件到内存
    $this->_data = file_get_contents(self::DATA_FILE);

    // 大小
    // $this->_fileSize = filesize(self::DATA_FILE);
    $this->_fileSize = strlen($this->_data);

    // 需要 ext-mmap 手动编译和安装扩展 可以考虑 
    // tag:todo
    // $this->_mmap = mmap($this->_fileHandle, $this->_fileSize);


    // 读取索引起始位置（前 4 个字节）
    $this->_indexBegin = unpack('L', substr($this->_data, 4, 4))[1];
  }




  // 单列 模式
  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }


  /**
   * 查找单个手机号码归属地信息
   * @param string $phone 手机号码
   * @return array|null 归属地信息
   * @throws \Exception 如果文件读取失败或手机号格式不正确
   */
  public function find(string $phone)
  {
    // 检查手机号码长度是否为 11 位  || 检查手机号码是否只包含数字字符。
    // if (empty($phone) || strlen($phone) !== 11 || !ctype_digit($phone)) {
    //   return null;
    // }

    // 检查手机号码长度是否为 11 位  || 检查手机号码是否只包含数字字符。
    if (!preg_match('/^\d{11}$/', $phone)) {
      return null;
    }

    $this->_phone = $phone;
    $this->_item = null;


    // 前7位
    $telPrefix = substr($phone, 0, 7);
    $total = ($this->_fileSize - $this->_indexBegin) / 9;
    $leftPos = 0;
    $rightPos = $total;

    while ($leftPos < $rightPos - 1) {
      $position = $leftPos + intval(($rightPos - $leftPos) / 2);
      $idx = unpack('L', substr($this->_data, $position * 9 + $this->_indexBegin, 4))[1];

      if ($idx < $telPrefix) {
        $leftPos = $position;
      } elseif ($idx > $telPrefix) {
        $rightPos = $position;
      } else {
        $itemIdx = unpack('Lidx_pos/Ctype', substr($this->_data, ($position * 9 + 4) + $this->_indexBegin, 5));
        $itemPos = $itemIdx['idx_pos'];
        $type = $itemIdx['type'];
        $itemStr = explode("\0", substr($this->_data, $itemPos))[0];
        $this->_item = $this->phoneInfo($itemStr, $type);
        break;
      }
    }

    return $this->_item;
  }




  /**
   * 解析归属地信息
   * @param string $itemStr 归属地信息字符串
   * @param int $type 运营商类型
   * @return array 归属地信息数组 
   */
  private function phoneInfo(string $itemStr, int $type)
  {
    // string 转 array
    $itemArr = explode('|', $itemStr);

    // 返回值
    return [
      'province' => $itemArr[0] ?? '',
      'city' => $itemArr[1] ?? '',
      'zip_code' => $itemArr[2] ?? '',
      'area_code' => $itemArr[3] ?? '',
      'type' => $type ?? '',
      'type_str' => self::TYPE_LISTS[$type] ?? '',
      'phone' => $this->_phone,
      'info' => implode('|', [$this->_phone, $type, $itemStr]),
    ];
  }






  /**
   * __destruct 魔术方法，在对象被销毁时执行
   * 
   * 本方法主要用于确保在对象销毁前，关闭可能打开的文件句柄
   * 这是重要的资源管理操作，可以防止资源泄露
   */
  public function __destruct()
  {
    // 换成 file_get_contents 了 
    // 避免 fseek() 和 fread() 频繁调用，直接在内存中查找数据，查询更快。
    // 检查文件句柄是否已初始化，避免对未打开的文件进行操作
    // if ($this->_fileHandle !== null) {
    //   // 关闭文件句柄，释放资源
    //   fclose($this->_fileHandle);
    // }
  }
}
