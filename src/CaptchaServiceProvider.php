<?php

namespace AngusDV\Captcha;

use AngusDV\Captcha\Rules\ACaptchaVerify;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class CaptchaServiceProvider extends ServiceProvider
{
    protected $except = [
        "/acaptcha/verify"
    ];
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/', 'ACaptcha');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ACaptcha');
        $this->publishes([
            __DIR__.'/../resources' => public_path('vendor/acaptcha'),
        ], 'acaptcha');
        $this->loadRoutesFrom(__DIR__ . '/captcha-routes.php');
        Validator::extend('a_captcha_verify', ACaptchaVerify::class);

    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        if (!file_exists(config_path('acaptcha.php'))) {
            $this->mergeConfigFrom(__DIR__ . '/../config/acaptcha.php', 'acaptcha');
        }else{
            $this->mergeConfigFrom(config_path('acaptcha.php'), 'acaptcha');
        }
        $this->publishes([
            __DIR__ . '/../config/acaptcha.php' => config_path('acaptcha.php'),
        ]);
    }
}
