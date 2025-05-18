<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventBackHistory
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if (method_exists($response, 'header')) {
            $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate, private')
                     ->header('Pragma', 'no-cache')
                     ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
        }
        return $response;
    }
}