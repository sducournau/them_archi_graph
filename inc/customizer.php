<?php
/**
 * WordPress Customizer Integration
 * 
 * Adds theme customization options via WordPress Customizer API
 * with live preview support for real-time changes.
 * 
 * @package Archi_Graph
 * @since 1.2.0
 */

/**
 * Register Customizer settings and controls
 */
function archi_customize_register($wp_customize) {
    
    // ========================================
    // SECTION 1: COULEURS GÉNÉRALES
    // ========================================
    $wp_customize->add_section('archi_colors', [
        'title' => __('🎨 Couleurs du Site', 'archi-graph'),
        'description' => __('Personnalisez les couleurs principales de votre site. Ces couleurs s\'appliquent automatiquement aux liens, boutons et éléments interactifs.', 'archi-graph'),
        'priority' => 20,
    ]);
    
    // Couleur principale (utilisée partout : liens, boutons, menu hover)
    $wp_customize->add_setting('archi_primary_color', [
        'default' => '#3498db',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color'
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_primary_color', [
        'label' => __('Couleur Principale', 'archi-graph'),
        'description' => __('Utilisée pour les liens, boutons, et éléments actifs du menu.', 'archi-graph'),
        'section' => 'archi_colors'
    ]));
    
    // Couleur secondaire (titres)
    $wp_customize->add_setting('archi_secondary_color', [
        'default' => '#2c3e50',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color'
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_secondary_color', [
        'label' => __('Couleur Secondaire', 'archi-graph'),
        'description' => __('Utilisée pour les titres et textes importants.', 'archi-graph'),
        'section' => 'archi_colors'
    ]));
    
    // Couleur de fond du header/menu
    $wp_customize->add_setting('archi_header_bg_color', [
        'default' => '#ffffff',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color'
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_header_bg_color', [
        'label' => __('Fond du Header', 'archi-graph'),
        'description' => __('Couleur de fond du menu de navigation.', 'archi-graph'),
        'section' => 'archi_colors'
    ]));
    
    // Couleur du texte du menu
    $wp_customize->add_setting('archi_header_text_color', [
        'default' => '#2c3e50',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color'
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_header_text_color', [
        'label' => __('Texte du Header', 'archi-graph'),
        'description' => __('Couleur du texte dans le menu de navigation.', 'archi-graph'),
        'section' => 'archi_colors'
    ]));
    
    // ========================================
    // SECTION 2: NAVIGATION
    // ========================================
    $wp_customize->add_section('archi_navigation_options', [
        'title' => __('🧭 Navigation & Menu', 'archi-graph'),
        'description' => __('Options d\'affichage et de comportement du menu de navigation.', 'archi-graph'),
        'priority' => 25,
    ]);
    
    // Header sticky
    $wp_customize->add_setting('archi_header_sticky', [
        'default' => true,
        'transport' => 'refresh',
        'sanitize_callback' => 'archi_sanitize_checkbox'
    ]);
    
    $wp_customize->add_control('archi_header_sticky', [
        'label' => __('Header fixe', 'archi-graph'),
        'description' => __('Le header reste visible quand vous scrollez la page.', 'archi-graph'),
        'section' => 'archi_navigation_options',
        'type' => 'checkbox'
    ]);
    
    // Header transparent (homepage uniquement)
    $wp_customize->add_setting('archi_header_transparent', [
        'default' => false,
        'transport' => 'refresh',
        'sanitize_callback' => 'archi_sanitize_checkbox'
    ]);
    
    $wp_customize->add_control('archi_header_transparent', [
        'label' => __('Header transparent', 'archi-graph'),
        'description' => __('Rend le header transparent sur la page d\'accueil uniquement.', 'archi-graph'),
        'section' => 'archi_navigation_options',
        'type' => 'checkbox'
    ]);
    
    // Afficher recherche
    $wp_customize->add_setting('archi_menu_show_search', [
        'default' => false,
        'transport' => 'refresh',
        'sanitize_callback' => 'archi_sanitize_checkbox'
    ]);
    
    $wp_customize->add_control('archi_menu_show_search', [
        'label' => __('Bouton de recherche', 'archi-graph'),
        'description' => __('Afficher un bouton de recherche dans le menu.', 'archi-graph'),
        'section' => 'archi_navigation_options',
        'type' => 'checkbox'
    ]);
    
    // Hauteur du header
    $wp_customize->add_setting('archi_header_height', [
        'default' => 'normal',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    
    $wp_customize->add_control('archi_header_height', [
        'label' => __('Hauteur du header', 'archi-graph'),
        'description' => __('Choisissez la hauteur du bandeau de navigation.', 'archi-graph'),
        'section' => 'archi_navigation_options',
        'type' => 'select',
        'choices' => [
            'compact' => __('Compact (60px)', 'archi-graph'),
            'normal' => __('Normal (80px)', 'archi-graph'),
            'large' => __('Large (100px)', 'archi-graph'),
            'extra-large' => __('Extra Large (120px)', 'archi-graph')
        ]
    ]);
    
    // Ombre du header
    $wp_customize->add_setting('archi_header_shadow', [
        'default' => 'light',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    
    $wp_customize->add_control('archi_header_shadow', [
        'label' => __('Ombre portée', 'archi-graph'),
        'description' => __('Intensité de l\'ombre sous le header.', 'archi-graph'),
        'section' => 'archi_navigation_options',
        'type' => 'select',
        'choices' => [
            'none' => __('Aucune', 'archi-graph'),
            'light' => __('Légère', 'archi-graph'),
            'medium' => __('Moyenne', 'archi-graph'),
            'strong' => __('Forte', 'archi-graph')
        ]
    ]);
    
    // Opacité du header transparent au scroll
    $wp_customize->add_setting('archi_header_scroll_opacity', [
        'default' => 0.95,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_header_scroll_opacity', [
        'label' => __('Opacité au scroll', 'archi-graph'),
        'description' => __('Transparence du header transparent après le scroll (0 = invisible, 1 = opaque).', 'archi-graph'),
        'section' => 'archi_navigation_options',
        'type' => 'range',
        'input_attrs' => [
            'min' => 0.5,
            'max' => 1,
            'step' => 0.05
        ]
    ]);
    
    // Position du logo/titre
    $wp_customize->add_setting('archi_header_logo_position', [
        'default' => 'left',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    
    $wp_customize->add_control('archi_header_logo_position', [
        'label' => __('Position du logo', 'archi-graph'),
        'description' => __('Alignement du logo/titre du site dans le header.', 'archi-graph'),
        'section' => 'archi_navigation_options',
        'type' => 'select',
        'choices' => [
            'left' => __('Gauche', 'archi-graph'),
            'center' => __('Centre', 'archi-graph'),
            'right' => __('Droite', 'archi-graph')
        ]
    ]);
    
    // Comportement sticky (show/hide on scroll)
    $wp_customize->add_setting('archi_header_sticky_behavior', [
        'default' => 'always',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    
    $wp_customize->add_control('archi_header_sticky_behavior', [
        'label' => __('Comportement au scroll', 'archi-graph'),
        'description' => __('Comment le header fixe réagit au scroll.', 'archi-graph'),
        'section' => 'archi_navigation_options',
        'type' => 'select',
        'choices' => [
            'always' => __('Toujours visible', 'archi-graph'),
            'hide-on-scroll-down' => __('Se cache en scrollant vers le bas', 'archi-graph'),
            'show-on-scroll-up' => __('Apparaît en scrollant vers le haut', 'archi-graph')
        ]
    ]);
    
    // ========================================
    // SECTION 3: GRAPHIQUE D3.JS
    // ========================================
    $wp_customize->add_section('archi_graph_options', [
        'title' => __('🔗 Graphique D3.js', 'archi-graph'),
        'description' => __('Personnalisez l\'apparence du graphique de relations sur la page d\'accueil.', 'archi-graph'),
        'priority' => 30,
    ]);
    
    // Couleur nœud par défaut
    $wp_customize->add_setting('archi_default_node_color', [
        'default' => '#3498db',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color'
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_default_node_color', [
        'label' => __('Couleur des nœuds', 'archi-graph'),
        'description' => __('Couleur par défaut pour les nœuds sans couleur personnalisée.', 'archi-graph'),
        'section' => 'archi_graph_options'
    ]));
    
    // Taille nœud par défaut
    $wp_customize->add_setting('archi_default_node_size', [
        'default' => 80,
        'transport' => 'refresh',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_default_node_size', [
        'label' => __('Taille des nœuds', 'archi-graph'),
        'description' => __('Taille par défaut en pixels (40-120).', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'range',
        'input_attrs' => [
            'min' => 40,
            'max' => 120,
            'step' => 5
        ]
    ]);
    
    // Force de clustering
    $wp_customize->add_setting('archi_cluster_strength', [
        'default' => 0.1,
        'transport' => 'refresh',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_cluster_strength', [
        'label' => __('Regroupement par catégorie', 'archi-graph'),
        'description' => __('Intensité du regroupement des articles similaires (0 = aucun, 0.5 = fort).', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'range',
        'input_attrs' => [
            'min' => 0,
            'max' => 0.5,
            'step' => 0.05
        ]
    ]);
    
    // Option : Afficher seulement le titre dans la popup
    $wp_customize->add_setting('archi_graph_popup_title_only', [
        'default' => false,
        'transport' => 'refresh',
        'sanitize_callback' => 'archi_sanitize_checkbox'
    ]);
    
    $wp_customize->add_control('archi_graph_popup_title_only', [
        'label' => __('Popup : titre uniquement', 'archi-graph'),
        'description' => __('Afficher seulement le titre dans la popup de survol (sans l\'extrait).', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'checkbox'
    ]);
    
    // Option : Afficher les commentaires dans la sidebar
    $wp_customize->add_setting('archi_graph_show_comments', [
        'default' => true,
        'transport' => 'refresh',
        'sanitize_callback' => 'archi_sanitize_checkbox'
    ]);
    
    $wp_customize->add_control('archi_graph_show_comments', [
        'label' => __('Afficher les commentaires', 'archi-graph'),
        'description' => __('Afficher les commentaires de l\'article dans la sidebar d\'information.', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'checkbox'
    ]);
    
    // --- EFFETS ET ANIMATIONS ---
    
    // Mode d'animation d'entrée
    $wp_customize->add_setting('archi_graph_animation_mode', [
        'default' => 'fade-in',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    
    $wp_customize->add_control('archi_graph_animation_mode', [
        'label' => __('Animation d\'entrée', 'archi-graph'),
        'description' => __('Effet d\'apparition des nœuds au chargement.', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'select',
        'choices' => [
            'none' => __('Aucune', 'archi-graph'),
            'fade-in' => __('Fondu progressif', 'archi-graph'),
            'scale-up' => __('Zoom progressif', 'archi-graph'),
            'slide-in' => __('Glissement', 'archi-graph'),
            'bounce' => __('Rebond', 'archi-graph')
        ]
    ]);
    
    // Vitesse des transitions
    $wp_customize->add_setting('archi_graph_transition_speed', [
        'default' => 500,
        'transport' => 'refresh',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_graph_transition_speed', [
        'label' => __('Vitesse des transitions', 'archi-graph'),
        'description' => __('Durée des animations en millisecondes (200-2000).', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'range',
        'input_attrs' => [
            'min' => 200,
            'max' => 2000,
            'step' => 100
        ]
    ]);
    
    // Effet de survol des nœuds
    $wp_customize->add_setting('archi_graph_hover_effect', [
        'default' => 'highlight',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    
    $wp_customize->add_control('archi_graph_hover_effect', [
        'label' => __('Effet de survol', 'archi-graph'),
        'description' => __('Réaction visuelle au passage de la souris.', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'select',
        'choices' => [
            'none' => __('Aucun', 'archi-graph'),
            'highlight' => __('Mise en surbrillance', 'archi-graph'),
            'scale' => __('Agrandissement', 'archi-graph'),
            'glow' => __('Halo lumineux', 'archi-graph'),
            'pulse' => __('Pulsation', 'archi-graph')
        ]
    ]);
    
    // Activer l'effet halo au survol
    $wp_customize->add_setting('archi_node_halo_enabled', [
        'default' => true,
        'transport' => 'refresh',
        'sanitize_callback' => 'archi_sanitize_checkbox'
    ]);
    
    $wp_customize->add_control('archi_node_halo_enabled', [
        'label' => __('Effet halo au survol', 'archi-graph'),
        'description' => __('Afficher un contour lumineux autour des nœuds au survol.', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'checkbox'
    ]);
    
    // Largeur du halo
    $wp_customize->add_setting('archi_node_halo_width', [
        'default' => 3,
        'transport' => 'refresh',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_node_halo_width', [
        'label' => __('Largeur du halo', 'archi-graph'),
        'description' => __('Épaisseur du contour lumineux en pixels (1-8).', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'range',
        'input_attrs' => [
            'min' => 1,
            'max' => 8,
            'step' => 1
        ]
    ]);
    
    // Opacité du halo
    $wp_customize->add_setting('archi_node_halo_opacity', [
        'default' => 0.5,
        'transport' => 'refresh',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_node_halo_opacity', [
        'label' => __('Opacité du halo', 'archi-graph'),
        'description' => __('Transparence du contour lumineux (0.0-1.0).', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'range',
        'input_attrs' => [
            'min' => 0.0,
            'max' => 1.0,
            'step' => 0.1
        ]
    ]);
    
    // --- LIENS ET CONNEXIONS ---
    
    // Couleur des liens
    $wp_customize->add_setting('archi_graph_link_color', [
        'default' => '#999999',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color'
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_graph_link_color', [
        'label' => __('Couleur des liens', 'archi-graph'),
        'description' => __('Couleur des lignes de connexion entre les nœuds.', 'archi-graph'),
        'section' => 'archi_graph_options'
    ]));
    
    // Épaisseur des liens
    $wp_customize->add_setting('archi_graph_link_width', [
        'default' => 1.5,
        'transport' => 'refresh',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_graph_link_width', [
        'label' => __('Épaisseur des liens', 'archi-graph'),
        'description' => __('Épaisseur en pixels des lignes de connexion (0.5-5).', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'range',
        'input_attrs' => [
            'min' => 0.5,
            'max' => 5,
            'step' => 0.5
        ]
    ]);
    
    // Opacité des liens
    $wp_customize->add_setting('archi_graph_link_opacity', [
        'default' => 0.6,
        'transport' => 'refresh',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_graph_link_opacity', [
        'label' => __('Opacité des liens', 'archi-graph'),
        'description' => __('Transparence des lignes (0 = invisible, 1 = opaque).', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'range',
        'input_attrs' => [
            'min' => 0.1,
            'max' => 1,
            'step' => 0.1
        ]
    ]);
    
    // Style de lien
    $wp_customize->add_setting('archi_graph_link_style', [
        'default' => 'solid',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    
    $wp_customize->add_control('archi_graph_link_style', [
        'label' => __('Style de lien', 'archi-graph'),
        'description' => __('Apparence des lignes de connexion.', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'select',
        'choices' => [
            'solid' => __('Ligne continue', 'archi-graph'),
            'dashed' => __('Ligne pointillée', 'archi-graph'),
            'curved' => __('Ligne courbe', 'archi-graph')
        ]
    ]);
    
    // Afficher les flèches directionnelles
    $wp_customize->add_setting('archi_graph_show_arrows', [
        'default' => false,
        'transport' => 'refresh',
        'sanitize_callback' => 'archi_sanitize_checkbox'
    ]);
    
    $wp_customize->add_control('archi_graph_show_arrows', [
        'label' => __('Flèches directionnelles', 'archi-graph'),
        'description' => __('Afficher des flèches pour indiquer le sens des relations.', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'checkbox'
    ]);
    
    // Animation des liens
    $wp_customize->add_setting('archi_graph_link_animation', [
        'default' => 'none',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    
    $wp_customize->add_control('archi_graph_link_animation', [
        'label' => __('Animation des liens', 'archi-graph'),
        'description' => __('Effet d\'animation sur les lignes de connexion.', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'select',
        'choices' => [
            'none' => __('Aucune', 'archi-graph'),
            'pulse' => __('Pulsation', 'archi-graph'),
            'flow' => __('Flux directionnel', 'archi-graph'),
            'glow' => __('Lueur', 'archi-graph')
        ]
    ]);
    
    // --- COULEURS PAR CATÉGORIE ---
    
    // Activer les couleurs par catégorie
    $wp_customize->add_setting('archi_graph_category_colors_enabled', [
        'default' => false,
        'transport' => 'refresh',
        'sanitize_callback' => 'archi_sanitize_checkbox'
    ]);
    
    $wp_customize->add_control('archi_graph_category_colors_enabled', [
        'label' => __('Couleurs par catégorie', 'archi-graph'),
        'description' => __('Attribuer automatiquement des couleurs différentes selon les catégories.', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'checkbox'
    ]);
    
    // Palette de couleurs pour les catégories
    $wp_customize->add_setting('archi_graph_category_palette', [
        'default' => 'default',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    
    $wp_customize->add_control('archi_graph_category_palette', [
        'label' => __('Palette de couleurs', 'archi-graph'),
        'description' => __('Jeu de couleurs pour différencier les catégories.', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'select',
        'choices' => [
            'default' => __('Par défaut (bleus)', 'archi-graph'),
            'warm' => __('Chaude (rouges/oranges)', 'archi-graph'),
            'cool' => __('Froide (bleus/verts)', 'archi-graph'),
            'vibrant' => __('Vibrante (multicolore)', 'archi-graph'),
            'pastel' => __('Pastel (doux)', 'archi-graph'),
            'nature' => __('Nature (terre/vert)', 'archi-graph'),
            'monochrome' => __('Monochrome (nuances de gris)', 'archi-graph')
        ]
    ]);
    
    // Afficher la légende des catégories
    $wp_customize->add_setting('archi_graph_show_category_legend', [
        'default' => true,
        'transport' => 'refresh',
        'sanitize_callback' => 'archi_sanitize_checkbox'
    ]);
    
    $wp_customize->add_control('archi_graph_show_category_legend', [
        'label' => __('Afficher la légende', 'archi-graph'),
        'description' => __('Afficher une légende des couleurs de catégories sur le graph.', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'checkbox'
    ]);
    
    // --- COULEURS PAR TYPE DE CONTENU ---
    
    // Couleur des projets
    $wp_customize->add_setting('archi_project_color', [
        'default' => '#f39c12',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color'
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_project_color', [
        'label' => __('Couleur des projets', 'archi-graph'),
        'description' => __('Couleur utilisée pour identifier les projets architecturaux.', 'archi-graph'),
        'section' => 'archi_graph_options',
    ]));
    
    // Couleur des illustrations
    $wp_customize->add_setting('archi_illustration_color', [
        'default' => '#3498db',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color'
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_illustration_color', [
        'label' => __('Couleur des illustrations', 'archi-graph'),
        'description' => __('Couleur utilisée pour identifier les illustrations.', 'archi-graph'),
        'section' => 'archi_graph_options',
    ]));
    
    // Couleur de la zone des pages
    $wp_customize->add_setting('archi_pages_zone_color', [
        'default' => '#9b59b6',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color'
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_pages_zone_color', [
        'label' => __('Couleur de la zone des pages', 'archi-graph'),
        'description' => __('Couleur de l\'enveloppe regroupant les pages.', 'archi-graph'),
        'section' => 'archi_graph_options',
    ]));
    
    // Couleur des liens du livre d'or
    $wp_customize->add_setting('archi_guestbook_link_color', [
        'default' => '#2ecc71',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color'
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_guestbook_link_color', [
        'label' => __('Couleur des liens du livre d\'or', 'archi-graph'),
        'description' => __('Couleur distinctive pour les liens créés depuis le livre d\'or.', 'archi-graph'),
        'section' => 'archi_graph_options',
    ]));
    
    // --- BADGES DE PRIORITÉ ---
    
    // Couleur du badge "Featured"
    $wp_customize->add_setting('archi_priority_featured_color', [
        'default' => '#e74c3c',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color'
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_priority_featured_color', [
        'label' => __('Couleur du badge "En vedette"', 'archi-graph'),
        'description' => __('Couleur du badge pour les articles en vedette.', 'archi-graph'),
        'section' => 'archi_graph_options',
    ]));
    
    // Couleur du badge "High"
    $wp_customize->add_setting('archi_priority_high_color', [
        'default' => '#f39c12',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color'
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_priority_high_color', [
        'label' => __('Couleur du badge "Haute priorité"', 'archi-graph'),
        'description' => __('Couleur du badge pour les articles de haute priorité.', 'archi-graph'),
        'section' => 'archi_graph_options',
    ]));
    
    // Taille du badge de priorité
    $wp_customize->add_setting('archi_priority_badge_size', [
        'default' => 8,
        'transport' => 'refresh',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_priority_badge_size', [
        'label' => __('Taille du badge de priorité', 'archi-graph'),
        'description' => __('Rayon du badge en pixels (5-15).', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'range',
        'input_attrs' => [
            'min' => 5,
            'max' => 15,
            'step' => 1
        ]
    ]);
    
    // --- ÉCHELLE ET APPARENCE DES NŒUDS ---
    
    // Type de symbole pour les nœuds
    $wp_customize->add_setting('archi_node_symbol_type', [
        'default' => 'none',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    
    $wp_customize->add_control('archi_node_symbol_type', [
        'label' => __('Type de symbole des nœuds', 'archi-graph'),
        'description' => __('Choisissez l\'apparence des nœuds dans le graphique.', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'select',
        'choices' => [
            'none' => __('Aucun symbole (images uniquement)', 'archi-graph'),
            'circle' => __('Cercles', 'archi-graph'),
            'square' => __('Carrés', 'archi-graph'),
            'diamond' => __('Losanges', 'archi-graph'),
            'triangle' => __('Triangles', 'archi-graph')
        ]
    ]);
    
    // Échelle du nœud actif
    $wp_customize->add_setting('archi_active_node_scale', [
        'default' => 1.5,
        'transport' => 'refresh',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_active_node_scale', [
        'label' => __('Échelle du nœud actif', 'archi-graph'),
        'description' => __('Agrandissement du nœud sélectionné (1.0-2.5).', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'range',
        'input_attrs' => [
            'min' => 1.0,
            'max' => 2.5,
            'step' => 0.1
        ]
    ]);
    
    // --- APPARENCE DES CLUSTERS ---
    
    // Opacité de remplissage des clusters
    $wp_customize->add_setting('archi_cluster_fill_opacity', [
        'default' => 0.12,
        'transport' => 'refresh',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_cluster_fill_opacity', [
        'label' => __('Opacité de remplissage des clusters', 'archi-graph'),
        'description' => __('Transparence du fond des enveloppes (0.0-0.5).', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'range',
        'input_attrs' => [
            'min' => 0.0,
            'max' => 0.5,
            'step' => 0.01
        ]
    ]);
    
    // Épaisseur du contour des clusters
    $wp_customize->add_setting('archi_cluster_stroke_width', [
        'default' => 3,
        'transport' => 'refresh',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_cluster_stroke_width', [
        'label' => __('Épaisseur du contour des clusters', 'archi-graph'),
        'description' => __('Largeur de la bordure des enveloppes en pixels (1-6).', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'range',
        'input_attrs' => [
            'min' => 1,
            'max' => 6,
            'step' => 1
        ]
    ]);
    
    // Opacité du contour des clusters
    $wp_customize->add_setting('archi_cluster_stroke_opacity', [
        'default' => 0.35,
        'transport' => 'refresh',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_cluster_stroke_opacity', [
        'label' => __('Opacité du contour des clusters', 'archi-graph'),
        'description' => __('Transparence de la bordure des enveloppes (0.0-1.0).', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'range',
        'input_attrs' => [
            'min' => 0.0,
            'max' => 1.0,
            'step' => 0.05
        ]
    ]);
    
    // ========================================
    // SECTION 3.5: EFFETS VISUELS DU GRAPHE
    // ========================================
    $wp_customize->add_section('archi_graph_effects', [
        'title' => __('✨ Effets Visuels du Graphe', 'archi-graph'),
        'description' => __('Personnalisez les effets visuels, animations et lueurs du graphique.', 'archi-graph'),
        'priority' => 32,
    ]);
    
    // --- PRESET D'EFFETS ---
    $wp_customize->add_setting('archi_effects_preset', [
        'default' => 'normal',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    
    $wp_customize->add_control('archi_effects_preset', [
        'label' => __('🎭 Preset d\'Effets', 'archi-graph'),
        'description' => __('Appliquez un ensemble d\'effets prédéfinis (remplace les paramètres individuels).', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'select',
        'choices' => [
            'none' => __('Aucun effet', 'archi-graph'),
            'subtle' => __('Subtil', 'archi-graph'),
            'normal' => __('Normal (recommandé)', 'archi-graph'),
            'intense' => __('Intense', 'archi-graph'),
            'custom' => __('Personnalisé', 'archi-graph')
        ]
    ]);
    
    // --- LUEUR DES NŒUDS ACTIFS ---
    $wp_customize->add_setting('archi_active_node_glow_enabled', [
        'default' => true,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_checkbox'
    ]);
    
    $wp_customize->add_control('archi_active_node_glow_enabled', [
        'label' => __('💫 Lueur des Nœuds Actifs', 'archi-graph'),
        'description' => __('Afficher une lueur autour des nœuds sélectionnés.', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'checkbox'
    ]);
    
    // Intensité de la lueur
    $wp_customize->add_setting('archi_active_node_glow_intensity', [
        'default' => 25,
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_active_node_glow_intensity', [
        'label' => __('Intensité de la Lueur', 'archi-graph'),
        'description' => __('Rayon de diffusion de la lueur en pixels (10-50).', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'range',
        'input_attrs' => [
            'min' => 10,
            'max' => 50,
            'step' => 5
        ]
    ]);
    
    // Opacité de la lueur
    $wp_customize->add_setting('archi_active_node_glow_opacity', [
        'default' => 0.8,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_active_node_glow_opacity', [
        'label' => __('Opacité de la Lueur', 'archi-graph'),
        'description' => __('Transparence de l\'effet lumineux (0.0-1.0).', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'range',
        'input_attrs' => [
            'min' => 0.0,
            'max' => 1.0,
            'step' => 0.1
        ]
    ]);
    
    // --- OMBRES ---
    $wp_customize->add_setting('archi_node_shadow_enabled', [
        'default' => true,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_checkbox'
    ]);
    
    $wp_customize->add_control('archi_node_shadow_enabled', [
        'label' => __('🌑 Ombres des Nœuds', 'archi-graph'),
        'description' => __('Afficher des ombres portées sous les nœuds.', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'checkbox'
    ]);
    
    // Intensité de l'ombre
    $wp_customize->add_setting('archi_node_shadow_blur', [
        'default' => 6,
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_node_shadow_blur', [
        'label' => __('Flou de l\'Ombre', 'archi-graph'),
        'description' => __('Rayon de flou de l\'ombre en pixels (2-20).', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'range',
        'input_attrs' => [
            'min' => 2,
            'max' => 20,
            'step' => 2
        ]
    ]);
    
    // Opacité de l'ombre
    $wp_customize->add_setting('archi_node_shadow_opacity', [
        'default' => 0.3,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_node_shadow_opacity', [
        'label' => __('Opacité de l\'Ombre', 'archi-graph'),
        'description' => __('Intensité de l\'ombre (0.0-1.0).', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'range',
        'input_attrs' => [
            'min' => 0.0,
            'max' => 1.0,
            'step' => 0.1
        ]
    ]);
    
    // --- ANIMATION DE PULSATION ---
    $wp_customize->add_setting('archi_node_pulse_enabled', [
        'default' => true,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_checkbox'
    ]);
    
    $wp_customize->add_control('archi_node_pulse_enabled', [
        'label' => __('💓 Animation de Pulsation', 'archi-graph'),
        'description' => __('Effet de pulsation sur les nœuds prioritaires.', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'checkbox'
    ]);
    
    // Durée de la pulsation
    $wp_customize->add_setting('archi_node_pulse_duration', [
        'default' => 2500,
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_node_pulse_duration', [
        'label' => __('Durée de la Pulsation', 'archi-graph'),
        'description' => __('Temps du cycle en millisecondes (1000-5000).', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'range',
        'input_attrs' => [
            'min' => 1000,
            'max' => 5000,
            'step' => 500
        ]
    ]);
    
    // Intensité de la pulsation
    $wp_customize->add_setting('archi_node_pulse_intensity', [
        'default' => 0.85,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_node_pulse_intensity', [
        'label' => __('Intensité de la Pulsation', 'archi-graph'),
        'description' => __('Opacité minimale du cycle (0.5-1.0).', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'range',
        'input_attrs' => [
            'min' => 0.5,
            'max' => 1.0,
            'step' => 0.05
        ]
    ]);
    
    // --- PARTICULES FLOTTANTES ---
    $wp_customize->add_setting('archi_particles_enabled', [
        'default' => true,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_checkbox'
    ]);
    
    $wp_customize->add_control('archi_particles_enabled', [
        'label' => __('⭐ Particules Flottantes', 'archi-graph'),
        'description' => __('Afficher des particules animées en arrière-plan.', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'checkbox'
    ]);
    
    // Nombre de particules
    $wp_customize->add_setting('archi_particles_count', [
        'default' => 20,
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_particles_count', [
        'label' => __('Nombre de Particules', 'archi-graph'),
        'description' => __('Quantité de particules à afficher (10-50).', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'range',
        'input_attrs' => [
            'min' => 10,
            'max' => 50,
            'step' => 5
        ]
    ]);
    
    // Opacité des particules
    $wp_customize->add_setting('archi_particles_opacity', [
        'default' => 0.15,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_particles_opacity', [
        'label' => __('Opacité des Particules', 'archi-graph'),
        'description' => __('Transparence des particules (0.05-0.5).', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'range',
        'input_attrs' => [
            'min' => 0.05,
            'max' => 0.5,
            'step' => 0.05
        ]
    ]);
    
    // Vitesse des particules
    $wp_customize->add_setting('archi_particles_speed', [
        'default' => 15,
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_particles_speed', [
        'label' => __('Vitesse des Particules', 'archi-graph'),
        'description' => __('Durée du cycle d\'animation en secondes (10-30).', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'range',
        'input_attrs' => [
            'min' => 10,
            'max' => 30,
            'step' => 5
        ]
    ]);
    
    // --- LUEUR AMBIANTE ---
    $wp_customize->add_setting('archi_ambient_glow_enabled', [
        'default' => true,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_checkbox'
    ]);
    
    $wp_customize->add_control('archi_ambient_glow_enabled', [
        'label' => __('🌌 Lueur Ambiante', 'archi-graph'),
        'description' => __('Effet de halo lumineux en arrière-plan.', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'checkbox'
    ]);
    
    // Opacité de la lueur ambiante
    $wp_customize->add_setting('archi_ambient_glow_opacity', [
        'default' => 0.3,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_ambient_glow_opacity', [
        'label' => __('Opacité de la Lueur Ambiante', 'archi-graph'),
        'description' => __('Intensité du halo lumineux (0.1-0.6).', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'range',
        'input_attrs' => [
            'min' => 0.1,
            'max' => 0.6,
            'step' => 0.05
        ]
    ]);
    
    // Durée de pulsation de la lueur ambiante
    $wp_customize->add_setting('archi_ambient_glow_duration', [
        'default' => 8,
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_ambient_glow_duration', [
        'label' => __('Durée de Pulsation', 'archi-graph'),
        'description' => __('Vitesse du cycle de pulsation en secondes (4-15).', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'range',
        'input_attrs' => [
            'min' => 4,
            'max' => 15,
            'step' => 1
        ]
    ]);
    
    // --- EFFETS AU SURVOL ---
    $wp_customize->add_setting('archi_hover_scale', [
        'default' => 1.2,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_hover_scale', [
        'label' => __('🔍 Agrandissement au Survol', 'archi-graph'),
        'description' => __('Facteur d\'agrandissement des nœuds survolés (1.0-1.5).', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'range',
        'input_attrs' => [
            'min' => 1.0,
            'max' => 1.5,
            'step' => 0.1
        ]
    ]);
    
    // Durée de transition au survol
    $wp_customize->add_setting('archi_hover_transition_duration', [
        'default' => 300,
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_hover_transition_duration', [
        'label' => __('Durée de Transition au Survol', 'archi-graph'),
        'description' => __('Vitesse de l\'animation de survol en millisecondes (100-800).', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'range',
        'input_attrs' => [
            'min' => 100,
            'max' => 800,
            'step' => 100
        ]
    ]);
    
    // Luminosité au survol
    $wp_customize->add_setting('archi_hover_brightness', [
        'default' => 1.15,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_hover_brightness', [
        'label' => __('Luminosité au Survol', 'archi-graph'),
        'description' => __('Augmentation de luminosité au survol (1.0-1.5).', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'range',
        'input_attrs' => [
            'min' => 1.0,
            'max' => 1.5,
            'step' => 0.05
        ]
    ]);
    
    // --- NŒUD ACTIF (SÉLECTIONNÉ) ---
    $wp_customize->add_setting('archi_active_node_scale', [
        'default' => 1.5,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_active_node_scale', [
        'label' => __('🎯 Agrandissement du Nœud Actif', 'archi-graph'),
        'description' => __('Facteur d\'agrandissement du nœud sélectionné (1.2-2.0).', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'range',
        'input_attrs' => [
            'min' => 1.2,
            'max' => 2.0,
            'step' => 0.1
        ]
    ]);
    
    // Animation de lueur du nœud actif
    $wp_customize->add_setting('archi_active_node_glow_animation', [
        'default' => 'pulse',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    
    $wp_customize->add_control('archi_active_node_glow_animation', [
        'label' => __('Animation de Lueur du Nœud Actif', 'archi-graph'),
        'description' => __('Type d\'animation pour la lueur du nœud sélectionné.', 'archi-graph'),
        'section' => 'archi_graph_effects',
        'type' => 'select',
        'choices' => [
            'none' => __('Aucune', 'archi-graph'),
            'pulse' => __('Pulsation', 'archi-graph'),
            'breathe' => __('Respiration', 'archi-graph'),
            'glow' => __('Lueur continue', 'archi-graph')
        ]
    ]);
    
    // ========================================
    // SECTION 4: TYPOGRAPHIE
    // ========================================
    $wp_customize->add_section('archi_typography', [
        'title' => __('📝 Typographie', 'archi-graph'),
        'description' => __('Personnalisez la taille et l\'apparence du texte.', 'archi-graph'),
        'priority' => 35,
    ]);
    
    // Police principale
    $wp_customize->add_setting('archi_font_family', [
        'default' => 'system',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    
    $wp_customize->add_control('archi_font_family', [
        'label' => __('Police principale', 'archi-graph'),
        'description' => __('Choisissez la police utilisée sur tout le site.', 'archi-graph'),
        'section' => 'archi_typography',
        'type' => 'select',
        'choices' => [
            'system' => __('Système par défaut', 'archi-graph'),
            'arial' => 'Arial',
            'helvetica' => 'Helvetica',
            'georgia' => 'Georgia',
            'times' => 'Times New Roman',
            'courier' => 'Courier New',
            'verdana' => 'Verdana',
            'trebuchet' => 'Trebuchet MS',
            'roboto' => 'Roboto (Google Fonts)',
            'open-sans' => 'Open Sans (Google Fonts)',
            'lato' => 'Lato (Google Fonts)',
            'montserrat' => 'Montserrat (Google Fonts)',
            'poppins' => 'Poppins (Google Fonts)',
            'inter' => 'Inter (Google Fonts)',
            'playfair' => 'Playfair Display (Google Fonts)',
            'merriweather' => 'Merriweather (Google Fonts)',
        ]
    ]);
    
    // Taille de police de base
    $wp_customize->add_setting('archi_font_size_base', [
        'default' => 16,
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_font_size_base', [
        'label' => __('Taille du texte', 'archi-graph'),
        'description' => __('Taille de base du texte en pixels (12-20).', 'archi-graph'),
        'section' => 'archi_typography',
        'type' => 'range',
        'input_attrs' => [
            'min' => 12,
            'max' => 20,
            'step' => 1
        ]
    ]);
    
    // ========================================
    // SECTION 5: RÉSEAUX SOCIAUX
    // ========================================
    $wp_customize->add_section('archi_social_media', [
        'title' => __('🌐 Réseaux Sociaux', 'archi-graph'),
        'description' => __('Ajoutez les liens vers vos profils de réseaux sociaux.', 'archi-graph'),
        'priority' => 40,
    ]);
    
    $social_networks = [
        'facebook' => 'Facebook',
        'twitter' => 'Twitter/X',
        'instagram' => 'Instagram',
        'linkedin' => 'LinkedIn',
        'github' => 'GitHub',
        'youtube' => 'YouTube'
    ];
    
    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting("archi_social_{$network}", [
            'default' => '',
            'sanitize_callback' => 'esc_url_raw'
        ]);
        
        $wp_customize->add_control("archi_social_{$network}", [
            'label' => $label,
            'section' => 'archi_social_media',
            'type' => 'url',
            'input_attrs' => [
                'placeholder' => 'https://...'
            ]
        ]);
    }
    
    // Afficher dans le footer
    $wp_customize->add_setting('archi_footer_show_social', [
        'default' => true,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_checkbox'
    ]);
    
    $wp_customize->add_control('archi_footer_show_social', [
        'label' => __('Afficher dans le footer', 'archi-graph'),
        'description' => __('Afficher les icônes de réseaux sociaux en bas de page.', 'archi-graph'),
        'section' => 'archi_social_media',
        'type' => 'checkbox'
    ]);
    
    // ========================================
    // SECTION 6: FOOTER
    // ========================================
    $wp_customize->add_section('archi_footer_options', [
        'title' => __('📄 Pied de Page', 'archi-graph'),
        'description' => __('Personnalisez le contenu du footer.', 'archi-graph'),
        'priority' => 45,
    ]);
    
    // Texte copyright
    $wp_customize->add_setting('archi_footer_copyright', [
        'default' => '© ' . date('Y') . ' ' . get_bloginfo('name'),
        'transport' => 'postMessage',
        'sanitize_callback' => 'wp_kses_post'
    ]);
    
    $wp_customize->add_control('archi_footer_copyright', [
        'label' => __('Texte de copyright', 'archi-graph'),
        'description' => __('Texte affiché en bas de page.', 'archi-graph'),
        'section' => 'archi_footer_options',
        'type' => 'textarea'
    ]);
}
add_action('customize_register', 'archi_customize_register');

/**
 * Sanitize float values
 */
function archi_sanitize_float($value) {
    return floatval($value);
}

/**
 * Sanitize checkbox values
 */
function archi_sanitize_checkbox($value) {
    return (bool) $value;
}

/**
 * Get category color palette
 */
function archi_get_category_color_palette($palette_name = 'default') {
    $palettes = [
        'default' => [
            '#3498db', '#2980b9', '#5dade2', '#1f618d', '#85c1e9',
            '#21618c', '#7fb3d5', '#154360', '#aed6f1', '#2e86c1'
        ],
        'warm' => [
            '#e74c3c', '#c0392b', '#ec7063', '#922b21', '#f1948a',
            '#e67e22', '#d35400', '#f39c12', '#f8c471', '#dc7633'
        ],
        'cool' => [
            '#16a085', '#1abc9c', '#48c9b0', '#0e6655', '#76d7c4',
            '#27ae60', '#229954', '#52be80', '#1e8449', '#82e0aa'
        ],
        'vibrant' => [
            '#e74c3c', '#3498db', '#9b59b6', '#f39c12', '#1abc9c',
            '#e67e22', '#2ecc71', '#8e44ad', '#34495e', '#16a085'
        ],
        'pastel' => [
            '#aed6f1', '#f9e79f', '#abebc6', '#f5b7b1', '#d7bde2',
            '#a9dfbf', '#f8b4d9', '#fad7a0', '#d5f4e6', '#fadbd8'
        ],
        'nature' => [
            '#27ae60', '#229954', '#52be80', '#7d6608', '#d68910',
            '#935116', '#6e2c00', '#52be80', '#a04000', '#82e0aa'
        ],
        'monochrome' => [
            '#2c3e50', '#34495e', '#566573', '#707b7c', '#95a5a6',
            '#7f8c8d', '#515a5a', '#a6acaf', '#626567', '#d5d8dc'
        ]
    ];
    
    return isset($palettes[$palette_name]) ? $palettes[$palette_name] : $palettes['default'];
}

/**
 * Get color for a specific category
 */
function archi_get_category_color($category_id, $palette = 'default') {
    $palette_colors = archi_get_category_color_palette($palette);
    $index = absint($category_id) % count($palette_colors);
    return $palette_colors[$index];
}

/**
 * Localize graph settings for JavaScript
 * ⚠️ DEPRECATED - Moved to functions.php for proper timing
 * This function had timing issues with wp_enqueue_scripts hook
 * Graph settings are now localized directly in functions.php after wp_enqueue_script('archi-app')
 */
function archi_localize_graph_settings() {
    // Function deprecated - settings now localized in functions.php
    // Kept for reference only
    return;
    
    // Only on front-end with graph
    if (is_admin() || !is_front_page()) {
        return;
    }
    
    $graph_settings = [
        // Node settings
        'defaultNodeColor' => get_theme_mod('archi_default_node_color', '#3498db'),
        'defaultNodeSize' => get_theme_mod('archi_default_node_size', 80), // ✅ Harmonized to 80px for consistency
        'clusterStrength' => get_theme_mod('archi_cluster_strength', 0.1),
        
        // Display options
        'popupTitleOnly' => get_theme_mod('archi_graph_popup_title_only', false),
        'showComments' => get_theme_mod('archi_graph_show_comments', true),
        
        // Animations and effects
        'animationMode' => get_theme_mod('archi_graph_animation_mode', 'fade-in'),
        'transitionSpeed' => get_theme_mod('archi_graph_transition_speed', 500),
        'hoverEffect' => get_theme_mod('archi_graph_hover_effect', 'highlight'),
        
        // Links configuration
        'linkColor' => get_theme_mod('archi_graph_link_color', '#999999'),
        'linkWidth' => get_theme_mod('archi_graph_link_width', 1.5),
        'linkOpacity' => get_theme_mod('archi_graph_link_opacity', 0.6),
        'linkStyle' => get_theme_mod('archi_graph_link_style', 'solid'),
        'showArrows' => get_theme_mod('archi_graph_show_arrows', false),
        'linkAnimation' => get_theme_mod('archi_graph_link_animation', 'none'),
        
        // Category colors
        'categoryColorsEnabled' => get_theme_mod('archi_graph_category_colors_enabled', false),
        'categoryPalette' => get_theme_mod('archi_graph_category_palette', 'default'),
        'showCategoryLegend' => get_theme_mod('archi_graph_show_category_legend', true),
        
        // Get actual palette colors
        'categoryColors' => archi_get_category_color_palette(get_theme_mod('archi_graph_category_palette', 'default'))
    ];
    
    // 🔥 FIX: Utiliser le bon handle de script 'archi-app' au lieu de 'archi-graph-main'
    if (wp_script_is('archi-app', 'enqueued')) {
        wp_localize_script('archi-app', 'archiGraphSettings', $graph_settings);
    }
}
// Hook disabled - localization moved to functions.php
// add_action('wp_enqueue_scripts', 'archi_localize_graph_settings', 20);

/**
 * Get font family CSS value
 */
function archi_get_font_family_css($font_family) {
    $font_stacks = [
        'system' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
        'arial' => 'Arial, Helvetica, sans-serif',
        'helvetica' => '"Helvetica Neue", Helvetica, Arial, sans-serif',
        'georgia' => 'Georgia, "Times New Roman", Times, serif',
        'times' => '"Times New Roman", Times, serif',
        'courier' => '"Courier New", Courier, monospace',
        'verdana' => 'Verdana, Geneva, sans-serif',
        'trebuchet' => '"Trebuchet MS", "Lucida Grande", "Lucida Sans Unicode", sans-serif',
        'roboto' => '"Roboto", -apple-system, BlinkMacSystemFont, sans-serif',
        'open-sans' => '"Open Sans", -apple-system, BlinkMacSystemFont, sans-serif',
        'lato' => '"Lato", -apple-system, BlinkMacSystemFont, sans-serif',
        'montserrat' => '"Montserrat", -apple-system, BlinkMacSystemFont, sans-serif',
        'poppins' => '"Poppins", -apple-system, BlinkMacSystemFont, sans-serif',
        'inter' => '"Inter", -apple-system, BlinkMacSystemFont, sans-serif',
        'playfair' => '"Playfair Display", Georgia, serif',
        'merriweather' => '"Merriweather", Georgia, serif',
    ];
    
    return $font_stacks[$font_family] ?? $font_stacks['system'];
}

/**
 * Enqueue Google Fonts if needed
 */
function archi_enqueue_google_fonts() {
    $font_family = get_theme_mod('archi_font_family', 'system');
    
    $google_fonts = [
        'roboto' => 'Roboto:300,400,500,700',
        'open-sans' => 'Open+Sans:300,400,600,700',
        'lato' => 'Lato:300,400,700',
        'montserrat' => 'Montserrat:300,400,500,600,700',
        'poppins' => 'Poppins:300,400,500,600,700',
        'inter' => 'Inter:300,400,500,600,700',
        'playfair' => 'Playfair+Display:400,500,700',
        'merriweather' => 'Merriweather:300,400,700',
    ];
    
    if (isset($google_fonts[$font_family])) {
        wp_enqueue_style(
            'archi-google-font',
            'https://fonts.googleapis.com/css2?family=' . $google_fonts[$font_family] . '&display=swap',
            [],
            null
        );
    }
}
add_action('wp_enqueue_scripts', 'archi_enqueue_google_fonts');

/**
 * Enqueue Customizer preview JavaScript
 */
function archi_customizer_preview_js() {
    wp_enqueue_script(
        'archi-customizer-preview',
        get_template_directory_uri() . '/assets/js/customizer-preview.js',
        ['jquery', 'customize-preview'],
        ARCHI_THEME_VERSION,
        true
    );
}
add_action('customize_preview_init', 'archi_customizer_preview_js');

/**
 * Enqueue Customizer controls JavaScript
 */
function archi_customizer_controls_js() {
    wp_enqueue_script(
        'archi-customizer-controls',
        get_template_directory_uri() . '/assets/js/customizer-controls.js',
        ['jquery', 'customize-controls'],
        ARCHI_THEME_VERSION,
        true
    );
}
add_action('customize_controls_enqueue_scripts', 'archi_customizer_controls_js');

/**
 * Output Customizer CSS
 */
/**
 * Applique les styles personnalisés du Customizer
 */
function archi_customizer_css() {
    // Get color settings (consolidated)
    $primary_color = get_theme_mod('archi_primary_color', '#3498db');
    $secondary_color = get_theme_mod('archi_secondary_color', '#2c3e50');
    $header_bg_color = get_theme_mod('archi_header_bg_color', '#ffffff');
    $header_text_color = get_theme_mod('archi_header_text_color', '#2c3e50');
    
    // Get typography settings
    $font_size_base = get_theme_mod('archi_font_size_base', 16);
    $font_family = get_theme_mod('archi_font_family', 'system');
    
    // Get header options
    $header_transparent = get_theme_mod('archi_header_transparent', false);
    $header_height = get_theme_mod('archi_header_height', 'normal');
    $header_shadow = get_theme_mod('archi_header_shadow', 'light');
    $header_scroll_opacity = get_theme_mod('archi_header_scroll_opacity', 0.95);
    $header_logo_position = get_theme_mod('archi_header_logo_position', 'left');
    $header_sticky_behavior = get_theme_mod('archi_header_sticky_behavior', 'always');
    
    // Map font family to CSS
    $font_family_css = archi_get_font_family_css($font_family);
    
    // Map header height values
    $height_map = [
        'compact' => '60px',
        'normal' => '80px',
        'large' => '100px',
        'extra-large' => '120px'
    ];
    $header_height_value = $height_map[$header_height] ?? '80px';
    
    // Map shadow values
    $shadow_map = [
        'none' => 'none',
        'light' => '0 2px 4px rgba(0,0,0,0.1)',
        'medium' => '0 4px 8px rgba(0,0,0,0.15)',
        'strong' => '0 6px 12px rgba(0,0,0,0.2)'
    ];
    $header_shadow_value = $shadow_map[$header_shadow] ?? '0 2px 4px rgba(0,0,0,0.1)';
    
    ?>
    <style id="archi-customizer-styles">
        /* Typography - Application globale de la police */
        body,
        html,
        input,
        textarea,
        select,
        button,
        .site-header,
        .site-navigation,
        .main-navigation,
        .site-content,
        .entry-content,
        .site-footer,
        h1, h2, h3, h4, h5, h6,
        p, span, div, a,
        .btn, .button,
        .wp-block,
        .graph-container,
        .article-card,
        .panel-content,
        .node-title-text,
        .node-label,
        .graph-legend,
        .graph-info-panel,
        .graph-instructions,
        .graph-controls,
        .side-panel,
        .title-overlay {
            font-family: <?php echo esc_attr($font_family_css); ?> !important;
        }
        
        body {
            font-size: <?php echo absint($font_size_base); ?>px;
        }
        
        /* Primary colors */
        a {
            color: <?php echo esc_attr($primary_color); ?>;
        }
        
        a:hover {
            color: <?php echo esc_attr(archi_adjust_color_brightness($primary_color, -20)); ?>;
        }
        
        h1, h2, h3, h4, h5, h6 {
            color: <?php echo esc_attr($secondary_color); ?>;
        }
        
        .button, .btn-primary, button[type="submit"] {
            background-color: <?php echo esc_attr($primary_color); ?>;
            border-color: <?php echo esc_attr($primary_color); ?>;
        }
        
        .button:hover, .btn-primary:hover, button[type="submit"]:hover {
            background-color: <?php echo esc_attr(archi_adjust_color_brightness($primary_color, -20)); ?>;
            border-color: <?php echo esc_attr(archi_adjust_color_brightness($primary_color, -20)); ?>;
        }
        
        /* Header & Navigation */
        .site-header {
            background-color: <?php echo esc_attr($header_bg_color); ?>;
            height: <?php echo esc_attr($header_height_value); ?>;
            box-shadow: <?php echo esc_attr($header_shadow_value); ?>;
            transition: all 0.3s ease-in-out;
        }
        
        /* Header logo/title position */
        .site-header .site-branding {
            text-align: <?php echo esc_attr($header_logo_position); ?>;
        }
        
        <?php if ($header_logo_position === 'center') : ?>
        .site-header .container {
            justify-content: center;
        }
        .site-header .main-navigation {
            margin-left: auto;
            margin-right: auto;
        }
        <?php elseif ($header_logo_position === 'right') : ?>
        .site-header .container {
            flex-direction: row-reverse;
        }
        <?php endif; ?>
        
        /* Sticky header behavior */
        <?php if ($header_sticky_behavior === 'hide-on-scroll-down') : ?>
        .site-header.sticky-header.scroll-down {
            transform: translateY(-100%);
        }
        .site-header.sticky-header.scroll-up {
            transform: translateY(0);
        }
        <?php elseif ($header_sticky_behavior === 'show-on-scroll-up') : ?>
        .site-header.sticky-header {
            transform: translateY(-100%);
        }
        .site-header.sticky-header.scroll-up {
            transform: translateY(0);
        }
        <?php endif; ?>
        
        <?php if ($header_transparent) : ?>
        .home .site-header.transparent-header {
            background-color: transparent;
            position: absolute;
            width: 100%;
            z-index: 1000;
            box-shadow: none;
        }
        
        .home .site-header.transparent-header.scrolled {
            background-color: <?php echo esc_attr($header_bg_color); ?>;
            opacity: <?php echo esc_attr($header_scroll_opacity); ?>;
            box-shadow: <?php echo esc_attr($header_shadow_value); ?>;
        }
        <?php endif; ?>
        
        .main-navigation .nav-menu a {
            color: <?php echo esc_attr($header_text_color); ?>;
        }
        
        .main-navigation .nav-menu a:hover,
        .main-navigation .nav-menu .current-menu-item > a {
            color: <?php echo esc_attr($primary_color); ?>;
        }
    </style>
    <?php
}
add_action('wp_head', 'archi_customizer_css', 999);

/**
 * Adjust color brightness
 * 
 * @param string $hex Hex color
 * @param int $steps Steps to adjust (-255 to 255)
 * @return string Adjusted hex color
 */
function archi_adjust_color_brightness($hex, $steps) {
    // Remove # if present
    $hex = str_replace('#', '', $hex);
    
    // Convert to RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Adjust
    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));
    
    // Convert back to hex
    return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
               . str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
               . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}

/**
 * 🔥 Apply visual effects preset values
 * Fonction appelée quand l'utilisateur change le preset d'effets visuels
 * 
 * @param string $preset Preset name: 'none', 'subtle', 'normal', 'intense', 'custom'
 * @return array Associative array of all settings values for the preset
 * @since 2.0.0
 */
function archi_get_effects_preset_values($preset = 'normal') {
    $presets = [
        'none' => [
            // Tout désactivé
            'archi_active_node_glow_enabled' => false,
            'archi_node_shadow_enabled' => false,
            'archi_node_pulse_enabled' => false,
            'archi_particles_enabled' => false,
            'archi_ambient_glow_enabled' => false,
            'archi_hover_scale' => 1.0,
            'archi_hover_brightness' => 1.0,
        ],
        
        'subtle' => [
            // Effets très discrets
            'archi_active_node_glow_enabled' => true,
            'archi_active_node_glow_intensity' => 15,
            'archi_active_node_glow_opacity' => 0.5,
            'archi_node_shadow_enabled' => true,
            'archi_node_shadow_blur' => 4,
            'archi_node_shadow_opacity' => 0.2,
            'archi_node_pulse_enabled' => false,
            'archi_node_pulse_duration' => 3000,
            'archi_node_pulse_intensity' => 0.9,
            'archi_particles_enabled' => true,
            'archi_particles_count' => 10,
            'archi_particles_opacity' => 0.08,
            'archi_particles_speed' => 20,
            'archi_ambient_glow_enabled' => true,
            'archi_ambient_glow_opacity' => 0.15,
            'archi_ambient_glow_duration' => 10,
            'archi_hover_scale' => 1.1,
            'archi_hover_transition_duration' => 400,
            'archi_hover_brightness' => 1.08,
            'archi_active_node_scale' => 1.3,
        ],
        
        'normal' => [
            // Valeurs par défaut équilibrées (recommandé)
            'archi_active_node_glow_enabled' => true,
            'archi_active_node_glow_intensity' => 25,
            'archi_active_node_glow_opacity' => 0.8,
            'archi_node_shadow_enabled' => true,
            'archi_node_shadow_blur' => 6,
            'archi_node_shadow_opacity' => 0.3,
            'archi_node_pulse_enabled' => true,
            'archi_node_pulse_duration' => 2500,
            'archi_node_pulse_intensity' => 0.85,
            'archi_particles_enabled' => true,
            'archi_particles_count' => 20,
            'archi_particles_opacity' => 0.15,
            'archi_particles_speed' => 15,
            'archi_ambient_glow_enabled' => true,
            'archi_ambient_glow_opacity' => 0.3,
            'archi_ambient_glow_duration' => 8,
            'archi_hover_scale' => 1.2,
            'archi_hover_transition_duration' => 300,
            'archi_hover_brightness' => 1.15,
            'archi_active_node_scale' => 1.5,
            'archi_active_node_glow_animation' => 'pulse',
        ],
        
        'intense' => [
            // Effets très marqués et dynamiques
            'archi_active_node_glow_enabled' => true,
            'archi_active_node_glow_intensity' => 40,
            'archi_active_node_glow_opacity' => 1.0,
            'archi_node_shadow_enabled' => true,
            'archi_node_shadow_blur' => 12,
            'archi_node_shadow_opacity' => 0.5,
            'archi_node_pulse_enabled' => true,
            'archi_node_pulse_duration' => 1500,
            'archi_node_pulse_intensity' => 0.7,
            'archi_particles_enabled' => true,
            'archi_particles_count' => 40,
            'archi_particles_opacity' => 0.25,
            'archi_particles_speed' => 10,
            'archi_ambient_glow_enabled' => true,
            'archi_ambient_glow_opacity' => 0.5,
            'archi_ambient_glow_duration' => 5,
            'archi_hover_scale' => 1.35,
            'archi_hover_transition_duration' => 200,
            'archi_hover_brightness' => 1.3,
            'archi_active_node_scale' => 1.8,
            'archi_active_node_glow_animation' => 'breathe',
        ],
    ];
    
    return $presets[$preset] ?? $presets['normal'];
}

/**
 * 🔥 Apply preset when user selects one in Customizer
 * Hook appelé quand archi_effects_preset change
 * 
 * @param mixed $value New preset value
 * @param WP_Customize_Setting $setting Setting object
 * @return mixed Sanitized value
 * @since 2.0.0
 */
function archi_apply_effects_preset_on_change($value, $setting) {
    // Si c'est "custom", ne rien faire (l'utilisateur ajustera manuellement)
    if ($value === 'custom') {
        return $value;
    }
    
    // Récupérer les valeurs du preset
    $preset_values = archi_get_effects_preset_values($value);
    
    // Appliquer chaque valeur (seulement côté PHP, le JS gérera la preview)
    foreach ($preset_values as $setting_id => $setting_value) {
        set_theme_mod($setting_id, $setting_value);
    }
    
    return $value;
}
// Note: Le hook sera ajouté dans le JS du Customizer pour la preview en temps réel

