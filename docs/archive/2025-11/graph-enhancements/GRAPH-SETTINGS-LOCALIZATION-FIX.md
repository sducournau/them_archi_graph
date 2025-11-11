# 🔥 Fix: Graph Settings Localization

## ❌ Problem Identified

User reported: **"certains parametres du graph ne changent pas le graph, les palettes de couleur, l'effet au survol ect, les liens"**

### Root Cause

The function `archi_localize_graph_settings()` in `inc/customizer.php` was:
1. ❌ Using wrong script handle: `'archi-graph-main'` (doesn't exist)
2. ❌ Called at wrong timing: `wp_enqueue_scripts` priority 20 (too late)
3. ❌ Separate hook from actual script enqueue

**Result**: `window.archiGraphSettings` was **never populated**, so all Customizer changes were ignored.

## ✅ Solution Applied

### Step 1: Moved Localization to functions.php

**File**: `functions.php` (lines 380-430)

```php
// Variables pour JavaScript (uniquement sur la page d'accueil où archi-app est chargé)
if (is_front_page()) {
    wp_localize_script('archi-app', 'archiGraph', [
        'apiUrl' => home_url('/wp-json/archi/v1/'),
        'nonce' => wp_create_nonce('wp_rest'),
        'themeUrl' => ARCHI_THEME_URI,
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'config' => archi_visual_get_frontend_config(),
    ]);
    
    wp_localize_script('archi-app', 'archiGraphConfig', [
        'popupTitleOnly' => get_theme_mod('archi_graph_popup_title_only', false),
        'showComments' => get_theme_mod('archi_graph_show_comments', true),
    ]);
    
    // 🔥 Graph settings from Customizer (NEW - properly localized)
    wp_localize_script('archi-app', 'archiGraphSettings', [
        // Node settings
        'defaultNodeColor' => get_theme_mod('archi_default_node_color', '#3498db'),
        'defaultNodeSize' => get_theme_mod('archi_default_node_size', 60),
        'clusterStrength' => get_theme_mod('archi_cluster_strength', 0.1),
        
        // Display options
        'popupTitleOnly' => get_theme_mod('archi_graph_popup_title_only', false),
        'showComments' => get_theme_mod('archi_graph_show_comments', true),
        
        // Animations and effects
        'animationMode' => get_theme_mod('archi_graph_animation_mode', 'fade-in'),
        'transitionSpeed' => get_theme_mod('archi_graph_transition_speed', 500),
        'hoverEffect' => get_theme_mod('archi_graph_hover_effect', 'highlight'),
        
        // Links configuration
        'linkColor' => get_theme_mod('archi_graph_link_color', '#999999'),
        'linkWidth' => get_theme_mod('archi_graph_link_width', 1.5),
        'linkOpacity' => get_theme_mod('archi_graph_link_opacity', 0.6),
        'linkStyle' => get_theme_mod('archi_graph_link_style', 'solid'),
        'showArrows' => get_theme_mod('archi_graph_show_arrows', false),
        'linkAnimation' => get_theme_mod('archi_graph_link_animation', 'none'),
        
        // Category colors
        'categoryColorsEnabled' => get_theme_mod('archi_graph_category_colors_enabled', false),
        'categoryPalette' => get_theme_mod('archi_graph_category_palette', 'default'),
        'showCategoryLegend' => get_theme_mod('archi_graph_show_category_legend', true),
        
        // Get actual palette colors
        'categoryColors' => archi_get_category_color_palette(get_theme_mod('archi_graph_category_palette', 'default'))
    ]);
```

**Benefits**:
✅ Called immediately after `wp_enqueue_script('archi-app')` at line 341-347
✅ Correct timing: same hook, same condition, sequential execution
✅ No conditional check needed - script is guaranteed to be enqueued at this point

### Step 2: Deprecated Old Function

**File**: `inc/customizer.php` (lines 730-776)

```php
/**
 * Localize graph settings for JavaScript
 * ⚠️ DEPRECATED - Moved to functions.php for proper timing
 * This function had timing issues with wp_enqueue_scripts hook
 * Graph settings are now localized directly in functions.php after wp_enqueue_script('archi-app')
 */
function archi_localize_graph_settings() {
    // Function deprecated - settings now localized in functions.php
    // Kept for reference only
    return;
    
    // ... rest of function commented out with early return
}
// Hook disabled - localization moved to functions.php
// add_action('wp_enqueue_scripts', 'archi_localize_graph_settings', 20);
```

## 🧪 Testing

### Step 1: Verify window.archiGraphSettings is Populated

Open browser DevTools Console (F12) on homepage and run:

```javascript
console.log(window.archiGraphSettings);
```

**Expected Output**:
```javascript
{
  defaultNodeColor: "#3498db",
  defaultNodeSize: 60,
  clusterStrength: 0.1,
  animationMode: "fade-in",
  transitionSpeed: 500,
  hoverEffect: "highlight",
  linkColor: "#999999",
  linkWidth: 1.5,
  linkOpacity: 0.6,
  linkStyle: "solid",
  showArrows: false,
  linkAnimation: "none",
  categoryColorsEnabled: false,
  categoryPalette: "default",
  showCategoryLegend: true,
  categoryColors: ["#3498db", "#2980b9", ...]
}
```

### Step 2: Test Customizer Changes

1. Go to **Appearance → Customize**
2. Navigate to **Graph Options**
3. Change:
   - Link color (e.g., to red `#ff0000`)
   - Hover effect (e.g., to "scale")
   - Category palette (e.g., to "warm")
4. **Publish** changes
5. Refresh homepage
6. Verify console shows updated values
7. Hover over nodes to see new effects

### Step 3: Verify JavaScript Usage

**File**: `assets/js/components/GraphContainer.jsx` (line 425)

```javascript
const customizerSettings = window.archiGraphSettings || {};
```

GraphContainer should now receive all settings properly.

## 📊 Affected Parameters

All these Customizer options should now work:

### 🎨 Visual
- ✅ `defaultNodeColor` - Node base color
- ✅ `defaultNodeSize` - Node diameter
- ✅ `categoryColorsEnabled` - Enable category-based colors
- ✅ `categoryPalette` - Color palette (default, warm, cool, vibrant, pastel, nature, monochrome)
- ✅ `categoryColors` - Actual palette array

### 🔗 Links
- ✅ `linkColor` - Link stroke color
- ✅ `linkWidth` - Link thickness
- ✅ `linkOpacity` - Link transparency
- ✅ `linkStyle` - Solid, dashed, dotted
- ✅ `showArrows` - Directional arrows
- ✅ `linkAnimation` - None, pulse, flow

### ✨ Effects
- ✅ `hoverEffect` - Highlight, scale, glow, shadow
- ✅ `animationMode` - Fade-in, slide-in, zoom-in
- ✅ `transitionSpeed` - Animation duration in ms

### 📦 Display
- ✅ `popupTitleOnly` - Minimize tooltip content
- ✅ `showComments` - Show comments in sidebar
- ✅ `showCategoryLegend` - Display category legend

## 🔧 Technical Details

### Why the Original Code Failed

**Problem 1: Wrong Script Handle**
```php
// ❌ This script doesn't exist
wp_localize_script('archi-graph-main', 'archiGraphSettings', $data);
```

**Problem 2: Wrong Hook Priority**
```php
// functions.php line 341-347 (priority 10 - default)
wp_enqueue_script('archi-app', ...);

// customizer.php line 776 (priority 20)
add_action('wp_enqueue_scripts', 'archi_localize_graph_settings', 20);
```

Even with correct handle, this timing is problematic because:
1. Script is enqueued at priority 10
2. Localization attempts at priority 20
3. By then, the script is already queued but localization may not attach properly
4. **Best practice**: Call `wp_localize_script()` immediately after `wp_enqueue_script()`

### Why the New Code Works

```php
// ✅ Sequential, same block, same condition
if (is_front_page()) {
    wp_enqueue_script('archi-app', ...);  // Line 341-347
    
    wp_localize_script('archi-app', 'archiGraph', ...);  // Line 382-388
    wp_localize_script('archi-app', 'archiGraphConfig', ...);  // Line 390-393
    wp_localize_script('archi-app', 'archiGraphSettings', ...);  // Line 395-430 NEW
}
```

**Benefits**:
- ✅ Same execution context
- ✅ Guaranteed script exists before localization
- ✅ No race conditions
- ✅ Follows WordPress best practices
- ✅ Consistent with other localizations in the theme

## 🚀 Cache Clearing

After applying fix:

1. **WordPress Cache**:
   ```php
   wp_cache_flush();
   ```

2. **Browser Cache**:
   - Hard reload: `Ctrl + Shift + R` (Windows/Linux)
   - Hard reload: `Cmd + Shift + R` (Mac)

3. **Theme Cache** (if using caching plugin):
   - Clear theme cache
   - Clear page cache

## ✅ Completion Checklist

- [x] Moved localization to functions.php
- [x] Deprecated old function in customizer.php
- [x] Documented reasoning and fix
- [ ] User testing: Verify window.archiGraphSettings populated
- [ ] User testing: Verify Customizer changes apply to graph
- [ ] User testing: Verify all parameters work correctly

## 📝 Related Files

- `functions.php` - Graph settings localization (lines 395-430)
- `inc/customizer.php` - Deprecated function (lines 730-776)
- `assets/js/components/GraphContainer.jsx` - Settings consumption (line 425)
- `assets/js/utils/graph-settings-helper.js` - Default fallbacks (lines 12-34)

---

**Date**: 2025-01-10  
**Issue**: Graph Customizer parameters not applying  
**Status**: Fixed ✅  
**Next Step**: User testing required
