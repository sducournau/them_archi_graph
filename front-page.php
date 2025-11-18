<?php
/**
 * Template pour la page d'accueil avec le graphique interactif
 * Ce template est automatiquement utilisé par WordPress pour la page d'accueil
 */

get_header(); ?>

<style>
/* Disable scrolling on homepage */
body.home,
body.page-template-front-page {
    overflow: hidden;
    height: 100vh;
    margin: 0;
    padding: 0 !important;
}

/* Full height graph container */
.graph-homepage-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    overflow: hidden;
}

/* Adjust for WordPress admin bar */
body.admin-bar .graph-homepage-container {
    top: 32px;
    height: calc(100vh - 32px);
}

@media screen and (max-width: 782px) {
    body.admin-bar .graph-homepage-container {
        top: 46px;
        height: calc(100vh - 46px);
    }
}

/* Header auto-hide on homepage */
body.home .site-header,
body.page-template-front-page .site-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
    transform: translateY(0);
    opacity: 1;
}

/* Adjust header position for admin bar */
body.admin-bar .site-header {
    top: 32px;
}

@media screen and (max-width: 782px) {
    body.admin-bar .site-header {
        top: 46px;
    }
}

body.home .site-header.header-hidden,
body.page-template-front-page .site-header.header-hidden {
    transform: translateY(-100%);
    opacity: 0;
}

/* Trigger zone at top of screen */
.header-trigger-zone {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: <?php echo absint(get_theme_mod('archi_header_trigger_height', 50)); ?>px;
    z-index: 999;
    pointer-events: all;
}

/* Adjust trigger zone for admin bar */
body.admin-bar .header-trigger-zone {
    top: 32px;
}

@media screen and (max-width: 782px) {
    body.admin-bar .header-trigger-zone {
        top: 46px;
    }
}
</style>

<div class="header-trigger-zone"></div>

<div class="graph-homepage-container">
    <!-- Container principal du graphique -->
    <div id="graph-container" class="graph-container" role="application" aria-label="<?php esc_attr_e('Graphique interactif des projets', 'archi-graph'); ?>">
        <!-- Le composant React sera monté ici -->
        <div id="graph-loading" class="graph-loading-inline">
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p><?php _e('Chargement du graphique...', 'archi-graph'); ?></p>
            </div>
        </div>
    </div>
    
    <!-- Panel d'informations -->
    <div id="graph-info-panel" class="graph-info-panel" role="complementary" aria-label="<?php esc_attr_e('Détails du projet', 'archi-graph'); ?>">
        <button class="close-panel" onclick="this.parentElement.classList.add('hidden')" aria-label="<?php esc_attr_e('Fermer le panneau', 'archi-graph'); ?>">×</button>
        <div class="panel-content">
            <img id="panel-thumbnail" src="" alt="" style="display: none;">
            <h3 id="panel-title"><?php _e('Explorez le graphique', 'archi-graph'); ?></h3>
            <div class="panel-meta" style="display: none;">
                <span class="panel-date" id="panel-date"></span>
                <span class="panel-author" id="panel-author"></span>
            </div>
            <p id="panel-excerpt"><?php _e('Cliquez sur un nœud pour voir les détails du projet ou de l\'article.', 'archi-graph'); ?></p>
            <div id="panel-categories" class="panel-categories"></div>
            <div id="panel-tags" class="panel-tags"></div>
            <div id="panel-comments" class="panel-comments"></div>
            <div class="panel-actions" style="display: none;">
                <a id="panel-link" href="#" class="btn btn-primary">
                    <?php _e('Voir le projet', 'archi-graph'); ?> →
                </a>
            </div>
        </div>
    </div>
    
    <!-- Fallback si JavaScript est désactivé -->
    <noscript>
        <div class="no-js-fallback">
            <h2><?php _e('JavaScript requis', 'archi-graph'); ?></h2>
            <p><?php _e('Cette page nécessite JavaScript pour afficher le graphique interactif.', 'archi-graph'); ?></p>
            <a href="<?php echo esc_url(home_url('/blog')); ?>" class="btn btn-primary">
                <?php _e('Voir tous les articles', 'archi-graph'); ?>
            </a>
        </div>
    </noscript>
