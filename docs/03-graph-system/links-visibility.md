# Option de visibilité des liens dans le graphe

## Description

Une nouvelle option a été ajoutée dans les paramètres du graphique pour permettre de masquer ou afficher les liens de proximité entre les nœuds.

## Fonctionnalité

### Configuration

L'option se trouve dans le panneau d'administration WordPress :
**Apparence > Paramètres Archi Graph > Afficher les liens**

- ✅ **Activée** (par défaut) : Les liens de proximité entre les articles sont affichés et influencent la simulation physique
- ❌ **Désactivée** : Les liens sont masqués, seuls les nœuds sont affichés avec des forces de répulsion

### Impact sur le graphe

Quand l'option est **activée** :
- Les liens visuels sont dessinés entre les nœuds ayant une proximité
- La simulation D3.js applique une force de lien (`forceLink`) qui influence le positionnement
- Les liens sont colorés selon leur force (rouge = très fort, gris = faible)
- L'épaisseur et l'opacité varient selon le score de proximité
- Les liens faibles sont affichés en pointillés

Quand l'option est **désactivée** :
- Aucun lien visuel n'est affiché
- La force de lien (`forceLink`) n'est pas appliquée à la simulation
- Les nœuds sont organisés uniquement par les forces de répulsion et de collision
- Le graphe est plus "aéré" et les nœuds se dispersent plus librement

## Implémentation technique

### Fichiers modifiés

1. **inc/admin-settings.php**
   - Ajout du champ de configuration dans le formulaire d'administration
   - Ajout de l'option dans `archi_get_all_options()`
   - Ajout de la sauvegarde dans `archi_save_admin_settings()`
   - Ajout de l'option dans la fonction d'export JavaScript

2. **template-parts/graph-homepage.php**
   - Ajout de `showLinks` dans la configuration `window.graphConfig.options`

3. **assets/js/components/GraphContainer.jsx**
   - Ajout de la logique conditionnelle pour afficher/masquer les liens
   - Application conditionnelle de la force de lien dans la simulation D3.js
   - Mise à jour conditionnelle des positions des liens pendant la simulation

### Code clé

```javascript
// Dans GraphContainer.jsx
const shouldShowLinks = options.showLinks !== false;

// Application conditionnelle de la force des liens
if (shouldShowLinks) {
  simulation.force("link", d3.forceLink(links)...);
  updateLinks(g, links);
} else {
  g.select(".links").selectAll(".graph-link").remove();
}

// Dans la boucle de simulation
simulation.on("tick", () => {
  updateNodePositions(g, filteredArticles);
  if (shouldShowLinks) {
    updateLinkPositions(g, links);
  }
  updateClusters(g, categories, filteredArticles);
});
```

### Base de données

L'option est stockée dans la table `wp_options` avec la clé :
- **Clé** : `graph_show_links`
- **Type** : Boolean (0 ou 1)
- **Défaut** : `true` (1)

## Cas d'usage

### Quand activer les liens

- Pour visualiser les relations entre les articles
- Pour regrouper visuellement les contenus similaires
- Pour une navigation intuitive basée sur la proximité thématique
- Pour analyser les connexions dans votre contenu

### Quand désactiver les liens

- Pour un graphe plus minimaliste et épuré
- Quand il y a trop d'articles et que les liens deviennent confus
- Pour mettre l'accent uniquement sur les nœuds et les clusters de catégories
- Pour améliorer les performances sur des graphes très denses
- Pour une présentation plus formelle sans les connexions

## Performance

La désactivation des liens peut légèrement améliorer les performances :
- Moins d'éléments DOM à gérer (pas de lignes SVG pour les liens)
- Moins de calculs dans la simulation D3.js (pas de force de lien)
- Rendu plus rapide lors des mises à jour de position

## Compatibilité

- ✅ Compatible avec toutes les autres options du graphe
- ✅ Fonctionne avec le filtrage par catégorie
- ✅ Compatible avec la sauvegarde automatique des positions
- ✅ Exportable/importable via la fonction d'export de configuration

## Notes de développement

L'option `showLinks` est transmise via la configuration PHP → JavaScript :

```php
<?php echo archi_get_option('graph_show_links', true) ? 'true' : 'false'; ?>
```

Elle est ensuite accessible dans le composant React via :

```javascript
const options = config.options || {};
const shouldShowLinks = options.showLinks !== false;
```

La valeur par défaut est `true` pour maintenir la compatibilité avec les installations existantes.
