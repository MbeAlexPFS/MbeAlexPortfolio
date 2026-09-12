<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! $user->isAdmin() || ! $user->is_active) {
            return back()
                ->withErrors(['email' => 'Accès réservé à l\'administrateur.'])
                ->onlyInput('email');
        }

        if (
            ! Auth::attempt(
                ['email' => $data['email'], 'password' => $data['password']],
                $request->boolean('remember'),
            )
        ) {
            return back()
                ->withErrors(['email' => 'Identifiants incorrects.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return to_route('admin.dashboard');
    }

    public function maintenanceLogin(Request $request): RedirectResponse
    {
        if (! Setting::isMaintenance()) {
            return to_route('home');
        }

        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! $user->isAdmin() || ! $user->is_active) {
            return back()->withErrors(['email' => 'Accès réservé à l\'administrateur.']);
        }

        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return back()->withErrors(['email' => 'Identifiants incorrects.']);
        }

        $request->session()->regenerate();

        return to_route('admin.dashboard')->with('success', 'Bienvenue ! Maintenance active.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('home');
    }
}
