<?php
/**
 * Graph Options Migration Script
 * 
 * Migrates old inconsistent option keys to the new standardized archi_graph_* pattern.
 * This script consolidates duplicate options and cleans up the database.
 * 
 * Usage:
 * - Via WP-CLI: wp eval-file utilities/maintenance/migrate-graph-options.php
 * - Via Admin: Access via Tools menu (if admin interface is added)
 * - Direct: Include this file and call archi_migrate_graph_options()
 * 
 * @package ArchiGraph
 * @since 1.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Migrate old graph option keys to standardized keys
 * 
 * This function:
 * 1. Maps old option keys to new standardized keys
 * 2. Migrates values only if new key doesn't exist
 * 3. Deletes old keys after successful migration
 * 4. Logs all operations for review
 * 
 * @return array Migration results with statistics
 */
function archi_migrate_graph_options() {
    global $wpdb;
    
    $results = [
        'migrated' => [],
        'skipped' => [],
        'deleted' => [],
        'errors' => [],
    ];
    
    // Define migration mapping: old_key => new_key
    $migrations = [
        // Old archi_ prefix (inconsistent)
        'archi_animation_duration'     => 'archi_graph_animation_duration',
        'archi_node_spacing'           => 'archi_graph_min_distance',
        'archi_cluster_strength'       => 'archi_graph_cluster_strength',
        
        // Old graph_ prefix (missing archi_)
        'graph_animation_duration'     => 'archi_graph_animation_duration',
        'graph_node_spacing'           => 'archi_graph_min_distance',
        'graph_cluster_strength'       => 'archi_graph_cluster_strength',
        'graph_show_categories'        => 'archi_graph_show_categories',
        'graph_show_links'             => 'archi_graph_show_links',
        'graph_auto_save_positions'    => 'archi_graph_auto_save_positions',
        'graph_max_articles'           => 'archi_graph_max_articles',
        
        // Inconsistent naming
        'default_node_color'           => 'archi_graph_default_color',
        'background_gradient_start'    => 'archi_graph_bg_gradient_start',
        'background_gradient_end'      => 'archi_graph_bg_gradient_end',
        'cache_duration'               => 'archi_graph_cache_duration',
    ];
    
    foreach ($migrations as $old_key => $new_key) {
        try {
            // Check if old option exists
            $old_value = get_option($old_key);
            
            if ($old_value === false) {
                $results['skipped'][] = [
                    'key' => $old_key,
                    'reason' => 'Old key does not exist'
                ];
                continue;
            }
            
            // Check if new option already exists
            $new_value = get_option($new_key);
            
            if ($new_value !== false) {
                // New key exists - compare values
                if ($new_value == $old_value) {
                    // Same value - safe to delete old key
                    delete_option($old_key);
                    $results['deleted'][] = [
                        'key' => $old_key,
                        'reason' => 'Duplicate of ' . $new_key
                    ];
                } else {
                    // Different values - keep new, delete old, log warning
                    delete_option($old_key);
                    $results['skipped'][] = [
                        'key' => $old_key,
                        'reason' => 'New key exists with different value',
                        'old_value' => $old_value,
                        'new_value' => $new_value
                    ];
                }
            } else {
                // New key doesn't exist - migrate
                $success = update_option($new_key, $old_value);
                
                if ($success) {
                    delete_option($old_key);
                    $results['migrated'][] = [
                        'from' => $old_key,
                        'to' => $new_key,
                        'value' => $old_value
                    ];
                } else {
                    $results['errors'][] = [
                        'key' => $old_key,
                        'error' => 'Failed to update new key'
                    ];
                }
            }
            
        } catch (Exception $e) {
            $results['errors'][] = [
                'key' => $old_key,
                'error' => $e->getMessage()
            ];
        }
    }
    
    // Add timestamp to results
    $results['timestamp'] = current_time('mysql');
    $results['summary'] = [
        'migrated_count' => count($results['migrated']),
        'deleted_count' => count($results['deleted']),
        'skipped_count' => count($results['skipped']),
        'error_count' => count($results['errors']),
    ];
    
    // Store migration log
    update_option('archi_graph_migration_log', $results);
    
    return $results;
}

/**
 * Display migration results in readable format
 * 
 * @param array $results Results from archi_migrate_graph_options()
 */
