<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm
 *
 * User: kx
 * Date: 2024/11/26
 * Time: 18:51
 */

namespace Shanla\Tools;


class ArrayTool
{
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

}