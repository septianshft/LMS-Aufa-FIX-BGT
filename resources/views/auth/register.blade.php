<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-lg">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Create Account</h2>
                <p class="text-gray-600">Join our training platform today</p>
            </div>

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Name -->
                <div class="space-y-2">
                    <x-input-label for="name" :value="__('Full Name')" class="text-sm font-medium text-gray-700" />
                    <x-text-input id="name"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                        type="text"
                        name="name"
                        :value="old('name')"
                        placeholder="Enter your full name"
                        required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Email Address -->
                <div class="space-y-2">
                    <x-input-label for="email" :value="__('Email Address')" class="text-sm font-medium text-gray-700" />
                    <x-text-input id="email"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                        type="email"
                        name="email"
                        :value="old('email')"
                        placeholder="your.email@example.com"
                        required autocomplete="email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Pekerjaan -->
                <div class="space-y-2">
                    <x-input-label for="pekerjaan" :value="__('Occupation')" class="text-sm font-medium text-gray-700" />
                    <x-text-input id="pekerjaan"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                        type="text"
                        name="pekerjaan"
                        :value="old('pekerjaan')"
                        placeholder="Your current occupation"
                        required />
                    <x-input-error :messages="$errors->get('pekerjaan')" class="mt-1" />
                </div>

                <!-- Avatar -->
                <div class="space-y-2">
                    <x-input-label for="avatar" :value="__('Profile Picture')" class="text-sm font-medium text-gray-700" />
                    <div class="flex items-center justify-center w-full">
                        <label for="avatar" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-200">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span></p>
                                <p class="text-xs text-gray-500">PNG, JPG or JPEG (MAX. 2MB)</p>
                            </div>
                            <input id="avatar" type="file" name="avatar" class="hidden" accept="image/*" required />
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('avatar')" class="mt-1" />
                </div>

                <!-- Role Selection -->
                <div class="space-y-2">
                    <x-input-label for="role" :value="__('I want to join as')" class="text-sm font-medium text-gray-700" />
                    <select id="role" name="role"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white"
                        required>
                        <option value="">{{ __('Select your role...') }}</option>
                        <option value="trainee" {{ old('role') == 'trainee' ? 'selected' : '' }}>
                            {{ __('🎓 Trainee - Learn from courses') }}
                        </option>
                        <option value="talent" {{ old('role') == 'talent' ? 'selected' : '' }}>
                            {{ __('⭐ Talent - Showcase your skills') }}
                        </option>
                        <option value="recruiter" {{ old('role') == 'recruiter' ? 'selected' : '' }}>
                            {{ __('👔 Recruiter - Find talented individuals') }}
                        </option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-1" />
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700" />
                    <div class="relative">
                        <x-text-input id="password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 pr-12"
                            type="password"
                            name="password"
                            placeholder="Create a strong password"
                            required autocomplete="new-password" />
                        <button type="button"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            onclick="togglePassword('password')">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        Password must be at least 8 characters long
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-sm font-medium text-gray-700" />
                    <div class="relative">
                        <x-text-input id="password_confirmation"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 pr-12"
                            type="password"
                            name="password_confirmation"
                            placeholder="Confirm your password"
                            required autocomplete="new-password" />
                        <button type="button"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            onclick="togglePassword('password_confirmation')">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <!-- Register Button -->
                <div class="space-y-4">
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 transform hover:scale-105">
                        {{ __('Create Account') }}
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition duration-200">
                            {{ __('Sign in here') }}
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for password toggle -->
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
        }

        // File upload preview
        document.getElementById('avatar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.className = 'w-16 h-16 rounded-full object-cover mx-auto mb-2';

                    const label = document.querySelector('label[for="avatar"]');
                    const existingPreview = label.querySelector('img');
                    if (existingPreview) {
                        existingPreview.remove();
                    }
                    label.insertBefore(preview, label.firstChild);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</x-guest-layout>
