@extends('layout.template.mainTemplate')

@section('title', 'My Projects')
@section('container')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Projects</h1>
            <p class="text-gray-600 mt-2">Manage your project-based talent recruitment</p>
        </div>
        <a href="{{ route('projects.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
            <i class="fas fa-plus mr-2"></i>Create New Project
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div id="success-notification" class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6 flex items-center justify-between shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-lg"></i>
                <div>
                    <h4 class="font-semibold">Success!</h4>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700 ml-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div id="error-notification" class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-6 flex items-center justify-between shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3 text-lg"></i>
                <div>
                    <h4 class="font-semibold">Error!</h4>
                    <p>{{ session('error') }}</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 ml-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Projects List -->
    @if($projects->count() > 0)
        <div class="grid gap-6">
            @foreach($projects as $project)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <!-- Project Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="hover:text-blue-600 transition duration-200">
                                        {{ $project->title }}
                                    </a>
                                </h3>
                                <p class="text-gray-600 mb-3">{{ Str::limit($project->description, 150) }}</p>
                            </div>

                            <!-- Status Badge -->
                            <div class="ml-4">
                                @php
                                    $statusColors = [
                                        'pending_approval' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-blue-100 text-blue-800',
                                        'active' => 'bg-green-100 text-green-800',
                                        'completed' => 'bg-gray-100 text-gray-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'overdue' => 'bg-red-100 text-red-800',
                                        'extension_requested' => 'bg-orange-100 text-orange-800',
                                        'closure_requested' => 'bg-purple-100 text-purple-800'
                                    ];
                                    $statusClass = $statusColors[$project->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                                    {{ ucwords(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </div>
                        </div>

                        <!-- Project Details -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-industry mr-2"></i>
                                <span>{{ $project->industry ?? 'Not specified' }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-calendar mr-2"></i>
                                <span>{{ $project->expected_start_date->format('M d, Y') }} - {{ $project->expected_end_date->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-clock mr-2"></i>
                                <span>{{ $project->estimated_duration_days }} days</span>
                            </div>
                        </div>

                        <!-- Budget Info -->
                        @if($project->overall_budget_min || $project->overall_budget_max)
                            <div class="flex items-center text-sm text-gray-600 mb-4">
                                <i class="fas fa-rupiah-sign mr-2"></i>
                                <span>
                                    Budget:
                                    @if($project->overall_budget_min && $project->overall_budget_max)
                                        Rp {{ number_format($project->overall_budget_min, 0, ',', '.') }} - Rp {{ number_format($project->overall_budget_max, 0, ',', '.') }}
                                    @elseif($project->overall_budget_min)
                                        From Rp {{ number_format($project->overall_budget_min, 0, ',', '.') }}
                                    @else
                                        Up to Rp {{ number_format($project->overall_budget_max, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                        @endif

                        <!-- Assignment Summary -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 text-sm">
                                <span class="flex items-center text-gray-600">
                                    <i class="fas fa-users mr-2"></i>
                                    {{ $project->assignments->count() }} assigned
                                </span>
                                @if($project->assignments->where('status', 'accepted')->count() > 0)
                                    <span class="flex items-center text-green-600">
                                        <i class="fas fa-check mr-2"></i>
                                        {{ $project->assignments->where('status', 'accepted')->count() }} accepted
                                    </span>
                                @endif
                                @if($project->assignments->where('status', 'declined')->count() > 0)
                                    <span class="flex items-center text-red-600">
                                        <i class="fas fa-times mr-2"></i>
                                        {{ $project->assignments->where('status', 'declined')->count() }} declined
                                    </span>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <a href="{{ route('projects.show', $project) }}"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium transition duration-200">
                                    View Details
                                </a>

                                @if($project->status === 'pending_approval')
                                    <a href="{{ route('projects.edit', $project) }}"
                                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm font-medium transition duration-200">
                                        Edit
                                    </a>
                                @endif

                                @if(in_array($project->status, ['active', 'overdue']))
                                    <button onclick="showClosureRequestModal({{ $project->id }}, {{ json_encode($project->title) }})"
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-medium transition duration-200">
                                        <i class="fas fa-times mr-1"></i>Request Closure
                                    </button>
                                @endif

                                @if($project->status === 'closure_requested')
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                        <div class="flex items-center">
                                            <i class="fas fa-clock text-yellow-600 mr-2"></i>
                                            <span class="text-yellow-800 text-sm font-medium">
                                                Closure request pending admin approval
                                            </span>
                                        </div>
                                        <p class="text-yellow-700 text-xs mt-1">
                                            A talent admin will review your closure request shortly.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Events Preview -->
                    @if($project->timelineEvents->count() > 0)
                        <div class="border-t border-gray-200 bg-gray-50 px-6 py-3">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-history mr-2"></i>
                                <span>Latest: {{ $project->timelineEvents->first()->description }}</span>
                                <span class="ml-auto">{{ $project->timelineEvents->first()->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $projects->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="max-w-md mx-auto">
                <div class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                    <i class="fas fa-project-diagram text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">No Projects Yet</h3>
                <p class="text-gray-600 mb-6">
                    Start your talent recruitment journey by creating your first project.
                    Once approved, you can assign multiple talents and manage the complete project lifecycle.
                </p>
                <a href="{{ route('projects.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                    <i class="fas fa-plus mr-2"></i>Create Your First Project
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

<!-- Closure Request Modal -->
<div id="closureRequestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-2xl rounded-2xl bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-times text-red-600 mr-3"></i>
                    Request Project Closure
                </h3>
                <button onclick="hideClosureRequestModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <form id="closureRequestForm" method="POST">
                @csrf
                <div class="space-y-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-yellow-800 mb-2">Important Notice</h4>
                                <p class="text-yellow-700 text-sm">
                                    Requesting project closure will notify the talent admin for review.
                                    If the project has not reached its deadline, admin approval is required to force-close the project.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="project_title_display" class="block text-sm font-medium text-gray-700 mb-2">
                            Project
                        </label>
                        <input type="text" id="project_title_display" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
                    </div>

                    <div>
                        <label for="closure_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Closure <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="closure_reason"
                            name="closure_reason"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            placeholder="Please provide a detailed reason for requesting project closure..."
                            required
                        ></textarea>
                        <p class="text-xs text-gray-500 mt-1">Minimum 10 characters required</p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" onclick="hideClosureRequestModal()"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium transition duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-dismiss notifications after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const successNotification = document.getElementById('success-notification');
        const errorNotification = document.getElementById('error-notification');

        if (successNotification) {
            setTimeout(function() {
                successNotification.style.transition = 'opacity 0.5s ease-out';
                successNotification.style.opacity = '0';
                setTimeout(function() {
                    successNotification.remove();
                }, 500);
            }, 5000);
        }

        if (errorNotification) {
            setTimeout(function() {
                errorNotification.style.transition = 'opacity 0.5s ease-out';
                errorNotification.style.opacity = '0';
                setTimeout(function() {
                    errorNotification.remove();
                }, 500);
            }, 7000); // Keep error messages visible longer
        }
    });

    // Auto-refresh status badges every 30 seconds for active projects
    setInterval(function() {
        // Only refresh if there are active projects
        const activeProjects = document.querySelectorAll('[data-status="active"]');
        if (activeProjects.length > 0) {
            // Could implement AJAX refresh here if needed
        }
    }, 30000);

    // Closure Request Modal Functions
    function showClosureRequestModal(projectId, projectTitle) {
        const modal = document.getElementById('closureRequestModal');
        const form = document.getElementById('closureRequestForm');
        const titleInput = document.getElementById('project_title_display');
        const reasonTextarea = document.getElementById('closure_reason');

        if (modal && form && titleInput) {
            // Set the form action
            form.action = `/projects/${projectId}/request-closure`;

            // Set the project title
            titleInput.value = projectTitle;

            // Clear previous reason
            reasonTextarea.value = '';

            // Show modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Focus on reason textarea
            setTimeout(() => reasonTextarea.focus(), 100);
        }
    }

    function hideClosureRequestModal() {
        const modal = document.getElementById('closureRequestModal');
        const form = document.getElementById('closureRequestForm');

        if (modal && form) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';

            // Reset form
            form.reset();
        }
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('closureRequestModal');
        if (modal && event.target === modal) {
            hideClosureRequestModal();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('closureRequestModal');
            if (modal && !modal.classList.contains('hidden')) {
                hideClosureRequestModal();
            }
        }
    });
</script>
@endpush
