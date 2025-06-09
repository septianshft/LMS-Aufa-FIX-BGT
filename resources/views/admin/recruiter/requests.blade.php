@extends('layout.template.mainTemplate')

@section('title', 'My Talent Requests')
@section('container')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Page Heading -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Talent Requests</h1>
            <p class="text-gray-600">Track and manage your talent collaboration requests</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('recruiter.dashboard') }}"
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium shadow-lg">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Requests Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 bg-gradient-to-r from-indigo-600 to-purple-700 text-white">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-clipboard-list text-xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold">All My Talent Requests</h2>
                    <p class="text-indigo-100 text-sm">Complete overview of your submissions</p>
                </div>
            </div>
        </div>

        <div class="p-8">
            @if($requests->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden lg:block">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b-2 border-gray-200">
                                    <th class="text-left py-4 px-4 font-semibold text-gray-700 uppercase tracking-wider text-sm">Talent</th>
                                    <th class="text-left py-4 px-4 font-semibold text-gray-700 uppercase tracking-wider text-sm">Project Details</th>
                                    <th class="text-left py-4 px-4 font-semibold text-gray-700 uppercase tracking-wider text-sm">Budget & Duration</th>
                                    <th class="text-left py-4 px-4 font-semibold text-gray-700 uppercase tracking-wider text-sm">Status</th>
                                    <th class="text-left py-4 px-4 font-semibold text-gray-700 uppercase tracking-wider text-sm">Submitted</th>
                                    <th class="text-left py-4 px-4 font-semibold text-gray-700 uppercase tracking-wider text-sm">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($requests as $request)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="py-6 px-4">
                                            <div class="flex items-center">
                                                @if($request->talent->user->avatar)
                                                    <img class="w-14 h-14 rounded-2xl object-cover mr-4 shadow-md border-2 border-white"
                                                         src="{{ asset('storage/' . $request->talent->user->avatar) }}"
                                                         alt="{{ $request->talent->user->name }}">
                                                @else
                                                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mr-4 shadow-md">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="font-semibold text-gray-900 text-lg">{{ $request->talent->user->name }}</div>
                                                    <div class="text-gray-500 text-sm">{{ $request->talent->user->email }}</div>
                                                    @if($request->talent->user->pekerjaan)
                                                        <div class="text-gray-400 text-xs mt-1 px-2 py-1 bg-gray-100 rounded-full inline-block">
                                                            {{ $request->talent->user->pekerjaan }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-6 px-4">
                                            <div class="max-w-xs">
                                                <div class="font-semibold text-indigo-600 text-lg mb-2">{{ $request->project_title }}</div>
                                                <p class="text-gray-600 text-sm leading-relaxed mb-3">{{ Str::limit($request->project_description, 120) }}</p>
                                                @if($request->requirements)
                                                    <div class="text-gray-500 text-xs mb-2">
                                                        <span class="font-medium">Requirements:</span> {{ Str::limit($request->requirements, 80) }}
                                                    </div>
                                                @endif
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                                    @if($request->urgency_level == 'high') bg-red-100 text-red-800 border border-red-200
                                                    @elseif($request->urgency_level == 'medium') bg-yellow-100 text-yellow-800 border border-yellow-200
                                                    @else bg-blue-100 text-blue-800 border border-blue-200 @endif">
                                                    <div class="w-2 h-2 rounded-full mr-2
                                                        @if($request->urgency_level == 'high') bg-red-400
                                                        @elseif($request->urgency_level == 'medium') bg-yellow-400
                                                        @else bg-blue-400 @endif"></div>
                                                    {{ ucfirst($request->urgency_level) }} Priority
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-6 px-4">
                                            <div class="space-y-2">
                                                @if($request->budget_range)
                                                    <div class="flex items-center text-sm text-gray-600">
                                                        <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center mr-2">
                                                            <i class="fas fa-dollar-sign text-green-600 text-xs"></i>
                                                        </div>
                                                        <span class="font-medium">{{ $request->budget_range }}</span>
                                                    </div>
                                                @endif
                                                @if($request->project_duration)
                                                    <div class="flex items-center text-sm text-gray-600">
                                                        <div class="w-6 h-6 bg-purple-100 rounded-lg flex items-center justify-center mr-2">
                                                            <i class="fas fa-clock text-purple-600 text-xs"></i>
                                                        </div>
                                                        <span class="font-medium">{{ $request->project_duration }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-6 px-4">
                                            <div class="space-y-3">
                                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                                                    @if($request->getStatusBadgeColor() == 'success') bg-green-100 text-green-800 border-2 border-green-200
                                                    @elseif($request->getStatusBadgeColor() == 'warning') bg-yellow-100 text-yellow-800 border-2 border-yellow-200
                                                    @elseif($request->getStatusBadgeColor() == 'info') bg-blue-100 text-blue-800 border-2 border-blue-200
                                                    @elseif($request->getStatusBadgeColor() == 'danger') bg-red-100 text-red-800 border-2 border-red-200
                                                    @else bg-gray-100 text-gray-800 border-2 border-gray-200 @endif">
                                                    <div class="w-2 h-2 rounded-full mr-2
                                                        @if($request->getStatusBadgeColor() == 'success') bg-green-400
                                                        @elseif($request->getStatusBadgeColor() == 'warning') bg-yellow-400
                                                        @elseif($request->getStatusBadgeColor() == 'info') bg-blue-400
                                                        @elseif($request->getStatusBadgeColor() == 'danger') bg-red-400
                                                        @else bg-gray-400 @endif"></div>
                                                    {{ $request->getFormattedStatus() }}
                                                </span>

                                                @if($request->admin_notes)
                                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                                        <div class="text-gray-700 text-xs font-medium mb-1">Admin Notes:</div>
                                                        <div class="text-gray-600 text-xs">{{ $request->admin_notes }}</div>
                                                    </div>
                                                @endif

                                                <div class="space-y-1">
                                                    @if($request->approved_at)
                                                        <div class="flex items-center text-green-600 text-xs">
                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            <span>Approved {{ $request->approved_at->format('M d, Y') }}</span>
                                                        </div>
                                                    @endif

                                                    @if($request->meeting_arranged_at)
                                                        <div class="flex items-center text-blue-600 text-xs">
                                                            <i class="fas fa-calendar mr-1"></i>
                                                            <span>Meeting arranged {{ $request->meeting_arranged_at->format('M d, Y') }}</span>
                                                        </div>
                                                    @endif

                                                    @if($request->onboarded_at)
                                                        <div class="flex items-center text-green-600 text-xs">
                                                            <i class="fas fa-handshake mr-1"></i>
                                                            <span>Onboarded {{ $request->onboarded_at->format('M d, Y') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-6 px-4">
                                            <div class="text-gray-900 font-medium text-sm">{{ $request->created_at->format('M d, Y') }}</div>
                                            <div class="text-gray-500 text-xs">{{ $request->created_at->format('H:i') }}</div>
                                            <div class="text-gray-400 text-xs mt-1">{{ $request->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="py-6 px-4">
                                            <div class="space-y-2">
                                                <button type="button"
                                                        class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all duration-200 font-medium text-sm"
                                                        onclick="viewRequestDetails('{{ addslashes($request->project_title) }}', '{{ addslashes($request->project_description) }}', '{{ addslashes($request->requirements ?? '') }}', '{{ $request->budget_range ?? 'Not specified' }}', '{{ $request->project_duration ?? 'Not specified' }}', '{{ ucfirst($request->urgency_level) }}', '{{ addslashes($request->recruiter_message ?? '') }}')">
                                                    <i class="fas fa-eye mr-1"></i> View Details
                                                </button>

                                                @if($request->status == 'approved' || $request->status == 'meeting_arranged')
                                                    <a href="mailto:{{ $request->talent->user->email }}"
                                                       class="block w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 font-medium text-sm text-center">
                                                        <i class="fas fa-envelope mr-1"></i> Contact Talent
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-6">
                    @foreach($requests as $request)
                        <div class="bg-white border-2 border-gray-100 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover-lift">
                            <!-- Mobile Card Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    @if($request->talent->user->avatar)
                                        <img class="w-12 h-12 rounded-xl object-cover mr-3 shadow-md"
                                             src="{{ asset('storage/' . $request->talent->user->avatar) }}"
                                             alt="{{ $request->talent->user->name }}">
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3 shadow-md">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $request->talent->user->name }}</div>
                                        <div class="text-gray-500 text-sm">{{ $request->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold status-badge
                                    @if($request->getStatusBadgeColor() == 'success') bg-green-100 text-green-800
                                    @elseif($request->getStatusBadgeColor() == 'warning') bg-yellow-100 text-yellow-800
                                    @elseif($request->getStatusBadgeColor() == 'info') bg-blue-100 text-blue-800
                                    @elseif($request->getStatusBadgeColor() == 'danger') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $request->getFormattedStatus() }}
                                </span>
                            </div>

                            <!-- Mobile Card Content -->
                            <div class="space-y-4">
                                <div>
                                    <div class="font-semibold text-indigo-600 text-lg mb-2">{{ $request->project_title }}</div>
                                    <p class="text-gray-600 text-sm">{{ Str::limit($request->project_description, 150) }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    @if($request->budget_range)
                                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-3 rounded-xl border border-gray-200">
                                            <div class="text-gray-500 text-xs font-medium uppercase tracking-wide">Budget</div>
                                            <div class="text-gray-900 font-semibold mt-1">{{ $request->budget_range }}</div>
                                        </div>
                                    @endif
                                    @if($request->project_duration)
                                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-3 rounded-xl border border-gray-200">
                                            <div class="text-gray-500 text-xs font-medium uppercase tracking-wide">Duration</div>
                                            <div class="text-gray-900 font-semibold mt-1">{{ $request->project_duration }}</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex gap-2">
                                    <button type="button"
                                            class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl"
                                            onclick="viewRequestDetails('{{ addslashes($request->project_title) }}', '{{ addslashes($request->project_description) }}', '{{ addslashes($request->requirements ?? '') }}', '{{ $request->budget_range ?? 'Not specified' }}', '{{ $request->project_duration ?? 'Not specified' }}', '{{ ucfirst($request->urgency_level) }}', '{{ addslashes($request->recruiter_message ?? '') }}')">
                                        <i class="fas fa-eye mr-1"></i> Details
                                    </button>
                                    @if($request->status == 'approved' || $request->status == 'meeting_arranged')
                                        <a href="mailto:{{ $request->talent->user->email }}"
                                           class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 font-medium text-sm text-center shadow-lg hover:shadow-xl">
                                            <i class="fas fa-envelope mr-1"></i> Contact
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-12 pt-8 border-t border-gray-200">
                    <div class="pagination-wrapper">
                        {{ $requests->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-clipboard-list text-4xl text-gray-400"></i>
                    </div>
                    <h5 class="text-xl font-semibold text-gray-700 mb-3">No Talent Requests Yet</h5>
                    <p class="text-gray-500 max-w-md mx-auto mb-6">You haven't submitted any talent requests yet. Start discovering and connecting with talented professionals.</p>
                    <a href="{{ route('recruiter.dashboard') }}"
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium shadow-lg">
                        <i class="fas fa-search mr-2"></i>
                        Discover Talents
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Request Details Modal -->
<div id="requestDetailsModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeRequestModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-clipboard-list text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white" id="modal-title">Request Details</h3>
                    </div>
                    <button type="button"
                            class="text-white hover:text-gray-200 transition-colors duration-200 p-2 hover:bg-white hover:bg-opacity-20 rounded-lg"
                            onclick="closeRequestModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="bg-white px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Project Title</label>
                            <p id="modalProjectTitle" class="text-gray-900 text-lg font-medium"></p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Budget Range</label>
                            <p id="modalBudgetRange" class="text-gray-900 font-medium"></p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Project Duration</label>
                            <p id="modalProjectDuration" class="text-gray-900 font-medium"></p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Urgency Level</label>
                            <p id="modalUrgencyLevel" class="text-gray-900 font-medium"></p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Project Description</label>
                            <p id="modalProjectDescription" class="text-gray-900 leading-relaxed"></p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Requirements</label>
                            <p id="modalRequirements" class="text-gray-900 leading-relaxed"></p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Personal Message</label>
                            <p id="modalRecruiterMessage" class="text-gray-900 leading-relaxed"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex justify-end">
                    <button type="button"
                            class="px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-200 font-medium shadow-lg"
                            onclick="closeRequestModal()">
                        <i class="fas fa-times mr-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewRequestDetails(title, description, requirements, budget, duration, urgency, message) {
    // Populate modal content
    document.getElementById('modalProjectTitle').textContent = title;
    document.getElementById('modalProjectDescription').textContent = description;
    document.getElementById('modalRequirements').textContent = requirements || 'Not specified';
    document.getElementById('modalBudgetRange').textContent = budget;
    document.getElementById('modalProjectDuration').textContent = duration;
    document.getElementById('modalUrgencyLevel').textContent = urgency;
    document.getElementById('modalRecruiterMessage').textContent = message || 'No personal message';

    // Show modal
    const modal = document.getElementById('requestDetailsModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Add fade-in animation
    setTimeout(() => {
        modal.classList.add('animate-fade-in');
    }, 10);
}

function closeRequestModal() {
    const modal = document.getElementById('requestDetailsModal');
    modal.classList.add('animate-fade-out');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('animate-fade-in', 'animate-fade-out');
        document.body.style.overflow = 'auto';
    }, 200);
}

// Close modal when pressing Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('requestDetailsModal');
        if (!modal.classList.contains('hidden')) {
            closeRequestModal();
        }
    }
});

// Add smooth transitions for better UX
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to action buttons
    const actionButtons = document.querySelectorAll('.bg-indigo-600, .bg-green-600');
    actionButtons.forEach(button => {
        button.addEventListener('mouseover', function() {
            this.style.transform = 'translateY(-1px)';
            this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
        });

        button.addEventListener('mouseout', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
});
</script>

<style>
/* Custom animations for modal */
.animate-fade-in {
    animation: fadeIn 0.2s ease-out;
}

.animate-fade-out {
    animation: fadeOut 0.2s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
        transform: scale(1);
    }
    to {
        opacity: 0;
        transform: scale(0.95);
    }
}

/* Enhanced pagination styling */
.pagination-wrapper .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
}

.pagination-wrapper .page-link {
    padding: 0.75rem 1rem;
    background: white;
    border: 2px solid #e5e7eb;
    color: #6b7280;
    border-radius: 0.75rem;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
}

.pagination-wrapper .page-link:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
    color: #374151;
    transform: translateY(-1px);
}

.pagination-wrapper .page-item.active .page-link {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-color: #4f46e5;
    color: white;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.pagination-wrapper .page-item.disabled .page-link {
    background: #f9fafb;
    border-color: #f3f4f6;
    color: #d1d5db;
    cursor: not-allowed;
}

/* Card hover effects */
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Status badge animations */
.status-badge {
    transition: all 0.2s ease;
}

.status-badge:hover {
    transform: scale(1.05);
}
</style>
@endsection
