<?php
declare (strict_types=1);

namespace ffhome\common\util;

class DateUtil
{
    const STARS = [
        [20, '摩羯座'], [19, '宝瓶座'], [20, '双鱼座'], [20, '白羊座'],
        [20, '金牛座'], [21, '双子座'], [23, '巨蟹座'], [23, '狮子座'],
        [23, '处女座'], [23, '天秤座'], [22, '天蝎座'], [21, '射手座']
    ];

    /**
     * 获取指定日期对应星座
     *
     * @param integer $month 月份 1-12
     * @param integer $day 日期 1-31
     * @return boolean|string
     */
    public static function getConstellation($month, $day)
    {
        $day = intval($day);
        $month = intval($month);
        if ($month < 1 || $month > 12 || $day < 1 || $day > 31) return false;
        $month--;
        list($start, $name) = self::STARS[$month];
        if ($day > $start) {
            $month++;
            list($start, $name) = self::STARS[($month == 12) ? 0 : $month];
        }
        return $name;
    }

    const PET = ['猴', '鸡', '狗', '猪', '鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊'];

    /**
     * 获取生肖
     *
     * @param $year
     * @return string
     */
    public static function getBirthdayPet($year): string
    {
        return self::PET[intval($year) % 12];
    }

    /**
     * 将00:00:00的时间格式化成00:00
     *
     * @param $time
     * @return string
     */
    public static function timeFormat($time): string
    {
        if (empty($time)) return '';
        return substr($time, 0, -3);
    }

    const WEEK = ['日', '一', '二', '三', '四', '五', '六'];

    public static function week($date): string
    {
        if (empty($date)) return '';
        return self::WEEK[date('w', strtotime($date))];
    }
}