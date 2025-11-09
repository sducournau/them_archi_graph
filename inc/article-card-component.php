<?php
/**
 * Unified Article Card Component
 * Displays posts, projects, and illustrations with all metadata
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render a unified article card with all metadata
 * 
 * @param WP_Post $post The post object
 * @param array $args Display options
 * @return string HTML output
 */
function archi_render_article_card($post, $args = []) {
    $defaults = [
        'layout' => 'card', // card, list, minimal, detailed
        'show_image' => true,
        'show_excerpt' => true,
        'show_metadata' => true,
        'show_graph_status' => false,
        'show_taxonomies' => true,
        'image_size' => 'medium',
        'excerpt_length' => 20,
        'show_read_more' => true,
        'show_type_badge' => true,
    ];
    
    $args = wp_parse_args($args, $defaults);
    
    // Get metadata
    $show_in_graph = get_post_meta($post->ID, '_archi_show_in_graph', true);
    $node_color = get_post_meta($post->ID, '_archi_node_color', true);
    $priority = get_post_meta($post->ID, '_archi_priority_level', true) ?: 'normal';
    
    // Get post type info
    $post_type_obj = get_post_type_object($post->post_type);
    $post_type_label = $post_type_obj->labels->singular_name;
    
    // Get taxonomies
    $categories = get_the_category($post->ID);
    $tags = get_the_tags($post->ID);
    
    // Get type-specific metadata
    $type_metadata = archi_get_type_specific_metadata($post);
    
    // Build card classes
    $card_classes = [
        'archi-article-card',
        'archi-layout-' . $args['layout'],
        'archi-type-' . $post->post_type,
        'archi-priority-' . $priority,
    ];
    
    if ($show_in_graph === '1') {
        $card_classes[] = 'archi-in-graph';
    }
    
    ob_start();
    ?>
    <article class="<?php echo esc_attr(implode(' ', $card_classes)); ?>" 
             data-post-id="<?php echo esc_attr($post->ID); ?>"
             data-post-type="<?php echo esc_attr($post->post_type); ?>">
        
        <?php if ($args['show_type_badge']): ?>
        <div class="archi-type-badge archi-badge-<?php echo esc_attr($post->post_type); ?>">
            <?php echo esc_html($post_type_label); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($args['show_image'] && has_post_thumbnail($post->ID)): ?>
        <div class="archi-card-image">
            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                <?php echo get_the_post_thumbnail($post->ID, $args['image_size'], [
                    'class' => 'archi-thumbnail',
                    'loading' => 'lazy'
                ]); ?>
            </a>
            
            <?php if ($args['show_graph_status'] && $show_in_graph === '1'): ?>
            <span class="archi-graph-indicator" 
                  style="background-color: <?php echo esc_attr($node_color ?: '#3498db'); ?>;"
                  title="<?php _e('Visible dans le graphique', 'archi-graph'); ?>">
                <span class="dashicons dashicons-networking"></span>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="archi-card-content">
            <header class="archi-card-header">
                <h3 class="archi-card-title">
                    <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                        <?php echo esc_html($post->post_title); ?>
                    </a>
                </h3>
                
                <div class="archi-card-meta">
                    <span class="archi-meta-date">
                        <span class="dashicons dashicons-calendar"></span>
                        <?php echo esc_html(get_the_date('', $post->ID)); ?>
                    </span>
                    
                    <span class="archi-meta-author">
                        <span class="dashicons dashicons-admin-users"></span>
                        <?php echo esc_html(get_the_author_meta('display_name', $post->post_author)); ?>
                    </span>
                </div>
            </header>
            
            <?php if ($args['show_excerpt']): ?>
            <div class="archi-card-excerpt">
                <?php echo wp_trim_words($post->post_excerpt ?: $post->post_content, $args['excerpt_length']); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($args['show_metadata'] && !empty($type_metadata)): ?>
            <div class="archi-card-metadata">
                <?php echo archi_render_type_metadata($post->post_type, $type_metadata); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($args['show_taxonomies'] && ($categories || $tags)): ?>
            <div class="archi-card-taxonomies">
                <?php if ($categories): ?>
                <div class="archi-categories">
                    <?php foreach ($categories as $category): ?>
                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" 
                       class="archi-category-tag">
                        <?php echo esc_html($category->name); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($tags): ?>
                <div class="archi-tags">
                    <?php foreach ($tags as $tag): ?>
                    <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" 
                       class="archi-tag">
                        #<?php echo esc_html($tag->name); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <footer class="archi-card-footer">
                <?php if ($args['show_read_more']): ?>
                <a href="<?php echo esc_url(get_permalink($post->ID)); ?>" 
                   class="archi-read-more">
                    <?php _e('En savoir plus', 'archi-graph'); ?>
                    <span class="dashicons dashicons-arrow-right-alt"></span>
                </a>
                <?php endif; ?>
                
                <div class="archi-card-stats">
                    <?php if ($comments_count = get_comments_number($post->ID)): ?>
                    <span class="archi-comments-count">
                        <span class="dashicons dashicons-admin-comments"></span>
                        <?php echo esc_html($comments_count); ?>
                    </span>
                    <?php endif; ?>
                    
                    <span class="archi-word-count" title="<?php _e('Nombre de mots', 'archi-graph'); ?>">
                        <span class="dashicons dashicons-text-page"></span>
                        <?php echo esc_html(str_word_count(strip_tags($post->post_content))); ?>
                    </span>
                </div>
            </footer>
        </div>
    </article>
    <?php
    return ob_get_clean();
}

