<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['exam_id', 'sub_test_id', 'text', 'explanation', 'type', 'timer_per_question', 'irt_weight'];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function subTest()
    {
        return $this->belongsTo(SubTest::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function getDifficultyLevelAttribute()
    {
        $weight = $this->irt_weight;

        if ($weight < 1.3) {
            return 'Mudah';
        } elseif ($weight < 1.7) {
            return 'Sedang';
        } else {
            return 'Sulit';
        }
    }
}
