<?php
/**
 * Admin Enhancements for Article Management
 * Bulk operations, quick edit, dashboard widgets
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add bulk action for graph visibility
 */
function archi_add_bulk_actions($bulk_actions) {
    $bulk_actions['archi_add_to_graph'] = __('Ajouter au graphique', 'archi-graph');
    $bulk_actions['archi_remove_from_graph'] = __('Retirer du graphique', 'archi-graph');
    $bulk_actions['archi_set_priority_high'] = __('Priorité élevée', 'archi-graph');
    $bulk_actions['archi_set_priority_normal'] = __('Priorité normale', 'archi-graph');
    return $bulk_actions;
}
add_filter('bulk_actions-edit-post', 'archi_add_bulk_actions');
add_filter('bulk_actions-edit-archi_project', 'archi_add_bulk_actions');
add_filter('bulk_actions-edit-archi_illustration', 'archi_add_bulk_actions');

/**
 * Handle bulk actions
 */
function archi_handle_bulk_actions($redirect_to, $doaction, $post_ids) {
    $actions = [
        'archi_add_to_graph' => ['_archi_show_in_graph', '1'],
        'archi_remove_from_graph' => ['_archi_show_in_graph', '0'],
        'archi_set_priority_high' => ['_archi_priority_level', 'high'],
        'archi_set_priority_normal' => ['_archi_priority_level', 'normal'],
    ];
    
    if (isset($actions[$doaction])) {
        list($meta_key, $meta_value) = $actions[$doaction];
        
        foreach ($post_ids as $post_id) {
            update_post_meta($post_id, $meta_key, $meta_value);
        }
        
        // Invalidate cache
        delete_transient('archi_graph_articles');
        
        $redirect_to = add_query_arg('archi_bulk_action', $doaction, $redirect_to);
        $redirect_to = add_query_arg('archi_changed', count($post_ids), $redirect_to);
    }
    
    return $redirect_to;
}
add_filter('handle_bulk_actions-edit-post', 'archi_handle_bulk_actions', 10, 3);
add_filter('handle_bulk_actions-edit-archi_project', 'archi_handle_bulk_actions', 10, 3);
add_filter('handle_bulk_actions-edit-archi_illustration', 'archi_handle_bulk_actions', 10, 3);

/**
 * Show admin notice after bulk action
 */
function archi_bulk_action_admin_notice() {
    if (!empty($_REQUEST['archi_bulk_action']) && !empty($_REQUEST['archi_changed'])) {
        $action = sanitize_text_field($_REQUEST['archi_bulk_action']);
        $count = intval($_REQUEST['archi_changed']);
        
        $messages = [
            'archi_add_to_graph' => __('%d article(s) ajouté(s) au graphique.', 'archi-graph'),
            'archi_remove_from_graph' => __('%d article(s) retiré(s) du graphique.', 'archi-graph'),
            'archi_set_priority_high' => __('%d article(s) défini(s) en priorité élevée.', 'archi-graph'),
            'archi_set_priority_normal' => __('%d article(s) défini(s) en priorité normale.', 'archi-graph'),
        ];
        
        if (isset($messages[$action])) {
            printf(
                '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
                esc_html(sprintf($messages[$action], $count))
            );
        }
    }
}
add_action('admin_notices', 'archi_bulk_action_admin_notice');

/**
 * Add quick edit support for graph metadata
 */
function archi_add_quick_edit_fields($column_name, $post_type) {
    if ($column_name !== 'archi_graph') {
        return;
    }
    
    ?>
    <fieldset class="inline-edit-col-right inline-edit-archi-graph">
        <div class="inline-edit-col">
            <label>
                <span class="title"><?php _e('Graphique', 'archi-graph'); ?></span>
                <span class="input-text-wrap">
                    <select name="archi_show_in_graph" class="archi-quick-edit-select">
                        <option value=""><?php _e('— Pas de modification —', 'archi-graph'); ?></option>
                        <option value="1"><?php _e('Afficher dans le graphique', 'archi-graph'); ?></option>
                        <option value="0"><?php _e('Masquer du graphique', 'archi-graph'); ?></option>
                    </select>
                </span>
            </label>
            
            <label>
                <span class="title"><?php _e('Priorité', 'archi-graph'); ?></span>
                <span class="input-text-wrap">
                    <select name="archi_priority_level" class="archi-quick-edit-select">
                        <option value=""><?php _e('— Pas de modification —', 'archi-graph'); ?></option>
                        <option value="low"><?php _e('Faible', 'archi-graph'); ?></option>
                        <option value="normal"><?php _e('Normal', 'archi-graph'); ?></option>
                        <option value="high"><?php _e('Élevé', 'archi-graph'); ?></option>
                        <option value="featured"><?php _e('Vedette', 'archi-graph'); ?></option>
                    </select>
                </span>
            </label>
            
            <label>
                <span class="title"><?php _e('Couleur du nœud', 'archi-graph'); ?></span>
                <span class="input-text-wrap">
                    <input type="color" 
                           name="archi_node_color" 
                           class="archi-quick-edit-color"
                           value="">
                </span>
            </label>
        </div>
    </fieldset>
    <?php
}
add_action('quick_edit_custom_box', 'archi_add_quick_edit_fields', 10, 2);
add_action('bulk_edit_custom_box', 'archi_add_quick_edit_fields', 10, 2);

