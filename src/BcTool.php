<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm
 *
 * User: kx
 * Date: 2024/11/20
 * Time: 16:20
 */

namespace Shanla\Tools;


use InvalidArgumentException;

class BcTool
{
    /**
     * 两个数字相加
     * @param float|int|string $a 第一个数字
     * @param float|int|string $b 第二个数字
     * @param int $scale 保留的小数位数
     * @return string 返回计算结果
     */
    public static function add(float|int|string $a, float|int|string $b, int $scale = 2) : string
    {
        return bcadd((string)$a, (string)$b, $scale);
    }

    /**
     * 两个数字相减
     * @param float|int|string $a 被减数
     * @param float|int|string $b 减数
     * @param int $scale 保留的小数位数
     * @return string 返回计算结果
     */
    public static function sub(float|int|string $a, float|int|string $b, int $scale = 2) : string
    {
        return bcsub((string)$a, (string)$b, $scale);
    }

    /**
     * 两个数字相乘
     * @param float|int|string $a 第一个数字
     * @param float|int|string $b 第二个数字
     * @param int $scale 保留的小数位数
     * @return string 返回计算结果
     */
    public static function mul(float|int|string $a, float|int|string $b, int $scale = 2) : string
    {
        return bcmul((string)$a, (string)$b, $scale);
    }

    /**
     * 两个数字相除
     * @param float|int|string $a 被除数
     * @param float|int|string $b 除数
     * @param int $scale 保留的小数位数
     * @return string 返回计算结果
     * @throws InvalidArgumentException 如果除数为零
     */
    public static function div(float|int|string $a, float|int|string $b, int $scale = 2): string
    {
        if ((float)$b == 0.0) {
            throw new InvalidArgumentException('除数不能为零。');
        }

        return bcdiv((string)$a, (string)$b, $scale);
    }

    /**
     * 比较两个数字
     * @param float|int|string $a 第一个数字
     * @param float|int|string $b 第二个数字
     * @return int 返回 0 如果相等，1 如果 $a > $b，-1 如果 $a < $b
     */
    public static function compare(float|int|string $a, float|int|string $b) : int
    {
        return bccomp((string)$a, (string)$b);
    }

    /**
     * 模运算
     * @param float|int|string $a 被模数
     * @param float|int|string $b 模数
     * @return string 返回模运算结果
     * @throws InvalidArgumentException 如果模数为零
     */
    public static function modulus(float|int|string $a, float|int|string $b) : string
    {
        if ((float)$b == 0.0) {
            throw new InvalidArgumentException('模数不能为零。');
        }

        return bcmod((string)$a, (string)$b);
    }

    /**
     * 四舍五入
     * @param float|int|string $number 要四舍五入的数字
     * @param int $scale 保留的小数位数
     * @return string 返回结果
     */
    public static function round(float|int|string $number, int $scale = 2) : string
    {
        $factor = bcpow('10', (string)$scale);
        return bcdiv(bcmul((string)$number, $factor, $scale + 1), $factor, $scale);
    }

    /**
     * 进行连续的高精度运算
     * @param float|int|string $initialValue 初始值
     * @param array $operations 运算数组，格式为 [['operation' => 'add', 'value' => 10], ['operation' => 'multiply', 'value' => 2]]
     * @param int $scale 保留的小数位数
     * @return string 返回计算结果
     * @throws InvalidArgumentException 如果操作无效
     */
    public static function chainOperations(float|int|string $initialValue, array $operations, int $scale = 2): string
    {
        $result = (string)$initialValue;

        foreach ($operations as $operation) {
            $op = $operation['operation'] ?? '';
            $value = $operation['value'] ?? '';

            $result = match ($op) {
                'add' => self::add($result, $value, $scale),
                'sub' => self::sub($result, $value, $scale),
                'mul' => self::mul($result, $value, $scale),
                'div' => self::div($result, $value, $scale),
                'modulus' => self::modulus($result, $value),
                default => throw new InvalidArgumentException("无效的操作: $op"),
            };
        }

        return $result;
    }
}