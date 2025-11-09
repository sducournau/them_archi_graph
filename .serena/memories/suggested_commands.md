# Development Commands

## Build Commands (Windows PowerShell)

### NPM Scripts
```powershell
# Install dependencies
npm install

# Development build with watch mode
npm run dev

# Production build (minified)
npm run build

# Build only Gutenberg blocks
npm run build:blocks
```

### Webpack Builds
The theme uses dual webpack configurations:
1. **Main app bundle** - `app.js`, `admin.js` → `dist/js/`
2. **Gutenberg blocks** - Block editor scripts → `dist/js/`

Output files:
- `dist/js/app.bundle.js` - Frontend application
- `dist/js/admin.bundle.js` - Admin scripts
- `dist/js/blocks-editor.bundle.js` - Block editor
- `dist/js/vendors.bundle.js` - React/D3 vendors

## WordPress Development

### Flush Caches
```powershell
# From theme root, access WordPress installation:
# Navigate to WordPress root
cd C:\wamp64\www\wordpress

# Via PHP scripts in theme root
php wp-content/themes/archi-graph-template/flush-rewrite-rules.php
php wp-content/themes/archi-graph-template/flush-rest-api.php
php wp-content/themes/archi-graph-template/quick-flush.php
```

### Diagnostic Tools
```powershell
# Check graph metadata
php check-graph-meta.php

# Deep database diagnostic
php deep-diagnostic.php

# Test REST API
php test-api-direct.php

# Complete API debug
php debug-api-complet.php
```

### Sample Data Generation
Via WordPress Admin:
- Navigate to **Appearance → 🔍 Diagnostic**
- Click "Create test articles" button
- Or use `inc/sample-data-generator.php` programmatically

## Testing Commands

### Manual Testing Workflow
1. Activate theme in WP Admin
2. Run diagnostic: **Appearance → Diagnostic**
3. Create test data if needed
4. Test REST API: `http://localhost/wordpress/wp-json/archi/v1/articles`
5. Verify graph on homepage
6. Check browser console (F12) for errors

### REST API Testing
```powershell
# Using curl (if installed)
curl http://localhost/wordpress/wp-json/archi/v1/articles

# Or visit URLs directly in browser:
# http://localhost/wordpress/wp-json/archi/v1/articles
# http://localhost/wordpress/wp-json/archi/v1/categories
# http://localhost/wordpress/wp-json/archi/v1/proximity-analysis
```

### Debug Mode
Enable in `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('ARCHI_DEBUG', true);  // Theme-specific debug
```

Check logs:
```powershell
# WordPress debug log
Get-Content C:\wamp64\www\wordpress\wp-content\debug.log -Tail 50

# Or open in editor
code C:\wamp64\www\wordpress\wp-content\debug.log
```

## Code Quality (No Linting/Formatting Scripts)

The theme does NOT have automated linting or formatting. Follow these manual checks:

### Before Committing
- [ ] Check for `unified_*` or `enhanced_*` prefixes (forbidden)
- [ ] Verify all text uses `archi-graph` text domain
- [ ] Ensure nonces on all forms
- [ ] Sanitize inputs, escape outputs
- [ ] Check browser console for JS errors
- [ ] Test REST API endpoints
- [ ] Verify graph metadata is saved correctly

### Manual Code Review Checklist
```php
// ❌ AVOID
function unified_archi_something() { }
$data = $_POST['value'];  // Unsanitized
echo $value;  // Unescaped

// ✅ CORRECT
function archi_something() { }
$data = sanitize_text_field($_POST['value']);
echo esc_html($value);
```

## Git Commands (Windows PowerShell)

### Basic Workflow
```powershell
# Check status
git status

# Stage changes
git add .
git add inc/custom-post-types.php

# Commit
git commit -m "Add new feature"

# View history
git log --oneline -10

# Create branch
git checkout -b feature/new-feature

# Switch branches
git checkout main
```

### Using GitKraken MCP (Serena)
Use Serena MCP tools:
- `mcp_gitkraken_git_status` - Check repo status
- `mcp_gitkraken_git_add_or_commit` - Stage and commit
- `mcp_gitkraken_git_branch` - Branch operations
- `mcp_gitkraken_git_push` - Push to remote

## Windows System Utilities

### PowerShell Commands
```powershell
# List directory
Get-ChildItem
ls  # Alias

# Change directory
cd C:\wamp64\www\wordpress\wp-content\themes\archi-graph-template

# Search files
Get-ChildItem -Recurse -Filter "*.php"

# Search content (like grep)
Select-String -Path "*.php" -Pattern "archi_"

# Find in files
Get-ChildItem -Recurse | Select-String "function_name"

# View file content
Get-Content functions.php
cat functions.php  # Alias

# Tail logs
Get-Content debug.log -Tail 50 -Wait

# Copy files
Copy-Item source.php destination.php

# Remove files
Remove-Item file.php
```

### File Operations
```powershell
# Create directory
New-Item -ItemType Directory -Path "new-folder"

# Create file
New-Item -ItemType File -Path "new-file.php"

# Open in VS Code
code functions.php
code .  # Open current folder
```

## Running the Project

### Local Development Setup (WAMP)
1. **Start WAMP** - Launch WAMP server
2. **Activate theme** - WordPress Admin → Appearance → Themes → Archi Graph
3. **Check dependencies** - Ensure WPForms plugin is active
4. **Run diagnostic** - Appearance → 🔍 Diagnostic
5. **Watch changes** - `npm run dev` in PowerShell
6. **View site** - `http://localhost/wordpress/`

### Entry Points
- **Homepage**: `front-page.php` (graph visualization)
- **Projects**: `single-archi_project.php`
- **Illustrations**: `single-archi_illustration.php`
- **Admin**: WordPress Admin → Posts / Projects / Illustrations

### No Automated Tests
The theme does not have PHPUnit or Jest tests. Testing is manual:
1. Use diagnostic tools (Appearance → Diagnostic)
2. Check REST API responses
3. Verify graph renders correctly
4. Test WPForms submissions
5. Check browser console for errors

## Task Completion Checklist

When finishing a task:
- [ ] Run `npm run build` for production
- [ ] Test changes in browser
- [ ] Check browser console (F12)
- [ ] Verify REST API if backend changed
- [ ] Clear WordPress cache (flush scripts)
- [ ] Check for forbidden prefixes
- [ ] Use Serena MCP to verify no duplicates
- [ ] Commit changes with descriptive message
