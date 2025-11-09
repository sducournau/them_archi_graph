<?php
/**
 * Bloc Image - Rendu côté serveur
 * 
 * Bloc pour gérer toutes les variations d'images :
 * - Standard
 * - Parallax scroll
 * - Parallax fixed
 * - Zoom
 * - Comparaison avant/après
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
            
            // Image secondaire (comparaison)
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
            
            // Comparaison
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
    $second_image_url = $attributes['secondImageUrl'] ?? '';
    $second_image_alt = $attributes['secondImageAlt'] ?? '';
    
    $height_mode = $attributes['heightMode'] ?? 'auto';
    $custom_height = $attributes['customHeight'] ?? 600;
    $object_fit = $attributes['objectFit'] ?? 'cover';
    
    $parallax_speed = $attributes['parallaxSpeed'] ?? 0.5;
    
    $overlay_enabled = $attributes['overlayEnabled'] ?? false;
    $overlay_color = $attributes['overlayColor'] ?? '#000000';
    $overlay_opacity = $attributes['overlayOpacity'] ?? 30;
    
    $text_enabled = $attributes['textEnabled'] ?? false;
    $text_content = $attributes['textContent'] ?? '';
    $text_position = $attributes['textPosition'] ?? 'center';
    $text_color = $attributes['textColor'] ?? '#ffffff';
    
    $comp_orientation = $attributes['comparisonOrientation'] ?? 'vertical';
    $comp_initial_position = $attributes['comparisonInitialPosition'] ?? 50;
    $comp_show_labels = $attributes['comparisonShowLabels'] ?? true;
    $comp_before_label = $attributes['comparisonBeforeLabel'] ?? __('Avant', 'archi-graph');
    $comp_after_label = $attributes['comparisonAfterLabel'] ?? __('Après', 'archi-graph');
    $comp_handle_color = $attributes['comparisonHandleColor'] ?? '#ffffff';
    
    $align = $attributes['align'] ?? 'full';
    
    // Vérification des images requises
    if (empty($image_url)) {
        return '<div class="archi-image-block-placeholder">' . 
               __('Veuillez sélectionner une image', 'archi-graph') . 
               '</div>';
    }
    
    if ($display_mode === 'comparison' && empty($second_image_url)) {
        return '<div class="archi-image-block-placeholder">' . 
               __('Le mode comparaison nécessite deux images', 'archi-graph') . 
               '</div>';
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
    }
    
    if ($text_enabled) {
        $classes[] = 'has-text';
        $classes[] = 'text-position-' . $text_position;
    }
    
    // Styles inline pour le conteneur
    $container_styles = [];
    if ($height_mode === 'custom') {
        $container_styles[] = 'min-height: ' . absint($custom_height) . 'px';
    } elseif ($height_mode === 'full-viewport') {
        $container_styles[] = 'min-height: 100vh';
    }
    
    // Data attributes pour JavaScript
    $data_attrs = [
        'data-mode' => esc_attr($display_mode),
        'data-parallax-speed' => esc_attr($parallax_speed),
        'data-block-id' => esc_attr($block_id)
    ];
    
    if ($display_mode === 'comparison') {
        $data_attrs['data-orientation'] = esc_attr($comp_orientation);
        $data_attrs['data-initial-position'] = absint($comp_initial_position);
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
            <!-- Mode Comparaison Avant/Après -->
            <?php echo archi_render_comparison_mode(
                $block_id,
                $image_url,
                $image_alt,
                $second_image_url,
                $second_image_alt,
                $comp_orientation,
                $comp_initial_position,
                $comp_show_labels,
                $comp_before_label,
                $comp_after_label,
                $comp_handle_color,
                $height_mode,
                $custom_height
            ); ?>
            
        <?php else: ?>
            <!-- Modes Standard/Parallax/Zoom/Cover -->
            <div class="image-block-container">
                <div class="image-wrapper <?php echo esc_attr('wrapper-' . $display_mode); ?>">
                    <img 
                        src="<?php echo esc_url($image_url); ?>" 
                        alt="<?php echo esc_attr($image_alt); ?>"
                        class="image-block <?php echo esc_attr('image-' . $display_mode); ?>"
                        loading="lazy"
                        style="object-fit: <?php echo esc_attr($object_fit); ?>;"
                    />
                    
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
        <?php endif; ?>
    </div>
    
    <?php if ($display_mode === 'parallax-scroll' || $display_mode === 'comparison'): ?>
        <!-- Script pour les effets interactifs -->
        <script>
        (function() {
            const blockId = '<?php echo esc_js($block_id); ?>';
            const container = document.getElementById(blockId);
            
            if (!container) return;
            
            <?php if ($display_mode === 'parallax-scroll'): ?>
                // Parallax scroll effect
                const parallaxSpeed = <?php echo floatval($parallax_speed); ?>;
                const image = container.querySelector('.unified-image');
                
                if (image) {
                    const handleScroll = () => {
                        const rect = container.getBoundingClientRect();
                        const scrolled = window.pageYOffset;
                        const rate = scrolled * parallaxSpeed;
                        
                        if (rect.top < window.innerHeight && rect.bottom > 0) {
                            image.style.transform = `translateY(${rate}px)`;
                        }
                    };
                    
                    // Throttle scroll events
                    let ticking = false;
                    window.addEventListener('scroll', () => {
                        if (!ticking) {
                            window.requestAnimationFrame(() => {
                                handleScroll();
                                ticking = false;
                            });
                            ticking = true;
                        }
                    });
                    
                    handleScroll(); // Initial call
                }
            <?php endif; ?>
            
            <?php if ($display_mode === 'comparison'): ?>
                // Comparison slider functionality
                archiInitComparisonSlider(blockId);
            <?php endif; ?>
        })();
        </script>
    <?php endif; ?>
    
    <?php
    return ob_get_clean();
}

/**
 * Rend le mode comparaison avant/après
 * 
 * @param string $block_id ID unique du bloc
 * @param string $before_url URL image avant
 * @param string $before_alt Alt image avant
 * @param string $after_url URL image après
 * @param string $after_alt Alt image après
 * @param string $orientation Orientation du slider
 * @param int $initial_position Position initiale du slider
 * @param bool $show_labels Afficher les étiquettes
 * @param string $before_label Étiquette avant
 * @param string $after_label Étiquette après
 * @param string $handle_color Couleur de la poignée
 * @param string $height_mode Mode de hauteur
 * @param int $custom_height Hauteur personnalisée
 * @return string HTML du mode comparaison
 */