function archi_display_migration_results($results) {
    echo "<div style='font-family: monospace; padding: 20px; background: #f5f5f5;'>\n";
    echo "<h2>🔄 Graph Options Migration Results</h2>\n";
    echo "<p><strong>Timestamp:</strong> " . esc_html($results['timestamp']) . "</p>\n";
    
    // Summary
    echo "<h3>📊 Summary</h3>\n";
    echo "<ul>\n";
    echo "<li>✅ Migrated: " . $results['summary']['migrated_count'] . "</li>\n";
    echo "<li>🗑️ Deleted: " . $results['summary']['deleted_count'] . "</li>\n";
    echo "<li>⏭️ Skipped: " . $results['summary']['skipped_count'] . "</li>\n";
    echo "<li>❌ Errors: " . $results['summary']['error_count'] . "</li>\n";
    echo "</ul>\n";
    
    // Migrated options
    if (!empty($results['migrated'])) {
        echo "<h3>✅ Successfully Migrated</h3>\n";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>\n";
        echo "<tr><th>From</th><th>To</th><th>Value</th></tr>\n";
        foreach ($results['migrated'] as $item) {
            echo "<tr>";
            echo "<td>" . esc_html($item['from']) . "</td>";
            echo "<td>" . esc_html($item['to']) . "</td>";
            echo "<td>" . esc_html(print_r($item['value'], true)) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
    
    // Deleted options
    if (!empty($results['deleted'])) {
        echo "<h3>🗑️ Deleted (Duplicates)</h3>\n";
        echo "<ul>\n";
        foreach ($results['deleted'] as $item) {
            echo "<li>" . esc_html($item['key']) . " - " . esc_html($item['reason']) . "</li>\n";
        }
        echo "</ul>\n";
    }
    
    // Skipped options
    if (!empty($results['skipped'])) {
        echo "<h3>⏭️ Skipped</h3>\n";
        echo "<ul>\n";
        foreach ($results['skipped'] as $item) {
            echo "<li>" . esc_html($item['key']) . " - " . esc_html($item['reason']);
            if (isset($item['old_value']) && isset($item['new_value'])) {
                echo "<br>&nbsp;&nbsp;Old: " . esc_html(print_r($item['old_value'], true));
                echo "<br>&nbsp;&nbsp;New: " . esc_html(print_r($item['new_value'], true));
            }
            echo "</li>\n";
        }
        echo "</ul>\n";
    }
    
    // Errors
    if (!empty($results['errors'])) {
        echo "<h3>❌ Errors</h3>\n";
        echo "<ul style='color: red;'>\n";
        foreach ($results['errors'] as $item) {
            echo "<li>" . esc_html($item['key']) . " - " . esc_html($item['error']) . "</li>\n";
        }
        echo "</ul>\n";
    }
    
    echo "</div>\n";
}

/**
 * Verify migration completed successfully
 * 
 * @return array Verification results
 */
function archi_verify_migration() {
    $old_keys = [
        'archi_animation_duration',
        'archi_node_spacing',
        'archi_cluster_strength',
        'graph_animation_duration',
        'graph_node_spacing',
        'graph_cluster_strength',
        'default_node_color',
    ];
    
    $remaining = [];
    foreach ($old_keys as $key) {
        if (get_option($key) !== false) {
            $remaining[] = $key;
        }
    }
    
    return [
        'clean' => empty($remaining),
        'remaining_keys' => $remaining,
        'message' => empty($remaining) 
            ? '✅ Migration complete - no old keys remain' 
            : '⚠️ Some old keys still exist: ' . implode(', ', $remaining)
    ];
}

// ============================================================================
// CLI SUPPORT
// ============================================================================

if (defined('WP_CLI') && WP_CLI) {
    /**
     * Migrate graph options to standardized keys
     *
     * ## EXAMPLES
     *
     *     wp eval-file utilities/maintenance/migrate-graph-options.php
     *
     * @when after_wp_load
     */
    WP_CLI::add_command('archi migrate-options', function() {
        WP_CLI::log('🔄 Starting graph options migration...');
        
        $results = archi_migrate_graph_options();
        
        WP_CLI::log('');
        WP_CLI::log('📊 SUMMARY:');
        WP_CLI::success('Migrated: ' . $results['summary']['migrated_count']);
        WP_CLI::log('Deleted: ' . $results['summary']['deleted_count']);
        WP_CLI::log('Skipped: ' . $results['summary']['skipped_count']);
        
        if ($results['summary']['error_count'] > 0) {
            WP_CLI::error('Errors: ' . $results['summary']['error_count']);
        }
        
        WP_CLI::log('');
        WP_CLI::log('🔍 Verifying migration...');
        $verification = archi_verify_migration();
        
        if ($verification['clean']) {
            WP_CLI::success($verification['message']);
        } else {
            WP_CLI::warning($verification['message']);
        }
    });
}

// ============================================================================
// DIRECT EXECUTION (if accessed directly)
// ============================================================================

if (basename($_SERVER['SCRIPT_FILENAME']) === 'migrate-graph-options.php') {
    if (current_user_can('manage_options')) {
        $results = archi_migrate_graph_options();
        archi_display_migration_results($results);
        
        echo "<hr>\n";
        $verification = archi_verify_migration();
        echo "<h3>" . esc_html($verification['message']) . "</h3>\n";
        
        if (!empty($verification['remaining_keys'])) {
            echo "<p>Remaining keys: " . esc_html(implode(', ', $verification['remaining_keys'])) . "</p>\n";
        }
    } else {
        wp_die('You do not have permission to run this migration.');
    }
}
