<?php
/**
 * Archi Graph Theme Functions
 * 
 * Configuration principale du thème avec support React/D3.js
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

// Constantes du thème
define('ARCHI_THEME_VERSION', '1.0.5');
define('ARCHI_THEME_DIR', get_template_directory());
define('ARCHI_THEME_URI', get_template_directory_uri());

/**
 * Configuration initiale du thème
 */
function archi_theme_setup() {
    // Support des images à la une
    add_theme_support('post-thumbnails');
    
    // Support des menus
    add_theme_support('menus');
    
    // Support du titre automatique
    add_theme_support('title-tag');
    
    // Support du logo personnalisé
    add_theme_support('custom-logo', [
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    
    // Support de l'image d'en-tête personnalisée
    add_theme_support('custom-header', [
        'width'         => 1920,
        'height'        => 400,
        'flex-height'   => true,
        'flex-width'    => true,
        'header-text'   => false,
        'uploads'       => true,
    ]);
    
    // Support du fond personnalisé
    add_theme_support('custom-background', [
        'default-color' => 'ffffff',
        'default-image' => '',
    ]);
    
    // Support HTML5
    add_theme_support('html5', [
        'search-form',
        'comment-form', 
        'comment-list',
        'gallery',
        'caption',
        'navigation-widgets'
    ]);
    
    // Support de l'éditeur de blocs
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    
    // Charger les styles pour l'éditeur Gutenberg
    add_editor_style('assets/css/editor-style.css');
    add_editor_style('assets/css/centered-content.css');
    
    // Tailles d'images personnalisées
    add_image_size('graph-node', 80, 80, true);
    add_image_size('graph-node-large', 120, 120, true);
    add_image_size('header-banner', 1920, 400, true);
    
    // Enregistrement des menus
    register_nav_menus([
        'primary' => __('Menu Principal', 'archi-graph'),
        'secondary' => __('Menu Secondaire (optionnel)', 'archi-graph'),
        'footer' => __('Menu Pied de page', 'archi-graph')
    ]);
}
add_action('after_setup_theme', 'archi_theme_setup');

/**
 * Enregistrement de la catégorie de blocs personnalisée
 */
function archi_register_block_category($categories) {
    return array_merge(
        $categories,
        [
            [
                'slug'  => 'archi-graph',
                'title' => __('Archi Graph', 'archi-graph'),
                'icon'  => 'admin-home',
            ],
        ]
    );
}
add_filter('block_categories_all', 'archi_register_block_category', 10, 1);

/**
 * Enregistrement des scripts et styles
 */
function archi_enqueue_scripts() {
    // Critical CSS fixes (load first with highest priority)
    wp_enqueue_style(
        'archi-fixes',
        ARCHI_THEME_URI . '/assets/css/fixes.css',
        [],
        ARCHI_THEME_VERSION . '-fix',
        'all'
    );
    
    // Style de l'en-tête (partout)
    wp_enqueue_style(
        'archi-header-style',
        ARCHI_THEME_URI . '/assets/css/header.css',
        ['archi-fixes'],
        ARCHI_THEME_VERSION
    );
    
    // Featured image header styles (for single posts/projects/illustrations)
    if (is_single()) {
        wp_enqueue_style(
            'archi-featured-image-header',
            ARCHI_THEME_URI . '/assets/css/featured-image-header.css',
            ['archi-fixes'],
            ARCHI_THEME_VERSION
        );
    }
    
    // Style simple et moderne pour les articles
    wp_enqueue_style(
        'archi-simple-style',
        ARCHI_THEME_URI . '/assets/css/simple-style.css',
        [],
        ARCHI_THEME_VERSION
    );
    
    // Style du footer (partout)
    wp_enqueue_style(
        'archi-footer-style',
        ARCHI_THEME_URI . '/assets/css/footer.css',
        [],
        ARCHI_THEME_VERSION
    );
    
    // Article card styles (globally available)
    wp_enqueue_style(
        'archi-article-card',
        ARCHI_THEME_URI . '/assets/css/article-card.css',
        [],
        ARCHI_THEME_VERSION
    );
    
    // Centered content style for articles and projects
    wp_enqueue_style(
        'archi-centered-content',
        ARCHI_THEME_URI . '/assets/css/centered-content.css',
        [],
        ARCHI_THEME_VERSION
    );
    
    // Simplified templates styles (related articles, specs grid, simple headers)
    wp_enqueue_style(
        'archi-simplified-templates',
        ARCHI_THEME_URI . '/assets/css/simplified-templates.css',
        [],
        ARCHI_THEME_VERSION
    );
    
    // Cover block styles (WordPress standard cover block classes)
    wp_enqueue_style(
        'archi-cover-block',
        ARCHI_THEME_URI . '/assets/css/cover-block.css',
        [],
        ARCHI_THEME_VERSION
    );
    
    // Page template styles
    if (is_page()) {
        wp_enqueue_style(
            'archi-page-style',
            ARCHI_THEME_URI . '/assets/css/page.css',
            [],
            ARCHI_THEME_VERSION
        );
    }
    
    // Single project/illustration template styles
    if (is_singular(['archi_project', 'archi_illustration'])) {
        wp_enqueue_style(
            'archi-single-project',
            ARCHI_THEME_URI . '/assets/css/single-project.css',
            [],
            ARCHI_THEME_VERSION
        );
    }
    
    // JavaScript pour le menu mobile
    wp_enqueue_script(
        'archi-navigation',
        ARCHI_THEME_URI . '/assets/js/navigation.js',
        [],
        ARCHI_THEME_VERSION,
        true
    );
    
    // ✅ Parallax scroll with GPU acceleration
    wp_enqueue_script(
        'archi-parallax',
        ARCHI_THEME_URI . '/assets/js/parallax.js',
        [],
        ARCHI_THEME_VERSION,
        true
    );
    
    // Consolidated parallax CSS for blocks
    wp_enqueue_style(
        'archi-parallax-image',
        ARCHI_THEME_URI . '/assets/css/parallax-image.css',
        [],
        ARCHI_THEME_VERSION
    );
    
    // Comparison slider JavaScript
    wp_enqueue_script(
        'archi-comparison-slider',
        ARCHI_THEME_URI . '/assets/js/comparison-slider.js',
        [],
        ARCHI_THEME_VERSION,
        true
    );
    
    // Localize customizer settings for frontend use
    $customizer_settings = [
        'animationDuration' => get_theme_mod('archi_header_animation_duration', 0.3),
        'animationType' => get_theme_mod('archi_header_animation_type', 'ease-in-out'),
        'graphAnimationDuration' => get_theme_mod('archi_graph_animation_duration', 1000),
        'primaryColor' => get_theme_mod('archi_primary_color', '#3498db'),
        'secondaryColor' => get_theme_mod('archi_secondary_color', '#2c3e50'),
    ];
    
    wp_localize_script(
        'archi-parallax',
        'archiCustomizerSettings',
        $customizer_settings
    );
    
    wp_localize_script(
        'archi-comparison-slider',
        'archiCustomizerSettings',
        $customizer_settings
    );
    
    // Hero Fullscreen avec scroll indicator (pour singles uniquement)
    if (is_singular(['post', 'archi_project', 'archi_illustration'])) {
        wp_enqueue_style(
            'archi-hero-fullscreen',
            ARCHI_THEME_URI . '/assets/css/hero-fullscreen-scroll.css',
            [],
            ARCHI_THEME_VERSION
        );
        
        // Fix CSS pour le hero fullscreen
        wp_enqueue_style(
            'archi-hero-fullscreen-fix',
            ARCHI_THEME_URI . '/assets/css/hero-fullscreen-fix.css',
            ['archi-hero-fullscreen'],
            ARCHI_THEME_VERSION
        );
        
        // Parallax effects pour les images featured
        wp_enqueue_script(
            'archi-featured-image-parallax',
            ARCHI_THEME_URI . '/assets/js/featured-image-parallax.js',
            [],
            ARCHI_THEME_VERSION,
            true
        );
        
        wp_enqueue_script(
            'archi-hero-fullscreen-scroll',
            ARCHI_THEME_URI . '/assets/js/hero-fullscreen-scroll.js',
            ['jquery'],
            ARCHI_THEME_VERSION,
            true
        );
    }
    
    // Charger les scripts React/D3 uniquement sur la page d'accueil
    if (is_front_page()) {
        // Note: CSS is bundled in app.bundle.js via webpack style-loader
        
        // Style graphique avec fond blanc
        wp_enqueue_style(
            'archi-graph-white',
            ARCHI_THEME_URI . '/assets/css/graph-white.css',
            [],
            ARCHI_THEME_VERSION
        );
        
        // Style des îles architecturales organiques
        wp_enqueue_style(
            'archi-organic-islands',
            ARCHI_THEME_URI . '/assets/css/organic-islands.css',
            ['archi-graph-white'],
            ARCHI_THEME_VERSION
        );
        
        // CSS d'urgence pour forcer la visibilité du graphe
        wp_enqueue_style(
            'archi-graph-force-visible',
            ARCHI_THEME_URI . '/assets/css/graph-force-visible.css',
            ['archi-organic-islands'],
            ARCHI_THEME_VERSION . '-force'
        );
        
        // Arrow satellites - GIFs animés autour des nodes
        wp_enqueue_style(
            'archi-arrow-satellites',
            ARCHI_THEME_URI . '/assets/css/arrow-satellites.css',
            ['archi-graph-force-visible'],
            ARCHI_THEME_VERSION
        );
        
        // Home page improvements
        wp_enqueue_style(
            'archi-home-improvements',
            ARCHI_THEME_URI . '/assets/css/home-improvements.css',
            ['archi-graph-white'],
            ARCHI_THEME_VERSION
        );
        
        wp_enqueue_script(
            'archi-vendors',
            ARCHI_THEME_URI . '/dist/js/vendors.bundle.js',
            [],
            ARCHI_THEME_VERSION,
            true
        );
        
        wp_enqueue_script(
            'archi-app',
            ARCHI_THEME_URI . '/dist/js/app.bundle.js',
            ['archi-vendors'],
            ARCHI_THEME_VERSION,
            true
        );
        
        // ✅ NEW: Comments node generator utility (must load before app)
        wp_enqueue_script(
            'archi-comments-nodes',
            ARCHI_THEME_URI . '/assets/js/utils/commentsNodeGenerator.js',
            [],
            ARCHI_THEME_VERSION,
            true
        );
        
        // Home page enhancements script
        wp_enqueue_script(
            'archi-home-enhancements',
            ARCHI_THEME_URI . '/assets/js/home-enhancements.js',
            ['archi-app'],
            ARCHI_THEME_VERSION,
            true
        );
    }
    
    // Guestbook styles
    if (is_page_template('page-guestbook.php') || is_singular('archi_guestbook')) {
        wp_enqueue_style(
            'archi-guestbook',
            ARCHI_THEME_URI . '/assets/css/guestbook.css',
            [],
            ARCHI_THEME_VERSION
        );
    }
    
    // Variables pour JavaScript (uniquement sur la page d'accueil où archi-app est chargé)
    if (is_front_page()) {
        wp_localize_script('archi-app', 'archiGraph', [
            'apiUrl' => home_url('/wp-json/archi/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'themeUrl' => ARCHI_THEME_URI,
            'ajaxUrl' => admin_url('admin-ajax.php')
        ]);
        
        // Éditeur de graphique pour les administrateurs
        if (current_user_can('edit_posts')) {
            wp_enqueue_style(
                'archi-graph-editor',
                ARCHI_THEME_URI . '/assets/css/graph-editor.css',
                [],
                ARCHI_THEME_VERSION
            );
            
            wp_enqueue_script(
                'archi-graph-editor',
                ARCHI_THEME_URI . '/assets/js/graph-editor.js',
                ['archi-app'],
                ARCHI_THEME_VERSION,
                true
            );
            
            // Charger la bibliothèque média WordPress pour le sélecteur d'images
            wp_enqueue_media();
        }
    }
}
add_action('wp_enqueue_scripts', 'archi_enqueue_scripts');

/**
 * Scripts admin
 */
function archi_admin_scripts($hook) {
    if ('post.php' === $hook || 'post-new.php' === $hook) {
        wp_enqueue_script(
            'archi-admin',
            ARCHI_THEME_URI . '/dist/js/admin.bundle.js',
            ['jquery'],
            ARCHI_THEME_VERSION,
            true
        );
    }
    
    // Media uploader pour la page des paramètres
    if ('appearance_page_archi-graph-settings' === $hook) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'archi_admin_scripts');

/**
 * Charger les styles pour l'éditeur de blocs Gutenberg
 */
function archi_enqueue_block_editor_assets() {
    // Consolidated editor styles for blocks preview
    wp_enqueue_style(
        'archi-blocks-editor',
        ARCHI_THEME_URI . '/assets/css/blocks-editor.css',
        [],
        ARCHI_THEME_VERSION
    );
}
add_action('enqueue_block_editor_assets', 'archi_enqueue_block_editor_assets');

/**
 * Inclusion des fichiers du thème
 */

// Gestionnaire centralisé de métadonnées
require_once ARCHI_THEME_DIR . '/inc/metadata-manager.php';

// Interface administration
require_once ARCHI_THEME_DIR . '/inc/admin-settings.php';

// Customizer API
require_once ARCHI_THEME_DIR . '/inc/customizer.php';

// Custom post types et taxonomies
require_once ARCHI_THEME_DIR . '/inc/custom-post-types.php';

// Calcul de proximité et relations
require_once ARCHI_THEME_DIR . '/inc/proximity-calculator.php';
require_once ARCHI_THEME_DIR . '/inc/automatic-relationships.php';

// REST API
require_once ARCHI_THEME_DIR . '/inc/rest-api.php';
require_once ARCHI_THEME_DIR . '/inc/graph-editor-api.php';

// Graph Meta Registration
require_once ARCHI_THEME_DIR . '/inc/graph-meta-registry.php';

// Graph UI and functionality
require_once ARCHI_THEME_DIR . '/inc/meta-boxes.php';
require_once ARCHI_THEME_DIR . '/inc/advanced-graph-migration.php';
require_once ARCHI_THEME_DIR . '/inc/graph-management.php';

// Blocs Gutenberg - Système modulaire de blocs
require_once ARCHI_THEME_DIR . '/inc/blocks/_loader.php';

// Fonctionnalités avancées
require_once ARCHI_THEME_DIR . '/inc/article-card-component.php';
require_once ARCHI_THEME_DIR . '/inc/admin-enhancements.php';
require_once ARCHI_THEME_DIR . '/inc/block-templates.php';
require_once ARCHI_THEME_DIR . '/inc/sample-data-generator.php';

// Animations et polygones de catégories
require_once ARCHI_THEME_DIR . '/inc/category-polygon-colors.php';

/**
 * Activation du thème - création des options par défaut
 */
function archi_theme_activation() {
    // Options par défaut
    $default_options = [
        'graph_animation_duration' => 1000,
        'graph_node_spacing' => 100,
        'graph_cluster_strength' => 0.1,
        'graph_show_categories' => true,
        'graph_show_links' => true,
        'graph_auto_save_positions' => false,
        'archi_site_description' => get_bloginfo('description'),
        'archi_show_social_links' => true,
        'archi_footer_text' => '',
        'archi_og_image' => '',
        'archi_twitter_card' => 'summary_large_image'
    ];
    
    foreach ($default_options as $option => $value) {
        if (!get_option($option)) {
            add_option($option, $value);
        }
    }
}
add_action('after_switch_theme', 'archi_theme_activation');

/**
 * Nettoyage lors de la désactivation
 */
function archi_theme_deactivation() {
    // Nettoyer le cache si nécessaire
    wp_cache_flush();
}
add_action('switch_theme', 'archi_theme_deactivation');

/**
 * Fonction helper pour récupérer les options du thème
 */
/**
 * Helper function to get theme option
 * Gère correctement les valeurs booléennes
 */
function archi_get_option($option_name, $default = '') {
    $value = get_option($option_name, $default);
    
    // Convertir les valeurs numériques en booléens si nécessaire
    if ($value === '0' || $value === 0) {
        return false;
    }
    
    if ($value === '1' || $value === 1) {
        return true;
    }
    
    return $value;
}

/**
 * Support des widgets
 */
function archi_widgets_init() {
    register_sidebar([
        'name' => __('Sidebar Principal', 'archi-graph'),
        'id' => 'sidebar-1',
        'description' => __('Widgets pour la sidebar principale', 'archi-graph'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ]);
    
    register_sidebar([
        'name' => __('Footer', 'archi-graph'),
        'id' => 'footer-1',
        'description' => __('Widgets pour le pied de page', 'archi-graph'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>'
    ]);
}
add_action('widgets_init', 'archi_widgets_init');

/**
 * Personnalisation de l'extrait
 */
function archi_custom_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'archi_custom_excerpt_length');

function archi_custom_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'archi_custom_excerpt_more');

/**
 * Ajouter une page de diagnostic dans l'admin
 */
function archi_add_diagnostic_menu() {
    add_theme_page(
        __('Diagnostic Graphique', 'archi-graph'),
        __('🔍 Diagnostic', 'archi-graph'),
        'manage_options',
        'archi-diagnostic',
        'archi_diagnostic_page'
    );
}
add_action('admin_menu', 'archi_add_diagnostic_menu');

function archi_diagnostic_page() {
    echo '<div class="wrap">';
    echo '<h1>Diagnostic du Graphique Archi</h1>';
    archi_diagnostic_graph();
    echo '</div>';
}

/**
 * Ajout de classes CSS personnalisées au body
 */
function archi_body_classes($classes) {
    if (is_front_page()) {
        $classes[] = 'graph-homepage';
    }
    
    if (is_single()) {
        $classes[] = 'single-article';
        
        // Ajouter les classes des catégories
        $categories = get_the_category();
        foreach ($categories as $category) {
            $classes[] = 'category-' . $category->slug;
        }
    }
    
    return $classes;
}
add_filter('body_class', 'archi_body_classes');

/**
 * Optimisation des performances
 */
function archi_performance_optimizations() {
    // Supprimer les emoji scripts si pas nécessaires
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    
    // Nettoyer wp_head
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
}
add_action('init', 'archi_performance_optimizations');

/**
 * Render custom meta tags for SEO and social media
 */
function archi_render_meta_tags() {
    // Site description
    $description = get_option('archi_site_description', get_bloginfo('description'));
    
    // Page-specific description
    if (is_single() || is_page()) {
        $post_excerpt = get_the_excerpt();
        if ($post_excerpt) {
            $description = wp_trim_words($post_excerpt, 30);
        }
    }
    
    // Page title
    $title = wp_get_document_title();
    
    // URL
    $url = get_permalink();
    if (is_front_page()) {
        $url = home_url('/');
    }
    
    // Image
    $image = get_option('archi_og_image', '');
    if (is_single() && has_post_thumbnail()) {
        $image = get_the_post_thumbnail_url(get_the_ID(), 'large');
    }
    
    // Favicon
    $favicon = get_option('archi_favicon', '');
    ?>
    
    <!-- Meta Description -->
    <meta name="description" content="<?php echo esc_attr($description); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo is_single() ? 'article' : 'website'; ?>">
    <meta property="og:url" content="<?php echo esc_url($url); ?>">
    <meta property="og:title" content="<?php echo esc_attr($title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($description); ?>">
    <?php if ($image) : ?>
    <meta property="og:image" content="<?php echo esc_url($image); ?>">
    <?php endif; ?>
    <meta property="og:site_name" content="<?php bloginfo('name'); ?>">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="<?php echo esc_attr(get_option('archi_twitter_card', 'summary_large_image')); ?>">
    <meta name="twitter:url" content="<?php echo esc_url($url); ?>">
    <meta name="twitter:title" content="<?php echo esc_attr($title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($description); ?>">
    <?php if ($image) : ?>
    <meta name="twitter:image" content="<?php echo esc_url($image); ?>">
    <?php endif; ?>
    <?php 
    $twitter_handle = get_option('archi_twitter_handle', '');
    if ($twitter_handle) : 
    ?>
    <meta name="twitter:site" content="@<?php echo esc_attr(str_replace('@', '', $twitter_handle)); ?>">
    <meta name="twitter:creator" content="@<?php echo esc_attr(str_replace('@', '', $twitter_handle)); ?>">
    <?php endif; ?>
    
    <!-- Favicon -->
    <?php if ($favicon) : ?>
    <link rel="icon" type="image/png" href="<?php echo esc_url($favicon); ?>">
    <link rel="apple-touch-icon" href="<?php echo esc_url($favicon); ?>">
    <?php endif; ?>
    
    <!-- Theme Color -->
    <meta name="theme-color" content="<?php echo esc_attr(get_option('archi_theme_color', '#667eea')); ?>">
    
    <?php
}

/**
 * Render social media links
 */
function archi_render_social_links() {
    $social_links = [
        'facebook' => get_option('archi_social_facebook', ''),
        'twitter' => get_option('archi_social_twitter', ''),
        'linkedin' => get_option('archi_social_linkedin', ''),
        'instagram' => get_option('archi_social_instagram', ''),
        'github' => get_option('archi_social_github', ''),
        'youtube' => get_option('archi_social_youtube', '')
    ];
    
    $icons = [
        'facebook' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        'twitter' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>',
        'linkedin' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
        'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
        'github' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>',
        'youtube' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>'
    ];
    
    if (array_filter($social_links)) {
        echo '<ul class="social-links">';
        foreach ($social_links as $platform => $url) {
            if (!empty($url)) {
                echo '<li class="social-link-item social-' . esc_attr($platform) . '">';
                echo '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr(ucfirst($platform)) . '">';
                echo $icons[$platform];
                echo '</a>';
                echo '</li>';
            }
        }
        echo '</ul>';
    }
}

/**
 * Override custom background color to ensure white background
 * This fixes any accidental custom background color settings
 */
function archi_override_custom_background() {
    // Remove any custom background colors that may have been set accidentally
    remove_theme_support('custom-background');
    
    // Re-add with forced white background
    add_theme_support('custom-background', [
        'default-color' => 'ffffff',
        'default-image' => '',
    ]);
}
add_action('after_setup_theme', 'archi_override_custom_background', 20);

/**
 * Force white background via inline CSS
 * This ensures the background is always white regardless of database settings
 */
function archi_force_white_background() {
    echo '<style id="archi-force-white-bg">
        body, 
        body.page,
        body.page-template,
        body.page-template-default,
        #page-wrapper,
        .site-content,
        #primary,
        .page-container {
            background-color: #ffffff !important;
            background-image: none !important;
        }
    </style>';
}
add_action('wp_head', 'archi_force_white_background', 999);

?>