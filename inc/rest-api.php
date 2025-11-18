<?php
/**
 * API REST personnalisée pour le graphique
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistrement des routes API REST
 */
function archi_register_rest_routes() {
    register_rest_route('archi/v1', '/articles', [
        'methods' => 'GET',
        'callback' => 'archi_get_articles_for_graph',
        'permission_callback' => '__return_true',
        'args' => [
            'category' => [
                'validate_callback' => function($param, $request, $key) {
                    return is_numeric($param) || empty($param);
                },
                'sanitize_callback' => 'absint'
            ],
            'limit' => [
                'default' => -1,
                'validate_callback' => function($param, $request, $key) {
                    return is_numeric($param);
                },
                'sanitize_callback' => function($param) {
                    // Permettre -1 pour "tous les posts"
                    return intval($param);
                }
            ]
        ]
    ]);
    
    register_rest_route('archi/v1', '/categories', [
        'methods' => 'GET',
        'callback' => 'archi_get_categories_for_graph',
        'permission_callback' => '__return_true'
    ]);
    
    register_rest_route('archi/v1', '/save-positions', [
        'methods' => 'POST',
        'callback' => 'archi_save_node_positions',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        }
    ]);
    
    register_rest_route('archi/v1', '/proximity-analysis', [
        'methods' => 'GET',
        'callback' => 'archi_get_proximity_analysis',
        'permission_callback' => '__return_true'
    ]);
    
    register_rest_route('archi/v1', '/related-articles/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'archi_get_related_articles',
        'permission_callback' => '__return_true',
        'args' => [
            'id' => [
                'validate_callback' => function($param) {
                    return is_numeric($param);
                },
                'sanitize_callback' => 'absint'
            ],
            'limit' => [
                'default' => 5,
                'validate_callback' => function($param) {
                    return is_numeric($param);
                },
                'sanitize_callback' => 'absint'
            ]
        ]
    ]);
}
add_action('rest_api_init', 'archi_register_rest_routes');

/**
 * Récupérer les articles pour le graphique
 */
