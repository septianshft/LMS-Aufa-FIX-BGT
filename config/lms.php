<?php

return [
    /*
    |--------------------------------------------------------------------------
    | LMS Integration Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for integrating with your friend's LMS system.
    | Switch 'enabled' to true when LMS is ready for integration.
    |
    */

    'enabled' => env('LMS_INTEGRATION_ENABLED', false),

    'api_url' => env('LMS_API_URL', 'https://your-friend-lms.com/api'),

    'api_token' => env('LMS_API_TOKEN', ''),

    'timeout' => env('LMS_API_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Mock Data Settings
    |--------------------------------------------------------------------------
    |
    | Settings for mock data when LMS integration is disabled
    |
    */
    'mock' => [
        'generate_realistic_scores' => true,
        'simulate_api_delay' => false, // Set to true to simulate real API delays
        'delay_ms' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Mapping
    |--------------------------------------------------------------------------
    |
    | Map LMS data fields to your application fields
    |
    */
    'field_mapping' => [
        'user_id' => 'user_id',
        'overall_score' => 'overall_score',
        'skills' => 'talent_skills',
        'progress' => 'learning_progress',
        'readiness' => 'readiness_score',
    ],

    /*
    |--------------------------------------------------------------------------
    | Endpoints
    |--------------------------------------------------------------------------
    |
    | LMS API endpoints (update these when your friend provides the actual API)
    |
    */
    'endpoints' => [
        'talent_profile' => '/talent/{user_id}/profile',
        'overall_score' => '/talent/{user_id}/score',
        'skill_analysis' => '/talent/{user_id}/skills',
        'learning_progress' => '/talent/{user_id}/progress',
        'recommendations' => '/talent/{user_id}/recommendations',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Cache LMS data to improve performance
    |
    */
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1 hour in seconds
        'prefix' => 'lms_data_',
    ],
];
