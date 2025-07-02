<?php
declare(strict_types = 1);

namespace Shanla\Tools;

use Predis\Client;
use Predis\Response\Status;

class RedisTool
{
    private static ?Client $client = null;

    private static function getClient(): Client
    {
        if (self::$client === null) {
            self::$client = new Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => '6379',
                'password' => '12345678'
            ]);
        }
        return self::$client;
    }

    public static function __callStatic(string $name, array $arguments)
    {
        $client = self::getClient();
        $result = $client->$name(...$arguments);
        
        // 处理 Predis\Response\Status 返回值
        if ($result instanceof Status) {
            return $result->getPayload() === 'OK';
        }
        
        return $result;
    }

    /**
     * 设置键值对
     * @param string $key
     * @param string $value
     * @param int $ttl
     */
    public static function set(string $key, string $value, ?int $ttl = null): bool
    {
        $client = self::getClient();
        if ($ttl !== null) {
            $result = $client->setex($key, $ttl, $value);
        } else {
            $result = $client->set($key, $value);
        }
        return $result instanceof Status ? $result->getPayload() === 'OK' : (bool)$result;
    }

    /**
     * 根据键获取值
     * @param string $key
     * @return string|null
     */
    public static function get(string $key): ?string
    {
        $client = self::getClient();
        $result = $client->get($key);
        return $result === null ? null : (string)$result;
    }

    /**
     * 删除
     * @param string $key
     * @return bool
     */
    public static function del(string $key): bool
    {
        $client = self::getClient();
        return (bool)$client->del($key);
    }

    /**
     * 设置过期时间
     * @param string $key
     * @param int $ttl
     * @return bool
     */
    public static function expire(string $key, int $ttl): bool
    {
        $client = self::getClient();
        return (bool)$client->expire($key, $ttl);
    }

    /**
     * 获取过期时间
     * @param string $key
     * @return int
     */
    public static function ttl(string $key): int
    {
        $client = self::getClient();
        return (int)$client->ttl($key);
    }

    /**
     * 按步长自增
     * @param string $key
     * @param int $increment
     * @return int
     */
    public static function incrBy(string $key, int $increment = 1): int
    {
        $client = self::getClient();
        return (int)$client->incrby($key, $increment);
    }

    /**
     * 按步长自减
     * @param string $key
     * @param int $decrement
     * @return int
     */
    public static function decrBy(string $key, int $decrement = 1): int
    {
        $client = self::getClient();
        return (int)$client->decrby($key, $decrement);
    }

    /**
     * 从左边加入列表
     * @param string $key
     * @param string $value
     * @return int
     */
    public static function lPush(string $key, string|array $value): int
    {
        $client = self::getClient();
        return (int)$client->lpush($key, $value);
    }

    /**
     * 从右边加入列表
     * @param string $key
     * @param string $value
     * @return int
     */
    public static function rPush(string $key, string|array $value): int
    {
        $client = self::getClient();
        return (int)$client->rpush($key, $value);
    }

    /**
     * 从左边弹出一个元素
     * @param string $key
     * @return string|null
     */
    public static function lPop(string $key): ?string
    {
        $client = self::getClient();
        $result = $client->lpop($key);
        return $result === null ? null : (string)$result;
    }

    /**
     * 从右边弹出一个元素
     * @param string $key
     * @return string|null
     */
    public static function rPop(string $key): ?string
    {
        $client = self::getClient();
        $result = $client->rpop($key);
        return $result === null ? null : (string)$result;
    }

    /**
     * 遍历列表
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array
     */
    public static function lRange(string $key, int $start = 0, int $end = -1): array
    {
        $client = self::getClient();
        return array_map('strval', $client->lrange($key, $start, $end));
    }

    /**
     * 获取列表长度
     * @param string $key
     * @return int
     */
    public static function lLen(string $key): int
    {
        $client = self::getClient();
        return (int)$client->llen($key);
    }

    /**
     * 截取指定范围的值再赋值给key
     * @param string $key
     * @param int $start
     * @param int $end
     * @return bool
     */
    public static function lTrim(string $key, int $start, int $end): bool
    {
        $client = self::getClient();
        return (bool)$client->ltrim($key, $start, $end);
    }

    /**
     * 设置下标为index的值为value
     * @param string $key
     * @param int $index
     * @param string $value
     * @return bool
     */
    public static function lSet(string $key, int $index, string $value): bool
    {
        $client = self::getClient();
        return (bool)$client->lset($key, $index, $value);
    }

    /**
     * 获取列表中下标为index的值
     * @param string $key
     * @param int $index
     * @return string|null
     */
    public static function lIndex(string $key, int $index): ?string
    {
        $client = self::getClient();
        $result = $client->lindex($key, $index);
        return $result === null ? null : (string)$result;
    }

    /**
     * 删除列表中count个值为value的元素
     * @param string $key
     * @param int $count
     * @param string $value
     * @return int
     */
    public static function lRem(string $key, int $count, string $value): int
    {
        $client = self::getClient();
        return (int)$client->lrem($key, $count, $value);
    }

    /**
     * 从右边弹出一个元素并将其加入到另一个列表的左边
     * @param string $key1
     * @param string $key2
     * @return string|null
     */
    public static function rPopLPush(string $key1, string $key2): ?string
    {
        $client = self::getClient();
        $result = $client->rpoplpush($key1, $key2);
        return $result === null ? null : (string)$result;
    }

    /**
     * 在列表中插入一个元素
     * @param string $key
     * @param string $value
     * @param string $newValue
     * @param string $where
     * @return int
     */
    public static function lInsert(string $key, string $value, string $newValue, string $where = 'before'): int
    {
        $client = self::getClient();
        return (int)$client->linsert($key, $where, $value, $newValue);
    }

    /**
     * 设置hash的单个字段值
     * @param string $key
     * @param string $field
     * @param string $value
     * @return bool
     */
    public static function hSet(string $key, string $field, string $value): bool
    {
        $client = self::getClient();
        return (bool)$client->hset($key, $field, $value);
    }

    /**
     * 获取hash单个字段值
     * @param string $key
     * @param string $field
     * @return string|null
     */
    public static function hGet(string $key, string $field): ?string
    {
        $client = self::getClient();
        $result = $client->hget($key, $field);
        return $result === null ? null : (string)$result;
    }

    /**
     * 删除hash的多个或一个字段
     * @param string $key
     * @param string $field
     * @return bool
     */
    public static function hDel(string $key, string|array $field): bool
    {
        $client = self::getClient();
        return (bool)$client->hdel($key, $field);
    }

    /**
     * 获取hash元素个数
     * @param string $key
     * @return int
     */
    public static function hLen(string $key): int
    {
        $client = self::getClient();
        return (int)$client->hlen($key);
    }

    /**
     * 获取hash的所有字段
     * @param string $key
     * @return array
     */
    public static function hKeys(string $key): array
    {
        $client = self::getClient();
        return array_map('strval', $client->hkeys($key));
    }

    /**
     * 获取hash的所有字段值
     * @param string $key
     * @return array
     */
    public static function hVals(string $key): array
    {
        $client = self::getClient();
        return array_map('strval', $client->hvals($key));
    }

    /**
     * 获取hash的所有字段
     * @param string $key
     * @return array
     */
    public static function hGetAll(string $key): array
    {
        $client = self::getClient();
        $result = $client->hgetall($key);
        return array_map('strval', $result);
    }

    /**
     * 设置hash多个字段值
     * @param string $key
     * @param array $hash
     * @return bool
     */
    public static function hMSet(string $key, array $hash): bool
    {
        $client = self::getClient();
        return (bool)$client->hmset($key, $hash);
    }

    /**
     * 获取hash多个字段值
     * @param string $key
     * @param array $fields
     * @return array
     */
    public static function hMGet(string $key, array $fields): array
    {
        $client = self::getClient();
        $result = $client->hmget($key, $fields);
        return array_map(function($value) {
            return $value === null ? null : (string)$value;
        }, $result);
    }

    /**
     * 判断hash字段是否存在
     * @param string $key
     * @param string $field
     * @return bool
     */
    public static function hExists(string $key, string $field): bool
    {
        $client = self::getClient();
        return (bool)$client->hexists($key, $field);
    }

    /**
     * 获取hash字段值并增加
     * @param string $key
     * @param string $field
     * @param int $increment
     * @return int
     */
    public static function hIncrBy(string $key, string $field, int $increment): int
    {
        $client = self::getClient();
        return (int)$client->hincrby($key, $field, $increment);
    }

    /**
     * 获取hash字段值并增加
     * @param string $key
     * @param string $field
     * @param float $increment
     * @return string
     */
    public static function hIncrByFloat(string $key, string $field, float $increment): string
    {
        $client = self::getClient();
        return (string)$client->hincrbyfloat($key, $field, $increment);
    }

    /**
     * 设置hash字段值（仅当字段不存在时）
     * @param string $key
     * @param string $field
     * @param string $value
     * @return bool
     */
    public static function hSetNx(string $key, string $field, string $value): bool
    {
        $client = self::getClient();
        return (bool)$client->hsetnx($key, $field, $value);
    }
}