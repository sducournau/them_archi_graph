<?php
/**
 * API REST pour l'édition en direct du graphique
 * Endpoints pour déplacer nodes, créer liens, éditer images, etc.
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistrer les endpoints REST pour l'éditeur de graphique
 */
function archi_register_graph_editor_endpoints() {
    // Endpoint: Sauvegarder la position d'un nœud
    register_rest_route('archi/v1', '/graph-editor/save-position', [
        'methods' => 'POST',
        'callback' => 'archi_save_node_position',
        'permission_callback' => 'archi_editor_permission_check',
        'args' => [
            'post_id' => [
                'required' => true,
                'type' => 'integer',
                'sanitize_callback' => 'absint'
            ],
            'x' => [
                'required' => true,
                'type' => 'number'
            ],
            'y' => [
                'required' => true,
                'type' => 'number'
            ]
        ]
    ]);
    
    // Endpoint: Sauvegarder plusieurs positions en batch
    register_rest_route('archi/v1', '/graph-editor/save-positions', [
        'methods' => 'POST',
        'callback' => 'archi_save_node_positions_batch',
        'permission_callback' => 'archi_editor_permission_check',
        'args' => [
            'positions' => [
                'required' => true,
                'type' => 'array'
            ]
        ]
    ]);
    
    // Endpoint: Créer un lien entre deux nœuds
    register_rest_route('archi/v1', '/graph-editor/create-link', [
        'methods' => 'POST',
        'callback' => 'archi_create_node_link',
        'permission_callback' => 'archi_editor_permission_check',
        'args' => [
            'source_id' => [
                'required' => true,
                'type' => 'integer',
                'sanitize_callback' => 'absint'
            ],
            'target_id' => [
                'required' => true,
                'type' => 'integer',
                'sanitize_callback' => 'absint'
            ]
        ]
    ]);
    
    // Endpoint: Supprimer un lien
    register_rest_route('archi/v1', '/graph-editor/delete-link', [
        'methods' => 'POST',
        'callback' => 'archi_delete_node_link',
        'permission_callback' => 'archi_editor_permission_check',
        'args' => [
            'source_id' => [
                'required' => true,
                'type' => 'integer',
                'sanitize_callback' => 'absint'
            ],
            'target_id' => [
                'required' => true,
                'type' => 'integer',
                'sanitize_callback' => 'absint'
            ]
        ]
    ]);
    
    // Endpoint: Mettre à jour l'image d'un nœud
    register_rest_route('archi/v1', '/graph-editor/update-image', [
        'methods' => 'POST',
        'callback' => 'archi_update_node_image',
        'permission_callback' => 'archi_editor_permission_check',
        'args' => [
            'post_id' => [
                'required' => true,
                'type' => 'integer',
                'sanitize_callback' => 'absint'
            ],
            'image_id' => [
                'required' => true,
                'type' => 'integer',
                'sanitize_callback' => 'absint'
            ]
        ]
    ]);
    
    // Endpoint: Mettre à jour les paramètres avancés d'un nœud
    register_rest_route('archi/v1', '/graph-editor/update-params', [
        'methods' => 'POST',
        'callback' => 'archi_update_node_params',
        'permission_callback' => 'archi_editor_permission_check',
        'args' => [
            'post_id' => [
                'required' => true,
                'type' => 'integer',
                'sanitize_callback' => 'absint'
            ],
            'params' => [
                'required' => true,
                'type' => 'object'
            ]
        ]
    ]);
    
    // Endpoint: Activer/désactiver un nœud dans le graphique
    register_rest_route('archi/v1', '/graph-editor/toggle-visibility', [
        'methods' => 'POST',
        'callback' => 'archi_toggle_node_visibility',
        'permission_callback' => 'archi_editor_permission_check',
        'args' => [
            'post_id' => [
                'required' => true,
                'type' => 'integer',
                'sanitize_callback' => 'absint'
            ],
            'visible' => [
                'required' => true,
                'type' => 'boolean'
            ]
        ]
    ]);
}
add_action('rest_api_init', 'archi_register_graph_editor_endpoints');

/**
 * Vérifier les permissions d'édition
 */
