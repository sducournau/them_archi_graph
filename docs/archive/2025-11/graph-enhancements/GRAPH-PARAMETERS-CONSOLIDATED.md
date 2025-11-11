# 📊 Paramètres du Graph - Documentation Consolidée

## 🎯 Vue d'ensemble

Ce document centralise **TOUS** les paramètres du système de graphique interactif. Tous les paramètres sont enregistrés via `graph-meta-registry.php` et accessibles de manière unifiée.

---

## 🏗️ Architecture

### Système Unifié
- **Enregistrement**: `inc/graph-meta-registry.php` - `archi_register_all_graph_meta()`
- **Lecture**: `archi_get_graph_params($post_id, $include_defaults = true)`
- **Écriture**: `archi_set_graph_params($post_id, $params)`
- **Interface Admin**: `inc/meta-boxes.php` - `archi_graph_meta_box_callback()`
- **API REST**: `inc/rest-api.php` - Tous les paramètres inclus automatiquement

### Clés de Métadonnées
Format interne: `_archi_[param_name]`  
Format frontend (API/JS): `[param_name]` (sans le préfixe `_archi_`)

---

## 📋 Catégories de Paramètres

### 1️⃣ **Core Graph Settings** (Paramètres de base)

#### `show_in_graph`
- **Type**: Boolean (`'0'` ou `'1'`)
- **Défaut**: `'0'`
- **Description**: Afficher ce nœud dans le graphique
- **Interface**: ✅ Checkbox
- **API**: ✅ Exposé

#### `priority_level`
- **Type**: String enum
- **Valeurs**: `'low'` | `'normal'` | `'high'` | `'featured'`
- **Défaut**: `'normal'`
- **Description**: Niveau de priorité visuelle
- **Interface**: ✅ Select dropdown
- **API**: ✅ Exposé

#### `graph_position`
- **Type**: Array `{x: float, y: float}`
- **Défaut**: `[]`
- **Description**: Position sauvegardée dans le graphique
- **Interface**: ⚙️ Automatique (lecture seule + reset)
- **API**: ✅ Exposé

#### `pin_node`
- **Type**: Boolean (`'0'` ou `'1'`)
- **Défaut**: `'0'`
- **Description**: Fixer la position du nœud (désactiver la physique)
- **Interface**: ❌ Non implémenté dans l'UI
- **API**: ✅ Exposé

#### `visual_group`
- **Type**: String
- **Défaut**: `''`
- **Description**: Groupe visuel pour clustering personnalisé
- **Interface**: ❌ Non implémenté dans l'UI
- **API**: ✅ Exposé

---

### 2️⃣ **Node Visual Properties** (Propriétés visuelles)

#### `node_color`
- **Type**: String (hex color `#RRGGBB`)
- **Défaut**: `#3498db` (bleu) / `#e67e22` (projets) / `#9b59b6` (illustrations)
- **Description**: Couleur du nœud
- **Interface**: ✅ Color picker
- **API**: ✅ Exposé

#### `node_size`
- **Type**: Integer
- **Range**: 40-500 px (tous types)
- **Défaut**: `60`
- **Description**: Taille du nœud en pixels
- **Interface**: ✅ Range slider
- **API**: ✅ Exposé

#### `node_shape`
- **Type**: String enum
- **Valeurs**: `'circle'` | `'square'` | `'diamond'` | `'triangle'` | `'star'` | `'hexagon'`
- **Défaut**: `'circle'`
- **Description**: Forme géométrique du nœud
- **Interface**: ❌ Non implémenté dans l'UI
- **API**: ✅ Exposé
- **Rendu**: ⚠️ Partiellement implémenté dans `advancedShapes.js`

#### `node_icon`
- **Type**: String (max 2 chars)
- **Défaut**: `''`
- **Description**: Icône/emoji affiché dans le nœud
- **Interface**: ❌ Non implémenté dans l'UI
- **API**: ✅ Exposé

#### `node_opacity`
- **Type**: Float
- **Range**: 0.1 - 1.0
- **Défaut**: `1.0`
- **Description**: Opacité du nœud
- **Interface**: ❌ Non implémenté dans l'UI
- **API**: ✅ Exposé

#### `node_border`
- **Type**: String enum
- **Valeurs**: `'none'` | `'solid'` | `'dashed'` | `'dotted'` | `'glow'`
- **Défaut**: `'none'`
- **Description**: Style de bordure
- **Interface**: ❌ Non implémenté dans l'UI
- **API**: ✅ Exposé
- **Rendu**: ⚠️ Partiellement implémenté dans `advancedShapes.js`

#### `border_color`
- **Type**: String (hex color)
- **Défaut**: `''`
- **Description**: Couleur de la bordure
- **Interface**: ❌ Non implémenté dans l'UI
- **API**: ✅ Exposé

#### `node_label`
- **Type**: String (max 20 chars)
- **Défaut**: `''`
- **Description**: Label personnalisé (sinon titre du post)
- **Interface**: ❌ Non implémenté dans l'UI
- **API**: ✅ Exposé

