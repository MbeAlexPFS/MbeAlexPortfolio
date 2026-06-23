<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Article $article): RedirectResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'min:2', 'max:2000'],
        ]);

        $existing = Comment::where('user_id', Auth::id())
            ->where('article_id', $article->id)
            ->first();

        if ($existing) {
            if ($existing->is_published) {
                return back()->with('error', 'Vous avez déjà commenté cet article.');
            }

            return back()->with('error', 'Votre commentaire est en attente de validation.');
        }

        Comment::create([
            'user_id' => Auth::id(),
            'article_id' => $article->id,
            'content' => $data['content'],
            'is_published' => false,
        ]);

        return back()->with('success', 'Commentaire soumis. En attente de modération.');
    }

    public function approve(Comment $comment): RedirectResponse
    {
        $comment->update(['is_published' => true]);

        return back()->with('success', 'Commentaire approuvé.');
    }

    public function reject(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('success', 'Commentaire rejeté et supprimé.');
    }
}
