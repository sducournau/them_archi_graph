# Image Comparison Slider - Modes de Hauteur

## 🎯 Objectif

Ajout des options de hauteur au bloc **Image Comparison Slider** (Avant/Après) pour offrir les mêmes possibilités que le bloc Parallax Image unifié.

## ✅ Fonctionnalités Ajoutées

### 1. Modes de Hauteur

Le bloc supporte maintenant 3 modes de hauteur :

#### **Mode Automatique** (`auto`)
- **Par défaut**
- Utilise les ratios d'aspect (16:9, 4:3, 1:1, 3:4, original)
- Hauteur proportionnelle à la largeur
- Idéal pour les images intégrées dans le contenu

```php
heightMode: 'auto'
aspectRatio: '16-9' // ou '4-3', '1-1', '3-4', 'original'
```

#### **Pleine Hauteur d'Écran** (`full-viewport`)
- Occupe 100% de la hauteur visible (100vh)
- Parfait pour les sections hero ou immersives
- Adaptatif sur mobile avec `100svh`
- Supprime les marges et bordures arrondies

```php
heightMode: 'full-viewport'
// L'aspect ratio est ignoré
```

#### **Hauteur Personnalisée** (`custom`)
- Hauteur fixe en pixels
- Configurable de 300px à 1200px (par pas de 50px)
- Contrôle précis de la taille
- Valeur par défaut : 600px

```php
heightMode: 'custom'
customHeight: 800 // en pixels
```

## 📁 Fichiers Modifiés

### 1. **JSX Block** - `assets/js/blocks/image-comparison-slider.jsx`

**Attributs ajoutés** :
```jsx
heightMode: {
  type: "string",
  default: "auto", // auto, custom, full-viewport
},
customHeight: {
  type: "number",
  default: 600,
},
```

**Interface Gutenberg** :
- Nouveau panneau "Options de Hauteur" dans InspectorControls
- SelectControl pour choisir le mode
- RangeControl conditionnel pour la hauteur personnalisée (300-1200px)
- Message d'aide indiquant que l'aspect ratio est ignoré en mode hauteur fixe
- Indicateur visuel dans l'éditeur montrant le mode actif

### 2. **PHP Rendering** - `inc/blocks/content/image-comparison-slider.php`

**Attributs PHP ajoutés** :
```php
'heightMode' => [
    'type' => 'string',
    'default' => 'auto'
],
'customHeight' => [
    'type' => 'number',
    'default' => 600
]
```

**Logique de rendu** :
```php
$height_mode = isset($attributes['heightMode']) ? esc_attr($attributes['heightMode']) : 'auto';
$custom_height = isset($attributes['customHeight']) ? absint($attributes['customHeight']) : 600;

// Classes CSS
$container_classes = [
    'archi-image-comparison-slider',
    'orientation-' . $orientation,
    'height-' . $height_mode
];

// Aspect ratio seulement en mode auto
if ($height_mode === 'auto') {
    $container_classes[] = 'aspect-ratio-' . $aspect_ratio;
}

// Style inline pour hauteur custom
$inline_styles = [];
if ($height_mode === 'custom') {
    $inline_styles[] = 'height: ' . $custom_height . 'px';
}
```

### 3. **CSS Styles** - `assets/css/image-comparison-slider.css`

**Styles ajoutés** :

```css
/* Mode pleine hauteur d'écran */
.archi-image-comparison-slider.height-full-viewport {
    height: 100vh;
    margin: 0;
    border-radius: 0;
}

.archi-image-comparison-slider.height-full-viewport .comparison-container {
    height: 100vh;
}

/* Mode hauteur personnalisée */
.archi-image-comparison-slider.height-custom .comparison-container {
    height: 100%;
}

/* Mode auto utilise l'aspect ratio (défaut) */
.archi-image-comparison-slider.height-auto .comparison-container {
    height: auto;
}

/* Ratios d'aspect (utilisés seulement en mode height-auto) */
.archi-image-comparison-slider.height-auto.aspect-ratio-16-9 .comparison-container {
    aspect-ratio: 16 / 9;
}
```

**Responsive mobile** :
```css
@media (max-width: 768px) {
    /* Safe viewport height sur mobile */
    .archi-image-comparison-slider.height-full-viewport {
        height: 100svh;
    }
    
    .archi-image-comparison-slider.height-full-viewport .comparison-container {
        height: 100svh;
    }
}
```

## 🎨 Interface Utilisateur

### Dans l'éditeur Gutenberg

**Panneau "Options de Hauteur"** (en haut des settings) :
1. **Mode de Hauteur** - SelectControl avec 3 options :
   - Automatique (aspect ratio)
   - Pleine hauteur d'écran
   - Hauteur personnalisée

2. **Hauteur personnalisée (px)** - RangeControl (visible uniquement si mode = custom) :
   - Min: 300px
   - Max: 1200px
   - Step: 50px
   - Défaut: 600px