/**
 * Save quick edit data
 */
function archi_save_quick_edit_data($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_REQUEST['archi_show_in_graph']) && $_REQUEST['archi_show_in_graph'] !== '') {
        update_post_meta($post_id, '_archi_show_in_graph', sanitize_text_field($_REQUEST['archi_show_in_graph']));
        delete_transient('archi_graph_articles');
    }
    
    if (isset($_REQUEST['archi_priority_level']) && $_REQUEST['archi_priority_level'] !== '') {
        $priority = sanitize_text_field($_REQUEST['archi_priority_level']);
        $allowed = ['low', 'normal', 'high', 'featured'];
        if (in_array($priority, $allowed)) {
            update_post_meta($post_id, '_archi_priority_level', $priority);
            delete_transient('archi_graph_articles');
        }
    }
    
    if (isset($_REQUEST['archi_node_color']) && $_REQUEST['archi_node_color'] !== '') {
        $color = sanitize_hex_color($_REQUEST['archi_node_color']);
        if ($color) {
            update_post_meta($post_id, '_archi_node_color', $color);
            delete_transient('archi_graph_articles');
        }
    }
}
add_action('save_post', 'archi_save_quick_edit_data');

/**
 * Add dashboard widget for pending submissions
 */
function archi_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'archi_pending_submissions',
        __('Soumissions en attente', 'archi-graph'),
        'archi_render_pending_submissions_widget'
    );
}
add_action('wp_dashboard_setup', 'archi_add_dashboard_widget');

/**
 * Render pending submissions widget
 */
function archi_render_pending_submissions_widget() {
    $pending_posts = get_posts([
        'post_type' => ['post', 'archi_project', 'archi_illustration'],
        'post_status' => 'pending',
        'posts_per_page' => 10,
        'orderby' => 'date',
        'order' => 'DESC'
    ]);
    
    if (empty($pending_posts)) {
        echo '<p>' . __('Aucune soumission en attente.', 'archi-graph') . '</p>';
        return;
    }
    
    echo '<div class="archi-pending-submissions-list">';
    
    foreach ($pending_posts as $post) {
        $post_type_obj = get_post_type_object($post->post_type);
        $has_wpforms_entry = get_post_meta($post->ID, '_archi_wpforms_entry_id', true);
        
        ?>
        <div class="archi-pending-item">
            <div class="archi-pending-header">
                <strong><?php echo esc_html($post->post_title); ?></strong>
                <span class="archi-pending-badge"><?php echo esc_html($post_type_obj->labels->singular_name); ?></span>
            </div>
            
            <div class="archi-pending-meta">
                <span class="dashicons dashicons-calendar"></span>
                <?php echo human_time_diff(strtotime($post->post_date), current_time('timestamp')); ?>
                <?php _e('ago', 'archi-graph'); ?>
                
                <?php if ($has_wpforms_entry): ?>
                <span class="archi-form-indicator" title="<?php _e('Soumis via formulaire', 'archi-graph'); ?>">
                    <span class="dashicons dashicons-feedback"></span>
                </span>
                <?php endif; ?>
            </div>
            
            <div class="archi-pending-actions">
                <a href="<?php echo get_edit_post_link($post->ID); ?>" class="button button-small">
                    <?php _e('Modifier', 'archi-graph'); ?>
                </a>
                <a href="<?php echo get_preview_post_link($post->ID); ?>" 
                   class="button button-small" 
                   target="_blank">
                    <?php _e('Prévisualiser', 'archi-graph'); ?>
                </a>
            </div>
        </div>
        <?php
    }
    
    echo '</div>';
    
    echo '<p class="archi-view-all">';
    echo '<a href="' . admin_url('edit.php?post_status=pending') . '">';
    _e('Voir toutes les soumissions en attente →', 'archi-graph');
    echo '</a>';
    echo '</p>';
}

/**
 * Add dashboard widget styles
 */
function archi_dashboard_widget_styles() {
    if (!is_admin()) {
        return;
    }
    
    ?>
    <style>
    .archi-pending-submissions-list {
        margin: 15px 0;
    }
    
    .archi-pending-item {
        padding: 12px;
        margin-bottom: 10px;
        background: #f8f9fa;
        border-left: 4px solid #3498db;
        border-radius: 4px;
    }
    
    .archi-pending-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    
    .archi-pending-badge {
        padding: 3px 8px;
        background: #3498db;
        color: white;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .archi-pending-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        font-size: 13px;
        color: #666;
    }
    
    .archi-pending-meta .dashicons {
        font-size: 16px;
        width: 16px;
        height: 16px;
    }
    
    .archi-form-indicator {
        margin-left: 10px;
        color: #27ae60;
    }
    
    .archi-pending-actions {
        display: flex;
        gap: 8px;
    }
    
    .archi-view-all {
        text-align: center;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #ddd;
    }
    
    .archi-view-all a {
        font-weight: 600;
        text-decoration: none;
    }
    </style>
    <?php
}
add_action('admin_head', 'archi_dashboard_widget_styles');