</div>

<script>
// Header auto-hide behavior
(function() {
    const header = document.getElementById('site-header');
    const triggerZone = document.querySelector('.header-trigger-zone');
    let hideTimeout;
    
    // Get customizer values with defaults
    const headerHideDelay = <?php echo absint(get_theme_mod('archi_header_hide_delay', 500)); ?>;
    const headerAnimationType = '<?php echo esc_js(get_theme_mod('archi_header_animation_type', 'ease-in-out')); ?>';
    const headerAnimationDuration = <?php echo floatval(get_theme_mod('archi_header_animation_duration', 0.3)); ?>;
    
    // Apply animation settings to header
    if (header) {
        header.style.transition = `transform ${headerAnimationDuration}s ${headerAnimationType}, opacity ${headerAnimationDuration}s ${headerAnimationType}`;
    }
    
    // Hide header after delay
    function hideHeader() {
        hideTimeout = setTimeout(function() {
            if (header) {
                header.classList.add('header-hidden');
            }
        }, headerHideDelay);
    }
    
    // Show header
    function showHeader() {
        clearTimeout(hideTimeout);
        if (header) {
            header.classList.remove('header-hidden');
        }
    }
    
    // Initialize: hide header after delay
    hideHeader();
    
    // Show header when mouse enters trigger zone
    if (triggerZone) {
        triggerZone.addEventListener('mouseenter', showHeader);
    }
    
    // Hide header when mouse leaves header
    if (header) {
        header.addEventListener('mouseleave', function() {
            hideHeader();
        });
        
        header.addEventListener('mouseenter', function() {
            clearTimeout(hideTimeout);
        });
    }
})();

// Configuration initiale pour le graphique
window.graphConfig = {
    containerId: 'graph-container',
    // ⚠️ NE PAS définir width/height ici - React utilisera 8000×6000 par défaut
    apiEndpoint: '<?php echo esc_url(home_url('/wp-json/archi/v1/articles')); ?>',
    themeUrl: '<?php echo esc_url(get_template_directory_uri()); ?>',
    nonce: '<?php echo wp_create_nonce('wp_rest'); ?>',
    categories: <?php echo json_encode(get_categories(['hide_empty' => true])); ?>,
    options: {
        animationDuration: <?php echo archi_get_option('graph_animation_duration', 1000); ?>,
        nodeSpacing: <?php echo archi_get_option('graph_node_spacing', 100); ?>,
        clusterStrength: <?php echo archi_get_option('graph_cluster_strength', 0.1); ?>,
        showCategories: <?php echo archi_get_option('graph_show_categories', true) ? 'true' : 'false'; ?>,
        showLinks: <?php echo archi_get_option('graph_show_links', true) ? 'true' : 'false'; ?>,
        autoSavePositions: <?php echo archi_get_option('graph_auto_save_positions', false) ? 'true' : 'false'; ?>
    }
};

// S'assurer que la classe body est présente
document.body.classList.add('graph-homepage');

// Responsive - avec vérification de la méthode resize
window.addEventListener('resize', function() {
    if (window.graphInstance && typeof window.graphInstance.resize === 'function') {
        window.graphInstance.resize(window.innerWidth, window.innerHeight);
    }
});

// Forcer l'initialisation du graphique
document.addEventListener('DOMContentLoaded', function() {
    // Tentative d'initialisation manuelle si nécessaire
    setTimeout(function() {
        if (!window.archiGraphApp && window.ArchiGraphApp) {
            new window.ArchiGraphApp();
        } else if (!window.archiGraphApp) {
            console.error('ArchiGraphApp not found - check if app.js is loaded');
        }
    }, 500);
});
</script>

    </div><!-- #page -->
    <?php wp_footer(); ?>
</body>
</html>