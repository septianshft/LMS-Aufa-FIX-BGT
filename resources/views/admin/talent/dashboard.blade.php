@extends('layout.template.mainTemplate')

@section('title', 'Talent Dashboard')
@section('container')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 p-6">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Welcome Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Welcome back, {{ $user->name }}! 👋</h1>
                    <p class="text-blue-100 text-lg">Ready to explore new opportunities and showcase your talent?</p>
                </div>
                <div class="hidden md:block">
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center">
                        <i class="fas fa-star text-4xl text-yellow-300 mb-2"></i>
                        <div class="text-sm font-medium">Talent Status</div>
                        <div class="text-xs opacity-90">Active</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Profile Completeness --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-user-circle text-blue-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-800">85%</div>
                        <div class="text-sm text-gray-500">Complete</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700 mb-2">Profile Status</div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 85%"></div>
                </div>
            </div>

            {{-- Active Opportunities --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-briefcase text-green-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-800">7</div>
                        <div class="text-sm text-gray-500">Available</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700">Job Opportunities</div>
                <div class="text-xs text-green-600 font-medium">+3 new this week</div>
            </div>

            {{-- Applications Sent --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-paper-plane text-purple-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-800">12</div>
                        <div class="text-sm text-gray-500">Sent</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700">Applications</div>
                <div class="text-xs text-purple-600 font-medium">3 pending review</div>
            </div>

            {{-- Messages --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <i class="fas fa-envelope text-orange-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-800">5</div>
                        <div class="text-sm text-gray-500">New</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700">Messages</div>
                <div class="text-xs text-orange-600 font-medium">2 from recruiters</div>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Latest Opportunities --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-800">🚀 Latest Opportunities</h2>
                            <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</a>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        {{-- Job Opportunity 1 --}}
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition-all cursor-pointer">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <h3 class="font-semibold text-gray-800">Senior Frontend Developer</h3>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">New</span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2">Tech Innovations Inc. • Remote</p>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span class="flex items-center"><i class="fas fa-dollar-sign mr-1"></i> $80k - $120k</span>
                                        <span class="flex items-center"><i class="fas fa-clock mr-1"></i> Full-time</span>
                                        <span class="flex items-center"><i class="fas fa-calendar mr-1"></i> Posted 2 days ago</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    Apply Now
                                </button>
                            </div>
                        </div>

                        {{-- Job Opportunity 2 --}}
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition-all cursor-pointer">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <h3 class="font-semibold text-gray-800">UX/UI Designer</h3>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">Trending</span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2">Creative Solutions Ltd. • Jakarta</p>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span class="flex items-center"><i class="fas fa-dollar-sign mr-1"></i> $60k - $90k</span>
                                        <span class="flex items-center"><i class="fas fa-clock mr-1"></i> Full-time</span>
                                        <span class="flex items-center"><i class="fas fa-calendar mr-1"></i> Posted 1 week ago</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    Apply Now
                                </button>
                            </div>
                        </div>

                        {{-- Job Opportunity 3 --}}
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition-all cursor-pointer">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <h3 class="font-semibold text-gray-800">Data Scientist</h3>
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full font-medium">Hot</span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2">Data Analytics Corp. • Hybrid</p>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span class="flex items-center"><i class="fas fa-dollar-sign mr-1"></i> $90k - $140k</span>
                                        <span class="flex items-center"><i class="fas fa-clock mr-1"></i> Full-time</span>
                                        <span class="flex items-center"><i class="fas fa-calendar mr-1"></i> Posted 3 days ago</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    Apply Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-gray-800">📋 Recent Activity</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">Application submitted to Tech Innovations Inc.</p>
                                <p class="text-xs text-gray-500">2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="bg-blue-100 p-2 rounded-full">
                                <i class="fas fa-eye text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">Your profile was viewed by Creative Solutions Ltd.</p>
                                <p class="text-xs text-gray-500">1 day ago</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="bg-purple-100 p-2 rounded-full">
                                <i class="fas fa-star text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">New skill badge earned: React Advanced</p>
                                <p class="text-xs text-gray-500">3 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Profile Quick Actions --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-800">⚡ Quick Actions</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-blue-50 transition-colors group">
                            <div class="bg-blue-100 p-2 rounded-lg group-hover:bg-blue-200 transition-colors">
                                <i class="fas fa-user-edit text-blue-600"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-800">Edit Profile</div>
                                <div class="text-xs text-gray-500">Update your information</div>
                            </div>
                        </a>
                        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-green-50 transition-colors group">
                            <div class="bg-green-100 p-2 rounded-lg group-hover:bg-green-200 transition-colors">
                                <i class="fas fa-upload text-green-600"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-800">Upload Resume</div>
                                <div class="text-xs text-gray-500">Keep it updated</div>
                            </div>
                        </a>
                        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-purple-50 transition-colors group">
                            <div class="bg-purple-100 p-2 rounded-lg group-hover:bg-purple-200 transition-colors">
                                <i class="fas fa-cogs text-purple-600"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-800">Skill Assessment</div>
                                <div class="text-xs text-gray-500">Test your abilities</div>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- Skill Progress --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-800">🎯 Skill Progress</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">JavaScript</span>
                                <span class="text-sm text-gray-500">90%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 90%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">React</span>
                                <span class="text-sm text-gray-500">85%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Node.js</span>
                                <span class="text-sm text-gray-500">75%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: 75%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Messages --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-800">💬 Messages</h2>
                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">5</span>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center space-x-3">
                            <img src="/asset/icons/profile-women.svg" alt="Recruiter" class="w-8 h-8 rounded-full">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800">Sarah Chen</div>
                                <div class="text-xs text-gray-500">Interested in your profile...</div>
                            </div>
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <img src="/asset/icons/profile-women.svg" alt="Recruiter" class="w-8 h-8 rounded-full">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800">Tech Innovations</div>
                                <div class="text-xs text-gray-500">Interview invitation</div>
                            </div>
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
