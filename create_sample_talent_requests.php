<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CREATING SAMPLE TALENT REQUESTS FOR TESTING ===\n\n";

try {
    // Get the talent user
    $talentUser = \App\Models\User::where('email', 'talent@test.com')->first();
    $recruiter = \App\Models\Recruiter::first();

    if (!$talentUser || !$recruiter) {
        echo "❌ Required users not found. Please run the seeders first.\n";
        exit;
    }

    echo "Talent User: {$talentUser->name}\n";
    echo "Recruiter: {$recruiter->user->name}\n\n";

    // Create sample talent requests with different statuses
    $sampleRequests = [
        [
            'status' => 'pending',
            'project_title' => 'Frontend Developer - React/Vue.js',
            'project_description' => 'We are looking for a talented frontend developer to join our team and help build amazing user interfaces for our web applications.',
            'requirements' => 'Experience with React.js or Vue.js, HTML5, CSS3, JavaScript ES6+, responsive design, Git version control',
            'budget_range' => '$3,000 - $5,000',
            'project_duration' => '2-3 months',
            'urgency_level' => 'high',
            'recruiter_message' => 'Hello! We have an exciting opportunity for a frontend developer position. Your profile shows excellent skills in JavaScript and modern frameworks. Would you be interested in discussing this role with us?'
        ],
        [
            'status' => 'approved',
            'project_title' => 'Full-Stack Web Application Development',
            'project_description' => 'Build a complete e-commerce platform with modern technologies including user authentication, payment processing, and admin dashboard.',
            'requirements' => 'Full-stack development experience, Node.js, Express.js, React, MongoDB or PostgreSQL, API development',
            'budget_range' => '$8,000 - $12,000',
            'project_duration' => '4-6 months',
            'urgency_level' => 'medium',
            'recruiter_message' => 'We were impressed by your portfolio and would love to have you work on our new e-commerce platform. This is a comprehensive project that will showcase your full-stack abilities.'
        ],
        [
            'status' => 'completed',
            'project_title' => 'Mobile App UI/UX Redesign',
            'project_description' => 'Redesign the user interface and improve user experience for our existing mobile application to increase user engagement and retention.',
            'requirements' => 'UI/UX design experience, mobile app design principles, Figma or Sketch, prototyping, user research',
            'budget_range' => '$2,500 - $4,000',
            'project_duration' => '1-2 months',
            'urgency_level' => 'low',
            'recruiter_message' => 'Your design portfolio is outstanding! We completed a successful mobile app redesign project together. Thank you for the excellent work and attention to detail.'
        ],
        [
            'status' => 'pending',
            'project_title' => 'API Development & Integration',
            'project_description' => 'Develop RESTful APIs and integrate third-party services for our growing platform.',
            'requirements' => 'Backend development, RESTful API design, PHP/Laravel or Node.js, database design, third-party integrations',
            'budget_range' => '$4,000 - $6,000',
            'project_duration' => '2-4 months',
            'urgency_level' => 'high',
            'recruiter_message' => 'We need a skilled backend developer for API development. Your experience with Laravel makes you a perfect fit for this project.'
        ]
    ];

    foreach ($sampleRequests as $index => $requestData) {
        $request = \App\Models\TalentRequest::create([
            'recruiter_id' => $recruiter->id,
            'talent_id' => $talentUser->id, // This might be different from talent_user_id
            'talent_user_id' => $talentUser->id,
            'project_title' => $requestData['project_title'],
            'project_description' => $requestData['project_description'],
            'requirements' => $requestData['requirements'],
            'budget_range' => $requestData['budget_range'],
            'project_duration' => $requestData['project_duration'],
            'urgency_level' => $requestData['urgency_level'],
            'status' => $requestData['status'],
            'recruiter_message' => $requestData['recruiter_message'],
            'created_at' => now()->subDays(rand(1, 30)),
            'updated_at' => now()->subDays(rand(0, 15))
        ]);

        echo "✅ Created request #{$request->id}: {$requestData['project_title']} ({$requestData['status']})\n";
    }

    echo "\n=== SAMPLE DATA CREATION COMPLETE ===\n";
    echo "You can now test the talent dashboard with realistic data.\n";
    echo "Visit: http://127.0.0.1:8000/admin/talent/dashboard\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
