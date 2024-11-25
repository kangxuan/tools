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

    public function testCheckAgeAdult()
    {
        $this->assertEquals(false, CheckTool::checkAgeAdult('2024-01-12'));
        $this->assertEquals(true, CheckTool::checkAgeAdult('1991-01-12'));
    }

    public function testCheckWeChatId()
    {
        $this->assertEquals(false, CheckTool::checkWeChatId('121'));
        $this->assertEquals(true, CheckTool::checkWeChatId('shanla_1214'));
    }

    public function testCheckQQ()
    {
        $this->assertEquals(false, CheckTool::checkQQ('shanla114254323'));
        $this->assertEquals(true, CheckTool::checkQQ('114254323'));
    }

    public function testCheckPasswordStrong()
    {
        $this->assertEquals(false, CheckTool::checkPasswordStrong('123456'));
        $this->assertEquals(true, CheckTool::checkPasswordStrong('Shanla982809285*'));
    }

    public function testCheckHexColor()
    {
        $this->assertEquals(false, CheckTool::checkHexColor('#sdf'));
        $this->assertEquals(true, CheckTool::checkHexColor('#000000'));
    }

    public function testCheckPositiveInteger()
    {
        $this->assertEquals(false, CheckTool::checkPositiveInteger('-1'));
        $this->assertEquals(false, CheckTool::checkPositiveInteger('0'));
        $this->assertEquals(true, CheckTool::checkPositiveInteger(1));
    }

    public function testCheckNegativeInteger()
    {
        $this->assertEquals(false, CheckTool::checkNegativeInteger('121.1'));
        $this->assertEquals(false, CheckTool::checkNegativeInteger('0'));
        $this->assertEquals(true, CheckTool::checkNegativeInteger('-1'));
    }

    public function testCheckXML()
    {
        $this->assertEquals(false, CheckTool::checkXML('<></>'));
        $this->assertEquals(false, CheckTool::checkXML('<?xml version="1.0" encoding="" ?>></>'));
        $this->assertEquals(true, CheckTool::checkXML('<?xml version="1.0" encoding="UTF-8"?>
<note>
    <to>Tove</to>
    <from>Jani</from>
    <heading>Reminder</heading>
    <body>Don\'t forget me this weekend!</body>
</note>'));
    }

    public function testCheckJSON()
    {
        $this->assertEquals(false, CheckTool::checkJSON('<></>'));
        $this->assertEquals(false, CheckTool::checkJSON('<?xml version="1.0" encoding="" ?>></>'));
        $this->assertEquals(true, CheckTool::checkJSON('{"json":1}'));
    }
}