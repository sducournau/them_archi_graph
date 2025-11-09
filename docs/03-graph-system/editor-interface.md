# Éditeur de Graphique en Direct

## 📝 Vue d'ensemble

L'éditeur de graphique en direct permet aux administrateurs de modifier le graphique interactif directement depuis la page d'accueil. Cette fonctionnalité offre une interface intuitive pour déplacer les nœuds, créer des liens, éditer les images et ajuster les paramètres visuels en temps réel.

## ✨ Fonctionnalités

### 🎯 Mode Édition

- **Activation/Désactivation** : Toggle simple pour activer le mode édition
- **Détection automatique** : Détecte si l'utilisateur est administrateur
- **Interface non-intrusive** : Panneau latéral pliable
- **Raccourcis clavier** : `Ctrl+E` pour toggle, `Ctrl+S` pour sauvegarder, `Échap` pour annuler

### 🖱️ Déplacement de Nœuds

- **Drag & Drop** : Déplacez les nœuds en les faisant glisser
- **Sauvegarde automatique** : Les positions sont sauvegardées automatiquement (debounce 1s)
- **Feedback visuel** : Indicateur de statut pendant le déplacement
- **Sauvegarde manuelle** : Bouton pour sauvegarder toutes les positions immédiatement

### 🔗 Création de Liens

- **Mode création** : Cliquez sur 2 nœuds pour créer un lien
- **Liens manuels** : Stockés dans `_archi_related_articles`
- **Style visuel distinctif** : Les liens créés manuellement sont affichés avec un style pointillé animé
- **Suppression** : Possibilité de supprimer des liens existants

### 🖼️ Édition d'Images

- **Bibliothèque média WordPress** : Utilise le sélecteur natif de WordPress
- **Aperçu en temps réel** : L'image est mise à jour immédiatement dans le graphique
- **Images optimisées** : Utilise la taille `graph-node` (80x80px)

### ⚙️ Paramètres Avancés

Édition rapide des paramètres de chaque nœud :

- **Forme** : Circle, Square, Diamond, Triangle, Star, Hexagon
- **Couleur** : Sélecteur de couleur
- **Taille** : Slider 40-120px
- **Icône** : Emoji personnalisé
- **Badge** : Nouveau, Featured, Hot, Updated, Popular

### 👁️ Visibilité

- **Toggle rapide** : Activer/désactiver un nœud dans le graphique
- **Indicateur visuel** : Les nœuds désactivés sont affichés avec 30% d'opacité

## 🔧 Implémentation Technique

### Architecture

```
inc/graph-editor-api.php          → API REST (endpoints)
assets/js/graph-editor.js          → Interface JavaScript (classe GraphEditor)
assets/css/graph-editor.css        → Styles du panneau et états visuels
functions.php                      → Chargement conditionnel (admin uniquement)
```

### API REST Endpoints

Tous les endpoints sont préfixés par `/wp-json/archi/v1/graph-editor/`

#### POST `/save-position`

Sauvegarder la position d'un seul nœud.

**Request:**
```json
{
  "post_id": 123,
  "x": 450.5,
  "y": 300.2
}
```

**Response:**
```json
{
  "success": true,
  "post_id": 123,
  "position": {"x": 450.5, "y": 300.2},
  "message": "Position sauvegardée"
}
```

#### POST `/save-positions`

Sauvegarder plusieurs positions en batch (optimisé).

**Request:**
```json
{
  "positions": [
    {"id": 123, "x": 450.5, "y": 300.2},
    {"id": 124, "x": 550.0, "y": 400.0}
  ]
}
```

**Response:**
```json
{
  "success": true,
  "saved": 2,
  "errors": [],
  "message": "2 positions sauvegardées"
}
```

#### POST `/create-link`

Créer un lien entre deux nœuds.

**Request:**
```json
{
  "source_id": 123,
  "target_id": 456
}
```

**Response:**
```json
{
  "success": true,
  "source_id": 123,
  "target_id": 456,
  "related_articles": [456, 789],
  "message": "Lien créé"
}
```

#### POST `/delete-link`

Supprimer un lien existant.

**Request:**
```json
{
  "source_id": 123,
  "target_id": 456
}
```

#### POST `/update-image`

Mettre à jour l'image d'un nœud.

**Request:**
```json
{
  "post_id": 123,
  "image_id": 789
}
```

**Response:**
```json
{
  "success": true,
  "post_id": 123,
  "image_id": 789,
  "image_url": "https://example.com/wp-content/uploads/...",
  "message": "Image mise à jour"
}
```

