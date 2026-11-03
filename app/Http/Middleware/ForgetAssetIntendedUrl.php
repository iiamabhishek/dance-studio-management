<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Discards stale "intended URL" session entries that point at static
 * assets. Laravel's auth middleware records whatever URL the visitor
 * requested as the post-login destination; when that entry is a JS/CSS
 * asset (e.g. recorded from a preload request on an old session),
 * Filament's `redirect()->intended()` would land the user on the raw
 * asset instead of the dashboard.
 */
class ForgetAssetIntendedUrl
{
    private const ASSET_PATTERN = '#\.(js|css|png|jpe?g|svg|gif|ico|woff2?|ttf|map)(\?.*)?$#';

    /**
     * @param  \Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasSession()) {
            $intended = $request->session()->get('url.intended');

            if (is_string($intended) && preg_match(self::ASSET_PATTERN, $intended)) {
                $request->session()->forget('url.intended');
            }
        }

        return $next($request);
    }
}
