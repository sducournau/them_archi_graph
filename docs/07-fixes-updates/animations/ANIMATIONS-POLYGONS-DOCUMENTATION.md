# Nouvelles Fonctionnalités : Animations & Polygones de Catégories

**Date**: 8 novembre 2025  
**Version**: 1.5.0

## 🎬 Système d'Animations du Graphique

### Fichier principal
- `assets/js/utils/graphAnimations.js`

### Types d'animations disponibles

Le système propose **10 types d'animations** différents pour l'apparition des nœuds :

1. **Fade In** (`fadeIn`) - Apparition progressive avec opacité
2. **Scale Up** (`scaleUp`) - Zoom progressif depuis le centre
3. **Bounce** (`bounce`) - Rebond élastique à l'apparition
4. **Spiral** (`spiral`) - Spirale depuis le centre du graphique
5. **Wave** (`wave`) - Effet de vague fluide
6. **Pulse** (`pulse`) - Pulsation continue
7. **Elastic** (`elastic`) - Rebond élastique exagéré
8. **Stagger** (`stagger`) - Cascade progressive
9. **Explode** (`explode`) - Explosion depuis le centre
10. **Morph** (`morph`) - Transformation de forme

### Configuration dans l'admin

Les animations se configurent via **Archi Graph → Graphique → Animations & Interactions** :

```php
// Paramètres enregistrés
- archi_graph_animation_type     : Type d'animation (défaut: fadeIn)
- archi_graph_animation_duration : Durée en ms (200-2000, défaut: 800)
- archi_graph_hover_effect       : Activer effet de survol (défaut: true)
- archi_graph_hover_scale        : Intensité du zoom (1.0-1.5, défaut: 1.15)
- archi_graph_link_animation     : Animer les liens (défaut: true)
- archi_graph_organic_mode       : Mode organique avec îles (défaut: true)
- archi_graph_cluster_strength   : Force de clustering (0-1, défaut: 0.1)
```

### Utilisation JavaScript

```javascript
import { runAnimation, ANIMATION_TYPES, applyHoverAnimation } from './utils/graphAnimations.js';

// Appliquer une animation aux nœuds
const nodes = svg.selectAll('.graph-node');
runAnimation(ANIMATION_TYPES.BOUNCE, nodes, {
  duration: 800,
  delay: 0
});

// Activer l'effet de survol
applyHoverAnimation(nodes, {
  scaleFactor: 1.15,
  duration: 200,
  shadowBlur: 20
});
```

### Animations des liens

Les liens peuvent être animés avec un effet de tracé progressif :

```javascript
import { animateLinks } from './utils/graphAnimations.js';

const links = svg.selectAll('.graph-link');
animateLinks(links, {
  duration: 1000,
  delay: 0,
  staggerDelay: 20  // Délai entre chaque lien
});
```

### Fonctions utilitaires

```javascript
// Réinitialiser toutes les animations
resetAnimations(selection);

// Transition entre états
transitionToNewState(selection, newPositions, { duration: 800 });

// Animation au clic
applyClickAnimation(selection, { duration: 300, scaleFactor: 0.9 });
```

---

## 🎨 Polygones de Catégories

### Fichiers principaux
- `inc/category-polygon-colors.php` (Backend)
- `assets/js/utils/polygonRenderer.js` (Frontend)

### Fonctionnalité

Le système dessine des **enveloppes convexes** (convex hulls) autour des groupes d'articles partageant la même catégorie, créant des zones colorées dans le graphique.

### Configuration par catégorie

Dans **Articles → Catégories**, chaque catégorie dispose de nouveaux champs :

#### Champs disponibles

1. **Polygone dans le graphique** (checkbox)
   - Active/désactive le polygone pour cette catégorie
   - Défaut : activé

2. **Couleur du polygone** (color picker)
   - Définit la couleur du polygone
   - Défaut : #3498db (bleu)

3. **Opacité du polygone** (range slider)
   - Contrôle la transparence (0-1)
   - Défaut : 0.2 (20%)

