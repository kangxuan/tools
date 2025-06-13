<?php

namespace Shanla\Tools\Tests;

use PHPUnit\Framework\TestCase;
use Shanla\Tools\RedisTool;

class RedisToolTest extends TestCase
{
    private string $testKey;

    protected function setUp(): void
    {
        $this->testKey = 'test_key_' . uniqid();
    }

    protected function tearDown(): void
    {
        // 清理测试键
        RedisTool::del($this->testKey);
    }

    public function testSetAndGet()
    {
        $value = 'test_value';
        RedisTool::set($this->testKey, $value);
        $this->assertEquals($value, RedisTool::get($this->testKey));
    }

    public function testDel()
    {
        RedisTool::set($this->testKey, 'test_value');
        $this->assertEquals(1, RedisTool::del($this->testKey));
        $this->assertNull(RedisTool::get($this->testKey));
    }

    public function testExpireAndTtl()
    {
        RedisTool::set($this->testKey, 'test_value');
        RedisTool::expire($this->testKey, 10);
        $this->assertLessThanOrEqual(10, RedisTool::ttl($this->testKey));
    }

    public function testIncrByAndDecrBy()
    {
        RedisTool::set($this->testKey, '0');
        $this->assertEquals(1, RedisTool::incrBy($this->testKey));
        $this->assertEquals(0, RedisTool::decrBy($this->testKey));
    }

    public function testLPushAndRPush()
    {
        $values = ['value1', 'value2'];
        RedisTool::lPush($this->testKey, $values);
        $this->assertEquals(['value2', 'value1'], RedisTool::lRange($this->testKey));
        RedisTool::rPush($this->testKey, $values);
        $this->assertEquals(['value2', 'value1', 'value1', 'value2'], RedisTool::lRange($this->testKey));
    }

    public function testLPopAndRPop()
    {
        RedisTool::lPush($this->testKey, ['value1', 'value2']);
        $this->assertEquals('value2', RedisTool::lPop($this->testKey));
        $this->assertEquals('value1', RedisTool::rPop($this->testKey));
    }

    public function testLRange()
    {
        RedisTool::lPush($this->testKey, ['value1', 'value2', 'value3']);
        $this->assertEquals(['value3', 'value2', 'value1'], RedisTool::lRange($this->testKey));
    }

    public function testLIndex()
    {
        RedisTool::lPush($this->testKey, ['value1', 'value2']);
        $this->assertEquals('value2', RedisTool::lIndex($this->testKey, 0));
    }

    public function testLRem()
    {
        RedisTool::lPush($this->testKey, ['value1', 'value2', 'value1']);
        $this->assertEquals(2, RedisTool::lRem($this->testKey, 2, 'value1'));
    }

    public function testLTrim()
    {
        RedisTool::lPush($this->testKey, ['value1', 'value2', 'value3']);
        RedisTool::lTrim($this->testKey, 0, 1);
        $this->assertEquals(['value3', 'value2'], RedisTool::lRange($this->testKey));
    }

    public function testRPopLPush()
    {
        RedisTool::lPush($this->testKey, ['value1', 'value2']);
        $this->assertEquals('value1', RedisTool::rPopLPush($this->testKey, $this->testKey));
    }

    public function testLSet()
    {
        RedisTool::lPush($this->testKey, ['value1', 'value2']);
        RedisTool::lSet($this->testKey, 0, 'new_value');
        $this->assertEquals('new_value', RedisTool::lIndex($this->testKey, 0));
    }

    public function testLInsert()
    {
        RedisTool::lPush($this->testKey, ['value1', 'value2']);
        RedisTool::lInsert($this->testKey, 'value1', 'new_value', 'before');
        $this->assertEquals(['value2', 'new_value', 'value1'], RedisTool::lRange($this->testKey));
    }

    public function testHSetAndHGet()
    {
        RedisTool::hSet($this->testKey, 'field1', 'value1');
        $this->assertEquals('value1', RedisTool::hGet($this->testKey, 'field1'));
    }

    public function testHMSetAndHMGet()
    {
        $map = ['field1' => 'value1', 'field2' => 'value2'];
        RedisTool::hMSet($this->testKey, $map);
        $this->assertEquals(['value1', 'value2'], RedisTool::hMGet($this->testKey, ['field1', 'field2']));
    }

    public function testHGetAll()
    {
        $map = ['field1' => 'value1', 'field2' => 'value2'];
        RedisTool::hMSet($this->testKey, $map);
        $this->assertEquals($map, RedisTool::hGetAll($this->testKey));
    }

    public function testHDel()
    {
        RedisTool::hSet($this->testKey, 'field1', 'value1');
        $this->assertEquals(1, RedisTool::hDel($this->testKey, ['field1']));
    }

    public function testHLen()
    {
        RedisTool::hSet($this->testKey, 'field1', 'value1');
        $this->assertEquals(1, RedisTool::hLen($this->testKey));
    }

    public function testHKeys()
    {
        RedisTool::hSet($this->testKey, 'field1', 'value1');
        $this->assertEquals(['field1'], RedisTool::hKeys($this->testKey));
    }

    public function testHValues()
    {
        RedisTool::hSet($this->testKey, 'field1', 'value1');
        $this->assertEquals(['value1'], RedisTool::hVals($this->testKey));
    }

    public function testHIncrBy()
    {
        RedisTool::hSet($this->testKey, 'field1', '0');
        $this->assertEquals(1, RedisTool::hIncrBy($this->testKey, 'field1', 1));
    }

    public function testHIncrByFloat()
    {
        RedisTool::hSet($this->testKey, 'field1', '0');
        $this->assertEquals('1.5', RedisTool::hIncrByFloat($this->testKey, 'field1', 1.5));
    }

    public function testHSetNx()
    {
        $this->assertEquals(1, RedisTool::hSetNx($this->testKey, 'field1', 'value1'));
        $this->assertEquals(0, RedisTool::hSetNx($this->testKey, 'field1', 'value2'));
    }
} 