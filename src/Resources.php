<?php
declare(strict_types=1);
/**
 * This file is part of ACaptcha.
 *
 * Licensed under The MIT License
 *
 * @author Hassan Mousavi
 */

namespace AngusDV\Captcha;

use Exception;

class Resources
{
    public  $BASE_PATH ;

    /**
     * @var string[]
     * 200 * 160 px multiple
     */
    protected $bg ;
    public $customBg = [];
    /**
     * @var string[][]
     *
     * mask wh 60 * 60 px
     * svg viewBox 15.875
     */
    protected $mask ;

    /**
     *
     */
    public function __construct()
    {
        if(is_dir(public_path('vendor/acaptcha/assets')))
            $this->BASE_PATH=public_path('vendor/acaptcha/assets/images/');
        else
            $this->BASE_PATH=__DIR__ . '/../resources/assets/images/';

        $this->setBackgrounds();
        $this->setMasks();

    }

    public function setBackgrounds()
    {
        $backgrounds=config('acaptcha.background_images',[
            'bg/1.png',
            'bg/2.png',
            'bg/3.png',
            'bg/4.png',
            'bg/5.png',
        ]);
        foreach ($backgrounds as $background){
            $this->bg[]=$this->BASE_PATH.$background;
        }
    }

    public function setMasks()
    {
        $masks=config('acaptcha.mask_images',[
            [
                'img' =>  'mask/star.png',
                'path' => 'M 1.136655,3.3203515 6.1060004,3.0099206 9.468514,0.50570386 c 0,0 0.2931707,-0.23202947 0.3872529,0.0956479 0.094082,0.32767733 1.8656861,4.71463374 1.8656861,4.71463374 l 3.447523,2.4491389 c 0,0 0.121937,0.1474338 0.0163,0.2898124 C 15.079641,8.1973152 11.25402,11.277177 11.25402,11.277177 L 9.9496262,15.38326 c 0,0 -0.040019,0.138767 -0.2671061,0.07276 C 9.4554327,15.39001 5.3588636,12.69956 5.3588636,12.69956 l -4.2185818,0.05788 c 0,0 -0.25781398,0.03713 -0.20864011,-0.292809 C 0.98081559,12.13469 2.2303792,7.5899824 2.2303792,7.5899824 L 0.85933882,3.5082599 c 0,0 -0.10626247,-0.2139054 0.27731618,-0.1879084 z',
                'viewBox' => 15.875,
            ],
            [
                'img' =>  'mask/circle.png',
                'path' => 'M 15.274299,7.9750312 C 15.2429,17.708162 0.59930244,17.685739 0.63279101,7.9568732 0.6127643,-2.1097735 15.231862,-1.9835272 15.274299,7.9750312 Z',
                'viewBox' => 15.875,
            ],
            [
                'img' =>  'mask/triangle.png',
                'path' => 'M 1.1498175,4.0726686 13.863502,1.0830571 c 0,0 0.527302,-0.24653047 0.691878,0.1895674 0.160777,0.4260304 -3.783944,13.4809525 -3.783944,13.4809525 0,0 -0.113272,0.552973 -0.576934,0.291881 C 9.7414735,14.790355 1.0158163,4.496519 1.0158163,4.496519 c 0,0 -0.13712788,-0.2798387 0.1340012,-0.4238504 z',
                'viewBox' => 15.875,
            ]
        ]);
        $masks=collect($masks)->map(function($mask){
            $mask['img']=$this->BASE_PATH.$mask['img'];
            return $mask;
        })->toArray();
        $this->mask=$masks;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function bg(): string
    {
        if (!empty($this->customBg)) {
            return Utils::randValue($this->customBg);
        }
        return Utils::randValue($this->bg);
    }

    /**
     * @return string[]
     * @throws Exception
     */
    public function mask(): array
    {

        return Utils::randValue($this->mask);
    }

    /**
     * @param string $img
     * @return array
     * @throws Exception
     */
    public  function uniqueMask($img)
    {
        $copyMask = [];
        foreach ($this->mask as $item) {
            if ($item['img'] !== $img) {
                array_push($copyMask, $item);
            }
        }
        return Utils::randValue($copyMask);
    }
}
