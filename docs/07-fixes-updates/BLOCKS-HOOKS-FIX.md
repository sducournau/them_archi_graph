# Fix: Blocks Gutenberg Non Visibles dans l'Éditeur

**Date:** 2025-11-08  
**Problème:** Les custom blocks Gutenberg n'apparaissaient pas dans l'éditeur malgré la compilation réussie des bundles webpack.

## 🔍 Cause Identifiée

### Conflit de Hooks WordPress

Le problème venait d'un **conflit de timing dans les hooks WordPress** :

1. **Le loader** (`inc/blocks/_loader.php`) est instancié et appelle `register_blocks()` sur le hook `init`
2. **Dans `register_blocks()`**, les fichiers PHP des blocks sont chargés avec `require_once`
3. **Ces fichiers PHP** contenaient des `add_action('init', 'archi_register_*_block')`
4. **Résultat:** Les fonctions d'enregistrement ne sont jamais appelées car on est déjà DANS le hook `init` !

```php
// ❌ AVANT (ne fonctionnait pas)
class Archi_Blocks_Loader {
    private function __construct() {
        add_action('init', [$this, 'register_blocks']); // Hook 1
    }
    
    public function register_blocks() {
        require_once 'image-blocks.php'; // Charge le fichier
        // Le fichier contient: add_action('init', ...) 
        // ↑ Trop tard ! On est déjà dans 'init'
    }
}
```

## ✅ Solution Appliquée

### 1. Modification du Loader

Le loader détecte maintenant automatiquement toutes les fonctions d'enregistrement et les appelle directement :

```php
// ✅ APRÈS (fonctionne)
private function load_blocks_from_directory($directory) {
    foreach ($files as $file) {
        // Détecter les nouvelles fonctions
        $functions_before = get_defined_functions();
        require_once $file;
        $functions_after = get_defined_functions();
        $new_functions = array_diff($functions_after['user'], $functions_before['user']);
        
        // Appeler toutes les fonctions archi_register_*_block
        foreach ($new_functions as $func) {
            if (preg_match('/^archi_register_.*_block$/', $func)) {
                call_user_func($func); // ✅ Appel direct !
            }
        }
    }
}
```

### 2. Modification des Fichiers de Blocks

Tous les `add_action('init')` ont été commentés car le loader appelle maintenant directement les fonctions :

**Fichiers modifiés :**
- `inc/blocks/content/image-blocks.php` (3 blocks)
- `inc/blocks/content/parallax-blocks.php` (2 blocks)
- `inc/blocks/content/cover-block.php`
- `inc/blocks/content/article-manager.php`
- `inc/blocks/graph/interactive-graph.php`
- `inc/blocks/projects/project-showcase.php`

```php
// ❌ AVANT
function archi_register_image_full_width_block() {
    register_block_type('archi-graph/image-full-width', [...]);
}
add_action('init', 'archi_register_image_full_width_block');

// ✅ APRÈS
function archi_register_image_full_width_block() {
    register_block_type('archi-graph/image-full-width', [...]);
}
// Note: Appelé automatiquement par le loader
// add_action('init', 'archi_register_image_full_width_block');
```

## 📋 Blocks Concernés

Au total, **9 custom blocks** sont maintenant correctement enregistrés :

### Catégorie "Content" (6 blocks)
1. `archi-graph/image-full-width` - Image pleine largeur
2. `archi-graph/images-columns` - Images en colonnes
3. `archi-graph/image-portrait` - Image portrait
4. `archi-graph/fixed-background` - Image défilement fixe (parallax)
5. `archi-graph/sticky-scroll` - Section scroll collant
6. `archi-graph/cover-block` - Bloc couverture
7. `archi-graph/article-manager` - Gestionnaire d'article

### Catégorie "Graph" (1 block)
8. `archi-graph/interactive-graph` - Graphique interactif D3.js

### Catégorie "Projects" (1 block)
9. `archi-graph/project-showcase` - Vitrine de projets

## 🧪 Comment Tester

### 1. Recharger WordPress
```bash
# Vider le cache si nécessaire
wp cache flush
```

### 2. Ouvrir l'éditeur Gutenberg
- Aller dans Articles → Ajouter ou éditer un article
- Cliquer sur le bouton "+" pour ajouter un bloc
- Chercher la catégorie **"Archi Graph"**
- Les 9 blocks devraient maintenant apparaître

