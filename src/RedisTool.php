<?php
declare(strict_types = 1);

namespace Shanla\Tools;


use Predis\Client;

class RedisTool
{
    private static Client $client;

    public function __construct()
    {
        if (self::$client === null) {
            self::$client = new Client([
                'scheme' => 'tcp',
                'host' => '127.0.0.1',
                'port' => '6379'
            ]);
        }
    }

    /**
     * 设置键值对
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     */
    public static function set(string $key, mixed $value, int $ttl = 0) : void
    {
        self::$client->set($key, $value);
        if ($ttl > 0) {
            self::$client->expire($key, $ttl);
        }
    }

    /**
     * 根据键获取值
     * @param string $key
     * @return string|null
     */
    public static function get(string $key) : ?string
    {
        return self::$client->get($key);
    }

    /**
     * 删除
     * @param string $key
     * @return int
     */
    public static function del(string $key) : int
    {
        return self::$client->del($key);
    }

    /**
     * 设置过期时间
     * @param string $key
     * @param int $ttl
     * @return int
     */
    public static function expire(string $key, int $ttl) : int
    {
        return self::$client->expire($key, $ttl);
    }

    /**
     * 获取过期时间
     * @param string $key
     * @return int
     */
    public static function ttl(string $key) : int
    {
        return self::$client->ttl($key);
    }

    /**
     * 按步长自增
     * @param string $key
     * @param int $step
     * @return int
     */
    public static function incrBy(string $key, int $step = 1) : int
    {
        return self::$client->incrby($key, $step);
    }

    /**
     * 按步长自减
     * @param string $key
     * @param int $step
     * @return int
     */
    public static function decrBy(string $key, int $step = 1) : int
    {
        return self::$client->decrby($key, $step);
    }

    /**
     * 从左边加入列表
     * @param string $key
     * @param array $values
     * @return int
     */
    public static function lPush(string $key, array $values) : int
    {
        return self::$client->lpush($key, $values);
    }

    /**
     * 从右边加入列表
     * @param string $key
     * @param array $values
     * @return int
     */
    public static function rPush(string $key, array $values) : int
    {
        return self::$client->rpush($key, $values);
    }

    /**
     * 从左边弹出一个元素
     * @param string $key
     * @return string|null
     */
    public static function lPop(string $key) : ?string
    {
        return self::$client->lpop($key);
    }

    /**
     * 从右边弹出一个元素
     * @param string $key
     * @return string|null
     */
    public static function rPop(string $key) : ?string
    {
        return self::$client->rpop($key);
    }

    /**
     * 遍历列表
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array
     */
    public static function lRange(string $key, int $start = 0, int $end = -1) : array
    {
        return self::$client->lrange($key, $start, $end);
    }

    /**
     * 按照索引下标获取列表元素
     * @param string $key
     * @param int $index
     * @return string|null
     */
    public static function lIndex(string $key, int $index) : ?string
    {
        return self::$client->lindex($key, $index);
    }

    /**
     * 从左边开始删除N个等于value的元素
     * @param string $key
     * @param int $count
     * @param string $value
     * @return int
     */
    public static function lRem(string $key, int $count, string $value) : int
    {
        return self::$client->lrem($key, $count, $value);
    }

    /**
     * 截取指定范围的值再赋值给key
     * @param string $key
     * @param int $start
     * @param int $end
     * @return mixed
     */
    public static function lTrim(string $key, int $start = 0, int $end = -1) : mixed
    {
        return self::$client->ltrim($key, $start, $end);
    }

    /**
     * 从源list右边pop一个元素到目标list的左边，如果源list和目标list是同一个，就将list最左边的元素移动到最右边
     * @param string $key1
     * @param string $key2
     * @return string|null
     */
    public static function rPopLPush(string $key1, string $key2) : ?string
    {
        return self::$client->rpoplpush($key1, $key2);
    }
}