function archi_get_articles_for_graph($request) {
    $category = $request->get_param('category');
    $limit = $request->get_param('limit');
    
    // Inclure tous les types de posts personnalisés (sauf archi_guestbook)
    $post_types = ['post', 'archi_project', 'archi_illustration'];
    
    // FORCER -1 pour récupérer TOUS les posts (pas de limite)
    // Même si limit vaut 0 ou autre, on force -1
    $posts_per_page = -1;
    if ($limit && $limit > 0) {
        $posts_per_page = $limit;
    }
    
    $args = [
        'post_type' => $post_types,
        'post_status' => 'publish',
        'posts_per_page' => $posts_per_page,
        'orderby' => 'date',
        'order' => 'DESC',
        'no_found_rows' => false,
        'meta_query' => [
            'relation' => 'OR',
            [
                'key' => '_archi_show_in_graph',
                'value' => '1',
                'compare' => '='
            ],
            [
                'key' => '_archi_show_in_graph',
                'value' => 1,
                'compare' => '=',
                'type' => 'NUMERIC'
            ]
        ]
    ];
    
    if (!empty($category)) {
        $args['cat'] = $category;
    }
    
    // UTILISER WP_Query AU LIEU DE get_posts() pour éviter les limitations
    $query = new WP_Query($args);
    $posts = $query->posts;
    
    $articles = [];
    
    foreach ($posts as $post) {
        $categories = get_the_category($post->ID);
        $tags = get_the_tags($post->ID);
        
        // Utiliser l'image en taille réelle (full) pour tous les types
        $thumbnail = get_the_post_thumbnail_url($post->ID, 'full');
        
        // Utiliser une image par défaut si pas de thumbnail
        if (!$thumbnail) {
            $thumbnail = ARCHI_THEME_URI . '/assets/images/placeholder-node.svg';
        }
        
        // Déterminer la couleur par défaut selon le type de post
        $default_color = '#3498db'; // Bleu pour les posts normaux
        if ($post->post_type === 'archi_project') {
            $default_color = '#e67e22'; // Orange pour les projets
        } elseif ($post->post_type === 'archi_illustration') {
            $default_color = '#9b59b6'; // Violet pour les illustrations
        } elseif ($post->post_type === 'archi_guestbook') {
            $default_color = '#2ecc71'; // Vert pour le livre d'or
        }
        
        // Métadonnées spécifiques aux illustrations
        $illustration_meta = [];
        if ($post->post_type === 'archi_illustration') {
            $illustration_meta = [
                'technique' => get_post_meta($post->ID, '_archi_illustration_technique', true),
                'dimensions' => get_post_meta($post->ID, '_archi_illustration_dimensions', true),
                'software' => get_post_meta($post->ID, '_archi_illustration_software', true),
                'project_link' => get_post_meta($post->ID, '_archi_illustration_project_link', true),
            ];
        }
        
        // ✨ NOUVEAU: Métadonnées spécifiques aux projets architecturaux
        $project_meta = [];
        if ($post->post_type === 'archi_project') {
            $project_meta = [
                'surface' => get_post_meta($post->ID, '_archi_project_surface', true),
                'cost' => get_post_meta($post->ID, '_archi_project_cost', true),
                'client' => get_post_meta($post->ID, '_archi_project_client', true),
                'location' => get_post_meta($post->ID, '_archi_project_location', true),
                'start_date' => get_post_meta($post->ID, '_archi_project_start_date', true),
                'end_date' => get_post_meta($post->ID, '_archi_project_end_date', true),
                'project_type' => get_post_meta($post->ID, '_archi_project_type', true),
                'certifications' => get_post_meta($post->ID, '_archi_project_certifications', true),
            ];
        }
        
        // Métadonnées spécifiques au livre d'or
        $guestbook_meta = [];
        if ($post->post_type === 'archi_guestbook') {
            $guestbook_meta = [
                'author_name' => get_post_meta($post->ID, '_archi_guestbook_author_name', true),
                'author_email' => get_post_meta($post->ID, '_archi_guestbook_author_email', true),
                'author_company' => get_post_meta($post->ID, '_archi_guestbook_author_company', true),
                'linked_articles' => get_post_meta($post->ID, '_archi_linked_articles', true) ?: [],
            ];
        }
        
        // Utiliser full pour tous les types (thumbnail_large identique à thumbnail)
        $thumbnail_large = get_the_post_thumbnail_url($post->ID, 'full');
        
        // ✅ UNIFIED INTERFACE: Get all graph parameters using centralized function
        // This replaces individual get_post_meta() calls and ensures consistency
        $graph_params = archi_get_graph_params($post->ID, true);
        
        // Build article data structure
        $article = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'excerpt' => wp_trim_words($post->post_content, 20, '...'),
            'permalink' => get_permalink($post->ID),
            'thumbnail' => $thumbnail,
            'thumbnail_large' => $thumbnail_large ?: $thumbnail,
            'date' => $post->post_date,
            'post_type' => $post->post_type,
            'post_type_label' => get_post_type_object($post->post_type)->labels->singular_name,
            'categories' => [],
            'tags' => [],
        ];
        
        // ✅ Merge all graph parameters into article root for backward compatibility
        // This ensures existing frontend code continues to work while providing all 23 parameters
        $article = array_merge($article, $graph_params);
        
        // ✅ NEW: Add comments node metadata
        $comments_list = [];
        $recent_comments = get_comments([
            'post_id' => $post->ID,
            'status' => 'approve',
            'number' => 5,
            'orderby' => 'comment_date',
            'order' => 'DESC'
        ]);
        
        foreach ($recent_comments as $comment) {
            $comments_list[] = [
                'author' => $comment->comment_author,
                'date' => $comment->comment_date,
                'content' => wp_trim_words($comment->comment_content, 30, '...')
            ];
        }
        
        $article['comments'] = [
            'show_as_node' => get_post_meta($post->ID, '_archi_show_comments_node', true) === '1',
            'count' => get_comments_number($post->ID),
            'node_color' => get_post_meta($post->ID, '_archi_comment_node_color', true) ?: '#16a085',
            'recent' => $comments_list
        ];
        
        // Ajouter les métadonnées spécifiques aux illustrations
        if (!empty($illustration_meta)) {
            $article['illustration_meta'] = $illustration_meta;
        }
        
        // ✨ NOUVEAU: Ajouter les métadonnées spécifiques aux projets
        if (!empty($project_meta)) {
            $article['project_meta'] = $project_meta;
        }
        
        // Ajouter les métadonnées du livre d'or
        if (!empty($guestbook_meta)) {
            $article['guestbook_meta'] = $guestbook_meta;
        }
        
        // Formater les catégories
        if ($categories) {
            foreach ($categories as $category) {
                $article['categories'][] = [
                    'id' => $category->term_id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'color' => get_term_meta($category->term_id, '_archi_category_color', true) ?: '#' . substr(md5($category->name), 0, 6)
                ];
            }
        }
        
        // Formater les tags
        if ($tags) {
            foreach ($tags as $tag) {
                $article['tags'][] = [
                    'id' => $tag->term_id,
                    'name' => $tag->name,
                    'slug' => $tag->slug
                ];
            }
        }
        
        $articles[] = $article;
    }
    
    $response = [
        'articles' => $articles,
        'total' => count($articles),
        'timestamp' => current_time('timestamp')
    ];
    
    return rest_ensure_response($response);
}

