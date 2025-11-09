<?php
/**
 * Bloc: Graphique Interactif
 * Affiche le graphique interactif D3.js avec les articles
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enregistrer le bloc
 */
function archi_register_interactive_graph_block() {
    $attributes = Archi_Shared_Block_Attributes::merge_attributes(
        Archi_Shared_Block_Attributes::get_display_attributes(),
        Archi_Shared_Block_Attributes::get_filter_attributes(),
        [
            'width' => [
                'type' => 'number',
                'default' => 1200
            ],
            'height' => [
                'type' => 'number', 
                'default' => 800
            ],
            'enableFilters' => [
                'type' => 'boolean',
                'default' => true
            ],
            'enableSearch' => [
                'type' => 'boolean',
                'default' => true
            ],
            'animationDuration' => [
                'type' => 'number',
                'default' => 1000
            ],
            'nodeSpacing' => [
                'type' => 'number',
                'default' => 100
            ],
            'clusterStrength' => [
                'type' => 'number',
                'default' => 10
            ],
            'showMinimap' => [
                'type' => 'boolean',
                'default' => false
            ],
            'enableZoom' => [
                'type' => 'boolean',
                'default' => true
            ],
            'enableDrag' => [
                'type' => 'boolean',
                'default' => true
            ]
        ]
    );
    
    register_block_type('archi-graph/interactive-graph', [
        'attributes' => $attributes,
        'render_callback' => 'archi_render_interactive_graph_block',
        'editor_script' => 'archi-blocks-editor',
        'editor_style' => 'archi-blocks-editor',
        'style' => 'archi-blocks'
    ]);
}
// Note: Appelé automatiquement par le loader
// add_action('init', 'archi_register_interactive_graph_block');

/**
 * Rendu du bloc
 */
