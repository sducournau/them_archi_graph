<?php
/**
 * Page d'administration avancée pour la gestion du graphique
 * 
 * NOTE: This file contains utility functions and page callbacks for graph management.
 * Menu registration is handled by inc/admin-settings.php
 * 
 * @package ArchiGraph
 * @since 1.0.0
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistrer les scripts et styles pour l'admin
 */
function archi_graph_admin_enqueue_scripts($hook) {
    if (strpos($hook, 'archi-graph') === false) {
        return;
    }
    
    wp_enqueue_style('archi-graph-admin', get_template_directory_uri() . '/assets/css/graph-admin.css', [], '1.0.0');
    wp_enqueue_script('archi-graph-admin', get_template_directory_uri() . '/assets/js/graph-admin.js', ['jquery'], '1.0.0', true);
    
    wp_localize_script('archi-graph-admin', 'archiGraphAdmin', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('archi_graph_admin'),
        'apiUrl' => rest_url('archi/v1/'),
        'restNonce' => wp_create_nonce('wp_rest')
    ]);
}
add_action('admin_enqueue_scripts', 'archi_graph_admin_enqueue_scripts');

/**
 * Page principale - Vue d'ensemble
 */
function archi_graph_manager_page() {
    $stats = archi_get_graph_statistics();
    ?>
    <div class="wrap archi-graph-admin">
        <h1><?php _e('Gestion du Graphique Interactif', 'archi-graph'); ?></h1>
        
        <div class="archi-dashboard">
            <div class="archi-stats-grid">
                <div class="archi-stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-content">
                        <h3><?php echo number_format($stats['total_nodes']); ?></h3>
                        <p><?php _e('Nœuds total', 'archi-graph'); ?></p>
                    </div>
                </div>
                
                <div class="archi-stat-card">
                    <div class="stat-icon">✓</div>
                    <div class="stat-content">
                        <h3><?php echo number_format($stats['active_nodes']); ?></h3>
                        <p><?php _e('Nœuds actifs', 'archi-graph'); ?></p>
                    </div>
                </div>
                
                <div class="archi-stat-card">
                    <div class="stat-icon">🏷️</div>
                    <div class="stat-content">
                        <h3><?php echo number_format($stats['categories']); ?></h3>
                        <p><?php _e('Catégories', 'archi-graph'); ?></p>
                    </div>
                </div>
                
                <div class="archi-stat-card">
                    <div class="stat-icon">🔗</div>
                    <div class="stat-content">
                        <h3><?php echo number_format($stats['relations']); ?></h3>
                        <p><?php _e('Relations', 'archi-graph'); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="archi-admin-sections">
                <div class="archi-admin-section">
                    <h2><?php _e('Répartition par type de contenu', 'archi-graph'); ?></h2>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('Type de contenu', 'archi-graph'); ?></th>
                                <th><?php _e('Total', 'archi-graph'); ?></th>
                                <th><?php _e('Dans le graphique', 'archi-graph'); ?></th>
                                <th><?php _e('Pourcentage', 'archi-graph'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['by_type'] as $type => $data): ?>
                            <tr>
                                <td><strong><?php echo esc_html($data['label']); ?></strong></td>
                                <td><?php echo number_format($data['total']); ?></td>
                                <td><?php echo number_format($data['in_graph']); ?></td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo esc_attr($data['percentage']); ?>%;"></div>
                                        <span class="progress-text"><?php echo esc_html($data['percentage']); ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="archi-admin-section">
                    <h2><?php _e('Actions rapides', 'archi-graph'); ?></h2>
                    <div class="archi-quick-actions">
                        <a href="<?php echo admin_url('admin.php?page=archi-graph-nodes'); ?>" class="button button-primary button-large">
                            <?php _e('Gérer les nœuds', 'archi-graph'); ?>
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=archi-graph-relations'); ?>" class="button button-primary button-large">
                            <?php _e('Gérer les relations', 'archi-graph'); ?>
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=archi-graph-categories'); ?>" class="button button-primary button-large">
                            <?php _e('Gérer les catégories', 'archi-graph'); ?>
                        </a>
                        <button type="button" class="button button-secondary button-large" id="archi-reset-positions">
                            <?php _e('Réinitialiser les positions', 'archi-graph'); ?>
                        </button>
                        <button type="button" class="button button-secondary button-large" id="archi-export-data">
                            <?php _e('Exporter les données', 'archi-graph'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Classe pour afficher la liste des nœuds avec WP_List_Table
 */
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Archi_Nodes_List_Table extends WP_List_Table {
    
    /**
     * Constructeur
     */
    public function __construct() {
        parent::__construct([
            'singular' => __('nœud', 'archi-graph'),
            'plural'   => __('nœuds', 'archi-graph'),
            'ajax'     => false
        ]);
    }
    
    /**
     * Définir les colonnes
     */
    public function get_columns() {
        return [
            'cb'            => '<input type="checkbox" />',
            'title'         => __('Titre', 'archi-graph'),
            'post_type'     => __('Type', 'archi-graph'),
            'show_in_graph' => __('Visible dans le graphique', 'archi-graph'),
            'node_color'    => __('Couleur', 'archi-graph'),
            'node_size'     => __('Taille', 'archi-graph'),
            'connections'   => __('Connexions', 'archi-graph'),
            'date'          => __('Date', 'archi-graph')
        ];
    }
    
    /**
     * Colonnes triables
     */
    public function get_sortable_columns() {
        return [
            'title'    => ['title', false],
            'post_type' => ['post_type', false],
            'date'     => ['date', true]
        ];
    }
    
    /**
     * Colonne checkbox
     */
    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="node_ids[]" value="%s" />',
            $item->ID
        );
    }
    
    /**
     * Colonne titre avec actions
     */
    public function column_title($item) {
        $edit_url = get_edit_post_link($item->ID);
        $view_url = get_permalink($item->ID);
        
        $actions = [
            'edit' => sprintf('<a href="%s">%s</a>', $edit_url, __('Modifier', 'archi-graph')),
            'view' => sprintf('<a href="%s">%s</a>', $view_url, __('Voir', 'archi-graph')),
        ];
        
        return sprintf(
            '<strong><a href="%s">%s</a></strong>%s',
            $edit_url,
            esc_html($item->post_title),
            $this->row_actions($actions)
        );
    }
    
    /**
     * Colonne type de post
     */
    public function column_post_type($item) {
        $post_type_obj = get_post_type_object($item->post_type);
        return $post_type_obj ? $post_type_obj->labels->singular_name : $item->post_type;
    }
    
    /**
     * Colonne visible dans le graphique
     */
    public function column_show_in_graph($item) {
        $show_in_graph = get_post_meta($item->ID, '_archi_show_in_graph', true);
        if ($show_in_graph == '1') {
            return '<span class="dashicons dashicons-yes" style="color: green;"></span> ' . __('Oui', 'archi-graph');
        }
        return '<span class="dashicons dashicons-no" style="color: red;"></span> ' . __('Non', 'archi-graph');
    }
    
    /**
     * Colonne couleur
     */
    public function column_node_color($item) {
        $color = get_post_meta($item->ID, '_archi_node_color', true) ?: '#3498db';
        return sprintf(
            '<span style="display: inline-block; width: 20px; height: 20px; background-color: %s; border: 1px solid #ccc; border-radius: 3px;"></span> %s',
            esc_attr($color),
            esc_html($color)
        );
    }
    
    /**
     * Colonne taille
     */
    public function column_node_size($item) {
        $size = get_post_meta($item->ID, '_archi_node_size', true) ?: 80;
        return sprintf('%d px', intval($size));
    }
    
    /**
     * Colonne connexions
     */
    public function column_connections($item) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'archi_relations';
        
        // Vérifier si la table existe
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            return '0';
        }
        
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE source_id = %d OR target_id = %d",
            $item->ID,
            $item->ID
        ));
        
        return intval($count);
    }
    
    /**
     * Colonne date
     */
    public function column_date($item) {
        return mysql2date(get_option('date_format'), $item->post_date);
    }
    
    /**
     * Colonne par défaut
     */
    public function column_default($item, $column_name) {
        return isset($item->$column_name) ? $item->$column_name : '';
    }
    
    /**
     * Préparer les items
     */
    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = [$columns, $hidden, $sortable];
        
        // Récupérer les données
        $post_types = ['post', 'archi_project', 'archi_illustration'];
        $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
        $order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DESC';
        
        $args = [
            'post_type' => $post_types,
            'posts_per_page' => -1,
            'orderby' => $orderby,
            'order' => $order,
            'post_status' => 'any'
        ];
        
        $this->items = get_posts($args);
        
        // Pagination
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $total_items = count($this->items);
        
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ]);
        
        $this->items = array_slice($this->items, (($current_page - 1) * $per_page), $per_page);
    }
    
    /**
     * Actions en masse
     */
    public function get_bulk_actions() {
        return [
            'show'   => __('Afficher dans le graphique', 'archi-graph'),
            'hide'   => __('Masquer du graphique', 'archi-graph'),
            'delete' => __('Supprimer', 'archi-graph')
        ];
    }
}

