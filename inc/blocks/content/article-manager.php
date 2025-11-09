<?php
/**
 * Bloc: Gestionnaire d'Article
 * Bloc complet pour gérer tous les aspects d'un article
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistrer le bloc
 */
function archi_register_article_manager_block() {
    $attributes = Archi_Shared_Block_Attributes::merge_attributes(
        Archi_Shared_Block_Attributes::get_display_attributes(),
        Archi_Shared_Block_Attributes::get_visibility_attributes(),
        [
            'showContent' => [
                'type' => 'boolean',
                'default' => false
            ],
            'showWordCount' => [
                'type' => 'boolean',
                'default' => false
            ],
            'layoutStyle' => [
                'type' => 'string',
                'default' => 'card' // card, list, grid, minimal
            ],
            'imagePosition' => [
                'type' => 'string',
                'default' => 'top' // top, left, right, background
            ],
            'showProjectDetails' => [
                'type' => 'boolean',
                'default' => true
            ],
            'showIllustrationDetails' => [
                'type' => 'boolean',
                'default' => true
            ]
        ]
    );
    
    register_block_type('archi-graph/article-manager', [
        'attributes' => $attributes,
        'render_callback' => 'archi_render_article_manager_block',
        'editor_script' => 'archi-article-manager-block',
        'editor_style' => 'archi-blocks-editor',
        'style' => 'archi-blocks'
    ]);
}
// Note: Appelé automatiquement par le loader
// add_action('init', 'archi_register_article_manager_block');

/**
 * Rendu du bloc
 */
function archi_render_article_manager_block($attributes) {
    global $post;
    
    if (!$post) {
        return '';
    }
    
    $attributes = archi_sanitize_block_attributes($attributes, [
        'showFeaturedImage' => ['type' => 'boolean', 'default' => true],
        'showTitle' => ['type' => 'boolean', 'default' => true],
        'showExcerpt' => ['type' => 'boolean', 'default' => true],
        'showContent' => ['type' => 'boolean', 'default' => false],
        'showAuthor' => ['type' => 'boolean', 'default' => true],
        'showDate' => ['type' => 'boolean', 'default' => true],
        'showCategories' => ['type' => 'boolean', 'default' => true],
        'showTags' => ['type' => 'boolean', 'default' => true],
        'showWordCount' => ['type' => 'boolean', 'default' => false],
        'layoutStyle' => ['type' => 'string', 'default' => 'card'],
        'imagePosition' => ['type' => 'string', 'default' => 'top'],
        'showProjectDetails' => ['type' => 'boolean', 'default' => true],
        'showIllustrationDetails' => ['type' => 'boolean', 'default' => true]
    ]);
    
    $post_type = get_post_type($post);
    $classes = archi_get_block_classes($attributes, 'archi-article-manager');
    $classes .= ' layout-' . esc_attr($attributes['layoutStyle']);
    $classes .= ' image-' . esc_attr($attributes['imagePosition']);
    
    ob_start();
    ?>
    <div class="<?php echo esc_attr($classes); ?>" data-post-id="<?php echo absint($post->ID); ?>">
        
        <div class="article-manager-container">
            
            <?php // Image featured ?>
            <?php if ($attributes['showFeaturedImage'] && $attributes['imagePosition'] === 'top'): ?>
                <?php echo archi_render_article_image($post, $attributes); ?>
            <?php endif; ?>
            
            <div class="article-manager-content">
                
                <?php // Image à gauche ?>
                <?php if ($attributes['showFeaturedImage'] && $attributes['imagePosition'] === 'left'): ?>
                    <?php echo archi_render_article_image($post, $attributes); ?>
                <?php endif; ?>
                
                <div class="article-main-content">
                    
                    <?php // Titre ?>
                    <?php if ($attributes['showTitle']): ?>
                    <header class="article-header">
                        <h2 class="article-title"><?php echo esc_html(get_the_title()); ?></h2>
                        
                        <?php // Badge type de post ?>
                        <span class="post-type-badge">
                            <?php echo esc_html(get_post_type_object($post_type)->labels->singular_name); ?>
                        </span>
                    </header>
                    <?php endif; ?>
                    
                    <?php // Métadonnées générales ?>
                    <?php if ($attributes['showAuthor'] || $attributes['showDate']): ?>
                    <div class="article-meta">
                        <?php if ($attributes['showAuthor']): ?>
                        <span class="meta-author">
                            <span class="dashicons dashicons-admin-users"></span>
                            <?php the_author(); ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($attributes['showDate']): ?>
                        <span class="meta-date">
                            <span class="dashicons dashicons-calendar"></span>
                            <?php echo get_the_date(); ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($attributes['showWordCount']): ?>
                        <span class="meta-word-count">
                            <span class="dashicons dashicons-text"></span>
                            <?php echo archi_get_word_count($post->ID); ?> mots
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php // Extrait ou contenu ?>
                    <?php if ($attributes['showExcerpt']): ?>
                    <div class="article-excerpt">
                        <?php echo wp_kses_post(get_the_excerpt()); ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($attributes['showContent']): ?>
                    <div class="article-content">
                        <?php the_content(); ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php // Taxonomies ?>
                    <?php if ($attributes['showCategories'] || $attributes['showTags']): ?>
                    <div class="article-taxonomies">
                        <?php if ($attributes['showCategories']): ?>
                        <?php echo archi_render_categories($post->ID); ?>
                        <?php endif; ?>
                        
                        <?php if ($attributes['showTags']): ?>
                        <?php echo archi_render_tags($post->ID); ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php // Métadonnées spécifiques au type de post ?>
                    <?php if ($post_type === 'archi_project' && $attributes['showProjectDetails']): ?>
                        <?php echo archi_render_project_details($post->ID); ?>
                    <?php endif; ?>
                    
                    <?php if ($post_type === 'archi_illustration' && $attributes['showIllustrationDetails']): ?>
                        <?php echo archi_render_illustration_details($post->ID); ?>
                    <?php endif; ?>
                    
                </div>
                
                <?php // Image à droite ?>
                <?php if ($attributes['showFeaturedImage'] && $attributes['imagePosition'] === 'right'): ?>
                    <?php echo archi_render_article_image($post, $attributes); ?>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}

