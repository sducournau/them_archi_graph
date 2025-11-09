# 📊 Graph System - Vue d'ensemble

## Introduction

Le système de graphique interactif est le cœur du thème Archi Graph. Il utilise D3.js pour créer une visualisation force-directed qui représente les articles, projets et illustrations comme des nœuds interconnectés.

---

## 🎯 Concepts Clés

### Nœuds (Nodes)

Chaque article publié peut apparaître comme un **nœud** dans le graphique :

| Type de Post | Icône | Couleur par Défaut |
|--------------|-------|---------------------|
| Article (post) | 📝 | Bleu (#3498db) |
| Projet (archi_project) | 🏗️ | Vert (#2ecc71) |
| Illustration (archi_illustration) | 🎨 | Violet (#9b59b6) |

### Liens (Links)

Les **liens** connectent les nœuds selon plusieurs facteurs :

| Facteur | Poids | Description |
|---------|-------|-------------|
| Catégories partagées | 40 pts | Même catégorie assignée |
| Tags communs | 25 pts | Tags partagés |
| Catégorie principale | 20 pts | Même catégorie principale |
| Proximité temporelle | 0-10 pts | Publié à peu près au même moment |
| Similarité contenu | 0-5 pts | Contenu similaire |

**Score total minimum pour créer un lien** : 30 points

### Forces de Simulation

Le graphique utilise une simulation physique D3.js :

```javascript
const simulation = d3.forceSimulation(nodes)
  .force('link', d3.forceLink(links)
    .id(d => d.id)
    .distance(150)        // Distance entre nœuds liés
    .strength(0.3)        // Force des liens
  )
  .force('charge', d3.forceManyBody()
    .strength(-400)       // Répulsion entre nœuds
  )
  .force('center', d3.forceCenter()
    .x(width / 2)
    .y(height / 2)
  )
  .force('collision', d3.forceCollide()
    .radius(60)           // Rayon de collision
  );
```

---

## 🎨 Métadonnées de Nœud

Chaque nœud peut être personnalisé via les **métadonnées post** :

### Dans l'Éditeur WordPress

**Méta-box "Paramètres du Graphique"** :

```
☑ Afficher dans le graphique
🎨 Couleur du nœud : [sélecteur de couleur]
📏 Taille du nœud : [slider 40-120px]
⭐ Priorité : [Faible | Normale | Élevée | Featured]
🔗 Articles liés : [sélecteur multi-articles]
```

### Valeurs des Métadonnées

| Méta Clé | Type | Défaut | Description |
|----------|------|--------|-------------|
| `_archi_show_in_graph` | string | '0' | '1' = visible, '0' = caché |
| `_archi_node_color` | string | '#3498db' | Couleur hexadécimale |
| `_archi_node_size` | int | 60 | Taille en pixels (40-120) |
| `_archi_priority_level` | string | 'normal' | low, normal, high, featured |
| `_archi_related_articles` | array | [] | IDs d'articles liés manuellement |
| `_archi_graph_position` | array | null | Position {x, y} sauvegardée |

---

## 🚀 Fonctionnalités Principales

### 1. Interactions Utilisateur

#### Survol (Hover)
- Affiche tooltip avec infos article
- Met en surbrillance les liens connectés
- Anime les GIFs (si activé)
- Affiche metadata du nœud

#### Clic (Click)
- Sélectionne le nœud (agrandissement 2.5x)
- Maintient l'animation GIF
- Affiche panel latéral avec détails
- Options : Voir article, Éditer (si admin)

#### Drag & Drop
- Déplacer les nœuds manuellement
- La simulation se réajuste
- Option de sauvegarde de position
- Double-clic pour libérer

#### Zoom & Pan
- Molette souris pour zoom
- Drag sur fond pour panoramique
- Pinch-to-zoom sur mobile
- Limites configurables

### 2. Filtrage et Recherche

#### Filtres par Catégorie
```javascript
// Afficher seulement catégorie "Architecture"
const filtered = nodes.filter(n => 
  n.categories.includes('architecture')
);
updateGraph(filtered);
```

#### Recherche par Texte
```javascript
// Rechercher dans titres
const results = nodes.filter(n => 
  n.title.toLowerCase().includes(query.toLowerCase())
);
highlightNodes(results);
```

#### Filtres par Type
- Afficher seulement projets
- Afficher seulement illustrations
- Afficher tout sauf articles

### 3. Affichage et Layout

#### Modes de Visualisation
| Mode | Description | Usage |
|------|-------------|-------|
| **Force-Directed** | Layout physique automatique | Par défaut |
| **Islands** | Groupement par catégories | Meilleure organisation |
| **Radial** | Disposition radiale | Vue hiérarchique |
| **Grid** | Grille ordonnée | Consultation méthodique |

#### Niveaux de Détail
- **Minimal** : Nœuds simples
- **Normal** : Nœuds avec images
- **Détaillé** : Images + labels + metadata

---

## 🔧 Configuration

### Options Globales

Accessibles via **Apparence → Archi Graph Settings → Graph** :

| Option | Type | Défaut | Description |
|--------|------|--------|-------------|
| Canvas Width | int | 1920 | Largeur en pixels |
| Canvas Height | int | 1080 | Hauteur en pixels |
| Animation Speed | float | 1.0 | Vitesse simulation (0.5-2.0) |
| Link Opacity | float | 0.6 | Opacité des liens (0.0-1.0) |
| Node Base Size | int | 60 | Taille par défaut des nœuds |
| Zoom Min | float | 0.5 | Zoom minimal |
| Zoom Max | float | 3.0 | Zoom maximal |
| Auto-Save Positions | bool | false | Sauver positions automatiquement |

### Configuration Avancée

Voir documentation détaillée : [advanced-parameters.md](advanced-parameters.md)

---

## 📡 API REST

### Endpoints Principaux

#### GET `/wp-json/archi/v1/articles`
Récupère tous les articles pour le graphique

**Réponse** :
```json
{
  "articles": [
    {
      "id": 123,
      "title": "Mon Projet",
      "post_type": "archi_project",
      "thumbnail": "https://...",
      "show_in_graph": true,
      "node_color": "#3498db",
      "node_size": 60,
      "priority_level": "high",
      "categories": [1, 5],
      "tags": [12, 45],
      "related_articles": [45, 67],
      "metadata": { ... }
    }
  ],
  "total": 42
}
```

#### GET `/wp-json/archi/v1/categories`
Récupère catégories avec couleurs

```json
{
  "categories": [
    {
      "id": 1,
      "name": "Architecture",
      "color": "#e74c3c",
      "count": 15
    }
  ]
}
```

#### POST `/wp-json/archi/v1/save-positions`
Sauvegarde les positions des nœuds

**Payload** :
```json
{
  "positions": {
    "123": { "x": 450, "y": 300 },
    "124": { "x": 550, "y": 400 }
  }
}
```

Documentation complète : [api-reference.md](../05-development/api-reference.md)

---

## 🎨 Personnalisation Visuelle

### CSS Variables

```css
:root {
  /* Nœuds */
  --graph-node-size: 60px;
  --graph-node-border: 2px;
  --graph-node-shadow: 0 2px 8px rgba(0,0,0,0.2);
  
  /* Liens */
  --graph-link-color: #95a5a6;
  --graph-link-width: 2px;
  --graph-link-opacity: 0.6;
  
  /* Sélection */
  --graph-selected-scale: 2.5;
  --graph-selected-glow: 0 0 20px rgba(52,152,219,0.8);
  
  /* Hover */
  --graph-hover-opacity: 1.0;
  --graph-hover-transform: scale(1.1);
}
```

### Classes CSS Personnalisées

```css
/* Nœud custom */
.graph-node.custom-style {
  border-radius: 50%;
  filter: drop-shadow(0 0 10px var(--custom-color));
}

/* Lien custom */
.graph-link.strong-connection {
  stroke-width: 4px;
  stroke: #e74c3c;
}

/* Animation custom */
@keyframes pulse-node {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.1); }
}

.graph-node.pulsing {
  animation: pulse-node 2s infinite;
}
```

---

## 🔍 Système de Proximité

Le système calcule automatiquement les relations entre articles.

### Algorithme de Base

```javascript
function calculateProximity(articleA, articleB) {
  let score = 0;
  
  // Catégories partagées
  const sharedCats = intersection(articleA.categories, articleB.categories);
  score += sharedCats.length * 40;
  
  // Tags partagés
  const sharedTags = intersection(articleA.tags, articleB.tags);
  score += sharedTags.length * 25;
  
  // Catégorie principale identique
  if (articleA.mainCategory === articleB.mainCategory) {
    score += 20;
  }
  
  // Proximité temporelle (max 10 pts)
  const daysDiff = Math.abs(articleA.date - articleB.date) / (1000 * 60 * 60 * 24);
  score += Math.max(0, 10 - daysDiff / 30);
  
  // Similarité contenu (max 5 pts)
  score += calculateContentSimilarity(articleA.content, articleB.content);
  
  return score;
}
```

### Système de Proximité Amélioré

Pour des calculs plus sophistiqués, voir : [proximity-system.md](proximity-system.md)

---

## 📱 Responsive & Mobile

### Adaptations Mobile

| Fonctionnalité | Desktop | Mobile |
|----------------|---------|--------|
| Contrôles | Souris | Touch |
| Zoom | Molette | Pinch |
| Pan | Drag fond | Swipe |
| Tooltip | Hover | Tap |
| Sélection | Clic | Tap |

### Media Queries

```css
/* Tablet */
@media (max-width: 1024px) {
  .graph-container {
    height: 70vh;
  }
  .graph-node {
    --node-size: 50px;
  }
}

/* Mobile */
@media (max-width: 768px) {
  .graph-container {
    height: 60vh;
  }
  .graph-node {
    --node-size: 40px;
  }
  .graph-controls {
    bottom: 10px;
    right: 10px;
  }
}
```

---

## 🐛 Dépannage Courant

### Graphique Vide

**Symptômes** : Aucun nœud n'apparaît

**Solutions** :
1. Vérifier que des articles ont `_archi_show_in_graph = '1'`
2. Vérifier l'API : `/wp-json/archi/v1/articles`
3. Vérifier console pour erreurs JavaScript
4. Flush cache : `utilities/maintenance/clear-wp-cache.php`

### Nœuds Statiques

**Symptômes** : Les nœuds ne bougent pas

**Solutions** :
1. Vérifier que la simulation est démarrée
2. Vérifier `alpha` de la simulation (devrait être > 0)
3. Relancer simulation : `simulation.restart()`

### Performance Dégradée

**Symptômes** : Lag, FPS bas

**Solutions** :
1. Réduire nombre de nœuds affichés
2. Désactiver GIF auto-play
3. Réduire complexité des forces
4. Utiliser mode minimal

Voir guide complet : [troubleshooting.md](../05-development/troubleshooting.md)

---

## 📚 Documentation Liée

### Graph System
- [Paramètres Avancés](advanced-parameters.md)
- [Système de Proximité](proximity-system.md)
- [Contrôle GIF](gif-animation-control.md)
- [Éditeur Graphique](editor-interface.md)
- [Îles Organiques](organic-islands.md)

### Développement
- [Guide Développeur](../05-development/developer-guide.md)
- [Référence API](../05-development/api-reference.md)
- [Build Process](../05-development/build-process.md)

### Customization
- [Configuration Couleurs](../04-customization/colors-configuration.md)
- [Préparation Images](../04-customization/image-preparation.md)

---

## ✅ Checklist d'Utilisation

### Configuration Initiale
- [ ] Activer thème
- [ ] Créer quelques articles avec images
- [ ] Cocher "Afficher dans le graphique"
- [ ] Assigner catégories
- [ ] Visiter page d'accueil

### Personnalisation
- [ ] Choisir couleurs de nœuds
- [ ] Ajuster tailles de nœuds
- [ ] Définir priorités
- [ ] Créer relations manuelles (optionnel)

### Optimisation
- [ ] Tester performance avec 50+ nœuds
- [ ] Ajuster paramètres de force
- [ ] Configurer zoom limites
- [ ] Optimiser images (PNG transparent)

---

**Version** : 1.1.0  
**Dernière mise à jour** : 4 novembre 2025

**Next** : [Paramètres Avancés →](advanced-parameters.md)
