<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_id',
        'score',
        'total_correct_pg',
        'total_essay_score',
        'is_graded',
        'violations_count',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'is_graded' => 'boolean',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
