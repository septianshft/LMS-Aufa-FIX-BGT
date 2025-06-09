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
                                        <form action="{{ route('talent_admin.toggle_talent_status', $talent) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 {{ $talent->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl"
                                                    onclick="return confirm('Are you sure you want to {{ $talent->is_active ? 'deactivate' : 'activate' }} this talent?')">
                                                <i class="fas fa-{{ $talent->is_active ? 'pause' : 'play' }} mr-2"></i>
                                                {{ $talent->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
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
                            <div class="flex justify-center">
                                <form action="{{ route('talent_admin.toggle_talent_status', $talent) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="w-full px-4 py-3 {{ $talent->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl"
                                            onclick="return confirm('Are you sure you want to {{ $talent->is_active ? 'deactivate' : 'activate' }} this talent?')">
                                        <i class="fas fa-{{ $talent->is_active ? 'pause' : 'play' }} mr-2"></i>
                                        {{ $talent->is_active ? 'Deactivate' : 'Activate' }}
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
