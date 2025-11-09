# Task Completion Guidelines

## Pre-Commit Checklist

### Code Quality Checks
- [ ] **No forbidden prefixes** - Verify no `unified_*`, `enhanced_*`, `new_*`, `improved_*` in new code
- [ ] **Security verified** - All inputs sanitized, all outputs escaped, nonces present
- [ ] **Translation ready** - All strings use `__()`, `_e()`, etc. with `archi-graph` text domain
- [ ] **Naming consistent** - PHP uses `archi_snake_case()`, CSS uses `.archi-kebab-case`
- [ ] **No duplicates** - Used Serena MCP to check for similar existing functionality

### Testing Requirements
- [ ] **Browser console clear** - No JavaScript errors (F12 console)
- [ ] **REST API tested** - Endpoints return expected data
- [ ] **Graph renders** - Visualization displays correctly on frontend
- [ ] **Admin UI works** - Meta boxes save correctly, no PHP warnings
- [ ] **Mobile responsive** - Test on mobile viewport if UI changes made

### Build Steps
```powershell
# Production build
npm run build

# Verify build output
ls dist/js/
# Should see: app.bundle.js, admin.bundle.js, blocks-editor.bundle.js, vendors.bundle.js
```

### Cache Clearing
```powershell
# Flush WordPress caches (run from theme root)
php flush-rewrite-rules.php
php flush-rest-api.php

# Or via WordPress Admin:
# Settings → Permalinks → Save Settings
```

## Post-Task Verification

### Backend Changes (PHP)
1. **Check WordPress debug log**
   ```powershell
   Get-Content C:\wamp64\www\wordpress\wp-content\debug.log -Tail 50
   ```

2. **Test REST API**
   - Visit: `http://localhost/wordpress/wp-json/archi/v1/articles`
   - Verify JSON structure
   - Check for new fields if added

3. **Verify metadata**
   ```powershell
   php check-graph-meta.php
   ```

4. **Run diagnostic**
   - WordPress Admin → Appearance → 🔍 Diagnostic
   - Check "System Status" section

### Frontend Changes (JS/CSS)
1. **Hard refresh browser** - Ctrl+Shift+R (clear cache)
2. **Check console** - F12 → Console tab, verify no errors
3. **Test interactions** - Click, drag, zoom if graph changed
4. **Responsive check** - F12 → Toggle device toolbar
5. **Performance** - F12 → Network tab, check bundle sizes

### Gutenberg Block Changes
1. **Clear browser cache** - Hard refresh (Ctrl+Shift+R)
2. **Test in block editor**
   - Create new post
   - Add block, verify it appears in inserter
   - Test block controls and preview
3. **Test block rendering**
   - Publish post
   - View on frontend
   - Verify server-side rendering matches editor

### Database Changes
1. **Backup first** - Export database via phpMyAdmin
2. **Test rollback** - Verify `archi_theme_deactivation()` cleans up
3. **Check for orphaned meta** - Run `deep-diagnostic.php`

## Documentation Updates

### When to Update `.serena/config.yaml`
- New metadata fields added → Update `metadata_schema` section
- New file patterns → Update `structure.key_directories`
- New architectural patterns → Update `architecture` section
- New common tasks → Update `common_tasks` section

### When to Update `copilot-instructions.md`
- New code patterns → Add to appropriate section
- New anti-patterns discovered → Add to "Anti-Patterns to Avoid"
- New workflow steps → Update "Serena MCP Integration Workflow"

### When to Update `docs/` folder
- New features → Update `docs/features.md`
- API changes → Update `docs/api.md`
- New blocks → Update `docs/blocks.md`
- Breaking changes → Update `docs/changelog.md`

## Git Workflow

### Commit Message Format
```
[Component] Brief description

- Detailed change 1
- Detailed change 2

Refs: #issue-number (if applicable)
```

Examples:
```
[Meta Boxes] Add graph position field

- Added _archi_graph_position metadata field
- Updated save handler in meta-boxes.php
- Added REST API field in rest-api.php

[Blocks] Consolidate card rendering

- Removed archi_unified_render_card()
- Updated all blocks to use archi_render_article_card()
- Cleaned up CSS class names

[Refactor] Remove enhanced_ prefixes

- Renamed archi_enhanced_query() → archi_query_posts()
- Updated all references across 8 files
- No functional changes
```

### Before Pushing
```powershell
# Check what changed
git status
git diff

# Review changes using Serena MCP
# Use: mcp_gitkraken_git_status

# Stage changes
git add .

# Commit with descriptive message
git commit -m "[Component] Description"

# Push to remote
git push origin branch-name
```

## Serena MCP Integration

### Before ANY Code Changes
1. **Use Serena MCP to analyze**
   ```
   Use mcp_oraios_serena_find_symbol to locate existing function
   Use mcp_oraios_serena_search_for_pattern to find similar code
   Use mcp_oraios_serena_get_symbols_overview to understand file
   ```

2. **Check for duplicates**
   - Query for similar function names
   - Review `.serena/config.yaml` patterns
   - Check consolidation targets

3. **Plan refactoring if needed**
   - If duplicate found → Merge instead of create new
   - If using forbidden prefix → Plan rename
   - If creating utility → Check `inc/` and `assets/js/utils/`

### After Changes
1. **Use GitKraken MCP for git**
   ```
   mcp_gitkraken_git_status - Review changes
   mcp_gitkraken_git_add_or_commit - Stage and commit
   mcp_gitkraken_git_push - Push to remote
   ```

2. **Verify with Serena**
   - Check no forbidden prefixes introduced
   - Verify patterns match config
   - Update memories if new patterns added

## Performance Verification

### For Backend Changes
- [ ] Check query count - Use Query Monitor plugin if available
- [ ] Verify transients used for expensive queries
- [ ] Test with 100+ posts if graph related

### For Frontend Changes
- [ ] Check bundle size - `ls -lh dist/js/`
- [ ] Verify lazy loading - Images and components
- [ ] Test graph with 50+ nodes - Performance acceptable?

### For Database Changes
- [ ] Index new meta keys if queried frequently
- [ ] Test with large dataset (use sample data generator)
- [ ] Check for N+1 queries

## Final Steps

1. **Build for production**: `npm run build`
2. **Clear all caches**: Run flush scripts
3. **Test in browser**: Fresh page load, hard refresh
4. **Check console**: No errors or warnings
5. **Verify functionality**: Feature works as expected
6. **Update documentation**: If public-facing change
7. **Commit changes**: Descriptive message with Serena MCP
8. **Push to remote**: After local verification

## Rollback Procedure

If something breaks:
1. **Git revert**
   ```powershell
   git log --oneline -5  # Find commit hash
   git revert <commit-hash>
   ```

2. **Rebuild**
   ```powershell
   npm run build
   ```

3. **Clear caches**
   ```powershell
   php flush-rewrite-rules.php
   ```

4. **Test again** - Verify issue resolved
