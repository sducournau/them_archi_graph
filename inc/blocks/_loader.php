<?php
/**
 * Loader pour les blocs Gutenberg modulaires
 * Charge tous les blocs depuis la structure inc/blocks/
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe de chargement des blocs
 */
class Archi_Blocks_Loader {
    
    private static $instance = null;
    private $blocks_loaded = [];
    
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
        // Enregistrer les SCRIPTS en premier (priorité 5 - avant register_blocks qui est à 10)
        add_action('init', [$this, 'register_block_scripts'], 5);
        // Enregistrer les BLOCKS ensuite (priorité 10 - par défaut)
        add_action('init', [$this, 'register_blocks'], 10);
        // Enqueue des assets
        add_action('enqueue_block_assets', [$this, 'enqueue_block_assets']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);
        // BACKUP: aussi sur admin_enqueue_scripts pour l'éditeur
        add_action('admin_enqueue_scripts', [$this, 'enqueue_editor_assets_backup']);
    }
    
    /**
     * Enregistrer tous les scripts de blocs (AVANT l'enregistrement des blocs)
     */
    public function register_block_scripts() {
        // Enregistrer tous les scripts de blocs individuels
        $block_scripts = [
            'blocks-editor' => ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-editor', 'wp-components', 'wp-data', 'wp-i18n'],
            'article-manager-block' => ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-editor', 'wp-components', 'wp-data', 'wp-i18n'],
            'image-block' => ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'],
            'interactive-map' => ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'],
            'd3-bar-chart' => ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'],
            'd3-timeline' => ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'],
        ];
        
        foreach ($block_scripts as $handle => $dependencies) {
            $script_url = get_template_directory_uri() . '/dist/js/' . $handle . '.bundle.js';
            
            wp_register_script(
                'archi-' . $handle,
                $script_url,
                $dependencies,
                ARCHI_THEME_VERSION,
                false // Charger dans le head pour l'éditeur
            );
            
            // Localisation pour chaque script
            wp_localize_script('archi-' . $handle, 'archiBlocks', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'restUrl' => rest_url('archi/v1/'),
                'nonce' => wp_create_nonce('archi_blocks'),
                'restNonce' => wp_create_nonce('wp_rest')
            ]);
        }
    }
    
    /**
     * Enregistrer tous les blocs
     */
    public function register_blocks() {
        if (!function_exists('register_block_type')) {
            return;
        }
        
        // Charger les fonctions partagées
        require_once dirname(__FILE__) . '/_shared-attributes.php';
        require_once dirname(__FILE__) . '/_shared-functions.php';
        
        // Charger les blocs par catégorie
        $this->load_blocks_from_directory('graph');
        $this->load_blocks_from_directory('projects');
        $this->load_blocks_from_directory('content');
        
        // Appeler toutes les fonctions d'enregistrement
        $this->call_registration_functions();
        
        // Hook pour permettre l'extension
        do_action('archi_blocks_loaded', $this->blocks_loaded);
    }
    
    /**
     * Charger les blocs d'un dossier
     */
    private function load_blocks_from_directory($directory) {
        $blocks_dir = dirname(__FILE__) . '/' . $directory;
        
        if (!is_dir($blocks_dir)) {
            return;
        }
        
        $files = glob($blocks_dir . '/*.php');
        
        foreach ($files as $file) {
            $block_name = basename($file, '.php');
            
            // Charger le fichier
            require_once $file;
            
            $this->blocks_loaded[$directory][] = $block_name;
        }
    }
    
    /**
     * Appeler toutes les fonctions d'enregistrement après chargement des fichiers
     */
    private function call_registration_functions() {
        // Récupérer toutes les fonctions utilisateur
        $functions = get_defined_functions();
        $user_functions = $functions['user'];
        
        // Filtrer celles qui correspondent au pattern archi_register_*_block
        $registration_functions = array_filter($user_functions, function($func) {
            return preg_match('/^archi_register_.*_block$/', $func);
        });
        
        // Appeler chaque fonction d'enregistrement
        foreach ($registration_functions as $func) {
            if (function_exists($func)) {
                call_user_func($func);
            }
        }
    }
    
    /**
     * Enqueue des assets pour tous les blocs
     */
    public function enqueue_block_assets() {
        // CSS commun pour tous les blocs (frontend + editor)
        wp_enqueue_style(
            'archi-blocks',
            get_template_directory_uri() . '/assets/css/blocks.css',
            [],
            ARCHI_THEME_VERSION
        );
        
        // CSS animations
        wp_enqueue_style(
            'archi-blocks-animations',
            get_template_directory_uri() . '/assets/css/blocks-animations.css',
            ['archi-blocks'],
            ARCHI_THEME_VERSION
        );
        
        // CSS image block unifié (inclut parallax, comparison, cover, etc.)
        wp_enqueue_style(
            'archi-image-block',
            get_template_directory_uri() . '/assets/css/image-block.css',
            ['archi-blocks'],
            ARCHI_THEME_VERSION
        );
        
        // CSS visualization blocks (Map + D3)
        wp_enqueue_style(
            'archi-visualization-blocks',
            get_template_directory_uri() . '/assets/css/visualization-blocks.css',
            ['archi-blocks'],
            ARCHI_THEME_VERSION
        );
        
        // Leaflet CSS for map block (frontend ET éditeur)
        wp_enqueue_style(
            'leaflet',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
            [],
            '1.9.4'
        );
        
        // Leaflet JS for map block (frontend ET éditeur)
        wp_enqueue_script(
            'leaflet',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
            [],
            '1.9.4',
            true
        );
        
        // ✅ Parallax script loaded from functions.php (assets/js/parallax.js)
    }
    
    /**
     * Enqueue des assets pour l'éditeur uniquement
     */
    public function enqueue_editor_assets() {
        // CSS éditeur
        wp_enqueue_style(
            'archi-blocks-editor',
            get_template_directory_uri() . '/assets/css/blocks-editor.css',
            ['wp-edit-blocks'],
            ARCHI_THEME_VERSION
        );
        
        // Enqueuer TOUS les scripts de blocks pour l'éditeur
        $block_scripts = [
            'blocks-editor',
            'article-manager-block',
            'image-block',
            'interactive-map',
            'd3-bar-chart',
            'd3-timeline',
        ];
        
        foreach ($block_scripts as $handle) {
            $full_handle = 'archi-' . $handle;
            if (wp_script_is($full_handle, 'registered')) {
                wp_enqueue_script($full_handle);
            }
        }
    }
    
    /**
     * BACKUP: Enqueue sur admin_enqueue_scripts si enqueue_block_editor_assets ne marche pas
     */
    public function enqueue_editor_assets_backup($hook) {
        // Seulement dans l'éditeur de posts
        if (!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }
        
        // Enqueuer TOUS les scripts de blocks
        $block_scripts = [
            'blocks-editor',
            'article-manager-block',
            'image-block',
            'interactive-map',
            'd3-bar-chart',
            'd3-timeline',
        ];
        
        foreach ($block_scripts as $handle) {
            $full_handle = 'archi-' . $handle;
            if (wp_script_is($full_handle, 'registered')) {
                wp_enqueue_script($full_handle);
            }
        }
    }
    
    /**
     * Obtenir la liste des blocs chargés
     */
    public function get_loaded_blocks() {
        return $this->blocks_loaded;
    }
}

// Initialiser
Archi_Blocks_Loader::get_instance();
