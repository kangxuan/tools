<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm
 *
 * User: kx
 * Date: 2021/1/9
 * Time: 6:00 PM
 */

namespace tool;


trait BcTool
{
    /**
     * 比较任意精度的数字
     *
     * @author kx
     * @param $left
     * @param $right
     * @param int $scale
     * @return int 0-相等 1-left大于right -1-right大于left
     */
    public function comp($left, $right, int $scale = 4) : int
    {
        if (!is_string($left)) {
            $left = (string)$left;
        }
        if (!is_string($right)) {
            $right = (string)$right;
        }

        return bccomp($left, $right, $scale);
    }
}