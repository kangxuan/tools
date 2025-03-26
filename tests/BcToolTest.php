<?php
declare(strict_types=1);

namespace Shanla\Tools\Tests;

use PHPUnit\Framework\TestCase;
use Shanla\Tools\BcTool;
use InvalidArgumentException;

class BcToolTest extends TestCase
{
    public function testAdd()
    {
        $this->assertEquals('3.00', BcTool::add(1, 2));
        $this->assertEquals('3.14', BcTool::add('1.1', '2.04', 2));
        $this->assertEquals('3.142', BcTool::add(1.123, 2.019, 3));
    }

    public function testSub()
    {
        $this->assertEquals('-1.00', BcTool::sub(2, 3));
        $this->assertEquals('0.90', BcTool::sub('1.00', '0.1'));
        $this->assertEquals('0.999', BcTool::sub('1.000', '0.001', 3));
    }

    public function testMul()
    {
        $this->assertEquals('0.02', BcTool::mul(0.1, 0.2));
        $this->assertEquals('0.0006', BcTool::mul('0.02', '0.03', 4));
        $this->assertEquals('2000000000.00', BcTool::mul(100000, 20000));
        $this->assertEquals('24691357802469135780.24', BcTool::mul('12345678901234567890.12345', 2));
    }

    public function testDiv()
    {
        $this->assertEquals('2.00', BcTool::div(6, 3));
        $this->assertEquals('0.33', BcTool::div(1, 3));
        $this->assertEquals('0.3333', BcTool::div(1, 3, 4));
        
        $this->expectException(InvalidArgumentException::class);
        BcTool::div(5, 0);
    }

    public function testCompare()
    {
        $this->assertEquals(1, BcTool::compare('5', '3'));
        $this->assertEquals(-1, BcTool::compare('2.999', '3.0'));
        $this->assertEquals(0, BcTool::compare('100.00', '100'));
    }

    public function testModulus()
    {
        $this->assertEquals('1', BcTool::modulus(5, 2));
        $this->assertEquals('3', BcTool::modulus('23', '10'));
        
        $this->expectException(InvalidArgumentException::class);
        BcTool::modulus(10, 0);
    }

    public function testRound()
    {
        $this->assertEquals('3.14', BcTool::round('3.1415926'));
        $this->assertEquals('3.1416', BcTool::round('3.1415926', 4));
        $this->assertEquals('3.00', BcTool::round(2.999));
        $this->assertEquals('2.35', BcTool::round('2.3456', 2)); // 新增边界测试
    }

    public function testLargeNumberHandling()
    {
        $bigNum = '12345678901234567890.12345';
        $this->assertEquals('24691357802469135780.24', BcTool::mul($bigNum, 2));
        $this->assertEquals('24691357802469135780.246', BcTool::mul($bigNum, 2, 3)); // 新增精度测试
    }

    public function testChainOperations()
    {
        $operations = [
            ['operation' => 'add', 'value' => 5],
            ['operation' => 'mul', 'value' => 2],
            ['operation' => 'sub', 'value' => 3]
        ];
        $result = BcTool::chainOperations(10, $operations);
        $this->assertEquals('27.00', $result); // 原 17.00 改为 27.00

        $invalidOps = [
            ['operation' => 'power', 'value' => 2] // 无效操作
        ];
        $this->expectException(InvalidArgumentException::class);
        BcTool::chainOperations(5, $invalidOps);
    }

    public function testNegativeNumbers()
    {
        $this->assertEquals('-5.00', BcTool::sub(5, 10));
        $this->assertEquals('25.00', BcTool::mul(-5, -5));
        $this->assertEquals('-2.00', BcTool::div(-10, 5));
    }
}