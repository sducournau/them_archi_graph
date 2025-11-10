<?php
/**
 * Image Consolidated Block - TEMPORARILY DISABLED FOR DEBUGGING
 */

if (!defined('ABSPATH')) {
    exit;
}

// Bloc temporairement désactivé pour diagnostic
// Une fois le site fonctionnel, nous réactiverons progressivement
return;

function archi_register_image_consolidated_block() {
    // Check if block editor is available
    if (!function_exists('register_block_type')) {
        return;
    }
    
    register_block_type('archi-graph/image-consolidated', array(
        'render_callback' => 'archi_render_image_consolidated_block',
        'attributes' => array(
            'comparisonMode' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'beforeImage' => array(
                'type' => 'object',
                'default' => array()
            ),
            'afterImage' => array(
                'type' => 'object',
                'default' => array()
            ),
            'sliderOrientation' => array(
                'type' => 'string',
                'default' => 'vertical'
            ),
            'initialPosition' => array(
                'type' => 'number',
                'default' => 50
            ),
            'showLabels' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'beforeLabel' => array(
                'type' => 'string',
                'default' => 'Avant'
            ),
            'afterLabel' => array(
                'type' => 'string',
                'default' => 'Après'
            )
        )
    ));
}
add_action('init', 'archi_register_image_consolidated_block');

function archi_render_image_consolidated_block($attributes) {
    // Si pas de mode comparaison, ne rien afficher
    $comparison_mode = isset($attributes['comparisonMode']) ? $attributes['comparisonMode'] : false;
    
    if (!$comparison_mode) {
        return '';
    }
    
    // Vérifier les images
    $before_image = isset($attributes['beforeImage']) ? $attributes['beforeImage'] : array();
    $after_image = isset($attributes['afterImage']) ? $attributes['afterImage'] : array();
    
    // Si les images ne sont pas configurées, ne rien afficher
    if (empty($before_image) || empty($after_image) || 
        !isset($before_image['url']) || !isset($after_image['url'])) {
        return '';
    }
    
    // Enqueue assets seulement s'ils sont enregistrés
    if (wp_style_is('archi-comparison-slider', 'registered')) {
        wp_enqueue_style('archi-comparison-slider');
    }
    if (wp_script_is('archi-comparison-slider', 'registered')) {
        wp_enqueue_script('archi-comparison-slider');
    }
    
    $orientation = isset($attributes['sliderOrientation']) ? $attributes['sliderOrientation'] : 'vertical';
    $initial_position = isset($attributes['initialPosition']) ? intval($attributes['initialPosition']) : 50;
    $show_labels = isset($attributes['showLabels']) ? $attributes['showLabels'] : true;
    $before_label = isset($attributes['beforeLabel']) ? $attributes['beforeLabel'] : 'Avant';
    $after_label = isset($attributes['afterLabel']) ? $attributes['afterLabel'] : 'Après';
    
    $orientation_class = $orientation === 'horizontal' ? 'horizontal-mode' : '';
    
    // Get alt text safely
    $before_alt = isset($before_image['alt']) ? $before_image['alt'] : '';
    $after_alt = isset($after_image['alt']) ? $after_image['alt'] : '';
    
    ob_start();
    ?>
    <div class="archi-image-comparison-container">
        <div class="archi-image-comparison-slider <?php echo esc_attr($orientation_class); ?>" 
             data-orientation="<?php echo esc_attr($orientation); ?>"
             data-initial-position="<?php echo esc_attr($initial_position); ?>">
            <img src="<?php echo esc_url($before_image['url']); ?>" 
                 alt="<?php echo esc_attr($before_alt); ?>" 
                 class="archi-comparison-before">
            <img src="<?php echo esc_url($after_image['url']); ?>" 
                 alt="<?php echo esc_attr($after_alt); ?>" 
                 class="archi-comparison-after">
        </div>
        <?php if ($show_labels): ?>
            <div class="archi-comparison-labels">
                <span class="archi-label-before"><?php echo esc_html($before_label); ?></span>
                <span class="archi-label-after"><?php echo esc_html($after_label); ?></span>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}