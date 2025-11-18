/**
 * Customizer Live Preview
 * 
 * Provides real-time preview of customizer changes without page reload.
 * 
 * @package Archi_Graph
 * @since 1.2.0
 */
(function($) {
    'use strict';
    
    // ========================================
    // INITIALIZE GRAPH SETTINGS UPDATE FUNCTION
    // ========================================
    
    /**
     * Initialize window.updateGraphSettings if not already defined by GraphContainer
     * This ensures the function exists even if GraphContainer isn't mounted yet
     */
    if (typeof window.updateGraphSettings !== 'function') {
        console.log('🎨 Initializing window.updateGraphSettings (Customizer fallback)');
        
        window.updateGraphSettings = function(newSettings) {
            console.log('🎨 Graph settings update requested (via fallback):', newSettings);
            
            // Update window.archiGraphSettings
            if (typeof window.archiGraphSettings === 'object') {
                Object.assign(window.archiGraphSettings, newSettings);
            } else {
                window.archiGraphSettings = newSettings;
            }
            
            // Dispatch event for GraphContainer to listen to
            var event = new CustomEvent('graphSettingsUpdated', { 
                detail: newSettings 
            });
            window.dispatchEvent(event);
            
            // If GraphContainer is mounted, it will handle the update via the event
            // If not, the settings are still updated for when it mounts later
        };
    }
    
    // ========================================
    // HEADER OPTIONS
    // ========================================
    
    // Header hide delay
    wp.customize('archi_header_hide_delay', function(value) {
        value.bind(function(newval) {
            // Update global config if exists
            if (typeof window.archiHeaderConfig !== 'undefined') {
                window.archiHeaderConfig.hideDelay = parseInt(newval);
            }
            
            // Re-initialize header behavior
            initHeaderBehavior(parseInt(newval));
        });
    });
    
    // Header animation type
    wp.customize('archi_header_animation_type', function(value) {
        value.bind(function(newval) {
            var $header = $('.site-header');
            var currentDuration = $header.css('transition-duration');
            $header.css({
                'transition': 'transform ' + currentDuration + ' ' + newval + ', opacity ' + currentDuration + ' ' + newval
            });
        });
    });
    
    // Header animation duration
    wp.customize('archi_header_animation_duration', function(value) {
        value.bind(function(newval) {
            var $header = $('.site-header');
            var currentTimingFunction = getComputedStyle($header[0]).transitionTimingFunction || 'ease-in-out';
            $header.css({
                'transition': 'transform ' + newval + 's ' + currentTimingFunction + ', opacity ' + newval + 's ' + currentTimingFunction
            });
        });
    });
    
    // Header trigger zone height
    wp.customize('archi_header_trigger_height', function(value) {
        value.bind(function(newval) {
            $('.header-trigger-zone').css('height', newval + 'px');
        });
    });
    
    // ========================================
    // TYPOGRAPHY
    // ========================================
    
    // Font family
    wp.customize('archi_font_family', function(value) {
        value.bind(function(newval) {
            // Get the font stack for the selected font
            var fontStack = getFontStackForFamily(newval);
            
            // Apply to all elements across the site, including graph elements
            var elements = [
                'body',
                'html',
                'input',
                'textarea',
                'select',
                'button',
                '.site-header',
                '.site-navigation',
                '.main-navigation',
                '.site-content',
                '.entry-content',
                '.site-footer',
                'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                'p', 'span', 'div', 'a',
                '.btn', '.button',
                '.wp-block',
                '.graph-container',
                '.article-card',
                '.panel-content',
                '.node-title-text',
                '.node-label',
                '.graph-legend',
                '.graph-info-panel',
                '.graph-instructions',
                '.graph-controls',
                '.side-panel',
                '.title-overlay'
            ].join(', ');
            
            $(elements).css('font-family', fontStack);
            
            // Load Google Font if needed
            loadGoogleFontIfNeeded(newval);
        });
    });
    
    // Font size base
    wp.customize('archi_font_size_base', function(value) {
        value.bind(function(newval) {
            $('body').css('font-size', newval + 'px');
        });
    });
    
    // ========================================
    // COLORS
    // ========================================
    
    // Primary color
    wp.customize('archi_primary_color', function(value) {
        value.bind(function(newval) {
            // Update links
            $('a').css('color', newval);
            
            // Update buttons
            $('.button, .btn-primary, button[type="submit"]').css({
                'background-color': newval,
                'border-color': newval
            });
            
            // Update other primary elements
            $('.archi-nav-tab.archi-nav-tab-active').css('border-bottom-color', newval);
        });
    });
    
    // Secondary color
    wp.customize('archi_secondary_color', function(value) {
        value.bind(function(newval) {
            $('h1, h2, h3, h4, h5, h6').css('color', newval);
        });
    });
    
    // ========================================
    // GRAPH VISUALIZATION OPTIONS
    // ========================================
    
    // Graph node color
    wp.customize('archi_default_node_color', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ defaultNodeColor: newval });
            }
        });
    });
    
    // Graph node size
    wp.customize('archi_default_node_size', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ defaultNodeSize: parseInt(newval) });
            }
        });
    });
    
    // Cluster strength
    wp.customize('archi_cluster_strength', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ clusterStrength: parseFloat(newval) });
            }
        });
    });
    
    // Link color
    wp.customize('archi_graph_link_color', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ linkColor: newval });
            }
            // Direct CSS update for immediate visual feedback
            $('.graph-container svg line, .graph-container svg path.link').css('stroke', newval);
        });
    });
    
    // Link width
    wp.customize('archi_graph_link_width', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ linkWidth: parseFloat(newval) });
            }
            $('.graph-container svg line, .graph-container svg path.link').css('stroke-width', newval);
        });
    });
    
    // Link opacity
    wp.customize('archi_graph_link_opacity', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ linkOpacity: parseFloat(newval) });
            }
            $('.graph-container svg line, .graph-container svg path.link').css('opacity', newval);
        });
    });
    
    // Link style
    wp.customize('archi_graph_link_style', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ linkStyle: newval });
            }
        });
    });
    
    // Show arrows
    wp.customize('archi_graph_show_arrows', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ showArrows: newval });
            }
        });
    });
    
    // Link animation
    wp.customize('archi_graph_link_animation', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ linkAnimation: newval });
            }
        });
    });
    
    // Animation mode
    wp.customize('archi_graph_animation_mode', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ animationMode: newval });
            }
        });
    });
    
    // Transition speed
    wp.customize('archi_graph_transition_speed', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ transitionSpeed: parseInt(newval) });
            }
        });
    });
    
    // Hover effect
    wp.customize('archi_graph_hover_effect', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ hoverEffect: newval });
            }
        });
    });
    
    // Category colors enabled
    wp.customize('archi_graph_category_colors_enabled', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ categoryColorsEnabled: newval });
            }
        });
    });
    
    // Category palette
    wp.customize('archi_graph_category_palette', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                // Get the palette colors from PHP
                var paletteColors = getCategoryPaletteColors(newval);
                window.updateGraphSettings({ 
                    categoryPalette: newval,
                    categoryColors: paletteColors
                });
            }
        });
    });
    
    // Show category legend
    wp.customize('archi_graph_show_category_legend', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ showCategoryLegend: newval });
            }
            
            // Toggle legend visibility immediately
            if (newval) {
                $('.graph-legend').fadeIn(300);
            } else {
                $('.graph-legend').fadeOut(300);
            }
        });
    });
    
    // Show comments
    wp.customize('archi_graph_show_comments', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ showComments: newval });
            }
        });
    });
    
    // Popup title only
    wp.customize('archi_graph_popup_title_only', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ popupTitleOnly: newval });
            }
        });
    });
    
    // ========================================
    // POST TYPE COLORS
    // ========================================
    
    // Project color
    wp.customize('archi_project_color', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ projectColor: newval });
            }
        });
    });
    
    // Illustration color
    wp.customize('archi_illustration_color', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ illustrationColor: newval });
            }
        });
    });
    
    // Pages zone color
    wp.customize('archi_pages_zone_color', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ pagesZoneColor: newval });
            }
        });
    });
    
    // Guestbook link color
    wp.customize('archi_guestbook_link_color', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ guestbookLinkColor: newval });
            }
        });
    });
    
    // ========================================
    // PRIORITY BADGES
    // ========================================
    
    // Priority featured color
    wp.customize('archi_priority_featured_color', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ priorityFeaturedColor: newval });
            }
        });
    });
    
    // Priority high color
    wp.customize('archi_priority_high_color', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ priorityHighColor: newval });
            }
        });
    });
    
    // Priority badge size
    wp.customize('archi_priority_badge_size', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ priorityBadgeSize: parseInt(newval) });
            }
        });
    });
    
    // ========================================
    // NODE SCALING
    // ========================================
    
    // Active node scale
    wp.customize('archi_active_node_scale', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ activeNodeScale: parseFloat(newval) });
            }
        });
    });
    
    // ========================================
    // CLUSTER APPEARANCE
    // ========================================
    
    // Cluster fill opacity
    wp.customize('archi_cluster_fill_opacity', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ clusterFillOpacity: parseFloat(newval) });
            }
        });
    });
    
    // Cluster stroke width
    wp.customize('archi_cluster_stroke_width', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ clusterStrokeWidth: parseInt(newval) });
            }
        });
    });
    
    // Cluster stroke opacity
    wp.customize('archi_cluster_stroke_opacity', function(value) {
        value.bind(function(newval) {
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ clusterStrokeOpacity: parseFloat(newval) });
            }
        });
    });
    
    // ========================================
    // FOOTER
    // ========================================
    
    // Footer copyright
    wp.customize('archi_footer_copyright', function(value) {
        value.bind(function(newval) {
            $('.site-footer .copyright-text').html(newval);
        });
    });
    
    // Footer show social
    wp.customize('archi_footer_show_social', function(value) {
        value.bind(function(newval) {
            if (newval) {
                $('.site-footer .social-links').show();
            } else {
                $('.site-footer .social-links').hide();
            }
        });
    });
    
    // ========================================
    // HELPER FUNCTIONS
    // ========================================
    
    /**
     * Re-initialize header behavior with new delay
     */
    function initHeaderBehavior(hideDelay) {
        var header = document.getElementById('site-header');
        var triggerZone = document.querySelector('.header-trigger-zone');
        var hideTimeout;
        
        if (!header) return;
        
        // Clear existing listeners (not perfect but works for preview)
        // In production, you'd want to properly remove event listeners
        
        function hideHeader() {
            clearTimeout(hideTimeout);
            hideTimeout = setTimeout(function() {
                if (header) {
                    header.classList.add('header-hidden');
                }
            }, hideDelay);
        }
        
        function showHeader() {
            clearTimeout(hideTimeout);
            if (header) {
                header.classList.remove('header-hidden');
            }
        }
        
        // Re-attach listeners
        if (triggerZone) {
            $(triggerZone).off('mouseenter').on('mouseenter', showHeader);
        }
        
        if (header) {
            $(header).off('mouseleave').on('mouseleave', hideHeader);
            $(header).off('mouseenter').on('mouseenter', function() {
                clearTimeout(hideTimeout);
            });
        }
        
        // Initial hide
        hideHeader();
    }
    
    /**
     * Adjust color brightness
     */
    function adjustColorBrightness(hex, percent) {
        hex = hex.replace('#', '');
        
        var r = parseInt(hex.substring(0, 2), 16);
        var g = parseInt(hex.substring(2, 4), 16);
        var b = parseInt(hex.substring(4, 6), 16);
        
        r = Math.round(r * (100 + percent) / 100);
        g = Math.round(g * (100 + percent) / 100);
        b = Math.round(b * (100 + percent) / 100);
        
        r = Math.min(255, Math.max(0, r));
        g = Math.min(255, Math.max(0, g));
        b = Math.min(255, Math.max(0, b));
        
        return '#' + 
            ('0' + r.toString(16)).slice(-2) +
            ('0' + g.toString(16)).slice(-2) +
            ('0' + b.toString(16)).slice(-2);
    }
    
    /**
     * Get font stack for a font family
     */
    function getFontStackForFamily(fontFamily) {
        var fontStacks = {
            'system': '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
            'arial': 'Arial, Helvetica, sans-serif',
            'helvetica': '"Helvetica Neue", Helvetica, Arial, sans-serif',
            'georgia': 'Georgia, "Times New Roman", Times, serif',
            'times': '"Times New Roman", Times, serif',
            'courier': '"Courier New", Courier, monospace',
            'verdana': 'Verdana, Geneva, sans-serif',
            'trebuchet': '"Trebuchet MS", "Lucida Grande", "Lucida Sans Unicode", sans-serif',
            'roboto': '"Roboto", -apple-system, BlinkMacSystemFont, sans-serif',
            'open-sans': '"Open Sans", -apple-system, BlinkMacSystemFont, sans-serif',
            'lato': '"Lato", -apple-system, BlinkMacSystemFont, sans-serif',
            'montserrat': '"Montserrat", -apple-system, BlinkMacSystemFont, sans-serif',
            'poppins': '"Poppins", -apple-system, BlinkMacSystemFont, sans-serif',
            'inter': '"Inter", -apple-system, BlinkMacSystemFont, sans-serif',
            'playfair': '"Playfair Display", Georgia, serif',
            'merriweather': '"Merriweather", Georgia, serif'
        };
        
        return fontStacks[fontFamily] || fontStacks['system'];
    }
    
    /**
     * Load Google Font if needed
     */
    function loadGoogleFontIfNeeded(fontFamily) {
        var googleFonts = {
            'roboto': 'Roboto:300,400,500,700',
            'open-sans': 'Open+Sans:300,400,600,700',
            'lato': 'Lato:300,400,700',
            'montserrat': 'Montserrat:300,400,500,600,700',
            'poppins': 'Poppins:300,400,500,600,700',
            'inter': 'Inter:300,400,500,600,700',
            'playfair': 'Playfair+Display:400,500,700',
            'merriweather': 'Merriweather:300,400,700'
        };
        
        // Remove existing Google Font link if present
        $('#archi-preview-google-font').remove();
        
        // Add new Google Font if needed
        if (googleFonts[fontFamily]) {
            var fontUrl = 'https://fonts.googleapis.com/css2?family=' + googleFonts[fontFamily] + '&display=swap';
            $('head').append('<link id="archi-preview-google-font" rel="stylesheet" href="' + fontUrl + '">');
        }
    }
    
    /**
     * Get category palette colors
     */
    function getCategoryPaletteColors(paletteName) {
        var palettes = {
            'default': [
                '#3498db', '#2980b9', '#5dade2', '#1f618d', '#85c1e9',
                '#21618c', '#7fb3d5', '#154360', '#aed6f1', '#2e86c1'
            ],
            'warm': [
                '#e74c3c', '#c0392b', '#ec7063', '#922b21', '#f1948a',
                '#e67e22', '#d35400', '#f39c12', '#f8c471', '#dc7633'
            ],
            'cool': [
                '#16a085', '#1abc9c', '#48c9b0', '#0e6655', '#76d7c4',
                '#27ae60', '#229954', '#52be80', '#1e8449', '#82e0aa'
            ],
            'vibrant': [
                '#e74c3c', '#3498db', '#9b59b6', '#f39c12', '#1abc9c',
                '#e67e22', '#2ecc71', '#8e44ad', '#34495e', '#16a085'
            ],
            'pastel': [
                '#aed6f1', '#f9e79f', '#abebc6', '#f5b7b1', '#d7bde2',
                '#a9dfbf', '#f8b4d9', '#fad7a0', '#d5f4e6', '#fadbd8'
            ],
            'nature': [
                '#27ae60', '#229954', '#52be80', '#7d6608', '#d68910',
                '#935116', '#6e2c00', '#52be80', '#a04000', '#82e0aa'
            ],
            'monochrome': [
                '#2c3e50', '#34495e', '#566573', '#707b7c', '#95a5a6',
                '#7f8c8d', '#515a5a', '#a6acaf', '#626567', '#d5d8dc'
            ]
        };
        
        return palettes[paletteName] || palettes['default'];
    }
    
    // ========================================
    // 🔥 VISUAL EFFECTS (NEW 2.0.0)
    // ========================================
    
    /**
     * 🎯 PRESET HANDLER - Apply complete preset when user selects one
     */
    wp.customize('archi_effects_preset', function(value) {
        value.bind(function(newval) {
            if (newval === 'custom') return; // Don't override custom settings
            
            var presets = {
                none: {
                    archi_active_node_glow_enabled: false,
                    archi_node_shadow_enabled: false,
                    archi_node_pulse_enabled: false,
                    archi_particles_enabled: false,
                    archi_ambient_glow_enabled: false,
                    archi_hover_scale: 1.0,
                    archi_hover_brightness: 1.0,
                },
                subtle: {
                    archi_active_node_glow_enabled: true,
                    archi_active_node_glow_intensity: 15,
                    archi_active_node_glow_opacity: 0.5,
                    archi_node_shadow_enabled: true,
                    archi_node_shadow_blur: 4,
                    archi_node_shadow_opacity: 0.2,
                    archi_node_pulse_enabled: false,
                    archi_particles_enabled: true,
                    archi_particles_count: 10,
                    archi_particles_opacity: 0.08,
                    archi_particles_speed: 20,
                    archi_ambient_glow_enabled: true,
                    archi_ambient_glow_opacity: 0.15,
                    archi_ambient_glow_duration: 10,
                    archi_hover_scale: 1.1,
                    archi_hover_transition_duration: 400,
                    archi_hover_brightness: 1.08,
                    archi_active_node_scale: 1.3,
                },
                normal: {
                    archi_active_node_glow_enabled: true,
                    archi_active_node_glow_intensity: 25,
                    archi_active_node_glow_opacity: 0.8,
                    archi_node_shadow_enabled: true,
                    archi_node_shadow_blur: 6,
                    archi_node_shadow_opacity: 0.3,
                    archi_node_pulse_enabled: true,
                    archi_node_pulse_duration: 2500,
                    archi_node_pulse_intensity: 0.85,
                    archi_particles_enabled: true,
                    archi_particles_count: 20,
                    archi_particles_opacity: 0.15,
                    archi_particles_speed: 15,
                    archi_ambient_glow_enabled: true,
                    archi_ambient_glow_opacity: 0.3,
                    archi_ambient_glow_duration: 8,
                    archi_hover_scale: 1.2,
                    archi_hover_transition_duration: 300,
                    archi_hover_brightness: 1.15,
                    archi_active_node_scale: 1.5,
                    archi_active_node_glow_animation: 'pulse',
                },
                intense: {
                    archi_active_node_glow_enabled: true,
                    archi_active_node_glow_intensity: 40,
                    archi_active_node_glow_opacity: 1.0,
                    archi_node_shadow_enabled: true,
                    archi_node_shadow_blur: 12,
                    archi_node_shadow_opacity: 0.5,
                    archi_node_pulse_enabled: true,
                    archi_node_pulse_duration: 1500,
                    archi_node_pulse_intensity: 0.7,
                    archi_particles_enabled: true,
                    archi_particles_count: 40,
                    archi_particles_opacity: 0.25,
                    archi_particles_speed: 10,
                    archi_ambient_glow_enabled: true,
                    archi_ambient_glow_opacity: 0.5,
                    archi_ambient_glow_duration: 5,
                    archi_hover_scale: 1.35,
                    archi_hover_transition_duration: 200,
                    archi_hover_brightness: 1.3,
                    archi_active_node_scale: 1.8,
                    archi_active_node_glow_animation: 'breathe',
                },
            };
            
            var presetValues = presets[newval];
            if (!presetValues) return;
            
            // Apply all preset values to controls
            Object.entries(presetValues).forEach(function(entry) {
                var key = entry[0];
                var val = entry[1];
                var control = wp.customize.control(key);
                if (control) {
                    control.setting.set(val);
                }
            });
        });
    });
    
    /**
     * 🎨 CSS VARIABLES LIVE UPDATES
     */
    
    // Active Node Glow
    wp.customize('archi_active_node_glow_intensity', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--archi-active-glow-intensity', newval + 'px');
        });
    });
    
    wp.customize('archi_active_node_glow_opacity', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--archi-active-glow-opacity', newval);
        });
    });
    
    // Shadows
    wp.customize('archi_node_shadow_blur', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--archi-shadow-blur', newval + 'px');
        });
    });
    
    wp.customize('archi_node_shadow_opacity', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--archi-shadow-opacity', newval);
        });
    });
    
    // Pulse
    wp.customize('archi_node_pulse_duration', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--archi-pulse-duration', newval + 'ms');
        });
    });
    
    wp.customize('archi_node_pulse_intensity', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--archi-pulse-intensity', newval);
        });
    });
    
    // Particles
    wp.customize('archi_particles_opacity', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--archi-particles-opacity', newval);
        });
    });
    
    wp.customize('archi_particles_speed', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--archi-particles-speed', newval + 's');
        });
    });
    
    wp.customize('archi_particles_count', function(value) {
        value.bind(function(newval) {
            // Particles count requires React component update
            if (window.location.pathname === '/' || window.location.pathname.includes('home')) {
                wp.customize.previewer.refresh();
            }
        });
    });
    
    // Ambient Glow
    wp.customize('archi_ambient_glow_opacity', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--archi-ambient-glow-opacity', newval);
        });
    });
    
    wp.customize('archi_ambient_glow_duration', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--archi-ambient-glow-duration', newval + 's');
        });
    });
    
    // Hover Effects
    wp.customize('archi_hover_scale', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--archi-hover-scale', newval);
        });
    });
    
    wp.customize('archi_hover_transition_duration', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--archi-hover-transition', newval + 'ms');
        });
    });
    
    wp.customize('archi_hover_brightness', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--archi-hover-brightness', newval);
        });
    });
    
    // Active Node
    wp.customize('archi_active_node_scale', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--archi-active-scale', newval);
            // Also update graph settings for D3
            if (typeof window.updateGraphSettings === 'function') {
                window.updateGraphSettings({ activeNodeScale: parseFloat(newval) });
            }
        });
    });
    
    wp.customize('archi_active_node_glow_animation', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--archi-active-animation', newval);
        });
    });
    
    /**
     * 🔄 ENABLED/DISABLED TOGGLES
     * These require React re-render so we refresh the preview
     */
    var toggleSettings = [
        'archi_active_node_glow_enabled',
        'archi_node_shadow_enabled',
        'archi_node_pulse_enabled',
        'archi_particles_enabled',
        'archi_ambient_glow_enabled',
    ];
    
    toggleSettings.forEach(function(setting) {
        wp.customize(setting, function(value) {
            value.bind(function(newval) {
                // Update CSS variable
                var varName = '--' + setting.replace(/_/g, '-');
                document.documentElement.style.setProperty(varName, newval ? '1' : '0');
                
                // Refresh preview for React components
                if (window.location.pathname === '/' || window.location.pathname.includes('home')) {
                    wp.customize.previewer.refresh();
                }
            });
        });
    });
    
})(jQuery);

