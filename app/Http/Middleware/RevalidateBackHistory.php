<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RevalidateBackHistory
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Check if the response is an instance of BinaryFileResponse
        if ($response instanceof BinaryFileResponse) {
            // If it's a BinaryFileResponse, return it as is
            return $response;
        }

        // For other response types, add headers for cache control
        return $response->header('Cache-Control', 'nocache, no-store, max-age=0, must-revalidate')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }
}
