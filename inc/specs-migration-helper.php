<?php
/**
 * Messages de migration pour les spécifications techniques
 * Informe les utilisateurs du passage des meta boxes aux blocs Gutenberg
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Afficher une notice admin pour expliquer la nouvelle approche
 */
function archi_specs_migration_notice() {
    $screen = get_current_screen();
    
    // Afficher seulement sur les pages d'édition des post types concernés
    if (!$screen || !in_array($screen->post_type, ['post', 'archi_project', 'archi_illustration'])) {
        return;
    }
    
    // Vérifier si l'utilisateur a déjà fermé la notice
    $dismissed = get_user_meta(get_current_user_id(), 'archi_specs_migration_dismissed', true);
    if ($dismissed) {
        return;
    }
    
    ?>
    <div class="notice notice-info is-dismissible archi-migration-notice" data-notice="archi_specs_migration">
        <h3>
            <span class="dashicons dashicons-info" style="color: #2271b1;"></span>
            <?php _e('🎨 Nouvelle façon de gérer les spécifications techniques !', 'archi-graph'); ?>
        </h3>
        <p>
            <strong><?php _e('Les spécifications techniques sont maintenant gérées directement dans l\'éditeur de contenu.', 'archi-graph'); ?></strong>
        </p>
        <p><?php _e('Pour une expérience d\'édition plus fluide et flexible, utilisez les nouveaux blocs Gutenberg :', 'archi-graph'); ?></p>
        <ul style="list-style: disc; margin-left: 2em;">
            <li><strong><?php _e('Spécifications Projet', 'archi-graph'); ?></strong> : <?php _e('Pour les projets architecturaux (surface, budget, client, etc.)', 'archi-graph'); ?></li>
            <li><strong><?php _e('Spécifications Illustration', 'archi-graph'); ?></strong> : <?php _e('Pour les illustrations (technique, dimensions, logiciels)', 'archi-graph'); ?></li>
            <li><strong><?php _e('Spécifications Article', 'archi-graph'); ?></strong> : <?php _e('Pour ajouter des spécifications personnalisées à tout type de contenu', 'archi-graph'); ?></li>
        </ul>
        <p>
            <strong><?php _e('Avantages :', 'archi-graph'); ?></strong>
        </p>
        <ul style="list-style: disc; margin-left: 2em;">
            <li>✅ <?php _e('Intégration naturelle dans le flux de contenu', 'archi-graph'); ?></li>
            <li>✅ <?php _e('Placement flexible des spécifications dans l\'article', 'archi-graph'); ?></li>
            <li>✅ <?php _e('Aperçu en temps réel dans l\'éditeur', 'archi-graph'); ?></li>
            <li>✅ <?php _e('Styles d\'affichage personnalisables (carte, liste, inline)', 'archi-graph'); ?></li>
            <li>✅ <?php _e('Compatible avec tous les blocs Gutenberg', 'archi-graph'); ?></li>
        </ul>
        <p>
            <em><?php _e('💡 Astuce : Recherchez "Spécifications" dans l\'ajout de blocs pour trouver ces nouveaux blocs rapidement !', 'archi-graph'); ?></em>
        </p>
        <p>
            <button type="button" class="button button-primary archi-migration-cta">
                <?php _e('👍 J\'ai compris, masquer ce message', 'archi-graph'); ?>
            </button>
            <a href="<?php echo admin_url('admin.php?page=archi-graph-settings'); ?>" class="button">
                <?php _e('📖 En savoir plus', 'archi-graph'); ?>
            </a>
        </p>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Dismiss notice
        $('.archi-migration-cta').on('click', function() {
            $.post(ajaxurl, {
                action: 'archi_dismiss_migration_notice',
                nonce: '<?php echo wp_create_nonce('archi_dismiss_migration'); ?>'
            }, function() {
                $('.archi-migration-notice').fadeOut();
            });
        });
        
        // Handle default dismiss button
        $('.archi-migration-notice .notice-dismiss').on('click', function() {
            $.post(ajaxurl, {
                action: 'archi_dismiss_migration_notice',
                nonce: '<?php echo wp_create_nonce('archi_dismiss_migration'); ?>'
            });
        });
    });
    </script>
    
    <style>
    .archi-migration-notice {
        border-left-color: #2271b1;
        padding: 1.5rem;
    }
    .archi-migration-notice h3 {
        margin-top: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.1em;
    }
    .archi-migration-notice ul {
        margin: 0.5rem 0;
    }
    .archi-migration-notice li {
        margin: 0.25rem 0;
    }
    .archi-migration-notice .button {
        margin-right: 0.5rem;
    }
    </style>
    <?php
}
add_action('admin_notices', 'archi_specs_migration_notice');

