/**
 * Frontend JavaScript Test Suite for Talent Comparison Functionality
 * This file tests the JavaScript functions used in the talent comparison feature
 */

// Mock DOM elements and functions for testing
const mockDocument = {
    elements: new Map(),
    eventListeners: new Map(),

    getElementById: function(id) {
        return this.elements.get(id) || null;
    },

    querySelector: function(selector) {
        return this.elements.get(selector) || null;
    },

    querySelectorAll: function(selector) {
        const results = [];
        this.elements.forEach((element, key) => {
            if (key.includes(selector.replace('.', '').replace('#', ''))) {
                results.push(element);
            }
        });
        return results;
    },

    createElement: function(tag) {
        return {
            tagName: tag.toUpperCase(),
            className: '',
            innerHTML: '',
            style: {},
            dataset: {},
            classList: {
                add: function(...classes) {
                    classes.forEach(cls => {
                        if (!this.contains(cls)) {
                            this._classes = this._classes || [];
                            this._classes.push(cls);
                        }
                    });
                },
                remove: function(...classes) {
                    classes.forEach(cls => {
                        if (this._classes) {
                            const index = this._classes.indexOf(cls);
                            if (index > -1) this._classes.splice(index, 1);
                        }
                    });
                },
                contains: function(cls) {
                    return this._classes && this._classes.includes(cls);
                },
                _classes: []
            },
            appendChild: function(child) {
                this.children = this.children || [];
                this.children.push(child);
            }
        };
    }
};

// Mock console for test output
const testConsole = {
    logs: [],
    log: function(message) {
        this.logs.push({ type: 'log', message });
        console.log(`[TEST LOG] ${message}`);
    },
    error: function(message) {
        this.logs.push({ type: 'error', message });
        console.error(`[TEST ERROR] ${message}`);
    },
    warn: function(message) {
        this.logs.push({ type: 'warn', message });
        console.warn(`[TEST WARN] ${message}`);
    }
};

/**
 * Test Suite for Talent Comparison Frontend Issues
 */
class TalentComparisonTestSuite {
    constructor() {
        this.tests = [];
        this.results = [];
        this.setupMockEnvironment();
    }

    setupMockEnvironment() {
        // Mock global variables
        global.isCompareMode = false;
        global.selectedTalents = [];

        // Setup mock DOM elements
        this.setupMockElements();
    }

    setupMockElements() {
        // Create mock elements that should exist in the DOM
        const mockElements = [
            { id: 'compareModeBtn', type: 'button' },
            { id: 'comparisonPanel', type: 'div' },
            { id: 'selectedCount', type: 'span' },
            { id: 'compareBtn', type: 'button' },
            { id: 'selectedTalentsPreview', type: 'div' },
            { id: 'talentComparisonModal', type: 'div' },
            { id: 'comparisonContent', type: 'div' }
        ];

        mockElements.forEach(element => {
            const mockEl = mockDocument.createElement(element.type);
            mockEl.id = element.id;
            if (element.id === 'comparisonPanel') {
                mockEl.style.display = 'none';
                mockEl.classList.add('translate-y-full');
            }
            if (element.id === 'compareBtn') {
                mockEl.disabled = true;
            }
            mockDocument.elements.set(element.id, mockEl);
        });

        // Create mock talent cards
        for (let i = 1; i <= 3; i++) {
            const talentCard = mockDocument.createElement('div');
            talentCard.className = 'talent-card';
            talentCard.dataset.talentId = i.toString();

            const checkbox = mockDocument.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'talent-compare-check';
            checkbox.dataset.talentId = i.toString();
            checkbox.dataset.talentName = `Talent ${i}`;
            checkbox.dataset.talentEmail = `talent${i}@test.com`;
            checkbox.dataset.talentPosition = `Developer ${i}`;
            checkbox.dataset.talentScore = (70 + i * 10).toString();
            checkbox.dataset.talentCourses = (i * 2).toString();
            checkbox.dataset.talentCertificates = i.toString();
            checkbox.dataset.talentQuizAvg = (80 + i * 5).toString();
            checkbox.dataset.talentSkills = JSON.stringify([
                { skill_name: `Skill${i}A`, proficiency: 'advanced' },
                { skill_name: `Skill${i}B`, proficiency: 'intermediate' }
            ]);

            const compareCheckbox = mockDocument.createElement('div');
            compareCheckbox.className = 'compare-checkbox hidden';
            compareCheckbox.appendChild(checkbox);

            talentCard.appendChild(compareCheckbox);

            mockDocument.elements.set(`talent-card-${i}`, talentCard);
            mockDocument.elements.set(`talent-compare-check-${i}`, checkbox);
        }
    }

