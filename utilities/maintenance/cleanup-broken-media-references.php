<?php
/**
 * Cleanup Broken Media References
 * 
 * This script finds and removes references to deleted/missing media attachments
 * that cause 404 errors in the WordPress editor.
 * 
 * Usage: Access via browser with admin privileges
 * URL: /wp-content/themes/archi-graph-template/utilities/maintenance/cleanup-broken-media-references.php
 */

// Load WordPress
require_once('../../../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    wp_die(__('Vous n\'avez pas les permissions nécessaires.', 'archi-graph'));
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nettoyage des Références Média</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #f0f0f1;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1d2327;
            margin-bottom: 10px;
        }
        .description {
            color: #646970;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .stats {
            background: #f6f7f7;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dcdcde;
        }
        .stat-item:last-child {
            border-bottom: none;
        }
        .stat-label {
            font-weight: 600;
            color: #1d2327;
        }
        .stat-value {
            color: #2271b1;
            font-weight: 600;
        }
        .results {
            margin-top: 20px;
        }
        .result-item {
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #2271b1;
            background: #f6f7f7;
        }
        .result-item.error {
            border-left-color: #d63638;
            background: #fcf0f1;
        }
        .result-item.success {
            border-left-color: #00a32a;
            background: #f0f6fc;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #2271b1;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .button:hover {
            background: #135e96;
        }
        .button.secondary {
            background: #646970;
        }
        .button.secondary:hover {
            background: #50575e;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dcdcde;
        }
        th {
            background: #f6f7f7;
            font-weight: 600;
            color: #1d2327;
        }
        tr:hover {
            background: #f6f7f7;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge.error {
            background: #d63638;
            color: white;
        }
        .badge.warning {
            background: #dba617;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧹 Nettoyage des Références Média</h1>
        <p class="description">
            Cet outil identifie et nettoie les références à des fichiers média supprimés ou manquants 
            qui causent des erreurs 404 dans l'éditeur WordPress/Gutenberg.
        </p>

        <?php
        if (isset($_GET['action']) && $_GET['action'] === 'cleanup' && check_admin_referer('archi_cleanup_media', 'nonce')) {
            echo '<div class="results">';
            
            // Find all posts with featured images
            $posts = get_posts([
                'post_type' => ['post', 'archi_project', 'archi_illustration'],
                'post_status' => 'any',
                'posts_per_page' => -1,
                'fields' => 'ids'
            ]);
            
            $cleaned = 0;
            $checked = 0;
            $broken_refs = [];
            
            foreach ($posts as $post_id) {
                $checked++;
                $thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);
                
                if ($thumbnail_id) {
                    // Check if attachment exists
                    $attachment = get_post($thumbnail_id);
                    
                    if (!$attachment || $attachment->post_type !== 'attachment') {
                        // Broken reference found
                        delete_post_meta($post_id, '_thumbnail_id');
                        $cleaned++;
                        
                        $post = get_post($post_id);
                        $broken_refs[] = [
                            'post_id' => $post_id,
                            'post_title' => $post->post_title,
                            'post_type' => $post->post_type,
                            'missing_attachment_id' => $thumbnail_id
                        ];
                    }
                }
            }
            
            echo '<div class="result-item success">';
            echo '<strong>✅ Nettoyage terminé</strong><br>';
            echo sprintf(__('%d posts vérifiés, %d références cassées nettoyées', 'archi-graph'), $checked, $cleaned);
            echo '</div>';
            
            if (!empty($broken_refs)) {
                echo '<h3>Références nettoyées :</h3>';
                echo '<table>';
                echo '<thead><tr>';
                echo '<th>ID Post</th><th>Titre</th><th>Type</th><th>ID Média Manquant</th>';
                echo '</tr></thead><tbody>';
                
                foreach ($broken_refs as $ref) {
                    echo '<tr>';
                    echo '<td><a href="' . get_edit_post_link($ref['post_id']) . '" target="_blank">' . $ref['post_id'] . '</a></td>';
                    echo '<td>' . esc_html($ref['post_title']) . '</td>';
                    echo '<td><span class="badge warning">' . esc_html($ref['post_type']) . '</span></td>';
                    echo '<td><span class="badge error">' . $ref['missing_attachment_id'] . '</span></td>';
                    echo '</tr>';
                }
                
                echo '</tbody></table>';
            }
            
            echo '</div>';
            echo '<p><a href="?" class="button secondary">Retour</a></p>';
            
        } else {
            // Scan for broken references
            global $wpdb;
            
            $broken_count = 0;
            $posts_with_thumbnails = $wpdb->get_results("
                SELECT pm.post_id, pm.meta_value as attachment_id, p.post_title, p.post_type
                FROM {$wpdb->postmeta} pm
                INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
                WHERE pm.meta_key = '_thumbnail_id'
                AND p.post_type IN ('post', 'archi_project', 'archi_illustration')
            ");
            
            $broken_items = [];
            foreach ($posts_with_thumbnails as $item) {
                $attachment = get_post($item->attachment_id);
                if (!$attachment || $attachment->post_type !== 'attachment') {
                    $broken_count++;
                    $broken_items[] = $item;
                }
            }
            
            // Display statistics
            echo '<div class="stats">';
            echo '<div class="stat-item">';
            echo '<span class="stat-label">Posts avec image à la une :</span>';
            echo '<span class="stat-value">' . count($posts_with_thumbnails) . '</span>';
            echo '</div>';
            echo '<div class="stat-item">';
            echo '<span class="stat-label">Références cassées détectées :</span>';
            echo '<span class="stat-value">' . $broken_count . '</span>';
            echo '</div>';
            echo '</div>';
            
            if ($broken_count > 0) {
                echo '<div class="result-item error">';
                echo '<strong>⚠️ Références cassées détectées</strong><br>';
                echo sprintf(__('%d posts ont des références à des médias supprimés ou manquants.', 'archi-graph'), $broken_count);
                echo '</div>';
                
                echo '<h3>Détails des références cassées :</h3>';
                echo '<table>';
                echo '<thead><tr>';
                echo '<th>ID Post</th><th>Titre</th><th>Type</th><th>ID Média Manquant</th>';
                echo '</tr></thead><tbody>';
                
                foreach ($broken_items as $item) {
                    echo '<tr>';
                    echo '<td><a href="' . get_edit_post_link($item->post_id) . '" target="_blank">' . $item->post_id . '</a></td>';
                    echo '<td>' . esc_html($item->post_title) . '</td>';
                    echo '<td><span class="badge warning">' . esc_html($item->post_type) . '</span></td>';
                    echo '<td><span class="badge error">' . $item->attachment_id . '</span></td>';
                    echo '</tr>';
                }
                
                echo '</tbody></table>';
                
                $cleanup_url = wp_nonce_url('?action=cleanup', 'archi_cleanup_media', 'nonce');
                echo '<p><a href="' . $cleanup_url . '" class="button">Nettoyer les références cassées</a></p>';
                
            } else {
                echo '<div class="result-item success">';
                echo '<strong>✅ Aucune référence cassée détectée</strong><br>';
                echo __('Toutes les références média sont valides.', 'archi-graph');
                echo '</div>';
            }
        }
        ?>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #dcdcde;">
        
        <h3>ℹ️ Informations</h3>
        <p><strong>Ce que fait cet outil :</strong></p>
        <ul>
            <li>Vérifie tous les posts (articles, projets, illustrations) pour les images à la une</li>
            <li>Détecte les références à des médias qui n'existent plus</li>
            <li>Nettoie les métadonnées <code>_thumbnail_id</code> cassées</li>
            <li>Prévient les erreurs 404 dans l'éditeur Gutenberg</li>
        </ul>
        
        <p><strong>Quand utiliser cet outil :</strong></p>
        <ul>
            <li>Après avoir supprimé des médias de la bibliothèque</li>
            <li>Si vous voyez des erreurs 404 dans la console du navigateur</li>
            <li>Lors de la maintenance régulière du site</li>
        </ul>
        
        <p><a href="<?php echo admin_url(); ?>" class="button secondary">← Retour au tableau de bord</a></p>
    </div>
</body>
</html>