/**
 * Récupérer les catégories pour le graphique
 */
function archi_get_categories_for_graph($request) {
    $categories = get_categories([
        'hide_empty' => true,
        'orderby' => 'count',
        'order' => 'DESC'
    ]);
    
    $formatted_categories = [];
    
    foreach ($categories as $category) {
        $color = get_term_meta($category->term_id, '_archi_category_color', true);
        if (!$color) {
            // Générer une couleur basée sur le nom de la catégorie
            $color = '#' . substr(md5($category->name), 0, 6);
        }
        
        $formatted_categories[] = [
            'id' => $category->term_id,
            'name' => $category->name,
            'slug' => $category->slug,
            'count' => $category->count,
            'description' => $category->description,
            'color' => $color,
            'link' => get_category_link($category->term_id)
        ];
    }
    
    return rest_ensure_response($formatted_categories);
}

/**
 * Internal function to save node positions (shared by multiple endpoints)
 * 
 * @param array $positions Array of position data
 * @return array Result with success status and count
 */
function archi_save_positions_internal($positions) {
    if (!is_array($positions)) {
        return [
            'success' => false,
            'saved' => 0,
            'errors' => [__('Données de position invalides', 'archi-graph')],
            'message' => __('Erreur de format de données', 'archi-graph')
        ];
    }
    
    $saved_count = 0;
    $errors = [];
    
    foreach ($positions as $position_data) {
        if (!isset($position_data['id']) || !isset($position_data['x']) || !isset($position_data['y'])) {
            continue;
        }
        
        $post_id = absint($position_data['id']);
        $x = floatval($position_data['x']);
        $y = floatval($position_data['y']);
        
        if ($post_id > 0) {
            $post = get_post($post_id);
            if (!$post) {
                $errors[] = ['id' => $post_id, 'message' => 'Post not found'];
                continue;
            }
            
            $position = [
                'x' => $x,
                'y' => $y,
                'timestamp' => current_time('timestamp')
            ];
            
            update_post_meta($post_id, '_archi_graph_position', $position);
            $saved_count++;
        }
    }
    
    // Clear cache
    delete_transient('archi_graph_articles');
    
    return [
        'success' => true,
        'saved' => $saved_count,
        'errors' => $errors,
        'message' => sprintf(__('%d positions sauvegardées', 'archi-graph'), $saved_count)
    ];
}

/**
 * Sauvegarder les positions des nœuds
 */
function archi_save_node_positions($request) {
    $positions = $request->get_json_params();
    $result = archi_save_positions_internal($positions);
    return rest_ensure_response($result);
}

/**
 * Analyse de proximité entre tous les articles
 */
