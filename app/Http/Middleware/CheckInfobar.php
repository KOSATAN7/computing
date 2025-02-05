<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInfobar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Periksa apakah pengguna memiliki peran super_admin
        if (!$user || $user->role !== 'infobar') {
            return response()->json([
                'message' => 'Akses ditolak. Hanya super_admin yang dapat melakukan aksi ini.',
            ], 403);
        }
        

        return $next($request);
    }
}
