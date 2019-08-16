<?php
/**
 *  FileName: Time.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/24
 *  Time: 13:53
 */


namespace Kernel\Support;

use Exception;

class Time
{
    static $suffixStrArr = ["秒", "分", "时", "天"];
    const TIME_TYPE_SECOND = "second";
    const TIME_TYPE_MILLISECOND = "millisecond";
    const DATE_FORMAT_ERROR = "日期格式错误";
    const TIME_FORMAT_ERROR = "时间格式错误，应该是H:i:s或者H:i";
    const TYPE_VALUE_ERROR = "非法类型值";
    const SHIFT_VALUE_ERROR = "偏移量必须为Int类型";

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return time();
    }

    /**
     * @return float
     */
    public function getMicroTimestamp()
    {
        return ceil(microtime(true) * 1000);
    }

    /**
     * @param $time
     * @return false|int
     */
    public function getTimeForString($time)
    {
        return strtotime($time);
    }

    /**
     * @return false|string
     */
    public function getTodayTimestamp()
    {
        return date("Y-m-d");
    }

    /**
     * 获取时间错，根据时分秒获取
     * @param $time
     * @param string $date
     * @return false|int
     * @throws Exception
     */
    public function getTimestampForHMS($time, $date = "1970-01-01")
    {
        if (!preg_match("/^(([0-1]?\d)|(2[0-4])):[0-5]?\d(:[0-5]?\d)?\s?(AM|PM)?$/", $time))
            throw new Exception(self::TIME_FORMAT_ERROR);
        $time = $date." " .  $this->toDate($this->getTimeForString($time), "H:i:s");
        return $this->getTimeForString($time);
    }


    /**
     * @param $timestamp
     * @param string $format
     * @return false|string
     */
    public function toDate($timestamp, $format = "Y-m-d H:i")
    {
        return date($format, $timestamp);
    }


    /**
     * 获取当天凌晨时间戳
     * @param int $shift
     * @return false|float|int
     * @throws Exception
     */
    public function getDayBreakTimestamp($shift = 0)
    {
        if (!is_int($shift))
            throw new Exception(self::SHIFT_VALUE_ERROR);
        return $this->getTimeForString($this->getTodayTimestamp()) + ($shift * 86400);
    }

    /**
     * @param $date
     * @return bool
     */
    public function isDateTime($date)
    {
        $ret = $this->getTimeForString($date);
        return $ret !== FALSE && $ret != -1;
    }

    /**
     * @param $date
     * @return false|int
     * @throws Exception
     */
    public function toTimestamp($date)
    {
        if (!$this->isDateTime($date)) {
            throw new Exception(self::DATE_FORMAT_ERROR);
        }
        return $this->getTimeForString($date);
    }


    /**
     * @param $timestamp
     * @param $now
     * @param string $format
     * @return false|string
     */
    public function toDateAgo($timestamp, $now, $format = "Y-m-d H:i")
    {
        $time = (int)$now - (int)$timestamp;
        if ($time <= 3540) {
            $str = ceil($time / 60) . "分钟前";
        } else if ($time <= 82800) {
            $str = ceil($time / (60 * 60)) . "小时前";
        } else if ($time <= 1209600) {
            $str = ceil($time / (60 * 60 * 24)) . "天前";
        } else {
            $str = self::toDate($timestamp, $format);
        }
        return $str;
    }

    /**
     * @param $seconds
     * @return string
     */
    public function toDateString($seconds)
    {
        $seconds = (int)$seconds;
        if ($seconds < 60) {
            $time = gmstrftime('%S', $seconds);
        } else if ($seconds < 3600) {
            $time = gmstrftime('%S %M', $seconds);
        } else if ($seconds < 86400) {
            $time = gmstrftime('%S %M %H', $seconds);
        } else {
            $time = gmstrftime('%S %M %H %j', $seconds);
        }
        $times = explode(' ', $time);
        $strArr = [];
        foreach ($times as $key => $time) {
            if ($key == 3)
                $time--;
            $strArr[] = (int)$time . self::$suffixStrArr[$key];
        }
        return implode(" ", array_reverse($strArr));
    }

    /**
     * @param $start_time
     * @param $end_time
     * @param string $type
     * @return \Kernel\Support\String|string
     * @throws Exception
     */
    public function toRunTime($start_time, $end_time, $type = self::TIME_TYPE_SECOND)
    {
        if (!is_numeric($start_time)) {
            $start_time = $this->toTimestamp($start_time);
        }
        if (!is_numeric($end_time)) {
            $end_time = $this->toTimestamp($end_time);
        }
        $runTime = $end_time - $start_time;
        switch ($type) {
            case self::TIME_TYPE_SECOND:
                $str = $this->toDateString($runTime);
                break;
            case self::TIME_TYPE_MILLISECOND:
                if ($runTime <= 1000)
                    $str = $runTime . "毫秒";
                else
                    $str = $this->toDateString(intval($runTime / 1000)) . " " . (int)substr($runTime, 3) . "毫秒";
                break;
            default:
                throw new Exception(self::TYPE_VALUE_ERROR);
                break;
        }
        return $str;
    }
}
