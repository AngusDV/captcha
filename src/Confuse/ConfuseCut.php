<?php
/**
 * This file is part of ACaptcha.
 *
 * Licensed under The MIT License
 *
 * @author hassan mousavi
 */
namespace AngusDV\Captcha\Confuse;

use AngusDV\Captcha\Utils;

/**
 * cut graphics
 * Class ConfuseCut
 * @package AngusDV\Captcha\Confuse
 */
class ConfuseCut implements ConfuseInterface
{
    private $dst;
    private $mask;
    private $imgMask;
    private $imgDst;

    /**
     * @var false|int
     */
    private $maskWH;

    const DIR_LT = 'LT';
    const DIR_TR = 'TR';
    const DIR_RB = 'RB';
    const DIR_BL = 'BL';
    const DIR_TB = 'TB';
    const DIR_LR = 'LR';

    const DIR_ARR = [
        self::DIR_LT,
        self::DIR_TR,
        self::DIR_RB,
        self::DIR_BL,
        self::DIR_TB,
        self::DIR_LR,
    ];

    private $cutPoint;
    private $curDir;
    private $bleA = 0;
    private $bleB = 0;

    /**
     * @throws \Exception
     */
    public function __construct($dst, $mask, $imgDst, $imgMask)
    {
        $this->dst = $dst;
        $this->mask = $mask;
        $this->imgMask = $imgMask;
        $this->imgDst = $imgDst;

        $this->maskWH = imagesx($imgMask);

        $this->initCutPoint();
        $this->initCurDir();
        $this->initBLEAB();
    }

    /**
     * @inheritDoc
     */
    public function swapPixels()
    {
        Utils::swapAndDeepMask($this->imgDst, $this->imgMask, $this->dst, function ($x, $y, $tx, $ty, $tRgb) {
            $color = imagecolorallocate($this->imgMask, $tRgb['red'], $tRgb['green'], $tRgb['blue']);
            imagesetpixel($this->imgMask, $x, $y, $color);

            if ($this->shouldSwap($x, $y) === false) {
                return;
            }
            $tColor = Utils::deepColor($this->imgDst, $tRgb);
            imagesetpixel($this->imgDst, $tx, $ty, $tColor);
        });
    }

    /**
     * @param $x
     * @param $y
     * @return bool
     */
    private function shouldSwap($x, $y)
    {
        list($cx, $cy) = $this->cutPoint;
        $bleY = $this->bleA * $x + $this->bleB;
        switch ($this->curDir) {
            case self::DIR_LT:
            case self::DIR_TR:
                return $y > $bleY;
            case self::DIR_RB:
            case self::DIR_BL:
                return $y < $bleY;
            case self::DIR_TB:
                return $x < $cx;
            case self::DIR_LR:
                return $y < $cy;
        }
        return true;
    }

    private function initBLEAB()
    {
        list($cx, $cy) = $this->cutPoint;
        switch ($this->curDir) {
            case self::DIR_LT:
                $this->bleA = -$cy / $cx;
                $this->bleB = $cy;
                break;
            case self::DIR_TR:
                $this->bleA = ($this->maskWH - $cx) / $cy;
                $this->bleB = -$cx * $this->bleA;
                break;
            case self::DIR_RB:
                $this->bleA = ($this->maskWH - $cx) / ($cy - $this->maskWH);
                $this->bleB = $cx - $this->maskWH * $this->bleA;
                break;
            case self::DIR_BL:
                $this->bleA = ($this->maskWH - $cy) / $cx;
                $this->bleB = $cy;
                break;
        }
    }

    /**
     * @throws \Exception
     */
    private function initCutPoint()
    {
        $min = (int)($this->maskWH * 0.4);
        $max = (int)($this->maskWH * 0.6);
        $this->cutPoint = [
            mt_rand($min, $max),
            mt_rand($min, $max)
        ];
    }

    /**
     * @throws \Exception
     */
    public function initCurDir()
    {
        $this->curDir = Utils::randValue(self::DIR_ARR);
    }
}
