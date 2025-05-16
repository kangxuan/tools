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
        return str_starts_with($str, $prefix);
    }

    /**
     * 检查字符串是否是子字符串结尾
     * @param string $str
     * @param string $suffix
     * @return bool
     */
    public static function endWith(string $str, string $suffix) : bool
    {
        return str_ends_with($str, $suffix);
    }

    /**
     * JSON decode
     * @param string $jsonStr
     * @return array
     */
    public static function jsonDecode(string $jsonStr) : array
    {
        if (empty($jsonStr)) {
            return [];
        }
        return json_decode($jsonStr, true);
    }

    /**
     * 隐藏手机号中间四位数
     * @param string $mobile
     * @return string
     */
    public static function hideMobile(string $mobile) : string
    {
        if (!empty($mobile)) {
            return substr_replace($mobile, '****', -8, 4);
        }
        return $mobile;
    }

    /**
     * 生成随机字符串
     * @param int $length 字符串长度
     * @param string $type 字符类型：alpha-字母, numeric-数字, alphanumeric-字母和数字, all-所有字符
     * @return string
     */
    public static function random(int $length = 16, string $type = 'alphanumeric') : string
    {
        $alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numeric = '0123456789';
        $special = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $chars = match($type) {
            'alpha' => $alpha,
            'numeric' => $numeric,
            'alphanumeric' => $alpha . $numeric,
            'all' => $alpha . $numeric . $special,
            default => $alpha . $numeric
        };

        $result = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $max)];
        }
        return $result;
    }

    /**
     * 将字符串转换为驼峰命名
     * @param string $str
     * @param string $separator 分隔符
     * @return string
     */
    public static function toCamelCase(string $str, string $separator = '_') : string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace($separator, ' ', $str))));
    }

    /**
     * 将字符串转换为下划线命名
     * @param string $str
     * @return string
     */
    public static function toSnakeCase(string $str) : string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $str));
    }

    /**
     * 将字符串转换为标题格式（每个单词首字母大写）
     * @param string $str
     * @param string $separator 分隔符
     * @return string
     */
    public static function toTitleCase(string $str, string $separator = '_') : string
    {
        return ucwords(str_replace($separator, ' ', $str));
    }

    /**
     * 检查字符串是否包含指定的子字符串
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function contains(string $haystack, string|array $needles) : bool
    {
        if (!is_array($needles)) {
            return str_contains($haystack, $needles);
        }

        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 将字符串转换为小写
     * @param string $str
     * @return string
     */
    public static function toLower(string $str) : string
    {
        return mb_strtolower($str);
    }

    /**
     * 将字符串转换为大写
     * @param string $str
     * @return string
     */
    public static function toUpper(string $str) : string
    {
        return mb_strtoupper($str);
    }

    /**
     * 将字符串转换为首字母大写
     * @param string $str
     * @return string
     */
    public static function toFirstUpper(string $str) : string
    {
        return mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1);
    }

    /**
     * 将字符串转换为首字母小写
     * @param string $str
     * @return string
     */
    public static function toFirstLower(string $str) : string
    {
        return mb_strtolower(mb_substr($str, 0, 1)) . mb_substr($str, 1);
    }
}