<?php
declare(strict_types = 1);

namespace Shanla\Tools;


class ArrayTool
{
    /**
     * 使用点号方式获取多维数组值
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(array $array, string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        return self::traverseArray($array, $segments, $default);
    }

    /**
     * 判断key是否存在（支持点号）
     * @param array $array
     * @param string $key
     * @return bool
     */
    public static function hasKey(array $array, string $key): bool
    {
        $segments = explode('.', $key);
        return self::traverseArray($array, $segments) !== null;
    }

    /**
     * 递归遍历多维数组以获取指定键的值
     *
     * 此方法用于递归遍历多维数组，根据传入的键段数组查找对应的值。
     * 如果在遍历过程中某个键不存在，将返回默认值。
     *
     * @param array $array 要遍历的多维数组
     * @param array $segments 键段数组，由点号分隔的键分割而成
     * @param mixed $default 如果未找到值，返回的默认值
     * @return mixed 找到的值或默认值
     */
    private static function traverseArray(array $array, array $segments, $default = null)
    {
        $current = $array;
        foreach ($segments as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return $default;
            }
            $current = $current[$segment];
        }
        return $current;
    }

    /**
     * 使用点号设置多维数组值
     * @param array $array
     * @param string $key
     * @param mixed $value
     * @return array
     */
    public static function setValue(array &$array, string $key, mixed $value): array
    {
        $current = &$array;
        $segments = explode('.', $key);
        foreach ($segments as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                $current[$segment] = [];
            }
            $current = &$current[$segment];
        }
        $current = $value;
        return $array;
    }

    /**
     * 数组分页
     * @param array $array
     * @param int $pageSize
     * @param int $page
     * @return array
     */
    public static function paginate(array $array, int $pageSize, int $page): array
    {
        if ($pageSize <= 0 || $page <= 0) {
            return ['data' => [], 'total' => 0, 'page_size' => 0, 'current_page' => 0, 'last_page' => 0];
        }
        $total = count($array);
        $offset = ($page - 1) * $pageSize;
        return [
            'data' => array_slice($array, $offset, $pageSize),
            'total' => $total,
            'page_size' => $pageSize,
            'current_page' => $page,
            'last_page' => ceil($total / $pageSize),
        ];
    }

    /**
     * 统计二维数组中指定字段为指定值或不为指定的个数
     * @param array $array
     * @param string $key
     * @param mixed $search
     * @param int $compare 比较符号 1相等 2不相等
     * @return int
     */
    public static function countSearchValues(array $array, string $key, mixed $search, int $compare = 1): int
    {
        $count = 0;
        foreach ($array as $item) {
            if (isset($item[$key])) {
                if ($compare == 1 && $item[$key] == $search) {
                    $count++;
                } elseif ($compare == 2 && $item[$key] != $search) {
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * 将数组转换成树形结构
     * @param array $list
     * @param string $pk
     * @param string $pid
     * @param string $child
     * @param int $root
     * @return array
     */
    public static function arrayToTree(array $list, string $pk = 'id', string $pid = 'p_id', string $child = '_child', int $root = 0): array
    {
        if (empty($list)) {
            return [];
        }
        $refer = [];
        // 修复引用残留问题
        foreach ($list as $k => $v) {
            if (!isset($v[$pk]) || !isset($v[$pid])) {
                continue;
            }
            // 强制初始化子节点数组
            $v[$child] = isset($v[$child]) && is_array($v[$child]) ? $v[$child] : [];
            $refer[$v[$pk]] = &$list[$k]; // 直接使用数组索引引用
        }
        unset($v); // 消除循环变量引用

        $tree = [];
        foreach ($list as $key => $data) {
            if (!isset($data[$pid])) {
                continue;
            }
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } elseif (isset($refer[$parentId])) {
                $parent = &$refer[$parentId];
                $parent[$child][] = &$list[$key]; // 直接引用原始数组元素
            }
        }
        return $tree;
    }

    /**
     * 根据条件筛选数组
     * @param array $array
     * @param array $conditions
     * @return array
     */
    public static function where(array $array, array $conditions) : array
    {
        return array_filter($array, function ($item) use ($conditions) {
            foreach ($conditions as $key => $value) {
                if (self::getValue($item, $key) != $value) {
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * 根据字段分组
     * @param array $array
     * @param string $key
     * @return array
     */
    public static function groupBy(array $array, string $key) : array
    {
        $result = [];
        foreach ($array as $item) {
            $groupKey = self::getValue($item, $key);
            $result[$groupKey][] = $item;
        }
        return $result;
    }

    /**
     * 排除指定键
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function exceptKeys(array $array, array $keys) : array
    {
        return array_diff_key($array, array_flip($keys));
    }

    /**
     * 保留指定键
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function onlyKeys(array $array, array $keys) : array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * 二维数组通过某一个key的值进行排序
     * @param array $list
     * @param string $key
     * @param string $sort
     * @return array
     */
    public static function arraySortByKey(array $list, string $key, string $sort = 'asc') : array
    {
        usort($list, function ($a, $b) use ($key, $sort) {
            if ($sort == 'asc') {
                return $a[$key] <=> $b[$key]; // 使用太空船操作符
            } else {
                return $b[$key] <=> $a[$key];
            }
        });
        return $list;
    }

    /**
     * 给二维数组增加一列
     * @param array $list
     * @param string $key
     * @param $value
     * @return array
     */
    public static function arrayAddColumn(array $list, string $key, $value) : array
    {
        return  array_map(function ($item) use($key, $value) {
            $item[$key] = $value;
            return $item;
        }, $list);
    }

}