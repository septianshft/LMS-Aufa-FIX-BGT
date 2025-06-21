@extends('layout.template.mainTemplate')

@section('title', 'Project Details')
@section('container')
<div class="container mx-auto px-4 py-8">
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

    <!-- Project Header -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden mb-8">
        <div class="p-6">
            <!-- Header Navigation -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <a href="{{ route('projects.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $project->title }}</h1>
                        <p class="text-gray-600 mt-1">
                            Created
                            @if($project->created_at instanceof \Carbon\Carbon)
                                {{ $project->created_at->diffForHumans() }}
                            @elseif($project->created_at)
                                {{ \Carbon\Carbon::parse($project->created_at)->diffForHumans() }}
                            @else
                                recently
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Status Badge -->
                @php
                    $statusColors = [
                        'draft' => 'bg-gray-100 text-gray-800',
                        'pending_admin' => 'bg-yellow-100 text-yellow-800',
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
                <span class="px-4 py-2 rounded-full text-sm font-medium {{ $statusClass }}">
                    {{ ucwords(str_replace('_', ' ', $project->status)) }}
                </span>
            </div>

            <!-- Project Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Industry</h3>
                        <p class="text-lg text-gray-900">{{ $project->industry ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Duration</h3>
                        <p class="text-lg text-gray-900">{{ $project->estimated_duration_days }} days</p>
                    </div>
                </div>
                  <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Start Date</h3>
                        <p class="text-lg text-gray-900">
                            {{ $project->expected_start_date ? $project->expected_start_date->format('M d, Y') : 'Not set' }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">End Date</h3>
                        <p class="text-lg text-gray-900">
                            {{ $project->expected_end_date ? $project->expected_end_date->format('M d, Y') : 'Not set' }}
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    @if($project->overall_budget_min || $project->overall_budget_max)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Budget Range</h3>
                        <p class="text-lg text-gray-900">
                            @if($project->overall_budget_min && $project->overall_budget_max)
                                Rp {{ number_format($project->overall_budget_min, 0, ',', '.') }} - Rp {{ number_format($project->overall_budget_max, 0, ',', '.') }}
                            @elseif($project->overall_budget_min)
                                From Rp {{ number_format($project->overall_budget_min, 0, ',', '.') }}
                            @else
                                Up to Rp {{ number_format($project->overall_budget_max, 0, ',', '.') }}
                            @endif
                        </p>
                    </div>
                    @endif

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Talent Interactions</h3>
                        <p class="text-lg text-gray-900">{{ $project->assignments->count() + $project->talentRequests->count() }} total interactions</p>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Project Description</h3>
                <p class="text-gray-700 leading-relaxed">{{ $project->description }}</p>
            </div>

            <!-- General Requirements -->
            @if($project->general_requirements)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">General Requirements</h3>
                <p class="text-gray-700 leading-relaxed">{{ $project->general_requirements }}</p>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3">
                @php
                    $isRecruiter = auth()->user() && auth()->user()->hasRole('recruiter');
                    $isProjectOwner = auth()->user() && auth()->user()->recruiter && auth()->user()->recruiter->id === $project->recruiter_id;
                    $hasPendingExtensions = $project->extensions()->where('status', 'pending')->exists();
                @endphp



                @if($isRecruiter && $isProjectOwner && $project->status === 'pending_admin')
                    <a href="{{ route('projects.edit', $project) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                        <i class="fas fa-edit mr-2"></i>Edit Project
                    </a>
                @endif

                @if($isRecruiter && $isProjectOwner && $project->status === 'approved')
                    <button onclick="showProjectTalentModal()"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                        <i class="fas fa-user-plus mr-2"></i>Request Talent
                    </button>
                @endif

                @if($isRecruiter && $isProjectOwner && in_array($project->status, ['active', 'overdue']) && !$hasPendingExtensions)
                    <button onclick="showExtensionModal()"
                            class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                        <i class="fas fa-clock mr-2"></i>Request Extension
                    </button>
                @endif

                @if($isRecruiter && $isProjectOwner && in_array($project->status, ['active', 'overdue']) && $project->status !== 'closure_requested')
                    <button onclick="showClosureRequestModal()"
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                        <i class="fas fa-times mr-2"></i>Request Closure
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="mb-8">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button onclick="showTab('assignments')"
                    class="tab-button active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600"
                    id="assignments-tab">
                Talent Management ({{ $project->assignments->count() + $project->talentRequests->count() }})
            </button>
            <button onclick="showTab('timeline')"
                    class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                    id="timeline-tab">
                Timeline Events
            </button>
            @if($project->extensions->count() > 0)
            <button onclick="showTab('extensions')"
                    class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                    id="extensions-tab">
                Extensions ({{ $project->extensions->count() }})
            </button>
            @endif
        </nav>
    </div>

    <!-- Tab Content -->

    <!-- Assignments Tab -->
    <div id="assignments-content" class="tab-content">
        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Talent Management</h2>
                    @if($isRecruiter && $isProjectOwner && $project->status === 'approved')
                        <div class="flex space-x-2">
                            @if($availableTalents->count() > 0)
                                <button onclick="showProjectTalentModal()"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                                    <i class="fas fa-plus mr-2"></i>Add Assignment
                                </button>
                            @endif
                        </div>
                    @endif
                </div>                @php
                    // Combine assignments and talent requests for display
                    $allTalentInteractions = collect();

                    // Add existing assignments
                    foreach($project->assignments as $assignment) {
                        $allTalentInteractions->push((object)[
                            'type' => 'assignment',
                            'id' => $assignment->id,
                            'talent_name' => $assignment->talent->user->name ?? $assignment->talentUser->name ?? 'Unknown Talent',
                            'role' => $assignment->specific_role,
                            'status' => $assignment->status,
                            'start_date' => $assignment->talent_start_date,
                            'end_date' => $assignment->talent_end_date,
                            'budget' => $assignment->individual_budget,
                            'priority' => $assignment->priority_level,
                            'requirements' => $assignment->specific_requirements,
                            'created_at' => $assignment->created_at,
                            'data' => $assignment
                        ]);
                    }

                    // Add talent requests
                    foreach($project->talentRequests as $request) {
                        // Get talent name with proper fallback chain
                        $talentName = 'Unknown Talent';

                        // Use the helper method first
                        $talentUser = $request->getTalentUser();
                        if ($talentUser && $talentUser->name) {
                            $talentName = $talentUser->name;
                        }
                        // Fallback to direct relationship checks
                        elseif ($request->talentUser && $request->talentUser->name) {
                            $talentName = $request->talentUser->name;
                        }
                        elseif ($request->talent && $request->talent->user && $request->talent->user->name) {
                            $talentName = $request->talent->user->name;
                        }

                        // Determine role description for talent request
                        $roleDescription = 'Project Contributor';
                        if (!empty($request->requirements)) {
                            // Extract role from requirements if it looks like a role description
                            $requirements = strtolower($request->requirements);
                            if (strpos($requirements, 'developer') !== false) {
                                $roleDescription = 'Developer';
                            } elseif (strpos($requirements, 'designer') !== false) {
                                $roleDescription = 'Designer';
                            } elseif (strpos($requirements, 'manager') !== false) {
                                $roleDescription = 'Project Manager';
                            } elseif (strpos($requirements, 'analyst') !== false) {
                                $roleDescription = 'Analyst';
                            } elseif (strpos($requirements, 'engineer') !== false) {
                                $roleDescription = 'Engineer';
                            } else {
                                // Use first few words of requirements as role if it's short
                                $firstWords = implode(' ', array_slice(explode(' ', $request->requirements), 0, 3));
                                if (strlen($firstWords) <= 30) {
                                    $roleDescription = $firstWords;
                                }
                            }
                        }

                        $allTalentInteractions->push((object)[
                            'type' => 'talent_request',
                            'id' => $request->id,
                            'talent_name' => $talentName,
                            'role' => $roleDescription,
                            'status' => $request->status,
                            'start_date' => $request->project_start_date,
                            'end_date' => $request->project_end_date,
                            'budget' => $request->budget_range,
                            'priority' => 'medium', // TalentRequest doesn't have priority field
                            'requirements' => $request->requirements,
                            'created_at' => $request->created_at,
                            'data' => $request
                        ]);
                    }

                    // Sort by creation date (newest first)
                    $allTalentInteractions = $allTalentInteractions->sortByDesc('created_at');
                @endphp

                @if($allTalentInteractions->count() > 0)
                    <div class="space-y-4">
                        @foreach($allTalentInteractions as $interaction)
                            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900">
                                                    @php
                                                        $talentId = null;
                                                        $isUserId = false;
                                                        $hasValidTalentLink = false;

                                                        if($interaction->type === 'assignment') {
                                                            $talentId = $interaction->data->talent_id;
                                                            $hasValidTalentLink = !empty($talentId);
                                                        } else {
                                                            // For talent requests, prefer talent_id if available, otherwise use talent_user_id
                                                            if($interaction->data->talent_id) {
                                                                $talentId = $interaction->data->talent_id;
                                                                $hasValidTalentLink = true;
                                                            } elseif($interaction->data->talent_user_id) {
                                                                $talentId = $interaction->data->talent_user_id;
                                                                $isUserId = true;
                                                                $hasValidTalentLink = true;
                                                            }
                                                        }
                                                    @endphp
                                                    @if($hasValidTalentLink)
                                                        <button onclick='viewTalentDetails({{ json_encode($interaction->data) }})'
                                                                class="text-blue-600 hover:text-blue-800 transition-colors cursor-pointer font-semibold hover:underline">
                                                            {{ $interaction->talent_name }}
                                                        </button>
                                                    @else
                                                        <span class="text-gray-900">{{ $interaction->talent_name }}</span>
                                                        <span class="text-xs text-gray-500 ml-2">(No profile link)</span>
                                                    @endif
                                                </h3>
                                                <p class="text-gray-600">{{ $interaction->role }}</p>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                        @if($interaction->type === 'assignment') bg-green-100 text-green-800
                                                        @else bg-blue-100 text-blue-800 @endif">
                                                        {{ $interaction->type === 'assignment' ? 'Assignment' : 'Talent Request' }}
                                                    </span>
                                                    @if($interaction->created_at)
                                                        <span class="text-xs text-gray-500">
                                                            • Created {{ $interaction->created_at->diffForHumans() }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                            <div>
                                                <span class="text-sm font-medium text-gray-500">Timeline:</span>
                                                <p class="text-gray-900">
                                                    @if($interaction->start_date && $interaction->end_date)
                                                        {{ $interaction->start_date->format('M d') }} - {{ $interaction->end_date->format('M d, Y') }}
                                                    @else
                                                        Not set
                                                    @endif
                                                </p>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-500">Budget:</span>
                                                <p class="text-gray-900">
                                                    @if($interaction->type === 'assignment')
                                                        @if($interaction->budget)
                                                            Rp {{ number_format($interaction->budget, 0, ',', '.') }}
                                                        @else
                                                            Not specified
                                                        @endif
                                                    @else
                                                        {{ $interaction->budget ?? 'Not specified' }}
                                                    @endif
                                                </p>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-500">Priority:</span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($interaction->priority === 'high') bg-red-100 text-red-800
                                                    @elseif($interaction->priority === 'medium') bg-yellow-100 text-yellow-800
                                                    @else bg-green-100 text-green-800 @endif">
                                                    {{ ucfirst($interaction->priority) }}
                                                </span>
                                            </div>
                                        </div>

                                        @if($interaction->requirements)
                                            <div class="mb-4">
                                                <span class="text-sm font-medium text-gray-500">Requirements:</span>
                                                <p class="text-gray-700 mt-1">{{ $interaction->requirements }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="ml-6 flex flex-col items-end space-y-2">
                                        <!-- Status Badge -->
                                        @php
                                            $statusColors = [
                                                // Assignment statuses
                                                'assigned' => 'bg-blue-100 text-blue-800',
                                                'admin_pending' => 'bg-yellow-100 text-yellow-800',
                                                'talent_pending' => 'bg-orange-100 text-orange-800',
                                                'accepted' => 'bg-green-100 text-green-800',
                                                'active' => 'bg-green-100 text-green-800',
                                                'completed' => 'bg-gray-100 text-gray-800',
                                                // Talent request statuses
                                                'pending_admin' => 'bg-yellow-100 text-yellow-800',
                                                'pending_talent' => 'bg-orange-100 text-orange-800',
                                                'approved' => 'bg-green-100 text-green-800',
                                                'declined' => 'bg-red-100 text-red-800',
                                                'cancelled' => 'bg-gray-100 text-gray-800'
                                            ];
                                            $statusClass = $statusColors[$interaction->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ ucwords(str_replace('_', ' ', $interaction->status)) }}
                                        </span>

                                        <!-- Actions -->
                                        <div class="flex flex-col space-y-2">
                                            <!-- View Details Button -->
                                            @php
                                                $talentId = null;
                                                $isUserId = false;
                                                $hasValidTalentLink = false;

                                                if($interaction->type === 'assignment') {
                                                    $talentId = $interaction->data->talent_id;
                                                    $hasValidTalentLink = !empty($talentId);
                                                } else {
                                                    // For talent requests, prefer talent_id if available, otherwise use talent_user_id
                                                    if($interaction->data->talent_id) {
                                                        $talentId = $interaction->data->talent_id;
                                                        $hasValidTalentLink = true;
                                                    } elseif($interaction->data->talent_user_id) {
                                                        $talentId = $interaction->data->talent_user_id;
                                                        $isUserId = true;
                                                        $hasValidTalentLink = true;
                                                    }
                                                }
                                            @endphp
                                            @if($hasValidTalentLink)
                                                <button onclick='viewTalentDetails({{ json_encode($interaction->data) }})'
                                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center transition-colors">
                                                    <i class="fas fa-eye mr-1"></i>View Details
                                                </button>
                                            @else
                                                <span class="text-gray-400 text-sm flex items-center">
                                                    <i class="fas fa-eye-slash mr-1"></i>Details unavailable
                                                </span>
                                            @endif

                                            @if($isRecruiter && $isProjectOwner && $interaction->type === 'assignment' && in_array($interaction->status, ['assigned', 'accepted']))
                                                <div class="flex space-x-2">
                                                    <button onclick="editAssignment({{ $interaction->id }})"
                                                            class="text-blue-600 hover:text-blue-800 text-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button onclick="removeAssignment({{ $interaction->id }})"
                                                            class="text-red-600 hover:text-red-800 text-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Talent Interactions</h3>
                        <p class="text-gray-600 mb-6">Start building your team by requesting talents for this project.</p>
                        @if($project->status === 'approved')
                            @if($availableTalents->count() > 0)
                                <button onclick="showProjectTalentModal()"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                                    <i class="fas fa-plus mr-2"></i>Request First Talent
                                </button>
                            @endif
                        @elseif($project->status === 'pending_admin')
                            <p class="text-sm text-gray-500">Project needs admin approval before you can request talents.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Timeline Tab -->
    <div id="timeline-content" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Project Timeline</h2>

                @if($project->timelineEvents->count() > 0)
                    <div class="space-y-4">
                        @foreach($project->timelineEvents as $event)                            <div class="flex items-start space-x-4 pb-4 border-b border-gray-100 last:border-b-0">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-circle text-blue-600 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-900">{{ $event->event_description }}</p>                                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                        <span>
                                            @if($event->created_at instanceof \Carbon\Carbon)
                                                {{ $event->created_at->format('M d, Y H:i') }}
                                            @elseif($event->created_at)
                                                {{ \Carbon\Carbon::parse($event->created_at)->format('M d, Y H:i') }}
                                            @else
                                                Unknown date
                                            @endif
                                        </span>
                                        @if($event->triggeredBy)
                                            <span>by {{ $event->triggeredBy->name }}</span>
                                        @endif
                                        <span class="px-2 py-1 bg-gray-100 rounded text-xs">{{ $event->event_type }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-history text-gray-400 text-3xl mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Timeline Events</h3>
                        <p class="text-gray-600">Timeline events will appear here as the project progresses.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Extensions Tab -->
    @if($project->extensions->count() > 0)
    <div id="extensions-content" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Extension Requests</h2>

                <div class="space-y-4">
                    @foreach($project->extensions as $extension)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-gray-900">Extension Request</h3>
                                @php
                                    $extensionStatusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800'
                                    ];
                                    $extensionStatusClass = $extensionStatusColors[$extension->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $extensionStatusClass }}">
                                    {{ ucfirst($extension->status) }}
                                </span>
                            </div>
                              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Original End Date:</span>
                                    <p class="text-gray-900">{{ $extension->old_end_date ? $extension->old_end_date->format('M d, Y') : 'Not set' }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Requested End Date:</span>
                                    <p class="text-gray-900">{{ $extension->new_end_date ? $extension->new_end_date->format('M d, Y') : 'Not set' }}</p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <span class="text-sm font-medium text-gray-500">Justification:</span>
                                <p class="text-gray-700 mt-1">{{ $extension->justification }}</p>
                            </div>

                            @if($extension->review_notes)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <span class="text-sm font-medium text-gray-500">Review Notes:</span>
                                    <p class="text-gray-700 mt-1">{{ $extension->review_notes }}</p>
                                </div>
                            @endif
                              <div class="text-sm text-gray-500 mt-3">                                Requested {{ $extension->created_at ? $extension->created_at->diffForHumans() : 'Unknown time' }}
                                @if($extension->reviewed_at)
                                    • Reviewed {{ $extension->reviewed_at ? $extension->reviewed_at->diffForHumans() : 'Unknown time' }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Unified Talent Request Modal (Canonical Implementation from Dashboard) -->
@if($project->status === 'approved')
<div class="modal fade" id="talentRequestModal" tabindex="-1" role="dialog" aria-labelledby="talentRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content rounded-2xl border-0 shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-green-600 to-emerald-700 text-white rounded-t-2xl border-0 p-6">
                <h5 class="modal-title text-xl font-bold flex items-center" id="talentRequestModalLabel">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-handshake text-white"></i>
                    </div>
                    Request Talent for Project: {{ $project->title }}
                </h5>
                <button type="button" class="text-white hover:text-gray-200 transition-colors duration-200" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="talentRequestForm">
                @csrf
                <div class="modal-body p-8">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 mt-0.5">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div>
                                <h6 class="font-semibold text-blue-800 mb-1">Project Assignment Request</h6>
                                <p class="text-blue-700 text-sm">Your request will be reviewed by the Talent Admin who will coordinate the talent assignment to this project. This uses the same system as regular talent requests but will be linked to your approved project.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields for project context -->
                    <input type="hidden" id="requestTalentId" name="talent_id">
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="is_project_assignment" value="1">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <!-- Talent Selection (Project-specific) -->
                            <div>
                                <label for="talent_select" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Select Talent <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                        id="talent_select" name="talent_id" required onchange="updateTalentIdField(this.value)">
                                    <option value="">Choose a talent...</option>
                                    @if($availableTalents->count() > 0)
                                        @foreach($availableTalents as $talent)
                                            <option value="{{ $talent->id }}" data-name="{{ $talent->user->name }}">
                                                {{ $talent->user->name }} - {{ $talent->user->pekerjaan ?? 'Developer' }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No available talents</option>
                                    @endif
                                </select>
                                @if($availableTalents->count() === 0)
                                    <p class="text-xs text-red-500 mt-1">No talents are currently available for assignment.</p>
                                @endif
                            </div>

                            <!-- Project Title (Auto-filled from project) -->
                            <div>
                                <label for="projectTitle" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Project Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                       id="projectTitle" name="project_title" required readonly
                                       value="{{ $project->title }}">
                                <p class="text-xs text-gray-500 mt-1">Auto-filled from current project</p>
                            </div>

                            <!-- Budget Range (Role-specific) -->
                            <div>
                                <label for="budgetRange" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Individual Budget Range
                                    <span class="text-xs text-gray-500 block font-normal">Budget for this specific talent's role</span>
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                        id="budgetRange" name="budget_range">
                                    <option value="">Select individual budget range</option>
                                    <option value="Under Rp 5.000.000">Under Rp 5.000.000</option>
                                    <option value="Rp 5.000.000 - Rp 15.000.000">Rp 5.000.000 - Rp 15.000.000</option>
                                    <option value="Rp 15.000.000 - Rp 30.000.000">Rp 15.000.000 - Rp 30.000.000</option>
                                    <option value="Rp 30.000.000 - Rp 50.000.000">Rp 30.000.000 - Rp 50.000.000</option>
                                    <option value="Rp 50.000.000+">Rp 50.000.000+</option>
                                    <option value="Negotiable">Negotiable</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">💡 This is for this talent's portion of the project</p>
                            </div>

                            <!-- Project Duration (Auto-calculated from project timeline) -->
                            <div>
                                <label for="projectDuration" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Project Duration <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                        id="projectDuration" name="project_duration" required>
                                    @php
                                        $projectDurationText = $project->estimated_duration_days ? "{$project->estimated_duration_days} days" : '3-6 months';
                                        if ($project->expected_start_date && $project->expected_end_date) {
                                            $projectDurationText = $project->expected_start_date->diffInDays($project->expected_end_date) . ' days (' .
                                                                 $project->expected_start_date->format('M d') . ' - ' .
                                                                 $project->expected_end_date->format('M d, Y') . ')';
                                        }
                                    @endphp
                                    <option value="">Select duration</option>
                                    <option value="1-2 weeks">1-2 weeks</option>
                                    <option value="1 month">1 month</option>
                                    <option value="2-3 months">2-3 months</option>
                                    <option value="3-6 months" {{ str_contains($projectDurationText, 'month') ? 'selected' : '' }}>3-6 months</option>
                                    <option value="6+ months">6+ months</option>
                                    <option value="Ongoing">Ongoing</option>
                                    @if($project->expected_start_date && $project->expected_end_date)
                                        <option value="{{ $projectDurationText }}" selected>{{ $projectDurationText }}</option>
                                    @endif
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Required for time-blocking to prevent overlapping projects</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <!-- Project Description (Auto-filled from project) -->
                            <div>
                                <label for="projectDescription" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Project Description <span class="text-red-500">*</span>
                                </label>
                                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                                          id="projectDescription" name="project_description" rows="5" required>{{ $project->description }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Pre-filled from project description (editable)</p>
                            </div>

                            <!-- Role-Specific Requirements -->
                            <div>
                                <label for="requirements" class="block text-sm font-semibold text-gray-700 mb-2">Specific Requirements</label>
                                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                                          id="requirements" name="requirements" rows="3"
                                          placeholder="List any specific skills, technologies, or qualifications needed for this role...">{{ $project->general_requirements }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 rounded-b-2xl border-0 p-6">
                    <button type="button" class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-all duration-200 font-medium mr-3"
                            data-dismiss="modal" onclick="$('#talentRequestModal').modal('hide')">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all duration-200 font-medium">
                        <i class="fas fa-paper-plane mr-2"></i>Request Talent Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Extension Request Modal -->
@if(in_array($project->status, ['active', 'overdue']) && !$project->extensions()->pending()->exists())
<div id="extensionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Request Project Extension</h3>
                    <button onclick="hideExtensionModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('projects.request-extension', $project) }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="new_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            New End Date <span class="text-red-500">*</span>
                        </label>                        <input type="date" id="new_end_date" name="new_end_date" required
                               min="{{ $project->expected_end_date ? $project->expected_end_date->addDay()->format('Y-m-d') : '' }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Current end date: {{ $project->expected_end_date ? $project->expected_end_date->format('M d, Y') : 'Not set' }}</p>
                    </div>

                    <div>
                        <label for="justification" class="block text-sm font-medium text-gray-700 mb-2">
                            Justification <span class="text-red-500">*</span>
                        </label>
                        <textarea id="justification" name="justification" rows="4" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Explain why you need to extend the project timeline..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="hideExtensionModal()"
                                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition duration-200">
                            <i class="fas fa-clock mr-2"></i>Request Extension
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Talent Details Modal -->
<div id="talent-details-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-2xl font-bold text-gray-900">Talent Details</h3>
            <div class="modal-close cursor-pointer z-50" onclick="closeModal('talent-details-modal')">
                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                    <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <div id="modal-talent-content"></div>
            <div id="modal-loading-state" class="text-center p-8">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-500"></i>
                <p class="mt-2 text-gray-600">Loading details...</p>
            </div>
            <div id="modal-error-state" class="text-center p-8 hidden">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500"></i>
                <p class="mt-2 text-gray-600">Could not load talent details.</p>
            </div>
        </div>
    </div>
</div>

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
            <form action="{{ route('projects.request-closure', $project) }}" method="POST">
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

@endsection

@push('scripts')
<script>
// Tab functionality
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');

    // Add active class to selected tab button
    const activeButton = document.getElementById(tabName + '-tab');
    activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500');
}

// Global variables for error handlers
let currentRequestTalentId = null;
let currentRequestTalentName = null;

// Modal functions
function showProjectTalentModal() {
    // Reset form and show the bootstrap modal
    document.getElementById('talentRequestForm').reset();

    // Set project context fields
    document.querySelector('input[name="project_id"]').value = '{{ $project->id }}';
    document.querySelector('input[name="is_project_assignment"]').value = '1';
    document.getElementById('projectTitle').value = '{{ $project->title }}';
    document.getElementById('projectDescription').value = '{{ addslashes($project->description) }}';

    // Pre-fill requirements if available
    const requirementsField = document.getElementById('requirements');
    if (requirementsField && '{{ $project->general_requirements }}') {
        requirementsField.value = '{{ addslashes($project->general_requirements) }}';
    }

    // Reset talent selection
    const talentSelect = document.getElementById('talent_select');
    const talentIdField = document.getElementById('requestTalentId');
    if (talentSelect) {
        talentSelect.value = '';
        if (talentIdField) {
            talentIdField.value = '';
        }
    }

    $('#talentRequestModal').modal('show');
}

function hideTalentRequestModal() {
    $('#talentRequestModal').modal('hide');
}

function showAssignTalentModal() {
    // Backward compatibility - redirect to new function
    showProjectTalentModal();
}

function hideAssignTalentModal() {
    // Backward compatibility - redirect to new function
    hideTalentRequestModal();
}

function showExtensionModal() {
    const modal = document.getElementById('extensionModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function hideExtensionModal() {
    const modal = document.getElementById('extensionModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function showClosureRequestModal() {
    const modal = document.getElementById('closureRequestModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function hideClosureRequestModal() {
    const modal = document.getElementById('closureRequestModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        // Reset form
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }
}

// Assignment actions
function editAssignment(assignmentId) {
    alert('Edit assignment functionality to be implemented');
}

function removeAssignment(assignmentId) {
    if (confirm('Are you sure you want to remove this talent assignment?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/assignments/${assignmentId}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const assignModal = document.getElementById('assignTalentModal');
    const extensionModal = document.getElementById('extensionModal');
    const talentDetailsModal = document.getElementById('talentDetailsModal');
    const talentDetailsModalNew = document.getElementById('talent-details-modal');

    if (assignModal && event.target === assignModal) {
        hideAssignTalentModal();
    }

    if (extensionModal && event.target === extensionModal) {
        hideExtensionModal();
    }

    if (talentDetailsModal && event.target === talentDetailsModal) {
        closeTalentDetailsModal();
    }

    if (talentDetailsModalNew && event.target === talentDetailsModalNew) {
        closeModal('talent-details-modal');
    }
});

// Date validation and form handling
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('talent_start_date');
    const endDateInput = document.getElementById('talent_end_date');

    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
        });
    }

    // Handle talent request form submission (integrated with existing system)
    const talentRequestForm = document.getElementById('talentRequestForm');
    if (talentRequestForm) {
        talentRequestForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;

            // Store current talent info for error handlers
            const talentSelect = document.getElementById('talent_select');
            const selectedOption = talentSelect.options[talentSelect.selectedIndex];
            currentRequestTalentId = talentSelect.value;
            currentRequestTalentName = selectedOption.getAttribute('data-name') || selectedOption.text;

            // Validate talent selection
            if (!currentRequestTalentId) {
                alert('Please select a talent before submitting the request.');
                return;
            }

            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting Request...';

            // Use the existing talent request endpoint
            fetch('{{ route("recruiter.submit_talent_request") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw { status: response.status, data: data };
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Success - talent request submitted
                    $('#talentRequestModal').modal('hide');

                    // Show success message
                    const successHtml = `
                        <div class="fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5);">
                            <div class="flex items-center justify-center min-h-screen px-4">
                                <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl">
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-check text-green-600 text-2xl"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">Request Submitted Successfully!</h3>
                                        <p class="text-gray-600 mb-4">${data.message}</p>
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                            <div class="text-sm">
                                                <p><strong>Project:</strong> {{ $project->title }}</p>
                                                <p><strong>Request ID:</strong> ${data.request_id || 'Generated'}</p>
                                                <p><strong>Timeline:</strong> ${data.project_timeline?.start_date || 'TBD'} - ${data.project_timeline?.end_date || 'TBD'}</p>
                                            </div>
                                        </div>
                                        <button onclick="location.reload()"
                                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                            <i class="fas fa-refresh mr-2"></i>Refresh Page
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.insertAdjacentHTML('beforeend', successHtml);
                } else {
                    throw { status: 400, data: data };
                }
            })
            .catch(error => {
                console.error('Error:', error);

                if (error.status === 409 && error.data) {
                    // Time-blocking conflict - show detailed availability info
                    showTimeBlockingConflict(error.data);
                } else if (error.status === 400 && error.data?.error === 'talent_already_onboarded') {
                    // Talent already onboarded with this recruiter - show specific modal
                    showTalentAlreadyOnboardedModal(error.data);
                } else if (error.status === 400 && error.data?.error === 'active_request_exists') {
                    // Active request exists - show detailed info
                    showActiveRequestExistsModal(error.data);
                } else {
                    // Regular error message
                    const errorAlert = document.createElement('div');
                    errorAlert.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl shadow-lg z-50 max-w-md';
                    errorAlert.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>Error: ' +
                        (error.data?.message || error.data?.error || 'Failed to submit request. Please try again.');
                    document.body.appendChild(errorAlert);

                    setTimeout(() => {
                        errorAlert.remove();
                    }, 5000);
                }
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            });
        });
    }
});

// Add the error handling functions from the dashboard
function showTimeBlockingConflict(errorData) {
    $('#talentRequestModal').modal('hide');

    let blockingProjectsHtml = '';
    if (errorData.blocking_projects && errorData.blocking_projects.length > 0) {
        blockingProjectsHtml = '<div class="mt-4"><h6 class="font-semibold text-gray-900 mb-3">Conflicting Projects:</h6><div class="space-y-2">';
        errorData.blocking_projects.forEach(project => {
            blockingProjectsHtml += `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-medium text-yellow-900">${project.title}</span>
                        <span class="text-yellow-700">${project.end_date}</span>
                    </div>
                </div>
            `;
        });
        blockingProjectsHtml += '</div></div>';
    }

    const nextAvailableDate = errorData.next_available_date ?
        new Date(errorData.next_available_date).toLocaleDateString('en-US', {
            year: 'numeric', month: 'long', day: 'numeric'
        }) : 'Unknown';

    const modalHtml = `
        <div class="fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5);">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar-times text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Talent Not Available</h3>
                        <p class="text-gray-600 mt-2">${errorData.message || 'This talent is currently committed to other projects.'}</p>
                    </div>

                    ${errorData.next_available_date ? `
                        <div class="text-center mb-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="text-sm font-medium text-blue-800">Next Available:</div>
                                <div class="text-lg font-bold text-blue-900">${nextAvailableDate}</div>
                            </div>
                        </div>
                    ` : ''}

                    ${blockingProjectsHtml}

                    <div class="mt-6 space-y-3">
                        <button onclick="showProjectTalentModal(); this.closest('.fixed').remove();"
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-calendar-plus mr-2"></i>Try Different Talent
                        </button>
                        <button onclick="this.closest('.fixed').remove()"
                                class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function showTalentAlreadyOnboardedModal(errorData) {
    // Close the request modal first
    $('#talentRequestModal').modal('hide');

    const onboardedDate = errorData.existing_project?.onboarded_date || 'Unknown';
    const projectTitle = errorData.existing_project?.title || 'Current Project';

    const modalHtml = `
        <div class="fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5);">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-check text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Talent Already Onboarded</h3>
                        <p class="text-gray-600 mt-2">${errorData.message}</p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <h6 class="font-semibold text-blue-900 mb-2">Current Project Details:</h6>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-blue-700">Project:</span>
                                <span class="font-medium text-blue-900">${projectTitle}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">Onboarded:</span>
                                <span class="font-medium text-blue-900">${onboardedDate}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">Status:</span>
                                <span class="font-medium text-blue-900">${errorData.existing_project?.status || 'Onboarded'}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-lightbulb text-yellow-600 mr-2 mt-0.5"></i>
                            <div class="text-sm text-yellow-800">
                                <p class="font-medium mb-1">Suggestion:</p>
                                <p>Since this talent is already part of your team, consider reaching out directly or using your internal project management tools for new assignments.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button onclick="this.closest('.fixed').remove()"
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-check mr-2"></i>Got It
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function showActiveRequestExistsModal(errorData) {
    // Close the request modal first
    $('#talentRequestModal').modal('hide');

    const submittedDate = errorData.existing_request?.submitted_date || 'Unknown';
    const projectTitle = errorData.existing_request?.project_title || 'Previous Request';
    const requestStatus = errorData.existing_request?.status || 'In Progress';

    const modalHtml = `
        <div class="fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5);">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-hourglass-half text-orange-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Active Request Pending</h3>
                        <p class="text-gray-600 mt-2">${errorData.message}</p>
                    </div>

                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                        <h6 class="font-semibold text-orange-900 mb-2">Your Current Request:</h6>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-orange-700">Project:</span>
                                <span class="font-medium text-orange-900">${projectTitle}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-orange-700">Submitted:</span>
                                <span class="font-medium text-orange-900">${submittedDate}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-orange-700">Status:</span>
                                <span class="font-medium text-orange-900">${requestStatus}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mr-2 mt-0.5"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">What's Next:</p>
                                <p>Your request is being processed. You can track its progress in the "My Requests" section of your dashboard.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button onclick="window.location.href='{{ route('recruiter.my_requests') }}'"
                                class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                            <i class="fas fa-list mr-2"></i>View My Requests
                        </button>
                        <button onclick="this.closest('.fixed').remove()"
                                class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

// Helper function to update the hidden talent ID field when selection changes
function updateTalentIdField(talentId) {
    document.getElementById('requestTalentId').value = talentId;
}

// Function to view talent details from talent interaction data
function viewTalentDetails(talentData) {
    console.log("Viewing details for:", talentData);

    const modal = document.getElementById('talent-details-modal');
    const content = document.getElementById('modal-talent-content');
    const loading = document.getElementById('modal-loading-state');
    const error = document.getElementById('modal-error-state');

    if (!modal) {
        console.error('Talent details modal element not found!');
        alert('Modal element not found. Please refresh the page.');
        return;
    }

    // Show the modal and loading state
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    content.innerHTML = '';
    loading.classList.remove('hidden');
    error.classList.add('hidden');

    // Display the talent details directly from the data
    displayTalentDetails(talentData);
}

function displayTalentDetails(data) {
    console.log("Displaying data:", data);
    const content = document.getElementById('modal-talent-content');
    const loading = document.getElementById('modal-loading-state');

    if (!content) return;

    // Extract talent information from different possible data structures
    let talentName = 'Unknown Talent';
    let talentEmail = 'Not provided';
    let talentRole = 'Not specified';
    let talentBudget = 'Not specified';

    if (data.talent && data.talent.user && data.talent.user.name) {
        talentName = data.talent.user.name;
        talentEmail = data.talent.user.email || 'Not provided';
    } else if (data.talentUser && data.talentUser.name) {
        talentName = data.talentUser.name;
        talentEmail = data.talentUser.email || 'Not provided';
    }

    if (data.specific_role) {
        talentRole = data.specific_role;
    } else if (data.project_title) {
        talentRole = data.project_title;
    }

    if (data.individual_budget) {
        talentBudget = `Rp ${Number(data.individual_budget).toLocaleString()}`;
    } else if (data.budget_range) {
        talentBudget = data.budget_range;
    }

    const content_html = `
        <div class="space-y-6">
            <!-- Profile Header -->
            <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white text-2xl"></i>
                </div>
                <div>
                    <h4 class="text-xl font-bold text-gray-900">${talentName}</h4>
                    <p class="text-gray-600">${talentRole}</p>
                    <p class="text-sm text-gray-500">${talentEmail}</p>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <h5 class="font-semibold text-gray-700">Assignment Type</h5>
                        <p class="text-gray-900">${data.type === 'assignment' ? 'Direct Assignment' : 'Talent Request'}</p>
                    </div>
                    <div>
                        <h5 class="font-semibold text-gray-700">Status</h5>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            ${data.status ? data.status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Unknown'}
                        </span>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <h5 class="font-semibold text-gray-700">Budget</h5>
                        <p class="text-gray-900">${talentBudget}</p>
                    </div>
                    <div>
                        <h5 class="font-semibold text-gray-700">Timeline</h5>
                        <p class="text-gray-900">
                            ${data.talent_start_date && data.talent_end_date ?

                                `${new Date(data.talent_start_date).toLocaleDateString()} - ${new Date(data.talent_end_date).toLocaleDateString()}` :
                                data.project_start_date && data.project_end_date ?
                                `${new Date(data.project_start_date).toLocaleDateString()} - ${new Date(data.project_end_date).toLocaleDateString()}` :
                                'Not set'
                            }
                        </p>
                    </div>
                </div>
            </div>

            <!-- Requirements -->
            ${data.specific_requirements || data.requirements ? `
                <div>
                    <h5 class="font-semibold text-gray-700 mb-2">Requirements</h5>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700">${data.specific_requirements || data.requirements}</p>
                    </div>
                </div>
            ` : ''}

            <!-- Additional Info -->
            <div class="bg-blue-50 rounded-lg p-4">
                <h5 class="font-semibold text-blue-800 mb-2">Additional Information</h5>
                <div class="text-sm text-blue-700 space-y-1">
                    <p><strong>Created:</strong> ${data.created_at ? new Date(data.created_at).toLocaleDateString() : 'Unknown'}</p>
                    <p><strong>Priority:</strong> ${data.priority_level || 'Medium'}</p>
                    ${data.talent_id ? `<p><strong>Talent ID:</strong> ${data.talent_id}</p>` : ''}
                </div>
            </div>

            <!-- Close Button -->
            <div class="flex justify-end">
                <button onclick="closeModal('talent-details-modal')"
                        class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Close
                </button>
            </div>
        </div>
    `;

    content.innerHTML = content_html;
    loading.classList.add('hidden');
}

// Close modal function
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Fallback function to show basic talent info when API is unavailable
function showBasicTalentInfo(talentId, isUserId = false) {
    const contentElement = document.getElementById('talentDetailsContent');
    if (!contentElement) return;

    // Create a basic talent info display
    const content = `
        <div class="text-center py-8">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-user text-white text-3xl"></i>
            </div>

            <h4 class="text-xl font-bold text-gray-900 mb-2">Talent Information</h4>
            <p class="text-gray-600 mb-6">Limited information available</p>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 text-left max-w-md mx-auto">
                <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-id-card text-blue-600 mr-2"></i>
                    Basic Information
                </h5>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-500">ID:</span>
                        <span class="text-gray-900">${talentId}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-500">Type:</span>
                        <span class="text-gray-900">${isUserId ? 'User ID' : 'Talent ID'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-500">Status:</span>
                        <span class="text-blue-900">Available for viewing</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                    <div class="text-left">
                        <h6 class="font-semibold text-blue-900 mb-1">Note</h6>
                        <p class="text-blue-800 text-sm">
                            Full talent details are currently unavailable. This may be due to system maintenance
                            or network connectivity issues. Please try again later or contact your administrator.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-center space-x-4 mt-8">
                <button onclick="closeModal('talent-details-modal')"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Close
                </button>
            </div>
        </div>
    `;

    const modalContent = document.getElementById('modal-talent-content');
    if (modalContent) {
        modalContent.innerHTML = content;
    }
}

// Auto-dismiss notifications and close modals on Escape key
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

// Auto-dismiss notifications and close modals on Escape key
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

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const talentDetailsModal = document.getElementById('talent-details-modal');
            if (talentDetailsModal && !talentDetailsModal.classList.contains('hidden')) {
                closeModal('talent-details-modal');
            }
        }
    });
});
</script>
@endpush
