# Satellites de Flèches Animées - Documentation

## Vue d'ensemble

Le système de satellites de flèches ajoute des GIFs animés de flèches qui orbitent autour des nodes du graph. Ces flèches pointent vers les articles comme des satellites et créent un effet visuel dynamique et attractif.

## Caractéristiques

### 🎯 Principales fonctionnalités

1. **Nombre dynamique** : Le nombre de flèches autour d'un node dépend de son `node_size` (importance)
   - Nodes très petits (< 40px) : 0 flèche
   - Nodes petits (40-49px) : 1 flèche
   - Nodes moyens (50-59px) : 2 flèches
   - Nodes moyens-grands (60-69px) : 3 flèches
   - Nodes grands (70-84px) : 4 flèches
   - Nodes très grands (85-99px) : 5 flèches
   - Nodes énormes (≥ 100px) : 6 flèches

2. **Animation orbitale** : Les flèches tournent autour des nodes en orbite circulaire
3. **Orientation dynamique** : Chaque flèche pointe toujours vers le centre du node
4. **Non-cliquables** : Les flèches n'interfèrent pas avec les interactions sur les nodes
5. **Optimisé pour la performance** : Utilise l'accélération matérielle GPU

## Structure des fichiers

```
assets/
├── js/
│   └── utils/
│       └── arrowSatellites.js      # Logique principale des satellites
└── css/
    └── arrow-satellites.css        # Styles des satellites

gif/
├── dancing-arrow-pointer-attracting-attention-transparent-background-usagif.gif
├── red-bouncing-arrow-pointer-transparent-background-usagif.gif
└── white-arrow-pointing-right-transparent-background-usagif.gif
```

## Intégration dans le code

### Dans GraphContainer.jsx

```javascript
import {
  createArrowSatellites,
  animateArrowSatellites,
  updateArrowSatellites,
} from "../utils/arrowSatellites";

// Après la fusion des nodes (enter + update)
updateArrowSatellites(nodeUpdate);

// Dans la boucle d'animation (tick)
simulation.on("tick", () => {
  // ... autres mises à jour
  const nodeGroups = g.selectAll(".graph-node");
  animateArrowSatellites(nodeGroups);
});
```

### Configuration WordPress

Le `themeUrl` est ajouté dans `window.graphConfig` pour permettre l'accès aux GIFs :

```php
window.graphConfig = {
    // ...
    themeUrl: '<?php echo esc_url(get_template_directory_uri()); ?>',
    // ...
};
```

## API des fonctions

### `calculateArrowCount(nodeSize)`
Calcule le nombre de flèches basé sur la taille du node.

**Paramètres :**
- `nodeSize` (number) : Taille du node en pixels (40-120)

**Retourne :** (number) Nombre de flèches (0-6)

### `calculateSatellitePositions(nodeSize, count, orbitRadius)`
Calcule les positions initiales des satellites autour du node.

**Paramètres :**
- `nodeSize` (number) : Taille du node
- `count` (number) : Nombre de satellites
- `orbitRadius` (number, optionnel) : Rayon de l'orbite (défaut : nodeSize/2 + 40)

**Retourne :** Array<{angle, x, y}> Positions des satellites

### `createArrowSatellites(nodeData, nodeGroup)`
Crée les éléments SVG des satellites pour un node.

**Paramètres :**
- `nodeData` (Object) : Données du node avec `node_size`
- `nodeGroup` (d3.Selection) : Sélection D3 du groupe du node

### `animateArrowSatellites(nodeGroups, time)`
Anime les satellites en orbite. À appeler dans la boucle d'animation.

**Paramètres :**
- `nodeGroups` (d3.Selection) : Sélection D3 de tous les groupes de nodes
- `time` (number, optionnel) : Timestamp actuel (défaut : Date.now())

### `updateArrowSatellites(nodeGroups)`
Met à jour les satellites quand les données des nodes changent.

**Paramètres :**
- `nodeGroups` (d3.Selection) : Sélection D3 de tous les groupes de nodes

### `toggleArrowSatellites(nodeGroups, visible)`
Affiche ou masque les satellites.

**Paramètres :**
- `nodeGroups` (d3.Selection) : Sélection D3 de tous les groupes de nodes
- `visible` (boolean) : true pour afficher, false pour masquer

## Styles CSS

### Classes principales