/**
 * Page de gestion des nœuds
 */
function archi_graph_nodes_page() {
    // Traiter les actions en masse
    if (isset($_POST['action']) && $_POST['action'] !== '-1' && check_admin_referer('archi_bulk_action')) {
        archi_process_bulk_action();
    }
    
    $nodes = archi_get_all_nodes();
    $post_types = get_post_types(['public' => true], 'objects');
    ?>
    <div class="wrap archi-graph-admin">
        <h1><?php _e('Gestion des nœuds du graphique', 'archi-graph'); ?></h1>
        
        <form method="get">
            <input type="hidden" name="page" value="archi-graph-nodes">
            <?php
            $nodes_table = new Archi_Nodes_List_Table();
            $nodes_table->prepare_items();
            $nodes_table->display();
            ?>
        </form>
        
        <div class="archi-bulk-actions-panel">
            <h2><?php _e('Actions en masse', 'archi-graph'); ?></h2>
            <form method="post" id="archi-bulk-form">
                <?php wp_nonce_field('archi_bulk_action'); ?>
                <input type="hidden" name="node_ids" id="bulk-node-ids" value="">
                
                <div class="archi-bulk-options">
                    <label>
                        <input type="radio" name="action" value="show_in_graph">
                        <?php _e('Afficher dans le graphique', 'archi-graph'); ?>
                    </label>
                    <label>
                        <input type="radio" name="action" value="hide_from_graph">
                        <?php _e('Masquer du graphique', 'archi-graph'); ?>
                    </label>
                    <label>
                        <input type="radio" name="action" value="change_color">
                        <?php _e('Changer la couleur', 'archi-graph'); ?>
                        <input type="color" name="bulk_color" value="#3498db">
                    </label>
                    <label>
                        <input type="radio" name="action" value="reset_positions">
                        <?php _e('Réinitialiser les positions', 'archi-graph'); ?>
                    </label>
                </div>
                
                <button type="submit" class="button button-primary">
                    <?php _e('Appliquer', 'archi-graph'); ?>
                </button>
            </form>
        </div>
    </div>
    <?php
}

