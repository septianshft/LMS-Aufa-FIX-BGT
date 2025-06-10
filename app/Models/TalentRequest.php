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
        'talent_user_id', // New field for direct user reference
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
        'onboarded_at',
        // New dual acceptance fields
        'talent_accepted',
        'talent_accepted_at',
        'talent_acceptance_notes',
        'admin_accepted',
        'admin_accepted_at',
        'admin_acceptance_notes',
        'both_parties_accepted',
        'workflow_completed_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'meeting_arranged_at' => 'datetime',
        'onboarded_at' => 'datetime',
        'talent_accepted_at' => 'datetime',
        'admin_accepted_at' => 'datetime',
        'workflow_completed_at' => 'datetime',
        'talent_accepted' => 'boolean',
        'admin_accepted' => 'boolean',
        'both_parties_accepted' => 'boolean',
        'deleted_at' => 'datetime'
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

    // New relationship to user directly
    public function talentUser()
    {
        return $this->belongsTo(User::class, 'talent_user_id');
    }

    // Alias for easier access in views and controllers
    public function user()
    {
        return $this->belongsTo(User::class, 'talent_user_id');
    }

    // Helper method to get talent user (either from direct reference or through talent)
    public function getTalentUser()
    {
        return $this->talentUser ?? $this->talent?->user;
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

    // Dual acceptance workflow methods
    public function isTalentAccepted()
    {
        return $this->talent_accepted;
    }

    public function isAdminAccepted()
    {
        return $this->admin_accepted;
    }

    public function areBothPartiesAccepted()
    {
        return $this->both_parties_accepted;
    }

    public function canProceedToMeeting()
    {
        return $this->talent_accepted && $this->admin_accepted && $this->both_parties_accepted;
    }

    /**
     * Check if the request can proceed to onboarding
     * Both talent and admin must have accepted
     */
    public function canProceedToOnboarding()
    {
        return $this->talent_accepted && $this->admin_accepted && $this->status !== 'rejected';
    }

    public function markTalentAccepted($notes = null)
    {
        $this->update([
            'talent_accepted' => true,
            'talent_accepted_at' => now(),
            'talent_acceptance_notes' => $notes
        ]);

        $this->checkAndMarkBothAccepted();
        return $this;
    }

    public function markAdminAccepted($notes = null)
    {
        $this->update([
            'admin_accepted' => true,
            'admin_accepted_at' => now(),
            'admin_acceptance_notes' => $notes
        ]);

        $this->checkAndMarkBothAccepted();
        return $this;
    }

    public function checkAndMarkBothAccepted()
    {
        if ($this->talent_accepted && $this->admin_accepted && !$this->both_parties_accepted) {
            $this->update([
                'both_parties_accepted' => true,
                'workflow_completed_at' => now(),
                'status' => 'approved' // Move to approved status when both accept
            ]);
        }
    }

    public function getAcceptanceStatus()
    {
        if ($this->both_parties_accepted) {
            return 'Both parties accepted - Ready for meeting';
        } elseif ($this->talent_accepted && $this->admin_accepted) {
            return 'Both accepted - Processing';
        } elseif ($this->talent_accepted) {
            return 'Talent accepted - Waiting for admin';
        } elseif ($this->admin_accepted) {
            return 'Admin accepted - Waiting for talent';
        } else {
            return 'Pending acceptance from both parties';
        }
    }

    public function getWorkflowProgress()
    {
        $progress = 0;

        if ($this->status !== 'rejected') {
            $progress += 20; // Request submitted
        }

        if ($this->talent_accepted) {
            $progress += 30; // Talent accepted
        }

        if ($this->admin_accepted) {
            $progress += 30; // Admin accepted
        }

        if ($this->both_parties_accepted) {
            $progress += 20; // Both accepted, ready for meeting
        }

        return min(100, $progress);
    }
}
