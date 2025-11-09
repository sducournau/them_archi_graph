# Common Issues and Troubleshooting

## Frequent Problems

### Graph Not Displaying

**Symptoms**: Blank area where graph should be

**Diagnosis**:
1. Check browser console (F12) for JavaScript errors
2. Verify REST API: `http://localhost/wordpress/wp-json/archi/v1/articles`
3. Check if posts have `_archi_show_in_graph = '1'`

**Solutions**:
```powershell
# 1. Check graph metadata
php check-graph-meta.php

# 2. Create test data
# Via WP Admin: Appearance → Diagnostic → Create test articles

# 3. Flush caches
php flush-rewrite-rules.php
php flush-rest-api.php

# 4. Rebuild bundles
npm run build

# 5. Hard refresh browser
# Ctrl+Shift+R
```

**Common Causes**:
- No posts have graph metadata enabled
- REST API rewrite rules not flushed
- JavaScript bundle not built
- Browser cache not cleared

### REST API 404 Error

**Symptoms**: `/wp-json/archi/v1/articles` returns 404

**Solutions**:
1. **Flush permalinks**
   - WordPress Admin → Settings → Permalinks
   - Select "Post name" structure
   - Click "Save Changes"

2. **Or via PHP script**
   ```powershell
   php flush-rewrite-rules.php
   ```

3. **Check .htaccess**
   ```powershell
   php fix-htaccess.php
   ```

4. **Verify pretty permalinks enabled**
   - Must not be "Plain" structure
   - Use "Post name" or custom structure

### WPForms Not Creating Posts

**Symptoms**: Form submission successful but no post created

**Diagnosis**:
1. Check WPForms plugin is active
2. Verify form IDs stored correctly
   ```php
   get_option('archi_project_form_id')
   get_option('archi_illustration_form_id')
   ```

**Solutions**:
1. **Deactivate and reactivate theme**
   - Forms are created on activation
   - Check: WPForms → Forms → Should see 2 forms

2. **Manually check form processing**
   - Edit `inc/wpforms-integration.php`
   - Add debug logging:
   ```php
   error_log('Form ID: ' . $form_data['id']);
   error_log('Expected: ' . get_option('archi_project_form_id'));
   ```

3. **Check WordPress debug log**
   ```powershell
   Get-Content C:\wamp64\www\wordpress\wp-content\debug.log -Tail 100
   ```

### Gutenberg Block Not Appearing

**Symptoms**: Block not in inserter or not rendering

**Solutions**:
1. **Rebuild block bundles**
   ```powershell
   npm run build:blocks
   # Or full build
   npm run build
   ```

2. **Clear browser cache**
   - Hard refresh: Ctrl+Shift+R
   - Or disable cache in F12 → Network tab

3. **Check block registration**
   - Verify in `inc/gutenberg-blocks.php`
   - Ensure `add_action('init', 'archi_register_block_name')`

4. **Check for JS errors**
   - F12 → Console tab
   - Look for block-related errors

5. **Verify block category exists**
   ```php
   // In inc/gutenberg-blocks.php
   add_filter('block_categories_all', 'archi_register_block_category');
   ```

### Metadata Not Saving

**Symptoms**: Meta box values not persisting

**Diagnosis**:
1. Check if save handler is hooked
2. Verify nonce is present
3. Check user capabilities

**Solutions**:
1. **Verify save hook**
   ```php
   add_action('save_post', 'archi_save_meta_box_data', 10, 2);
   ```

2. **Check nonce field in meta box**
   ```php
   wp_nonce_field('archi_meta_box_nonce', 'archi_meta_box_nonce_field');
   ```

3. **Verify save function checks**
   ```php
   // In save function:
   if (!isset($_POST['archi_meta_box_nonce_field'])) return;
   if (!wp_verify_nonce($_POST['archi_meta_box_nonce_field'], 'archi_meta_box_nonce')) return;
   if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
   if (!current_user_can('edit_post', $post_id)) return;
   ```

4. **Add debug logging**
   ```php
   error_log('Saving meta for post: ' . $post_id);
   error_log('Field value: ' . print_r($_POST['field_name'], true));
   ```

### Node Positions Not Persisting

**Symptoms**: Graph resets position on page reload

**Solutions**:
1. **Check AJAX endpoint**
   - Verify `save-positions` endpoint registered
   - Test: POST to `/wp-json/archi/v1/save-positions`

2. **Check console for AJAX errors**
   - F12 → Console → Filter for "save-positions"
   - Look for 403 or 500 errors

3. **Verify nonce in AJAX**
   ```javascript
   // In GraphContainer.jsx
   fetch('/wp-json/archi/v1/save-positions', {
       method: 'POST',
       headers: {
           'X-WP-Nonce': wpApiSettings.nonce
       }
   })
   ```

### Build Errors

**Symptoms**: `npm run build` fails

**Common Errors and Solutions**:

1. **"Cannot find module"**
   ```powershell
   # Reinstall dependencies
   Remove-Item -Recurse node_modules
   Remove-Item package-lock.json
   npm install
   ```

2. **"Babel preset not found"**
   ```powershell
   npm install --save-dev @babel/preset-env @babel/preset-react
   ```

3. **"Webpack CLI not found"**
   ```powershell
   npm install --save-dev webpack webpack-cli
   ```

4. **Syntax errors in JSX**
   - Check for missing closing tags
   - Verify `className` not `class`
   - Ensure all JSX wrapped in single parent

## Deprecated Code Issues

### Using Forbidden Prefixes

**Problem**: Code uses `unified_*` or `enhanced_*` prefixes

**Impact**: 
- Confusion about which function to use
- Duplicated functionality
- Maintenance nightmare

