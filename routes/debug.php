<?php

use Illuminate\Support\Facades\Route;

// Temporary debug route to test the request details view
Route::get('/debug-request-details', function() {
    $talentRequest = \App\Models\TalentRequest::with(['recruiter.user', 'talent.user'])->first();

    if (!$talentRequest) {
        return response('No talent request found in database.', 404);
    }

    $debug = [
        'request_id' => $talentRequest->id,
        'project_title' => $talentRequest->project_title,
        'status' => $talentRequest->status,
        'recruiter_exists' => $talentRequest->recruiter ? 'YES' : 'NO',
        'recruiter_user_exists' => ($talentRequest->recruiter && $talentRequest->recruiter->user) ? 'YES' : 'NO',
        'talent_exists' => $talentRequest->talent ? 'YES' : 'NO',
        'talent_user_exists' => ($talentRequest->talent && $talentRequest->talent->user) ? 'YES' : 'NO'
    ];

    if ($talentRequest->recruiter && $talentRequest->recruiter->user) {
        $debug['recruiter_name'] = $talentRequest->recruiter->user->name;
        $debug['recruiter_email'] = $talentRequest->recruiter->user->email;
        $debug['recruiter_company'] = $talentRequest->recruiter->company_name ?? 'N/A';
    }

    if ($talentRequest->talent && $talentRequest->talent->user) {
        $debug['talent_name'] = $talentRequest->talent->user->name;
        $debug['talent_email'] = $talentRequest->talent->user->email;
        $debug['talent_skills'] = $talentRequest->talent->skills ?? 'N/A';
    }

    return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
});
