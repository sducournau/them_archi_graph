# GitHub Copilot Instructions for Archi-Graph Theme

## ✨ CODEBASE STATUS: CLEANED & HARMONIZED (Nov 2025)

**Recent Improvements:**
- ✅ **NO** `enhanced_*` or `unified_*` prefixes found
- ✅ CSS files consolidated: `blocks-editor.css`, `parallax-image.css`, `image-comparison-slider.css`
- ✅ Debug statements cleaned (only essential error logging remains)
- ✅ TODO comments converted to implementations or user-facing messages
- ✅ All functions use clean `archi_*` prefix pattern
- ✅ No redundant or duplicate files

## 🚨 MANDATORY RULE: ALWAYS USE SERENA MCP

**THIS IS A NON-NEGOTIABLE REQUIREMENT FOR ALL OPERATIONS ON THIS PROJECT.**

You MUST use Serena MCP tools (`mcp_oraios_serena_*`) for:
- ✅ ALL code exploration and analysis
- ✅ ALL symbol searches and navigation
- ✅ ALL file operations and pattern searches
- ✅ ALL code modifications and refactoring
- ✅ Understanding project architecture before ANY changes
- ✅ Checking existing implementations before creating new code

**DO NOT proceed with any task without first consulting Serena MCP.**

## Project Overview

This is a WordPress theme for architectural portfolio and project management with an interactive graph visualization system. The theme handles custom post types (projects and illustrations), WPForms integration for content submission, and a D3.js-powered relationship graph.

## ⚠️ CRITICAL: Use Serena MCP

**MANDATORY: This project REQUIRES Serena MCP for all code operations.**

**ALWAYS use Serena MCP tools for code analysis, navigation, and understanding:**
- Use `mcp_oraios_serena_*` tools for code exploration and symbol analysis
- Use `mcp_gitkraken_git_*` tools for all git operations
- Use Serena's project understanding capabilities before making changes
- Leverage Serena's context-aware suggestions aligned with `.serena/config.yaml`
- Consult Serena memories for project context and patterns

**Before any code change:**
1. Query Serena for existing similar functionality
2. Use `mcp_oraios_serena_find_symbol` to locate functions
3. Use `mcp_oraios_serena_search_for_pattern` to find patterns
4. Review Serena memories for architecture guidelines
5. Check `.serena/config.yaml` for conventions

**Serena is activated for this project at:**
`c:\wamp64\www\wordpress\wp-content\themes\archi-graph-template`

## Naming Conventions - CLEAN AND CONSISTENT

✅ **ALWAYS use clean, descriptive names:**
- `archi_render_article_card()` - Simple, clear function names
- `archi_get_metadata()` - Direct, purposeful naming
- `archi_project_details()` - No unnecessary prefixes

❌ **NEVER use these anti-patterns:**
- Avoid temporary prefixes: `new_*`, `temp_*`, `tmp_*`
- No redundant suffixes: `_v2`, `_updated`, `_final`
- Don't duplicate: Check existing functions first with Serena MCP

## Code Quality Standards

### Debug and Development Code
- ❌ **NO excessive `error_log()` in production code**
- ✅ Use `error_log()` only for actual errors/warnings
- ✅ Wrap verbose debug in `if (WP_DEBUG && WP_DEBUG_LOG)` when needed
- ❌ Remove all TEMPORARY/TODO code before committing

### File Organization
- **Modular blocks system**: `inc/blocks/_loader.php` loads all Gutenberg blocks
- **No duplicate files**: `blocks-render.php` was removed (replaced by modular system)
- **Clean includes**: Remove commented-out `require` statements
- **Asset management**: Use only necessary CSS/JS files

## Code Harmonization Rules

When working with existing code:
1. **Merge, don't duplicate** - If similar functionality exists, refactor and merge
2. **Remove redundant utilities** - Check `inc/` for existing functions before creating new ones
3. **Consolidate CSS classes** - Use existing `.archi-*` classes, avoid creating variants
4. **Leverage existing components** - Reuse React components in `assets/js/components/`
5. **Clean as you go** - Remove old implementations when adding new ones

## Core Principles

### 1. WordPress Best Practices

- Always use WordPress core functions instead of direct database queries
- Follow WordPress Coding Standards (WPCS)
- Use proper sanitization and escaping functions
- Check user capabilities before sensitive operations
- Use nonces for form security
- Prefix all custom functions with `archi_`
- Use text domain `archi-graph` for all translatable strings

