<?php
/**
 * NOUVEAUX CONTRÔLES CUSTOMIZER À AJOUTER
 * 
 * Ce fichier contient tous les nouveaux contrôles Customizer pour les 50+ paramètres
 * ajoutés à GraphContainer.jsx et functions.php.
 * 
 * À INTÉGRER DANS: inc/customizer.php
 * 
 * Instructions:
 * 1. Ajouter ces sections après les sections existantes dans archi_customize_register()
 * 2. Respecter la structure avec add_setting() puis add_control()
 * 3. Tous utilisent 'transport' => 'postMessage' pour live preview
 */

// ========================================
// SECTION: PHYSIQUE DE LA SIMULATION
// ========================================
$wp_customize->add_section('archi_graph_simulation', [
    'title' => __('⚙️ Physique de la Simulation', 'archi-graph'),
    'description' => __('Contrôlez les forces et le comportement du graphe D3.js. Ces paramètres affectent comment les nœuds se repoussent et s\'attirent.', 'archi-graph'),
    'panel' => 'archi_graph_panel', // Utiliser le panel existant
    'priority' => 30,
]);

// Force de répulsion
$wp_customize->add_setting('archi_charge_strength', [
    'default' => -300,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_charge_strength', [
    'label' => __('Force de Répulsion', 'archi-graph'),
    'description' => __('Force négative : les nœuds se repoussent. Plus le nombre est négatif, plus la répulsion est forte.', 'archi-graph'),
    'section' => 'archi_graph_simulation',
    'type' => 'number',
    'input_attrs' => [
        'min' => -1000,
        'max' => -50,
        'step' => 10
    ]
]);

// Distance de répulsion
$wp_customize->add_setting('archi_charge_distance', [
    'default' => 200,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_charge_distance', [
    'label' => __('Distance de Répulsion', 'archi-graph'),
    'description' => __('Distance maximale à laquelle la répulsion agit (en pixels).', 'archi-graph'),
    'section' => 'archi_graph_simulation',
    'type' => 'number',
    'input_attrs' => [
        'min' => 50,
        'max' => 500,
        'step' => 10
    ]
]);

// Padding de collision
$wp_customize->add_setting('archi_collision_padding', [
    'default' => 10,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_collision_padding', [
    'label' => __('Espace entre Nœuds', 'archi-graph'),
    'description' => __('Espace minimal entre deux nœuds (en pixels).', 'archi-graph'),
    'section' => 'archi_graph_simulation',
    'type' => 'number',
    'input_attrs' => [
        'min' => 0,
        'max' => 50,
        'step' => 1
    ]
]);

// Alpha initial
$wp_customize->add_setting('archi_simulation_alpha', [
    'default' => 1,
    'transport' => 'postMessage',
    'sanitize_callback' => 'archi_sanitize_float'
]);

$wp_customize->add_control('archi_simulation_alpha', [
    'label' => __('Énergie Initiale', 'archi-graph'),
    'description' => __('Énergie de départ de la simulation (0-1). Plus c\'est élevé, plus le graphe s\'anime au chargement.', 'archi-graph'),
    'section' => 'archi_graph_simulation',
    'type' => 'number',
    'input_attrs' => [
        'min' => 0.1,
        'max' => 1,
        'step' => 0.1
    ]
]);

// Déclin d'alpha
$wp_customize->add_setting('archi_simulation_alpha_decay', [
    'default' => 0.02,
    'transport' => 'postMessage',
    'sanitize_callback' => 'archi_sanitize_float'
]);

$wp_customize->add_control('archi_simulation_alpha_decay', [
    'label' => __('Vitesse de Stabilisation', 'archi-graph'),
    'description' => __('Vitesse à laquelle la simulation se calme (0.01-0.1). Plus c\'est élevé, plus rapide.', 'archi-graph'),
    'section' => 'archi_graph_simulation',
    'type' => 'number',
    'input_attrs' => [
        'min' => 0.01,
        'max' => 0.1,
        'step' => 0.01
    ]
]);

