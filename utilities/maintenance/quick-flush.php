<?php
/**
 * Quick Flush REST API
 * Visit: http://localhost/wordpress/wp-content/themes/archi-graph-template/quick-flush.php
 * This will automatically flush and redirect
 */

// Load WordPress (go up 3 levels: archi-graph-template -> themes -> wp-content -> wordpress root)
require_once(dirname(__FILE__) . '/../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    wp_die('Unauthorized access', 'Error', ['response' => 403]);
}

// Flush rewrite rules
flush_rewrite_rules(true);

// Force REST API initialization
do_action('rest_api_init');

// Test the endpoint
$test_url = home_url('/wp-json/archi/v1/articles');
$response = wp_remote_get($test_url);
$status_code = is_wp_error($response) ? 'Error' : wp_remote_retrieve_response_code($response);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Flush Complete</title>
    <meta http-equiv="refresh" content="3;url=<?php echo esc_url(home_url()); ?>">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 500px;
        }
        h1 {
            color: #333;
            margin: 0 0 20px 0;
            font-size: 32px;
        }
        .status {
            font-size: 64px;
            margin: 20px 0;
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        p {
            color: #666;
            line-height: 1.6;
            margin: 10px 0;
        }
        .endpoint {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 14px;
            word-break: break-all;
        }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="status <?php echo ($status_code === 200) ? 'success' : 'error'; ?>">
            <?php echo ($status_code === 200) ? '✓' : '⚠'; ?>
        </div>
        <h1>REST API Flushed</h1>
        <p>Rewrite rules have been flushed successfully.</p>
        
        <div class="endpoint">
            <strong>Test Endpoint:</strong><br>
            <?php echo esc_html($test_url); ?><br>
            <strong>Status:</strong> <?php echo esc_html($status_code); ?>
        </div>
        
        <?php if ($status_code === 200): ?>
            <p class="success">✓ REST API is working correctly!</p>
        <?php else: ?>
            <p class="error">⚠ Endpoint returned status <?php echo esc_html($status_code); ?></p>
            <p>You may need to visit <a href="<?php echo admin_url('options-permalink.php'); ?>">Permalink Settings</a> and save.</p>
        <?php endif; ?>
        
        <div class="spinner"></div>
        <p>Redirecting to homepage in 3 seconds...</p>
        
        <p><small>Delete this file after use for security.</small></p>
    </div>
</body>
</html>
