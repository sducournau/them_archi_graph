# Graph Customizer Options Implementation

**Date:** 2025
**Version:** 1.0.9

## Overview

This document describes the implementation of two new WordPress Customizer options for controlling the graph display behavior:

1. **Popup: Title Only** - Display only the article title in hover popups (without excerpt)
2. **Show Comments** - Display article comments in the sidebar info panel

## Implementation Details

### 1. Customizer Settings (inc/customizer.php)

Added two new settings in the "Graph Options" section:

#### Setting 1: Title-Only Popup
```php
$wp_customize->add_setting('archi_graph_popup_title_only', [
    'default' => false,
    'transport' => 'refresh',
    'sanitize_callback' => 'archi_sanitize_checkbox'
]);

$wp_customize->add_control('archi_graph_popup_title_only', [
    'label' => __('Popup : titre uniquement', 'archi-graph'),
    'description' => __('Afficher seulement le titre dans la popup de survol (sans l\'extrait).', 'archi-graph'),
    'section' => 'archi_graph_options',
    'type' => 'checkbox'
]);
```

#### Setting 2: Show Comments
```php
$wp_customize->add_setting('archi_graph_show_comments', [
    'default' => true,
    'transport' => 'refresh',
    'sanitize_callback' => 'archi_sanitize_checkbox'
]);

$wp_customize->add_control('archi_graph_show_comments', [
    'label' => __('Afficher les commentaires', 'archi-graph'),
    'description' => __('Afficher les commentaires de l\'article dans la sidebar d\'information.', 'archi-graph'),
    'section' => 'archi_graph_options',
    'type' => 'checkbox'
]);
```

### 2. JavaScript Configuration (functions.php)

Exposed the Customizer options to JavaScript via `wp_localize_script`:

```php
wp_localize_script('archi-app', 'archiGraphConfig', [
    'popupTitleOnly' => get_theme_mod('archi_graph_popup_title_only', false),
    'showComments' => get_theme_mod('archi_graph_show_comments', true),
]);
```

### 3. REST API Enhancement (inc/rest-api.php)

Extended the `/wp-json/archi/v1/articles` endpoint to include recent comments for each article:

```php
$recent_comments = get_comments([
    'post_id' => $post->ID,
    'status' => 'approve',
    'number' => 5,
    'orderby' => 'comment_date',
    'order' => 'DESC'
]);

foreach ($recent_comments as $comment) {
    $comments_list[] = [
        'author' => $comment->comment_author,
        'date' => $comment->comment_date,
        'content' => wp_trim_words($comment->comment_content, 30, '...')
    ];
}

$article['comments'] = [
    'show_as_node' => get_post_meta($post->ID, '_archi_show_comments_node', true) === '1',
    'count' => get_comments_number($post->ID),
    'node_color' => get_post_meta($post->ID, '_archi_comment_node_color', true) ?: '#16a085',
    'recent' => $comments_list
];
```

### 4. Popup Display Logic (assets/js/utils/nodeInteractions.js)

Modified the `showNodeTooltip()` function to check the `popupTitleOnly` setting:

```javascript
// Check if we should show only the title (from Customizer option)
const showTitleOnly = window.archiGraphConfig?.popupTitleOnly || false;

if (!showTitleOnly) {
    if (node.excerpt) {
        description = node.excerpt;
    } else if (node.content) {
        // Extract excerpt from content...
    }
}
```

### 5. Sidebar Comments Display (assets/js/utils/sidebarUtils.js)

Enhanced `showInfoPanel()` to render comments when enabled:

```javascript
const commentsContainer = document.getElementById("panel-comments");
if (commentsContainer && window.archiGraphConfig?.showComments && nodeData.comments) {
    if (nodeData.comments.count > 0) {
        const commentsSection = document.createElement("div");
        commentsSection.className = "comments-section";
        
        const commentsTitle = document.createElement("h4");
        commentsTitle.textContent = `Commentaires (${nodeData.comments.count})`;
        
        // Render recent comments...
        nodeData.comments.recent.forEach((comment) => {
            // Build comment HTML...
        });
    }
}
```

### 6. HTML Templates

Added comments container to both `front-page.php` and `page-home.php`:

```html
<div id="panel-comments" class="panel-comments"></div>
```

### 7. CSS Styling (assets/css/home-improvements.css)

Added styles for the comments section:

```css
.panel-comments {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

.comments-section h4 {
    font-size: 1rem;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.comments-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.comment-item {
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 0.75rem;
}
```

## User Experience

### Popup Title-Only Mode

**Default (OFF):**
- Popup shows: Title + Excerpt
- Rich information on hover

**Enabled (ON):**
- Popup shows: Title only
- Cleaner, minimalist view
- Better for dense graphs with many nodes

### Comments Display

**Default (ON):**
- Sidebar shows up to 5 recent comments
- Displays: author name, date, comment excerpt
- Helps users see community engagement

**Disabled (OFF):**
- No comments section in sidebar
- Cleaner view focused on article metadata

## Technical Notes

1. **Transport Setting:** Both options use `'transport' => 'refresh'` because they require JavaScript re-initialization
2. **Default Values:** `popupTitleOnly = false`, `showComments = true`
3. **Comments Limit:** API returns max 5 recent approved comments per article
4. **Performance:** Comments are fetched once during page load via REST API
5. **TypeScript Warnings:** Type errors in JSDoc are informational only and don't affect functionality

## Testing

To test these features:

1. Navigate to **Appearance → Customize → Graph Options**
2. Toggle "Popup : titre uniquement" and refresh to see popup changes
3. Toggle "Afficher les commentaires" and refresh to see sidebar changes
4. Click on graph nodes to verify sidebar displays correctly

## Files Modified

- `inc/customizer.php` - Added Customizer settings
- `functions.php` - Version bump + JavaScript config
- `inc/rest-api.php` - Added comments data to API
- `assets/js/utils/nodeInteractions.js` - Popup title-only logic
- `assets/js/utils/sidebarUtils.js` - Comments rendering
- `front-page.php` - Added comments container
- `page-home.php` - Added comments container
- `assets/css/home-improvements.css` - Comments styling

## Version History

- **1.0.9** - Initial implementation of graph customizer options
