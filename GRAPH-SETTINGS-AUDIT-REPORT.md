# 🔍 Graph Settings Audit Report

**Date:** November 11, 2025  
**Issue:** Graph personalization settings not persisting or affecting the graph visualization  
**Severity:** HIGH - Core functionality not working as expected

---

## 📋 Executive Summary

The graph settings system has **TWO SEPARATE AND CONFLICTING** configuration systems that don't communicate with each other:

1. **WordPress Customizer Settings** (`inc/customizer.php`) - 25+ granular settings
2. **Graph Config Presets** (`inc/graph-config.php`) - 4 preset-based configurations

**Root Cause:** The JavaScript graph implementation reads Customizer settings, but the Customizer settings are **NOT being used** when preset system is active. The preset system overrides everything but doesn't expose the detailed controls.

---

## 🚨 Critical Issues Identified

### Issue #1: Dual Settings Systems (Architectural Conflict)

**Location:** `inc/customizer.php` vs `inc/graph-config.php`

**The Problem:**
- **Customizer** defines 25+ granular settings: `archi_graph_animation_mode`, `archi_graph_hover_effect`, `archi_graph_link_color`, etc.
- **Graph Config** defines 4 presets (minimal, standard, rich, performance) with simplified settings
- **NO SYNCHRONIZATION** between the two systems

**Code Evidence:**

```php
// inc/customizer.php (Lines 317-335)
$wp_customize->add_setting('archi_graph_animation_mode', [
    'default' => 'fade-in',
    'transport' => 'refresh',
    'sanitize_callback' => 'sanitize_text_field'
]);
// ... 24 more settings like this

// inc/graph-config.php (Lines 32-92)
function archi_visual_get_presets() {
    return [
        'standard' => [
            'settings' => [
                'animation_type' => 'slide',  // Different naming!
                'hover_effect' => 'scale',     // Different semantics!
                // Only 8 settings vs 25+ in Customizer
            ]
        ]
    ];
}
```

**Impact:** When a user changes settings in the Customizer, they expect it to affect the graph. But if the preset system is active, the preset values are used instead, ignoring the Customizer changes.

---

### Issue #2: Preset System Doesn't Read Customizer Values

**Location:** `inc/graph-config.php` - `archi_visual_get_current_config()`

**The Problem:**
The preset system attempts to override settings, but it uses **DIFFERENT SETTING NAMES** and **INCOMPLETE MAPPING**.

**Code Evidence:**

```php
// Lines 257-306 in graph-config.php
function archi_visual_get_current_config() {
    $preset = get_option('archi_graph_preset', 'standard');
    
    // Apply preset overrides - BUT LOOK AT THE MAPPING!
    foreach ($presets[$preset]['settings'] as $key => $value) {
        switch ($key) {
            case 'animation_enabled':
                $config['animation']['enabled'] = $value;
                break;
            case 'animation_type':
                $config['animation']['type'] = $value;
                break;
            // ... only 7 cases mapped
        }
    }
    
    return $config;  // ❌ Customizer settings IGNORED!
}
```

**Missing Mappings:**
- ❌ No mapping for `archi_graph_link_color` → not used
- ❌ No mapping for `archi_graph_link_width` → not used  
- ❌ No mapping for `archi_graph_category_palette` → not used
- ❌ No mapping for `archi_graph_show_category_legend` → not used
- ❌ 18+ more settings completely ignored

---

### Issue #3: Frontend JavaScript Reads Customizer, Not Presets

**Location:** `functions.php` (Lines 395-430) → `GraphContainer.jsx` (Line 425)

**The Problem:**
The frontend JavaScript receives Customizer settings via `wp_localize_script()`, but these are **STATIC VALUES** that don't reflect preset changes.

**Code Evidence:**

```php
// functions.php - Lines 395-430
wp_localize_script('archi-app', 'archiGraphSettings', [
    'defaultNodeColor' => get_theme_mod('archi_default_node_color', '#3498db'),
    'animationMode' => get_theme_mod('archi_graph_animation_mode', 'fade-in'),
    'linkColor' => get_theme_mod('archi_graph_link_color', '#999999'),
    // ... ALL from Customizer theme_mod, NONE from preset system
]);
```

```javascript
// GraphContainer.jsx - Line 425
const customizerSettings = window.archiGraphSettings || {};
console.log('🎨 Using Customizer settings:', customizerSettings);
```

