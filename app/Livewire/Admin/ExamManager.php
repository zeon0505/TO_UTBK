<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Exam;

class ExamManager extends Component
{
    public $exams;
    public $examId, $title, $category, $sub_category, $duration, $is_active = true;
    public $isEdit = false;
    public $isManagingQuestions = false;
    public $selectedSubTestId = null;
    public $questionsList = [];
    
    // Question Editing State
    public $editingQuestionId = null;
    public $editQuestionText = '';
    public $editExplanation = '';
    public $editOptions = []; // Array of ['id' => ..., 'text' => ..., 'point' => ...]

    public function mount()
    {
        $this->loadExams();
    }

    public function loadExams()
    {
        $this->exams = Exam::orderBy('created_at', 'desc')->get();
    }

    public function resetFields()
    {
        $this->title = '';
        $this->category = 'TPS';
        $this->sub_category = '';
        $this->duration = 0;
        $this->is_active = true;
        $this->isEdit = false;
        $this->examId = null;
    }

    public function store()
    {
        $this->validate([
            'title' => 'required',
            'category' => 'required',
            'duration' => 'required|integer',
        ]);

        $exam = Exam::updateOrCreate(['id' => $this->examId], [
            'title' => $this->title,
            'category' => $this->category,
            'sub_category' => $this->sub_category,
            'duration' => $this->duration,
            'is_active' => $this->is_active,
        ]);

        // Auto-create/update SubTest if sub_category is defined (for individual subject exams)
        if ($this->sub_category) {
            \App\Models\SubTest::updateOrCreate(
                ['exam_id' => $exam->id],
                [
                    'title' => $this->sub_category,
                    'duration' => $this->duration,
                    'sort_order' => 1
                ]
            );
        }

        $this->dispatch('play-sfx', type: 'success');
        session()->flash('message', $this->examId ? 'Exam Updated.' : 'Exam Created.');
        $this->resetFields();
        $this->loadExams();
    }

    public function edit($id)
    {
        $exam = Exam::findOrFail($id);
        $this->examId = $id;
        $this->title = $exam->title;
        $this->category = $exam->category;
        $this->sub_category = $exam->sub_category;
        $this->duration = $exam->duration;
        $this->is_active = $exam->is_active;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        Exam::find($id)->delete();
        $this->loadExams();
    }

    public function recalculateIRT($id)
    {
        \App\Services\IRTService::recalculateWeights($id);
        session()->flash('message', 'Penilaian IRT telah dikalkulasi ulang untuk exam ini.');
    }

    public function openQuestionManager($examId)
    {
        $this->examId = $examId;
        $this->isManagingQuestions = true;
        $this->selectedSubTestId = \App\Models\SubTest::where('exam_id', $examId)->first()?->id;
        $this->loadQuestions();
    }

    public function loadQuestions()
    {
        if ($this->selectedSubTestId) {
            $this->questionsList = \App\Models\Question::where('sub_test_id', $this->selectedSubTestId)->get();
        } else {
            $this->questionsList = [];
        }
    }

    public function deleteQuestion($id)
    {
        \App\Models\Question::find($id)->delete();
        $this->loadQuestions();
        $this->dispatch('play-sfx', type: 'delete');
    }

    public function editQuestion($id)
    {
        $q = \App\Models\Question::with('options')->findOrFail($id);
        $this->editingQuestionId = $id;
        $this->editQuestionText = $q->text;
        $this->editExplanation = $q->explanation;
        
        $this->editOptions = [];
        foreach ($q->options as $opt) {
            $this->editOptions[] = [
                'id' => $opt->id,
                'text' => $opt->text,
                'point' => $opt->point,
            ];
        }

        $this->dispatch('play-sfx', type: 'click');
    }

    public function cancelEditQuestion()
    {
        $this->editingQuestionId = null;
        $this->editQuestionText = '';
        $this->editExplanation = '';
        $this->editOptions = [];
    }

    public function saveQuestion()
    {
        $q = \App\Models\Question::findOrFail($this->editingQuestionId);
        $q->update([
            'text' => $this->editQuestionText,
            'explanation' => $this->editExplanation,
        ]);

        // Save Options
        foreach ($this->editOptions as $optData) {
            if (isset($optData['id'])) {
                \App\Models\Option::where('id', $optData['id'])->update([
                    'text' => $optData['text'],
                    'point' => $optData['point'],
                ]);
            }
        }

        session()->flash('question_message', 'Pertanyaan & Pilihan Jawaban berhasil diperbarui.');
        $this->cancelEditQuestion();
        $this->loadQuestions();
        $this->dispatch('play-sfx', type: 'success');
    }

    public function deleteAllInSubTest()
    {
        if ($this->selectedSubTestId) {
            \App\Models\Question::where('sub_test_id', $this->selectedSubTestId)->delete();
            $this->loadQuestions();
            $this->dispatch('play-sfx', type: 'delete');
            session()->flash('message', 'Semua soal di materi ini telah dihapus.');
        }
    }

    public function closeQuestionManager()
    {
        $this->isManagingQuestions = false;
        $this->resetFields();
    }

    public function updatedSelectedSubTestId()
    {
        $this->loadQuestions();
    }

    public function render()
    {
        return view('livewire.admin.exam-manager')->layout('layouts.app');
    }
}
