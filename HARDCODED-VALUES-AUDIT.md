# Hardcoded Values Audit Report
**Date:** November 13, 2025  
**Theme:** Archi-Graph Template  
**Purpose:** Identify hardcoded values that should use dynamic Customizer settings

---

## Executive Summary

This audit identifies hardcoded colors, sizes, opacity values, and other constants throughout the JavaScript codebase that should be replaced with dynamic values from `window.archiGraphSettings` (WordPress Customizer).

**Statistics:**
- **Files Analyzed:** 8 JavaScript files
- **Hardcoded Colors Found:** 35+ instances
- **Hardcoded Sizes/Opacity:** 20+ instances
- **Priority:** HIGH (affects personalization functionality)

---

## Available Settings (from functions.php)

These settings are already available in `window.archiGraphSettings`:

```javascript
{
  // Node Settings
  defaultNodeColor: '#3498db',        // ✅ Already used in some places
  defaultNodeSize: 60,                // ✅ Already used in some places
  clusterStrength: 0.1,
  
  // Display Options
  popupTitleOnly: false,
  showComments: true,
  
  // Animations
  animationMode: 'fade-in',           // ✅ Implemented
  transitionSpeed: 500,
  hoverEffect: 'highlight',
  
  // Links
  linkColor: '#999999',               // ✅ Already used
  linkWidth: 1.5,                     // ✅ Already used
  linkOpacity: 0.6,                   // ✅ Already used
  linkStyle: 'solid',                 // ✅ Already used
  showArrows: false,
  linkAnimation: 'none',              // ✅ Implemented
  
  // Category Colors
  categoryColorsEnabled: false,       // ✅ Implemented
  categoryPalette: 'default',
  showCategoryLegend: true,           // ✅ Implemented
  categoryColors: [...]               // ✅ Implemented
}
```

---

## 🔴 CRITICAL: GraphContainer.jsx

### Hardcoded Colors

| Line | Current Code | Should Use | Priority |
|------|-------------|------------|----------|
| 270 | `.style("background", "#ffffff")` | `settings.backgroundColor` or keep as SVG bg | LOW |
| 616 | `return '#2ecc71'; // Guestbook link` | Create `settings.guestbookLinkColor` | MEDIUM |
| 778 | `"#e74c3c" : "#f39c12"` (priority badges) | Create `settings.priorityFeaturedColor` & `settings.priorityHighColor` | HIGH |
| 780 | `.style("stroke", "#ffffff")` | Keep (badge border always white) | LOW |
| 992 | `color: cat.color \|\| '#3498db'` | Already fallback to `settings.defaultNodeColor` ✅ | DONE |
| 1086 | `color: '#9b59b6'` (pages zone) | Create `settings.pagesZoneColor` | MEDIUM |
| 1393 | `let accentColor = '#3498db'` | Use `settings.defaultNodeColor` | HIGH |

### Hardcoded Sizes & Opacity

| Line | Current Code | Should Use | Priority |
|------|-------------|------------|----------|
| 774 | `.attr("r", 8)` (priority badge) | Create `settings.priorityBadgeSize` (default: 8) | MEDIUM |
| 781 | `.style("stroke-width", 2)` | Keep or create `settings.badgeStrokeWidth` | LOW |
| 910 | `.style("fill-opacity", 0.12)` | Create `settings.clusterFillOpacity` | MEDIUM |
| 912 | `.style("stroke-width", 3)` | Create `settings.clusterStrokeWidth` | MEDIUM |
| 913 | `.style("stroke-opacity", 0.35)` | Create `settings.clusterStrokeOpacity` | MEDIUM |
| 937 | `.style("opacity", 0.7)` | Create `settings.clusterLabelOpacity` | LOW |
| 1118 | `.style("fill-opacity", 0.12)` | Same as cluster settings | MEDIUM |
| 1120-1121 | Island stroke styles | Same as cluster settings | MEDIUM |
| 1146 | `.style("opacity", 0.7)` | Island label opacity | LOW |

### Node Scaling (Active/Hover States)

| Line | Current Code | Should Use | Priority |
|------|-------------|------------|----------|
| 1418-1421 | `(d.node_size \|\| 60) * 1.5` | Use `settings.activeNodeScale` (default: 1.5) | HIGH |

---

## 🟠 MEDIUM: nodeInteractions.js

### Color Fallbacks (All use `#3498db` or `#f39c12`)

| Line | Function | Current | Should Use |
|------|----------|---------|----------|
| 32-33 | `showNodeTooltip` | `#f39c12`, `#3498db` | `settings.projectColor`, `settings.illustrationColor` |
| 35 | `showNodeTooltip` | `titleColor = '#3498db'` | `settings.defaultNodeColor` |
| 149-150 | `showSideTitlePanel` | `#f39c12`, `#3498db` | Same as above |
| 152 | `showSideTitlePanel` | `titleColor = '#3498db'` | `settings.defaultNodeColor` |
| 224-225 | `getNodeColor` | `#f39c12`, `#3498db` | Same as above |
| 235 | `getNodeColor` | `return '#3498db'` | `settings.defaultNodeColor` |

**Recommendation:** Add these to `archiGraphSettings`:
```php
'projectColor' => get_theme_mod('archi_project_color', '#f39c12'),
'illustrationColor' => get_theme_mod('archi_illustration_color', '#3498db'),
```

---

## 🟡 LOW: Other Utility Files

### sidebarUtils.js
- **Line 67:** `cat.color || "#3498db"` → Already uses fallback ✅

