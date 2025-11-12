<?php

namespace App\Http\Controllers;

use App\Models\CBTTest;
use App\Models\Question;
use App\Models\Attempt;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TestController extends Controller
{
    public function getQuestions($id)
    {
        $test = CBTTest::findOrFail($id);
        $questions = Question::where('test_id', $id)
            ->select('id', 'question_text', 'stimulus', 'stimulus_type')
            ->with('answers:id,question_id,answer_text')
            ->get();

        return response()->json($questions);
    }

    public function startAttempt($id)
    {
        $test = CBTTest::findOrFail($id);
        
        $attempt = new Attempt();
        $attempt->id = (string) Str::uuid();
        $attempt->test_id = $id;
        $attempt->status = 'in_progress';
        $attempt->save();

        return response()->json([
            'attemptId' => $attempt->id
        ]);
    }

    public function submitAnswer(Request $request)
    {
        $validated = $request->validate([
            'attemptId' => 'required|string',
            'questionId' => 'required',
            'answer' => 'required|string'
        ]);

        $attempt = Attempt::findOrFail($validated['attemptId']);
        
        $attempt->answers()->updateOrCreate(
            ['question_id' => $validated['questionId']],
            ['answer_text' => $validated['answer']]
        );

        return response()->json(['success' => true]);
    }

    public function finishAttempt($attemptId)
    {
        $attempt = Attempt::findOrFail($attemptId);
        $attempt->status = 'completed';
        $attempt->completed_at = now();
        $attempt->save();

        // Calculate score
        $test = $attempt->test;
        $totalQuestions = $test->questions()->count();
        $correctAnswers = 0;

        foreach ($attempt->answers as $answer) {
            $question = $answer->question;

            // Get correct answer based on correct_answer field (A, B, C, D)
            $correctAnswer = null;
            $optionMapping = [
                'A' => $question->option_a,
                'B' => $question->option_b,
                'C' => $question->option_c,
                'D' => $question->option_d,
            ];

            if ($question->correct_answer && isset($optionMapping[$question->correct_answer])) {
                $correctAnswer = $optionMapping[$question->correct_answer];
            }

            if ($correctAnswer && $answer->answer_text === $correctAnswer) {
                $correctAnswers++;
            }
        }

        $score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
        $attempt->score = $score;
        $attempt->save();

        return response()->json([
            'score' => $score,
            'totalQuestions' => $totalQuestions,
            'correctAnswers' => $correctAnswers
        ]);
    }
}