/**
 * AJAX pour masquer la notice de migration
 */
function archi_dismiss_migration_notice_ajax() {
    check_ajax_referer('archi_dismiss_migration', 'nonce');
    
    if (!current_user_can('edit_posts')) {
        wp_send_json_error();
    }
    
    update_user_meta(get_current_user_id(), 'archi_specs_migration_dismissed', true);
    wp_send_json_success();
}
add_action('wp_ajax_archi_dismiss_migration_notice', 'archi_dismiss_migration_notice_ajax');

/**
 * Ajouter un meta box d'information dans l'éditeur
 */
function archi_add_specs_info_meta_box() {
    $post_types = ['post', 'archi_project', 'archi_illustration'];
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'archi_specs_info',
            '📋 ' . __('Spécifications Techniques', 'archi-graph'),
            'archi_specs_info_meta_box_callback',
            $post_type,
            'side',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'archi_add_specs_info_meta_box');

/**
 * Callback pour le meta box d'information
 */
function archi_specs_info_meta_box_callback($post) {
    ?>
    <div class="archi-specs-info-box">
        <p style="margin-top: 0;">
            <strong><?php _e('💡 Nouveau !', 'archi-graph'); ?></strong>
        </p>
        <p>
            <?php _e('Ajoutez des spécifications techniques directement dans votre contenu avec les blocs Gutenberg.', 'archi-graph'); ?>
        </p>
        
        <div style="background: #f0f6fc; border-left: 3px solid #0073aa; padding: 0.75rem; margin: 1rem 0;">
            <strong><?php _e('Blocs disponibles :', 'archi-graph'); ?></strong>
            <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                <?php if ($post->post_type === 'archi_project'): ?>
                    <li><?php _e('🏗️ Spécifications Projet', 'archi-graph'); ?></li>
                <?php elseif ($post->post_type === 'archi_illustration'): ?>
                    <li><?php _e('🎨 Spécifications Illustration', 'archi-graph'); ?></li>
                <?php endif; ?>
                <li><?php _e('📝 Spécifications Article', 'archi-graph'); ?></li>
            </ul>
        </div>
        
        <p style="font-size: 0.9em; color: #666;">
            <?php _e('Recherchez "Spécifications" dans l\'ajout de blocs (+) pour les trouver.', 'archi-graph'); ?>
        </p>
        
        <hr style="margin: 1rem 0;">
        
        <p style="margin-bottom: 0;">
            <a href="#" class="button button-secondary" onclick="wp.data.dispatch('core/edit-post').openGeneralSidebar('edit-post/block'); return false;">
                <?php _e('➕ Ajouter un bloc', 'archi-graph'); ?>
            </a>
        </p>
    </div>
    
    <style>
    .archi-specs-info-box {
        font-size: 0.95em;
        line-height: 1.5;
    }
    .archi-specs-info-box ul {
        list-style: none;
        padding-left: 0;
    }
    .archi-specs-info-box li {
        margin: 0.25rem 0;
    }
    </style>
    <?php
}

/**
 * Ajouter une section dans les paramètres du thème
 */
function archi_specs_settings_section() {
    add_settings_section(
        'archi_specs_section',
        __('Spécifications Techniques', 'archi-graph'),
        'archi_specs_settings_callback',
        'archi-graph-settings'
    );
}
add_action('admin_init', 'archi_specs_settings_section');

