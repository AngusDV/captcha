<?php

/**
 * This file is part of ACaptcha.
 *
 * Licensed under The MIT License
 *
 * @author hassan mousavi
 */
namespace AngusDV\Captcha;

use AngusDV\Captcha\Confuse\ConfuseCut;
use AngusDV\Captcha\Confuse\ConfuseExpand;
use AngusDV\Captcha\Confuse\ConfuseInterface;

class Maker
{
    private $useConfuse;

    private $confuseClass = [
        'AngusDV\Captcha\Confuse\ConfuseCut',
        'AngusDV\Captcha\Confuse\ConfuseExpand',
    ];

    public function __construct($useConfuse = false) {
        $this->useConfuse = (bool) $useConfuse;
    }

    /**
     * @param array $dst
     * @param array $mask
     * @param resource $imgDst
     * @param resource $imgMask
     * @throws \Exception
     */
    public function swapPixels($dst, $mask, $imgDst, $imgMask)
    {
        if ($this->useConfuse) {
            $this->confuse($dst, $mask, $imgDst, $imgMask)->swapPixels();
            return;
        }

        //normal
        Utils::swapAndDeepMask($imgDst, $imgMask, $dst, function ($x, $y, $tx, $ty, $tRgb) use ($imgMask, $imgDst) {
            $color = imagecolorallocate($imgMask, $tRgb['red'], $tRgb['green'], $tRgb['blue']);
            imagesetpixel($imgMask, $x, $y, $color);

            $tColor = Utils::deepColor($imgDst, $tRgb);
            imagesetpixel($imgDst, $tx, $ty, $tColor);
        });
    }

    /**
     * @param array $dstPosition
     * @param array $mask
     * @param resource $imgDst
     * @param resource $imgMask
     * @return ConfuseInterface
     * @throws \Exception
     */
    private function confuse($dstPosition, $mask, $imgDst, $imgMask)
    {
        $class = Utils::randValue($this->confuseClass);
        return new $class($dstPosition, $mask, $imgDst, $imgMask);
    }
}
