<?php
/**
 * Fonctions helper pour les templates d'articles individuels
 * Centralise la logique commune à tous les types de posts
 *
 * @package Archi_Graph
 */

/**
 * Récupère les métadonnées d'un post selon son type
 *
 * @param int $post_id ID du post
 * @return array Tableau associatif des métadonnées formatées
 */
function archi_get_post_metadata($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $post_type = get_post_type($post_id);
    $metadata = [];
    
    switch ($post_type) {
        case 'archi_project':
            // Métadonnées des projets architecturaux
            $location = get_post_meta($post_id, '_archi_project_location', true);
            $start_date = get_post_meta($post_id, '_archi_project_start_date', true);
            $client = get_post_meta($post_id, '_archi_project_client', true);
            $cost = get_post_meta($post_id, '_archi_project_cost', true);
            $surface = get_post_meta($post_id, '_archi_project_surface', true);
            
            if ($location) {
                $metadata[] = [
                    'label' => __('Localisation', 'archi-graph'),
                    'value' => esc_html($location),
                    'icon' => 'location-alt'
                ];
            }
            
            if ($start_date) {
                $metadata[] = [
                    'label' => __('Année', 'archi-graph'),
                    'value' => date_i18n('Y', strtotime($start_date)),
                    'icon' => 'calendar-alt'
                ];
            }
            
            if ($client) {
                $metadata[] = [
                    'label' => __('Maître d\'ouvrage', 'archi-graph'),
                    'value' => esc_html($client),
                    'icon' => 'businessman'
                ];
            }
            
            if ($cost) {
                $metadata[] = [
                    'label' => __('Coût', 'archi-graph'),
                    'value' => ($cost > 0) ? number_format($cost, 0, ',', ' ') . ' €' : 'nc',
                    'icon' => 'money-alt'
                ];
            }
            
            if ($surface) {
                $metadata[] = [
                    'label' => __('Surface', 'archi-graph'),
                    'value' => number_format($surface, 0, ',', ' ') . ' m²',
                    'icon' => 'editor-expand'
                ];
            }
            break;
            
        case 'archi_illustration':
            // Métadonnées des illustrations
            $technique = get_post_meta($post_id, '_archi_illustration_technique', true);
            $dimensions = get_post_meta($post_id, '_archi_illustration_dimensions', true);
            $software = get_post_meta($post_id, '_archi_illustration_software', true);
            
            if ($technique) {
                $metadata[] = [
                    'label' => __('Technique', 'archi-graph'),
                    'value' => esc_html($technique),
                    'icon' => 'art'
                ];
            }
            
            if ($dimensions) {
                $metadata[] = [
                    'label' => __('Dimensions', 'archi-graph'),
                    'value' => esc_html($dimensions),
                    'icon' => 'image-crop'
                ];
            }
            
            if ($software) {
                $metadata[] = [
                    'label' => __('Logiciels', 'archi-graph'),
                    'value' => esc_html($software),
                    'icon' => 'desktop'
                ];
            }
            break;
            
        case 'archi_guestbook':
            // Métadonnées du livre d'or
            $author_name = get_post_meta($post_id, '_archi_guestbook_author_name', true);
            $author_email = get_post_meta($post_id, '_archi_guestbook_author_email', true);
            $author_company = get_post_meta($post_id, '_archi_guestbook_author_company', true);
            
            if ($author_name) {
                $metadata[] = [
                    'label' => __('Auteur', 'archi-graph'),
                    'value' => esc_html($author_name),
                    'icon' => 'admin-users'
                ];
            }
            
            if ($author_company) {
                $metadata[] = [
                    'label' => __('Organisation', 'archi-graph'),
                    'value' => esc_html($author_company),
                    'icon' => 'building'
                ];
            }
            
            if ($author_email && is_user_logged_in()) {
                $metadata[] = [
                    'label' => __('Email', 'archi-graph'),
                    'value' => '<a href="mailto:' . esc_attr($author_email) . '">' . esc_html($author_email) . '</a>',
                    'icon' => 'email'
                ];
            }
            break;
            
        default:
            // Articles standard (post)
            // Pas de métadonnées spécifiques par défaut
            break;
    }
    
    return apply_filters('archi_post_metadata', $metadata, $post_id, $post_type);
}