function archi_get_proximity_analysis($request) {
    $articles_request = new WP_REST_Request('GET', '/archi/v1/articles');
    $articles_response = archi_get_articles_for_graph($articles_request);
    $articles_data = $articles_response->get_data();
    $articles = $articles_data['articles'];
    
    $proximities = [];
    $stats = [
        'total_comparisons' => 0,
        'total_links' => 0,
        'avg_score' => 0,
        'distribution' => [
            'very_strong' => 0,
            'strong' => 0,
            'medium' => 0,
            'weak' => 0
        ],
        'top_factors' => [
            'categories' => 0,
            'tags' => 0,
            'primary_category' => 0,
            'date_proximity' => 0
        ]
    ];
    
    $total_score = 0;
    
    // Calculer la proximité entre tous les articles
    for ($i = 0; $i < count($articles); $i++) {
        for ($j = $i + 1; $j < count($articles); $j++) {
            $articleA = $articles[$i];
            $articleB = $articles[$j];
            
            $proximity = archi_calculate_proximity_score($articleA, $articleB);
            $stats['total_comparisons']++;
            
            // Ne garder que les liens significatifs (score >= 20)
            if ($proximity['score'] >= 20) {
                $proximities[] = [
                    'article_a' => $articleA['id'],
                    'article_b' => $articleB['id'],
                    'score' => $proximity['score'],
                    'strength' => $proximity['strength'],
                    'details' => $proximity['details']
                ];
                
                $stats['total_links']++;
                $total_score += $proximity['score'];
                
                // Distribution par force
                if ($proximity['score'] >= 100) {
                    $stats['distribution']['very_strong']++;
                } elseif ($proximity['score'] >= 70) {
                    $stats['distribution']['strong']++;
                } elseif ($proximity['score'] >= 40) {
                    $stats['distribution']['medium']++;
                } else {
                    $stats['distribution']['weak']++;
                }
                
                // Comptabiliser les facteurs
                if ($proximity['details']['shared_categories_count'] > 0) {
                    $stats['top_factors']['categories']++;
                }
                if ($proximity['details']['shared_tags_count'] > 0) {
                    $stats['top_factors']['tags']++;
                }
                if ($proximity['details']['same_primary_category']) {
                    $stats['top_factors']['primary_category']++;
                }
                if (isset($proximity['details']['date_proximity_score'])) {
                    $stats['top_factors']['date_proximity']++;
                }
            }
        }
    }
    
    $stats['avg_score'] = $stats['total_links'] > 0 ? 
        round($total_score / $stats['total_links'], 2) : 0;
    
    return rest_ensure_response([
        'proximities' => $proximities,
        'stats' => $stats,
        'timestamp' => current_time('timestamp')
    ]);
}

/**
 * Récupérer les articles les plus proches d'un article donné
 */
function archi_get_related_articles($request) {
    $article_id = $request->get_param('id');
    $limit = $request->get_param('limit');
    
    // Vérifier que l'article existe
    $main_article = get_post($article_id);
    if (!$main_article || $main_article->post_status !== 'publish') {
        return new WP_Error('article_not_found', 'Article non trouvé', ['status' => 404]);
    }
    
    // Récupérer tous les articles
    $articles_request = new WP_REST_Request('GET', '/archi/v1/articles');
    $articles_response = archi_get_articles_for_graph($articles_request);
    $articles_data = $articles_response->get_data();
    $articles = $articles_data['articles'];
    
    // Trouver l'article principal dans la liste
    $main_article_data = null;
    foreach ($articles as $article) {
        if ($article['id'] == $article_id) {
            $main_article_data = $article;
            break;
        }
    }
    
    if (!$main_article_data) {
        return new WP_Error('article_not_in_graph', 'Article non disponible dans le graphique', ['status' => 404]);
    }
    
    // Calculer la proximité avec tous les autres articles
    $related = [];
    foreach ($articles as $article) {
        if ($article['id'] == $article_id) continue;
        
        $proximity = archi_calculate_proximity_score($main_article_data, $article);
        
        if ($proximity['score'] > 0) {
            $related[] = [
                'article' => $article,
                'proximity' => $proximity
            ];
        }
    }
    
    // Trier par score décroissant
    usort($related, function($a, $b) {
        return $b['proximity']['score'] - $a['proximity']['score'];
    });
    
    // Limiter le résultat
    $related = array_slice($related, 0, $limit);
    
    return rest_ensure_response([
        'main_article' => $main_article_data,
        'related_articles' => $related,
        'total_found' => count($related),
        'timestamp' => current_time('timestamp')
    ]);
}