function archi_editor_permission_check() {
    // Vérifier que l'utilisateur peut éditer des posts
    if (!current_user_can('edit_posts')) {
        return new WP_Error(
            'rest_forbidden',
            __('Vous n\'avez pas les permissions nécessaires', 'archi-graph'),
            ['status' => 403]
        );
    }
    
    return true;
}

/**
 * Sauvegarder la position d'un nœud
 */
function archi_save_node_position($request) {
    $post_id = $request->get_param('post_id');
    $x = floatval($request->get_param('x'));
    $y = floatval($request->get_param('y'));
    
    // Vérifier que le post existe
    $post = get_post($post_id);
    if (!$post) {
        return new WP_Error(
            'invalid_post',
            __('Article introuvable', 'archi-graph'),
            ['status' => 404]
        );
    }
    
    // Sauvegarder la position
    $position = ['x' => $x, 'y' => $y];
    update_post_meta($post_id, '_archi_graph_position', $position);
    
    // Invalider le cache
    delete_transient('archi_graph_articles');
    
    return [
        'success' => true,
        'post_id' => $post_id,
        'position' => $position,
        'message' => __('Position sauvegardée', 'archi-graph')
    ];
}

/**
 * Sauvegarder plusieurs positions en batch
 */
function archi_save_node_positions_batch($request) {
    $positions = $request->get_param('positions');
    $result = archi_save_positions_internal($positions);
    return $result;
}

/**
 * Créer un lien entre deux nœuds
 */
function archi_create_node_link($request) {
    $source_id = $request->get_param('source_id');
    $target_id = $request->get_param('target_id');
    
    // Vérifier que les posts existent
    if (!get_post($source_id) || !get_post($target_id)) {
        return new WP_Error(
            'invalid_post',
            __('Un ou plusieurs articles introuvables', 'archi-graph'),
            ['status' => 404]
        );
    }
    
    // Récupérer les liens existants
    $related = get_post_meta($source_id, '_archi_related_articles', true);
    if (!is_array($related)) {
        $related = [];
    }
    
    // Ajouter le nouveau lien si pas déjà présent
    if (!in_array($target_id, $related)) {
        $related[] = $target_id;
        update_post_meta($source_id, '_archi_related_articles', $related);
    }
    
    // Invalider le cache
    delete_transient('archi_graph_articles');
    
    return [
        'success' => true,
        'source_id' => $source_id,
        'target_id' => $target_id,
        'related_articles' => $related,
        'message' => __('Lien créé', 'archi-graph')
    ];
}

/**
 * Supprimer un lien entre deux nœuds
 */
function archi_delete_node_link($request) {
    $source_id = $request->get_param('source_id');
    $target_id = $request->get_param('target_id');
    
    // Récupérer les liens existants
    $related = get_post_meta($source_id, '_archi_related_articles', true);
    if (!is_array($related)) {
        $related = [];
    }
    
    // Retirer le lien
    $related = array_diff($related, [$target_id]);
    $related = array_values($related); // Réindexer
    
    update_post_meta($source_id, '_archi_related_articles', $related);
    
    // Invalider le cache
    delete_transient('archi_graph_articles');
    
    return [
        'success' => true,
        'source_id' => $source_id,
        'target_id' => $target_id,
        'related_articles' => $related,
        'message' => __('Lien supprimé', 'archi-graph')
    ];
}

/**
 * Mettre à jour l'image d'un nœud
 */
function archi_update_node_image($request) {
    $post_id = $request->get_param('post_id');
    $image_id = $request->get_param('image_id');
    
    // Vérifier que le post existe
    $post = get_post($post_id);
    if (!$post) {
        return new WP_Error(
            'invalid_post',
            __('Article introuvable', 'archi-graph'),
            ['status' => 404]
        );
    }
    
    // Vérifier que l'image existe
    $image = get_post($image_id);
    if (!$image || $image->post_type !== 'attachment') {
        return new WP_Error(
            'invalid_image',
            __('Image introuvable', 'archi-graph'),
            ['status' => 404]
        );
    }
    
    // Mettre à jour l'image à la une
    set_post_thumbnail($post_id, $image_id);
    
    // Invalider le cache
    delete_transient('archi_graph_articles');
    
    return [
        'success' => true,
        'post_id' => $post_id,
        'image_id' => $image_id,
        'image_url' => wp_get_attachment_image_url($image_id, 'medium'),
        'message' => __('Image mise à jour', 'archi-graph')
    ];
}

