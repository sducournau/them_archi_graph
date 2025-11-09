<?php
/**
 * Widget Admin pour l'analyse des relations
 * 
 * @package Archi-Graph
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ajouter le widget au tableau de bord
 */
function archi_add_relationships_dashboard_widget() {
    wp_add_dashboard_widget(
        'archi_relationships_widget',
        '🔗 Analyse des Relations - Archi Graph',
        'archi_render_relationships_widget'
    );
}
add_action('wp_dashboard_setup', 'archi_add_relationships_dashboard_widget');

/**
 * Afficher le widget
 */
function archi_render_relationships_widget() {
    // Statistiques globales
    $last_recalc = get_option('archi_last_full_recalculation', 0);
    $last_stats = get_option('archi_last_recalculation_stats', []);
    
    // Compte des articles dans le graphique
    $articles_count = wp_count_posts('post')->publish + 
                     wp_count_posts('archi_project')->publish + 
                     wp_count_posts('archi_illustration')->publish;
    
    // Articles avec le flag graphique activé
    global $wpdb;
    $graph_articles = $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->postmeta} 
         WHERE meta_key = '_archi_show_in_graph' AND meta_value = '1'"
    );
    
    ?>
    <div class="archi-relationships-widget">
        <style>
            .archi-relationships-widget {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            }
            .archi-stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 15px;
                margin-bottom: 20px;
            }
            .archi-stat-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 15px;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
            }
            .archi-stat-value {
                font-size: 32px;
                font-weight: bold;
                margin-bottom: 5px;
            }
            .archi-stat-label {
                font-size: 13px;
                opacity: 0.9;
            }
            .archi-recalc-section {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 15px;
            }
            .archi-recalc-btn {
                background: #667eea !important;
                border-color: #667eea !important;
                color: white !important;
                box-shadow: 0 2px 5px rgba(102, 126, 234, 0.3);
            }
            .archi-recalc-btn:hover {
                background: #5568d3 !important;
                border-color: #5568d3 !important;
            }
            .archi-recent-activity {
                margin-top: 15px;
            }
            .archi-activity-item {
                padding: 10px;
                background: white;
                border-left: 3px solid #667eea;
                margin-bottom: 8px;
                font-size: 13px;
            }
            .archi-loading {
                text-align: center;
                padding: 20px;
            }
            .archi-spinner {
                display: inline-block;
                width: 30px;
                height: 30px;
                border: 3px solid #f3f3f3;
                border-top: 3px solid #667eea;
                border-radius: 50%;
                animation: archi-spin 1s linear infinite;
            }
            @keyframes archi-spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
        
        <!-- Statistiques -->
        <div class="archi-stats-grid">
            <div class="archi-stat-card">
                <div class="archi-stat-value"><?php echo number_format($articles_count); ?></div>
                <div class="archi-stat-label">Articles publiés</div>
            </div>
            <div class="archi-stat-card">
                <div class="archi-stat-value"><?php echo number_format($graph_articles); ?></div>
                <div class="archi-stat-label">Dans le graphique</div>
            </div>
            <?php if (!empty($last_stats['total_links_created'])) : ?>
            <div class="archi-stat-card">
                <div class="archi-stat-value"><?php echo number_format($last_stats['total_links_created']); ?></div>
                <div class="archi-stat-label">Liens automatiques</div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Recalcul -->
        <div class="archi-recalc-section">
            <p style="margin-top: 0;">
                <strong>Dernière mise à jour :</strong>
                <?php 
                if ($last_recalc) {
                    echo human_time_diff($last_recalc, current_time('timestamp')) . ' (';
                    echo date_i18n('d/m/Y H:i', $last_recalc) . ')';
                } else {
                    echo '<span style="color: #dc3545;">Jamais effectuée</span>';
                }
                ?>
            </p>
            
            <button class="button button-primary archi-recalc-btn" id="archi-recalc-all">
                🔄 Recalculer toutes les relations
            </button>
            
            <div id="archi-recalc-progress" style="display: none; margin-top: 10px;">
                <div class="archi-loading">
                    <div class="archi-spinner"></div>
                    <p style="margin-top: 10px;">Recalcul en cours...</p>
                </div>
            </div>
            
            <div id="archi-recalc-result" style="display: none; margin-top: 10px;"></div>
        </div>
        
        <!-- Articles populaires (plus de liens) -->
        <div class="archi-recent-activity">
            <h4 style="margin-top: 0;">📊 Articles les plus connectés</h4>
            <?php
            $popular_articles = $wpdb->get_results(
                "SELECT p.ID, p.post_title, pm.meta_value as links
                 FROM {$wpdb->posts} p
                 INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                 WHERE pm.meta_key = '_archi_auto_links'
                   AND p.post_status = 'publish'
                 ORDER BY CHAR_LENGTH(pm.meta_value) DESC
                 LIMIT 5"
            );
            
            if ($popular_articles) {
                foreach ($popular_articles as $article) {
                    $links = maybe_unserialize($article->links);
                    $link_count = is_array($links) ? count($links) : 0;
                    $edit_link = get_edit_post_link($article->ID);
                    
                    echo '<div class="archi-activity-item">';
                    echo '<a href="' . esc_url($edit_link) . '" style="text-decoration: none; color: #333;">';
                    echo '<strong>' . esc_html($article->post_title) . '</strong>';
                    echo '</a>';
                    echo ' <span style="color: #667eea;">(' . $link_count . ' liens)</span>';
                    echo '</div>';
                }
            } else {
                echo '<p style="color: #999;">Aucun article avec des liens automatiques.</p>';
            }
            ?>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#archi-recalc-all').on('click', function() {
                var $btn = $(this);
                var $progress = $('#archi-recalc-progress');
                var $result = $('#archi-recalc-result');
                
                $btn.prop('disabled', true);
                $progress.show();
                $result.hide();
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'archi_recalculate_relationships',
                        _wpnonce: '<?php echo wp_create_nonce('archi_recalc'); ?>'
                    },
                    success: function(response) {
                        $progress.hide();
                        
                        if (response.success) {
                            var data = response.data;
                            $result.html(
                                '<div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;">' +
                                '<strong>✓ Recalcul terminé avec succès !</strong><br>' +
                                '• Articles traités : ' + data.total_processed + '<br>' +
                                '• Liens créés : ' + data.total_links_created + '<br>' +
                                '• Temps de traitement : ' + data.processing_time + 's<br>' +
                                '<em style="font-size: 12px;">Rafraîchir la page pour voir les nouveaux chiffres.</em>' +
                                '</div>'
                            ).show();
                        } else {
                            $result.html(
                                '<div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;">' +
                                '<strong>✗ Erreur</strong><br>' + response.data.message +
                                '</div>'
                            ).show();
                        }
                        
                        $btn.prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        $progress.hide();
                        $result.html(
                            '<div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;">' +
                            '<strong>✗ Erreur de communication</strong><br>' + error +
                            '</div>'
                        ).show();
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
        </script>
    </div>
    <?php
}

