# Graphique avec Images PNG Transparentes

## Vue d'ensemble

Le système de graphique a été modifié pour afficher des **images PNG complètes avec fond transparent** au lieu de bulles circulaires avec images recadrées.

## Changements Effectués

### 1. Suppression des Bulles Circulaires

**Avant :** Chaque nœud avait un cercle coloré en arrière-plan (`node-background`) avec l'image découpée en forme circulaire (`clip-path: circle(50%)`).

**Après :** Les images PNG sont affichées en entier avec leur fond transparent, sans cercle de fond ni découpage.

#### Fichiers modifiés :
- `assets/js/components/GraphContainer.jsx` : Suppression de l'ajout du cercle de fond
- `assets/js/components/Node.jsx` : Suppression de l'élément `<circle>` de fond
- `assets/css/main.scss` : `.node-background` mis à `display: none`
- `assets/css/graph-white.css` : Suppression du `clip-path: circle(50%)`
- `assets/css/graph-force-visible.css` : `.node-background` forcé à `display: none`

### 2. Affichage des Images PNG Complètes

Les images sont maintenant affichées avec :
- `preserveAspectRatio="xMidYMid meet"` : Conserve les proportions de l'image
- Pas de `clip-path` : L'image complète est visible
- Pas de `border-radius` : Les coins ne sont pas arrondis (utiliser des PNG avec transparence)
- `filter: drop-shadow()` : Ombre portée maintenue pour profondeur visuelle

### 3. Tailles Différentes pour les Projets Architecturaux

Les projets architecturaux (`archi_project`) ont maintenant des **plages de taille différentes et plus grandes** :

#### Fichier modifié : `inc/meta-boxes.php`

**Articles normaux (`post`, `archi_illustration`) :**
- Taille minimale : 40px
- Taille maximale : 120px
- Pas d'ajustement : 10px

**Projets architecturaux (`archi_project`) :**
- Taille minimale : 60px
- Taille maximale : 200px
- Pas d'ajustement : 20px

Le contrôle de taille dans l'éditeur s'adapte automatiquement selon le type de post.

## Utilisation

### Configuration d'un Nœud dans l'Éditeur

1. Ouvrir la page d'édition d'un projet architectural
2. Dans la meta box **"Paramètres du graphique"** (barre latérale droite) :
   - ✅ Cocher **"Afficher dans le graphique"**
   - 🎨 Choisir une couleur (optionnel, non visible si pas de cercle)
   - 📏 Ajuster la **"Taille du nœud"** avec le curseur (60-200px pour les projets)
   - ⭐ Sélectionner le niveau de priorité

### Recommandations pour les Images

Pour un meilleur rendu dans le graphique :

1. **Format :** PNG avec canal alpha (transparence)
2. **Dimensions :** 
   - Articles normaux : 100-150px de côté
   - Projets architecturaux : 150-250px de côté
3. **Fond :** Transparent (pas de fond blanc ou coloré)
4. **Contenu :** 
   - Icônes, logos, illustrations vectorielles
   - Photos détourées
   - Croquis sur fond transparent
5. **Poids :** Optimiser pour le web (< 50 Ko idéalement)

### Exemples de Tailles

```php
// Article normal - petit
_archi_node_size: 60px

// Article normal - moyen
_archi_node_size: 90px

// Projet architectural - moyen
_archi_node_size: 120px

// Projet architectural - grand
_archi_node_size: 160px

// Projet architectural - très grand
_archi_node_size: 200px
```

## Interaction avec les Nœuds

Les interactions sont préservées :

- **Hover :** L'image s'agrandit de 20% avec une ombre plus prononcée
- **Clic :** Sélectionne le nœud et active l'animation GIF (si applicable)
- **Drag :** Déplace le nœud dans le graphique
- **Badge de priorité :** Petit cercle coloré en haut à droite (preserved)

## Compatibilité

### Anciennes Images Circulaires

Les anciennes images qui étaient optimisées pour l'affichage circulaire fonctionneront toujours, mais :
- Les coins seront visibles s'il y a du contenu
- Recommandé de remplacer par des PNG avec fond transparent

### Migration

Pour migrer vos images existantes :

1. Exporter l'image miniature actuelle
2. Détourer l'élément principal (supprimer le fond)
3. Exporter en PNG avec transparence
4. Télécharger comme nouvelle image mise en avant
5. Ajuster la taille dans les paramètres du graphique

## Code Technique

### Structure du Nœud (GraphContainer.jsx)

```jsx
// Avant : avec cercle de fond
<g class="graph-node">
  <circle class="node-background" r="35" fill="#3498db" />
  <image class="node-image" 
         width="60" height="60"
         style="clip-path: circle(50%)" />
</g>

// Après : PNG transparent sans cercle
<g class="graph-node">
  <image class="node-image" 
         width="60" height="60"
         preserveAspectRatio="xMidYMid meet" />
</g>
```

### Paramètres de Taille (meta-boxes.php)

```php
// Détection du type de post
if ($post->post_type === 'archi_project') {
    $min_size = 60;
    $max_size = 200;
    $step = 20;
} else {
    $min_size = 40;
    $max_size = 120;
    $step = 10;
}
```

## Support

Pour des questions ou des problèmes :
- Vérifier que les images sont bien en PNG avec transparence
- S'assurer que `_archi_show_in_graph` est à '1'
- Vider le cache du navigateur après modifications CSS
- Vérifier la console JavaScript pour les erreurs
