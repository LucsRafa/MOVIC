<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherStudent extends Model
{
    protected $table = 'teacher_student';

    protected $fillable = [
        'teacher_id',
        'student_id',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
