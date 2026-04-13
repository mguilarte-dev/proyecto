<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationResult;
use App\Models\Answer;
use App\Models\ProgressLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuizController extends Controller
{
    public function show(Evaluation $evaluation)
    {
        $user = auth()->user();

        // Verificar que el usuario haya completado todas las lecciones del curso
        $course = $evaluation->course;
        $totalLessons = $course->lessons()->count();

        $completedLessonsCount = ProgressLog::where('user_id', $user->id)
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->where('is_completed', true)
            ->count();

        // Si no ha completado todas las lecciones, no permitir acceso al quiz
        if ($completedLessonsCount < $totalLessons) {
            $completedLessonIds = ProgressLog::where('user_id', $user->id)
                ->whereIn('lesson_id', $course->lessons->pluck('id'))
                ->where('is_completed', true)
                ->pluck('lesson_id')
                ->toArray();

            return Inertia::render('Empleado/QuizBlocked', [
                'evaluation' => $evaluation,
                'course' => $course->load('lessons'),
                'totalLessons' => $totalLessons,
                'completedLessons' => $completedLessonsCount,
                'completedLessonIds' => $completedLessonIds,
                'message' => 'Debes visualizar todo el material suministrado (lecciones del curso) antes de poder realizar la evaluación. Completa todas las clases para desbloquear el examen.'
            ]);
        }

        $evaluation->load('questions.answers');
        return Inertia::render('Empleado/QuizView', [
            'evaluation' => $evaluation
        ]);
    }

    public function submit(Request $request, Evaluation $evaluation)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|exists:answers,id'
        ]);

        $totalQuestions = $evaluation->questions()->count();
        $correctAnswersCount = 0;

        foreach ($request->answers as $answerId) {
            $answer = Answer::find($answerId);
            if ($answer && $answer->is_correct) {
                $correctAnswersCount++;
            }
        }

        $score = ($totalQuestions > 0) ? ($correctAnswersCount / $totalQuestions) * 100 : 0;
        $passed = $score >= $evaluation->min_score;

        EvaluationResult::create([
            'user_id' => auth()->id(),
            'evaluation_id' => $evaluation->id,
            'score' => round($score, 2),
            'passed' => $passed,
            'attempted_at' => now()
        ]);

        return redirect()->back()->with('quiz_result', [
            'score' => round($score, 2),
            'passed' => $passed,
        ]);
    }
}