/**
 * Page de gestion des relations
 */
function archi_graph_relations_page() {
    $relations = archi_get_all_relations();
    ?>
    <div class="wrap archi-graph-admin">
        <h1><?php _e('Gestion des relations', 'archi-graph'); ?></h1>
        
        <div class="archi-relations-manager">
            <div class="archi-add-relation">
                <h2><?php _e('Ajouter une relation', 'archi-graph'); ?></h2>
                <form method="post" id="archi-add-relation-form">
                    <?php wp_nonce_field('archi_add_relation'); ?>
                    
                    <table class="form-table">
                        <tr>
                            <th><label for="source_node"><?php _e('Nœud source:', 'archi-graph'); ?></label></th>
                            <td>
                                <select name="source_node" id="source_node" class="regular-text" required>
                                    <option value=""><?php _e('Sélectionner...', 'archi-graph'); ?></option>
                                    <?php archi_render_node_options(); ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="target_node"><?php _e('Nœud cible:', 'archi-graph'); ?></label></th>
                            <td>
                                <select name="target_node" id="target_node" class="regular-text" required>
                                    <option value=""><?php _e('Sélectionner...', 'archi-graph'); ?></option>
                                    <?php archi_render_node_options(); ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="relation_type"><?php _e('Type de relation:', 'archi-graph'); ?></label></th>
                            <td>
                                <select name="relation_type" id="relation_type" class="regular-text">
                                    <option value="related"><?php _e('Lié à', 'archi-graph'); ?></option>
                                    <option value="inspired"><?php _e('Inspiré par', 'archi-graph'); ?></option>
                                    <option value="similar"><?php _e('Similaire à', 'archi-graph'); ?></option>
                                    <option value="evolution"><?php _e('Évolution de', 'archi-graph'); ?></option>
                                    <option value="contrast"><?php _e('Contraste avec', 'archi-graph'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="relation_strength"><?php _e('Force de la relation:', 'archi-graph'); ?></label></th>
                            <td>
                                <input type="range" 
                                       name="relation_strength" 
                                       id="relation_strength" 
                                       min="1" 
                                       max="10" 
                                       value="5"
                                       oninput="document.getElementById('strength-value').textContent = this.value">
                                <span id="strength-value">5</span>
                            </td>
                        </tr>
                    </table>
                    
                    <button type="submit" class="button button-primary">
                        <?php _e('Ajouter la relation', 'archi-graph'); ?>
                    </button>
                </form>
            </div>
            
            <div class="archi-relations-list">
                <h2><?php _e('Relations existantes', 'archi-graph'); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Source', 'archi-graph'); ?></th>
                            <th><?php _e('Type', 'archi-graph'); ?></th>
                            <th><?php _e('Cible', 'archi-graph'); ?></th>
                            <th><?php _e('Force', 'archi-graph'); ?></th>
                            <th><?php _e('Actions', 'archi-graph'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($relations)): ?>
                        <tr>
                            <td colspan="5" class="text-center">
                                <?php _e('Aucune relation définie', 'archi-graph'); ?>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($relations as $relation): ?>
                            <tr>
                                <td><strong><?php echo esc_html($relation['source_title']); ?></strong></td>
                                <td><?php echo esc_html($relation['type']); ?></td>
                                <td><strong><?php echo esc_html($relation['target_title']); ?></strong></td>
                                <td><?php echo esc_html($relation['strength']); ?>/10</td>
                                <td>
                                    <button class="button button-small edit-relation" data-id="<?php echo esc_attr($relation['id']); ?>">
                                        <?php _e('Modifier', 'archi-graph'); ?>
                                    </button>
                                    <button class="button button-small delete-relation" data-id="<?php echo esc_attr($relation['id']); ?>">
                                        <?php _e('Supprimer', 'archi-graph'); ?>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Page de gestion des catégories
 */
function archi_graph_categories_page() {
    $categories = get_categories(['hide_empty' => false]);
    ?>
    <div class="wrap archi-graph-admin">
        <h1><?php _e('Gestion des catégories et clusters', 'archi-graph'); ?></h1>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Catégorie', 'archi-graph'); ?></th>
                    <th><?php _e('Couleur', 'archi-graph'); ?></th>
                    <th><?php _e('Articles', 'archi-graph'); ?></th>
                    <th><?php _e('Position du cluster', 'archi-graph'); ?></th>
                    <th><?php _e('Actions', 'archi-graph'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): 
                    $color = get_term_meta($category->term_id, '_archi_category_color', true) ?: '#3498db';
                    $cluster_position = get_term_meta($category->term_id, '_archi_cluster_position', true);
                ?>
                <tr>
                    <td><strong><?php echo esc_html($category->name); ?></strong></td>
                    <td>
                        <input type="color" 
                               class="category-color" 
                               data-category="<?php echo esc_attr($category->term_id); ?>"
                               value="<?php echo esc_attr($color); ?>">
                    </td>
                    <td><?php echo number_format($category->count); ?></td>
                    <td>
                        <?php if ($cluster_position): ?>
                            X: <?php echo esc_html($cluster_position['x'] ?? 'auto'); ?>, 
                            Y: <?php echo esc_html($cluster_position['y'] ?? 'auto'); ?>
                        <?php else: ?>
                            <em><?php _e('Automatique', 'archi-graph'); ?></em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="button button-small edit-category" data-id="<?php echo esc_attr($category->term_id); ?>">
                            <?php _e('Configurer', 'archi-graph'); ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Page de configuration - CONSOLIDATED (merged from admin-settings.php)
 */
function archi_graph_config_page() {
    // Traitement de la soumission du formulaire
    if (isset($_POST['submit']) && wp_verify_nonce($_POST['archi_settings_nonce'], 'archi_settings_save')) {
        archi_save_graph_config_consolidated();
        echo '<div class="notice notice-success"><p>' . __('Paramètres sauvegardés !', 'archi-graph') . '</p></div>';
    }
    
    // Récupérer les options actuelles
    $options = archi_get_all_graph_options();
    ?>
    <div class="wrap archi-graph-admin">
        <h1><?php _e('Configuration du Graphique Archi', 'archi-graph'); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('archi_settings_save', 'archi_settings_nonce'); ?>
            
            <h2 class="nav-tab-wrapper">
                <a href="#tab-graph-physics" class="nav-tab nav-tab-active"><?php _e('Physique du Graphique', 'archi-graph'); ?></a>
                <a href="#tab-visual-defaults" class="nav-tab"><?php _e('Paramètres Visuels', 'archi-graph'); ?></a>
                <a href="#tab-behavior" class="nav-tab"><?php _e('Comportement', 'archi-graph'); ?></a>
                <a href="#tab-cache" class="nav-tab"><?php _e('Cache & Performance', 'archi-graph'); ?></a>
            </h2>
            
            <!-- Tab: Graph Physics -->
            <div id="tab-graph-physics" class="tab-content" style="display: block;">
                <h2><?php _e('Paramètres Physiques du Graphique', 'archi-graph'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="graph_animation_duration"><?php _e('Durée des animations (ms)', 'archi-graph'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="graph_animation_duration" name="graph_animation_duration" 
                                   value="<?php echo esc_attr($options['graph_animation_duration']); ?>"
                                   min="100" max="5000" step="100">
                            <p class="description"><?php _e('Durée des animations du graphique en millisecondes', 'archi-graph'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="graph_node_spacing"><?php _e('Espacement des nœuds', 'archi-graph'); ?></label>
                        </th>
                        <td>
                            <input type="range" id="graph_node_spacing" name="graph_node_spacing" 
                                   value="<?php echo esc_attr($options['graph_node_spacing']); ?>"
                                   min="50" max="200" step="10"
                                   oninput="document.getElementById('spacing-value').textContent = this.value">
                            <span id="spacing-value"><?php echo esc_html($options['graph_node_spacing']); ?></span>px
                            <p class="description"><?php _e('Espacement minimum entre les nœuds du graphique', 'archi-graph'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="graph_cluster_strength"><?php _e('Force de clustering', 'archi-graph'); ?></label>
                        </th>
                        <td>
                            <input type="range" id="graph_cluster_strength" name="graph_cluster_strength" 
                                   value="<?php echo esc_attr($options['graph_cluster_strength'] * 100); ?>"
                                   min="0" max="50" step="1"
                                   oninput="document.getElementById('cluster-value').textContent = this.value + '%'">
                            <span id="cluster-value"><?php echo esc_html($options['graph_cluster_strength'] * 100); ?>%</span>
                            <p class="description"><?php _e('Force de regroupement des nœuds par catégorie', 'archi-graph'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Options d\'affichage', 'archi-graph'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="graph_show_categories" value="1" 
                                       <?php checked($options['graph_show_categories']); ?>>
                                <?php _e('Afficher les catégories', 'archi-graph'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="graph_show_links" value="1" 
                                       <?php checked($options['graph_show_links']); ?>>
                                <?php _e('Afficher les liens entre articles', 'archi-graph'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Tab: Visual Defaults -->
            <div id="tab-visual-defaults" class="tab-content" style="display: none;">
                <h2><?php _e('Paramètres Visuels par Défaut', 'archi-graph'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="default_node_color"><?php _e('Couleur de nœud par défaut', 'archi-graph'); ?></label>
                        </th>
                        <td>
                            <input type="color" id="default_node_color" name="default_node_color" 
                                   value="<?php echo esc_attr($options['default_node_color']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="background_gradient_start"><?php _e('Couleur de fond (début)', 'archi-graph'); ?></label>
                        </th>
                        <td>
                            <input type="color" id="background_gradient_start" name="background_gradient_start" 
                                   value="<?php echo esc_attr($options['background_gradient_start']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="background_gradient_end"><?php _e('Couleur de fond (fin)', 'archi-graph'); ?></label>
                        </th>
                        <td>
                            <input type="color" id="background_gradient_end" name="background_gradient_end" 
                                   value="<?php echo esc_attr($options['background_gradient_end']); ?>">
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Tab: Behavior -->
            <div id="tab-behavior" class="tab-content" style="display: none;">
                <h2><?php _e('Comportement du Graphique', 'archi-graph'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="graph_auto_save_positions"><?php _e('Sauvegarde automatique', 'archi-graph'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="graph_auto_save_positions" name="graph_auto_save_positions" value="1" 
                                       <?php checked($options['graph_auto_save_positions']); ?>>
                                <?php _e('Sauvegarder automatiquement les positions des nœuds', 'archi-graph'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="graph_max_articles"><?php _e('Nombre maximum d\'articles', 'archi-graph'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="graph_max_articles" name="graph_max_articles" 
                                   value="<?php echo esc_attr($options['graph_max_articles']); ?>"
                                   min="10" max="500" step="10">
                            <p class="description"><?php _e('Nombre maximum d\'articles à afficher dans le graphique', 'archi-graph'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Tab: Cache & Performance -->
            <div id="tab-cache" class="tab-content" style="display: none;">
                <h2><?php _e('Cache et Performance', 'archi-graph'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="cache_duration"><?php _e('Durée du cache (minutes)', 'archi-graph'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="cache_duration" name="cache_duration" 
                                   value="<?php echo esc_attr($options['cache_duration'] / 60); ?>"
                                   min="1" max="1440" step="1">
                            <p class="description"><?php _e('Durée de mise en cache des données du graphique', 'archi-graph'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Actions cache', 'archi-graph'); ?></th>
                        <td>
                            <button type="button" class="button" onclick="archiClearCache()">
                                <?php _e('Vider le cache maintenant', 'archi-graph'); ?>
                            </button>
                            <p class="description"><?php _e('Supprime toutes les données en cache du graphique', 'archi-graph'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <h3><?php _e('Statistiques', 'archi-graph'); ?></h3>
                <?php archi_display_graph_stats(); ?>
            </div>
            
            <p class="submit">
                <input type="submit" name="submit" class="button button-primary" value="<?php _e('Enregistrer les modifications', 'archi-graph'); ?>">
            </p>
        </form>
    </div>
    
    <script>
    // Tab switching
    jQuery(document).ready(function($) {
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.tab-content').hide();
            $($(this).attr('href')).show();
        });
    });
    
    // Clear cache
    function archiClearCache() {
        if (!confirm('<?php echo esc_js(__('Êtes-vous sûr de vouloir vider le cache ?', 'archi-graph')); ?>')) {
            return;
        }
        
        jQuery.post(ajaxurl, {
            action: 'archi_clear_cache',
            nonce: '<?php echo wp_create_nonce('archi_clear_cache'); ?>'
        }, function(response) {
            if (response.success) {
                alert(response.data.message);
            }
        });
    }
    </script>
    <?php
}

/**
 * Obtenir les statistiques du graphique
 */
function archi_get_graph_statistics() {
    $post_types = ['post', 'archi_article', 'archi_illustration'];
    $stats = [
        'total_nodes' => 0,
        'active_nodes' => 0,
        'categories' => wp_count_terms('category'),
        'relations' => 0,
        'by_type' => []
    ];
    
    foreach ($post_types as $type) {
        // Vérifier si le type de post existe
        if (!post_type_exists($type)) {
            continue;
        }
        
        // Obtenir le nombre de posts publiés avec vérification
        $count_posts = wp_count_posts($type);
        $total = isset($count_posts->publish) ? (int) $count_posts->publish : 0;
        
        // Compter les posts actifs dans le graphique
        $active = count(get_posts([
            'post_type' => $type,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => [
                ['key' => '_archi_show_in_graph', 'value' => '1']
            ],
            'fields' => 'ids'
        ]));
        
        $stats['total_nodes'] += $total;
        $stats['active_nodes'] += $active;
        
        // Obtenir l'objet du type de post avec vérification
        $post_type_object = get_post_type_object($type);
        $label = $post_type_object && isset($post_type_object->label) 
            ? $post_type_object->label 
            : ucfirst($type);
        
        $stats['by_type'][$type] = [
            'label' => $label,
            'total' => $total,
            'in_graph' => $active,
            'percentage' => $total > 0 ? round(($active / $total) * 100, 1) : 0
        ];
    }
    
    return $stats;
}

/**
 * Obtenir tous les nœuds
 */
function archi_get_all_nodes() {
    $post_types = ['post', 'archi_article', 'archi_illustration'];
    return get_posts([
        'post_type' => $post_types,
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC'
    ]);
}

/**
 * Obtenir toutes les relations
 */
function archi_get_all_relations() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'archi_relations';
    
    // Créer la table si elle n'existe pas
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        archi_create_relations_table();
        return [];
    }
    
    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC", ARRAY_A);
    
    foreach ($results as &$relation) {
        $relation['source_title'] = get_the_title($relation['source_id']);
        $relation['target_title'] = get_the_title($relation['target_id']);
    }
    
    return $results;
}

/**
 * Créer la table des relations
 */
function archi_create_relations_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'archi_relations';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        source_id bigint(20) NOT NULL,
        target_id bigint(20) NOT NULL,
        relation_type varchar(50) NOT NULL,
        strength tinyint(2) DEFAULT 5,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY source_id (source_id),
        KEY target_id (target_id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Render options for node selection
 */
function archi_render_node_options() {
    $nodes = archi_get_all_nodes();
    foreach ($nodes as $node) {
        $type = get_post_type_object($node->post_type);
        echo '<option value="' . esc_attr($node->ID) . '">';
        echo esc_html($node->post_title) . ' (' . esc_html($type->labels->singular_name) . ')';
        echo '</option>';
    }
}

/**
 * Obtenir la configuration du graphique - CONSOLIDATED
 */
function archi_get_graph_config() {
    return [
        'animation_duration' => get_option('archi_animation_duration', 1000),
        'node_spacing' => get_option('archi_node_spacing', 100),
        'cluster_strength' => get_option('archi_cluster_strength', 10),
        'enabled_post_types' => get_option('archi_enabled_post_types', ['post', 'archi_article', 'archi_illustration'])
    ];
}

/**
 * Récupérer toutes les options du graphique (merged from admin-settings.php)
 */
function archi_get_all_graph_options() {
    return [
        'graph_animation_duration' => get_option('graph_animation_duration', 1000),
        'graph_node_spacing' => get_option('graph_node_spacing', 100),
        'graph_cluster_strength' => get_option('graph_cluster_strength', 0.1),
        'graph_show_categories' => (bool) get_option('graph_show_categories', 1),
        'graph_show_links' => (bool) get_option('graph_show_links', 1),
        'graph_auto_save_positions' => (bool) get_option('graph_auto_save_positions', 0),
        'graph_max_articles' => get_option('graph_max_articles', 100),
        'default_node_color' => get_option('default_node_color', '#3498db'),
        'background_gradient_start' => get_option('background_gradient_start', '#667eea'),
        'background_gradient_end' => get_option('background_gradient_end', '#764ba2'),
        'cache_duration' => get_option('cache_duration', 3600)
    ];
}

/**
 * Sauvegarder la configuration - CONSOLIDATED
 */
function archi_save_graph_config() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    update_option('archi_animation_duration', absint($_POST['animation_duration']));
    update_option('archi_node_spacing', absint($_POST['node_spacing']));
    update_option('archi_cluster_strength', floatval($_POST['cluster_strength']));
    update_option('archi_enabled_post_types', $_POST['enabled_post_types'] ?? []);
}

/**
 * Sauvegarder la configuration consolidée (merged from admin-settings.php)
 */
function archi_save_graph_config_consolidated() {
    $settings = [
        'graph_animation_duration' => absint($_POST['graph_animation_duration']),
        'graph_node_spacing' => absint($_POST['graph_node_spacing']),
        'graph_cluster_strength' => floatval($_POST['graph_cluster_strength']) / 100,
        'graph_show_categories' => isset($_POST['graph_show_categories']) ? 1 : 0,
        'graph_show_links' => isset($_POST['graph_show_links']) ? 1 : 0,
        'graph_auto_save_positions' => isset($_POST['graph_auto_save_positions']) ? 1 : 0,
        'graph_max_articles' => absint($_POST['graph_max_articles']),
        'default_node_color' => sanitize_hex_color($_POST['default_node_color']),
        'background_gradient_start' => sanitize_hex_color($_POST['background_gradient_start']),
        'background_gradient_end' => sanitize_hex_color($_POST['background_gradient_end']),
        'cache_duration' => absint($_POST['cache_duration']) * 60,
    ];
    
    foreach ($settings as $option => $value) {
        update_option($option, $value);
    }
    
    // Invalider le cache
    delete_transient('archi_graph_articles');
    delete_transient('archi_graph_categories');
}

/**
 * Afficher les statistiques du graphique (merged from admin-settings.php)
 */
function archi_display_graph_stats() {
    global $wpdb;
    
    // Compter les articles avec le graphique activé
    $graph_articles = $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->postmeta} pm 
         JOIN {$wpdb->posts} p ON pm.post_id = p.ID 
         WHERE pm.meta_key = '_archi_show_in_graph' 
         AND pm.meta_value = '1' 
         AND p.post_status = 'publish'"
    );
    
    // Compter les articles avec positions sauvegardées
    $positioned_articles = $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->postmeta} 
         WHERE meta_key = '_archi_graph_position'"
    );
    
    // Compter les catégories avec couleurs personnalisées
    $colored_categories = $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->termmeta} 
         WHERE meta_key = '_archi_category_color'"
    );
    
    echo '<ul>';
    echo '<li>' . sprintf(__('Articles dans le graphique : %d', 'archi-graph'), $graph_articles) . '</li>';
    echo '<li>' . sprintf(__('Articles avec positions sauvegardées : %d', 'archi-graph'), $positioned_articles) . '</li>';
    echo '<li>' . sprintf(__('Catégories avec couleurs personnalisées : %d', 'archi-graph'), $colored_categories) . '</li>';
    echo '</ul>';
}

/**
 * AJAX pour vider le cache (merged from admin-settings.php)
 */
function archi_clear_cache_ajax() {
    check_ajax_referer('archi_clear_cache', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('Accès non autorisé');
    }
    
    // Vider les transients
    delete_transient('archi_graph_articles');
    delete_transient('archi_graph_categories');
    
    // Vider le cache WP
    wp_cache_flush();
    
    wp_send_json_success(['message' => __('Cache vidé avec succès', 'archi-graph')]);
}
add_action('wp_ajax_archi_clear_cache', 'archi_clear_cache_ajax');

/**
 * Process bulk actions
 */
function archi_process_bulk_action() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $action = $_POST['action'];
    $node_ids = explode(',', $_POST['node_ids']);
    
    foreach ($node_ids as $node_id) {
        $node_id = absint($node_id);
        if (!$node_id) continue;
        
        switch ($action) {
            case 'show_in_graph':
                update_post_meta($node_id, '_archi_show_in_graph', '1');
                break;
            case 'hide_from_graph':
                update_post_meta($node_id, '_archi_show_in_graph', '0');
                break;
            case 'change_color':
                if (isset($_POST['bulk_color'])) {
                    update_post_meta($node_id, '_archi_node_color', sanitize_hex_color($_POST['bulk_color']));
                }
                break;
            case 'reset_positions':
                delete_post_meta($node_id, '_archi_graph_position');
                break;
        }
    }
}

// Créer la table des relations à l'activation
register_activation_hook(__FILE__, 'archi_create_relations_table');
