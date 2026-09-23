<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * API har doim JSON qaytarsin: validatsiya xatolari 302 redirect (HTML sahifa)
 * o'rniga 422 JSON bo'lib qaytadi, Accept sarlavhasi yuborilmagan bo'lsa ham.
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
