<?php

namespace Shanla\Tools\Tests;

use PHPUnit\Framework\TestCase;
use Shanla\Tools\DateTimeTool;
use DateTime;

class DateTimeToolTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // 设置固定的测试时间为 2024-03-15 10:00:00
        DateTimeTool::setTestNow(new DateTime('2024-03-15 10:00:00'));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // 清除测试时间
        DateTimeTool::setTestNow(new DateTime());
    }

    public function testNow()
    {
        $result = DateTimeTool::now();
        $this->assertEquals('2024-03-15 10:00:00', $result);

        $customFormat = DateTimeTool::now('Y-m-d');
        $this->assertEquals('2024-03-15', $customFormat);
    }

    public function testStartOfDay()
    {
        $date = '2024-03-15 14:30:00';
        $result = DateTimeTool::startOfDay($date);
        $this->assertEquals('2024-03-15 00:00:00', $result);
    }

    public function testEndOfDay()
    {
        $date = '2024-03-15 14:30:00';
        $result = DateTimeTool::endOfDay($date);
        $this->assertEquals('2024-03-15 23:59:59', $result);
    }

    public function testIsWeekend()
    {
        $this->assertTrue(DateTimeTool::isWeekend('2024-03-16')); // 周六
        $this->assertTrue(DateTimeTool::isWeekend('2024-03-17')); // 周日
        $this->assertFalse(DateTimeTool::isWeekend('2024-03-15')); // 周五
    }

    public function testIsWeekDay()
    {
        $this->assertFalse(DateTimeTool::isWeekDay('2024-03-16')); // 周六
        $this->assertFalse(DateTimeTool::isWeekDay('2024-03-17')); // 周日
        $this->assertTrue(DateTimeTool::isWeekDay('2024-03-15')); // 周五
    }

    public function testIsLeapYear()
    {
        $this->assertTrue(DateTimeTool::isLeapYear('2024-03-15')); // 2024是闰年
        $this->assertFalse(DateTimeTool::isLeapYear('2023-03-15')); // 2023不是闰年
    }

    public function testGetQuarter()
    {
        $this->assertEquals(1, DateTimeTool::getQuarter('2024-01-15'));
        $this->assertEquals(1, DateTimeTool::getQuarter('2024-03-15'));
        $this->assertEquals(2, DateTimeTool::getQuarter('2024-04-15'));
        $this->assertEquals(2, DateTimeTool::getQuarter('2024-06-15'));
        $this->assertEquals(3, DateTimeTool::getQuarter('2024-07-15'));
        $this->assertEquals(3, DateTimeTool::getQuarter('2024-09-15'));
        $this->assertEquals(4, DateTimeTool::getQuarter('2024-10-15'));
        $this->assertEquals(4, DateTimeTool::getQuarter('2024-12-15'));
    }

    public function testGetTimeDifference()
    {
        $this->assertEquals('1年2月3天4小时5分6秒', DateTimeTool::getTimeDifference('2023-01-12 10:00:00', '2024-03-15 14:05:06'));
        $this->assertEquals('2小时30分', DateTimeTool::getTimeDifference('2024-03-15 10:00:00', '2024-03-15 12:30:00'));
        $this->assertEquals('', DateTimeTool::getTimeDifference('', '2024-03-15 12:30:00'));
        $this->assertEquals('', DateTimeTool::getTimeDifference('2024-03-15 10:00:00', ''));
    }

    public function testGetTimeAgo()
    {
        $this->assertEquals('1分钟前', DateTimeTool::getTimeAgo('2024-03-15 09:59:00'));
        $this->assertEquals('30分钟前', DateTimeTool::getTimeAgo('2024-03-15 09:30:00'));
        $this->assertEquals('2小时前', DateTimeTool::getTimeAgo('2024-03-15 08:00:00'));
        $this->assertEquals('2天前', DateTimeTool::getTimeAgo('2024-03-13 10:00:00'));
        $this->assertEquals('1分钟前测试', DateTimeTool::getTimeAgo('2024-03-15 09:59:00', '测试'));
    }

    public function testGetNextMonthDate()
    {
        $this->assertEquals('2024-04-15', DateTimeTool::getNextMonthDate(15));
        $this->assertEquals('2024-04-30', DateTimeTool::getNextMonthDate(31));
        $this->assertEquals('2024-04-15 00:00:00', DateTimeTool::getNextMonthDate(15, 'Y-m-d H:i:s'));
    }

    public function testGetNextWeekDate()
    {
        $this->assertEquals('2024-03-22', DateTimeTool::getNextWeekDate(5)); // 下周五
        $this->assertEquals('2024-03-23', DateTimeTool::getNextWeekDate(6)); // 下周六
        $this->assertEquals('2024-03-24', DateTimeTool::getNextWeekDate(7)); // 下周日
        $this->assertEquals('2024-03-22 00:00:00', DateTimeTool::getNextWeekDate(5, 'Y-m-d H:i:s'));
    }
}