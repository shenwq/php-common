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
