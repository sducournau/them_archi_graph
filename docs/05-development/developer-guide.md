# 🎨 Système de Paramètres Avancés du Graphique - Guide Développeur

## 📦 Fichiers Créés

### PHP (Backend)
```
inc/
├── advanced-graph-settings.php      (825 lignes)  - Meta boxes & enregistrement
├── advanced-graph-rest-api.php      (315 lignes)  - Endpoints REST API
└── advanced-graph-migration.php     (445 lignes)  - Outil de migration
```

### JavaScript (Frontend)
```
assets/js/
├── utils/
│   ├── dataFetcher.js              (modifié)     - Valeurs par défaut avancées
│   └── advancedShapes.js           (660 lignes)  - Rendu formes & animations
└── examples/
    └── advanced-graph-integration.js (380 lignes)  - Guide d'intégration
```

### Documentation
```
docs/
├── advanced-graph-parameters.md     (520 lignes)  - Guide utilisateur complet
└── GRAPH-IMPROVEMENTS-SUMMARY.md    (420 lignes)  - Résumé exécutif
```

### Utilitaires
```
test-advanced-graph.sh               (150 lignes)  - Script de test automatisé
```

**Total : ~3715 lignes de code créées/modifiées** 🎉

---

## 🚀 Installation & Activation

### 1. Vérification de l'Installation

Les fichiers sont automatiquement chargés via `functions.php` :

```php
// Déjà ajouté dans functions.php
require_once ARCHI_THEME_DIR . '/inc/advanced-graph-settings.php';
require_once ARCHI_THEME_DIR . '/inc/advanced-graph-rest-api.php';
require_once ARCHI_THEME_DIR . '/inc/advanced-graph-migration.php';
```

### 2. Tester l'Installation

```bash
cd /mnt/c/wamp64/www/wordpress/wp-content/themes/archi-graph-template
./test-advanced-graph.sh
```

### 3. Accéder à la Migration

**Admin WordPress** → **Outils** → **Migration Graphique**

URL : `/wp-admin/tools.php?page=archi-advanced-migration`

---

## 🎯 Utilisation des Nouveaux Paramètres

### Dans l'Admin WordPress

#### Éditer un Article/Projet/Illustration

1. Ouvrir l'éditeur WordPress
2. Trouver la meta box **"⚙️ Paramètres Avancés du Graphique"**
3. Naviguer entre les onglets :
   - **🎨 Apparence** : Forme, icône, bordure, badge, opacité
   - **⚡ Comportement** : Poids, animations, labels, épinglage
   - **🔗 Connexions** : Profondeur, force, style des liens

#### Prévisualisation

La zone SVG en bas de la meta box montre le nœud en temps réel.

---

## 🔌 API REST

### Endpoints Disponibles

#### 1. Valeurs par Défaut

```http
GET /wp-json/archi/v1/graph-defaults
```

**Réponse :**
```json
{
  "post": {
    "node_color": "#3498db",
    "node_size": 60,
    "node_shape": "circle",
    "hover_effect": "zoom"
  },
  "archi_project": {
    "node_color": "#e74c3c",
    "node_size": 80,
    "node_shape": "square",
    "hover_effect": "glow"
  },
  "shapes": {
    "circle": {...},
    "square": {...}
  }
}
```

#### 2. Statistiques du Graphique

```http
GET /wp-json/archi/v1/graph-stats
Authorization: Bearer {token}
```

**Réponse :**
```json
{
  "total_nodes": 45,
  "nodes_by_type": [
    {"post_type": "post", "count": 20},
    {"post_type": "archi_project", "count": 15}
  ],
  "shapes_distribution": [
    {"shape": "circle", "count": 25},
    {"shape": "square", "count": 15}
  ],
  "visual_groups": [
    {"group_name": "Architecture", "count": 18}
  ],
  "badges_used": [
    {"badge": "featured", "count": 8}
  ],
  "pinned_nodes": 3,
  "total_connections": 120
}
```

#### 3. Données d'Article avec Paramètres

```http
GET /wp-json/wp/v2/posts/123
```

