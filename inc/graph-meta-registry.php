<?php
/**
 * Graph Meta Registry - Consolidated Graph Parameter Registration
 * 
 * This file consolidates ALL graph-related metadata registration
 * Previously split between meta-boxes.php and advanced-graph-settings.php
 * 
 * @package ArchiGraph
 * @since 1.0.0
 */

// Security: Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register all graph-related metadata for posts
 * 
 * Registers metadata for all post types that can appear in the graph:
 * - post (standard WordPress posts)
 * - archi_project (architectural projects)
 * - archi_illustration (illustrations)
 * 
 * Meta fields are organized into categories:
 * 1. Core Graph Settings
 * 2. Node Visual Properties
 * 3. Node Behavior & Animation
 * 4. Link & Relationship Settings
 * 
 * @return void
 */
function archi_register_all_graph_meta() {
    $post_types = ['post', 'archi_project', 'archi_illustration'];
    
    foreach ($post_types as $post_type) {
        
        // ========================================
        // 1. CORE GRAPH SETTINGS
        // ========================================
        
        /**
         * Visibility in graph
         * @var string '0' or '1'
         */
        register_post_meta($post_type, '_archi_show_in_graph', [
            'type' => 'string',
            'single' => true,
            'default' => '0',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                return $value === '1' ? '1' : '0';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Priority level in graph
         * @var string 'low'|'normal'|'high'|'featured'
         */
        register_post_meta($post_type, '_archi_priority_level', [
            'type' => 'string',
            'single' => true,
            'default' => 'normal',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $allowed = ['low', 'normal', 'high', 'featured'];
                return in_array($value, $allowed) ? $value : 'normal';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Position in graph (x, y coordinates)
         * @var array ['x' => float, 'y' => float]
         */
        register_post_meta($post_type, '_archi_graph_position', [
            'type' => 'array',
            'single' => true,
            'default' => [],
            'sanitize_callback' => function($value) {
                if (!is_array($value)) {
                    return [];
                }
                return [
                    'x' => isset($value['x']) ? floatval($value['x']) : 0,
                    'y' => isset($value['y']) ? floatval($value['y']) : 0
                ];
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Pin node (fixed position)
         * @var string '0' or '1'
         */
        register_post_meta($post_type, '_archi_pin_node', [
            'type' => 'string',
            'single' => true,
            'default' => '0',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                return $value === '1' ? '1' : '0';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Visual group for node clustering
         * @var string
         */
        register_post_meta($post_type, '_archi_visual_group', [
            'type' => 'string',
            'single' => true,
            'default' => '',
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        // ========================================
        // 2. NODE VISUAL PROPERTIES
        // ========================================
        
        /**
         * Node color (hex format)
         * @var string #RRGGBB
         */
        register_post_meta($post_type, '_archi_node_color', [
            'type' => 'string',
            'single' => true,
            'default' => '#3498db',
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_hex_color',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Node size in pixels
         * @var int 40-500 (depending on post type)
         */
        register_post_meta($post_type, '_archi_node_size', [
            'type' => 'integer',
            'single' => true,
            'default' => 60,
            'show_in_rest' => true,
            'sanitize_callback' => function($value) use ($post_type) {
                $size = absint($value);
                
                // Validation according to post type
                $min_size = 40;
                $max_size = 500;
                
                if ($post_type === 'archi_project') {
                    $min_size = 60;
                    $max_size = 500;
                }
                
                // Return default if out of bounds
                if ($size < $min_size || $size > $max_size) {
                    return 60;
                }
                
                return $size;
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Node shape
         * @var string 'circle'|'square'|'diamond'|'triangle'|'star'|'hexagon'
         */
        register_post_meta($post_type, '_archi_node_shape', [
            'type' => 'string',
            'single' => true,
            'default' => 'circle',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $allowed = ['circle', 'square', 'diamond', 'triangle', 'star', 'hexagon'];
                return in_array($value, $allowed) ? $value : 'circle';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Node icon (emoji or unicode character)
         * @var string Max 2 characters
         */
        register_post_meta($post_type, '_archi_node_icon', [
            'type' => 'string',
            'single' => true,
            'default' => '',
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Node opacity (0.1 to 1.0)
         * @var float
         */
        register_post_meta($post_type, '_archi_node_opacity', [
            'type' => 'number',
            'single' => true,
            'default' => 1.0,
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $opacity = floatval($value);
                return max(0.1, min(1.0, $opacity));
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Border effect
         * @var string 'none'|'solid'|'dashed'|'dotted'|'glow'
         */
        register_post_meta($post_type, '_archi_node_border', [
            'type' => 'string',
            'single' => true,
            'default' => 'none',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $allowed = ['none', 'solid', 'dashed', 'dotted', 'glow'];
                return in_array($value, $allowed) ? $value : 'none';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Border color
         * @var string #RRGGBB
         */
        register_post_meta($post_type, '_archi_border_color', [
            'type' => 'string',
            'single' => true,
            'default' => '',
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_hex_color',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Custom label (max 20 characters)
         * @var string
         */
        register_post_meta($post_type, '_archi_node_label', [
            'type' => 'string',
            'single' => true,
            'default' => '',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                return substr(sanitize_text_field($value), 0, 20);
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Always show label
         * @var string '0' or '1'
         */
        register_post_meta($post_type, '_archi_show_label', [
            'type' => 'string',
            'single' => true,
            'default' => '0',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                return $value === '1' ? '1' : '0';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Visual badge/tag
         * @var string ''|'new'|'featured'|'hot'|'updated'|'popular'
         */
        register_post_meta($post_type, '_archi_node_badge', [
            'type' => 'string',
            'single' => true,
            'default' => '',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $allowed = ['', 'new', 'featured', 'hot', 'updated', 'popular'];
                return in_array($value, $allowed) ? $value : '';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        // ========================================
        // 3. NODE BEHAVIOR & ANIMATION
        // ========================================
        
        /**
         * Node weight for physics simulation (1-10)
         * @var int
         */
        register_post_meta($post_type, '_archi_node_weight', [
            'type' => 'integer',
            'single' => true,
            'default' => 1,
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $weight = absint($value);
                return max(1, min(10, $weight));
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Hover effect animation
         * @var string 'none'|'zoom'|'pulse'|'glow'|'rotate'|'bounce'
         */
        register_post_meta($post_type, '_archi_hover_effect', [
            'type' => 'string',
            'single' => true,
            'default' => 'zoom',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $allowed = ['none', 'zoom', 'pulse', 'glow', 'rotate', 'bounce'];
                return in_array($value, $allowed) ? $value : 'zoom';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Entrance animation
         * @var string 'none'|'fade'|'scale'|'slide'|'bounce'
         */
        register_post_meta($post_type, '_archi_entrance_animation', [
            'type' => 'string',
            'single' => true,
            'default' => 'fade',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $allowed = ['none', 'fade', 'scale', 'slide', 'bounce'];
                return in_array($value, $allowed) ? $value : 'fade';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Animation intensity level
         * @var string 'none'|'subtle'|'normal'|'intense'
         */
        register_post_meta($post_type, '_archi_animation_level', [
            'type' => 'string',
            'single' => true,
            'default' => 'normal',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $allowed = ['none', 'subtle', 'normal', 'intense'];
                return in_array($value, $allowed) ? $value : 'normal';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Animation duration in milliseconds
         * @var integer 0-5000ms
         */
        register_post_meta($post_type, '_archi_animation_duration', [
            'type' => 'integer',
            'single' => true,
            'default' => 800,
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $duration = absint($value);
                return ($duration >= 0 && $duration <= 5000) ? $duration : 800;
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Animation delay in milliseconds
         * @var integer 0-5000ms
         */
        register_post_meta($post_type, '_archi_animation_delay', [
            'type' => 'integer',
            'single' => true,
            'default' => 0,
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $delay = absint($value);
                return ($delay >= 0 && $delay <= 5000) ? $delay : 0;
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Animation easing function
         * @var string CSS/D3 easing function
         */
        register_post_meta($post_type, '_archi_animation_easing', [
            'type' => 'string',
            'single' => true,
            'default' => 'ease-out',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $allowed = [
                    'linear', 'ease', 'ease-in', 'ease-out', 'ease-in-out',
                    'cubic-bezier', 'elastic', 'bounce'
                ];
                return in_array($value, $allowed) ? $value : 'ease-out';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Hover scale multiplier
         * @var float 1.0-2.0
         */
        register_post_meta($post_type, '_archi_hover_scale', [
            'type' => 'number',
            'single' => true,
            'default' => 1.15,
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $scale = floatval($value);
                return ($scale >= 1.0 && $scale <= 2.0) ? $scale : 1.15;
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Continuous pulse effect
         * @var string '0' or '1'
         */
        register_post_meta($post_type, '_archi_pulse_effect', [
            'type' => 'string',
            'single' => true,
            'default' => '0',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                return $value === '1' ? '1' : '0';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Glow effect on hover
         * @var string '0' or '1'
         */
        register_post_meta($post_type, '_archi_glow_effect', [
            'type' => 'string',
            'single' => true,
            'default' => '0',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                return $value === '1' ? '1' : '0';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Entry direction for animation
         * @var string 'center'|'top'|'bottom'|'left'|'right'
         */
        register_post_meta($post_type, '_archi_enter_from', [
            'type' => 'string',
            'single' => true,
            'default' => 'center',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $allowed = ['center', 'top', 'bottom', 'left', 'right'];
                return in_array($value, $allowed) ? $value : 'center';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        // ========================================
        // 4. LINK & RELATIONSHIP SETTINGS
        // ========================================
        
        /**
         * Manual related articles (array of post IDs)
         * @var array
         */
        register_post_meta($post_type, '_archi_related_articles', [
            'type' => 'array',
            'single' => true,
            'default' => [],
            'sanitize_callback' => function($value) {
                if (!is_array($value)) {
                    return [];
                }
                return array_map('absint', $value);
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Hide links from this node
         * @var string '0' or '1'
         */
        register_post_meta($post_type, '_archi_hide_links', [
            'type' => 'string',
            'single' => true,
            'default' => '0',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                return $value === '1' ? '1' : '0';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Connection depth (levels of links to show: 1-5)
         * @var int
         */
        register_post_meta($post_type, '_archi_connection_depth', [
            'type' => 'integer',
            'single' => true,
            'default' => 2,
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $depth = absint($value);
                return max(1, min(5, $depth));
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Link strength/thickness multiplier (0.1-3.0)
         * @var float
         */
        register_post_meta($post_type, '_archi_link_strength', [
            'type' => 'number',
            'single' => true,
            'default' => 1.0,
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $strength = floatval($value);
                return max(0.1, min(3.0, $strength));
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        /**
         * Link visual style
         * @var string 'straight'|'curve'|'wave'|'dotted'|'dashed'
         */
        register_post_meta($post_type, '_archi_link_style', [
            'type' => 'string',
            'single' => true,
            'default' => 'curve',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                $allowed = ['straight', 'curve', 'wave', 'dotted', 'dashed'];
                return in_array($value, $allowed) ? $value : 'curve';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
    }
}
add_action('init', 'archi_register_all_graph_meta');

/**
 * Get all registered graph meta keys
 * Useful for bulk operations and validation
 * 
 * @return array Associative array of meta keys grouped by category
 */
function archi_get_graph_meta_keys() {
    return [
        'core' => [
            '_archi_show_in_graph',
            '_archi_priority_level',
            '_archi_graph_position',
            '_archi_pin_node',
            '_archi_visual_group',
        ],
        'visual' => [
            '_archi_node_color',
            '_archi_node_size',
            '_archi_node_shape',
            '_archi_node_icon',
            '_archi_node_opacity',
            '_archi_node_border',
            '_archi_border_color',
            '_archi_node_label',
            '_archi_show_label',
            '_archi_node_badge',
        ],
        'behavior' => [
            '_archi_node_weight',
            '_archi_hover_effect',
            '_archi_entrance_animation',
            '_archi_animation_level',
            '_archi_animation_type',
            '_archi_animation_duration',
            '_archi_animation_delay',
            '_archi_animation_easing',
            '_archi_enter_from',
            '_archi_hover_scale',
            '_archi_pulse_effect',
            '_archi_glow_effect',
        ],
        'links' => [
            '_archi_related_articles',
            '_archi_hide_links',
            '_archi_connection_depth',
            '_archi_link_strength',
            '_archi_link_style',
        ],
    ];
}

/**
 * Get default values for all graph meta
 * 
 * @return array Associative array of meta keys and their default values
 */
function archi_get_graph_meta_defaults() {
    return [
        '_archi_show_in_graph' => '0',
        '_archi_priority_level' => 'normal',
        '_archi_graph_position' => [],
        '_archi_pin_node' => '0',
        '_archi_visual_group' => '',
        '_archi_node_color' => '#3498db',
        '_archi_node_size' => 80, // 🔥 FIX: Harmonized to 80px (was 60)
        '_archi_node_shape' => 'circle',
        '_archi_node_icon' => '',
        '_archi_node_opacity' => 1.0,
        '_archi_node_border' => 'none',
        '_archi_border_color' => '',
        '_archi_node_label' => '',
        '_archi_show_label' => '0',
        '_archi_node_badge' => '',
        '_archi_node_weight' => 1,
        '_archi_hover_effect' => 'zoom',
        '_archi_entrance_animation' => 'fade',
        '_archi_animation_level' => 'normal',
        '_archi_animation_type' => 'fadeIn',
        '_archi_animation_duration' => 800,
        '_archi_animation_delay' => 0,
        '_archi_animation_easing' => 'ease-out',
        '_archi_enter_from' => 'center',
        '_archi_hover_scale' => 1.15,
        '_archi_pulse_effect' => '0',
        '_archi_glow_effect' => '0',
        '_archi_related_articles' => [],
        '_archi_hide_links' => '0',
        '_archi_connection_depth' => 2,
        '_archi_link_strength' => 1.0,
        '_archi_link_style' => 'curve',
    ];
}

/**
 * Get all graph parameters for a post (UNIFIED INTERFACE)
 * 
 * This function provides a single source of truth for retrieving all graph parameters
 * for any post. It returns a consistent structure with proper type conversion and defaults.
 * 
 * @since 1.1.0
 * @param int $post_id Post ID
 * @param bool $include_defaults Include default values for missing params (default: true)
 * @return array All graph parameters with frontend-friendly keys
 * 
 * @example
 * $params = archi_get_graph_params(123);
 * echo $params['node_color']; // '#3498db'
 * echo $params['node_size']; // 60
 * echo $params['priority_level']; // 'normal'
 */
function archi_get_graph_params($post_id, $include_defaults = true) {
    $post = get_post($post_id);
    if (!$post) {
        return [];
    }
    
    // Determine default color based on post type
    $default_color = '#3498db'; // Blue for posts
    if ($post->post_type === 'archi_project') {
        $default_color = '#e67e22'; // Orange for projects
    } elseif ($post->post_type === 'archi_illustration') {
        $default_color = '#9b59b6'; // Purple for illustrations
    }
    
    // Get defaults and override color with post-type specific default
    $defaults = $include_defaults ? archi_get_graph_meta_defaults() : [];
    if ($include_defaults) {
        $defaults['_archi_node_color'] = $default_color;
    }
    
    $params = [];
    $all_meta_keys = archi_get_graph_meta_keys();
    
    // Process all meta keys from registry
    foreach ($all_meta_keys as $category => $meta_keys) {
        foreach ($meta_keys as $meta_key) {
            $value = get_post_meta($post_id, $meta_key, true);
            
            // Use default if empty and defaults are requested
            if (($value === '' || $value === false || $value === null) && $include_defaults && isset($defaults[$meta_key])) {
                $value = $defaults[$meta_key];
            }
            
            // Convert meta key to frontend key (remove _archi_ prefix)
            $frontend_key = str_replace('_archi_', '', $meta_key);
            
            // Type conversion for proper JavaScript consumption
            if (in_array($meta_key, ['_archi_node_size', '_archi_node_weight', '_archi_connection_depth', '_archi_animation_duration', '_archi_animation_delay'])) {
                // Integer fields
                $params[$frontend_key] = intval($value);
            } elseif (in_array($meta_key, ['_archi_node_opacity', '_archi_link_strength', '_archi_hover_scale'])) {
                // Float fields
                $params[$frontend_key] = floatval($value);
            } elseif (in_array($meta_key, ['_archi_hide_links', '_archi_show_in_graph', '_archi_pin_node', '_archi_show_label', '_archi_pulse_effect', '_archi_glow_effect'])) {
                // Boolean fields (stored as '0' or '1' in WordPress)
                $params[$frontend_key] = $value === '1';
            } elseif ($meta_key === '_archi_related_articles') {
                // Array field - ensure it's an array and contains only integers
                if (!is_array($value)) {
                    $value = !empty($value) ? maybe_unserialize($value) : [];
                }
                if (!is_array($value)) {
                    $value = [];
                }
                $params[$frontend_key] = array_map('intval', array_filter($value, 'is_numeric'));
            } elseif ($meta_key === '_archi_graph_position') {
                // Array field - position coordinates
                if (!is_array($value)) {
                    $value = [];
                }
                $params[$frontend_key] = $value;
            } else {
                // String fields
                $params[$frontend_key] = $value;
            }
        }
    }
    
    return $params;
}

/**
 * Set graph parameters for a post (UNIFIED INTERFACE)
 * 
 * This function provides a single way to update graph parameters.
 * It handles validation and type conversion automatically.
 * 
 * @since 1.1.0
 * @param int $post_id Post ID
 * @param array $params Associative array of parameters (frontend keys)
 * @return array Array with 'success' boolean and 'updated' list of keys
 * 
 * @example
 * $result = archi_set_graph_params(123, [
 *     'node_color' => '#ff0000',
 *     'node_size' => 80,
 *     'priority_level' => 'high'
 * ]);
 */
function archi_set_graph_params($post_id, $params) {
    $post = get_post($post_id);
    if (!$post) {
        return [
            'success' => false,
            'error' => 'Post not found',
            'updated' => []
        ];
    }
    
    $updated = [];
    $all_meta_keys = archi_get_graph_meta_keys();
    
    // Build a flat list of all valid meta keys
    $valid_meta_keys = [];
    foreach ($all_meta_keys as $category_keys) {
        $valid_meta_keys = array_merge($valid_meta_keys, $category_keys);
    }
    
    foreach ($params as $key => $value) {
        // Convert frontend key to meta key
        $meta_key = '_archi_' . $key;
        
        // Check if this is a valid registered meta key
        if (!in_array($meta_key, $valid_meta_keys)) {
            continue; // Skip invalid keys
        }
        
        // Type conversion for storage
        if (is_bool($value)) {
            // Convert boolean to '0' or '1'
            $value = $value ? '1' : '0';
        }
        
        // Update the post meta
        update_post_meta($post_id, $meta_key, $value);
        $updated[] = $key;
    }
    
    // Invalidate cache when parameters are updated
    delete_transient('archi_graph_articles');
    
    return [
        'success' => true,
        'updated' => $updated,
        'count' => count($updated)
    ];
}
