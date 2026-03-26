<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LessonController extends Controller
{
    public function index(Course $course)
    {
        return Inertia::render('Admin/Lessons/Index', [
            'course' => $course,
            'lessons' => $course->lessons()->orderBy('lesson_order')->get()
        ]);
    }

    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content_type' => 'required|in:video,pdf,text',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi',
            'pdf_file' => 'nullable|file|mimes:pdf',
            'content_text' => 'nullable|string',
        ]);

        $data = $request->only('title', 'content_type', 'content_text');
        $data['course_id'] = $course->id;
        $data['lesson_order'] = $course->lessons()->count() + 1;

        if ($request->hasFile('video_file') && $request->content_type === 'video') {
            $path = $request->file('video_file')->store('lessons/videos', 'public');
            $data['content_url'] = Storage::url($path);
        } elseif ($request->hasFile('pdf_file') && $request->content_type === 'pdf') {
            $path = $request->file('pdf_file')->store('lessons/pdfs', 'public');
            $data['content_url'] = Storage::url($path);
        }

        Lesson::create($data);

        return redirect()->back()->with('message', 'Lección agregada.');
    }

    public function edit(Lesson $lesson)
    {
        return Inertia::render('Admin/Lessons/Edit', [
            'lesson' => $lesson,
            'course' => $lesson->course
        ]);
    }

    public function update(Request $request, Lesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content_type' => 'required|in:video,pdf,text',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi',
            'pdf_file' => 'nullable|file|mimes:pdf',
            'content_text' => 'nullable|string',
        ]);

        $data = $request->only('title', 'content_type', 'content_text');

        // Handle file uploads
        if ($request->hasFile('video_file') && $request->content_type === 'video') {
            // Delete old file if exists
            if ($lesson->content_url && $lesson->content_type === 'video') {
                $oldPath = str_replace('/storage/', '', $lesson->content_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('video_file')->store('lessons/videos', 'public');
            $data['content_url'] = Storage::url($path);
        } elseif ($request->hasFile('pdf_file') && $request->content_type === 'pdf') {
            // Delete old file if exists
            if ($lesson->content_url && $lesson->content_type === 'pdf') {
                $oldPath = str_replace('/storage/', '', $lesson->content_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('pdf_file')->store('lessons/pdfs', 'public');
            $data['content_url'] = Storage::url($path);
        } elseif ($request->content_type === 'text') {
            // If changing to text, remove old file
            if ($lesson->content_url && in_array($lesson->content_type, ['video', 'pdf'])) {
                $oldPath = str_replace('/storage/', '', $lesson->content_url);
                Storage::disk('public')->delete($oldPath);
                $data['content_url'] = null;
            }
        }

        $lesson->update($data);

        return redirect()->route('admin.courses.lessons.index', $lesson->course)->with('message', 'Lección actualizada.');
    }

    public function destroy(Lesson $lesson)
    {
        // Delete file if exists
        if ($lesson->content_url) {
            $path = str_replace('/storage/', '', $lesson->content_url);
            Storage::disk('public')->delete($path);
        }
        
        $lesson->delete();
        return redirect()->back()->with('message', 'Lección eliminada.');
    }
}
