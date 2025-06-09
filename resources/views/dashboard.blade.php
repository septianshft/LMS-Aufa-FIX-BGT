<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ Auth::user()->hasRole('admin') ? __('Admin Dashboard') : __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 flex flex-col gap-y-5">
                @role('admin')
                <!-- Admin Dashboard Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <!-- Courses Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-6 rounded-xl border border-blue-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-blue-500 rounded-xl">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M22 7.81V16.19C22 19.83 19.83 22 16.19 22H7.81C4.17 22 2 19.83 2 16.19V7.81C2 7.3 2.04 6.81 2.13 6.36C2.64 3.61 4.67 2.01 7.77 2H16.23C19.33 2.01 21.36 3.61 21.87 6.36C21.96 6.81 22 7.3 22 7.81Z" fill="white"/>
                                    <path d="M14.4391 12.7198L12.3591 11.5198C11.5891 11.0798 10.8491 11.0198 10.2691 11.3498C9.68914 11.6798 9.36914 12.3598 9.36914 13.2398V15.6398C9.36914 16.5198 9.68914 17.1998 10.2691 17.5298C10.5191 17.6698 10.7991 17.7398 11.0891 17.7398C11.4891 17.7398 11.9191 17.6098 12.3591 17.3598L14.4391 16.1598C15.2091 15.7198 15.6291 15.0998 15.6291 14.4298C15.6291 13.7598 15.1991 13.1698 14.4391 12.7198Z" fill="white"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-slate-600 text-sm font-medium">Courses</p>
                                <h3 class="text-2xl font-bold text-slate-900">{{$courses}}</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Transactions Card -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-100 p-6 rounded-xl border border-green-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-green-500 rounded-xl">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M19 10.2798V17.4298C18.97 20.2798 18.19 20.9998 15.22 20.9998H5.78003C2.76003 20.9998 2 20.2498 2 17.2698V10.2798C2 7.5798 2.63 6.7098 5 6.5698C5.24 6.5598 5.50003 6.5498 5.78003 6.5498H15.22C18.24 6.5498 19 7.2998 19 10.2798Z" fill="white"/>
                                    <path d="M19 11.8599H2V13.3599H19V11.8599Z" fill="white"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-slate-600 text-sm font-medium">Transactions</p>
                                <h3 class="text-2xl font-bold text-slate-900">{{$transactions}}</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Students Card -->
                    <div class="bg-gradient-to-br from-purple-50 to-violet-100 p-6 rounded-xl border border-purple-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-purple-500 rounded-xl">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M9 2C6.38 2 4.25 4.13 4.25 6.75C4.25 9.32 6.26 11.4 8.88 11.49C8.96 11.48 9.04 11.48 9.1 11.49C9.12 11.49 9.13 11.49 9.15 11.49C9.16 11.49 9.16 11.49 9.17 11.49C11.73 11.4 13.74 9.32 13.75 6.75C13.75 4.13 11.62 2 9 2Z" fill="white"/>
                                    <path d="M14.0809 14.1499C11.2909 12.2899 6.74094 12.2899 3.93094 14.1499C2.66094 14.9999 1.96094 16.1499 1.96094 17.3799C1.96094 18.6099 2.66094 19.7499 3.92094 20.5899C5.32094 21.5299 7.16094 21.9999 9.00094 21.9999C10.8409 21.9999 12.6809 21.5299 14.0809 20.5899C15.3409 19.7399 16.0409 18.5999 16.0409 17.3599C16.0309 16.1299 15.3409 14.9899 14.0809 14.1499Z" fill="white"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-slate-600 text-sm font-medium">Students</p>
                                <h3 class="text-2xl font-bold text-slate-900">{{$students}}</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Teachers Card -->
                    <div class="bg-gradient-to-br from-amber-50 to-orange-100 p-6 rounded-xl border border-amber-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-amber-500 rounded-xl">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="white"/>
                                    <path opacity="0.4" d="M21.0901 21.5C21.0901 21.78 20.8701 22 20.5901 22H3.41016C3.13016 22 2.91016 21.78 2.91016 21.5C2.91016 17.36 6.99015 14 12.0002 14C17.0102 14 21.0901 17.36 21.0901 21.5Z" fill="white"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-slate-600 text-sm font-medium">Teachers</p>
                                <h3 class="text-2xl font-bold text-slate-900">{{$teachers}}</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Categories Card -->
                    <div class="bg-gradient-to-br from-rose-50 to-pink-100 p-6 rounded-xl border border-rose-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-rose-500 rounded-xl">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M21.9897 6.01996C22.0097 6.25996 21.9897 6.50995 21.9297 6.75995L18.5597 20.2899C18.3197 21.2999 17.4198 21.9999 16.3798 21.9999H3.23974C1.72974 21.9999 0.659755 20.5199 1.09976 19.0699L5.30975 5.53992C5.59975 4.59992 6.46976 3.95996 7.44976 3.95996H19.7498C20.7097 3.95996 21.4898 4.52996 21.8198 5.32996C21.9198 5.53996 21.9697 5.77996 21.9897 6.01996Z" fill="white"/>
                                    <path d="M15.6992 12.75H7.69922C7.28922 12.75 6.94922 12.41 6.94922 12C6.94922 11.59 7.28922 11.25 7.69922 11.25H15.6992C16.1092 11.25 16.4492 11.59 16.4492 12C16.4492 12.41 16.1092 12.75 15.6992 12.75Z" fill="white"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-slate-600 text-sm font-medium">Categories</p>
                                <h3 class="text-2xl font-bold text-slate-900">{{$categories}}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                @endrole
                @role('trainer')
                <!-- Trainer Dashboard Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Courses Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-6 rounded-xl border border-blue-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-blue-500 rounded-xl">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M22 7.81V16.19C22 19.83 19.83 22 16.19 22H7.81C4.17 22 2 19.83 2 16.19V7.81C2 7.3 2.04 6.81 2.13 6.36C2.64 3.61 4.67 2.01 7.77 2H16.23C19.33 2.01 21.36 3.61 21.87 6.36C21.96 6.81 22 7.3 22 7.81Z" fill="white"/>
                                    <path d="M14.4391 12.7198L12.3591 11.5198C11.5891 11.0798 10.8491 11.0198 10.2691 11.3498C9.68914 11.6798 9.36914 12.3598 9.36914 13.2398V15.6398C9.36914 16.5198 9.68914 17.1998 10.2691 17.5298C10.5191 17.6698 10.7991 17.7398 11.0891 17.7398C11.4891 17.7398 11.9191 17.6098 12.3591 17.3598L14.4391 16.1598C15.2091 15.7198 15.6291 15.0998 15.6291 14.4298C15.6291 13.7598 15.1991 13.1698 14.4391 12.7198Z" fill="white"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-slate-600 text-sm font-medium">My Courses</p>
                                <h3 class="text-2xl font-bold text-slate-900">{{$courses}}</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Students Card -->
                    <div class="bg-gradient-to-br from-purple-50 to-violet-100 p-6 rounded-xl border border-purple-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-purple-500 rounded-xl">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M9 2C6.38 2 4.25 4.13 4.25 6.75C4.25 9.32 6.26 11.4 8.88 11.49C8.96 11.48 9.04 11.48 9.1 11.49C9.12 11.49 9.13 11.49 9.15 11.49C9.16 11.49 9.16 11.49 9.17 11.49C11.73 11.4 13.74 9.32 13.75 6.75C13.75 4.13 11.62 2 9 2Z" fill="white"/>
                                    <path d="M14.0809 14.1499C11.2909 12.2899 6.74094 12.2899 3.93094 14.1499C2.66094 14.9999 1.96094 16.1499 1.96094 17.3799C1.96094 18.6099 2.66094 19.7499 3.92094 20.5899C5.32094 21.5299 7.16094 21.9999 9.00094 21.9999C10.8409 21.9999 12.6809 21.5299 14.0809 20.5899C15.3409 19.7399 16.0409 18.5999 16.0409 17.3599C16.0309 16.1299 15.3409 14.9899 14.0809 14.1499Z" fill="white"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-slate-600 text-sm font-medium">My Students</p>
                                <h3 class="text-2xl font-bold text-slate-900">{{$students}}</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Action Card -->
                    <div class="bg-gradient-to-br from-emerald-50 to-green-100 p-6 rounded-xl border border-emerald-200 hover:shadow-lg transition-shadow flex items-center justify-center">
                        <a href="{{route('admin.courses.create')}}" class="flex items-center space-x-3 font-bold py-3 px-6 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors duration-200">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 5V19M5 12H19" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Create New Course</span>
                        </a>
                    </div>
                </div>
                @endrole
                @role('trainee')
                <!-- Trainee Dashboard Content -->
                <div class="text-center space-y-6">
                    <div class="max-w-md mx-auto">
                        <div class="p-4 bg-gradient-to-br from-indigo-50 to-blue-100 rounded-2xl mb-6">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-4">
                                <path opacity="0.4" d="M22 7.81V16.19C22 19.83 19.83 22 16.19 22H7.81C4.17 22 2 19.83 2 16.19V7.81C2 7.3 2.04 6.81 2.13 6.36C2.64 3.61 4.67 2.01 7.77 2H16.23C19.33 2.01 21.36 3.61 21.87 6.36C21.96 6.81 22 7.3 22 7.81Z" fill="#4F46E5"/>
                                <path d="M14.4391 12.7198L12.3591 11.5198C11.5891 11.0798 10.8491 11.0198 10.2691 11.3498C9.68914 11.6798 9.36914 12.3598 9.36914 13.2398V15.6398C9.36914 16.5198 9.68914 17.1998 10.2691 17.5298C10.5191 17.6698 10.7991 17.7398 11.0891 17.7398C11.4891 17.7398 11.9191 17.6098 12.3591 17.3598L14.4391 16.1598C15.2091 15.7198 15.6291 15.0998 15.6291 14.4298C15.6291 13.7598 15.1991 13.1698 14.4391 12.7198Z" fill="#4F46E5"/>
                            </svg>
                        </div>
                        <h3 class="text-3xl font-bold text-slate-900 mb-4">Upgrade Skills Today</h3>
                        <p class="text-slate-600 text-lg leading-relaxed mb-8">
                            Grow your career with experienced teachers in Alqowy Class. Discover courses that will take your skills to the next level.
                        </p>
                        <a href="{{route('front.index')}}" class="inline-flex items-center space-x-3 font-bold py-4 px-8 bg-indigo-700 text-white rounded-full hover:bg-indigo-800 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="white" stroke-width="2"/>
                                <path d="M9 12L13 16L21 8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Explore Catalog</span>
                        </a>
                    </div>
                </div>
                @endrole
            </div>
        </div>
    </div>
</x-app-layout>
