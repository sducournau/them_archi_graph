#!/usr/bin/env php
<?php
/**
 * Script de test pour le système de liens amélioré du graphe
 * 
 * Ce script teste les nouveaux critères de création de liens entre nœuds
 * 
 * Usage: php test-graph-links.php
 */

// Charger WordPress
require_once(__DIR__ . '/../../../.../wp-load.php');

if (!defined('ABSPATH')) {
    die('WordPress non chargé. Lancez ce script depuis wp-content/themes/archi-graph-template/');
}

echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "  TEST DES AMÉLIORATIONS DU SYSTÈME DE LIENS DU GRAPHE\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// Statistiques
$stats = [
    'total_nodes' => 0,
    'project_nodes' => 0,
    'illustration_nodes' => 0,
    'post_nodes' => 0,
    'nodes_with_project_meta' => 0,
    'nodes_with_illustration_meta' => 0,
    'potential_links' => [],
];

// Récupérer tous les nœuds visibles dans le graphe
$args = [
    'post_type' => ['post', 'archi_project', 'archi_illustration'],
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_query' => [
        [
            'key' => '_archi_show_in_graph',
            'value' => '1',
            'compare' => '='
        ]
    ]
];

$posts = get_posts($args);
$stats['total_nodes'] = count($posts);

echo "📊 ANALYSE DES NŒUDS\n";
echo str_repeat('─', 59) . "\n";
echo "Total de nœuds visibles: {$stats['total_nodes']}\n\n";

// Analyser chaque nœud
$nodes_data = [];

foreach ($posts as $post) {
    $node = [
        'id' => $post->ID,
        'title' => $post->post_title,
        'type' => $post->post_type,
        'categories' => wp_get_post_categories($post->ID),
        'tags' => wp_get_post_tags($post->ID, ['fields' => 'ids']),
    ];
    
    // Compter par type
    if ($post->post_type === 'archi_project') {
        $stats['project_nodes']++;
        
        // Récupérer les métadonnées
        $node['project_meta'] = [
            'client' => get_post_meta($post->ID, '_archi_project_client', true),
            'location' => get_post_meta($post->ID, '_archi_project_location', true),
            'project_type' => get_post_meta($post->ID, '_archi_project_type', true),
            'surface' => get_post_meta($post->ID, '_archi_project_surface', true),
        ];
        
        if (!empty(array_filter($node['project_meta']))) {
            $stats['nodes_with_project_meta']++;
        }
        
    } elseif ($post->post_type === 'archi_illustration') {
        $stats['illustration_nodes']++;
        
        // Récupérer les métadonnées
        $node['illustration_meta'] = [
            'technique' => get_post_meta($post->ID, '_archi_illustration_technique', true),
            'software' => get_post_meta($post->ID, '_archi_illustration_software', true),
            'project_link' => get_post_meta($post->ID, '_archi_illustration_project_link', true),
        ];
        
        if (!empty(array_filter($node['illustration_meta']))) {
            $stats['nodes_with_illustration_meta']++;
        }
        
    } else {
        $stats['post_nodes']++;
    }
    
    $nodes_data[] = $node;
}

echo "Projets: {$stats['project_nodes']}\n";
echo "  → Avec métadonnées: {$stats['nodes_with_project_meta']}\n";
echo "Illustrations: {$stats['illustration_nodes']}\n";
echo "  → Avec métadonnées: {$stats['nodes_with_illustration_meta']}\n";
echo "Articles: {$stats['post_nodes']}\n\n";

// Analyser les liens potentiels
echo "🔗 ANALYSE DES LIENS POTENTIELS\n";
echo str_repeat('─', 59) . "\n";

$potential_links = [
    'same_client' => [],
    'same_location' => [],
    'same_technique' => [],
    'same_software' => [],
    'linked_projects' => [],
];

