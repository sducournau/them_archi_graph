<?php
/**
 * Enregistrement des Custom Post Types pour le thème Archi Graph
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistrer le Custom Post Type : Projets Architecturaux
 */
function archi_register_project_post_type() {
    $labels = [
        'name'                  => __('Projets Architecturaux', 'archi-graph'),
        'singular_name'         => __('Projet Architectural', 'archi-graph'),
        'menu_name'             => __('Projets Archi', 'archi-graph'),
        'add_new'               => __('Ajouter', 'archi-graph'),
        'add_new_item'          => __('Ajouter un projet', 'archi-graph'),
        'edit_item'             => __('Modifier le projet', 'archi-graph'),
        'new_item'              => __('Nouveau projet', 'archi-graph'),
        'view_item'             => __('Voir le projet', 'archi-graph'),
        'search_items'          => __('Rechercher des projets', 'archi-graph'),
        'not_found'             => __('Aucun projet trouvé', 'archi-graph'),
        'not_found_in_trash'    => __('Aucun projet dans la corbeille', 'archi-graph'),
        'all_items'             => __('Tous les projets', 'archi-graph'),
    ];

    $args = [
        'labels'                => $labels,
        'description'           => __('Projets architecturaux complets avec métadonnées détaillées (surface, coût, localisation, client, etc.). Ce type de contenu est conçu pour présenter des réalisations architecturales dans le portfolio et les intégrer dans le graphique de relations. Chaque projet peut être lié à des illustrations, articles et autres projets pour créer un réseau visuel interactif de votre travail architectural.', 'archi-graph'),
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_rest'          => true,
        'rest_base'             => 'projets-archi',
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-building',
        'supports'              => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'author', 'comments'],
        'has_archive'           => true,
        'rewrite'               => ['slug' => 'projet', 'with_front' => false],
        'capability_type'       => 'post',
        'hierarchical'          => false,
        'taxonomies'            => ['category', 'post_tag'],
        'show_in_nav_menus'     => true,
        'can_export'            => true,
    ];

    register_post_type('archi_project', $args);
}
add_action('init', 'archi_register_project_post_type');

/**
 * Enregistrer les taxonomies pour les projets architecturaux
 */
function archi_register_project_taxonomies() {
    // Type de projet
    $labels_type = [
        'name'              => __('Types de Projet', 'archi-graph'),
        'singular_name'     => __('Type de Projet', 'archi-graph'),
        'search_items'      => __('Rechercher des types', 'archi-graph'),
        'all_items'         => __('Tous les types', 'archi-graph'),
        'parent_item'       => __('Type parent', 'archi-graph'),
        'parent_item_colon' => __('Type parent:', 'archi-graph'),
        'edit_item'         => __('Modifier le type', 'archi-graph'),
        'update_item'       => __('Mettre à jour le type', 'archi-graph'),
        'add_new_item'      => __('Ajouter un nouveau type', 'archi-graph'),
        'new_item_name'     => __('Nom du nouveau type', 'archi-graph'),
        'menu_name'         => __('Types de Projet', 'archi-graph'),
    ];

    $args_type = [
        'hierarchical'      => true,
        'labels'            => $labels_type,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'type-projet'],
    ];

    register_taxonomy('archi_project_type', ['archi_project'], $args_type);

    // Statut du projet
    $labels_status = [
        'name'              => __('Statuts de Projet', 'archi-graph'),
        'singular_name'     => __('Statut de Projet', 'archi-graph'),
        'search_items'      => __('Rechercher des statuts', 'archi-graph'),
        'all_items'         => __('Tous les statuts', 'archi-graph'),
        'edit_item'         => __('Modifier le statut', 'archi-graph'),
        'update_item'       => __('Mettre à jour le statut', 'archi-graph'),
        'add_new_item'      => __('Ajouter un nouveau statut', 'archi-graph'),
        'new_item_name'     => __('Nom du nouveau statut', 'archi-graph'),
        'menu_name'         => __('Statuts', 'archi-graph'),
    ];

    $args_status = [
        'hierarchical'      => false,
        'labels'            => $labels_status,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'statut-projet'],
    ];

    register_taxonomy('archi_project_status', ['archi_project'], $args_status);
}
add_action('init', 'archi_register_project_taxonomies');