#### `show_label`
- **Type**: Boolean
- **Défaut**: `'0'`
- **Description**: Afficher le label en permanence (pas seulement au survol)
- **Interface**: ❌ Non implémenté dans l'UI
- **API**: ✅ Exposé

#### `node_badge`
- **Type**: String enum
- **Valeurs**: `''` | `'new'` | `'featured'` | `'hot'` | `'updated'` | `'popular'`
- **Défaut**: `''`
- **Description**: Badge visuel sur le nœud
- **Interface**: ❌ Non implémenté dans l'UI
- **API**: ✅ Exposé

---

### 3️⃣ **Node Behavior & Animation** (Comportement et animations)

#### `node_weight`
- **Type**: Integer
- **Range**: 1-10
- **Défaut**: `1`
- **Description**: Poids pour la simulation physique (plus lourd = moins mobile)
- **Interface**: ❌ Non implémenté dans l'UI
- **API**: ✅ Exposé

#### `hover_effect`
- **Type**: String enum
- **Valeurs**: `'none'` | `'zoom'` | `'pulse'` | `'glow'` | `'rotate'` | `'bounce'`
- **Défaut**: `'zoom'`
- **Description**: Type d'effet au survol
- **Interface**: ❌ Non implémenté dans l'UI
- **API**: ✅ Exposé
- **Rendu**: ✅ Implémenté dans `advancedShapes.js`

#### `entrance_animation`
- **Type**: String enum
- **Valeurs**: `'none'` | `'fade'` | `'scale'` | `'slide'` | `'bounce'`
- **Défaut**: `'fade'`
- **Description**: Animation d'apparition du nœud
- **Interface**: ❌ Non implémenté dans l'UI
- **API**: ✅ Exposé

#### `animation_level`
- **Type**: String enum
- **Valeurs**: `'none'` | `'subtle'` | `'normal'` | `'intense'`
- **Défaut**: `'normal'`
- **Description**: Intensité globale des animations
- **Interface**: ✅ Select dropdown
- **API**: ✅ Exposé

#### `animation_duration`
- **Type**: Integer (milliseconds)
- **Range**: 0-5000 ms
- **Défaut**: `800`
- **Description**: Durée de l'animation d'entrée
- **Interface**: ✅ Number input
- **API**: ✅ Exposé

#### `animation_delay`
- **Type**: Integer (milliseconds)
- **Range**: 0-5000 ms
- **Défaut**: `0`
- **Description**: Délai avant le début de l'animation
- **Interface**: ✅ Number input
- **API**: ✅ Exposé

#### `animation_easing`
- **Type**: String enum
- **Valeurs**: `'linear'` | `'ease'` | `'ease-in'` | `'ease-out'` | `'ease-in-out'` | `'elastic'` | `'bounce'`
- **Défaut**: `'ease-out'`
- **Description**: Fonction d'easing pour les animations
- **Interface**: ✅ Select dropdown
- **API**: ✅ Exposé

#### `enter_from`
- **Type**: String enum
- **Valeurs**: `'center'` | `'top'` | `'bottom'` | `'left'` | `'right'`
- **Défaut**: `'center'`
- **Description**: Direction d'entrée du nœud
- **Interface**: ✅ Select dropdown
- **API**: ✅ Exposé

#### `hover_scale`
- **Type**: Float
- **Range**: 1.0 - 2.0
- **Défaut**: `1.15`
- **Description**: Facteur d'agrandissement au survol
- **Interface**: ✅ Number input
- **API**: ✅ Exposé

#### `pulse_effect`
- **Type**: Boolean
- **Défaut**: `'0'`
- **Description**: Effet de pulsation continue
- **Interface**: ✅ Checkbox
- **API**: ✅ Exposé
- **Rendu**: ✅ Implémenté dans `GraphManager.js`

#### `glow_effect`
- **Type**: Boolean
- **Défaut**: `'0'`
- **Description**: Halo lumineux au survol
- **Interface**: ✅ Checkbox
- **API**: ✅ Exposé
- **Rendu**: ✅ Implémenté dans `GraphManager.js` et `GraphContainer.jsx`

---

### 4️⃣ **Link & Relationship Settings** (Liens et relations)

#### `hide_links`
- **Type**: Boolean
- **Défaut**: `'0'`
- **Description**: Masquer les liens de/vers ce nœud
- **Interface**: ✅ Checkbox
- **API**: ✅ Exposé

#### `related_articles`
- **Type**: Array of integers (post IDs)
- **Défaut**: `[]`
- **Description**: Liens manuels vers d'autres articles
- **Interface**: ✅ Table avec checkboxes
- **API**: ✅ Exposé

#### `link_strength`
- **Type**: Float
- **Range**: 0.0 - 2.0
- **Défaut**: `1.0`
- **Description**: Force des liens (affecte la physique)
- **Interface**: ❌ Non implémenté dans l'UI
- **API**: ✅ Exposé

#### `connection_depth`
- **Type**: Integer
- **Range**: 1-3
- **Défaut**: `2`
- **Description**: Profondeur des connexions à afficher
- **Interface**: ❌ Non implémenté dans l'UI
- **API**: ✅ Exposé

