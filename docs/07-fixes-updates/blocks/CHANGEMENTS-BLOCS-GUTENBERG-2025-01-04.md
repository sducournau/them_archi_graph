# Réparation des Blocs Gutenberg - 4 janvier 2025

## 🐛 Problème identifié

Le bloc **Gestionnaire d'Article** (`archi-graph/article-manager`) ne s'affichait pas dans l'éditeur Gutenberg car :
1. Le fichier `article-manager.jsx` n'était pas inclus dans la configuration Webpack
2. Le bundle compilé n'était pas chargé par WordPress
3. Les descriptions des post types n'étaient pas assez détaillées

## ✅ Corrections appliquées

### 1. Configuration Webpack (`webpack.config.js`)

**Ajout de l'entrée pour le bloc article-manager :**
```javascript
entry: {
  "blocks-editor": "./assets/js/blocks-editor.js",
  "article-info-block": "./assets/js/article-info-block.js",
  "project-illustration-card-block": "./assets/js/project-illustration-card-block.js",
  "article-manager-block": "./assets/js/blocks/article-manager.jsx", // ✅ NOUVEAU
}
```

### 2. Chargement des scripts (`inc/gutenberg-blocks.php`)

**Ajout du chargement du bundle article-manager :**
```php
// Charger le bloc Gestionnaire d'Article si compilé
$article_manager_compiled = get_template_directory() . '/dist/js/article-manager-block.bundle.js';
if (file_exists($article_manager_compiled)) {
    wp_enqueue_script(
        'archi-article-manager-block',
        get_template_directory_uri() . '/dist/js/article-manager-block.bundle.js',
        ['wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-data', 'wp-i18n'],
        filemtime($article_manager_compiled)
    );
}
```

### 3. Amélioration des descriptions de post types (`inc/custom-post-types.php`)

#### Post type standard `post` (Articles & Blog)
```php
function archi_customize_standard_post_type() {
    global $wp_post_types;
    
    if (isset($wp_post_types['post'])) {
        $wp_post_types['post']->description = __(
            'Articles de blog, actualités et publications textuelles. Utilisez ce type pour le contenu éditorial standard, les réflexions architecturales et les actualités du studio. Ces articles peuvent être affichés dans le graphique interactif pour créer des connexions thématiques avec les projets et illustrations.',
            'archi-graph'
        );
        
        $wp_post_types['post']->labels->name = __('Articles & Blog', 'archi-graph');
        $wp_post_types['post']->labels->menu_name = __('Articles & Blog', 'archi-graph');
    }
}
add_action('init', 'archi_customize_standard_post_type', 11);
```

#### Custom post type `archi_project` (Projets Architecturaux)
**Description enrichie :**
> Projets architecturaux complets avec métadonnées détaillées (surface, coût, localisation, client, etc.). Ce type de contenu est conçu pour présenter des réalisations architecturales dans le portfolio et les intégrer dans le graphique de relations. Chaque projet peut être lié à des illustrations, articles et autres projets pour créer un réseau visuel interactif de votre travail architectural.

#### Custom post type `archi_illustration` (Illustrations)
**Description enrichie :**
> Illustrations, explorations graphiques, croquis et visualisations architecturales. Ce type de contenu permet de présenter vos créations visuelles avec des métadonnées spécifiques (technique utilisée, dimensions, logiciels, support, etc.). Les illustrations peuvent être intégrées dans le graphique de relations pour montrer les liens créatifs entre vos différents travaux artistiques, projets architecturaux et articles de réflexion.

## 📦 Fichiers générés

Après compilation avec `npm run build`, les bundles suivants sont créés :

```
dist/js/
├── admin.bundle.js
├── app.bundle.js
├── article-info-block.bundle.js
├── article-manager-block.bundle.js          ✅ NOUVEAU
├── blocks-editor.bundle.js
├── project-illustration-card-block.bundle.js
└── vendors.bundle.js
```

## 🎯 Résultat

✅ Le bloc **Gestionnaire d'Article** s'affiche maintenant correctement dans l'éditeur Gutenberg  
✅ Tous les blocs Gutenberg sont fonctionnels  
✅ Les descriptions des post types sont plus claires et explicites  
✅ Meilleure compréhension de l'usage de chaque type de contenu  

## 🔧 Pour développer à l'avenir

Si vous ajoutez un nouveau bloc Gutenberg :

1. **Créer le fichier bloc** dans `assets/js/blocks/`
2. **Ajouter l'entrée dans webpack.config.js** :
   ```javascript
   "nouveau-bloc": "./assets/js/blocks/nouveau-bloc.jsx"
   ```
3. **Charger le script dans `inc/gutenberg-blocks.php`** :
   ```php
   $nouveau_bloc_compiled = get_template_directory() . '/dist/js/nouveau-bloc.bundle.js';
   if (file_exists($nouveau_bloc_compiled)) {
       wp_enqueue_script(
           'archi-nouveau-bloc',
           get_template_directory_uri() . '/dist/js/nouveau-bloc.bundle.js',
           ['wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor'],
           filemtime($nouveau_bloc_compiled)
       );
   }
   ```
4. **Compiler avec** : `npm run build`

## 📝 Notes importantes

- **Toujours compiler après modification** des fichiers JS/JSX : `npm run build`
- Les blocs ne s'affichent que si leur bundle compilé existe dans `dist/js/`
- Utiliser les dépendances WordPress (`wp-blocks`, `wp-element`, etc.) pour éviter les conflits
- Respecter les conventions de nommage : `archi-graph/nom-du-bloc`

## 🔗 Fichiers modifiés

1. `webpack.config.js` - Configuration de compilation
2. `inc/gutenberg-blocks.php` - Enregistrement et chargement des blocs
3. `inc/custom-post-types.php` - Descriptions des post types

## ⚠️ Avertissements de compilation (non critiques)

Quelques avertissements Sass concernant `darken()` qui est déprécié. À corriger ultérieurement en remplaçant par `color.adjust()` dans `assets/css/main.scss`.

---

**Date** : 4 janvier 2025  
**Version** : 1.0.1  
**Statut** : ✅ Complété avec succès
