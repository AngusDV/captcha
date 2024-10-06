<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

if (!function_exists('a_captcha_verify')) {
    function a_captcha_verify($hashSalt){
        // Define validation rules for the entire string before exploding
        $validator = Validator::make(['hashSalt' => $hashSalt], [
            'hashSalt' => 'required|string|regex:/^[a-f0-9]{64}:[a-zA-Z0-9]{15}$/'
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return false;
        }

        // Now we can safely explode the string
        [$hash, $salt] = explode(':', $hashSalt);
        if(!Cache::has('a-captcha-'.$salt)){
            return false;
        }
        if($hash!=hash('sha256',Cache::get('a-captcha-'.$salt))){
            return false;
        }
        return true;
    }
}

