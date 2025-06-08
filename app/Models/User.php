<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Concerns\HasUniqueStringIds;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'avatar',
        'pekerjaan',
        'email',
        'password',
        // Talent scouting fields
        'available_for_scouting',
        'talent_skills',
        'hourly_rate',
        'talent_bio',
        'portfolio_url',
        'location',
        'phone',
        'experience_level',
        'is_active_talent',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'talent_skills' => 'array',
            'available_for_scouting' => 'boolean',
            'is_active_talent' => 'boolean',
            'hourly_rate' => 'decimal:2',
        ];
    }

    public function courses(){
        return $this->belongsToMany(Course::class, 'course_trainees');
    }

    public function subscribe_transaction(){
        return $this->hasMany(SubscribeTransaction::class);
    }

    public function hasActiveSubscription(?Course $course = null)
    {
        $query = SubscribeTransaction::where('user_id', $this->id)
            ->where('is_paid', true);

        if ($course) {
            $query->where('course_id', $course->id); // hanya boleh akses kelas yang dibayarkan
        }

        return $query->exists();
    }

    public function trainer()
    {
        return $this->hasOne(Trainer::class);
    }

    // New talent scouting relationships
    public function talentAdmin()
    {
        return $this->hasOne(TalentAdmin::class);
    }

    public function talent()
    {
        return $this->hasOne(Talent::class);
    }

    public function recruiter()
    {
        return $this->hasOne(Recruiter::class);
    }

    // Talent utility methods
    public function enableTalentScouting($skills = [], $hourlyRate = null, $bio = null)
    {
        $this->update([
            'available_for_scouting' => true,
            'talent_skills' => $skills,
            'hourly_rate' => $hourlyRate,
            'talent_bio' => $bio,
        ]);

        // Assign talent role if not already assigned
        if (!$this->hasRole('talent')) {
            $this->assignRole('talent');
        }

        // Create Talent record if it doesn't exist
        if (!$this->talent) {
            Talent::create([
                'user_id' => $this->id,
                'is_active' => true,
            ]);
        }

        return $this;
    }

    public function disableTalentScouting()
    {
        $this->update([
            'available_for_scouting' => false,
            'is_active_talent' => false,
        ]);

        // Optionally deactivate Talent record
        if ($this->talent) {
            $this->talent->update(['is_active' => false]);
        }

        return $this;
    }

    public function addSkillFromCourse(Course $course)
    {
        $currentSkills = $this->talent_skills ?? [];
        $newSkill = [
            'name' => $course->name,
            'level' => $course->level ? $course->level->name : 'Beginner',
            'acquired_from' => 'course_completion',
            'course_id' => $course->id,
            'acquired_at' => now()->toDateString(),
        ];

        // Check if skill already exists
        $exists = collect($currentSkills)->contains(function ($skill) use ($course) {
            return isset($skill['course_id']) && $skill['course_id'] == $course->id;
        });

        if (!$exists) {
            $currentSkills[] = $newSkill;
            $this->update(['talent_skills' => $currentSkills]);
        }

        return $this;
    }

    public function isAvailableForScouting()
    {
        return $this->available_for_scouting && $this->is_active_talent;
    }
}
