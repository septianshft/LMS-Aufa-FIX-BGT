@extends('layout.template.mainTemplate')

@section('title', 'Recruiter Dashboard')
@section('container')


<!-- Premium Statistics Dashboard -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16 mt-8 relative z-20 max-w-7xl mx-auto">
    <!-- Available Talents Card - Enhanced -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-users text-xl text-white"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-blue-600 uppercase">Available Talents</p>
                <p class="text-gray-500 text-sm">Ready for collaboration</p>
            </div>
        </div>

        <div class="mb-4">
            <div class="text-4xl font-bold text-gray-900 mb-1">
                {{ $talents->total() }}
            </div>
            <p class="text-gray-600">Active professionals</p>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="flex items-center text-sm text-green-600">
                <i class="fas fa-check-circle mr-2"></i>
                <span class="font-medium">Ready to hire</span>
            </div>
            <div class="inline-flex items-center px-3 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-full">
                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                Live
            </div>
        </div>
    </div>

    <!-- Account Status Card - Enhanced -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-{{ $user->recruiter && $user->recruiter->is_active ? 'emerald' : 'red' }}-500 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-{{ $user->recruiter && $user->recruiter->is_active ? 'shield-check' : 'shield-exclamation' }} text-xl text-white"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-{{ $user->recruiter && $user->recruiter->is_active ? 'emerald' : 'red' }}-600 uppercase">Account Status</p>
                <p class="text-gray-500 text-sm">Your recruitment access</p>
            </div>
        </div>

        <div class="mb-4">
            <div class="text-4xl font-bold text-gray-900 mb-1">
                {{ $user->recruiter && $user->recruiter->is_active ? 'Active' : 'Inactive' }}
            </div>
            <p class="text-gray-600">{{ $user->recruiter && $user->recruiter->is_active ? 'All systems operational' : 'Limited access mode' }}</p>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="flex items-center text-sm text-{{ $user->recruiter && $user->recruiter->is_active ? 'emerald' : 'red' }}-600">
                <i class="fas fa-{{ $user->recruiter && $user->recruiter->is_active ? 'check-circle' : 'exclamation-triangle' }} mr-2"></i>
                <span class="font-medium">{{ $user->recruiter && $user->recruiter->is_active ? 'Ready to use' : 'Contact admin' }}</span>
            </div>
            <div class="inline-flex items-center px-3 py-1 bg-{{ $user->recruiter && $user->recruiter->is_active ? 'emerald' : 'red' }}-50 text-{{ $user->recruiter && $user->recruiter->is_active ? 'emerald' : 'red' }}-700 text-xs font-medium rounded-full">
                <div class="w-2 h-2 bg-{{ $user->recruiter && $user->recruiter->is_active ? 'emerald' : 'red' }}-500 rounded-full mr-2"></div>
                {{ $user->recruiter && $user->recruiter->is_active ? 'Live' : 'Offline' }}
            </div>
        </div>
    </div>

    <!-- My Requests Card - Enhanced -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-paper-plane text-xl text-white"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-purple-600 uppercase">My Requests</p>
                <p class="text-gray-500 text-sm">Collaboration requests</p>
            </div>
        </div>

        <div class="mb-4">
            <div class="text-4xl font-bold text-gray-900 mb-1">
                {{ isset($myRequests) && (method_exists($myRequests, 'count') ? $myRequests->count() : (is_countable($myRequests) ? count($myRequests) : 0)) }}
            </div>
            <p class="text-gray-600">Active submissions</p>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="flex items-center text-sm text-purple-600">
                <i class="fas fa-chart-line mr-2"></i>
                <span class="font-medium">Track progress</span>
            </div>
            <div class="inline-flex items-center px-3 py-1 bg-purple-50 text-purple-700 text-xs font-medium rounded-full">
                <div class="w-2 h-2 bg-purple-500 rounded-full mr-2"></div>
                Active
            </div>
        </div>
    </div>