---

### 5️⃣ **Special Features** (Fonctionnalités spéciales)

#### `show_comments_node`
- **Type**: Boolean
- **Défaut**: `'0'`
- **Description**: Créer un nœud séparé pour les commentaires
- **Interface**: ✅ Checkbox
- **API**: ✅ Exposé (via `comments` object)

#### `comment_node_color`
- **Type**: String (hex color)
- **Défaut**: `#16a085` (turquoise)
- **Description**: Couleur du nœud commentaires
- **Interface**: ✅ Color picker
- **API**: ✅ Exposé (via `comments` object)

---

## 🔄 Flux de Données

```
WordPress Admin (meta-boxes.php)
        ↓
    save_post hook
        ↓
update_post_meta() → Database (_archi_* keys)
        ↓
REST API (rest-api.php)
        ↓
archi_get_graph_params($post_id)
        ↓
Frontend API Response (without _archi_ prefix)
        ↓
D3.js Graph (GraphContainer.jsx, advancedShapes.js)
```

---

## ✅ État d'Implémentation

### Complètement Implémenté
- ✅ Core graph settings (show_in_graph, priority_level, graph_position)
- ✅ Basic visuals (node_color, node_size)
- ✅ Basic animation (animation_level, duration, delay, easing, enter_from)
- ✅ Hover effects (hover_scale, pulse_effect, glow_effect)
- ✅ Links & relationships (hide_links, related_articles)
- ✅ Comments nodes (show_comments_node, comment_node_color)

### Partiellement Implémenté
- ⚠️ Advanced shapes (registered, partially rendered)
- ⚠️ Node borders (registered, partially rendered)
- ⚠️ Hover effect types (registered, some implemented)

### Non Implémenté dans l'UI (mais registered)
- ❌ pin_node, visual_group
- ❌ node_shape, node_icon, node_opacity
- ❌ node_border, border_color
- ❌ node_label, show_label, node_badge
- ❌ node_weight, entrance_animation, hover_effect (dropdown)
- ❌ link_strength, connection_depth

---

## 🎨 Recommandations d'Utilisation

### Presets d'Animation Suggérés

#### **Subtle** (Discret)
```php
animation_level: 'subtle'
animation_duration: 600
hover_scale: 1.05
pulse_effect: '0'
glow_effect: '0'
```

#### **Normal** (Par défaut)
```php
animation_level: 'normal'
animation_duration: 800
hover_scale: 1.15
pulse_effect: '0'
glow_effect: '0'
```

#### **Intense** (Dynamique)
```php
animation_level: 'intense'
animation_duration: 1000
hover_scale: 1.3
pulse_effect: '1'
glow_effect: '1'
```

#### **Featured** (Article vedette)
```php
priority_level: 'featured'
node_size: 120
pulse_effect: '1'
glow_effect: '1'
hover_scale: 1.2
```

---

## 🔧 Utilisation Programmatique

### Lire tous les paramètres
```php
$params = archi_get_graph_params($post_id, true);
echo $params['node_color']; // '#3498db'
echo $params['animation_duration']; // 800
```

### Modifier des paramètres
```php
$result = archi_set_graph_params($post_id, [
    'node_color' => '#ff0000',
    'node_size' => 100,
    'priority_level' => 'high',
    'pulse_effect' => true
]);
// Returns: ['success' => true, 'updated' => ['node_color', 'node_size', ...]]
```

### Via REST API
```javascript
// Lecture (automatique dans /wp-json/archi/v1/articles)
fetch('/wp-json/archi/v1/articles')
  .then(r => r.json())
  .then(data => {
    data.articles.forEach(article => {
      console.log(article.node_color);
      console.log(article.pulse_effect);
    });
  });
```

---

## 📊 Statistiques

- **Total de paramètres enregistrés**: 32
- **Paramètres exposés dans l'UI**: 15
- **Paramètres dans l'API REST**: 32 (tous)
- **Effets visuels implémentés**: 5 (glow, pulse, zoom, rotate, bounce)
- **Types de post supportés**: 3 (post, archi_project, archi_illustration)

---

## 🚀 Améliorations Futures

### Interface Admin
1. **Groupes Accordéon** - Organiser les paramètres par catégorie pliable
2. **Presets d'Animation** - Boutons pour appliquer des configs prédéfinies
3. **Aperçu Live** - Prévisualiser le nœud avec les paramètres actuels
4. **Bulk Edit** - Modifier plusieurs nœuds simultanément

### Fonctionnalités Visuelles
5. **Plus de formes** - Formes personnalisées (logos, SVG)
6. **Gradients** - Couleurs dégradées pour les nœuds
7. **Animations personnalisées** - Définir des animations CSS custom
8. **Effets de particules** - Effets autour des nœuds featured

### Performance
9. **Lazy Loading** - Charger les nœuds par lot
10. **WebGL Renderer** - Utiliser WebGL pour de grandes quantités de nœuds

---

**Dernière mise à jour**: Novembre 2025  
**Version du thème**: 1.1.0
