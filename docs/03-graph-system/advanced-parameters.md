# 🎨 Paramètres Avancés du Graphique - Guide Complet

## Vue d'ensemble

Le système de paramètres avancés du graphique vous permet de personnaliser finement l'apparence et le comportement de chaque nœud dans la visualisation D3.js.

## 📋 Fonctionnalités Ajoutées

### 1. **Apparence Visuelle**

#### Forme du Nœud
- ✅ **6 formes disponibles** : Cercle, Carré, Diamant, Triangle, Étoile, Hexagone
- Sélection visuelle avec icônes
- Chaque forme peut être personnalisée par article

#### Icône Personnalisée
- ✅ **Support des emojis et Unicode**
- Exemples : 🏗️ (architecture), 🎨 (design), 📐 (urbanisme)
- Affichée au centre du nœud

#### Opacité
- ✅ **Contrôle précis** : 10% à 100%
- Slider interactif avec affichage en temps réel
- Utile pour mettre en avant certains éléments

#### Bordures
- ✅ **5 styles** : Aucune, Solide, Tirets, Points, Lueur
- Sélecteur de couleur pour la bordure
- Effet "glow" pour les éléments importants

#### Badges
- ✅ **6 types de badges** :
  - 🆕 Nouveau
  - ⭐ À la une
  - 🔥 Populaire
  - 🔄 Mis à jour
  - 💎 Tendance
- Badge visuel sur le nœud

### 2. **Groupes Visuels**

#### Organisation par Groupes
- ✅ **Regroupement automatique** des nœuds similaires
- Exemples : "Architecture", "Design", "Urbanisme"
- Les nœuds du même groupe sont visuellement rapprochés

#### Statistiques
- Endpoint REST pour voir la distribution des groupes
- Tableau de bord avec analytics (à venir)

### 3. **Comportement et Interactions**

#### Poids du Nœud
- ✅ **Échelle 1-10**
- Influence la simulation physique D3.js
- Nœuds plus lourds = plus stables

#### Animations au Survol
- ✅ **6 effets disponibles** :
  - 🔍 Zoom
  - 💓 Pulsation
  - ✨ Lueur
  - 🔄 Rotation
  - ⬆️ Rebond
  - ❌ Aucun

#### Animations d'Entrée
- ✅ **5 types** :
  - 🌫️ Fondu
  - 📏 Échelle
  - ➡️ Glissement
  - 🎾 Rebond
  - ❌ Aucune

#### Position Épinglée
- ✅ **Fixer un nœud** à sa position actuelle
- Le nœud n'est plus affecté par la simulation
- Utile pour créer des points d'ancrage

#### Labels Personnalisés
- ✅ **Label court** (max 20 caractères)
- Option d'affichage permanent ou au survol
- Alternative au titre complet

### 4. **Connexions et Relations**

#### Profondeur des Connexions
- ✅ **1 à 5 niveaux**
- Contrôle la portée des relations affichées
- Réduit la complexité visuelle

#### Force des Liens
- ✅ **Échelle 0.1x à 3.0x**
- Influence l'épaisseur visuelle
- Impact sur la simulation physique

#### Style des Liens
- ✅ **5 styles disponibles** :
  - ─ Droite
  - ╰ Courbe
  - 〰 Vague
  - ⋯ Pointillés
  - ╌ Tirets

## 🚀 Utilisation

### Interface Admin

#### Accès
1. Éditer un article, projet ou illustration
2. Trouver la meta box **"⚙️ Paramètres Avancés du Graphique"**
3. Naviguer entre les 3 onglets :
   - 🎨 **Apparence** - Forme, couleur, bordure, badge
   - ⚡ **Comportement** - Poids, animations, labels
   - 🔗 **Connexions** - Profondeur, force, style des liens

#### Prévisualisation
- Une zone de prévisualisation SVG montre le nœud en temps réel
- Mise à jour automatique lors des changements

### Via API REST

#### Récupérer les Paramètres

```http
GET /wp-json/wp/v2/posts/123
```

Réponse inclut :
```json
{
  "id": 123,
  "title": {...},
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

#### Mettre à Jour les Paramètres

```http
POST /wp-json/wp/v2/posts/123
Content-Type: application/json

{
  "advanced_graph_params": {
    "node_shape": "star",
    "node_badge": "hot",
    "hover_effect": "glow"
  }
}
```

#### Obtenir les Valeurs par Défaut

```http
GET /wp-json/archi/v1/graph-defaults
```

Réponse :
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
  "shapes": {...},
  "animations": {...}
}
```

