<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\ProgressLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmpleadoController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        // Get courses assigned to the user's area (unless admin, but this is EmpleadoController)
        $courses = Course::with(['category', 'evaluations', 'areas'])
            ->withCount('lessons')
            ->where('active', true)
            ->whereHas('areas', function($query) use ($user) {
                $query->where('areas.id', $user->area_id);
            })
            ->get();
        
        // Get user enrollments
        $enrollments = $user->enrollments()->pluck('course_id')->toArray();

        // Calculate progress and completion for each course
        $courseProgress = [];
        $userId = $user->id;

        foreach ($courses as $course) {
            if (in_array($course->id, $enrollments)) {
                $totalLessons = $course->lessons_count;
                $percentage = 0;
                
                if ($totalLessons > 0) {
                    $completedCount = ProgressLog::where('user_id', $userId)
                        ->whereIn('lesson_id', function($query) use ($course) {
                            $query->select('id')->from('lessons')->where('course_id', $course->id);
                        })
                        ->where('is_completed', true)
                        ->count();
                    $percentage = round(($completedCount / $totalLessons) * 100);
                }

                // Check evaluations
                $hasEvaluations = $course->evaluations->count() > 0;
                $passedEvaluations = false;
                if ($hasEvaluations) {
                    $passedEvaluations = auth()->user()->evaluationResults()
                        ->whereIn('evaluation_id', $course->evaluations->pluck('id'))
                        ->where('passed', true)
                        ->exists();
                }

                // Final completion logic
                $isFullyCompleted = ($percentage >= 100);
                if ($hasEvaluations) {
                    $isFullyCompleted = $isFullyCompleted && $passedEvaluations;
                }

                $courseProgress[$course->id] = [
                    'percentage' => $percentage,
                    'is_completed' => $isFullyCompleted,
                    'has_evaluations' => $hasEvaluations,
                    'passed_evaluations' => $passedEvaluations
                ];
            }
        }

        return Inertia::render('Empleado/Dashboard', [
            'courses' => $courses,
            'myEnrollments' => $enrollments,
            'courseProgress' => $courseProgress
        ]);
    }

    public function showCourse(Course $course)
    {
        $course->load('evaluations');
        $lessons = $course->lessons()->orderBy('lesson_order')->get();
        
        // Get completed lessons for this user in this course
        $completedLessons = auth()->user()->progressLogs()
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        // Ensure user is enrolled or enroll them automatically for demo
        $enrollment = auth()->user()->enrollments()->firstOrCreate(
            ['course_id' => $course->id],
            ['status' => 'en_progreso', 'enrolled_at' => now()]
        );

        // Get evaluation results for this user in this course
        $evaluationResults = auth()->user()->evaluationResults()
            ->whereIn('evaluation_id', $course->evaluations->pluck('id'))
            ->get()
            ->groupBy('evaluation_id')
            ->map(function ($results) {
                return $results->sortByDesc('score')->first();
            });

        return Inertia::render('Empleado/CourseView', [
            'course' => $course,
            'lessons' => $lessons,
            'enrollment' => $enrollment,
            'completedLessons' => $completedLessons,
            'evaluationResults' => $evaluationResults
        ]);
    }

    public function showLesson(Course $course, Lesson $lesson)
    {
        // Record access/progress
        ProgressLog::updateOrCreate(
            ['user_id' => auth()->id(), 'lesson_id' => $lesson->id],
            ['last_accessed' => now()]
        );

        return Inertia::render('Empleado/LessonPlayer', [
            'course' => $course,
            'lesson' => $lesson,
            'allLessons' => $course->lessons()->orderBy('lesson_order')->get()
        ]);
    }

    public function completeLesson(Request $request, Lesson $lesson)
    {
        ProgressLog::updateOrCreate(
            ['user_id' => auth()->id(), 'lesson_id' => $lesson->id],
            ['is_completed' => true, 'last_accessed' => now()]
        );

        return redirect()->back()->with('message', '¡Lección completada!');
    }
}
