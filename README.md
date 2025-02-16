# 手机号码归属地信息库、手机号归属地查询


## 项目描述
dat 文件信息，由于新号码段出现，原作者的数据没有覆盖到，自己着手手动更新了数据。  
为了配套使用，写了一个php composer 包(已发布)，方便使用。
- 归属地信息库文件大小：4557kb
- 归属地信息库最后更新：2025年02月
- 手机号段记录条数：517258

如果你只要dat文件，在目录 [src/data/phone.dat](https://github.com/pangongzi/phone/raw/refs/heads/master/src/data/phone.dat)

## 安装

使用 Composer 安装：

```bash
# 安装
composer require pangongzi/phone

# 更新
composer update pangongzi/phone

# 删除
composer remove pangongzi/phone

```
使用方法
初始化
首先，需要引入自动加载文件并实例化 PhoneLocation 类：
```
<?php
require 'vendor/autoload.php';

use Pangongzi\Phone\PhoneLocation;

$phoneLocation = PhoneLocation::getInstance();

or

$dat = 'xxxx/phone.dat'; // 自定义dat文件
$phoneLocation = PhoneLocation::getInstance($dat);

```
查询手机号码归属地
使用 find 方法查询手机号码的归属地信息：
```
<?php
$phone = '13800138000';
$result = $phoneLocation->find($phone);

if ($result !== null) {
    echo "省份: " . $result['province'] . PHP_EOL;
    echo "城市: " . $result['city'] . PHP_EOL;
    echo "邮政编码: " . $result['zip_code'] . PHP_EOL;
    echo "区号: " . $result['area_code'] . PHP_EOL;
    echo "运营商: " . $result['type_str'] . PHP_EOL;
} else {
    echo "未找到归属地信息" . PHP_EOL;
}
```
返回结果示例(完整)
```
<?php
Array
(
    [province] => 浙江
    [city] => 嘉兴
    [zip_code] => 314000
    [area_code] => 0573
    [type] => 1
    [type_str] => 移动
    [phone] => 13800138000
    [info] => 13800138000|1|浙江|嘉兴|314000|0573
)
```




## 出处和说明
基于github开源库  
作者: [https://github.com/ls0f/phone](https://github.com/ls0f/phone)  
记录条数：499527 (updated:2023年12月)

### 文件格式
#### 结构 原作者保持一致

```
| 4 bytes |                     <- phone.dat 版本号（如：1701即17年1月份）
------------
| 4 bytes |                     <-  第一个索引的偏移
-----------------------
|  offset - 8            |      <-  记录区
-----------------------
|  index                 |      <-  索引区
-----------------------
```
1. 头部为8个字节，版本号为4个字节，第一个索引的偏移为4个字节；
2. 记录区 中每条记录的格式为"<省份>|<城市>|<邮编>|<长途区号>\0"。 每条记录以\0结束；
3. 索引区 中每条记录的格式为"<手机号前七位><记录区的偏移><卡类型>"，每个索引的长度为9个字节；


### 类型 运营商
#### 字段 原作者保持一致
* 1: 移动,
* 2: 联通,
* 3: 电信,
* 4: 电信虚拟运营商,
* 5: 联通虚拟运营商,
* 6: 移动虚拟运营商,
* 7: 中国广电,
* 8: 中国广电虚拟运营商


### 安全保证
- 1.由于2019年11月携号转网已开始实行，手机号的运营商可能与实际不符，请谨慎将运营商信息用于重要的业务上。
- 2.手机号归属地信息是通过网上公开数据进行收集整理。
- 3.对手机号归属地信息数据的不绝对保证正确。因此在生产环境使用前请您自行校对测试。

### 语言实现

python:  
https://github.com/ls0f/phone

php:  
https://github.com/pangongzi/phone
https://github.com/shitoudev/phone-location  
https://github.com/iwantofun/php_phone

php ext:     
https://github.com/jonnywang/phone

Java:   
https://github.com/fengjiajie/phone-number-geo  
https://github.com/EeeMt/phone-number-geo

Node:   
https://github.com/conzi/phone

C++:   
https://github.com/yanxijian/phonedata

C#:  
https://github.com/sndnvaps/Phonedata

Rust:  
https://github.com/vincascm/phonedata

Kotlin:  
https://github.com/bytebeats/phone-geo

Ruby:  
https://github.com/forwaard/phonedata

Go:   
https://github.com/xluohome/phonedata

欢迎补充





### 交流
加作者微信  
[![wechat.jpg](https://i.postimg.cc/hvvW2WWw/wechat.jpg)](https://postimg.cc/S2BvKPK7)