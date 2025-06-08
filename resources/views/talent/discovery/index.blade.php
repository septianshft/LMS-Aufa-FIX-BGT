@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-search mr-3 text-purple-600"></i>
            Talent Discovery
        </h1>
        <p class="text-gray-600">Find the perfect talent for your project based on skills and experience from our LMS platform.</p>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Skill Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-tags mr-2"></i>Skills
                </label>
                <input type="text" id="skillSearch"
                       placeholder="e.g., JavaScript, Python, React"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                <small class="text-gray-500">Separate multiple skills with commas</small>
            </div>

            <!-- Experience Level -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-chart-line mr-2"></i>Experience Level
                </label>
                <select id="experienceLevel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Any Level</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>

            <!-- Minimum Skills -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-trophy mr-2"></i>Minimum Skills
                </label>
                <select id="minExperience" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Any</option>
                    <option value="1">1+ Skills</option>
                    <option value="3">3+ Skills</option>
                    <option value="5">5+ Skills</option>
                    <option value="10">10+ Skills</option>
                </select>
            </div>
        </div>        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3 mt-6">
            <button onclick="searchTalents()"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-search mr-2"></i>Search Talents
            </button>
            <button onclick="getRecommendations()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-magic mr-2"></i>Get Recommendations
            </button>
            <button onclick="showAllTalents()"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-users mr-2"></i>Show All Available
            </button>
            <button onclick="showAdvancedSearch()"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-cog mr-2"></i>Advanced Search
            </button>
            <button onclick="clearFilters()"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-times mr-2"></i>Clear
            </button>
        </div>
    </div>

    <!-- Welcome Message -->
    <div id="welcomeMessage" class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl p-8 mb-8 text-center">
        <div class="max-w-2xl mx-auto">
            <i class="fas fa-rocket text-4xl text-purple-600 mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-900 mb-3">Welcome to Talent Discovery</h2>
            <p class="text-gray-600 mb-6">Find the perfect talent for your project from our extensive pool of skilled professionals who have completed courses on our LMS platform. Use the search filters above to find talents with specific skills, or get personalized recommendations.</p>
            <button onclick="showAllTalents()"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-search mr-2"></i>
                Discover Available Talents
            </button>
        </div>
    </div>

    <!-- Results Section -->
    <div id="resultsSection" class="hidden">
        <!-- Results Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
                <span id="resultsTitle">Search Results</span>
                <span id="resultsCount" class="text-sm text-gray-500 ml-2"></span>
            </h2>
            <div class="flex gap-2">
                <button onclick="toggleView('grid')" id="gridViewBtn"
                        class="p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <i class="fas fa-th-large"></i>
                </button>
                <button onclick="toggleView('list')" id="listViewBtn"
                        class="p-2 text-purple-600 transition-colors duration-200">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>

        <!-- Results Container -->
        <div id="resultsContainer" class="space-y-4">
            <!-- Dynamic talent cards will be inserted here -->
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="hidden text-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Searching for talents...</p>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="hidden text-center py-12">
            <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-medium text-gray-900 mb-2">No talents found</h3>
            <p class="text-gray-600 mb-4">Try adjusting your search criteria or filters.</p>
            <button onclick="clearFilters()"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                Clear Filters
            </button>
        </div>
    </div>

    <!-- Analytics Section -->
    <div id="analyticsSection" class="mt-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-bar mr-2"></i>Talent Analytics
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4" id="analyticsCards">
                <!-- Analytics cards will be dynamically loaded -->
            </div>
        </div>
    </div>
</div>

<!-- Talent Profile Modal -->
<div id="talentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeTalentModal()"></div>
        <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Talent Profile</h3>
                <button onclick="closeTalentModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="talentModalContent">
                <!-- Modal content will be dynamically loaded -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentView = 'list';
let currentResults = [];

// Search for talents
async function searchTalents() {
    const skills = document.getElementById('skillSearch').value.split(',').map(s => s.trim()).filter(s => s);
    const level = document.getElementById('experienceLevel').value;
    const minExperience = document.getElementById('minExperience').value;

    const filters = {
        skills: skills,
        level: level || undefined,
        min_experience: minExperience || undefined
    };

    await performSearch('/recruiter/discovery/search', filters, 'Search Results');
}

