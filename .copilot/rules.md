# Copilot Rules for Archi-Graph Theme

## Code Generation Rules

### 1. WordPress Function Prefixing

When creating new functions, ALWAYS use the `archi_` prefix:

```php
✅ function archi_custom_function() {}
❌ function custom_function() {}
```

### 2. Metadata Key Naming

All custom post meta keys MUST start with `_archi_`:

```php
✅ update_post_meta($post_id, '_archi_custom_field', $value);
❌ update_post_meta($post_id, 'custom_field', $value);
```

### 3. Translation Domain

Always use `archi-graph` as the text domain:

```php
✅ __('Text', 'archi-graph')
❌ __('Text', 'my-theme')
❌ __('Text') // Missing domain
```

### 4. Security First

ALWAYS include security checks:

```php
// Required security checks for form processing
if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'action_name')) {
    wp_die('Security check failed');
}

if (!current_user_can('edit_post', $post_id)) {
    return;
}

if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
}
```

### 5. Sanitization and Escaping

Input sanitization and output escaping are MANDATORY:

```php
// Sanitize inputs
$text = sanitize_text_field($_POST['text']);
$textarea = sanitize_textarea_field($_POST['textarea']);
$email = sanitize_email($_POST['email']);
$url = esc_url_raw($_POST['url']);
$int = absint($_POST['number']);
$float = floatval($_POST['decimal']);
$html = wp_kses_post($_POST['content']);

// Escape outputs
echo esc_html($text);
echo esc_attr($attribute);
echo esc_url($url);
echo esc_js($js_string);
echo wp_kses_post($html_content);
```

### 6. Graph Metadata Schema

When working with graph visualization, use the standard metadata schema:

```php
// Required graph metadata
'_archi_show_in_graph'     // '1' or '0'
'_archi_node_color'        // Hex color: '#3498db'
'_archi_node_size'         // Integer 40-120
'_archi_priority_level'    // 'low'|'normal'|'high'|'featured'
'_archi_graph_position'    // Array: ['x' => float, 'y' => float]
'_archi_related_articles'  // Array of post IDs
```

### 7. Custom Post Type Queries

When querying posts for the graph, always use this pattern:

```php
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
```

### 8. WPForms Processing Pattern

When processing WPForms submissions:

```php
function archi_process_form_entries($fields, $entry, $form_data, $entry_id) {
    // 1. Identify form type
    $form_id = $form_data['id'];
    $expected_form_id = get_option('archi_project_form_id');

    if ($form_id != $expected_form_id) {
        return;
    }

    // 2. Create post with pending status
    $post_data = [
        'post_title' => sanitize_text_field($fields['1']['value'] ?? ''),
        'post_content' => wp_kses_post($fields['3']['value'] ?? ''),
        'post_status' => 'pending', // Always pending, admin reviews
        'post_type' => 'archi_project',
        'meta_input' => [
            '_archi_wpforms_entry_id' => $entry_id,
            // ... more metadata
        ]
    ];

    $post_id = wp_insert_post($post_data);

    if (!$post_id || is_wp_error($post_id)) {
        return;
    }

    // 3. Assign taxonomies
    wp_set_post_terms($post_id, [$term_id], 'taxonomy_name');

    // 4. Process file uploads
    archi_process_uploaded_files($fields, $post_id, ['41', '42']);
}
add_action('wpforms_process_complete', 'archi_process_form_entries', 10, 4);
```

### 9. React Block Registration

When creating Gutenberg blocks with React:

```jsx
import { registerBlockType } from "@wordpress/blocks";
import { InspectorControls } from "@wordpress/block-editor";
import { PanelBody, ToggleControl } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { Fragment } from "@wordpress/element";

registerBlockType("archi-graph/block-name", {
  title: __("Block Title", "archi-graph"),
  description: __("Block description", "archi-graph"),
  icon: "icon-name",
  category: "archi-graph", // Our custom category
  keywords: [__("keyword1", "archi-graph"), __("keyword2", "archi-graph")],

  attributes: {
    attributeName: {
      type: "boolean",
      default: true,
    },
  },

  edit: ({ attributes, setAttributes }) => {
    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={__("Settings", "archi-graph")}>
            <ToggleControl
              label={__("Option", "archi-graph")}
              checked={attributes.attributeName}
              onChange={(value) => setAttributes({ attributeName: value })}
            />
          </PanelBody>
        </InspectorControls>
        <div className="archi-block-editor">{/* Editor preview */}</div>
      </Fragment>
    );
  },

  save: () => null, // Server-side rendering
});
```

### 10. PHP Block Registration (Server-Side)

```php
function archi_register_server_block() {
    register_block_type('archi-graph/block-name', [
        'render_callback' => 'archi_render_block',
        'attributes' => [
            'attributeName' => [
                'type' => 'boolean',
                'default' => true
            ]
        ]
    ]);
}
add_action('init', 'archi_register_server_block');

function archi_render_block($attributes, $content) {
    $attribute_value = $attributes['attributeName'] ?? true;

    ob_start();
    ?>
    <div class="archi-block-wrapper">
        <?php if ($attribute_value): ?>
            <!-- Output HTML -->
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
```

