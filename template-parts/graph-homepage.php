<?php
/**
 * Template part pour la page d'accueil avec graphique
 */
?>

<div class="graph-homepage-container">
    <!-- Container principal du graphique -->
    <div id="graph-container" class="graph-container">
        <!-- Le composant React sera monté ici -->
        <div id="graph-loading" class="graph-loading-inline">
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p><?php _e('Chargement du graphique...', 'archi-graph'); ?></p>
            </div>
        </div>
    </div>
    
    <!-- Légende des catégories -->
    <div id="graph-legend" class="graph-legend">
        <h3><?php _e('Catégories', 'archi-graph'); ?></h3>
        <div class="legend-items" id="legend-items">
            <!-- Généré dynamiquement par JavaScript -->
        </div>
    </div>
    
    <!-- Fallback si JavaScript est désactivé -->
    <noscript>
        <div class="no-js-fallback">
            <h2><?php _e('JavaScript requis', 'archi-graph'); ?></h2>
            <p><?php _e('Cette page nécessite JavaScript pour afficher le graphique interactif.', 'archi-graph'); ?></p>
            <a href="<?php echo esc_url(home_url('/blog')); ?>" class="btn btn-primary">
                <?php _e('Voir la liste des articles', 'archi-graph'); ?>
            </a>
        </div>
    </noscript>
</div>

<script>
// Configuration initiale pour le graphique
window.graphConfig = {
    containerId: 'graph-container',
    // ⚠️ NE PAS définir width/height - React utilisera 8000×6000 par défaut pour le viewBox
    apiEndpoint: '<?php echo esc_url(home_url('/wp-json/archi/v1/articles')); ?>',
    themeUrl: '<?php echo esc_url(get_template_directory_uri()); ?>',
    nonce: '<?php echo wp_create_nonce('wp_rest'); ?>',
    categories: <?php echo json_encode(get_categories(['hide_empty' => true])); ?>,
    options: {
        animationDuration: <?php echo archi_get_option('graph_animation_duration', 1000); ?>,
        nodeSpacing: <?php echo archi_get_option('graph_node_spacing', 100); ?>,
        clusterStrength: <?php echo archi_get_option('graph_cluster_strength', 0.1); ?>,
        showCategories: <?php echo archi_get_option('graph_show_categories', true) ? 'true' : 'false'; ?>,
        showLinks: <?php 
            $show_links_value = archi_get_option('graph_show_links', true);
            echo $show_links_value ? 'true' : 'false'; 
        ?>,
        autoSavePositions: <?php echo archi_get_option('graph_auto_save_positions', false) ? 'true' : 'false'; ?>,
        islandColor: '<?php echo archi_get_option('graph_island_color', '#f39c12'); ?>',
        illustrationIslandColor: '<?php echo archi_get_option('graph_illustration_island_color', '#e74c3c'); ?>'
    }
};

// Forcer l'ajout de la classe au body
document.body.classList.add('graph-homepage');

// Responsive - avec vérification de la méthode resize
window.addEventListener('resize', function() {
    if (window.graphInstance && typeof window.graphInstance.resize === 'function') {
        window.graphInstance.resize(window.innerWidth, window.innerHeight - 100);
    }
});
</script>