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
            
            // Image secondaire (pour mode comparison)
            'secondImageUrl' => [
                'type' => 'string',
                'default' => ''
            ],
            'secondImageId' => [
                'type' => 'number'
            ],
            'secondImageAlt' => [
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
            'textSize' => [
                'type' => 'number',
                'default' => 32
            ],
            'textWeight' => [
                'type' => 'string',
                'default' => '600'
            ],
            'textShadow' => [
                'type' => 'boolean',
                'default' => true
            ],
            'textPadding' => [
                'type' => 'number',
                'default' => 40
            ],
            'textMaxWidth' => [
                'type' => 'number',
                'default' => 80
            ],
            'textAlign' => [
                'type' => 'string',
                'default' => 'center'
            ],
            
            // Mode Comparison (avant/après)
            'comparisonOrientation' => [
                'type' => 'string',
                'default' => 'vertical'
            ],
            'comparisonInitialPosition' => [
                'type' => 'number',
                'default' => 50
            ],
            'comparisonShowLabels' => [
                'type' => 'boolean',
                'default' => true
            ],
            'comparisonBeforeLabel' => [
                'type' => 'string',
                'default' => 'Avant'
            ],
            'comparisonAfterLabel' => [
                'type' => 'string',
                'default' => 'Après'
            ],
            'comparisonHandleColor' => [
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
            
            // Filtres CSS
            'filterEnabled' => [
                'type' => 'boolean',
                'default' => false
            ],
            'filterGrayscale' => [
                'type' => 'number',
                'default' => 0 // 0-100
            ],
            'filterSepia' => [
                'type' => 'number',
                'default' => 0 // 0-100
            ],
            'filterBlur' => [
                'type' => 'number',
                'default' => 0 // 0-20 px
            ],
            'filterBrightness' => [
                'type' => 'number',
                'default' => 100 // 0-200
            ],
            'filterContrast' => [
                'type' => 'number',
                'default' => 100 // 0-200
            ],
            'filterSaturate' => [
                'type' => 'number',
                'default' => 100 // 0-200
            ],
            'filterHueRotate' => [
                'type' => 'number',
                'default' => 0 // 0-360 deg
            ],
            
            // Effet Duotone
            'duotoneEnabled' => [
                'type' => 'boolean',
                'default' => false
            ],
            'duotoneColor1' => [
                'type' => 'string',
                'default' => '#000000'
            ],
            'duotoneColor2' => [
                'type' => 'string',
                'default' => '#ffffff'
            ],
            
            // Mix Blend Mode
            'blendMode' => [
                'type' => 'string',
                'default' => 'normal' // normal, multiply, screen, overlay, etc.
            ],
            
            // Bordures et cadres
            'borderEnabled' => [
                'type' => 'boolean',
                'default' => false
            ],
            'borderWidth' => [
                'type' => 'number',
                'default' => 2
            ],
            'borderColor' => [
                'type' => 'string',
                'default' => '#ffffff'
            ],
            'borderRadius' => [
                'type' => 'number',
                'default' => 0
            ],
            'borderStyle' => [
                'type' => 'string',
                'default' => 'solid' // solid, dashed, dotted, double
            ],
            
            // Ombre
            'shadowEnabled' => [
                'type' => 'boolean',
                'default' => false
            ],
            'shadowX' => [
                'type' => 'number',
                'default' => 0
            ],
            'shadowY' => [
                'type' => 'number',
                'default' => 10
            ],
            'shadowBlur' => [
                'type' => 'number',
                'default' => 30
            ],
            'shadowColor' => [
                'type' => 'string',
                'default' => 'rgba(0,0,0,0.3)'
            ],
            
            // Animations au scroll
            'scrollAnimationEnabled' => [
                'type' => 'boolean',
                'default' => false
            ],
            'scrollAnimationType' => [
                'type' => 'string',
                'default' => 'fade' // fade, slide-up, slide-down, slide-left, slide-right, zoom
            ],
            'scrollAnimationDuration' => [
                'type' => 'number',
                'default' => 800 // ms
            ],
            'scrollAnimationDelay' => [
                'type' => 'number',
                'default' => 0 // ms
            ],
            
            // Ken Burns effect (zoom automatique)
            'kenBurnsEnabled' => [
                'type' => 'boolean',
                'default' => false
            ],
            'kenBurnsDuration' => [
                'type' => 'number',
                'default' => 20 // secondes
            ],
            'kenBurnsDirection' => [
                'type' => 'string',
                'default' => 'zoom-in' // zoom-in, zoom-out, pan-left, pan-right
            ],
            
            // Effet 3D au survol
            'tiltEnabled' => [
                'type' => 'boolean',
                'default' => false
            ],
            'tiltIntensity' => [
                'type' => 'number',
                'default' => 10 // degrés
            ],
            
            // Lightbox
            'lightboxEnabled' => [
                'type' => 'boolean',
                'default' => false
            ],
            'lightboxCaption' => [
                'type' => 'string',
                'default' => ''
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
    
    // Image secondaire pour le mode comparison
    $second_image_url = $attributes['secondImageUrl'] ?? '';
    $second_image_alt = $attributes['secondImageAlt'] ?? '';
    
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
    $text_size = $attributes['textSize'] ?? 32;
    $text_weight = $attributes['textWeight'] ?? '600';
    $text_shadow = $attributes['textShadow'] ?? true;
    $text_padding = $attributes['textPadding'] ?? 40;
    $text_max_width = $attributes['textMaxWidth'] ?? 80;
    $text_align = $attributes['textAlign'] ?? 'center';
    
    // Attributs du mode comparison
    $comparison_orientation = $attributes['comparisonOrientation'] ?? 'vertical';
    $comparison_initial_position = $attributes['comparisonInitialPosition'] ?? 50;
    $comparison_show_labels = $attributes['comparisonShowLabels'] ?? true;
    $comparison_before_label = $attributes['comparisonBeforeLabel'] ?? __('Avant', 'archi-graph');
    $comparison_after_label = $attributes['comparisonAfterLabel'] ?? __('Après', 'archi-graph');
    $comparison_handle_color = $attributes['comparisonHandleColor'] ?? '#ffffff';
    
    $align = $attributes['align'] ?? 'full';
    
    // Nouveaux attributs pour les effets avancés
    $filter_enabled = $attributes['filterEnabled'] ?? false;
    $filter_grayscale = $attributes['filterGrayscale'] ?? 0;
    $filter_sepia = $attributes['filterSepia'] ?? 0;
    $filter_blur = $attributes['filterBlur'] ?? 0;
    $filter_brightness = $attributes['filterBrightness'] ?? 100;
    $filter_contrast = $attributes['filterContrast'] ?? 100;
    $filter_saturate = $attributes['filterSaturate'] ?? 100;
    $filter_hue_rotate = $attributes['filterHueRotate'] ?? 0;
    
    $duotone_enabled = $attributes['duotoneEnabled'] ?? false;
    $duotone_color1 = $attributes['duotoneColor1'] ?? '#000000';
    $duotone_color2 = $attributes['duotoneColor2'] ?? '#ffffff';
    
    $blend_mode = $attributes['blendMode'] ?? 'normal';
    
    $border_enabled = $attributes['borderEnabled'] ?? false;
    $border_width = $attributes['borderWidth'] ?? 2;
    $border_color = $attributes['borderColor'] ?? '#ffffff';
    $border_radius = $attributes['borderRadius'] ?? 0;
    $border_style = $attributes['borderStyle'] ?? 'solid';
    
    $shadow_enabled = $attributes['shadowEnabled'] ?? false;
    $shadow_x = $attributes['shadowX'] ?? 0;
    $shadow_y = $attributes['shadowY'] ?? 10;
    $shadow_blur = $attributes['shadowBlur'] ?? 30;
    $shadow_color = $attributes['shadowColor'] ?? 'rgba(0,0,0,0.3)';
    
    $scroll_animation_enabled = $attributes['scrollAnimationEnabled'] ?? false;
    $scroll_animation_type = $attributes['scrollAnimationType'] ?? 'fade';
    $scroll_animation_duration = $attributes['scrollAnimationDuration'] ?? 800;
    $scroll_animation_delay = $attributes['scrollAnimationDelay'] ?? 0;
    
    $ken_burns_enabled = $attributes['kenBurnsEnabled'] ?? false;
    $ken_burns_duration = $attributes['kenBurnsDuration'] ?? 20;
    $ken_burns_direction = $attributes['kenBurnsDirection'] ?? 'zoom-in';
    
    $tilt_enabled = $attributes['tiltEnabled'] ?? false;
    $tilt_intensity = $attributes['tiltIntensity'] ?? 10;
    
    $lightbox_enabled = $attributes['lightboxEnabled'] ?? false;
    $lightbox_caption = $attributes['lightboxCaption'] ?? '';
    
    // Vérification des images requises
    if (empty($image_url)) {
        return '';
    }
    
    // Pour le mode comparison, vérifier qu'on a les deux images
    if ($display_mode === 'comparison' && empty($second_image_url)) {
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
    
    // Classes pour les nouveaux effets
    if ($filter_enabled) {
        $classes[] = 'filter-enabled';
    }
    
    if ($duotone_enabled) {
        $classes[] = 'duotone-enabled';
    }
    
    if ($border_enabled) {
        $classes[] = 'border-enabled';
    }
    
    if ($shadow_enabled) {
        $classes[] = 'shadow-enabled';
    }
    
    if ($scroll_animation_enabled) {
        $classes[] = 'scroll-animation-enabled';
        $classes[] = 'scroll-animation-' . $scroll_animation_type;
    }
    
    if ($ken_burns_enabled) {
        $classes[] = 'ken-burns-enabled';
        $classes[] = 'ken-burns-' . $ken_burns_direction;
    }
    
    if ($tilt_enabled) {
        $classes[] = 'tilt-enabled';
    }
    
    if ($lightbox_enabled) {
        $classes[] = 'lightbox-enabled';
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
    
    // Variables CSS pour les nouveaux effets
    if ($filter_enabled) {
        $container_styles[] = '--filter-grayscale: ' . absint($filter_grayscale) . '%';
        $container_styles[] = '--filter-sepia: ' . absint($filter_sepia) . '%';
        $container_styles[] = '--filter-blur: ' . absint($filter_blur) . 'px';
        $container_styles[] = '--filter-brightness: ' . absint($filter_brightness) . '%';
        $container_styles[] = '--filter-contrast: ' . absint($filter_contrast) . '%';
        $container_styles[] = '--filter-saturate: ' . absint($filter_saturate) . '%';
        $container_styles[] = '--filter-hue-rotate: ' . absint($filter_hue_rotate) . 'deg';
    }
    
    if ($duotone_enabled) {
        $container_styles[] = '--duotone-color-1: ' . esc_attr($duotone_color1);
        $container_styles[] = '--duotone-color-2: ' . esc_attr($duotone_color2);
    }
    
    if ($blend_mode !== 'normal') {
        $container_styles[] = '--blend-mode: ' . esc_attr($blend_mode);
    }
    
    if ($border_enabled) {
        $container_styles[] = '--border-width: ' . absint($border_width) . 'px';
        $container_styles[] = '--border-color: ' . esc_attr($border_color);
        $container_styles[] = '--border-radius: ' . absint($border_radius) . 'px';
        $container_styles[] = '--border-style: ' . esc_attr($border_style);
    }
    
    if ($shadow_enabled) {
        $container_styles[] = '--shadow-x: ' . intval($shadow_x) . 'px';
        $container_styles[] = '--shadow-y: ' . intval($shadow_y) . 'px';
        $container_styles[] = '--shadow-blur: ' . absint($shadow_blur) . 'px';
        $container_styles[] = '--shadow-color: ' . esc_attr($shadow_color);
    }
    
    if ($scroll_animation_enabled) {
        $container_styles[] = 'transition-duration: ' . absint($scroll_animation_duration) . 'ms';
        $container_styles[] = 'transition-delay: ' . absint($scroll_animation_delay) . 'ms';
    }
    
    if ($ken_burns_enabled) {
        $container_styles[] = '--ken-burns-duration: ' . absint($ken_burns_duration) . 's';
    }
    
    if ($tilt_enabled) {
        $container_styles[] = '--tilt-intensity: ' . absint($tilt_intensity) . 'deg';
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
    
    // Ajouter les data attributes pour les nouvelles fonctionnalités
    if ($lightbox_enabled) {
        $data_attrs['data-lightbox'] = 'true';
        $data_attrs['data-lightbox-caption'] = esc_attr($lightbox_caption);
    }
    
    if ($tilt_enabled) {
        $data_attrs['data-tilt-intensity'] = esc_attr($tilt_intensity);
    }
    
    if ($scroll_animation_enabled) {
        $data_attrs['data-scroll-animation'] = esc_attr($scroll_animation_type);
    }
    
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
        <?php if ($display_mode === 'comparison'): ?>
            <!-- Mode Comparison avant/après -->
            <?php
            // Enqueue comparison slider assets
            wp_enqueue_style('archi-image-comparison-slider');
            wp_enqueue_script('archi-image-comparison-slider');
            
            $orientation_class = $comparison_orientation === 'horizontal' ? 'horizontal' : 'vertical';
            ?>
            <div class="archi-comparison-block">
                <div 
                    id="<?php echo esc_attr($block_id); ?>"
                    class="comparison-container" 
                    data-orientation="<?php echo esc_attr($comparison_orientation); ?>"
                    data-initial-position="<?php echo esc_attr($comparison_initial_position); ?>"
                    data-handle-color="<?php echo esc_attr($comparison_handle_color); ?>"
                >
                    <!-- Image Avant (clippée) -->
                    <div class="before-image">
                        <img 
                            src="<?php echo esc_url($image_url); ?>" 
                            alt="<?php echo esc_attr($image_alt); ?>"
                            draggable="false"
                        />
                    </div>
                    
                    <!-- Image Après (fond) -->
                    <div class="after-image">
                        <img 
                            src="<?php echo esc_url($second_image_url); ?>" 
                            alt="<?php echo esc_attr($second_image_alt); ?>"
                            draggable="false"
                        />
                    </div>
                    
                    <!-- Poignée de slider -->
                    <div class="comparison-slider-handle" style="background-color: <?php echo esc_attr($comparison_handle_color); ?>;">
                        <div class="handle-line"></div>
                        <div class="handle-circle">
                            <svg width="40" height="40" viewBox="0 0 40 40">
                                <circle cx="20" cy="20" r="18" fill="currentColor" stroke="white" stroke-width="2"/>
                                <path d="M15 20 L12 20 L12 18 L15 18 M25 20 L28 20 L28 18 L25 18" stroke="white" stroke-width="2" fill="none"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <?php if ($comparison_show_labels): ?>
                    <div class="comparison-labels">
                        <span class="label-before"><?php echo esc_html($comparison_before_label); ?></span>
                        <span class="label-after"><?php echo esc_html($comparison_after_label); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
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
                        <?php 
                        $text_styles = [
                            'color: ' . esc_attr($text_color),
                            'font-size: ' . absint($text_size) . 'px',
                            'font-weight: ' . esc_attr($text_weight),
                            'text-align: ' . esc_attr($text_align),
                            'max-width: ' . absint($text_max_width) . '%'
                        ];
                        
                        if ($text_shadow) {
                            $text_styles[] = 'text-shadow: 0 2px 8px rgba(0,0,0,0.5)';
                        }
                        
                        // Positioning avec padding
                        $position_classes = 'text-position-' . esc_attr($text_position);
                        
                        if (strpos($text_position, 'top') !== false) {
                            $text_styles[] = 'top: ' . absint($text_padding) . 'px';
                        } elseif (strpos($text_position, 'bottom') !== false) {
                            $text_styles[] = 'bottom: ' . absint($text_padding) . 'px';
                        }
                        
                        if (strpos($text_position, 'left') !== false) {
                            $text_styles[] = 'left: ' . absint($text_padding) . 'px';
                        } elseif (strpos($text_position, 'right') !== false) {
                            $text_styles[] = 'right: ' . absint($text_padding) . 'px';
                        }
                        ?>
                        <div 
                            class="image-text <?php echo esc_attr($position_classes); ?>"
                            style="<?php echo esc_attr(implode('; ', $text_styles)); ?>"
                        >
                            <?php echo wp_kses_post($text_content); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($display_mode === 'comparison'): ?>
        <!-- Initialisation du comparison slider -->
        <script>
        (function() {
            function initSlider() {
                const container = document.getElementById('<?php echo esc_js($block_id); ?>');
                if (container && typeof window.archiInitComparisonSlider === 'function') {
                    window.archiInitComparisonSlider(container);
                }
            }
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initSlider);
            } else {
                initSlider();
            }
        })();
        </script>
    <?php endif; ?>
    
    <?php
    return ob_get_clean();
}
