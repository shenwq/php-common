<?php

namespace ffhome\common\util;

/**
 * Class Thumb 图片压缩类
 * @package app\admin
 */
class Thumb
{
    /**
     * 压缩指定目录下的图片文件
     * @param string $dir
     */
    public static function compressDir(string $dir = 'upload')
    {
        $handle = opendir($dir);
        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $file = $dir . '/' . $file;
            if (is_dir($file)) {
                self::compressDir($file);
            } else {
                self::compress('/' . $file);
            }
        }
    }

    /**
     * 直接压缩原图，文件名必须以"/upload/"开始
     *  压缩后的图片会出现t_前缀
     * @param string $fileName
     * @param bool $replace
     */
    public static function compress(string $fileName, bool $replace = true): string
    {
        $fileName = 'upload/' . self::getSubFileName($fileName);
        return '/' . self::make($fileName, '', 0, 0, 1, $replace);
    }

    /**
     * 将图片缩放到指定宽度，高度任意，文件名必须以"/upload/"开始
     * @param string $fileName
     * @param int $width
     * @param bool $replace
     * @return 压缩后的文件名，文件名必须以"/thumb/w$width"开始
     */
    public static function width(string $fileName, int $width, bool $replace = false): string
    {
        $subFileName = self::getSubFileName($fileName);
        if ($subFileName == '') {
            return $fileName;
        }
        return '/' . self::make("upload/{$subFileName}", "thumb/w{$width}/{$subFileName}", $width, 0, 2, $replace);
    }

    /**
     * 将图片缩放到指定高度，宽度任意，文件名必须以"/upload/"开始
     * @param string $fileName
     * @param int $height
     * @param bool $replace
     * @return 压缩后的文件名，文件名必须以"/thumb/w$widthh$height"开始
     */
    public static function height(string $fileName, int $height, bool $replace = false): string
    {
        $subFileName = self::getSubFileName($fileName);
        if ($subFileName == '') {
            return $fileName;
        }
        return '/' . self::make("upload/{$subFileName}", "thumb/h{$height}/{$subFileName}", 0, $height, 3, $replace);
    }

    /**
     * 将图片等比例缩放到指定的尺寸，文件名必须以"/upload/"开始
     * @param string $fileName
     * @param int $width
     * @param int $height
     * @param bool $replace
     * @return 压缩后的文件名，文件名必须以"/thumb/h$height"开始
     */
    public static function size(string $fileName, int $width = 0, int $height = 0, bool $replace = false): string
    {
        if ($width == 0 && $height == 0) {
            return self::compress($fileName, $replace);
        } elseif ($width == 0) {
            return self::height($fileName, $height, $replace);
        } elseif ($height == 0) {
            return self::width($fileName, $width, $replace);
        }
        $subFileName = self::getSubFileName($fileName);
        if ($subFileName == '') {
            return $fileName;
        }
        return '/' . self::make("upload/{$subFileName}", "thumb/w{$width}h{$height}/{$subFileName}", $width, $height, 4, $replace);
    }

    private static function getSubFileName(string $fileName): string
    {
        if (strpos($fileName, '/upload/') !== 0) {
            return '';
        }
        return substr($fileName, 8);
    }

    private static function make(string $from, string $to, int $width, int $height, int $type, bool $replace = false): string
    {
        $arr = explode('vendor', __DIR__);
        $basePath = $arr[0] . 'public' . DIRECTORY_SEPARATOR;
        // 不是压缩自己，存在已压缩文件直接返回
        if (!$replace && is_file($basePath . $to)) {
            return $to;
        }

        // 判断源文件是否是图片格式
        if (!is_file($basePath . $from)) {
            return $from;
        }
        $imageInfo = getimagesize($basePath . $from);
        if ($imageInfo === false) {
            return $from;
        }
        if ($imageInfo[2] > 3) {
            return $from;
        }

        if ($replace) {
            // 生成临时目标文件名称
            $index = strrpos($from, '/');
            $path = substr($from, 0, $index);
            $fileName = substr($from, $index + 1);
            $to = "{$path}/t_{$fileName}";
        } else {
            // 生成目标文件目录
            $index = strrpos($to, '/');
            $path = substr($to, 0, $index);
            if (!is_dir($basePath . $path)) {
                mkdir($basePath . $path, 0777, true);
            }
        }

        // 压缩并保存文件
        $functions = [1 => 'imagecreatefromgif', 2 => 'imagecreatefromjpeg', 3 => 'imagecreatefrompng'];
        $image = $functions[$imageInfo[2]]($basePath . $from);
        $info = self::computeSize($width, $height, imagesx($image), imagesy($image), $type);
        $res = imagecreatetruecolor($info[0], $info[1]);
        imagecopyresampled($res, $image, 0, 0, 0, 0, $info[0], $info[1], $info[2], $info[3]);
        $functions = [1 => 'imagegif', 2 => 'imagejpeg', 3 => 'imagepng'];
        $functions[$imageInfo[2]]($res, $basePath . $to);

        //替换根据文件大小保留小的文件
        if ($replace) {
            if (filesize($basePath . $from) > filesize($basePath . $to)) {
                unlink($basePath . $from);
                return $to;
            } else {
                unlink($basePath . $to);
                return $from;
            }
        }
        return $to;
    }

    private static function computeSize(int $rw, int $rh, int $iw, int $ih, int $type)
    {
        switch ($type) {
            case 1:
                //宽高都不变
                $rw = $iw;
                $rh = $ih;
                break;
            case 2:
                //保留宽度，高度自动
                $rh = $rw / $iw * $ih;
                break;
            case 3:
                //保留高度，宽度自动
                $rw = $rh / $ih * $iw;
                break;
            case 4:
            default:
                if ($iw / $rw > $ih / $rh) {
                    $iw = $ih / $rh * $rw;
                } else {
                    $ih = $iw / $rw * $rh;
                }
        }

        return [$rw, $rh, $iw, $ih];
    }
}