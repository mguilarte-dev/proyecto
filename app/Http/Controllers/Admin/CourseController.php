<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Area;

class CourseController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Courses/Index', [
            'courses' => Course::with(['category', 'areas'])->get(),
            'categories' => Category::all(),
            'areas' => Area::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'areas' => 'required|array',
            'areas.*' => 'exists:areas,id'
        ]);

        $course = Course::create($request->except('areas'));
        $course->areas()->sync($request->areas);
        
        return redirect()->route('admin.courses.index')->with('message', 'Curso creado con sus áreas.');
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'areas' => 'required|array',
            'areas.*' => 'exists:areas,id'
        ]);

        $course->update($request->except('areas'));
        $course->areas()->sync($request->areas);
        
        return redirect()->route('admin.courses.index')->with('message', 'Curso actualizado con sus áreas.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('message', 'Curso eliminado.');
    }
}
