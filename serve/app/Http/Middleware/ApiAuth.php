<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Ugly\Base\Traits\ApiResource;

class ApiAuth
{
    use ApiResource;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$userType): Response
    {
        $user = $request->user('api');
        if (empty($user)) {
            return $this->failed('未登录', Response::HTTP_UNAUTHORIZED);
        }
        if (empty($user->status)) {
            return $this->failed('账号被禁用');
        }
        if (! empty($userType) && ! in_array(strtolower($user->type->name), $userType)) {
            return $this->failed('无权限访问！', Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
