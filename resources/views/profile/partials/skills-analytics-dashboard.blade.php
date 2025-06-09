<section class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6" id="skills-dashboard">
    <header class="mb-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
            <i class="fas fa-chart-line mr-2 text-blue-600"></i>
            {{ __('Skills & Learning Analytics') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Track your skill development and learning progress.') }}
        </p>
    </header>

    @php
        $skillAnalytics = auth()->user()->getSkillAnalytics();
        $skillsByCategory = auth()->user()->getSkillsByCategory();
        $totalSkills = $skillAnalytics['total_skills'];
    @endphp

    <!-- Skills Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Skills</p>
                    <p class="text-2xl font-bold">{{ $totalSkills }}</p>
                </div>
                <i class="fas fa-cog text-2xl text-blue-200"></i>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Categories</p>
                    <p class="text-2xl font-bold">{{ $skillAnalytics['categories_count'] }}</p>
                </div>
                <i class="fas fa-layer-group text-2xl text-green-200"></i>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">High Demand</p>
                    <p class="text-2xl font-bold">{{ $skillAnalytics['high_demand_skills'] }}</p>
                </div>
                <i class="fas fa-fire text-2xl text-purple-200"></i>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Recent (30d)</p>
                    <p class="text-2xl font-bold">{{ $skillAnalytics['recent_skills'] }}</p>
                </div>
                <i class="fas fa-calendar text-2xl text-orange-200"></i>
            </div>
        </div>
    </div>

    @if($totalSkills > 0)
        <!-- Skills by Category -->
        <div class="mb-6">
            <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-tags mr-2"></i>Skills by Category
            </h3>
            <div class="space-y-4">
                @foreach($skillsByCategory as $category => $skills)
                    <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100 flex items-center">
                                @switch($category)
                                    @case('Frontend Development')
                                        <i class="fab fa-html5 mr-2 text-orange-500"></i>
                                        @break
                                    @case('Backend Development')
                                        <i class="fas fa-server mr-2 text-green-500"></i>
                                        @break
                                    @case('Data Science')
                                        <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
                                        @break
                                    @case('Mobile Development')
                                        <i class="fas fa-mobile-alt mr-2 text-purple-500"></i>
                                        @break
                                    @case('UI/UX Design')
                                        <i class="fas fa-paint-brush mr-2 text-pink-500"></i>
                                        @break
                                    @case('Cybersecurity')
                                        <i class="fas fa-shield-alt mr-2 text-red-500"></i>
                                        @break
                                    @case('Cloud Computing')
                                        <i class="fas fa-cloud mr-2 text-cyan-500"></i>
                                        @break
                                    @default
                                        <i class="fas fa-code mr-2 text-gray-500"></i>
                                @endswitch
                                {{ $category }}
                                <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                    {{ count($skills) }} skill{{ count($skills) > 1 ? 's' : '' }}
                                </span>
                            </h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($skills as $skill)
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $skill['name'] }}</span>
                                        @if(isset($skill['market_demand']))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($skill['market_demand'] === 'Very High') bg-red-100 text-red-800
                                                @elseif($skill['market_demand'] === 'High') bg-orange-100 text-orange-800
                                                @else bg-green-100 text-green-800 @endif">
                                                <i class="fas fa-trending-up mr-1"></i>
                                                {{ $skill['market_demand'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                                        <span class="flex items-center">
                                            <i class="fas fa-signal mr-1"></i>
                                            {{ $skill['level'] }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ \Carbon\Carbon::parse($skill['acquired_at'])->format('M Y') }}
                                        </span>
                                    </div>
                                    @if(isset($skill['verified']) && $skill['verified'])
                                        <div class="mt-2 flex items-center text-xs text-green-600">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Course Verified
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Skill Level Distribution -->
        <div class="mb-6">
            <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-chart-pie mr-2"></i>Skill Level Distribution
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($skillAnalytics['skill_levels'] as $level => $count)
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $count }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ $level }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Learning Progress Insight -->
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900 dark:to-blue-900 rounded-lg p-4">
            <h3 class="text-md font-semibold text-indigo-900 dark:text-indigo-100 mb-2">
                <i class="fas fa-lightbulb mr-2"></i>Learning Insights
            </h3>
            <div class="text-sm text-indigo-800 dark:text-indigo-200">
                @if($skillAnalytics['recent_skills'] > 0)
                    <p class="mb-2">🎉 You've gained {{ $skillAnalytics['recent_skills'] }} new skill{{ $skillAnalytics['recent_skills'] > 1 ? 's' : '' }} in the last 30 days!</p>
                @endif

                @if($skillAnalytics['high_demand_skills'] > 0)
                    <p class="mb-2">🔥 You have {{ $skillAnalytics['high_demand_skills'] }} high-demand skill{{ $skillAnalytics['high_demand_skills'] > 1 ? 's' : '' }} that employers are actively seeking.</p>
                @endif

                @if($totalSkills >= 5 && !auth()->user()->available_for_scouting)
                    <p class="mb-2">💼 With {{ $totalSkills }} verified skills, you're ready to attract recruiters! Consider enabling talent scouting below.</p>
                @endif

                @if($skillAnalytics['categories_count'] >= 3)
                    <p>🌟 Your diverse skill set across {{ $skillAnalytics['categories_count'] }} categories makes you a versatile candidate!</p>
                @endif
            </div>
        </div>
    @else
        <!-- No Skills Yet -->
        <div class="text-center py-8">
            <i class="fas fa-graduation-cap text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Skills Tracked Yet</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Complete courses and pass quizzes to automatically build your skill profile.</p>
            <a href="{{ route('front.index') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-play mr-2"></i>Start Learning
            </a>
        </div>
    @endif
</section>

@push('scripts')
<script>
// Add any interactive features for the skills dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Animate skill cards on page load
    const skillCards = document.querySelectorAll('#skills-dashboard .bg-white, #skills-dashboard .bg-gray-50');
    skillCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endpush
