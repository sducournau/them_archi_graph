# Contributing to Archi-Graph Theme

Thank you for your interest in contributing to the Archi-Graph WordPress theme! This document provides guidelines and instructions for contributors.

**📚 Essential Reading:**
- [Copilot Instructions](/.github/copilot-instructions.md) - Complete coding guidelines
- [Code Style Conventions](/.serena/memories/code_style_conventions.md) - Naming and patterns
- [Recent Cleanup Work](/docs/changelogs/2025-11-09-cleanup-harmonization.md) - What's been harmonized
- [Codebase Audit](/docs/06-changelogs/consolidation/CODEBASE-AUDIT-2025.md) - Known issues and roadmap

## Table of Contents
1. [Code Standards](#code-standards)
2. [Development Setup](#development-setup)
3. [Project Structure](#project-structure)
4. [Git Workflow](#git-workflow)
5. [Testing](#testing)
6. [Pull Request Process](#pull-request-process)

## Code Standards

### Before Writing Code

**🚨 MANDATORY: Use Serena MCP for code exploration**

Before creating any new code, you MUST:
1. **Search for existing functionality:** `mcp_oraios_serena_find_symbol`
2. **Check for patterns:** `mcp_oraios_serena_search_for_pattern`
3. **Review architecture:** Check `.serena/config.yaml` for conventions
4. **Consult memories:** Review `.serena/memories/` for project patterns

**Never duplicate existing functionality!**

### PHP
- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- All functions must be prefixed with `archi_`
- All metadata keys must start with `_archi_`
- Always use text domain `archi-graph` for translations
- Security first: sanitize inputs, escape outputs, verify nonces
- Use type hints where appropriate (PHP 7.0+)

Example:
```php
<?php
/**
 * Process form submission
 * 
 * @param array $fields Form fields
 * @param int   $entry_id Entry ID
 * @return int|false Post ID or false on failure
 */
function archi_process_submission($fields, $entry_id) {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'archi_action')) {
        return false;
    }
    
    // Sanitize input
    $title = sanitize_text_field($fields['title'] ?? '');
    
    // Process...
    
    return $post_id;
}
```

### JavaScript/React
- Use ES6+ syntax
- Use functional components with hooks for React
- Follow React best practices
- Use WordPress data store (@wordpress/data) for state management
- Camel case for functions and variables
- Use `archi` prefix for global functions

Example:
```jsx
import { registerBlockType } from '@wordpress/blocks';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

registerBlockType('archi-graph/my-block', {
    title: __('My Block', 'archi-graph'),
    
    edit: ({ attributes, setAttributes }) => {
        const postData = useSelect((select) => {
            return select('core/editor').getCurrentPost();
        }, []);
        
        return <div>Block content</div>;
    },
    
    save: () => null
});
```

### CSS
- Use BEM methodology with `archi-` prefix
- Mobile-first responsive design
- Use CSS variables for theming
- Organize by component

Example:
```css
.archi-block {
    padding: 20px;
}

.archi-block__element {
    margin: 10px 0;
}

.archi-block--modifier {
    background: var(--archi-primary-color);
}
```

**⚠️ CSS Consolidation Note:**
As of January 2025, CSS files have been consolidated:
- Use `blocks-editor.css` (not `blocks-editor-enhanced.css`)
- Use `parallax-image.css` (not `parallax-image-enhanced.css`)
- Use `image-comparison-slider.css` (not `image-comparison-enhanced.css`)

When adding styles, extend existing files with modifiers, don't create duplicates.

## Development Setup

### Prerequisites
- WordPress 6.0+
- PHP 7.4+
- Node.js 14+
- npm or yarn
- WPForms plugin

### Installation

1. Clone the repository into your WordPress themes directory:
```bash
cd wp-content/themes/
git clone [repository-url] archi-graph-template
cd archi-graph-template
```

2. Install dependencies:
```bash
npm install
```

3. Build assets:
```bash
npm run build
```

4. For development with hot reload:
```bash
npm run start
```

### Environment Setup

1. Activate the theme in WordPress admin
2. Install and activate WPForms plugin
3. The theme will auto-create necessary forms on activation
4. Import sample data (optional) via Tools > Import

## Project Structure

```
archi-graph-template/
├── .copilot/                 # Copilot instructions
│   └── rules.md
├── .github/                  # GitHub specific files
│   └── copilot-instructions.md
├── .serena/                  # Serena MCP config
│   └── config.yaml
├── assets/                   # Frontend assets
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript
│   │   ├── blocks/          # Gutenberg blocks (React)
│   │   ├── components/      # React components
│   │   └── utils/           # Utility functions
│   └── images/              # Image assets
├── inc/                      # PHP includes
│   ├── custom-post-types.php
│   ├── wpforms-integration.php
│   ├── meta-boxes.php
│   ├── rest-api.php
│   ├── gutenberg-blocks.php
│   └── ...
├── template-parts/           # Reusable templates
├── docs/                     # Documentation
├── functions.php             # Theme bootstrap
├── style.css                 # Theme header
└── ...
```

## Git Workflow

### Branch Naming
- Feature: `feature/description`
- Bug fix: `fix/description`
- Enhancement: `enhance/description`
- Documentation: `docs/description`

Examples:
- `feature/add-gallery-block`
- `fix/graph-node-overlap`
- `enhance/form-validation`
- `docs/update-api-documentation`

### Commit Messages
Use conventional commits format:

```
type(scope): brief description

Detailed description if needed

Fixes #issue_number
```

Types:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting)
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Maintenance tasks

Examples:
```
feat(blocks): add project gallery block

Added a new Gutenberg block for displaying project galleries
with lightbox functionality.

Fixes #123
```

```
fix(graph): prevent node overlap on mobile

Updated force simulation parameters to improve node
positioning on smaller screens.

Fixes #456
```

## Testing

### Manual Testing

Before submitting a pull request, test:

1. **Custom Post Types**
   - Create, edit, delete projects and illustrations
   - Verify metadata saves correctly
   - Check taxonomies assignment

2. **WPForms Integration**
   - Submit each form type
   - Verify posts are created with correct metadata
   - Check file uploads work

3. **Graph Visualization**
   - Load graph on frontend
   - Verify all nodes appear
   - Test interactions (click, hover, drag)
   - Check relationships display correctly

4. **Gutenberg Blocks**
   - Add blocks in editor
   - Configure all settings
   - Preview on frontend
   - Test on different post types

5. **Responsive Design**
   - Test on mobile (320px+)
   - Test on tablet (768px+)
   - Test on desktop (1024px+)

### Diagnostic Scripts

Use the included diagnostic scripts:

```bash
# Check graph metadata
php check-graph-meta.php

# Deep diagnostic
php deep-diagnostic.php

# Test API
php test-api-direct.php
```

### Browser Testing
Test in:
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Pull Request Process

### Before Submitting

1. **Update your branch**
   ```bash
   git checkout main
   git pull origin main
   git checkout your-branch
   git rebase main
   ```

2. **Run tests**
   - Test all affected functionality
   - Check for PHP/JS errors
   - Verify responsive design

3. **Review your changes**
   ```bash
   git diff main
   ```

4. **Update documentation**
   - Update README if needed
   - Add/update code comments
   - Update CHANGELOG.md

### Submitting

1. **Push your branch**
   ```bash
   git push origin your-branch
   ```

2. **Create Pull Request**
   - Use descriptive title
   - Reference related issues
   - Describe changes in detail
   - Add screenshots/videos if relevant

3. **PR Template**
   ```markdown
   ## Description
   Brief description of changes
   
   ## Type of Change
   - [ ] Bug fix
   - [ ] New feature
   - [ ] Enhancement
   - [ ] Documentation
   
   ## Related Issues
   Fixes #123
   
   ## Changes Made
   - Change 1
   - Change 2
   
   ## Testing
   - [ ] Manual testing completed
   - [ ] Browser compatibility checked
   - [ ] Responsive design verified
   
   ## Screenshots
   (if applicable)
   ```

### Review Process

1. Code will be reviewed for:
   - Code quality and standards
   - Security concerns
   - Performance impact
   - Compatibility
   - Documentation

2. Address review comments:
   ```bash
   # Make changes
   git add .
   git commit -m "fix: address review comments"
   git push origin your-branch
   ```

3. Once approved, the PR will be merged

## Adding New Features

### Custom Post Type
1. Register in `inc/custom-post-types.php`
2. Add taxonomies if needed
3. Create meta boxes in `inc/meta-boxes.php`
4. Update REST API in `inc/rest-api.php`
5. Add frontend templates
6. Update documentation

### Gutenberg Block
1. Create React component in `assets/js/blocks/`
2. Register server-side in `inc/gutenberg-blocks.php`
3. Add styles in `assets/css/`
4. Enqueue in `functions.php`
5. Add to block category
6. Document usage

### WPForms Integration
1. Create form structure in `inc/wpforms-integration.php`
2. Add processing function
3. Hook to `wpforms_process_complete`
4. Map fields to metadata
5. Handle file uploads
6. Test thoroughly

## Code Review Checklist

- [ ] Code follows WordPress coding standards
- [ ] All functions have proper prefixes
- [ ] Security: inputs sanitized, outputs escaped
- [ ] Nonces verified for forms
- [ ] User capabilities checked
- [ ] Text domain used consistently
- [ ] No PHP/JS errors in console
- [ ] Responsive design works
- [ ] Browser compatibility verified
- [ ] Documentation updated
- [ ] Commit messages are clear

## Questions?

If you have questions about contributing:
1. Check existing documentation
2. Search closed issues for similar questions
3. Open a new issue with `question` label
4. Join our community discussions

## Code of Conduct

- Be respectful and constructive
- Follow WordPress community guidelines
- Help others learn and grow
- Report security issues privately

## License

By contributing, you agree that your contributions will be licensed under the same license as the project.

---

Thank you for contributing to Archi-Graph Theme! 🎉
