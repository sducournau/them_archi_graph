# Blocs de Visualisation - Documentation

Documentation complète des nouveaux blocs de visualisation ajoutés au thème Archi-Graph : carte interactive et graphiques D3.js.

## 📋 Vue d'ensemble

**Blocs créés :**
- 🗺️ **Carte Interactive** (Leaflet.js) - Cartes avec marqueurs et popups personnalisables
- 📊 **Graphique en Barres** (D3.js) - Barres verticales/horizontales avec animations
- 📅 **Timeline** (D3.js) - Chronologie horizontale pour événements de projets

**Build :**
- Bundles : `interactive-map.bundle.js` (8.85 KiB), `d3-bar-chart.bundle.js` (7.04 KiB), `d3-timeline.bundle.js` (6.42 KiB)
- CSS : `visualization-blocks.css` (styles communs pour tous les blocs)
- Compilation : ✅ Webpack 5.102.1 - Build réussi

---

## 🗺️ Bloc Carte Interactive

### Description

Bloc Gutenberg pour afficher une carte interactive basée sur Leaflet.js avec marqueurs personnalisables, popups et différents styles de cartes.

### Fichiers

```
assets/js/blocks/interactive-map.jsx          # Composant React pour l'éditeur
inc/blocks/content/interactive-map.php        # Rendu serveur PHP
assets/css/visualization-blocks.css           # Styles CSS (section Carte)
```

### Fonctionnalités

✅ **Carte personnalisable**
- Centre de la carte (latitude/longitude)
- Niveau de zoom (1-18)
- Hauteur personnalisable (300-800px)

✅ **Styles de carte**
- OpenStreetMap (standard)
- OpenStreetMap France
- Stamen Terrain
- Esri World Imagery (satellite)

✅ **Marqueurs interactifs**
- Ajouter/modifier/supprimer des marqueurs
- Position (lat/lng) pour chaque marqueur
- Couleur personnalisable par marqueur
- Titre et description dans popups
- Icônes SVG personnalisées

✅ **Contrôles**
- Afficher/masquer les contrôles de zoom
- Activer/désactiver le zoom au scroll

### Attributs

```javascript
{
  latitude: 48.8566,         // Latitude du centre
  longitude: 2.3522,         // Longitude du centre
  zoom: 13,                  // Niveau de zoom (1-18)
  height: 400,               // Hauteur en pixels (300-800)
  mapStyle: 'osm',           // Style : 'osm'|'osm-fr'|'terrain'|'satellite'
  markers: [                 // Tableau de marqueurs
    {
      id: '1',
      lat: 48.8566,
      lng: 2.3522,
      title: 'Titre',
      description: 'Description',
      color: '#e74c3c'
    }
  ],
  showControls: true,        // Afficher contrôles de zoom
  enableScroll: true         // Zoom au scroll activé
}
```

### Usage dans l'éditeur

1. **Ajouter le bloc**
   - Tapez `/carte` ou cherchez "Carte Interactive" dans l'inserter
   - Le bloc s'insère avec une carte par défaut (Paris)

2. **Configurer la carte**
   - Panneau de droite : réglages de la carte (centre, zoom, hauteur)
   - Choisir le style de carte (OSM, Terrain, Satellite)

3. **Ajouter des marqueurs**
   - Cliquez sur "Ajouter un marqueur"
   - Définir position (lat/lng), titre, description, couleur
   - Ordre des marqueurs = ordre dans la liste

4. **Options**
   - Afficher/masquer contrôles de zoom
   - Activer/désactiver zoom au scroll

### Code d'exemple

```php
<!-- Bloc carte dans le contenu -->
<!-- wp:archi-graph/interactive-map {
  "latitude":48.8566,
  "longitude":2.3522,
  "zoom":13,
  "height":400,
  "mapStyle":"osm",
  "markers":[
    {
      "id":"1",
      "lat":48.8566,
      "lng":2.3522,
      "title":"Projet A",
      "description":"Description du projet",
      "color":"#e74c3c"
    }
  ],
  "showControls":true,
  "enableScroll":false
} /-->
```

### Intégration technique

**Chargement des assets :**
```php
// Leaflet CSS (frontend uniquement)
wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');

// Leaflet JS (chargé via CDN dans le renderer)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
```

