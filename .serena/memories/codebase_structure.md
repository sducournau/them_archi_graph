# Codebase Structure

## Directory Organization

### Root Level
- `functions.php` - Main theme bootstrap, enqueue scripts, theme setup
- `front-page.php` - Homepage template with graph visualization
- `single.php` / `single-archi_project.php` / `single-archi_illustration.php` - Single post templates
- `header.php` / `footer.php` - Template parts
- `style.css` - Theme metadata (required by WordPress)

### `/inc/` - Core PHP Functionality
**Entry Points:**
- `custom-post-types.php` - Register `archi_project` and `archi_illustration` CPTs + taxonomies
- `meta-boxes.php` - Graph metadata boxes (show_in_graph, node_color, node_size, etc.)
- `wpforms-integration.php` - Form processing, auto-create posts from submissions
- `rest-api.php` - REST endpoints: `/wp-json/archi/v1/articles`, `/categories`, `/proximity-analysis`
- `gutenberg-blocks.php` - Block registration and server-side rendering

**Supporting Files:**
- `admin-settings.php` - Theme settings page
- `admin-enhancements.php` - Admin UI improvements
- `article-card-component.php` - Unified card rendering function
- `automatic-relationships.php` - Auto-link posts by category/tag
- `enhanced-proximity-calculator.php` - Relationship scoring algorithm
- `graph-management.php` - Graph configuration
- `relationships-dashboard.php` - Admin dashboard for relationships
- `sample-data-generator.php` - Test data creation

### `/assets/js/` - JavaScript/React
**Blocks:**
- `blocks/article-manager.jsx` - React block for article management
- `article-info-block.js` - Article metadata display block
- `project-illustration-card-block.js` - Card block for projects/illustrations

**Components:**
- `components/GraphContainer.jsx` - Main D3.js graph visualization
- `components/Node.jsx` - Individual graph node component
- `components/NodeTooltip.jsx` - Node hover tooltip
- `components/CategoryCluster.jsx` - Category grouping component

**Utilities:**
- `utils/dataFetcher.js` - API data fetching utilities
- `utils/gifController.js` - GIF animation control
- `utils/graphHelpers.js` - D3.js helper functions
- `utils/proximityCalculator.js` - Relationship calculation

**Main Files:**
- `app.js` - Frontend application entry point
- `admin.js` - Admin area scripts
- `graph-admin.js` - Graph admin UI

### `/assets/css/` - Stylesheets
- `main.scss` - Main SASS entry
- `article-card.css` - Card component styles
- `graph-white.css` - Graph visualization styles
- `blocks.css` / `blocks-editor.css` - Gutenberg block styles
- Component-specific CSS files

### `/template-parts/`
- `graph-homepage.php` - Graph visualization template partial

### `/docs/` - Documentation
- `setup.md` - Installation guide
- `features.md` - Feature reference
- `blocks.md` - Block documentation
- `api.md` - REST API reference
- `relationships-guide.md` - Relationship system guide

### `/dist/js/` (Build Output)
- `app.bundle.js` - Main frontend bundle
- `admin.bundle.js` - Admin bundle
- `blocks-editor.bundle.js` - Block editor scripts
- `vendors.bundle.js` - React/D3 vendor code

## Diagnostic Scripts (Root)
- `check-graph-meta.php` - Verify graph metadata
- `deep-diagnostic.php` - Deep database inspection
- `test-api-direct.php` - Test REST API
- `debug-api-complet.php` - Complete API debugging
- `flush-rest-api.php` / `flush-rewrite-rules.php` - Clear rewrite cache
