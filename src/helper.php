<?php
declare (strict_types=1);

if (!function_exists('currentUrl')) {
    /**
     * 当前URL
     * @return string
     */
    function currentUrl(): string
    {
        return \ffhome\common\util\CommonUtil::getUrl();
    }
}

if (!function_exists('isWeixin')) {
    /**
     * 是否是Weixin浏览器
     * @return bool
     */
    function isWeixin(): bool
    {
        return \ffhome\common\util\CommonUtil::isWeixin();
    }
}

if (!function_exists('isH5App')) {
    /**
     * 是否是H5App
     * @return bool
     */
    function isH5App(): bool
    {
        return \ffhome\common\util\CommonUtil::isH5App();
    }
}

if (!function_exists('browser')) {
    /**
     * 返回浏览器类型,app,wx,h5
     * @return string
     */
    function browser(): string
    {
        return \ffhome\common\util\CommonUtil::browser();
    }
}

if (!function_exists('isMobile')) {
    /**
     * 是否是手机
     * @return bool
     */
    function isMobile(): bool
    {
        return \ffhome\common\util\CommonUtil::isMobile();
    }
}

if (!function_exists('thumb')) {
    /**
     * 压缩图片
     * @param string $fileName
     * @param int $width
     * @param int $height
     * @param bool $replace
     * @return string
     */
    function thumb(string $fileName, int $width = 0, int $height = 0, bool $replace = false): string
    {
        return \ffhome\common\util\Thumb::size($fileName, $width, $height, $replace);
    }
}

if (!function_exists('getConstellation')) {
    /**
     * 获取指定日期对应星座
     *
     * @param integer $month 月份 1-12
     * @param integer $day 日期 1-31
     * @return boolean|string
     */
    function getConstellation($month, $day)
    {
        $day = intval($day);
        $month = intval($month);
        if ($month < 1 || $month > 12 || $day < 1 || $day > 31) return false;
        $month--;
        $signs = [
            [20, '摩羯座'], [19, '宝瓶座'], [20, '双鱼座'], [20, '白羊座'],
            [20, '金牛座'], [21, '双子座'], [23, '巨蟹座'], [23, '狮子座'],
            [23, '处女座'], [23, '天秤座'], [22, '天蝎座'], [21, '射手座']
        ];
        list($start, $name) = $signs[$month];
        if ($day > $start) {
            $month++;
            list($start, $name) = $signs[($month == 12) ? 0 : $month];
        }
        return $name;
    }
}

if (!function_exists('getBirthdayPet')) {
    /**
     * 获取生肖
     *
     * @param $year
     * @return string
     */
    function getBirthdayPet($year)
    {
        $year = intval($year);
        $signs = ['猴', '鸡', '狗', '猪', '鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊'];
        return $signs[$year % 12];
    }
}