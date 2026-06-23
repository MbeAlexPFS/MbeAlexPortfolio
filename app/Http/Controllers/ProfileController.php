<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = Auth::user();
        $answers = $user
            ->answers()
            ->with(['question.poll', 'options'])
            ->latest()
            ->get();

        return view('profile.show', compact('user', 'answers'));
    }

    public function updatePseudo(Request $request): RedirectResponse
    {
        $request->validate([
            'pseudo' => ['required', 'string', 'max:255'],
        ]);

        Auth::user()->update(['pseudo' => $request->pseudo]);

        return back()->with('success', 'Pseudo mis à jour.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Mot de passe mis à jour.');
    }

    public function updateNewsletters(Request $request): RedirectResponse
    {
        $request->validate([
            'newsletter_articles' => ['boolean'],
            'newsletter_polls' => ['boolean'],
        ]);

        Auth::user()->update([
            'newsletter_articles' => $request->boolean('newsletter_articles'),
            'newsletter_polls' => $request->boolean('newsletter_polls'),
        ]);

        return back()->with(
            'success',
            'Préférences de newsletter mises à jour.',
        );
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048',
            ],
        ]);

        $user = Auth::user();

        if ($user->avatar_url) {
            Storage::delete($user->avatar_url);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar_url' => $path]);

        return back()->with('success', 'Photo de profil mise à jour.');
    }

    public function deleteAvatar(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->avatar_url) {
            Storage::delete($user->avatar_url);
            $user->update(['avatar_url' => null]);
        }

        return back()->with('success', 'Photo de profil supprimée.');
    }
}