**Initialisation carte :**
```javascript
const map = L.map('archi-map-{id}').setView([lat, lng], zoom);
L.tileLayer(tileUrl).addTo(map);

// Marqueurs personnalisés
markers.forEach(marker => {
  const icon = L.divIcon({
    html: `<svg>...</svg>`, // SVG coloré
    className: 'custom-marker'
  });
  L.marker([marker.lat, marker.lng], {icon})
    .bindPopup(`<strong>${marker.title}</strong><p>${marker.description}</p>`)
    .addTo(map);
});
```

---

## 📊 Bloc Graphique en Barres (D3.js)

### Description

Graphique en barres interactif avec D3.js, orientation verticale/horizontale, schémas de couleurs et animations.

### Fichiers

```
assets/js/blocks/d3-bar-chart.jsx              # Composant React
inc/blocks/content/d3-bar-chart.php            # Rendu D3.js serveur
assets/css/visualization-blocks.css            # Styles CSS
```

### Fonctionnalités

✅ **Graphique personnalisable**
- Titre du graphique
- Orientation : verticale ou horizontale
- Hauteur personnalisable (300-800px)

✅ **Données**
- Ajouter/modifier/supprimer des points de données
- Étiquette et valeur pour chaque barre
- Éditeur visuel dans Gutenberg

✅ **Apparence**
- 5 schémas de couleurs (bleu, vert, orange, violet, personnalisé)
- Couleur personnalisée (si schéma "custom")
- Afficher/masquer les valeurs sur les barres
- Afficher/masquer la grille

✅ **Animations**
- Transition animée à 800ms
- Effet hover sur les barres
- Désactivable pour accessibilité

### Attributs

```javascript
{
  title: 'Mon graphique',    // Titre
  data: [                    // Données
    { label: 'A', value: 30 },
    { label: 'B', value: 50 },
    { label: 'C', value: 40 }
  ],
  orientation: 'vertical',   // 'vertical' | 'horizontal'
  colorScheme: 'blue',       // 'blue'|'green'|'orange'|'purple'|'custom'
  customColor: '#3498db',    // Couleur si colorScheme='custom'
  showValues: true,          // Afficher valeurs sur barres
  showGrid: true,            // Afficher grille
  height: 400,               // Hauteur en pixels (300-800)
  animate: true              // Activer animations
}
```

### Schémas de couleurs

```javascript
const colorSchemes = {
  blue: '#3498db',
  green: '#2ecc71',
  orange: '#e67e22',
  purple: '#9b59b6',
  custom: customColor  // Personnalisé
};
```

### Usage dans l'éditeur

1. **Ajouter le bloc**
   - Tapez `/graphique` ou cherchez "Graphique en Barres D3" dans l'inserter

2. **Ajouter des données**
   - Cliquez sur "Ajouter une valeur"
   - Saisir étiquette et valeur numérique
   - Aperçu en temps réel (barres CSS)

3. **Configurer l'apparence**
   - Panneau de droite : titre, orientation, couleur
   - Options : valeurs, grille, animations

### Code d'exemple

```php
<!-- Bloc graphique barres -->
<!-- wp:archi-graph/d3-bar-chart {
  "title":"Projets par année",
  "data":[
    {"label":"2021","value":15},
    {"label":"2022","value":23},
    {"label":"2023","value":18},
    {"label":"2024","value":27}
  ],
  "orientation":"vertical",
  "colorScheme":"blue",
  "showValues":true,
  "showGrid":true,
  "height":400,
  "animate":true
} /-->
```

### Intégration D3.js

**Import ES modules :**
```javascript
import * as d3 from 'https://cdn.jsdelivr.net/npm/d3@7/+esm';
```

**Création du graphique :**
```javascript
// Échelles
const xScale = d3.scaleBand()
  .domain(data.map(d => d.label))
  .range([0, width])
  .padding(0.2);

const yScale = d3.scaleLinear()
  .domain([0, d3.max(data, d => d.value)])
  .range([height, 0]);

// Barres avec animation
svg.selectAll('.bar')
  .data(data)
  .join('rect')
  .attr('class', 'bar')
  .attr('fill', color)
  .attr('x', d => xScale(d.label))
  .attr('width', xScale.bandwidth())
  .attr('y', height)
  .attr('height', 0)
  .transition()
  .duration(800)
  .attr('y', d => yScale(d.value))
  .attr('height', d => height - yScale(d.value));
```