// Liens entre projets (même client)
for ($i = 0; $i < count($nodes_data); $i++) {
    for ($j = $i + 1; $j < count($nodes_data); $j++) {
        $nodeA = $nodes_data[$i];
        $nodeB = $nodes_data[$j];
        
        // Projets avec même client
        if ($nodeA['type'] === 'archi_project' && $nodeB['type'] === 'archi_project') {
            if (!empty($nodeA['project_meta']['client']) && 
                !empty($nodeB['project_meta']['client']) &&
                strtolower($nodeA['project_meta']['client']) === strtolower($nodeB['project_meta']['client'])) {
                $potential_links['same_client'][] = [
                    'a' => $nodeA['title'],
                    'b' => $nodeB['title'],
                    'client' => $nodeA['project_meta']['client'],
                ];
            }
            
            // Projets avec même localisation
            if (!empty($nodeA['project_meta']['location']) && 
                !empty($nodeB['project_meta']['location'])) {
                $locA = strtolower($nodeA['project_meta']['location']);
                $locB = strtolower($nodeB['project_meta']['location']);
                
                if ($locA === $locB || strpos($locA, $locB) !== false || strpos($locB, $locA) !== false) {
                    $potential_links['same_location'][] = [
                        'a' => $nodeA['title'],
                        'b' => $nodeB['title'],
                        'location' => $nodeA['project_meta']['location'],
                    ];
                }
            }
        }
        
        // Illustrations avec même technique
        if ($nodeA['type'] === 'archi_illustration' && $nodeB['type'] === 'archi_illustration') {
            if (!empty($nodeA['illustration_meta']['technique']) && 
                !empty($nodeB['illustration_meta']['technique']) &&
                strtolower($nodeA['illustration_meta']['technique']) === strtolower($nodeB['illustration_meta']['technique'])) {
                $potential_links['same_technique'][] = [
                    'a' => $nodeA['title'],
                    'b' => $nodeB['title'],
                    'technique' => $nodeA['illustration_meta']['technique'],
                ];
            }
            
            // Illustrations avec même logiciel
            if (!empty($nodeA['illustration_meta']['software']) && 
                !empty($nodeB['illustration_meta']['software']) &&
                strtolower($nodeA['illustration_meta']['software']) === strtolower($nodeB['illustration_meta']['software'])) {
                $potential_links['same_software'][] = [
                    'a' => $nodeA['title'],
                    'b' => $nodeB['title'],
                    'software' => $nodeA['illustration_meta']['software'],
                ];
            }
        }
        
        // Liens projet ↔ illustration
        if (($nodeA['type'] === 'archi_project' && $nodeB['type'] === 'archi_illustration') ||
            ($nodeA['type'] === 'archi_illustration' && $nodeB['type'] === 'archi_project')) {
            
            $illustration = $nodeA['type'] === 'archi_illustration' ? $nodeA : $nodeB;
            $project = $nodeA['type'] === 'archi_project' ? $nodeA : $nodeB;
            
            if (!empty($illustration['illustration_meta']['project_link']) &&
                $illustration['illustration_meta']['project_link'] == $project['id']) {
                $potential_links['linked_projects'][] = [
                    'project' => $project['title'],
                    'illustration' => $illustration['title'],
                ];
            }
        }
    }
}

// Afficher les résultats
echo "\n✨ Liens par MÊME CLIENT (35 pts):\n";
if (empty($potential_links['same_client'])) {
    echo "  Aucun lien trouvé\n";
} else {
    foreach ($potential_links['same_client'] as $link) {
        echo "  • {$link['a']} ↔ {$link['b']}\n";
        echo "    Client: {$link['client']}\n";
    }
}

echo "\n✨ Liens par MÊME LOCALISATION (25 pts):\n";
if (empty($potential_links['same_location'])) {
    echo "  Aucun lien trouvé\n";
} else {
    foreach ($potential_links['same_location'] as $link) {
        echo "  • {$link['a']} ↔ {$link['b']}\n";
        echo "    Localisation: {$link['location']}\n";
    }
}

echo "\n✨ Liens par MÊME TECHNIQUE (30 pts):\n";
if (empty($potential_links['same_technique'])) {
    echo "  Aucun lien trouvé\n";
} else {
    foreach ($potential_links['same_technique'] as $link) {
        echo "  • {$link['a']} ↔ {$link['b']}\n";
        echo "    Technique: {$link['technique']}\n";
    }
}

echo "\n✨ Liens par MÊME LOGICIEL (20 pts):\n";
if (empty($potential_links['same_software'])) {
    echo "  Aucun lien trouvé\n";
} else {
    foreach ($potential_links['same_software'] as $link) {
        echo "  • {$link['a']} ↔ {$link['b']}\n";
        echo "    Logiciel: {$link['software']}\n";
    }
}

echo "\n✨ Liens PROJET ↔ ILLUSTRATION (50 pts):\n";
if (empty($potential_links['linked_projects'])) {
    echo "  Aucun lien trouvé\n";
} else {
    foreach ($potential_links['linked_projects'] as $link) {
        echo "  • {$link['project']} ↔ {$link['illustration']}\n";
    }
}

// Résumé final
echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "  RÉSUMÉ\n";
echo "═══════════════════════════════════════════════════════════\n";

$total_potential_links = 
    count($potential_links['same_client']) +
    count($potential_links['same_location']) +
    count($potential_links['same_technique']) +
    count($potential_links['same_software']) +
    count($potential_links['linked_projects']);

echo "\nTotal de liens potentiels détectés: {$total_potential_links}\n";

if ($total_potential_links > 0) {
    echo "\n✅ Le système devrait créer des liens basés sur ces critères.\n";
    echo "   Rechargez le graphe pour voir les nouvelles connexions.\n";
} else {
    echo "\n⚠️  Aucun lien potentiel détecté avec les nouveaux critères.\n";
    echo "   Suggestions:\n";
    echo "   1. Ajoutez des métadonnées aux projets (client, localisation)\n";
    echo "   2. Ajoutez des métadonnées aux illustrations (technique, logiciel)\n";
    echo "   3. Liez des illustrations à des projets\n";
}

echo "\n📝 Les liens basés sur catégories/tags/contenu restent actifs.\n";
echo "\n";
