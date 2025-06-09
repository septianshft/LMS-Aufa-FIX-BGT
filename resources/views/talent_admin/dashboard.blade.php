@extends('layout.template.mainTemplate')

@section('title', 'Dashboard Admin Talent')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('container')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Page Heading with Welcome Message -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-tachometer-alt text-purple-600 mr-3"></i>
                Dashboard Pencarian Talent
            </h1>
            <p class="text-gray-600">Selamat datang kembali! Berikut adalah yang terjadi dengan platform pencarian talent Anda.</p>
        </div>
    </div>

    <!-- Overview Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Talents Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover-lift">
            <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="text-white">
                        <div class="text-3xl font-bold">{{ $totalTalents }}</div>
                        <div class="text-blue-100 text-sm font-medium">Total Talent</div>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-blue-50">
                <div class="flex items-center text-blue-700 text-sm">
                    <i class="fas fa-arrow-up mr-2"></i>
                    <span>Aktif: {{ $activeTalents }}</span>
                </div>
            </div>
        </div>

        <!-- Total Recruiters Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover-lift">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="text-white">
                        <div class="text-3xl font-bold">{{ $totalRecruiters }}</div>
                        <div class="text-indigo-100 text-sm font-medium">Total Perekrut</div>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-indigo-50">
                <div class="flex items-center text-indigo-700 text-sm">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Aktif: {{ $activeRecruiters }}</span>
                </div>
            </div>
        </div>

        <!-- Total Requests Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover-lift">
            <div class="bg-gradient-to-br from-green-500 to-green-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="text-white">
                        <div class="text-3xl font-bold">{{ $totalRequests }}</div>
                        <div class="text-green-100 text-sm font-medium">Total Permintaan</div>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-green-50">
                <div class="flex items-center text-green-700 text-sm">
                    <i class="fas fa-thumbs-up mr-2"></i>
                    <span>Disetujui: {{ $approvedRequests }}</span>
                </div>
            </div>
        </div>

        <!-- Pending Requests Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover-lift">
            <div class="bg-gradient-to-br from-orange-500 to-orange-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="text-white">
                        <div class="text-3xl font-bold">{{ $pendingRequests }}</div>
                        <div class="text-orange-100 text-sm font-medium">Permintaan Tertunda</div>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-orange-50">
                <div class="flex items-center text-orange-700 text-sm">
                    @if($pendingRequests > 0)
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span>Perlu Perhatian</span>
                    @else
                        <i class="fas fa-check mr-2"></i>
                        <span>Semua Aman</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Management Cards -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-8">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-t-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <h3 class="text-lg font-semibold text-white">Aksi Manajemen Cepat</h3>
                </div>
            </div>
        </div>
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Analytics Dashboard (Phase 1 Enhancement) -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 hover-lift border border-purple-200">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-chart-bar text-white text-xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Analytics</h4>
                        <p class="text-gray-600 text-sm mb-6">Lihat analitik skill, konversi, dan permintaan pasar talent.</p>
                        <div class="space-y-3">
                            <a href="{{ route('talent_admin.analytics') }}" class="block w-full px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-all duration-200 font-medium text-sm">
                                <i class="fas fa-chart-line mr-2"></i>Lihat Analytics
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Manage Talents -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 hover-lift border border-blue-200">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Kelola Talent</h4>
                        <p class="text-gray-600 text-sm mb-6">Lihat dan kelola profil talent, keahlian, dan status ketersediaan.</p>
                        <div class="space-y-3">
                            <a href="{{ route('talent_admin.manage_talents') }}" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium text-sm">
                                <i class="fas fa-eye mr-2"></i>Lihat Semua Talent
                            </a>
                            <button class="w-full px-4 py-2 bg-white text-blue-600 border-2 border-blue-600 rounded-xl hover:bg-blue-50 transition-all duration-200 font-medium text-sm" onclick="showComingSoon('Tambah Talent Baru')">
                                <i class="fas fa-plus mr-2"></i>Tambah Talent Baru
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Manage Recruiters -->
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl p-6 hover-lift border border-indigo-200">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Kelola Perekrut</h4>
                        <p class="text-gray-600 text-sm mb-6">Awasi akun perekrut dan informasi perusahaan.</p>
                        <div class="space-y-3">
                            <a href="{{ route('talent_admin.manage_recruiters') }}" class="block w-full px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all duration-200 font-medium text-sm">
                                <i class="fas fa-eye mr-2"></i>Lihat Semua Perekrut
                            </a>
                            <button class="w-full px-4 py-2 bg-white text-indigo-600 border-2 border-indigo-600 rounded-xl hover:bg-indigo-50 transition-all duration-200 font-medium text-sm" onclick="showComingSoon('Tambah Perekrut Baru')">
                                <i class="fas fa-plus mr-2"></i>Tambah Perekrut Baru
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Manage Requests -->
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-6 hover-lift border border-orange-200">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-handshake text-white text-xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Kelola Permintaan</h4>
                        <p class="text-gray-600 text-sm mb-6">Tinjau dan proses permintaan pencarian talent dari perekrut.</p>
                        <div class="space-y-3">
                            <a href="{{ route('talent_admin.manage_requests') }}" class="block w-full px-4 py-2 bg-orange-600 text-white rounded-xl hover:bg-orange-700 transition-all duration-200 font-medium text-sm">
                                <i class="fas fa-list mr-2"></i>Lihat Semua Permintaan
                            </a>
                            <a href="{{ route('talent_admin.manage_requests', ['status' => 'pending']) }}" class="block w-full px-4 py-2 bg-white text-orange-600 border-2 border-orange-600 rounded-xl hover:bg-orange-50 transition-all duration-200 font-medium text-sm">
                                <i class="fas fa-clock mr-2"></i>Tertunda ({{ $pendingRequests }})
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Recent Activity Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-8">
        <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-t-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Permintaan Talent Terbaru</h3>
                        <p class="text-green-100 text-sm">Permintaan terbaru dari perekrut yang memerlukan perhatian Anda</p>
                    </div>
                </div>
                <a href="{{ route('talent_admin.manage_requests') }}" class="px-4 py-2 bg-white text-green-600 rounded-xl hover:bg-green-50 transition-all duration-200 font-medium text-sm shadow-sm border border-white border-opacity-30">
                    <i class="fas fa-eye mr-2"></i>Lihat Semua Permintaan
                </a>
            </div>
        </div>
        <div class="p-6">
            @forelse($latestRequests as $request)
                <div class="flex items-center p-4 mb-4 bg-gray-50 rounded-xl hover:bg-white hover:shadow-lg transition-all duration-200 border border-gray-100">
                    <div class="mr-4">
                        @if($request->recruiter->user->avatar)
                            <img class="w-12 h-12 rounded-xl object-cover shadow-md" src="{{ asset('storage/' . $request->recruiter->user->avatar) }}"
                                 alt="{{ $request->recruiter->user->name }}">
                        @else
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                                <i class="fas fa-building text-white"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-900">{{ $request->recruiter->user->name }}</h4>
                            <span class="text-gray-500 text-sm">{{ $request->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-600 text-sm mb-2">{{ Str::limit($request->project_title, 60) }}</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="text-gray-500 text-xs">Untuk: {{ $request->talent->user->name }}</span>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'meeting_arranged' => 'bg-blue-100 text-blue-800',
                                        'agreement_reached' => 'bg-purple-100 text-purple-800',
                                        'onboarded' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'completed' => 'bg-green-100 text-green-800'
                                    ];
                                    $statusIcons = [
                                        'pending' => 'fas fa-clock',
                                        'approved' => 'fas fa-check',
                                        'meeting_arranged' => 'fas fa-calendar',
                                        'agreement_reached' => 'fas fa-handshake',
                                        'onboarded' => 'fas fa-user-plus',
                                        'rejected' => 'fas fa-times',
                                        'completed' => 'fas fa-flag-checkered'
                                    ];
                                    $statusTranslations = [
                                        'pending' => 'Tertunda',
                                        'approved' => 'Disetujui',
                                        'meeting_arranged' => 'Pertemuan Diatur',
                                        'agreement_reached' => 'Kesepakatan Tercapai',
                                        'onboarded' => 'Bergabung',
                                        'rejected' => 'Ditolak',
                                        'completed' => 'Selesai'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    <i class="{{ $statusIcons[$request->status] ?? 'fas fa-question' }} mr-1"></i>
                                    {{ $statusTranslations[$request->status] ?? ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </div>
                            <a href="{{ route('talent_admin.show_request', $request) }}" class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 text-sm">
                                <i class="fas fa-eye mr-1"></i>Lihat
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Belum ada permintaan talent</h4>
                    <p class="text-gray-500">Permintaan baru akan muncul di sini saat perekrut mengirimkannya.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Recent Users Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Talents -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <h3 class="text-lg font-semibold text-white">Talent Terbaru</h3>
                    </div>
                    <span class="px-3 py-1 bg-white bg-opacity-20 text-black rounded-full text-sm font-medium">{{ $latestTalents->count() }}</span>
                </div>
            </div>
            <div class="p-6">
                @forelse($latestTalents as $talent)
                    <div class="flex items-center p-3 mb-3 rounded-xl hover:bg-gray-50 transition-all duration-200">
                        <div class="mr-3">
                            @if($talent->avatar)
                                <img class="w-11 h-11 rounded-xl object-cover shadow-md" src="{{ asset('storage/' . $talent->avatar) }}"
                                     alt="{{ $talent->name }}">
                            @else
                                <div class="w-11 h-11 bg-gradient-to-br from-gray-400 to-gray-600 rounded-xl flex items-center justify-center shadow-md">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $talent->name }}</h4>
                            <p class="text-gray-500 text-sm">
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ $talent->pekerjaan ?? 'Posisi tidak ditentukan' }}
                            </p>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $talent->is_active_talent ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                <i class="fas fa-{{ $talent->is_active_talent ? 'check-circle' : 'pause-circle' }} mr-1"></i>
                                {{ $talent->is_active_talent ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-user-plus text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">Belum ada talent yang terdaftar.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Recruiters -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <h3 class="text-lg font-semibold text-white">Perekrut Terbaru</h3>
                    </div>
                    <span class="px-3 py-1 bg-white bg-opacity-20 text-black rounded-full text-sm font-medium">{{ $latestRecruiters->count() }}</span>
                </div>
            </div>
            <div class="p-6">
                @forelse($latestRecruiters as $recruiter)
                    <div class="flex items-center p-3 mb-3 rounded-xl hover:bg-gray-50 transition-all duration-200">
                        <div class="mr-3">
                            @if($recruiter->avatar)
                                <img class="w-11 h-11 rounded-xl object-cover shadow-md" src="{{ asset('storage/' . $recruiter->avatar) }}"
                                     alt="{{ $recruiter->name }}">
                            @else
                                <div class="w-11 h-11 bg-gradient-to-br from-gray-400 to-gray-600 rounded-xl flex items-center justify-center shadow-md">
                                    <i class="fas fa-building text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $recruiter->name }}</h4>
                            <p class="text-gray-500 text-sm">
                                <i class="fas fa-building mr-1"></i>
                                {{ $recruiter->company_name ?? $recruiter->pekerjaan ?? 'Perusahaan tidak ditentukan' }}
                            </p>
                        </div>
                        <div>
                            @php
                                // Check if user has recruiter role since we don't have a separate is_active field
                                $isActiveRecruiter = $recruiter->hasRole('recruiter');
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $isActiveRecruiter ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                <i class="fas fa-{{ $isActiveRecruiter ? 'check-circle' : 'pause-circle' }} mr-1"></i>
                                {{ $isActiveRecruiter ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-user-plus text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">Belum ada perekrut yang terdaftar.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
function showComingSoon(feature) {
    // Try to use SweetAlert if available, otherwise use regular alert
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Segera Hadir!',
            text: `Fitur ${feature} akan tersedia dalam pembaruan berikutnya.`,
            icon: 'info',
            confirmButtonText: 'Mengerti!',
            confirmButtonColor: '#7c3aed'
        });
    } else {
        alert(`Segera Hadir!\n\nFitur ${feature} akan tersedia dalam pembaruan berikutnya.`);
    }
}
</script>

<style>
/* Card hover effects */
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

/* Button animations */
.transition-all {
    transition: all 0.2s ease;
}
</style>

@if (session('success'))
    <script>
        // Show success message if available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#7c3aed'
            });
        } else {
            alert('Berhasil: {{ session('success') }}');
        }
    </script>
@endif

@if (session('error'))
    <script>
        // Show error message if available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Kesalahan!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#7c3aed'
            });
        } else {
            alert('Kesalahan: {{ session('error') }}');
        }
    </script>
@endif
@endsection
