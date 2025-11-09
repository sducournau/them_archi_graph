# Code Style and Conventions

## Naming Conventions

### ❌ FORBIDDEN PREFIXES (Never Use)
- `unified_*` - Deprecated, causes confusion
- `enhanced_*` - Implies multiple versions
- `new_*` - Temporary only, refactor ASAP
- `improved_*` - All code should be improved by default
- `v2_*` - Use git for versions, not code

### ✅ CORRECT PATTERNS

**PHP Functions:**
```php
archi_action_subject()  // e.g., archi_render_card(), archi_get_metadata()
archi_snake_case()      // WordPress standard
```

**CSS Classes:**
```css
.archi-component-modifier  /* e.g., .archi-card-large, .archi-button-primary */
```

**JavaScript:**
```javascript
camelCase()           // Functions and variables
ComponentName         // React components (PascalCase)
```

**Metadata Keys:**
```php
_archi_meta_key       // All metadata starts with _archi_
```

**Post Types & Taxonomies:**
```php
archi_posttype        // e.g., archi_project, archi_illustration
```

## WordPress Best Practices

### Security First
```php
// 1. Verify nonces
wp_verify_nonce($_POST['nonce'], 'action_name')

// 2. Sanitize inputs
sanitize_text_field($_POST['value'])
sanitize_email($_POST['email'])
esc_url_raw($_POST['url'])
absint($_POST['number'])

// 3. Escape outputs
esc_html($text)
esc_attr($attribute)
esc_url($url)

// 4. Check capabilities
current_user_can('edit_post', $post_id)
```

### Translation
```php
__('Text', 'archi-graph')              // Return translated
_e('Text', 'archi-graph')              // Echo translated
esc_html__('Text', 'archi-graph')      // Escaped return
esc_html_e('Text', 'archi-graph')      // Escaped echo
_n('Singular', 'Plural', $count, 'archi-graph')  // Plural
```

### File Header
```php
<?php
/**
 * Description
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;
```

## JavaScript/React Standards

### ES6+ Syntax
- Use `const` and `let` (never `var`)
- Arrow functions preferred
- Destructuring for props and objects
- Template literals for strings

### React Patterns
```jsx
// Hooks over classes
const MyComponent = ({ prop1, prop2 }) => {
  const [state, setState] = useState();
  
  useEffect(() => {
    // Side effects
  }, [dependencies]);
  
  return <div>{state}</div>;
};

// WordPress data store
const data = useSelect((select) => {
  return select('core/editor').getCurrentPost();
}, []);
```

## Code Organization Rules

### Consolidation Principles
1. **One component per responsibility**
2. **Merge similar functions** - Use parameters for variations, not separate functions
3. **Reuse existing utilities** - Check `inc/` and `assets/js/utils/` before creating new
4. **Single card renderer** - Use `archi_render_article_card()` in `inc/article-card-component.php`
5. **Consolidate CSS** - Use modifiers, not duplicate classes

### Before Creating New Code
- [ ] Check if similar function exists in `inc/` directory
- [ ] Can existing component be extended with props?
- [ ] Are there duplicate CSS patterns?
- [ ] Can utility function be generalized?
- [ ] Use Serena MCP to search for existing implementations

## Metadata Schema

### Graph Metadata (All Post Types)
- `_archi_show_in_graph` - "1" or "0"
- `_archi_node_color` - Hex color
- `_archi_node_size` - 40-120 (px)
- `_archi_priority_level` - "low|normal|high|featured"
- `_archi_graph_position` - JSON {"x": 100, "y": 200}
- `_archi_related_articles` - Array of post IDs

### Project Metadata
- `_archi_project_surface` - Float (m²)
- `_archi_project_cost` - Integer (€)
- `_archi_project_client` - String
- `_archi_project_location` - String
- `_archi_project_start_date` - Date
- `_archi_project_end_date` - Date
- `_archi_project_bet` - String
- `_archi_project_certifications` - String

### Illustration Metadata
- `_archi_illustration_technique` - String
- `_archi_illustration_dimensions` - String
- `_archi_illustration_software` - String
- `_archi_illustration_project_link` - Post ID

## Comments & Documentation
```php
/**
 * Function description
 *
 * @param string $param Description
 * @return array Description
 * @since 1.0.0
 */
function archi_function_name($param) {
    // Implementation
}
```

## Performance Best Practices
- Use transients for expensive queries: `set_transient('key', $data, HOUR_IN_SECONDS)`
- Delete transients on data change: `delete_transient('key')`
- Minimize database queries in loops
- Use `wp_cache_*` for object caching
- Lazy load images