### linkAnimations.js
- **Line 145:** `.attr('flood-color', '#fff')` → Keep (glow effect filter)

### nodeVisualEffects.js
- **Line 40:** `.attr('flood-color', '#fff')` → Keep (glow effect filter)

### dataFetcher.js
- **Line 265:** `article.node_color = article.node_color || "#3498db"` → Use `settings.defaultNodeColor`

### polygonRenderer.js
- **Line 192:** `color: category.color || "#3498db"` → Use `settings.defaultNodeColor`

### GraphManager.js (Legacy)
- Multiple instances of `#3498db`, `#999` → **Note:** This file may be deprecated, verify usage first

---

## 📋 Recommended New Customizer Settings

Add these settings to `inc/customizer.php`:

```php
// Priority Badge Colors
$wp_customize->add_setting('archi_priority_featured_color', [
    'default' => '#e74c3c',
    'transport' => 'refresh',
    'sanitize_callback' => 'sanitize_hex_color'
]);

$wp_customize->add_setting('archi_priority_high_color', [
    'default' => '#f39c12',
    'transport' => 'refresh',
    'sanitize_callback' => 'sanitize_hex_color'
]);

// Post Type Colors
$wp_customize->add_setting('archi_project_color', [
    'default' => '#f39c12',
    'transport' => 'refresh',
    'sanitize_callback' => 'sanitize_hex_color'
]);

$wp_customize->add_setting('archi_illustration_color', [
    'default' => '#3498db',
    'transport' => 'refresh',
    'sanitize_callback' => 'sanitize_hex_color'
]);

$wp_customize->add_setting('archi_pages_zone_color', [
    'default' => '#9b59b6',
    'transport' => 'refresh',
    'sanitize_callback' => 'sanitize_hex_color'
]);

$wp_customize->add_setting('archi_guestbook_link_color', [
    'default' => '#2ecc71',
    'transport' => 'refresh',
    'sanitize_callback' => 'sanitize_hex_color'
]);

// Cluster/Island Appearance
$wp_customize->add_setting('archi_cluster_fill_opacity', [
    'default' => 0.12,
    'transport' => 'refresh',
    'sanitize_callback' => 'archi_sanitize_float'
]);

$wp_customize->add_setting('archi_cluster_stroke_width', [
    'default' => 3,
    'transport' => 'refresh',
    'sanitize_callback' => 'absint'
]);

$wp_customize->add_setting('archi_cluster_stroke_opacity', [
    'default' => 0.35,
    'transport' => 'refresh',
    'sanitize_callback' => 'archi_sanitize_float'
]);

// Node Scaling
$wp_customize->add_setting('archi_active_node_scale', [
    'default' => 1.5,
    'transport' => 'refresh',
    'sanitize_callback' => 'archi_sanitize_float'
]);

$wp_customize->add_setting('archi_priority_badge_size', [
    'default' => 8,
    'transport' => 'refresh',
    'sanitize_callback' => 'absint'
]);
```

---

## 🎯 Implementation Priority

### Phase 1: HIGH Priority (Do First)
1. **Priority badge colors** (lines 778 in GraphContainer.jsx)
2. **Active node accent color** (line 1393 in GraphContainer.jsx)
3. **Active node scale** (lines 1418-1421 in GraphContainer.jsx)
4. **Post type colors** (nodeInteractions.js - all functions)
5. **dataFetcher.js fallback** (line 265)

### Phase 2: MEDIUM Priority
1. **Guestbook link color** (line 616 in GraphContainer.jsx)
2. **Pages zone color** (line 1086 in GraphContainer.jsx)
3. **Cluster/Island opacity & stroke** (lines 910-1121 in GraphContainer.jsx)
4. **Priority badge size** (line 774 in GraphContainer.jsx)

### Phase 3: LOW Priority (Optional)
1. **Background colors** (SVG backgrounds)
2. **Label opacity values** (can stay hardcoded)
3. **Badge stroke widths** (minor visual detail)

---

## 📝 Implementation Steps

1. **Add new settings to `inc/customizer.php`**
   - Group by category (Colors, Appearance, Post Types)
   - Use appropriate sanitization callbacks
   - Add descriptive labels in French

2. **Update `functions.php` localization**
   - Add new theme_mod values to `archiGraphSettings` array
   - Ensure they're available to JavaScript

3. **Replace hardcoded values in JavaScript**
   - Start with HIGH priority items
   - Use `settings.propertyName || 'fallback'` pattern
   - Test each change individually

4. **Update presets in `inc/graph-config.php`**
   - Add new settings to preset configurations
   - Ensure `archi_visual_save_preset()` syncs new values

5. **Test thoroughly**
   - Check Customizer live preview
   - Verify settings persist after page reload
   - Test all 4 presets (minimal, balanced, rich, maximum)

---

## 🔍 Search Commands Used

```bash
# Find hex colors
grep -r "#[0-9a-fA-F]\{6\}" assets/js/

# Find opacity values
grep -r "opacity.*0\.[0-9]" assets/js/

# Find stroke-width
grep -r "stroke-width.*[0-9]" assets/js/

# Find node size references
grep -r "node_size.*60\|defaultNodeSize.*60" assets/js/
```

---

## ✅ Next Actions

1. Review this audit with the development team
2. Prioritize which settings to add based on user needs
3. Create a branch for implementing changes
4. Add Customizer settings first (backend)
5. Update JavaScript to use settings (frontend)
6. Test each phase before moving to next
7. Update documentation with new settings

---

**End of Audit Report**
