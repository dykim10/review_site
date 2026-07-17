<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdminPasswordConfirmed
{
    /** 세션 유효 시간 (초) — 8시간 */
    private const TTL = 28800;

    public function handle(Request $request, Closure $next)
    {
        $confirmedAt = session('admin_password_confirmed_at');

        $confirmed = $confirmedAt && (now()->timestamp - $confirmedAt) < self::TTL;

        if (! $confirmed) {
            session(['url.intended' => $request->url()]);

            return redirect()->route('admin.password.confirm');
        }

        return $next($request);
    }
}