</div>
</div>

    <!-- Recent Requests Section -->
    @if(isset($myRequests) && (method_exists($myRequests, 'count') ? $myRequests->count() > 0 : (is_countable($myRequests) ? count($myRequests) > 0 : false)))
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden max-w-7xl mx-auto mb-12">
        <!-- Header with gradient background -->
        <div class="bg-blue-600 text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Recent Requests</h2>
                    <p class="text-blue-100">Your collaboration pipeline</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('recruiter.my_requests') }}"
                       class="px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        <i class="fas fa-eye mr-2"></i>View All
                    </a>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            @if(isset($myRequests) && is_iterable($myRequests) && count($myRequests) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($myRequests as $request)
                        <div class="bg-white border rounded-xl p-6 hover:shadow-lg transition-shadow">
                            <!-- Profile -->
                            <div class="text-center mb-4">
                                @if($request->talent->user->avatar)
                                    <img class="w-16 h-16 rounded-full mx-auto mb-3 object-cover"
                                         src="{{ asset('storage/' . $request->talent->user->avatar) }}"
                                         alt="{{ $request->talent->user->name }}">
                                @else
                                    <div class="w-16 h-16 bg-blue-500 rounded-full mx-auto mb-3 flex items-center justify-center">
                                        <i class="fas fa-user text-white text-xl"></i>
                                    </div>
                                @endif

                                <h3 class="font-bold text-lg text-gray-900">{{ $request->talent->user->name }}</h3>
                                @if($request->talent->user->pekerjaan)
                                    <p class="text-gray-600 text-sm">{{ $request->talent->user->pekerjaan }}</p>
                                @endif
                            </div>

                            <!-- Project Title -->
                            <div class="text-center mb-4">
                                <div class="bg-gray-50 py-2 px-3 rounded-lg">
                                    <div class="text-xs text-gray-600">Project</div>
                                    <div class="font-medium text-sm">{{ $request->project_title }}</div>
                                </div>
                            </div>

                            <!-- Status and Date -->
                            <div class="grid grid-cols-1 gap-3 mb-4 text-center">
                                <div class="bg-gray-50 py-2 rounded">
                                    <div class="text-xs text-gray-600">Status</div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        @if($request->getStatusBadgeColor() == 'success') bg-green-100 text-green-800
                                        @elseif($request->getStatusBadgeColor() == 'warning') bg-yellow-100 text-yellow-800
                                        @elseif($request->getStatusBadgeColor() == 'info') bg-blue-100 text-blue-800
                                        @elseif($request->getStatusBadgeColor() == 'danger') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $request->getFormattedStatus() }}
                                    </span>
                                </div>
                                <div class="bg-gray-50 py-2 rounded">
                                    <div class="text-xs text-gray-600">Requested</div>
                                    <div class="font-bold text-sm">{{ $request->created_at->diffForHumans() }}</div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="space-y-2">
                                <div class="grid grid-cols-2 gap-2">
                                    <button onclick="viewRequestDetails('{{ $request->id }}')"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                        <i class="fas fa-eye mr-1"></i>Details
                                    </button>
                                    <a href="mailto:{{ $request->talent->user->email }}"
                                       class="px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-center text-sm">
                                        <i class="fas fa-envelope mr-1"></i>Email
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-inbox text-2xl text-gray-400"></i>
                    </div>
                    <h5 class="text-lg font-medium text-gray-700 mb-2">No requests yet</h5>
                    <p class="text-gray-500">Start discovering talents to see your requests here.</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Premium Talent Discovery Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden max-w-7xl mx-auto">
        <!-- Simple Header -->
        <div class="bg-emerald-600 text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Talent Scouting</h2>
                    <p class="text-emerald-100">Discover and connect with talented professionals</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="toggleScoutingFilters()"
                            class="px-4 py-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filters
                    </button>
                    <button onclick="toggleCompareMode()"
                            class="px-4 py-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors" id="compareModeBtn">
                        <i class="fas fa-balance-scale mr-2"></i>Compare
                    </button>
                    <button onclick="refreshTalents()"
                            class="px-4 py-2 bg-white text-emerald-600 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Simple Filters Panel -->
        <div id="scoutingFilters" class="hidden bg-gray-50 border-b p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <select class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Performance Levels</option>
                    <option value="5">⭐⭐⭐⭐⭐ Elite (5★)</option>
                    <option value="4">⭐⭐⭐⭐ High (4★)</option>
                    <option value="3">⭐⭐⭐ Good (3★)</option>
                </select>
                <select class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Skill Levels</option>
                    <option value="expert">Expert Level</option>
                    <option value="advanced">Advanced</option>
                    <option value="intermediate">Intermediate</option>
                </select>
                <div class="flex gap-2">
                    <button onclick="resetFilters()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Reset</button>
                    <button onclick="applyFilters()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                        Apply
                    </button>
                </div>
            </div>
        </div>

        <!-- Talent Cards -->
        <div class="p-6">
            @if(isset($talents) && is_iterable($talents) && (method_exists($talents, 'count') ? $talents->count() > 0 : count($talents) > 0))
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($talents as $talent)
                        @php
                            $metrics = $talent->scouting_metrics ?? [];
                            $overallScore = 0;
                            $scoreCount = 0;
                            foreach(['learning_velocity', 'consistency', 'certifications', 'market_demand'] as $key) {
                                if(isset($metrics[$key]['score'])) {
                                    $overallScore += $metrics[$key]['score'];
                                    $scoreCount++;
                                }
                            }
                            $overallScore = $scoreCount > 0 ? round($overallScore / $scoreCount) : 0;
                        @endphp

                        <div class="bg-white border rounded-xl p-6 hover:shadow-lg transition-shadow talent-card" data-talent-id="{{ $talent->id }}">
                            <!-- Compare Checkbox (Hidden by default) -->
                            <div class="compare-checkbox hidden absolute top-4 right-4 z-10">
                                <input type="checkbox"
                                       class="talent-compare-check w-5 h-5 text-emerald-600 rounded focus:ring-emerald-500"
                                       data-talent-id="{{ $talent->id }}"
                                       data-talent-name="{{ $talent->user->name }}"
                                       data-talent-email="{{ $talent->user->email }}"
                                       data-talent-position="{{ $talent->user->pekerjaan ?? 'Not specified' }}"
                                       data-talent-score="{{ $overallScore }}"
                                       data-talent-courses="{{ $metrics['progress_tracking']['completed_courses'] ?? 0 }}"
                                       data-talent-certificates="{{ $metrics['certifications']['total_certificates'] ?? 0 }}"
                                       data-talent-quiz-avg="{{ $metrics['quiz_performance']['average_score'] ?? 0 }}"
                                       onchange="updateCompareSelection()">
                            </div>

                            <!-- Profile -->
                            <div class="text-center mb-4">
                                @if($talent->user->avatar)
                                    <img class="w-16 h-16 rounded-full mx-auto mb-3 object-cover"
                                         src="{{ asset('storage/' . $talent->user->avatar) }}"
                                         alt="{{ $talent->user->name }}">
                                @else
                                    <div class="w-16 h-16 bg-emerald-500 rounded-full mx-auto mb-3 flex items-center justify-center">
                                        <i class="fas fa-user text-white text-xl"></i>
                                    </div>
                                @endif

                                <h3 class="font-bold text-lg text-gray-900">{{ $talent->user->name }}</h3>
                                @if($talent->user->pekerjaan)
                                    <p class="text-gray-600 text-sm">{{ $talent->user->pekerjaan }}</p>
                                @endif
                            </div>

                            <!-- Score -->
                            <div class="text-center mb-4">
                                <div class="inline-flex items-center px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">
                                    <span class="font-bold">Score: {{ $overallScore }}/100</span>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                                <div class="bg-gray-50 py-2 rounded">
                                    <div class="text-xs text-gray-600">Courses</div>
                                    <div class="font-bold">{{ $metrics['progress_tracking']['completed_courses'] ?? 0 }}</div>
                                </div>
                                <div class="bg-gray-50 py-2 rounded">
                                    <div class="text-xs text-gray-600">Certificates</div>
                                    <div class="font-bold">{{ $metrics['certifications']['total_certificates'] ?? 0 }}</div>
                                </div>
                                <div class="bg-gray-50 py-2 rounded">
                                    <div class="text-xs text-gray-600">Quiz Avg</div>
                                    <div class="font-bold">{{ $metrics['quiz_performance']['average_score'] ?? 0 }}%</div>
                                </div>
                            </div>

                            <!-- Status -->
                            @php $existingRequest = $talent->talentRequests->first(); @endphp
                            @if($existingRequest)
                                <div class="mb-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($existingRequest->getStatusBadgeColor() == 'success') bg-green-100 text-green-800
                                        @elseif($existingRequest->getStatusBadgeColor() == 'warning') bg-yellow-100 text-yellow-800
                                        @elseif($existingRequest->getStatusBadgeColor() == 'info') bg-blue-100 text-blue-800
                                        @elseif($existingRequest->getStatusBadgeColor() == 'danger') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $existingRequest->getFormattedStatus() }}
                                    </span>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="space-y-2">
                                @if(!$existingRequest || $existingRequest->status == 'rejected')
                                    <button onclick="openRequestModal('{{ $talent->id }}', '{{ $talent->user->name }}')"
                                            class="w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                                        <i class="fas fa-handshake mr-2"></i>Request Talent
                                    </button>
                                @endif
                                <div class="grid grid-cols-2 gap-2">
                                    <button onclick="viewScoutingReport('{{ $talent->id }}', '{{ $talent->user->name }}')"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                        <i class="fas fa-chart-line mr-1"></i>Report
                                    </button>
                                    <a href="mailto:{{ $talent->user->email }}"
                                       class="px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-center text-sm">
                                        <i class="fas fa-envelope mr-1"></i>Email
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if(isset($talents) && method_exists($talents, 'links'))
                    <div class="mt-8 flex justify-center">
                        {{ $talents->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-user-tie text-2xl text-gray-400"></i>
                    </div>
                    <h5 class="text-lg font-medium text-gray-700 mb-2">No Talents Available</h5>
                    <p class="text-gray-500">Check back later or contact your administrator.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Talent Details Modal -->
<div class="modal fade" id="talentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="talentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded-2xl border-0 shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-t-2xl border-0 p-6">
                <h5 class="modal-title text-xl font-bold flex items-center" id="talentDetailsModalLabel">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user-tie text-white"></i>
                    </div>
                    Talent Profile Details
                </h5>
                <button type="button" class="text-white hover:text-gray-200 transition-colors duration-200" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="modal-body p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                            <p id="modalTalentName" class="text-gray-900 font-medium"></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <p id="modalTalentEmail" class="text-gray-900 font-medium"></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                            <p id="modalTalentPhone" class="text-gray-900 font-medium"></p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Profession</label>
                            <p id="modalTalentProfession" class="text-gray-900 font-medium"></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                            <p id="modalTalentAddress" class="text-gray-900 font-medium"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-gray-50 rounded-b-2xl border-0 p-6">
                <button type="button" class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-all duration-200 font-medium mr-3" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Close
                </button>
                <button type="button" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium" onclick="contactTalent()">
                    <i class="fas fa-envelope mr-2"></i>Send Email
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Talent Request Modal -->
<div class="modal fade" id="talentRequestModal" tabindex="-1" role="dialog" aria-labelledby="talentRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content rounded-2xl border-0 shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-green-600 to-emerald-700 text-white rounded-t-2xl border-0 p-6">
                <h5 class="modal-title text-xl font-bold flex items-center" id="talentRequestModalLabel">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-handshake text-white"></i>
                    </div>
                    Request Talent Collaboration
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
                                <h6 class="font-semibold text-blue-800 mb-1">Important Information</h6>
                                <p class="text-blue-700 text-sm">Your request will be reviewed by the Talent Admin who will coordinate a meeting between you and the talent. Please provide detailed project information to expedite the process.</p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="requestTalentId" name="talent_id">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <label for="projectTitle" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Project Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                       id="projectTitle" name="project_title" required
                                       placeholder="e.g., Mobile App Development">
                            </div>

                            <div>
                                <label for="budgetRange" class="block text-sm font-semibold text-gray-700 mb-2">Budget Range</label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                        id="budgetRange" name="budget_range">
                                    <option value="">Select budget range</option>
                                    <option value="Under $1,000">Under $1,000</option>
                                    <option value="$1,000 - $5,000">$1,000 - $5,000</option>
                                    <option value="$5,000 - $10,000">$5,000 - $10,000</option>
                                    <option value="$10,000 - $25,000">$10,000 - $25,000</option>
                                    <option value="$25,000+">$25,000+</option>
                                    <option value="Negotiable">Negotiable</option>
                                </select>
                            </div>

                            <div>
                                <label for="projectDuration" class="block text-sm font-semibold text-gray-700 mb-2">Project Duration</label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                        id="projectDuration" name="project_duration">
                                    <option value="">Select duration</option>
                                    <option value="1-2 weeks">1-2 weeks</option>
                                    <option value="1 month">1 month</option>
                                    <option value="2-3 months">2-3 months</option>
                                    <option value="3-6 months">3-6 months</option>
                                    <option value="6+ months">6+ months</option>
                                    <option value="Ongoing">Ongoing</option>
                                </select>
                            </div>

                            <div>
                                <label for="urgencyLevel" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Urgency Level <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                        id="urgencyLevel" name="urgency_level" required>
                                    <option value="low">Low - Flexible timeline</option>
                                    <option value="medium" selected>Medium - Standard timeline</option>
                                    <option value="high">High - Urgent requirement</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label for="projectDescription" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Project Description <span class="text-red-500">*</span>
                                </label>
                                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                                          id="projectDescription" name="project_description" rows="5" required
                                          placeholder="Describe your project, goals, and what you're looking for..."></textarea>
                            </div>

                            <div>
                                <label for="requirements" class="block text-sm font-semibold text-gray-700 mb-2">Specific Requirements</label>
                                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                                          id="requirements" name="requirements" rows="3"
                                          placeholder="List any specific skills, technologies, or qualifications needed..."></textarea>
                            </div>

                            <div>
                                <label for="recruiterMessage" class="block text-sm font-semibold text-gray-700 mb-2">Personal Message</label>
                                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                                          id="recruiterMessage" name="recruiter_message" rows="3"
                                          placeholder="Add a personal message to the talent (optional)..."></textarea>
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
                        <i class="fas fa-paper-plane mr-2"></i>Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentTalentEmail = '';
let currentRequestTalentId = '';
let currentRequestTalentName = '';

function viewTalentDetails(name, email, profession, address, phone) {
    document.getElementById('modalTalentName').textContent = name;
    document.getElementById('modalTalentEmail').textContent = email;
    document.getElementById('modalTalentProfession').textContent = profession;
    document.getElementById('modalTalentAddress').textContent = address;
    document.getElementById('modalTalentPhone').textContent = phone;
    currentTalentEmail = email;

    $('#talentDetailsModal').modal('show');
}

function openRequestModal(talentId, talentName) {
    currentRequestTalentId = talentId;
    currentRequestTalentName = talentName;

    // Reset form
    document.getElementById('talentRequestForm').reset();
    document.getElementById('requestTalentId').value = talentId;

    // Update modal title
    document.getElementById('talentRequestModalLabel').innerHTML =
        '<div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3"><i class="fas fa-handshake text-white"></i></div>Request Talent: ' + talentName;

    $('#talentRequestModal').modal('show');
}

function contactTalent() {
    if (currentTalentEmail) {
        window.location.href = 'mailto:' + currentTalentEmail;
    }
}

// New Scouting Functions
function toggleScoutingFilters() {
    const filtersPanel = document.getElementById('scoutingFilters');
    const isHidden = filtersPanel.classList.contains('hidden');

    if (isHidden) {
        filtersPanel.classList.remove('hidden');
        filtersPanel.classList.add('animate-slideDown');
    } else {
        filtersPanel.classList.add('hidden');
        filtersPanel.classList.remove('animate-slideDown');
    }
}

function toggleViewMode() {
    const talentCards = document.querySelectorAll('.talent-card');
    const grid = talentCards[0]?.parentElement;

    if (grid.classList.contains('grid-cols-3')) {
        // Switch to list view
        grid.classList.remove('grid-cols-1', 'md:grid-cols-2', 'xl:grid-cols-3');
        grid.classList.add('grid-cols-1');

        talentCards.forEach(card => {
            card.classList.add('flex', 'flex-row');
            card.classList.remove('flex-col');
        });
    } else {
        // Switch back to grid view
        grid.classList.remove('grid-cols-1');
        grid.classList.add('grid-cols-1', 'md:grid-cols-2', 'xl:grid-cols-3');

        talentCards.forEach(card => {
            card.classList.remove('flex', 'flex-row');
            card.classList.add('flex-col');
        });
    }
}

function viewScoutingReport(talentId, talentName) {
    // Create a detailed scouting report modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-chart-line mr-3 text-blue-600"></i>
                        Scouting Report: ${talentName}
                    </h2>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                    <p class="text-gray-600">Loading detailed scouting report...</p>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    // Here you would make an AJAX call to get detailed metrics
    // For now, we'll show a placeholder
    setTimeout(() => {
        const content = modal.querySelector('.p-6:last-child');
        content.innerHTML = `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-blue-50 p-4 rounded-xl">
                        <h3 class="font-semibold text-blue-900 mb-3">Learning Performance</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between"><span>Velocity:</span><span class="font-semibold">⭐⭐⭐⭐</span></div>
                            <div class="flex justify-between"><span>Consistency:</span><span class="font-semibold">⭐⭐⭐⭐⭐</span></div>
                            <div class="flex justify-between"><span>Adaptability:</span><span class="font-semibold">⭐⭐⭐</span></div>
                        </div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-xl">
                        <h3 class="font-semibold text-green-900 mb-3">Achievement Metrics</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between"><span>Certifications:</span><span class="font-semibold">5 earned</span></div>
                            <div class="flex justify-between"><span>Quiz Performance:</span><span class="font-semibold">87% avg</span></div>
                            <div class="flex justify-between"><span>Completion Rate:</span><span class="font-semibold">92%</span></div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-xl">
                    <h3 class="font-semibold text-gray-900 mb-3">Recommendation</h3>
                    <p class="text-gray-700">This talent shows excellent learning consistency and strong performance across multiple skill areas. Recommended for projects requiring adaptable and dedicated team members.</p>
                </div>

                <div class="flex gap-4 pt-4">
                    <button onclick="openRequestModal('${talentId}', '${talentName}')" class="flex-1 px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors font-semibold">
                        <i class="fas fa-handshake mr-2"></i>Request This Talent
                    </button>
                    <button onclick="this.closest('.fixed').remove()" class="px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors font-semibold">
                        Close Report
                    </button>
                </div>
            </div>
        `;
    }, 1000);
}

function viewRequestDetails(requestId) {
    // Create a modal to show request details
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-clipboard-list mr-3 text-blue-600"></i>
                        Request Details
                    </h2>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                    <p class="text-gray-600">Loading request details...</p>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    // Fetch request details from server
    fetch(`/recruiter/request-details/${requestId}`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const request = data.request;
            const content = modal.querySelector('.p-6:last-child');
            content.innerHTML = `
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 p-4 rounded-xl">
                            <h3 class="font-semibold text-blue-900 mb-3">Talent Information</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between"><span>Name:</span><span class="font-semibold">${request.talent_name}</span></div>
                                <div class="flex justify-between"><span>Email:</span><span class="font-semibold">${request.talent_email}</span></div>
                                <div class="flex justify-between"><span>Position:</span><span class="font-semibold">${request.talent_position || 'Not specified'}</span></div>
                            </div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-xl">
                            <h3 class="font-semibold text-green-900 mb-3">Request Status</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between"><span>Status:</span><span class="font-semibold">${request.status}</span></div>
                                <div class="flex justify-between"><span>Requested:</span><span class="font-semibold">${request.created_at}</span></div>
                                <div class="flex justify-between"><span>Updated:</span><span class="font-semibold">${request.updated_at}</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl">
                        <h3 class="font-semibold text-gray-900 mb-3">Project Details</h3>
                        <div class="space-y-2">
                            <div><span class="font-medium">Title:</span> ${request.project_title}</div>
                            <div><span class="font-medium">Description:</span> ${request.project_description || 'No description provided'}</div>
                            <div><span class="font-medium">Budget:</span> ${request.budget || 'Not specified'}</div>
                            <div><span class="font-medium">Duration:</span> ${request.project_duration || 'Not specified'}</div>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <a href="mailto:${request.talent_email}" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-semibold text-center">
                            <i class="fas fa-envelope mr-2"></i>Contact Talent
                        </a>
                        <button onclick="this.closest('.fixed').remove()" class="px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors font-semibold">
                            Close
                        </button>
                    </div>
                </div>
            `;
        } else {
            const content = modal.querySelector('.p-6:last-child');
            content.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                    <p class="text-gray-600">Error loading request details: ${data.message || 'Unknown error'}</p>
                    <button onclick="this.closest('.fixed').remove()" class="mt-4 px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Close
                    </button>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const content = modal.querySelector('.p-6:last-child');
        content.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <p class="text-gray-600">Failed to load request details. Please try again.</p>
                <button onclick="this.closest('.fixed').remove()" class="mt-4 px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Close
                </button>
            </div>
        `;
    });
}

function resetFilters() {
    const selects = document.querySelectorAll('#scoutingFilters select');
    selects.forEach(select => select.value = '');
}

function applyFilters() {
    // Here you would implement the filtering logic
    // For now, we'll show a loading message
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Applying...';
    button.disabled = true;

    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        // In a real implementation, you would reload the page with filter parameters
        // window.location.href = window.location.pathname + '?' + new URLSearchParams(filterData).toString();
    }, 1000);
}

function refreshTalents() {
    // Add smooth loading animation
    const refreshButton = document.querySelector('[onclick="refreshTalents()"]');
    const originalHTML = refreshButton.innerHTML;
    refreshButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Refreshing...';
    refreshButton.disabled = true;

    setTimeout(() => {
        location.reload();
    }, 500);
}

// Ensure modal close functionality works
$(document).ready(function() {
    // Handle modal close buttons
    $('.modal .close, .modal [data-dismiss="modal"]').on('click', function() {
        $(this).closest('.modal').modal('hide');
    });

    // Add smooth scroll behavior
    $('html').css('scroll-behavior', 'smooth');

    // Add hover effects for talent cards
    $('.talent-card').hover(
        function() {
            $(this).addClass('transform scale-105');
        },
        function() {
            $(this).removeClass('transform scale-105');
        }
    );
});

// ===== TALENT COMPARISON FUNCTIONALITY =====
let isCompareMode = false;
let selectedTalents = [];

function toggleCompareMode() {
    isCompareMode = !isCompareMode;
    const checkboxes = document.querySelectorAll('.compare-checkbox');
    const compareBtn = document.getElementById('compareModeBtn');
    const comparisonPanel = document.getElementById('comparisonPanel');

    if (isCompareMode) {
        // Enable compare mode
        checkboxes.forEach(cb => cb.classList.remove('hidden'));
        compareBtn.innerHTML = '<i class="fas fa-times mr-2"></i>Exit Compare';
        compareBtn.classList.add('bg-red-600', 'hover:bg-red-700');
        compareBtn.classList.remove('bg-white/20', 'hover:bg-white/30');

        // Show comparison panel
        comparisonPanel.style.display = 'block';
        setTimeout(() => {
            comparisonPanel.classList.remove('translate-y-full');
        }, 10);

        // Add margin to body to account for panel
        document.body.style.marginBottom = '120px';
    } else {
        // Disable compare mode
        checkboxes.forEach(cb => {
            cb.classList.add('hidden');
            cb.querySelector('input').checked = false;
        });
        compareBtn.innerHTML = '<i class="fas fa-balance-scale mr-2"></i>Compare';
        compareBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
        compareBtn.classList.add('bg-white/20', 'hover:bg-white/30');

        // Hide comparison panel
        comparisonPanel.classList.add('translate-y-full');
        setTimeout(() => {
            comparisonPanel.style.display = 'none';
        }, 300);

        // Reset margin
        document.body.style.marginBottom = '0';

        // Clear selection
        selectedTalents = [];
        updateCompareSelection();
    }
}

function updateCompareSelection() {
    const checkedBoxes = document.querySelectorAll('.talent-compare-check:checked');
    selectedTalents = Array.from(checkedBoxes).map(cb => ({
        id: cb.dataset.talentId,
        name: cb.dataset.talentName,
        email: cb.dataset.talentEmail,
        position: cb.dataset.talentPosition,
        score: cb.dataset.talentScore,
        courses: cb.dataset.talentCourses,
        certificates: cb.dataset.talentCertificates,
        quizAvg: cb.dataset.talentQuizAvg
    }));

    // Update counter
    const selectedCount = document.getElementById('selectedCount');
    selectedCount.textContent = `${selectedTalents.length} selected`;

    // Update compare button state
    const compareBtn = document.getElementById('compareBtn');
    compareBtn.disabled = selectedTalents.length < 2;

    // Update preview
    updateSelectedTalentsPreview();
}

function updateSelectedTalentsPreview() {
    const preview = document.getElementById('selectedTalentsPreview');
    preview.innerHTML = '';

    selectedTalents.forEach(talent => {
        const talentCard = document.createElement('div');
        talentCard.className = 'flex-shrink-0 bg-gray-50 rounded-lg p-3 min-w-48';
        talentCard.innerHTML = `
            <div class="flex items-center">
                <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div>
                    <div class="font-medium text-sm">${talent.name}</div>
                    <div class="text-xs text-gray-600">${talent.position}</div>
                </div>
            </div>
        `;
        preview.appendChild(talentCard);
    });
}

function clearComparison() {
    // Uncheck all checkboxes
    document.querySelectorAll('.talent-compare-check').forEach(cb => {
        cb.checked = false;
    });

    // Clear array and update UI
    selectedTalents = [];
    updateCompareSelection();
}

function viewComparison() {
    if (selectedTalents.length < 2) {
        alert('Please select at least 2 talents to compare.');
        return;
    }

    const modal = document.getElementById('talentComparisonModal');
    const content = document.getElementById('comparisonContent');

    // Generate comparison table
    content.innerHTML = generateComparisonTable();

    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeComparisonModal() {
    const modal = document.getElementById('talentComparisonModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function generateComparisonTable() {
    if (selectedTalents.length === 0) return '<p>No talents selected for comparison.</p>';

    return `
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-200 p-4 text-left font-semibold">Criteria</th>
                        ${selectedTalents.map(talent => `
                            <th class="border border-gray-200 p-4 text-center font-semibold min-w-48">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center mb-2">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <div class="font-bold">${talent.name}</div>
                                    <div class="text-sm text-gray-600">${talent.position}</div>
                                </div>
                            </th>
                        `).join('')}
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-200 p-4 font-medium bg-gray-50">Overall Score</td>
                        ${selectedTalents.map(talent => `
                            <td class="border border-gray-200 p-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold">
                                    ${talent.score}/100
                                </span>
                            </td>
                        `).join('')}
                    </tr>
                    <tr>
                        <td class="border border-gray-200 p-4 font-medium bg-gray-50">Completed Courses</td>
                        ${selectedTalents.map(talent => `
                            <td class="border border-gray-200 p-4 text-center font-semibold">${talent.courses}</td>
                        `).join('')}
                    </tr>
                    <tr>
                        <td class="border border-gray-200 p-4 font-medium bg-gray-50">Certificates Earned</td>
                        ${selectedTalents.map(talent => `
                            <td class="border border-gray-200 p-4 text-center font-semibold">${talent.certificates}</td>
                        `).join('')}
                    </tr>
                    <tr>
                        <td class="border border-gray-200 p-4 font-medium bg-gray-50">Quiz Average</td>
                        ${selectedTalents.map(talent => `
                            <td class="border border-gray-200 p-4 text-center font-semibold">${talent.quizAvg}%</td>
                        `).join('')}
                    </tr>
                    <tr>
                        <td class="border border-gray-200 p-4 font-medium bg-gray-50">Contact</td>
                        ${selectedTalents.map(talent => `
                            <td class="border border-gray-200 p-4 text-center">
                                <a href="mailto:${talent.email}" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    <i class="fas fa-envelope mr-1"></i>Email
                                </a>
                            </td>
                        `).join('')}
                    </tr>
                    <tr>
                        <td class="border border-gray-200 p-4 font-medium bg-gray-50">Actions</td>
                        ${selectedTalents.map(talent => `
                            <td class="border border-gray-200 p-4 text-center">
                                <div class="flex flex-col gap-2">
                                    <button onclick="openRequestModal('${talent.id}', '${talent.name}')"
                                            class="px-3 py-1 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm">
                                        <i class="fas fa-handshake mr-1"></i>Request
                                    </button>
                                    <button onclick="viewScoutingReport('${talent.id}', '${talent.name}')"
                                            class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                        <i class="fas fa-chart-line mr-1"></i>Report
                                    </button>
                                </div>
                            </td>
                        `).join('')}
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-center">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-4xl mx-auto">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-blue-900 mb-2">Best Overall Score</h4>
                    <p class="text-blue-700">${getBestTalent('score')}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-green-900 mb-2">Most Experienced</h4>
                    <p class="text-green-700">${getBestTalent('courses')}</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-purple-900 mb-2">Best Quiz Performance</h4>
                    <p class="text-purple-700">${getBestTalent('quizAvg')}</p>
                </div>
            </div>
        </div>
    `;
}

function getBestTalent(criteria) {
    if (selectedTalents.length === 0) return 'No data';

    let best = selectedTalents[0];
    let value = parseFloat(best[criteria]);

    selectedTalents.forEach(talent => {
        const talentValue = parseFloat(talent[criteria]);
        if (talentValue > value) {
            best = talent;
            value = talentValue;
        }
    });

    return best.name;
}

// Handle talent request form submission
document.getElementById('talentRequestForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    // Show loading state
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting Request...';

    fetch('{{ route("recruiter.submit_talent_request") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message with better styling
            const successAlert = document.createElement('div');
            successAlert.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl shadow-lg z-50';
            successAlert.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Success! Your talent request has been submitted.';
            document.body.appendChild(successAlert);

            // Close modal and refresh page
            $('#talentRequestModal').modal('hide');
            setTimeout(() => {
                successAlert.remove();
                location.reload();
            }, 2000);
        } else {
            // Show error message
            const errorAlert = document.createElement('div');
            errorAlert.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl shadow-lg z-50';
            errorAlert.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>Error: ' + (data.message || 'Something went wrong.');
            document.body.appendChild(errorAlert);

            setTimeout(() => {
                errorAlert.remove();
            }, 5000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const errorAlert = document.createElement('div');
        errorAlert.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl shadow-lg z-50';
        errorAlert.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>Error: Failed to submit request. Please try again.';
        document.body.appendChild(errorAlert);

        setTimeout(() => {
            errorAlert.remove();
        }, 5000);
    })
    .finally(() => {
        // Reset button state
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
});
</script>

<style>
/* Enhanced Premium Tailwind Styling */
.pagination-wrapper .pagination {
    @apply flex items-center justify-center space-x-3;
}

.pagination-wrapper .page-item {
    @apply block;
}

.pagination-wrapper .page-link {
    @apply px-5 py-3 text-sm font-semibold text-gray-700 bg-white border-2 border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300 transition-all duration-300 shadow-sm hover:shadow-md;
}

.pagination-wrapper .page-item.active .page-link {
    @apply bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700 shadow-lg;
}

.pagination-wrapper .page-item.disabled .page-link {
    @apply text-gray-400 cursor-not-allowed hover:bg-white hover:text-gray-400 hover:border-gray-200;
}

/* Premium Card Animations */
.talent-card {
    transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
    transform-origin: center;
}

.talent-card:hover {
    transform: translateY(-16px) scale(1.02);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(16, 185, 129, 0.1);
}

/* Sophisticated Gradient Animations */
@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes floatingPulse {
    0%, 100% {
        opacity: 0.6;
        transform: scale(1);
    }
    50% {
        opacity: 1;
        transform: scale(1.1);
    }
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradientShift 6s ease infinite;
}

.animate-floating {
    animation: floatingPulse 4s ease-in-out infinite;
}

/* Premium Hover Effects */
.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

.group:hover .group-hover\:translate-x-1 {
    transform: translateX(0.25rem);
}

.group:hover .group-hover\:rotate-12 {
    transform: rotate(12deg);
}

.group:hover .group-hover\:rotate-180 {
    transform: rotate(180deg);
}

/* Advanced Shadow Effects */
.shadow-3xl {
    box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.3);
}

.shadow-4xl {
    box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.4);
}

/* Enhanced Loading States */
.loading-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .3;
    }
}

/* Premium Scrollbar Styling */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: linear-gradient(to bottom, #f1f5f9, #e2e8f0);
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #64748b, #475569);
    border-radius: 10px;
    border: 2px solid #f1f5f9;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #475569, #334155);
}

/* Enhanced Modal Styling */
.modal-backdrop {
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8));
    backdrop-filter: blur(8px);
}

.modal-content {
    border-radius: 1.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Advanced Star Rating */
.star-rating {
    display: inline-flex;
    align-items: center;
    gap: 2px;
}

.star-rating .star {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
}

.star-rating .star:hover {
    color: #fbbf24;
    transform: scale(1.2);
}

/* Premium Button Hover Effects */
.btn-premium {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.btn-premium::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: all 0.5s ease;
}

.btn-premium:hover::before {
    width: 300px;
    height: 300px;
}

/* Enhanced Metric Cards */
.metric-card {
    position: relative;
    overflow: hidden;
}

.metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.5s;
}

.metric-card:hover::before {
    left: 100%;
}

/* Sophisticated Gradient Backgrounds */
.bg-premium-blue {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 50%, #93c5fd 100%);
}

.bg-premium-green {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 50%, #6ee7b7 100%);
}

.bg-premium-purple {
    background: linear-gradient(135deg, #e9d5ff 0%, #ddd6fe 50%, #c4b5fd 100%);
}

.bg-premium-orange {
    background: linear-gradient(135deg, #fed7aa 0%, #fdba74 50%, #fb923c 100%);
}

/* Advanced Animation Utilities */
@keyframes slideInUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes slideInLeft {
    from {
        transform: translateX(-30px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes scaleIn {
    from {
        transform: scale(0.9);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.animate-slideInUp {
    animation: slideInUp 0.6s ease-out;
}

.animate-slideInLeft {
    animation: slideInLeft 0.6s ease-out;
}

.animate-scaleIn {
    animation: scaleIn 0.5s ease-out;
}

/* Premium Glass Morphism */
.glass-effect {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Enhanced Focus States */
.focus-ring {
    @apply focus:outline-none focus:ring-4 focus:ring-emerald-500 focus:ring-opacity-50 focus:border-emerald-500;
}

/* Responsive Design Enhancements */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem;
        max-width: calc(100vw - 2rem);
    }

    .talent-card {
        margin-bottom: 2rem;
    }

    .grid-responsive {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
}

@media (min-width: 1536px) {
    .container-2xl {
        max-width: 1536px;
    }
}

/* Dark Mode Support (if needed) */
@media (prefers-color-scheme: dark) {
    .auto-dark {
        --tw-bg-opacity: 1;
        background-color: rgb(17 24 39 / var(--tw-bg-opacity));
        color: rgb(243 244 246 / var(--tw-text-opacity));
    }
}

/* Print Optimizations */
@media print {
    .no-print {
        display: none !important;
    }

    .talent-card {
        box-shadow: none;
        border: 1px solid #e5e7eb;
        page-break-inside: avoid;
    }
}

/* Performance Optimizations */
.talent-card,
.metric-card,
.btn-premium {
    will-change: transform;
}

/* Accessibility Enhancements */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* High Contrast Mode Support */
@media (prefers-contrast: high) {
    .talent-card {
        border-width: 2px;
        border-color: #000;
    }

    .btn-premium {
        border: 2px solid currentColor;
    }
}

/* Reduced Motion Support */
@media (prefers-reduced-motion: reduce) {
    .talent-card,
    .metric-card,
    .btn-premium,
    .animate-pulse,
    .animate-ping,
    .animate-gradient,
    .animate-floating {
        animation: none !important;
        transition: none !important;
    }
}

/* Talent Comparison Styles */
.talent-card {
    position: relative;
}

.compare-checkbox {
    transition: all 0.3s ease;
}

.talent-compare-check:checked + label,
.talent-card:has(.talent-compare-check:checked) {
    background-color: rgba(16, 185, 129, 0.1);
    border-color: #10b981;
}

#comparisonPanel {
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
}

.comparison-highlight {
    background-color: #fef3c7;
    font-weight: bold;
}

/* Modal responsive adjustments */
@media (max-width: 768px) {
    #talentComparisonModal .p-6 {
        padding: 1rem;
    }

    #comparisonContent table {
        font-size: 0.875rem;
    }

    #comparisonContent th,
    #comparisonContent td {
        padding: 0.5rem;
    }
}
</style>

<!-- Comparison Panel (Fixed at Bottom) -->
<div id="comparisonPanel" class="fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg transform translate-y-full transition-transform duration-300 ease-in-out z-40" style="display: none;">
    <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <h3 class="text-lg font-semibold text-gray-900 mr-4">Compare Talents</h3>
                <span id="selectedCount" class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-sm font-medium">0 selected</span>
            </div>
            <div class="flex gap-3">
                <button onclick="viewComparison()"
                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        id="compareBtn" disabled>
                    <i class="fas fa-chart-bar mr-2"></i>Compare Details
                </button>
                <button onclick="clearComparison()"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-times mr-2"></i>Clear
                </button>
                <button onclick="toggleCompareMode()"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-times mr-2"></i>Exit Compare
                </button>
            </div>
        </div>

        <!-- Selected Talents Preview -->
        <div id="selectedTalentsPreview" class="mt-4 flex gap-4 overflow-x-auto pb-2">
            <!-- Selected talents will be populated here by JavaScript -->
        </div>
    </div>
</div>

<!-- Talent Comparison Modal -->
<div id="talentComparisonModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="display: none;">
    <div class="bg-white rounded-2xl max-w-7xl w-full max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
        <div class="bg-emerald-600 text-white p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-balance-scale mr-3"></i>
                    Talent Comparison
                </h2>
                <button onclick="closeComparisonModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Content -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
            <div id="comparisonContent">
                <!-- Comparison content will be populated here -->
            </div>
        </div>
    </div>
</div>

@endsection
