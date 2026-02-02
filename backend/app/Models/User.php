<?php

namespace App\Models;

use App\Enums\UserRole;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar_url',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function teacherProfile()
    {
        return $this->hasOne(TeacherProfile::class);
    }

    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function exercises()
    {
        return $this->hasMany(Exercise::class, 'teacher_id');
    }

    public function workoutPlansAsTeacher()
    {
        return $this->hasMany(WorkoutPlan::class, 'teacher_id');
    }

    public function workoutPlansAsStudent()
    {
        return $this->hasMany(WorkoutPlan::class, 'student_id');
    }

    public function workoutSessionsAsStudent()
    {
        return $this->hasMany(WorkoutSession::class, 'student_id');
    }

    public function workoutSessionsAsTeacher()
    {
        return $this->hasMany(WorkoutSession::class, 'teacher_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_id');
    }

    public function invites()
    {
        return $this->hasMany(Invite::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'teacher_student', 'teacher_id', 'student_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'teacher_student', 'student_id', 'teacher_id');
    }
}
