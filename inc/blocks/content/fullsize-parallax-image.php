<?php
/**
 * Bloc Image Pleine Taille avec Parallax Avancé
 * Image immersive en pleine largeur/hauteur avec effets parallax sophistiqués
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistrement du bloc Image Pleine Taille Parallax
 */
function archi_register_fullsize_parallax_image_block() {
    register_block_type('archi-graph/fullsize-parallax-image', [
        'attributes' => [
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
            'heightMode' => [
                'type' => 'string',
                'default' => 'full-viewport'
            ],
            'customHeight' => [
                'type' => 'number',
                'default' => 600
            ],
            'parallaxEffect' => [
                'type' => 'string',
                'default' => 'scroll'
            ],
            'parallaxSpeed' => [
                'type' => 'number',
                'default' => 0.5
            ],
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
            'enableZoom' => [
                'type' => 'boolean',
                'default' => false
            ],
            'objectFit' => [
                'type' => 'string',
                'default' => 'cover'
            ]
        ],
        'render_callback' => 'archi_render_fullsize_parallax_image_block',
        'editor_script' => 'archi-fullsize-parallax-image',
        'editor_style' => 'archi-blocks-editor',
        'style' => 'archi-blocks'
    ]);
}

/**
 * Rendu côté serveur du bloc
 */
function archi_render_fullsize_parallax_image_block($attributes) {
    $image_url = isset($attributes['imageUrl']) ? esc_url($attributes['imageUrl']) : '';
    $image_alt = isset($attributes['imageAlt']) ? esc_attr($attributes['imageAlt']) : '';
    $height_mode = isset($attributes['heightMode']) ? esc_attr($attributes['heightMode']) : 'full-viewport';
    $custom_height = isset($attributes['customHeight']) ? absint($attributes['customHeight']) : 600;
    $parallax_effect = isset($attributes['parallaxEffect']) ? esc_attr($attributes['parallaxEffect']) : 'scroll';
    $parallax_speed = isset($attributes['parallaxSpeed']) ? floatval($attributes['parallaxSpeed']) : 0.5;
    $overlay_enabled = isset($attributes['overlayEnabled']) ? (bool)$attributes['overlayEnabled'] : false;
    $overlay_color = isset($attributes['overlayColor']) ? esc_attr($attributes['overlayColor']) : '#000000';
    $overlay_opacity = isset($attributes['overlayOpacity']) ? absint($attributes['overlayOpacity']) : 30;
    $text_enabled = isset($attributes['textEnabled']) ? (bool)$attributes['textEnabled'] : false;
    $text_content = isset($attributes['textContent']) ? wp_kses_post($attributes['textContent']) : '';
    $text_position = isset($attributes['textPosition']) ? esc_attr($attributes['textPosition']) : 'center';
    $text_color = isset($attributes['textColor']) ? esc_attr($attributes['textColor']) : '#ffffff';
    $enable_zoom = isset($attributes['enableZoom']) ? (bool)$attributes['enableZoom'] : false;
    $object_fit = isset($attributes['objectFit']) ? esc_attr($attributes['objectFit']) : 'cover';
    
    if (empty($image_url)) {
        return '';
    }
    
    // ID unique pour ce bloc
    $block_id = 'archi-fullsize-' . uniqid();
    
    // Classes CSS
    $container_classes = [
        'archi-fullsize-parallax-image',
        'height-' . $height_mode,
        'parallax-' . $parallax_effect,
        'object-fit-' . $object_fit
    ];
    
    if ($enable_zoom && $parallax_effect === 'zoom') {
        $container_classes[] = 'has-zoom';
    }
    
    if ($text_enabled) {
        $container_classes[] = 'has-text';
    }
    
    // Styles inline
    $container_style = '';
    if ($height_mode === 'custom') {
        $container_style = 'height: ' . $custom_height . 'px;';
    }
    
    ob_start();
    ?>
    <div class="<?php echo implode(' ', $container_classes); ?>" 
         id="<?php echo $block_id; ?>"
         <?php if (!empty($container_style)) : ?>style="<?php echo $container_style; ?>"<?php endif; ?>
         data-parallax-effect="<?php echo $parallax_effect; ?>"
         data-parallax-speed="<?php echo $parallax_speed; ?>">
        
        <div class="fullsize-image-container">
            <div class="image-wrapper">
                <img src="<?php echo $image_url; ?>" 
                     alt="<?php echo $image_alt; ?>" 
                     class="parallax-image"
                     loading="lazy">
            </div>
            
            <?php if ($overlay_enabled) : ?>
                <div class="image-overlay" 
                     style="background-color: <?php echo $overlay_color; ?>; opacity: <?php echo ($overlay_opacity / 100); ?>;"></div>
            <?php endif; ?>
            
            <?php if ($text_enabled && !empty($text_content)) : ?>
                <div class="image-text text-position-<?php echo $text_position; ?>" 
                     style="color: <?php echo $text_color; ?>;">
                    <?php echo $text_content; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($parallax_effect === 'scroll') : ?>
    <script>
    (function() {
        const initParallax = function() {
            const block = document.getElementById('<?php echo $block_id; ?>');
            if (!block || block.dataset.initialized === 'true') return;
            
            const image = block.querySelector('.parallax-image');
            if (!image) return;
            
            const speed = <?php echo $parallax_speed; ?>;
            
            function updateParallax() {
                const rect = block.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                
                // Calculer si l'élément est visible
                if (rect.top < windowHeight && rect.bottom > 0) {
                    const scrollProgress = (windowHeight - rect.top) / (windowHeight + rect.height);
                    const translateY = (scrollProgress - 0.5) * 100 * speed;
                    
                    image.style.transform = 'translateY(' + translateY + 'px) scale(1.1)';
                }
            }
            
            // Throttle pour les performances
            let ticking = false;
            function requestTick() {
                if (!ticking) {
                    window.requestAnimationFrame(function() {
                        updateParallax();
                        ticking = false;
                    });
                    ticking = true;
                }
            }
            
            window.addEventListener('scroll', requestTick, { passive: true });
            window.addEventListener('resize', requestTick, { passive: true });
            
            // Initialisation
            updateParallax();
            block.dataset.initialized = 'true';
        };
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initParallax);
        } else {
            initParallax();
        }
    })();
    </script>
    <?php endif; ?>
    <?php
    return ob_get_clean();
}