/**
 * Render l'image de l'article
 */
function archi_render_article_image($post, $attributes) {
    if (!has_post_thumbnail($post)) {
        return '';
    }
    
    $size = $attributes['imagePosition'] === 'top' ? 'large' : 'medium';
    
    ob_start();
    ?>
    <div class="article-featured-image">
        <?php echo get_the_post_thumbnail($post, $size, ['loading' => 'lazy']); ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render les catégories
 */
function archi_render_categories($post_id) {
    $categories = get_the_category($post_id);
    
    if (empty($categories)) {
        return '';
    }
    
    ob_start();
    ?>
    <div class="article-categories">
        <span class="label"><?php _e('Catégories:', 'archi-graph'); ?></span>
        <?php foreach ($categories as $category): ?>
        <a href="<?php echo esc_url(get_category_link($category)); ?>" 
           class="category-badge">
            <?php echo esc_html($category->name); ?>
        </a>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render les tags
 */
function archi_render_tags($post_id) {
    $tags = get_the_tags($post_id);
    
    if (empty($tags)) {
        return '';
    }
    
    ob_start();
    ?>
    <div class="article-tags">
        <span class="label"><?php _e('Tags:', 'archi-graph'); ?></span>
        <?php foreach ($tags as $tag): ?>
        <a href="<?php echo esc_url(get_tag_link($tag)); ?>" 
           class="tag-badge">
            #<?php echo esc_html($tag->name); ?>
        </a>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render détails projet
 */
function archi_render_project_details($post_id) {
    $surface = archi_get_project_meta($post_id, '_archi_project_surface');
    $cost = archi_get_project_meta($post_id, '_archi_project_cost');
    $location = archi_get_project_meta($post_id, '_archi_project_location');
    $year = archi_get_project_meta($post_id, '_archi_project_year');
    $client = archi_get_project_meta($post_id, '_archi_project_client');
    
    ob_start();
    ?>
    <div class="project-details">
        <h3><?php _e('Détails du Projet', 'archi-graph'); ?></h3>
        <dl class="details-list">
            <?php if ($location): ?>
            <dt><?php _e('Localisation', 'archi-graph'); ?></dt>
            <dd><?php echo esc_html($location); ?></dd>
            <?php endif; ?>
            
            <?php if ($surface): ?>
            <dt><?php _e('Surface', 'archi-graph'); ?></dt>
            <dd><?php echo esc_html(number_format($surface, 0, ',', ' ')); ?> m²</dd>
            <?php endif; ?>
            
            <?php if ($year): ?>
            <dt><?php _e('Année', 'archi-graph'); ?></dt>
            <dd><?php echo esc_html($year); ?></dd>
            <?php endif; ?>
            
            <?php if ($client): ?>
            <dt><?php _e('Client', 'archi-graph'); ?></dt>
            <dd><?php echo esc_html($client); ?></dd>
            <?php endif; ?>
            
            <?php if ($cost): ?>
            <dt><?php _e('Budget', 'archi-graph'); ?></dt>
            <dd><?php echo esc_html(number_format($cost, 0, ',', ' ')); ?> €</dd>
            <?php endif; ?>
        </dl>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render détails illustration
 */
function archi_render_illustration_details($post_id) {
    $technique = archi_get_illustration_meta($post_id, '_archi_illustration_technique');
    $software = archi_get_illustration_meta($post_id, '_archi_illustration_software');
    $dimensions = archi_get_illustration_meta($post_id, '_archi_illustration_dimensions');
    $year = archi_get_illustration_meta($post_id, '_archi_illustration_year');
    
    ob_start();
    ?>
    <div class="illustration-details">
        <h3><?php _e('Détails Techniques', 'archi-graph'); ?></h3>
        <dl class="details-list">
            <?php if ($technique): ?>
            <dt><?php _e('Technique', 'archi-graph'); ?></dt>
            <dd><?php echo esc_html($technique); ?></dd>
            <?php endif; ?>
            
            <?php if ($software): ?>
            <dt><?php _e('Logiciel', 'archi-graph'); ?></dt>
            <dd><?php echo esc_html($software); ?></dd>
            <?php endif; ?>
            
            <?php if ($dimensions): ?>
            <dt><?php _e('Dimensions', 'archi-graph'); ?></dt>
            <dd><?php echo esc_html($dimensions); ?></dd>
            <?php endif; ?>
            
            <?php if ($year): ?>
            <dt><?php _e('Année', 'archi-graph'); ?></dt>
            <dd><?php echo esc_html($year); ?></dd>
            <?php endif; ?>
        </dl>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Obtenir le nombre de mots
 */
function archi_get_word_count($post_id) {
    $content = get_post_field('post_content', $post_id);
    $content = strip_tags($content);
    $content = strip_shortcodes($content);
    return str_word_count($content);
}

// Enregistrer le bloc
archi_register_article_manager_block();
