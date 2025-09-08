<?php

return [
    'language'=>env('ACAPTCHA_LANGUAGE','en'),
    /*
     * If this feature is enabled,
     * a rate limit is applied based on IP, allowing 2 requests per IP every 60 seconds.
     * Otherwise, a CAPTCHA code is displayed. Additionally, the user agent of the request is
     * checked to determine if it was sent by a bot.
     */
    'robot_detection'=>env('ACAPTCHA_ROBOT_DETECTION',false),

    /*
     * You can enable a game ,
     * allowing users to play while entering the CAPTCHA.
     * This extends the time they spend interacting with the CAPTCHA,
     * making the process more engaging.
     */
    'game'=>env('ACAPTCHA_GAME',true),
    'game_random_show'=>[
        "min"=>env('ACAPTCHA_GAME_RANDOM_MIN',1),
        "max"=>env('ACAPTCHA_GAME_RANDOM_MAX',5)
    ],
    'background_width'=>env('ACAPTCHA_BACKGROUND_WIDTH',250),
    'background_height'=>env('ACAPTCHA_BACKGROUND_HEIGHT',160),
    /*
     * mask_width_height=60*60 pixel
     */
    'mask_width_height'=>env('ACAPTCHA_MASK_WIDTH_HEIGHT',60),
    /*
     * When the 'confuse' option is activated,
     * it gracefully transforms the path of the masked image in the background,
     * ensuring that the content remains undetectable by OCR.
     */
    'confuse'=>env('ACAPTCHA_ROBOT_CONFUSE',true),
    /*
     * A smaller offset indicates higher accuracy, meaning the user has successfully
     * dragged the element to the intended target area
     */
    'offset'=>env('ACAPTCHA_OFFSET',30),

    /*
     *  After publishing the assets, you can access them from the following path:
     * public/vendor/acaptcha/assets/images
     * Make sure to run the vendor:publish command to ensure the assets are copied
     * to the public directory, where they can be accessed by your application.
     */
    'background_images'=>[
         'bg/1.png',
         'bg/2.png',
         'bg/3.png',
         'bg/4.png',
         'bg/5.png',
    ],
    'mask_images'=>[
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
    ],
    'middlewares'=>env('ACAPTCHA_MIDDLEWARES',[
        'web'
    ])

];
