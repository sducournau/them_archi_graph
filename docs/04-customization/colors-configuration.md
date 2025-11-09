# Configuration des Couleurs des Polygones

## Vue d'ensemble

Le système de graphique supporte maintenant des polygones colorés distincts pour :
- **Projets architecturaux** : Polygones jaunes par défaut (`#f39c12`)
- **Illustrations** : Polygones rouges par défaut (`#e74c3c`)

## Fonctionnement

### Système de Polygones

Les polygones (îles architecturales) sont créés automatiquement pour regrouper visuellement les contenus similaires :

1. **Polygones de Projets** (Jaune)
   - Groupent les projets architecturaux (`archi_project`)
   - Basés sur les catégories et tags partagés
   - Minimum 2 projets pour créer un polygone

2. **Polygones d'Illustrations** (Rouge)
   - Groupent les illustrations (`archi_illustration`)
   - Basés sur les catégories et tags partagés
   - Minimum 2 illustrations pour créer un polygone

### Critères de Regroupement

Un polygone est créé quand :
- Au moins **2 catégories communes** OU
- Au moins **2 tags communs** OU
- Lien manuel entre les contenus

## Configuration des Couleurs

### Via WordPress (Recommandé)

Ajoutez ces options dans votre panneau d'administration WordPress :

```php
// Dans inc/admin-settings.php ou via le customizer

// Couleur des polygones de projets (défaut: jaune)
update_option('graph_island_color', '#f39c12');

// Couleur des polygones d'illustrations (défaut: rouge)
update_option('graph_illustration_island_color', '#e74c3c');
```

### Via le Template

Les couleurs sont configurées dans `template-parts/graph-homepage.php` :

```php
options: {
    // ... autres options ...
    islandColor: '<?php echo archi_get_option('graph_island_color', '#f39c12'); ?>',
    illustrationIslandColor: '<?php echo archi_get_option('graph_illustration_island_color', '#e74c3c'); ?>'
}
```

### Couleurs Personnalisées

Vous pouvez utiliser n'importe quelle couleur au format hexadécimal :

```php
// Exemples de couleurs
'#f39c12' // Jaune orangé (défaut projets)
'#e74c3c' // Rouge (défaut illustrations)
'#3498db' // Bleu
'#2ecc71' // Vert
'#9b59b6' // Violet
'#e67e22' // Orange
'#1abc9c' // Turquoise
```

## Styles des Polygones

Chaque polygone comprend :

### Contour Principal (island-background)
- **Remplissage** : Couleur avec opacité 12%
- **Bordure** : Couleur avec opacité 30%
- **Épaisseur** : 3px
- **Style** : Ligne pointillée (8,4)
- **Effet** : Filtre de lueur

### Texture Interne (island-texture)
- **Bordure** : Couleur avec opacité 15%
- **Épaisseur** : 1px
- **Style** : Ligne pointillée (3,3)
- **Position** : 20px à l'intérieur du contour principal

## Animations

Les polygones incluent des animations CSS définies dans `assets/css/organic-islands.css` :

- **Animation de pulsation** : Cycle de 8 secondes
- **Animation d'ondulation** : Cycle de 12 secondes
- **Effet au survol** : Augmentation de l'opacité

## Exemple de Configuration Complète

```php
// functions.php ou inc/admin-settings.php

function archi_setup_graph_colors() {
    // Couleurs par défaut
    add_option('graph_island_color', '#f39c12'); // Jaune pour projets
    add_option('graph_illustration_island_color', '#e74c3c'); // Rouge pour illustrations
}
add_action('after_setup_theme', 'archi_setup_graph_colors');

// Fonction pour récupérer une option avec valeur par défaut
function archi_get_option($option_name, $default = '') {
    return get_option($option_name, $default);
}
```

## Personnalisation Avancée

### Modifier les Critères de Regroupement

Dans `assets/js/components/GraphContainer.jsx`, fonction `updateArchitecturalIslands()` :

```javascript
// Modifier le nombre minimum de catégories/tags partagés
return sharedCategories.length >= 2 || // Change ce nombre
       sharedTags.length >= 2 ||        // Change ce nombre
       hasManualLink;
```

### Modifier le Nombre Minimum de Membres

```javascript
// Ne créer une île que s'il y a au moins X membres
if (island.members.length >= 2) { // Change ce nombre
    // ...
}
```

### Ajuster l'Espacement des Polygones

```javascript
// Agrandir l'enveloppe pour plus d'espace
hull = expandHull(hull, 60); // Augmente/diminue ce nombre (en pixels)
```

## Dépannage

### Les polygones n'apparaissent pas
- Vérifiez qu'il y a au moins 2 contenus du même type avec catégories/tags communs
- Vérifiez que le mode îles architecturales est activé dans `graphHelpers.js`

### Les couleurs ne changent pas
- Videz le cache du navigateur
- Recompilez avec `npm run build`
- Vérifiez que les options WordPress sont bien définies

### Les polygones se chevauchent
- Ajustez le padding dans `expandHull(hull, 60)`
- Modifiez les forces dans `graphHelpers.js`

## Fichiers Concernés

- `assets/js/components/GraphContainer.jsx` - Logique des polygones
- `assets/js/utils/graphHelpers.js` - Configuration des forces
- `assets/css/organic-islands.css` - Styles et animations
- `template-parts/graph-homepage.php` - Configuration initiale
- `inc/admin-settings.php` - Options WordPress (à créer/modifier)

## Notes Importantes

1. Les polygones remplacent les anciens clusters de catégories
2. Chaque type de contenu (projet/illustration) a ses propres polygones
3. Les couleurs sont configurables mais les valeurs par défaut sont recommandées
4. Les animations peuvent être désactivées avec `prefers-reduced-motion`
