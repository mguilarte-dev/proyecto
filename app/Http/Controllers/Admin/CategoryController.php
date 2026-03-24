<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Categories/Index', [
            'categories' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        Category::create($request->only('name', 'description'));
        return redirect()->back()->with('message', 'Categoría creada.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->back()->with('message', 'Categoría eliminada.');
    }
}
