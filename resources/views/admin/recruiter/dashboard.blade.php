@extends('layout.template.mainTemplate')

@section('title', 'Recruiter Dashboard')
@section('container')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Page Heading -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Recruiter Dashboard</h1>
            <p class="text-gray-600">Manage your talent acquisition and discovery</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <span class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                <i class="fas fa-user-circle mr-2"></i>
                Welcome back, {{ $user->name }}!
            </span>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-xl p-8 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-10 rounded-full transform translate-x-16 -translate-y-16"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white opacity-10 rounded-full transform -translate-x-8 translate-y-8"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-blue-200 text-sm font-semibold uppercase tracking-wider mb-2">
                        Welcome Back!
                    </div>
                    <h2 class="text-2xl font-bold mb-3">
                        Hello, {{ $user->name }}
                    </h2>
                    <p class="text-blue-100 max-w-2xl">
                        Discover talented individuals and connect with potential candidates for your opportunities.
                        Start building your dream team today.
                    </p>
                </div>
                <div class="hidden md:block">
                    <div class="w-24 h-24 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-search text-3xl text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        <!-- Available Talents Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2">
                        Available Talents
                    </div>
                    <div class="text-3xl font-bold text-gray-900 mb-1">{{ $talents->total() }}</div>
                    <div class="text-sm text-gray-500">Active professionals</div>
                </div>
                <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-tie text-2xl text-blue-600"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center text-sm text-green-600">
                    <i class="fas fa-arrow-up mr-1"></i>
                    <span class="font-medium">Ready to hire</span>
                </div>
            </div>
        </div>

        <!-- Active Status Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-bold text-green-600 uppercase tracking-wider mb-2">
                        Account Status
                    </div>
                    <div class="text-3xl font-bold text-gray-900 mb-1">
                        {{ $user->recruiter && $user->recruiter->is_active ? 'Active' : 'Inactive' }}
                    </div>
                    <div class="text-sm text-gray-500">Recruitment status</div>
                </div>
                <div class="w-16 h-16 {{ $user->recruiter && $user->recruiter->is_active ? 'bg-green-100' : 'bg-red-100' }} rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl {{ $user->recruiter && $user->recruiter->is_active ? 'text-green-600' : 'text-red-600' }}"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center text-sm {{ $user->recruiter && $user->recruiter->is_active ? 'text-green-600' : 'text-red-600' }}">
                    <i class="fas fa-circle mr-2 text-xs"></i>
                    <span class="font-medium">{{ $user->recruiter && $user->recruiter->is_active ? 'Fully operational' : 'Account inactive' }}</span>
                </div>
            </div>
        </div>

        <!-- My Requests Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-bold text-orange-600 uppercase tracking-wider mb-2">
                        My Requests
                    </div>
                    <div class="text-3xl font-bold text-gray-900 mb-1">{{ $myRequests->count() }}</div>
                    <div class="text-sm text-gray-500">Total submissions</div>
                </div>
                <div class="w-16 h-16 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-handshake text-2xl text-orange-600"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <a href="{{ route('recruiter.my_requests') }}" class="flex items-center text-sm text-orange-600 hover:text-orange-700 transition-colors duration-200">
                    <span class="font-medium">View all requests</span>
                    <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- My Recent Requests Section -->
    @if($myRequests->count() > 0)
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-8 overflow-hidden">
        <div class="px-8 py-6 bg-gradient-to-r from-purple-600 to-indigo-700 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center mb-4 sm:mb-0">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-clipboard-list text-xl text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">My Recent Talent Requests</h2>
                        <p class="text-purple-100 text-sm">Track your submission status</p>
                    </div>
                </div>
                <a href="{{ route('recruiter.my_requests') }}"
                   class="inline-flex items-center px-6 py-3 bg-white text-purple-700 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium shadow-lg">
                    <i class="fas fa-list mr-2"></i>
                    View All Requests
                </a>
            </div>
        </div>

        <div class="p-8">
            <div class="overflow-x-auto">
                <div class="min-w-full">
                    <!-- Desktop Table View -->
                    <div class="hidden lg:block">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b-2 border-gray-200">
                                    <th class="text-left py-4 px-4 font-semibold text-gray-700 uppercase tracking-wider text-sm">Talent</th>
                                    <th class="text-left py-4 px-4 font-semibold text-gray-700 uppercase tracking-wider text-sm">Project Details</th>
                                    <th class="text-left py-4 px-4 font-semibold text-gray-700 uppercase tracking-wider text-sm">Status</th>
                                    <th class="text-left py-4 px-4 font-semibold text-gray-700 uppercase tracking-wider text-sm">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($myRequests as $request)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="py-6 px-4">
                                        <div class="flex items-center">
                                            @if($request->talent->user->avatar)
                                                <img class="w-12 h-12 rounded-xl object-cover mr-4 shadow-md"
                                                     src="{{ asset('storage/' . $request->talent->user->avatar) }}"
                                                     alt="{{ $request->talent->user->name }}">
                                            @else
                                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4 shadow-md">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $request->talent->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $request->talent->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="max-w-xs">
                                            <div class="font-semibold text-gray-900 mb-1">{{ $request->project_title }}</div>
                                            <p class="text-gray-600 text-sm leading-relaxed">{{ Str::limit($request->project_description, 60) }}</p>
                                        </div>
                                    </td>
                                    <td class="py-6 px-4">
                                        <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium
                                            @if($request->getStatusBadgeColor() == 'success') bg-green-100 text-green-800 border border-green-200
                                            @elseif($request->getStatusBadgeColor() == 'warning') bg-yellow-100 text-yellow-800 border border-yellow-200
                                            @elseif($request->getStatusBadgeColor() == 'info') bg-blue-100 text-blue-800 border border-blue-200
                                            @elseif($request->getStatusBadgeColor() == 'danger') bg-red-100 text-red-800 border border-red-200
                                            @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                                            <div class="w-2 h-2 rounded-full mr-2
                                                @if($request->getStatusBadgeColor() == 'success') bg-green-400
                                                @elseif($request->getStatusBadgeColor() == 'warning') bg-yellow-400
                                                @elseif($request->getStatusBadgeColor() == 'info') bg-blue-400
                                                @elseif($request->getStatusBadgeColor() == 'danger') bg-red-400
                                                @else bg-gray-400 @endif"></div>
                                            {{ $request->getFormattedStatus() }}
                                        </span>
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="text-gray-900 font-medium">{{ $request->created_at->format('M d, Y') }}</div>
                                        <div class="text-gray-500 text-sm">{{ $request->created_at->diffForHumans() }}</div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="lg:hidden space-y-4">
                        @foreach($myRequests as $request)
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                            <div class="flex items-start justify-between mb-4">
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
                                        <div class="text-sm text-gray-500">{{ $request->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if($request->getStatusBadgeColor() == 'success') bg-green-100 text-green-800
                                    @elseif($request->getStatusBadgeColor() == 'warning') bg-yellow-100 text-yellow-800
                                    @elseif($request->getStatusBadgeColor() == 'info') bg-blue-100 text-blue-800
                                    @elseif($request->getStatusBadgeColor() == 'danger') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $request->getFormattedStatus() }}
                                </span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 mb-2">{{ $request->project_title }}</div>
                                <p class="text-gray-600 text-sm">{{ Str::limit($request->project_description, 100) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Talent Discovery Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 bg-gradient-to-r from-emerald-600 to-teal-700 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center mb-4 sm:mb-0">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-users text-xl text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">Discover Talents</h2>
                        <p class="text-emerald-100 text-sm">Find the perfect match for your projects</p>
                    </div>
                </div>
                <button onclick="refreshTalents()"
                        class="inline-flex items-center px-6 py-3 bg-white text-emerald-700 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium shadow-lg">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh List
                </button>
            </div>
        </div>

        <div class="p-8">
            @if($talents->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                    @foreach($talents as $talent)
                        <div class="group bg-white rounded-2xl border-2 border-gray-100 hover:border-blue-200 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
                            <!-- Profile Header -->
                            <div class="p-6 text-center bg-gradient-to-br from-gray-50 to-white">
                                @if($talent->user->avatar)
                                    <img class="w-20 h-20 rounded-2xl object-cover mx-auto mb-4 shadow-xl border-4 border-white"
                                         src="{{ asset('storage/' . $talent->user->avatar) }}"
                                         alt="{{ $talent->user->name }}">
                                @else
                                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl">
                                        <i class="fas fa-user-tie text-2xl text-white"></i>
                                    </div>
                                @endif

                                <h3 class="font-bold text-gray-900 text-lg mb-2">{{ $talent->user->name }}</h3>

                                @if($talent->user->pekerjaan)
                                    <p class="text-gray-600 text-sm mb-4 px-3 py-1 bg-gray-100 rounded-full inline-block">{{ $talent->user->pekerjaan }}</p>
                                @endif

                                @php
                                    $existingRequest = $talent->talentRequests->first();
                                @endphp

                                @if($existingRequest)
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                                        @if($existingRequest->getStatusBadgeColor() == 'success') bg-green-100 text-green-800 border-2 border-green-200
                                        @elseif($existingRequest->getStatusBadgeColor() == 'warning') bg-yellow-100 text-yellow-800 border-2 border-yellow-200
                                        @elseif($existingRequest->getStatusBadgeColor() == 'info') bg-blue-100 text-blue-800 border-2 border-blue-200
                                        @elseif($existingRequest->getStatusBadgeColor() == 'danger') bg-red-100 text-red-800 border-2 border-red-200
                                        @else bg-gray-100 text-gray-800 border-2 border-gray-200 @endif">
                                        <div class="w-2 h-2 rounded-full mr-2
                                            @if($existingRequest->getStatusBadgeColor() == 'success') bg-green-400
                                            @elseif($existingRequest->getStatusBadgeColor() == 'warning') bg-yellow-400
                                            @elseif($existingRequest->getStatusBadgeColor() == 'info') bg-blue-400
                                            @elseif($existingRequest->getStatusBadgeColor() == 'danger') bg-red-400
                                            @else bg-gray-400 @endif"></div>
                                        {{ $existingRequest->getFormattedStatus() }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 border-2 border-green-200">
                                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                        Available
                                    </span>
                                @endif
                            </div>

                            <!-- Contact Info -->
                            <div class="px-6 pb-6">
                                <div class="space-y-3 mb-6">
                                    <div class="flex items-center text-sm text-gray-600 bg-gray-50 p-3 rounded-xl">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-envelope text-blue-600 text-xs"></i>
                                        </div>
                                        <span class="truncate font-medium">{{ $talent->user->email }}</span>
                                    </div>

                                    @if($talent->user->no_telp)
                                        <div class="flex items-center text-sm text-gray-600 bg-gray-50 p-3 rounded-xl">
                                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-phone text-green-600 text-xs"></i>
                                            </div>
                                            <span class="font-medium">{{ $talent->user->no_telp }}</span>
                                        </div>
                                    @endif

                                    @if($talent->user->alamat)
                                        <div class="flex items-center text-sm text-gray-600 bg-gray-50 p-3 rounded-xl">
                                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-map-marker-alt text-purple-600 text-xs"></i>
                                            </div>
                                            <span class="truncate font-medium">{{ Str::limit($talent->user->alamat, 25) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Member Since -->
                                <div class="text-center mb-6 py-3 bg-gray-50 rounded-xl">
                                    <span class="text-gray-500 text-sm font-medium">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        Member since {{ $talent->created_at->format('M Y') }}
                                    </span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="space-y-3">
                                    <button type="button"
                                            onclick="viewTalentDetails('{{ $talent->user->name }}', '{{ $talent->user->email }}', '{{ $talent->user->pekerjaan ?? 'Not specified' }}', '{{ $talent->user->alamat ?? 'Not specified' }}', '{{ $talent->user->no_telp ?? 'Not specified' }}')"
                                            class="w-full px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                        <i class="fas fa-eye mr-2"></i>View Profile
                                    </button>

                                    <div class="grid grid-cols-2 gap-3">
                                        @php
                                            $existingRequest = $talent->talentRequests->first();
                                        @endphp

                                        @if($existingRequest)
                                            @if($existingRequest->status == 'pending')
                                                <button type="button" class="px-3 py-2 bg-yellow-100 text-yellow-800 rounded-xl font-semibold cursor-not-allowed" disabled>
                                                    <i class="fas fa-clock mr-1"></i>Pending
                                                </button>
                                            @elseif($existingRequest->status == 'approved')
                                                <button type="button" class="px-3 py-2 bg-blue-100 text-blue-800 rounded-xl font-semibold cursor-not-allowed" disabled>
                                                    <i class="fas fa-check mr-1"></i>Approved
                                                </button>
                                            @elseif($existingRequest->status == 'onboarded')
                                                <button type="button" class="px-3 py-2 bg-green-100 text-green-800 rounded-xl font-semibold cursor-not-allowed" disabled>
                                                    <i class="fas fa-handshake mr-1"></i>Onboarded
                                                </button>
                                            @else
                                                <button type="button"
                                                        onclick="openRequestModal('{{ $talent->id }}', '{{ $talent->user->name }}')"
                                                        class="px-3 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all duration-200 font-semibold">
                                                    <i class="fas fa-handshake mr-1"></i>Request
                                                </button>
                                            @endif
                                        @else
                                            <button type="button"
                                                    onclick="openRequestModal('{{ $talent->id }}', '{{ $talent->user->name }}')"
                                                    class="px-3 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all duration-200 font-semibold">
                                                <i class="fas fa-handshake mr-1"></i>Request
                                            </button>
                                        @endif

                                        <a href="mailto:{{ $talent->user->email }}"
                                           class="px-3 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-200 font-semibold text-center">
                                            <i class="fas fa-envelope mr-1"></i>Email
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-12 pt-8 border-t border-gray-200">
                    <div class="pagination-wrapper">
                        {{ $talents->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-user-tie text-4xl text-gray-400"></i>
                    </div>
                    <h5 class="text-xl font-semibold text-gray-700 mb-3">No Talents Available</h5>
                    <p class="text-gray-500 max-w-md mx-auto">There are currently no active talents in the system. Check back later or contact your administrator.</p>
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
});

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
/* Custom Tailwind enhancements */
.pagination-wrapper .pagination {
    @apply flex items-center justify-center space-x-2;
}

.pagination-wrapper .page-item {
    @apply block;
}

.pagination-wrapper .page-link {
    @apply px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-all duration-200;
}

.pagination-wrapper .page-item.active .page-link {
    @apply bg-blue-600 text-white border-blue-600 hover:bg-blue-700;
}

.pagination-wrapper .page-item.disabled .page-link {
    @apply text-gray-400 cursor-not-allowed hover:bg-white hover:text-gray-400;
}

/* Modal backdrop */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(2px);
}

/* Smooth transitions for cards */
.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

/* Custom scrollbar for modal content */
.modal-body {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.modal-body::-webkit-scrollbar {
    width: 6px;
}

.modal-body::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* Animation for loading states */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-in {
    animation: fadeIn 0.3s ease-out;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem;
    }

    .grid-cols-1.md\:grid-cols-2.xl\:grid-cols-3 > * {
        margin-bottom: 1.5rem;
    }
}
</style>
@endsection
