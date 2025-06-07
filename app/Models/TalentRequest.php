<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TalentRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'talent_requests';

    protected $fillable = [
        'recruiter_id',
        'talent_id',
        'project_title',
        'project_description',
        'requirements',
        'budget_range',
        'project_duration',
        'urgency_level',
        'status',
        'recruiter_message',
        'admin_notes',
        'approved_at',
        'meeting_arranged_at',
        'onboarded_at'
    ];

    protected $dates = [
        'approved_at',
        'meeting_arranged_at',
        'onboarded_at',
        'deleted_at'
    ];

    // Relationships
    public function recruiter()
    {
        return $this->belongsTo(Recruiter::class);
    }

    public function talent()
    {
        return $this->belongsTo(Talent::class);
    }

    // Status helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isMeetingArranged()
    {
        return $this->status === 'meeting_arranged';
    }

    public function isOnboarded()
    {
        return $this->status === 'onboarded';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    // Status badge color
    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'meeting_arranged' => 'primary',
            'agreement_reached' => 'success',
            'onboarded' => 'success',
            'rejected' => 'danger',
            'completed' => 'secondary',
            default => 'secondary'
        };
    }

    // Formatted status
    public function getFormattedStatus()
    {
        return match($this->status) {
            'pending' => 'Pending Review',
            'approved' => 'Approved by Admin',
            'meeting_arranged' => 'Meeting Arranged',
            'agreement_reached' => 'Agreement Reached',
            'onboarded' => 'Talent Onboarded',
            'rejected' => 'Rejected',
            'completed' => 'Project Completed',
            default => ucfirst($this->status)
        };
    }
}
