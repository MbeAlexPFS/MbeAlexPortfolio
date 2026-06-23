<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pseudo' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'pseudo' => $data['pseudo'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'is_verified' => true,
        ]);

        Auth::login($user);

        return to_route('home')->with('success', 'Compte créé avec succès.');
    }

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

        if (! $user || ! $user->is_active) {
            return back()
                ->withErrors(['email' => 'Accès refusé.'])
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

        return to_route('home');
    }

    public function redirectToGoogle(): RedirectResponse
    {
        $params = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/auth?'.$params);
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        $code = $request->get('code');

        if (! $code) {
            return to_route('auth.login')->withErrors([
                'email' => 'Authentification Google annulée.',
            ]);
        }

        $response = Http::post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri' => config('services.google.redirect'),
            'grant_type' => 'authorization_code',
        ]);

        if (! $response->successful()) {
            return to_route('auth.login')->withErrors([
                'email' => 'Erreur lors de l\'échange du token.',
            ]);
        }

        $tokenData = $response->json();
        $userInfo = Http::withToken($tokenData['access_token'])
            ->get('https://www.googleapis.com/oauth2/v2/userinfo')
            ->json();

        if (! isset($userInfo['email'])) {
            return to_route('auth.login')->withErrors([
                'email' => 'Impossible de récupérer les informations.',
            ]);
        }

        $user = User::where('email', $userInfo['email'])->first();

        if ($user) {
            if (! $user->is_active) {
                return to_route('auth.login')->withErrors([
                    'email' => 'Compte désactivé.',
                ]);
            }
            $user->update(['google_id' => $userInfo['id']]);
        } else {
            $user = User::create([
                'pseudo' => $userInfo['name'] ?? explode('@', $userInfo['email'])[0],
                'email' => $userInfo['email'],
                'google_id' => $userInfo['id'],
                'is_verified' => true,
                'role' => 'user',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return to_route('home')->with('success', 'Connecté avec Google.');
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