/**
 * Calculer le score de proximité entre deux articles
 * Basé sur catégories, tags, dates, etc.
 * 
 * @param array $article_a Premier article
 * @param array $article_b Deuxième article
 * @return array Score et détails
 */
function archi_calculate_proximity_score($article_a, $article_b) {
    // Utiliser le calculateur de proximité si disponible
    if (class_exists('Archi_Proximity_Calculator')) {
        return Archi_Proximity_Calculator::calculate_proximity($article_a, $article_b);
    }
    
    // Fallback sur l'ancien système
    $weights = [
        'shared_category' => 40,
        'shared_tag' => 25,
        'same_primary_category' => 20,
        'date_proximity' => 10,
        'content_similarity' => 5
    ];
    
    $score = 0;
    $details = [
        'shared_categories' => [],
        'shared_tags' => [],
        'shared_categories_count' => 0,
        'shared_tags_count' => 0,
        'same_primary_category' => false,
        'factors' => []
    ];
    
    // 1. Catégories partagées
    $shared_categories = [];
    foreach ($article_a['categories'] as $cat_a) {
        foreach ($article_b['categories'] as $cat_b) {
            if ($cat_a['id'] === $cat_b['id']) {
                $shared_categories[] = $cat_a;
            }
        }
    }
    
    if (count($shared_categories) > 0) {
        $category_score = $weights['shared_category'] * count($shared_categories);
        $score += $category_score;
        $details['shared_categories'] = $shared_categories;
        $details['shared_categories_count'] = count($shared_categories);
        $details['factors']['categories'] = $category_score;
    }
    
    // 2. Catégorie principale identique (bonus)
    if (!empty($article_a['categories']) && !empty($article_b['categories'])) {
        if ($article_a['categories'][0]['id'] === $article_b['categories'][0]['id']) {
            $score += $weights['same_primary_category'];
            $details['same_primary_category'] = true;
            $details['factors']['primary_category'] = $weights['same_primary_category'];
        }
    }
    
    // 3. Tags partagés
    $shared_tags = [];
    if (!empty($article_a['tags']) && !empty($article_b['tags'])) {
        foreach ($article_a['tags'] as $tag_a) {
            foreach ($article_b['tags'] as $tag_b) {
                if ($tag_a['id'] === $tag_b['id']) {
                    $shared_tags[] = $tag_a;
                }
            }
        }
    }
    
    if (count($shared_tags) > 0) {
        $tag_score = $weights['shared_tag'] * count($shared_tags);
        $score += $tag_score;
        $details['shared_tags'] = $shared_tags;
        $details['shared_tags_count'] = count($shared_tags);
        $details['factors']['tags'] = $tag_score;
    }
    
    // 4. Proximité temporelle
    if (!empty($article_a['date']) && !empty($article_b['date'])) {
        $date_a = strtotime($article_a['date']);
        $date_b = strtotime($article_b['date']);
        $days_diff = abs(($date_a - $date_b) / (60 * 60 * 24));
        
        $details['date_proximity_days'] = round($days_diff);
        
        if ($days_diff <= 7) {
            $score += $weights['date_proximity'];
            $details['factors']['date_proximity'] = $weights['date_proximity'];
            $details['date_proximity_score'] = $weights['date_proximity'];
        } elseif ($days_diff <= 30) {
            $date_score = $weights['date_proximity'] * 0.5;
            $score += $date_score;
            $details['factors']['date_proximity'] = $date_score;
            $details['date_proximity_score'] = $date_score;
        }
    }
    
    // 5. Similarité de contenu (longueur)
    if (!empty($article_a['excerpt']) && !empty($article_b['excerpt'])) {
        $length_a = strlen($article_a['excerpt']);
        $length_b = strlen($article_b['excerpt']);
        $length_ratio = min($length_a, $length_b) / max($length_a, $length_b);
        
        if ($length_ratio > 0.7) {
            $score += $weights['content_similarity'];
            $details['factors']['content_similarity'] = $weights['content_similarity'];
        }
    }
    
    // Déterminer la force du lien
    $strength = 'very-weak';
    if ($score >= 100) {
        $strength = 'very-strong';
    } elseif ($score >= 70) {
        $strength = 'strong';
    } elseif ($score >= 40) {
        $strength = 'medium';
    } elseif ($score >= 20) {
        $strength = 'weak';
    }
    
    return [
        'score' => round($score),
        'strength' => $strength,
        'details' => $details
    ];
}