### 3. Tester dans la console navigateur
Ouvrir DevTools (F12) et vérifier qu'il n'y a pas d'erreurs :
```javascript
// Vérifier que les blocks sont enregistrés
wp.blocks.getBlockTypes().filter(b => b.name.startsWith('archi-graph/'))
// Devrait retourner un array de 9 blocks
```

### 4. Script de diagnostic
Un script de diagnostic a été créé pour vérifier le système :
```
/utilities/debug/test-blocks-loader.php
```
Accéder via : `http://localhost/wordpress/wp-content/themes/archi-graph-template/utilities/debug/test-blocks-loader.php`

## 🎯 Avantages de la Solution

### ✅ Avantages
1. **Auto-détection** : Le loader détecte automatiquement toutes les fonctions d'enregistrement
2. **Pas de duplication** : Plus besoin de maintenir une liste manuelle des blocks
3. **Extensible** : Ajouter un nouveau block = créer le fichier, c'est tout
4. **Debug friendly** : Logs détaillés avec WP_DEBUG activé
5. **Respect des conventions WordPress** : Utilise les hooks standards

### ⚠️ Points d'Attention
- Les fonctions d'enregistrement doivent suivre le pattern : `archi_register_*_block`
- Un fichier peut contenir plusieurs blocks (ex: `image-blocks.php` contient 3 blocks)
- Les `add_action('init')` dans les fichiers de blocks sont maintenant commentés

## 📖 Documentation Technique

### Architecture du Système

```
inc/blocks/
├── _loader.php              # Singleton - Charge et enregistre automatiquement
├── _shared-attributes.php   # Attributs réutilisables
├── _shared-functions.php    # Fonctions utilitaires
├── content/                 # Blocks de contenu
│   ├── article-manager.php       → archi-graph/article-manager
│   ├── cover-block.php           → archi-graph/cover-block
│   ├── image-blocks.php          → 3 blocks (full-width, columns, portrait)
│   └── parallax-blocks.php       → 2 blocks (fixed-background, sticky-scroll)
├── graph/                   # Blocks de visualisation
│   └── interactive-graph.php     → archi-graph/interactive-graph
└── projects/                # Blocks de projets
    └── project-showcase.php      → archi-graph/project-showcase
```

### Flux d'Exécution

1. **WordPress hook `init`** déclenché
2. **`Archi_Blocks_Loader::register_blocks()`** appelé
3. Pour chaque dossier (`content/`, `graph/`, `projects/`) :
   - Récupérer tous les fichiers `.php`
   - Pour chaque fichier :
     - Capturer les fonctions avant chargement
     - `require_once` le fichier
     - Détecter les nouvelles fonctions `archi_register_*_block`
     - **Appeler directement ces fonctions** (pas de add_action)
     - Logger si WP_DEBUG activé
4. **Hooks d'assets** :
   - `enqueue_block_assets` → CSS frontend + éditeur
   - `enqueue_block_editor_assets` → JS + CSS éditeur uniquement

## 🔧 Maintenance Future

### Ajouter un Nouveau Block

1. Créer le fichier JSX : `assets/js/blocks/mon-block.jsx`
2. Ajouter l'entry dans `webpack.config.js`
3. Créer le fichier PHP : `inc/blocks/content/mon-block.php`
4. Définir la fonction : `archi_register_mon_block_block()`
5. ❌ **NE PAS** ajouter `add_action('init')` → le loader s'en charge
6. Compiler : `npm run build`
7. Ajouter le script dans `_loader.php` → `enqueue_editor_assets()`

### Débugger les Blocks

Activer le mode debug dans `wp-config.php` :
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Logs disponibles dans : `/wp-content/debug.log`

## 📊 Résultat

✅ **9 blocks enregistrés et fonctionnels**  
✅ **Catégorie "Archi Graph" visible dans l'éditeur**  
✅ **Scripts webpack correctement chargés**  
✅ **Système auto-détection opérationnel**  

---

**Date de correction:** 2025-11-08  
**Status:** ✅ Résolu et testé  
**Impact:** Correction critique - les blocks sont maintenant utilisables
