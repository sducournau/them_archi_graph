<?php
/**
 * Blocs Parallax et Scroll Fixe
 * Image avec effet parallax et sections avec scroll collant
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Bloc Image à Défilement Fixe
 */
function archi_register_fixed_background_block() {
    register_block_type('archi-graph/fixed-background', [
        'attributes' => [
            'imageUrl' => [
                'type' => 'string',
                'default' => ''
            ],
            'imageId' => [
                'type' => 'number'
            ],
            'minHeight' => [
                'type' => 'number',
                'default' => 500
            ],
            'overlayOpacity' => [
                'type' => 'number',
                'default' => 0
            ],
            'overlayColor' => [
                'type' => 'string',
                'default' => '#000000'
            ],
            'content' => [
                'type' => 'string',
                'default' => ''
            ],
            'contentPosition' => [
                'type' => 'string',
                'default' => 'center'
            ],
            'enableParallax' => [
                'type' => 'boolean',
                'default' => true
            ]
        ],
        'render_callback' => 'archi_render_fixed_background_block',
        'editor_script' => 'archi-parallax-blocks',
        'editor_style' => 'archi-blocks-editor',
        'style' => 'archi-blocks'
    ]);
}
// Note: Appelé automatiquement par le loader
// add_action('init', 'archi_register_fixed_background_block');

/**
 * Rendu côté serveur du bloc Fixed Background
 */
function archi_render_fixed_background_block($attributes) {
    $image_url = isset($attributes['imageUrl']) ? esc_url($attributes['imageUrl']) : '';
    $min_height = isset($attributes['minHeight']) ? absint($attributes['minHeight']) : 500;
    $overlay_opacity = isset($attributes['overlayOpacity']) ? absint($attributes['overlayOpacity']) : 0;
    $overlay_color = isset($attributes['overlayColor']) ? esc_attr($attributes['overlayColor']) : '#000000';
    $content = isset($attributes['content']) ? wp_kses_post($attributes['content']) : '';
    $content_position = isset($attributes['contentPosition']) ? esc_attr($attributes['contentPosition']) : 'center';
    $enable_parallax = isset($attributes['enableParallax']) && $attributes['enableParallax'];
    
    if (empty($image_url)) {
        return '';
    }
    
    // Classes CSS
    $classes = ['archi-fixed-background'];
    if ($enable_parallax) {
        $classes[] = 'has-parallax-effect';
    }
    if ($content_position === 'top') {
        $classes[] = 'content-top';
    } elseif ($content_position === 'bottom') {
        $classes[] = 'content-bottom';
    } else {
        $classes[] = 'content-center';
    }
    
    // Styles inline
    $styles = [
        'min-height: ' . $min_height . 'px',
        'background-image: url(' . $image_url . ')'
    ];
    
    ob_start();
    ?>
    <section 
        class="<?php echo esc_attr(implode(' ', $classes)); ?>" 
        style="<?php echo esc_attr(implode('; ', $styles)); ?>"
        data-parallax="<?php echo $enable_parallax ? 'true' : 'false'; ?>"
    >
        <?php if ($overlay_opacity > 0) : ?>
            <div 
                class="archi-fixed-background-overlay" 
                style="background-color: <?php echo $overlay_color; ?>; opacity: <?php echo $overlay_opacity / 100; ?>;"
            ></div>
        <?php endif; ?>
        
        <?php if (!empty($content)) : ?>
            <div class="archi-fixed-background-content">
                <div class="archi-fixed-background-inner">
                    <?php echo $content; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Bloc Scroll Collant (Sticky Scroll)
 */
function archi_register_sticky_scroll_block() {
    register_block_type('archi-graph/sticky-scroll', [
        'attributes' => [
            'imageUrl' => [
                'type' => 'string',
                'default' => ''
            ],
            'imageId' => [
                'type' => 'number'
            ],
            'imagePosition' => [
                'type' => 'string',
                'default' => 'left'
            ],
            'title' => [
                'type' => 'string',
                'default' => ''
            ],
            'content' => [
                'type' => 'string',
                'default' => ''
            ],
            'items' => [
                'type' => 'array',
                'default' => []
            ]
        ],
        'render_callback' => 'archi_render_sticky_scroll_block',
        'editor_script' => 'archi-parallax-blocks',
        'editor_style' => 'archi-blocks-editor',
        'style' => 'archi-blocks'
    ]);
}
// Note: Appelé automatiquement par le loader
// add_action('init', 'archi_register_sticky_scroll_block');

/**
 * Rendu côté serveur du bloc Sticky Scroll
 */
function archi_render_sticky_scroll_block($attributes) {
    $image_url = isset($attributes['imageUrl']) ? esc_url($attributes['imageUrl']) : '';
    $image_position = isset($attributes['imagePosition']) ? esc_attr($attributes['imagePosition']) : 'left';
    $title = isset($attributes['title']) ? wp_kses_post($attributes['title']) : '';
    $content = isset($attributes['content']) ? wp_kses_post($attributes['content']) : '';
    $items = isset($attributes['items']) ? $attributes['items'] : [];
    
    if (empty($image_url) && empty($title) && empty($items)) {
        return '';
    }
    
    // Classes CSS
    $classes = ['archi-sticky-scroll'];
    if ($image_position === 'right') {
        $classes[] = 'image-right';
    } else {
        $classes[] = 'image-left';
    }
    
    ob_start();
    ?>
    <section class="<?php echo esc_attr(implode(' ', $classes)); ?>">
        <div class="archi-sticky-scroll-container">
            <?php if (!empty($image_url)) : ?>
                <div class="archi-sticky-scroll-image">
                    <div class="archi-sticky-scroll-image-inner">
                        <img src="<?php echo $image_url; ?>" alt="" loading="lazy">
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="archi-sticky-scroll-content">
                <?php if (!empty($title)) : ?>
                    <h2 class="archi-sticky-scroll-title"><?php echo $title; ?></h2>
                <?php endif; ?>
                
                <?php if (!empty($content)) : ?>
                    <div class="archi-sticky-scroll-intro">
                        <?php echo wpautop($content); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($items)) : ?>
                    <div class="archi-sticky-scroll-items">
                        <?php foreach ($items as $index => $item) : 
                            $item_title = isset($item['title']) ? wp_kses_post($item['title']) : '';
                            $item_description = isset($item['description']) ? wp_kses_post($item['description']) : '';
                            
                            if (empty($item_title) && empty($item_description)) continue;
                        ?>
                            <div class="archi-sticky-scroll-item" data-index="<?php echo $index; ?>">
                                <?php if (!empty($item_title)) : ?>
                                    <h3 class="archi-sticky-scroll-item-title"><?php echo $item_title; ?></h3>
                                <?php endif; ?>
                                
                                <?php if (!empty($item_description)) : ?>
                                    <div class="archi-sticky-scroll-item-description">
                                        <?php echo wpautop($item_description); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
