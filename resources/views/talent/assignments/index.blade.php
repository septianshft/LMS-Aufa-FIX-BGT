@extends('layout.template.mainTemplate')

@section('title', 'My Assignments - Talent')
@section('container')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Project Assignments</h1>
            <p class="text-gray-600 mt-2">Manage your project assignments and track progress</p>
        </div>
        <div class="flex space-x-3">
            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                {{ $pendingCount }} Pending Response
            </span>
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                {{ $activeCount }} Active Projects
            </span>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6">
        <nav class="flex space-x-8" aria-label="Tabs">
            <a href="{{ route('talent.assignments.index') }}"
               class="@if(request('status') == '' || !request('status')) text-blue-600 border-blue-500 @else text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                All Assignments
            </a>
            <a href="{{ route('talent.assignments.index', ['status' => 'pending']) }}"
               class="@if(request('status') == 'pending') text-blue-600 border-blue-500 @else text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Pending Response
            </a>
            <a href="{{ route('talent.assignments.index', ['status' => 'accepted']) }}"
               class="@if(request('status') == 'accepted') text-blue-600 border-blue-500 @else text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Active Projects
            </a>
            <a href="{{ route('talent.assignments.index', ['status' => 'completed']) }}"
               class="@if(request('status') == 'completed') text-blue-600 border-blue-500 @else text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Completed
            </a>
        </nav>
    </div>

    <!-- Assignments Grid -->
    <div class="grid gap-6">
        @forelse($assignments as $assignment)
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
                <div class="p-6">
                    <!-- Assignment Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-1">{{ $assignment->project->title }}</h3>
                            <p class="text-gray-600">{{ $assignment->project->recruiter->company_name ?? 'Unknown Company' }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="@if($assignment->status === 'pending') bg-yellow-100 text-yellow-800 @elseif($assignment->status === 'accepted') bg-green-100 text-green-800 @elseif($assignment->status === 'declined') bg-red-100 text-red-800 @else bg-gray-100 text-gray-800 @endif px-3 py-1 rounded-full text-sm font-medium">
                                {{ ucfirst($assignment->status) }}
                            </span>
                            <span class="@if($assignment->project->status === 'pending') bg-yellow-100 text-yellow-800 @elseif($assignment->project->status === 'approved') bg-green-100 text-green-800 @elseif($assignment->project->status === 'active') bg-blue-100 text-blue-800 @elseif($assignment->project->status === 'completed') bg-gray-100 text-gray-800 @else bg-red-100 text-red-800 @endif px-2 py-1 rounded-full text-xs font-medium">
                                Project: {{ ucfirst($assignment->project->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Assignment Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Assignment Details</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Duration:</span>
                                    <span class="font-medium">{{ $assignment->project->duration_weeks }} weeks</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Assigned:</span>
                                    <span class="font-medium">{{ $assignment->created_at->format('M j, Y') }}</span>
                                </div>
                                @if($assignment->responded_at)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Responded:</span>
                                        <span class="font-medium">{{ $assignment->responded_at->format('M j, Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Project Info</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Budget:</span>
                                    <span class="font-medium">${{ number_format($assignment->project->budget) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Skills Required:</span>
                                    <span class="font-medium">{{ $assignment->project->required_skills }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Experience:</span>
                                    <span class="font-medium">{{ ucfirst($assignment->project->experience_level) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Team Size:</span>
                                    <span class="font-medium">{{ $assignment->project->required_talents }} talents</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Timeline</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Start:</span>
                                    <span class="font-medium">{{ $assignment->project->start_date ? $assignment->project->start_date->format('M j, Y') : 'TBD' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">End:</span>
                                    <span class="font-medium">{{ $assignment->project->end_date ? $assignment->project->end_date->format('M j, Y') : 'TBD' }}</span>
                                </div>
                                @if($assignment->project->start_date && $assignment->project->end_date)
                                    <div class="mt-2">
                                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                                            <span>Progress</span>
                                            <span>{{ $assignment->project->getProgressPercentage() }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $assignment->project->getProgressPercentage() }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Project Description -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-2">Project Description</h4>
                        <p class="text-gray-700 text-sm">{{ Str::limit($assignment->project->description, 200) }}</p>
                    </div>

                    <!-- Assignment Notes -->
                    @if($assignment->notes)
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Assignment Notes</h4>
                            <p class="text-gray-700 text-sm">{{ $assignment->notes }}</p>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center">
                        <a href="{{ route('talent.assignments.show', $assignment) }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                            View Details
                        </a>

                        @if($assignment->status === 'pending')
                            <div class="flex space-x-3">
                                <button onclick="respondToAssignment({{ $assignment->id }}, 'accepted')"
                                        class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                                    Accept Assignment
                                </button>
                                <button onclick="respondToAssignment({{ $assignment->id }}, 'declined')"
                                        class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                                    Decline
                                </button>
                            </div>
                        @elseif($assignment->status === 'accepted' && $assignment->project->status === 'active')
                            <div class="flex space-x-3">
                                <button onclick="openProgressModal({{ $assignment->id }})"
                                        class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-700 transition-colors">
                                    Update Progress
                                </button>
                                @if($assignment->project->canRequestExtension())
                                    <button onclick="openExtensionModal({{ $assignment->id }})"
                                            class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors">
                                        Request Extension
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No assignments found</h3>
                <p class="mt-1 text-sm text-gray-500">You don't have any assignments matching your current filter.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($assignments->hasPages())
        <div class="mt-8">
            {{ $assignments->links() }}
        </div>
    @endif
</div>

<!-- Response Modal -->
<div id="responseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 id="responseModalTitle" class="text-lg font-medium text-gray-900 mb-4"></h3>
            <form id="responseForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Response Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Add any comments about your decision..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeResponseModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="responseSubmitBtn"
                            class="px-4 py-2 rounded-lg transition-colors">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Progress Update Modal -->
<div id="progressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Update Progress</h3>
            <form id="progressForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Progress Update</label>
                    <textarea name="progress_update" rows="4" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Describe your current progress, completed tasks, and next steps..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Completion Percentage</label>
                    <input type="range" name="completion_percentage" min="0" max="100" value="0"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                           oninput="updateProgressValue(this.value)">
                    <div class="flex justify-between text-xs text-gray-600 mt-1">
                        <span>0%</span>
                        <span id="progressValue">0%</span>
                        <span>100%</span>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeProgressModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Update Progress
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Extension Request Modal -->
<div id="extensionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Request Project Extension</h3>
            <form id="extensionForm" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Weeks</label>
                    <input type="number" name="additional_weeks" min="1" max="12" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Budget ($)</label>
                    <input type="number" name="additional_budget" min="0" step="100"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Extension</label>
                    <textarea name="reason" rows="4" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Explain why you need this extension..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeExtensionModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function respondToAssignment(assignmentId, status) {
    const modal = document.getElementById('responseModal');
    const form = document.getElementById('responseForm');
    const title = document.getElementById('responseModalTitle');
    const submitBtn = document.getElementById('responseSubmitBtn');

    form.action = `/talent/assignments/${assignmentId}/respond`;
    form.querySelector('input[name="_method"]').value = 'PUT';

    // Add hidden status field
    let statusInput = form.querySelector('input[name="status"]');
    if (!statusInput) {
        statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        form.appendChild(statusInput);
    }
    statusInput.value = status;

    if (status === 'accepted') {
        title.textContent = 'Accept Assignment';
        submitBtn.textContent = 'Accept Assignment';
        submitBtn.className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors';
    } else {
        title.textContent = 'Decline Assignment';
        submitBtn.textContent = 'Decline Assignment';
        submitBtn.className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors';
    }

    modal.classList.remove('hidden');
}

function closeResponseModal() {
    document.getElementById('responseModal').classList.add('hidden');
}

function openProgressModal(assignmentId) {
    const modal = document.getElementById('progressModal');
    const form = document.getElementById('progressForm');

    form.action = `/talent/assignments/${assignmentId}/progress`;
    modal.classList.remove('hidden');
}

function closeProgressModal() {
    document.getElementById('progressModal').classList.add('hidden');
}

function updateProgressValue(value) {
    document.getElementById('progressValue').textContent = value + '%';
}

function openExtensionModal(assignmentId) {
    const modal = document.getElementById('extensionModal');
    const form = document.getElementById('extensionForm');

    form.action = `/talent/assignments/${assignmentId}/request-extension`;
    modal.classList.remove('hidden');
}

function closeExtensionModal() {
    document.getElementById('extensionModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const responseModal = document.getElementById('responseModal');
    const progressModal = document.getElementById('progressModal');
    const extensionModal = document.getElementById('extensionModal');

    if (event.target === responseModal) {
        closeResponseModal();
    }
    if (event.target === progressModal) {
        closeProgressModal();
    }
    if (event.target === extensionModal) {
        closeExtensionModal();
    }
}
</script>
@endsection