/**
 * Add export functionality for articles with metadata
 */
function archi_add_export_submenu() {
    add_submenu_page(
        'edit.php',
        __('Exporter les articles', 'archi-graph'),
        __('Exporter', 'archi-graph'),
        'export',
        'archi-export',
        'archi_render_export_page'
    );
}
add_action('admin_menu', 'archi_add_export_submenu');

/**
 * Render export page
 */
function archi_render_export_page() {
    if (!current_user_can('export')) {
        wp_die(__('Vous n\'avez pas les permissions nécessaires.', 'archi-graph'));
    }
    
    // Handle export
    if (isset($_POST['archi_export']) && check_admin_referer('archi_export_action')) {
        archi_generate_csv_export();
        return;
    }
    
    ?>
    <div class="wrap">
        <h1><?php _e('Exporter les articles avec métadonnées', 'archi-graph'); ?></h1>
        
        <div class="card">
            <h2><?php _e('Options d\'export', 'archi-graph'); ?></h2>
            
            <form method="post">
                <?php wp_nonce_field('archi_export_action'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Types de contenu', 'archi-graph'); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="checkbox" name="export_types[]" value="post" checked>
                                    <?php _e('Articles', 'archi-graph'); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="export_types[]" value="archi_project" checked>
                                    <?php _e('Projets', 'archi-graph'); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="export_types[]" value="archi_illustration" checked>
                                    <?php _e('Illustrations', 'archi-graph'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Inclure', 'archi-graph'); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="checkbox" name="include_metadata" checked>
                                    <?php _e('Métadonnées personnalisées', 'archi-graph'); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="include_graph_data" checked>
                                    <?php _e('Données du graphique', 'archi-graph'); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="include_taxonomies" checked>
                                    <?php _e('Catégories et étiquettes', 'archi-graph'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" name="archi_export" class="button button-primary">
                        <?php _e('Générer l\'export CSV', 'archi-graph'); ?>
                    </button>
                </p>
            </form>
        </div>
    </div>
    <?php
}

/**
 * Generate CSV export
 */
function archi_generate_csv_export() {
    $export_types = isset($_POST['export_types']) ? (array) $_POST['export_types'] : ['post'];
    $include_metadata = isset($_POST['include_metadata']);
    $include_graph_data = isset($_POST['include_graph_data']);
    $include_taxonomies = isset($_POST['include_taxonomies']);
    
    $posts = get_posts([
        'post_type' => $export_types,
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC'
    ]);
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=archi-export-' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Headers
    $headers = ['ID', 'Titre', 'Type', 'Date', 'Auteur', 'URL'];
    
    if ($include_metadata) {
        $headers = array_merge($headers, ['Surface', 'Coût', 'Client', 'Localisation', 'Technique', 'Logiciels']);
    }
    
    if ($include_graph_data) {
        $headers = array_merge($headers, ['Dans le graphique', 'Couleur', 'Taille', 'Priorité']);
    }
    
    if ($include_taxonomies) {
        $headers = array_merge($headers, ['Catégories', 'Étiquettes']);
    }
    
    fputcsv($output, $headers);
    
    // Data rows
    foreach ($posts as $post) {
        $row = [
            $post->ID,
            $post->post_title,
            get_post_type_object($post->post_type)->labels->singular_name,
            get_the_date('Y-m-d H:i', $post->ID),
            get_the_author_meta('display_name', $post->post_author),
            get_permalink($post->ID)
        ];
        
        if ($include_metadata) {
            $row = array_merge($row, [
                get_post_meta($post->ID, '_archi_project_surface', true),
                get_post_meta($post->ID, '_archi_project_cost', true),
                get_post_meta($post->ID, '_archi_project_client', true),
                get_post_meta($post->ID, '_archi_project_location', true),
                get_post_meta($post->ID, '_archi_illustration_technique', true),
                get_post_meta($post->ID, '_archi_illustration_software', true),
            ]);
        }
        
        if ($include_graph_data) {
            $row = array_merge($row, [
                get_post_meta($post->ID, '_archi_show_in_graph', true) === '1' ? 'Oui' : 'Non',
                get_post_meta($post->ID, '_archi_node_color', true),
                get_post_meta($post->ID, '_archi_node_size', true),
                get_post_meta($post->ID, '_archi_priority_level', true),
            ]);
        }
        
        if ($include_taxonomies) {
            $categories = wp_get_post_categories($post->ID, ['fields' => 'names']);
            $tags = wp_get_post_tags($post->ID, ['fields' => 'names']);
            
            $row = array_merge($row, [
                implode(', ', $categories),
                implode(', ', $tags),
            ]);
        }
        
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}

