<?php
// +----------------------------------------------------------------------
// | 功能介绍
// +----------------------------------------------------------------------
// | @author PanWenHao
// +----------------------------------------------------------------------
// | @copyright PanWenHao Inc.
// +----------------------------------------------------------------------

require __DIR__ . '/../vendor/autoload.php';

use Pangongzi\Phone\PhoneLocation;

$phone = new PhoneLocation();

$start = microtime(true);
var_dump($phone->find('13800138000'));
echo 'Time taken: ' . number_format((microtime(true) - $start) * 1000, 3) . " ms\n";

$start = microtime(true);
var_dump($phone->find('15024335577'));
echo 'Time taken: ' . number_format((microtime(true) - $start) * 1000, 3) . " ms\n";

$start = microtime(true);
var_dump($phone->find('123456'));
echo 'Time taken: ' . number_format((microtime(true) - $start) * 1000, 3) . " ms\n";

$start = microtime(true);
var_dump($phone->find('00000000000'));
echo 'Time taken: ' . number_format((microtime(true) - $start) * 1000, 3) . " ms\n";
