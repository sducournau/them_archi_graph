<?php
/**
 * Graph Configuration Registry - Single Source of Truth
 * 
 * This file provides THE authoritative function for retrieving all graph-related
 * configuration options. All other files should call archi_get_graph_options()
 * rather than directly accessing options with get_option().
 * 
 * Standardized Option Key Pattern: archi_graph_*
 * 
 * @package ArchiGraph
 * @since 1.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get all graph configuration options (SINGLE SOURCE OF TRUTH)
 * 
 * This is the ONLY function that should be used to retrieve graph configuration.
 * All option keys follow the pattern: archi_graph_*
 * 
 * @param bool $expand_presets Whether to expand preset values (for advanced usage)
 * @return array Complete graph configuration array
 * 
 * @since 1.2.0
 */
function archi_get_graph_options($expand_presets = false) {
    $options = [
        // === ANIMATION SETTINGS ===
        'animation_duration'   => absint(get_option('archi_graph_animation_duration', 800)),
        'animation_type'       => sanitize_text_field(get_option('archi_graph_animation_type', 'fadeIn')),
        'animation_enabled'    => (bool) get_option('archi_graph_animation_enabled', true),
        'link_animation'       => (bool) get_option('archi_graph_link_animation', true),
        
        // === NODE VISUAL SETTINGS ===
        'node_spacing'         => absint(get_option('archi_graph_min_distance', 100)),
        'node_default_color'   => sanitize_hex_color(get_option('archi_graph_default_color', '#3498db')),
        'node_default_size'    => absint(get_option('archi_graph_default_size', 60)),
        'show_labels'          => (bool) get_option('archi_graph_show_labels', true),
        
        // === FORCE SIMULATION SETTINGS ===
        'cluster_strength'     => floatval(get_option('archi_graph_cluster_strength', 0.1)),
        'link_strength'        => absint(get_option('archi_graph_link_strength', 80)),
        
        // === INTERACTION SETTINGS ===
        'hover_effect'         => (bool) get_option('archi_graph_hover_effect', true),
        'hover_scale'          => floatval(get_option('archi_graph_hover_scale', 1.15)),
        
        // === BEHAVIOR SETTINGS ===
        'auto_add_posts'       => (bool) get_option('archi_graph_auto_add_posts', false),
        'auto_calculate_relations' => (bool) get_option('archi_graph_auto_calculate_relations', true),
        'organic_mode'         => (bool) get_option('archi_graph_organic_mode', true),
        'auto_save_positions'  => (bool) get_option('archi_graph_auto_save_positions', false),
        
        // === VISUAL PRESET ===
        'preset'               => sanitize_text_field(get_option('archi_graph_preset', 'standard')),
        
        // === ADVANCED SETTINGS ===
        'enabled_post_types'   => get_option('archi_graph_enabled_post_types', ['post', 'archi_project', 'archi_illustration']),
        'cache_duration'       => absint(get_option('archi_graph_cache_duration', HOUR_IN_SECONDS)),
        'max_articles'         => absint(get_option('archi_graph_max_articles', 100)),
        
        // === BACKGROUND/THEME ===
        'background_gradient_start' => sanitize_hex_color(get_option('archi_graph_bg_gradient_start', '#667eea')),
        'background_gradient_end'   => sanitize_hex_color(get_option('archi_graph_bg_gradient_end', '#764ba2')),
    ];
    
    // Optionally expand preset values
    if ($expand_presets && function_exists('archi_visual_get_presets')) {
        $preset = $options['preset'];
        $presets = archi_visual_get_presets();
        
        if (isset($presets[$preset])) {
            // Merge preset values (preset values take precedence)
            $options = array_merge($options, $presets[$preset]);
        }
    }
    
    /**
     * Filter the complete graph configuration
     * 
     * @param array $options Complete array of graph configuration options
     * @param bool  $expand_presets Whether presets were expanded
     */
    return apply_filters('archi_graph_options', $options, $expand_presets);
}