// Déclin de vélocité
$wp_customize->add_setting('archi_simulation_velocity_decay', [
    'default' => 0.3,
    'transport' => 'postMessage',
    'sanitize_callback' => 'archi_sanitize_float'
]);

$wp_customize->add_control('archi_simulation_velocity_decay', [
    'label' => __('Amortissement', 'archi-graph'),
    'description' => __('Frein du mouvement (0-1). Plus c\'est élevé, plus les nœuds ralentissent vite.', 'archi-graph'),
    'section' => 'archi_graph_simulation',
    'type' => 'number',
    'input_attrs' => [
        'min' => 0,
        'max' => 1,
        'step' => 0.1
    ]
]);

// Alpha au resize
$wp_customize->add_setting('archi_resize_alpha', [
    'default' => 0.3,
    'transport' => 'postMessage',
    'sanitize_callback' => 'archi_sanitize_float'
]);

$wp_customize->add_control('archi_resize_alpha', [
    'label' => __('Énergie après Redimensionnement', 'archi-graph'),
    'description' => __('Énergie réinjectée quand la fenêtre est redimensionnée.', 'archi-graph'),
    'section' => 'archi_graph_simulation',
    'type' => 'number',
    'input_attrs' => [
        'min' => 0.1,
        'max' => 1,
        'step' => 0.1
    ]
]);

// ========================================
// SECTION: LIENS AVANCÉS
// ========================================
$wp_customize->add_section('archi_graph_links_advanced', [
    'title' => __('🔗 Liens Avancés', 'archi-graph'),
    'description' => __('Paramètres détaillés de la physique et de l\'apparence des liens entre nœuds.', 'archi-graph'),
    'panel' => 'archi_graph_panel',
    'priority' => 40,
]);

// Distance de lien
$wp_customize->add_setting('archi_link_distance', [
    'default' => 150,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_link_distance', [
    'label' => __('Distance de Lien', 'archi-graph'),
    'description' => __('Distance idéale entre nœuds connectés (en pixels).', 'archi-graph'),
    'section' => 'archi_graph_links_advanced',
    'type' => 'number',
    'input_attrs' => [
        'min' => 50,
        'max' => 300,
        'step' => 10
    ]
]);

// Variation de distance
$wp_customize->add_setting('archi_link_distance_variation', [
    'default' => 50,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_link_distance_variation', [
    'label' => __('Variation de Distance', 'archi-graph'),
    'description' => __('Ajustement selon la proximité des nœuds (en pixels).', 'archi-graph'),
    'section' => 'archi_graph_links_advanced',
    'type' => 'number',
    'input_attrs' => [
        'min' => 0,
        'max' => 200,
        'step' => 10
    ]
]);

// Diviseur de force
$wp_customize->add_setting('archi_link_strength_divisor', [
    'default' => 200,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_link_strength_divisor', [
    'label' => __('Force d\'Attraction', 'archi-graph'),
    'description' => __('Contrôle la force des liens. Plus grand = liens plus souples.', 'archi-graph'),
    'section' => 'archi_graph_links_advanced',
    'type' => 'number',
    'input_attrs' => [
        'min' => 50,
        'max' => 500,
        'step' => 10
    ]
]);

// Motif pointillé
$wp_customize->add_setting('archi_dashed_line_pattern', [
    'default' => '5,5',
    'transport' => 'postMessage',
    'sanitize_callback' => 'sanitize_text_field'
]);

$wp_customize->add_control('archi_dashed_line_pattern', [
    'label' => __('Motif Pointillé', 'archi-graph'),
    'description' => __('Pattern SVG pour les liens en pointillés (ex: "5,5" ou "10,3").', 'archi-graph'),
    'section' => 'archi_graph_links_advanced',
    'type' => 'text'
]);

