<?php
/**
 * Bloc: Vitrine de Projets
 * Affiche une sélection de projets architecturaux
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistrer le bloc
 */
function archi_register_project_showcase_block() {
    $attributes = Archi_Shared_Block_Attributes::merge_attributes(
        Archi_Shared_Block_Attributes::get_display_attributes(),
        Archi_Shared_Block_Attributes::get_layout_attributes(),
        Archi_Shared_Block_Attributes::get_visibility_attributes(),
        [
            'projectIds' => [
                'type' => 'array',
                'default' => []
            ],
            'autoSelect' => [
                'type' => 'boolean',
                'default' => false
            ],
            'autoSelectCriteria' => [
                'type' => 'string',
                'default' => 'recent' // recent, featured, random
            ],
            'autoSelectCount' => [
                'type' => 'number',
                'default' => 6
            ],
            'imageSize' => [
                'type' => 'string',
                'default' => 'medium'
            ],
            'showMetadata' => [
                'type' => 'boolean',
                'default' => true
            ],
            'showReadMore' => [
                'type' => 'boolean',
                'default' => true
            ]
        ]
    );
    
    register_block_type('archi-graph/project-showcase', [
        'attributes' => $attributes,
        'render_callback' => 'archi_render_project_showcase_block',
        'editor_script' => 'archi-blocks-editor',
        'editor_style' => 'archi-blocks-editor',
        'style' => 'archi-blocks'
    ]);
}
// Note: Appelé automatiquement par le loader
// add_action('init', 'archi_register_project_showcase_block');

/**
 * Rendu du bloc
 */
function archi_render_project_showcase_block($attributes) {
    $attributes = archi_sanitize_block_attributes($attributes, [
        'projectIds' => ['type' => 'array', 'default' => []],
        'autoSelect' => ['type' => 'boolean', 'default' => false],
        'autoSelectCriteria' => ['type' => 'string', 'default' => 'recent'],
        'autoSelectCount' => ['type' => 'number', 'default' => 6],
        'layout' => ['type' => 'string', 'default' => 'grid'],
        'columns' => ['type' => 'number', 'default' => 3],
        'imageSize' => ['type' => 'string', 'default' => 'medium']
    ]);
    
    // Récupérer les projets
    $projects = archi_get_projects_for_showcase($attributes);
    
    if (empty($projects)) {
        if (current_user_can('edit_posts')) {
            return '<div class="archi-block-notice">' . 
                   __('Aucun projet à afficher. Sélectionnez des projets dans les paramètres du bloc.', 'archi-graph') . 
                   '</div>';
        }
        return '';
    }
    
    // Classes et styles
    $classes = archi_get_block_classes($attributes, 'archi-project-showcase');
    $classes .= ' archi-layout-' . esc_attr($attributes['layout']);
    $classes .= ' archi-columns-' . absint($attributes['columns']);
    
    $styles = archi_get_block_styles($attributes);
    
    ob_start();
    ?>
    <div class="<?php echo esc_attr($classes); ?>"<?php echo $styles; ?>>
        <div class="archi-showcase-grid" style="--columns: <?php echo absint($attributes['columns']); ?>;">
            <?php foreach ($projects as $project): ?>
                <?php 
                // Map block attributes to card options
                $card_options = [
                    'show_excerpt' => $attributes['showExcerpt'] ?? true,
                    'show_meta' => $attributes['showMetadata'] ?? true,
                    'show_thumbnail' => $attributes['showFeaturedImage'] ?? true,
                    'card_class' => 'archi-project-card'
                ];
                echo archi_render_article_card($project, $card_options); 
                ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}

/**
 * Obtenir les projets pour le showcase
 */
function archi_get_projects_for_showcase($attributes) {
    // Sélection manuelle
    if (!empty($attributes['projectIds']) && !$attributes['autoSelect']) {
        return get_posts([
            'post_type' => 'archi_project',
            'include' => array_map('absint', $attributes['projectIds']),
            'orderby' => 'post__in',
            'post_status' => 'publish'
        ]);
    }
    
    // Sélection automatique
    $args = [
        'post_type' => 'archi_project',
        'posts_per_page' => absint($attributes['autoSelectCount']),
        'post_status' => 'publish'
    ];
    
    switch ($attributes['autoSelectCriteria']) {
        case 'featured':
            $args['meta_key'] = '_archi_priority_level';
            $args['meta_value'] = ['high', 'featured'];
            $args['meta_compare'] = 'IN';
            $args['orderby'] = 'meta_value date';
            break;
            
        case 'random':
            $args['orderby'] = 'rand';
            break;
            
        case 'recent':
        default:
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            break;
    }
    
    return get_posts($args);
}

// Enregistrer le bloc
archi_register_project_showcase_block();