#### POST `/update-params`

Mettre à jour les paramètres avancés d'un nœud.

**Request:**
```json
{
  "post_id": 123,
  "params": {
    "node_shape": "star",
    "node_color": "#ff5733",
    "node_size": 80,
    "node_icon": "🏗️",
    "node_badge": "featured"
  }
}
```

#### POST `/toggle-visibility`

Activer/désactiver un nœud dans le graphique.

**Request:**
```json
{
  "post_id": 123,
  "visible": true
}
```

#### GET `/state`

Obtenir l'état d'édition (permissions, user info, nonce).

**Response:**
```json
{
  "can_edit": true,
  "user_id": 1,
  "user_name": "Admin",
  "is_admin": true,
  "nonce": "abc123def456"
}
```

### Classe JavaScript

```javascript
// Initialisation automatique
window.archiGraphInstance = /* votre instance D3.js */;
window.graphEditor = new GraphEditor(window.archiGraphInstance);

// Méthodes principales
graphEditor.setEditMode(true);          // Activer le mode édition
graphEditor.toggleLinkCreationMode();   // Mode création de lien
graphEditor.saveAllPositions();         // Sauvegarder toutes les positions
graphEditor.selectNode(nodeData);       // Sélectionner un nœud
graphEditor.toggle();                   // Afficher/masquer le panneau
```

### Sécurité

- **Vérification des permissions** : `current_user_can('edit_posts')`
- **Nonces WordPress** : Tous les endpoints utilisent `wp_verify_nonce`
- **Sanitization** : Toutes les entrées sont sanitizées (`sanitize_text_field`, `absint`, etc.)
- **Escape output** : Utilisation systématique de `esc_html`, `esc_attr`, `esc_url`
- **Chargement conditionnel** : Scripts chargés uniquement pour les utilisateurs autorisés

## 🎨 Interface Utilisateur

### Panneau d'Édition

Le panneau latéral contient :

1. **Header** : Titre et bouton de fermeture
2. **Toggle Mode Édition** : Switch principal
3. **Section Outils** (visible si édition active) :
   - Bouton "Créer un lien"
   - Bouton "Sauvegarder"
4. **Section Nœud Sélectionné** (visible si un nœud est sélectionné) :
   - Infos du nœud (titre, ID)
   - Changer l'image
   - Toggle visibilité
   - Éditer paramètres avancés
5. **Section Paramètres Avancés** (visible si édition de paramètres) :
   - Forme (select)
   - Couleur (color picker)
   - Taille (slider)
   - Icône (input text)
   - Badge (select)
   - Boutons Appliquer/Annuler
6. **Statut** : Messages de feedback

### Bouton d'Ouverture

Un bouton flottant "🎨 Éditer" apparaît en haut à droite pour ouvrir le panneau.

### États Visuels

- **Mode édition actif** : `body.archi-edit-mode-active`
  - Curseur `move` sur les nœuds
  - Brightness augmenté au hover
- **Mode création de lien** : `body.archi-link-creation-mode`
  - Curseur `crosshair` sur les nœuds
  - Border verte au hover
- **Nœud sélectionné** : `.node-selected`
  - Drop shadow bleu
  - Border 3px #667eea
- **Lien manuel** : `.link-manual`
  - Style pointillé animé
  - Couleur #667eea

## 🚀 Utilisation

### Pour les Administrateurs

1. **Accéder à la page d'accueil** en étant connecté en tant qu'administrateur
2. **Cliquer sur le bouton "🎨 Éditer"** en haut à droite
3. **Activer le mode édition** avec le toggle
4. **Déplacer les nœuds** en les faisant glisser
5. **Créer des liens** :
   - Cliquer sur "Créer un lien"
   - Cliquer sur le nœud source
   - Cliquer sur le nœud cible
6. **Éditer un nœud** :
   - Cliquer sur un nœud pour le sélectionner
   - Utiliser les boutons de la section "Nœud sélectionné"
7. **Sauvegarder** :
   - Automatique après chaque déplacement (1s debounce)
   - Ou cliquer sur "Sauvegarder" pour une sauvegarde immédiate

### Raccourcis Clavier

| Raccourci | Action |
|-----------|--------|
| `Ctrl+E` | Toggle mode édition |
| `Ctrl+S` | Sauvegarder toutes les positions |
| `Échap` | Annuler mode création de lien |

