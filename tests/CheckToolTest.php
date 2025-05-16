<?php
declare(strict_types = 1);

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
        $this->assertEquals(true, CheckTool::checkXML('<?xml version="1.0" encoding="UTF-8"?><root><item>test</item></root>'));
    }

    public function testCheckJSON()
    {
        $this->assertEquals(false, CheckTool::checkJSON('<></>'));
        $this->assertEquals(false, CheckTool::checkJSON('<?xml version="1.0" encoding="" ?>></>'));
        $this->assertEquals(true, CheckTool::checkJSON('{"json":1}'));
    }

    public function testCheckAgeAudltByIdCard()
    {
        $this->assertEquals(false, CheckTool::checkAgeAudltByIdCard('110101199001011234')); // 1990年出生，已成年
        $this->assertEquals(false, CheckTool::checkAgeAudltByIdCard('110101201001011234')); // 2010年出生，未成年
        $this->assertEquals(false, CheckTool::checkAgeAudltByIdCard('invalid')); // 无效身份证号
    }

    public function testIsAlpha()
    {
        $this->assertEquals(true, CheckTool::isAlpha('abc'));
        $this->assertEquals(false, CheckTool::isAlpha('abc123'));
        $this->assertEquals(false, CheckTool::isAlpha('abc 123'));
    }

    public function testIsNumeric()
    {
        $this->assertEquals(true, CheckTool::isNumeric('123'));
        $this->assertEquals(false, CheckTool::isNumeric('123abc'));
        $this->assertEquals(false, CheckTool::isNumeric('123 456'));
    }

    public function testIsAlphanumeric()
    {
        $this->assertEquals(true, CheckTool::isAlphanumeric('abc123'));
        $this->assertEquals(false, CheckTool::isAlphanumeric('abc 123'));
        $this->assertEquals(false, CheckTool::isAlphanumeric('abc-123'));
    }

    public function testIsPostcode()
    {
        $this->assertEquals(true, CheckTool::isPostcode('100000'));
        $this->assertEquals(false, CheckTool::isPostcode('10000'));
        $this->assertEquals(false, CheckTool::isPostcode('1000000'));
    }

    public function testIsQq()
    {
        $this->assertEquals(true, CheckTool::isQq('123456789'));
        $this->assertEquals(false, CheckTool::isQq('1234'));
        $this->assertEquals(false, CheckTool::isQq('123456789012'));
    }

    public function testIsBankCard()
    {
        $this->assertEquals(true, CheckTool::isBankCard('6222021234567890123'));
        $this->assertEquals(false, CheckTool::isBankCard('622202123456789'));
        $this->assertEquals(false, CheckTool::isBankCard('62220212345678901234'));
    }

    public function testIsStrongPassword()
    {
        $this->assertEquals(true, CheckTool::isStrongPassword('Abc123456'));
        $this->assertEquals(false, CheckTool::isStrongPassword('abc123456'));
        $this->assertEquals(false, CheckTool::isStrongPassword('Abc123'));
    }

    public function testIsRealname()
    {
        $this->assertEquals(true, CheckTool::isRealname('张三'));
        $this->assertEquals(false, CheckTool::isRealname('张'));
        $this->assertEquals(false, CheckTool::isRealname('张三李四王五赵六钱七孙八周九吴十'));
    }

    public function testIsAddress()
    {
        $this->assertEquals(true, CheckTool::isAddress('北京市朝阳区建国路88号'));
        $this->assertEquals(false, CheckTool::isAddress('北京'));
        $this->assertEquals(false, CheckTool::isAddress('北京市朝阳区建国路88号北京市朝阳区建国路88号北京市朝阳区建国路88号北京市朝阳区建国路88号北京市朝阳区建国路88号北京市朝阳区建国路88号北京市朝阳区建国路88号北京市朝阳区建国路88号北京市朝阳区建国路88号北京市朝阳区建国路88号北京市朝阳区建国路88号北京市朝阳区建国路88号北京市朝阳区建国路88号北京市朝阳区建国路88号北京市朝阳区建国路88号北京市朝阳区建国路88号'));
    }

    public function testIsCreditCode()
    {
        $this->assertEquals(true, CheckTool::isCreditCode('91110000100000000A'));
        $this->assertEquals(false, CheckTool::isCreditCode('9111000010000000'));
        $this->assertEquals(false, CheckTool::isCreditCode('911100001000000000'));
        $this->assertEquals(false, CheckTool::isCreditCode('91110000100000000B'));
    }

    public function testIsOrgCode()
    {
        $this->assertEquals(true, CheckTool::isOrgCode('12345678-9'));
        $this->assertEquals(true, CheckTool::isOrgCode('123456789'));
        $this->assertEquals(false, CheckTool::isOrgCode('12345678'));
        $this->assertEquals(false, CheckTool::isOrgCode('1234567890'));
    }

    public function testIsTaxCode()
    {
        $this->assertEquals(true, CheckTool::isTaxCode('91110000100000000A'));
        $this->assertEquals(false, CheckTool::isTaxCode('9111000010000000'));
        $this->assertEquals(false, CheckTool::isTaxCode('911100001000000000'));
        $this->assertEquals(false, CheckTool::isTaxCode('91110000100000000B'));
    }

    public function testIsLicense()
    {
        $this->assertEquals(true, CheckTool::isLicense('91110000100000000A'));
        $this->assertEquals(false, CheckTool::isLicense('9111000010000000'));
        $this->assertEquals(false, CheckTool::isLicense('911100001000000000'));
        $this->assertEquals(false, CheckTool::isLicense('91110000100000000B'));
    }

    public function testIsOrgOrCreditCode()
    {
        $this->assertEquals(true, CheckTool::isOrgOrCreditCode('91110000100000000A'));
        $this->assertEquals(true, CheckTool::isOrgOrCreditCode('12345678-9'));
        $this->assertEquals(false, CheckTool::isOrgOrCreditCode('invalid'));
    }

    public function testIsCreditOrTaxCode()
    {
        $this->assertEquals(true, CheckTool::isCreditOrTaxCode('91110000100000000A'));
        $this->assertEquals(false, CheckTool::isCreditOrTaxCode('invalid'));
    }

    public function testIsCreditOrLicense()
    {
        $this->assertEquals(true, CheckTool::isCreditOrLicense('91110000100000000A'));
        $this->assertEquals(false, CheckTool::isCreditOrLicense('invalid'));
    }

    public function testIsBusinessCode()
    {
        $this->assertEquals(true, CheckTool::isBusinessCode('91110000100000000A'));
        $this->assertEquals(true, CheckTool::isBusinessCode('12345678-9'));
        $this->assertEquals(false, CheckTool::isBusinessCode('invalid'));
    }

    public function testIsValidVersion()
    {
        $this->assertEquals(true, CheckTool::isValidVersion('1.0.0'));
        $this->assertEquals(true, CheckTool::isValidVersion('1.0.0-beta'));
        $this->assertEquals(true, CheckTool::isValidVersion('1.0.0-beta.1'));
        $this->assertEquals(false, CheckTool::isValidVersion('1.0.0.'));
        $this->assertEquals(false, CheckTool::isValidVersion('1.0.0-'));
    }

    public function testIsValidIntegerString()
    {
        $this->assertEquals(true, CheckTool::isValidIntegerString('123'));
        $this->assertEquals(true, CheckTool::isValidIntegerString('-123'));
        $this->assertEquals(false, CheckTool::isValidIntegerString('123.45'));
        $this->assertEquals(false, CheckTool::isValidIntegerString('abc'));
    }
}