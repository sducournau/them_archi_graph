# WordPress Customizer Integration

## Overview

The Archi-Graph theme now includes complete WordPress Customizer API integration, providing a user-friendly interface for theme personalization with real-time live preview.

## Features

### 1. Header Options
Control the behavior and appearance of the auto-hiding header on homepage/graph pages.

**Settings:**
- **Hide Delay** (0-5000ms): Time before header automatically hides
  - Default: 500ms
  - Live Preview: Yes
  
- **Animation Type**: Choose transition timing function
  - Options: Linear, Ease, Ease-in, Ease-out, Ease-in-out, Cubic-bezier
  - Default: ease-in-out
  - Live Preview: Yes
  
- **Animation Duration** (0.1-2s): Speed of show/hide animation
  - Default: 0.3s
  - Live Preview: Yes
  
- **Trigger Zone Height** (20-150px): Height of invisible zone at top that triggers header
  - Default: 50px
  - Live Preview: Partial (CSS updates immediately)

### 2. Graph Visualization
Default parameters for graph nodes and behavior.

**Settings:**
- **Default Node Color**: Base color for graph nodes (#hex)
  - Default: #3498db
  
- **Default Node Size** (40-120px): Base size for nodes
  - Default: 60px
  
- **Cluster Strength** (0-1): Force strength for node clustering
  - Default: 0.3
  
- **Animation Duration** (500-5000ms): Speed of graph animations
  - Default: 1500ms

### 3. Typography
Global font settings.

**Settings:**
- **Font Family**: Choose from dropdown or custom
  - Options: System fonts, Google Fonts, Custom
  - Default: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto...
  
- **Base Font Size** (12-24px): Root font size
  - Default: 16px
  - Live Preview: Yes

### 4. Colors
Theme color scheme.

**Settings:**
- **Primary Color**: Main brand color
  - Default: #3498db
  - Live Preview: Yes
  
- **Secondary Color**: Accent color
  - Default: #2ecc71
  - Live Preview: Yes

### 5. Social Media
Social network profile URLs.

**Settings:**
- Facebook URL
- Twitter URL
- Instagram URL
- LinkedIn URL
- YouTube URL
- GitHub URL

### 6. Footer
Footer content and display options.

**Settings:**
- **Copyright Text**: Custom copyright notice
  - Default: "© 2025 Archi-Graph. Tous droits réservés."
  - Live Preview: Yes
  
- **Show Social Links**: Toggle social icons in footer
  - Default: Yes
  - Live Preview: Yes

## Usage

### Accessing the Customizer

1. Go to **Appearance > Customize** in WordPress admin
2. Navigate through the sections in the left sidebar
3. Changes preview in real-time in the right panel
4. Click **Publish** to save changes

### Live Preview

Settings marked with ⚡ in the control panel update in real-time without page reload. Other settings may require clicking the refresh button in the preview panel.

### Code Usage

**Retrieving Options in PHP:**
```php
// Get header hide delay
$delay = get_theme_mod('archi_header_hide_delay', 500);

// Get animation type
$animation = get_theme_mod('archi_header_animation_type', 'ease-in-out');

// Get primary color
$color = get_theme_mod('archi_primary_color', '#3498db');
```

**Using in Templates:**
```php
<!-- Dynamic header behavior -->
<script>
const headerDelay = <?php echo absint(get_theme_mod('archi_header_hide_delay', 500)); ?>;
const animationType = '<?php echo esc_js(get_theme_mod('archi_header_animation_type', 'ease-in-out')); ?>';
</script>

<!-- Dynamic trigger zone -->
<div class="header-trigger-zone" style="height: <?php echo absint(get_theme_mod('archi_header_trigger_height', 50)); ?>px;"></div>
```

**Using in JavaScript (via wp.customize):**
```javascript
wp.customize('archi_header_hide_delay', function(value) {
    value.bind(function(newval) {
        // Update header behavior
        updateHeaderBehavior(newval);
    });
});
```

## File Structure

```
inc/
  └── customizer.php              # Main customizer registration
assets/
  └── js/
      ├── customizer-preview.js   # Live preview bindings
      └── customizer-controls.js  # Enhanced control panel UX
```

## Implementation Details

### Customizer Registration (`inc/customizer.php`)

**Main Functions:**
- `archi_customize_register($wp_customize)` - Registers all settings, sections, and controls
- `archi_customizer_css()` - Outputs dynamic CSS based on customizer values
- `archi_sanitize_float($value)` - Sanitizes float inputs
- `archi_sanitize_checkbox($checked)` - Sanitizes checkbox values
- `archi_adjust_color_brightness($hex, $steps)` - Color manipulation helper

**Hooks:**
- `customize_register` - Registers customizer options
- `wp_head` - Outputs dynamic CSS
- `customize_preview_init` - Enqueues preview JavaScript
- `customize_controls_enqueue_scripts` - Enqueues control panel JavaScript

### Live Preview (`customizer-preview.js`)

Binds `postMessage` transport settings to live update the preview panel:
- Header behavior (delay, animation)
- Typography (font family, size)
- Colors (primary, secondary)
- Footer content

### Control Enhancements (`customizer-controls.js`)

Adds UX improvements to the control panel:
- Contextual help tips
- Live preview indicators (⚡)
- Range slider value displays
- Enhanced color picker styling
- Export/Import placeholders (future feature)

## Integration with Existing Code

### Modified Files

**functions.php:**
- Added: `require_once ARCHI_THEME_DIR . '/inc/customizer.php';`

**front-page.php:**
- Replaced hardcoded header delay (500ms) with `get_theme_mod('archi_header_hide_delay', 500)`
- Replaced hardcoded animation type ('ease-in-out') with `get_theme_mod('archi_header_animation_type', 'ease-in-out')`
- Replaced hardcoded animation duration (0.3s) with `get_theme_mod('archi_header_animation_duration', 0.3)`
- Replaced hardcoded trigger height (50px) with `get_theme_mod('archi_header_trigger_height', 50)`

**page-home.php:**
- Same updates as front-page.php

### Backward Compatibility

All customizer options use sensible defaults matching the previous hardcoded values:
- Header hide delay: 500ms (unchanged)
- Animation type: ease-in-out (unchanged)
- Animation duration: 0.3s (unchanged)
- Trigger height: 50px (unchanged)

Existing sites will behave identically until users modify settings in the Customizer.

## Security

All settings use appropriate sanitization:
- `absint()` for integers (delays, sizes)
- `floatval()` for decimals (animation duration, cluster strength)
- `esc_js()` for JavaScript strings
- `esc_attr()` for HTML attributes
- `sanitize_hex_color()` for color values
- `esc_url_raw()` for URLs
- `sanitize_text_field()` for text inputs

## Performance

- CSS is output inline in `<head>` via `archi_customizer_css()` hooked to `wp_head`
- JavaScript files are only loaded in customizer context (`customize_preview_init`, `customize_controls_enqueue_scripts`)
- No database queries beyond standard `get_theme_mod()` calls
- Settings use `postMessage` transport for instant preview (no page reload)

## Extending the Customizer

### Adding a New Setting

1. **Register in `inc/customizer.php`:**
```php
// Add setting
$wp_customize->add_setting('archi_new_setting', [
    'default' => 'default_value',
    'transport' => 'postMessage', // or 'refresh'
    'sanitize_callback' => 'sanitize_text_field'
]);

// Add control
$wp_customize->add_control('archi_new_setting', [
    'label' => __('New Setting', 'archi-graph'),
    'section' => 'archi_section_name',
    'type' => 'text'
]);
```

2. **Add live preview in `customizer-preview.js`:**
```javascript
wp.customize('archi_new_setting', function(value) {
    value.bind(function(newval) {
        // Update DOM or styles
        $('.element').css('property', newval);
    });
});
```

3. **Use in templates:**
```php
$value = get_theme_mod('archi_new_setting', 'default_value');
```

### Adding a New Section

```php
$wp_customize->add_section('archi_new_section', [
    'title' => __('New Section', 'archi-graph'),
    'description' => __('Section description', 'archi-graph'),
    'priority' => 100,
]);
```

## Future Enhancements

Planned features:
- [ ] Export/import customizer settings
- [ ] Advanced header animation presets
- [ ] Graph theme presets (color schemes)
- [ ] Typography font pairing suggestions
- [ ] Real-time CSS editor
- [ ] Mobile-specific settings
- [ ] Dark mode toggle

## Troubleshooting

**Issue:** Changes don't appear in preview
- **Solution:** Check browser console for JavaScript errors. Ensure `postMessage` transport is set for the setting.

**Issue:** Customizer styles conflict with existing CSS
- **Solution:** Check CSS specificity in `archi_customizer_css()`. Use `!important` sparingly.

**Issue:** Live preview is slow
- **Solution:** Reduce frequency of updates. Use `debounce` for intensive operations.

**Issue:** Settings not saving
- **Solution:** Check sanitization callbacks. Ensure user has proper capabilities.

## References

- [WordPress Customizer API](https://developer.wordpress.org/themes/customize-api/)
- [Customizer Controls](https://developer.wordpress.org/themes/customize-api/customizer-objects/#controls)
- [Customizer Transport](https://developer.wordpress.org/themes/customize-api/tools-for-improved-user-experience/#using-postmessage-for-improved-setting-previewing)

## Changelog

### Version 1.2.0 (2025-01-XX)
- ✅ Added complete WordPress Customizer integration
- ✅ Implemented live preview for header, typography, and color settings
- ✅ Created enhanced control panel with UX improvements
- ✅ Replaced hardcoded header values with dynamic options
- ✅ Added 6 customizer sections with 20+ settings
- ✅ Full backward compatibility maintained
