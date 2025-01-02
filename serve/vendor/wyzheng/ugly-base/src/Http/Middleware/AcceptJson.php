<?php

namespace Ugly\Base\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 设置请求头为 application/json.
 */
class AcceptJson
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
