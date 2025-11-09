# Architecture Patterns and Design Guidelines

## Custom Post Types Architecture

### `archi_project` (Architectural Projects)
**Registration**: `inc/custom-post-types.php`
**Supports**: title, editor, thumbnail, excerpt, custom-fields, revisions, author, comments
**Taxonomies**: 
- `archi_project_type` (hierarchical) - Résidentiel, Commercial, Industriel, etc.
- `archi_project_status` (flat) - Étude, Conception, En cours, Terminé, Suspendu
- `category` (core) - Standard WordPress categories
- `post_tag` (core) - Standard WordPress tags

**Key Metadata Fields**:
```php
_archi_project_surface      // Float - Surface area in m²
_archi_project_cost         // Integer - Project cost in €
_archi_project_client       // String - Client name
_archi_project_location     // String - Project location
_archi_project_start_date   // String/Date - Project start
_archi_project_end_date     // String/Date - Project completion
_archi_project_bet          // String - Engineering firm (Bureau d'études techniques)
_archi_project_certifications  // String - Project certifications
```

### `archi_illustration` (Architectural Illustrations)
**Registration**: `inc/custom-post-types.php`
**Supports**: title, editor, thumbnail, excerpt, custom-fields, author
**Taxonomies**:
- `illustration_type` (hierarchical) - Plan, Élévation, 3D, Perspective, etc.
- `category` (core)
- `post_tag` (core)

**Key Metadata Fields**:
```php
_archi_illustration_technique    // String - Drawing/rendering technique
_archi_illustration_dimensions   // String - Physical dimensions
_archi_illustration_software     // String - Software used
_archi_illustration_project_link // Post ID - Linked project
```

### Universal Graph Metadata (All Post Types)
Applied to: `post`, `archi_project`, `archi_illustration`

```php
_archi_show_in_graph        // "1" or "0" - Visibility in graph
_archi_node_color           // String - Hex color (e.g., "#3498db")
_archi_node_size            // Integer - 40-120 px
_archi_priority_level       // String - "low|normal|high|featured"
_archi_graph_position       // Array/JSON - {"x": 100, "y": 200}
_archi_related_articles     // Array - [12, 45, 67] Post IDs for manual links
```

## WPForms Integration Pattern

### Form Creation (On Theme Activation)
**File**: `inc/wpforms-integration.php`
**Function**: `archi_create_forms_on_activation()`

Creates 2 forms:
1. **Project Submission Form** (42 fields)
   - Sections: Basic Info, Technical Details, Location, Certifications, Media
   - Fields: Title, Description, Surface, Cost, Client, etc.
   - File uploads for images, plans, documents

2. **Illustration Submission Form** (20 fields)
   - Sections: Basic Info, Technical Details, Media
   - Fields: Title, Technique, Software, Dimensions, etc.
   - File uploads for illustration files

### Form Processing Flow
```
User submits form
    ↓
wpforms_process_complete hook fires
    ↓
archi_process_form_entries($fields, $entry, $form_data, $entry_id)
    ↓
Identify form type (project vs illustration)
    ↓
Extract field values and sanitize
    ↓
wp_insert_post() with 'pending' status
    ↓
update_post_meta() for all metadata fields
    ↓
wp_set_post_terms() for taxonomies
    ↓
archi_process_uploaded_files() for media
    ↓
Store WPForms entry ID: _archi_wpforms_entry_id
    ↓
Admin reviews and publishes
```

**Key Pattern**: Forms auto-create posts in `pending` status, preserving all metadata for admin review.

## Gutenberg Blocks Architecture

### Block Registration Pattern
**File**: `inc/gutenberg-blocks.php`

```php
function archi_register_custom_block() {
    register_block_type('archi-graph/block-name', [
        'render_callback' => 'archi_render_block_name',
        'attributes' => [
            'attributeName' => [
                'type' => 'string|boolean|number',
                'default' => 'value'
            ]
        ]
    ]);
}
add_action('init', 'archi_register_custom_block');
```

### Server-Side Rendering Pattern
```php
function archi_render_block_name($attributes) {
    // Extract attributes with defaults
    $show_feature = $attributes['showFeature'] ?? true;
    
    // Start output buffering
    ob_start();
    
    // Generate HTML
    ?>
    <div class="archi-block-wrapper">
        <?php if ($show_feature): ?>
            <!-- Content -->
        <?php endif; ?>
    </div>
    <?php
    
    // Return buffered content
    return ob_get_clean();
}
```

### React Block Pattern
**File**: `assets/js/blocks/article-manager.jsx`

```jsx
import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';

registerBlockType('archi-graph/block-name', {
    title: __('Block Title', 'archi-graph'),
    icon: 'icon-name',
    category: 'archi-graph',
    
    attributes: {
        showFeature: { type: 'boolean', default: true }
    },
    
    edit: ({ attributes, setAttributes }) => {
        return (
            <>
                <InspectorControls>
                    <PanelBody title={__('Settings', 'archi-graph')}>
                        <ToggleControl
                            label={__('Show Feature', 'archi-graph')}
                            checked={attributes.showFeature}
                            onChange={(value) => setAttributes({ showFeature: value })}
                        />
                    </PanelBody>
                </InspectorControls>
                <div className="archi-block-editor">
                    {/* Editor preview */}
                </div>
            </>
        );
    },
    
    save: () => null  // Server-side rendering
});
```

## REST API Architecture

### Endpoint Pattern
**File**: `inc/rest-api.php`