**Réponse inclut :**
```json
{
  "id": 123,
  "title": "...",
  "advanced_graph_params": {
    "node_shape": "diamond",
    "node_icon": "🏗️",
    "visual_group": "Architecture",
    "node_opacity": 0.9,
    "node_border": "glow",
    "border_color": "#e74c3c",
    "node_weight": 5,
    "hover_effect": "zoom",
    "entrance_animation": "scale",
    "pin_node": false,
    "node_label": "Projet phare",
    "show_label": true,
    "node_badge": "featured",
    "connection_depth": 3,
    "link_strength": 1.5,
    "link_style": "curve"
  }
}
```

---

## 💻 Intégration JavaScript

### 1. Importer les Utilitaires

```javascript
import { fetchGraphData, validateArticleData } from './utils/dataFetcher.js';
import {
  createNodeShape,
  applyNodeBorder,
  addNodeIcon,
  addNodeBadge,
  addNodeLabel,
  applyEntranceAnimation,
  applyHoverEffect,
  applyLinkStyle
} from './utils/advancedShapes.js';
```

### 2. Créer un Nœud avec Forme Personnalisée

```javascript
// Pour chaque nœud dans D3.js
nodes.each(function(d) {
  const nodeGroup = d3.select(this);
  
  // 1. Créer la forme (automatiquement selon node_shape)
  const shape = createNodeShape(nodeGroup, d);
  
  // 2. Appliquer la couleur et l'opacité
  shape
    .attr('fill', d.node_color || '#3498db')
    .attr('opacity', d.advanced_graph_params?.node_opacity || 1.0);
  
  // 3. Appliquer la bordure
  applyNodeBorder(shape, d);
  
  // 4. Ajouter l'icône
  addNodeIcon(nodeGroup, d);
  
  // 5. Ajouter le badge
  addNodeBadge(nodeGroup, d);
  
  // 6. Ajouter le label
  addNodeLabel(nodeGroup, d);
});
```

### 3. Appliquer les Animations

```javascript
// Animation d'entrée (lors de la création du graphique)
nodes.each(function(d, i) {
  applyEntranceAnimation(
    d3.select(this),
    d,
    i * 50  // Décalage de 50ms entre chaque nœud
  );
});

// Effet au survol
nodes
  .on('mouseenter', function(event, d) {
    applyHoverEffect(d3.select(this), d, true);
  })
  .on('mouseleave', function(event, d) {
    applyHoverEffect(d3.select(this), d, false);
  });
```

### 4. Gérer les Liens Personnalisés

```javascript
// Appliquer le style aux liens
linkElements.each(function(d) {
  applyLinkStyle(d3.select(this), d.source);
});

// Créer des liens selon connection_depth
const createLinks = (node, allNodes, visited = new Set()) => {
  const depth = node.advanced_graph_params?.connection_depth || 2;
  const links = [];
  
  // Logique de création basée sur depth...
  
  return links;
};
```

### 5. Groupement Visuel

```javascript
import { groupNodesByVisualGroup } from './utils/advancedShapes.js';

// Grouper les nœuds
const groups = groupNodesByVisualGroup(articles);

// Utiliser dans la simulation
Object.keys(groups).forEach(groupName => {
  const groupNodes = groups[groupName];
  
  // Appliquer une force de cluster spécifique au groupe
  simulation.force(`group-${groupName}`, 
    d3.forceCluster()
      .nodes(groupNodes)
      .strength(0.3)
  );
});
```

---

## 🎨 Paramètres Disponibles

### Apparence (7 paramètres)

| Paramètre | Type | Valeurs | Description |
|-----------|------|---------|-------------|
| `node_shape` | string | circle, square, diamond, triangle, star, hexagon | Forme du nœud |
| `node_icon` | string | Emoji/Unicode | Icône affichée au centre |
| `visual_group` | string | Texte libre | Nom du groupe visuel |
| `node_opacity` | float | 0.1 - 1.0 | Transparence |
| `node_border` | string | none, solid, dashed, dotted, glow | Style de bordure |
| `border_color` | string | Hex color | Couleur de la bordure |
| `node_badge` | string | '', new, featured, hot, updated, popular | Badge visuel |

