<?php
declare (strict_types=1);

namespace ffhome\common\util;

/**
 * 身份证工具类
 * @package ffhome\common\util
 */
class IdCardUtil
{
    /**
     * 根据身份证获得生日
     * @param $idcard
     * @return string|null
     */
    public static function getBirthday($idcard)
    {
        if (empty($idcard)) return null;
        $bir = substr($idcard, 6, 8);
        return substr($bir, 0, 4) . '-' . substr($bir, 4, 2) . '-' . substr($bir, 6, 2);
    }

    /**
     * 根据身份证获得性别(1男2女)
     * @param $idcard
     * @return int|null
     */
    public static function getSex($idcard)
    {
        if (empty($idcard)) return null;
        $sex = (int)substr($idcard, 16, 1);
        return $sex % 2 === 0 ? 2 : 1;
    }
}