// Motif points
$wp_customize->add_setting('archi_dotted_line_pattern', [
    'default' => '2,2',
    'transport' => 'postMessage',
    'sanitize_callback' => 'sanitize_text_field'
]);

$wp_customize->add_control('archi_dotted_line_pattern', [
    'label' => __('Motif Points', 'archi-graph'),
    'description' => __('Pattern SVG pour les liens en petits points (ex: "2,2" ou "1,3").', 'archi-graph'),
    'section' => 'archi_graph_links_advanced',
    'type' => 'text'
]);

// ========================================
// SECTION: LIENS LIVRE D'OR
// ========================================
$wp_customize->add_section('archi_graph_guestbook_links', [
    'title' => __('📖 Liens Livre d\'Or', 'archi-graph'),
    'description' => __('Apparence distinctive des liens vers le livre d\'or.', 'archi-graph'),
    'panel' => 'archi_graph_panel',
    'priority' => 45,
]);

// Couleur
$wp_customize->add_setting('archi_guestbook_link_color', [
    'default' => '#2ecc71',
    'transport' => 'postMessage',
    'sanitize_callback' => 'sanitize_hex_color'
]);

$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_guestbook_link_color', [
    'label' => __('Couleur des Liens', 'archi-graph'),
    'description' => __('Couleur distinctive pour les liens vers le livre d\'or.', 'archi-graph'),
    'section' => 'archi_graph_guestbook_links'
]));

// Épaisseur
$wp_customize->add_setting('archi_guestbook_link_width', [
    'default' => 3,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_guestbook_link_width', [
    'label' => __('Épaisseur', 'archi-graph'),
    'description' => __('Épaisseur en pixels (généralement plus épais que les liens standards).', 'archi-graph'),
    'section' => 'archi_graph_guestbook_links',
    'type' => 'number',
    'input_attrs' => [
        'min' => 1,
        'max' => 10,
        'step' => 0.5
    ]
]);

// Opacité
$wp_customize->add_setting('archi_guestbook_link_opacity', [
    'default' => 0.8,
    'transport' => 'postMessage',
    'sanitize_callback' => 'archi_sanitize_float'
]);

$wp_customize->add_control('archi_guestbook_link_opacity', [
    'label' => __('Opacité', 'archi-graph'),
    'description' => __('Transparence du lien (0-1).', 'archi-graph'),
    'section' => 'archi_graph_guestbook_links',
    'type' => 'number',
    'input_attrs' => [
        'min' => 0,
        'max' => 1,
        'step' => 0.1
    ]
]);

// Motif de tirets
$wp_customize->add_setting('archi_guestbook_dash_pattern', [
    'default' => '10,5',
    'transport' => 'postMessage',
    'sanitize_callback' => 'sanitize_text_field'
]);

$wp_customize->add_control('archi_guestbook_dash_pattern', [
    'label' => __('Motif de Tirets', 'archi-graph'),
    'description' => __('Pattern SVG unique pour ces liens (ex: "10,5").', 'archi-graph'),
    'section' => 'archi_graph_guestbook_links',
    'type' => 'text'
]);

// ========================================
// SECTION: BADGES DE PRIORITÉ
// ========================================
$wp_customize->add_section('archi_graph_priority_badges', [
    'title' => __('🎖️ Badges de Priorité', 'archi-graph'),
    'description' => __('Petits cercles colorés indiquant l\'importance des articles.', 'archi-graph'),
    'panel' => 'archi_graph_panel',
    'priority' => 50,
]);

// Décalage
$wp_customize->add_setting('archi_priority_badge_offset', [
    'default' => 5,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_priority_badge_offset', [
    'label' => __('Décalage depuis le Bord', 'archi-graph'),
    'description' => __('Distance depuis le bord du nœud (en pixels).', 'archi-graph'),
    'section' => 'archi_graph_priority_badges',
    'type' => 'number',
    'input_attrs' => [
        'min' => 0,
        'max' => 20,
        'step' => 1
    ]
]);

