<?php
declare(strict_types = 1);

namespace Shanla\Tools;


use DateInterval;
use DateTime;
use Exception;

class DateTimeTool
{
    /**
     * 用于测试的当前时间
     * @var DateTime|null
     */
    private static ?DateTime $testNow = null;

    /**
     * 设置测试用的当前时间
     * @param DateTime $dateTime
     * @return void
     */
    public static function setTestNow(DateTime $dateTime): void
    {
        self::$testNow = $dateTime;
    }

    /**
     * 获取当前时间
     * @return DateTime
     */
    private static function getNow(): DateTime
    {
        return self::$testNow ?? new DateTime();
    }

    /**
     * 获取当前日期
     * @param string $format
     * @return string
     */
    public static function now(string $format = 'Y-m-d H:i:s') : string
    {
        return self::getNow()->format($format);
    }

    /**
     * 获取指定日期的开始时间（00:00:00）
     * @param string $date
     * @return string
     * @throws Exception
     */
    public static function startOfDay(string $date) : string
    {
        return (new DateTime($date))->setTime(0, 0)->format('Y-m-d H:i:s');
    }

    /**
     * 获取指定日期的结束时间（23:59:59）
     * @param string $date
     * @return string
     * @throws Exception
     */
    public static function endOfDay(string $date) : string
    {
        return (new DateTime($date))->setTime(23, 59, 59)->format('Y-m-d H:i:s');
    }

    /**
     * 判断给定的日期是否周末
     * @param string $date
     * @return bool
     * @throws Exception
     */
    public static function isWeekend(string $date) : bool
    {
        $datetime = new DateTime($date);
        return $datetime->format('N') >= 6;
    }

    /**
     * 判断给定的日期是否工作日
     * @param string $date
     * @return bool
     * @throws Exception
     */
    public static function isWeekDay(string $date) : bool
    {
        $datetime = new DateTime($date);
        return $datetime->format('N') < 6;
    }

    /**
     * 判断给定的日期是否是润年
     * @param string $date
     * @return bool
     * @throws Exception
     */
    public static function isLeapYear(string $date) : bool
    {
        $datetime = new DateTime($date);
        return $datetime->format('L') == 1;
    }

    /**
     * 获取指定的日期属于第几季度
     * @param string $date
     * @return int
     * @throws Exception
     */
    public static function getQuarter(string $date) : int
    {
        $month = intval((new DateTime($date))->format('m'));
        return intval(ceil($month / 3));
    }

    /**
     * 获取时间差
     * @param string $startTime
     * @param string $endTime
     * @return string
     * @throws Exception
     */
    public static function getTimeDifference(string $startTime, string $endTime) : string
    {
        $difference = '';
        if (empty($startTime) || empty($endTime)) {
            return $difference;
        }
        $startTime = new DateTime($startTime);
        $endTime = new DateTime($endTime);
        $diff = $startTime->diff($endTime);

        if ($diff->y > 0) {
            $difference .= $diff->y . '年';
        }
        if ($diff->m > 0) {
            $difference .= $diff->m . '月';
        }
        if ($diff->d > 0) {
            $difference .= $diff->d . '天';
        }
        if ($diff->h > 0) {
            $difference .= $diff->h . '小时';
        }
        if ($diff->i > 0) {
            $difference .= $diff->i . '分';
        }
        if ($diff->s > 0) {
            $difference .= $diff->s . '秒';
        }
        return $difference;
    }

    /**
     * 获取指定时间距今的时间
     * @param string $startTime
     * @param string $suffix
     * @return string
     */
    public static function getTimeAgo(string $startTime, string $suffix = '') : string
    {
        if (empty($startTime)) {
            return '';
        }
        $startTimestamp = strtotime($startTime);
        $diffTimestamp = self::getNow()->getTimestamp() - $startTimestamp;

        if ($diffTimestamp < 60) {
            return '1分钟前' . $suffix;
        } elseif ($diffTimestamp < 3600) {
            $minutes = floor($diffTimestamp / 60);
            return $minutes . '分钟前' . $suffix;
        } elseif ($diffTimestamp < 86400) {
            $hours = floor($diffTimestamp / 3600);
            return $hours . '小时前' . $suffix;
        } elseif ($diffTimestamp < 86400 * 3) {
            $days = floor($diffTimestamp / 86400);
            return $days . '天前' . $suffix;
        } else {
            $yearNow = self::getNow()->format('Y');
            $yearStart = date('Y', $startTimestamp);

            if ($yearNow == $yearStart) {
                return date('m-d', $startTimestamp);
            } else {
                return date('Y-m-d', $startTimestamp);
            }
        }
    }

    /**
     * 获取下个月几号，如果不存在则返回下个月最后一天的时间
     * @param int $day
     * @param string $format
     * @return string
     */
    public static function getNextMonthDate(int $day, string $format = 'Y-m-d') : string
    {
        // 使用当前时间或测试时间
        $currentDateTime = self::getNow();

        // 获取当前月份和年份
        $currentMonth = intval($currentDateTime->format('m'));
        $currentYear = intval($currentDateTime->format('Y'));

        // 计算下个月的月份和年份
        $nextMonth = $currentMonth == 12 ? 1 : $currentMonth + 1;
        $nextYear = $currentMonth == 12 ? $currentYear + 1 : $currentYear;

        // 创建下个月指定日期的 DateTime 对象，并设置时间为 00:00:00
        $nextMonthDateTime = DateTime::createFromFormat('Y-m-d H:i:s', "$nextYear-$nextMonth-$day 00:00:00");

        // 如果下个月不存在的日期则返回下个月最后一天
        if ($nextMonthDateTime->format('m') != $nextMonth || $nextMonthDateTime->format('d') != $day) {
            $lastDayOfNextMonth = new DateTime("last day of $nextYear-$nextMonth");
            $lastDayOfNextMonth->setTime(0, 0);
            return $lastDayOfNextMonth->format($format);
        }

        return $nextMonthDateTime->format($format);
    }

    /**
     * 获取下周几的日期
     * @param int $dayOfWeek
     * @param string $format
     * @return string
     * @throws Exception
     */
    public static function getNextWeekDate(int $dayOfWeek, string $format = 'Y-m-d') : string
    {
        $currentDateTime = self::getNow();

        // 获取当前是星期几
        $currentDayOfWeek = $currentDateTime->format('N');

        // 计算到下周指定星期几的天数差
        // 总是先加上7天到下周，然后调整到指定的星期几
        $daysToAdd = 7 + ($dayOfWeek - $currentDayOfWeek);

        // 创建新的 DateTime 对象以避免修改原始对象
        $nextWeekDate = clone $currentDateTime;
        $nextWeekDate->add(new DateInterval('P' . $daysToAdd . 'D'));
        
        // 设置时间为 00:00:00
        $nextWeekDate->setTime(0, 0);

        // 返回格式化后的日期字符串
        return $nextWeekDate->format($format);
    }


}