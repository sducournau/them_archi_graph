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
    // SECTION: HEADER OPTIONS
    // ========================================
    $wp_customize->add_section('archi_header_options', [
        'title' => __('Options du Header', 'archi-graph'),
        'description' => __('Personnalisez le comportement et l\'apparence du header.', 'archi-graph'),
        'priority' => 30,
    ]);
    
    // Temps avant disparition du header
    $wp_customize->add_setting('archi_header_hide_delay', [
        'default' => 500,
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_header_hide_delay', [
        'label' => __('Délai avant masquage (ms)', 'archi-graph'),
        'description' => __('Temps en millisecondes avant que le header ne se masque automatiquement sur la page d\'accueil.', 'archi-graph'),
        'section' => 'archi_header_options',
        'type' => 'number',
        'input_attrs' => [
            'min' => 0,
            'max' => 5000,
            'step' => 100
        ]
    ]);
    
    // Type d'animation header
    $wp_customize->add_setting('archi_header_animation_type', [
        'default' => 'ease-in-out',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    
    $wp_customize->add_control('archi_header_animation_type', [
        'label' => __('Style d\'animation', 'archi-graph'),
        'description' => __('Type de courbe d\'animation pour le header.', 'archi-graph'),
        'section' => 'archi_header_options',
        'type' => 'select',
        'choices' => [
            'linear' => __('Linear (vitesse constante)', 'archi-graph'),
            'ease' => __('Ease (naturel)', 'archi-graph'),
            'ease-in' => __('Ease In (accélération)', 'archi-graph'),
            'ease-out' => __('Ease Out (décélération)', 'archi-graph'),
            'ease-in-out' => __('Ease In Out (fluide)', 'archi-graph'),
            'cubic-bezier(0.68, -0.55, 0.265, 1.55)' => __('Bounce (rebond)', 'archi-graph')
        ]
    ]);
    
    // Durée animation header
    $wp_customize->add_setting('archi_header_animation_duration', [
        'default' => 0.3,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_float'
    ]);
    
    $wp_customize->add_control('archi_header_animation_duration', [
        'label' => __('Durée animation (secondes)', 'archi-graph'),
        'description' => __('Durée totale de l\'animation en secondes.', 'archi-graph'),
        'section' => 'archi_header_options',
        'type' => 'number',
        'input_attrs' => [
            'min' => 0.1,
            'max' => 2.0,
            'step' => 0.1
        ]
    ]);
    
    // Hauteur de la zone de déclenchement
    $wp_customize->add_setting('archi_header_trigger_height', [
        'default' => 50,
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_header_trigger_height', [
        'label' => __('Hauteur zone de déclenchement (px)', 'archi-graph'),
        'description' => __('Hauteur de la zone en haut de page qui révèle le header au survol.', 'archi-graph'),
        'section' => 'archi_header_options',
        'type' => 'number',
        'input_attrs' => [
            'min' => 20,
            'max' => 150,
            'step' => 10
        ]
    ]);
    
    // ========================================
    // SECTION: GRAPH VISUALIZATION
    // ========================================
    $wp_customize->add_section('archi_graph_options', [
        'title' => __('Visualisation du Graphique', 'archi-graph'),
        'description' => __('Options visuelles pour le graphique D3.js sur la page d\'accueil.', 'archi-graph'),
        'priority' => 35,
    ]);
    
    // Couleur nœud par défaut
    $wp_customize->add_setting('archi_default_node_color', [
        'default' => '#3498db',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_hex_color'
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_default_node_color', [
        'label' => __('Couleur nœud par défaut', 'archi-graph'),
        'description' => __('Couleur utilisée pour les nœuds sans couleur personnalisée.', 'archi-graph'),
        'section' => 'archi_graph_options'
    ]));
    
    // Taille nœud par défaut
    $wp_customize->add_setting('archi_default_node_size', [
        'default' => 60,
        'transport' => 'refresh',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_default_node_size', [
        'label' => __('Taille nœud par défaut (px)', 'archi-graph'),
        'description' => __('Taille en pixels des nœuds sans taille personnalisée.', 'archi-graph'),
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
        'label' => __('Force de regroupement', 'archi-graph'),
        'description' => __('Intensité du regroupement des nœuds par catégorie.', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'range',
        'input_attrs' => [
            'min' => 0,
            'max' => 0.5,
            'step' => 0.01
        ]
    ]);
    
    // Durée des animations
    $wp_customize->add_setting('archi_graph_animation_duration', [
        'default' => 1000,
        'transport' => 'refresh',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_graph_animation_duration', [
        'label' => __('Durée des animations (ms)', 'archi-graph'),
        'description' => __('Durée des animations du graphique en millisecondes.', 'archi-graph'),
        'section' => 'archi_graph_options',
        'type' => 'number',
        'input_attrs' => [
            'min' => 100,
            'max' => 3000,
            'step' => 100
        ]
    ]);
    
    // ========================================
    // SECTION: TYPOGRAPHY
    // ========================================
    $wp_customize->add_section('archi_typography', [
        'title' => __('Typographie', 'archi-graph'),
        'description' => __('Personnalisez les polices et styles de texte.', 'archi-graph'),
        'priority' => 40,
    ]);
    
    // Font famille principale
    $wp_customize->add_setting('archi_font_family', [
        'default' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    
    $wp_customize->add_control('archi_font_family', [
        'label' => __('Police principale', 'archi-graph'),
        'description' => __('Police utilisée pour le corps du texte. Utilisez des polices système pour de meilleures performances.', 'archi-graph'),
        'section' => 'archi_typography',
        'type' => 'text'
    ]);
    
    // Taille de police de base
    $wp_customize->add_setting('archi_font_size_base', [
        'default' => 16,
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint'
    ]);
    
    $wp_customize->add_control('archi_font_size_base', [
        'label' => __('Taille de police de base (px)', 'archi-graph'),
        'description' => __('Taille de base du texte en pixels.', 'archi-graph'),
        'section' => 'archi_typography',
        'type' => 'range',
        'input_attrs' => [
            'min' => 12,
            'max' => 20,
            'step' => 1
        ]
    ]);
    
    // ========================================
    // SECTION: COLORS
    // ========================================
    $wp_customize->add_section('archi_colors', [
        'title' => __('Couleurs du Thème', 'archi-graph'),
        'description' => __('Personnalisez les couleurs principales du thème.', 'archi-graph'),
        'priority' => 45,
    ]);
    
    // Couleur principale
    $wp_customize->add_setting('archi_primary_color', [
        'default' => '#3498db',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color'
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_primary_color', [
        'label' => __('Couleur principale', 'archi-graph'),
        'description' => __('Couleur utilisée pour les liens et éléments interactifs.', 'archi-graph'),
        'section' => 'archi_colors'
    ]));
    
    // Couleur secondaire
    $wp_customize->add_setting('archi_secondary_color', [
        'default' => '#2c3e50',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color'
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_secondary_color', [
        'label' => __('Couleur secondaire', 'archi-graph'),
        'description' => __('Couleur pour les titres et éléments secondaires.', 'archi-graph'),
        'section' => 'archi_colors'
    ]));
    
    // ========================================
    // SECTION: SOCIAL MEDIA
    // ========================================
    $wp_customize->add_section('archi_social_media', [
        'title' => __('Réseaux Sociaux', 'archi-graph'),
        'description' => __('Ajoutez vos liens de réseaux sociaux.', 'archi-graph'),
        'priority' => 50,
    ]);
    
    $social_networks = [
        'facebook' => __('Facebook', 'archi-graph'),
        'twitter' => __('Twitter/X', 'archi-graph'),
        'instagram' => __('Instagram', 'archi-graph'),
        'linkedin' => __('LinkedIn', 'archi-graph'),
        'github' => __('GitHub', 'archi-graph'),
        'youtube' => __('YouTube', 'archi-graph')
    ];
    
    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting("archi_social_{$network}", [
            'default' => '',
            'sanitize_callback' => 'esc_url_raw'
        ]);
        
        $wp_customize->add_control("archi_social_{$network}", [
            'label' => $label . ' URL',
            'section' => 'archi_social_media',
            'type' => 'url',
            'input_attrs' => [
                'placeholder' => 'https://'
            ]
        ]);
    }
    
    // ========================================
    // SECTION: FOOTER
    // ========================================
    $wp_customize->add_section('archi_footer_options', [
        'title' => __('Options du Footer', 'archi-graph'),
        'description' => __('Personnalisez le pied de page.', 'archi-graph'),
        'priority' => 55,
    ]);
    
    // Texte copyright
    $wp_customize->add_setting('archi_footer_copyright', [
        'default' => '© ' . date('Y') . ' ' . get_bloginfo('name'),
        'transport' => 'postMessage',
        'sanitize_callback' => 'wp_kses_post'
    ]);
    
    $wp_customize->add_control('archi_footer_copyright', [
        'label' => __('Texte copyright', 'archi-graph'),
        'description' => __('Texte affiché dans le footer.', 'archi-graph'),
        'section' => 'archi_footer_options',
        'type' => 'textarea'
    ]);
    
    // Afficher les réseaux sociaux
    $wp_customize->add_setting('archi_footer_show_social', [
        'default' => true,
        'transport' => 'postMessage',
        'sanitize_callback' => 'archi_sanitize_checkbox'
    ]);
    
    $wp_customize->add_control('archi_footer_show_social', [
        'label' => __('Afficher les réseaux sociaux', 'archi-graph'),
        'description' => __('Afficher les liens de réseaux sociaux dans le footer.', 'archi-graph'),
        'section' => 'archi_footer_options',
        'type' => 'checkbox'
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
function archi_customizer_css() {
    $header_animation_type = get_theme_mod('archi_header_animation_type', 'ease-in-out');
    $header_animation_duration = get_theme_mod('archi_header_animation_duration', 0.3);
    $header_trigger_height = get_theme_mod('archi_header_trigger_height', 50);
    $font_family = get_theme_mod('archi_font_family', '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif');
    $font_size_base = get_theme_mod('archi_font_size_base', 16);
    $primary_color = get_theme_mod('archi_primary_color', '#3498db');
    $secondary_color = get_theme_mod('archi_secondary_color', '#2c3e50');
    
    ?>
    <style id="archi-customizer-styles">
        /* Header animations */
        .site-header {
            transition: transform <?php echo esc_attr($header_animation_duration); ?>s <?php echo esc_attr($header_animation_type); ?>,
                        opacity <?php echo esc_attr($header_animation_duration); ?>s <?php echo esc_attr($header_animation_type); ?>;
        }
        
        /* Header trigger zone */
        .header-trigger-zone {
            height: <?php echo absint($header_trigger_height); ?>px;
        }
        
        /* Typography */
        body {
            font-family: <?php echo esc_attr($font_family); ?>;
            font-size: <?php echo absint($font_size_base); ?>px;
        }
        
        /* Colors */
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
    </style>
    <?php
}
add_action('wp_head', 'archi_customizer_css');

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
 * Enqueue Customizer preview scripts
 */
function archi_customizer_preview_scripts() {
    wp_enqueue_script(
        'archi-customizer-preview',
        get_template_directory_uri() . '/assets/js/customizer-preview.js',
        ['jquery', 'customize-preview'],
        '1.2.0',
        true
    );
}
add_action('customize_preview_init', 'archi_customizer_preview_scripts');

/**
 * Enqueue Customizer control scripts
 */
function archi_customizer_control_scripts() {
    wp_enqueue_script(
        'archi-customizer-controls',
        get_template_directory_uri() . '/assets/js/customizer-controls.js',
        ['jquery', 'customize-controls'],
        '1.2.0',
        true
    );
}
add_action('customize_controls_enqueue_scripts', 'archi_customizer_control_scripts');