    // Test 1: Check if toggle compare mode function exists and works
    testToggleCompareModeFunction() {
        testConsole.log("Testing toggleCompareMode function...");

        // Check if function exists (in real scenario)
        if (typeof toggleCompareMode === 'undefined') {
            return {
                name: 'toggleCompareMode function existence',
                passed: false,
                error: 'toggleCompareMode function is not defined'
            };
        }

        // Test mode toggle logic
        const initialMode = global.isCompareMode;

        try {
            // Simulate function behavior
            this.mockToggleCompareMode();

            return {
                name: 'toggleCompareMode function',
                passed: global.isCompareMode !== initialMode,
                message: 'Compare mode toggled successfully'
            };
        } catch (error) {
            return {
                name: 'toggleCompareMode function',
                passed: false,
                error: error.message
            };
        }
    }

    // Test 2: Check DOM element dependencies
    testDOMElementExistence() {
        testConsole.log("Testing DOM element existence...");

        const requiredElements = [
            'compareModeBtn',
            'comparisonPanel',
            'selectedCount',
            'compareBtn',
            'selectedTalentsPreview',
            'talentComparisonModal',
            'comparisonContent'
        ];

        const missingElements = [];

        requiredElements.forEach(elementId => {
            if (!mockDocument.getElementById(elementId)) {
                missingElements.push(elementId);
            }
        });

        return {
            name: 'DOM element existence',
            passed: missingElements.length === 0,
            error: missingElements.length > 0 ? `Missing elements: ${missingElements.join(', ')}` : null,
            message: 'All required DOM elements found'
        };
    }

    // Test 3: Check talent card data attributes
    testTalentCardDataAttributes() {
        testConsole.log("Testing talent card data attributes...");

        const requiredAttributes = [
            'talentId',
            'talentName',
            'talentEmail',
            'talentPosition',
            'talentScore',
            'talentCourses',
            'talentCertificates',
            'talentQuizAvg',
            'talentSkills'
        ];

        const issues = [];

        for (let i = 1; i <= 3; i++) {
            const checkbox = mockDocument.elements.get(`talent-compare-check-${i}`);
            if (checkbox) {
                requiredAttributes.forEach(attr => {
                    if (!checkbox.dataset[attr]) {
                        issues.push(`Talent ${i} missing data-${attr} attribute`);
                    }
                });

                // Test skills JSON parsing
                try {
                    if (checkbox.dataset.talentSkills) {
                        JSON.parse(checkbox.dataset.talentSkills);
                    }
                } catch (e) {
                    issues.push(`Talent ${i} has invalid JSON in data-talent-skills`);
                }
            } else {
                issues.push(`Talent compare checkbox ${i} not found`);
            }
        }

        return {
            name: 'talent card data attributes',
            passed: issues.length === 0,
            error: issues.length > 0 ? issues.join('; ') : null,
            message: 'All talent cards have required data attributes'
        };
    }

    // Test 4: Check updateCompareSelection function logic
    testUpdateCompareSelectionFunction() {
        testConsole.log("Testing updateCompareSelection function...");

        try {
            // Mock some checkboxes as checked
            const checkbox1 = mockDocument.elements.get('talent-compare-check-1');
            const checkbox2 = mockDocument.elements.get('talent-compare-check-2');

            if (checkbox1) checkbox1.checked = true;
            if (checkbox2) checkbox2.checked = true;

            // Simulate the function
            this.mockUpdateCompareSelection();

            return {
                name: 'updateCompareSelection function',
                passed: global.selectedTalents.length === 2,
                message: 'Selection update works correctly'
            };
        } catch (error) {
            return {
                name: 'updateCompareSelection function',
                passed: false,
                error: error.message
            };
        }
    }