#### Aperçu en temps réel

L'interface d'édition affiche un aperçu visuel du polygone avec la couleur et l'opacité sélectionnées.

### Métadonnées enregistrées

```php
// Pour chaque term (catégorie)
archi_polygon_enabled  : boolean (true/false)
archi_polygon_color    : string  (hex color)
archi_polygon_opacity  : float   (0-1)
```

### API REST

**Endpoint** : `/wp-json/archi/v1/polygon-colors`

Retourne toutes les configurations de polygones :

```json
[
  {
    "category_id": 12,
    "category_name": "Architecture",
    "category_slug": "architecture",
    "enabled": true,
    "color": "#e74c3c",
    "opacity": 0.25
  },
  {
    "category_id": 15,
    "category_name": "Urbanisme",
    "category_slug": "urbanisme",
    "enabled": true,
    "color": "#3498db",
    "opacity": 0.2
  }
]
```

### Utilisation JavaScript

```javascript
import { 
  createCategoryPolygons, 
  drawPolygons, 
  loadPolygonColors 
} from './utils/polygonRenderer.js';

// Charger les configurations
const polygonColors = await loadPolygonColors();

// Créer les polygones
const polygons = createCategoryPolygons(nodes, categories, polygonColors);

// Dessiner sur le SVG
drawPolygons(svg, polygons, {
  className: 'category-polygon',
  animated: true,
  animationDuration: 800
});
```

### Fonctionnalités du rendu

#### Algorithme de convex hull
Utilise l'algorithme de **Graham scan** pour calculer l'enveloppe convexe des points.

#### Expansion avec padding
Les polygones sont automatiquement agrandis de 30px pour englober visuellement les nœuds.

#### Lissage des courbes
Les polygones sont lissés avec des **courbes de Bézier** pour un rendu plus organique :

```javascript
const path = smoothHull(hull, 0.5); // tension = 0.5
```

#### Mise à jour dynamique

```javascript
// Mettre à jour quand les nœuds bougent
updatePolygons(svg, nodes, categories, polygonColors);

// Toggle visibilité
togglePolygonsVisibility(svg, true, 300); // show
togglePolygonsVisibility(svg, false, 300); // hide
```

### Interactions

- **Survol** : Le polygone devient plus opaque et son contour s'épaissit
- **Tooltip** : Affiche le nom de la catégorie et le nombre d'articles

---

## 🔧 Intégration dans le Graphique

### Ordre de rendu

Pour que les polygones apparaissent **derrière** les nœuds :

```javascript
// 1. Créer le groupe de polygones en premier
const polygonGroup = svg.insert("g", ":first-child").attr("class", "polygons-layer");

// 2. Dessiner les polygones
drawPolygons(svg, polygons);

// 3. Puis dessiner les nœuds et liens
// Les nœuds apparaîtront au-dessus
```

### Exemple complet

```javascript
import * as d3 from 'd3';
import { runAnimation, ANIMATION_TYPES } from './utils/graphAnimations.js';
import { loadPolygonColors, createCategoryPolygons, drawPolygons } from './utils/polygonRenderer.js';

// Charger les données
const articles = await fetch('/wp-json/archi/v1/articles').then(r => r.json());
const polygonColors = await loadPolygonColors();

// Créer le SVG
const svg = d3.select('#graph-container')
  .append('svg')
  .attr('width', 1200)
  .attr('height', 800);

// 1. Dessiner les polygones
const polygons = createCategoryPolygons(
  articles.nodes, 
  articles.categories, 
  polygonColors
);
drawPolygons(svg, polygons);

// 2. Dessiner les nœuds
const nodes = svg.selectAll('.graph-node')
  .data(articles.nodes)
  .enter()
  .append('g')
  .attr('class', 'graph-node');

// 3. Appliquer l'animation
runAnimation(ANIMATION_TYPES.BOUNCE, nodes, { duration: 800 });
```

---

## 📊 Colonne Admin

Une nouvelle colonne **"Polygone Graphique"** apparaît dans la liste des catégories :

