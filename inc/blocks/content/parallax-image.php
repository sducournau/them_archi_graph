<?php
/**
 * Bloc Image Parallax Universel
 * Combine toutes les fonctionnalités parallax en un seul bloc optimisé
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistrement du bloc Image Parallax
 */
function archi_register_parallax_image_block() {
    register_block_type('archi-graph/parallax-image', [
        'attributes' => [
            // Image
            'imageUrl' => [
                'type' => 'string',
                'default' => ''
            ],
            'imageId' => [
                'type' => 'number'
            ],
            'imageAlt' => [
                'type' => 'string',
                'default' => ''
            ],
            
            // Dimensions
            'heightMode' => [
                'type' => 'string',
                'default' => 'custom'
            ],
            'customHeight' => [
                'type' => 'number',
                'default' => 600
            ],
            
            // Effet Parallax
            'parallaxEffect' => [
                'type' => 'string',
                'default' => 'fixed'
            ],
            'parallaxSpeed' => [
                'type' => 'number',
                'default' => 0.5
            ],
            'enableZoom' => [
                'type' => 'boolean',
                'default' => false
            ],
            
            // Ajustement Image
            'objectFit' => [
                'type' => 'string',
                'default' => 'cover'
            ],
            
            // Overlay
            'overlayEnabled' => [
                'type' => 'boolean',
                'default' => false
            ],
            'overlayColor' => [
                'type' => 'string',
                'default' => '#000000'
            ],
            'overlayOpacity' => [
                'type' => 'number',
                'default' => 30
            ],
            
            // Texte
            'textEnabled' => [
                'type' => 'boolean',
                'default' => false
            ],
            'textContent' => [
                'type' => 'string',
                'default' => ''
            ],
            'textPosition' => [
                'type' => 'string',
                'default' => 'center'
            ],
            'textColor' => [
                'type' => 'string',
                'default' => '#ffffff'
            ]
        ],
        'render_callback' => 'archi_render_parallax_image_block',
        'editor_script' => 'archi-parallax-image',
        'editor_style' => 'archi-blocks-editor',
        'style' => 'archi-blocks'
    ]);
}

/**
 * Rendu côté serveur du bloc
 */
function archi_render_parallax_image_block($attributes) {
    // Récupération des attributs
    $image_url = isset($attributes['imageUrl']) ? esc_url($attributes['imageUrl']) : '';
    $image_alt = isset($attributes['imageAlt']) ? esc_attr($attributes['imageAlt']) : '';
    $height_mode = isset($attributes['heightMode']) ? esc_attr($attributes['heightMode']) : 'custom';
    $custom_height = isset($attributes['customHeight']) ? absint($attributes['customHeight']) : 600;
    $parallax_effect = isset($attributes['parallaxEffect']) ? esc_attr($attributes['parallaxEffect']) : 'fixed';
    $parallax_speed = isset($attributes['parallaxSpeed']) ? floatval($attributes['parallaxSpeed']) : 0.5;
    $enable_zoom = isset($attributes['enableZoom']) ? (bool)$attributes['enableZoom'] : false;
    $object_fit = isset($attributes['objectFit']) ? esc_attr($attributes['objectFit']) : 'cover';
    
    $overlay_enabled = isset($attributes['overlayEnabled']) ? (bool)$attributes['overlayEnabled'] : false;
    $overlay_color = isset($attributes['overlayColor']) ? esc_attr($attributes['overlayColor']) : '#000000';
    $overlay_opacity = isset($attributes['overlayOpacity']) ? absint($attributes['overlayOpacity']) : 30;
    
    $text_enabled = isset($attributes['textEnabled']) ? (bool)$attributes['textEnabled'] : false;
    $text_content = isset($attributes['textContent']) ? wp_kses_post($attributes['textContent']) : '';
    $text_position = isset($attributes['textPosition']) ? esc_attr($attributes['textPosition']) : 'center';
    $text_color = isset($attributes['textColor']) ? esc_attr($attributes['textColor']) : '#ffffff';
    
    if (empty($image_url)) {
        return '';
    }
    
    // Construction des classes CSS
    $classes = ['archi-parallax-image'];
    $classes[] = 'height-' . $height_mode;
    $classes[] = 'parallax-' . $parallax_effect;
    $classes[] = 'object-fit-' . $object_fit;
    
    if ($parallax_effect === 'zoom' && $enable_zoom) {
        $classes[] = 'has-zoom';
    }
    
    // Styles inline
    $container_styles = [];
    if ($height_mode === 'custom') {
        $container_styles[] = 'height: ' . $custom_height . 'px';
    }
    
    // Data attributes pour JS
    $data_attrs = [
        'data-parallax-effect' => $parallax_effect,
    ];
    
    if ($parallax_effect === 'scroll') {
        $data_attrs['data-parallax-speed'] = $parallax_speed;
    }
    
    ob_start();
    ?>
    <section 
        class="<?php echo esc_attr(implode(' ', $classes)); ?>"
        <?php if (!empty($container_styles)) : ?>
            style="<?php echo esc_attr(implode('; ', $container_styles)); ?>"
        <?php endif; ?>
        <?php foreach ($data_attrs as $key => $value) : ?>
            <?php echo esc_attr($key); ?>="<?php echo esc_attr($value); ?>"
        <?php endforeach; ?>
    >
        <div class="fullsize-image-container">
            <div class="image-wrapper">
                <img 
                    src="<?php echo $image_url; ?>" 
                    alt="<?php echo $image_alt; ?>"
                    class="parallax-image"
                    loading="lazy"
                />
            </div>
            
            <?php if ($overlay_enabled) : ?>
                <div 
                    class="image-overlay"
                    style="background-color: <?php echo $overlay_color; ?>; opacity: <?php echo ($overlay_opacity / 100); ?>;"
                ></div>
            <?php endif; ?>
            
            <?php if ($text_enabled && !empty($text_content)) : ?>
                <div 
                    class="image-text text-position-<?php echo $text_position; ?>"
                    style="color: <?php echo $text_color; ?>;"
                >
                    <?php echo $text_content; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
    
    return ob_get_clean();
}
