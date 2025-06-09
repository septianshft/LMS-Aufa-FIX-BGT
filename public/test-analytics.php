<?php

// Test to verify AdvancedSkillAnalyticsService foreach error resolution
// Run this in a web context or artisan command where Laravel is fully loaded

use App\Services\AdvancedSkillAnalyticsService;
use App\Models\User;

echo "<!DOCTYPE html><html><head><title>Analytics Service Test</title></head><body>";
echo "<h1>AdvancedSkillAnalyticsService Test</h1>";

try {
    echo "<h2>Testing Service Methods</h2>";

    // Create service instance
    $service = new AdvancedSkillAnalyticsService();

    // Test skill category distribution (line 43 area where error occurred)
    echo "<h3>1. Testing getSkillCategoryDistribution (Line 43 area)</h3>";
    $categories = $service->getSkillCategoryDistribution();
    echo "<p>✅ Categories found: " . count($categories) . "</p>";

    // Test market demand analysis
    echo "<h3>2. Testing getMarketDemandAnalysis</h3>";
    $demand = $service->getMarketDemandAnalysis();
    echo "<p>✅ Market demand sections: " . count($demand) . "</p>";

    // Test conversion metrics
    echo "<h3>3. Testing getTalentConversionMetrics</h3>";
    $conversion = $service->getTalentConversionMetrics();
    echo "<p>✅ Conversion metrics sections: " . count($conversion) . "</p>";

    // Test skill progression trends
    echo "<h3>4. Testing getSkillProgressionTrends</h3>";
    $progression = $service->getSkillProgressionTrends();
    echo "<p>✅ Progression trend sections: " . count($progression) . "</p>";

    // Test top performing skills
    echo "<h3>5. Testing getTopPerformingSkills</h3>";
    $topSkills = $service->getTopPerformingSkills();
    echo "<p>✅ Top skills sections: " . count($topSkills) . "</p>";

    // Test conversion funnel metrics
    echo "<h3>6. Testing getConversionFunnelMetrics</h3>";
    $funnel = $service->getConversionFunnelMetrics();
    echo "<p>✅ Funnel metrics sections: " . count($funnel) . "</p>";

    // Test learning to earning metrics
    echo "<h3>7. Testing getLearningToEarningMetrics</h3>";
    $earning = $service->getLearningToEarningMetrics();
    echo "<p>✅ Learning to earning sections: " . count($earning) . "</p>";

    // Test full analytics (this calls all the above methods)
    echo "<h3>8. Testing getSkillAnalytics (Full Suite)</h3>";
    $analytics = $service->getSkillAnalytics();
    echo "<p>✅ Analytics sections: " . count($analytics) . "</p>";

    echo "<div style='background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h2 style='color: #155724; margin: 0;'>🎉 SUCCESS!</h2>";
    echo "<p style='color: #155724; margin: 10px 0 0 0;'>All tests passed! The foreach error has been successfully resolved.</p>";
    echo "</div>";

    // Show some sample data
    echo "<h3>Sample Data Output</h3>";
    echo "<h4>Category Distribution:</h4>";
    echo "<ul>";
    $count = 0;
    foreach ($categories as $category => $num) {
        echo "<li>{$category}: {$num} talents</li>";
        if (++$count >= 5) break; // Show max 5
    }
    if (count($categories) > 5) {
        echo "<li><em>... and " . (count($categories) - 5) . " more categories</em></li>";
    }
    echo "</ul>";

} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h2 style='color: #721c24; margin: 0;'>❌ ERROR DETECTED</h2>";
    echo "<p style='color: #721c24;'><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p style='color: #721c24;'><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
    echo "<details style='color: #721c24;'><summary>Stack Trace</summary><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre></details>";
    echo "</div>";
} catch (Error $e) {
    echo "<div style='background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h2 style='color: #721c24; margin: 0;'>❌ PHP ERROR DETECTED</h2>";
    echo "<p style='color: #721c24;'><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p style='color: #721c24;'><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
    echo "<details style='color: #721c24;'><summary>Stack Trace</summary><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre></details>";
    echo "</div>";
}

echo "</body></html>";
