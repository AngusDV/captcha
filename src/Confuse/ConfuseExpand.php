<?php
/**
 * This file is part of ACaptcha.
 *
 * Licensed under The MIT License
 *
 * @author hassan mousavi
 */
namespace AngusDV\Captcha\Confuse;

use Exception;
use AngusDV\Captcha\Resources;
use AngusDV\Captcha\Utils;

/**
 * Overlay expands new graphics
 * Class ConfuseExpand
 * @package AngusDV\Captcha\Confuse
 */
class ConfuseExpand implements ConfuseInterface
{
    private $dst;
    private $mask;
    private $imgDst;
    private $imgMask;

    private $cDst;
    private $cImgMask = null;

    /**
     * @throws Exception
     */
    public function __construct($dst, $mask, $imgDst, $imgMask)
    {
        $this->dst = $dst;
        $this->mask = $mask;
        $this->imgMask = $imgMask;
        $this->imgDst = $imgDst;

        $this->initCDst();
        $this->initCImgMask();
    }

    /**
     * @inheritDoc
     * @return void
     */
    public function swapPixels()
    {
        $maps = [];

        Utils::swapAndDeepMask($this->imgDst, $this->imgMask, $this->dst, function ($x, $y, $tx, $ty, $tRgb) use (&$maps) {
            $color = imagecolorallocate($this->imgMask, $tRgb['red'], $tRgb['green'], $tRgb['blue']);
            imagesetpixel($this->imgMask, $x, $y, $color);

            $tColor = Utils::deepColor($this->imgDst, $tRgb);
            imagesetpixel($this->imgDst, $tx, $ty, $tColor);

            $maps[] = $tx . '-' . $ty;
        });

        Utils::swapAndDeepMask($this->imgDst, $this->cImgMask, $this->cDst, function ($x, $y, $tx, $ty, $tRgb) use (&$maps) {
            if (in_array($tx . '-' . $ty, $maps, true)) {
                return;
            }
            $tColor = Utils::deepColor($this->imgDst, $tRgb);
            imagesetpixel($this->imgDst, $tx, $ty, $tColor);
        });
    }

    /**
     * @throws Exception
     */
    private function initCDst()
    {
        $offset = (int)(imagesx($this->imgMask) * 0.5);
        $this->cDst = [
            'left' => $this->dst['left'] + (mt_rand(-$offset, $offset)),
            'top' => $this->dst['top'] + (mt_rand(-$offset, $offset)),
        ];
    }

    /**
     * @throws Exception
     */
    private function initCImgMask()
    {
        $resources=new Resources();
        $mask = $resources->uniqueMask($this->mask['img']);
        $this->cImgMask = imagecreatefrompng($mask['img']);
        imagesavealpha($this->cImgMask, true);
    }

    public function __destruct()
    {
        if ($this->cImgMask) {
            imagedestroy($this->cImgMask);
            $this->cImgMask = null;
        }
    }
}
