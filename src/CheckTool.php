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


class CheckTool
{
    /**
     * 校验手机号格式
     * @param string $mobile
     * @return bool
     */
    public static function checkMobile(string $mobile) : bool
    {
        if (preg_match('/^1[3|4|5|6|7|8|9]\d{9}$/', $mobile)) {
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
        if (preg_match('/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$/', $email)) {
            return true;
        }
        return false;
    }

    /**
     * 校验邮编格式
     * @param string $postalCode
     * @return bool
     */
    public static function checkPostalCode(string $postalCode) : bool
    {
        if (preg_match("/^[1-9]\d{5}$/", $postalCode)) {
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
        if (preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" .
            "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $ipAddress)) {
            return true;
        }
        return false;
    }

    /**
     * 校验生日格式
     * @param string $birth
     * @param string $format
     * @return bool
     */
    public static function checkBirth(string $birth, string $format = 'Y-m-d') : bool
    {
        $unixTime = strtotime($birth);
        if (!$unixTime) {
            return false;
        }

        // 校验生日的格式是否正确
        if (date($format, $unixTime) == $birth) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 校验中国身份证号格式
     * @param string $idCard
     * @return bool
     */
    public static function checkChineseIdCardNo(string $idCard) : bool
    {
        if (preg_match('/(^([\d]{15}|[\d]{18}|[\d]{17}x)$)/', $idCard)) {
            return true;
        }
        return false;
    }

    /**
     * 校验是否是中文
     * @param string $str
     * @return bool
     */
    public static function checkChinese(string $str) : bool
    {
        if(preg_match("/[\x{4e00}-\x{9fa5}]+/u", $str)) {
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
        $toDateArr = explode('-', $birthday);
        if(count($toDateArr) != 3){
            return false;
        }
        if($toDateArr[0] < 1 || $toDateArr[1] < 1 || $toDateArr[2] < 1){
            return false;
        }
        list($nowYear,$nowMonth,$nowDay) = explode('-', date('Y-m-d'));
        list($toYear,$toMonth,$toDay) = $toDateArr;
        if($nowYear - $toYear < 18){
            return false;
        }elseif($nowYear - $toYear ==18){
            //同一年-判断月-日
            if($nowMonth < $toMonth){
                return false;
            }elseif($nowMonth -$toMonth == 0){
                //同一月判断日
                if($nowDay < $toDay){
                    return false;
                }
            }
        }
        return true;
    }
}