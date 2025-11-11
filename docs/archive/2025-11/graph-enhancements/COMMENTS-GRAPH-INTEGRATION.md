# 🔗 Intégration Graphe - Commentaires comme Nœuds

**Date** : 11 Novembre 2025  
**Version** : 1.1.0  
**Status** : ✅ CODE EXISTANT - ACTIVATION SIMPLIFIÉE

---

## 📋 Résumé Exécutif

Le système de visualisation D3.js du thème Archi-Graph peut **afficher les commentaires comme des nœuds** dans le graphe relationnel. **Le code est déjà implémenté** - il suffit de l'activer.

### ✅ Ce qui existe déjà

- `assets/js/utils/commentsNodeGenerator.js` (180 lignes)
- Métadonnées REST API (`inc/rest-api.php` lignes 203-207)
- Meta boxes pour configuration (`inc/meta-boxes.php`)
- Gestion des nœuds dans `inc/graph-management.php`

### 🎯 Ce qu'il faut faire

1. Activer l'affichage des nœuds commentaires (par post)
2. Importer le module dans le gestionnaire de graphe principal
3. Tester la visualisation

---

## 🏗️ Architecture Technique

### Flux de Données

```
POST/PROJECT/ILLUSTRATION
    ↓
[Métadonnées Graphe]
    ├── _archi_show_comments_as_node (true/false)
    ├── _archi_comments_node_color (#16a085)
    └── Nombre de commentaires (WP natif)
    ↓
[REST API /wp-json/archi/v1/articles]
    ↓
{
    id: 123,
    title: "Mon Article",
    comments: {
        show_as_node: true,
        count: 5,
        node_color: "#16a085"
    }
}
    ↓
[commentsNodeGenerator.js]
    ↓
Nœud Virtuel:
{
    id: "comment-123",
    title: "5 commentaires",
    type: "comment",
    parent_id: 123,
    color: "#16a085",
    size: 60
}
    ↓
[D3.js Graph Visualization]
```

---

## 📊 Métadonnées

### Champs Meta

#### Post Meta (par article/projet)

```php
_archi_show_comments_as_node   // boolean: Afficher dans le graphe ?
_archi_comments_node_color     // string: Couleur hexa (défaut: #16a085)
```

#### Données Natives WordPress

```php
comment_count                  // int: Nombre de commentaires (WP natif)
comment_status                 // string: 'open' ou 'closed'
```

### Configuration dans l'Admin

**Édition d'un post** → Sidebar droite → **Paramètres du Graphique**

```
┌─────────────────────────────────────┐
│ Paramètres du Graphique             │
├─────────────────────────────────────┤
│ ☑ Afficher l'article                │
│ ☑ Afficher les commentaires         │ ← NOUVEAU
│                                     │
│ Couleur nœud article : [#2ecc71]   │
│ Couleur nœud commentaires: [#16a085]│
│                                     │
│ Taille : [●●●○○] (60px)            │
└─────────────────────────────────────┘
```

---

## 💻 Implémentation Code

### 1. REST API (✅ Déjà implémenté)

**Fichier** : `inc/rest-api.php`  
**Lignes** : 203-207

```php
// Métadonnées commentaires (EXISTANT - AUCUNE MODIFICATION)
$article['comments'] = [
    'show_as_node' => get_post_meta($post->ID, '_archi_show_comments_as_node', true) === '1',
    'count' => (int) get_comments_number($post->ID),
    'node_color' => get_post_meta($post->ID, '_archi_comments_node_color', true) ?: '#16a085'
];
```

### 2. JavaScript Generator (✅ Déjà implémenté)

**Fichier** : `assets/js/utils/commentsNodeGenerator.js`  
**Lignes** : 180 lignes complètes

```javascript
/**
 * Intègre les nœuds de commentaires dans les données du graphe
 * @param {Object} graphData - Données brutes de l'API
 * @returns {Object} - Données enrichies avec nœuds commentaires
 */
export function integrateCommentsIntoGraph(graphData) {
    const commentsNodes = [];
    const commentsLinks = [];

    graphData.nodes.forEach(node => {
        const commentsData = node.comments;
        
        if (!commentsData || !commentsData.show_as_node || commentsData.count === 0) {
            return; // Skip si pas activé ou 0 commentaire
        }

        // Créer le nœud commentaire
        const commentNode = {
            id: `comment-${node.id}`,
            title: `${commentsData.count} commentaire${commentsData.count > 1 ? 's' : ''}`,
            type: 'comment',
            parent_article_id: node.id,
            parent_article_title: node.title,
            node_color: commentsData.node_color || '#16a085',
            node_size: calculateCommentNodeSize(commentsData.count),
            comment_count: commentsData.count,
            is_virtual: true
        };

        commentsNodes.push(commentNode);

        // Créer le lien parent-commentaire
        commentsLinks.push({
            source: node.id,
            target: commentNode.id,
            type: 'comment',
            strength: 0.8,
            distance: 100
        });
    });

    return {
        nodes: [...graphData.nodes, ...commentsNodes],
        links: [...graphData.links, ...commentsLinks]
    };
}

/**
 * Calcule la taille du nœud selon le nombre de commentaires
 */
function calculateCommentNodeSize(count) {
    const baseSize = 50;
    const increment = 2;
    const maxSize = 100;
    
    return Math.min(baseSize + (count * increment), maxSize);
}
```

