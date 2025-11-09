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
            $('body').css('font-family', newval);
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
    
})(jQuery);
