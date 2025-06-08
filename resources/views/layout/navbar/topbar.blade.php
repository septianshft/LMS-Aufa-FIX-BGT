<div class="ol-header print-d-none d-flex align-items-center justify-content-between py-2 ps-3">
    <div class="header-title-menubar d-flex align-items-start flex-wrap mt-md-1">
        <div class="main-header-title d-flex align-items-start pb-sm-0 h-auto p-0">
            <button class="menu-toggler sidebar-plus" id="sidebarToggleTop">
                <span class="fi-rr-menu-burger"></span>
            </button>
            <h1 class="page-title ms-2 fs-18px d-flex flex-column row-gap-0">
                <span style="display: -webkit-box !important; -webkit-line-clamp: 1; -webkit-box-orient: vertical !important; overflow: hidden !important; text-overflow: ellipsis !important; white-space: normal !important;">
                    {{ config('app.name', 'Web Pelatihan') }}
                </span>
                <p class="text-12px fw-400 d-none d-lg-none d-xl-inline-block mt-1">Admin Panel</p>
            </h1>
        </div>
        <a href="{{ route('home') }}" target="_blank" class="btn btn-sm p-0 ms-4 ms-md-2 text-14px text-muted">
            <span>View site</span>
            <i class="fi-rr-arrow-up-right-from-square text-12px text-muted"></i>
        </a>
    </div>
    <div class="header-content-right d-flex align-items-center justify-content-end">
        <div class="header-dropdown-md">
            <button class="header-dropdown-toggle-md" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-profile-sm">
                    @if (Auth()->user()->gambar == null)
                        <img src="/asset/icons/profile-women.svg" alt="">
                    @else
                        <img src="{{ asset('storage/user-images/' . Auth()->user()->gambar) }}" alt="">
                    @endif
                </div>
            </button>
            <div class="header-dropdown-menu-md p-3">
                <div class="d-flex column-gap-2 mb-12px pb-12px ol-border-bottom-2">
                    <div class="user-profile-sm">
                        @if (Auth()->user()->gambar == null)
                            <img src="/asset/icons/profile-women.svg" alt="">
                        @else
                            <img src="{{ asset('storage/user-images/' . Auth()->user()->gambar) }}" alt="">
                        @endif
                    </div>
                    <div>
                        <h6 class="title fs-12px mb-2px">{{ Auth()->user()->name }}</h6>
                        <p class="sub-title fs-12px">
                            @if (Auth()->user()->roles_id == 1)
                                Admin
                            @elseif (Auth()->user()->roles_id == 2)
                                Pengajar
                            @elseif (Auth()->user()->roles_id == 3)
                                Siswa
                            @elseif (Auth()->user()->hasRole('talent_admin'))
                                Talent Admin
                            @elseif (Auth()->user()->hasRole('talent'))
                                Talent
                            @elseif (Auth()->user()->hasRole('recruiter'))
                                Recruiter
                            @endif
                        </p>
                    </div>
                </div>
                <ul class="mb-12px pb-12px ol-border-bottom-2">
                    @if (Auth()->user()->roles_id == 1 || Auth()->user()->roles_id == 2)
                        <li class="dropdown-list-1"><a class="dropdown-item-1" href="{{ route('viewProfilePengajar', ['token' => encrypt(Auth()->User()->id)]) }}">My Profile</a></li>
                    @elseif (Auth()->user()->roles_id == 3)
                        <li class="dropdown-list-1"><a class="dropdown-item-1" href="{{ route('viewProfileSiswa', ['token' => encrypt(Auth()->User()->id)]) }}">My Profile</a></li>
                    @elseif (Auth()->user()->hasRole(['talent_admin', 'talent', 'recruiter']))
                        <li class="dropdown-list-1"><a class="dropdown-item-1" href="{{ route('profile.edit') }}">My Profile</a></li>
                    @endif
                </ul>
                <ul>
                    <li class="dropdown-list-1"><a class="dropdown-item-1" href="#" aria-expanded="false" data-toggle="modal" data-target="#logoutModal">Sign Out</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