/**
 * Enregistrer le Custom Post Type : Illustrations
 */
function archi_register_illustration_post_type() {
    $labels = [
        'name'                  => __('Illustrations', 'archi-graph'),
        'singular_name'         => __('Illustration', 'archi-graph'),
        'menu_name'             => __('Illustrations', 'archi-graph'),
        'add_new'               => __('Ajouter', 'archi-graph'),
        'add_new_item'          => __('Ajouter une illustration', 'archi-graph'),
        'edit_item'             => __('Modifier l\'illustration', 'archi-graph'),
        'new_item'              => __('Nouvelle illustration', 'archi-graph'),
        'view_item'             => __('Voir l\'illustration', 'archi-graph'),
        'search_items'          => __('Rechercher des illustrations', 'archi-graph'),
        'not_found'             => __('Aucune illustration trouvée', 'archi-graph'),
        'not_found_in_trash'    => __('Aucune illustration dans la corbeille', 'archi-graph'),
        'all_items'             => __('Toutes les illustrations', 'archi-graph'),
    ];

    $args = [
        'labels'                => $labels,
        'description'           => __('Illustrations, explorations graphiques, croquis et visualisations architecturales. Ce type de contenu permet de présenter vos créations visuelles avec des métadonnées spécifiques (technique utilisée, dimensions, logiciels, support, etc.). Les illustrations peuvent être intégrées dans le graphique de relations pour montrer les liens créatifs entre vos différents travaux artistiques, projets architecturaux et articles de réflexion.', 'archi-graph'),
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_rest'          => true,
        'rest_base'             => 'illustrations',
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-images-alt2',
        'supports'              => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'author'],
        'has_archive'           => true,
        'rewrite'               => ['slug' => 'illustration', 'with_front' => false],
        'capability_type'       => 'post',
        'hierarchical'          => false,
        'taxonomies'            => ['category', 'post_tag', 'illustration_type'],
        'show_in_nav_menus'     => true,
        'can_export'            => true,
    ];

    register_post_type('archi_illustration', $args);
}
add_action('init', 'archi_register_illustration_post_type');

/**
 * Enregistrer la taxonomie personnalisée pour les illustrations
 */
function archi_register_illustration_taxonomy() {
    $labels = [
        'name'              => __('Types d\'illustration', 'archi-graph'),
        'singular_name'     => __('Type d\'illustration', 'archi-graph'),
        'search_items'      => __('Rechercher des types', 'archi-graph'),
        'all_items'         => __('Tous les types', 'archi-graph'),
        'parent_item'       => __('Type parent', 'archi-graph'),
        'parent_item_colon' => __('Type parent:', 'archi-graph'),
        'edit_item'         => __('Modifier le type', 'archi-graph'),
        'update_item'       => __('Mettre à jour le type', 'archi-graph'),
        'add_new_item'      => __('Ajouter un nouveau type', 'archi-graph'),
        'new_item_name'     => __('Nom du nouveau type', 'archi-graph'),
        'menu_name'         => __('Types', 'archi-graph'),
    ];

    $args = [
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'type-illustration'],
    ];

    register_taxonomy('illustration_type', ['archi_illustration'], $args);
}
add_action('init', 'archi_register_illustration_taxonomy');

/**
 * Personnaliser les labels du post type 'post' standard
 * Pour mieux expliquer son usage dans le contexte du thème
 */
