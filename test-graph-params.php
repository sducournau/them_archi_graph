<?php
// Load WordPress from correct path
$wp_load_path = dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';
if (!file_exists($wp_load_path)) {
    die("❌ Cannot find wp-load.php at: $wp_load_path\n");
}
require_once($wp_load_path);

// Get the first published post
$args = array(
    'post_type' => array('post', 'archi_project', 'archi_illustration'),
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'meta_query' => array(
        array(
            'key' => '_archi_show_in_graph',
            'value' => '1',
            'compare' => '='
        )
    )
);

$posts = get_posts($args);

if (empty($posts)) {
    echo "❌ No posts found with _archi_show_in_graph = 1\n";
    exit;
}

$post = $posts[0];
echo "Testing post: {$post->post_title} (ID: {$post->ID})\n";
echo str_repeat('-', 60) . "\n\n";

// Test direct meta retrieval
echo "1. Direct get_post_meta() calls:\n";
$meta_keys = array('pulse_effect', 'glow_effect', 'animation_level', 'animation_duration', 'hover_scale', 'node_size', 'node_color');
foreach ($meta_keys as $key) {
    $value = get_post_meta($post->ID, '_archi_' . $key, true);
    $display_value = $value === '' ? '(empty)' : (is_bool($value) ? ($value ? 'true' : 'false') : $value);
    echo "  _archi_{$key}: {$display_value}\n";
}

echo "\n2. Using archi_get_graph_params():\n";
if (function_exists('archi_get_graph_params')) {
    $unified_params = archi_get_graph_params($post->ID, true);
    echo "  pulse_effect: " . json_encode($unified_params['pulse_effect'] ?? null) . "\n";
    echo "  glow_effect: " . json_encode($unified_params['glow_effect'] ?? null) . "\n";
    echo "  animation_level: " . json_encode($unified_params['animation_level'] ?? null) . "\n";
    echo "  animation_duration: " . json_encode($unified_params['animation_duration'] ?? null) . "\n";
    echo "  hover_scale: " . json_encode($unified_params['hover_scale'] ?? null) . "\n";
} else {
    echo "  ❌ Function archi_get_graph_params() not found!\n";
}

echo "\n✅ Test complete\n";
