<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm
 *
 * User: kx
 * Date: 2024/11/20
 * Time: 16:23
 */

namespace Shanla\Tools\Tests;


use PHPUnit\Framework\TestCase;
use Shanla\Tools\CheckTool;

class CheckToolTest extends TestCase
{
    public function testCheckMobile()
    {
        $result = CheckTool::checkMobile('127sdfadfk');
        $this->assertEquals(false, $result);
        $result = CheckTool::checkMobile('13121341212');
        $this->assertEquals(true, $result);
    }

    public function testCheckEmail()
    {
        $result = CheckTool::checkEmail('127sdfadfk');
        $this->assertEquals(false, $result);
        $result = CheckTool::checkEmail('895560759@qq.com');
        $this->assertEquals(true, $result);
    }

    public function testCheckPostalCode()
    {
        $this->assertEquals(false, CheckTool::checkPostalCode('A1356'));
        $this->assertEquals(true, CheckTool::checkPostalCode('123456'));
    }

    public function testCheckIpAddress()
    {
        $this->assertEquals(false, CheckTool::checkIpAddress('a1.15.12.1'));
        $this->assertEquals(true, CheckTool::checkIpAddress('192.168.1.12'));
    }

    public function testCheckDate()
    {
        $this->assertEquals(false, CheckTool::checkDate('2024-02-31'));
        $this->assertEquals(true, CheckTool::checkDate('2024-02-28'));
    }

    public function testCheckChineseIdCardNo()
    {
        $this->assertEquals(false, CheckTool::checkChineseIdCardNo('AAA-GG-SSSS'));
    }

    public function testCheckContainsChinese()
    {
        $this->assertEquals(false, CheckTool::checkContainsChinese('abcdefg'));
        $this->assertEquals(true, CheckTool::checkContainsChinese('1中国'));
    }

    public function testCheckUrl()
    {
        $this->assertEquals(false, CheckTool::checkUrl('://x//xx'));
        $this->assertEquals(true, CheckTool::checkUrl('http://baidu.com'));
    }
}