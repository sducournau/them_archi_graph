# 📋 Résumé des Corrections - Paramètres du Graphique

**Date:** 13 novembre 2025  
**Objectif:** Harmoniser tous les paramètres par défaut du graphique

---

## ✅ FICHIERS CORRIGÉS

### 1. Configuration PHP
**Fichier:** `inc/graph-config.php`

```php
'visual' => [
    'default_node_size' => 80,  // ✅ 120 → 80
],
'physics' => [
    'charge_strength' => -200,   // ✅ -800 → -200
    'link_distance' => 100,      // ✅ 80 → 100
    'collision_radius' => 50,    // ✅ 65 → 50
],
```

### 2. Configuration JavaScript
**Fichier:** `assets/js/utils/graph-settings-helper.js`

```javascript
defaultNodeSize: 80,  // ✅ 60 → 80
```

### 3. Utilitaires JavaScript
**Fichiers corrigés:**

- ✅ `assets/js/utils/graphHelpers.js`
  ```javascript
  .radius((d) => (d.node_size || 80) / 2 + ...)  // 120 → 80
  ```

- ✅ `assets/js/utils/dataFetcher.js`
  ```javascript
  article.node_size = article.node_size || 80;  // 60 → 80
  ```

- ✅ `assets/js/utils/categoryColors.js`
  ```javascript
  const nodeSize = d.node_size || settings.defaultNodeSize || 80;  // 60 → 80
  ```

### 4. Composants React
**Fichier:** `assets/js/components/GraphContainer.jsx`
- ✅ Déjà à 80px (valeur de référence correcte)

---

## ⚠️ FICHIERS À CORRIGER MANUELLEMENT

Les fichiers suivants contiennent encore des valeurs hardcodées à `60`:

### Utilitaires:
- [ ] `assets/js/utils/physicsUtils.js` (2 occurrences)
- [ ] `assets/js/utils/nodeVisualEffects.js` (2 occurrences)
- [ ] `assets/js/utils/nodeInteractions.js` (1 occurrence)
- [ ] `assets/js/utils/GraphManager.js` (~14 occurrences)
- [ ] `assets/js/utils/advancedShapes.js` (4 occurrences)

### Composants:
- [ ] `assets/js/components/Node.jsx` (6 occurrences)

**Script fourni:** `utilities/maintenance/harmonize-node-sizes.sh`

---

## 🎯 VALEURS HARMONISÉES

| Paramètre | Valeur Harmonisée | Justification |
|-----------|-------------------|---------------|
| `defaultNodeSize` | **80px** | Équilibre optimal densité/lisibilité |
| `chargeStrength` | **-200** | Répulsion modérée, bon espacement |
| `linkDistance` | **100** | Distance confortable entre nœuds liés |
| `collision_radius` | **50** | 80/2 + 10 padding (évite chevauchements) |
| `clusterStrength` | **0.1** | Déjà cohérent partout ✅ |

---

## 📦 PROCHAINES ÉTAPES

1. **Corrections manuelles restantes:**
   ```bash
   cd /mnt/c/wamp64/www/wordpress/wp-content/themes/archi-graph-template
   bash utilities/maintenance/harmonize-node-sizes.sh
   ```

2. **Rebuild des assets:**
   ```bash
   npm run build
   # ou
   npm run dev
   ```

3. **Clear cache:**
   - WP Fastest Cache: Vider tout le cache
   - Navigateur: Ctrl+F5 (hard refresh)

4. **Tests:**
   - Vérifier affichage de la page d'accueil
   - Tester zoom/pan sur le graphique
   - Vérifier que les nœuds ne se chevauchent plus
   - Tester le customizer (modifier `defaultNodeSize`)

---

## 🔍 ERREURS CONSOLE POTENTIELLEMENT RÉSOLUES

Les erreurs suivantes dans la console devaient être causées par les incohérences:

❌ **Avant:**
```
Error: <text> attribute y: Expected length, "NaN"
Error: <text> attribute y: Expected length, "-400"
Error: <text> attribute y: Expected length, "465.3168140..."
```

✅ **Après:**
- Tailles cohérentes évitent les calculs NaN
- Positions calculées correctement
- Collisions précises (pas de chevauchements)

---

## 📚 DOCUMENTATION

- Guide complet: `docs/GRAPH-PARAMETERS-FIX.md`
- Script d'harmonisation: `utilities/maintenance/harmonize-node-sizes.sh`

---

## ✨ RÉSULTAT ATTENDU

### Comportement graphique:
- ✅ Tous les nœuds font 80px par défaut
- ✅ Espacement uniforme et prévisible
- ✅ Pas de chevauchements grâce à `collision_radius: 50`
- ✅ Répulsion optimale avec `chargeStrength: -200`
- ✅ Distance confortable entre nœuds liés: 100px

### Performance:
- ✅ Pas d'erreurs console dues aux NaN
- ✅ Simulation plus stable
- ✅ Rendu plus fluide

---

**Statut:** 🟡 Partiellement complété (fichiers principaux ✅, utilitaires ⏳)  
**Tests:** ⏳ En attente après rebuild
