<?php

namespace Shanla\Tools\Tests;

use PHPUnit\Framework\TestCase;
use Shanla\Tools\StringTool;

class StringToolTest extends TestCase
{
    public function testCalculateSimilarityForChinese()
    {
        $this->assertEquals(1.0, StringTool::calculateSimilarityForChinese('你好', '你好'));
        $this->assertEquals(0.5, StringTool::calculateSimilarityForChinese('你好', '你好世界'));
        $this->assertEquals(0.0, StringTool::calculateSimilarityForChinese('你好', '世界'));
    }

    public function testRemoveAllSpace()
    {
        $this->assertEquals('helloworld', StringTool::removeAllSpace('hello world'));
        $this->assertEquals('helloworld', StringTool::removeAllSpace('hello  world'));
        $this->assertEquals('helloworld', StringTool::removeAllSpace(' hello world '));
    }

    public function testRemoveAllWhitespace()
    {
        $this->assertEquals('helloworld', StringTool::removeAllWhitespace("hello\nworld"));
        $this->assertEquals('helloworld', StringTool::removeAllWhitespace("hello\tworld"));
        $this->assertEquals('helloworld', StringTool::removeAllWhitespace("hello\r\nworld"));
    }

    public function testTruncate()
    {
        $this->assertEquals('hello...', StringTool::truncate('hello world', 5));
        $this->assertEquals('hello world', StringTool::truncate('hello world', 11));
        $this->assertEquals('hello***', StringTool::truncate('hello world', 5, '***'));
    }

    public function testStartWith()
    {
        $this->assertTrue(StringTool::startWith('hello world', 'hello'));
        $this->assertFalse(StringTool::startWith('hello world', 'world'));
        $this->assertTrue(StringTool::startWith('hello world', ''));
    }

    public function testEndWith()
    {
        $this->assertTrue(StringTool::endWith('hello world', 'world'));
        $this->assertFalse(StringTool::endWith('hello world', 'hello'));
        $this->assertTrue(StringTool::endWith('hello world', ''));
    }

    public function testJsonDecode()
    {
        $this->assertEquals(['name' => 'test'], StringTool::jsonDecode('{"name":"test"}'));
        $this->assertEquals([], StringTool::jsonDecode(''));
        $this->assertEquals([], StringTool::jsonDecode('invalid json'));
    }

    public function testHideMobile()
    {
        $this->assertEquals('138****8888', StringTool::hideMobile('13812348888'));
        $this->assertEquals('', StringTool::hideMobile(''));
        $this->assertEquals('123', StringTool::hideMobile('123'));
    }

    public function testRandom()
    {
        // 测试不同长度的随机字符串
        $this->assertEquals(16, strlen(StringTool::random()));
        $this->assertEquals(8, strlen(StringTool::random(8)));

        // 测试不同类型的随机字符串
        $alpha = StringTool::random(10, 'alpha');
        $this->assertMatchesRegularExpression('/^[a-zA-Z]{10}$/', $alpha);

        $numeric = StringTool::random(10, 'numeric');
        $this->assertMatchesRegularExpression('/^[0-9]{10}$/', $numeric);

        $alphanumeric = StringTool::random(10, 'alphanumeric');
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{10}$/', $alphanumeric);

        $all = StringTool::random(10, 'all');
        $this->assertEquals(10, strlen($all));
    }

    public function testToCamelCase()
    {
        $this->assertEquals('helloWorld', StringTool::toCamelCase('hello_world'));
        $this->assertEquals('helloWorld', StringTool::toCamelCase('hello-world', '-'));
        $this->assertEquals('helloWorld', StringTool::toCamelCase('hello world', ' '));
    }

    public function testToSnakeCase()
    {
        $this->assertEquals('hello_world', StringTool::toSnakeCase('helloWorld'));
        $this->assertEquals('hello_world', StringTool::toSnakeCase('HelloWorld'));
        $this->assertEquals('hello_world', StringTool::toSnakeCase('hello_world'));
    }

    public function testToTitleCase()
    {
        $this->assertEquals('Hello World', StringTool::toTitleCase('hello_world'));
        $this->assertEquals('Hello World', StringTool::toTitleCase('hello-world', '-'));
        $this->assertEquals('Hello World', StringTool::toTitleCase('hello world', ' '));
    }

    public function testContains()
    {
        $this->assertTrue(StringTool::contains('hello world', 'world'));
        $this->assertFalse(StringTool::contains('hello world', 'test'));
        $this->assertTrue(StringTool::contains('hello world', ['test', 'world']));
        $this->assertFalse(StringTool::contains('hello world', ['test', 'foo']));
    }

    public function testToLower()
    {
        $this->assertEquals('hello world', StringTool::toLower('HELLO WORLD'));
        $this->assertEquals('hello world', StringTool::toLower('Hello World'));
        $this->assertEquals('hello world', StringTool::toLower('hello world'));
    }

    public function testToUpper()
    {
        $this->assertEquals('HELLO WORLD', StringTool::toUpper('hello world'));
        $this->assertEquals('HELLO WORLD', StringTool::toUpper('Hello World'));
        $this->assertEquals('HELLO WORLD', StringTool::toUpper('HELLO WORLD'));
    }

    public function testToFirstUpper()
    {
        $this->assertEquals('Hello world', StringTool::toFirstUpper('hello world'));
        $this->assertEquals('Hello world', StringTool::toFirstUpper('Hello world'));
        $this->assertEquals('Hello world', StringTool::toFirstUpper('HELLO WORLD'));
    }

    public function testToFirstLower()
    {
        $this->assertEquals('hello world', StringTool::toFirstLower('Hello world'));
        $this->assertEquals('hello world', StringTool::toFirstLower('hello world'));
        $this->assertEquals('hello world', StringTool::toFirstLower('HELLO WORLD'));
    }
} 