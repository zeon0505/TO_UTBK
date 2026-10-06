<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_admin',
        'nim',
        'nip',
        'prodi',
        'semester',
        'kelas',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']) || $this->is_admin;
    }

    public function isDosen(): bool
    {
        return $this->role === 'dosen' || $this->isAdmin();
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'lecturer_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'lecturer_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function answers()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
