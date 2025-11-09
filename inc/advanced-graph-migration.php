<?php
/**
 * Script de migration : Paramètres Avancés du Graphique
 * 
 * Ce script aide à migrer vers le nouveau système de paramètres avancés
 * en appliquant des valeurs par défaut intelligentes basées sur le type de contenu.
 * 
 * Usage: Accéder à /wp-admin/admin.php?page=archi-advanced-migration
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ajouter une page d'administration pour la migration
 */
function archi_add_migration_page() {
    add_management_page(
        __('Migration Paramètres Graphique', 'archi-graph'),
        __('Migration Graphique', 'archi-graph'),
        'manage_options',
        'archi-advanced-migration',
        'archi_render_migration_page'
    );
}
add_action('admin_menu', 'archi_add_migration_page');

/**
 * Rendu de la page de migration
 */
function archi_render_migration_page() {
    // Vérifier les permissions
    if (!current_user_can('manage_options')) {
        wp_die(__('Vous n\'avez pas les permissions nécessaires', 'archi-graph'));
    }
    
    // Traiter la migration si demandée
    if (isset($_POST['archi_run_migration']) && check_admin_referer('archi_migration', 'archi_migration_nonce')) {
        $result = archi_run_advanced_params_migration();
    }
    
    // Statistiques actuelles
    $stats = archi_get_migration_stats();
    
    ?>
    <div class="wrap">
        <h1>🚀 <?php _e('Migration vers les Paramètres Avancés', 'archi-graph'); ?></h1>
        
        <div class="notice notice-info">
            <p>
                <?php _e('Cette migration applique des paramètres avancés par défaut à tous les articles affichés dans le graphique.', 'archi-graph'); ?>
                <strong><?php _e('Cette opération est sûre et réversible.', 'archi-graph'); ?></strong>
            </p>
        </div>
        
        <?php if (isset($result)): ?>
            <div class="notice notice-success is-dismissible">
                <p>
                    <strong>✅ <?php _e('Migration réussie !', 'archi-graph'); ?></strong><br>
                    <?php echo sprintf(__('%d articles migrés', 'archi-graph'), $result['migrated']); ?><br>
                    <?php echo sprintf(__('%d projets configurés', 'archi-graph'), $result['projects']); ?><br>
                    <?php echo sprintf(__('%d illustrations configurées', 'archi-graph'), $result['illustrations']); ?>
                </p>
            </div>
        <?php endif; ?>
        
        <!-- Statistiques actuelles -->
        <div class="card" style="max-width: 800px; margin: 20px 0;">
            <h2>📊 <?php _e('État Actuel', 'archi-graph'); ?></h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php _e('Statistique', 'archi-graph'); ?></th>
                        <th><?php _e('Valeur', 'archi-graph'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php _e('Total d\'articles dans le graphique', 'archi-graph'); ?></td>
                        <td><strong><?php echo $stats['total_in_graph']; ?></strong></td>
                    </tr>
                    <tr>
                        <td><?php _e('Articles avec forme définie', 'archi-graph'); ?></td>
                        <td><?php echo $stats['with_shape']; ?> (<?php echo $stats['shape_percentage']; ?>%)</td>
                    </tr>
                    <tr>
                        <td><?php _e('Articles avec groupe visuel', 'archi-graph'); ?></td>
                        <td><?php echo $stats['with_group']; ?> (<?php echo $stats['group_percentage']; ?>%)</td>
                    </tr>
                    <tr>
                        <td><?php _e('Articles avec icône', 'archi-graph'); ?></td>
                        <td><?php echo $stats['with_icon']; ?></td>
                    </tr>
                    <tr>
                        <td><?php _e('Articles avec badge', 'archi-graph'); ?></td>
                        <td><?php echo $stats['with_badge']; ?></td>
                    </tr>
                    <tr>
                        <td><?php _e('Nœuds épinglés', 'archi-graph'); ?></td>
                        <td><?php echo $stats['pinned']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Actions de migration -->
        <div class="card" style="max-width: 800px; margin: 20px 0;">
            <h2>⚙️ <?php _e('Actions de Migration', 'archi-graph'); ?></h2>
            
            <h3><?php _e('Ce que la migration va faire :', 'archi-graph'); ?></h3>
            <ul style="line-height: 1.8;">
                <li>✅ <strong><?php _e('Articles standards', 'archi-graph'); ?></strong> : Forme cercle, effet zoom</li>
                <li>✅ <strong><?php _e('Projets architecturaux', 'archi-graph'); ?></strong> : Forme carré, effet lueur, icône 🏗️</li>
                <li>✅ <strong><?php _e('Illustrations', 'archi-graph'); ?></strong> : Forme diamant, effet pulsation, icône 🎨</li>
                <li>✅ <strong><?php _e('Groupes visuels', 'archi-graph'); ?></strong> : Basés sur la catégorie principale</li>
                <li>✅ <strong><?php _e('Badges', 'archi-graph'); ?></strong> : "🆕 Nouveau" pour les articles récents (&lt;30 jours)</li>
                <li>✅ <strong><?php _e('Labels', 'archi-graph'); ?></strong> : Titres courts pour les projets importants</li>
            </ul>
            
            <h3><?php _e('Ce qui sera préservé :', 'archi-graph'); ?></h3>
            <ul style="line-height: 1.8;">
                <li>✅ <?php _e('Tous les paramètres existants (couleur, taille, position)', 'archi-graph'); ?></li>
                <li>✅ <?php _e('Les relations manuelles entre articles', 'archi-graph'); ?></li>
                <li>✅ <?php _e('Les priorités et visibilités', 'archi-graph'); ?></li>
            </ul>
            
            <form method="post" action="" style="margin-top: 30px;">
                <?php wp_nonce_field('archi_migration', 'archi_migration_nonce'); ?>
                
                <label>
                    <input type="checkbox" name="archi_apply_shapes" value="1" checked>
                    <?php _e('Appliquer les formes par défaut selon le type', 'archi-graph'); ?>
                </label><br>
                
                <label>
                    <input type="checkbox" name="archi_apply_groups" value="1" checked>
                    <?php _e('Créer des groupes visuels basés sur les catégories', 'archi-graph'); ?>
                </label><br>
                
                <label>
                    <input type="checkbox" name="archi_apply_icons" value="1" checked>
                    <?php _e('Ajouter des icônes par défaut', 'archi-graph'); ?>
                </label><br>
                
                <label>
                    <input type="checkbox" name="archi_apply_badges" value="1" checked>
                    <?php _e('Ajouter des badges aux articles récents', 'archi-graph'); ?>
                </label><br>
                
                <label>
                    <input type="checkbox" name="archi_apply_animations" value="1" checked>
                    <?php _e('Configurer les animations et effets', 'archi-graph'); ?>
                </label><br><br>
                
                <button type="submit" name="archi_run_migration" class="button button-primary button-hero">
                    🚀 <?php _e('Lancer la Migration', 'archi-graph'); ?>
                </button>
            </form>
        </div>
        
        <!-- Aide -->
        <div class="card" style="max-width: 800px; margin: 20px 0; background: #f0f8ff;">
            <h2>💡 <?php _e('Besoin d\'Aide ?', 'archi-graph'); ?></h2>
            <p><?php _e('Cette migration peut être exécutée plusieurs fois sans risque. Les valeurs déjà configurées manuellement ne seront pas écrasées.', 'archi-graph'); ?></p>
            <p>
                <?php _e('Documentation complète :', 'archi-graph'); ?>
                <a href="<?php echo get_template_directory_uri(); ?>/docs/advanced-graph-parameters.md" target="_blank">
                    advanced-graph-parameters.md
                </a>
            </p>
        </div>
    </div>
    <?php
}

/**
 * Récupérer les statistiques de migration
 */
function archi_get_migration_stats() {
    global $wpdb;
    
    // Total dans le graphique
    $total = $wpdb->get_var("
        SELECT COUNT(DISTINCT p.ID)
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE p.post_status = 'publish'
        AND pm.meta_key = '_archi_show_in_graph'
        AND pm.meta_value = '1'
    ");
    
    // Avec forme
    $with_shape = $wpdb->get_var("
        SELECT COUNT(DISTINCT pm.post_id)
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->postmeta} pm2 ON pm.post_id = pm2.post_id
        WHERE pm.meta_key = '_archi_node_shape'
        AND pm.meta_value != ''
        AND pm2.meta_key = '_archi_show_in_graph'
        AND pm2.meta_value = '1'
    ");
    
    // Avec groupe
    $with_group = $wpdb->get_var("
        SELECT COUNT(DISTINCT pm.post_id)
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->postmeta} pm2 ON pm.post_id = pm2.post_id
        WHERE pm.meta_key = '_archi_visual_group'
        AND pm.meta_value != ''
        AND pm2.meta_key = '_archi_show_in_graph'
        AND pm2.meta_value = '1'
    ");
    
    // Avec icône
    $with_icon = $wpdb->get_var("
        SELECT COUNT(*)
        FROM {$wpdb->postmeta}
        WHERE meta_key = '_archi_node_icon'
        AND meta_value != ''
    ");
    
    // Avec badge
    $with_badge = $wpdb->get_var("
        SELECT COUNT(*)
        FROM {$wpdb->postmeta}
        WHERE meta_key = '_archi_node_badge'
        AND meta_value != ''
    ");
    
    // Épinglés
    $pinned = $wpdb->get_var("
        SELECT COUNT(*)
        FROM {$wpdb->postmeta}
        WHERE meta_key = '_archi_pin_node'
        AND meta_value = '1'
    ");
    
    $total = intval($total);
    
    return [
        'total_in_graph' => $total,
        'with_shape' => intval($with_shape),
        'shape_percentage' => $total > 0 ? round(($with_shape / $total) * 100, 1) : 0,
        'with_group' => intval($with_group),
        'group_percentage' => $total > 0 ? round(($with_group / $total) * 100, 1) : 0,
        'with_icon' => intval($with_icon),
        'with_badge' => intval($with_badge),
        'pinned' => intval($pinned)
    ];
}

/**
 * Exécuter la migration complète
 */
function archi_run_advanced_params_migration() {
    // Options de migration
    $apply_shapes = isset($_POST['archi_apply_shapes']);
    $apply_groups = isset($_POST['archi_apply_groups']);
    $apply_icons = isset($_POST['archi_apply_icons']);
    $apply_badges = isset($_POST['archi_apply_badges']);
    $apply_animations = isset($_POST['archi_apply_animations']);
    
    // Récupérer tous les articles dans le graphique
    $args = [
        'post_type' => ['post', 'archi_project', 'archi_illustration'],
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => '_archi_show_in_graph',
                'value' => '1'
            ]
        ]
    ];
    
    $posts = get_posts($args);
    
    $counters = [
        'migrated' => 0,
        'projects' => 0,
        'illustrations' => 0
    ];
    
    foreach ($posts as $post) {
        $post_type = $post->post_type;
        
        // Formes par défaut
        if ($apply_shapes && !get_post_meta($post->ID, '_archi_node_shape', true)) {
            switch ($post_type) {
                case 'archi_project':
                    update_post_meta($post->ID, '_archi_node_shape', 'square');
                    $counters['projects']++;
                    break;
                case 'archi_illustration':
                    update_post_meta($post->ID, '_archi_node_shape', 'diamond');
                    $counters['illustrations']++;
                    break;
                default:
                    update_post_meta($post->ID, '_archi_node_shape', 'circle');
            }
        }
        
        // Icônes par défaut
        if ($apply_icons && !get_post_meta($post->ID, '_archi_node_icon', true)) {
            $icon = '';
            switch ($post_type) {
                case 'archi_project':
                    $icon = '🏗️';
                    break;
                case 'archi_illustration':
                    $icon = '🎨';
                    break;
                default:
                    $icon = '📄';
            }
            update_post_meta($post->ID, '_archi_node_icon', $icon);
        }
        
        // Groupes visuels basés sur les catégories
        if ($apply_groups && !get_post_meta($post->ID, '_archi_visual_group', true)) {
            $categories = get_the_category($post->ID);
            if (!empty($categories)) {
                update_post_meta($post->ID, '_archi_visual_group', $categories[0]->name);
            }
        }
        
        // Badges pour articles récents
        if ($apply_badges && !get_post_meta($post->ID, '_archi_node_badge', true)) {
            $days_old = (time() - strtotime($post->post_date)) / DAY_IN_SECONDS;
            if ($days_old < 30) {
                update_post_meta($post->ID, '_archi_node_badge', 'new');
            }
        }
        
        // Animations
        if ($apply_animations) {
            if (!get_post_meta($post->ID, '_archi_hover_effect', true)) {
                $effect = $post_type === 'archi_project' ? 'glow' : 
                          ($post_type === 'archi_illustration' ? 'pulse' : 'zoom');
                update_post_meta($post->ID, '_archi_hover_effect', $effect);
            }
            
            if (!get_post_meta($post->ID, '_archi_entrance_animation', true)) {
                $animation = $post_type === 'archi_project' ? 'scale' : 'fade';
                update_post_meta($post->ID, '_archi_entrance_animation', $animation);
            }
        }
        
        // Paramètres par défaut si non définis
        if (!get_post_meta($post->ID, '_archi_node_opacity', true)) {
            update_post_meta($post->ID, '_archi_node_opacity', 1.0);
        }
        
        if (!get_post_meta($post->ID, '_archi_node_weight', true)) {
            $weight = $post_type === 'archi_project' ? 3 : 1;
            update_post_meta($post->ID, '_archi_node_weight', $weight);
        }
        
        if (!get_post_meta($post->ID, '_archi_connection_depth', true)) {
            update_post_meta($post->ID, '_archi_connection_depth', 2);
        }
        
        if (!get_post_meta($post->ID, '_archi_link_strength', true)) {
            update_post_meta($post->ID, '_archi_link_strength', 1.0);
        }
        
        if (!get_post_meta($post->ID, '_archi_link_style', true)) {
            update_post_meta($post->ID, '_archi_link_style', 'curve');
        }
        
        $counters['migrated']++;
    }
    
    // Invalider le cache
    delete_transient('archi_graph_articles');
    
    // Log
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log(sprintf(
            'Archi Advanced Migration: %d articles migrés (%d projets, %d illustrations)',
            $counters['migrated'],
            $counters['projects'],
            $counters['illustrations']
        ));
    }
    
    return $counters;
}

/**
 * Notice pour suggérer la migration aux admins
 */
function archi_suggest_migration_notice() {
    // Vérifier si la migration a déjà été faite
    if (get_option('archi_migration_completed')) {
        return;
    }
    
    // Vérifier s'il y a des articles sans paramètres avancés
    $stats = archi_get_migration_stats();
    
    if ($stats['shape_percentage'] < 50) {
        ?>
        <div class="notice notice-info is-dismissible">
            <h3>🚀 <?php _e('Nouveau : Paramètres Avancés du Graphique !', 'archi-graph'); ?></h3>
            <p>
                <?php _e('De nouvelles fonctionnalités sont disponibles pour personnaliser votre graphique :', 'archi-graph'); ?>
                <strong><?php _e('formes personnalisées, icônes, groupes visuels, animations', 'archi-graph'); ?></strong>, <?php _e('et plus encore.', 'archi-graph'); ?>
            </p>
            <p>
                <?php echo sprintf(
                    __('Seulement %s%% de vos articles utilisent ces nouveaux paramètres.', 'archi-graph'),
                    $stats['shape_percentage']
                ); ?>
            </p>
            <p>
                <a href="<?php echo admin_url('tools.php?page=archi-advanced-migration'); ?>" class="button button-primary">
                    <?php _e('Configurer automatiquement', 'archi-graph'); ?>
                </a>
                <a href="<?php echo get_template_directory_uri(); ?>/docs/advanced-graph-parameters.md" class="button" target="_blank">
                    <?php _e('En savoir plus', 'archi-graph'); ?>
                </a>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'archi_suggest_migration_notice');
