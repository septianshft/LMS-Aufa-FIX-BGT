<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Notification Persistence Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">🧪 Notification Persistence Test</h1>
          <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Test Controls</h2>
            <div class="grid grid-cols-2 gap-4">
                <button onclick="createTestNotification()"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    📢 Create Test Notification
                </button>
                <button onclick="checkNotificationStatus()"
                        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    📊 Check Status
                </button>
                <button onclick="simulateDismissal()"
                        class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                    ❌ Simulate Dismissal
                </button>
                <button onclick="simulate23HourDismissal()"
                        class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                    ⏰ Simulate 23h Ago Dismissal
                </button>
                <button onclick="clearDismissal()"
                        class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                    🔄 Clear Dismissal (24h+ passed)
                </button>
                <button onclick="testLocalStorage()"
                        class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                    🧪 Test LocalStorage Logic
                </button>
                <button onclick="cleanupTestData()"
                        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        🗑️ Cleanup Test Data
                </button>
                <button onclick="refreshNotificationDisplay()"
                        class="bg-teal-500 text-white px-4 py-2 rounded hover:bg-teal-600">
                        🔄 Refresh Display
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Notification Test Area</h2>
            <div id="notification-area" class="min-h-32 border-2 border-dashed border-gray-300 rounded-lg p-4">
                <p class="text-gray-500 text-center">Notifications will appear here...</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Test Results</h2>
            <div id="test-results" class="bg-gray-50 rounded p-4 font-mono text-sm">
                <p>Click "Check Status" to see current state...</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">LocalStorage State</h2>
            <div id="localStorage-state" class="bg-gray-50 rounded p-4 font-mono text-sm">
                <p>Click "Test LocalStorage Logic" to see localStorage state...</p>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function apiCall(endpoint, data = {}) {
            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify(data)
                });
                return await response.json();
            } catch (error) {
                console.error('API call failed:', error);
                return { error: error.message };
            }
        }

        async function createTestNotification() {
            updateResults('Creating test notification...');
            const result = await apiCall('/test/create-notification');
            updateResults(`✅ Created test notification: ${JSON.stringify(result, null, 2)}`);
            renderTestNotification();
        }

        async function checkNotificationStatus() {
            updateResults('Checking notification status...');
            const result = await apiCall('/test/check-status');
            updateResults(`📊 Current status:\n${JSON.stringify(result, null, 2)}`);
        }

        async function simulateDismissal() {
            updateResults('Simulating dismissal...');
            const result = await apiCall('/dismiss-suggestion', { suggestion_type: 'smart_talent_suggestion' });
            updateResults(`✅ Simulated dismissal: ${JSON.stringify(result, null, 2)}`);

            // Also set localStorage
            localStorage.setItem('dismissed_smart_talent_suggestion', Date.now());
            updateResults('✅ Set localStorage dismissal flag');

            hideNotification();
        }

        async function clearDismissal() {
            updateResults('Clearing dismissal (simulating 24h passage)...');
            const result = await apiCall('/test/clear-dismissal');
            updateResults(`✅ Cleared dismissal: ${JSON.stringify(result, null, 2)}`);

            // Also clear localStorage
            localStorage.removeItem('dismissed_smart_talent_suggestion');
            updateResults('✅ Cleared localStorage dismissal flag');

            renderTestNotification();
        }

        function testLocalStorage() {
            const dismissalKey = 'dismissed_smart_talent_suggestion';
            const currentTime = Date.now();

            // Show current state
            let state = 'LocalStorage State:\n';
            state += `Current timestamp: ${currentTime}\n`;

            const dismissedTime = localStorage.getItem(dismissalKey);
            if (dismissedTime) {
                const hoursAgo = Math.floor((currentTime - parseInt(dismissedTime)) / (60 * 60 * 1000));
                state += `Dismissed timestamp: ${dismissedTime}\n`;
                state += `Hours since dismissal: ${hoursAgo}\n`;
                state += `Should hide notification: ${hoursAgo < 24}\n\n`;
            } else {
                state += `No dismissal timestamp found\n`;
                state += `Should show notification: true\n\n`;
            }

            // Test scenarios
            state += 'Test Scenarios:\n';

            // Scenario 1: Just dismissed
            const justDismissed = currentTime;
            state += `1. Just dismissed (${justDismissed}): Hide = true\n`;

            // Scenario 2: 12 hours ago
            const twelveHoursAgo = currentTime - (12 * 60 * 60 * 1000);
            state += `2. 12h ago (${twelveHoursAgo}): Hide = true\n`;

            // Scenario 3: 25 hours ago
            const twentyFiveHoursAgo = currentTime - (25 * 60 * 60 * 1000);
            state += `3. 25h ago (${twentyFiveHoursAgo}): Hide = false\n`;

            document.getElementById('localStorage-state').innerHTML = `<pre>${state}</pre>`;
        }

        async function cleanupTestData() {
            updateResults('Cleaning up test data...');
            const result = await apiCall('/test/cleanup');
            updateResults(`✅ Cleanup complete: ${JSON.stringify(result, null, 2)}`);

            // Clear localStorage
            localStorage.removeItem('dismissed_smart_talent_suggestion');
            localStorage.removeItem('dismissed_certificate_talent_suggestion');

            document.getElementById('notification-area').innerHTML = '<p class="text-gray-500 text-center">Notifications cleared...</p>';
        }

        function renderTestNotification() {
            const dismissedTime = localStorage.getItem('dismissed_smart_talent_suggestion');
            const shouldHide = dismissedTime && (Date.now() - parseInt(dismissedTime)) < 24 * 60 * 60 * 1000;

            if (shouldHide) {
                updateResults('❌ Notification hidden due to localStorage dismissal');
                return;
            }

            const notificationHTML = `
                <div class="smart-talent-notification bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 rounded-lg shadow-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-300 text-blue-600 rounded-full flex items-center justify-center text-xl font-bold">
                                ⭐
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-semibold mb-1">🧪 TEST: Notification Persistence</h3>
                            <p class="mb-3">This is a test notification to verify persistence behavior.</p>
                            <div class="bg-white bg-opacity-20 rounded-lg p-3 mb-3">
                                <p class="text-sm font-medium">Testing notification dismissal and reappearance after 24 hours</p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <button onclick="window.location.href='/profile/edit'"
                                       class="inline-flex items-center px-4 py-2 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors duration-200 text-center">
                                    🚀 Join Talent Platform
                                </button>
                                <button onclick="dismissTestNotification()" type="button"
                                        class="inline-flex items-center px-4 py-2 bg-transparent border border-white text-white font-semibold rounded-lg hover:bg-white hover:text-blue-600 transition-colors duration-200">
                                    ❌ Maybe Later
                                </button>
                            </div>
                        </div>
                        <button onclick="dismissTestNotification()" type="button"
                                class="flex-shrink-0 ml-3 text-white hover:text-gray-200 transition-colors duration-200 w-6 h-6 flex items-center justify-center">
                            ✕
                        </button>
                    </div>
                </div>
            `;

            document.getElementById('notification-area').innerHTML = notificationHTML;
            updateResults('✅ Test notification rendered');
        }        function dismissTestNotification() {
            simulateDismissal();
        }

        function simulate23HourDismissal() {
            const twentyThreeHoursAgo = Date.now() - (23 * 60 * 60 * 1000);
            localStorage.setItem('dismissed_smart_talent_suggestion', twentyThreeHoursAgo);
            updateResults(`⏰ Set dismissal to 23 hours ago (${twentyThreeHoursAgo})`);
            testLocalStorage();
            refreshNotificationDisplay();
        }

        function refreshNotificationDisplay() {
            updateResults('🔄 Refreshing notification display...');

            // Clear current notifications
            document.getElementById('notification-area').innerHTML = '<p class="text-gray-500 text-center">Checking dismissal state...</p>';

            // Re-check localStorage and display accordingly
            setTimeout(() => {
                createTestNotification();
            }, 500);
        }

        function hideNotification() {
            const notification = document.querySelector('.smart-talent-notification');
            if (notification) {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 300);
            }
        }

        function updateResults(message) {
            const now = new Date().toLocaleTimeString();
            const current = document.getElementById('test-results').innerHTML;
            document.getElementById('test-results').innerHTML = `[${now}] ${message}\n${current}`;
        }

        // Initial state check
        document.addEventListener('DOMContentLoaded', function() {
            testLocalStorage();
            updateResults('🧪 Test page loaded - ready for testing');
        });
    </script>
</body>
</html>
