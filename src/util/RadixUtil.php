<?php
declare (strict_types=1);

namespace ffhome\common\util;

class RadixUtil
{
    const RADIX_62 = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i',
        'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E',
        'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    const RADIX_36 = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i',
        'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];

    /**
     * 10进制转62进制
     * @param int $number
     * @return string
     */
    public static function convert10to62(int $number): string
    {
        return self::convert10ToOther($number, self::RADIX_62);
    }

    /**
     * 62进制转10进制
     * @param string $number
     * @return int
     */
    public static function convert62to10(string $number): int
    {
        return self::convertOtherTo10($number, self::RADIX_62);
    }

    /**
     * 10进制转36进制
     * @param int $number
     * @return string
     */
    public static function convert10to36(int $number): string
    {
        return self::convert10ToOther($number, self::RADIX_36);
    }

    /**
     * 36进制转10进制
     * @param string $number
     * @return int
     */
    public static function convert36to10(string $number): int
    {
        return self::convertOtherTo10($number, self::RADIX_36);
    }

    private static function convert10ToOther(int $number, array $radix): string
    {
        $size = count($radix);
        $stack = [];
        while ($number != 0) {
            array_push($stack, $radix[$number % $size]);
            $number = intval($number / $size);
        }
        return implode('', array_reverse($stack));
    }

    private static function convertOtherTo10(string $number, array $radix): int
    {
        $size = count($radix);
        $multiple = 1;
        $result = 0;
        $length = strlen($number);
        for ($i = 0; $i < $length; ++$i) {
            $result += self::valueOther($number[$length - $i - 1], $radix, $size) * $multiple;
            $multiple *= $size;
        }
        return $result;
    }

    private static function valueOther(string $number, array $radix, int $size): int
    {
        for ($i = 0; $i < $size; ++$i) {
            if ($number == $radix[$i]) {
                return $i;
            }
        }
        return -1;
    }
}