function archi_customize_standard_post_type() {
    global $wp_post_types;
    
    if (isset($wp_post_types['post'])) {
        // Mettre à jour la description du post type standard
        $wp_post_types['post']->description = __('Articles de blog, actualités et publications textuelles. Utilisez ce type pour le contenu éditorial standard, les réflexions architecturales et les actualités du studio. Ces articles peuvent être affichés dans le graphique interactif pour créer des connexions thématiques avec les projets et illustrations.', 'archi-graph');
        
        // Optionnel : personnaliser les labels pour plus de clarté
        $wp_post_types['post']->labels->name = __('Articles & Blog', 'archi-graph');
        $wp_post_types['post']->labels->menu_name = __('Articles & Blog', 'archi-graph');
        $wp_post_types['post']->labels->add_new_item = __('Ajouter un article', 'archi-graph');
        $wp_post_types['post']->labels->edit_item = __('Modifier l\'article', 'archi-graph');
        $wp_post_types['post']->labels->view_item = __('Voir l\'article', 'archi-graph');
    }
}
add_action('init', 'archi_customize_standard_post_type', 11); // Priorité 11 pour s'exécuter après l'enregistrement

/**
 * Ajouter des meta boxes personnalisées pour les custom post types
 */
function archi_add_custom_meta_boxes() {
    $post_types = ['archi_project', 'archi_illustration', 'post'];
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'archi_graph_settings',
            __('Paramètres du Graphique', 'archi-graph'),
            'archi_graph_meta_box_callback',
            $post_type,
            'side',
            'default'
        );
        
        if ($post_type === 'archi_illustration') {
            add_meta_box(
                'archi_illustration_settings',
                __('Paramètres de l\'illustration', 'archi-graph'),
                'archi_illustration_meta_box_callback',
                $post_type,
                'normal',
                'high'
            );
        }
        
        if ($post_type === 'archi_project') {
            add_meta_box(
                'archi_project_settings',
                __('Détails du Projet', 'archi-graph'),
                'archi_project_meta_box_callback',
                $post_type,
                'normal',
                'high'
            );
        }
    }
}
add_action('add_meta_boxes', 'archi_add_custom_meta_boxes');

/**
 * Callback pour la meta box des illustrations
 */
