<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Testing updated SmartConversionTrackingService with analytics template requirements...\n";

    $service = new App\Services\SmartConversionTrackingService();

    echo "Testing getConversionAnalytics()...\n";
    $analytics = $service->getConversionAnalytics();

    // Check required keys for the Blade template
    $requiredKeys = ['conversion_ready', 'readiness_distribution', 'average_readiness_score', 'top_conversion_candidates'];

    foreach ($requiredKeys as $key) {
        if (isset($analytics[$key])) {
            echo "✅ Found required key: {$key}\n";
        } else {
            echo "❌ Missing required key: {$key}\n";
        }
    }

    echo "\n📊 Analytics Summary:\n";
    echo "- Conversion Ready: " . ($analytics['conversion_ready'] ?? 'N/A') . "\n";
    echo "- Average Readiness Score: " . ($analytics['average_readiness_score'] ?? 'N/A') . "%\n";
    echo "- Top Candidates: " . count($analytics['top_conversion_candidates'] ?? []) . "\n";

    if (isset($analytics['readiness_distribution'])) {
        echo "- Readiness Distribution:\n";
        foreach ($analytics['readiness_distribution'] as $level => $count) {
            echo "  - {$level}: {$count}\n";
        }
    }

    echo "\n🎉 Analytics method updated successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