#### Statistiques du Graphique

```http
GET /wp-json/archi/v1/graph-stats
```

Réponse :
```json
{
  "total_nodes": 45,
  "nodes_by_type": [
    {"post_type": "post", "count": 20},
    {"post_type": "archi_project", "count": 15},
    {"post_type": "archi_illustration", "count": 10}
  ],
  "shapes_distribution": [
    {"shape": "circle", "count": 25},
    {"shape": "square", "count": 15},
    {"shape": "diamond", "count": 5}
  ],
  "visual_groups": [
    {"group_name": "Architecture", "count": 18},
    {"group_name": "Design", "count": 12}
  ],
  "badges_used": [
    {"badge": "featured", "count": 8},
    {"badge": "new", "count": 5}
  ],
  "pinned_nodes": 3,
  "total_connections": 120
}
```

## 🎯 Cas d'Usage

### 1. Mettre en Avant un Projet Important

```php
update_post_meta($post_id, '_archi_node_shape', 'star');
update_post_meta($post_id, '_archi_node_size', 120);
update_post_meta($post_id, '_archi_node_badge', 'featured');
update_post_meta($post_id, '_archi_hover_effect', 'glow');
update_post_meta($post_id, '_archi_node_border', 'glow');
update_post_meta($post_id, '_archi_border_color', '#f39c12');
```

### 2. Créer des Groupes Thématiques

```php
// Groupe Architecture
update_post_meta($post_id, '_archi_visual_group', 'Architecture');
update_post_meta($post_id, '_archi_node_shape', 'square');
update_post_meta($post_id, '_archi_node_color', '#e74c3c');

// Groupe Design
update_post_meta($post_id, '_archi_visual_group', 'Design');
update_post_meta($post_id, '_archi_node_shape', 'diamond');
update_post_meta($post_id, '_archi_node_color', '#f39c12');
```

### 3. Nœuds Discrets vs Nœuds Proéminents

```php
// Nœud discret
update_post_meta($post_id, '_archi_node_opacity', 0.5);
update_post_meta($post_id, '_archi_node_size', 40);
update_post_meta($post_id, '_archi_hover_effect', 'none');

// Nœud proéminent
update_post_meta($post_id, '_archi_node_opacity', 1.0);
update_post_meta($post_id, '_archi_node_size', 100);
update_post_meta($post_id, '_archi_hover_effect', 'pulse');
update_post_meta($post_id, '_archi_entrance_animation', 'bounce');
```

### 4. Liens Forts vs Liens Faibles

```php
// Lien fort (relations principales)
update_post_meta($post_id, '_archi_link_strength', 2.5);
update_post_meta($post_id, '_archi_link_style', 'solid');

// Lien faible (relations secondaires)
update_post_meta($post_id, '_archi_link_strength', 0.5);
update_post_meta($post_id, '_archi_link_style', 'dotted');
```

## 🔧 Intégration JavaScript

### Utiliser les Paramètres dans D3.js

```javascript
// Dans assets/js/utils/graphHelpers.js

// Appliquer la forme du nœud
function renderNode(selection, data) {
  const shape = data.advanced_graph_params?.node_shape || 'circle';
  
  switch(shape) {
    case 'circle':
      return selection.append('circle')
        .attr('r', data.node_size / 2);
    
    case 'square':
      return selection.append('rect')
        .attr('width', data.node_size)
        .attr('height', data.node_size)
        .attr('x', -data.node_size / 2)
        .attr('y', -data.node_size / 2);
    
    case 'diamond':
      return selection.append('polygon')
        .attr('points', getDiamondPoints(data.node_size));
    
    // ... autres formes
  }
}

// Appliquer l'animation au survol
function applyHoverEffect(node, effect) {
  switch(effect) {
    case 'zoom':
      node.transition()
        .duration(200)
        .attr('transform', 'scale(1.2)');
      break;
    
    case 'pulse':
      node.transition()
        .duration(300)
        .ease(d3.easeSinInOut)
        .attr('opacity', 0.7)
        .transition()
        .duration(300)
        .attr('opacity', 1);
      break;
    
    case 'glow':
      node.attr('filter', 'url(#glow-filter)');
      break;
    
    // ... autres effets
  }
}

// Appliquer le groupement visuel
simulation
  .force('group', d3.forceCluster()
    .groups(d => d.advanced_graph_params?.visual_group)
    .strength(0.5));
```

