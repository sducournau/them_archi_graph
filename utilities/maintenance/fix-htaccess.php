<?php
/**
 * Fix WordPress .htaccess and Permalinks
 * Visit: http://localhost/wordpress/wp-content/themes/archi-graph-template/fix-htaccess.php
 */

// Load WordPress (go up 3 levels: archi-graph-template -> themes -> wp-content -> wordpress root)
require_once(dirname(__FILE__) . '/../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    wp_die('Unauthorized access', 'Error', ['response' => 403]);
}

$messages = [];
$errors = [];

// Check if mod_rewrite is available
function check_mod_rewrite() {
    if (function_exists('apache_get_modules')) {
        $modules = apache_get_modules();
        return in_array('mod_rewrite', $modules);
    }
    return true; // Assume it's available if we can't check
}

// Fix .htaccess
$htaccess_file = ABSPATH . '.htaccess';
$htaccess_content = "# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /wordpress/
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /wordpress/index.php [L]
</IfModule>
# END WordPress";

// Backup existing .htaccess
if (file_exists($htaccess_file)) {
    $backup_file = $htaccess_file . '.backup.' . time();
    if (copy($htaccess_file, $backup_file)) {
        $messages[] = "✓ Backed up existing .htaccess to: " . basename($backup_file);
    }
}

// Write new .htaccess
if (is_writable(dirname($htaccess_file))) {
    $result = file_put_contents($htaccess_file, $htaccess_content);
    if ($result !== false) {
        $messages[] = "✓ Created new .htaccess file";
    } else {
        $errors[] = "❌ Failed to write .htaccess file";
    }
} else {
    $errors[] = "❌ WordPress root directory is not writable";
}

// Update permalink structure
update_option('permalink_structure', '/%postname%/');
$messages[] = "✓ Set permalink structure to /%postname%/";

// Flush rewrite rules
flush_rewrite_rules(true);
$messages[] = "✓ Flushed rewrite rules";

// Force REST API init
do_action('rest_api_init');
$messages[] = "✓ Initialized REST API";

// Test REST API endpoints
$test_results = [];
$endpoints_to_test = [
    'REST API Root' => '/wp-json/',
    'Archi Articles' => '/wp-json/archi/v1/articles',
    'Archi Categories' => '/wp-json/archi/v1/categories'
];

foreach ($endpoints_to_test as $name => $endpoint) {
    $url = home_url($endpoint);
    $response = wp_remote_get($url, ['timeout' => 10]);
    
    if (is_wp_error($response)) {
        $test_results[$name] = [
            'status' => 'error',
            'message' => $response->get_error_message()
        ];
    } else {
        $status_code = wp_remote_retrieve_response_code($response);
        $test_results[$name] = [
            'status' => ($status_code === 200) ? 'success' : 'warning',
            'code' => $status_code,
            'url' => $url
        ];
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fix .htaccess - Archi Graph</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h1, h2 {
            color: #333;
            margin-top: 0;
        }
        .message {
            padding: 12px 15px;
            border-radius: 4px;
            margin: 10px 0;
            border-left: 4px solid;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border-left-color: #ffc107;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border-left-color: #17a2b8;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 13px;
        }
        .test-result {
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            background: #f8f9fa;
        }
        .test-result.success {
            background: #d4edda;
            border-left: 4px solid #28a745;
        }
        .test-result.error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        .test-result.warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
        }
        .button:hover {
            background: #5568d3;
        }
        code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔧 WordPress .htaccess & Permalinks Fixed</h1>
        
        <?php if (!empty($messages)): ?>
            <h2>✓ Success Messages</h2>
            <?php foreach ($messages as $message): ?>
                <div class="message success"><?php echo esc_html($message); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <h2>❌ Errors</h2>
            <?php foreach ($errors as $error): ?>
                <div class="message error"><?php echo esc_html($error); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h2>📋 .htaccess Content</h2>
        <p>The following content has been written to <code><?php echo esc_html($htaccess_file); ?></code>:</p>
        <pre><?php echo esc_html($htaccess_content); ?></pre>
    </div>
    
    <div class="card">
        <h2>🧪 REST API Tests</h2>
        <?php foreach ($test_results as $name => $result): ?>
            <div class="test-result <?php echo esc_attr($result['status']); ?>">
                <strong><?php echo esc_html($name); ?></strong><br>
                <?php if (isset($result['code'])): ?>
                    Status: <?php echo esc_html($result['code']); ?>
                    <?php if ($result['code'] === 200): ?>
                        ✓ OK
                    <?php else: ?>
                        (Expected 200)
                    <?php endif; ?>
                    <br>
                    <a href="<?php echo esc_url($result['url']); ?>" target="_blank" style="font-size: 12px;">
                        <?php echo esc_html($result['url']); ?>
                    </a>
                <?php else: ?>
                    Error: <?php echo esc_html($result['message']); ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="card">
        <h2>✅ Next Steps</h2>
        <ol>
            <li>Refresh your homepage: <a href="<?php echo esc_url(home_url()); ?>" target="_blank"><?php echo esc_url(home_url()); ?></a></li>
            <li>The graph should now load correctly with data from the REST API</li>
            <li>If you still see issues, clear your browser cache (Ctrl+Shift+Delete)</li>
            <li>Check browser console for any remaining errors</li>
        </ol>
        
        <a href="<?php echo esc_url(home_url()); ?>" class="button">🏠 Go to Homepage</a>
        <a href="<?php echo esc_url(admin_url('options-permalink.php')); ?>" class="button">⚙️ Permalink Settings</a>
    </div>
    
    <div class="card">
        <h2>ℹ️ System Information</h2>
        <p><strong>WordPress URL:</strong> <?php echo esc_html(home_url()); ?></p>
        <p><strong>Site URL:</strong> <?php echo esc_html(site_url()); ?></p>
        <p><strong>REST API Base:</strong> <?php echo esc_html(rest_get_url_prefix()); ?></p>
        <p><strong>Permalink Structure:</strong> <?php echo esc_html(get_option('permalink_structure')); ?></p>
        <p><strong>mod_rewrite Available:</strong> <?php echo check_mod_rewrite() ? '✓ Yes' : '❌ No (WARNING)'; ?></p>
    </div>
    
    <div class="card">
        <div class="message info">
            <strong>⚠️ Security Notice</strong><br>
            Please delete this file after use: <code><?php echo esc_html(__FILE__); ?></code>
        </div>
    </div>
</body>
</html>
