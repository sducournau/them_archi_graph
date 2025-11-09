<?php
/**
 * Fonctions utilitaires partagées entre les blocs
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Générer des classes CSS pour un bloc
 */
function archi_get_block_classes($attributes, $base_class = '') {
    $classes = [$base_class];
    
    // Alignement
    if (!empty($attributes['align'])) {
        $classes[] = 'align' . $attributes['align'];
    }
    
    // Classes personnalisées
    if (!empty($attributes['className'])) {
        $classes[] = $attributes['className'];
    }
    
    // Couleurs
    if (!empty($attributes['backgroundColor'])) {
        $classes[] = 'has-background';
        $classes[] = 'has-' . $attributes['backgroundColor'] . '-background-color';
    }
    
    if (!empty($attributes['textColor'])) {
        $classes[] = 'has-text-color';
        $classes[] = 'has-' . $attributes['textColor'] . '-color';
    }
    
    return implode(' ', array_filter($classes));
}

/**
 * Générer des styles inline pour un bloc
 */
function archi_get_block_styles($attributes) {
    $styles = [];
    
    // Couleurs personnalisées
    if (!empty($attributes['customBackgroundColor'])) {
        $styles[] = 'background-color: ' . esc_attr($attributes['customBackgroundColor']);
    }
    
    if (!empty($attributes['customTextColor'])) {
        $styles[] = 'color: ' . esc_attr($attributes['customTextColor']);
    }
    
    // Gap pour les grids
    if (isset($attributes['gap'])) {
        $styles[] = 'gap: ' . absint($attributes['gap']) . 'px';
    }
    
    return !empty($styles) ? ' style="' . implode('; ', $styles) . '"' : '';
}

/**
 * Query builder pour les posts
 */
function archi_build_posts_query($attributes) {
    $defaults = [
        'post_type' => ['post'],
        'posts_per_page' => 10,
        'orderby' => 'date',
        'order' => 'DESC',
        'post_status' => 'publish'
    ];
    
    $args = $defaults;
    
    // Post types
    if (!empty($attributes['postTypes'])) {
        $args['post_type'] = $attributes['postTypes'];
    }
    
    // Nombre max
    if (isset($attributes['maxItems'])) {
        $args['posts_per_page'] = absint($attributes['maxItems']);
    }
    
    // Ordre
    if (!empty($attributes['orderBy'])) {
        $args['orderby'] = sanitize_text_field($attributes['orderBy']);
    }
    
    if (!empty($attributes['order'])) {
        $args['order'] = sanitize_text_field($attributes['order']);
    }
    
    // Taxonomies
    $tax_query = [];
    
    if (!empty($attributes['categories'])) {
        $tax_query[] = [
            'taxonomy' => 'category',
            'field' => 'term_id',
            'terms' => array_map('absint', $attributes['categories'])
        ];
    }
    
    if (!empty($attributes['tags'])) {
        $tax_query[] = [
            'taxonomy' => 'post_tag',
            'field' => 'term_id',
            'terms' => array_map('absint', $attributes['tags'])
        ];
    }
    
    if (!empty($tax_query)) {
        $tax_query['relation'] = 'AND';
        $args['tax_query'] = $tax_query;
    }
    
    return $args;
}

/**
 * Render post metadata
 */
function archi_render_post_meta($post_id, $attributes) {
    $output = '<div class="archi-post-meta">';
    
    // Date
    if (!empty($attributes['showDate'])) {
        $output .= sprintf(
            '<span class="post-date">%s</span>',
            get_the_date('', $post_id)
        );
    }
    
    // Auteur
    if (!empty($attributes['showAuthor'])) {
        $output .= sprintf(
            '<span class="post-author">%s</span>',
            get_the_author_meta('display_name', get_post_field('post_author', $post_id))
        );
    }
    
    // Catégories
    if (!empty($attributes['showCategories'])) {
        $categories = get_the_category($post_id);
        if (!empty($categories)) {
            $cat_links = array_map(function($cat) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    esc_url(get_category_link($cat->term_id)),
                    esc_html($cat->name)
                );
            }, $categories);
            
            $output .= '<span class="post-categories">' . implode(', ', $cat_links) . '</span>';
        }
    }
    
    // Tags
    if (!empty($attributes['showTags'])) {
        $tags = get_the_tags($post_id);
        if (!empty($tags)) {
            $tag_links = array_map(function($tag) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    esc_url(get_tag_link($tag->term_id)),
                    esc_html($tag->name)
                );
            }, $tags);
            
            $output .= '<span class="post-tags">' . implode(', ', $tag_links) . '</span>';
        }
    }
    
    $output .= '</div>';
    
    return $output;
}

/**
 * Render featured image
 */
function archi_render_featured_image($post_id, $size = 'medium', $attributes = []) {
    if (empty($attributes['showFeaturedImage'])) {
        return '';
    }
    
    if (!has_post_thumbnail($post_id)) {
        return '';
    }
    
    return sprintf(
        '<div class="archi-featured-image">%s</div>',
        get_the_post_thumbnail($post_id, $size, ['class' => 'archi-block-image'])
    );
}

/**
 * Wrapper pour le output buffering
 */
function archi_render_block($callback, ...$args) {
    ob_start();
    call_user_func_array($callback, $args);
    return ob_get_clean();
}

/**
 * Sanitize block attributes
 */
function archi_sanitize_block_attributes($attributes, $schema) {
    $sanitized = [];
    
    foreach ($schema as $key => $definition) {
        if (!isset($attributes[$key])) {
            $sanitized[$key] = $definition['default'] ?? null;
            continue;
        }
        
        $value = $attributes[$key];
        $type = $definition['type'] ?? 'string';
        
        switch ($type) {
            case 'boolean':
                $sanitized[$key] = (bool) $value;
                break;
            case 'number':
                $sanitized[$key] = is_numeric($value) ? (int) $value : $definition['default'];
                break;
            case 'array':
                $sanitized[$key] = is_array($value) ? $value : [];
                break;
            case 'string':
            default:
                $sanitized[$key] = sanitize_text_field($value);
                break;
        }
    }
    
    return $sanitized;
}

/**
 * Validation des attributs
 */
function archi_validate_block_attributes($attributes, $required_fields = []) {
    foreach ($required_fields as $field) {
        if (empty($attributes[$field])) {
            return new WP_Error(
                'missing_attribute',
                sprintf(__('Attribut requis manquant: %s', 'archi-graph'), $field)
            );
        }
    }
    
    return true;
}
