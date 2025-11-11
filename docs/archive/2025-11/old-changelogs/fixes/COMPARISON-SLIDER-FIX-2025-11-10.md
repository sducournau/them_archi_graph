# Fix: Comparison Slider 404 & JavaScript Error

**Date:** 10 novembre 2025  
**Type:** Bug Fix  
**Severity:** Critical (Feature Broken)

## 🐛 Problèmes Identifiés

### Erreur 1: 404 Not Found
```
GET http://localhost/wordpress/wp-content/themes/archi-graph-template/dist/comparison-slider.bundle.js?ver=1.0.5
net::ERR_ABORTED 404 (Not Found)
```

**Cause:** Chemin incorrect dans `functions.php` ligne 225
- **Chemin incorrect:** `/dist/comparison-slider.bundle.js`
- **Chemin correct:** `/dist/js/comparison-slider.bundle.js`

### Erreur 2: TypeError JavaScript
```
Uncaught TypeError: Cannot read properties of undefined (reading 'initialized')
    at initComparisonSlider (comparison-slider.js:8:41)
```

**Cause:** Fonction `archiInitComparisonSlider()` appelée avec un string au lieu d'un élément DOM
- La fonction attendait un élément DOM: `container.dataset.initialized`
- Elle recevait un string (ID du bloc): `'archi-image-block-xxx'`

### Erreur 3: Conteneur sans ID
Le conteneur `.comparison-container` n'avait pas d'attribut `id`, empêchant `getElementById()` de fonctionner.

## ✅ Solutions Appliquées

### 1. Correction du chemin webpack dans `functions.php`

**Fichier:** `functions.php` ligne 225

**Avant:**
```php
wp_register_script(
    'archi-image-comparison-slider',
    ARCHI_THEME_URI . '/dist/comparison-slider.bundle.js',
    [],
    ARCHI_THEME_VERSION,
    true
);

wp_enqueue_script(
    'archi-comparison-slider',
    ARCHI_THEME_URI . '/assets/js/comparison-slider.js',
    [],
    ARCHI_THEME_VERSION,
    true
);
```

**Après:**
```php
wp_register_script(
    'archi-image-comparison-slider',
    ARCHI_THEME_URI . '/dist/js/comparison-slider.bundle.js', // ✅ Ajout de /js/
    [],
    ARCHI_THEME_VERSION,
    true
);

wp_enqueue_script(
    'archi-comparison-slider',
    ARCHI_THEME_URI . '/assets/js/comparison-slider.js',
    ['archi-image-comparison-slider'], // ✅ Ajout de la dépendance
    ARCHI_THEME_VERSION,
    true
);
```

**Changements:**
- ✅ Chemin corrigé: `/dist/js/comparison-slider.bundle.js`
- ✅ Dépendance ajoutée: `archi-comparison-slider` dépend de `archi-image-comparison-slider`

### 2. Ajout de l'ID au conteneur

**Fichier:** `inc/blocks/content/image.php` ligne 615

**Avant:**
```php
<div 
    class="comparison-container" 
    data-orientation="<?php echo esc_attr($comparison_orientation); ?>"
    data-initial-position="<?php echo esc_attr($comparison_initial_position); ?>"
    data-handle-color="<?php echo esc_attr($comparison_handle_color); ?>"
>
```

**Après:**
```php
<div 
    id="<?php echo esc_attr($block_id); ?>"
    class="comparison-container" 
    data-orientation="<?php echo esc_attr($comparison_orientation); ?>"
    data-initial-position="<?php echo esc_attr($comparison_initial_position); ?>"
    data-handle-color="<?php echo esc_attr($comparison_handle_color); ?>"
>
```

### 3. Correction de l'appel JavaScript

**Fichier:** `inc/blocks/content/image.php` ligne 746-758

**Avant:**
```javascript
<script>
(function() {
    if (typeof window.archiInitComparisonSlider === 'function') {
        window.archiInitComparisonSlider('<?php echo esc_js($block_id); ?>');
    } else {
        // Attendre que le script soit chargé
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.archiInitComparisonSlider === 'function') {
                window.archiInitComparisonSlider('<?php echo esc_js($block_id); ?>');
            }
        });
    }
})();
</script>
```

**Après:**
```javascript
<script>
(function() {
    function initSlider() {
        const container = document.getElementById('<?php echo esc_js($block_id); ?>');
        if (container && typeof window.archiInitComparisonSlider === 'function') {
            window.archiInitComparisonSlider(container);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSlider);
    } else {
        initSlider();
    }
})();
</script>
```

**Changements:**
- ✅ Récupération de l'élément DOM avec `getElementById()`
- ✅ Passage de l'élément DOM au lieu du string
- ✅ Vérification de l'existence du conteneur
- ✅ Gestion correcte du timing (DOMContentLoaded)