### 2. Security First

```php
// Always verify nonces
if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'action_name')) {
    return;
}

// Sanitize inputs
$value = sanitize_text_field($_POST['value']);
$email = sanitize_email($_POST['email']);
$url = esc_url_raw($_POST['url']);

// Escape outputs
echo esc_html($value);
echo esc_attr($attribute);
echo esc_url($url);

// Check capabilities
if (!current_user_can('edit_post', $post_id)) {
    return;
}
```

### 3. Metadata Schema Consistency

All custom metadata uses these naming conventions:

- Graph metadata: `_archi_show_in_graph`, `_archi_node_color`, `_archi_node_size`, etc.
- Project metadata: `_archi_project_*` (surface, cost, client, location, etc.)
- Illustration metadata: `_archi_illustration_*` (technique, dimensions, software, etc.)

## Custom Post Types

### Architectural Projects (`archi_project`)

```php
// Register in inc/custom-post-types.php
// Supports: title, editor, thumbnail, excerpt, custom-fields, revisions, author, comments
// Taxonomies: archi_project_type, archi_project_status, category, post_tag

// Key metadata:
update_post_meta($post_id, '_archi_project_surface', floatval($surface));
update_post_meta($post_id, '_archi_project_cost', absint($cost));
update_post_meta($post_id, '_archi_project_client', sanitize_text_field($client));
update_post_meta($post_id, '_archi_project_location', sanitize_text_field($location));
```

### Illustrations (`archi_illustration`)

```php
// Register in inc/custom-post-types.php
// Supports: title, editor, thumbnail, excerpt, custom-fields, author
// Taxonomies: illustration_type, category, post_tag

// Key metadata:
update_post_meta($post_id, '_archi_illustration_technique', sanitize_text_field($technique));
update_post_meta($post_id, '_archi_illustration_software', sanitize_text_field($software));
```

## WPForms Integration

### Form Processing Pattern

```php
function archi_process_form_entries($fields, $entry, $form_data, $entry_id) {
    $form_id = $form_data['id'];
    $project_form_id = get_option('archi_project_form_id');

    if ($form_id == $project_form_id) {
        // Process project submission
        $post_data = [
            'post_title' => sanitize_text_field($fields['1']['value'] ?? ''),
            'post_content' => wp_kses_post($fields['3']['value'] ?? ''),
            'post_status' => 'pending',
            'post_type' => 'archi_project',
            'meta_input' => [
                '_archi_wpforms_entry_id' => $entry_id,
                '_archi_surface' => intval($fields['11']['value'] ?? 0),
                // ... more metadata
            ]
        ];

        $post_id = wp_insert_post($post_data);

        // Assign taxonomies
        wp_set_post_terms($post_id, [$term_id], 'archi_project_type');

        // Process file uploads
        archi_process_uploaded_files($fields, $post_id, ['41', '42']);
    }
}
add_action('wpforms_process_complete', 'archi_process_form_entries', 10, 4);
```

## Graph Visualization System

### Graph Metadata

Every post (post, archi_project, archi_illustration) can be a graph node:

```php
// Visibility
update_post_meta($post_id, '_archi_show_in_graph', '1'); // or '0'

// Visual properties
update_post_meta($post_id, '_archi_node_color', '#3498db');
update_post_meta($post_id, '_archi_node_size', 60); // 40-120px

// Priority and positioning
update_post_meta($post_id, '_archi_priority_level', 'normal'); // low|normal|high|featured
update_post_meta($post_id, '_archi_graph_position', ['x' => 100, 'y' => 200]);

// Manual relationships
update_post_meta($post_id, '_archi_related_articles', [12, 45, 67]); // Post IDs
```

### REST API for Graph Data

```php
// Endpoint: /wp-json/archi/v1/articles
// Returns all posts with _archi_show_in_graph = '1'
// Includes: metadata, categories, tags, relationships

// Query pattern in inc/rest-api.php:
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
```

## Gutenberg Blocks

### React Block Pattern (article-manager.jsx)

