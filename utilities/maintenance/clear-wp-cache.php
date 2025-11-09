<?php
/**
 * Script de vidage du cache WordPress
 * À exécuter depuis le navigateur : http://localhost/wordpress/wp-content/themes/archi-graph-template/clear-wp-cache.php
 */

// Charger WordPress
require_once('../../../../../wp-load.php');

// Vérifier que c'est bien en environnement de développement
if (!defined('WP_DEBUG') || !WP_DEBUG) {
    die('Ce script ne peut être exécuté qu\'en mode développement (WP_DEBUG doit être activé).');
}

echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vidage du cache WordPress</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .container {
            background: white;
            border-radius: 16px;
            padding: 3rem;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 2rem;
        }
        .status {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            border-left: 4px solid #3498db;
        }
        .status.success {
            border-left-color: #2ecc71;
            background: #d5f4e6;
        }
        .status.error {
            border-left-color: #e74c3c;
            background: #fadbd8;
        }
        .status h3 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        .status p {
            color: #5a6c7d;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn {
            flex: 1;
            padding: 1rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            transition: all 0.2s ease;
        }
        .btn-primary {
            background: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        .btn-success {
            background: #2ecc71;
            color: white;
        }
        .btn-success:hover {
            background: #27ae60;
            transform: translateY(-2px);
        }
        ul {
            list-style: none;
            padding: 0;
        }
        ul li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e1e8ed;
        }
        ul li:last-child {
            border-bottom: none;
        }
        .icon {
            font-size: 3rem;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🧹</div>
        <h1>Vidage du cache WordPress</h1>';

$results = [];
$has_error = false;

// 1. Vider le cache des objets WordPress
if (function_exists('wp_cache_flush')) {
    $flushed = wp_cache_flush();
    $results[] = [
        'title' => 'Cache d\'objets WordPress',
        'success' => $flushed,
        'message' => $flushed ? 'Vidé avec succès' : 'Échec du vidage'
    ];
    if (!$flushed) $has_error = true;
} else {
    $results[] = [
        'title' => 'Cache d\'objets WordPress',
        'success' => false,
        'message' => 'Fonction wp_cache_flush() non disponible'
    ];
}

// 2. Vider les transients
global $wpdb;
$deleted = $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%'");
$results[] = [
    'title' => 'Transients WordPress',
    'success' => $deleted !== false,
    'message' => $deleted !== false ? "$deleted transients supprimés" : 'Échec de la suppression'
];
if ($deleted === false) $has_error = true;

// 3. Vider le cache des options
wp_cache_delete('alloptions', 'options');
$results[] = [
    'title' => 'Cache des options',
    'success' => true,
    'message' => 'Vidé avec succès'
];

// 4. Vider les règles de réécriture
flush_rewrite_rules(false);
$results[] = [
    'title' => 'Règles de réécriture',
    'success' => true,
    'message' => 'Regénérées avec succès'
];

// 5. Supprimer les fichiers de cache si le dossier existe
$cache_dir = WP_CONTENT_DIR . '/cache/';
if (is_dir($cache_dir)) {
    $files_deleted = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cache_dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            @unlink($file->getRealPath());
            $files_deleted++;
        }
    }
    
    $results[] = [
        'title' => 'Fichiers de cache',
        'success' => true,
        'message' => "$files_deleted fichiers supprimés du dossier cache/"
    ];
}

// 6. Nettoyer le cache du thème actuel (si des fichiers générés existent)
$theme_cache = get_template_directory() . '/cache/';
if (is_dir($theme_cache)) {
    $theme_files = 0;
    $files = glob($theme_cache . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            @unlink($file);
            $theme_files++;
        }
    }
    $results[] = [
        'title' => 'Cache du thème',
        'success' => true,
        'message' => "$theme_files fichiers supprimés"
    ];
}

// Afficher les résultats
echo '<div class="results">';
foreach ($results as $result) {
    $class = $result['success'] ? 'success' : 'error';
    $icon = $result['success'] ? '✅' : '❌';
    echo "<div class='status $class'>
            <h3>$icon {$result['title']}</h3>
            <p>{$result['message']}</p>
          </div>";
}
echo '</div>';

// Instructions supplémentaires
echo '<div class="status">
        <h3>💡 Prochaines étapes</h3>
        <ul>
            <li>✅ Cache WordPress vidé</li>
            <li>🔄 Rechargez votre page avec Ctrl+Shift+R (ou Cmd+Shift+R sur Mac)</li>
            <li>🧹 Videz également le cache de votre navigateur (Ctrl+Shift+Del)</li>
            <li>🔍 Testez l\'affichage des labels au survol des nœuds</li>
        </ul>
      </div>';

echo '<div class="actions">
        <a href="/" class="btn btn-primary">🏠 Retour à l\'accueil</a>
        <a href="javascript:location.reload()" class="btn btn-success">🔄 Vider à nouveau</a>
      </div>';

echo '</div>
</body>
</html>';