## File Organization Rules

### When creating new files:

1. **PHP functionality**: Place in `inc/` directory

   - Custom post types: `inc/custom-post-types.php`
   - Meta boxes: `inc/meta-boxes.php`
   - REST API: `inc/rest-api.php`
   - WPForms: `inc/wpforms-integration.php`

2. **React blocks**: Place in `assets/js/blocks/`
3. **React components**: Place in `assets/js/components/`
4. **Utility functions**: Place in `assets/js/utils/`
5. **CSS files**: Place in `assets/css/`
6. **Template parts**: Place in `template-parts/`

### File header template:

```php
<?php
/**
 * Brief description of file purpose
 *
 * @package Archi_Graph
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// File contents...
```

## CSS Class Naming Rules

Use BEM-inspired naming with `archi-` prefix:

```css
✅ .archi-block-name {
}
✅ .archi-block-name__element {
}
✅ .archi-block-name--modifier {
}

❌ .block-name {
}
❌ .blockName {
}
```

## JavaScript Function Naming

- Use camelCase for functions
- Prefix global functions with `archi`:

```javascript
✅ function archiInitGraph() {}
✅ function archiHandleNodeClick() {}

❌ function init_graph() {}
❌ function handleNodeClick() {} // Only if scoped locally
```

## REST API Endpoint Naming

All custom REST API endpoints use `/wp-json/archi/v1/` namespace:

```php
register_rest_route('archi/v1', '/articles', [
    'methods' => 'GET',
    'callback' => 'archi_get_articles_callback',
    'permission_callback' => '__return_true'
]);
```

## Database Query Rules

1. **Prefer WordPress functions over direct queries**:

```php
✅ $posts = get_posts($args);
✅ $meta = get_post_meta($post_id, 'key', true);
❌ $wpdb->get_results("SELECT * FROM wp_posts...");
```

2. **When direct queries are necessary, use $wpdb properly**:

```php
global $wpdb;
$results = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM {$wpdb->posts} WHERE post_type = %s",
    'archi_project'
));
```

## Performance Rules

1. **Use transients for expensive queries**:

```php
$cache_key = 'archi_graph_articles';
$articles = get_transient($cache_key);

if (false === $articles) {
    $articles = // ... expensive query
    set_transient($cache_key, $articles, HOUR_IN_SECONDS);
}
```

2. **Delete transients when data changes**:

```php
function archi_clear_cache($post_id) {
    delete_transient('archi_graph_articles');
    delete_transient('archi_graph_categories');
}
add_action('save_post', 'archi_clear_cache');
```

## Error Handling Rules

Always handle errors gracefully:

```php
$post_id = wp_insert_post($post_data);

if (is_wp_error($post_id)) {
    error_log('Archi Graph: Failed to create post - ' . $post_id->get_error_message());
    return false;
}

if (!$post_id) {
    error_log('Archi Graph: Post creation returned null');
    return false;
}

// Success - continue processing
```

## Documentation Rules

Document complex functions with PHPDoc:

```php
/**
 * Process project submission from WPForms
 *
 * @param array $fields Form field data
 * @param array $entry  Entry data
 * @param int   $entry_id Entry ID
 * @return int|false Post ID on success, false on failure
 * @since 1.0.0
 */
function archi_process_project_submission($fields, $entry, $entry_id) {
    // Function code
}
```

## Deprecated Features - DO NOT USE

The following features have been removed from the theme:

- ❌ `archi_article` post type (use `post` or `archi_illustration` instead)
- ❌ `archi_create_article_form()` function
- ❌ `archi_process_article_submission()` function

## Quick Reference

### Post Types

- `post` - Regular WordPress posts
- `archi_project` - Architectural projects
- `archi_illustration` - Illustrations and graphics

### Taxonomies

- `archi_project_type` - Project categories (hierarchical)
- `archi_project_status` - Project status (flat)
- `illustration_type` - Illustration categories (hierarchical)
- `category` - Standard categories (all post types)
- `post_tag` - Standard tags (all post types)

### Critical Dependencies

- WordPress 6.0+
- WPForms plugin (required)
- React (via @wordpress/element)
- D3.js (for graph visualization)

### Key Files to Know

- `functions.php` - Theme bootstrap
- `inc/custom-post-types.php` - CPT definitions
- `inc/wpforms-integration.php` - Form handling
- `inc/meta-boxes.php` - Graph metadata UI
- `inc/rest-api.php` - API endpoints
- `assets/js/blocks/article-manager.jsx` - Main React block
- `assets/js/components/GraphContainer.jsx` - Graph visualization

## When in Doubt

1. Check existing code patterns in the theme
2. Follow WordPress Coding Standards
3. Prioritize security (sanitize, escape, verify)
4. Use the `archi_` prefix for everything custom
5. Test with the diagnostic scripts in the root directory
