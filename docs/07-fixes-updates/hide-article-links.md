# Masquer les liens d'un article dans le graphique

## Description

Cette fonctionnalité permet de masquer tous les liens de proximité d'un article spécifique dans le graphique, tout en gardant le nœud visible.

## Utilisation

### Dans le panneau d'administration

1. Ouvrez l'article, le projet ou l'illustration que vous souhaitez modifier
2. Dans le panneau latéral "Paramètres du graphique", cochez l'option **"Masquer les liens de cet article"**
3. Enregistrez ou publiez l'article

### Effet sur le graphique

Quand l'option est activée pour un article :
- ✅ Le nœud reste visible dans le graphique
- ❌ Aucun lien de proximité n'est dessiné depuis ou vers cet article
- ❌ Les relations automatiques (catégories, tags) ne créent pas de liens visuels
- ❌ Les relations manuelles ne sont pas affichées

### Cas d'usage

**Quand utiliser cette option :**
- Pour isoler visuellement un article important sans le retirer du graphique
- Pour réduire la complexité visuelle autour d'articles très connectés
- Pour créer des nœuds "flottants" qui ne sont reliés à rien
- Pour mettre en avant un article sans distractions de liens

**Exemples :**
- Un projet "phare" que vous voulez isoler visuellement
- Un article d'introduction qui ne doit pas être lié aux autres
- Un nœud central qui serait autrement surchargé de connexions

## Différence avec "Afficher dans le graphique"

| Option | Nœud visible ? | Liens visibles ? |
|--------|---------------|------------------|
| **Afficher dans le graphique** (décoché) | ❌ Non | ❌ Non |
| **Afficher dans le graphique** (coché) + **Masquer les liens** (décoché) | ✅ Oui | ✅ Oui |
| **Afficher dans le graphique** (coché) + **Masquer les liens** (coché) | ✅ Oui | ❌ Non |

## Implémentation technique

### Métadonnée

- **Clé** : `_archi_hide_links`
- **Type** : String ('0' ou '1')
- **Défaut** : '0' (liens visibles)
- **Visibilité REST** : Oui (show_in_rest: true)

### Fichiers modifiés

#### PHP (Backend)

1. **inc/meta-boxes.php**
   - Enregistrement de la métadonnée `_archi_hide_links`
   - Ajout du champ checkbox dans la meta box
   - Sauvegarde lors de l'enregistrement du post

2. **inc/rest-api.php**
   - Ajout du champ `hide_links` (boolean) dans la réponse API

#### JavaScript (Frontend)

1. **assets/js/utils/graphHelpers.js**
   - Modification de `calculateNodeLinks()` pour filtrer les liens
   - Vérification de `node.hide_links` avant de créer un lien

### Code clé

```php
// Enregistrement de la métadonnée
register_post_meta($post_type, '_archi_hide_links', [
    'type' => 'string',
    'single' => true,
    'default' => '0',
    'show_in_rest' => true,
    'sanitize_callback' => function($value) {
        return $value === '1' ? '1' : '0';
    },
    'auth_callback' => function() {
        return current_user_can('edit_posts');
    }
]);

// Dans l'API REST
'hide_links' => get_post_meta($post->ID, '_archi_hide_links', true) === '1',
```

```javascript
// Dans calculateNodeLinks() - graphHelpers.js
for (let i = 0; i < nodes.length; i++) {
    for (let j = i + 1; j < nodes.length; j++) {
        const nodeA = nodes[i];
        const nodeB = nodes[j];

        // Ignorer les liens si un des nœuds a hide_links activé
        if (nodeA.hide_links || nodeB.hide_links) {
            continue;
        }
        
        // ... reste du code
    }
}
```

## API REST

### Endpoint : `/wp-json/archi/v1/articles`

Le champ `hide_links` est inclus dans la réponse pour chaque article :

```json
{
  "articles": [
    {
      "id": 123,
      "title": "Mon Article",
      "hide_links": false,
      ...
    }
  ]
}
```

## Compatibilité

- ✅ Compatible avec tous les types de posts (post, archi_project, archi_illustration)
- ✅ Compatible avec l'éditeur Gutenberg
- ✅ Fonctionne avec le filtrage par catégorie
- ✅ Compatible avec la sauvegarde automatique des positions
- ✅ Exportable/importable via les outils WordPress standards

## Performance

Cette fonctionnalité améliore légèrement les performances :
- Moins de liens à calculer et à afficher
- Moins d'éléments DOM (lignes SVG)
- Simulation D3.js plus légère (moins de forces de lien)

## Notes

- Cette option est indépendante de l'option globale "Afficher les liens" dans les paramètres du thème
- Si l'option globale "Afficher les liens" est désactivée, cette option n'a aucun effet (pas de liens affichés de toute façon)
- Les liens manuels (via "_archi_related_articles") sont également masqués si cette option est activée
