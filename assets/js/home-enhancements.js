/**
 * Home Page Enhancements
 * Interactive features and improvements for the graph visualization
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        setupHeroAnimation();
        setupSearch();
        setupControls();
        setupLegendInteraction();
        setupKeyboardNavigation();
        setupStatisticsUpdater();
    }

    /**
     * Hero Animation - Fade out after delay
     */
    function setupHeroAnimation() {
        const hero = document.getElementById('graph-hero');
        if (!hero) return;

        setTimeout(() => {
            hero.classList.add('fade-out');
            setTimeout(() => {
                hero.style.display = 'none';
            }, 500);
        }, 3000);
    }

    /**
     * Search Functionality
     */
    function setupSearch() {
        const searchInput = document.getElementById('graph-search');
        if (!searchInput) return;

        let searchTimeout;
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const searchTerm = e.target.value.toLowerCase().trim();

            searchTimeout = setTimeout(() => {
                if (window.graphInstance) {
                    performSearch(searchTerm);
                } else {
                }
            }, 300);
        });

        // Clear search on escape
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                searchInput.value = '';
                performSearch('');
            }
        });
    }

    /**
     * Perform search in graph
     */
    function performSearch(searchTerm) {
        if (!searchTerm) {
            // Reset all nodes to normal state
            document.querySelectorAll('.graph-node').forEach(node => {
                node.classList.remove('highlighted', 'dimmed');
            });
            return;
        }

        // Get all nodes
        const nodes = document.querySelectorAll('.graph-node');
        let matchFound = false;

        nodes.forEach(node => {
            const title = node.getAttribute('data-title') || '';
            const excerpt = node.getAttribute('data-excerpt') || '';
            const categories = node.getAttribute('data-categories') || '';
            
            const matches = 
                title.toLowerCase().includes(searchTerm) ||
                excerpt.toLowerCase().includes(searchTerm) ||
                categories.toLowerCase().includes(searchTerm);

            if (matches) {
                node.classList.add('highlighted');
                node.classList.remove('dimmed');
                matchFound = true;
            } else {
                node.classList.remove('highlighted');
                node.classList.add('dimmed');
            }
        });

        // Call graph instance method if available
        if (window.graphInstance && typeof window.graphInstance.search === 'function') {
            window.graphInstance.search(searchTerm);
        }
    }

    /**
     * Setup Control Buttons
     */
    function setupControls() {
        // Zoom In
        const zoomInBtn = document.getElementById('btn-zoom-in');
        if (zoomInBtn) {
            zoomInBtn.addEventListener('click', () => {
                if (window.graphInstance && typeof window.graphInstance.zoomIn === 'function') {
                    window.graphInstance.zoomIn();
                } else {
                }
            });
        }

        // Zoom Out
        const zoomOutBtn = document.getElementById('btn-zoom-out');
        if (zoomOutBtn) {
            zoomOutBtn.addEventListener('click', () => {
                if (window.graphInstance && typeof window.graphInstance.zoomOut === 'function') {
                    window.graphInstance.zoomOut();
                } else {
                }
            });
        }

        // Reset View
        const resetBtn = document.getElementById('btn-reset-view');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                if (window.graphInstance && typeof window.graphInstance.resetView === 'function') {
                    window.graphInstance.resetView();
                } else {
                }
                // Also reset search
                const searchInput = document.getElementById('graph-search');
                if (searchInput) {
                    searchInput.value = '';
                    performSearch('');
                }
            });
        }

        // Fullscreen
        const fullscreenBtn = document.getElementById('btn-fullscreen');
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', toggleFullscreen);

            // Update button on fullscreen change
            document.addEventListener('fullscreenchange', updateFullscreenButton);
        }
    }

    /**
     * Toggle Fullscreen Mode
     */
    function toggleFullscreen() {
        const container = document.querySelector('.graph-homepage-container');
        if (!container) return;

        if (!document.fullscreenElement) {
            container.requestFullscreen().catch(err => {
                console.error('Error enabling fullscreen:', err);
            });
        } else {
            document.exitFullscreen();
        }
    }

    /**
     * Update Fullscreen Button Icon
     */
    function updateFullscreenButton() {
        const btn = document.getElementById('btn-fullscreen');
        if (!btn) return;

        const span = btn.querySelector('span');
        if (document.fullscreenElement) {
            span.textContent = '⛶';
            btn.setAttribute('title', 'Quitter le plein écran');
        } else {
            span.textContent = '⛶';
            btn.setAttribute('title', 'Plein écran');
        }
    }

    /**
     * Setup Legend Interaction
     */
    function setupLegendInteraction() {
        const legendItems = document.querySelectorAll('.legend-item');
        
        legendItems.forEach(item => {
            item.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                
                // Toggle active state
                this.classList.toggle('active');
                
                // Filter nodes by category
                if (window.graphInstance && typeof window.graphInstance.filterByCategory === 'function') {
                    window.graphInstance.filterByCategory(category);
                } else {
                    // Manual filtering
                    filterNodesByCategory(category, this.classList.contains('active'));
                }
            });
        });
    }

    /**
     * Filter nodes by category
     */
    function filterNodesByCategory(category, show) {
        const nodes = document.querySelectorAll('.graph-node');
        
        nodes.forEach(node => {
            const nodeCategories = (node.getAttribute('data-categories') || '').split(',');
            
            if (nodeCategories.includes(category)) {
                if (show) {
                    node.classList.remove('dimmed');
                    node.classList.add('highlighted');
                } else {
                    node.classList.remove('highlighted');
                }
            }
        });
    }

    /**
     * Setup Keyboard Navigation
     */
    function setupKeyboardNavigation() {
        document.addEventListener('keydown', function(e) {
            // Don't interfere with input fields
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                return;
            }

            switch(e.key) {
                case '+':
                case '=':
                    e.preventDefault();
                    document.getElementById('btn-zoom-in')?.click();
                    break;
                case '-':
                case '_':
                    e.preventDefault();
                    document.getElementById('btn-zoom-out')?.click();
                    break;
                case 'r':
                case 'R':
                    e.preventDefault();
                    document.getElementById('btn-reset-view')?.click();
                    break;
                case 'f':
                case 'F':
                    if (!e.ctrlKey && !e.metaKey) {
                        e.preventDefault();
                        document.getElementById('btn-fullscreen')?.click();
                    }
                    break;
                case '/':
                    e.preventDefault();
                    document.getElementById('graph-search')?.focus();
                    break;
                case 'Escape':
                    // Close info panel
                    const panel = document.getElementById('graph-info-panel');
                    if (panel && !panel.classList.contains('hidden')) {
                        panel.classList.add('hidden');
                    }
                    break;
            }
        });
    }

    /**
     * Setup Statistics Updater
     */
    function setupStatisticsUpdater() {
        // Listen for custom graph loaded event
        window.addEventListener('graphLoaded', function(event) {
            updateStatistics(event.detail);
        });

        // Or check periodically if graph instance is available
        const checkInterval = setInterval(() => {
            if (window.graphInstance && window.graphInstance.data) {
                updateStatistics(window.graphInstance.data);
                clearInterval(checkInterval);
            }
        }, 1000);

        // Clear after 10 seconds if still not loaded
        setTimeout(() => clearInterval(checkInterval), 10000);
    }

    /**
     * Update Statistics Display
     */
    function updateStatistics(data) {
        const nodesElement = document.getElementById('stats-nodes');
        const connectionsElement = document.getElementById('stats-connections');

        if (nodesElement && data) {
            const nodesCount = data.nodes?.length || data.nodeCount || 0;
            animateCount(nodesElement, nodesCount);
        }

        if (connectionsElement && data) {
            const connectionsCount = data.links?.length || data.connectionCount || 0;
            animateCount(connectionsElement, connectionsCount);
        }
    }

    /**
     * Animate Count Up
     */
    function animateCount(element, targetValue) {
        const duration = 1000;
        const startValue = 0;
        const startTime = performance.now();

        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function
            const easeOutQuad = progress * (2 - progress);
            const currentValue = Math.floor(startValue + (targetValue - startValue) * easeOutQuad);
            
            element.textContent = currentValue;

            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                element.textContent = targetValue;
            }
        }

        requestAnimationFrame(update);
    }

    /**
     * Enhanced tooltip behavior
     */
    function enhanceTooltips() {
        // Add touch support for mobile
        if ('ontouchstart' in window) {
            document.querySelectorAll('.graph-node').forEach(node => {
                node.addEventListener('touchstart', function(e) {
                    e.preventDefault();
                    // Trigger node click/info display
                    if (window.graphInstance && typeof window.graphInstance.showNodeInfo === 'function') {
                        window.graphInstance.showNodeInfo(this);
                    }
                });
            });
        }
    }

    // Export functions to global scope if needed
    window.archiHomeEnhancements = {
        performSearch,
        updateStatistics,
        filterNodesByCategory
    };

})();
