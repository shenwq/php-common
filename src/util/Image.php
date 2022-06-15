<?php

namespace ffhome\common\util;

class Image
{
    public static function blur($from, $blurFactor = 3)
    {
        $arr = explode('.', $from);
        $arr[count($arr) - 2] = $arr[count($arr) - 2] . '_blur';
        $to = implode('.', $arr);

        $arr = explode('vendor', __DIR__);
        $basePath = $arr[0] . 'public';

        // 存在模糊文件直接返回
        if (is_file($basePath . $to)) {
            return $to;
        }

        // 无源文件
        if (!is_file($basePath . $from)) {
            return '';
        }

        $gdImageResource = self::image_create_from_ext($basePath . $from);
        $srcImgObj = self::_blur($gdImageResource, $blurFactor);
        $srcinfo = @getimagesize($basePath . $from);
        switch ($srcinfo[2]) {
            case 1:
                imagegif($srcImgObj, $basePath . $to);
                break;
            case 2:
                imagejpeg($srcImgObj, $basePath . $to);
                break;
            case 3:
                imagepng($srcImgObj, $basePath . $to);
                break;
            default:
                return ''; //保存失败
        }
        imagedestroy($srcImgObj);
        return $to;
    }

    private static function _blur($gdImageResource, $blurFactor = 3)
    {
        // blurFactor has to be an integer
        $blurFactor = round($blurFactor);
        $originalWidth = imagesx($gdImageResource);
        $originalHeight = imagesy($gdImageResource);
        $smallestWidth = ceil($originalWidth * pow(0.5, $blurFactor));
        $smallestHeight = ceil($originalHeight * pow(0.5, $blurFactor));
        // for the first run, the previous image is the original input
        $prevImage = $gdImageResource;
        $prevWidth = $originalWidth;
        $prevHeight = $originalHeight;
        // scale way down and gradually scale back up, blurring all the way
        for ($i = 0; $i < $blurFactor; $i += 1) {
            // determine dimensions of next image
            $nextWidth = $smallestWidth * pow(2, $i);
            $nextHeight = $smallestHeight * pow(2, $i);
            // resize previous image to next size
            $nextImage = imagecreatetruecolor($nextWidth, $nextHeight);
            imagecopyresized($nextImage, $prevImage, 0, 0, 0, 0,
                $nextWidth, $nextHeight, $prevWidth, $prevHeight);
            // apply blur filter
            imagefilter($nextImage, IMG_FILTER_GAUSSIAN_BLUR);
            // now the new image becomes the previous image for the next step
            $prevImage = $nextImage;
            $prevWidth = $nextWidth;
            $prevHeight = $nextHeight;
        }
        // scale back to original size and blur one more time
        imagecopyresized($gdImageResource, $nextImage,
            0, 0, 0, 0, $originalWidth, $originalHeight, $nextWidth, $nextHeight);
        imagefilter($gdImageResource, IMG_FILTER_GAUSSIAN_BLUR);
        // clean up
        imagedestroy($prevImage);
        // return result
        return $gdImageResource;
    }

    private static function image_create_from_ext($imgfile)
    {
        $info = getimagesize($imgfile);
        $im = null;
        switch ($info[2]) {
            case 1:
                return imagecreatefromgif($imgfile);
            case 2:
                return imagecreatefromjpeg($imgfile);
            case 3:
                return imagecreatefrompng($imgfile);
        }
        return $im;
    }
}