/**
 * Ajouter une colonne "Relations" dans la liste des articles
 */
function archi_add_relationships_column($columns) {
    $columns['archi_relationships'] = '🔗 Relations';
    return $columns;
}
add_filter('manage_post_posts_columns', 'archi_add_relationships_column');
add_filter('manage_archi_project_posts_columns', 'archi_add_relationships_column');
add_filter('manage_archi_illustration_posts_columns', 'archi_add_relationships_column');

/**
 * Remplir la colonne Relations
 */
function archi_fill_relationships_column($column, $post_id) {
    if ($column === 'archi_relationships') {
        $stats = archi_get_relationship_stats($post_id);
        
        echo '<div style="font-size: 12px;">';
        echo '<strong style="color: #667eea;">' . $stats['total_count'] . ' total</strong><br>';
        echo '<span style="color: #28a745;">' . $stats['auto_count'] . ' auto</span> | ';
        echo '<span style="color: #dc3545;">' . $stats['manual_count'] . ' manuel</span>';
        echo '</div>';
    }
}
add_action('manage_post_posts_custom_column', 'archi_fill_relationships_column', 10, 2);
add_action('manage_archi_project_posts_custom_column', 'archi_fill_relationships_column', 10, 2);
add_action('manage_archi_illustration_posts_custom_column', 'archi_fill_relationships_column', 10, 2);

/**
 * Ajouter une meta box pour les statistiques de relations
 */
function archi_add_relationships_stats_metabox() {
    $post_types = ['post', 'archi_project', 'archi_illustration'];
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'archi_relationship_stats',
            '📊 Statistiques de Relations',
            'archi_render_relationships_stats_metabox',
            $post_type,
            'side',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'archi_add_relationships_stats_metabox');

/**
 * Afficher la meta box des statistiques
 */
