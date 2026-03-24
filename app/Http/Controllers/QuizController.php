<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationResult;
use App\Models\Answer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuizController extends Controller
{
    public function show(Evaluation $evaluation)
    {
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
