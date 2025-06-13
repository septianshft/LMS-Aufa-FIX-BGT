@extends('layout.template.mainTemplate')

@section('title', 'My Requests')
@section('container')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Premium Page Header -->
    <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 to-purple-600/5"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%236366f1" fill-opacity="0.03"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-40"></div>

        <div class="relative max-w-7xl mx-auto px-6 py-12">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-8 lg:mb-0">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                            <i class="fas fa-tasks text-2xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl lg:text-5xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent mb-2">
                                My Requests
                            </h1>
                            <p class="text-lg text-gray-600 font-medium">Track all collaboration requests and opportunities</p>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="flex flex-wrap gap-4 mt-6">
                        <div class="bg-white/70 backdrop-blur-sm rounded-xl px-4 py-2 border border-white/50">
                            <span class="text-sm text-gray-600">Total Requests:</span>
                            <span class="font-bold text-blue-600 ml-1">{{ $requests->total() ?? $requests->count() }}</span>
                        </div>
                        <div class="bg-white/70 backdrop-blur-sm rounded-xl px-4 py-2 border border-white/50">
                            <span class="text-sm text-gray-600">This Page:</span>
                            <span class="font-bold text-purple-600 ml-1">{{ $requests->count() }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button onclick="window.location.reload()"
                            class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm text-gray-700 rounded-xl hover:bg-white/30 transition-all duration-300 font-medium border border-white/30 shadow-lg">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Refresh
                    </button>
                    <a href="{{ route('talent.dashboard') }}"
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 font-medium shadow-xl hover:shadow-2xl hover:scale-105">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-6 pb-12">
        <!-- Filter and Sort Controls -->
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Search and Filters -->
                <div class="flex flex-col sm:flex-row gap-3 flex-1">
                    <div class="relative flex-1 min-w-[300px]">
                        <input type="text" id="searchInput" placeholder="Search by project title, recruiter, or company..."
                               class="w-full pl-12 pr-4 py-3 bg-white/60 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>

                    <div class="flex gap-2">
                        <!-- Status Filter -->
                        <div class="relative">
                            <button id="statusFilterBtn" class="px-6 py-3 bg-white/60 border border-gray-200 rounded-xl hover:bg-white transition-all duration-300 flex items-center gap-2 min-w-[140px]">
                                <i class="fas fa-filter text-gray-500"></i>
                                <span id="statusFilterText">All Status</span>
                                <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                            </button>
                            <div id="statusFilterDropdown" class="absolute top-full left-0 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 min-w-[200px] z-50 hidden">
                                <div class="p-2">
                                    <button class="status-filter-option w-full text-left px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors" data-status="">
                                        All Status
                                    </button>
                                    <button class="status-filter-option w-full text-left px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors" data-status="pending">
                                        <span class="inline-block w-2 h-2 bg-yellow-500 rounded-full mr-3"></span>
                                        Pending
                                    </button>
                                    <button class="status-filter-option w-full text-left px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors" data-status="accepted">
                                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                        Accepted
                                    </button>
                                    <button class="status-filter-option w-full text-left px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors" data-status="rejected">
                                        <span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-3"></span>
                                        Rejected
                                    </button>
                                    <button class="status-filter-option w-full text-left px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors" data-status="completed">
                                        <span class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                        Completed
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Urgency Filter -->
                        <div class="relative">
                            <button id="urgencyFilterBtn" class="px-6 py-3 bg-white/60 border border-gray-200 rounded-xl hover:bg-white transition-all duration-300 flex items-center gap-2 min-w-[140px]">
                                <i class="fas fa-exclamation-triangle text-gray-500"></i>
                                <span id="urgencyFilterText">All Urgency</span>
                                <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                            </button>
                            <div id="urgencyFilterDropdown" class="absolute top-full left-0 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 min-w-[200px] z-50 hidden">
                                <div class="p-2">
                                    <button class="urgency-filter-option w-full text-left px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors" data-urgency="">
                                        All Urgency
                                    </button>
                                    <button class="urgency-filter-option w-full text-left px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors" data-urgency="low">
                                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                        Low
                                    </button>
                                    <button class="urgency-filter-option w-full text-left px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors" data-urgency="medium">
                                        <span class="inline-block w-2 h-2 bg-yellow-500 rounded-full mr-3"></span>
                                        Medium
                                    </button>
                                    <button class="urgency-filter-option w-full text-left px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors" data-urgency="high">
                                        <span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-3"></span>
                                        High
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sort Controls -->
                <div class="flex gap-2">
                    <div class="relative">
                        <button id="sortBtn" class="px-6 py-3 bg-white/60 border border-gray-200 rounded-xl hover:bg-white transition-all duration-300 flex items-center gap-2 min-w-[140px]">
                            <i class="fas fa-sort text-gray-500"></i>
                            <span id="sortText">Latest First</span>
                            <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                        </button>
                        <div id="sortDropdown" class="absolute top-full right-0 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 min-w-[200px] z-50 hidden">
                            <div class="p-2">
                                <button class="sort-option w-full text-left px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors" data-sort="latest">
                                    <i class="fas fa-clock mr-3 text-gray-400"></i>
                                    Latest First
                                </button>
                                <button class="sort-option w-full text-left px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors" data-sort="oldest">
                                    <i class="fas fa-history mr-3 text-gray-400"></i>
                                    Oldest First
                                </button>
                                <button class="sort-option w-full text-left px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors" data-sort="title">
                                    <i class="fas fa-sort-alpha-down mr-3 text-gray-400"></i>
                                    Project Title A-Z
                                </button>
                                <button class="sort-option w-full text-left px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors" data-sort="recruiter">
                                    <i class="fas fa-user-tie mr-3 text-gray-400"></i>
                                    Recruiter A-Z
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requests Grid -->
        <div id="requestsContainer" class="space-y-6">
            @if($requests->count() > 0)
                @foreach($requests as $request)
                    @php $displayStatus = $request->getDisplayStatus(); @endphp
                    <div class="request-card bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 overflow-hidden hover:shadow-2xl transition-all duration-300"
                         data-search-content="{{ strtolower($request->project_title . ' ' . $request->recruiter->user->name . ' ' . ($request->recruiter->user->pekerjaan ?? '')) }}"
                         data-status="{{ $displayStatus }}"
                         data-urgency="{{ $request->urgency_level ?? 'medium' }}"
                         data-created="{{ $request->created_at->timestamp }}">

                        <div class="p-6">
                            <!-- Request Header -->
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between mb-4">
                                <div class="flex-1 mb-4 lg:mb-0">
                                    <div class="flex items-start gap-4">
                                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-briefcase text-white text-xl"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $request->project_title }}</h3>
                                            <div class="flex flex-wrap items-center gap-3 mb-3">
                                                <div class="flex items-center text-gray-600">
                                                    <i class="fas fa-user-tie mr-2 text-blue-500"></i>
                                                    <span class="font-medium">{{ $request->recruiter->user->name }}</span>
                                                </div>
                                                @if($request->recruiter->user->pekerjaan)
                                                    <div class="flex items-center text-gray-600">
                                                        <i class="fas fa-building mr-2 text-purple-500"></i>
                                                        <span>{{ $request->recruiter->user->pekerjaan }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Status and Urgency Badges -->
                                            <div class="flex flex-wrap gap-2">
                                                @php
                                                    $displayStatus = $request->getDisplayStatus();
                                                    $statusClasses = $request->getStatusBadgeClasses();
                                                    $urgencyConfig = [
                                                        'low' => ['bg-gray-100', 'text-gray-800', 'border-gray-200'],
                                                        'medium' => ['bg-yellow-100', 'text-yellow-800', 'border-yellow-200'],
                                                        'high' => ['bg-red-100', 'text-red-800', 'border-red-200'],
                                                    ];
                                                    $urgencyClasses = $urgencyConfig[$request->urgency_level] ?? $urgencyConfig['medium'];
                                                @endphp

                                                <span class="px-3 py-1 text-sm font-medium rounded-full border {{ $statusClasses }}">
                                                    {{ ucfirst($displayStatus) }}
                                                </span>
                                                @if($request->urgency_level)
                                                    <span class="px-3 py-1 text-sm font-medium rounded-full border {{ implode(' ', $urgencyClasses) }}">
                                                        {{ ucfirst($request->urgency_level) }} Priority
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2 flex-shrink-0">
                                    <button onclick="openRequestModal({{ $request->id }})"
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 flex items-center gap-2">
                                        <i class="fas fa-eye text-sm"></i>
                                        <span class="hidden sm:inline">View Details</span>
                                    </button>

                                    @php $displayStatus = $request->getDisplayStatus(); @endphp
                                    @if($displayStatus === 'pending')
                                        <button onclick="acceptRequest({{ $request->id }})"
                                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-300 flex items-center gap-2">
                                            <i class="fas fa-check text-sm"></i>
                                            <span class="hidden sm:inline">Accept</span>
                                        </button>
                                        <button onclick="rejectRequest({{ $request->id }})"
                                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-300 flex items-center gap-2">
                                            <i class="fas fa-times text-sm"></i>
                                            <span class="hidden sm:inline">Reject</span>
                                        </button>
                                    @elseif($displayStatus === 'accepted')
                                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-medium">
                                            <i class="fas fa-clock text-sm mr-1"></i>
                                            Awaiting Admin Approval
                                        </span>
                                    @elseif($displayStatus === 'completed')
                                        <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-lg text-sm font-medium">
                                            <i class="fas fa-trophy text-sm mr-1"></i>
                                            Completed
                                        </span>
                                    @elseif($displayStatus === 'rejected')
                                        <span class="px-4 py-2 bg-red-100 text-red-800 rounded-lg text-sm font-medium">
                                            <i class="fas fa-times-circle text-sm mr-1"></i>
                                            Rejected
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Request Details Summary -->
                            <div class="bg-gray-50/50 rounded-xl p-4">
                                <p class="text-gray-700 mb-3 line-clamp-2">{{ $request->project_description }}</p>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-dollar-sign mr-2 text-green-500"></i>
                                        <span>{{ $request->budget_range }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-clock mr-2 text-blue-500"></i>
                                        <span>{{ $request->project_duration }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-calendar mr-2 text-purple-500"></i>
                                        <span>{{ $request->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                @if($requests->hasPages())
                    <div class="flex justify-center mt-8">
                        <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-4">
                            {{ $requests->links() }}
                        </div>
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-inbox text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">No Requests Yet</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        You haven't received any collaboration requests yet. Keep building your profile and showcase your skills to attract opportunities!
                    </p>
                    <a href="{{ route('talent.dashboard') }}"
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 font-medium shadow-xl hover:shadow-2xl hover:scale-105">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Request Details Modal -->
<div id="requestModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6" id="modalContent">
            <!-- Modal content will be loaded here -->
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing components...');

    // Initialize filters and search
    initializeSearch();
    initializeFilters();
    initializeSorting();
    initializeModals();

    console.log('All components initialized successfully');
});

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    const requestCards = document.querySelectorAll('.request-card');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();

        requestCards.forEach(card => {
            const searchContent = card.dataset.searchContent;
            if (searchContent.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

function initializeFilters() {
    console.log('Initializing filters...');

    // Status Filter
    const statusBtn = document.getElementById('statusFilterBtn');
    const statusDropdown = document.getElementById('statusFilterDropdown');
    const statusOptions = document.querySelectorAll('.status-filter-option');

    console.log('Status elements found:', {
        button: !!statusBtn,
        dropdown: !!statusDropdown,
        optionsCount: statusOptions.length
    });

    if (statusBtn && statusDropdown) {
        statusBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            console.log('Status button clicked, toggling dropdown');
            statusDropdown.classList.toggle('hidden');
            // Close other dropdowns
            document.getElementById('urgencyFilterDropdown')?.classList.add('hidden');
            document.getElementById('sortDropdown')?.classList.add('hidden');
        });

        statusOptions.forEach((option, index) => {
            option.addEventListener('click', function() {
                const status = this.dataset.status;
                console.log('Status option clicked:', status, 'Index:', index);
                document.getElementById('statusFilterText').textContent = this.textContent.trim();
                statusDropdown.classList.add('hidden');
                filterByStatus(status);
            });
        });
    }

    // Urgency Filter
    const urgencyBtn = document.getElementById('urgencyFilterBtn');
    const urgencyDropdown = document.getElementById('urgencyFilterDropdown');
    const urgencyOptions = document.querySelectorAll('.urgency-filter-option');

    console.log('Urgency elements found:', {
        button: !!urgencyBtn,
        dropdown: !!urgencyDropdown,
        optionsCount: urgencyOptions.length
    });

    if (urgencyBtn && urgencyDropdown) {
        urgencyBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            console.log('Urgency button clicked, toggling dropdown');
            urgencyDropdown.classList.toggle('hidden');
            // Close other dropdowns
            document.getElementById('statusFilterDropdown')?.classList.add('hidden');
            document.getElementById('sortDropdown')?.classList.add('hidden');
        });

        urgencyOptions.forEach((option, index) => {
            option.addEventListener('click', function() {
                const urgency = this.dataset.urgency;
                console.log('Urgency option clicked:', urgency, 'Index:', index);
                document.getElementById('urgencyFilterText').textContent = this.textContent.trim();
                urgencyDropdown.classList.add('hidden');
                filterByUrgency(urgency);
            });
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.getElementById('statusFilterDropdown')?.classList.add('hidden');
        document.getElementById('urgencyFilterDropdown')?.classList.add('hidden');
        document.getElementById('sortDropdown')?.classList.add('hidden');
    });

    console.log('Filters initialization complete');
}

function filterByStatus(status) {
    const requestCards = document.querySelectorAll('.request-card');

    requestCards.forEach(card => {
        if (!status || card.dataset.status === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function filterByUrgency(urgency) {
    const requestCards = document.querySelectorAll('.request-card');

    requestCards.forEach(card => {
        if (!urgency || card.dataset.urgency === urgency) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function initializeSorting() {
    console.log('Initializing sorting...');

    const sortBtn = document.getElementById('sortBtn');
    const sortDropdown = document.getElementById('sortDropdown');
    const sortOptions = document.querySelectorAll('.sort-option');

    console.log('Sort elements found:', {
        button: !!sortBtn,
        dropdown: !!sortDropdown,
        optionsCount: sortOptions.length
    });

    if (sortBtn && sortDropdown) {
        sortBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            console.log('Sort button clicked, toggling dropdown');
            sortDropdown.classList.toggle('hidden');
            // Close other dropdowns
            document.getElementById('statusFilterDropdown')?.classList.add('hidden');
            document.getElementById('urgencyFilterDropdown')?.classList.add('hidden');
        });

        sortOptions.forEach((option, index) => {
            option.addEventListener('click', function() {
                const sortType = this.dataset.sort;
                console.log('Sort option clicked:', sortType, 'Index:', index);
                document.getElementById('sortText').textContent = this.textContent.trim();
                sortDropdown.classList.add('hidden');
                sortRequests(sortType);
            });
        });
    }

    console.log('Sorting initialization complete');
}

function sortRequests(sortType) {
    const container = document.getElementById('requestsContainer');
    const cards = Array.from(container.querySelectorAll('.request-card'));

    cards.sort((a, b) => {
        switch(sortType) {
            case 'latest':
                return parseInt(b.dataset.created) - parseInt(a.dataset.created);
            case 'oldest':
                return parseInt(a.dataset.created) - parseInt(b.dataset.created);
            case 'title':
                const titleA = a.querySelector('h3').textContent.toLowerCase();
                const titleB = b.querySelector('h3').textContent.toLowerCase();
                return titleA.localeCompare(titleB);
            case 'recruiter':
                const recruiterA = a.dataset.searchContent.split(' ')[1] || '';
                const recruiterB = b.dataset.searchContent.split(' ')[1] || '';
                return recruiterA.localeCompare(recruiterB);
            default:
                return 0;
        }
    });

    cards.forEach(card => container.appendChild(card));
}

function initializeModals() {
    const modal = document.getElementById('requestModal');

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    // Add keyboard support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
}

function openRequestModal(requestId) {
    const modal = document.getElementById('requestModal');
    const modalContent = document.getElementById('modalContent');

    if (!modal || !modalContent) {
        console.error('Modal elements not found');
        return;
    }

    // Show loading state
    modalContent.innerHTML = `
        <div class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600">Loading request details...</span>
        </div>
    `;

    // Show modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Fetch request details
    fetch(`{{ route('talent.api.request_details', '') }}/${requestId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.request) {
            displayRequestDetails(data.request);
        } else {
            throw new Error(data.message || 'Failed to load request details');
        }
    })
    .catch(error => {
        console.error('Error loading request details:', error);
        modalContent.innerHTML = `
            <div class="text-center py-12">
                <div class="text-red-500 mb-4">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Error Loading Request</h3>
                <p class="text-gray-600 mb-4">Unable to load request details. Please try again.</p>
                <button onclick="closeModal()"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    Close
                </button>
            </div>
        `;
    });
}

function displayRequestDetails(request) {
    const modalContent = document.getElementById('modalContent');

    const skillsHtml = request.skills_required && request.skills_required.length > 0
        ? request.skills_required.map(skill => `<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">${skill}</span>`).join('')
        : '<span class="text-gray-500 italic">No specific skills mentioned</span>';

    // Use acceptance_status for talent-friendly status display
    const displayStatus = request.display_status || request.status;
    const acceptanceStatus = request.acceptance_status || request.formatted_status || 'Pending review';
    
    // Use backend-provided status badge color instead of hardcoded mapping
    const statusColor = request.status_badge_color || 'bg-gray-100 text-gray-800';

    modalContent.innerHTML = `
        <div class="flex items-center justify-between mb-6 pb-4 border-b">
            <h2 class="text-2xl font-bold text-gray-900">${request.title}</h2>
            <button onclick="closeModal()"
                    class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                <i class="fas fa-times text-gray-500"></i>
            </button>
        </div>

        <div class="space-y-6">
            <!-- Status and Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 mb-2">Status</h4>
                    <span class="px-3 py-1 rounded-full text-sm font-medium ${statusColor}">${displayStatus.charAt(0).toUpperCase() + displayStatus.slice(1)}</span>
                    <p class="text-xs text-gray-500 mt-1">${acceptanceStatus}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 mb-2">Submitted</h4>
                    <p class="text-gray-600">${request.submitted_at}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 mb-2">Last Updated</h4>
                    <p class="text-gray-600">${request.updated_at}</p>
                </div>
            </div>

            <!-- Recruiter Information -->
            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-700 mb-3">Recruiter Information</h4>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mr-4">
                        ${request.recruiter.avatar
                            ? `<img src="${request.recruiter.avatar}" alt="Avatar" class="w-12 h-12 rounded-full object-cover">`
                            : `<i class="fas fa-user text-white"></i>`
                        }
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">${request.recruiter.name}</p>
                        <p class="text-gray-600">${request.recruiter.email}</p>
                        ${request.recruiter.company ? `<p class="text-blue-600">${request.recruiter.company}</p>` : ''}
                    </div>
                </div>
            </div>

            <!-- Project Details -->
            <div>
                <h4 class="font-semibold text-gray-700 mb-3">Project Description</h4>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-700 leading-relaxed">${request.description}</p>
                </div>
            </div>

            <!-- Project Specifications -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Budget Range</h4>
                    <p class="text-gray-600 bg-gray-50 p-3 rounded-lg">${request.budget_range}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Duration</h4>
                    <p class="text-gray-600 bg-gray-50 p-3 rounded-lg">${request.project_duration}</p>
                </div>
            </div>

            <!-- Skills Required -->
            <div>
                <h4 class="font-semibold text-gray-700 mb-3">Skills Required</h4>
                <div class="flex flex-wrap gap-2">
                    ${skillsHtml}
                </div>
            </div>

            <!-- Collaboration Type -->
            <div>
                <h4 class="font-semibold text-gray-700 mb-3">Collaboration Type</h4>
                <p class="text-gray-600 bg-gray-50 p-3 rounded-lg">${request.collaboration_type}</p>
            </div>

            <!-- Current Status Message -->
            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-700 mb-2">Current Status</h4>
                <p class="text-gray-600">${acceptanceStatus}</p>
            </div>

            <!-- Action Buttons -->
            ${(request.can_accept || request.can_reject) ? `
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">
                    ${request.can_accept ? `
                        <button onclick="acceptRequest(${request.id}); closeModal();"
                                class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                            <i class="fas fa-check mr-2"></i>Accept Request
                        </button>
                    ` : ''}
                    ${request.can_reject ? `
                        <button onclick="rejectRequest(${request.id}); closeModal();"
                                class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                            <i class="fas fa-times mr-2"></i>Reject Request
                        </button>
                    ` : ''}
                </div>
            ` : `
                <div class="pt-4 border-t">
                    <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>${acceptanceStatus}
                        </p>
                    </div>
                    <button onclick="closeModal()"
                            class="w-full px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                        Close
                    </button>
                </div>
            `}
        </div>
    `;
}

function closeModal() {
    const modal = document.getElementById('requestModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function acceptRequest(requestId) {
    if (confirm('Are you sure you want to accept this request?')) {
        fetch(`{{ route('talent.accept_request', '') }}/${requestId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error accepting request: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while accepting the request.');
        });
    }
}

function rejectRequest(requestId) {
    if (confirm('Are you sure you want to reject this request?')) {
        fetch(`{{ route('talent.reject_request', '') }}/${requestId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error rejecting request: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while rejecting the request.');
        });
    }
}
</script>
@endsection
