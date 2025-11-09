<?php
/**
 * Bloc Comparaison d'Images Avant/Après
 * Slider interactif pour comparer deux images avec effet de glissement
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistrement du bloc Comparaison Avant/Après
 */
function archi_register_image_comparison_slider_block() {
    register_block_type('archi-graph/image-comparison-slider', [
        'attributes' => [
            'beforeImageUrl' => [
                'type' => 'string',
                'default' => ''
            ],
            'beforeImageId' => [
                'type' => 'number'
            ],
            'beforeImageAlt' => [
                'type' => 'string',
                'default' => ''
            ],
            'afterImageUrl' => [
                'type' => 'string',
                'default' => ''
            ],
            'afterImageId' => [
                'type' => 'number'
            ],
            'afterImageAlt' => [
                'type' => 'string',
                'default' => ''
            ],
            'initialPosition' => [
                'type' => 'number',
                'default' => 50
            ],
            'orientation' => [
                'type' => 'string',
                'default' => 'vertical'
            ],
            'showLabels' => [
                'type' => 'boolean',
                'default' => true
            ],
            'beforeLabel' => [
                'type' => 'string',
                'default' => 'Avant'
            ],
            'afterLabel' => [
                'type' => 'string',
                'default' => 'Après'
            ],
            'aspectRatio' => [
                'type' => 'string',
                'default' => '16-9'
            ],
            'heightMode' => [
                'type' => 'string',
                'default' => 'auto'
            ],
            'customHeight' => [
                'type' => 'number',
                'default' => 600
            ],
            'handleColor' => [
                'type' => 'string',
                'default' => '#ffffff'
            ]
        ],
        'render_callback' => 'archi_render_image_comparison_slider_block',
        'editor_script' => 'archi-image-comparison-slider',
        'editor_style' => 'archi-blocks-editor',
        'style' => 'archi-blocks'
    ]);
}

/**
 * Rendu côté serveur du bloc
 */
