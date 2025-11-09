<?php
/**
 * Gestion des couleurs de polygone par catégorie
 * Permet de définir des couleurs personnalisées pour les clusters de catégories
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistrer les métadonnées de polygone pour les catégories
 * Note: Les polygones utilisent maintenant la couleur native de la catégorie
 */
function archi_register_category_polygon_meta() {
    register_term_meta('category', 'archi_polygon_enabled', [
        'type' => 'boolean',
        'description' => __('Afficher le polygone pour cette catégorie', 'archi-graph'),
        'single' => true,
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'show_in_rest' => true,
    ]);
}
add_action('init', 'archi_register_category_polygon_meta');

/**
 * Ajouter les champs de polygone aux catégories (formulaire d'ajout)
 */
function archi_add_category_polygon_fields() {
    ?>
    <div class="form-field term-polygon-wrap">
        <label for="archi_polygon_enabled">
            <?php _e('Polygone dans le graphique', 'archi-graph'); ?>
        </label>
        <label>
            <input type="checkbox" 
                   name="archi_polygon_enabled" 
                   id="archi_polygon_enabled" 
                   value="1" 
                   checked>
            <?php _e('Afficher un polygone pour cette catégorie dans le graphique', 'archi-graph'); ?>
        </label>
        <p class="description">
            <?php _e('Un polygone englobant sera dessiné autour des articles de cette catégorie. La couleur du polygone sera la couleur native de la catégorie.', 'archi-graph'); ?>
        </p>
    </div>
    <?php
}
add_action('category_add_form_fields', 'archi_add_category_polygon_fields');

/**
 * Ajouter les champs de polygone aux catégories (formulaire d'édition)
 */
function archi_edit_category_polygon_fields($term) {
    $enabled = get_term_meta($term->term_id, 'archi_polygon_enabled', true);
    
    // Valeur par défaut
    if ($enabled === '') {
        $enabled = true;
    }
    ?>
    <tr class="form-field term-polygon-wrap">
        <th scope="row">
            <label for="archi_polygon_enabled">
                <?php _e('Polygone dans le graphique', 'archi-graph'); ?>
            </label>
        </th>
        <td>
            <label>
                <input type="checkbox" 
                       name="archi_polygon_enabled" 
                       id="archi_polygon_enabled" 
                       value="1" 
                       <?php checked($enabled, true); ?>>
                <?php _e('Afficher un polygone pour cette catégorie dans le graphique', 'archi-graph'); ?>
            </label>
            <p class="description">
                <?php _e('Un polygone englobant sera dessiné autour des articles de cette catégorie. La couleur du polygone sera la couleur native de la catégorie.', 'archi-graph'); ?>
            </p>
        </td>
    </tr>
    <?php
}
add_action('category_edit_form_fields', 'archi_edit_category_polygon_fields');

/**
 * Sauvegarder les métadonnées de polygone de catégorie
 */
function archi_save_category_polygon_meta($term_id) {
    if (!current_user_can('edit_term', $term_id)) {
        return;
    }
    
    // Sauvegarder l'état activé/désactivé
    if (isset($_POST['archi_polygon_enabled'])) {
        update_term_meta($term_id, 'archi_polygon_enabled', true);
    } else {
        update_term_meta($term_id, 'archi_polygon_enabled', false);
    }
}
add_action('created_category', 'archi_save_category_polygon_meta');
add_action('edited_category', 'archi_save_category_polygon_meta');

/**
 * Ajouter une colonne pour la couleur du polygone dans la liste des catégories
 */
function archi_add_category_polygon_column($columns) {
    $columns['archi_polygon'] = __('Polygone Graphique', 'archi-graph');
    return $columns;
}
add_filter('manage_edit-category_columns', 'archi_add_category_polygon_column');

/**
 * Afficher le contenu de la colonne polygone
 */
function archi_category_polygon_column_content($content, $column_name, $term_id) {
    if ($column_name === 'archi_polygon') {
        $enabled = get_term_meta($term_id, 'archi_polygon_enabled', true);
        
        if ($enabled === '') {
            $enabled = true;
        }
        
        if ($enabled) {
            $content = '<span style="color: #2ecc71; font-weight: bold;">✓ ' . __('Activé', 'archi-graph') . '</span>';
        } else {
            $content = '<span style="color: #999;">—</span>';
        }
    }
    return $content;
}
add_filter('manage_category_custom_column', 'archi_category_polygon_column_content', 10, 3);

/**
 * Endpoint REST API pour récupérer les couleurs de polygone
 */
function archi_register_polygon_colors_endpoint() {
    register_rest_route('archi/v1', '/polygon-colors', [
        'methods' => 'GET',
        'callback' => 'archi_get_polygon_colors',
        'permission_callback' => '__return_true',
    ]);
}
add_action('rest_api_init', 'archi_register_polygon_colors_endpoint');

/**
 * Récupérer toutes les couleurs de polygone pour les catégories
 * Note: Utilise maintenant la couleur native de la catégorie
 */
function archi_get_polygon_colors() {
    $categories = get_categories([
        'hide_empty' => false,
    ]);
    
    $polygon_colors = [];
    
    foreach ($categories as $category) {
        $enabled = get_term_meta($category->term_id, 'archi_polygon_enabled', true);
        
        // Valeur par défaut
        if ($enabled === '') {
            $enabled = true;
        }
        
        $polygon_colors[] = [
            'category_id' => $category->term_id,
            'category_name' => $category->name,
            'category_slug' => $category->slug,
            'enabled' => rest_sanitize_boolean($enabled),
        ];
    }
    
    return rest_ensure_response($polygon_colors);
}

/**
 * Ajouter les couleurs de polygone aux données de catégories dans l'API du graphique
 * Note: Les polygones utilisent maintenant la couleur native de la catégorie
 */
function archi_add_polygon_colors_to_graph_api($response, $post, $request) {
    $categories = get_the_category($post->ID);
    
    if (!empty($categories)) {
        $category_polygons = [];
        
        foreach ($categories as $category) {
            $enabled = get_term_meta($category->term_id, 'archi_polygon_enabled', true);
            
            if ($enabled === '') {
                $enabled = true;
            }
            
            $category_polygons[$category->term_id] = [
                'enabled' => rest_sanitize_boolean($enabled),
            ];
        }
        
        $response->data['polygon_colors'] = $category_polygons;
    }
    
    return $response;
}
add_filter('rest_prepare_post', 'archi_add_polygon_colors_to_graph_api', 10, 3);
add_filter('rest_prepare_archi_project', 'archi_add_polygon_colors_to_graph_api', 10, 3);
add_filter('rest_prepare_archi_illustration', 'archi_add_polygon_colors_to_graph_api', 10, 3);
