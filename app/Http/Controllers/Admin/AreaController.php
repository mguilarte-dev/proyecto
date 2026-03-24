<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AreaController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Areas/Index', [
            'areas' => Area::withCount(['users', 'courses'])->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:areas',
            'description' => 'nullable|string'
        ]);

        Area::create($request->all());
        return redirect()->back()->with('message', 'Área creada.');
    }

    public function update(Request $request, Area $area)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:areas,name,'.$area->id,
            'description' => 'nullable|string'
        ]);

        $area->update($request->all());
        return redirect()->back()->with('message', 'Área actualizada.');
    }

    public function destroy(Area $area)
    {
        if ($area->users()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar un área con usuarios activos.');
        }
        $area->delete();
        return redirect()->back()->with('message', 'Área eliminada.');
    }
}