// Couleur du contour
$wp_customize->add_setting('archi_priority_badge_stroke_color', [
    'default' => '#ffffff',
    'transport' => 'postMessage',
    'sanitize_callback' => 'sanitize_hex_color'
]);

$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'archi_priority_badge_stroke_color', [
    'label' => __('Couleur du Contour', 'archi-graph'),
    'description' => __('Contour autour du badge.', 'archi-graph'),
    'section' => 'archi_graph_priority_badges'
]));

// Épaisseur du contour
$wp_customize->add_setting('archi_priority_badge_stroke_width', [
    'default' => 2,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_priority_badge_stroke_width', [
    'label' => __('Épaisseur du Contour', 'archi-graph'),
    'description' => __('Épaisseur en pixels.', 'archi-graph'),
    'section' => 'archi_graph_priority_badges',
    'type' => 'number',
    'input_attrs' => [
        'min' => 0,
        'max' => 5,
        'step' => 1
    ]
]);

// ========================================
// SECTION: CLUSTERS
// ========================================
$wp_customize->add_section('archi_graph_clusters', [
    'title' => __('🌐 Clusters', 'archi-graph'),
    'description' => __('Enveloppes colorées regroupant les articles par catégorie.', 'archi-graph'),
    'panel' => 'archi_graph_panel',
    'priority' => 55,
]);

// Opacité de remplissage
$wp_customize->add_setting('archi_cluster_fill_opacity', [
    'default' => 0.12,
    'transport' => 'postMessage',
    'sanitize_callback' => 'archi_sanitize_float'
]);

$wp_customize->add_control('archi_cluster_fill_opacity', [
    'label' => __('Opacité du Fond', 'archi-graph'),
    'description' => __('Transparence du remplissage (0-1).', 'archi-graph'),
    'section' => 'archi_graph_clusters',
    'type' => 'number',
    'input_attrs' => [
        'min' => 0,
        'max' => 1,
        'step' => 0.05
    ]
]);

// Épaisseur du contour
$wp_customize->add_setting('archi_cluster_stroke_width', [
    'default' => 3,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_cluster_stroke_width', [
    'label' => __('Épaisseur du Contour', 'archi-graph'),
    'description' => __('Pixels du contour.', 'archi-graph'),
    'section' => 'archi_graph_clusters',
    'type' => 'number',
    'input_attrs' => [
        'min' => 1,
        'max' => 10,
        'step' => 1
    ]
]);

// Opacité du contour
$wp_customize->add_setting('archi_cluster_stroke_opacity', [
    'default' => 0.35,
    'transport' => 'postMessage',
    'sanitize_callback' => 'archi_sanitize_float'
]);

$wp_customize->add_control('archi_cluster_stroke_opacity', [
    'label' => __('Opacité du Contour', 'archi-graph'),
    'description' => __('Transparence du contour (0-1).', 'archi-graph'),
    'section' => 'archi_graph_clusters',
    'type' => 'number',
    'input_attrs' => [
        'min' => 0,
        'max' => 1,
        'step' => 0.05
    ]
]);

// Taille police label
$wp_customize->add_setting('archi_cluster_label_font_size', [
    'default' => 14,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_cluster_label_font_size', [
    'label' => __('Taille Label', 'archi-graph'),
    'description' => __('Taille en pixels du nom du cluster.', 'archi-graph'),
    'section' => 'archi_graph_clusters',
    'type' => 'number',
    'input_attrs' => [
        'min' => 10,
        'max' => 24,
        'step' => 1
    ]
]);

// Poids police label
$wp_customize->add_setting('archi_cluster_label_font_weight', [
    'default' => 'bold',
    'transport' => 'postMessage',
    'sanitize_callback' => 'sanitize_text_field'
]);

