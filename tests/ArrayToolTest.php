<?php
declare(strict_types=1);

namespace Shanla\Tools\Tests;

use PHPUnit\Framework\TestCase;
use Shanla\Tools\ArrayTool;

class ArrayToolTest extends TestCase
{
    public function testGetValue()
    {
        $array = ['user' => ['name' => 'John', 'age' => 30]];
        $this->assertEquals('John', ArrayTool::getValue($array, 'user.name'));
        $this->assertEquals('default', ArrayTool::getValue($array, 'user.email', 'default'));
    }

    public function testHasKey()
    {
        $array = ['a' => ['b' => 1]];
        $this->assertTrue(ArrayTool::hasKey($array, 'a.b'));
        $this->assertFalse(ArrayTool::hasKey($array, 'a.c'));
    }

    public function testSetValue()
    {
        $array = [];
        ArrayTool::setValue($array, 'level1.level2', 'value');
        $this->assertEquals(['level1' => ['level2' => 'value']], $array);
    }

    public function testPaginate()
    {
        $data = range(1, 100);
        $result = ArrayTool::paginate($data, 10, 3);
        $this->assertEquals(10, count($result['data']));
        $this->assertEquals(21, $result['data'][0]);
        $this->assertEquals(10, $result['page_size']);
    }

    public function testCountSearchValues()
    {
        $users = [
            ['role' => 'admin'],
            ['role' => 'user'],
            ['role' => 'admin']
        ];
        $this->assertEquals(2, ArrayTool::countSearchValues($users, 'role', 'admin'));
    }

    public function testArrayToTree()
    {
        $data = [
            ['id' => 1, 'p_id' => 0],
            ['id' => 2, 'p_id' => 1],
            ['id' => 3, 'p_id' => 1]
        ];
        $tree = ArrayTool::arrayToTree($data);
        $this->assertCount(1, $tree);
        $this->assertCount(2, $tree[0]['_child']);
    }

    public function testGroupBy()
    {
        $users = [
            ['group' => 'admin', 'name' => 'John'],
            ['group' => 'user', 'name' => 'Alice'],
            ['group' => 'admin', 'name' => 'Bob']
        ];
        $result = ArrayTool::groupBy($users, 'group');
        $this->assertCount(2, $result['admin']);
        $this->assertCount(1, $result['user']);
    }

    public function testExceptKeys()
    {
        $data = ['name' => 'John', 'age' => 30, 'email' => 'john@test.com'];
        $result = ArrayTool::exceptKeys($data, ['age']);
        $this->assertArrayNotHasKey('age', $result);
        $this->assertArrayHasKey('name', $result);
    }

    public function testOnlyKeys()
    {
        $data = ['name' => 'John', 'age' => 30, 'email' => 'john@test.com'];
        $result = ArrayTool::onlyKeys($data, ['name', 'age']);
        $this->assertCount(2, $result);
        $this->assertArrayNotHasKey('email', $result);
    }

    public function testArraySortByKey()
    {
        $data = [
            ['sort' => 3],
            ['sort' => 1],
            ['sort' => 2]
        ];
        $ascResult = ArrayTool::arraySortByKey($data, 'sort');
        $this->assertEquals(1, $ascResult[0]['sort']);
        
        $descResult = ArrayTool::arraySortByKey($data, 'sort', 'desc');
        $this->assertEquals(3, $descResult[0]['sort']);
    }

    public function testArrayAddColumn()
    {
        $data = [['name' => 'John'], ['name' => 'Alice']];
        $result = ArrayTool::arrayAddColumn($data, 'role', 'user');
        $this->assertEquals('user', $result[0]['role']);
        $this->assertEquals('user', $result[1]['role']);
    }

    // 异常测试
    public function testPaginateWithInvalidParams()
    {
        // 测试页数为0的情况
        $result = ArrayTool::paginate([1,2,3], 10, 0);
        $this->assertEmpty($result['data']);
        
        // 测试负数的分页大小
        $result = ArrayTool::paginate([1,2,3], -1, 1);
        $this->assertEquals(0, $result['page_size']);
    }

    // 边界条件测试
    public function testEmptyArrayToTree()
    {
        $result = ArrayTool::arrayToTree([]);
        $this->assertEmpty($result);
    }

    public function testMultiLevelTree()
    {
        $data = [
            ['id' => 1, 'p_id' => 0],
            ['id' => 2, 'p_id' => 1],
            ['id' => 3, 'p_id' => 2]
        ];
        $tree = ArrayTool::arrayToTree($data);
        $this->assertCount(1, $tree[0]['_child'][0]['_child']);
    }

    // 类型安全测试
    public function testStrictTypeValidation()
    {
        $this->expectException(\TypeError::class); // 声明期待抛出类型错误
        ArrayTool::paginate([1,2,3], 10, 1); // 修正为传入有效的整数类型的分页大小
    }
}