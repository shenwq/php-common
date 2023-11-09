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
        return \ffhome\common\util\DateUtil::getConstellation($month, $day);
    }
}

if (!function_exists('getBirthdayPet')) {
    /**
     * 获取生肖
     *
     * @param $year
     * @return string
     */
    function getBirthdayPet($year): string
    {
        return \ffhome\common\util\DateUtil::getBirthdayPet($year);
    }
}

if (!function_exists('timeFormat')) {
    /**
     * 将00:00:00的时间格式化成00:00
     *
     * @param $time
     * @return string
     */
    function timeFormat($time): string
    {
        return \ffhome\common\util\DateUtil::timeFormat($time);
    }
}