```jsx
import { registerBlockType } from "@wordpress/blocks";
import { InspectorControls } from "@wordpress/block-editor";
import { PanelBody, ToggleControl } from "@wordpress/components";
import { useSelect } from "@wordpress/data";

registerBlockType("archi-graph/block-name", {
  title: __("Block Title", "archi-graph"),
  icon: "icon-name",
  category: "archi-graph",

  attributes: {
    showFeature: {
      type: "boolean",
      default: true,
    },
  },

  edit: ({ attributes, setAttributes }) => {
    const postData = useSelect((select) => {
      const { getCurrentPost } = select("core/editor");
      return getCurrentPost();
    }, []);

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={__("Settings", "archi-graph")}>
            <ToggleControl
              label={__("Show Feature", "archi-graph")}
              checked={attributes.showFeature}
              onChange={(value) => setAttributes({ showFeature: value })}
            />
          </PanelBody>
        </InspectorControls>
        <div>Block Editor Preview</div>
      </Fragment>
    );
  },

  save: () => null, // Server-side rendering
});
```

### PHP Server-Side Block (gutenberg-blocks.php)

```php
function archi_register_custom_block() {
    register_block_type('archi-graph/block-name', [
        'render_callback' => 'archi_render_custom_block',
        'attributes' => [
            'showFeature' => [
                'type' => 'boolean',
                'default' => true
            ]
        ]
    ]);
}
add_action('init', 'archi_register_custom_block');

function archi_render_custom_block($attributes) {
    $show_feature = $attributes['showFeature'] ?? true;

    ob_start();
    ?>
    <div class="archi-custom-block">
        <?php if ($show_feature): ?>
            <!-- Output HTML -->
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
```

## Common Code Patterns

### Adding a New Metadata Field

1. Add meta box field in `inc/meta-boxes.php`:

```php
<tr>
    <td>
        <label for="archi_new_field"><?php _e('New Field:', 'archi-graph'); ?></label>
        <input type="text"
               id="archi_new_field"
               name="archi_new_field"
               value="<?php echo esc_attr(get_post_meta($post->ID, '_archi_new_field', true)); ?>">
    </td>
</tr>
```

2. Add save logic:

```php
if (isset($_POST['archi_new_field'])) {
    update_post_meta($post_id, '_archi_new_field', sanitize_text_field($_POST['archi_new_field']));
}
```

3. Update REST API to include it (if needed for graph):

```php
$article['custom_meta']['new_field'] = get_post_meta($post->ID, '_archi_new_field', true);
```

### Creating a WPForms Handler

```php
function archi_create_custom_form() {
    $form_data = [
        'settings' => [
            'form_title' => __('Form Title', 'archi-graph'),
            'submit_text' => __('Submit', 'archi-graph'),
            'notification_enable' => '1',
            'notifications' => [
                '1' => [
                    'email' => '{admin_email}',
                    'subject' => __('New Submission', 'archi-graph'),
                    'message' => 'Field 1: {field_id="1"}'
                ]
            ]
        ],
        'fields' => [
            '1' => [
                'id' => '1',
                'type' => 'text',
                'label' => __('Field Label', 'archi-graph'),
                'required' => '1'
            ]
        ]
    ];

    return wpforms()->form->add(__('Form Title', 'archi-graph'), $form_data);
}
```

### Querying Posts with Metadata

```php
$args = [
    'post_type' => ['post', 'archi_project', 'archi_illustration'],
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_query' => [
        'relation' => 'AND',
        [
            'key' => '_archi_show_in_graph',
            'value' => '1',
            'compare' => '='
        ],
        [
            'key' => '_archi_priority_level',
            'value' => 'high',
            'compare' => '='
        ]
    ]
];

$posts = get_posts($args);
```

## File Organization & Architecture

### Current Structure (Clean & Consolidated)
- **Custom post types**: `inc/custom-post-types.php`
- **Meta boxes**: `inc/meta-boxes.php`
- **WPForms integration**: `inc/wpforms-integration.php`
- **REST API endpoints**: `inc/rest-api.php`
- **Gutenberg blocks**: `inc/blocks/_loader.php` (modular system)
  - Graph blocks: `inc/blocks/graph/`
  - Project blocks: `inc/blocks/projects/`
  - Content blocks: `inc/blocks/content/`
- **React components**: `assets/js/components/`
- **Utilities**: `assets/js/utils/`
- **Styles**: `assets/css/`

