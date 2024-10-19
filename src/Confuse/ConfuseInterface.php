<?php
/**
 * This file is part of ACaptcha.
 *
 * Licensed under The MIT License
 *
 * @author hassan mousavi
 */
namespace AngusDV\Captcha\Confuse;

interface ConfuseInterface
{
    /**
     * ConfuseInterface constructor.
     */
    public function __construct($dst, $mask, $imgDst, $imgMask);

    /**
     * Swap background and mask pixels
     * @return void
     */
    public function swapPixels();
}
?>
