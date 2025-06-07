<x-guest-layout>
    <div class="min-h-screen flex">
        <!-- Left Side - Welcome Section -->
        <div class="hidden lg:flex lg:w-1/2 gradient-bg relative overflow-hidden">
            <div class="floating-shapes"></div>
            <div class="relative z-10 flex flex-col justify-center items-center text-white p-12 slide-in-left">
                <div class="max-w-md text-center">
                    <!-- Logo/Icon -->
                    <div class="mb-8">
                        <div class="w-20 h-20 mx-auto bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Welcome Content -->
                    <h1 class="text-4xl font-bold mb-4">Welcome Back!</h1>
                    <p class="text-lg text-white text-opacity-90 mb-8">
                        Sign in to access your account and continue your learning journey with our comprehensive training platform.
                    </p>

                    <!-- Features -->
                    <div class="space-y-4 text-left">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span>Access premium courses</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span>Track your progress</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span>Connect with professionals</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50 slide-in-right relative">
            <!-- Home Button -->
            <div class="absolute top-6 right-6">
                <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Home
                </a>
            </div>

            <div class="w-full max-w-md">
                <!-- Mobile Logo (visible on small screens) -->
                <div class="lg:hidden text-center mb-8">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-r from-purple-600 to-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                </div>

                <!-- Header -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Sign In</h2>
                    <p class="text-gray-600">Enter your credentials to access your account</p>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div class="space-y-2">
                        <x-input-label for="email" :value="__('Email Address')" class="text-sm font-semibold text-gray-700" />
                        <x-text-input id="email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200 bg-white"
                            type="email"
                            name="email"
                            :value="old('email')"
                            placeholder="your.email@example.com"
                            required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-gray-700" />
                        <div class="relative">
                            <x-text-input id="password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200 bg-white pr-12"
                                type="password"
                                name="password"
                                placeholder="Enter your password"
                                required autocomplete="current-password" />
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-gray-600 transition duration-200">
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center text-sm">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500">
                            <span class="ml-2 text-gray-600">{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-purple-600 hover:text-purple-800 hover:underline transition duration-200" href="{{ route('password.request') }}">
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200 transform hover:scale-[1.02]">
                            {{ __('Sign In') }}
                        </button>
                    </div>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-gray-50 text-gray-500">New to our platform?</span>
                        </div>
                    </div>

                    <!-- Register Link -->
                    <div class="text-center">
                        <a href="{{ route('register') }}" class="w-full inline-flex justify-center py-3 px-4 border border-purple-300 rounded-xl shadow-sm text-sm font-semibold text-purple-600 bg-white hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200 transform hover:scale-[1.02]">
                            {{ __('Create an Account') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L12 12m0 0l3.878 3.878M12 12l-3.878-3.878"></path>
                `;
            } else {
                passwordField.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        });
    </script>
</x-guest-layout>