/**
 * Get type-specific metadata
 * 
 * @param WP_Post $post
 * @return array
 */
function archi_get_type_specific_metadata($post) {
    $metadata = [];
    
    switch ($post->post_type) {
        case 'archi_project':
            $metadata = [
                'surface' => get_post_meta($post->ID, '_archi_project_surface', true),
                'cost' => get_post_meta($post->ID, '_archi_project_cost', true),
                'client' => get_post_meta($post->ID, '_archi_project_client', true),
                'location' => get_post_meta($post->ID, '_archi_project_location', true),
                'start_date' => get_post_meta($post->ID, '_archi_project_start_date', true),
                'end_date' => get_post_meta($post->ID, '_archi_project_end_date', true),
                'status' => wp_get_post_terms($post->ID, 'archi_project_status', ['fields' => 'names']),
                'type' => wp_get_post_terms($post->ID, 'archi_project_type', ['fields' => 'names']),
            ];
            break;
            
        case 'archi_illustration':
            $metadata = [
                'technique' => get_post_meta($post->ID, '_archi_illustration_technique', true),
                'dimensions' => get_post_meta($post->ID, '_archi_illustration_dimensions', true),
                'software' => get_post_meta($post->ID, '_archi_illustration_software', true),
                'project_link' => get_post_meta($post->ID, '_archi_illustration_project_link', true),
            ];
            break;
    }
    
    return array_filter($metadata); // Remove empty values
}

/**
 * Render type-specific metadata
 * 
 * @param string $post_type
 * @param array $metadata
 * @return string
 */