### When Adding New Features:
1. **Use Serena MCP first** - Search for existing similar functionality
2. **Check the loader** - Block registration is automatic via `_loader.php`
3. **Follow the pattern** - Look at existing implementations
4. **One feature, one file** - Keep concerns separated
5. **Clean integration** - No temporary/force-enqueue functions needed

### Enqueuing Assets

```php
function archi_enqueue_assets() {
    wp_enqueue_style(
        'archi-custom-style',
        get_template_directory_uri() . '/assets/css/custom-style.css',
        [],
        '1.0.0'
    );

    wp_enqueue_script(
        'archi-custom-script',
        get_template_directory_uri() . '/assets/js/custom-script.js',
        ['jquery'],
        '1.0.0',
        true
    );

    // Localize for AJAX
    wp_localize_script('archi-custom-script', 'archiData', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('archi_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'archi_enqueue_assets');
```

## Testing and Debugging

### Diagnostic Scripts

The theme includes diagnostic scripts in the root directory:

- `check-graph-meta.php` - Verify graph metadata
- `deep-diagnostic.php` - Deep database inspection
- `test-api-direct.php` - Test REST API responses
- `debug-api-complet.php` - Complete API debugging

### Debug Pattern

```php
// Enable WordPress debugging
if (WP_DEBUG) {
    error_log('Debug message: ' . print_r($data, true));
}

// Use diagnostic queries
global $wpdb;
$results = $wpdb->get_results("
    SELECT p.ID, p.post_title, pm.meta_value
    FROM {$wpdb->posts} p
    LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_archi_show_in_graph'
    WHERE p.post_status = 'publish'
");
```

## Translation

Always use translation functions:

```php
__('Text', 'archi-graph')              // Return translated
_e('Text', 'archi-graph')              // Echo translated
esc_html__('Text', 'archi-graph')      // Return escaped translated
esc_html_e('Text', 'archi-graph')      // Echo escaped translated
_n('Singular', 'Plural', $count, 'archi-graph')  // Pluralization
```

## Performance Considerations

- Use transients for expensive queries: `set_transient('archi_graph_articles', $data, HOUR_IN_SECONDS)`
- Delete transients when data changes: `delete_transient('archi_graph_articles')`
- Lazy load images in graph visualization
- Use `wp_cache_*` functions for object caching
- Minimize database queries in loops

## Common Pitfalls to Avoid

1. ❌ Don't use direct database queries without sanitization
2. ❌ Don't forget to check nonces and capabilities
3. ❌ Don't use `$_POST` or `$_GET` directly - sanitize first
4. ❌ Don't output data without escaping
5. ❌ Don't create global variables - use proper WordPress hooks
6. ❌ Don't hardcode URLs - use `get_template_directory_uri()`
7. ❌ Don't forget to check if plugins (WPForms) are active
8. ❌ Don't use deprecated `archi_article` post type - it's been removed

## Key Reminders

- **Always use Serena MCP** for project analysis and code navigation
- All custom functions start with `archi_` (never `unified_archi_` or `enhanced_archi_`)
- All custom metadata starts with `_archi_`
- Text domain is always `archi-graph`
- WPForms is a critical dependency
- Graph system relies on specific metadata schema
- Support both automatic (category/tag-based) and manual relationships
- Posts are created as 'pending' from forms, then admin publishes
- The theme is bilingual-ready (French primary, English secondary)

## Serena MCP Integration Workflow

1. **Before any code change:**
   - Use Serena to understand project structure
   - Query `.serena/config.yaml` for patterns
   - Check for existing similar functionality

2. **During development:**
   - Use `mcp_gitkraken_git_status` to track changes
   - Leverage Serena's context-aware suggestions
   - Follow architecture patterns defined in Serena config

3. **After changes:**
   - Use `mcp_gitkraken_git_add_or_commit` for version control
   - Ensure changes align with `.serena/config.yaml` standards
   - Update Serena config if new patterns are introduced

## Anti-Patterns to Avoid

1. ❌ Creating `archi_unified_*` or `archi_enhanced_*` functions
2. ❌ Duplicating existing utility functions
3. ❌ Adding CSS with `unified-*` or `enhanced-*` prefixes
4. ❌ Creating new components without checking existing ones
5. ❌ Making changes without consulting Serena MCP first
6. ❌ Ignoring `.serena/config.yaml` architecture guidelines
