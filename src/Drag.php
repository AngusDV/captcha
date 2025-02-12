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
use Illuminate\Support\Facades\Cache;

class Drag
{
    public const BASE64_HEADER = 'data:image/png;base64,';
    /**
     * @var int drag image bg width
     */
    private $bgWidth;
    /**
     * @var int  drag image bg height
     */
    private $bgHeight;
    /**
     * @var int drag image mask width/height
     */
    private $maskWH;
    /**
     * @var int verify offset default
     */
    private $offset;
    /**
     * construct class drag
     */
    public function __construct()
    {
        $this->bgWidth=config('acaptcha.background_width',250);
        $this->bgHeight=config('acaptcha.background_height',160);
        $this->maskWH=config('acaptcha.mask_width_height',60);
        $this->offset=config('acaptcha.offset',3);

    }
    /**
     * @throws \Exception
     */
    public function generate(): array
    {
        $resources=new Resources();
        $bgPath = $resources->bg();
        $mask = $resources->mask();
        $imgBG = imagecreatefrompng($bgPath);
        $imgDst = imagecreatetruecolor($this->bgWidth, $this->bgHeight);
        $imgMask = imagecreatefrompng($mask['img']);

        imagesavealpha($imgMask, true);
        imagesavealpha($imgDst, true);

        imagecopyresized(
            $imgDst,
            $imgBG,
            0,
            0,
            0,
            0,
            $this->bgWidth,
            $this->bgHeight,
            imagesx($imgBG),
            imagesy($imgBG)
        );

        $position = $this->getPosition();
        $dstPosition = $position[0];
        $maskPosition = $position[1];

        $maker = new Maker(true);
        $maker->swapPixels($dstPosition, $mask, $imgDst, $imgMask);

        ob_start();
        imagepng($imgDst);
        imagedestroy($imgDst);
        $bgData = ob_get_contents();
        ob_end_clean();

        ob_start();
        imagepng($imgMask);
        imagedestroy($imgMask);
        $maskData = ob_get_contents();
        ob_end_clean();
        imagedestroy($imgBG);

        return [
            $dstPosition,
            [
                'bgBase64' => self::BASE64_HEADER . base64_encode($bgData),
                'bgW' => $this->bgWidth,
                'bgH' => $this->bgHeight,
                'maskBase64' => self::BASE64_HEADER . base64_encode($maskData),
                'maskPath' => $mask['path'],
                'maskLeft' => $maskPosition['left'],
                'maskTop' => $maskPosition['top'],
                'maskViewBox' =>  $mask['viewBox'],
                //Better mask drag to target location display
                'maskWH' => $this->maskWH - 3,

            ]
        ];
    }
    /**
     * Calculate the position, drag the target $dst, and drag the initial position of the mask, Temporary random
     * @return array[]
     */
    private function getPosition(): array
    {
        $dst = [
            'left' => rand( 0, $this->bgWidth - $this->maskWH),
            'top' => rand( 0, $this->bgHeight - $this->maskWH),
        ];

        $mask = [
            'left' => rand( 0, $this->bgWidth - $this->maskWH),
            'top' => rand( 0, $this->bgHeight - $this->maskWH),
        ];

        return [$dst, $mask];
    }
    /**
     * @param array $dst  ['left' => 160, 'top' => 50]
     * @param array $mask  ['left' => 162, 'top' => 51]
     * @return bool
     */
    public  function verify(array $dst, array $mask): bool
    {
        if (! isset($mask['left'], $mask['top'])) {
            return false;
        }
        $answerLeft = $dst['left'];
        $answerTop = $dst['top'];
        $dataLeft = (int)$mask['left'];
        $dataTop = (int)$mask['top'];
        if (abs($answerLeft - $dataLeft) < $this->offset && abs($answerTop - $dataTop) < $this->offset) {
            return true;
        }
        return  false;
    }
    public function score(){
        return rand (config('acaptcha.game_random_show.min'),config('acaptcha.game_random_show.max'));
    }

    public function detectBot($request)
    {
        if(config('acaptcha.robot_detection',false)==false){
            return true;
        }
        // you can send $request->header('User-Agent')
        $userAgent=$request->header('User-Agent');
        // Basic check for common bot user agents
        $bots = [
            'bot', 'crawl', 'slurp', 'spider', 'curl', 'wget', 'java', 'python',
            'httpclient', 'http_request', 'robot', 'fetch', 'postman'
        ];

        foreach ($bots as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return true; // Detected as a robot
            }
        }
        $ip = $request->ip();
        $key = 'rate_limit:' . $ip;
        $limit = 2; // Maximum requests allowed
        $expireTime = 60; // Time frame in seconds

        // Increment request count
        $requestCount = Cache::increment($key);

        // Set expiration if it's the first request
        if ($requestCount === 1) {
            Cache::put($key, $requestCount, $expireTime);
        }

        // Check if limit exceeded
        if ($requestCount > $limit) {
            return true;
        }

        return false; // Not a robot
    }
}
