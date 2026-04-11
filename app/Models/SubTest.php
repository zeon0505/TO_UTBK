<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubTest extends Model
{
    protected $fillable = ['exam_id', 'title', 'duration', 'sort_order'];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