### Comportement (6 paramètres)

| Paramètre | Type | Valeurs | Description |
|-----------|------|---------|-------------|
| `node_weight` | int | 1 - 10 | Poids dans simulation |
| `hover_effect` | string | none, zoom, pulse, glow, rotate, bounce | Effet au survol |
| `entrance_animation` | string | none, fade, scale, slide, bounce | Animation d'entrée |
| `pin_node` | boolean | true, false | Position fixe |
| `node_label` | string | Max 20 char | Label court personnalisé |
| `show_label` | boolean | true, false | Affichage permanent du label |

### Connexions (3 paramètres)

| Paramètre | Type | Valeurs | Description |
|-----------|------|---------|-------------|
| `connection_depth` | int | 1 - 5 | Niveaux de connexions |
| `link_strength` | float | 0.1 - 3.0 | Force/épaisseur des liens |
| `link_style` | string | straight, curve, wave, dotted, dashed | Style visuel des liens |

---

## 🧪 Tests

### Test Automatisé

```bash
./test-advanced-graph.sh
```

**Vérifie :**
- ✅ Endpoints REST API accessibles
- ✅ Fichiers PHP créés et syntaxe valide
- ✅ Intégration dans functions.php
- ✅ Statistiques du code

### Tests Manuels

#### 1. Interface Admin

```
1. Créer/éditer un article
2. Ouvrir "Paramètres Avancés du Graphique"
3. Tester chaque onglet (Apparence, Comportement, Connexions)
4. Vérifier la prévisualisation SVG
5. Sauvegarder et recharger → valeurs conservées
```

#### 2. API REST

```bash
# Test endpoint defaults
curl http://localhost/wordpress/wp-json/archi/v1/graph-defaults

# Test avec authentification
curl -H "Authorization: Bearer TOKEN" \
     http://localhost/wordpress/wp-json/archi/v1/graph-stats

# Test données article
curl http://localhost/wordpress/wp-json/wp/v2/posts/123
```

#### 3. Graphique D3.js

```
1. Charger la page avec le graphique
2. Vérifier les formes personnalisées
3. Tester les effets au survol
4. Vérifier les animations d'entrée
5. Tester le drag & drop avec épinglage
6. Vérifier les liens personnalisés
```

---

## 🐛 Débogage

### Logs WordPress

```php
// Activer les logs
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Vérifier les logs
tail -f wp-content/debug.log
```

### Console JavaScript

```javascript
// Vérifier les données chargées
console.log('Articles:', articles);
console.log('Paramètres avancés:', articles[0].advanced_graph_params);

// Vérifier les groupes
import { groupNodesByVisualGroup } from './utils/advancedShapes.js';
const groups = groupNodesByVisualGroup(articles);
console.log('Groupes visuels:', groups);
```

### Outils Navigateur

```
F12 → Network → Filtrer "archi"
- Vérifier les requêtes API
- Vérifier les réponses JSON
- Vérifier les temps de chargement
```

---

## 📚 Exemples de Code

### Exemple 1 : Mettre en Avant un Projet Important

```php
$post_id = 123;

update_post_meta($post_id, '_archi_node_shape', 'star');
update_post_meta($post_id, '_archi_node_size', 120);
update_post_meta($post_id, '_archi_node_badge', 'featured');
update_post_meta($post_id, '_archi_hover_effect', 'glow');
update_post_meta($post_id, '_archi_node_border', 'glow');
update_post_meta($post_id, '_archi_border_color', '#f39c12');
update_post_meta($post_id, '_archi_pin_node', '1');
```

### Exemple 2 : Créer des Groupes Thématiques

