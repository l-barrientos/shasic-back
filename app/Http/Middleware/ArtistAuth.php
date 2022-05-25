<?php

namespace App\Http\Middleware;

use App\Models\Artist;
use Closure;
use Illuminate\Http\Request;

class ArtistAuth {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {
        $artist = Artist::where('access_token', $request->header('access_token'))->first();
        if ($artist == null) {
            return response('Failed on Authentication', 403);
        } else {
            return $next($request);
        }
    }
}