/**
 * Get a single graph option by key
 * 
 * @param string $key     Option key (without 'archi_graph_' prefix)
 * @param mixed  $default Default value if option doesn't exist
 * @return mixed Option value
 * 
 * @since 1.2.0
 */
function archi_get_graph_option($key, $default = null) {
    $options = archi_get_graph_options();
    
    return isset($options[$key]) ? $options[$key] : $default;
}

/**
 * Update a single graph option
 * 
 * @param string $key   Option key (without 'archi_graph_' prefix)
 * @param mixed  $value Option value
 * @return bool Whether the option was updated
 * 
 * @since 1.2.0
 */
function archi_update_graph_option($key, $value) {
    // Map key to full option name
    $option_name = 'archi_graph_' . $key;
    
    // Handle special keys that have different storage names
    $key_mapping = [
        'node_spacing' => 'archi_graph_min_distance',
        'background_gradient_start' => 'archi_graph_bg_gradient_start',
        'background_gradient_end' => 'archi_graph_bg_gradient_end',
    ];
    
    if (isset($key_mapping[$key])) {
        $option_name = $key_mapping[$key];
    }
    
    return update_option($option_name, $value);
}

/**
 * Get graph configuration for JavaScript localization
 * 
 * Returns a subset of options formatted for use in wp_localize_script()
 * 
 * @return array Configuration array for JavaScript
 * @since 1.2.0
 */
function archi_get_graph_js_config() {
    $options = archi_get_graph_options();
    
    // Return only options needed by JavaScript
    return [
        'animationDuration'   => $options['animation_duration'],
        'animationType'       => $options['animation_type'],
        'animationEnabled'    => $options['animation_enabled'],
        'nodeSpacing'         => $options['node_spacing'],
        'nodeDefaultColor'    => $options['node_default_color'],
        'nodeDefaultSize'     => $options['node_default_size'],
        'clusterStrength'     => $options['cluster_strength'],
        'linkStrength'        => $options['link_strength'],
        'hoverEffect'         => $options['hover_effect'],
        'hoverScale'          => $options['hover_scale'],
        'showLabels'          => $options['show_labels'],
        'organicMode'         => $options['organic_mode'],
        'maxArticles'         => $options['max_articles'],
    ];
}

// ============================================================================
// DEPRECATED FUNCTIONS (for backward compatibility)
// These will be removed in version 2.0.0
// ============================================================================

/**
 * @deprecated 1.2.0 Use archi_get_graph_options() instead
 */
function archi_get_graph_config() {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        trigger_error(
            'archi_get_graph_config() is deprecated since version 1.2.0. Use archi_get_graph_options() instead.',
            E_USER_DEPRECATED
        );
    }
    
    $options = archi_get_graph_options();
    
    // Return old format for backward compatibility
    return [
        'animation_duration' => $options['animation_duration'],
        'node_spacing'       => $options['node_spacing'],
        'cluster_strength'   => $options['cluster_strength'],
        'enabled_post_types' => $options['enabled_post_types'],
    ];
}

/**
 * @deprecated 1.2.0 Use archi_get_graph_options() instead
 */
function archi_get_all_graph_options() {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        trigger_error(
            'archi_get_all_graph_options() is deprecated since version 1.2.0. Use archi_get_graph_options() instead.',
            E_USER_DEPRECATED
        );
    }
    
    $options = archi_get_graph_options();
    
    // Return old format for backward compatibility
    return [
        'graph_animation_duration'   => $options['animation_duration'],
        'graph_node_spacing'         => $options['node_spacing'],
        'graph_cluster_strength'     => $options['cluster_strength'],
        'graph_show_categories'      => true, // Legacy option
        'graph_show_links'           => true, // Legacy option
        'graph_auto_save_positions'  => $options['auto_save_positions'],
        'graph_max_articles'         => $options['max_articles'],
        'default_node_color'         => $options['node_default_color'],
        'background_gradient_start'  => $options['background_gradient_start'],
        'background_gradient_end'    => $options['background_gradient_end'],
        'cache_duration'             => $options['cache_duration'],
    ];
}
