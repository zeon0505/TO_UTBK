<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Exam;
use App\Models\SubTest;
use App\Models\Question;
use App\Models\Option;

class QuestionGenerator extends Component
{
    public $rawText;
    public $apiKey;
    public $examId;
    public $subTestId;
    public $isGenerating = false;
    public $generatedCount = 0;

    public function mount()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
    }

    public function getExamsProperty()
    {
        return Exam::all();
    }

    public function getSubTestsProperty()
    {
        if (!$this->examId) return collect();
        return SubTest::where('exam_id', $this->examId)->get();
    }

    public function generate()
    {
        $this->validate([
            'rawText' => 'required|string',
            'examId' => 'required',
            'subTestId' => 'required',
        ]);

        $this->isGenerating = true;

        try {
            // Try AI First if API Key is provided
            if ($this->apiKey) {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $this->apiKey;
                
                $prompt = "Extract questions into JSON format: [{\"question\":\"...\", \"options\":[{\"text\":\"...\",\"is_correct\":true},...]}] \n\n TEXT: " . $this->rawText;

                $response = \Illuminate\Support\Facades\Http::timeout(60)->post($url, [
                    "contents" => [["parts" => [["text" => $prompt]]]]
                ]);

                if ($response->successful()) {
                    $result = $response->json();
                    $aiText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
                    $aiText = str_replace(['```json', '```'], '', $aiText);
                    $questionsData = json_decode(trim($aiText), true);
                    
                    if (is_array($questionsData)) {
                        $this->saveToDb($questionsData);
                        session()->flash('success', "AI Berhasil membuat {$this->generatedCount} pertanyaan.");
                        $this->reset(['rawText']);
                        $this->isGenerating = false;
                        return;
                    }
                }
            }

            // FALLBACK: Smart Local Regex Parser if AI fails or No Key
            $this->parseLocally();
            session()->flash('success', "Sistem (Local) Berhasil mengekstrak {$this->generatedCount} pertanyaan.");
            $this->reset(['rawText']);

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses: ' . $e->getMessage());
        }

        $this->isGenerating = false;
    }

    private function parseLocally()
    {
        $text = $this->rawText;
        // Normalize line endings and whitespace
        $text = str_replace("\r\n", "\n", $text);
        
        // 1. Split text into blocks where each block starts with a number (e.g., "1.", "2.", "10.")
        // We look for any digit followed by dot/bracket at the start of a line.
        $blocks = preg_split('/(?m)^\d+[\.\)]\s*/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        // If splitting by number didn't work (no numbers?), try splitting by blank lines
        if (count($blocks) <= 1) {
            $blocks = explode("\n\n", $text);
        }

        $this->generatedCount = 0;

        foreach ($blocks as $block) {
            $block = trim($block);
            if (empty($block)) continue;

            // 2. In each block, identify where the options start (A., B., C...)
            // We split the block into "Question text" and "Options text"
            $parts = preg_split('/(?m)^[A-E][\.\)\s]/', $block, 2);
            
            if (count($parts) >= 1) {
                $qText = trim($parts[0]);
                if (strlen($qText) < 3) continue;

                $question = Question::create([
                    'exam_id' => $this->examId,
                    'sub_test_id' => $this->subTestId,
                    'text' => $qText,
                    'type' => 'Multiple Choice',
                    'timer_per_question' => 60,
                ]);

                // 3. Extract options from the rest of the block
                // We re-examine the whole block specifically for A-E patterns
                preg_match_all('/(?m)^([A-E])[\.\)\s]\s*(.+?)(?=\n[A-E][\.\)\s]|$)/s', $block, $oMatches, PREG_SET_ORDER);
                
                if (count($oMatches) > 0) {
                    foreach ($oMatches as $idx => $oMatch) {
                        $optText = trim($oMatch[2]);
                        $isCorrect = str_contains($optText, '*') || ($idx === 0 && !str_contains($block, '*'));
                        $optClean = trim(str_replace('*', '', $optText));

                        Option::create([
                            'question_id' => $question->id,
                            'text' => $optClean,
                            'point' => $isCorrect ? 10 : 0,
                        ]);
                    }
                } else {
                    // Fallback: search for options even if not at start of line
                    preg_match_all('/([A-E])[\.\)\s]\s*(.+?)(?=\s[A-E][[\.\)\s]|$)/', $block, $oMatches, PREG_SET_ORDER);
                    foreach ($oMatches as $idx => $oMatch) {
                        $optText = trim($oMatch[2]);
                        Option::create([
                            'question_id' => $question->id,
                            'text' => trim(str_replace('*', '', $optText)),
                            'point' => ($idx === 0) ? 10 : 0,
                        ]);
                    }
                }
                $this->generatedCount++;
            }
        }
    }

    private function saveToDb($data)
    {
        $this->generatedCount = 0;
        foreach ($data as $qData) {
            $question = Question::create([
                'exam_id' => $this->examId,
                'sub_test_id' => $this->subTestId,
                'text' => $qData['question'],
                'type' => 'Multiple Choice',
                'timer_per_question' => 60,
            ]);

            foreach ($qData['options'] as $oData) {
                Option::create([
                    'question_id' => $question->id,
                    'text' => $oData['text'],
                    'point' => ($oData['is_correct'] ?? false) ? 10 : 0,
                ]);
            }
            $this->generatedCount++;
        }
    }

    public function render()
    {
        return view('livewire.admin.question-generator')->layout('layouts.app');
    }
}
