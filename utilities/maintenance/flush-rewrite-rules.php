<?php
/**
 * Script pour forcer le rechargement des règles de réécriture
 * À exécuter une seule fois après l'installation du thème
 */

// Charger WordPress
require_once('../../../wp-load.php');

// Vérifier les permissions
if (!current_user_can('manage_options')) {
    die('Accès refusé');
}

echo '<h1>Rechargement des règles de réécriture</h1>';

// Forcer le rechargement des règles
flush_rewrite_rules(true);

echo '<p style="color: green;">✓ Règles de réécriture rechargées avec succès!</p>';

// Tester les routes REST API
echo '<h2>Test des routes REST API</h2>';

$routes = [
    '/wp-json/archi/v1/articles',
    '/wp-json/archi/v1/categories',
];

foreach ($routes as $route) {
    $url = home_url($route);
    echo '<p>Test de : <a href=\"' . $url . '\" target=\"_blank\">' . $url . '</a></p>';
}

echo '<hr>';
echo '<p><strong>Actions à faire :</strong></p>';
echo '<ol>';
echo '<li>Vérifier que les liens ci-dessus fonctionnent (cliquez dessus)</li>';
echo '<li>Recharger votre page d\'accueil</li>';
echo '<li>Supprimer ce fichier après utilisation</li>';
echo '</ol>';
