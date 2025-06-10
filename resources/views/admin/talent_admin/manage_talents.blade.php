@extends('layout.template.mainTemplate')

@section('title', 'Manage Talents')

@section('container')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Page Heading -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-user-tie text-blue-600 mr-3"></i>
                Manage Talents
            </h1>
            <p class="text-gray-600">Manage talent profiles and their status</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('talent_admin.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
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

    <!-- Talents Table -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-2xl p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-star text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-white">Talents List</h3>
            </div>
        </div>
        <div class="p-6">
            @if($talents->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b-2 border-gray-200">
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Talent</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Email</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Status</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Joined</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($talents as $talent)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="py-6 px-4">
                                        <div class="flex items-center">
                                            @if($talent->user->avatar)
                                                <img class="w-12 h-12 rounded-xl object-cover mr-4 shadow-md"
                                                     src="{{ asset('storage/' . $talent->user->avatar) }}"
                                                     alt="{{ $talent->user->name }}">
                                            @else
                                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4 shadow-md">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $talent->user->name }}</div>
                                                <div class="text-gray-500 text-sm">{{ $talent->user->pekerjaan }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 px-4">
                                        <span class="text-gray-900 font-medium">{{ $talent->user->email }}</span>
                                    </td>
                                    <td class="py-6 px-4">
                                        @if($talent->is_active)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                <i class="fas fa-pause-circle mr-1"></i>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="text-gray-900 font-medium text-sm">{{ $talent->created_at->format('M d, Y') }}</div>
                                        <div class="text-gray-500 text-xs">{{ $talent->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="flex space-x-2">
                                            <button onclick="viewTalentDetails({{ $talent->id }})"
                                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl">
                                                <i class="fas fa-eye mr-2"></i>
                                                View Details
                                            </button>
                                            <form action="{{ route('talent_admin.toggle_talent_status', $talent) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-2 {{ $talent->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl"
                                                        onclick="return confirm('Are you sure you want to {{ $talent->is_active ? 'deactivate' : 'activate' }} this talent?')">
                                                    <i class="fas fa-{{ $talent->is_active ? 'pause' : 'play' }} mr-1"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-6">
                    @foreach($talents as $talent)
                        <div class="bg-gradient-to-br from-white to-gray-50 border-2 border-gray-100 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover-lift">
                            <!-- Mobile Card Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    @if($talent->user->avatar)
                                        <img class="w-12 h-12 rounded-xl object-cover mr-3 shadow-md"
                                             src="{{ asset('storage/' . $talent->user->avatar) }}"
                                             alt="{{ $talent->user->name }}">
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3 shadow-md">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $talent->user->name }}</div>
                                        <div class="text-gray-500 text-sm">{{ $talent->user->pekerjaan }}</div>
                                    </div>
                                </div>
                                @if($talent->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                        <i class="fas fa-pause-circle mr-1"></i>
                                        Inactive
                                    </span>
                                @endif
                            </div>

                            <!-- Talent Details -->
                            <div class="grid grid-cols-1 gap-4 mb-4">
                                <div class="bg-white p-4 rounded-xl border border-gray-200">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Email</div>
                                    <div class="text-gray-900 font-medium">{{ $talent->user->email }}</div>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-gray-200">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Joined Date</div>
                                    <div class="text-gray-900 font-medium">{{ $talent->created_at->format('M d, Y H:i') }}</div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-3">
                                <button onclick="viewTalentDetails({{ $talent->id }})"
                                        class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                                    <i class="fas fa-eye mr-2"></i>
                                    View Details
                                </button>
                                <form action="{{ route('talent_admin.toggle_talent_status', $talent) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="px-4 py-3 {{ $talent->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl"
                                            onclick="return confirm('Are you sure you want to {{ $talent->is_active ? 'deactivate' : 'activate' }} this talent?')">
                                        <i class="fas fa-{{ $talent->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
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
                    <h5 class="text-xl font-semibold text-gray-700 mb-3">No talents found</h5>
                    <p class="text-gray-500 max-w-md mx-auto">No talents have registered yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Talent Details Modal -->
<div id="talentDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-2xl bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-user-tie text-blue-600 mr-3"></i>
                    Talent Details
                </h3>
                <button onclick="closeTalentModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div id="talentDetailsContent" class="space-y-6">
                <!-- Content will be loaded here -->
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
                    <p class="text-gray-600">Loading talent details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Talent details functionality
function viewTalentDetails(talentId) {
    document.getElementById('talentDetailsModal').classList.remove('hidden');

    // Reset content
    document.getElementById('talentDetailsContent').innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
            <p class="text-gray-600">Loading talent details...</p>
        </div>
    `;

    // Fetch talent details (you would implement this endpoint)
    fetch(`/talent-admin/talents/${talentId}/details`)
        .then(response => response.json())
        .then(data => {
            displayTalentDetails(data);
        })
        .catch(error => {
            document.getElementById('talentDetailsContent').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-600 mb-4"></i>
                    <p class="text-red-600">Error loading talent details</p>
                    <p class="text-gray-600 text-sm mt-2">Please try again later</p>
                </div>
            ;
        });
}

function displayTalentDetails(talent) {
    const content = `
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Info -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-6 text-center">
                    ${talent.avatar ?
                        `<img class="w-24 h-24 rounded-2xl object-cover mx-auto mb-4 shadow-lg" src="${talent.avatar}" alt="${talent.name}">` :
                        `<div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-user text-white text-2xl"></i>
                        </div>`
                    }
                    <h4 class="text-xl font-bold text-gray-900 mb-2">${talent.name}</h4>
                    <p class="text-gray-600 mb-4">${talent.job || 'No job specified'}</p>
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold ${talent.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                        <i class="fas fa-${talent.is_active ? 'check-circle' : 'pause-circle'} mr-1"></i>
                        ${talent.is_active ? 'Active' : 'Inactive'}
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Information -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-address-card text-blue-600 mr-2"></i>
                        Contact Information
                    </h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</label>
                            <p class="text-gray-900 font-medium">${talent.email}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Phone</label>
                            <p class="text-gray-900 font-medium">${talent.phone || 'Not provided'}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Location</label>
                            <p class="text-gray-900 font-medium">${talent.location || 'Not provided'}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Joined Date</label>
                            <p class="text-gray-900 font-medium">${talent.joined_date}</p>
                        </div>
                    </div>
                </div>

                <!-- Skills -->
                ${talent.skills && talent.skills.length > 0 ? `
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-cogs text-purple-600 mr-2"></i>
                        Skills & Expertise
                    </h5>
                    <div class="flex flex-wrap gap-2">
                        ${talent.skills.map(skill => `
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                ${skill.name} ${skill.level ? `(${skill.level})` : ''}
                            </span>
                        `).join('')}
                    </div>
                </div>
                ` : ''}

                <!-- Portfolio/Projects -->
                ${talent.portfolio && talent.portfolio.length > 0 ? `
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-briefcase text-green-600 mr-2"></i>
                        Portfolio & Projects
                    </h5>
                    <div class="space-y-3">
                        ${talent.portfolio.map(project => `
                            <div class="border border-gray-100 rounded-lg p-4">
                                <h6 class="font-semibold text-gray-900">${project.title}</h6>
                                <p class="text-gray-600 text-sm mt-1">${project.description}</p>
                                ${project.url ? `<a href="${project.url}" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm mt-2 inline-flex items-center">
                                    <i class="fas fa-external-link-alt mr-1"></i>
                                    View Project
                                </a>` : ''}
                            </div>
                        `).join('')}
                    </div>
                </div>
                ` : ''}

                <!-- Statistics -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-yellow-600 mr-2"></i>
                        Statistics
                    </h5>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">${talent.stats?.completed_courses || 0}</div>
                            <div class="text-xs text-gray-500">Courses</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">${talent.stats?.certificates || 0}</div>
                            <div class="text-xs text-gray-500">Certificates</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">${talent.stats?.skill_level || 'N/A'}</div>
                            <div class="text-xs text-gray-500">Avg Skill Level</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">${talent.stats?.experience_years || 0}</div>
                            <div class="text-xs text-gray-500">Years Exp</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
            <button onclick="closeTalentModal()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Close
            </button>
            <button onclick="toggleTalentStatus(${talent.id}, ${talent.is_active})" class="px-6 py-2 ${talent.is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700'} text-white rounded-lg transition-colors">
                ${talent.is_active ? 'Deactivate' : 'Activate'} Talent
            </button>
        </div>
    `;

    document.getElementById('talentDetailsContent').innerHTML = content;
}

function closeTalentModal() {
    document.getElementById('talentDetailsModal').classList.add('hidden');
}

function toggleTalentStatus(talentId, isActive) {
    if (confirm(`Are you sure you want to ${isActive ? 'deactivate' : 'activate'} this talent?`)) {
        // Submit the form or make an AJAX request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/talent-admin/talents/${talentId}/toggle-status`;

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

// Close modal when clicking outside
document.getElementById('talentDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTalentModal();
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
    background: linear-gradient(135deg, #2563eb, #6366f1);
    border-color: #2563eb;
    color: white;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
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
</style>
@endsection
