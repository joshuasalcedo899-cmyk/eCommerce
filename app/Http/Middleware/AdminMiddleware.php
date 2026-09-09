<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            $previousUrl = url()->previous();
            $previousPath = parse_url($previousUrl, PHP_URL_PATH) ?? '';

            if ($previousUrl === $request->fullUrl() || str_starts_with($previousPath, '/admin')) {
                return redirect()->route('store.index');
            }

            return redirect()->to($previousUrl);
        }

        return $next($request);
    }
}