<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class ResponseCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $subjectCodes = [$request->input('occupation_1'), $request->input('occupation_2')];
        
        asort($subjectCodes);

        $key = 'request|' . $request->url() . '|' . implode($subjectCodes);

        return Cache::remember($key, 300, function () use ($next, $request) {
            return $next($request);
        });
    }
}
