<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['title', 'category', 'sub_category', 'duration', 'is_active'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function subTests()
    {
        return $this->hasMany(SubTest::class)->orderBy('sort_order');
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
