<?php
/**
 * Graph Configuration - Centralized Settings
 * 
 * This file provides a simplified, centralized configuration system for the graph.
 * All default values, presets, and validation rules are defined here.
 * 
 * @package Archi_Graph
 * @since 1.3.1
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Safe translation wrapper
 * 
 * @param string $text Text to translate
 * @param string $domain Text domain
 * @return string Translated text or original if WordPress not loaded
 */
function archi_graph_translate($text, $domain = 'archi-graph') {
    if (function_exists('__')) {
        return __($text, $domain);
    }
    return $text;
}

/**
 * Get simplified graph configuration presets
 * 
 * @return array Configuration presets
 */
function archi_visual_get_presets() {
    return [
        'minimal' => [
            'name' => archi_graph_translate('Minimal'),
            'description' => archi_graph_translate('Simple graph with basic interactions'),
            'settings' => [
                'animation_enabled' => true,
                'animation_type' => 'fade',
                'animation_speed' => 'normal',
                'hover_effect' => 'scale',
                'hover_intensity' => 'subtle',
                'visual_effects' => 'minimal',
                'link_animation' => false,
                'pulse_inactive' => false,
            ]
        ],
        'standard' => [
            'name' => archi_graph_translate('Standard'),
            'description' => archi_graph_translate('Balanced visual effects and performance'),
            'settings' => [
                'animation_enabled' => true,
                'animation_type' => 'slide',
                'animation_speed' => 'normal',
                'hover_effect' => 'scale',
                'hover_intensity' => 'medium',
                'visual_effects' => 'standard',
                'link_animation' => true,
                'pulse_inactive' => true,
            ]
        ],
        'rich' => [
            'name' => archi_graph_translate('Rich'),
            'description' => archi_graph_translate('Maximum visual effects and animations'),
            'settings' => [
                'animation_enabled' => true,
                'animation_type' => 'bounce',
                'animation_speed' => 'slow',
                'hover_effect' => 'multi',
                'hover_intensity' => 'strong',
                'visual_effects' => 'rich',
                'link_animation' => true,
                'pulse_inactive' => true,
            ]
        ],
        'performance' => [
            'name' => archi_graph_translate('Performance'),
            'description' => archi_graph_translate('Minimal effects for best performance'),
            'settings' => [
                'animation_enabled' => false,
                'animation_type' => 'none',
                'animation_speed' => 'fast',
                'hover_effect' => 'none',
                'hover_intensity' => 'none',
                'visual_effects' => 'none',
                'link_animation' => false,
                'pulse_inactive' => false,
            ]
        ],
    ];
}

/**
 * Get unified graph configuration with simplified parameters
 * 
 * @return array Simplified configuration structure
 */
function archi_visual_get_config() {
    return [
        // VISUAL APPEARANCE
        'visual' => [
            'default_node_color' => '#3498db',
            'default_node_size' => 80, // 🔥 FIX: Harmonized to 80px for optimal visibility and density
            'node_opacity' => 1.0,
            'show_labels' => true,
            'show_polygons' => true,
        ],
        
        // ANIMATION SETTINGS (Simplified)
        'animation' => [
            'enabled' => true,
            'type' => 'slide', // fade, slide, bounce, zoom, none
            'speed' => 'normal', // fast (400ms), normal (800ms), slow (1200ms)
            'easing' => 'ease-out', // ease, ease-in, ease-out, ease-in-out
            'stagger_delay' => 50, // Delay between each node animation
        ],
        
        // HOVER EFFECTS (Simplified)
        'hover' => [
            'enabled' => true,
            'effect' => 'scale', // scale, glow, multi, none
            'intensity' => 'medium', // subtle (1.1x), medium (1.15x), strong (1.25x)
            'show_halo' => true,
            'elevate_node' => true, // Bring to front on hover
        ],
        
        // INACTIVE NODE BEHAVIOR
        'inactive' => [
            'enabled' => true,
            'pulse_enabled' => true,
            'pulse_speed' => 2000, // milliseconds
            'opacity_min' => 0.3,
            'opacity_max' => 0.4,
            'grayscale' => 30, // percentage
        ],
        
        // CLICK INTERACTIONS
        'click' => [
            'toggle_state' => true,
            'shockwave_enabled' => true,
            'shockwave_duration' => 600,
            'bounce_animation' => true,
        ],
        
        // LINK BEHAVIOR
        'links' => [
            'animation_enabled' => true,
            'highlight_on_hover' => true,
            'style' => 'curve', // straight, curve
            'opacity' => 0.3,
            'hover_opacity' => 1.0,
        ],
        
        // PHYSICS SIMULATION
        'physics' => [
            'charge_strength' => -200, // 🔥 FIX: Reduced from -150 to -200 for natural dispersion
            'charge_distance' => 400, // 🔥 FIX: Increased from 300 to 400 for wider influence
            'link_distance' => 150, // 🔥 FIX: Increased from 120 to 150 for more breathing room
            'collision_radius' => 55, // 🔥 FIX: Increased from 50 to 55 for better spacing
            'collision_padding' => 20, // 🔥 FIX: Increased from 15 to 20px for more space
            'center_strength' => 0.03, // 🔥 FIX: Reduced from 0.15 to 0.03 for free dispersion
            'cluster_strength' => 0.15, // 🔥 FIX: Reduced from 0.25 to 0.15 for natural grouping
        ],
        
        // PERFORMANCE
        'performance' => [
            'enable_lazy_load' => true,
            'max_visible_nodes' => 100,
            'reduce_motion_media_query' => true,
        ],
    ];
}

