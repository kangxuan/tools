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
}