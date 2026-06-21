<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $projects = Project::with(['skills', 'tags'])->latest('created_at')->take(6)->get();
        $skills = Skill::orderBy('category')->orderByDesc('level')->get()->groupBy('category');
        $admin = User::where('role', 'admin')->first();

        return view('home', compact('projects', 'skills', 'admin'));
    }
}
