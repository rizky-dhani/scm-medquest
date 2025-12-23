<?php

namespace App\Http\Middleware;

use Log;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Response;

class HandleForbiddenAccess
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only apply to web requests, not API or AJAX requests
        if ($request->ajax() || $request->wantsJson() || str_starts_with($request->path(), 'api/')) {
            return $next($request);
        }
        
        try {
            $response = $next($request);
            
            // Check if response is a 403 error
            if ($response->getStatusCode() === 403) {
                // Log the forbidden access attempt
                Log::warning('Forbidden access attempt', [
                    'user_id' => Auth::check() ? Auth::id() : null,
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                ]);
                
                // Redirect to logout with 403 flag
                return redirect()->route('logout.403');
            }
            
            return $response;
        } catch (AccessDeniedHttpException $e) {
            // Handle thrown 403 exceptions
            Log::warning('Access denied exception', [
                'user_id' => Auth::check() ? Auth::id() : null,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'message' => $e->getMessage(),
            ]);
            
            // Redirect to logout with 403 flag
            return redirect()->route('logout.403');
        }
    }
}