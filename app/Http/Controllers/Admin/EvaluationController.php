<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Evaluation;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    public function index(Course $course)
    {
        return Inertia::render('Admin/Evaluations/Index', [
            'course' => $course,
            'evaluations' => $course->evaluations()->with('questions.answers')->get()
        ]);
    }

    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'min_score' => 'required|numeric|min:0|max:100',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.answers' => 'required|array|min:2',
            'questions.*.answers.*.text' => 'required|string',
            'questions.*.answers.*.is_correct' => 'required|boolean',
        ]);

        DB::transaction(function () use ($request, $course) {
            $evaluation = Evaluation::create([
                'course_id' => $course->id,
                'title' => $request->title,
                'min_score' => $request->min_score
            ]);

            foreach ($request->questions as $qData) {
                $question = Question::create([
                    'evaluation_id' => $evaluation->id,
                    'question_text' => $qData['text'],
                    'question_type' => 'multiple_choice'
                ]);

                foreach ($qData['answers'] as $aData) {
                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => $aData['text'],
                        'is_correct' => $aData['is_correct']
                    ]);
                }
            }
        });

        return redirect()->back()->with('message', 'Evaluación creada exitosamente.');
    }

    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();
        return redirect()->back()->with('message', 'Evaluación eliminada.');
    }
}
