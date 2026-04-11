<?php

namespace App\Services;

class QuestionParser
{
    /**
     * Parse raw text into structured question data.
     * Supports formats like:
     * 1. What is 1+1?
     * A. 1
     * B. 2
     * C. 3
     * Jawaban: B
     * Pembahasan: Penjumlahan dasar.
     */
    public static function parse($text)
    {
        // Split text into potential question blocks (numbered lists)
        $blocks = preg_split('/\n(?=\d+[\.\)])/', trim($text));
        $questions = [];

        foreach ($blocks as $block) {
            $data = [
                'text' => '',
                'options' => [],
                'correct_label' => '',
                'explanation' => '',
            ];

            // 1. Extract Question Text (Line 1 or until first option)
            $lines = explode("\n", trim($block));
            $questionLines = [];
            $parsingOptions = false;

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                // Detect Option (A. B. C. etc)
                if (preg_match('/^[A-E][\.\)]/i', $line)) {
                    $parsingOptions = true;
                    $label = strtoupper(substr($line, 0, 1));
                    $data['options'][$label] = trim(substr($line, 2));
                } elseif (preg_match('/^(Jawaban|Kunci):?\s*([A-E])/i', $line, $matches)) {
                    $data['correct_label'] = strtoupper($matches[2]);
                } elseif (preg_match('/^(Pembahasan|Penjelasan):?\s*(.*)/i', $line, $matches)) {
                    $data['explanation'] = $matches[2];
                } elseif (!$parsingOptions) {
                    // Remove leading numbers from question text
                    $questionLines[] = preg_replace('/^\d+[\.\)]\s*/', '', $line);
                }
            }

            $data['text'] = implode(' ', $questionLines);
            
            if (!empty($data['text']) && !empty($data['options'])) {
                $questions[] = $data;
            }
        }

        return $questions;
    }
}