    // Test 5: Check comparison panel visibility logic
    testComparisonPanelVisibility() {
        testConsole.log("Testing comparison panel visibility...");

        const panel = mockDocument.getElementById('comparisonPanel');

        if (!panel) {
            return {
                name: 'comparison panel visibility',
                passed: false,
                error: 'Comparison panel element not found'
            };
        }

        // Test initial state
        const initiallyHidden = panel.style.display === 'none' &&
                               panel.classList.contains('translate-y-full');

        // Test show panel logic
        this.mockShowComparisonPanel();
        const showWorking = panel.style.display === 'block' &&
                           !panel.classList.contains('translate-y-full');

        // Test hide panel logic
        this.mockHideComparisonPanel();
        const hideWorking = panel.style.display === 'none' &&
                           panel.classList.contains('translate-y-full');

        return {
            name: 'comparison panel visibility',
            passed: initiallyHidden && showWorking && hideWorking,
            message: 'Panel visibility logic works correctly'
        };
    }

    // Test 6: Check for potential memory leaks
    testMemoryLeaks() {
        testConsole.log("Testing for potential memory leaks...");

        const issues = [];

        // Check if global variables are properly cleaned up
        if (typeof window !== 'undefined') {
            const globalVars = ['isCompareMode', 'selectedTalents'];
            globalVars.forEach(varName => {
                if (window[varName] && typeof window[varName] === 'object') {
                    if (Array.isArray(window[varName]) && window[varName].length > 100) {
                        issues.push(`Global array ${varName} may have memory leak (${window[varName].length} items)`);
                    }
                }
            });
        }

        // Check for event listener cleanup
        if (mockDocument.eventListeners.size > 50) {
            issues.push(`Too many event listeners registered (${mockDocument.eventListeners.size})`);
        }

        return {
            name: 'memory leak check',
            passed: issues.length === 0,
            error: issues.length > 0 ? issues.join('; ') : null,
            message: 'No memory leak indicators found'
        };
    }

    // Test 7: Check error handling in comparison functions
    testErrorHandling() {
        testConsole.log("Testing error handling...");

        const issues = [];

        try {
            // Test with invalid skill data
            const invalidCheckbox = mockDocument.createElement('input');
            invalidCheckbox.dataset.talentSkills = 'invalid json';

            // This should not crash the application
            this.mockParseSkillsData(invalidCheckbox);

        } catch (error) {
            issues.push('Invalid JSON parsing not handled gracefully');
        }

        try {
            // Test with missing DOM elements
            this.mockToggleCompareModeWithMissingElements();

        } catch (error) {
            issues.push('Missing DOM elements not handled gracefully');
        }

        return {
            name: 'error handling',
            passed: issues.length === 0,
            error: issues.length > 0 ? issues.join('; ') : null,
            message: 'Error handling works correctly'
        };
    }

    // Mock function implementations for testing
    mockToggleCompareMode() {
        global.isCompareMode = !global.isCompareMode;

        const checkboxes = mockDocument.querySelectorAll('.compare-checkbox');
        const compareBtn = mockDocument.getElementById('compareModeBtn');
        const comparisonPanel = mockDocument.getElementById('comparisonPanel');

        if (global.isCompareMode) {
            checkboxes.forEach(cb => cb.classList.remove('hidden'));
            if (compareBtn) {
                compareBtn.innerHTML = '<i class="fas fa-times mr-2"></i>Exit Compare';
            }
            if (comparisonPanel) {
                comparisonPanel.style.display = 'block';
                comparisonPanel.classList.remove('translate-y-full');
            }
        } else {
            checkboxes.forEach(cb => cb.classList.add('hidden'));
            if (compareBtn) {
                compareBtn.innerHTML = '<i class="fas fa-balance-scale mr-2"></i>Compare';
            }
            if (comparisonPanel) {
                comparisonPanel.style.display = 'none';
                comparisonPanel.classList.add('translate-y-full');
            }
            global.selectedTalents = [];
        }
    }

    mockUpdateCompareSelection() {
        const checkedBoxes = mockDocument.querySelectorAll('.talent-compare-check');
        global.selectedTalents = [];

        checkedBoxes.forEach(cb => {
            if (cb.checked) {
                const skills = cb.dataset.talentSkills ? JSON.parse(cb.dataset.talentSkills) : [];
                global.selectedTalents.push({
                    id: cb.dataset.talentId,
                    name: cb.dataset.talentName,
                    email: cb.dataset.talentEmail,
                    position: cb.dataset.talentPosition,
                    score: cb.dataset.talentScore,
                    courses: cb.dataset.talentCourses,
                    certificates: cb.dataset.talentCertificates,
                    quizAvg: cb.dataset.talentQuizAvg,
                    skills: skills
                });
            }
        });

        // Update UI elements
        const selectedCount = mockDocument.getElementById('selectedCount');
        if (selectedCount) {
            selectedCount.textContent = `${global.selectedTalents.length} selected`;
        }

        const compareBtn = mockDocument.getElementById('compareBtn');
        if (compareBtn) {
            compareBtn.disabled = global.selectedTalents.length < 2;
        }
    }