/**
 * Fonction helper pour optimiser les requêtes avec cache
 */
function archi_get_cached_articles($cache_key = 'archi_graph_articles', $expiry = HOUR_IN_SECONDS) {
    $cached_data = get_transient($cache_key);
    
    if ($cached_data !== false) {
        return $cached_data;
    }
    
    // Récupérer les données fraîches
    $request = new WP_REST_Request('GET', '/archi/v1/articles');
    $response = archi_get_articles_for_graph($request);
    $data = $response->get_data();
    
    // Mettre en cache
    set_transient($cache_key, $data, $expiry);
    
    return $data;
}

/**
 * Invalider le cache quand un article est modifié
 */
function archi_clear_articles_cache($post_id) {
    if (get_post_type($post_id) === 'post') {
        delete_transient('archi_graph_articles');
    }
}
add_action('save_post', 'archi_clear_articles_cache');
add_action('delete_post', 'archi_clear_articles_cache');

/**
 * Ajouter des headers CORS si nécessaire
 */
function archi_add_cors_headers() {
    $origin = get_http_origin();
    
    if ($origin && in_array($origin, [home_url(), get_site_url()])) {
        header("Access-Control-Allow-Origin: $origin");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-WP-Nonce');
    }
}
add_action('rest_api_init', 'archi_add_cors_headers');

// ============================================================================
// MERGED FROM advanced-graph-rest-api.php - Advanced Graph Parameters
// ============================================================================

/**
 * Ajouter les paramètres avancés aux données REST des articles
 */
function archi_add_advanced_graph_fields_to_rest() {
    $post_types = ['post', 'archi_project', 'archi_illustration'];
    
    foreach ($post_types as $post_type) {
        // Ajouter un champ groupé pour tous les paramètres avancés
        register_rest_field($post_type, 'advanced_graph_params', [
            'get_callback' => 'archi_get_advanced_graph_params',
            'update_callback' => 'archi_update_advanced_graph_params',
            'schema' => [
                'description' => __('Paramètres avancés pour le graphique', 'archi-graph'),
                'type' => 'object',
                'context' => ['view', 'edit'],
                'properties' => [
                    'node_shape' => ['type' => 'string', 'enum' => ['circle', 'square', 'diamond', 'triangle', 'star', 'hexagon']],
                    'node_icon' => ['type' => 'string'],
                    'visual_group' => ['type' => 'string'],
                    'node_opacity' => ['type' => 'number'],
                    'node_border' => ['type' => 'string', 'enum' => ['none', 'solid', 'dashed', 'dotted', 'glow']],
                    'border_color' => ['type' => 'string'],
                    'node_weight' => ['type' => 'integer'],
                    'hover_effect' => ['type' => 'string', 'enum' => ['none', 'zoom', 'pulse', 'glow', 'rotate', 'bounce']],
                    'entrance_animation' => ['type' => 'string', 'enum' => ['none', 'fade', 'scale', 'slide', 'bounce']],
                    'pin_node' => ['type' => 'boolean'],
                    'node_label' => ['type' => 'string'],
                    'show_label' => ['type' => 'boolean'],
                    'node_badge' => ['type' => 'string', 'enum' => ['', 'new', 'featured', 'hot', 'updated', 'popular']],
                    'connection_depth' => ['type' => 'integer'],
                    'link_strength' => ['type' => 'number'],
                    'link_style' => ['type' => 'string', 'enum' => ['straight', 'curve', 'wave', 'dotted', 'dashed']]
                ]
            ]
        ]);
    }
}
add_action('rest_api_init', 'archi_add_advanced_graph_fields_to_rest');

