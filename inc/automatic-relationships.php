<?php
/**
 * Système de Relations Automatiques
 * Recalcule automatiquement les liens entre articles
 * 
 * @package Archi-Graph
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe pour gérer les relations automatiques
 */
class Archi_Automatic_Relationships {
    
    /**
     * Score minimum pour créer un lien automatique
     */
    const MIN_SCORE = 30;
    
    /**
     * Nombre maximum de liens automatiques par article
     */
    const MAX_AUTO_LINKS = 10;
    
    /**
     * Initialisation
     */
    public static function init() {
        // Hook sur la sauvegarde d'un post
        add_action('save_post', [__CLASS__, 'recalculate_on_save'], 20, 3);
        
        // Hook sur la suppression d'un post
        add_action('before_delete_post', [__CLASS__, 'cleanup_on_delete'], 10, 2);
        
        // Cron pour recalcul périodique
        add_action('archi_recalculate_all_relationships', [__CLASS__, 'recalculate_all_relationships']);
        
        // Admin notices
        add_action('admin_notices', [__CLASS__, 'show_recalculation_notice']);
        
        // AJAX pour recalcul manuel
        add_action('wp_ajax_archi_recalculate_relationships', [__CLASS__, 'ajax_recalculate']);
    }
    
    /**
     * Recalculer les relations quand un post est sauvegardé
     * 
     * @param int $post_id ID du post
     * @param WP_Post $post Objet post
     * @param bool $update Mise à jour ou nouveau
     */
    public static function recalculate_on_save($post_id, $post, $update) {
        // Vérifications
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }
        
        if (!in_array($post->post_type, ['post', 'archi_project', 'archi_illustration'])) {
            return;
        }
        
        if ($post->post_status !== 'publish') {
            return;
        }
        
        // Recalculer uniquement pour ce post
        self::recalculate_for_post($post_id);
        
        // Invalider le cache global
        delete_transient('archi_relationship_cache');
        delete_transient('archi_graph_articles');
        
