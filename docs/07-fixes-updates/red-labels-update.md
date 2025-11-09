# Mise à jour - Labels rouges sans fond

## 🎨 Changements visuels

### Style des étiquettes amélioré

**AVANT** :
- Rectangle blanc avec coins arrondis
- Ombre portée
- Texte gris foncé
- Animation scale + fade

**APRÈS** :
- ✅ **Texte rouge vif (#e74c3c)** directement sur le fond du graphe
- ✅ **Pas de rectangle de fond**
- ✅ **Pas d'ombre**
- ✅ **Texte en MAJUSCULES**
- ✅ **Animation fade simple** (plus rapide et fluide)
- ✅ **Meilleur espacement des lettres** (letter-spacing: 0.5px)
- ✅ **Plus proche du nœud** (15px au lieu de 20px)

## 🔧 Modifications techniques

### GraphContainer.jsx

#### Suppression
```javascript
// ❌ Retiré : Rectangle de fond
titleGroup.append("rect")
  .attr("class", "node-title-bg")
  .style("fill", "rgba(255, 255, 255, 0.95)")
  .style("filter", "drop-shadow(...)");

// ❌ Retiré : Animation scale
.attr("transform", "scale(0.8)")
```

#### Ajout
```javascript
// ✅ Texte rouge directement
.style("font-size", "16px")
.style("font-weight", "700")
.style("fill", "#e74c3c")  // Rouge vif
.style("letter-spacing", "0.5px")
.style("text-transform", "uppercase")

// ✅ Position plus proche
const yOffset = (d.node_size || 60) / 2 + 15; // 15px au lieu de 20px
```

### graph-white.css

```css
/* Simplification des styles */
.node-title-label {
  transition: opacity 0.25s ease;  /* Plus rapide */
  opacity: 0;
}

.node-title-text {
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  paint-order: stroke fill;  /* Meilleure visibilité */
}

/* Support du high contrast */
@media (prefers-contrast: high) {
  .node-title-text {
    font-weight: 900;
    letter-spacing: 1px;
  }
}
```

## 🎯 Résultat visuel

```
        [Image du nœud]
              ↓
       TITRE DU PROJET  ← Texte rouge en majuscules
     SUITE DU TITRE...     Directement sur fond blanc
```

**Caractéristiques** :
- ✨ **Visibilité maximale** : Rouge vif #e74c3c
- ✨ **Style épuré** : Pas de décoration, juste le texte
- ✨ **Animation rapide** : 250ms fade-in
- ✨ **Position optimale** : 15px sous le nœud
- ✨ **Lisibilité** : MAJUSCULES + espacement lettres

## 📊 Comparaison performance

| Aspect | Avant | Après | Gain |
|--------|-------|-------|------|
| **Éléments SVG** | 2 (rect + text) | 1 (text) | -50% |
| **Transitions CSS** | 2 (opacity + transform) | 1 (opacity) | -50% |
| **Durée animation** | 300ms | 250ms | +17% plus rapide |
| **Calculs layout** | 2 (rect + position) | 1 (position) | -50% |

## ✅ Avantages

1. **🎨 Plus épuré** : Pas de boîte, texte direct sur le fond
2. **⚡ Plus rapide** : Moins d'éléments, animation simplifiée
3. **👁️ Plus visible** : Rouge vif contraste fortement avec le fond blanc
4. **🧹 Code plus simple** : Moins de calculs, moins de styles
5. **📱 Meilleur sur mobile** : Texte plus gros (16px au lieu de 14px)

## 🧪 Tests recommandés

- [ ] Vérifier la lisibilité du texte rouge sur fond blanc
- [ ] Tester avec des titres courts et longs
- [ ] Vérifier l'animation au survol
- [ ] Tester sur écran haute résolution
- [ ] Vérifier en mode high contrast

## 🎨 Personnalisation

### Changer la couleur du texte

Dans `GraphContainer.jsx` :
```javascript
.style("fill", "#e74c3c")  // Remplacer par votre couleur
```

Suggestions de couleurs :
- `#e74c3c` - Rouge vif (actuel)
- `#c0392b` - Rouge foncé
- `#e67e22` - Orange
- `#d35400` - Orange foncé
- `#8e44ad` - Violet
- `#2980b9` - Bleu

### Changer la taille du texte

```javascript
.style("font-size", "16px")  // Augmenter ou diminuer
```

### Changer la position

```javascript
const yOffset = (d.node_size || 60) / 2 + 15;  // Ajuster la valeur 15
```

### Retirer les majuscules

Dans `GraphContainer.jsx`, supprimer :
```javascript
.style("text-transform", "uppercase")
```

Et dans le code, retirer :
```javascript
.text(line.toUpperCase());  // → .text(line);
```

## 📦 Fichiers modifiés

```
Modified:
  ✏️ assets/js/components/GraphContainer.jsx  (-30 lignes)
  ✏️ assets/css/graph-white.css               (+15 lignes, -20 lignes)

Created:
  📄 docs/red-labels-update.md
```

## 🚀 Déploiement

```bash
✅ npm run build - Compilation réussie
✅ Fichiers générés :
   - app.bundle.js (129 KiB)
   - vendors.bundle.js (132 KiB)
```

---

**Date** : 3 novembre 2025  
**Version** : 2.1.0  
**Type** : Amélioration visuelle
