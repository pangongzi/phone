<?php

namespace Pangongzi\Phone\Tests;

use Pangongzi\Phone\PhoneLocation;
use PHPUnit\Framework\TestCase;


// vendor\bin\phpunit --bootstrap vendor\autoload.php tests\PhoneTest.php
class PhoneTest extends TestCase
{
  private $phone;

  protected function setUp(): void
  {
    $this->phone = PhoneLocation::getInstance();
  }

  public function testFindValidPhoneNumber()
  {
    $result = $this->phone->find('15024335577');
    $this->assertNotEmpty($result);
    $this->assertEquals('浙江', $result['province']);
    $this->assertEquals('嘉兴', $result['city']);
    $this->assertEquals('314000', $result['zip_code']);
    $this->assertEquals('0573', $result['area_code']);
    $this->assertEquals(1, $result['type']);
    $this->assertEquals('移动', $result['type_str']);
  }

  public function testFindInvalidPhoneNumber()
  {
    $result = $this->phone->find('123456');
    $this->assertEmpty($result);
  }

  public function testFindNonExistentPhoneNumber()
  {
    $result = $this->phone->find('00000000000');
    $this->assertEmpty($result);
  }
}