/**
 * Convert simplified config to detailed parameters
 * 
 * @param array $config Simplified configuration
 * @return array Detailed parameters for frontend
 */
function archi_visual_expand_config($config = null) {
    if ($config === null) {
        $config = archi_visual_get_config();
    }
    
    // Animation speed mapping
    $speed_map = [
        'fast' => 400,
        'normal' => 800,
        'slow' => 1200,
    ];
    
    // Hover intensity mapping
    $intensity_map = [
        'none' => 1.0,
        'subtle' => 1.1,
        'medium' => 1.15,
        'strong' => 1.25,
    ];
    
    return [
        // Visual
        'nodeColor' => $config['visual']['default_node_color'],
        'nodeSize' => $config['visual']['default_node_size'],
        'defaultNodeSize' => $config['visual']['default_node_size'], // 🔥 FIX: Alias pour compatibilité
        'nodeOpacity' => $config['visual']['node_opacity'],
        'showLabels' => $config['visual']['show_labels'],
        'showPolygons' => $config['visual']['show_polygons'],
        
        // Animation
        'animationEnabled' => $config['animation']['enabled'],
        'animationType' => $config['animation']['type'],
        'animationDuration' => $speed_map[$config['animation']['speed']] ?? 800,
        'animationEasing' => $config['animation']['easing'],
        'staggerDelay' => $config['animation']['stagger_delay'],
        
        // Hover
        'hoverEnabled' => $config['hover']['enabled'],
        'hoverEffect' => $config['hover']['effect'],
        'hoverScale' => $intensity_map[$config['hover']['intensity']] ?? 1.15,
        'showHalo' => $config['hover']['show_halo'],
        'elevateOnHover' => $config['hover']['elevate_node'],
        
        // Inactive
        'inactiveEnabled' => $config['inactive']['enabled'],
        'pulseInactive' => $config['inactive']['pulse_enabled'],
        'pulseSpeed' => $config['inactive']['pulse_speed'],
        'inactiveOpacityMin' => $config['inactive']['opacity_min'],
        'inactiveOpacityMax' => $config['inactive']['opacity_max'],
        'inactiveGrayscale' => $config['inactive']['grayscale'],
        
        // Click
        'clickToggle' => $config['click']['toggle_state'],
        'shockwaveEnabled' => $config['click']['shockwave_enabled'],
        'shockwaveDuration' => $config['click']['shockwave_duration'],
        'bounceOnClick' => $config['click']['bounce_animation'],
        
        // Links
        'linkAnimation' => $config['links']['animation_enabled'],
        'highlightLinksOnHover' => $config['links']['highlight_on_hover'],
        'linkStyle' => $config['links']['style'],
        'linkOpacity' => $config['links']['opacity'],
        'linkHoverOpacity' => $config['links']['hover_opacity'],
        
        // Physics
        'chargeStrength' => $config['physics']['charge_strength'],
        'chargeDistance' => $config['physics']['charge_distance'] ?? 400, // 🔥 FIX: Updated default to 400
        'linkDistance' => $config['physics']['link_distance'],
        'collisionRadius' => $config['physics']['collision_radius'],
        'collisionPadding' => $config['physics']['collision_padding'] ?? 20, // 🔥 FIX: Updated to 20
        'centerStrength' => $config['physics']['center_strength'],
        'clusterStrength' => $config['physics']['cluster_strength'],
        'simulationAlpha' => 0.3, // 🔥 FIX: Démarrage doux
        'simulationAlphaDecay' => 0.02, // 🔥 FIX: Stabilisation rapide
        'simulationVelocityDecay' => 0.4, // 🔥 FIX: Plus de friction
        
        // Performance
        'lazyLoad' => $config['performance']['enable_lazy_load'],
        'maxVisibleNodes' => $config['performance']['max_visible_nodes'],
        'respectReducedMotion' => $config['performance']['reduce_motion_media_query'],
    ];
}