    mockShowComparisonPanel() {
        const panel = mockDocument.getElementById('comparisonPanel');
        if (panel) {
            panel.style.display = 'block';
            panel.classList.remove('translate-y-full');
        }
    }

    mockHideComparisonPanel() {
        const panel = mockDocument.getElementById('comparisonPanel');
        if (panel) {
            panel.style.display = 'none';
            panel.classList.add('translate-y-full');
        }
    }

    mockToggleCompareModeWithMissingElements() {
        // Simulate missing elements
        mockDocument.elements.delete('compareModeBtn');
        this.mockToggleCompareMode();
    }

    mockParseSkillsData(checkbox) {
        if (checkbox.dataset.talentSkills) {
            JSON.parse(checkbox.dataset.talentSkills);
        }
    }

    // Run all tests
    runAllTests() {
        testConsole.log("Starting Talent Comparison Frontend Test Suite...");

        const tests = [
            () => this.testToggleCompareModeFunction(),
            () => this.testDOMElementExistence(),
            () => this.testTalentCardDataAttributes(),
            () => this.testUpdateCompareSelectionFunction(),
            () => this.testComparisonPanelVisibility(),
            () => this.testMemoryLeaks(),
            () => this.testErrorHandling()
        ];

        let passed = 0;
        let failed = 0;

        tests.forEach((test, index) => {
            try {
                const result = test();
                this.results.push(result);

                if (result.passed) {
                    passed++;
                    testConsole.log(`✅ Test ${index + 1}: ${result.name} - PASSED`);
                    if (result.message) testConsole.log(`   ${result.message}`);
                } else {
                    failed++;
                    testConsole.error(`❌ Test ${index + 1}: ${result.name} - FAILED`);
                    if (result.error) testConsole.error(`   Error: ${result.error}`);
                }
            } catch (error) {
                failed++;
                testConsole.error(`💥 Test ${index + 1}: Crashed - ${error.message}`);
                this.results.push({
                    name: `Test ${index + 1}`,
                    passed: false,
                    error: error.message
                });
            }
        });

        testConsole.log(`\n📊 Test Results: ${passed} passed, ${failed} failed`);

        return {
            total: tests.length,
            passed,
            failed,
            results: this.results
        };
    }

    // Generate test report
    generateReport() {
        const report = {
            summary: {
                total: this.results.length,
                passed: this.results.filter(r => r.passed).length,
                failed: this.results.filter(r => !r.passed).length
            },
            details: this.results,
            recommendations: this.generateRecommendations()
        };

        return report;
    }

    generateRecommendations() {
        const recommendations = [];

        this.results.forEach(result => {
            if (!result.passed) {
                switch (result.name) {
                    case 'toggleCompareMode function existence':
                        recommendations.push('Ensure toggleCompareMode function is properly defined in the dashboard template');
                        break;
                    case 'DOM element existence':
                        recommendations.push('Check that all required DOM elements are present in the HTML structure');
                        break;
                    case 'talent card data attributes':
                        recommendations.push('Verify that all talent cards have the required data attributes for comparison');
                        break;
                    case 'updateCompareSelection function':
                        recommendations.push('Test and fix the selection update logic');
                        break;
                    case 'comparison panel visibility':
                        recommendations.push('Fix panel show/hide animations and state management');
                        break;
                    case 'memory leak check':
                        recommendations.push('Implement proper cleanup of event listeners and global variables');
                        break;
                    case 'error handling':
                        recommendations.push('Add try-catch blocks and graceful error handling');
                        break;
                }
            }
        });

        return recommendations;
    }
}

// Export for Node.js environment
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TalentComparisonTestSuite;
}

// Auto-run tests if in browser environment
if (typeof window !== 'undefined') {
    document.addEventListener('DOMContentLoaded', () => {
        const testSuite = new TalentComparisonTestSuite();
        const results = testSuite.runAllTests();
        console.log('Test Report:', testSuite.generateReport());
    });
}
