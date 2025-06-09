{{-- Smart Talent Conversion Notifications --}}

{{-- Smart Talent Suggestion --}}
@if(session('smart_talent_suggestion'))
    @php $suggestion = session('smart_talent_suggestion'); @endphp
    <div class="smart-talent-notification bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 rounded-lg mb-6 shadow-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-star text-2xl text-yellow-300"></i>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-lg font-semibold mb-1">🎉 You're Ready for the Next Level!</h3>
                <p class="mb-3">{{ $suggestion['message'] }}</p>
                <div class="bg-white bg-opacity-20 rounded-lg p-3 mb-3">
                    <p class="text-sm font-medium">{{ $suggestion['reason'] }}</p>
                    <div class="mt-2 flex items-center text-sm">
                        <span class="bg-white bg-opacity-30 px-2 py-1 rounded mr-2">
                            <i class="fas fa-cog mr-1"></i>{{ $suggestion['skill_count'] }} Skills
                        </span>
                        <span class="bg-white bg-opacity-30 px-2 py-1 rounded">
                            <i class="fas fa-chart-line mr-1"></i>High Market Value
                        </span>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <a href="{{ $suggestion['action_url'] }}"
                       class="inline-flex items-center px-4 py-2 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors duration-200 text-center">
                        <i class="fas fa-rocket mr-2"></i>Join Talent Platform
                    </a>
                    <button onclick="dismissSuggestion('smart_talent_suggestion')"
                            class="inline-flex items-center px-4 py-2 bg-transparent border border-white text-white font-semibold rounded-lg hover:bg-white hover:text-blue-600 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>Maybe Later
                    </button>
                </div>
            </div>
            <button onclick="dismissSuggestion('smart_talent_suggestion')"
                    class="flex-shrink-0 ml-3 text-white hover:text-gray-200 transition-colors duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif

{{-- Certificate Talent Suggestion --}}
@if(session('certificate_talent_suggestion'))
    @php $suggestion = session('certificate_talent_suggestion'); @endphp
    <div class="certificate-talent-notification bg-gradient-to-r from-green-500 to-teal-600 text-white p-4 rounded-lg mb-6 shadow-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-certificate text-2xl text-yellow-300"></i>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-lg font-semibold mb-1">🏆 Course Completed & Certificate Earned!</h3>
                <p class="mb-3">{{ $suggestion['message'] }}</p>
                <div class="bg-white bg-opacity-20 rounded-lg p-3 mb-3">
                    <div class="flex items-center text-sm">
                        <span class="bg-white bg-opacity-30 px-2 py-1 rounded mr-2">
                            <i class="fas fa-award mr-1"></i>Certificate Ready
                        </span>
                        <span class="bg-white bg-opacity-30 px-2 py-1 rounded">
                            <i class="fas fa-briefcase mr-1"></i>Employer Ready
                        </span>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <a href="{{ $suggestion['action_url'] }}"
                       class="inline-flex items-center px-4 py-2 bg-white text-green-600 font-semibold rounded-lg hover:bg-green-50 transition-colors duration-200 text-center">
                        <i class="fas fa-user-tie mr-2"></i>Become Discoverable
                    </a>
                    <button onclick="dismissSuggestion('certificate_talent_suggestion')"
                            class="inline-flex items-center px-4 py-2 bg-transparent border border-white text-white font-semibold rounded-lg hover:bg-white hover:text-green-600 transition-colors duration-200">
                        <i class="fas fa-clock mr-2"></i>Not Now
                    </button>
                </div>
            </div>
            <button onclick="dismissSuggestion('certificate_talent_suggestion')"
                    class="flex-shrink-0 ml-3 text-white hover:text-gray-200 transition-colors duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif

{{-- Legacy Talent Suggestion (fallback) --}}
@if(session('talent_suggestion'))
    @php $suggestion = session('talent_suggestion'); @endphp
    <div class="talent-notification bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-lightbulb text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-blue-700">{{ $suggestion['message'] }}</p>
                <div class="mt-2">
                    <a href="{{ $suggestion['action_url'] }}"
                       class="text-blue-600 hover:text-blue-500 font-medium">
                        Enable Talent Scouting →
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
function dismissSuggestion(suggestionType) {
    // Hide the notification with animation
    const notification = document.querySelector(`.${suggestionType.replace('_', '-')}-notification`);
    if (notification) {
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.style.display = 'none';
        }, 300);

        // Store dismissal in localStorage to prevent showing again soon
        localStorage.setItem(`dismissed_${suggestionType}`, Date.now());
    }
}

// Add smooth animation on page load
document.addEventListener('DOMContentLoaded', function() {
    const notifications = document.querySelectorAll('.smart-talent-notification, .certificate-talent-notification');
    notifications.forEach((notification, index) => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            notification.style.transition = 'all 0.5s ease';
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
        }, index * 200);
    });
});
</script>

<style>
.smart-talent-notification,
.certificate-talent-notification {
    transition: all 0.3s ease;
}

.smart-talent-notification:hover,
.certificate-talent-notification:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}
</style>
@endpush