/**
 * Get current graph configuration (from WordPress options or defaults)
 * 
 * @return array Current configuration
 */
function archi_visual_get_current_config() {
    // Check if WordPress is loaded
    if (!function_exists('get_option')) {
        return archi_visual_get_config();
    }
    
    $preset = get_option('archi_graph_preset', 'standard');
    $presets = archi_visual_get_presets();
    
    if (isset($presets[$preset])) {
        $config = archi_visual_get_config();
        
        // Apply preset overrides
        foreach ($presets[$preset]['settings'] as $key => $value) {
            // Map preset settings to config structure
            switch ($key) {
                case 'animation_enabled':
                    $config['animation']['enabled'] = $value;
                    break;
                case 'animation_type':
                    $config['animation']['type'] = $value;
                    break;
                case 'animation_speed':
                    $config['animation']['speed'] = $value;
                    break;
                case 'hover_effect':
                    $config['hover']['effect'] = $value;
                    break;
                case 'hover_intensity':
                    $config['hover']['intensity'] = $value;
                    break;
                case 'link_animation':
                    $config['links']['animation_enabled'] = $value;
                    break;
                case 'pulse_inactive':
                    $config['inactive']['pulse_enabled'] = $value;
                    break;
            }
        }
        
        return $config;
    }
    
    return archi_visual_get_config();
}

/**
 * Save graph configuration preset and sync with Customizer settings
 * 
 * @param string $preset_name Preset name (minimal, standard, rich, performance)
 * @return bool Success
 */
function archi_visual_save_preset($preset_name) {
    // Check if WordPress is loaded
    if (!function_exists('update_option')) {
        return false;
    }
    
    $presets = archi_visual_get_presets();
    
    if (!isset($presets[$preset_name])) {
        return false;
    }
    
    // Save preset name
    update_option('archi_graph_preset', $preset_name);
    
    // 🔥 NEW: Sync preset values with Customizer theme_mods
    // This ensures the frontend JavaScript gets the correct values
    $preset_settings = $presets[$preset_name]['settings'];
    
    // Map preset settings to Customizer theme_mod keys
    $mapping = [
        'animation_enabled' => ['archi_graph_animation_enabled', 'bool'],
        'animation_type' => ['archi_graph_animation_mode', 'text'],
        'animation_speed' => ['archi_graph_transition_speed', 'speed'],
        'hover_effect' => ['archi_graph_hover_effect', 'text'],
        'hover_intensity' => ['archi_graph_hover_intensity', 'text'],
        'link_animation' => ['archi_graph_link_animation', 'link_anim'],
        'pulse_inactive' => ['archi_graph_pulse_inactive', 'bool'],
        'visual_effects' => ['archi_graph_visual_effects_level', 'text'],
    ];
    
    foreach ($mapping as $preset_key => $config) {
        list($theme_mod_key, $type) = $config;
        
        if (!isset($preset_settings[$preset_key])) {
            continue;
        }
        
        $value = $preset_settings[$preset_key];
        
        // Convert preset values to Customizer-compatible values
        switch ($type) {
            case 'bool':
                set_theme_mod($theme_mod_key, (bool) $value);
                break;
                
            case 'text':
                set_theme_mod($theme_mod_key, $value);
                break;
                
            case 'speed':
                // Map speed keywords to milliseconds
                $speed_map = [
                    'fast' => 200,
                    'normal' => 500,
                    'slow' => 1000,
                ];
                $speed_value = isset($speed_map[$value]) ? $speed_map[$value] : 500;
                set_theme_mod('archi_graph_transition_speed', $speed_value);
                break;
                
            case 'link_anim':
                // Map boolean to animation type
                $anim_value = $value ? 'pulse' : 'none';
                set_theme_mod('archi_graph_link_animation', $anim_value);
                break;
        }
    }
    
    // Set hover effect intensity based on preset
    if (isset($preset_settings['hover_intensity'])) {
        $intensity = $preset_settings['hover_intensity'];
        
        // Map intensity to actual hover effect if needed
        if ($intensity === 'none') {
            set_theme_mod('archi_graph_hover_effect', 'none');
        } elseif ($intensity === 'subtle') {
            set_theme_mod('archi_graph_hover_effect', 'highlight');
        } elseif ($intensity === 'strong') {
            set_theme_mod('archi_graph_hover_effect', 'glow');
        }
    }
    
    return true;
}

/**
 * Get graph configuration for frontend (JavaScript)
 * 
 * @return array Configuration for wp_localize_script
 */
function archi_visual_get_frontend_config() {
    $config = archi_visual_get_current_config();
    return archi_visual_expand_config($config);
}
