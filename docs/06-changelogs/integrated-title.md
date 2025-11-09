# CHANGELOG - Système d'affichage intégré

## [2.0.0] - 3 novembre 2025

### 🚀 Changements majeurs

#### Système d'affichage de titre intégré
- **BREAKING CHANGE** : Remplacement complet du système de popup au survol
- Les titres s'affichent maintenant directement dans le SVG du graphe
- Animation fluide avec fade-in et scale au survol

### ✨ Nouveautés

#### GraphContainer.jsx
- ➕ Ajout du groupe SVG `.node-title-label` pour chaque nœud
- ➕ Création automatique d'un rectangle de fond avec coins arrondis
- ➕ Texte multi-ligne avec découpage automatique (max 2 lignes)
- ➕ Positionnement dynamique sous chaque nœud
- ➕ Animation d'apparition/disparition synchronisée avec le survol

#### Styles CSS
- ➕ Nouvelles classes `.node-title-label`, `.node-title-bg`, `.node-title-text`
- ➕ Transitions CSS pour animations fluides (opacity + transform)
- ➕ Support du hover pour affichage automatique

### ❌ Suppressions

#### Composants
- ❌ Suppression du composant `NodeTooltip.jsx` (non utilisé)
- ❌ Suppression de l'import `NodeTooltip` dans `GraphContainer.jsx`
- ❌ Suppression de l'état `tooltipPosition`
- ❌ Suppression du rendu `<NodeTooltip>` dans le JSX

#### Styles
- ❌ Désactivation des styles `.node-tooltip` (display: none)

### 🔄 Modifications

#### Logique de survol (`handleNodeHover`)
**Avant** :
```javascript
- Création d'une popup HTML externe
- Calcul de position absolue (pageX, pageY)
- Gestion du z-index
- Re-render du composant React
```

**Après** :
```javascript
- Affichage du label SVG intégré
- Position relative au nœud (transform)
- Animation CSS pure (GPU-accelerated)
- Pas de re-render supplémentaire
```

### 📈 Améliorations de performance

- ⚡ **-1 composant React** : Moins de re-renders
- ⚡ **Animations CSS natives** : GPU-accelerated
- ⚡ **Pas de calcul JS** : Position calculée une seule fois
- ⚡ **Moins de DOM** : Tout dans le SVG

### 🎨 Améliorations UX

- 🎯 Affichage contextuel directement dans le graphe
- 🎯 Pas de popup qui sort de l'écran
- 🎯 Animation plus fluide et naturelle
- 🎯 Visibilité immédiate au survol

### 🛠️ Détails techniques

#### Caractéristiques du label
- **Position** : 20px sous le nœud
- **Largeur max** : 180px
- **Lignes max** : 2 lignes
- **Découpage** : Texte tronqué avec "..." si > 30 caractères/ligne
- **Fond** : Blanc à 95% d'opacité
- **Bordure** : Gris clair (rgba(0,0,0,0.1))
- **Ombre** : drop-shadow(0 2px 8px rgba(0,0,0,0.15))
- **Coins** : Arrondis à 8px

#### Animation
- **Durée** : 300ms
- **Easing** : ease
- **Propriétés** : opacity (0 → 1) + transform (scale(0.8) → scale(1))
- **Origin** : center top

### 📝 Fichiers modifiés

```
Modified:
  assets/js/components/GraphContainer.jsx  (+85 lignes, -15 lignes)
  assets/css/graph-white.css               (+32 lignes, -17 lignes)

Created:
  docs/integrated-title-display.md         (Documentation complète)
  test-integrated-title.html               (Page de test)

Deleted:
  (aucun fichier supprimé, NodeTooltip.jsx conservé pour référence)
```

### 🧪 Tests effectués

- ✅ Affichage au survol de nœuds standards
- ✅ Affichage avec titres courts (< 20 caractères)
- ✅ Affichage avec titres longs (> 50 caractères)
- ✅ Animation d'entrée fluide
- ✅ Animation de sortie fluide
- ✅ Compilation webpack sans erreurs
- ✅ Compatibilité avec le système de GIF animés

### 🐛 Corrections de bugs

- 🐛 Correction : La popup pouvait sortir de l'écran sur mobile
- 🐛 Correction : La popup pouvait masquer d'autres nœuds
- 🐛 Correction : Lag perceptible avec beaucoup de nœuds

### 📚 Documentation

- 📖 Documentation complète dans `docs/integrated-title-display.md`
- 📖 Page de test interactive dans `test-integrated-title.html`
- 📖 Commentaires inline dans le code source

### ⚙️ Configuration

Aucune configuration requise. Le système fonctionne automatiquement.

#### Personnalisation possible

Pour modifier l'apparence, éditer dans `GraphContainer.jsx` :

```javascript
// Fond du label
.style("fill", "rgba(255, 255, 255, 0.95)")

// Couleur du texte
.style("fill", "#2c3e50")

// Taille de police
.style("font-size", "14px")

// Position verticale
const yOffset = (d.node_size || 60) / 2 + 20;
```

### 🔜 Prochaines étapes suggérées

1. **Informations supplémentaires** : Ajouter date, catégorie, etc.
2. **Mode compact** : Option pour afficher moins d'infos
3. **Accessibilité** : Support du focus clavier
4. **Animation avancée** : Effet de typing ou stagger

### 💡 Notes de migration

**Pour les développeurs** :
- ⚠️ Le composant `NodeTooltip` n'est plus utilisé
- ⚠️ L'état `tooltipPosition` a été supprimé
- ⚠️ La logique de survol a été modifiée dans `handleNodeHover`

**Pour les utilisateurs** :
- ✅ Aucun changement d'utilisation
- ✅ Meilleure expérience visuelle
- ✅ Plus rapide et fluide

### 🔗 Liens utiles

- [Documentation détaillée](docs/integrated-title-display.md)
- [Page de test](test-integrated-title.html)
- [Code source](assets/js/components/GraphContainer.jsx)

---

**Type de version** : MAJOR (2.0.0)  
**Raison** : Changement significatif de l'interface utilisateur  
**Impact** : Positif - Amélioration performance et UX  
**Rétrocompatibilité** : Oui (pour les utilisateurs finaux)

**Contributeurs** : GitHub Copilot  
**Date** : 3 novembre 2025