```php
function archi_register_api_routes() {
    register_rest_route('archi/v1', '/endpoint', [
        'methods' => 'GET',
        'callback' => 'archi_handle_endpoint',
        'permission_callback' => '__return_true'  // Or custom check
    ]);
}
add_action('rest_api_init', 'archi_register_api_routes');
```

### Key Endpoints
1. **`/wp-json/archi/v1/articles`**
   - Returns all posts with `_archi_show_in_graph = '1'`
   - Includes: metadata, categories, tags, relationships
   - Used by: Graph visualization

2. **`/wp-json/archi/v1/categories`**
   - Returns all categories with colors
   - Includes: category metadata, post counts
   - Used by: Category clusters

3. **`/wp-json/archi/v1/proximity-analysis`**
   - Analyzes relationships between posts
   - Returns: Relationship scores, connections
   - Used by: Relationship calculator

4. **`/wp-json/archi/v1/related-articles/{id}`**
   - Returns related posts for given ID
   - Includes: Automatic + manual relationships
   - Used by: Single post related content

5. **`/wp-json/archi/v1/save-positions`** (POST)
   - Saves node positions from graph
   - Stores in: `_archi_graph_position`
   - Used by: Graph interaction persistence

## Graph Visualization Architecture

### Data Flow
```
GraphContainer.jsx mounts
    ↓
dataFetcher.js calls /wp-json/archi/v1/articles
    ↓
rest-api.php queries posts with WP_Query
    ↓
Filters posts where _archi_show_in_graph = '1'
    ↓
Builds JSON response with nodes and relationships
    ↓
D3.js processes data and creates force simulation
    ↓
Node.jsx components render individual nodes
    ↓
User interactions update positions via AJAX
    ↓
Positions saved back to database
```

### Relationship Scoring Algorithm
**File**: `inc/enhanced-proximity-calculator.php`

```php
Shared Categories: 40 points (each)
Shared Tags: 25 points (each)
Same Primary Category: 20 points (bonus)
Temporal Proximity: 0-10 points (published within days)
Content Similarity: 0-5 points (title/content matching)
Manual Links: 100 points (from _archi_related_articles)
```

**Threshold**: Minimum 45 points to create relationship link

### D3.js Force Simulation
**File**: `assets/js/components/GraphContainer.jsx`

Forces applied:
- **Charge** - Repulsion between nodes (-300 to -800 based on size)
- **Link** - Spring force on connections (distance: 100-200)
- **Center** - Gravity toward canvas center
- **Collision** - Prevent node overlap (radius based on node size)
- **Category Cluster** - Group nodes by category color

## Component Reuse Pattern

### Single Card Renderer
**File**: `inc/article-card-component.php`
**Function**: `archi_render_article_card($post_id, $options = [])`

All card rendering should use this function instead of duplicating markup.

**Usage**:
```php
$options = [
    'show_excerpt' => true,
    'show_meta' => true,
    'show_thumbnail' => true,
    'card_class' => 'additional-class'
];

echo archi_render_article_card($post_id, $options);
```

### React Component Reuse
**Location**: `assets/js/components/`

Reusable components:
- `Node.jsx` - Graph node (don't create variants)
- `NodeTooltip.jsx` - Hover info (reuse with props)
- `CategoryCluster.jsx` - Category grouping (extensible)

**Pattern**: Extend with props, NOT by creating new similar components

## Hook System

### Custom Actions
```php
do_action('archi_before_graph_render');
do_action('archi_after_graph_render');
do_action('archi_before_post_save', $post_id);
do_action('archi_after_post_save', $post_id);
```

### Custom Filters
```php
apply_filters('archi_proximity_score', $score, $post_a, $post_b);
apply_filters('archi_graph_node_data', $node, $post_id);
apply_filters('archi_card_output', $html, $post_id, $options);
```

## Security Architecture

### Input Validation Pattern
```php
function archi_save_function() {
    // 1. Verify nonce
    check_admin_referer('archi_action_nonce');
    
    // 2. Check capabilities
    if (!current_user_can('edit_post', $post_id)) {
        wp_die(__('Permission denied', 'archi-graph'));
    }
    
    // 3. Sanitize inputs
    $value = sanitize_text_field($_POST['value']);
    $number = absint($_POST['number']);
    $email = sanitize_email($_POST['email']);
    
    // 4. Validate business logic
    if ($number < 0 || $number > 1000) {
        return new WP_Error('invalid', __('Invalid range', 'archi-graph'));
    }
    
    // 5. Save to database
    update_post_meta($post_id, '_archi_field', $value);
}
```

### Output Escaping Pattern
```php
// HTML content
echo esc_html($text);

// HTML attributes
echo '<div data-value="' . esc_attr($attr) . '">';

// URLs
echo '<a href="' . esc_url($url) . '">';

// JavaScript
echo '<script>var data = ' . wp_json_encode($data) . ';</script>';
```

## Performance Patterns

### Transient Caching
```php
$cache_key = 'archi_graph_data';
$data = get_transient($cache_key);

if (false === $data) {
    // Expensive query
    $data = expensive_query();
    set_transient($cache_key, $data, HOUR_IN_SECONDS);
}

return $data;
```

### Delete on Update
```php
function archi_clear_caches($post_id) {
    delete_transient('archi_graph_data');
    delete_transient('archi_categories_' . $post_id);
}
add_action('save_post', 'archi_clear_caches');
```