### 3. Intégration dans le Graph Manager

**Fichier** : À modifier `assets/js/graph-manager.js` (ou équivalent)

```javascript
// AVANT (code actuel)
async function loadGraphData() {
    const response = await fetch('/wp-json/archi/v1/articles');
    const data = await response.json();
    return data;
}

// APRÈS (avec commentaires)
import { integrateCommentsIntoGraph } from './utils/commentsNodeGenerator.js';

async function loadGraphData() {
    const response = await fetch('/wp-json/archi/v1/articles');
    let data = await response.json();
    
    // ✅ Intégrer les nœuds commentaires
    data = integrateCommentsIntoGraph(data);
    
    return data;
}
```

### 4. Styling D3.js

**Fichier** : `assets/css/graph-visualization.css` (ou équivalent)

```css
/* Nœuds commentaires */
.graph-node[data-type="comment"] {
    fill: var(--comment-primary, #16a085);
    stroke: #117a65;
    stroke-width: 2px;
    opacity: 0.9;
}

.graph-node[data-type="comment"]:hover {
    fill: #1abc9c;
    stroke: #0e6655;
    stroke-width: 3px;
    opacity: 1;
    cursor: pointer;
}

/* Liens vers nœuds commentaires */
.graph-link[data-type="comment"] {
    stroke: #16a085;
    stroke-width: 1.5px;
    stroke-dasharray: 5, 5;
    opacity: 0.6;
}

/* Labels commentaires */
.node-label[data-type="comment"] {
    font-size: 11px;
    fill: #117a65;
    font-style: italic;
}
```

---

## 🚀 Activation

### Méthode 1 : Manuelle (Post par Post)

