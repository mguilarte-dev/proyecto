<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationResult;
use App\Models\Answer;
use App\Models\ProgressLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Enrollment;

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

        // Verificar número de intentos (máximo 5)
        $attemptCount = EvaluationResult::where('user_id', $user->id)
            ->where('evaluation_id', $evaluation->id)
            ->count();

        // Verificar si ya aprobó la evaluación (obtener el de mejor puntaje)
        $approvedResult = EvaluationResult::where('user_id', $user->id)
            ->where('evaluation_id', $evaluation->id)
            ->where('passed', true)
            ->orderByDesc('score')
            ->first();

        if ($approvedResult) {
            return Inertia::render('Empleado/QuizBlocked', [
                'evaluation' => $evaluation,
                'course' => $course,
                'message' => 'Ya has aprobado esta evaluación. No es necesario intentarla de nuevo.',
                'isPassed' => true,
                'score' => $approvedResult->score
            ]);
        }

        $maxAttempts = 5;
        $remainingAttempts = max(0, $maxAttempts - $attemptCount);

        // Si ya agotó los intentos, bloquear acceso
        if ($attemptCount >= $maxAttempts) {
            return Inertia::render('Empleado/QuizBlocked', [
                'evaluation' => $evaluation,
                'course' => $course,
                'message' => 'Has agotado el número máximo de intentos permitidos (5) para esta evaluación. Contacta con tu administrador si necesitas más información.',
                'isAttemptsExhausted' => true,
                'totalAttempts' => $attemptCount,
                'maxAttempts' => $maxAttempts
            ]);
        }

        $evaluation->load('questions.answers');
        return Inertia::render('Empleado/QuizView', [
            'evaluation' => $evaluation,
            'attemptCount' => $attemptCount,
            'maxAttempts' => $maxAttempts,
            'remainingAttempts' => $remainingAttempts
        ]);
    }

    public function submit(Request $request, Evaluation $evaluation)
    {
        $user = auth()->user();

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|exists:answers,id'
        ]);

        // Validar que no haya agotado los intentos
        $attemptCount = EvaluationResult::where('user_id', $user->id)
            ->where('evaluation_id', $evaluation->id)
            ->count();

        $maxAttempts = 5;
        if ($attemptCount >= $maxAttempts) {
            return response()->json([
                'error' => 'Has agotado el número máximo de intentos permitidos para esta evaluación.'
            ], 403);
        }

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

        // Crear registro con número de intento
        $attemptNumber = $attemptCount + 1;
        EvaluationResult::create([
            'user_id' => $user->id,
            'evaluation_id' => $evaluation->id,
            'score' => round($score, 2),
            'passed' => $passed,
            'attempt_number' => $attemptNumber,
            'attempted_at' => now()
        ]);

        // Actualizar enrollment a 'completado' cuando se aprueba la evaluación
        if ($passed) {
            Enrollment::where('user_id', $user->id)
                ->where('course_id', $evaluation->course_id)
                ->update([
                    'status' => 'completado',
                    'completed_at' => now()
                ]);
        }

        return redirect()->back()->with('quiz_result', [
            'score' => round($score, 2),
            'passed' => $passed,
            'attempt_number' => $attemptNumber,
            'remaining_attempts' => max(0, $maxAttempts - $attemptNumber)
        ]);
    }
}