**Impact:** 
1. If you change a preset in the Graph Settings page, **nothing happens** because JS reads Customizer values
2. If you change Customizer values, they work UNLESS a preset overrides them server-side
3. **Inconsistent behavior** - sometimes settings work, sometimes they don't

---

### Issue #4: Settings Not Persisted Properly

**Location:** `inc/graph-settings-page.php` - `archi_render_graph_settings_page()`

**The Problem:**
The Graph Settings admin page saves preset choice to `archi_graph_preset` option, but this **DOESN'T UPDATE** the individual Customizer settings.

**Code Evidence:**

```php
// Lines 33-36
if (isset($_POST['archi_graph_preset']) && check_admin_referer('archi_graph_settings')) {
    $preset = sanitize_text_field($_POST['archi_graph_preset']);
    archi_visual_save_preset($preset);  // ✅ Saves preset name
    // ❌ DOESN'T update individual theme_mod settings!
}
```

```php
// Lines 315-319 - archi_visual_save_preset()
function archi_visual_save_preset($preset_name) {
    if (isset($presets[$preset_name])) {
        return update_option('archi_graph_preset', $preset_name);  // Only saves preset name
        // ❌ Doesn't call set_theme_mod() for each setting
    }
}
```

**Impact:** 
- Preset selection saves to `wp_options` table
- Customizer settings remain unchanged in `wp_options` (theme_mods)
- **Frontend still reads old Customizer values**
- User sees no changes on the graph

---

### Issue #5: Incomplete Settings Application in JavaScript

**Location:** `assets/js/utils/nodeVisualEffects.js` - `applyContinuousEffects()`

**The Problem:**
Even when settings are passed correctly, some settings are **PARTIALLY IMPLEMENTED** or **MISUNDERSTOOD**.

**Code Evidence:**

```javascript
// Lines 142-177
export function applyContinuousEffects(nodeElements, svg, settings = {}) {
    const hoverEffect = settings.hoverEffect || 'highlight';
    
    if (hoverEffect === 'pulse') {
        pulseEnabled = true;
    } else if (hoverEffect === 'glow') {
        glowEnabled = true;
    } else {
        // ❌ Falls back to NODE-LEVEL properties instead of respecting settings
        pulseEnabled = (d.hover?.pulse === true) || (d.pulse_effect === true);
        glowEnabled = (d.hover?.glow === true) || (d.glow_effect === true);
    }
}
```

**Issues:**
- `hoverEffect: 'highlight'` → Does nothing (no implementation)
- `hoverEffect: 'scale'` → Only partially works
- `animationMode` settings (fade-in, bounce, etc.) → **NOT IMPLEMENTED AT ALL**
- `linkAnimation` settings → **NOT IMPLEMENTED**

---

## 🔍 Data Flow Analysis

### Current Broken Flow

```
┌─────────────────────┐
│ User changes        │
│ Customizer Settings │
│ (25+ options)       │
└──────────┬──────────┘
           │ set_theme_mod()
           ▼
┌─────────────────────┐
│ wp_options table    │
│ theme_mods_archi-   │
│ graph-template      │
└──────────┬──────────┘
           │
           ├─────────────────────────────┐
           │                             │
           ▼                             ▼
┌─────────────────────┐      ┌─────────────────────┐
│ functions.php       │      │ Graph Settings Page │
│ wp_localize_script  │      │ (Ignored)           │
│ ✅ WORKS            │      └─────────────────────┘
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ window.             │
│ archiGraphSettings  │
│ (JavaScript)        │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ GraphContainer.jsx  │
│ ✅ Reads correctly  │
│ ⚠️  Some not impl.  │
└─────────────────────┘

VS

┌─────────────────────┐
│ User selects Preset │
│ (Graph Settings pg) │
└──────────┬──────────┘
           │ update_option()
           ▼
┌─────────────────────┐
│ wp_options:         │
│ archi_graph_preset  │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ archi_visual_get_   │
│ current_config()    │
│ ❌ NOT USED BY JS   │
└─────────────────────┘
```

---

## 📊 Settings Coverage Matrix