/**
 * Récupérer tous les paramètres avancés d'un article
 */
function archi_get_advanced_graph_params($post) {
    return [
        'node_shape' => get_post_meta($post['id'], '_archi_node_shape', true) ?: 'circle',
        'node_icon' => get_post_meta($post['id'], '_archi_node_icon', true) ?: '',
        'visual_group' => get_post_meta($post['id'], '_archi_visual_group', true) ?: '',
        'node_opacity' => floatval(get_post_meta($post['id'], '_archi_node_opacity', true)) ?: 1.0,
        'node_border' => get_post_meta($post['id'], '_archi_node_border', true) ?: 'none',
        'border_color' => get_post_meta($post['id'], '_archi_border_color', true) ?: '',
        'node_weight' => intval(get_post_meta($post['id'], '_archi_node_weight', true)) ?: 1,
        'hover_effect' => get_post_meta($post['id'], '_archi_hover_effect', true) ?: 'zoom',
        'entrance_animation' => get_post_meta($post['id'], '_archi_entrance_animation', true) ?: 'fade',
        'animation_level' => get_post_meta($post['id'], '_archi_animation_level', true) ?: 'normal',
        'animation' => [
            'duration' => intval(get_post_meta($post['id'], '_archi_animation_duration', true) ?: 800),
            'delay' => intval(get_post_meta($post['id'], '_archi_animation_delay', true) ?: 0),
            'easing' => get_post_meta($post['id'], '_archi_animation_easing', true) ?: 'ease-out',
            'enterFrom' => get_post_meta($post['id'], '_archi_enter_from', true) ?: 'center',
        ],
        'hover' => [
            'scale' => floatval(get_post_meta($post['id'], '_archi_hover_scale', true) ?: 1.15),
            'pulse' => get_post_meta($post['id'], '_archi_pulse_effect', true) === '1',
            'glow' => get_post_meta($post['id'], '_archi_glow_effect', true) === '1',
        ],
        'pin_node' => get_post_meta($post['id'], '_archi_pin_node', true) === '1',
        'node_label' => get_post_meta($post['id'], '_archi_node_label', true) ?: '',
        'show_label' => get_post_meta($post['id'], '_archi_show_label', true) === '1',
        'node_badge' => get_post_meta($post['id'], '_archi_node_badge', true) ?: '',
        'connection_depth' => intval(get_post_meta($post['id'], '_archi_connection_depth', true)) ?: 2,
        'link_strength' => floatval(get_post_meta($post['id'], '_archi_link_strength', true)) ?: 1.0,
        'link_style' => get_post_meta($post['id'], '_archi_link_style', true) ?: 'curve'
    ];
}

/**
 * Mettre à jour les paramètres avancés via REST
 */
function archi_update_advanced_graph_params($params, $post) {
    if (!current_user_can('edit_post', $post->ID)) {
        return new WP_Error('rest_cannot_edit', __('Vous ne pouvez pas modifier cet article', 'archi-graph'), ['status' => 403]);
    }
    
    $meta_mapping = [
        'node_shape' => '_archi_node_shape',
        'node_icon' => '_archi_node_icon',
        'visual_group' => '_archi_visual_group',
        'node_opacity' => '_archi_node_opacity',
        'node_border' => '_archi_node_border',
        'border_color' => '_archi_border_color',
        'node_weight' => '_archi_node_weight',
        'hover_effect' => '_archi_hover_effect',
        'entrance_animation' => '_archi_entrance_animation',
        'animation_level' => '_archi_animation_level',
        'pin_node' => '_archi_pin_node',
        'node_label' => '_archi_node_label',
        'show_label' => '_archi_show_label',
        'node_badge' => '_archi_node_badge',
        'connection_depth' => '_archi_connection_depth',
        'link_strength' => '_archi_link_strength',
        'link_style' => '_archi_link_style'
    ];
    
    foreach ($params as $key => $value) {
        if (isset($meta_mapping[$key])) {
            update_post_meta($post->ID, $meta_mapping[$key], $value);
        }
    }
    
    delete_transient('archi_graph_articles');
    return true;
}

