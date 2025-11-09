<?php
/**
 * Bloc Couverture avec Image
 * Similaire au bloc Cover WordPress natif
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistrement du bloc Couverture
 */
function archi_register_cover_block() {
    register_block_type('archi-graph/cover-block', [
        'attributes' => [
            'imageUrl' => [
                'type' => 'string',
                'default' => ''
            ],
            'imageId' => [
                'type' => 'number'
            ],
            'title' => [
                'type' => 'string',
                'default' => ''
            ],
            'subtitle' => [
                'type' => 'string',
                'default' => ''
            ],
            'overlayOpacity' => [
                'type' => 'number',
                'default' => 50
            ],
            'overlayColor' => [
                'type' => 'string',
                'default' => '#000000'
            ],
            'minHeight' => [
                'type' => 'number',
                'default' => 400
            ],
            'contentPosition' => [
                'type' => 'string',
                'default' => 'center'
            ],
            'hasParallax' => [
                'type' => 'boolean',
                'default' => false
            ]
        ],
        'render_callback' => 'archi_render_cover_block',
        'editor_script' => 'archi-cover-block',
        'editor_style' => 'archi-blocks-editor',
        'style' => 'archi-blocks'
    ]);
}
// Note: Appelé automatiquement par le loader
// add_action('init', 'archi_register_cover_block');

/**
 * Rendu côté serveur du bloc Couverture
 */
function archi_render_cover_block($attributes) {
    $image_url = isset($attributes['imageUrl']) ? esc_url($attributes['imageUrl']) : '';
    $title = isset($attributes['title']) ? wp_kses_post($attributes['title']) : '';
    $subtitle = isset($attributes['subtitle']) ? wp_kses_post($attributes['subtitle']) : '';
    $overlay_opacity = isset($attributes['overlayOpacity']) ? absint($attributes['overlayOpacity']) : 50;
    $overlay_color = isset($attributes['overlayColor']) ? esc_attr($attributes['overlayColor']) : '#000000';
    $min_height = isset($attributes['minHeight']) ? absint($attributes['minHeight']) : 400;
    $content_position = isset($attributes['contentPosition']) ? esc_attr($attributes['contentPosition']) : 'center';
    $has_parallax = isset($attributes['hasParallax']) && $attributes['hasParallax'];
    
    if (empty($image_url)) {
        return '';
    }
    
    // Construire les classes CSS
    $classes = ['wp-block-cover', 'archi-cover-block'];
    
    // Classe pour l'alignement du contenu
    if ($content_position === 'top') {
        $classes[] = 'has-custom-content-position';
        $classes[] = 'is-position-top-center';
    } elseif ($content_position === 'bottom') {
        $classes[] = 'has-custom-content-position';
        $classes[] = 'is-position-bottom-center';
    } else {
        $classes[] = 'is-position-center-center';
    }
    
    // Classe pour le parallax
    if ($has_parallax) {
        $classes[] = 'has-parallax';
    }
    
    // Classe pour l'opacité de l'overlay
    $opacity_class = 'has-background-dim';
    if ($overlay_opacity !== 50) {
        $opacity_class .= ' has-background-dim-' . $overlay_opacity;
    }
    
    ob_start();
    ?>
    <div 
        class="<?php echo esc_attr(implode(' ', $classes)); ?>"
        style="min-height: <?php echo $min_height; ?>px;"
    >
        <span 
            aria-hidden="true" 
            class="wp-block-cover__background <?php echo esc_attr($opacity_class); ?>"
            style="background-color: <?php echo $overlay_color; ?>;"
        ></span>
        
        <?php if ($image_url) : ?>
            <img 
                class="wp-block-cover__image-background" 
                alt="" 
                src="<?php echo $image_url; ?>" 
                style="object-fit: cover;"
                data-object-fit="cover"
            />
        <?php endif; ?>
        
        <div class="wp-block-cover__inner-container is-layout-flow wp-block-cover-is-layout-flow">
            <?php if (!empty($title)) : ?>
                <h2 class="wp-block-heading has-text-align-center cover-title">
                    <?php echo $title; ?>
                </h2>
            <?php endif; ?>
            
            <?php if (!empty($subtitle)) : ?>
                <p class="has-text-align-center cover-subtitle">
                    <?php echo $subtitle; ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