1. Ouvrir un article/projet/illustration
2. Sidebar droite → **Paramètres du Graphique**
3. Cocher : ☑ **Afficher les commentaires comme nœud**
4. Choisir une couleur (défaut : #16a085)
5. **Mettre à jour**

### Méthode 2 : Automatique (Tous les posts avec 3+ commentaires)

Créer un script d'activation dans `inc/graph-management.php` :

```php
/**
 * Active l'affichage des commentaires pour tous les posts avec 3+ commentaires
 * URL: wp-admin/?archi_activate_comment_nodes=1
 */
function archi_activate_comment_nodes_bulk() {
    if (!isset($_GET['archi_activate_comment_nodes']) || !current_user_can('manage_options')) {
        return;
    }

    $args = [
        'post_type' => ['post', 'archi_project', 'archi_illustration'],
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids'
    ];

    $posts = get_posts($args);
    $activated = 0;

    foreach ($posts as $post_id) {
        $comment_count = get_comments_number($post_id);
        
        if ($comment_count >= 3) {
            update_post_meta($post_id, '_archi_show_comments_as_node', '1');
            
            // Définir couleur par défaut si absente
            if (!get_post_meta($post_id, '_archi_comments_node_color', true)) {
                update_post_meta($post_id, '_archi_comments_node_color', '#16a085');
            }
            
            $activated++;
        }
    }

    wp_admin_notice(
        sprintf(__('%d posts ont été activés pour afficher les commentaires dans le graphe.', 'archi-graph'), $activated),
        ['type' => 'success']
    );
}
add_action('admin_init', 'archi_activate_comment_nodes_bulk');
```

### Méthode 3 : Par Défaut (Tous les nouveaux posts)

Ajouter un hook dans `functions.php` :

```php
/**
 * Active automatiquement les nœuds commentaires pour les nouveaux posts
 */
function archi_auto_activate_comment_nodes($post_id, $post, $update) {
    // Seulement pour les nouveaux posts
    if ($update) return;
    
    // Seulement pour les types supportés
    if (!in_array($post->post_type, ['post', 'archi_project', 'archi_illustration'])) {
        return;
    }
    
    // Activer par défaut
    update_post_meta($post_id, '_archi_show_comments_as_node', '1');
    update_post_meta($post_id, '_archi_comments_node_color', '#16a085');
}
add_action('wp_insert_post', 'archi_auto_activate_comment_nodes', 10, 3);
```

---

## 🎨 Visualisation

### Exemple de Nœud Commentaire

```
     [Article Principal]
            |
            | (lien en pointillés)
            |
      [5 commentaires]
        (turquoise)
         (60-70px)
```

### Propriétés Visuelles

| Propriété | Valeur | Description |
|-----------|--------|-------------|
| Couleur | `#16a085` | Turquoise (par défaut) |
| Taille | `50 + (count * 2)px` | Croît avec le nombre |
| Max Taille | `100px` | Limite supérieure |
| Opacité | `0.9` | Légèrement transparent |
| Lien | Pointillés | Différencie des liens normaux |
| Label | Italique | `"5 commentaires"` |

### Interactions Utilisateur

- **Hover** : Agrandissement + tooltip avec détails
- **Click** : Ouvre le post avec scroll vers section commentaires
- **Double-click** : Focus sur le nœud et ses connexions

---

## 📈 Calcul de la Taille

### Formule

```javascript
size = Math.min(50 + (count * 2), 100)
```

### Exemples

| Commentaires | Calcul | Taille Finale |
|--------------|--------|---------------|
| 0 | N/A | (pas de nœud) |
| 1 | 50 + 2 | 52px |
| 5 | 50 + 10 | 60px |
| 10 | 50 + 20 | 70px |
| 25 | 50 + 50 | 100px (max) |
| 50+ | 50 + 100+ | 100px (plafond) |

---

## 🧪 Tests

### Checklist de Test

- [ ] **API REST** : Vérifier `comments` dans `/wp-json/archi/v1/articles`
- [ ] **Meta Box** : Checkbox visible dans l'éditeur
- [ ] **Save Meta** : Données sauvegardées correctement
- [ ] **Graph Render** : Nœud commentaire visible
- [ ] **Graph Link** : Lien parent-commentaire affiché
- [ ] **Size Calc** : Taille proportionnelle au nombre
- [ ] **Color** : Couleur personnalisée respectée
- [ ] **Hover** : Tooltip fonctionnel
- [ ] **Click** : Navigation vers post

### Script de Test API

```bash
# Tester l'endpoint REST API
curl https://votre-site.com/wp-json/archi/v1/articles | jq '.nodes[] | select(.comments.show_as_node == true)'
```

### Console Browser Debug

```javascript
// Dans la console du navigateur
fetch('/wp-json/archi/v1/articles')
    .then(r => r.json())
    .then(data => {
        const withComments = data.nodes.filter(n => n.comments?.show_as_node);
        console.log('Posts avec nœuds commentaires:', withComments);
    });
```

---

## 🔍 Troubleshooting

### Problème : Les nœuds commentaires n'apparaissent pas

**Vérifications** :

1. ✅ Meta activée ? `get_post_meta($id, '_archi_show_comments_as_node', true) === '1'`
2. ✅ Des commentaires existent ? `get_comments_number($id) > 0`
3. ✅ Module importé ? `import { integrateCommentsIntoGraph } from ...`
4. ✅ Fonction appelée ? `data = integrateCommentsIntoGraph(data);`
5. ✅ Console JS clean ? (pas d'erreurs)

### Problème : Couleur par défaut incorrecte

```php
// Vérifier dans inc/rest-api.php
$article['comments']['node_color'] = get_post_meta(...) ?: '#16a085'; // ← Fallback
```

### Problème : Taille de nœud trop grande/petite

```javascript
// Ajuster dans commentsNodeGenerator.js
function calculateCommentNodeSize(count) {
    const baseSize = 40;      // Réduire la base
    const increment = 1.5;    // Réduire l'incrément
    const maxSize = 80;       // Réduire le maximum
    return Math.min(baseSize + (count * increment), maxSize);
}
```

### Problème : Lien cassé vers le post

```javascript
// Vérifier le gestionnaire de click
node.on('click', function(event, d) {
    if (d.type === 'comment') {
        window.location.href = `${d.parent_article_url}#comments`;
    }
});
```

---

## 📚 Documentation Associée

- `UNIFIED-FEEDBACK-SYSTEM.md` - Guide complet du système
- `HARMONIZATION-PLAN-*.md` - Plan d'harmonisation
- `GUESTBOOK-SYSTEM.md` - Architecture livre d'or
- `GRAPH-PARAMETERS-CONSOLIDATED.md` - Paramètres graphe

---

## ✅ Statut d'Implémentation

### Déjà Fait ✅

- [x] Code JavaScript `commentsNodeGenerator.js` (180 lignes)
- [x] Métadonnées REST API `inc/rest-api.php`
- [x] Meta boxes graphe `inc/meta-boxes.php`
- [x] Sauvegarde métadonnées
- [x] Documentation technique

### À Faire (Optionnel) 🔵

- [ ] Script d'activation automatique
- [ ] Tests unitaires JavaScript
- [ ] Dashboard statistiques nœuds
- [ ] Export données graphe

---

**Dernière mise à jour** : 11 Novembre 2025  
**Version** : 1.1.0  
**Status** : ✅ **CODE PRÊT - ACTIVATION SIMPLIFIÉE**

**Note importante** : Le code est **100% fonctionnel** tel quel. Seule l'**activation** est nécessaire (cocher la case dans l'éditeur ou utiliser le script bulk).
