<?php
/**
 * Script temporaire pour vider le cache du graphe
 * À exécuter une fois puis supprimer
 * 
 * URL: http://localhost/wordpress/wp-content/themes/archi-graph-template/clear-graph-cache.php
 */

// Charger WordPress
// Le chemin depuis themes/archi-graph-template/ vers la racine WordPress
// themes/archi-graph-template/ -> themes/ -> wp-content/ -> wordpress/
require_once(__DIR__ . '/../../../wp-load.php');

// Vérifier si on est admin
if (!current_user_can('manage_options')) {
    die('Accès refusé - Vous devez être administrateur');
}

echo '<h1>🧹 Nettoyage du cache du graphe</h1>';

// Supprimer tous les transients liés au graphe
global $wpdb;
$transients_deleted = $wpdb->query(
    "DELETE FROM {$wpdb->options} 
    WHERE option_name LIKE '%_transient_archi_%' 
    OR option_name LIKE '%_transient_timeout_archi_%'"
);

echo "<p>✅ <strong>{$transients_deleted}</strong> transients supprimés</p>";

// Forcer la régénération de la config
delete_option('archi_visual_config_cache');
echo "<p>✅ Cache de configuration supprimé</p>";

// Vider le cache objet WordPress si actif
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "<p>✅ Cache objet WordPress vidé</p>";
}

// Afficher la configuration actuelle
echo '<hr>';
echo '<h2>📊 Configuration actuelle du graphe</h2>';

// Charger la config
require_once('inc/graph-config.php');
$config = archi_visual_get_frontend_config();

echo '<pre style="background: #f5f5f5; padding: 20px; border-radius: 5px; overflow-x: auto;">';
echo '<strong>Physics Settings:</strong>' . "\n";
echo 'chargeStrength: ' . ($config['chargeStrength'] ?? 'NOT SET') . " (attendu: -300 - beaucoup plus d'espace)\n";
echo 'chargeDistance: ' . ($config['chargeDistance'] ?? 'NOT SET') . " (attendu: 500 - portée augmentée)\n";
echo 'collisionPadding: ' . ($config['collisionPadding'] ?? 'NOT SET') . " (attendu: 35 - espacement maximal)\n";
echo 'centerStrength: ' . ($config['centerStrength'] ?? 'NOT SET') . " (attendu: 0.05 - faible pour plus d'expansion)\n";
echo 'clusterStrength: ' . ($config['clusterStrength'] ?? 'NOT SET') . " (attendu: 0.15 - clusters plus larges)\n";
echo 'linkDistance: ' . ($config['linkDistance'] ?? 'NOT SET') . " (attendu: 200 - liens plus espacés)\n";
echo 'simulationAlpha: ' . ($config['simulationAlpha'] ?? 'NOT SET') . " (attendu: 0.3)\n";
echo 'simulationAlphaDecay: ' . ($config['simulationAlphaDecay'] ?? 'NOT SET') . " (attendu: 0.02)\n";
echo 'simulationVelocityDecay: ' . ($config['simulationVelocityDecay'] ?? 'NOT SET') . " (attendu: 0.4)\n";
echo "\n" . '<strong>Visual Settings:</strong>' . "\n";
echo 'defaultNodeSize: ' . ($config['defaultNodeSize'] ?? 'NOT SET') . " (attendu: 80)\n";
echo 'nodeSize: ' . ($config['nodeSize'] ?? 'NOT SET') . " (attendu: 80)\n";
echo "\n" . '<strong>🚀 Améliorations majeures:</strong>' . "\n";
echo '- Boundary désactivée pour espace libre !' . "\n";
echo '- ViewBox DYNAMIQUE: adapté à la résolution de l\'écran' . "\n";
echo '  Calcul: Math.max(screenWidth * 1.5, 2000) x Math.max(screenHeight * 1.5, 1400)' . "\n";
echo '  Example (1920x1080): 2880x1620' . "\n";
echo '- Collision iterations: 6 avec strength 1.0' . "\n";
echo '- REPULSION_FORCE JavaScript: 1200 (doublée)' . "\n";
echo '- Responsive: s\'adapte au redimensionnement de fenêtre' . "\n";
echo '</pre>';

echo '<hr>';
echo '<h3>✅ Cache vidé avec succès !</h3>';
echo '<p><strong>Prochaines étapes :</strong></p>';
echo '<ol>';
echo '<li>Rafraîchir la page du graphe avec <code>Ctrl+F5</code> (ou <code>Cmd+Shift+R</code> sur Mac)</li>';
echo '<li>Ouvrir la console du navigateur (F12) et chercher le log "🎯 Graph Physics Settings"</li>';
echo '<li>Vérifier que les nouvelles valeurs sont bien chargées</li>';
echo '<li><strong style="color: red;">Supprimer ce fichier après utilisation pour des raisons de sécurité</strong></li>';
echo '</ol>';

echo '<p><a href="/" style="display: inline-block; background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px; margin-top: 20px;">← Retour au site</a></p>';