## 📊 Performance et Optimisation

### Mise en Cache
- Les paramètres avancés sont inclus dans le transient `archi_graph_articles`
- Invalidation automatique lors de la sauvegarde
- Durée de cache : 1 heure

### Requêtes Optimisées
- Un seul champ REST `advanced_graph_params` pour tous les paramètres
- Pas de requêtes multiples pour chaque meta
- Lazy loading dans l'interface admin

### Recommandations
- ✅ Utiliser des groupes visuels pour réduire la complexité
- ✅ Limiter la profondeur de connexion (≤3 niveaux)
- ✅ Épingler les nœuds centraux pour stabiliser le graphique
- ⚠️ Éviter trop de badges (impact visuel)
- ⚠️ Limiter les effets d'animation sur grands graphiques (>100 nœuds)

## 🐛 Débogage

### Vérifier les Valeurs Enregistrées

```php
$post_id = 123;
$params = archi_get_advanced_graph_params(['id' => $post_id]);
var_dump($params);
```

### Logs WordPress

```php
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('Advanced Graph Params: ' . print_r($params, true));
}
```

### Test REST API

```bash
# Tester l'endpoint
curl -X GET "https://votre-site.com/wp-json/archi/v1/graph-defaults"

# Tester les statistiques (avec authentification)
curl -X GET "https://votre-site.com/wp-json/archi/v1/graph-stats" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🔄 Migration depuis l'Ancien Système

### Paramètres Existants Conservés
Tous les anciens paramètres continuent de fonctionner :
- `_archi_show_in_graph` ✅
- `_archi_node_color` ✅
- `_archi_node_size` ✅
- `_archi_priority_level` ✅
- `_archi_graph_position` ✅
- `_archi_related_articles` ✅
- `_archi_hide_links` ✅

### Nouveaux Paramètres Additionnels
Les nouveaux paramètres s'ajoutent sans conflit :
- `_archi_node_shape` 🆕
- `_archi_node_icon` 🆕
- `_archi_visual_group` 🆕
- etc.

### Script de Migration (optionnel)

```php
function archi_migrate_to_advanced_params() {
    $posts = get_posts([
        'post_type' => ['post', 'archi_project', 'archi_illustration'],
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => '_archi_show_in_graph',
                'value' => '1'
            ]
        ]
    ]);
    
    foreach ($posts as $post) {
        // Appliquer des valeurs par défaut basées sur le type
        if ($post->post_type === 'archi_project') {
            update_post_meta($post->ID, '_archi_node_shape', 'square');
            update_post_meta($post->ID, '_archi_hover_effect', 'glow');
        } elseif ($post->post_type === 'archi_illustration') {
            update_post_meta($post->ID, '_archi_node_shape', 'diamond');
            update_post_meta($post->ID, '_archi_hover_effect', 'pulse');
        }
        
        // Appliquer des groupes basés sur les catégories
        $categories = get_the_category($post->ID);
        if (!empty($categories)) {
            update_post_meta($post->ID, '_archi_visual_group', $categories[0]->name);
        }
    }
    
    return count($posts) . ' articles migrés';
}
```

## 📝 Prochaines Étapes

### Phase 2 : Interface Gutenberg
- [ ] Bloc Gutenberg pour visualiser les paramètres
- [ ] Prévisualisation en direct du nœud
- [ ] Sélecteur visuel de couleurs et formes

### Phase 3 : Analytics
- [ ] Dashboard des statistiques du graphique
- [ ] Visualisation des groupes visuels
- [ ] Rapport de densité des connexions

### Phase 4 : Préréglages
- [ ] Templates de configuration (Architectural, Minimaliste, Coloré)
- [ ] Import/Export de configurations
- [ ] Copier les paramètres d'un article à l'autre

## 🎓 Ressources

- [Documentation D3.js Force Simulation](https://github.com/d3/d3-force)
- [WordPress REST API Handbook](https://developer.wordpress.org/rest-api/)
- [SVG Shapes Reference](https://developer.mozilla.org/en-US/docs/Web/SVG/Tutorial/Basic_Shapes)

## 💬 Support

Pour toute question ou suggestion :
- Issues GitHub du thème
- Documentation wiki
- Forum WordPress
