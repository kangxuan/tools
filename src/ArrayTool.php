<?php
declare(strict_types = 1);

namespace Shanla\Tools;


class ArrayTool
{
    /**
     * 使用点号方式获取多维数组值
     * @param array $array
     * @param string $key
     * @param $default
     * @return mixed
     */
    public static function getValue(array $array, string $key, $default = null) : mixed
    {
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }
        return $array;
    }

    /**
     * 使用点号设置多维数组值
     * @param array $array
     * @param string $key
     * @param $value
     * @return array
     */
    public static function setValue(array &$array, string $key, $value) : array
    {
        $current = &$array;
        foreach (explode('.', $key) as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                $current[$segment] = [];
            }
            $current = &$current[$segment];
        }
        $current = $value;
        return $array;
    }

    /**
     * 判断key是否存在（支持点号）
     * @param array $array
     * @param string $key
     * @return bool
     */
    public static function hasKey(array $array, string $key): bool
    {
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return false;
            }
            $array = $array[$segment];
        }
        return true;
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
     * 数组分页
     * @param array $array
     * @param int $pageSize
     * @param int $page
     * @return array
     */
    public static function paginate(array $array, int $pageSize, int $page) : array
    {
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
     * 统计二维数组中指定字段为指定值或不为指定的个数
     * @param array $array
     * @param string $key
     * @param mixed $search
     * @param int $compare 比较符号 1相等 2不相等
     * @return int
     */
    public static function countSearchValues(array $array, string $key, mixed $search, int $compare = 1): int
    {
        // 使用 array_column 提取指定字段，结合 array_filter 过滤掉空字符
        $filteredValues = array_filter(array_column($array, $key), function($value) use ($search, $compare) {
            if ($compare == 1) {
                return $value == $search;
            } else {
                return $value != $search;
            }
        });

        // 返回个数
        return count($filteredValues);
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
    public static function arrayToTree(array $list, string $pk = 'id', string $pid = 'p_id', string $child = '_child', int $root = 0) : array
    {
        // 创建Tree
        $tree = [];
        if (!empty($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $data) {
                $refer[$data[$pk]] = &$data;
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] = &$data;
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }

    /**
     * 将二维数组转换成某一个key为下标的新数组
     * @param array $list
     * @param string $key
     * @return array
     */
    public static function arrayIndexByKey(array $list, string $key) : array
    {
        return array_reduce($list, function ($carry, $item) use ($key) {
            $index = $item[$key];
            if (!isset($carry[$index])) {
                $carry[$index] = [];
            }
            $carry[$index][] = $item;
            return $carry;
        }, []);
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