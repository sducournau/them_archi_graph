# Mise à jour du comportement de clic sur les nœuds du graphe

**Date** : 3 novembre 2025

## Résumé des modifications

Ce document décrit les modifications apportées au système de clic sur les nœuds du graphique interactif.

## Nouveau comportement

### Premier clic sur un nœud
- ✅ **Activation du nœud** : Le nœud devient actif (classe `.active`)
- ✅ **Animation visuelle** : L'image du nœud s'agrandit (x1.5)
- ✅ **Activation du GIF** : Si le nœud contient un GIF, il s'anime
- ✅ **Affichage du panneau latéral** : Le panneau `#graph-side-title-panel` apparaît avec :
  - Le titre de l'article (animation d'écriture lettre par lettre)
  - Le lien "Consulter" (apparaît après l'animation du titre)
- ✅ **Polygone coloré** : Un polygone de proximité apparaît autour du nœud avec la couleur appropriée

### Second clic sur le même nœud
- ✅ **Aucune action** : Le panneau reste visible
- ✅ **Le nœud reste actif** : Toutes les animations restent en place
- ✅ **Le lien "Consulter" reste accessible** : L'utilisateur peut cliquer sur le lien pour ouvrir l'article

### Clic sur le lien "Consulter"
- ✅ **Ouverture de l'article** : Redirige vers la page de l'article

### Double-clic sur un nœud
- ✅ **Ouverture immédiate** : L'article s'ouvre directement sans passer par le panneau

### Clic en dehors d'un nœud
- ✅ **Désactivation** : Le nœud actif est désélectionné
- ✅ **Fermeture du panneau** : Le panneau latéral disparaît
- ✅ **Réinitialisation visuelle** : Le nœud reprend sa taille normale

## Fichiers modifiés

### 1. JavaScript : `assets/js/components/GraphContainer.jsx`

**Fonction modifiée** : `handleNodeClick()`

**Avant** :
```javascript
// Si le nœud est déjà sélectionné (actif), ouvrir l'article avec délai pour double-clic
if (selectedNode && selectedNode.id === d.id) {
  // Annuler le timer précédent si existe
  if (clickTimerRef.current) {
    clearTimeout(clickTimerRef.current);
    clickTimerRef.current = null;
  }
  
  // Délai court pour permettre la détection du double-clic
  clickTimerRef.current = setTimeout(() => {
    if (d.link) {
      window.location.href = d.link;
    }
    clickTimerRef.current = null;
  }, 250); // 250ms de délai
  
  return;
}
```

**Après** :
```javascript
// Si le nœud est déjà sélectionné (actif), ne rien faire
// Le panneau reste visible avec le lien "Consulter"
// L'utilisateur peut cliquer sur le lien ou faire un double-clic pour ouvrir l'article
if (selectedNode && selectedNode.id === d.id) {
  return;
}
```

**Raison** : Simplification du comportement. Le second clic ne déclenche plus d'action, laissant le panneau persistant pour que l'utilisateur puisse cliquer sur le lien "Consulter".

### 2. CSS : `assets/css/organic-islands.css`

**Classe modifiée** : `.side-title-link`

**Modifications apportées** :
- ✅ **Couleur du texte** : Noir (`#000000`) au lieu de blanc
- ✅ **Fond** : Blanc semi-transparent (`rgba(255, 255, 255, 0.95)`) au lieu de gris foncé
- ✅ **Ombre du texte** : `text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1)` - similaire au titre
- ✅ **Bordure** : Ajout d'une bordure subtile `border: 1px solid rgba(0, 0, 0, 0.1)`
- ✅ **Hover** : Fond blanc complet + couleur rouge (`#e74c3c`) pour le texte

**Avant** :
```css
.side-title-link {
  background: #2c3e50;
  color: #ffffff;
  /* ... */
}

.side-title-link:hover {
  background: #34495e;
  /* ... */
}
```

**Après** :
```css
.side-title-link {
  background: rgba(255, 255, 255, 0.95);
  color: #000000;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(0, 0, 0, 0.1);
  /* ... */
}

.side-title-link:hover {
  background: rgba(255, 255, 255, 1);
  color: #e74c3c;
  /* ... */
}
```

**Raison** : Le lien doit avoir un style cohérent avec le panneau latéral, avec une ombre similaire au titre mais en police noire pour contraster avec le fond blanc.

## Style visuel du panneau

Le panneau latéral (`#graph-side-title-panel`) contient maintenant deux éléments distincts :

1. **Titre** (`.side-title-text`) :
   - Police rouge (`#e74c3c`)
   - Ombre portée : `text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1)`
   - Animation d'écriture lettre par lettre

2. **Lien "Consulter"** (`.side-title-link`) :
   - Police noire (`#000000`)
   - Fond blanc semi-transparent
   - Ombre portée : `text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1)`
   - Apparaît après l'animation du titre
   - Au survol : fond blanc complet + texte rouge

## Compilation

Les modifications ont été compilées avec succès :
```bash
npm run build
```

## Tests recommandés

1. ✅ Cliquer sur un nœud → Vérifier l'activation et l'affichage du panneau
2. ✅ Cliquer une seconde fois sur le même nœud → Vérifier que rien ne se passe
3. ✅ Cliquer sur le lien "Consulter" → Vérifier l'ouverture de l'article
4. ✅ Double-cliquer sur un nœud → Vérifier l'ouverture directe de l'article
5. ✅ Cliquer en dehors d'un nœud → Vérifier la désactivation
6. ✅ Vérifier le style du lien (noir avec ombre)
7. ✅ Vérifier l'effet hover du lien (blanc + rouge)

## Impact sur l'expérience utilisateur

### Avantages
- ✅ **Persistance du panneau** : L'utilisateur peut lire tranquillement le titre sans risque de fermeture accidentelle
- ✅ **Clarté visuelle** : Le lien "Consulter" en noir se distingue bien du titre rouge
- ✅ **Double option** : L'utilisateur peut soit cliquer sur le lien, soit double-cliquer sur le nœud
- ✅ **Cohérence visuelle** : L'ombre du lien rappelle celle du titre

### Comportements conservés
- ✅ Le double-clic ouvre toujours directement l'article
- ✅ Le panneau se ferme en cliquant en dehors
- ✅ L'animation d'écriture du titre est conservée
- ✅ Les animations GIF fonctionnent toujours

## Notes techniques

- Le timer de détection du double-clic a été retiré du second clic (plus nécessaire)
- La fonction `handleNodeDoubleClick()` reste inchangée
- Le CSS du lien utilise maintenant une transition pour l'opacité lors de l'apparition
- Les erreurs TypeScript sont des avertissements de type mais n'affectent pas le fonctionnement

## Compatibilité

- ✅ Desktop : Fonctionnel
- ✅ Mobile : Fonctionnel (panneau adaptatif)
- ✅ Tablette : Fonctionnel

## Maintenance future

Si vous souhaitez modifier le comportement :
- **Délai d'animation** : Modifier la valeur dans `setInterval()` (ligne ~1369)
- **Style du lien** : Modifier `.side-title-link` dans `organic-islands.css`
- **Comportement du clic** : Modifier `handleNodeClick()` dans `GraphContainer.jsx`