function archi_illustration_meta_box_callback($post) {
    wp_nonce_field('archi_illustration_meta_box', 'archi_illustration_meta_box_nonce');
    
    $technique = get_post_meta($post->ID, '_archi_illustration_technique', true);
    $dimensions = get_post_meta($post->ID, '_archi_illustration_dimensions', true);
    $software = get_post_meta($post->ID, '_archi_illustration_software', true);
    $project_link = get_post_meta($post->ID, '_archi_illustration_project_link', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="archi_illustration_technique"><?php _e('Technique:', 'archi-graph'); ?></label></th>
            <td>
                <input type="text" 
                       id="archi_illustration_technique" 
                       name="archi_illustration_technique" 
                       value="<?php echo esc_attr($technique); ?>"
                       class="regular-text"
                       placeholder="Ex: 3D, Aquarelle, Numérique...">
            </td>
        </tr>
        <tr>
            <th><label for="archi_illustration_dimensions"><?php _e('Dimensions:', 'archi-graph'); ?></label></th>
            <td>
                <input type="text" 
                       id="archi_illustration_dimensions" 
                       name="archi_illustration_dimensions" 
                       value="<?php echo esc_attr($dimensions); ?>"
                       class="regular-text"
                       placeholder="Ex: 1920x1080px, A4, etc.">
            </td>
        </tr>
        <tr>
            <th><label for="archi_illustration_software"><?php _e('Logiciels utilisés:', 'archi-graph'); ?></label></th>
            <td>
                <input type="text" 
                       id="archi_illustration_software" 
                       name="archi_illustration_software" 
                       value="<?php echo esc_attr($software); ?>"
                       class="regular-text"
                       placeholder="Ex: Photoshop, Blender, SketchUp...">
            </td>
        </tr>
        <tr>
            <th><label for="archi_illustration_project_link"><?php _e('Lien vers le projet:', 'archi-graph'); ?></label></th>
            <td>
                <input type="url" 
                       id="archi_illustration_project_link" 
                       name="archi_illustration_project_link" 
                       value="<?php echo esc_url($project_link); ?>"
                       class="regular-text"
                       placeholder="https://">
                <p class="description"><?php _e('Lien vers le projet associé à cette illustration', 'archi-graph'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Callback pour la meta box des projets architecturaux
 */
function archi_project_meta_box_callback($post) {
    wp_nonce_field('archi_project_meta_box', 'archi_project_meta_box_nonce');
    
    $surface = get_post_meta($post->ID, '_archi_project_surface', true);
    $cost = get_post_meta($post->ID, '_archi_project_cost', true);
    $client = get_post_meta($post->ID, '_archi_project_client', true);
    $location = get_post_meta($post->ID, '_archi_project_location', true);
    $start_date = get_post_meta($post->ID, '_archi_project_start_date', true);
    $end_date = get_post_meta($post->ID, '_archi_project_end_date', true);
    $bet = get_post_meta($post->ID, '_archi_project_bet', true);
    $certifications = get_post_meta($post->ID, '_archi_project_certifications', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="archi_project_surface"><?php _e('Surface (m²):', 'archi-graph'); ?></label></th>
            <td>
                <input type="number" 
                       id="archi_project_surface" 
                       name="archi_project_surface" 
                       value="<?php echo esc_attr($surface); ?>"
                       class="regular-text"
                       min="0"
                       step="0.01"
                       placeholder="Ex: 1500">
            </td>
        </tr>
        <tr>
            <th><label for="archi_project_cost"><?php _e('Coût estimé (€):', 'archi-graph'); ?></label></th>
            <td>
                <input type="number" 
                       id="archi_project_cost" 
                       name="archi_project_cost" 
                       value="<?php echo esc_attr($cost); ?>"
                       class="regular-text"
                       min="0"
                       step="1"
                       placeholder="Ex: 500000">
            </td>
        </tr>
        <tr>
            <th><label for="archi_project_client"><?php _e('Maîtrise d\'ouvrage:', 'archi-graph'); ?></label></th>
            <td>
                <input type="text" 
                       id="archi_project_client" 
                       name="archi_project_client" 
                       value="<?php echo esc_attr($client); ?>"
                       class="regular-text"
                       placeholder="Nom du client ou maître d'ouvrage">
            </td>
        </tr>
        <tr>
            <th><label for="archi_project_location"><?php _e('Localisation:', 'archi-graph'); ?></label></th>
            <td>
                <input type="text" 
                       id="archi_project_location" 
                       name="archi_project_location" 
                       value="<?php echo esc_attr($location); ?>"
                       class="regular-text"
                       placeholder="Ville, Région, Pays">
            </td>
        </tr>
        <tr>
            <th><label for="archi_project_start_date"><?php _e('Date de début:', 'archi-graph'); ?></label></th>
            <td>
                <input type="date" 
                       id="archi_project_start_date" 
                       name="archi_project_start_date" 
                       value="<?php echo esc_attr($start_date); ?>"
                       class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="archi_project_end_date"><?php _e('Date de fin (prévue ou réelle):', 'archi-graph'); ?></label></th>
            <td>
                <input type="date" 
                       id="archi_project_end_date" 
                       name="archi_project_end_date" 
                       value="<?php echo esc_attr($end_date); ?>"
                       class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="archi_project_bet"><?php _e('BET (Bureau d\'Études Techniques):', 'archi-graph'); ?></label></th>
            <td>
                <input type="text" 
                       id="archi_project_bet" 
                       name="archi_project_bet" 
                       value="<?php echo esc_attr($bet); ?>"
                       class="regular-text"
                       placeholder="Nom du BET">
            </td>
        </tr>
        <tr>
            <th><label for="archi_project_certifications"><?php _e('Certifications:', 'archi-graph'); ?></label></th>
            <td>
                <textarea 
                       id="archi_project_certifications" 
                       name="archi_project_certifications" 
                       class="large-text"
                       rows="3"
                       placeholder="Ex: HQE, BREEAM, LEED, BBC, etc."><?php echo esc_textarea($certifications); ?></textarea>
                <p class="description"><?php _e('Listez les certifications obtenues ou en cours', 'archi-graph'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Sauvegarder les meta boxes
 */
function archi_save_custom_meta_boxes($post_id) {
    // Vérifications de sécurité communes
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // NOTE: Les paramètres du graphique (show_in_graph, node_color, node_size, node_shape, priority_level)
    // sont gérés dans inc/meta-boxes.php avec validation appropriée des plages de valeurs.
    // Ne pas dupliquer ici pour éviter les écrasements de valeurs.
    
    // Sauvegarder les paramètres des illustrations
    if (isset($_POST['archi_illustration_meta_box_nonce']) && 
        wp_verify_nonce($_POST['archi_illustration_meta_box_nonce'], 'archi_illustration_meta_box')) {
        
        if (isset($_POST['archi_illustration_technique'])) {
            update_post_meta($post_id, '_archi_illustration_technique', sanitize_text_field($_POST['archi_illustration_technique']));
        }
        
        if (isset($_POST['archi_illustration_dimensions'])) {
            update_post_meta($post_id, '_archi_illustration_dimensions', sanitize_text_field($_POST['archi_illustration_dimensions']));
        }
        
        if (isset($_POST['archi_illustration_software'])) {
            update_post_meta($post_id, '_archi_illustration_software', sanitize_text_field($_POST['archi_illustration_software']));
        }
        
        if (isset($_POST['archi_illustration_project_link'])) {
            update_post_meta($post_id, '_archi_illustration_project_link', esc_url_raw($_POST['archi_illustration_project_link']));
        }
    }
    
    // Sauvegarder les paramètres des projets
    if (isset($_POST['archi_project_meta_box_nonce']) && 
        wp_verify_nonce($_POST['archi_project_meta_box_nonce'], 'archi_project_meta_box')) {
        
        if (isset($_POST['archi_project_surface'])) {
            update_post_meta($post_id, '_archi_project_surface', floatval($_POST['archi_project_surface']));
        }
        
        if (isset($_POST['archi_project_cost'])) {
            update_post_meta($post_id, '_archi_project_cost', absint($_POST['archi_project_cost']));
        }
        
        if (isset($_POST['archi_project_client'])) {
            update_post_meta($post_id, '_archi_project_client', sanitize_text_field($_POST['archi_project_client']));
        }
        
        if (isset($_POST['archi_project_location'])) {
            update_post_meta($post_id, '_archi_project_location', sanitize_text_field($_POST['archi_project_location']));
        }
        
        if (isset($_POST['archi_project_start_date'])) {
            update_post_meta($post_id, '_archi_project_start_date', sanitize_text_field($_POST['archi_project_start_date']));
        }
        
        if (isset($_POST['archi_project_end_date'])) {
            update_post_meta($post_id, '_archi_project_end_date', sanitize_text_field($_POST['archi_project_end_date']));
        }
        
        if (isset($_POST['archi_project_bet'])) {
            update_post_meta($post_id, '_archi_project_bet', sanitize_text_field($_POST['archi_project_bet']));
        }
        
        if (isset($_POST['archi_project_certifications'])) {
            update_post_meta($post_id, '_archi_project_certifications', sanitize_textarea_field($_POST['archi_project_certifications']));
        }
    }
}
add_action('save_post', 'archi_save_custom_meta_boxes');

/**
 * Ajouter des colonnes personnalisées dans la liste des posts
 */
function archi_add_custom_columns($columns) {
    $new_columns = [];
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['show_in_graph'] = __('Dans le graphique', 'archi-graph');
            $new_columns['node_color'] = __('Couleur', 'archi-graph');
        }
    }
    return $new_columns;
}
add_filter('manage_archi_project_posts_columns', 'archi_add_custom_columns');
add_filter('manage_archi_illustration_posts_columns', 'archi_add_custom_columns');
add_filter('manage_posts_columns', 'archi_add_custom_columns');

/**
 * Remplir les colonnes personnalisées
 */
function archi_fill_custom_columns($column, $post_id) {
    switch ($column) {
        case 'show_in_graph':
            $show = get_post_meta($post_id, '_archi_show_in_graph', true);
            echo $show === '1' ? '✓' : '—';
            break;
            
        case 'node_color':
            $color = get_post_meta($post_id, '_archi_node_color', true) ?: '#3498db';
            echo '<span style="display:inline-block;width:20px;height:20px;background:' . esc_attr($color) . ';border:1px solid #ccc;border-radius:3px;"></span>';
            break;
    }
}
add_action('manage_archi_project_posts_custom_column', 'archi_fill_custom_columns', 10, 2);
add_action('manage_archi_illustration_posts_custom_column', 'archi_fill_custom_columns', 10, 2);
add_action('manage_archi_guestbook_posts_custom_column', 'archi_fill_custom_columns', 10, 2);
add_action('manage_posts_custom_column', 'archi_fill_custom_columns', 10, 2);

/**
 * Enregistrer le Custom Post Type : Livre d'Or
 */
function archi_register_guestbook_post_type() {
    $labels = [
        'name'                  => __('Livre d\'Or', 'archi-graph'),
        'singular_name'         => __('Entrée Livre d\'Or', 'archi-graph'),
        'menu_name'             => __('Livre d\'Or', 'archi-graph'),
        'add_new'               => __('Ajouter', 'archi-graph'),
        'add_new_item'          => __('Ajouter une entrée', 'archi-graph'),
        'edit_item'             => __('Modifier l\'entrée', 'archi-graph'),
        'new_item'              => __('Nouvelle entrée', 'archi-graph'),
        'view_item'             => __('Voir l\'entrée', 'archi-graph'),
        'search_items'          => __('Rechercher des entrées', 'archi-graph'),
        'not_found'             => __('Aucune entrée trouvée', 'archi-graph'),
        'not_found_in_trash'    => __('Aucune entrée dans la corbeille', 'archi-graph'),
        'all_items'             => __('Toutes les entrées', 'archi-graph'),
    ];

    $args = [
        'labels'                => $labels,
        'description'           => __('Entrées du livre d\'or où les visiteurs peuvent laisser leurs commentaires et impressions. Ces entrées peuvent être liées à des projets ou articles spécifiques et affichées dans le graphique de relations comme des nœuds connectés.', 'archi-graph'),
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_rest'          => true,
        'rest_base'             => 'livre-or',
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-book-alt',
        'supports'              => ['title', 'editor', 'custom-fields', 'author'],
        'has_archive'           => true,
        'rewrite'               => ['slug' => 'livre-or', 'with_front' => false],
        'capability_type'       => 'post',
        'hierarchical'          => false,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
    ];

    register_post_type('archi_guestbook', $args);
}
add_action('init', 'archi_register_guestbook_post_type');

/**
 * Ajouter des colonnes personnalisées pour le livre d'or
 */
function archi_add_guestbook_columns($columns) {
    $new_columns = [];
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['guestbook_author_name'] = __('Auteur', 'archi-graph');
            $new_columns['linked_article'] = __('Article lié', 'archi-graph');
            $new_columns['show_in_graph'] = __('Graphique', 'archi-graph');
        }
    }
    return $new_columns;
}
add_filter('manage_archi_guestbook_posts_columns', 'archi_add_guestbook_columns');

/**
 * Remplir les colonnes personnalisées du livre d'or
 */
function archi_fill_guestbook_columns($column, $post_id) {
    switch ($column) {
        case 'guestbook_author_name':
            $author_name = get_post_meta($post_id, '_archi_guestbook_author_name', true);
            echo esc_html($author_name ?: '—');
            break;
            
        case 'linked_article':
            $linked_ids = get_post_meta($post_id, '_archi_linked_articles', true);
            if (!empty($linked_ids) && is_array($linked_ids)) {
                $titles = [];
                foreach ($linked_ids as $linked_id) {
                    $post = get_post($linked_id);
                    if ($post) {
                        $titles[] = '<a href="' . get_edit_post_link($linked_id) . '">' . esc_html($post->post_title) . '</a>';
                    }
                }
                echo implode(', ', $titles);
            } else {
                echo '—';
            }
            break;
    }
}
add_action('manage_archi_guestbook_posts_custom_column', 'archi_fill_guestbook_columns', 10, 2);
