<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;

class NewsletterController extends Controller
{
    public function unsubscribeArticles(User $user): RedirectResponse
    {
        if (!URL::hasValidSignature(request())) {
            abort(401);
        }

        $user->update(['newsletter_articles' => false]);

        return to_route('home')->with('success', 'Vous êtes désabonné des articles.');
    }

    public function unsubscribePolls(User $user): RedirectResponse
    {
        if (!URL::hasValidSignature(request())) {
            abort(401);
        }

        $user->update(['newsletter_polls' => false]);

        return to_route('home')->with('success', 'Vous êtes désabonné des sondages.');
    }
}