$wp_customize->add_control('archi_cluster_label_font_weight', [
    'label' => __('Poids du Label', 'archi-graph'),
    'section' => 'archi_graph_clusters',
    'type' => 'select',
    'choices' => [
        'normal' => __('Normal', 'archi-graph'),
        '600' => __('Semi-Bold', 'archi-graph'),
        'bold' => __('Bold', 'archi-graph'),
        '800' => __('Extra-Bold', 'archi-graph')
    ]
]);

// Taille compteur
$wp_customize->add_setting('archi_cluster_count_font_size', [
    'default' => 11,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_cluster_count_font_size', [
    'label' => __('Taille Compteur', 'archi-graph'),
    'description' => __('Taille du nombre d\'articles.', 'archi-graph'),
    'section' => 'archi_graph_clusters',
    'type' => 'number',
    'input_attrs' => [
        'min' => 8,
        'max' => 18,
        'step' => 1
    ]
]);

// Opacité compteur
$wp_customize->add_setting('archi_cluster_count_opacity', [
    'default' => 0.7,
    'transport' => 'postMessage',
    'sanitize_callback' => 'archi_sanitize_float'
]);

$wp_customize->add_control('archi_cluster_count_opacity', [
    'label' => __('Opacité Compteur', 'archi-graph'),
    'section' => 'archi_graph_clusters',
    'type' => 'number',
    'input_attrs' => [
        'min' => 0,
        'max' => 1,
        'step' => 0.1
    ]
]);

// Ombre du texte
$wp_customize->add_setting('archi_cluster_text_shadow', [
    'default' => '2px 2px 4px rgba(255,255,255,0.8)',
    'transport' => 'postMessage',
    'sanitize_callback' => 'sanitize_text_field'
]);

$wp_customize->add_control('archi_cluster_text_shadow', [
    'label' => __('Ombre du Texte', 'archi-graph'),
    'description' => __('CSS text-shadow (ex: "2px 2px 4px rgba(255,255,255,0.8)").', 'archi-graph'),
    'section' => 'archi_graph_clusters',
    'type' => 'text'
]);

// Padding de l'enveloppe
$wp_customize->add_setting('archi_cluster_hull_padding', [
    'default' => 40,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_cluster_hull_padding', [
    'label' => __('Padding de l\'Enveloppe', 'archi-graph'),
    'description' => __('Espace autour des nœuds (en pixels).', 'archi-graph'),
    'section' => 'archi_graph_clusters',
    'type' => 'number',
    'input_attrs' => [
        'min' => 20,
        'max' => 100,
        'step' => 5
    ]
]);

// Rayon du cercle
$wp_customize->add_setting('archi_cluster_circle_radius', [
    'default' => 80,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_cluster_circle_radius', [
    'label' => __('Rayon du Cercle', 'archi-graph'),
    'description' => __('Si moins de 3 nœuds (en pixels).', 'archi-graph'),
    'section' => 'archi_graph_clusters',
    'type' => 'number',
    'input_attrs' => [
        'min' => 40,
        'max' => 150,
        'step' => 5
    ]
]);

// Points du cercle
$wp_customize->add_setting('archi_cluster_circle_points', [
    'default' => 12,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_cluster_circle_points', [
    'label' => __('Points du Cercle', 'archi-graph'),
    'description' => __('Nombre de points pour lisser le cercle.', 'archi-graph'),
    'section' => 'archi_graph_clusters',
    'type' => 'number',
    'input_attrs' => [
        'min' => 6,
        'max' => 24,
        'step' => 1
    ]
]);

// ========================================
// SECTION: ÎLES ARCHITECTURALES
// ========================================
$wp_customize->add_section('archi_graph_islands', [
    'title' => __('🏝️ Îles Architecturales', 'archi-graph'),
    'description' => __('Zones visuelles regroupant projets, illustrations et pages avec texture distinctive.', 'archi-graph'),
    'panel' => 'archi_graph_panel',
    'priority' => 60,
]);

