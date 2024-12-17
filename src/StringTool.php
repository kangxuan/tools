<?php
declare(strict_types = 1);

namespace Shanla\Tools;


class StringTool
{
    /**
     * 比较两个字符串的重复度
     * @param string $str1
     * @param string $str2
     * @return float
     */
    public static function calculateSimilarityForChinese(string $str1, string $str2) : float
    {
        // 使用 mb_str_split 来处理多字节字符串，确保正确分割中文字符
        $set1 = array_unique(mb_str_split($str1));
        $set2 = array_unique(mb_str_split($str2));

        // 求交集和并集
        $intersection = array_intersect($set1, $set2);
        $union = array_unique(array_merge($set1, $set2));

        // 计算Jaccard相似度
        return count($intersection) / count($union);
    }

    /**
     * 移除所有空格
     * @param string $str
     * @return string
     */
    public static function removeAllSpace(string $str) : string
    {
        return str_replace(' ', '', $str);
    }

    /**
     * 移除所有空白字符（空格、制表符、换行符等）
     * @param string $str
     * @return string
     */
    public static function removeAllWhitespace(string $str) : string
    {
        return preg_replace('/\s+/', '', $str);
    }

    /**
     * 字符串截断，按指定长度截断字符串，并添加省略号
     * @param string $str
     * @param int $length
     * @param string $suffix
     * @return string
     */
    public static function truncate(string $str, int $length, string $suffix = '...') : string
    {
        if (strlen($str) <= $length) {
            return $str;
        }
        return mb_substr($str, 0, $length) . $suffix;
    }

    /**
     * 检查字符串是否是子字符串开头
     * @param string $str
     * @param string $prefix
     * @return bool
     */
    public static function startWith(string $str, string $prefix) : bool
    {
        return strpos($str, $prefix) === 0;
    }

    /**
     * 检查字符串是否是子字符串结尾
     * @param string $str
     * @param string $suffix
     * @return bool
     */
    public static function endWith(string $str, string $suffix) : bool
    {
        return substr($str, -strlen($suffix)) === $suffix;
    }


}