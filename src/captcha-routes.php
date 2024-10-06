<?php

use AngusDV\Captcha\Controllers\CaptchaController;
use Illuminate\Support\Facades\Route;
Route::as('acaptcha.')->namespace('\AngusDV\Captcha\Controllers')
    ->prefix('acaptcha')->middleware(["web"])->group(function () {
    Route::get('generate', [CaptchaController::class,'generate']);
    Route::post('verify', [CaptchaController::class,'verify']);
    Route::post('verify/code', [CaptchaController::class,'verifyCode']);
});
