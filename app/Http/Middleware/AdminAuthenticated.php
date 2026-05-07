<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('afayar_admin_authenticated', false)) {
            return redirect()
                ->route('admin.login')
                ->with('error', 'Please log in to access the admin panel.');
        }

        return $next($request);
    }
}