- ✅ Affiche un aperçu visuel de la couleur du polygone
- ❌ Affiche "—" si le polygone est désactivé

---

## 🎯 Bonnes Pratiques

### Couleurs de polygones

1. **Contraste** : Choisir des couleurs suffisamment différentes entre catégories
2. **Opacité** : Rester entre 0.15 et 0.3 pour ne pas masquer les nœuds
3. **Palette cohérente** : Utiliser une palette de couleurs harmonieuse

### Animations

1. **Performance** : Pour >100 nœuds, privilégier `fadeIn` ou `scaleUp`
2. **Durée** : 600-1000ms pour un bon équilibre vitesse/fluidité
3. **Mode organique** : Activer pour de meilleurs regroupements visuels

### Polygones

1. **Minimum de nœuds** : Au moins 3 nœuds requis pour dessiner un polygone
2. **Mise à jour** : Recalculer les polygones après mouvement des nœuds
3. **Performance** : Désactiver les polygones si >20 catégories visibles

---

## 🔄 Workflow de Développement

### Ajouter une nouvelle animation

1. Créer la fonction dans `graphAnimations.js`
2. Ajouter le type dans `ANIMATION_TYPES`
3. Enregistrer l'option dans `admin-unified-settings.php`
4. Ajouter l'option dans le `<select>` de l'admin

### Modifier le rendu des polygones

1. Éditer `polygonRenderer.js`
2. Ajuster l'algorithme dans `calculateConvexHull()`
3. Modifier le padding dans `expandHull()`
4. Personnaliser le lissage dans `smoothHull()`

---

## 🐛 Dépannage

### Les animations ne fonctionnent pas

- Vérifier que D3.js est bien chargé
- Vérifier la console pour les erreurs
- S'assurer que les nœuds ont des positions `x` et `y`

### Les polygones ne s'affichent pas

- Vérifier qu'au moins 3 nœuds existent par catégorie
- Vérifier que `archi_polygon_enabled` est à `true`
- Vérifier l'API `/wp-json/archi/v1/polygon-colors`

### Performance dégradée

- Réduire le nombre de nœuds visibles
- Désactiver les animations des liens
- Utiliser une animation plus simple (fadeIn)
- Limiter le nombre de polygones visibles

---

## 📝 Changelog

### Version 1.5.0 (2025-11-08)

**✨ Nouveautés**
- 10 types d'animations pour l'apparition des nœuds
- Système de polygones de catégories avec convex hull
- Interface d'édition des couleurs de polygone par catégorie
- Paramètres avancés d'animation dans l'admin
- Mode organique avec îles architecturales
- Effets de survol et clic configurables

**🔧 Technique**
- Nouveau fichier : `assets/js/utils/graphAnimations.js`
- Nouveau fichier : `assets/js/utils/polygonRenderer.js`
- Nouveau fichier : `inc/category-polygon-colors.php`
- Paramètres admin étendus dans `admin-unified-settings.php`
- Endpoint REST : `/wp-json/archi/v1/polygon-colors`

**🎨 UI/UX**
- Aperçu en temps réel des polygones dans l'admin
- Colonne "Polygone Graphique" dans la liste des catégories
- Tooltips sur les polygones au survol
- Animations fluides et professionnelles

---

## 📚 Références

### Documentation D3.js
- [D3 Transitions](https://github.com/d3/d3-transition)
- [D3 Force Simulation](https://github.com/d3/d3-force)
- [D3 Easing Functions](https://github.com/d3/d3-ease)

### Algorithmes
- [Graham Scan (Convex Hull)](https://en.wikipedia.org/wiki/Graham_scan)
- [Bézier Curves](https://en.wikipedia.org/wiki/B%C3%A9zier_curve)

### WordPress
- [Term Meta](https://developer.wordpress.org/reference/functions/register_term_meta/)
- [REST API](https://developer.wordpress.org/rest-api/)
- [Settings API](https://developer.wordpress.org/plugins/settings/)
