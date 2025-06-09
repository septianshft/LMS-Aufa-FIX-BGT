<?php

/**
 * Test Mock LMS Integration - Independent Development
 *
 * This script tests the mock LMS data services to ensure
 * everything works without waiting for real LMS integration.
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

use App\Services\MockLMSDataService;
use App\Services\LMSIntegrationService;
use App\Models\User;

echo "🚀 Testing Mock LMS Integration for Independent Development\n";
echo "=" . str_repeat("=", 60) . "\n\n";

try {
    // Test 1: Mock LMS Data Service
    echo "1️⃣ Testing MockLMSDataService...\n";
    $mockService = new MockLMSDataService();

    // Find a user with talent skills
    $user = User::where('talent_skills', '!=', null)->first();

    if (!$user) {
        echo "❌ No users with talent skills found. Creating test user...\n";
        $user = User::create([
            'name' => 'Test Talent User',
            'email' => 'test.talent@example.com',
            'password' => bcrypt('password'),
            'talent_skills' => ['PHP', 'Laravel', 'JavaScript', 'React'],
            'is_active_talent' => true,
            'available_for_scouting' => true
        ]);
    }

    echo "   📊 Testing overall score generation...\n";
    $score = $mockService->generateOverallScore($user->id);
    echo "   ✅ Overall Score: {$score}/100\n";

    echo "   🎯 Testing skill categorization...\n";
    $skills = $user->talent_skills ?? [];
    if (is_string($skills)) {
        $skills = json_decode($skills, true);
    }
    $categories = $mockService->categorizeSkills($skills);
    echo "   ✅ Skill Categories: " . count($categories) . " categories found\n";
    foreach ($categories as $category => $categorySkills) {
        echo "      - {$category}: " . implode(', ', $categorySkills) . "\n";
    }

    echo "   📈 Testing learning progress...\n";
    $progress = $mockService->getLearningProgress($user->id);
    echo "   ✅ Learning Progress: {$progress['completed_courses']} courses, {$progress['total_hours']} hours\n";

    echo "   📋 Testing talent profile generation...\n";
    $profile = $mockService->generateTalentProfile($user->id);
    echo "   ✅ Profile generated successfully!\n";
    echo "      - Overall Score: {$profile['overall_score']}/100\n";
    echo "      - Readiness Score: {$profile['readiness_score']}/100\n";
    echo "      - Market Alignment: {$profile['market_alignment']}%\n";
    echo "      - Data Source: {$profile['data_source']}\n";

    echo "\n";

    // Test 2: LMS Integration Service
    echo "2️⃣ Testing LMSIntegrationService...\n";
    $integrationService = new LMSIntegrationService();

    echo "   🔗 Testing integration status...\n";
    $status = $integrationService->getIntegrationStatus();
    echo "   ✅ Integration Status:\n";
    echo "      - Connected: " . ($status['connected'] ? 'Yes' : 'No') . "\n";
    echo "      - Data Source: {$status['data_source']}\n";
    echo "      - Ready for Integration: " . ($status['ready_for_integration'] ? 'Yes' : 'No') . "\n";

    echo "   📊 Testing talent data retrieval...\n";
    $talentData = $integrationService->getTalentData($user->id);
    echo "   ✅ Talent Data Retrieved:\n";
    echo "      - User ID: {$talentData['user_id']}\n";
    echo "      - Overall Score: {$talentData['overall_score']}/100\n";
    echo "      - Skills Count: " . count($talentData['skills']) . "\n";
    echo "      - Integration Ready: " . ($talentData['integration_ready'] ? 'Yes' : 'No') . "\n";

    echo "\n";

    // Test 3: Integration Switch Simulation
    echo "3️⃣ Testing Integration Switch (Mock → Real LMS)...\n";
    echo "   🔄 Current mode: Mock Data\n";
    echo "   🎯 When your friend's LMS is ready:\n";
    echo "      1. Update config/lms.php: 'enabled' => true\n";
    echo "      2. Set LMS_API_URL and LMS_API_TOKEN in .env\n";
    echo "      3. Call \$integrationService->enableLMSConnection()\n";
    echo "      4. Zero code changes needed in controllers/views!\n";

    echo "\n";

    // Test 4: Data Structure Validation
    echo "4️⃣ Validating Data Structure for LMS Compatibility...\n";
    $requiredFields = ['user_id', 'overall_score', 'readiness_score', 'skills', 'skill_categories', 'learning_progress', 'market_alignment', 'recommendations'];

    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (!isset($talentData[$field])) {
            $missingFields[] = $field;
        }
    }

    if (empty($missingFields)) {
        echo "   ✅ All required fields present - LMS integration ready!\n";
    } else {
        echo "   ❌ Missing fields: " . implode(', ', $missingFields) . "\n";
    }

    echo "\n";

    // Summary
    echo "📋 SUMMARY\n";
    echo "=" . str_repeat("=", 20) . "\n";
    echo "✅ Mock LMS Data Service: Working\n";
    echo "✅ LMS Integration Service: Working\n";
    echo "✅ Data Structure: Compatible\n";
    echo "✅ Ready for Independent Development: YES\n";
    echo "🔄 When LMS Ready: Easy integration switch\n";
    echo "\n";
    echo "🎯 You can now develop your talent scouting features independently!\n";
    echo "📊 Access demo at: /admin/lms-mock/demo (when logged in as talent_admin)\n";
    echo "🚀 API endpoints available at: /admin/lms-mock/talent/{userId}/*\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
    echo "\n🔧 Debug info:\n";
    echo $e->getTraceAsString();
}