// Padding de l'enveloppe
$wp_customize->add_setting('archi_island_hull_padding', [
    'default' => 60,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_island_hull_padding', [
    'label' => __('Padding de l\'Enveloppe', 'archi-graph'),
    'description' => __('Espace généreux autour des nœuds (en pixels).', 'archi-graph'),
    'section' => 'archi_graph_islands',
    'type' => 'number',
    'input_attrs' => [
        'min' => 30,
        'max' => 150,
        'step' => 5
    ]
]);

// Facteur de lissage
$wp_customize->add_setting('archi_island_smooth_factor', [
    'default' => 0.3,
    'transport' => 'postMessage',
    'sanitize_callback' => 'archi_sanitize_float'
]);

$wp_customize->add_control('archi_island_smooth_factor', [
    'label' => __('Facteur de Lissage', 'archi-graph'),
    'description' => __('Arrondi des coins (0-1). Plus élevé = plus arrondi.', 'archi-graph'),
    'section' => 'archi_graph_islands',
    'type' => 'number',
    'input_attrs' => [
        'min' => 0,
        'max' => 1,
        'step' => 0.1
    ]
]);

// Rayon du cercle
$wp_customize->add_setting('archi_island_circle_radius', [
    'default' => 80,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_island_circle_radius', [
    'label' => __('Rayon du Cercle', 'archi-graph'),
    'description' => __('Si moins de 3 nœuds (en pixels).', 'archi-graph'),
    'section' => 'archi_graph_islands',
    'type' => 'number',
    'input_attrs' => [
        'min' => 40,
        'max' => 150,
        'step' => 5
    ]
]);

// Points du cercle
$wp_customize->add_setting('archi_island_circle_points', [
    'default' => 12,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_island_circle_points', [
    'label' => __('Points du Cercle', 'archi-graph'),
    'section' => 'archi_graph_islands',
    'type' => 'number',
    'input_attrs' => [
        'min' => 6,
        'max' => 24,
        'step' => 1
    ]
]);

// Padding interne
$wp_customize->add_setting('archi_island_inner_padding', [
    'default' => -20,
    'transport' => 'postMessage',
    'sanitize_callback' => 'intval'
]);

$wp_customize->add_control('archi_island_inner_padding', [
    'label' => __('Padding Interne Texture', 'archi-graph'),
    'description' => __('Décalage de la texture interne (peut être négatif).', 'archi-graph'),
    'section' => 'archi_graph_islands',
    'type' => 'number',
    'input_attrs' => [
        'min' => -50,
        'max' => 50,
        'step' => 5
    ]
]);

// Motif du contour
$wp_customize->add_setting('archi_island_stroke_dash_array', [
    'default' => '8,4',
    'transport' => 'postMessage',
    'sanitize_callback' => 'sanitize_text_field'
]);

$wp_customize->add_control('archi_island_stroke_dash_array', [
    'label' => __('Motif du Contour', 'archi-graph'),
    'description' => __('Pattern SVG du contour (ex: "8,4").', 'archi-graph'),
    'section' => 'archi_graph_islands',
    'type' => 'text'
]);

// Taille police label
$wp_customize->add_setting('archi_island_label_font_size', [
    'default' => 14,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_island_label_font_size', [
    'label' => __('Taille Label', 'archi-graph'),
    'section' => 'archi_graph_islands',
    'type' => 'number',
    'input_attrs' => [
        'min' => 10,
        'max' => 24,
        'step' => 1
    ]
]);

// Poids police label
$wp_customize->add_setting('archi_island_label_font_weight', [
    'default' => '600',
    'transport' => 'postMessage',
    'sanitize_callback' => 'sanitize_text_field'
]);

$wp_customize->add_control('archi_island_label_font_weight', [
    'label' => __('Poids Label', 'archi-graph'),
    'section' => 'archi_graph_islands',
    'type' => 'select',
    'choices' => [
        'normal' => __('Normal', 'archi-graph'),
        '600' => __('Semi-Bold', 'archi-graph'),
        'bold' => __('Bold', 'archi-graph'),
        '800' => __('Extra-Bold', 'archi-graph')
    ]
]);

