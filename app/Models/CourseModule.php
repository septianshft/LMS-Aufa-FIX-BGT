<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'order',
    ];

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class);
    }
}
