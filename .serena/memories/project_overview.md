# Archi-Graph WordPress Theme - Project Overview

## Purpose
WordPress theme for architectural portfolios with interactive graph visualization. Features:
- D3.js-powered force-directed graph displaying articles as nodes
- Custom post types for architectural projects and illustrations
- WPForms integration for content submission
- Gutenberg blocks for flexible layouts
- REST API for graph data and relationships

## Technology Stack
- **Backend**: PHP 7.4+ / WordPress 6.0+
- **Frontend**: React 18.3, D3.js 7.9
- **Build Tools**: Webpack 5, Babel 7, SASS
- **Integration**: WPForms (required plugin)
- **Text Domain**: `archi-graph`
- **Function Prefix**: `archi_`

## Key Features
1. **Graph Visualization** - Interactive D3.js graph with categories, tags, and content-based relationships
2. **Custom Post Types** - `archi_project` and `archi_illustration` with specialized metadata
3. **11+ Gutenberg Blocks** - React-based blocks for content management
4. **REST API** - `/wp-json/archi/v1/*` endpoints for graph data
5. **WPForms Integration** - Automatic post creation from form submissions
6. **Relationship System** - Automatic (category/tag) + manual relationship management
7. **Diagnostic Tools** - Built-in scripts for debugging metadata and API

## System Information
- **Development OS**: Windows (PowerShell)
- **Theme Version**: 1.1.0
- **License**: GPL v3
- **Bilingual**: French primary, English secondary
