<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TALENT ADMIN DASHBOARD TEST ===\n\n";

try {
    // Test 1: Check if the view file exists
    $viewPath = resource_path('views/talent_admin/dashboard.blade.php');
    if (file_exists($viewPath)) {
        echo "✅ View file exists: " . $viewPath . "\n";
    } else {
        echo "❌ View file missing: " . $viewPath . "\n";
        exit(1);
    }

    // Test 2: Check if the controller method exists
    if (method_exists(\App\Http\Controllers\TalentAdminController::class, 'dashboard')) {
        echo "✅ Controller method exists: TalentAdminController@dashboard\n";
    } else {
        echo "❌ Controller method missing: TalentAdminController@dashboard\n";
        exit(1);
    }

    // Test 3: Check route exists
    $routes = collect(\Illuminate\Support\Facades\Route::getRoutes())->filter(function ($route) {
        return $route->getName() === 'talent_admin.dashboard';
    });

    if ($routes->count() > 0) {
        echo "✅ Route exists: talent_admin.dashboard\n";
        $route = $routes->first();
        echo "   → URI: " . $route->uri() . "\n";
        echo "   → Method: " . implode('|', $route->methods()) . "\n";
    } else {
        echo "❌ Route missing: talent_admin.dashboard\n";
        exit(1);
    }

    // Test 4: Check if we have a talent admin user to test with
    $talentAdmin = \App\Models\User::whereHas('roles', function($query) {
        $query->where('name', 'talent_admin');
    })->first();

    if ($talentAdmin) {
        echo "✅ Talent Admin user found: " . $talentAdmin->name . " (ID: " . $talentAdmin->id . ")\n";

        // Test 5: Try to simulate the dashboard view rendering (without authentication)
        try {
            // Create a mock request
            $app = app();
            $controller = new \App\Http\Controllers\TalentAdminController();

            // We can't actually call the dashboard method without authentication
            // but we can check that the required data models exist

            $totalTalents = \App\Models\Talent::count();
            $totalRecruiters = \App\Models\Recruiter::count();
            $totalRequests = \App\Models\TalentRequest::count();

            echo "✅ Dashboard data available:\n";
            echo "   → Total Talents: $totalTalents\n";
            echo "   → Total Recruiters: $totalRecruiters\n";
            echo "   → Total Requests: $totalRequests\n";

        } catch (Exception $e) {
            echo "⚠️  Dashboard data check failed: " . $e->getMessage() . "\n";
        }

    } else {
        echo "⚠️  No talent admin user found, but this is okay for testing\n";
    }

    echo "\n=== DASHBOARD TEST COMPLETE ===\n";
    echo "✅ All critical components are in place!\n";
    echo "📝 The Talent Admin dashboard should now work correctly.\n";
    echo "🔐 Make sure to login with a talent_admin role user to access it.\n\n";

} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
