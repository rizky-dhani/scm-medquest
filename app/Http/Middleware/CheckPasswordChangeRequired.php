<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordChangeRequired
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip middleware entirely for non-authenticated users
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Skip check for AJAX/Livewire requests and asset requests
        if ($request->ajax() || 
            $request->header('X-Livewire') || 
            $request->header('X-Requested-With') === 'XMLHttpRequest' ||
            $request->isMethod('POST') && str_contains($request->header('Content-Type', ''), 'application/json') ||
            str_starts_with($request->path(), 'livewire/') ||
            str_contains($request->path(), 'livewire') ||
            str_contains($request->path(), 'assets/') ||
            str_contains($request->path(), 'css/') ||
            str_contains($request->path(), 'js/')) {
            return $next($request);
        }
        
        // Skip check if user is already on password change pages or logging out
        $currentRoute = $request->route()?->getName();
        $currentPath = $request->path();
        
        // Define allowed routes/paths for users who need password change
        $allowedRoutes = [
            'filament.dashboard.auth.profile',
            'filament.dashboard.pages.first-time-password-change',
            'filament.dashboard.pages.edit-profile',
            'filament.dashboard.auth.logout',
            'logout'
        ];
        
        $allowedPaths = [
            'profile',
            'first-time-password-change'
        ];
        
        if (in_array($currentRoute, $allowedRoutes) || 
            in_array($currentPath, $allowedPaths)) {
            return $next($request);
        }

        // If user requires password change, redirect to first-time password change page
        if ($user->requiresPasswordChange()) {
            return redirect()->route('filament.dashboard.pages.first-time-password-change')
                ->with('password_change_required', 'You must change your password before continuing to use the system.');
        }

        return $next($request);
    }
}
