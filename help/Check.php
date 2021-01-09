<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm
 *
 * User: kx
 * Date: 2021/1/9
 * Time: 5:05 PM
 */

namespace help\check;


class Check
{
    /**
     * 校验手机号格式
     *
     * @author kx
     * @param string $mobile
     * @return bool
     */
    public static function checkMobile(string $mobile) : bool
    {
        if (preg_match('/^1[3|4|5|6|7|8|9]\d{9}$/', $mobile)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 校验邮箱格式
     *
     * @author kx
     * @param string $email
     * @return bool
     */
    public static function checkEmail(string $email) : bool 
    {
        if (preg_match('/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/', $email)) {
            return true;
        } else {
            return false;
        }
    }
}