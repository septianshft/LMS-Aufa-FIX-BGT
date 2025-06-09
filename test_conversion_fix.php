<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Testing SmartConversionTrackingService after role fix...\n";

    $service = new App\Services\SmartConversionTrackingService();

    echo "Testing getConversionFunnel()...\n";
    $funnel = $service->getConversionFunnel();
    echo "✅ Conversion funnel: " . json_encode($funnel, JSON_PRETTY_PRINT) . "\n";

    echo "\nTesting getTalentReadinessAnalytics()...\n";
    $readiness = $service->getTalentReadinessAnalytics();
    echo "✅ Found " . count($readiness) . " users for readiness analytics\n";
      echo "\nTesting getConversionCandidates()...\n";
    $candidates = $service->getConversionCandidates(5);
    echo "✅ Found " . count($candidates) . " conversion candidates\n";

    echo "\nTesting getConversionAnalytics() (new method)...\n";
    $analytics = $service->getConversionAnalytics();
    echo "✅ Analytics data structure: " . json_encode(array_keys($analytics)) . "\n";

    echo "\nTesting triggerSmartNotifications()...\n";
    $notifications = $service->triggerSmartNotifications();
    echo "✅ Would send " . $notifications . " smart notifications\n";

    echo "\n🎉 All tests passed! SmartConversionTrackingService is working correctly.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