/**
 * Mettre à jour les paramètres avancés d'un nœud
 * 
 * ✅ REFACTORED: Now uses unified archi_set_graph_params() function
 * instead of manual mapping. This ensures consistency across all endpoints.
 */
function archi_update_node_params($request) {
    $post_id = $request->get_param('post_id');
    $params = $request->get_param('params');
    
    // Vérifier que le post existe
    $post = get_post($post_id);
    if (!$post) {
        return new WP_Error(
            'invalid_post',
            __('Article introuvable', 'archi-graph'),
            ['status' => 404]
        );
    }
    
    // ✅ Use unified setter function from graph-meta-registry.php
    $result = archi_set_graph_params($post_id, $params);
    
    if (!$result['success']) {
        return new WP_Error(
            'update_failed',
            $result['error'] ?? __('Échec de la mise à jour', 'archi-graph'),
            ['status' => 500]
        );
    }
    
    return [
        'success' => true,
        'post_id' => $post_id,
        'updated' => $result['updated'],
        'message' => sprintf(__('%d paramètres mis à jour', 'archi-graph'), $result['count'])
    ];
}

/**
 * Activer/désactiver un nœud dans le graphique
 */
function archi_toggle_node_visibility($request) {
    $post_id = $request->get_param('post_id');
    $visible = $request->get_param('visible');
    
    // Vérifier que le post existe
    $post = get_post($post_id);
    if (!$post) {
        return new WP_Error(
            'invalid_post',
            __('Article introuvable', 'archi-graph'),
            ['status' => 404]
        );
    }
    
    // Mettre à jour la visibilité
    update_post_meta($post_id, '_archi_show_in_graph', $visible ? '1' : '0');
    
    // Invalider le cache
    delete_transient('archi_graph_articles');
    
    return [
        'success' => true,
        'post_id' => $post_id,
        'visible' => $visible,
        'message' => $visible 
            ? __('Nœud activé dans le graphique', 'archi-graph')
            : __('Nœud désactivé du graphique', 'archi-graph')
    ];
}

/**
 * Endpoint pour récupérer l'état d'édition
 */
function archi_get_editor_state() {
    register_rest_route('archi/v1', '/graph-editor/state', [
        'methods' => 'GET',
        'callback' => function() {
            $can_edit = current_user_can('edit_posts');
            $user = wp_get_current_user();
            
            return [
                'can_edit' => $can_edit,
                'user_id' => $user->ID,
                'user_name' => $user->display_name,
                'is_admin' => current_user_can('administrator'),
                'nonce' => wp_create_nonce('archi_graph_editor')
            ];
        },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'archi_get_editor_state');

/**
 * Ajouter les données d'édition au frontend
 */
function archi_enqueue_editor_data() {
    if (is_front_page() || is_page_template('page-home.php')) {
        $can_edit = current_user_can('edit_posts');
        
        wp_localize_script('archi-graph-main', 'archiGraphEditor', [
            'canEdit' => $can_edit,
            'apiUrl' => rest_url('archi/v1/graph-editor/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'strings' => [
                'editMode' => __('Mode Édition', 'archi-graph'),
                'savePositions' => __('Sauvegarder les positions', 'archi-graph'),
                'createLink' => __('Créer un lien', 'archi-graph'),
                'deleteLink' => __('Supprimer le lien', 'archi-graph'),
                'editNode' => __('Éditer le nœud', 'archi-graph'),
                'changeImage' => __('Changer l\'image', 'archi-graph'),
                'toggleVisibility' => __('Activer/Désactiver', 'archi-graph'),
                'saved' => __('Sauvegardé !', 'archi-graph'),
                'error' => __('Erreur', 'archi-graph'),
                'selectSource' => __('Cliquez sur le nœud source', 'archi-graph'),
                'selectTarget' => __('Cliquez sur le nœud cible', 'archi-graph'),
                'linkCreated' => __('Lien créé', 'archi-graph'),
                'linkDeleted' => __('Lien supprimé', 'archi-graph')
            ]
        ]);
    }
}
add_action('wp_enqueue_scripts', 'archi_enqueue_editor_data', 20);