function archi_render_comparison_mode(
    $block_id,
    $before_url,
    $before_alt,
    $after_url,
    $after_alt,
    $orientation,
    $initial_position,
    $show_labels,
    $before_label,
    $after_label,
    $handle_color,
    $height_mode,
    $custom_height
) {
    $container_classes = [
        'comparison-container',
        'orientation-' . $orientation
    ];
    
    $container_styles = [];
    if ($height_mode === 'custom') {
        $container_styles[] = 'height: ' . absint($custom_height) . 'px';
    }
    
    ob_start();
    ?>
    
    <div 
        class="<?php echo esc_attr(implode(' ', $container_classes)); ?>"
        <?php if (!empty($container_styles)): ?>
            style="<?php echo esc_attr(implode('; ', $container_styles)); ?>"
        <?php endif; ?>
    >
        <!-- Image Après (arrière-plan) -->
        <div class="comparison-image after-image">
            <img 
                src="<?php echo esc_url($after_url); ?>" 
                alt="<?php echo esc_attr($after_alt); ?>"
                loading="lazy"
            />
            <?php if ($show_labels): ?>
                <div class="image-label after-label">
                    <?php echo esc_html($after_label); ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Image Avant (premier plan avec clip-path) -->
        <div class="comparison-image before-image" style="<?php echo $orientation === 'vertical' ? 'clip-path: inset(0 ' . (100 - $initial_position) . '% 0 0)' : 'clip-path: inset(0 0 ' . (100 - $initial_position) . '% 0)'; ?>">
            <img 
                src="<?php echo esc_url($before_url); ?>" 
                alt="<?php echo esc_attr($before_alt); ?>"
                loading="lazy"
            />
            <?php if ($show_labels): ?>
                <div class="image-label before-label">
                    <?php echo esc_html($before_label); ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Poignée du slider -->
        <div 
            class="comparison-slider-handle"
            style="<?php echo $orientation === 'vertical' ? 'left: ' . $initial_position . '%' : 'top: ' . $initial_position . '%'; ?>; --handle-color: <?php echo esc_attr($handle_color); ?>;"
        >
            <div class="handle-line"></div>
            <div class="handle-circle">
                <?php if ($orientation === 'vertical'): ?>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                        <path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/>
                    </svg>
                <?php else: ?>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/>
                        <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6z"/>
                    </svg>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php
    return ob_get_clean();
}

/**
 * Enqueue le script de comparaison d'images
 */
function archi_enqueue_image_scripts() {
    if (has_block('archi-graph/image-block')) {
        wp_enqueue_script(
            'archi-image-comparison',
            get_template_directory_uri() . '/assets/js/comparison-slider.js',
            [],
            '1.0.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'archi_enqueue_image_scripts');
