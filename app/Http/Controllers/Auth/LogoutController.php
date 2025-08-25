<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class LogoutController extends Controller
{
    /**
     * Handle logout specifically for 403 Forbidden errors.
     * This method handles both GET and POST requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logoutFrom403(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('filament.admin.auth.login')
            ->with('error', 'Access denied. You don\'t have permission to access that resource. Please log in again or contact your administrator.');
    }
}