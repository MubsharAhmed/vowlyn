<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class RequireVerifiedUploader
{
    public function handle(Request $request, Closure $next): mixed
    {
        abort_unless($request->user()?->email_verified_at !== null, 403);

        return $next($request);
    }
}
