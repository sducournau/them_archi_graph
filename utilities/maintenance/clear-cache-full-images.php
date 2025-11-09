<?php
/**
 * Script pour vider le cache de l'API REST et voir les changements d'images
 */

require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    die('Accès refusé');
}

// Vider le cache
delete_transient('archi_graph_articles');
delete_transient('archi_graph_articles_data');

// Vider tous les transients liés au graphique
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_archi_graph_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_archi_graph_%'");

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cache vidé - Images Full Scale</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .success {
            background: #4caf50;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #2196f3;
        }
        .test-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        h1, h2 {
            margin-top: 0;
        }
        ul {
            line-height: 1.8;
        }
        .button {
            display: inline-block;
            background: #2196f3;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            margin: 5px;
        }
        .button:hover {
            background: #1976d2;
        }
        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="success">
        <h1>✅ Cache vidé avec succès !</h1>
        <p>Les modifications suivantes ont été appliquées :</p>
    </div>

    <div class="info">
        <h2>📋 Modifications appliquées</h2>
        <ul>
            <li><strong>Tous les types de contenu</strong> : Articles, projets et illustrations utilisent maintenant l'image en taille réelle (full)</li>
            <li><strong>Qualité maximale</strong> : Aucune perte de qualité due au redimensionnement</li>
            <li><strong>Images originales</strong> : Les images sont affichées dans leur résolution d'origine</li>
            <li><strong>Cache API REST</strong> : Vidé pour afficher les nouvelles images</li>
        </ul>
    </div>

    <div class="test-section">
        <h2>🧪 Test des images</h2>
        <p>Vérifions les images pour tous les types de contenu :</p>
        
        <?php
        $test_posts = get_posts([
            'post_type' => ['post', 'archi_project', 'archi_illustration'],
            'post_status' => 'publish',
            'posts_per_page' => 5,
            'meta_query' => [
                [
                    'key' => '_archi_show_in_graph',
                    'value' => '1',
                    'compare' => '='
                ]
            ]
        ]);
        
        if (!empty($test_posts)) {
            echo '<table style="width: 100%; border-collapse: collapse;">';
            echo '<tr style="background: #f5f5f5; font-weight: bold;">';
            echo '<td style="padding: 10px; border: 1px solid #ddd;">Titre</td>';
            echo '<td style="padding: 10px; border: 1px solid #ddd;">Type</td>';
            echo '<td style="padding: 10px; border: 1px solid #ddd;">Image Full</td>';
            echo '<td style="padding: 10px; border: 1px solid #ddd;">Dimensions</td>';
            echo '</tr>';
            
            foreach ($test_posts as $post) {
                $full_url = get_the_post_thumbnail_url($post->ID, 'full');
                $attachment_id = get_post_thumbnail_id($post->ID);
                $dimensions = '';
                
                if ($attachment_id) {
                    $metadata = wp_get_attachment_metadata($attachment_id);
                    if ($metadata) {
                        $dimensions = $metadata['width'] . ' x ' . $metadata['height'] . 'px';
                    }
                }
                
                $post_type_obj = get_post_type_object($post->post_type);
                $post_type_label = $post_type_obj ? $post_type_obj->labels->singular_name : $post->post_type;
                
                echo '<tr>';
                echo '<td style="padding: 10px; border: 1px solid #ddd;">' . esc_html($post->post_title) . '</td>';
                echo '<td style="padding: 10px; border: 1px solid #ddd;"><span style="background: #2196f3; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px;">' . esc_html($post_type_label) . '</span></td>';
                echo '<td style="padding: 10px; border: 1px solid #ddd;">';
                if ($full_url) {
                    echo '<img src="' . esc_url($full_url) . '" style="max-width: 150px; height: auto;">';
                } else {
                    echo '❌ Pas d\'image';
                }
                echo '</td>';
                echo '<td style="padding: 10px; border: 1px solid #ddd;">' . $dimensions . '</td>';
                echo '</tr>';
            }
            
            echo '</table>';
        } else {
            echo '<p>⚠️ Aucun article trouvé dans le graphique</p>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>🔗 Actions</h2>
        <a href="<?php echo home_url(); ?>" class="button">Voir la page d'accueil</a>
        <a href="<?php echo home_url('/wp-json/archi/v1/articles'); ?>" class="button" target="_blank">Tester l'API REST</a>
        <a href="<?php echo admin_url('edit.php'); ?>" class="button">Gérer les articles</a>
    </div>

    <div class="info" style="margin-top: 20px;">
        <h2>💡 Notes importantes</h2>
        <ul>
            <li><strong>Tous les contenus</strong> utilisent maintenant leurs images en taille réelle (full scale)</li>
            <li><strong>Qualité maximale</strong> : Articles, projets ET illustrations affichent leurs images d'origine</li>
            <li><strong>Performance</strong> : Cela peut augmenter le temps de chargement si les images sont très grandes</li>
            <li><strong>Recommandation</strong> : Optimisez vos images pour le web avant de les télécharger</li>
            <li><strong>Résolution idéale</strong> : Entre 800x800px et 1200x1200px pour un bon équilibre qualité/performance</li>
            <li><strong>Format</strong> : JPEG pour les photos, PNG pour les images avec transparence</li>
        </ul>
    </div>

    <div class="test-section">
        <h2>📊 Test API JSON</h2>
        <pre><?php
        // Faire un appel à l'API pour voir le résultat
        $api_url = home_url('/wp-json/archi/v1/articles');
        $response = wp_remote_get($api_url);
        
        if (!is_wp_error($response)) {
            $data = json_decode(wp_remote_retrieve_body($response), true);
            if (!empty($data)) {
                // Afficher seulement le premier article pour l'exemple
                $first = array_shift($data);
                echo json_encode([
                    'id' => $first['id'],
                    'title' => $first['title'],
                    'post_type' => $first['post_type'],
                    'thumbnail' => $first['thumbnail'],
                    'thumbnail_large' => $first['thumbnail_large'],
                    'node_size' => $first['custom_meta']['node_size']
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }
        }
        ?></pre>
    </div>
</body>
</html>
