<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Pastikan pengguna login dan memiliki role super_admin
        if ($user && $user->role === 'super_admin') {
            return $next($request);
        }

        // Jika bukan super_admin, kembalikan respons unauthorized
        return response()->json(['message' => 'Access denied. You are not a super admin.'], 403);
    }
}