function archi_render_type_metadata($post_type, $metadata) {
    ob_start();
    
    switch ($post_type) {
        case 'archi_project':
            ?>
            <div class="archi-project-metadata">
                <?php if (!empty($metadata['surface'])): ?>
                <div class="archi-meta-item">
                    <span class="archi-meta-icon">📐</span>
                    <span class="archi-meta-label"><?php _e('Surface:', 'archi-graph'); ?></span>
                    <span class="archi-meta-value"><?php echo esc_html($metadata['surface']); ?> m²</span>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($metadata['cost'])): ?>
                <div class="archi-meta-item">
                    <span class="archi-meta-icon">💰</span>
                    <span class="archi-meta-label"><?php _e('Budget:', 'archi-graph'); ?></span>
                    <span class="archi-meta-value"><?php echo number_format($metadata['cost'], 0, ',', ' '); ?> €</span>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($metadata['location'])): ?>
                <div class="archi-meta-item">
                    <span class="archi-meta-icon">📍</span>
                    <span class="archi-meta-label"><?php _e('Localisation:', 'archi-graph'); ?></span>
                    <span class="archi-meta-value"><?php echo esc_html($metadata['location']); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($metadata['type'])): ?>
                <div class="archi-meta-item">
                    <span class="archi-meta-icon">🏗️</span>
                    <span class="archi-meta-label"><?php _e('Type:', 'archi-graph'); ?></span>
                    <span class="archi-meta-value"><?php echo esc_html(implode(', ', $metadata['type'])); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($metadata['status'])): ?>
                <div class="archi-meta-item">
                    <span class="archi-meta-icon">⏱️</span>
                    <span class="archi-meta-label"><?php _e('Statut:', 'archi-graph'); ?></span>
                    <span class="archi-meta-value"><?php echo esc_html(implode(', ', $metadata['status'])); ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php
            break;
            
        case 'archi_illustration':
            ?>
            <div class="archi-illustration-metadata">
                <?php if (!empty($metadata['technique'])): ?>
                <div class="archi-meta-item">
                    <span class="archi-meta-icon">🎨</span>
                    <span class="archi-meta-label"><?php _e('Technique:', 'archi-graph'); ?></span>
                    <span class="archi-meta-value"><?php echo esc_html($metadata['technique']); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($metadata['software'])): ?>
                <div class="archi-meta-item">
                    <span class="archi-meta-icon">💻</span>
                    <span class="archi-meta-label"><?php _e('Logiciels:', 'archi-graph'); ?></span>
                    <span class="archi-meta-value"><?php echo esc_html($metadata['software']); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($metadata['dimensions'])): ?>
                <div class="archi-meta-item">
                    <span class="archi-meta-icon">📏</span>
                    <span class="archi-meta-label"><?php _e('Dimensions:', 'archi-graph'); ?></span>
                    <span class="archi-meta-value"><?php echo esc_html($metadata['dimensions']); ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php
            break;
    }
    
    return ob_get_clean();
}

/**
 * Shortcode for displaying article cards
 * 
 * Usage: [archi_articles type="archi_project" limit="6" layout="grid"]
 */
function archi_articles_shortcode($atts) {
    $atts = shortcode_atts([
        'type' => 'post',
        'limit' => 10,
        'layout' => 'card',
        'category' => '',
        'tag' => '',
        'orderby' => 'date',
        'order' => 'DESC',
        'show_image' => 'true',
        'show_excerpt' => 'true',
        'show_metadata' => 'true',
        'columns' => 3,
    ], $atts);
    
    $args = [
        'post_type' => explode(',', $atts['type']),
        'posts_per_page' => intval($atts['limit']),
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
    ];
    
    if (!empty($atts['category'])) {
        $args['category_name'] = $atts['category'];
    }
    
    if (!empty($atts['tag'])) {
        $args['tag'] = $atts['tag'];
    }
    
    $posts = get_posts($args);
    
    if (empty($posts)) {
        return '<p class="archi-no-results">' . __('Aucun article trouvé.', 'archi-graph') . '</p>';
    }
    
    ob_start();
    ?>
    <div class="archi-articles-grid" data-columns="<?php echo esc_attr($atts['columns']); ?>">
        <?php foreach ($posts as $post): ?>
            <?php echo archi_render_article_card($post, [
                'layout' => $atts['layout'],
                'show_image' => $atts['show_image'] === 'true',
                'show_excerpt' => $atts['show_excerpt'] === 'true',
                'show_metadata' => $atts['show_metadata'] === 'true',
            ]); ?>
        <?php endforeach; ?>
    </div>
    <?php
    
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('archi_articles', 'archi_articles_shortcode');

