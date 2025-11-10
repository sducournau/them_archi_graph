<?php
/**
 * Graph Settings Admin Page
 * 
 * Simple admin interface for managing graph visual effects presets
 * 
 * @package Archi_Graph
 * @since 1.3.1
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add graph settings menu
 */
function archi_add_graph_settings_menu() {
    add_theme_page(
        __('Graph Settings', 'archi-graph'),
        __('Graph Settings', 'archi-graph'),
        'manage_options',
        'archi-graph-settings',
        'archi_render_graph_settings_page'
    );
}
add_action('admin_menu', 'archi_add_graph_settings_menu');

/**
 * Render graph settings page
 */
function archi_render_graph_settings_page() {
    // Save settings
    if (isset($_POST['archi_graph_preset']) && check_admin_referer('archi_graph_settings')) {
        $preset = sanitize_text_field($_POST['archi_graph_preset']);
        archi_visual_save_preset($preset);
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Settings saved successfully!', 'archi-graph') . '</p></div>';
    }
    
    $current_preset = get_option('archi_graph_preset', 'standard');
    $presets = archi_visual_get_presets();
    $current_config = archi_visual_get_current_config();
    $expanded_config = archi_visual_expand_config($current_config);
    
    ?>
    <div class="wrap">
        <h1><?php _e('Graph Visual Effects Settings', 'archi-graph'); ?></h1>
        
        <div class="archi-graph-settings">
            <form method="post" action="">
                <?php wp_nonce_field('archi_graph_settings'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="archi_graph_preset"><?php _e('Visual Preset', 'archi-graph'); ?></label>
                        </th>
                        <td>
                            <select name="archi_graph_preset" id="archi_graph_preset" class="regular-text" onchange="updatePreview(this.value)">
                                <?php foreach ($presets as $key => $preset): ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($current_preset, $key); ?>>
                                        <?php echo esc_html($preset['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description" id="preset-description">
                                <?php echo esc_html($presets[$current_preset]['description']); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <h2><?php _e('Preset Details', 'archi-graph'); ?></h2>
                <div class="archi-preset-details">
                    <?php foreach ($presets as $key => $preset): ?>
                        <div class="preset-card <?php echo $current_preset === $key ? 'active' : ''; ?>" data-preset="<?php echo esc_attr($key); ?>">
                            <h3><?php echo esc_html($preset['name']); ?></h3>
                            <p class="description"><?php echo esc_html($preset['description']); ?></p>
                            <ul class="preset-features">
                                <?php foreach ($preset['settings'] as $setting_key => $setting_value): ?>
                                    <li>
                                        <strong><?php echo esc_html(ucwords(str_replace('_', ' ', $setting_key))); ?>:</strong>
                                        <?php 
                                        if (is_bool($setting_value)) {
                                            echo $setting_value ? '<span class="dashicons dashicons-yes-alt" style="color: green;"></span>' : '<span class="dashicons dashicons-dismiss" style="color: red;"></span>';
                                        } else {
                                            echo esc_html($setting_value);
                                        }
                                        ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <h2><?php _e('Current Configuration', 'archi-graph'); ?></h2>
                <div class="archi-config-preview">
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php _e('Category', 'archi-graph'); ?></th>
                                <th><?php _e('Settings', 'archi-graph'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $categories = [
                                'visual' => __('Visual Appearance', 'archi-graph'),
                                'animation' => __('Animation', 'archi-graph'),
                                'hover' => __('Hover Effects', 'archi-graph'),
                                'inactive' => __('Inactive Nodes', 'archi-graph'),
                                'click' => __('Click Interactions', 'archi-graph'),
                                'links' => __('Links', 'archi-graph'),
                                'physics' => __('Physics Simulation', 'archi-graph'),
                                'performance' => __('Performance', 'archi-graph'),
                            ];
                            
                            foreach ($categories as $cat_key => $cat_label):
                                if (isset($current_config[$cat_key])):
                            ?>
                                <tr>
                                    <td><strong><?php echo esc_html($cat_label); ?></strong></td>
                                    <td>
                                        <ul style="margin: 0; padding-left: 20px;">
                                            <?php foreach ($current_config[$cat_key] as $key => $value): ?>
                                                <li>
                                                    <code><?php echo esc_html($key); ?></code>: 
                                                    <strong>
                                                        <?php 
                                                        if (is_bool($value)) {
                                                            echo $value ? 'true' : 'false';
                                                        } else if (is_array($value)) {
                                                            echo '[array]';
                                                        } else {
                                                            echo esc_html($value);
                                                        }
                                                        ?>
                                                    </strong>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                </tr>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <?php submit_button(__('Save Settings', 'archi-graph')); ?>
            </form>
        </div>
    </div>
    
    <style>
        .archi-graph-settings {
            max-width: 1200px;
        }
        
        .archi-preset-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .preset-card {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background: #f9f9f9;
            display: none;
        }
        
        .preset-card.active {
            display: block;
            border-color: #2271b1;
            background: #f0f6fc;
        }
        
        .preset-card h3 {
            margin-top: 0;
            color: #2271b1;
        }
        
        .preset-features {
            list-style: none;
            padding: 0;
            margin: 10px 0 0 0;
        }
        
        .preset-features li {
            padding: 5px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .preset-features li:last-child {
            border-bottom: none;
        }
        
        .archi-config-preview {
            margin: 20px 0;
        }
        
        .archi-config-preview table {
            margin-top: 10px;
        }
        
        .archi-config-preview code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
        }
    </style>
    
    <script>
        const presetDescriptions = <?php echo json_encode(array_map(function($p) { return $p['description']; }, $presets)); ?>;
        
        function updatePreview(presetKey) {
            // Update description
            const description = presetDescriptions[presetKey];
            document.getElementById('preset-description').textContent = description;
            
            // Update active card
            document.querySelectorAll('.preset-card').forEach(card => {
                card.classList.remove('active');
            });
            document.querySelector(`.preset-card[data-preset="${presetKey}"]`).classList.add('active');
        }
    </script>
    <?php
}
