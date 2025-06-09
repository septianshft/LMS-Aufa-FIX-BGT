<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📊 Advanced Analytics
            </h2>
            <div class="flex space-x-4">
                <button onclick="refreshAnalytics()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh Data
                </button>
                <a href="{{ route('talent_admin.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </x-slot>
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Conversion Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Conversion Funnel -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-funnel-dollar text-green-600 mr-3"></i>
                        Conversion Funnel
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @if(isset($skillAnalytics['conversion_funnel']['funnel_stages']))
                            @php $stages = $skillAnalytics['conversion_funnel']['funnel_stages']; @endphp
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                                <span class="font-medium">👥 Total Users</span>
                                <span class="text-2xl font-bold text-blue-600">{{ number_format($stages['total_users']) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-indigo-50 rounded-lg">
                                <span class="font-medium">📚 Registered Trainees</span>
                                <span class="text-2xl font-bold text-indigo-600">{{ number_format($stages['registered_trainees']) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                                <span class="font-medium">✅ Course Completions</span>
                                <span class="text-2xl font-bold text-purple-600">{{ number_format($stages['course_completions']) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-pink-50 rounded-lg">
                                <span class="font-medium">🎯 Skill Acquisitions</span>
                                <span class="text-2xl font-bold text-pink-600">{{ number_format($stages['skill_acquisitions']) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                <span class="font-medium">💼 Talent Opt-ins</span>
                                <span class="text-2xl font-bold text-green-600">{{ number_format($stages['talent_opt_ins']) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                                <span class="font-medium">🎉 Successful Placements</span>
                                <span class="text-2xl font-bold text-yellow-600">{{ number_format($stages['successful_placements']) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Conversion Readiness -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-line text-blue-600 mr-3"></i>
                        Conversion Readiness
                    </h2>
                </div>
                <div class="p-6">
                    <div class="text-center mb-6">
                        <div class="text-3xl font-bold text-blue-600">{{ $conversionAnalytics['conversion_ready'] }}</div>
                        <div class="text-gray-600">Ready to Convert</div>
                    </div>

                    <div class="space-y-3">
                        @if(isset($conversionAnalytics['readiness_distribution']))
                            <div class="flex justify-between items-center">
                                <span class="text-sm">🔥 High (80-100)</span>
                                <span class="font-bold text-red-600">{{ $conversionAnalytics['readiness_distribution']['high'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm">🟡 Medium (60-79)</span>
                                <span class="font-bold text-yellow-600">{{ $conversionAnalytics['readiness_distribution']['medium'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm">🟢 Low (40-59)</span>
                                <span class="font-bold text-green-600">{{ $conversionAnalytics['readiness_distribution']['low'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm">⚪ Very Low (0-39)</span>
                                <span class="font-bold text-gray-600">{{ $conversionAnalytics['readiness_distribution']['very_low'] }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <div class="text-center">
                            <div class="text-lg font-bold text-blue-600">{{ $conversionAnalytics['average_readiness_score'] }}%</div>
                            <div class="text-xs text-blue-600">Average Readiness Score</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Skill Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Skill Categories Distribution -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-tags text-purple-600 mr-3"></i>
                        Skill Categories
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @if(isset($skillAnalytics['skill_categories']))
                            @foreach(array_slice($skillAnalytics['skill_categories'], 0, 8, true) as $category => $count)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-700">{{ $category }}</span>
                                    <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-bold">{{ $count }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Market Demand Analysis -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-bar text-green-600 mr-3"></i>
                        Market Demand
                    </h2>
                </div>
                <div class="p-6">
                    @if(isset($skillAnalytics['market_demand_analysis']['distribution']))
                        <div class="space-y-4">
                            @php $distribution = $skillAnalytics['market_demand_analysis']['distribution']; @endphp
                            <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                                <span class="font-medium">🔥 Very High Demand</span>
                                <span class="text-red-600 font-bold">{{ $distribution['Very High'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                                <span class="font-medium">⚡ High Demand</span>
                                <span class="text-yellow-600 font-bold">{{ $distribution['High'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                <span class="font-medium">📊 Medium Demand</span>
                                <span class="text-blue-600 font-bold">{{ $distribution['Medium'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="font-medium">📉 Low Demand</span>
                                <span class="text-gray-600 font-bold">{{ $distribution['Low'] ?? 0 }}</span>
                            </div>
                        </div>

                        <!-- Top Demanded Skills -->
                        @if(isset($skillAnalytics['market_demand_analysis']['top_demanded_skills']))
                            <div class="mt-6">
                                <h3 class="font-bold text-gray-700 mb-3">🏆 Top Demanded Skills</h3>
                                <div class="space-y-2">
                                    @foreach(array_slice($skillAnalytics['market_demand_analysis']['top_demanded_skills'], 0, 5, true) as $skill => $count)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600">{{ $skill }}</span>
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-bold">{{ $count }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Conversion Candidates -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-star text-yellow-500 mr-3"></i>
                    Top Conversion Candidates
                    <span class="ml-3 bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">Ready to Convert</span>
                </h2>
                <p class="text-gray-600 mt-2">Trainees with high conversion readiness scores</p>
            </div>
            <div class="p-6">
                @if(isset($conversionAnalytics['top_conversion_candidates']) && count($conversionAnalytics['top_conversion_candidates']) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">User</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Readiness Score</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Skills</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Courses</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($conversionAnalytics['top_conversion_candidates'] as $candidate)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-4 px-4">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                                    {{ substr($candidate['user']['name'], 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ $candidate['user']['name'] }}</div>
                                                    <div class="text-sm text-gray-600">{{ $candidate['user']['email'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center">
                                                <div class="w-16 bg-gray-200 rounded-full h-3 mr-3">
                                                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full" style="width: {{ $candidate['score'] }}%"></div>
                                                </div>
                                                <span class="font-bold text-green-600">{{ $candidate['score'] }}%</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                                {{ $candidate['skills'] }} skills
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                                                {{ $candidate['courses'] }} courses
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <button onclick="suggestConversion({{ $candidate['user']['id'] }})"
                                                    class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition-colors">
                                                <i class="fas fa-paper-plane mr-1"></i>
                                                Suggest
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-user-clock text-4xl mb-4"></i>
                        <p>No conversion candidates ready at this time.</p>
                        <p class="text-sm">Check back as users complete more courses and gain skills.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Learning to Earning Correlation -->
        @if(isset($skillAnalytics['learning_to_earning']))
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-dollar-sign text-green-600 mr-3"></i>
                        Learning to Earning Analysis
                    </h2>
                    <p class="text-gray-600 mt-2">Correlation between skills and earning potential</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- ROI Analysis -->
                        @if(isset($skillAnalytics['learning_to_earning']['roi_analysis']))
                            @php $roi = $skillAnalytics['learning_to_earning']['roi_analysis']; @endphp
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">${{ number_format($roi['avg_investment_per_talent']) }}</div>
                                <div class="text-sm text-gray-600">Avg Investment per Talent</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">${{ number_format($roi['avg_earning_potential']) }}</div>
                                <div class="text-sm text-gray-600">Avg Earning Potential</div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">{{ $roi['roi_percentage'] }}%</div>
                                <div class="text-sm text-gray-600">ROI Percentage</div>
                            </div>
                        @endif
                    </div>

                    <!-- Hourly Rate by Category -->
                    @if(isset($skillAnalytics['learning_to_earning']['avg_hourly_rate_by_category']))
                        <div class="mt-6">
                            <h3 class="font-bold text-gray-700 mb-4">💰 Average Hourly Rate by Skill Category</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($skillAnalytics['learning_to_earning']['avg_hourly_rate_by_category'] as $category => $rate)
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="font-medium text-gray-700">{{ $category }}</span>
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold">${{ $rate }}/hr</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- JavaScript for interactions -->
<script>
function refreshAnalytics() {
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Refreshing...';
    button.disabled = true;

    // Simulate refresh
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function suggestConversion(userId) {
    // In a real implementation, this would send a notification or email
    alert(`Conversion suggestion sent to user ${userId}!`);

    // You could implement an AJAX call here
    // fetch(`/talent-admin/suggest-conversion/${userId}`, { method: 'POST' })
    //     .then(response => response.json())
    //     .then(data => {
    //         // Handle response
    //     });
}

// Initialize any charts or interactive elements
document.addEventListener('DOMContentLoaded', function() {
    // Add any chart initialization here
    console.log('Analytics dashboard loaded');
});
</script>
</x-app-layout>
