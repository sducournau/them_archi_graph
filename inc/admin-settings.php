<?php
/**
 * Interface d'administration pour Archi Graph
 * Consolide toutes les pages d'administration en une interface cohérente
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principale de l'interface admin
 */
class Archi_Admin_Settings {
    
    private static $instance = null;
    private $current_tab = 'dashboard';
    
    /**
     * Singleton
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructeur
     */
    private function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_ajax_archi_save_settings', [$this, 'ajax_save_settings']);
        add_action('wp_ajax_archi_clear_cache', [$this, 'ajax_clear_cache']);
        add_action('wp_ajax_archi_recalculate_relations', [$this, 'ajax_recalculate_relations']);
    }
    
    /**
     * Ajouter le menu admin
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Archi Graph', 'archi-graph'),
            __('Archi Graph', 'archi-graph'),
            'manage_options',
            'archi-admin',
            [$this, 'render_admin_page'],
            'dashicons-networking',
            30
        );
        
        // Sous-menus (pour l'accessibilité)
        add_submenu_page(
            'archi-admin',
            __('Dashboard', 'archi-graph'),
            __('Dashboard', 'archi-graph'),
            'manage_options',
            'archi-admin',
            [$this, 'render_admin_page']
        );
        
        add_submenu_page(
            'archi-admin',
            __('Configuration du Graphique', 'archi-graph'),
            __('Graphique', 'archi-graph'),
            'manage_options',
            'archi-admin&tab=graph',
            [$this, 'render_admin_page']
        );
        
        add_submenu_page(
            'archi-admin',
            __('Types de Contenu', 'archi-graph'),
            __('Contenus', 'archi-graph'),
            'manage_options',
            'archi-admin&tab=content',
            [$this, 'render_admin_page']
        );
        
        add_submenu_page(
            'archi-admin',
            __('Blocs Gutenberg', 'archi-graph'),
            __('Blocs', 'archi-graph'),
            'manage_options',
            'archi-admin&tab=blocks',
            [$this, 'render_admin_page']
        );
        
        add_submenu_page(
            'archi-admin',
            __('Outils & Maintenance', 'archi-graph'),
            __('Outils', 'archi-graph'),
            'manage_options',
            'archi-admin&tab=tools',
            [$this, 'render_admin_page']
        );
    }
    
    /**
     * Enregistrer les assets
     */
    public function enqueue_assets($hook) {
        if (strpos($hook, 'archi-admin') === false) {
            return;
        }
        
        // Scripts
        wp_enqueue_script(
            'archi-admin-settings',
            get_template_directory_uri() . '/assets/js/admin-settings.js',
            ['jquery', 'wp-api'],
            '1.0.0',
            true
        );
        
        // Localisation
        wp_localize_script('archi-admin-settings', 'archiAdmin', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('archi_admin_settings'),
            'restUrl' => rest_url('archi/v1/'),
            'restNonce' => wp_create_nonce('wp_rest'),
            'strings' => [
                'saved' => __('Paramètres enregistrés', 'archi-graph'),
                'error' => __('Erreur lors de la sauvegarde', 'archi-graph'),
                'confirm' => __('Êtes-vous sûr ?', 'archi-graph')
            ]
        ]);
    }
    
    /**
     * Enregistrer les settings
     */
    public function register_settings() {
        // Paramètres du graphique
        register_setting('archi_graph_settings', 'archi_graph_auto_add_posts', [
            'type' => 'boolean',
            'default' => false,
            'sanitize_callback' => 'rest_sanitize_boolean'
        ]);
        
        register_setting('archi_graph_settings', 'archi_graph_auto_calculate_relations', [
            'type' => 'boolean',
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean'
        ]);
        
        register_setting('archi_graph_settings', 'archi_graph_link_strength', [
            'type' => 'integer',
            'default' => 80,
            'sanitize_callback' => 'absint'
        ]);
        
        register_setting('archi_graph_settings', 'archi_graph_min_distance', [
            'type' => 'integer',
            'default' => 100,
            'sanitize_callback' => 'absint'
        ]);
        
        register_setting('archi_graph_settings', 'archi_graph_default_color', [
            'type' => 'string',
            'default' => '#3498db',
            'sanitize_callback' => 'sanitize_hex_color'
        ]);
        
        register_setting('archi_graph_settings', 'archi_graph_default_size', [
            'type' => 'integer',
            'default' => 60,
            'sanitize_callback' => 'absint'
        ]);
        
        register_setting('archi_graph_settings', 'archi_graph_animation_enabled', [
            'type' => 'boolean',
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean'
        ]);
        
        register_setting('archi_graph_settings', 'archi_graph_show_labels', [
            'type' => 'boolean',
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean'
        ]);
        
        // Nouveaux paramètres d'animation
        register_setting('archi_graph_settings', 'archi_graph_animation_type', [
            'type' => 'string',
            'default' => 'fadeIn',
            'sanitize_callback' => 'sanitize_text_field'
        ]);
        
        register_setting('archi_graph_settings', 'archi_graph_animation_duration', [
            'type' => 'integer',
            'default' => 800,
            'sanitize_callback' => 'absint'
        ]);
        
        register_setting('archi_graph_settings', 'archi_graph_hover_effect', [
            'type' => 'boolean',
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean'
        ]);
        
        register_setting('archi_graph_settings', 'archi_graph_hover_scale', [
            'type' => 'number',
            'default' => 1.15,
            'sanitize_callback' => 'floatval'
        ]);
        
        register_setting('archi_graph_settings', 'archi_graph_link_animation', [
            'type' => 'boolean',
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean'
        ]);
        
        register_setting('archi_graph_settings', 'archi_graph_organic_mode', [
            'type' => 'boolean',
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean'
        ]);
        
        register_setting('archi_graph_settings', 'archi_graph_cluster_strength', [
            'type' => 'number',
            'default' => 0.1,
            'sanitize_callback' => 'floatval'
        ]);
    }
    
    /**
     * Render de la page principale
     */
    public function render_admin_page() {
        // Vérifier permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires.', 'archi-graph'));
        }
        
        // Déterminer l'onglet actif
        $this->current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'dashboard';
        
        ?>
                ?>
        
        <div class="wrap archi-admin-settings">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php $this->render_tabs(); ?>
            
            <div class="archi-admin-content">
                <?php $this->render_tab_content(); ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render des onglets
     */
    private function render_tabs() {
        $tabs = [
            'dashboard' => [
                'label' => __('Dashboard', 'archi-graph'),
                'icon' => '📊'
            ],
            'graph' => [
                'label' => __('Graphique', 'archi-graph'),
                'icon' => '🎨'
            ],
            'content' => [
                'label' => __('Contenus', 'archi-graph'),
                'icon' => '📝'
            ],
            'blocks' => [
                'label' => __('Blocs', 'archi-graph'),
                'icon' => '🧱'
            ],
            'tools' => [
                'label' => __('Outils', 'archi-graph'),
                'icon' => '🔧'
            ]
        ];
        
        ?>
        <nav class="archi-nav-tabs">
            <?php foreach ($tabs as $key => $tab): ?>
                <a href="<?php echo admin_url('admin.php?page=archi-admin&tab=' . $key); ?>"
                   class="archi-nav-tab <?php echo $this->current_tab === $key ? 'active' : ''; ?>">
                    <span class="tab-icon"><?php echo $tab['icon']; ?></span>
                    <span class="tab-label"><?php echo esc_html($tab['label']); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <?php
    }
    
    /**
     * Render du contenu de l'onglet
     */
    private function render_tab_content() {
        switch ($this->current_tab) {
            case 'dashboard':
                $this->render_dashboard_tab();
                break;
            case 'graph':
                $this->render_graph_tab();
                break;
            case 'content':
                $this->render_content_tab();
                break;
            case 'blocks':
                $this->render_blocks_tab();
                break;
            case 'tools':
                $this->render_tools_tab();
                break;
            default:
                $this->render_dashboard_tab();
        }
    }
    
    /**
     * Onglet Dashboard
     */
    private function render_dashboard_tab() {
        $stats = $this->get_dashboard_stats();
        ?>
        <div class="archi-dashboard">
            <h2><?php _e('Vue d\'ensemble', 'archi-graph'); ?></h2>
            
            <div class="archi-stats-grid">
                <div class="archi-stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-content">
                        <h3><?php echo number_format($stats['total_posts']); ?></h3>
                        <p><?php _e('Contenus totaux', 'archi-graph'); ?></p>
                    </div>
                </div>
                
                <div class="archi-stat-card">
                    <div class="stat-icon">✓</div>
                    <div class="stat-content">
                        <h3><?php echo number_format($stats['active_nodes']); ?></h3>
                        <p><?php _e('Nœuds actifs dans le graphe', 'archi-graph'); ?></p>
                    </div>
                </div>
                
                <div class="archi-stat-card">
                    <div class="stat-icon">🏗️</div>
                    <div class="stat-content">
                        <h3><?php echo number_format($stats['projects']); ?></h3>
                        <p><?php _e('Projets architecturaux', 'archi-graph'); ?></p>
                    </div>
                </div>
                
                <div class="archi-stat-card">
                    <div class="stat-icon">🎨</div>
                    <div class="stat-content">
                        <h3><?php echo number_format($stats['illustrations']); ?></h3>
                        <p><?php _e('Illustrations', 'archi-graph'); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="archi-dashboard-sections">
                <div class="archi-dashboard-section">
                    <h3><?php _e('Actions rapides', 'archi-graph'); ?></h3>
                    <div class="archi-quick-actions">
                        <a href="<?php echo admin_url('post-new.php?post_type=archi_project'); ?>" class="button button-primary">
                            <?php _e('Nouveau Projet', 'archi-graph'); ?>
                        </a>
                        <a href="<?php echo admin_url('post-new.php?post_type=archi_illustration'); ?>" class="button button-primary">
                            <?php _e('Nouvelle Illustration', 'archi-graph'); ?>
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=archi-admin&tab=graph'); ?>" class="button">
                            <?php _e('Gérer le Graphique', 'archi-graph'); ?>
                        </a>
                        <a href="<?php echo home_url('/'); ?>" class="button" target="_blank">
                            <?php _e('Voir le site', 'archi-graph'); ?>
                        </a>
                    </div>
                </div>
                
                <div class="archi-dashboard-section">
                    <h3><?php _e('Santé du système', 'archi-graph'); ?></h3>
                    <?php $this->render_system_health(); ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Onglet Graphique
     */
    private function render_graph_tab() {
        ?>
        <form method="post" action="options.php" class="archi-settings-form">
            <?php settings_fields('archi_graph_settings'); ?>
            
            <div class="archi-settings-section">
                <h2><?php _e('Configuration du Graphique Interactif', 'archi-graph'); ?></h2>
                
                <div class="archi-settings-group">
                    <h3><?php _e('Nœuds & Relations', 'archi-graph'); ?></h3>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="archi_graph_auto_add_posts">
                                    <?php _e('Ajout automatique', 'archi-graph'); ?>
                                </label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" 
                                           name="archi_graph_auto_add_posts" 
                                           id="archi_graph_auto_add_posts" 
                                           value="1" 
                                           <?php checked(get_option('archi_graph_auto_add_posts', false)); ?>>
                                    <?php _e('Afficher automatiquement les nouveaux posts dans le graphique', 'archi-graph'); ?>
                                </label>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="archi_graph_auto_calculate_relations">
                                    <?php _e('Relations automatiques', 'archi-graph'); ?>
                                </label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" 
                                           name="archi_graph_auto_calculate_relations" 
                                           id="archi_graph_auto_calculate_relations" 
                                           value="1" 
                                           <?php checked(get_option('archi_graph_auto_calculate_relations', true)); ?>>
                                    <?php _e('Calculer automatiquement les relations entre articles', 'archi-graph'); ?>
                                </label>
                                <p class="description">
                                    <?php _e('Basé sur les catégories, tags et contenu similaire', 'archi-graph'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="archi_graph_link_strength">
                                    <?php _e('Force de liaison', 'archi-graph'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="range" 
                                       name="archi_graph_link_strength" 
                                       id="archi_graph_link_strength" 
                                       min="0" 
                                       max="100" 
                                       value="<?php echo esc_attr(get_option('archi_graph_link_strength', 80)); ?>"
                                       class="archi-range-slider">
                                <span class="range-value"></span>%
                                <p class="description">
                                    <?php _e('Intensité des forces de liaison entre les nœuds', 'archi-graph'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="archi_graph_min_distance">
                                    <?php _e('Distance minimale', 'archi-graph'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="number" 
                                       name="archi_graph_min_distance" 
                                       id="archi_graph_min_distance" 
                                       min="50" 
                                       max="300" 
                                       value="<?php echo esc_attr(get_option('archi_graph_min_distance', 100)); ?>"
                                       class="small-text">
                                px
                                <p class="description">
                                    <?php _e('Distance minimale entre les nœuds', 'archi-graph'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="archi-settings-group">
                    <h3><?php _e('Apparence', 'archi-graph'); ?></h3>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="archi_graph_default_color">
                                    <?php _e('Couleur par défaut', 'archi-graph'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="color" 
                                       name="archi_graph_default_color" 
                                       id="archi_graph_default_color" 
                                       value="<?php echo esc_attr(get_option('archi_graph_default_color', '#3498db')); ?>">
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="archi_graph_default_size">
                                    <?php _e('Taille par défaut', 'archi-graph'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="range" 
                                       name="archi_graph_default_size" 
                                       id="archi_graph_default_size" 
                                       min="40" 
                                       max="120" 
                                       value="<?php echo esc_attr(get_option('archi_graph_default_size', 60)); ?>"
                                       class="archi-range-slider">
                                <span class="range-value"></span>px
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="archi_graph_animation_enabled">
                                    <?php _e('Animations', 'archi-graph'); ?>
                                </label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" 
                                           name="archi_graph_animation_enabled" 
                                           id="archi_graph_animation_enabled" 
                                           value="1" 
                                           <?php checked(get_option('archi_graph_animation_enabled', true)); ?>>
                                    <?php _e('Activer les animations au chargement', 'archi-graph'); ?>
                                </label>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="archi_graph_show_labels">
                                    <?php _e('Labels', 'archi-graph'); ?>
                                </label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" 
                                           name="archi_graph_show_labels" 
                                           id="archi_graph_show_labels" 
                                           value="1" 
                                           <?php checked(get_option('archi_graph_show_labels', true)); ?>>
                                    <?php _e('Afficher les labels des nœuds', 'archi-graph'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="archi-settings-group">
                    <h3><?php _e('Animations & Interactions', 'archi-graph'); ?></h3>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="archi_graph_animation_type">
                                    <?php _e('Type d\'animation', 'archi-graph'); ?>
                                </label>
                            </th>
                            <td>
                                <select name="archi_graph_animation_type" id="archi_graph_animation_type" class="regular-text">
                                    <option value="fadeIn" <?php selected(get_option('archi_graph_animation_type', 'fadeIn'), 'fadeIn'); ?>>
                                        <?php _e('Fade In - Apparition progressive', 'archi-graph'); ?>
                                    </option>
                                    <option value="scaleUp" <?php selected(get_option('archi_graph_animation_type'), 'scaleUp'); ?>>
                                        <?php _e('Scale Up - Zoom progressif', 'archi-graph'); ?>
                                    </option>
                                    <option value="bounce" <?php selected(get_option('archi_graph_animation_type'), 'bounce'); ?>>
                                        <?php _e('Bounce - Rebond élastique', 'archi-graph'); ?>
                                    </option>
                                    <option value="spiral" <?php selected(get_option('archi_graph_animation_type'), 'spiral'); ?>>
                                        <?php _e('Spiral - Spirale depuis le centre', 'archi-graph'); ?>
                                    </option>
                                    <option value="wave" <?php selected(get_option('archi_graph_animation_type'), 'wave'); ?>>
                                        <?php _e('Wave - Effet de vague', 'archi-graph'); ?>
                                    </option>
                                    <option value="elastic" <?php selected(get_option('archi_graph_animation_type'), 'elastic'); ?>>
                                        <?php _e('Elastic - Élastique exagéré', 'archi-graph'); ?>
                                    </option>
                                    <option value="stagger" <?php selected(get_option('archi_graph_animation_type'), 'stagger'); ?>>
                                        <?php _e('Stagger - Cascade progressive', 'archi-graph'); ?>
                                    </option>
                                    <option value="explode" <?php selected(get_option('archi_graph_animation_type'), 'explode'); ?>>
                                        <?php _e('Explode - Explosion depuis le centre', 'archi-graph'); ?>
                                    </option>
                                    <option value="morph" <?php selected(get_option('archi_graph_animation_type'), 'morph'); ?>>
                                        <?php _e('Morph - Transformation de forme', 'archi-graph'); ?>
                                    </option>
                                </select>
                                <p class="description">
                                    <?php _e('Effet d\'animation lors de l\'apparition des nœuds', 'archi-graph'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="archi_graph_animation_duration">
                                    <?php _e('Durée d\'animation', 'archi-graph'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="range" 
                                       name="archi_graph_animation_duration" 
                                       id="archi_graph_animation_duration" 
                                       min="200" 
                                       max="2000" 
                                       step="100"
                                       value="<?php echo esc_attr(get_option('archi_graph_animation_duration', 800)); ?>"
                                       class="archi-range-slider">
                                <span class="range-value"></span>ms
                                <p class="description">
                                    <?php _e('Durée de l\'animation en millisecondes', 'archi-graph'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="archi_graph_hover_effect">
                                    <?php _e('Effet de survol', 'archi-graph'); ?>
                                </label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" 
                                           name="archi_graph_hover_effect" 
                                           id="archi_graph_hover_effect" 
                                           value="1" 
                                           <?php checked(get_option('archi_graph_hover_effect', true)); ?>>
                                    <?php _e('Activer l\'effet de zoom au survol', 'archi-graph'); ?>
                                </label>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="archi_graph_hover_scale">
                                    <?php _e('Intensité du zoom', 'archi-graph'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="range" 
                                       name="archi_graph_hover_scale" 
                                       id="archi_graph_hover_scale" 
                                       min="1.0" 
                                       max="1.5" 
                                       step="0.05"
                                       value="<?php echo esc_attr(get_option('archi_graph_hover_scale', 1.15)); ?>"
                                       class="archi-range-slider">
                                <span class="range-value"></span>x
                                <p class="description">
                                    <?php _e('Facteur d\'agrandissement au survol', 'archi-graph'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="archi_graph_link_animation">
                                    <?php _e('Animation des liens', 'archi-graph'); ?>
                                </label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" 
                                           name="archi_graph_link_animation" 
                                           id="archi_graph_link_animation" 
                                           value="1" 
                                           <?php checked(get_option('archi_graph_link_animation', true)); ?>>
                                    <?php _e('Animer l\'apparition des liens (tracé progressif)', 'archi-graph'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="archi-settings-group">
                    <h3><?php _e('Mode Organique', 'archi-graph'); ?></h3>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="archi_graph_organic_mode">
                                    <?php _e('Mode organique', 'archi-graph'); ?>
                                </label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" 
                                           name="archi_graph_organic_mode" 
                                           id="archi_graph_organic_mode" 
                                           value="1" 
                                           <?php checked(get_option('archi_graph_organic_mode', true)); ?>>
                                    <?php _e('Activer le mode organique avec îles architecturales', 'archi-graph'); ?>
                                </label>
                                <p class="description">
                                    <?php _e('Crée des regroupements naturels de projets liés', 'archi-graph'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="archi_graph_cluster_strength">
                                    <?php _e('Force de clustering', 'archi-graph'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="range" 
                                       name="archi_graph_cluster_strength" 
                                       id="archi_graph_cluster_strength" 
                                       min="0" 
                                       max="1" 
                                       step="0.05"
                                       value="<?php echo esc_attr(get_option('archi_graph_cluster_strength', 0.1)); ?>"
                                       class="archi-range-slider">
                                <span class="range-value"></span>
                                <p class="description">
                                    <?php _e('Intensité de l\'attraction des nœuds vers leur groupe', 'archi-graph'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <?php submit_button(__('Enregistrer les paramètres', 'archi-graph')); ?>
        </form>
        <?php
    }
    
    /**
     * Onglet Types de Contenu
     */
    private function render_content_tab() {
        ?>
        <div class="archi-content-settings">
            <h2><?php _e('Gestion des Types de Contenu', 'archi-graph'); ?></h2>
            
            <div class="archi-content-type-card">
                <h3>🏗️ <?php _e('Projets Architecturaux', 'archi-graph'); ?></h3>
                <p><?php _e('Métadonnées: Surface, Coût, Client, Localisation, Année, Durée, Équipe', 'archi-graph'); ?></p>
                <a href="<?php echo admin_url('edit.php?post_type=archi_project'); ?>" class="button">
                    <?php _e('Gérer les projets', 'archi-graph'); ?>
                </a>
            </div>
            
            <div class="archi-content-type-card">
                <h3>🎨 <?php _e('Illustrations', 'archi-graph'); ?></h3>
                <p><?php _e('Métadonnées: Technique, Logiciel, Dimensions, Année', 'archi-graph'); ?></p>
                <a href="<?php echo admin_url('edit.php?post_type=archi_illustration'); ?>" class="button">
                    <?php _e('Gérer les illustrations', 'archi-graph'); ?>
                </a>
            </div>
            
            <div class="archi-content-type-card">
                <h3>📝 <?php _e('Articles (Posts)', 'archi-graph'); ?></h3>
                <p><?php _e('Posts WordPress standards avec métadonnées graphiques', 'archi-graph'); ?></p>
                <a href="<?php echo admin_url('edit.php'); ?>" class="button">
                    <?php _e('Gérer les articles', 'archi-graph'); ?>
                </a>
            </div>
        </div>
        <?php
    }
    
    /**
     * Onglet Blocs
     */
    private function render_blocks_tab() {
        ?>
        <div class="archi-blocks-settings">
            <h2><?php _e('Blocs Gutenberg Disponibles', 'archi-graph'); ?></h2>
            <p class="description">
                <?php _e('12 blocs personnalisés disponibles dans l\'éditeur Gutenberg', 'archi-graph'); ?>
            </p>
            
            <div class="archi-blocks-grid">
                <?php
                $blocks = [
                    ['name' => 'interactive-graph', 'label' => 'Graphique Interactif', 'icon' => '🕸️'],
                    ['name' => 'project-showcase', 'label' => 'Vitrine Projets', 'icon' => '🏗️'],
                    ['name' => 'illustration-grid', 'label' => 'Grille Illustrations', 'icon' => '🎨'],
                    ['name' => 'category-filter', 'label' => 'Filtre Catégories', 'icon' => '🏷️'],
                    ['name' => 'featured-projects', 'label' => 'Projets Vedettes', 'icon' => '⭐'],
                    ['name' => 'timeline', 'label' => 'Timeline', 'icon' => '📅'],
                    ['name' => 'before-after', 'label' => 'Avant/Après', 'icon' => '↔️'],
                    ['name' => 'technical-specs', 'label' => 'Spécifications', 'icon' => '📋'],
                    ['name' => 'project-info', 'label' => 'Info Projet', 'icon' => 'ℹ️'],
                    ['name' => 'article-manager', 'label' => 'Gestionnaire Article', 'icon' => '⚙️']
                ];
                
                foreach ($blocks as $block):
                ?>
                <div class="archi-block-card">
                    <div class="block-icon"><?php echo $block['icon']; ?></div>
                    <div class="block-name"><?php echo esc_html($block['label']); ?></div>
                    <div class="block-slug">archi-graph/<?php echo esc_html($block['name']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Onglet Outils
     */
    private function render_tools_tab() {
        ?>
        <div class="archi-tools">
            <h2><?php _e('Outils & Maintenance', 'archi-graph'); ?></h2>
            
            <div class="archi-tool-section">
                <h3><?php _e('Cache', 'archi-graph'); ?></h3>
                <p><?php _e('Vider le cache du graphique pour forcer le rechargement des données', 'archi-graph'); ?></p>
                <button type="button" class="button" id="archi-clear-cache">
                    <?php _e('Vider le cache', 'archi-graph'); ?>
                </button>
            </div>
            
            <div class="archi-tool-section">
                <h3><?php _e('Recalcul des relations', 'archi-graph'); ?></h3>
                <p><?php _e('Recalculer toutes les relations automatiques entre les articles', 'archi-graph'); ?></p>
                <button type="button" class="button" id="archi-recalculate-relations">
                    <?php _e('Recalculer les relations', 'archi-graph'); ?>
                </button>
            </div>
            
            <?php if (WP_DEBUG): ?>
            <div class="archi-tool-section">
                <h3><?php _e('Données de test (DEV)', 'archi-graph'); ?></h3>
                <p><?php _e('Générer des données de test pour le développement', 'archi-graph'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=archi-sample-data'); ?>" class="button">
                    <?php _e('Gérer les données de test', 'archi-graph'); ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Obtenir les statistiques du dashboard
     */
    private function get_dashboard_stats() {
        return [
            'total_posts' => wp_count_posts('post')->publish + 
                            wp_count_posts('archi_project')->publish + 
                            wp_count_posts('archi_illustration')->publish,
            'active_nodes' => $this->count_active_nodes(),
            'projects' => wp_count_posts('archi_project')->publish,
            'illustrations' => wp_count_posts('archi_illustration')->publish
        ];
    }
    
    /**
     * Compter les nœuds actifs
     */
    private function count_active_nodes() {
        global $wpdb;
        
        return (int) $wpdb->get_var("
            SELECT COUNT(DISTINCT pm.post_id)
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
            WHERE pm.meta_key = '_archi_show_in_graph'
            AND pm.meta_value = '1'
            AND p.post_status = 'publish'
        ");
    }
    
    /**
     * Render santé du système
     */
    private function render_system_health() {
        $health_checks = [
            'metadata_manager' => class_exists('Archi_Metadata_Manager'),
            'rest_api' => function_exists('archi_register_rest_routes'),
            'gutenberg' => function_exists('register_block_type'),
        ];
        
        ?>
        <ul class="archi-health-checks">
            <?php foreach ($health_checks as $check => $status): ?>
            <li class="<?php echo $status ? 'health-ok' : 'health-error'; ?>">
                <span class="health-icon"><?php echo $status ? '✓' : '✗'; ?></span>
                <?php 
                $labels = [
                    'metadata_manager' => __('Gestionnaire de métadonnées', 'archi-graph'),
                    'rest_api' => __('REST API', 'archi-graph'),
                    'gutenberg' => __('Support Gutenberg', 'archi-graph'),
                ];
                echo esc_html($labels[$check]);
                ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php
    }
    
    /**
     * AJAX: Sauvegarder les paramètres
     */
    public function ajax_save_settings() {
        check_ajax_referer('archi_admin_settings', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permissions insuffisantes', 'archi-graph')]);
        }
        
        // Traiter les données
        $settings = isset($_POST['settings']) ? $_POST['settings'] : [];
        
        foreach ($settings as $key => $value) {
            update_option($key, $value);
        }
        
        wp_send_json_success(['message' => __('Paramètres enregistrés', 'archi-graph')]);
    }
    
    /**
     * AJAX: Vider le cache
     */
    public function ajax_clear_cache() {
        check_ajax_referer('archi_admin_settings', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permissions insuffisantes', 'archi-graph')]);
        }
        
        // Supprimer les transients du graphique
        delete_transient('archi_graph_articles');
        delete_transient('archi_graph_categories');
        delete_transient('archi_graph_statistics');
        
        // Vider le cache objet si disponible
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        wp_send_json_success(['message' => __('Cache vidé avec succès', 'archi-graph')]);
    }
    
    /**
     * AJAX: Recalculer les relations
     */
    public function ajax_recalculate_relations() {
        check_ajax_referer('archi_admin_settings', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permissions insuffisantes', 'archi-graph')]);
        }
        
        // Recalculer les relations pour tous les posts
        $args = [
            'post_type' => ['post', 'archi_project', 'archi_illustration'],
            'post_status' => 'publish',
            'posts_per_page' => -1
        ];
        
        $posts = get_posts($args);
        $count = 0;
        
        foreach ($posts as $post) {
            // Utiliser la fonction de calcul automatique des relations si elle existe
            if (function_exists('archi_calculate_automatic_relationships')) {
                archi_calculate_automatic_relationships($post->ID);
                $count++;
            }
        }
        
        // Vider le cache
        delete_transient('archi_graph_articles');
        
        wp_send_json_success([
            'message' => sprintf(__('%d relations recalculées', 'archi-graph'), $count)
        ]);
    }
}

// Initialiser
function archi_init_admin_settings() {
    Archi_Admin_Settings::get_instance();
}
add_action('init', 'archi_init_admin_settings');