// Opacité label
$wp_customize->add_setting('archi_island_label_opacity', [
    'default' => 0.7,
    'transport' => 'postMessage',
    'sanitize_callback' => 'archi_sanitize_float'
]);

$wp_customize->add_control('archi_island_label_opacity', [
    'label' => __('Opacité Label', 'archi-graph'),
    'section' => 'archi_graph_islands',
    'type' => 'number',
    'input_attrs' => [
        'min' => 0,
        'max' => 1,
        'step' => 0.1
    ]
]);

// Décalage Y
$wp_customize->add_setting('archi_island_label_y_offset', [
    'default' => -10,
    'transport' => 'postMessage',
    'sanitize_callback' => 'intval'
]);

$wp_customize->add_control('archi_island_label_y_offset', [
    'label' => __('Décalage Vertical Label', 'archi-graph'),
    'description' => __('Position verticale (peut être négatif).', 'archi-graph'),
    'section' => 'archi_graph_islands',
    'type' => 'number',
    'input_attrs' => [
        'min' => -50,
        'max' => 50,
        'step' => 5
    ]
]);

// Ombre texte
$wp_customize->add_setting('archi_island_text_shadow', [
    'default' => '2px 2px 6px rgba(255,255,255,0.9)',
    'transport' => 'postMessage',
    'sanitize_callback' => 'sanitize_text_field'
]);

$wp_customize->add_control('archi_island_text_shadow', [
    'label' => __('Ombre du Texte', 'archi-graph'),
    'description' => __('CSS text-shadow.', 'archi-graph'),
    'section' => 'archi_graph_islands',
    'type' => 'text'
]);

// Taille compteur
$wp_customize->add_setting('archi_island_count_font_size', [
    'default' => 11,
    'transport' => 'postMessage',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_control('archi_island_count_font_size', [
    'label' => __('Taille Compteur', 'archi-graph'),
    'section' => 'archi_graph_islands',
    'type' => 'number',
    'input_attrs' => [
        'min' => 8,
        'max' => 18,
        'step' => 1
    ]
]);

// Opacité compteur
$wp_customize->add_setting('archi_island_count_opacity', [
    'default' => 0.6,
    'transport' => 'postMessage',
    'sanitize_callback' => 'archi_sanitize_float'
]);

$wp_customize->add_control('archi_island_count_opacity', [
    'label' => __('Opacité Compteur', 'archi-graph'),
    'section' => 'archi_graph_islands',
    'type' => 'number',
    'input_attrs' => [
        'min' => 0,
        'max' => 1,
        'step' => 0.1
    ]
]);

// Opacité texture
$wp_customize->add_setting('archi_island_texture_opacity', [
    'default' => 0.15,
    'transport' => 'postMessage',
    'sanitize_callback' => 'archi_sanitize_float'
]);

$wp_customize->add_control('archi_island_texture_opacity', [
    'label' => __('Opacité Texture', 'archi-graph'),
    'description' => __('Transparence des lignes internes.', 'archi-graph'),
    'section' => 'archi_graph_islands',
    'type' => 'number',
    'input_attrs' => [
        'min' => 0,
        'max' => 1,
        'step' => 0.05
    ]
]);

// Motif texture
$wp_customize->add_setting('archi_island_texture_dash_array', [
    'default' => '3,3',
    'transport' => 'postMessage',
    'sanitize_callback' => 'sanitize_text_field'
]);

$wp_customize->add_control('archi_island_texture_dash_array', [
    'label' => __('Motif Texture', 'archi-graph'),
    'description' => __('Pattern SVG des lignes internes.', 'archi-graph'),
    'section' => 'archi_graph_islands',
    'type' => 'text'
]);

// ========================================
// FONCTION DE SANITIZATION POUR FLOAT
// ========================================
/**
 * Sanitize float values
 */
function archi_sanitize_float($value) {
    return floatval($value);
}
