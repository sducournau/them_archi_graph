<?php
/**
 * Modèles de blocs par défaut pour l'éditeur WYSIWYG
 * Fournit des layouts prédéfinis pour les posts, pages, projets et illustrations
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistrer les modèles de blocs par défaut pour les articles
 */
function archi_register_post_templates() {
    $post_type_object = get_post_type_object('post');
    if ($post_type_object) {
        $post_type_object->template = [
            // Bloc de gestion de l'article (métadonnées et paramètres graph)
            ['archi-graph/article-manager', [
                'showFeaturedImage' => false,
                'showTitle' => false,
                'showExcerpt' => true,
                'showAuthor' => false,
                'showDate' => true,
                'showCategories' => true,
                'showTags' => true,
                'showGraphSettings' => true,
                'layoutStyle' => 'card',
                'imagePosition' => 'top'
            ]],
            
            // Bloc d'informations de l'article (fiche identité)
            ['archi-graph/article-specs', []],
        ];
        
        $post_type_object->template_lock = false; // Permet l'ajout/suppression de blocs
    }
}
add_action('init', 'archi_register_post_templates');

/**
 * Modèle de blocs pour les projets architecturaux
 */
function archi_register_project_templates() {
    $post_type_object = get_post_type_object('archi_project');
    if ($post_type_object) {
        $post_type_object->template = [
            // Bloc de gestion du projet (métadonnées et paramètres graph)
            ['archi-graph/article-manager', [
                'showFeaturedImage' => false,
                'showTitle' => false,
                'showExcerpt' => true,
                'showAuthor' => false,
                'showDate' => true,
                'showCategories' => true,
                'showTags' => true,
                'showGraphSettings' => true,
                'showProjectDetails' => true,
                'layoutStyle' => 'card',
                'imagePosition' => 'top'
            ]],
            
            // Bloc de spécifications techniques du projet (fiche technique)
            ['archi-graph/project-specs', []],
        ];
        
        $post_type_object->template_lock = false; // Permet l'ajout/suppression de blocs
    }
}
add_action('init', 'archi_register_project_templates');

/**
 * Modèle de blocs pour les illustrations
 */
function archi_register_illustration_templates() {
    $post_type_object = get_post_type_object('archi_illustration');
    if ($post_type_object) {
        $post_type_object->template = [
            // Bloc de gestion de l'illustration (métadonnées et paramètres graph)
            ['archi-graph/article-manager', [
                'showFeaturedImage' => false,
                'showTitle' => false,
                'showExcerpt' => true,
                'showAuthor' => true,
                'showDate' => true,
                'showCategories' => true,
                'showTags' => true,
                'showGraphSettings' => true,
                'showIllustrationDetails' => true,
                'layoutStyle' => 'card',
                'imagePosition' => 'top'
            ]],
            
            // Bloc de spécifications de l'illustration (fiche technique)
            ['archi-graph/illustration-specs', []],
        ];
        
        $post_type_object->template_lock = false;
    }
}
add_action('init', 'archi_register_illustration_templates');

/**
 * Modèle de blocs pour les pages standards
 */
function archi_register_page_templates() {
    $post_type_object = get_post_type_object('page');
    if ($post_type_object) {
        // Ne pas forcer un template pour les pages (trop restrictif)
        // Mais fournir des patterns de blocs réutilisables
    }
}
add_action('init', 'archi_register_page_templates');

/**
 * Enregistrer des patterns de blocs réutilisables
 */
function archi_register_block_patterns() {
    // Pattern: Section Hero pour page
    register_block_pattern('archi-graph/page-hero', [
        'title' => __('Hero Page', 'archi-graph'),
        'description' => __('Section hero avec image de fond et titre', 'archi-graph'),
        'categories' => ['archi-graph'],
        'content' => '<!-- wp:cover {"url":"","dimRatio":50,"minHeight":400,"align":"full","className":"archi-page-hero"} -->
<div class="wp-block-cover alignfull archi-page-hero" style="min-height:400px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":1,"textColor":"white"} -->
<h1 class="has-text-align-center has-white-color has-text-color">Titre de la Page</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color">Description ou sous-titre de la page</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:cover -->'
    ]);
    
    // Pattern: Grille de projets
    register_block_pattern('archi-graph/projects-grid', [
        'title' => __('Grille de Projets', 'archi-graph'),
        'description' => __('Affiche une grille de projets architecturaux', 'archi-graph'),
        'categories' => ['archi-graph'],
        'content' => '<!-- wp:heading {"level":2} -->
<h2>Nos Projets</h2>
<!-- /wp:heading -->

<!-- wp:archi-graph/project-showcase {"layout":"grid","columns":3,"showDescription":true,"showMetadata":true} /-->'
    ]);
    
    // Pattern: Section avec colonnes de texte/image
    register_block_pattern('archi-graph/text-image-section', [
        'title' => __('Section Texte + Image', 'archi-graph'),
        'description' => __('Section avec texte à gauche et image à droite', 'archi-graph'),
        'categories' => ['archi-graph'],
        'content' => '<!-- wp:group {"className":"archi-text-image-section"} -->
<div class="wp-block-group archi-text-image-section"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":2} -->
<h2>Titre de la Section</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Votre contenu ici. Décrivez votre projet, votre approche, vos valeurs...</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link">En savoir plus</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"align":"center","sizeSlug":"large"} -->
<figure class="wp-block-image aligncenter size-large"><img src="" alt=""/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->'
    ]);
    
    // Pattern: Call to Action
    register_block_pattern('archi-graph/cta-section', [
        'title' => __('Call to Action', 'archi-graph'),
        'description' => __('Section d\'appel à l\'action avec bouton', 'archi-graph'),
        'categories' => ['archi-graph'],
        'content' => '<!-- wp:group {"backgroundColor":"accent","textColor":"white","className":"archi-cta-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group archi-cta-section has-white-color has-accent-background-color has-text-color has-background"><!-- wp:heading {"textAlign":"center","level":2} -->
<h2 class="has-text-align-center">Prêt à démarrer votre projet ?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Contactez-nous pour discuter de votre projet architectural</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"white","textColor":"accent"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-accent-color has-white-background-color has-text-color has-background">Nous Contacter</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->'
    ]);
}
add_action('init', 'archi_register_block_patterns');