/**
 * Callback pour la section des paramètres
 */
function archi_specs_settings_callback() {
    ?>
    <div class="archi-settings-specs">
        <h3><?php _e('📋 Gestion des spécifications techniques', 'archi-graph'); ?></h3>
        
        <div class="notice notice-info inline">
            <p>
                <strong><?php _e('Les spécifications techniques sont maintenant gérées via des blocs Gutenberg.', 'archi-graph'); ?></strong>
            </p>
            <p><?php _e('Cette approche offre plus de flexibilité et s\'intègre mieux dans le flux de création de contenu.', 'archi-graph'); ?></p>
        </div>
        
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php _e('Type de contenu', 'archi-graph'); ?></th>
                    <th><?php _e('Bloc recommandé', 'archi-graph'); ?></th>
                    <th><?php _e('Champs disponibles', 'archi-graph'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong><?php _e('Projets Architecturaux', 'archi-graph'); ?></strong></td>
                    <td><code>archi-graph/project-specs</code></td>
                    <td>
                        <?php _e('Surface, Budget, Client, Localisation, Dates, BET, Certifications', 'archi-graph'); ?>
                    </td>
                </tr>
                <tr>
                    <td><strong><?php _e('Illustrations', 'archi-graph'); ?></strong></td>
                    <td><code>archi-graph/illustration-specs</code></td>
                    <td>
                        <?php _e('Technique, Dimensions, Logiciels, Durée, Lien projet', 'archi-graph'); ?>
                    </td>
                </tr>
                <tr>
                    <td><strong><?php _e('Articles génériques', 'archi-graph'); ?></strong></td>
                    <td><code>archi-graph/article-specs</code></td>
                    <td>
                        <?php _e('Spécifications personnalisables (label/valeur)', 'archi-graph'); ?>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <h4 style="margin-top: 2rem;"><?php _e('Migration des données existantes', 'archi-graph'); ?></h4>
        <p>
            <?php _e('Si vous avez des articles avec des spécifications dans les anciennes meta boxes, elles restent accessibles via get_post_meta().', 'archi-graph'); ?>
            <?php _e('Vous pouvez les migrer manuellement vers les nouveaux blocs ou continuer à les utiliser.', 'archi-graph'); ?>
        </p>
        
        <p>
            <button type="button" class="button" id="archi-show-migration-guide">
                <?php _e('📖 Voir le guide de migration', 'archi-graph'); ?>
            </button>
        </p>
        
        <div id="archi-migration-guide" style="display: none; margin-top: 1rem; padding: 1rem; background: #f9f9f9; border-left: 4px solid #2271b1;">
            <h5><?php _e('Guide de migration étape par étape', 'archi-graph'); ?></h5>
            <ol>
                <li><?php _e('Ouvrez un article existant avec des spécifications', 'archi-graph'); ?></li>
                <li><?php _e('Dans l\'éditeur, ajoutez le bloc de spécifications approprié', 'archi-graph'); ?></li>
                <li><?php _e('Copiez les valeurs depuis les anciens champs vers le nouveau bloc', 'archi-graph'); ?></li>
                <li><?php _e('Sauvegardez l\'article', 'archi-graph'); ?></li>
                <li><?php _e('Les anciennes valeurs restent disponibles si besoin', 'archi-graph'); ?></li>
            </ol>
            <p><em><?php _e('Note : Cette migration n\'est pas obligatoire. Les deux systèmes peuvent coexister.', 'archi-graph'); ?></em></p>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#archi-show-migration-guide').on('click', function() {
                $('#archi-migration-guide').slideToggle();
                $(this).text($(this).text().includes('Voir') ? '📖 Masquer le guide de migration' : '📖 Voir le guide de migration');
            });
        });
        </script>
    </div>
    
    <style>
    .archi-settings-specs table {
        margin-top: 1rem;
    }
    .archi-settings-specs code {
        background: #f0f0f0;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.9em;
    }
    </style>
    <?php
}
