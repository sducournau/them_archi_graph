<?php
/**
 * Flush REST API Rewrite Rules
 * 
 * Visit this file in your browser to flush rewrite rules and ensure REST API endpoints are registered.
 * URL: http://localhost/wordpress/wp-content/themes/archi-graph-template/flush-rest-api.php
 * 
 * After running this, delete this file for security.
 */

// Load WordPress
require_once('../../../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    wp_die('Unauthorized access', 'Error', ['response' => 403]);
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Flush REST API - Archi Graph Theme</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-top: 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #c3e6cb;
            margin: 20px 0;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #bee5eb;
            margin: 20px 0;
        }
        .test-results {
            margin-top: 30px;
        }
        .endpoint {
            background: #f8f9fa;
            padding: 10px;
            margin: 10px 0;
            border-left: 4px solid #667eea;
            font-family: monospace;
        }
        .endpoint.success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .endpoint.error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .button:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔧 Flush REST API Rewrite Rules</h1>
        
        <?php
        // Flush rewrite rules
        flush_rewrite_rules(true);
        
        // Force REST API initialization
        do_action('rest_api_init');
        
        echo '<div class="success">';
        echo '<strong>✓ Rewrite rules flushed successfully!</strong><br>';
        echo 'REST API endpoints should now be registered and accessible.';
        echo '</div>';
        
        // Test REST API endpoints
        echo '<div class="test-results">';
        echo '<h2>Testing REST API Endpoints</h2>';
        
        $endpoints_to_test = [
            '/wp-json/archi/v1/articles',
            '/wp-json/archi/v1/categories'
        ];
        
        foreach ($endpoints_to_test as $endpoint) {
            $url = home_url($endpoint);
            $response = wp_remote_get($url);
            
            echo '<div class="endpoint ';
            if (is_wp_error($response)) {
                echo 'error">';
                echo '<strong>❌ ' . esc_html($endpoint) . '</strong><br>';
                echo 'Error: ' . esc_html($response->get_error_message());
            } else {
                $status_code = wp_remote_retrieve_response_code($response);
                if ($status_code === 200) {
                    echo 'success">';
                    echo '<strong>✓ ' . esc_html($endpoint) . '</strong><br>';
                    echo 'Status: ' . $status_code . ' OK<br>';
                    
                    $body = wp_remote_retrieve_body($response);
                    $data = json_decode($body, true);
                    
                    if (isset($data['articles'])) {
                        echo 'Articles found: ' . count($data['articles']);
                    } elseif (is_array($data)) {
                        echo 'Categories found: ' . count($data);
                    }
                } else {
                    echo 'error">';
                    echo '<strong>❌ ' . esc_html($endpoint) . '</strong><br>';
                    echo 'Status: ' . $status_code . ' (Expected 200)';
                }
            }
            echo '</div>';
        }
        
        echo '</div>';
        
        // Debug information
        echo '<div class="info">';
        echo '<h3>Debug Information</h3>';
        echo '<strong>WordPress URL:</strong> ' . esc_html(home_url()) . '<br>';
        echo '<strong>REST API Base:</strong> ' . esc_html(rest_get_url_prefix()) . '<br>';
        echo '<strong>Theme Directory:</strong> ' . esc_html(get_template_directory()) . '<br>';
        
        // Check if rest-api.php is loaded
        $rest_api_file = get_template_directory() . '/inc/rest-api.php';
        echo '<strong>REST API File:</strong> ';
        if (file_exists($rest_api_file)) {
            echo '✓ Found<br>';
        } else {
            echo '❌ Not Found<br>';
        }
        
        // Check if action is registered
        global $wp_filter;
        if (isset($wp_filter['rest_api_init'])) {
            echo '<strong>rest_api_init hooks:</strong> ' . count($wp_filter['rest_api_init']->callbacks) . ' registered<br>';
        }
        
        echo '</div>';
        
        echo '<div class="info">';
        echo '<h3>⚠️ Security Notice</h3>';
        echo 'For security reasons, please <strong>delete this file</strong> after use:<br>';
        echo '<code>' . esc_html(__FILE__) . '</code>';
        echo '</div>';
        
        echo '<a href="' . esc_url(home_url()) . '" class="button">← Back to Site</a>';
        ?>
    </div>
</body>
</html>