/**
 * Enregistrer une catégorie pour les patterns
 */
function archi_register_pattern_categories() {
    register_block_pattern_category('archi-graph', [
        'label' => __('Architecture & Graphique', 'archi-graph')
    ]);
}
add_action('init', 'archi_register_pattern_categories');

/**
 * Personnaliser les options de l'éditeur par type de post
 */
function archi_customize_editor_settings($settings, $context) {
    // Désactiver le fullscreen par défaut pour une meilleure expérience
    $settings['initialEditingMode'] = 'visual';
    
    // Ajouter des couleurs personnalisées
    $settings['colors'] = [
        [
            'name' => __('Bleu Architecture', 'archi-graph'),
            'slug' => 'archi-blue',
            'color' => '#3498db'
        ],
        [
            'name' => __('Vert Architecture', 'archi-graph'),
            'slug' => 'archi-green',
            'color' => '#2ecc71'
        ],
        [
            'name' => __('Orange Architecture', 'archi-graph'),
            'slug' => 'archi-orange',
            'color' => '#e67e22'
        ],
        [
            'name' => __('Gris Clair', 'archi-graph'),
            'slug' => 'light-gray',
            'color' => '#ecf0f1'
        ],
        [
            'name' => __('Gris Foncé', 'archi-graph'),
            'slug' => 'dark-gray',
            'color' => '#34495e'
        ]
    ];
    
    // Tailles de police personnalisées
    $settings['fontSizes'] = [
        [
            'name' => __('Petit', 'archi-graph'),
            'slug' => 'small',
            'size' => 14
        ],
        [
            'name' => __('Normal', 'archi-graph'),
            'slug' => 'normal',
            'size' => 16
        ],
        [
            'name' => __('Moyen', 'archi-graph'),
            'slug' => 'medium',
            'size' => 18
        ],
        [
            'name' => __('Grand', 'archi-graph'),
            'slug' => 'large',
            'size' => 24
        ],
        [
            'name' => __('Très Grand', 'archi-graph'),
            'slug' => 'x-large',
            'size' => 32
        ]
    ];
    
    return $settings;
}
add_filter('block_editor_settings_all', 'archi_customize_editor_settings', 10, 2);

/**
 * Ajouter des styles CSS pour l'éditeur
 */
function archi_add_editor_styles() {
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');
}
add_action('after_setup_theme', 'archi_add_editor_styles');

/**
 * Personnaliser les blocs autorisés par type de post
 * Tous les blocs WordPress core et personnalisés sont autorisés
 */