// Get recommendations
async function getRecommendations() {
    await performSearch('/recruiter/discovery/recommendations', {}, 'Recommended Talents');
}

// Show all available talents
async function showAllTalents() {
    await performSearch('/recruiter/discovery/search', {}, 'All Available Talents');
}

// Perform search request
async function performSearch(endpoint, data, title) {
    showLoading();

    try {
        const response = await fetch(endpoint, {
            method: data.skills || data.level ? 'POST' : 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: data.skills || data.level ? JSON.stringify(data) : null
        });

        const result = await response.json();

        if (result.success) {
            currentResults = result.data;
            displayResults(result.data, title);
        } else {
            showError('Search failed: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        showError('Network error: ' + error.message);
    }
}

// Display search results
function displayResults(talents, title) {
    // Hide welcome message and show results
    document.getElementById('welcomeMessage').classList.add('hidden');
    document.getElementById('resultsSection').classList.remove('hidden');
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('resultsTitle').textContent = title;
    document.getElementById('resultsCount').textContent = `(${talents.length} found)`;

    const container = document.getElementById('resultsContainer');

    if (talents.length === 0) {
        container.innerHTML = '';
        document.getElementById('emptyState').classList.remove('hidden');
        return;
    }

    document.getElementById('emptyState').classList.add('hidden');
    container.innerHTML = talents.map(talent => createTalentCard(talent)).join('');
}

// Create talent card HTML
function createTalentCard(talent) {
    const skills = talent.skills.slice(0, 3); // Show first 3 skills
    const remainingSkills = talent.skills.length - 3;

    return `
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 cursor-pointer"
             onclick="showTalentProfile(${talent.id})">
            <div class="flex items-start space-x-4">
                <img src="${talent.avatar || '/images/default-avatar.png'}"
                     alt="${talent.name}"
                     class="w-16 h-16 rounded-full object-cover">

                <div class="flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-semibold text-gray-900">${talent.name}</h3>
                        ${talent.match_score ? `<span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">${Math.round(talent.match_score)}% match</span>` : ''}
                    </div>

                    <p class="text-gray-600 text-sm mb-3">${talent.bio || 'No bio available'}</p>

                    <div class="flex flex-wrap gap-2 mb-3">
                        ${skills.map(skill => `
                            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded-full">
                                ${skill.name} (${skill.level})
                            </span>
                        `).join('')}
                        ${remainingSkills > 0 ? `<span class="text-xs text-gray-500">+${remainingSkills} more</span>` : ''}
                    </div>

                    <div class="flex justify-between items-center text-sm text-gray-500">
                        <span><i class="fas fa-trophy mr-1"></i>${talent.skill_count} skills</span>
                        <span><i class="fas fa-star mr-1"></i>${talent.experience_level}</span>
                        ${talent.hourly_rate ? `<span><i class="fas fa-dollar-sign mr-1"></i>$${talent.hourly_rate}/hr</span>` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Show talent profile in modal
async function showTalentProfile(talentId) {
    try {
        const response = await fetch(`/recruiter/discovery/talent/${talentId}`);
        const result = await response.json();

        if (result.success) {
            const talent = result.data;
            document.getElementById('talentModalContent').innerHTML = createTalentProfileHTML(talent);
            document.getElementById('talentModal').classList.remove('hidden');
        } else {
            showError('Failed to load talent profile');
        }
    } catch (error) {
        showError('Error loading profile: ' + error.message);
    }
}

// Create talent profile HTML for modal
function createTalentProfileHTML(talent) {
    return `
        <div class="space-y-6">
            <div class="flex items-center space-x-4">
                <img src="${talent.avatar || '/images/default-avatar.png'}"
                     alt="${talent.name}"
                     class="w-20 h-20 rounded-full object-cover">
                <div>
                    <h4 class="text-xl font-semibold text-gray-900">${talent.name}</h4>
                    <p class="text-gray-600">${talent.experience_level} level</p>
                    ${talent.hourly_rate ? `<p class="text-green-600 font-medium">$${talent.hourly_rate}/hour</p>` : ''}
                </div>
            </div>

            ${talent.bio ? `
                <div>
                    <h5 class="font-medium text-gray-900 mb-2">Bio</h5>
                    <p class="text-gray-600">${talent.bio}</p>
                </div>
            ` : ''}

            <div>
                <h5 class="font-medium text-gray-900 mb-3">Skills (${talent.skill_count})</h5>
                <div class="grid grid-cols-2 gap-2">
                    ${talent.skills.map(skill => `
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="font-medium text-gray-900">${skill.name}</div>
                            <div class="text-sm text-gray-600 capitalize">${skill.level} level</div>
                            <div class="text-xs text-gray-500">From ${skill.acquired_from}</div>
                        </div>
                    `).join('')}
                </div>
            </div>

            ${talent.specializations.length > 0 ? `
                <div>
                    <h5 class="font-medium text-gray-900 mb-2">Specializations</h5>
                    <div class="flex flex-wrap gap-2">
                        ${talent.specializations.map(spec => `
                            <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">${spec}</span>
                        `).join('')}
                    </div>
                </div>
            ` : ''}

            <div class="flex gap-3 pt-4">
                <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-envelope mr-2"></i>Contact Talent
                </button>
                ${talent.portfolio_url ? `
                    <a href="${talent.portfolio_url}" target="_blank"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-external-link-alt mr-2"></i>View Portfolio
                    </a>
                ` : ''}
            </div>
        </div>
    `;
}

// Close talent modal
function closeTalentModal() {
    document.getElementById('talentModal').classList.add('hidden');
}

// Show loading state
function showLoading() {
    document.getElementById('resultsSection').classList.remove('hidden');
    document.getElementById('loadingState').classList.remove('hidden');
    document.getElementById('resultsContainer').innerHTML = '';
    document.getElementById('emptyState').classList.add('hidden');
}

// Show error message
function showError(message) {
    document.getElementById('loadingState').classList.add('hidden');
    alert('Error: ' + message);
}

// Clear all filters
function clearFilters() {
    document.getElementById('skillSearch').value = '';
    document.getElementById('experienceLevel').value = '';
    document.getElementById('minExperience').value = '';
    document.getElementById('resultsSection').classList.add('hidden');
    document.getElementById('welcomeMessage').classList.remove('hidden');
}

// Toggle view (grid/list)
function toggleView(view) {
    currentView = view;
    document.getElementById('gridViewBtn').className = view === 'grid'
        ? 'p-2 text-purple-600 transition-colors duration-200'
        : 'p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200';
    document.getElementById('listViewBtn').className = view === 'list'
        ? 'p-2 text-purple-600 transition-colors duration-200'
        : 'p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200';

    if (currentResults.length > 0) {
        displayResults(currentResults, document.getElementById('resultsTitle').textContent);
    }
}

// Load analytics on page load
document.addEventListener('DOMContentLoaded', function() {
    loadAnalytics();
});

// Load analytics
async function loadAnalytics() {
    try {
        const response = await fetch('/recruiter/discovery/analytics');
        const result = await response.json();

        if (result.success) {
            displayAnalytics(result.data);
        }
    } catch (error) {
        console.error('Failed to load analytics:', error);
    }
}

// Display analytics
function displayAnalytics(analytics) {
    const container = document.getElementById('analyticsCards');
    container.innerHTML = `
        <div class="bg-purple-50 rounded-lg p-4">
            <div class="text-2xl font-bold text-purple-600">${analytics.total_talents}</div>
            <div class="text-sm text-gray-600">Total Talents</div>
        </div>
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="text-2xl font-bold text-blue-600">${Object.keys(analytics.skill_distribution).length}</div>
            <div class="text-sm text-gray-600">Unique Skills</div>
        </div>
        <div class="bg-green-50 rounded-lg p-4">
            <div class="text-2xl font-bold text-green-600">${analytics.experience_distribution.expert || 0}</div>
            <div class="text-sm text-gray-600">Expert Level</div>
        </div>
        <div class="bg-orange-50 rounded-lg p-4">
            <div class="text-2xl font-bold text-orange-600">${Object.keys(analytics.specialization_distribution).length}</div>
            <div class="text-sm text-gray-600">Specializations</div>
        </div>
    `;
}
</script>
@endpush
@endsection