/**
 * Affiche les métadonnées d'un post dans une grille
 *
 * @param int $post_id ID du post (optionnel)
 * @return void
 */
function archi_display_post_metadata($post_id = null) {
    $metadata = archi_get_post_metadata($post_id);
    
    if (empty($metadata)) {
        return;
    }
    ?>
    <div class="archi-specs-grid">
        <?php foreach ($metadata as $meta) : ?>
            <div class="spec-item">
                <div class="spec-label">
                    <?php if (!empty($meta['icon'])) : ?>
                        <span class="dashicons dashicons-<?php echo esc_attr($meta['icon']); ?>"></span>
                    <?php endif; ?>
                    <?php echo $meta['label']; ?> :
                </div>
                <div class="spec-value"><?php echo $meta['value']; ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * Récupère les articles similaires intelligemment selon le type de post
 *
 * @param int $post_id ID du post
 * @param int $count Nombre d'articles à récupérer
 * @return array|false Tableau de WP_Post ou false
 */
function archi_get_related_posts($post_id = null, $count = 3) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $post_type = get_post_type($post_id);
    $related = [];
    
    // D'abord, chercher les relations manuelles
    $manual_relations = get_post_meta($post_id, '_archi_related_articles', true);
    if (!empty($manual_relations) && is_array($manual_relations)) {
        $related = get_posts([
            'post__in' => $manual_relations,
            'post_type' => 'any',
            'posts_per_page' => $count,
            'post_status' => 'publish'
        ]);
    }
    
    // Si pas assez d'articles manuels, compléter avec des articles similaires automatiques
    if (count($related) < $count) {
        $remaining = $count - count($related);
        $exclude_ids = array_merge([$post_id], wp_list_pluck($related, 'ID'));
        
        switch ($post_type) {
            case 'archi_project':
                // Projets du même type
                $terms = wp_get_post_terms($post_id, 'archi_project_type', ['fields' => 'ids']);
                if (!empty($terms) && !is_wp_error($terms)) {
                    $auto_related = get_posts([
                        'post_type' => 'archi_project',
                        'numberposts' => $remaining,
                        'post__not_in' => $exclude_ids,
                        'tax_query' => [
                            [
                                'taxonomy' => 'archi_project_type',
                                'field' => 'term_id',
                                'terms' => $terms
                            ]
                        ]
                    ]);
                } else {
                    // Fallback: projets récents
                    $auto_related = get_posts([
                        'post_type' => 'archi_project',
                        'numberposts' => $remaining,
                        'post__not_in' => $exclude_ids
                    ]);
                }
                break;
                
            case 'archi_illustration':
                // Illustrations du même type
                $terms = wp_get_post_terms($post_id, 'illustration_type', ['fields' => 'ids']);
                if (!empty($terms) && !is_wp_error($terms)) {
                    $auto_related = get_posts([
                        'post_type' => 'archi_illustration',
                        'numberposts' => $remaining,
                        'post__not_in' => $exclude_ids,
                        'tax_query' => [
                            [
                                'taxonomy' => 'illustration_type',
                                'field' => 'term_id',
                                'terms' => $terms
                            ]
                        ]
                    ]);
                } else {
                    // Fallback: illustrations récentes
                    $auto_related = get_posts([
                        'post_type' => 'archi_illustration',
                        'numberposts' => $remaining,
                        'post__not_in' => $exclude_ids
                    ]);
                }
                break;
                
            case 'archi_guestbook':
                // Entrées du livre d'or reliées
                $linked_articles = get_post_meta($post_id, '_archi_guestbook_linked_articles', true);
                if (!empty($linked_articles) && is_array($linked_articles)) {
                    $auto_related = get_posts([
                        'post__in' => $linked_articles,
                        'post_type' => 'any',
                        'numberposts' => $remaining,
                        'post__not_in' => $exclude_ids
                    ]);
                } else {
                    $auto_related = [];
                }
                break;
                
            default:
                // Articles standard: même catégorie
                $categories = wp_get_post_categories($post_id);
                if (!empty($categories)) {
                    $auto_related = get_posts([
                        'category__in' => $categories,
                        'numberposts' => $remaining,
                        'post__not_in' => $exclude_ids,
                        'post_type' => $post_type
                    ]);
                } else {
                    // Fallback: articles récents du même type
                    $auto_related = get_posts([
                        'post_type' => $post_type,
                        'numberposts' => $remaining,
                        'post__not_in' => $exclude_ids
                    ]);
                }
                break;
        }
        
        if (!empty($auto_related)) {
            $related = array_merge($related, $auto_related);
        }
    }
    
    return apply_filters('archi_related_posts', $related, $post_id, $count);
}