function archi_render_interactive_graph_block($attributes) {
    // Sanitize attributes
    $attributes = archi_sanitize_block_attributes($attributes, [
        'width' => ['type' => 'number', 'default' => 1200],
        'height' => ['type' => 'number', 'default' => 800],
        'categories' => ['type' => 'array', 'default' => []],
        'postTypes' => ['type' => 'array', 'default' => ['post', 'archi_project', 'archi_illustration']],
        'maxItems' => ['type' => 'number', 'default' => 100],
        'enableFilters' => ['type' => 'boolean', 'default' => true],
        'enableSearch' => ['type' => 'boolean', 'default' => true],
        'animationDuration' => ['type' => 'number', 'default' => 1000],
        'nodeSpacing' => ['type' => 'number', 'default' => 100],
        'clusterStrength' => ['type' => 'number', 'default' => 10]
    ]);
    
    // Générer ID unique
    $graph_id = 'archi-graph-' . uniqid();
    
    // Classes CSS
    $classes = archi_get_block_classes($attributes, 'archi-graph-block-wrapper');
    
    // Configuration pour JS
    $config = [
        'width' => $attributes['width'],
        'height' => $attributes['height'],
        'categories' => $attributes['categories'],
        'postTypes' => $attributes['postTypes'],
        'maxArticles' => $attributes['maxItems'],
        'animationDuration' => $attributes['animationDuration'],
        'nodeSpacing' => $attributes['nodeSpacing'],
        'clusterStrength' => $attributes['clusterStrength'],
        'enableZoom' => $attributes['enableZoom'] ?? true,
        'enableDrag' => $attributes['enableDrag'] ?? true,
        'showMinimap' => $attributes['showMinimap'] ?? false
    ];
    
    ob_start();
    ?>
    <div class="<?php echo esc_attr($classes); ?>" style="max-width: <?php echo esc_attr($attributes['width']); ?>px;">
        
        <?php if ($attributes['enableFilters'] || $attributes['enableSearch']): ?>
        <div class="archi-graph-controls">
            
            <?php if ($attributes['enableSearch']): ?>
            <div class="archi-search-control">
                <input type="text" 
                       id="<?php echo esc_attr($graph_id); ?>-search" 
                       placeholder="<?php esc_attr_e('Rechercher dans le graphe...', 'archi-graph'); ?>"
                       class="archi-search-input"
                       aria-label="<?php esc_attr_e('Rechercher', 'archi-graph'); ?>" />
                <button type="button" 
                        class="archi-search-clear" 
                        aria-label="<?php esc_attr_e('Effacer', 'archi-graph'); ?>">×</button>
            </div>
            <?php endif; ?>
            
            <?php if ($attributes['enableFilters']): ?>
            <div class="archi-category-filters" role="group" aria-label="<?php esc_attr_e('Filtres par catégorie', 'archi-graph'); ?>">
                <button type="button" 
                        class="archi-filter-btn active" 
                        data-category="all"
                        aria-pressed="true">
                    <?php _e('Toutes', 'archi-graph'); ?>
                </button>
                <?php
                $category_terms = get_terms([
                    'taxonomy' => 'category',
                    'hide_empty' => true,
                    'number' => 20
                ]);
                
                if (!is_wp_error($category_terms) && !empty($category_terms)):
                    foreach ($category_terms as $term):
                ?>
                <button type="button" 
                        class="archi-filter-btn" 
                        data-category="<?php echo esc_attr($term->slug); ?>"
                        data-term-id="<?php echo esc_attr($term->term_id); ?>"
                        aria-pressed="false">
                    <?php echo esc_html($term->name); ?>
                    <span class="count">(<?php echo absint($term->count); ?>)</span>
                </button>
                <?php 
                    endforeach;
                endif;
                ?>
            </div>
            <?php endif; ?>
            
        </div>
        <?php endif; ?>
        
        <div id="<?php echo esc_attr($graph_id); ?>" 
             class="archi-interactive-graph"
             style="width: 100%; height: <?php echo esc_attr($attributes['height']); ?>px;"
             data-config='<?php echo esc_attr(wp_json_encode($config)); ?>'
             role="img"
             aria-label="<?php esc_attr_e('Graphique interactif des articles', 'archi-graph'); ?>">
            
            <!-- Fallback pour navigateurs sans JS -->
            <noscript>
                <div class="archi-graph-fallback">
                    <p><?php _e('Ce graphique nécessite JavaScript pour fonctionner.', 'archi-graph'); ?></p>
                    <a href="<?php echo esc_url(home_url('/articles')); ?>" class="button">
                        <?php _e('Voir tous les articles', 'archi-graph'); ?>
                    </a>
                </div>
            </noscript>
            
            <!-- Loading state -->
            <div class="archi-graph-loading" aria-live="polite">
                <div class="archi-loading"></div>
                <p><?php _e('Chargement du graphique...', 'archi-graph'); ?></p>
            </div>
        </div>
        
        <?php if ($attributes['showMinimap'] ?? false): ?>
        <div class="archi-graph-minimap" aria-hidden="true"></div>
        <?php endif; ?>
        
    </div>
    
    <script>
    (function() {
        'use strict';
        
        // Initialisation différée pour éviter les conflits
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initGraph);
        } else {
            initGraph();
        }
        
        function initGraph() {
            const graphElement = document.getElementById('<?php echo esc_js($graph_id); ?>');
            
            if (!graphElement) {
                console.error('Graph element not found');
                return;
            }
            
            // Vérifier que la lib D3/ArchiGraph est chargée
            if (typeof window.ArchiGraph === 'undefined') {
                console.error('ArchiGraph library not loaded');
                graphElement.innerHTML = '<p class="error"><?php esc_html_e('Erreur: Bibliothèque de graphique non chargée', 'archi-graph'); ?></p>';
                return;
            }
            
            try {
                const config = JSON.parse(graphElement.dataset.config);
                const graph = new window.ArchiGraph('<?php echo esc_js($graph_id); ?>', config);
                
                // Exposer l'instance pour accès externe
                graphElement.archiGraphInstance = graph;
                
                // Event pour permettre l'extension
                graphElement.dispatchEvent(new CustomEvent('archi-graph-initialized', {
                    detail: { graph: graph, config: config }
                }));
                
            } catch (error) {
                console.error('Error initializing graph:', error);
                graphElement.innerHTML = '<p class="error"><?php esc_html_e('Erreur d\'initialisation du graphique', 'archi-graph'); ?></p>';
            }
        }
    })();
    </script>
    <?php
    
    return ob_get_clean();
}

// Enregistrer le bloc
archi_register_interactive_graph_block();
