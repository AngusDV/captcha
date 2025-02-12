<?php

namespace AngusDV\Captcha\Controllers;

use AngusDV\Captcha\Drag;
use AngusDV\Captcha\Requests\VerifyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CaptchaController
{
    public function generate(Request $request)
    {
        $drag = new Drag();
        [$dst, $font] = $drag->generate();
        if (!$drag->detectBot($request)) {
            $salt = Str::random(15);
            $randomStr = Str::random(32);
            $hash = hash('sha256', $randomStr);
            if (!config('acaptcha.game')) {
                Cache::put('a-captcha-' . $salt, $randomStr, 20);
                $font["game_end"] = 1;
            }
            $font["hash"] = $hash . ':' . $salt;
            return response()->json($font);
        }
        if (Session::has('a-captcha')) {
            $session = json_decode(Session::get('a-captcha'), true);
            if (config('acaptcha.game')) {
                if (!isset($session["game"]) || (isset($session["game"]) && $session["game"] == 0)) {
                    $session["game"] = $drag->score();
                }
                $dst["game"] = ((int)$session["game"]) - 1;
                if ($dst["game"] <= 0) $dst["game"] = 0;
            }
        } else {
            if (config('acaptcha.game')) {
                $dst["game"] = $drag->score();
            }
        }
        Session::put('a-captcha', json_encode($dst));
        return response()->json($font);
    }

    public function verify(VerifyRequest $request)
    {
        $res = ['hash' => 0];
        $mask = $request->mask;
        if (!Session::has('a-captcha')) {
            return response()->json($res);
        }
        $dst = json_decode(Session::get('a-captcha'), true);
        $game = 0;
        if (config('acaptcha.game')) {
            $game = $dst["game"];
        }
        if ((new Drag)->verify($dst, $mask)) {
            $salt = Str::random(15);
            $randomStr = Str::random(32);
            $hash = hash('sha256', $randomStr);
            $res = ['hash' => $hash . ':' . $salt];
            if ($game == 0) {
                Cache::put('a-captcha-' . $salt, $randomStr, 20);
                $res["game_end"] = 1;
            }
        } else {
            if (Session::has('a-captcha')) {
                if (config('acaptcha.game')) {
                    $dst["game"] = (new Drag)->score();
                }
                Session::put('a-captcha', json_encode($dst));
            }
        }
        return response()->json($res);
    }

}
