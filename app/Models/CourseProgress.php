<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseProgress extends Model
{
    use HasFactory;

    protected $table = 'course_progresses'; // ⬅️ ini wajib ditambahkan

    protected $fillable = [
        'user_id',
        'course_id',
        'completed_videos',
        'quiz_passed',
        'progress',
    ];

    protected $casts = [
        'completed_videos' => 'array',
        'quiz_passed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
