<?php
/**
 * Bloc Image - Rendu côté serveur
 * 
 * Bloc universel pour gérer les variations d'images :
 * - Standard
 * - Parallax scroll
 * - Parallax fixed
 * - Zoom
 * - Cover avec overlay
 * 
 * @package Archi_Graph
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistre le bloc Image
 */
function archi_register_image_block() {
    register_block_type('archi-graph/image-block', [
        'api_version' => 2,
        'render_callback' => 'archi_render_image_block',
        'attributes' => [
            // Mode d'affichage
            'displayMode' => [
                'type' => 'string',
                'default' => 'standard'
            ],
            
            // Image principale
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
                'default' => 'auto'
            ],
            'customHeight' => [
                'type' => 'number',
                'default' => 600
            ],
            'objectFit' => [
                'type' => 'string',
                'default' => 'cover'
            ],
            
            // Parallax
            'parallaxSpeed' => [
                'type' => 'number',
                'default' => 0.5
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
            ],
            
            // Overlay presets
            'overlayPreset' => [
                'type' => 'string',
                'default' => 'none'
            ],
            
            // Preview settings
            'enableAnimatedPreview' => [
                'type' => 'boolean',
                'default' => true
            ],
            
            // Alignement
            'align' => [
                'type' => 'string',
                'default' => 'full'
            ]
        ]
    ]);
}
add_action('init', 'archi_register_image_block');

/**
 * Rend le bloc Image
 * 
 * @param array $attributes Attributs du bloc
 * @return string HTML du bloc
 */
