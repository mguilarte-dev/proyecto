<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\EvaluationResult;
use App\Models\Course;

class GerenteController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        $departmentMetrics = EvaluationResult::query()
            ->selectRaw('areas.name as department, AVG(evaluation_results.score) as avg_score')
            ->join('users', 'evaluation_results.user_id', '=', 'users.id')
            ->join('areas', 'users.area_id', '=', 'areas.id')
            ->where('users.area_id', $user->area_id)
            ->groupBy('areas.name')
            ->orderBy('areas.name')
            ->get();

        $courseMetrics = Course::query()
            ->select('courses.id', 'courses.title')
            ->withCount(['enrollments as total_enrolled' => function ($query) use ($user) {
                $query->whereHas('user', function ($query) use ($user) {
                    $query->where('area_id', $user->area_id);
                });
            }])
            ->get()
            ->map(function ($course) use ($user) {
                $avgScore = EvaluationResult::query()
                    ->selectRaw('AVG(evaluation_results.score) as avg_score')
                    ->join('evaluations', 'evaluation_results.evaluation_id', '=', 'evaluations.id')
                    ->join('users', 'evaluation_results.user_id', '=', 'users.id')
                    ->where('evaluations.course_id', $course->id)
                    ->where('users.area_id', $user->area_id)
                    ->value('avg_score');

                $completions = $course->enrollments()->whereHas('user', function ($query) use ($user) {
                    $query->where('area_id', $user->area_id);
                })->whereIn('status', ['completado', 'completed'])->count();

                $completionRate = $course->total_enrolled ? round(($completions / $course->total_enrolled) * 100, 2) : 0;

                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'total_enrolled' => $course->total_enrolled,
                    'avg_score' => round($avgScore ?? 0, 2),
                    'completion_rate' => $completionRate,
                ];
            });

        return Inertia::render('Gerente/Dashboard', [
            'departmentMetrics' => $departmentMetrics,
            'courseMetrics' => $courseMetrics
        ]);
    }

    public function results()
    {
        $user = auth()->user();

        $evaluationResults = EvaluationResult::with(['user.area', 'evaluation.course'])
            ->whereHas('user', function ($query) use ($user) {
                $query->where('area_id', $user->area_id)
                      ->where('role', 'empleado');
            })
            ->orderByDesc('attempted_at')
            ->get()
            ->map(function ($result) {
                return [
                    'id' => $result->id,
                    'employee' => [
                        'name' => $result->user->name,
                        'email' => $result->user->email,
                        'area' => $result->user->area?->name,
                    ],
                    'evaluation' => [
                        'title' => $result->evaluation->title,
                        'course' => $result->evaluation->course?->title,
                    ],
                    'score' => $result->score,
                    'passed' => $result->passed,
                    'attempted_at' => $result->attempted_at,
                ];
            });

        return Inertia::render('Gerente/Results', [
            'evaluationResults' => $evaluationResults,
        ]);
    }
}
