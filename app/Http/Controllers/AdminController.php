<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Formation;
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
            'projects' => Project::count(),
            'skills' => Skill::count(),
            'formations' => Formation::count(),
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
        ];

        $latestMessages = ContactMessage::latest('created_at')->take(5)->get();

        $maintenance = Setting::isMaintenance();

        return view('admin.dashboard', compact('stats', 'latestMessages', 'maintenance'));
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

        $projects = Project::where('type', 'web_static')->latest()->get();

        $formations = Formation::orderBy('year', 'desc')->get();

        $pdf = Pdf::loadView('admin.cv', compact('admin', 'skills', 'projects', 'formations'));

        return $pdf->download('cv-mbe-alex.pdf');
    }
}