**Solution**:
1. **Find all instances**
   ```powershell
   Get-ChildItem -Recurse -Include *.php | Select-String "unified_|enhanced_"
   ```

2. **Rename systematically**
   ```php
   // ❌ WRONG
   function archi_unified_render_card($post_id) { }
   
   // ✅ CORRECT
   function archi_render_card($post_id) { }
   ```

3. **Update all references**
   - Use Serena MCP: `mcp_oraios_serena_find_referencing_symbols`
   - Update calls across all files
   - Test thoroughly

### Duplicate Card Rendering Functions

**Problem**: Multiple functions doing same thing

**Files to Check**:
- `inc/gutenberg-blocks.php`
- `inc/article-card-component.php`
- Custom block files

**Solution**:
1. **Use single function**: `archi_render_article_card()`
2. **Pass options for variations**:
   ```php
   $options = [
       'show_excerpt' => true,
       'show_meta' => false,
       'card_class' => 'custom-variant'
   ];
   echo archi_render_article_card($post_id, $options);
   ```

3. **Remove duplicate functions**
4. **Update all callers**

## Performance Issues

### Graph Slow with Many Nodes

**Symptoms**: Graph lags or freezes with 50+ nodes

**Solutions**:
1. **Reduce force iterations**
   ```javascript
   // In GraphContainer.jsx
   simulation.alphaDecay(0.05) // Faster settling
   ```

2. **Limit visible nodes**
   ```php
   // In REST API
   'posts_per_page' => 100, // Limit total nodes
   ```

3. **Use pagination**
   - Implement lazy loading
   - Load visible nodes first

4. **Optimize D3.js**
   - Reduce tick callback frequency
   - Use canvas instead of SVG for large graphs

### Large Bundle Sizes

**Symptoms**: Slow page load, large JS files

**Solutions**:
1. **Check bundle sizes**
   ```powershell
   ls -lh dist/js/
   ```

2. **Verify code splitting**
   - Webpack should create `vendors.bundle.js`
   - Check `webpack.config.js` optimization section

3. **Tree shaking**
   ```javascript
   // Use named imports
   import { forceSimulation } from 'd3-force';
   // NOT: import * as d3 from 'd3';
   ```

4. **Production build**
   ```powershell
   npm run build  # Minifies code
   ```

## Database Issues

### Orphaned Metadata

**Symptoms**: Old metadata from deleted posts cluttering database

**Diagnosis**:
```powershell
php deep-diagnostic.php
```

**Solutions**:
1. **Clean orphaned post meta**
   ```sql
   DELETE pm FROM wp_postmeta pm
   LEFT JOIN wp_posts p ON pm.post_id = p.ID
   WHERE p.ID IS NULL;
   ```

2. **Clean orphaned term relationships**
   ```sql
   DELETE tr FROM wp_term_relationships tr
   LEFT JOIN wp_posts p ON tr.object_id = p.ID
   WHERE p.ID IS NULL;
   ```

**Caution**: Always backup database before running cleanup queries!

### Corrupted Graph Metadata

**Symptoms**: Graph positions or colors wrong

**Solutions**:
1. **Reset all graph metadata**
   ```php
   // Create script: reset-graph-meta.php
   $posts = get_posts(['post_type' => 'any', 'posts_per_page' => -1]);
   foreach ($posts as $post) {
       delete_post_meta($post->ID, '_archi_graph_position');
       update_post_meta($post->ID, '_archi_show_in_graph', '1');
       update_post_meta($post->ID, '_archi_node_size', 60);
   }
   ```

2. **Regenerate from admin**
   - Edit each post
   - Re-save graph metadata

## Debugging Tools

### Enable Debug Mode
```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
define('ARCHI_DEBUG', true);
```

### View Debug Log
```powershell
# Real-time tail
Get-Content C:\wamp64\www\wordpress\wp-content\debug.log -Wait -Tail 50

# Last 100 lines
Get-Content C:\wamp64\www\wordpress\wp-content\debug.log -Tail 100

# Search for specific term
Select-String -Path C:\wamp64\www\wordpress\wp-content\debug.log -Pattern "archi_"
```

### Browser Console Debugging
```javascript
// In GraphContainer.jsx or other components
console.log('Graph data:', data);
console.table(nodes); // Nice table format
console.error('Error:', error);
console.warn('Warning:', message);
```

### Network Debugging
```
F12 → Network Tab
Filter: Fetch/XHR
Look for: /wp-json/archi/v1/
Check: Status codes, response times, payloads
```

## Prevention Tips

### Before Making Changes
- [ ] Use Serena MCP to understand existing code
- [ ] Check for similar functionality
- [ ] Review `.serena/config.yaml` patterns
- [ ] Test in isolated environment first

### After Making Changes
- [ ] Clear all caches (browser, WordPress, transients)
- [ ] Test in multiple browsers
- [ ] Check console for errors
- [ ] Verify REST API responses
- [ ] Test with both admin and non-admin users

### Regular Maintenance
- [ ] Run diagnostic tools monthly
- [ ] Clean orphaned metadata quarterly
- [ ] Review and refactor deprecated code
- [ ] Update dependencies (`npm update`)
- [ ] Backup database before major changes

## Getting Help

### Information to Gather
1. **Error messages** - Full text from console/logs
2. **Steps to reproduce** - Exact sequence that causes issue
3. **Environment** - PHP version, WordPress version, browser
4. **Recent changes** - What was modified before issue appeared

### Diagnostic Commands
```powershell
# Full diagnostic
php deep-diagnostic.php

# Graph metadata check
php check-graph-meta.php

# API test
php test-api-direct.php

# Check WordPress version
php -r "define('WP_USE_THEMES', false); require('C:/wamp64/www/wordpress/wp-load.php'); echo get_bloginfo('version');"
```
