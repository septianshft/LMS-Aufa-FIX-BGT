@extends('layout.template.mainTemplate')

@section('title', 'Manage Recruiters')

@section('container')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Page Heading -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-building text-indigo-600 mr-3"></i>
                Manage Recruiters
            </h1>
            <p class="text-gray-600">Manage recruiter accounts and their status</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('talent_admin.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-lg"></i>
                <div>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-3 text-lg"></i>
                <div>
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Recruiters Table -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-2xl p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-users text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-white">Recruiters List</h3>
            </div>
        </div>
        <div class="p-6">
            @if($recruiters->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b-2 border-gray-200">
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Recruiter</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Email</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Status</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Joined Date</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($recruiters as $recruiter)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="py-6 px-4">
                                        <div class="flex items-center">
                                            @if($recruiter->user->avatar)
                                                <img class="w-12 h-12 rounded-xl object-cover mr-4 shadow-md"
                                                     src="{{ asset('storage/' . $recruiter->user->avatar) }}"
                                                     alt="{{ $recruiter->user->name }}">
                                            @else
                                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 shadow-md">
                                                    <i class="fas fa-building text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $recruiter->user->name }}</div>
                                                @if($recruiter->user->pekerjaan)
                                                    <div class="text-gray-500 text-sm">{{ $recruiter->user->pekerjaan }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 px-4">
                                        <span class="text-gray-900 font-medium">{{ $recruiter->user->email }}</span>
                                    </td>
                                    <td class="py-6 px-4">
                                        @if($recruiter->is_active)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                <i class="fas fa-pause-circle mr-1"></i>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="text-gray-900 font-medium text-sm">{{ $recruiter->created_at->format('M d, Y') }}</div>
                                        <div class="text-gray-500 text-xs">{{ $recruiter->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="flex space-x-2">
                                            <button onclick="viewRecruiterDetails({{ $recruiter->id }})"
                                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl">
                                                <i class="fas fa-eye mr-2"></i>
                                                View Details
                                            </button>
                                            <form action="{{ route('talent_admin.toggle_recruiter_status', $recruiter) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-2 {{ $recruiter->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl"
                                                        onclick="return confirm('Are you sure you want to {{ $recruiter->is_active ? 'deactivate' : 'activate' }} this recruiter?')">
                                                    <i class="fas fa-{{ $recruiter->is_active ? 'pause' : 'play' }} mr-1"></i>
                                                </button>
                                            </form>
                                            <button onclick="deleteRecruiter({{ $recruiter->id }}, '{{ $recruiter->user->name }}')"
                                                    class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl btn-delete">
                                                <i class="fas fa-trash mr-1"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-6">
                    @foreach($recruiters as $recruiter)
                        <div class="bg-gradient-to-br from-white to-gray-50 border-2 border-gray-100 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover-lift">
                            <!-- Mobile Card Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    @if($recruiter->user->avatar)
                                        <img class="w-12 h-12 rounded-xl object-cover mr-3 shadow-md"
                                             src="{{ asset('storage/' . $recruiter->user->avatar) }}"
                                             alt="{{ $recruiter->user->name }}">
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mr-3 shadow-md">
                                            <i class="fas fa-building text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $recruiter->user->name }}</div>
                                        @if($recruiter->user->pekerjaan)
                                            <div class="text-gray-500 text-sm">{{ $recruiter->user->pekerjaan }}</div>
                                        @endif
                                    </div>
                                </div>
                                @if($recruiter->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <i class="fas fa-pause-circle mr-1"></i>
                                        Inactive
                                    </span>
                                @endif
                            </div>

                            <!-- Recruiter Details -->
                            <div class="grid grid-cols-1 gap-4 mb-4">
                                <div class="bg-white p-4 rounded-xl border border-gray-200">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Email</div>
                                    <div class="text-gray-900 font-medium">{{ $recruiter->user->email }}</div>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-gray-200">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Joined Date</div>
                                    <div class="text-gray-900 font-medium">{{ $recruiter->created_at->format('M d, Y H:i') }}</div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-3">
                                <button onclick="viewRecruiterDetails({{ $recruiter->id }})"
                                        class="flex-1 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                                    <i class="fas fa-eye mr-2"></i>
                                    View Details
                                </button>
                                <form action="{{ route('talent_admin.toggle_recruiter_status', $recruiter) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="px-4 py-3 {{ $recruiter->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl"
                                            onclick="return confirm('Are you sure you want to {{ $recruiter->is_active ? 'deactivate' : 'activate' }} this recruiter?')">
                                        <i class="fas fa-{{ $recruiter->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <button onclick="deleteRecruiter({{ $recruiter->id }}, '{{ $recruiter->user->name }}')"
                                        class="flex-shrink-0 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl btn-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-12 pt-8 border-t border-gray-200">
                    <div class="pagination-wrapper">
                        {{ $recruiters->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-users text-4xl text-gray-400"></i>
                    </div>
                    <h5 class="text-xl font-semibold text-gray-700 mb-3">No recruiters found</h5>
                    <p class="text-gray-500 max-w-md mx-auto">No recruiters have registered yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Recruiter Details Modal -->
<div id="recruiterDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-2xl bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-building text-indigo-600 mr-3"></i>
                    Recruiter Details
                </h3>
                <button onclick="closeRecruiterModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div id="recruiterDetailsContent" class="space-y-6">
                <!-- Content will be loaded here -->
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-indigo-600 mb-4"></i>
                    <p class="text-gray-600">Loading recruiter details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Recruiter details functionality
function viewRecruiterDetails(recruiterId) {
    document.getElementById('recruiterDetailsModal').classList.remove('hidden');

    // Reset content
    document.getElementById('recruiterDetailsContent').innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-3xl text-indigo-600 mb-4"></i>
            <p class="text-gray-600">Loading recruiter details...</p>
        </div>
    `;

    // Fetch recruiter details (you would implement this endpoint)
    fetch(`/talent-admin/recruiters/${recruiterId}/details`)
        .then(response => response.json())
        .then(data => {
            displayRecruiterDetails(data);
        })
        .catch(error => {
            document.getElementById('recruiterDetailsContent').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-600 mb-4"></i>
                    <p class="text-red-600">Error loading recruiter details</p>
                    <p class="text-gray-600 text-sm mt-2">Please try again later</p>
                </div>
            `;
        });
}

function displayRecruiterDetails(recruiter) {
    const content = `
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Info -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-indigo-50 to-purple-100 rounded-2xl p-6 text-center">
                    ${recruiter.avatar ?
                        `<img class="w-24 h-24 rounded-2xl object-cover mx-auto mb-4 shadow-lg" src="${recruiter.avatar}" alt="${recruiter.name}">` :
                        `<div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-building text-white text-2xl"></i>
                        </div>`
                    }
                    <h4 class="text-xl font-bold text-gray-900 mb-2">${recruiter.name}</h4>
                    <p class="text-gray-600 mb-2">${recruiter.company || 'Company not specified'}</p>
                    <p class="text-gray-500 text-sm mb-4">${recruiter.job || 'Position not specified'}</p>
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold ${recruiter.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                        <i class="fas fa-${recruiter.is_active ? 'check-circle' : 'pause-circle'} mr-1"></i>
                        ${recruiter.is_active ? 'Active' : 'Inactive'}
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Information -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-address-card text-indigo-600 mr-2"></i>
                        Contact Information
                    </h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</label>
                            <p class="text-gray-900 font-medium">${recruiter.email}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Phone</label>
                            <p class="text-gray-900 font-medium">${recruiter.phone || 'Not provided'}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Company</label>
                            <p class="text-gray-900 font-medium">${recruiter.company || 'Not provided'}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Joined Date</label>
                            <p class="text-gray-900 font-medium">${recruiter.joined_date}</p>
                        </div>
                    </div>
                </div>

                <!-- Company Information -->
                ${recruiter.company_details ? `
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-building text-purple-600 mr-2"></i>
                        Company Information
                    </h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Industry</label>
                            <p class="text-gray-900 font-medium">${recruiter.company_details.industry || 'Not specified'}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Company Size</label>
                            <p class="text-gray-900 font-medium">${recruiter.company_details.size || 'Not specified'}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Website</label>
                            <p class="text-gray-900 font-medium">
                                ${recruiter.company_details.website ?
                                    `<a href="${recruiter.company_details.website}" target="_blank" class="text-indigo-600 hover:text-indigo-700">
                                        ${recruiter.company_details.website}
                                        <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                    </a>` :
                                    'Not provided'
                                }
                            </p>
                        </div>
                        ${recruiter.company_details.description ? `
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</label>
                            <p class="text-gray-900 font-medium">${recruiter.company_details.description}</p>
                        </div>
                        ` : ''}
                    </div>
                </div>
                ` : ''}

                <!-- Recruitment Activity -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                        Recruitment Activity
                    </h5>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">${recruiter.stats?.total_requests || 0}</div>
                            <div class="text-xs text-gray-500">Total Requests</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">${recruiter.stats?.approved_requests || 0}</div>
                            <div class="text-xs text-gray-500">Approved</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">${recruiter.stats?.pending_requests || 0}</div>
                            <div class="text-xs text-gray-500">Pending</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">${recruiter.stats?.success_rate || 0}%</div>
                            <div class="text-xs text-gray-500">Success Rate</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Requests -->
                ${recruiter.recent_requests && recruiter.recent_requests.length > 0 ? `
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-history text-yellow-600 mr-2"></i>
                        Recent Talent Requests
                    </h5>
                    <div class="space-y-3">
                        ${recruiter.recent_requests.slice(0, 3).map(request => `
                            <div class="border border-gray-100 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h6 class="font-semibold text-gray-900">${request.project_title}</h6>
                                        <p class="text-gray-600 text-sm mt-1">${request.description}</p>
                                        <p class="text-xs text-gray-500 mt-2">${request.created_at}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                        ${request.status === 'approved' ? 'bg-green-100 text-green-800' :
                                          request.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                          'bg-red-100 text-red-800'}">
                                        ${request.status}
                                    </span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
                ` : ''}
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
            <button onclick="closeRecruiterModal()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Close
            </button>
            <button onclick="deleteRecruiter(${recruiter.id}, '${recruiter.name}')" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors btn-delete">
                <i class="fas fa-trash mr-2"></i>
                Delete Account
            </button>
            <button onclick="toggleRecruiterStatus(${recruiter.id}, ${recruiter.is_active})" class="px-6 py-2 ${recruiter.is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700'} text-white rounded-lg transition-colors">
                ${recruiter.is_active ? 'Deactivate' : 'Activate'} Recruiter
            </button>
        </div>
    `;

    document.getElementById('recruiterDetailsContent').innerHTML = content;
}

function closeRecruiterModal() {
    document.getElementById('recruiterDetailsModal').classList.add('hidden');
}

function toggleRecruiterStatus(recruiterId, isActive) {
    if (confirm(`Are you sure you want to ${isActive ? 'deactivate' : 'activate'} this recruiter?`)) {
        // Submit the form or make an AJAX request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/talent-admin/recruiters/${recruiterId}/toggle-status`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Delete recruiter functionality
function deleteRecruiter(recruiterId, recruiterName) {
    // Enhanced confirmation dialog
    const confirmed = confirm(
        `⚠️ WARNING: Delete Recruiter Account\n\n` +
        `Are you absolutely sure you want to delete "${recruiterName}"?\n\n` +
        `This action will:\n` +
        `• Permanently delete the recruiter account\n` +
        `• Remove all associated data\n` +
        `• Cannot be undone\n\n` +
        `Type "DELETE" to confirm this action.`
    );

    if (!confirmed) {
        return;
    }

    // Additional security confirmation
    const confirmText = prompt(
        `To confirm deletion of "${recruiterName}", please type "DELETE" (in uppercase):`
    );

    if (confirmText !== 'DELETE') {
        alert('❌ Deletion cancelled. Confirmation text did not match.');
        return;
    }

    // Show loading state
    const loadingModal = document.createElement('div');
    loadingModal.id = 'deleteLoadingModal';
    loadingModal.className = 'fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50';
    loadingModal.innerHTML = `
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 text-center shadow-2xl">
            <div class="mb-4">
                <i class="fas fa-spinner fa-spin text-4xl text-red-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Deleting Recruiter...</h3>
            <p class="text-gray-600">Please wait while we process the deletion.</p>
        </div>
    `;
    document.body.appendChild(loadingModal);

    // Perform AJAX delete request
    fetch(`/talent-admin/recruiter/${recruiterId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        // Remove loading modal
        document.body.removeChild(loadingModal);

        if (data.success) {
            // Success notification
            showNotification('success', data.message || 'Recruiter deleted successfully!');

            // Close modal if open
            closeRecruiterModal();

            // Refresh the page to update the list
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            // Error notification
            showNotification('error', data.message || 'Failed to delete recruiter.');
        }
    })
    .catch(error => {
        // Remove loading modal
        if (document.getElementById('deleteLoadingModal')) {
            document.body.removeChild(loadingModal);
        }

        console.error('Delete error:', error);
        showNotification('error', 'An error occurred while deleting the recruiter. Please try again.');
    });
}

// Notification system
function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 max-w-md rounded-lg shadow-lg p-4 transform transition-all duration-300 notification-enter ${
        type === 'success'
            ? 'bg-green-500 text-white'
            : 'bg-red-500 text-white'
    }`;

    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} mr-3 text-lg"></i>
            <div class="flex-1">
                <p class="font-medium">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Close modal when clicking outside
document.getElementById('recruiterDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRecruiterModal();
    }
});
</script>

<style>
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
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-color: #6366f1;
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
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

/* Delete button specific styling */
.btn-delete {
    position: relative;
    overflow: hidden;
}

.btn-delete:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-delete:hover:before {
    left: 100%;
}

/* Loading spinner animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.fa-spin {
    animation: spin 1s linear infinite;
}

/* Notification animations */
.notification-enter {
    opacity: 0;
    transform: translateX(100%);
    animation: slideInRight 0.3s ease-out forwards;
}

@keyframes slideInRight {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>
@endsection