function archi_render_image_comparison_slider_block($attributes) {
    $before_url = isset($attributes['beforeImageUrl']) ? esc_url($attributes['beforeImageUrl']) : '';
    $before_alt = isset($attributes['beforeImageAlt']) ? esc_attr($attributes['beforeImageAlt']) : '';
    $after_url = isset($attributes['afterImageUrl']) ? esc_url($attributes['afterImageUrl']) : '';
    $after_alt = isset($attributes['afterImageAlt']) ? esc_attr($attributes['afterImageAlt']) : '';
    $initial_position = isset($attributes['initialPosition']) ? absint($attributes['initialPosition']) : 50;
    $orientation = isset($attributes['orientation']) ? esc_attr($attributes['orientation']) : 'vertical';
    $show_labels = isset($attributes['showLabels']) ? (bool)$attributes['showLabels'] : true;
    $before_label = isset($attributes['beforeLabel']) ? esc_html($attributes['beforeLabel']) : __('Avant', 'archi-graph');
    $after_label = isset($attributes['afterLabel']) ? esc_html($attributes['afterLabel']) : __('Après', 'archi-graph');
    $aspect_ratio = isset($attributes['aspectRatio']) ? esc_attr($attributes['aspectRatio']) : '16-9';
    $height_mode = isset($attributes['heightMode']) ? esc_attr($attributes['heightMode']) : 'auto';
    $custom_height = isset($attributes['customHeight']) ? absint($attributes['customHeight']) : 600;
    $handle_color = isset($attributes['handleColor']) ? esc_attr($attributes['handleColor']) : '#ffffff';
    
    // Ne rien afficher si les images ne sont pas définies
    if (empty($before_url) || empty($after_url)) {
        return '';
    }
    
    // ID unique pour ce slider
    $slider_id = 'archi-comparison-' . uniqid();
    
    // Classes CSS
    $container_classes = [
        'archi-image-comparison-slider',
        'orientation-' . $orientation,
        'height-' . $height_mode
    ];
    
    // Ajouter la classe aspect-ratio seulement si en mode auto
    if ($height_mode === 'auto') {
        $container_classes[] = 'aspect-ratio-' . $aspect_ratio;
    }
    
    // Styles inline pour hauteur personnalisée
    $inline_styles = [];
    if ($height_mode === 'custom') {
        $inline_styles[] = 'height: ' . $custom_height . 'px';
    }
    
    ob_start();
    ?>
    <div class="<?php echo implode(' ', $container_classes); ?>" 
         id="<?php echo $slider_id; ?>"
         <?php if (!empty($inline_styles)) : ?>
         style="<?php echo implode('; ', $inline_styles); ?>"
         <?php endif; ?>
         data-initial-position="<?php echo $initial_position; ?>"
         data-orientation="<?php echo $orientation; ?>"
         data-handle-color="<?php echo $handle_color; ?>">
        
        <div class="comparison-container">
            <!-- Image Après (en arrière-plan) -->
            <div class="comparison-image after-image">
                <img src="<?php echo $after_url; ?>" 
                     alt="<?php echo $after_alt; ?>" 
                     loading="lazy">
                <?php if ($show_labels && !empty($after_label)) : ?>
                    <div class="image-label after-label"><?php echo $after_label; ?></div>
                <?php endif; ?>
            </div>
            
            <!-- Image Avant (clippée) -->
            <div class="comparison-image before-image">
                <img src="<?php echo $before_url; ?>" 
                     alt="<?php echo $before_alt; ?>" 
                     loading="lazy">
                <?php if ($show_labels && !empty($before_label)) : ?>
                    <div class="image-label before-label"><?php echo $before_label; ?></div>
                <?php endif; ?>
            </div>
            
            <!-- Slider Handle -->
            <div class="comparison-slider-handle" style="--handle-color: <?php echo $handle_color; ?>">
                <div class="handle-line"></div>
                <div class="handle-circle">
                    <?php if ($orientation === 'vertical') : ?>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 19l7-7-7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    <?php else : ?>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 15l-7-7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    (function() {
        const initSlider = function() {
            const slider = document.getElementById('<?php echo $slider_id; ?>');
            if (!slider || slider.dataset.initialized === 'true') return;
            
            const container = slider.querySelector('.comparison-container');
            const beforeImage = slider.querySelector('.before-image');
            const handle = slider.querySelector('.comparison-slider-handle');
            const orientation = slider.dataset.orientation || 'vertical';
            const initialPosition = parseFloat(slider.dataset.initialPosition) || 50;
            
            let isDragging = false;
            
            // Initialiser la position
            updatePosition(initialPosition);
            
            function updatePosition(percentage) {
                percentage = Math.max(0, Math.min(100, percentage));
                
                if (orientation === 'vertical') {
                    beforeImage.style.clipPath = `inset(0 ${100 - percentage}% 0 0)`;
                    handle.style.left = percentage + '%';
                } else {
                    beforeImage.style.clipPath = `inset(0 0 ${100 - percentage}% 0)`;
                    handle.style.top = percentage + '%';
                }
            }
            
            function getPositionFromEvent(event) {
                const rect = container.getBoundingClientRect();
                const clientX = event.clientX || (event.touches && event.touches[0].clientX);
                const clientY = event.clientY || (event.touches && event.touches[0].clientY);
                
                if (orientation === 'vertical') {
                    const x = clientX - rect.left;
                    return (x / rect.width) * 100;
                } else {
                    const y = clientY - rect.top;
                    return (y / rect.height) * 100;
                }
            }
            
            function onMove(event) {
                if (!isDragging) return;
                event.preventDefault();
                updatePosition(getPositionFromEvent(event));
            }
            
            function onStart(event) {
                isDragging = true;
                slider.classList.add('is-dragging');
                updatePosition(getPositionFromEvent(event));
            }
            
            function onEnd() {
                isDragging = false;
                slider.classList.remove('is-dragging');
            }
            
            // Mouse events
            handle.addEventListener('mousedown', onStart);
            container.addEventListener('mousedown', onStart);
            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onEnd);
            
            // Touch events
            handle.addEventListener('touchstart', onStart);
            container.addEventListener('touchstart', onStart);
            document.addEventListener('touchmove', onMove, { passive: false });
            document.addEventListener('touchend', onEnd);
            
            slider.dataset.initialized = 'true';
        };
        
        // Initialiser au chargement
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSlider);
        } else {
            initSlider();
        }
    })();
    </script>
    <?php
    return ob_get_clean();
}