## 🔍 Analyse Technique

### Architecture du Comparison Slider

```
┌─────────────────────────────────────────┐
│ functions.php                           │
│ ├─ Register script (bundle webpack)     │
│ └─ Enqueue script (vanilla JS)          │
└─────────────────────────────────────────┘
           │
           ▼
┌─────────────────────────────────────────┐
│ inc/blocks/content/image.php            │
│ ├─ Render HTML with ID                  │
│ └─ Inline <script> initialization       │
└─────────────────────────────────────────┘
           │
           ▼
┌─────────────────────────────────────────┐
│ assets/js/comparison-slider.js          │
│ ├─ initComparisonSlider(container)      │
│ ├─ Event listeners (drag/touch)         │
│ └─ updatePosition() logic               │
└─────────────────────────────────────────┘
```

### Webpack Output Structure

```
dist/
└── js/
    ├── comparison-slider.bundle.js  ← Fichier généré par webpack
    ├── image-block.bundle.js
    ├── hero-cover.bundle.js
    └── ...
```

### Ordre de Chargement

1. **Bundle webpack** (`archi-image-comparison-slider`) - Chargé en premier
2. **Script vanilla** (`archi-comparison-slider`) - Dépend du bundle
3. **Inline script** - Initialisation du conteneur spécifique

## 🧪 Tests à Effectuer

### Test 1: Vérifier le chargement des fichiers
- [ ] Ouvrir DevTools > Network
- [ ] Vérifier que `/dist/js/comparison-slider.bundle.js` charge avec 200 OK
- [ ] Vérifier que `/assets/js/comparison-slider.js` charge avec 200 OK

### Test 2: Vérifier l'initialisation JavaScript
- [ ] Ouvrir DevTools > Console
- [ ] Vérifier qu'il n'y a pas d'erreur `Cannot read properties of undefined`
- [ ] Taper `window.archiInitComparisonSlider` - doit retourner une fonction

### Test 3: Tester le slider
- [ ] Créer un bloc image en mode "comparison"
- [ ] Ajouter deux images (avant/après)
- [ ] Sauvegarder et visualiser en frontend
- [ ] Vérifier que le slider fonctionne (drag avec souris)
- [ ] Vérifier que le slider fonctionne (touch sur mobile)

### Test 4: Tester les orientations
- [ ] Slider vertical (par défaut)
- [ ] Slider horizontal
- [ ] Position initiale personnalisée (25%, 50%, 75%)

### Test 5: Tester les attributs data-*
```javascript
const container = document.querySelector('.comparison-container');
console.log(container.dataset.orientation);      // "vertical" ou "horizontal"
console.log(container.dataset.initialPosition);  // "50" (nombre)
console.log(container.dataset.handleColor);      // "#ffffff" (couleur)
console.log(container.dataset.initialized);      // "true" après init
```

## 📝 Notes

### Pourquoi deux scripts ?
- **Bundle webpack** (`comparison-slider.bundle.js`): 
  - Généré par webpack
  - Peut inclure des dépendances npm
  - Minifié et optimisé
  
- **Vanilla JS** (`comparison-slider.js`):
  - Code source original
  - Plus facile à déboguer
  - Chargé en complément

**Note:** Il semble y avoir une duplication ici. Le bundle webpack devrait suffire. À nettoyer plus tard.

### Amélioration Future
Envisager de **supprimer le double enqueue** et ne garder que le bundle webpack:

```php
// À simplifier:
wp_enqueue_script(
    'archi-image-comparison-slider',
    ARCHI_THEME_URI . '/dist/js/comparison-slider.bundle.js',
    [],
    ARCHI_THEME_VERSION,
    true
);
// Supprimer l'enqueue de assets/js/comparison-slider.js
```

## ✅ Statut

- [x] Erreur 404 corrigée
- [x] TypeError JavaScript corrigé
- [x] ID ajouté au conteneur
- [x] Dépendances scripts configurées
- [ ] Tests manuels à effectuer
- [ ] Nettoyage du double enqueue (optionnel)

## 📚 Fichiers Modifiés

1. **`functions.php`** (ligne 225-237)
   - Chemin webpack corrigé
   - Dépendance ajoutée

2. **`inc/blocks/content/image.php`** (ligne 615 & 746-758)
   - ID ajouté au conteneur
   - Script d'initialisation corrigé

## 🎯 Impact

**Avant:** Bloc comparison slider complètement cassé (404 + JS errors)  
**Après:** Bloc comparison slider fonctionnel avec gestion correcte des événements

**Priorité:** 🔴 **Critique** - Feature majeure réparée
