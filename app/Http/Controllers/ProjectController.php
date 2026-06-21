<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Skill;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::with(['skills', 'tags'])
            ->latest('created_at')
            ->paginate(12);

        $types = ['web_static', 'web_dynamic', 'web_live', 'design', 'affiche', 'logo', 'montage_video'];

        return view('projects.index', compact('projects', 'types'));
    }

    public function show(Project $project): View
    {
        return view('projects.show', [
            'project' => $project->load(['skills', 'tags']),
        ]);
    }

    public function adminIndex(): View
    {
        $projects = Project::with(['skills', 'tags'])->latest('created_at')->paginate(20);

        return view('admin.projects.index', compact('projects'));
    }

    public function create(): View
    {
        $skills = Skill::all();
        $tags = Tag::all();
        $types = ['web_static', 'web_dynamic', 'web_live', 'design', 'affiche', 'logo', 'montage_video'];

        return view('admin.projects.form', compact('skills', 'tags', 'types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', 'in:web_static,web_dynamic,web_live,design,affiche,logo,montage_video'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'github_url' => ['nullable', 'url', 'max:2048'],
            'live_url' => ['nullable', 'url', 'max:2048'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ]);

        $project = Project::create($data);

        if (!empty($data['skills'])) {
            $project->skills()->attach($data['skills']);
        }
        if (!empty($data['tags'])) {
            $project->tags()->attach($data['tags']);
        }

        return to_route('admin.projects.index')->with('success', 'Projet créé avec succès.');
    }

    public function edit(Project $project): View
    {
        $project->load(['skills', 'tags']);
        $skills = Skill::all();
        $tags = Tag::all();
        $types = ['web_static', 'web_dynamic', 'web_live', 'design', 'affiche', 'logo', 'montage_video'];

        return view('admin.projects.form', compact('project', 'skills', 'tags', 'types'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', 'in:web_static,web_dynamic,web_live,design,affiche,logo,montage_video'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'github_url' => ['nullable', 'url', 'max:2048'],
            'live_url' => ['nullable', 'url', 'max:2048'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ]);

        $project->update($data);

        $project->skills()->sync($data['skills'] ?? []);
        $project->tags()->sync($data['tags'] ?? []);

        return to_route('admin.projects.index')->with('success', 'Projet mis à jour.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return back()->with('success', 'Projet supprimé.');
    }
}
