<?php
/**
 * Test d'installation du système de blocs
 * 
 * Ce fichier peut être supprimé après vérification
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Vérification que tous les composants sont chargés
 */
function archi_blocks_system_check() {
    $checks = [
        'blocks_file' => file_exists(get_template_directory() . '/inc/gutenberg-blocks.php'),
        'blocks_css' => file_exists(get_template_directory() . '/assets/css/blocks.css'),
        'editor_css' => file_exists(get_template_directory() . '/assets/css/blocks-editor.css'),
        'editor_js_source' => file_exists(get_template_directory() . '/assets/js/blocks-editor.js'),
        'editor_js_compiled' => file_exists(get_template_directory() . '/assets/dist/blocks/blocks-editor.js'),
        'wp_version' => version_compare(get_bloginfo('version'), '5.0', '>=')
    ];
    
    return $checks;
}

/**
 * Affichage du rapport de vérification en admin
 */
function archi_blocks_admin_notice() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $checks = archi_blocks_system_check();
    $all_ok = !in_array(false, $checks, true);
    
    $class = $all_ok ? 'notice-success' : 'notice-warning';
    $title = $all_ok ? 'Système de Blocs Archi Graph : ✅ Opérationnel' : 'Système de Blocs Archi Graph : ⚠️ Vérification requise';
    
    echo '<div class="notice ' . $class . ' is-dismissible">';
    echo '<h3>' . $title . '</h3>';
    echo '<ul>';
    
    $labels = [
        'blocks_file' => 'Fichier de définition des blocs',
        'blocks_css' => 'Styles CSS des blocs (frontend)',
        'editor_css' => 'Styles CSS éditeur',
        'editor_js_source' => 'Scripts JavaScript source',
        'editor_js_compiled' => 'Scripts JavaScript compilés',
        'wp_version' => 'Version WordPress compatible (5.0+)'
    ];
    
    foreach ($checks as $key => $status) {
        $icon = $status ? '✅' : '❌';
        $label = $labels[$key] ?? $key;
        echo '<li>' . $icon . ' ' . $label . '</li>';
    }
    
    echo '</ul>';
    
    if (!$all_ok) {
        echo '<p><strong>Actions recommandées :</strong></p>';
        echo '<ul>';
        if (!$checks['editor_js_compiled']) {
            echo '<li>Exécuter <code>npm run build:blocks</code> pour compiler les scripts</li>';
        }
        if (!$checks['wp_version']) {
            echo '<li>Mettre à jour WordPress vers la version 5.0 ou supérieure</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>🎉 <strong>Tous les blocs Gutenberg sont prêts à être utilisés !</strong></p>';
        echo '<p>Allez dans l\'éditeur d\'articles/pages et recherchez la catégorie "Archi Graph" dans l\'ajout de blocs.</p>';
    }
    
    echo '</div>';
}

// Afficher le rapport pendant 30 jours après activation du thème
$theme_activated = get_option('archi_theme_activated', 0);
if ($theme_activated && (time() - $theme_activated < 30 * DAY_IN_SECONDS)) {
    add_action('admin_notices', 'archi_blocks_admin_notice');
}

/**
 * Marquer la date d'activation du thème
 */
function archi_mark_theme_activation() {
    add_option('archi_theme_activated', time());
}
add_action('after_switch_theme', 'archi_mark_theme_activation');