```php
// Groupe Architecture
$architecture_posts = get_posts(['category_name' => 'architecture']);

foreach ($architecture_posts as $post) {
    update_post_meta($post->ID, '_archi_visual_group', 'Architecture');
    update_post_meta($post->ID, '_archi_node_shape', 'square');
    update_post_meta($post->ID, '_archi_node_color', '#e74c3c');
    update_post_meta($post->ID, '_archi_node_icon', '🏗️');
}

// Groupe Design
$design_posts = get_posts(['category_name' => 'design']);

foreach ($design_posts as $post) {
    update_post_meta($post->ID, '_archi_visual_group', 'Design');
    update_post_meta($post->ID, '_archi_node_shape', 'diamond');
    update_post_meta($post->ID, '_archi_node_color', '#f39c12');
    update_post_meta($post->ID, '_archi_node_icon', '🎨');
}
```

### Exemple 3 : Animation Personnalisée en JavaScript

```javascript
// Animation spéciale pour les projets "featured"
nodes.filter(d => d.advanced_graph_params?.node_badge === 'featured')
  .each(function(d) {
    const node = d3.select(this);
    
    // Pulsation continue
    function pulse() {
      node.transition()
        .duration(1000)
        .attr('transform', `translate(${d.x},${d.y}) scale(1.1)`)
        .transition()
        .duration(1000)
        .attr('transform', `translate(${d.x},${d.y}) scale(1)`)
        .on('end', pulse);
    }
    
    pulse();
  });
```

---

## 🔄 Migration depuis l'Ancien Système

### Compatibilité

✅ **Tous les anciens paramètres continuent de fonctionner :**

- `_archi_show_in_graph`
- `_archi_node_color`
- `_archi_node_size`
- `_archi_priority_level`
- `_archi_graph_position`
- `_archi_related_articles`
- `_archi_hide_links`

Les nouveaux paramètres s'ajoutent sans conflit.

### Outil de Migration Automatique

**Admin** → **Outils** → **Migration Graphique**

Applique automatiquement :
- Formes par défaut selon le type de contenu
- Groupes visuels basés sur catégories
- Icônes par défaut (🏗️, 🎨, 📄)
- Badges pour articles récents
- Animations adaptées au type

---

## 🚀 Prochaines Étapes (Roadmap)

### Phase 2 : Interface Gutenberg (Proposée)
- [ ] Bloc Gutenberg pour configuration dans l'éditeur
- [ ] Prévisualisation en direct du nœud
- [ ] Sélecteur visuel intégré

### Phase 3 : Analytics (Proposée)
- [ ] Dashboard WordPress des statistiques
- [ ] Visualisation des groupes visuels
- [ ] Export analytics CSV/PDF

### Phase 4 : Préréglages (Proposée)
- [ ] Templates de configuration
- [ ] Import/Export de configurations
- [ ] Application en masse

---

## 💡 Conseils de Performance

### Optimisations Recommandées

1. **Limiter les nœuds** : Pour >100 nœuds, utiliser la pagination
2. **Caching** : Les données sont mises en cache (transient 1h)
3. **Lazy Loading** : Charger les images progressivement
4. **Connection Depth** : Limiter à ≤3 niveaux pour grands graphiques
5. **Animations** : Désactiver sur mobile/grands graphiques

### Monitoring

```javascript
// Mesurer les performances
console.time('Graph Init');
const graph = await initAdvancedGraph(...);
console.timeEnd('Graph Init');

// Surveiller la simulation
simulation.on('tick', () => {
  performance.mark('tick');
});
```

---

## 📞 Support

### Documentation

- **Guide utilisateur** : `docs/advanced-graph-parameters.md`
- **Résumé** : `docs/GRAPH-IMPROVEMENTS-SUMMARY.md`
- **Exemples** : `assets/js/examples/advanced-graph-integration.js`

### Ressources Externes

- [D3.js Documentation](https://d3js.org/)
- [WordPress REST API](https://developer.wordpress.org/rest-api/)
- [SVG Shapes Reference](https://developer.mozilla.org/docs/Web/SVG)

### Liens Utiles

- Repo GitHub : [archi-graph-template](https://github.com/...)
- Issues : [GitHub Issues](https://github.com/.../issues)
- Wiki : [Documentation Wiki](https://github.com/.../wiki)

---

**Version : 1.0.0** | **Date : Novembre 2025** | **Auteur : Équipe Archi-Graph**