/**
 * Endpoint pour obtenir la configuration par défaut du graphique
 */
function archi_register_graph_defaults_endpoint() {
    register_rest_route('archi/v1', '/graph-defaults', [
        'methods' => 'GET',
        'callback' => 'archi_get_graph_defaults',
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'archi_register_graph_defaults_endpoint');

/**
 * Récupérer les valeurs par défaut pour chaque type de post
 */
function archi_get_graph_defaults() {
    return [
        'post' => [
            'node_color' => '#3498db',
            'node_size' => 80,
            'node_shape' => 'circle',
            'priority_level' => 'normal',
            'node_opacity' => 1.0,
            'hover_effect' => 'zoom',
            'entrance_animation' => 'fade'
        ],
        'archi_project' => [
            'node_color' => '#e74c3c',
            'node_size' => 80,
            'node_shape' => 'square',
            'priority_level' => 'high',
            'node_opacity' => 1.0,
            'hover_effect' => 'glow',
            'entrance_animation' => 'scale'
        ],
        'archi_illustration' => [
            'node_color' => '#f39c12',
            'node_size' => 70,
            'node_shape' => 'diamond',
            'priority_level' => 'normal',
            'node_opacity' => 1.0,
            'hover_effect' => 'pulse',
            'entrance_animation' => 'fade'
        ],
        'shapes' => [
            'circle' => ['label' => __('Cercle', 'archi-graph'), 'path' => 'circle'],
            'square' => ['label' => __('Carré', 'archi-graph'), 'path' => 'rect'],
            'diamond' => ['label' => __('Diamant', 'archi-graph'), 'path' => 'polygon'],
            'triangle' => ['label' => __('Triangle', 'archi-graph'), 'path' => 'polygon'],
            'star' => ['label' => __('Étoile', 'archi-graph'), 'path' => 'polygon'],
            'hexagon' => ['label' => __('Hexagone', 'archi-graph'), 'path' => 'polygon']
        ],
        'animations' => [
            'hover' => ['none', 'zoom', 'pulse', 'glow', 'rotate', 'bounce'],
            'entrance' => ['none', 'fade', 'scale', 'slide', 'bounce']
        ],
        'borders' => ['none', 'solid', 'dashed', 'dotted', 'glow'],
        'link_styles' => ['straight', 'curve', 'wave', 'dotted', 'dashed'],
        'badges' => ['', 'new', 'featured', 'hot', 'updated', 'popular']
    ];
}

/**
 * Endpoint pour les statistiques du graphique
 */
function archi_register_graph_stats_endpoint() {
    register_rest_route('archi/v1', '/graph-stats', [
        'methods' => 'GET',
        'callback' => 'archi_get_graph_stats_rest',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        }
    ]);
}
add_action('rest_api_init', 'archi_register_graph_stats_endpoint');

/**
 * Récupérer les statistiques du graphique via REST
 */
function archi_get_graph_stats_rest() {
    global $wpdb;
    
    $stats = [
        'total_nodes' => 0,
        'active_nodes' => 0,
        'nodes_by_type' => [],
        'relationships' => 0
    ];
    
    $post_types = ['post', 'archi_project', 'archi_illustration'];
    
    foreach ($post_types as $type) {
        if (!post_type_exists($type)) continue;
        
        $total = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'",
            $type
        ));
        
        $active = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT p.ID) 
             FROM {$wpdb->posts} p 
             INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
             WHERE p.post_type = %s AND p.post_status = 'publish' 
             AND pm.meta_key = '_archi_show_in_graph' AND pm.meta_value = '1'",
            $type
        ));
        
        $stats['total_nodes'] += $total;
        $stats['active_nodes'] += $active;
        $stats['nodes_by_type'][$type] = [
            'total' => (int) $total,
            'active' => (int) $active,
            'percentage' => $total > 0 ? round(($active / $total) * 100, 1) : 0
        ];
    }
    
    return $stats;
}