| Setting Name (Customizer)             | Used by JS | In Presets | Applied |
|---------------------------------------|-----------|-----------|---------|
| `archi_default_node_color`            | ✅ Yes     | ❌ No      | ✅ Works |
| `archi_default_node_size`             | ✅ Yes     | ❌ No      | ✅ Works |
| `archi_cluster_strength`              | ✅ Yes     | ❌ No      | ✅ Works |
| `archi_graph_animation_mode`          | ✅ Yes     | ⚠️  Partial | ❌ **Not implemented** |
| `archi_graph_transition_speed`        | ✅ Yes     | ⚠️  Partial | ⚠️  Partial |
| `archi_graph_hover_effect`            | ✅ Yes     | ⚠️  Partial | ⚠️  Partial |
| `archi_graph_link_color`              | ✅ Yes     | ❌ No      | ✅ Works |
| `archi_graph_link_width`              | ✅ Yes     | ❌ No      | ✅ Works |
| `archi_graph_link_opacity`            | ✅ Yes     | ❌ No      | ✅ Works |
| `archi_graph_link_style`              | ✅ Yes     | ❌ No      | ✅ Works |
| `archi_graph_show_arrows`             | ✅ Yes     | ❌ No      | ❌ **Not implemented** |
| `archi_graph_link_animation`          | ✅ Yes     | ⚠️  Partial | ❌ **Not implemented** |
| `archi_graph_category_colors_enabled` | ✅ Yes     | ❌ No      | ❌ **Not implemented** |
| `archi_graph_category_palette`        | ✅ Yes     | ❌ No      | ❌ **Not implemented** |
| `archi_graph_show_category_legend`    | ✅ Yes     | ❌ No      | ❌ **Not implemented** |
| `archi_graph_popup_title_only`        | ✅ Yes     | ❌ No      | ✅ Works |
| `archi_graph_show_comments`           | ✅ Yes     | ❌ No      | ✅ Works |

**Legend:**
- ✅ **Works**: Fully functional
- ⚠️  **Partial**: Partially implemented or inconsistent
- ❌ **Not implemented**: No code to handle this setting

---

## 🎯 Root Causes Summary

### Primary Root Cause
**Architectural Design Flaw:** Two independent settings systems were created without integration:
1. Granular Customizer controls for user customization
2. Preset system for quick configuration

Neither system was made aware of the other, creating a **split-brain** scenario.

### Secondary Root Causes

1. **Incomplete Feature Implementation**
   - Many Customizer settings exist but have no JavaScript implementation
   - Visual effects like `animationMode`, `linkAnimation`, category colors not coded

2. **Settings Persistence Gap**
   - Preset changes don't update Customizer theme_mods
   - Frontend always reads stale Customizer values

3. **Poor Documentation**
   - No clear guidance on which system to use
   - Developers added to both systems without syncing

---

## ✅ Recommendations

### Option A: Use Customizer Only (Recommended)

**Pros:**
- Already works for most settings
- Direct connection to frontend
- Live preview capability
- WordPress native interface

**Implementation:**
1. Remove Graph Settings admin page
2. Remove preset system from `graph-config.php`
3. Implement missing JavaScript features
4. Keep only `archi_visual_get_frontend_config()` for default values

**Estimated Effort:** 8-12 hours

---

### Option B: Integrate Preset with Customizer

**Pros:**
- Keep both interfaces
- Quick presets + fine-tuning
- Best of both worlds

**Implementation:**
1. When preset selected, update ALL individual Customizer settings via `set_theme_mod()`
2. Map preset values to Customizer setting names correctly
3. Make preset selector available in Customizer
4. Sync both ways (preset → customizer, customizer → detect "custom" preset)

**Estimated Effort:** 16-24 hours

---

### Option C: Use Presets Only (Not Recommended)

**Pros:**
- Simpler UI
- Fewer settings to manage

**Cons:**
- Loses granular control
- Need to rewrite entire frontend integration
- Breaks existing Customizer investment

**Estimated Effort:** 20-30 hours

---

## 🛠️ Immediate Fixes Needed (Regardless of Option)

### 1. Implement Missing JavaScript Features

**Priority: HIGH**

```javascript
// In GraphContainer.jsx or new animation utility
function applyEntranceAnimation(nodeElements, settings) {
    const mode = settings.animationMode || 'fade-in';
    const speed = settings.transitionSpeed || 500;
    
    switch(mode) {
        case 'fade-in':
            nodeElements
                .style('opacity', 0)
                .transition()
                .duration(speed)
                .style('opacity', 1);
            break;
        case 'scale-up':
            // Implementation
            break;
        case 'bounce':
            // Implementation
            break;
    }
}
```

