@extends('layout.template.mainTemplate')

@section('title', 'Create New Project')
@section('container')
<div class="container mx-auto px-4 py-8">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div id="success-notification" class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6 flex items-center justify-between shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-lg"></i>
                <div>
                    <h4 class="font-semibold">Success!</h4>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700 ml-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="{{ route('projects.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Create New Project</h1>
            <p class="text-gray-600 mt-2">Set up your project details and submit for admin approval</p>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Project Creation Form -->
    <form action="{{ route('projects.store') }}" method="POST" class="space-y-8">
        @csrf

        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
            <!-- Basic Information -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Basic Information</h2>

                <div class="grid grid-cols-1 gap-6">
                    <!-- Project Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Project Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="title"
                               name="title"
                               value="{{ old('title') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter a descriptive project title"
                               required>
                        <p class="text-sm text-gray-500 mt-1">This will be visible to all assigned talents</p>
                    </div>

                    <!-- Industry -->
                    <div>
                        <label for="industry" class="block text-sm font-medium text-gray-700 mb-2">
                            Industry
                        </label>
                        <select id="industry"
                                name="industry"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select an industry</option>
                            <option value="Technology" {{ old('industry') === 'Technology' ? 'selected' : '' }}>Technology</option>
                            <option value="Finance" {{ old('industry') === 'Finance' ? 'selected' : '' }}>Finance</option>
                            <option value="Healthcare" {{ old('industry') === 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                            <option value="Education" {{ old('industry') === 'Education' ? 'selected' : '' }}>Education</option>
                            <option value="E-commerce" {{ old('industry') === 'E-commerce' ? 'selected' : '' }}>E-commerce</option>
                            <option value="Manufacturing" {{ old('industry') === 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                            <option value="Marketing" {{ old('industry') === 'Marketing' ? 'selected' : '' }}>Marketing</option>
                            <option value="Other" {{ old('industry') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <!-- Project Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Project Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="5"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Provide detailed information about your project, goals, and expected outcomes"
                                  required>{{ old('description') }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">Be specific about project goals and deliverables</p>
                    </div>

                    <!-- General Requirements -->
                    <div>
                        <label for="general_requirements" class="block text-sm font-medium text-gray-700 mb-2">
                            General Requirements
                        </label>
                        <textarea id="general_requirements"
                                  name="general_requirements"
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="List general skills, experience, or qualifications needed for this project">{{ old('general_requirements') }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">These will apply to all talent assignments (you can specify individual requirements later)</p>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Project Timeline</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Start Date -->
                    <div>
                        <label for="expected_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Expected Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               id="expected_start_date"
                               name="expected_start_date"
                               value="{{ old('expected_start_date', date('Y-m-d')) }}"
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="expected_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Expected End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               id="expected_end_date"
                               name="expected_end_date"
                               value="{{ old('expected_end_date') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>
                </div>

                <div id="duration-display" class="mt-4 p-3 bg-blue-50 rounded-lg hidden">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        Project duration: <span id="duration-days">0</span> days
                    </p>
                </div>
            </div>

            <!-- Budget (Optional) -->
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Overall Budget (Optional)</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Minimum Budget -->
                    <div>
                        <label for="overall_budget_min" class="block text-sm font-medium text-gray-700 mb-2">
                            Minimum Budget (Rp)
                        </label>
                        <input type="number"
                               id="overall_budget_min"
                               name="overall_budget_min"
                               value="{{ old('overall_budget_min') }}"
                               min="0"
                               step="100000"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               placeholder="5000000">
                    </div>

                    <!-- Maximum Budget -->
                    <div>
                        <label for="overall_budget_max" class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Budget (Rp)
                        </label>
                        <input type="number"
                               id="overall_budget_max"
                               name="overall_budget_max"
                               value="{{ old('overall_budget_max') }}"
                               min="0"
                               step="100000"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               placeholder="15000000">
                    </div>
                </div>

                <p class="text-sm text-gray-500 mt-4">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Budget ranges help talents understand the project scope. You can set individual budgets for each talent assignment later.
                </p>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-between items-center">
            <a href="{{ route('projects.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg font-medium transition duration-200">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>

            <button type="submit"
                    id="submit-button"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition duration-200">
                <i id="submit-icon" class="fas fa-paper-plane mr-2"></i>
                <span id="submit-text">Submit for Approval</span>
            </button>
        </div>

        <!-- Help Text -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">
                <i class="fas fa-info-circle mr-2 text-blue-600"></i>What happens next?
            </h3>
            <div class="space-y-2 text-sm text-gray-600">
                <p>• Your project will be reviewed by our admin team for approval</p>
                <p>• Once approved, you can assign talents to specific roles within your project</p>
                <p>• Each talent can have customized requirements, budgets, and timelines</p>
                <p>• You'll receive notifications when talents accept or decline assignments</p>
                <p>• Track project progress through our comprehensive dashboard</p>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('expected_start_date');
    const endDateInput = document.getElementById('expected_end_date');
    const durationDisplay = document.getElementById('duration-display');
    const durationDays = document.getElementById('duration-days');
    const minBudgetInput = document.getElementById('overall_budget_min');
    const maxBudgetInput = document.getElementById('overall_budget_max');

    // Calculate and display project duration
    function updateDuration() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (startDate && endDate && endDate > startDate) {
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            durationDays.textContent = diffDays;
            durationDisplay.classList.remove('hidden');
        } else {
            durationDisplay.classList.add('hidden');
        }
    }

    // Update end date minimum when start date changes
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
        updateDuration();
    });

    endDateInput.addEventListener('change', updateDuration);

    // Budget validation
    function validateBudget() {
        const minValue = parseFloat(minBudgetInput.value) || 0;
        const maxValue = parseFloat(maxBudgetInput.value) || 0;

        if (minValue > 0 && maxValue > 0 && minValue > maxValue) {
            maxBudgetInput.setCustomValidity('Maximum budget must be greater than minimum budget');
        } else {
            maxBudgetInput.setCustomValidity('');
        }
    }

    minBudgetInput.addEventListener('input', validateBudget);
    maxBudgetInput.addEventListener('input', validateBudget);

    // Form submission handling
    const form = document.querySelector('form');
    const submitButton = document.getElementById('submit-button');
    const submitIcon = document.getElementById('submit-icon');
    const submitText = document.getElementById('submit-text');

    form.addEventListener('submit', function() {
        // Disable button and show loading state
        submitButton.disabled = true;
        submitButton.classList.remove('hover:bg-blue-700');
        submitButton.classList.add('bg-blue-500', 'cursor-not-allowed');

        // Change icon to spinner
        submitIcon.className = 'fas fa-spinner fa-spin mr-2';
        submitText.textContent = 'Submitting...';

        // Optional: Add a timeout fallback in case of network issues
        setTimeout(function() {
            if (submitButton.disabled) {
                submitButton.disabled = false;
                submitButton.classList.remove('bg-blue-500', 'cursor-not-allowed');
                submitButton.classList.add('hover:bg-blue-700');
                submitIcon.className = 'fas fa-paper-plane mr-2';
                submitText.textContent = 'Submit for Approval';
            }
        }, 10000); // Reset after 10 seconds if no response
    });

    // Initialize duration display if dates are already set
    updateDuration();
});
</script>
@endpush