function archi_render_image_block($attributes) {
    // Extraction des attributs avec valeurs par défaut
    $display_mode = $attributes['displayMode'] ?? 'standard';
    $image_url = $attributes['imageUrl'] ?? '';
    $image_alt = $attributes['imageAlt'] ?? '';
    
    $height_mode = $attributes['heightMode'] ?? 'auto';
    $custom_height = $attributes['customHeight'] ?? 600;
    $object_fit = $attributes['objectFit'] ?? 'cover';
    
    $parallax_speed = $attributes['parallaxSpeed'] ?? 0.5;
    
    $overlay_enabled = $attributes['overlayEnabled'] ?? false;
    $overlay_color = $attributes['overlayColor'] ?? '#000000';
    $overlay_opacity = $attributes['overlayOpacity'] ?? 30;
    $overlay_preset = $attributes['overlayPreset'] ?? 'none';
    
    $text_enabled = $attributes['textEnabled'] ?? false;
    $text_content = $attributes['textContent'] ?? '';
    $text_position = $attributes['textPosition'] ?? 'center';
    $text_color = $attributes['textColor'] ?? '#ffffff';
    
    $align = $attributes['align'] ?? 'full';
    
    // Vérification des images requises
    if (empty($image_url)) {
        return '';
    }
    
    // Génération de l'ID unique pour les scripts
    $block_id = 'archi-image-block-' . uniqid();
    
    // Classes CSS
    $classes = [
        'archi-image-block',
        'display-mode-' . $display_mode,
        'height-' . $height_mode,
        'object-fit-' . $object_fit
    ];
    
    if (!empty($align)) {
        $classes[] = 'align' . $align;
    }
    
    if ($overlay_enabled) {
        $classes[] = 'has-overlay';
        if ($overlay_preset !== 'none') {
            $classes[] = 'overlay-preset-' . $overlay_preset;
        }
    }
    
    if ($text_enabled) {
        $classes[] = 'has-text';
        $classes[] = 'text-position-' . $text_position;
    }
    
    if ($display_mode === 'gallery') {
        $classes[] = 'gallery-transition-' . $gallery_transition;
    }
    
    // Styles inline pour le conteneur
    $container_styles = [];
    if ($height_mode === 'custom') {
        $container_styles[] = 'height: ' . absint($custom_height) . 'px';
        $container_styles[] = 'min-height: ' . absint($custom_height) . 'px';
    } elseif ($height_mode === 'full-viewport') {
        $container_styles[] = 'height: 100vh';
        $container_styles[] = 'min-height: 100vh';
    }
    
    // Styles pour les éléments internes (container, wrapper, image)
    $inner_container_styles = [];
    $inner_image_styles = ['object-fit: ' . esc_attr($object_fit)];
    
    if ($height_mode === 'custom' || $height_mode === 'full-viewport') {
        $inner_container_styles[] = 'height: 100%';
        $inner_container_styles[] = 'min-height: 100%';
        $inner_image_styles[] = 'height: 100%';
        $inner_image_styles[] = 'width: 100%';
    }
    
    // Data attributes pour JavaScript
    $data_attrs = [
        'data-mode' => esc_attr($display_mode),
        'data-parallax-speed' => esc_attr($parallax_speed),
        'data-block-id' => esc_attr($block_id)
    ];
    
    // Début du output buffering
    ob_start();
    ?>
    
    <div 
        id="<?php echo esc_attr($block_id); ?>"
        class="<?php echo esc_attr(implode(' ', $classes)); ?>"
        <?php foreach ($data_attrs as $key => $value): ?>
            <?php echo $key . '="' . $value . '"'; ?>
        <?php endforeach; ?>
        <?php if (!empty($container_styles)): ?>
            style="<?php echo esc_attr(implode('; ', $container_styles)); ?>"
        <?php endif; ?>
    >
        <!-- Modes Standard/Parallax/Zoom/Cover -->
        <div class="image-block-container"<?php if (!empty($inner_container_styles)): ?> style="<?php echo esc_attr(implode('; ', $inner_container_styles)); ?>"<?php endif; ?>>
                <div class="image-wrapper"<?php if (!empty($inner_container_styles)): ?> style="<?php echo esc_attr(implode('; ', $inner_container_styles)); ?>"<?php endif; ?>>
                    <?php if ($display_mode === 'parallax-fixed'): ?>
                        <!-- Parallax Fixed utilise un div avec background-image -->
                        <?php 
                        $parallax_fixed_styles = ['background-image: url(' . esc_url($image_url) . ')'];
                        if ($height_mode === 'custom') {
                            $parallax_fixed_styles[] = 'height: ' . absint($custom_height) . 'px';
                            $parallax_fixed_styles[] = 'min-height: ' . absint($custom_height) . 'px';
                        } elseif ($height_mode === 'full-viewport') {
                            $parallax_fixed_styles[] = 'height: 100vh';
                            $parallax_fixed_styles[] = 'min-height: 100vh';
                        }
                        ?>
                        <div 
                            class="image-block parallax-fixed"
                            role="img"
                            aria-label="<?php echo esc_attr($image_alt); ?>"
                            style="<?php echo esc_attr(implode('; ', $parallax_fixed_styles)); ?>"
                            data-parallax-speed="<?php echo esc_attr($parallax_speed); ?>"
                            data-parallax-mode="fixed"
                        ></div>
                    <?php else: ?>
                        <!-- Autres modes utilisent une balise img -->
                        <img 
                            src="<?php echo esc_url($image_url); ?>" 
                            alt="<?php echo esc_attr($image_alt); ?>"
                            class="image-block<?php echo $display_mode === 'parallax-scroll' ? ' parallax-scroll' : ''; ?>"
                            loading="lazy"
                            style="<?php echo esc_attr(implode('; ', $inner_image_styles)); ?>"
                            <?php if ($display_mode === 'parallax-scroll'): ?>
                            data-parallax-speed="<?php echo esc_attr($parallax_speed); ?>"
                            data-parallax-effect="scroll"
                            <?php endif; ?>
                        />
                    <?php endif; ?>
                    
                    <?php if ($overlay_enabled): ?>
                        <div 
                            class="image-overlay"
                            style="background-color: <?php echo esc_attr($overlay_color); ?>; opacity: <?php echo esc_attr($overlay_opacity / 100); ?>;"
                        ></div>
                    <?php endif; ?>
                    
                    <?php if ($text_enabled && !empty($text_content)): ?>
                        <div 
                            class="image-text text-position-<?php echo esc_attr($text_position); ?>"
                            style="color: <?php echo esc_attr($text_color); ?>;"
                        >
                            <?php echo wp_kses_post($text_content); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
    </div>
    
    <?php
    return ob_get_clean();
}
