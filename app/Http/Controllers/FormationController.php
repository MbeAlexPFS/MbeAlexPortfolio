<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FormationController extends Controller
{
    public function adminIndex(): View
    {
        $formations = Formation::latest('year')->paginate(20);

        return view('admin.formations.index', compact('formations'));
    }

    public function create(): View
    {
        return view('admin.formations.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'institution' => ['required', 'string', 'max:255'],
            'year' => ['required', 'string', 'size:4'],
            'status' => ['required', 'in:completed,in_progress'],
        ]);

        Formation::create($data);

        return redirect()->route('admin.formations.index')->with('success', 'Formation ajoutée.');
    }

    public function edit(Formation $formation): View
    {
        return view('admin.formations.form', compact('formation'));
    }

    public function update(Request $request, Formation $formation): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'institution' => ['required', 'string', 'max:255'],
            'year' => ['required', 'string', 'size:4'],
            'status' => ['required', 'in:completed,in_progress'],
        ]);

        $formation->update($data);

        return redirect()->route('admin.formations.index')->with('success', 'Formation mise à jour.');
    }

    public function destroy(Formation $formation): RedirectResponse
    {
        $formation->delete();

        return redirect()->route('admin.formations.index')->with('success', 'Formation supprimée.');
    }
}