**Panneau "Paramètres du Slider"** (après Options de Hauteur) :
- Orientation (vertical/horizontal)
- Position initiale (0-100%)
- **Ratio d'aspect** - avec message d'aide indiquant quand il est ignoré :
  - 16:9 (paysage)
  - 4:3 (standard)
  - 1:1 (carré)
  - 3:4 (portrait)
  - Original
  - _Message : "Le ratio d'aspect est ignoré en mode hauteur fixe"_
- Couleur de la poignée

**Indicateur visuel** :
Le titre affiche des informations sur le mode actif :
- "Comparaison Avant/Après (Pleine hauteur d'écran)"
- "Comparaison Avant/Après (800px)"

## 🔧 Usage

### Mode Automatique (Défaut)
```
Comparaison Avant/Après
└── Options de Hauteur
    ├── Mode: Automatique (aspect ratio)
    └── Paramètres du Slider
        └── Ratio: 16:9
```

**Résultat** : Image responsive avec ratio 16:9, s'adapte à la largeur du conteneur.

### Mode Pleine Hauteur
```
Comparaison Avant/Après
└── Options de Hauteur
    ├── Mode: Pleine hauteur d'écran
    └── [Ratio ignoré]
```

**Résultat** : Slider occupe 100vh (100svh sur mobile), effet immersif plein écran.

### Mode Hauteur Personnalisée
```
Comparaison Avant/Après
└── Options de Hauteur
    ├── Mode: Hauteur personnalisée
    ├── Hauteur: 800px
    └── [Ratio ignoré]
```

**Résultat** : Slider avec hauteur fixe de 800px, contrôle précis de la taille.

## 📊 Architecture des Classes CSS

### Hiérarchie
```
.archi-image-comparison-slider
├── .orientation-{vertical|horizontal}
├── .height-{auto|custom|full-viewport}
└── .aspect-ratio-{16-9|4-3|1-1|3-4|original} [seulement si height-auto]
```

### Exemples de combinaisons
```css
/* Auto avec ratio 16:9 */
.archi-image-comparison-slider.orientation-vertical.height-auto.aspect-ratio-16-9

/* Pleine hauteur */
.archi-image-comparison-slider.orientation-vertical.height-full-viewport

/* Hauteur custom 800px */
.archi-image-comparison-slider.orientation-vertical.height-custom
/* + style="height: 800px" */
```

## 🎯 Cas d'Usage

### 1. **Section Hero Immersive**
```
Mode: Pleine hauteur d'écran
Orientation: Vertical
Usage: Page d'accueil, présentation de projet avant/après
```

### 2. **Galerie de Rénovations**
```
Mode: Hauteur personnalisée (700px)
Orientation: Vertical
Usage: Portfolio de transformations, comparaisons détaillées
```

### 3. **Contenu Article**
```
Mode: Automatique
Ratio: 16:9 ou 4:3
Usage: Images intégrées dans le flux de contenu
```

### 4. **Comparaison Mobile**
```
Mode: Pleine hauteur (100svh)
Orientation: Horizontal (haut/bas)
Usage: Expérience immersive sur smartphone
```

## ✨ Améliorations UX

1. **Indication claire du mode actif** dans l'éditeur
2. **Message d'aide contextuel** pour l'aspect ratio
3. **Cohérence avec le bloc Parallax Image** (même structure d'options)
4. **RangeControl intuitif** pour la hauteur personnalisée
5. **Safe viewport height** (`100svh`) pour une meilleure compatibilité mobile

## 🔄 Rétrocompatibilité

Les blocs existants continueront de fonctionner avec le mode `auto` par défaut et leur aspect ratio configuré. Aucune migration nécessaire.

**Attributs par défaut** :
```jsx
heightMode: 'auto'
customHeight: 600
aspectRatio: '16-9'
```

## 📦 Build

### Compilation
```bash
npm run build
```

**Résultat** :
```
✅ image-comparison-slider.bundle.js - 8.78 KiB [emitted]
```

### Assets
- **JSX** : `/assets/js/blocks/image-comparison-slider.jsx`
- **PHP** : `/inc/blocks/content/image-comparison-slider.php`
- **CSS** : `/assets/css/image-comparison-slider.css`
- **Bundle** : `/dist/image-comparison-slider.bundle.js`

## 🎉 Résumé

Le bloc **Image Comparison Slider** offre maintenant :
- ✅ 3 modes de hauteur (auto, custom, full-viewport)
- ✅ Contrôle précis de la hauteur (300-1200px)
- ✅ Option plein écran pour sections hero
- ✅ Cohérence avec le bloc Parallax Image
- ✅ Interface Gutenberg intuitive
- ✅ Responsive mobile avec safe viewport
- ✅ Rétrocompatibilité totale

**Le slider Avant/Après est maintenant aussi flexible et polyvalent que le bloc Parallax ! 🚀**
