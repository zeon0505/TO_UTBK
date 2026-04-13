<?php

namespace App\Services;

use App\Models\Question;
use App\Models\UserAnswer;
use App\Models\Result;
use App\Models\Option;

class IRTService
{
    /**
     * Recalculate IRT weights for all questions in an exam.
     */
    public static function recalculateWeights($examId)
    {
        $questions = Question::whereHas('subTest', function($q) use ($examId) {
            $q->where('exam_id', $examId);
        })->get();

        $totalParticipants = Result::where('exam_id', $examId)->whereNotNull('finished_at')->count();

        if ($totalParticipants == 0) return;

        foreach ($questions as $question) {
            // Find the correct option(s)
            $correctOptionIds = Option::where('question_id', $question->id)
                ->where('point', '>', 0)
                ->pluck('id');

            // Count how many answered correctly
            $correctCount = UserAnswer::where('question_id', $question->id)
                ->whereIn('option_id', $correctOptionIds)
                ->count();

            // Correctness Rate (0.0 to 1.0)
            $rate = $correctCount / $totalParticipants;

            // Simplified IRT Weight formula: 
            // Questions answered correctly by few people get higher weights.
            // Range: 1.0 (very easy) to 2.0 (very hard)
            $newWeight = 1 + (1 - $rate);

            $question->update(['irt_weight' => $newWeight]);
        }

        // After updating weights, we should also update all users' total scores for this exam
        self::updateAllUserScores($examId);
    }

    /**
     * Update scores for all participants based on new weights.
     */
    public static function updateAllUserScores($examId)
    {
        $results = Result::where('exam_id', $examId)->whereNotNull('finished_at')->get();

        foreach ($results as $result) {
            $totalScore = 0;
            $rawPoints = 0;
            $answers = UserAnswer::where('result_id', $result->id)->with(['question', 'option'])->get();

            foreach ($answers as $answer) {
                if ($answer->option && $answer->option->point > 0) {
                    $rawPoints += $answer->option->point;
                    $totalScore += ($answer->option->point * ($answer->question->irt_weight ?? 1.0));
                }
            }

            $result->update([
                'total_score' => $totalScore,
                'raw_points' => $rawPoints
            ]);
        }
    }
}
