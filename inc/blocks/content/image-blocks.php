<?php
/**
 * Bloc Image Pleine Largeur
 * Affiche une image qui s'étend sur toute la largeur de l'écran
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistrement du bloc Image Pleine Largeur
 */
function archi_register_image_full_width_block() {
    register_block_type('archi-graph/image-full-width', [
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
            'caption' => [
                'type' => 'string',
                'default' => ''
            ],
            'heightMode' => [
                'type' => 'string',
                'default' => 'normal'
            ]
        ],
        'render_callback' => 'archi_render_image_full_width_block',
        'editor_script' => 'archi-image-blocks',
        'editor_style' => 'archi-blocks-editor',
        'style' => 'archi-blocks'
    ]);
}
// Note: Appelé automatiquement par le loader, pas besoin de add_action
// add_action('init', 'archi_register_image_full_width_block');

/**
 * Rendu côté serveur du bloc
 */
function archi_render_image_full_width_block($attributes) {
    $image_url = isset($attributes['imageUrl']) ? esc_url($attributes['imageUrl']) : '';
    $image_alt = isset($attributes['imageAlt']) ? esc_attr($attributes['imageAlt']) : '';
    $caption = isset($attributes['caption']) ? wp_kses_post($attributes['caption']) : '';
    $height_mode = isset($attributes['heightMode']) ? esc_attr($attributes['heightMode']) : 'normal';
    
    if (empty($image_url)) {
        return '';
    }
    
    // Ajouter la classe de hauteur si nécessaire
    $height_class = '';
    if ($height_mode === 'full-viewport') {
        $height_class = ' full-viewport';
    } elseif ($height_mode === 'half-viewport') {
        $height_class = ' half-viewport';
    }
    
    ob_start();
    ?>
    <figure class="archi-image-full-width<?php echo $height_class; ?>">
        <img src="<?php echo $image_url; ?>" alt="<?php echo $image_alt; ?>" loading="lazy">
        <?php if (!empty($caption)) : ?>
            <figcaption><?php echo $caption; ?></figcaption>
        <?php endif; ?>
    </figure>
    <?php
    return ob_get_clean();
}

/**
 * Bloc Images en Colonnes
 */
function archi_register_images_columns_block() {
    register_block_type('archi-graph/images-columns', [
        'attributes' => [
            'columns' => [
                'type' => 'number',
                'default' => 2
            ],
            'images' => [
                'type' => 'array',
                'default' => []
            ]
        ],
        'render_callback' => 'archi_render_images_columns_block',
        'editor_script' => 'archi-image-blocks',
        'editor_style' => 'archi-blocks-editor',
        'style' => 'archi-blocks'
    ]);
}
// Note: Appelé automatiquement par le loader
// add_action('init', 'archi_register_images_columns_block');

/**
 * Rendu côté serveur du bloc Images en Colonnes
 */
function archi_render_images_columns_block($attributes) {
    $columns = isset($attributes['columns']) ? absint($attributes['columns']) : 2;
    $images = isset($attributes['images']) ? $attributes['images'] : [];
    
    if (empty($images)) {
        return '';
    }
    
    $class = 'archi-images-columns-' . $columns;
    
    ob_start();
    ?>
    <div class="<?php echo esc_attr($class); ?>">
        <?php foreach ($images as $image) : 
            $url = isset($image['url']) ? esc_url($image['url']) : '';
            $alt = isset($image['alt']) ? esc_attr($image['alt']) : '';
            $caption = isset($image['caption']) ? wp_kses_post($image['caption']) : '';
            
            if (empty($url)) continue;
        ?>
            <figure>
                <img src="<?php echo $url; ?>" alt="<?php echo $alt; ?>" loading="lazy">
                <?php if (!empty($caption)) : ?>
                    <figcaption><?php echo $caption; ?></figcaption>
                <?php endif; ?>
            </figure>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Bloc Image Portrait
 */
function archi_register_image_portrait_block() {
    register_block_type('archi-graph/image-portrait', [
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
            'caption' => [
                'type' => 'string',
                'default' => ''
            ]
        ],
        'render_callback' => 'archi_render_image_portrait_block',
        'editor_script' => 'archi-image-blocks',
        'editor_style' => 'archi-blocks-editor',
        'style' => 'archi-blocks'
    ]);
}
// Note: Appelé automatiquement par le loader
// add_action('init', 'archi_register_image_portrait_block');

/**
 * Rendu côté serveur du bloc Image Portrait
 */
function archi_render_image_portrait_block($attributes) {
    $image_url = isset($attributes['imageUrl']) ? esc_url($attributes['imageUrl']) : '';
    $image_alt = isset($attributes['imageAlt']) ? esc_attr($attributes['imageAlt']) : '';
    $caption = isset($attributes['caption']) ? wp_kses_post($attributes['caption']) : '';
    
    if (empty($image_url)) {
        return '';
    }
    
    ob_start();
    ?>
    <figure class="archi-image-portrait">
        <img src="<?php echo $image_url; ?>" alt="<?php echo $image_alt; ?>" loading="lazy">
        <?php if (!empty($caption)) : ?>
            <figcaption><?php echo $caption; ?></figcaption>
        <?php endif; ?>
    </figure>
    <?php
    return ob_get_clean();
}