- `.satellites-group` : Conteneur des satellites pour un node
- `.arrow-satellite` : Un satellite individuel
- `.arrow-gif` : L'image GIF de la flèche

### Interactions

```css
/* Survol du node - satellites plus visibles */
.graph-node:hover .satellites-group .arrow-gif {
  opacity: 1;
  filter: drop-shadow(0 0 4px rgba(255, 255, 255, 0.8));
}

/* Node sélectionné - satellites pulsent */
.graph-node.selected .satellites-group .arrow-gif {
  animation: arrow-pulse 2s ease-in-out infinite;
}
```

## Personnalisation

### Ajouter de nouveaux GIFs

1. Placez les GIFs dans le dossier `gif/`
2. Ajoutez le nom du fichier dans `ARROW_GIFS` dans `arrowSatellites.js` :

```javascript
const ARROW_GIFS = [
  'dancing-arrow-pointer-attracting-attention-transparent-background-usagif.gif',
  'red-bouncing-arrow-pointer-transparent-background-usagif.gif',
  'white-arrow-pointing-right-transparent-background-usagif.gif',
  'votre-nouveau-gif.gif' // Ajouter ici
];
```

### Modifier le nombre de flèches

Ajustez la fonction `calculateArrowCount` dans `arrowSatellites.js` :

```javascript
export const calculateArrowCount = (nodeSize) => {
  const size = nodeSize || 60;
  
  if (size >= 100) return 8; // Augmenté de 6 à 8
  if (size >= 85) return 6;
  // ...
};
```

### Modifier la vitesse d'orbite

Dans `animateArrowSatellites`, ajustez `rotationSpeed` :

```javascript
const rotationSpeed = 0.001; // Plus rapide (défaut: 0.0005)
```

### Modifier le rayon d'orbite

Dans `createArrowSatellites`, ajustez le calcul :

```javascript
nodeData._satelliteOrbitRadius = (nodeData.node_size || 60) / 2 + 60; // Plus loin (défaut: +40)
```

### Modifier la taille des flèches

Dans `createArrowSatellites`, ajustez les dimensions :

```javascript
newSatellites.append('image')
  .attr('width', 40)  // Plus grand (défaut: 30)
  .attr('height', 40)
  .attr('x', -20)     // Ajuster le centrage
  .attr('y', -20);
```

## Performance

### Optimisations implémentées

1. **Accélération GPU** : `will-change: transform` et `transform: translateZ(0)`
2. **Événements désactivés** : `pointer-events: none` sur tous les satellites
3. **Animation requestAnimationFrame** : Intégrée dans le tick de la simulation D3
4. **Réutilisation des éléments** : Pattern enter/update/exit de D3

### Recommandations

- Pour de très nombreux nodes (>200), envisagez de limiter les satellites aux nodes importants
- Sur mobile, les satellites sont automatiquement réduits (scale 0.8)
- L'animation est optimisée mais peut impacter les performances sur des appareils anciens

## Débogage

### Vérifier que les satellites sont créés

```javascript
const satellites = d3.selectAll('.satellites-group');
console.log('Nombre de groupes de satellites:', satellites.size());

satellites.each(function(d) {
  const arrows = d3.select(this).selectAll('.arrow-satellite');
  console.log(`Node ${d.id}: ${arrows.size()} flèches`);
});
```

### Vérifier l'animation

```javascript
// Dans la console du navigateur
setInterval(() => {
  const satellite = document.querySelector('.arrow-satellite');
  if (satellite) {
    console.log('Transform:', satellite.getAttribute('transform'));
  }
}, 1000);
```

### Désactiver temporairement

```javascript
// Dans GraphContainer.jsx, commenter ces lignes :
// updateArrowSatellites(nodeUpdate);
// animateArrowSatellites(nodeGroups);
```

## Compatibilité

- ✅ Chrome/Edge (Chromium) 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Mobile (iOS Safari, Chrome Mobile)
- ⚠️ Performances réduites sur IE11 (non supporté officiellement)

## Changelog

### Version 1.0.0 (2025-01-04)
- Première implémentation
- Support de 6 niveaux de satellites (0-6 flèches)
- Animation orbitale avec orientation dynamique
- 3 GIFs de flèches disponibles
- Styles et interactions interactives
- Intégration complète dans le système de graph

## Crédits

GIFs de flèches animées fournis par USA GIF (transparent background).