function archi_render_relationships_stats_metabox($post) {
    $stats = archi_get_relationship_stats($post->ID);
    $auto_links = get_post_meta($post->ID, '_archi_auto_links', true) ?: [];
    
    ?>
    <style>
        .archi-stats-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .archi-stats-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .archi-stats-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .archi-stats-label {
            font-weight: 600;
        }
        .archi-stats-value {
            font-size: 18px;
        }
        .archi-recalc-btn-small {
            width: 100%;
            background: white !important;
            color: #667eea !important;
            border: none !important;
            padding: 10px !important;
            border-radius: 5px !important;
            font-weight: 600 !important;
            cursor: pointer;
        }
        .archi-recalc-btn-small:hover {
            background: #f8f9fa !important;
        }
    </style>
    
    <div class="archi-stats-box">
        <div class="archi-stats-row">
            <span class="archi-stats-label">Total</span>
            <span class="archi-stats-value"><?php echo $stats['total_count']; ?></span>
        </div>
        <div class="archi-stats-row">
            <span class="archi-stats-label">Automatiques</span>
            <span class="archi-stats-value" style="color: #90EE90;"><?php echo $stats['auto_count']; ?></span>
        </div>
        <div class="archi-stats-row">
            <span class="archi-stats-label">Manuels</span>
            <span class="archi-stats-value" style="color: #FFB6C1;"><?php echo $stats['manual_count']; ?></span>
        </div>
    </div>
    
    <p style="font-size: 12px; color: #666; margin-bottom: 10px;">
        <strong>Dernier calcul :</strong><br>
        <?php echo $stats['last_calculated_ago']; ?>
    </p>
    
    <button type="button" class="button archi-recalc-btn-small" id="archi-recalc-single" data-post-id="<?php echo $post->ID; ?>">
        🔄 Recalculer pour cet article
    </button>
    
    <div id="archi-single-result" style="display: none; margin-top: 10px;"></div>
    
    <script>
    jQuery(document).ready(function($) {
        $('#archi-recalc-single').on('click', function() {
            var $btn = $(this);
            var postId = $btn.data('post-id');
            var $result = $('#archi-single-result');
            
            $btn.prop('disabled', true).text('Calcul en cours...');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'archi_recalculate_single_post',
                    post_id: postId,
                    _wpnonce: '<?php echo wp_create_nonce('archi_recalc_single'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $result.html(
                            '<div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; font-size: 12px;">' +
                            '<strong>✓ Succès !</strong><br>' +
                            'Liens auto : ' + response.data.auto_links_found +
                            '</div>'
                        ).show();
                        
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        $result.html(
                            '<div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; font-size: 12px;">' +
                            '✗ Erreur' +
                            '</div>'
                        ).show();
                    }
                    
                    $btn.prop('disabled', false).text('🔄 Recalculer pour cet article');
                },
                error: function() {
                    $result.html('<div style="background: #f8d7da; color: #721c24; padding: 10px;">Erreur</div>').show();
                    $btn.prop('disabled', false).text('🔄 Recalculer pour cet article');
                }
            });
        });
    });
    </script>
    
    <?php if (!empty($auto_links) && is_array($auto_links)) : ?>
    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
        <h4 style="margin: 0 0 10px 0; font-size: 13px;">Liens automatiques détectés :</h4>
        <ul style="margin: 0; padding-left: 20px; font-size: 12px;">
            <?php 
            $limit = 5;
            $count = 0;
            foreach ($auto_links as $linked_id) {
                if ($count >= $limit) break;
                $linked_post = get_post($linked_id);
                if ($linked_post) {
                    echo '<li><a href="' . get_edit_post_link($linked_id) . '">' . esc_html($linked_post->post_title) . '</a></li>';
                    $count++;
                }
            }
            if (count($auto_links) > $limit) {
                echo '<li><em>... et ' . (count($auto_links) - $limit) . ' autres</em></li>';
            }
            ?>
        </ul>
    </div>
    <?php endif; ?>
    <?php
}

/**
 * AJAX pour recalculer un seul post
 */
function archi_ajax_recalculate_single_post() {
    check_ajax_referer('archi_recalc_single');
    
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(['message' => 'Permissions insuffisantes']);
    }
    
    $post_id = absint($_POST['post_id']);
    
    if (!$post_id) {
        wp_send_json_error(['message' => 'ID de post invalide']);
    }
    
    $results = archi_recalculate_post_relationships($post_id);
    
    wp_send_json_success($results);
}
add_action('wp_ajax_archi_recalculate_single_post', 'archi_ajax_recalculate_single_post');
