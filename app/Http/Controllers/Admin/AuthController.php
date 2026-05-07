<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('afayar_admin_authenticated', false)) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $adminEmail = env('ADMIN_EMAIL', 'admin@afayar.com');
        $adminPassword = env('ADMIN_PASSWORD', 'hamza@admin@2026');

        if ($credentials['email'] !== $adminEmail || $credentials['password'] !== $adminPassword) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors(['email' => 'The email or password is incorrect.']);
        }

        $request->session()->put('afayar_admin_authenticated', true);
        $request->session()->put('afayar_admin_email', $adminEmail);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')->with('success', 'Welcome to the Afayar admin panel.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['afayar_admin_authenticated', 'afayar_admin_email']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been logged out successfully.');
    }
}
