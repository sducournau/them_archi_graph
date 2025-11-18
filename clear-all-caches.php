<?php
/**
 * Script de vidage complet de tous les caches
 * À exécuter via wp-cli ou navigateur en cas de problème de cache persistant
 * Usage: php clear-all-caches.php
 */

// Charger WordPress
require_once(__DIR__ . '/../../../wp-load.php');

echo "🔄 Nettoyage de tous les caches...\n\n";

// 1. Vider le cache objet WordPress
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "✅ Cache objet WordPress vidé\n";
}

// 2. Vider tous les transients
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_%'");
echo "✅ Tous les transients vidés\n";

// 3. Vider le cache de metadata
clean_post_cache(null);
echo "✅ Cache de metadata vidé\n";

// 4. Réinitialiser les permalinks (flush rewrite rules)
flush_rewrite_rules();
echo "✅ Règles de réécriture réinitialisées\n";

// 5. Vider le cache de thème
if (function_exists('wp_clean_themes_cache')) {
    wp_clean_themes_cache();
    echo "✅ Cache de thème vidé\n";
}

// 6. OPcache (si disponible)
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache réinitialisé\n";
} else {
    echo "⚠️  OPcache non disponible\n";
}

// 7. APCu (si disponible)
if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    echo "✅ APCu vidé\n";
} else {
    echo "⚠️  APCu non disponible\n";
}

// 8. Vider le cache des scripts/styles enqueued
wp_cache_delete('alloptions', 'options');
echo "✅ Cache des options vidé\n";

echo "\n🎉 Nettoyage terminé !\n";
echo "💡 Effectuez un hard refresh dans le navigateur : Ctrl+Shift+R (ou Cmd+Shift+R sur Mac)\n\n";

// Afficher les timestamps actuels des fichiers JS
echo "📊 Timestamps actuels des fichiers JS:\n";
$theme_dir = get_template_directory();
$files = [
    'dist/js/vendors.bundle.js',
    'dist/js/app.bundle.js'
];

foreach ($files as $file) {
    $full_path = $theme_dir . '/' . $file;
    if (file_exists($full_path)) {
        $mtime = filemtime($full_path);
        $date = date('Y-m-d H:i:s', $mtime);
        echo "   $file: $mtime ($date)\n";
    }
}

echo "\n";
