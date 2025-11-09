# Fix: Blocs Gutenberg Non Disponibles dans l'Éditeur

**Date:** 2025-11-08  
**Problème:** Les blocs personnalisés Archi Graph n'apparaissaient pas dans l'éditeur Gutenberg pour les articles et custom post types.

## Causes Identifiées

### 1. Scripts JavaScript Non Enregistrés
Les bundles webpack des blocs (`article-manager-block.bundle.js`, `image-blocks.bundle.js`, `cover-block.bundle.js`) étaient compilés mais jamais enregistrés dans WordPress.

### 2. Fonctions d'Enregistrement Non Appelées
Les blocs `article-manager`, `interactive-graph` et `project-showcase` définissaient leurs fonctions d'enregistrement mais ne les appelaient jamais avec `add_action('init')`.

### 3. Références de Scripts Incorrectes
Les blocs référençaient un script générique `'archi-blocks-editor'` au lieu de leurs bundles webpack individuels.

## Corrections Appliquées

### 1. Mise à Jour du Loader (`inc/blocks/_loader.php`)

Ajout de l'enregistrement automatique de tous les bundles webpack de blocs dans la méthode `enqueue_editor_assets()`:

```php
// Enregistrer tous les scripts de blocs individuels
$block_scripts = [
    'article-manager-block' => [...],
    'image-blocks' => [...],
    'cover-block' => [...],
    'article-info-block' => [...],
    'project-illustration-card-block' => [...]
];

foreach ($block_scripts as $handle => $dependencies) {
    // Enregistrement et chargement automatique
}
```

### 2. Ajout des Appels `add_action('init')`

**Fichiers modifiés:**
- `inc/blocks/content/article-manager.php` - Ajout de `add_action('init', 'archi_register_article_manager_block');`
- `inc/blocks/graph/interactive-graph.php` - Ajout de `add_action('init', 'archi_register_interactive_graph_block');`
- `inc/blocks/projects/project-showcase.php` - Ajout de `add_action('init', 'archi_register_project_showcase_block');`

### 3. Mise à Jour des Références de Scripts

**Blocs avec bundles webpack individuels:**
- `article-manager.php` → `'editor_script' => 'archi-article-manager-block'`
- `image-blocks.php` (3 blocs) → `'editor_script' => 'archi-image-blocks'`
- `cover-block.php` → `'editor_script' => 'archi-cover-block'`

**Blocs sans bundles individuels (gardent le script générique):**
- `interactive-graph.php` → `'editor_script' => 'archi-blocks-editor'`
- `project-showcase.php` → `'editor_script' => 'archi-blocks-editor'`

## Blocs Disponibles Après la Correction

### Catégorie "Archi Graph"

1. **Gestionnaire d'Article** (`archi-graph/article-manager`)
   - Gestion complète des métadonnées d'article
   - Compatible: `post`, `archi_project`, `archi_illustration`

2. **Image Pleine Largeur** (`archi-graph/image-full-width`)
   - Image qui s'étend sur toute la largeur
   - Modes de hauteur: normal, full-viewport, half-viewport

3. **Images en Colonnes** (`archi-graph/images-columns`)
   - Grille d'images avec 2-6 colonnes

4. **Image Portrait** (`archi-graph/image-portrait`)
   - Image verticale optimisée

5. **Bloc Couverture** (`archi-graph/cover-block`)
   - Héros avec image de fond et superposition

6. **Graphique Interactif** (`archi-graph/interactive-graph`)
   - Visualisation D3.js du réseau d'articles

7. **Vitrine de Projets** (`archi-graph/project-showcase`)
   - Grille de projets architecturaux

## Vérification du Build Webpack

Les bundles suivants sont compilés dans `/dist/js/`:
- ✅ `article-manager-block.bundle.js`
- ✅ `image-blocks.bundle.js`
- ✅ `cover-block.bundle.js`
- ✅ `article-info-block.bundle.js`
- ✅ `project-illustration-card-block.bundle.js`

## Test de Fonctionnement

### Pour tester les blocs:

1. **Vider les caches:**
   ```bash
   # WordPress
   wp cache flush
   
   # ou depuis l'admin
   # Paramètres → Performance → Vider les caches
   ```

2. **Éditer un article/projet:**
   - Aller dans Articles → Ajouter
   - Cliquer sur le bouton "+" pour ajouter un bloc
   - Chercher "Archi Graph" dans les catégories
   - Tous les blocs devraient être visibles

3. **Vérifier dans la console:**
   - Ouvrir les DevTools (F12)
   - Onglet Console
   - Chercher les messages `Archi Block script enqueued: ...`

## Notes Techniques

### Architecture des Blocs

Le système de blocs utilise une architecture modulaire:

```
inc/blocks/
├── _loader.php              # Chargement automatique
├── _shared-attributes.php   # Attributs réutilisables
├── _shared-functions.php    # Fonctions utilitaires
├── content/                 # Blocs de contenu
│   ├── article-manager.php
│   ├── image-blocks.php
│   └── cover-block.php
├── graph/                   # Blocs de visualisation
│   └── interactive-graph.php
└── projects/                # Blocs de projets
    └── project-showcase.php
```

### Webpack Configuration

Les blocs React sont compilés séparément dans `webpack.config.js`:

```javascript
entry: {
  'article-manager-block': './assets/js/blocks/article-manager.jsx',
  'image-blocks': './assets/js/blocks/image-blocks.jsx',
  'cover-block': './assets/js/blocks/cover-block.jsx',
}
```

### Dépendances WordPress

Tous les blocs utilisent les packages WordPress standards:
- `@wordpress/blocks` - API de blocs
- `@wordpress/element` - React wrapper
- `@wordpress/block-editor` - Composants éditeur
- `@wordpress/components` - UI components
- `@wordpress/data` - State management
- `@wordpress/i18n` - Traductions

## Prochaines Étapes (Optionnelles)

1. **Ajouter des tests automatisés** pour vérifier l'enregistrement des blocs
2. **Créer des patterns de blocs** pour faciliter la mise en page
3. **Ajouter des variations de blocs** pour les cas d'usage courants
4. **Optimiser le chargement** en ne chargeant que les scripts nécessaires

## Références

- [WordPress Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [register_block_type() Reference](https://developer.wordpress.org/reference/functions/register_block_type/)
- [wp_enqueue_script() Reference](https://developer.wordpress.org/reference/functions/wp_enqueue_script/)