function archi_allowed_block_types($allowed_blocks, $context) {
    // Si pas de contexte, on permet tout
    if (empty($context->post)) {
        return $allowed_blocks;
    }
    
    $post_type = $context->post->post_type;
    
    // Tous les blocs WordPress core
    $core_blocks = [
        // Texte
        'core/paragraph',
        'core/heading',
        'core/list',
        'core/quote',
        'core/code',
        'core/preformatted',
        'core/pullquote',
        'core/table',
        'core/verse',
        
        // Média
        'core/image',
        'core/gallery',
        'core/audio',
        'core/video',
        'core/file',
        'core/media-text',
        'core/cover',
        
        // Design
        'core/button',
        'core/buttons',
        'core/columns',
        'core/group',
        'core/row',
        'core/stack',
        'core/separator',
        'core/spacer',
        
        // Widgets
        'core/shortcode',
        'core/archives',
        'core/calendar',
        'core/categories',
        'core/html',
        'core/latest-comments',
        'core/latest-posts',
        'core/page-list',
        'core/rss',
        'core/search',
        'core/social-links',
        'core/social-link',
        'core/tag-cloud',
        
        // Thème
        'core/navigation',
        'core/navigation-link',
        'core/navigation-submenu',
        'core/site-logo',
        'core/site-title',
        'core/site-tagline',
        'core/query',
        'core/post-title',
        'core/post-content',
        'core/post-date',
        'core/post-excerpt',
        'core/post-featured-image',
        'core/post-terms',
        'core/post-author',
        'core/post-author-biography',
        'core/post-navigation-link',
        'core/read-more',
        
        // Embed
        'core/embed',
        'core-embed/youtube',
        'core-embed/vimeo',
        'core-embed/twitter',
        'core-embed/instagram',
        'core-embed/facebook',
        'core-embed/spotify',
        'core-embed/soundcloud',
        
        // Autres
        'core/more',
        'core/nextpage',
        'core/block',
        'core/pattern',
        'core/template-part',
    ];
    
    // Tous les blocs personnalisés du thème Archi Graph
    $archi_blocks = [
        'archi-graph/interactive-graph',          // Graphique interactif
        'archi-graph/project-showcase',           // Vitrine de projets
        'archi-graph/illustration-grid',          // Grille d'illustrations
        'archi-graph/category-filter',            // Filtre par catégorie
        'archi-graph/featured-projects',          // Projets en vedette
        'archi-graph/timeline',                   // Timeline
        'archi-graph/before-after',               // Avant/Après
        'archi-graph/technical-specs',            // Spécifications techniques
        'archi-graph/article-manager',            // Gestionnaire d'article
        'archi-graph/project-specs',              // Fiche technique projet
        'archi-graph/illustration-specs',         // Fiche technique illustration
        'archi-graph/article-specs',              // Fiche identité article
        // Blocs d'images
        'archi-graph/image-full-width',           // Image pleine largeur
        'archi-graph/images-columns',             // Images en colonnes
        'archi-graph/image-portrait',             // Image portrait
        'archi-graph/image-block',                // Bloc image unifié (standard, parallax, zoom, comparison, cover)
        'archi-graph/hero-cover',                 // Hero cover pleine page pour en-tête d'article
        // Blocs de défilement/parallax
        'archi-graph/fixed-background',           // Image défilement fixe
        'archi-graph/sticky-scroll',              // Section scroll collant
        // Blocs de visualisation
        'archi-graph/interactive-map',            // Carte interactive
        'archi-graph/d3-bar-chart',               // Graphique en barres D3
        'archi-graph/d3-timeline',                // Timeline D3
    ];
    
    // Tous les blocs disponibles
    $all_blocks = array_merge($core_blocks, $archi_blocks);
    
    switch ($post_type) {
        case 'archi_project':
        case 'archi_illustration':
        case 'post':
            // Articles, projets et illustrations : tous les blocs disponibles
            return $all_blocks;
            
        case 'page':
            // Les pages ont accès à tous les blocs (comportement par défaut)
            return true;
            
        default:
            return $allowed_blocks;
    }
}
add_filter('allowed_block_types_all', 'archi_allowed_block_types', 10, 2);

/**
 * Ajouter des instructions dans l'éditeur
 */
function archi_add_editor_notices() {
    $screen = get_current_screen();
    
    if (!$screen || $screen->base !== 'post') {
        return;
    }
    
    $post_type = $screen->post_type;
    $messages = [];
    
    switch ($post_type) {
        case 'archi_project':
            $messages[] = __('💡 Astuce : Utilisez le bloc "Détails Techniques" pour présenter les spécifications de votre projet.', 'archi-graph');
            $messages[] = __('📸 N\'oubliez pas d\'ajouter une galerie d\'images pour montrer l\'évolution du projet.', 'archi-graph');
            break;
            
        case 'archi_illustration':
            $messages[] = __('🎨 Astuce : Montrez votre processus créatif en ajoutant des images des différentes étapes.', 'archi-graph');
            $messages[] = __('🔗 Liez cette illustration à un projet pour créer une connexion dans le graphique.', 'archi-graph');
            break;
            
        case 'post':
            $messages[] = __('📝 Astuce : Structurez votre article avec des titres (H2, H3) pour faciliter la lecture.', 'archi-graph');
            $messages[] = __('🏷️ N\'oubliez pas d\'assigner des catégories et tags pour améliorer la navigation.', 'archi-graph');
            break;
    }
    
    if (!empty($messages)) {
        echo '<div class="notice notice-info is-dismissible archi-editor-notice">';
        echo '<p><strong>' . __('Conseils d\'édition', 'archi-graph') . '</strong></p>';
        echo '<ul>';
        foreach ($messages as $message) {
            echo '<li>' . esc_html($message) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
}
add_action('admin_notices', 'archi_add_editor_notices');