## 📊 Données Persistantes

### Métadonnées WordPress

- **`_archi_graph_position`** : `{x: float, y: float}` - Position du nœud
- **`_archi_related_articles`** : `[int, int, ...]` - IDs des articles liés manuellement
- **`_archi_show_in_graph`** : `'1'` ou `'0'` - Visibilité dans le graphique
- **`_archi_node_shape`** : `string` - Forme du nœud
- **`_archi_node_color`** : `string` - Couleur (hex)
- **`_archi_node_size`** : `int` - Taille (40-120)
- **`_archi_node_icon`** : `string` - Emoji/Unicode
- **`_archi_node_badge`** : `string` - Badge type

### Cache Invalidation

Après chaque modification, le transient `archi_graph_articles` est supprimé pour forcer le rechargement des données.

## 🔄 Intégration avec le Graphique Existant

L'éditeur s'intègre avec votre instance D3.js existante :

```javascript
// Votre code existant
const graphInstance = {
  svg: d3.select('#graph-container'),
  nodes: [...],
  links: [...]
};

// Enregistrer l'instance globalement
window.archiGraphInstance = graphInstance;

// L'éditeur s'initialisera automatiquement
```

## 🎭 Personnalisation

### CSS Variables

```css
/* Couleur principale */
--archi-editor-primary: #667eea;

/* Couleur de succès */
--archi-editor-success: #4caf50;

/* Couleur d'erreur */
--archi-editor-error: #f44336;

/* Largeur du panneau */
--archi-editor-width: 380px;
```

### Traductions

Toutes les chaînes sont traduisibles via le text domain `archi-graph`.

```php
// Exemple
__('Mode Édition', 'archi-graph');
```

## 🐛 Débogage

### Console Logs

L'éditeur log les événements importants :

```javascript
console.log('GraphEditor: Initializing editor mode');
console.log('GraphEditor: Edit mode enabled');
console.log('GraphEditor: User cannot edit - editor disabled');
```

### Erreurs API

Les erreurs API sont affichées dans le panneau de statut et dans la console :

```javascript
console.error('Save positions error:', error);
```

### Mode Debug

Activez `WP_DEBUG` dans `wp-config.php` pour voir les logs PHP :

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 📱 Responsive

- **Desktop** : Panneau latéral 380px
- **Mobile** : Panneau plein écran
- **Tablet** : Panneau adaptatif

## ♿ Accessibilité

- **Focus visible** : Outline 2px sur tous les contrôles interactifs
- **ARIA labels** : Sur tous les boutons
- **Keyboard navigation** : Tous les contrôles accessibles au clavier
- **High contrast mode** : Styles adaptés pour `prefers-contrast: high`

## 🌓 Dark Mode

Support automatique via `prefers-color-scheme: dark` :

- Background #1e1e1e
- Text #e0e0e0
- Borders #333
- Inputs #2a2a2a

## 🔮 Évolutions Futures

- [ ] **Undo/Redo** : Stack d'annulation/rétablissement
- [ ] **Bulk operations** : Sélection multiple de nœuds
- [ ] **Grid snapping** : Alignement sur une grille
- [ ] **Export/Import** : Exporter/importer la disposition du graphique
- [ ] **Historique** : Voir l'historique des modifications
- [ ] **Templates** : Sauvegarder des dispositions prédéfinies
- [ ] **Collaboration** : Édition multi-utilisateurs en temps réel

## 📝 Notes de Version

### Version 1.0.0 (Actuelle)

**Ajouté :**
- Mode édition pour administrateurs
- Drag & drop avec sauvegarde auto
- Création/suppression de liens
- Édition d'images via bibliothèque média
- Édition de paramètres avancés
- Panneau latéral pliable
- Raccourcis clavier
- 7 endpoints REST API
- Feedback visuel temps réel
- Support responsive et dark mode

## 🆘 Support

Pour toute question ou problème :

1. Vérifier que vous êtes connecté en tant qu'administrateur
2. Vérifier que les scripts sont bien chargés (DevTools → Network)
3. Consulter la console JavaScript (F12)
4. Vérifier les logs PHP (`wp-content/debug.log`)
5. Tester les endpoints API directement avec curl/Postman

## 📚 Voir Aussi

- [Advanced Graph Parameters](./advanced-graph-parameters.md)
- [Graph Simplification Update](./graph-simplification-update.md)
- [Relationships Guide](./relationships-guide.md)
- [API Documentation](./api.md)
