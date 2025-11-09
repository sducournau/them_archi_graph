/**
 * Customizer Controls Enhancement
 * 
 * Adds custom controls and behaviors to the WordPress Customizer panel.
 * 
 * @package Archi_Graph
 * @since 1.2.0
 */
(function($) {
    'use strict';
    
    wp.customize.bind('ready', function() {
        
        // ========================================
        // ADD HELPFUL DESCRIPTIONS
        // ========================================
        
        // Add info notice for header options
        wp.customize.section('archi_header_options', function(section) {
            section.expanded.bind(function(isExpanded) {
                if (isExpanded) {
                    showTip('header-tip', 'Ces options contrôlent le comportement du header sur la page d\'accueil avec le graphique.');
                }
            });
        });
        
        // Add info notice for graph options
        wp.customize.section('archi_graph_options', function(section) {
            section.expanded.bind(function(isExpanded) {
                if (isExpanded) {
                    showTip('graph-tip', 'Modifiez ces valeurs pour voir les changements sur la page d\'accueil. Certaines options nécessitent un rechargement.');
                }
            });
        });
        
        // ========================================
        // LIVE PREVIEW INDICATORS
        // ========================================
        
        // Add indicators for live preview vs refresh
        var livePreviewSettings = [
            'archi_header_hide_delay',
            'archi_header_animation_type',
            'archi_header_animation_duration',
            'archi_header_trigger_height',
            'archi_font_family',
            'archi_font_size_base',
            'archi_primary_color',
            'archi_secondary_color',
            'archi_footer_copyright',
            'archi_footer_show_social'
        ];
        
        livePreviewSettings.forEach(function(settingId) {
            addLiveIndicator(settingId);
        });
        
        // ========================================
        // RANGE SLIDER VALUE DISPLAY
        // ========================================
        
        // Add value display for range inputs
        $('.customize-control-range input[type="range"]').each(function() {
            var $input = $(this);
            var $control = $input.closest('.customize-control');
            
            // Create value display if it doesn't exist
            if ($control.find('.range-value-display').length === 0) {
                var $display = $('<span class="range-value-display"></span>');
                $display.css({
                    'display': 'inline-block',
                    'margin-left': '10px',
                    'font-weight': 'bold',
                    'color': '#0073aa'
                });
                $input.after($display);
                
                // Update display
                updateRangeDisplay($input, $display);
                
                // Update on change
                $input.on('input change', function() {
                    updateRangeDisplay($input, $display);
                });
            }
        });
        
        // ========================================
        // COLOR PREVIEW IN CONTROLS
        // ========================================
        
        // Enhance color controls with better preview
        $('.customize-control-color input[type="text"]').each(function() {
            var $input = $(this);
            var $wpColorPicker = $input.closest('.wp-picker-container');
            
            if ($wpColorPicker.length) {
                $wpColorPicker.css({
                    'border': '2px solid #ddd',
                    'border-radius': '4px',
                    'padding': '5px'
                });
            }
        });
        
        // ========================================
        // HELPER FUNCTIONS
        // ========================================
        
        /**
         * Show tip message
         */
        function showTip(id, message) {
            var existingTip = $('#' + id);
            if (existingTip.length > 0) {
                existingTip.slideDown();
                return;
            }
            
            var $tip = $('<div>', {
                id: id,
                class: 'archi-customizer-tip',
                html: '<p><span class="dashicons dashicons-info"></span> ' + message + '</p>'
            });
            
            $tip.css({
                'background': '#e8f5e9',
                'border-left': '4px solid #4caf50',
                'padding': '10px',
                'margin': '10px 0',
                'border-radius': '4px'
            });
            
            $('.wp-full-overlay-sidebar-content').prepend($tip);
        }
        
        /**
         * Add live preview indicator
         */
        function addLiveIndicator(settingId) {
            wp.customize.control(settingId, function(control) {
                if (!control) return;
                
                var $container = control.container;
                var $label = $container.find('.customize-control-title');
                
                if ($label.length && $label.find('.live-indicator').length === 0) {
                    var $indicator = $('<span class="live-indicator">⚡</span>');
                    $indicator.css({
                        'margin-left': '5px',
                        'color': '#4caf50',
                        'font-size': '14px',
                        'cursor': 'help'
                    });
                    $indicator.attr('title', 'Aperçu en temps réel');
                    $label.append($indicator);
                }
            });
        }
        
        /**
         * Update range slider value display
         */
        function updateRangeDisplay($input, $display) {
            var value = $input.val();
            var unit = '';
            
            // Determine unit based on control
            var $control = $input.closest('.customize-control');
            var controlId = $control.attr('id');
            
            if (controlId.includes('duration') || controlId.includes('delay')) {
                unit = $input.attr('step') === '0.1' ? 's' : 'ms';
            } else if (controlId.includes('size') || controlId.includes('height')) {
                unit = 'px';
            } else if (controlId.includes('strength') || controlId.includes('opacity')) {
                // Convert to percentage for display
                value = Math.round(parseFloat(value) * 100);
                unit = '%';
            }
            
            $display.text(value + unit);
        }
        
        // ========================================
        // EXPORT/IMPORT SETTINGS (FUTURE)
        // ========================================
        
        // Add export/import buttons to bottom of customizer
        var $exportImportSection = $('<div>', {
            class: 'archi-export-import',
            html: '<h3>Sauvegarder / Restaurer</h3>' +
                  '<p><button class="button" id="archi-export-settings">Exporter les réglages</button></p>' +
                  '<p><button class="button" id="archi-import-settings">Importer les réglages</button></p>'
        });
        
        $exportImportSection.css({
            'padding': '15px',
            'border-top': '1px solid #ddd',
            'margin-top': '20px'
        });
        
        // Note: Export/Import functionality would require additional PHP endpoints
        // This is a placeholder for future implementation
        
    });
    
})(jQuery);