        // Log
        error_log("Archi Relations: Recalculated relationships for post {$post_id} ({$post->post_title})");
    }
    
    /**
     * Recalculer les relations pour un post spécifique
     * 
     * @param int $post_id ID du post
     * @return array Résultats
     */
    public static function recalculate_for_post($post_id) {
        $results = [
            'post_id' => $post_id,
            'auto_links_found' => 0,
            'manual_links_kept' => 0,
            'scores' => []
        ];
        
        // Récupérer le post
        $post = get_post($post_id);
        if (!$post || $post->post_status !== 'publish') {
            return $results;
        }
        
        // Préparer les données du post
        $article = self::prepare_article_data($post);
        
        // Récupérer tous les autres posts éligibles
        $candidates = get_posts([
            'post_type' => ['post', 'archi_project', 'archi_illustration'],
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'post__not_in' => [$post_id],
            'meta_query' => [
                [
                    'key' => '_archi_show_in_graph',
                    'value' => '1',
                    'compare' => '='
                ]
            ]
        ]);
        
        $auto_links = [];
        
        // Calculer la proximité avec chaque candidat
        foreach ($candidates as $candidate) {
            $candidate_article = self::prepare_article_data($candidate);
            
            // Utiliser le calculateur de proximité
            if (class_exists('Archi_Proximity_Calculator')) {
                $proximity = Archi_Proximity_Calculator::calculate_proximity(
                    $article,
                    $candidate_article
                );
            } else {
                $proximity = archi_calculate_proximity_score($article, $candidate_article);
            }
            
            // Garder si le score dépasse le minimum
            if ($proximity['score'] >= self::MIN_SCORE) {
                $auto_links[] = [
                    'id' => $candidate->ID,
                    'score' => $proximity['score'],
                    'strength' => $proximity['strength']
                ];
            }
        }
        
        // Trier par score décroissant
        usort($auto_links, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        // Limiter au nombre maximum
        $auto_links = array_slice($auto_links, 0, self::MAX_AUTO_LINKS);
        
        // Récupérer les relations manuelles existantes
        $manual_links = get_post_meta($post_id, '_archi_related_articles', true);
        if (!is_array($manual_links)) {
            $manual_links = [];
        }
        
        // Fusionner auto + manuel (manuel prioritaire)
        $auto_link_ids = array_column($auto_links, 'id');
        $all_links = array_unique(array_merge($manual_links, $auto_link_ids));
        
        // Sauvegarder
        update_post_meta($post_id, '_archi_related_articles', $all_links);
        update_post_meta($post_id, '_archi_auto_links', $auto_link_ids);
        update_post_meta($post_id, '_archi_manual_links', $manual_links);
        update_post_meta($post_id, '_archi_links_last_calculated', current_time('timestamp'));
        
        $results['auto_links_found'] = count($auto_link_ids);
        $results['manual_links_kept'] = count($manual_links);
        $results['scores'] = $auto_links;
        
        return $results;
    }
    
    /**
     * Recalculer toutes les relations (cron ou manuel)
     * 
     * @param int $batch_size Nombre de posts à traiter par lot
     * @return array Statistiques
     */
    public static function recalculate_all_relationships($batch_size = 50) {
        $stats = [
            'total_processed' => 0,
            'total_links_created' => 0,
            'processing_time' => 0,
            'errors' => []
        ];
        
        $start_time = microtime(true);
        
        // Récupérer tous les posts éligibles
        $posts = get_posts([
            'post_type' => ['post', 'archi_project', 'archi_illustration'],
            'post_status' => 'publish',
            'posts_per_page' => $batch_size,
            'meta_query' => [
                [
                    'key' => '_archi_show_in_graph',
                    'value' => '1',
                    'compare' => '='
                ]
            ]
        ]);
        
        foreach ($posts as $post) {
            try {
                $results = self::recalculate_for_post($post->ID);
                $stats['total_processed']++;
                $stats['total_links_created'] += $results['auto_links_found'];
            } catch (Exception $e) {
                $stats['errors'][] = [
                    'post_id' => $post->ID,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        $stats['processing_time'] = round(microtime(true) - $start_time, 2);
        
        // Mettre à jour les métadonnées globales
        update_option('archi_last_full_recalculation', current_time('timestamp'));
        update_option('archi_last_recalculation_stats', $stats);
        
        // Invalider les caches
        delete_transient('archi_relationship_cache');
        delete_transient('archi_graph_articles');
        
        return $stats;
    }
    
    /**
     * Préparer les données d'un article pour le calcul
     * 
     * @param WP_Post $post Post
     * @return array Données formatées
     */
    private static function prepare_article_data($post) {
        $categories = get_the_category($post->ID);
        $tags = get_the_tags($post->ID);
        
        $article = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'excerpt' => wp_trim_words($post->post_content, 50, '...'),
            'date' => $post->post_date,
            'post_type' => $post->post_type,
            'categories' => [],
            'tags' => []
        ];
        
        if ($categories) {
            foreach ($categories as $cat) {
                $article['categories'][] = [
                    'id' => $cat->term_id,
                    'name' => $cat->name,
                    'slug' => $cat->slug
                ];
            }
        }
        
        if ($tags) {
            foreach ($tags as $tag) {
                $article['tags'][] = [
                    'id' => $tag->term_id,
                    'name' => $tag->name,
                    'slug' => $tag->slug
                ];
            }
        }
        
        // Métadonnées spécifiques aux illustrations
        if ($post->post_type === 'archi_illustration') {
            $article['illustration_meta'] = [
                'technique' => get_post_meta($post->ID, '_archi_illustration_technique', true),
                'software' => get_post_meta($post->ID, '_archi_illustration_software', true),
                'project_link' => get_post_meta($post->ID, '_archi_illustration_project_link', true)
            ];
        }
        
        return $article;
    }
    
    /**
     * Nettoyer les références quand un post est supprimé
     * 
     * @param int $post_id ID du post
     * @param WP_Post $post Post
     */
    public static function cleanup_on_delete($post_id, $post) {
        if (!in_array($post->post_type, ['post', 'archi_project', 'archi_illustration'])) {
            return;
        }
        
        // Supprimer ce post des relations des autres posts
        global $wpdb;
        
        $query = "SELECT post_id, meta_value 
                  FROM {$wpdb->postmeta} 
                  WHERE meta_key = '_archi_related_articles'";
        
        $results = $wpdb->get_results($query);
        
        foreach ($results as $row) {
            $related = maybe_unserialize($row->meta_value);
            if (is_array($related) && in_array($post_id, $related)) {
                $related = array_diff($related, [$post_id]);
                update_post_meta($row->post_id, '_archi_related_articles', $related);
            }
        }
    }
    
    /**
     * Afficher une notice admin pour recalcul manuel
     */
    public static function show_recalculation_notice() {
        $screen = get_current_screen();
        
        if ($screen && $screen->id === 'dashboard') {
            $last_recalc = get_option('archi_last_full_recalculation', 0);
            $time_since = current_time('timestamp') - $last_recalc;
            
            // Afficher si > 7 jours
            if ($time_since > (7 * DAY_IN_SECONDS) || !$last_recalc) {
                ?>
                <div class="notice notice-info is-dismissible">
                    <p>
                        <strong><?php _e('Archi-Graph:', 'archi-graph'); ?></strong>
                        <?php _e('Les relations entre articles n\'ont pas été recalculées depuis longtemps.', 'archi-graph'); ?>
                        <a href="#" id="archi-recalc-btn" class="button button-primary">
                            <?php _e('Recalculer maintenant', 'archi-graph'); ?>
                        </a>
                    </p>
                </div>
                <script>
                document.getElementById('archi-recalc-btn').addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('<?php _e('Recalculer toutes les relations ? Cela peut prendre quelques minutes.', 'archi-graph'); ?>')) {
                        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                            body: 'action=archi_recalculate_relationships&_wpnonce=<?php echo wp_create_nonce('archi_recalc'); ?>'
                        })
                        .then(r => r.json())
                        .then(data => {
                            alert('✓ Recalcul terminé ! ' + data.data.total_processed + ' articles traités.');
                            location.reload();
                        })
                        .catch(err => alert('Erreur : ' + err));
                    }
                });
                </script>
                <?php
            }
        }
    }
    
    /**
     * AJAX pour recalcul manuel
     */
    public static function ajax_recalculate() {
        check_ajax_referer('archi_recalc');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permissions insuffisantes']);
        }
        
        $stats = self::recalculate_all_relationships();
        
        wp_send_json_success($stats);
    }
    
    /**
     * Obtenir les statistiques de relations pour un post
     * 
     * @param int $post_id ID du post
     * @return array Statistiques
     */
    public static function get_relationship_stats($post_id) {
        $auto_links = get_post_meta($post_id, '_archi_auto_links', true) ?: [];
        $manual_links = get_post_meta($post_id, '_archi_manual_links', true) ?: [];
        $last_calc = get_post_meta($post_id, '_archi_links_last_calculated', true);
        
        return [
            'auto_count' => is_array($auto_links) ? count($auto_links) : 0,
            'manual_count' => is_array($manual_links) ? count($manual_links) : 0,
            'total_count' => is_array($auto_links) ? count(array_unique(array_merge($auto_links, $manual_links))) : count($manual_links),
            'last_calculated' => $last_calc ? date('Y-m-d H:i:s', $last_calc) : 'Jamais',
            'last_calculated_ago' => $last_calc ? human_time_diff($last_calc, current_time('timestamp')) : 'N/A'
        ];
    }
}

// Initialiser
Archi_Automatic_Relationships::init();

/**
 * Fonction helper pour recalcul manuel
 */
function archi_recalculate_post_relationships($post_id) {
    return Archi_Automatic_Relationships::recalculate_for_post($post_id);
}

/**
 * Fonction helper pour obtenir les stats
 */
function archi_get_relationship_stats($post_id) {
    return Archi_Automatic_Relationships::get_relationship_stats($post_id);
}

/**
 * Planifier le cron si pas déjà fait
 */
if (!wp_next_scheduled('archi_recalculate_all_relationships')) {
    wp_schedule_event(time(), 'daily', 'archi_recalculate_all_relationships');
}
