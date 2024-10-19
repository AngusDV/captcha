<?php
/**
 * This file is part of ACaptcha.
 *
 * Licensed under The MIT License
 *
 * @author hassan mousavi
 */
namespace AngusDV\Captcha;

class Utils
{
    /**
     * @param resource $img
     * @param int $x
     * @param int $y
     * @return array|false
     */
    public static function getIndexRgb($img, $x, $y)
    {
        try {
            $idx = @ImageColorAt($img, $x, $y);
            if ($idx === false) {
                return false;
            }
            return imagecolorsforindex($img, $idx);
        } catch (\OutOfRangeException $e) {
            return false;
        }
    }

    /**
     * @param resource $img
     * @param array $oriRgb
     * @return false|int
     */
    public static function deepColor($img, $oriRgb)
    {
        $r = $oriRgb['red'] * 97 / 255;
        $g = $oriRgb['green'] * 97 / 255;
        $b = $oriRgb['blue'] * 97 / 255;
        return imagecolorallocate($img, (int)$r, (int)$g, (int)$b);
    }

    /**
     * @param resource $imgDst
     * @param resource $imgMask
     * @param array $dst
     * @param callable $swapFun
     */
    public static function swapAndDeepMask($imgDst, $imgMask, $dst, $swapFun)
    {
        $maskWH = imagesx($imgMask);
        for ($x = 0; $x < $maskWH; $x++) {
            for ($y = 0; $y < $maskWH; $y++) {
                $maskRgb = self::getIndexRgb($imgMask, $x, $y);
                if ($maskRgb === false) {
                    continue;
                }
                if ($maskRgb['alpha'] !== 127) {
                    $tx = $dst['left'] + $x;
                    $ty = $dst['top'] + $y;

                    $tRgb = self::getIndexRgb($imgDst, $tx, $ty);
                    if ($tRgb === false) {
                        continue;
                    }

                    $swapFun($x, $y, $tx, $ty, $tRgb);
                }
            }
        }
    }

    /**
     * @param array $arr
     * @return mixed
     * @throws \Exception
     */
    public static function randValue(array $arr)
    {
        if (empty($arr)) {
            return null;
        }
        return $arr[array_rand($arr)];
    }
}