/**
 * Affiche les articles similaires dans une grille
 *
 * @param int $post_id ID du post (optionnel)
 * @param int $count Nombre d'articles à afficher
 * @return void
 */
function archi_display_related_posts($post_id = null, $count = 3) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $related = archi_get_related_posts($post_id, $count);
    
    if (empty($related)) {
        return;
    }
    
    $post_type = get_post_type($post_id);
    
    // Titre de la section selon le type de post
    $titles = [
        'archi_project' => __('Projets Similaires', 'archi-graph'),
        'archi_illustration' => __('Illustrations Similaires', 'archi-graph'),
        'archi_guestbook' => __('Articles Reliés', 'archi-graph'),
        'post' => __('Articles Similaires', 'archi-graph')
    ];
    
    $section_title = isset($titles[$post_type]) ? $titles[$post_type] : __('Contenus Similaires', 'archi-graph');
    ?>
    <aside class="archi-related-section">
        <h2 class="archi-related-title"><?php echo esc_html($section_title); ?></h2>
        <div class="archi-related-grid">
            <?php foreach ($related as $related_post) : ?>
                <article class="archi-related-card">
                    <a href="<?php echo get_permalink($related_post->ID); ?>" class="archi-related-link">
                        <?php if (has_post_thumbnail($related_post->ID)) : ?>
                            <div class="archi-related-image">
                                <?php echo get_the_post_thumbnail($related_post->ID, 'large'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="archi-related-content">
                            <?php
                            // Type de post badge
                            $related_type = get_post_type($related_post->ID);
                            $type_labels = [
                                'post' => __('Article', 'archi-graph'),
                                'archi_project' => __('Projet', 'archi-graph'),
                                'archi_illustration' => __('Illustration', 'archi-graph'),
                                'archi_guestbook' => __('Livre d\'or', 'archi-graph')
                            ];
                            if (isset($type_labels[$related_type])) : ?>
                                <span class="archi-post-type-badge"><?php echo esc_html($type_labels[$related_type]); ?></span>
                            <?php endif; ?>
                            
                            <h3 class="archi-related-card-title">
                                <?php echo esc_html($related_post->post_title); ?>
                            </h3>
                            
                            <?php
                            // Afficher une métadonnée contextuelle
                            if ($related_type === 'archi_project') {
                                $location = get_post_meta($related_post->ID, '_archi_project_location', true);
                                if ($location) {
                                    echo '<p class="archi-related-meta">' . esc_html($location) . '</p>';
                                }
                            }
                            ?>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </aside>
    <?php
}

/**
 * Récupère le nom d'affichage du type de post
 *
 * @param string $post_type Type de post
 * @return string Nom d'affichage
 */
function archi_get_post_type_label($post_type = null) {
    if (!$post_type) {
        $post_type = get_post_type();
    }
    
    $labels = [
        'post' => __('Article', 'archi-graph'),
        'archi_project' => __('Projet Architectural', 'archi-graph'),
        'archi_illustration' => __('Illustration', 'archi-graph'),
        'archi_guestbook' => __('Entrée du Livre d\'or', 'archi-graph')
    ];
    
    return isset($labels[$post_type]) ? $labels[$post_type] : get_post_type_object($post_type)->labels->singular_name;
}
