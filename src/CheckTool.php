<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm
 *
 * User: kx
 * Date: 2024/11/20
 * Time: 16:16
 */

namespace Shanla\Tools;


use DateTime;

class CheckTool
{
    /**
     * 校验手机号格式
     * @param string $mobile
     * @param string $country
     * @return bool
     */
    public static function checkMobile(string $mobile, string $country = 'CN') : bool
    {
        $patterns = [
            'CN' => '/^1[3-9]\d{9}$/', // 中国
            'US' => '/^\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}$/', // 美国
            'UK' => '/^07\d{9}$/', // 英国
            'INTL' => '/^\+?[1-9]\d{1,14}$/', // 国际
        ];
        if (!isset($patterns[$country])) {
            return false;
        }
        if (preg_match($patterns[$country], $mobile)) {
            return true;
        }
        return false;
    }

    /**
     * 校验邮箱格式
     * @param string $email
     * @return bool
     */
    public static function checkEmail(string $email) : bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * 校验邮编格式
     * @param string $postalCode
     * @param string $country
     * @return bool
     */
    public static function checkPostalCode(string $postalCode, string $country = 'CN') : bool
    {
        $patterns = [
            'CN' => '/^\d{6}$/', // 中国
            'US' => '/^\d{5}(-\d{4})?$/', // 美国
            'UK' => '/^[A-Z]{1,2}\d[A-Z\d]? \d[A-Z]{2}$/i', // 英国
        ];
        if (!isset($patterns[$country])) {
            return false;
        }
        if (preg_match($patterns[$country], $postalCode)) {
            return true;
        }
        return false;
    }

    /**
     * 校验IP地址格式
     * @param string $ipAddress
     * @return bool
     */
    public static function checkIpAddress(string $ipAddress) : bool
    {
        return filter_var($ipAddress, FILTER_VALIDATE_IP) != false;
    }

    /**
     * 校验日期有效性
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function checkDate(string $date, string $format = 'Y-m-d') : bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * 校验中国大陆身份证号格式
     * @param string $idCard
     * @return bool
     */
    public static function checkChineseIdCardNo(string $idCard) : bool
    {
        // 检查长度和基本格式
        if (!preg_match('/^\d{15}$|^\d{17}(\d|X)$/i', $idCard)) {
            return false;
        }
        // 转换为18位（如果是 15 位）
        if (strlen($idCard) === 15) {
            $idCard = substr($idCard, 0, 6) . '19' . substr($idCard, 6, 9);
            $idCard .= self::calculateCheckDigit($idCard);
        }
        // 校验出生日期
        $birthDate = substr($idCard, 6, 8);
        if (!self::checkDate($birthDate, 'Ymd')) {
            return false;
        }
        // 校验校验码
        return strtoupper(substr($idCard, 17, 1)) === self::calculateCheckDigit($idCard);
    }

    /**
     * 计算身份证的校验码
     * @param string $idCard
     * @return string
     */
    private static function calculateCheckDigit(string $idCard) : string
    {
        $weights = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        $checkDigits = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
        $sum = 0;

        for ($i = 0; $i < 17; $i++) {
            $sum += intval($idCard[$i]) * $weights[$i];
        }

        return $checkDigits[$sum % 11];
    }

    /**
     * 校验包含中文
     * @param string $str
     * @return bool
     */
    public static function checkContainsChinese(string $str) : bool
    {
        if(preg_match("/[\x{4e00}-\x{9fa5}]/u", $str)) {
            return true;
        }
        return false;
    }

    /**
     * 检测是否为合法url
     * @param string $url
     * @return bool
     */
    public static function checkUrl(string $url) : bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * 校验是否成年
     * @param string $birthday
     * @return bool
     */
    public static function checkAgeAdult(string $birthday) : bool
    {
        $birthDateObj = DateTime::createFromFormat('Y-m-d', $birthday);
        if (!$birthDateObj) {
            return false; // 无效的日期格式
        }

        $today = new DateTime(); // 当前日期
        $age = $today->diff($birthDateObj)->y; // 计算年龄

        return $age >= 18; // 判断是否成年
    }

    /**
     * 校验微信号格式
     * @param string $weChatId
     * @return bool
     */
    public static function checkWeChatId(string $weChatId) : bool
    {
        if (preg_match('/^[a-zA-Z][a-zA-Z0-9_]{5,19}$/', $weChatId)) {
            return true;
        }
        return false;
    }

    /**
     * 校验QQ格式
     * @param string $qq
     * @return bool
     */
    public static function checkQQ(string $qq) : bool
    {
        if (preg_match('/^[1-9][0-9]{4,11}$/', $qq)) {
            return true;
        }
        return false;
    }

    /**
     * 校验密码强度（长度至少8个字符并且包含大写字母、小写字母、数字和特殊字符）
     * @param string $password
     * @return bool
     */
    public static function checkPasswordStrong(string $password) : bool
    {
        if (preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
            return true;
        }
        return false;
    }

    /**
     * 校验HEX颜色代码格式
     * @param string $color
     * @return bool
     */
    public static function checkHexColor(string $color) : bool
    {
        if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color)) {
            return true;
        }
        return false;
    }

    /**
     * 校验是否是正整数
     * @param mixed $value
     * @return bool
     */
    public static function checkPositiveInteger(mixed $value) : bool
    {
        if (preg_match('/^[1-9][0-9]*$/', (string)$value)) {
            return true;
        }
        return false;
    }

    /**
     * 校验是否是负整数
     * @param mixed $value
     * @return bool
     */
    public static function checkNegativeInteger(mixed $value) : bool
    {
        if (preg_match('/^-\d+$/', (string)$value)) {
            return true;
        }
        return false;
    }

    /**
     * 校验XML格式
     * @param string $xml
     * @return bool
     */
    public static function checkXML(string $xml) : bool
    {
        libxml_use_internal_errors(true);
        simplexml_load_string($xml);
        return !libxml_get_errors();
    }

    /**
     * 校验JSON格式
     * @param string $json
     * @return bool
     */
    public static function checkJSON(string $json) : bool
    {
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }
}