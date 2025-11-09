# Fixing JavaScript Errors - Build Process

## The Problem

The JavaScript files `blocks-editor.js`, `article-info-block.js`, and `project-illustration-card-block.js` contain JSX syntax that browsers cannot execute directly. This causes errors like:

```
Uncaught SyntaxError: Unexpected token '<'
```

## The Solution

These files need to be transpiled (compiled) from JSX to standard JavaScript using a build process.

## Setup Instructions

### 1. Install Dependencies

Run the following command in the theme directory:

```bash
npm install
```

This will install:

- Webpack (module bundler)
- Babel (JavaScript transpiler)
- @wordpress/scripts (WordPress build tools)

### 2. Build the JavaScript Files

After installation, run:

```bash
npm run build
```

This will:

- Transpile JSX to standard JavaScript
- Bundle the files
- Output compiled files to `dist/js/`

### 3. Development Mode (Optional)

For active development with auto-rebuild on file changes:

```bash
npm run dev
```

## What Happens After Building?

Once built, the theme will automatically:

1. Detect the compiled files in `dist/js/`
2. Load the compiled versions instead of raw JSX files
3. Your Gutenberg blocks will work without errors

## File Structure

```
assets/js/               (Source files - JSX)
├── blocks-editor.js
├── article-info-block.js
└── project-illustration-card-block.js

dist/js/                 (Compiled files - Browser-ready)
├── blocks-editor.bundle.js
├── article-info-block.bundle.js
└── project-illustration-card-block.bundle.js
```

## Troubleshooting

### "npm: command not found"

You need to install Node.js and npm:

- Download from: https://nodejs.org/
- Or use your package manager (apt, brew, etc.)

### Build fails

Check that you're in the correct directory:

```bash
cd /path/to/wordpress/wp-content/themes/archi-graph-template
```

### Blocks still not showing

1. Clear WordPress cache
2. Refresh the page (Ctrl+F5 or Cmd+Shift+R)
3. Check browser console for any remaining errors

## Quick Fix (Temporary)

If you can't run the build process right now, the theme has been updated to:

- **Not load** the problematic JSX files
- **Only load** compiled versions if they exist

This means:

- ✅ No more JavaScript errors
- ❌ But custom Gutenberg blocks won't be available until you build them

To enable the blocks, you must complete the build process above.