### 2. Implement Link Animations

**Priority: MEDIUM**

```javascript
function updateLinks(container, links, settings) {
    const linkAnimation = settings.linkAnimation || 'none';
    
    if (linkAnimation === 'pulse') {
        // Add pulsing effect to links
    } else if (linkAnimation === 'flow') {
        // Add animated flow effect
    }
}
```

### 3. Implement Category Colors

**Priority: MEDIUM**

```javascript
function getNodeColor(nodeData, settings) {
    if (settings.categoryColorsEnabled && nodeData.categories?.length > 0) {
        const categoryId = nodeData.categories[0].id;
        const palette = settings.categoryColors || [];
        const index = categoryId % palette.length;
        return palette[index];
    }
    return nodeData.node_color || settings.defaultNodeColor;
}
```

### 4. Fix Preset Save Function

**Priority: HIGH** (if keeping presets)

```php
function archi_visual_save_preset($preset_name) {
    $presets = archi_visual_get_presets();
    
    if (!isset($presets[$preset_name])) {
        return false;
    }
    
    // Update the preset option
    update_option('archi_graph_preset', $preset_name);
    
    // 🔥 NEW: Also update all Customizer settings
    $settings = $presets[$preset_name]['settings'];
    
    // Map preset settings to Customizer theme_mods
    $mapping = [
        'animation_enabled' => 'archi_graph_animation_enabled',
        'animation_type' => 'archi_graph_animation_mode',
        'hover_effect' => 'archi_graph_hover_effect',
        // ... complete mapping for all 25+ settings
    ];
    
    foreach ($mapping as $preset_key => $theme_mod_key) {
        if (isset($settings[$preset_key])) {
            set_theme_mod($theme_mod_key, $settings[$preset_key]);
        }
    }
    
    return true;
}
```

---

## 📝 Testing Checklist

After implementing fixes, verify:

- [ ] Change node color in Customizer → Reflects immediately on graph
- [ ] Change link color in Customizer → Links update
- [ ] Change animation mode → Nodes animate on load
- [ ] Change hover effect → Hovering triggers correct effect
- [ ] Enable category colors → Nodes use category palette
- [ ] Change link animation → Links animate correctly
- [ ] Save settings → Persist after page reload
- [ ] Select preset → All visual aspects change
- [ ] Change preset then customize → Custom changes persist

---

## 📚 Files to Modify

### Critical Files
1. `inc/graph-config.php` - Preset system logic
2. `inc/customizer.php` - Customizer definitions
3. `functions.php` - Settings localization
4. `assets/js/components/GraphContainer.jsx` - Main graph component
5. `assets/js/utils/nodeVisualEffects.js` - Visual effects implementation

### Supporting Files
6. `inc/graph-settings-page.php` - Admin settings page
7. `assets/js/utils/nodeInteractions.js` - Interaction handlers
8. `assets/js/components/LinkRenderer.jsx` - Link rendering

---

## 💡 Quick Win: Browser Console Test

Run this in browser console on homepage to verify current settings:

```javascript
// Check what settings are available
console.log('Available settings:', window.archiGraphSettings);

// Check what the graph is actually using
console.log('Graph config:', window.archiGraph?.config);

// Manually test a setting change
const testSettings = {
    ...window.archiGraphSettings,
    linkColor: '#ff0000',
    defaultNodeColor: '#00ff00'
};
console.log('Test with:', testSettings);
// Then manually call updateNodes/updateLinks with testSettings
```

---

## 🎓 Lessons Learned

1. **Avoid Duplicate Systems:** Before creating new settings interface, check existing ones
2. **Settings Should Have Single Source of Truth:** Either Customizer OR custom page, not both
3. **Backend-Frontend Contract:** Settings defined in PHP must be used in JavaScript
4. **Feature Parity:** If a setting exists in UI, it must be implemented in code
5. **Testing Coverage:** Every setting should have a test case

---

## 📞 Next Steps

1. **Decide on Architecture:** Choose Option A, B, or C above
2. **Create Implementation Plan:** Break down into tasks
3. **Implement Missing Features:** Complete partial implementations
4. **Test Thoroughly:** Use checklist above
5. **Document for Users:** Update theme documentation

---

**End of Audit Report**
