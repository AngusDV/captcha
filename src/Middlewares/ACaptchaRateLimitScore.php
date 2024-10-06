<?php

namespace AngusDV\Captcha\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ACaptchaRateLimitScore
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $key = 'rate_limit:' . $ip;
        $limit = 5; // Maximum requests allowed
        $expireTime = 60; // Time frame in seconds

        // Increment request count
        $requestCount = Cache::increment($key);

        // Set expiration if it's the first request
        if ($requestCount === 1) {
            Cache::put($key, $requestCount, $expireTime);
        }

        // Check if limit exceeded
        if ($requestCount > $limit) {
            return response()->json(['message' => 'Rate limit exceeded'], 429);
        }

        return $next($request);
    }
}
