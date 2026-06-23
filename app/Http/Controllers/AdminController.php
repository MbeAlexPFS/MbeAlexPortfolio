<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\Formation;
use App\Models\Poll;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $stats = [
            'users' => User::count(),
            'articles' => Article::count(),
            'pending_comments' => Comment::where('is_published', false)->count(),
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
            'active_polls' => Poll::where('is_active', true)->count(),
        ];

        $latestMessages = ContactMessage::latest('created_at')->take(5)->get();
        $pendingComments = Comment::with('user', 'article')->where('is_published', false)->latest('created_at')->take(5)->get();

        $maintenance = Setting::isMaintenance();

        return view('admin.dashboard', compact('stats', 'latestMessages', 'pendingComments', 'maintenance'));
    }

    public function users(): View
    {
        $users = User::latest()->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function toggleUserActive(User $user): RedirectResponse
    {
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Impossible de désactiver le dernier administrateur.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', 'Statut de l\'utilisateur mis à jour.');
    }

    public function editProfile(): View
    {
        $user = Auth::user();

        return view('admin.profile', compact('user'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'headline' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'social_links' => ['nullable', 'array', 'max:10'],
            'social_links.*.platform' => ['required', 'string', 'max:50'],
            'social_links.*.url' => ['required', 'url', 'max:2048'],
        ]);

        Auth::user()->update($data);

        return to_route('admin.profile.edit')->with('success', 'Profil mis à jour.');
    }

    public function toggleMaintenance(): RedirectResponse
    {
        $active = Setting::toggleMaintenance();

        return back()->with('success', $active
            ? 'Mode maintenance activé.'
            : 'Mode maintenance désactivé.');
    }

    public function cv(): Response
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $skills = Skill::get()->groupBy(function ($skill) {
            return $skill->category ?? 'Autres';
        });

        $projects = Project::latest()->get();

        $formations = Formation::orderBy('year', 'desc')->get();

        $pdf = Pdf::loadView('admin.cv', compact('admin', 'skills', 'projects', 'formations'));

        return $pdf->download('cv-mbe-alex.pdf');
    }
}