---

## 📅 Bloc Timeline (D3.js)

### Description

Timeline horizontale pour visualiser chronologiquement des événements de projet avec D3.js.

### Fichiers

```
assets/js/blocks/d3-timeline.jsx               # Composant React
inc/blocks/content/d3-timeline.php             # Rendu D3.js serveur
assets/css/visualization-blocks.css            # Styles CSS
```

### Fonctionnalités

✅ **Timeline chronologique**
- Ligne horizontale avec échelle temporelle
- Événements positionnés par date
- Tri automatique chronologique

✅ **Événements**
- Ajouter/modifier/supprimer des événements
- Date (date picker dans l'éditeur)
- Titre et description
- Couleur personnalisable par événement

✅ **Apparence**
- Titre de la timeline
- Hauteur personnalisable (200-600px)
- Cercles colorés pour chaque événement
- Labels positionnés dessus/dessous alternés

### Attributs

```javascript
{
  title: 'Historique du projet',  // Titre
  events: [                        // Événements
    {
      id: '1',
      title: 'Début du projet',
      date: '2023-01-15',           // Format YYYY-MM-DD
      description: 'Lancement',
      color: '#3498db'
    },
    {
      id: '2',
      title: 'Phase 2',
      date: '2023-06-20',
      description: 'Développement',
      color: '#2ecc71'
    }
  ],
  height: 300                      // Hauteur en pixels (200-600)
}
```

### Usage dans l'éditeur

1. **Ajouter le bloc**
   - Tapez `/timeline` ou cherchez "Timeline D3" dans l'inserter

2. **Ajouter des événements**
   - Cliquez sur "Ajouter un événement"
   - Sélectionner date avec date picker
   - Saisir titre et description
   - Choisir couleur

3. **Configurer**
   - Panneau de droite : titre et hauteur de la timeline
   - Événements triés automatiquement par date

### Code d'exemple

```php
<!-- Bloc timeline -->
<!-- wp:archi-graph/d3-timeline {
  "title":"Phases du projet",
  "events":[
    {
      "id":"1",
      "title":"Conception",
      "date":"2023-01-15",
      "description":"Phase de conception initiale",
      "color":"#3498db"
    },
    {
      "id":"2",
      "title":"Construction",
      "date":"2023-06-01",
      "description":"Début des travaux",
      "color":"#e67e22"
    },
    {
      "id":"3",
      "title":"Livraison",
      "date":"2023-12-20",
      "description":"Remise des clés",
      "color":"#2ecc71"
    }
  ],
  "height":300
} /-->
```

### Intégration D3.js

**Échelle temporelle :**
```javascript
const parseDate = d3.timeParse('%Y-%m-%d');
const dates = events.map(e => parseDate(e.date));

const xScale = d3.scaleTime()
  .domain([d3.min(dates), d3.max(dates)])
  .range([50, width - 50]);
```

**Rendu événements :**
```javascript
const eventGroups = svg.selectAll('.event')
  .data(sortedEvents)
  .join('g')
  .attr('class', 'event')
  .attr('transform', d => `translate(${xScale(parseDate(d.date))}, ${height/2})`);

// Cercles
eventGroups.append('circle')
  .attr('r', 6)
  .attr('fill', d => d.color);

// Texte
eventGroups.append('text')
  .attr('y', (d, i) => i % 2 === 0 ? -20 : 35)
  .text(d => d.title);
```

---

## 🎨 Styles CSS

### Structure

**Fichier :** `assets/css/visualization-blocks.css`

**Sections :**
1. Carte interactive (Leaflet)
2. Graphiques D3.js (barres + timeline)
3. Éléments communs (axes, grille, tooltips)
4. Responsive
5. Accessibilité

### Classes CSS principales

**Carte :**
```css
.archi-interactive-map-wrapper   /* Conteneur */
.archi-interactive-map            /* Carte Leaflet */
.custom-marker                    /* Marqueurs SVG */
```

**Graphiques D3 :**
```css
.archi-d3-bar-chart-wrapper       /* Conteneur graphique barres */
.archi-d3-bar-chart               /* SVG graphique */
.archi-d3-timeline-wrapper        /* Conteneur timeline */
.archi-d3-timeline                /* SVG timeline */
.chart-title / .timeline-title    /* Titres */
```

**Éléments D3 :**
```css
.bar                              /* Barres du graphique */
.event                            /* Événements timeline */
.grid                             /* Grille */
.domain, .tick                    /* Axes */
.value                            /* Valeurs sur barres */
```

### Responsive

```css
@media (max-width: 768px) {
  /* Réduction padding et marges */
  .archi-interactive-map-wrapper,
  .archi-d3-bar-chart-wrapper,
  .archi-d3-timeline-wrapper {
    padding: 1rem;
    margin: 1.5rem 0;
  }
  
  /* Tailles de police réduites */
  .chart-title,
  .timeline-title {
    font-size: 1.25rem;
  }
  
  .tick text,
  .value {
    font-size: 10px;
  }
}
```

### Accessibilité

```css
/* Désactiver animations si préférence réduite */
@media (prefers-reduced-motion: reduce) {
  .archi-d3-bar-chart .bar,
  .archi-d3-timeline .event circle {
    transition: none !important;
  }
}
```

---

## 🔧 Configuration technique

### Webpack

**Entrées ajoutées :**
```javascript
{
  entry: {
    'interactive-map': './assets/js/blocks/interactive-map.jsx',
    'd3-bar-chart': './assets/js/blocks/d3-bar-chart.jsx',
    'd3-timeline': './assets/js/blocks/d3-timeline.jsx'
  }
}
```

### Loader PHP

**Scripts enregistrés :**
```php
$block_scripts = [
  'interactive-map' => ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'],
  'd3-bar-chart' => ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'],
  'd3-timeline' => ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'],
];
```

**CSS enqueued :**
```php
wp_enqueue_style('archi-visualization-blocks', 
  get_template_directory_uri() . '/assets/css/visualization-blocks.css'
);

// Leaflet CSS (frontend uniquement)
if (!is_admin()) {
  wp_enqueue_style('leaflet', 
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
  );
}
```

### Dépendances externes

**Leaflet.js (Carte) :**
- Version : 1.9.4
- CDN : `https://unpkg.com/leaflet@1.9.4/dist/`
- Chargement : CSS frontend + JS inline dans renderer

**D3.js (Graphiques) :**
- Version : 7.9.0
- CDN : `https://cdn.jsdelivr.net/npm/d3@7/+esm`
- Import : ES modules (type="module")

---

## 🚀 Build et déploiement

### Compilation

```bash
cd /path/to/theme
npm run build
```

**Output :**
```
✅ interactive-map.bundle.js       8.85 KiB
✅ d3-bar-chart.bundle.js          7.04 KiB
✅ d3-timeline.bundle.js           6.42 KiB
```

### Mode développement

```bash
npm run dev   # Watch mode avec source maps
```

### Vérification

1. **Activer le thème** dans WordPress
2. **Créer/éditer une page** avec Gutenberg
3. **Chercher les blocs :**
   - Tapez `/carte` → Carte Interactive
   - Tapez `/graphique` → Graphique en Barres D3
   - Tapez `/timeline` → Timeline D3
4. **Tester les fonctionnalités** :
   - Ajouter marqueurs/données/événements
   - Changer styles/couleurs/options
   - Prévisualiser et publier

---

## 📝 Bonnes pratiques

### Performance

✅ **Chargement conditionnel**
- Leaflet CSS uniquement en frontend (`!is_admin()`)
- Scripts D3.js/Leaflet chargés uniquement si bloc présent

✅ **Optimisation bundles**
- Bundles minifiés en production
- Scripts séparés (pas de vendor unique)

✅ **Rendu serveur**
- `save: () => null` - pas de contenu dans bloc sauvegardé
- Rendu dynamique via `render_callback`

### Données

✅ **Validation**
- Sanitization des entrées utilisateur
- Valeurs par défaut pour tous les attributs
- Vérification types dans PHP

✅ **Sécurité**
```php
$latitude = floatval($attributes['latitude'] ?? 48.8566);
$zoom = intval($attributes['zoom'] ?? 13);
$title = esc_html($attributes['title'] ?? '');
```

### Accessibilité

✅ **Support clavier** : tous les contrôles accessibles au clavier
✅ **Labels ARIA** : attributs aria pour éléments SVG
✅ **Motion réduite** : désactivation animations si préférence utilisateur
✅ **Contraste** : couleurs par défaut avec bon contraste

### Compatibilité

✅ **Navigateurs** : Chrome, Firefox, Safari, Edge (2 dernières versions)
✅ **WordPress** : 6.0+
✅ **PHP** : 7.4+
✅ **Mobile** : Responsive, touch support (carte)

---

## 🐛 Dépannage

### La carte ne s'affiche pas

1. Vérifier que Leaflet CSS est chargé :
```
<!-- Dans le HTML frontend -->
<link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
```

2. Vérifier la console JavaScript :
```javascript
console.log(typeof L); // Doit être 'object'
```

3. Vérifier l'initialisation :
```javascript
// Doit avoir l'attribut data-initialized="true"
<div class="archi-interactive-map" data-initialized="true">
```

### Les graphiques D3 ne s'affichent pas

1. Vérifier l'import D3 :
```javascript
// Dans le HTML
<script type="module">
  import * as d3 from 'https://cdn.jsdelivr.net/npm/d3@7/+esm';
</script>
```

2. Vérifier la console :
```
// Pas d'erreur d3 is not defined
```

3. Vérifier les données :
```php
$data = $attributes['data'] ?? [];
if (empty($data)) {
  // Pas de graphique si pas de données
}
```

### Les blocs n'apparaissent pas dans l'éditeur

1. Vérifier le build :
```bash
npm run build
# Doit créer les 3 bundles dans dist/js/
```

2. Vérifier l'enregistrement :
```php
// Dans inc/blocks/_loader.php
error_log('Archi Block registered via: archi_register_interactive_map_block');
```

3. Vérifier les scripts :
```php
// Dans l'admin
wp_script_is('archi-interactive-map', 'enqueued'); // true
```

---

## 🎯 Prochaines améliorations

### Court terme
- [ ] Ajouter plus de styles de carte (Mapbox, CartoDB)
- [ ] Exporter données graphiques en CSV
- [ ] Tooltips personnalisables pour graphiques

### Moyen terme
- [ ] Graphiques supplémentaires (camembert, ligne, aire)
- [ ] Clustering de marqueurs pour carte
- [ ] Animation de timeline (lecture chronologique)

### Long terme
- [ ] Graphiques 3D (Three.js)
- [ ] Cartes interactives avancées (itinéraires, zones)
- [ ] Dashboard de visualisations multiples

---

## 📚 Ressources

### Documentation externe

**Leaflet.js :**
- Site officiel : https://leafletjs.com/
- Documentation : https://leafletjs.com/reference.html
- Exemples : https://leafletjs.com/examples.html

**D3.js :**
- Site officiel : https://d3js.org/
- Documentation API : https://d3js.org/api
- Galerie : https://observablehq.com/@d3/gallery

**WordPress Gutenberg :**
- Handbook : https://developer.wordpress.org/block-editor/
- Components : https://developer.wordpress.org/block-editor/reference-guides/components/
- Block API : https://developer.wordpress.org/block-editor/reference-guides/block-api/

### Exemples de code

**GitHub :**
- Leaflet : https://github.com/Leaflet/Leaflet
- D3 : https://github.com/d3/d3

**CodePen :**
- Recherche "Leaflet examples"
- Recherche "D3 charts examples"

---

## ✅ Checklist d'intégration

### Développement
- [x] Créer composants React pour l'éditeur
- [x] Créer renderers PHP serveur
- [x] Ajouter CSS responsive
- [x] Configurer webpack
- [x] Mettre à jour loader PHP
- [x] Build webpack réussi

### Tests
- [ ] Tester dans éditeur Gutenberg
- [ ] Tester rendu frontend
- [ ] Tester responsive (mobile/tablette)
- [ ] Tester accessibilité
- [ ] Tester performances

### Documentation
- [x] Documentation technique complète
- [x] Exemples de code
- [x] Guide dépannage
- [ ] Captures d'écran
- [ ] Vidéo tutoriel

### Déploiement
- [ ] Tests sur environnement staging
- [ ] Tests navigateurs multiples
- [ ] Validation W3C
- [ ] Tests performance (GTmetrix/Lighthouse)
- [ ] Déploiement production

---

**Dernière mise à jour :** 2024
**Version thème :** 1.0.5
**Statut :** ✅ Build réussi - Prêt pour tests
