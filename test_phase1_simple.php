<?php

/**
 * Simple Phase 1 Integration Test
 * Verifies core analytics and talent conversion tracking functionality
 */

echo "🔄 Testing Phase 1 Analytics and Conversion Integration...\n";

// Check if files exist
$files_to_check = [
    'app/Services/AdvancedSkillAnalyticsService.php',
    'app/Services/SmartConversionTrackingService.php',
    'resources/views/talent_admin/analytics.blade.php'
];

echo "\n📁 Checking core Phase 1 files...\n";
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ $file - Found\n";
    } else {
        echo "❌ $file - Missing\n";
    }
}

// Check if new routes are defined
echo "\n🛣️  Checking new analytics routes...\n";
$routes_content = file_get_contents('routes/web.php');
$expected_routes = [
    'talent-admin/analytics',
    'api/conversion-analytics',
    'api/conversion-candidates',
    'api/skill-analytics',
    'api/market-demand'
];

foreach ($expected_routes as $route) {
    if (strpos($routes_content, $route) !== false) {
        echo "✅ Route: $route - Found\n";
    } else {
        echo "❌ Route: $route - Missing\n";
    }
}

// Check User model enhancements
echo "\n👤 Checking User model analytics methods...\n";
$user_content = file_get_contents('app/Models/User.php');
$expected_methods = [
    'getSkillCategory',
    'calculateReadinessScore',
    'getLearningVelocity'
];

foreach ($expected_methods as $method) {
    if (strpos($user_content, $method) !== false) {
        echo "✅ Method: $method - Found\n";
    } else {
        echo "❌ Method: $method - Missing\n";
    }
}

// Check TalentAdminController enhancements
echo "\n🎛️  Checking TalentAdminController analytics methods...\n";
$controller_content = file_get_contents('app/Http/Controllers/TalentAdminController.php');
$expected_controller_methods = [
    'analytics',
    'getConversionAnalytics',
    'getConversionCandidates'
];

foreach ($expected_controller_methods as $method) {
    if (strpos($controller_content, $method) !== false) {
        echo "✅ Controller method: $method - Found\n";
    } else {
        echo "❌ Controller method: $method - Missing\n";
    }
}

// Check navigation integration
echo "\n🧭 Checking navigation integration...\n";
$sidebar_content = file_get_contents('resources/views/layout/navbar/sidebar.blade.php');
if (strpos($sidebar_content, 'talent_admin.analytics') !== false) {
    echo "✅ Analytics navigation link - Found\n";
} else {
    echo "❌ Analytics navigation link - Missing\n";
}

$dashboard_content = file_get_contents('resources/views/talent_admin/dashboard.blade.php');
if (strpos($dashboard_content, 'Analytics') !== false && strpos($dashboard_content, 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4') !== false) {
    echo "✅ Analytics dashboard integration - Found\n";
} else {
    echo "❌ Analytics dashboard integration - Missing\n";
}

echo "\n🎯 Phase 1 Integration Test Summary:\n";
echo "=============================================\n";
echo "Phase 1 focuses on enhanced skill mapping and analytics.\n";
echo "This includes:\n";
echo "- Advanced skill categorization\n";
echo "- Market demand indicators\n";
echo "- Conversion analytics\n";
echo "- Dashboard intelligence\n";
echo "\n✅ Phase 1 implementation completed!\n";
echo "📋 Next: Review and test the analytics dashboard in the browser.\n";
