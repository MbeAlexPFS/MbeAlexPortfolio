<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SkillController extends Controller
{
    public function index(): View
    {
        $skills = Skill::orderBy('category')->orderByDesc('level')->get()->groupBy('category');

        return view('skills.index', compact('skills'));
    }

    public function adminIndex(): View
    {
        $skills = Skill::latest()->paginate(20);

        return view('admin.skills.index', compact('skills'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'integer', 'min:1', 'max:5'],
            'icon_url' => ['nullable', 'url', 'max:2048'],
            'category' => ['required', 'string', 'max:255'],
        ]);

        Skill::create($data);

        return back()->with('success', 'Compétence ajoutée.');
    }

    public function update(Request $request, Skill $skill): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'integer', 'min:1', 'max:5'],
            'icon_url' => ['nullable', 'url', 'max:2048'],
            'category' => ['required', 'string', 'max:255'],
        ]);

        $skill->update($data);

        return back()->with('success', 'Compétence mise à jour.');
    }

    public function destroy(Skill $skill): RedirectResponse
    {
        $skill->delete();

        return back()->with('success', 'Compétence supprimée.');
    }
}
