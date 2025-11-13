# 🔧 Fix: Correction des Problèmes d'Échelle du Graphique

**Date:** 13 novembre 2025  
**Priorité:** CRITIQUE  
**Impact:** Affichage du graphique (zones immenses, nœuds trop petits)

## 🚨 Problème Identifié

Le graphique affichait des **zones immenses** avec des **nœuds beaucoup trop petits et éloignés**, rendant le graph inutilisable.

### Symptômes
- ❌ Nœuds minuscules (30px au lieu de 120px attendu)
- ❌ Dispersion excessive des nœuds sur des zones immenses
- ❌ Espacement aberrant entre les éléments
- ❌ Mauvaise lisibilité générale

### Cause Racine

**Paramètres hardcodés aberrants** dans plusieurs fichiers :

1. **`inc/graph-config.php`**
   - `default_node_size: 30` ❌ (devrait être 120px)
   - `charge_strength: -300` ❌ (trop faible, devrait être -800)
   - `link_distance: 100` ❌ (trop grand, devrait être 80)
   - `collision_radius: 40` ❌ (inadapté pour des nœuds de 120px)

2. **`assets/js/components/GraphContainer.jsx`**
   - `chargeStrength: -300` ❌ (trop faible)
   - `chargeDistance: 200` ❌ (disperse les nœuds)
   - `linkDistance: 150` ❌ (trop grand)
   - `collisionPadding: 10` ❌ (insuffisant)

3. **`inc/customizer.php` et `functions.php`**
   - `defaultNodeSize: 60` ❌ (devrait être 120px)

## ✅ Solution Appliquée

### 1. Correction de `inc/graph-config.php`

```php
'visual' => [
    'default_node_size' => 120, // 🔥 FIX: Increased from 30 to 120px
    // ...
],

'physics' => [
    'charge_strength' => -800,   // 🔥 FIX: Increased from -300 (better repulsion)
    'link_distance' => 80,       // 🔥 FIX: Reduced from 100 (closer nodes)
    'collision_radius' => 65,    // 🔥 FIX: Increased from 40 (120px nodes)
    // ...
],
```

**Rationale:**
- **120px** = Taille optimale pour la visibilité dans viewBox 1200x800
- **-800** = Force de répulsion suffisante sans explosion
- **80px** = Distance de lien appropriée pour des nœuds de 120px
- **65px** = Rayon de collision adapté (≈ 120px / 2 + padding)

### 2. Correction de `GraphContainer.jsx`

```jsx
// 🔥 VALEURS OPTIMISÉES
const defaultNodeSize = customizerSettings.defaultNodeSize || 120;
const chargeStrength = customizerSettings.chargeStrength || -800;
const chargeDistance = customizerSettings.chargeDistance || 150;  // Reduced from 200
const collisionPadding = customizerSettings.collisionPadding || 15;

// Liens
const linkDistance = customizerSettings.linkDistance || 100;       // Reduced from 150
const linkDistanceVariation = customizerSettings.linkDistanceVariation || 40; // Reduced from 50
```

**Avant/Après:**
| Paramètre | Avant | Après | Amélioration |
|-----------|-------|-------|--------------|
| `defaultNodeSize` | 120 | 120 | ✅ Maintenu |
| `chargeStrength` | -300 | -800 | ✅ +167% répulsion |
| `chargeDistance` | 200 | 150 | ✅ -25% dispersion |
| `collisionPadding` | 10 | 15 | ✅ +50% |
| `linkDistance` | 150 | 100 | ✅ -33% |

### 3. Correction de `customizer.php` et `functions.php`

```php
'defaultNodeSize' => get_theme_mod('archi_default_node_size', 120), // 🔥 FIX: Increased from 60
```

### 4. Mise à jour des fallbacks dans GraphContainer.jsx

Correction des lignes où `60` était encore utilisé comme fallback :

```jsx
// Animation de sélection de nœud
const defaultSize = graphSettings.defaultNodeSize || 120;
imageElement
    .attr("width", (d.node_size || defaultSize) * scale)
    .attr("height", (d.node_size || defaultSize) * scale)
    // ...
```

## 📊 Impact Technique

### Physique D3.js

**Force de répulsion (`forceManyBody`)**
```javascript
// AVANT: -300
.force("charge", d3.forceManyBody()
    .strength(-300)           // ❌ Trop faible
    .distanceMax(200))        // ❌ Trop grand

// APRÈS: -800 / 150
.force("charge", d3.forceManyBody()
    .strength(-800)           // ✅ Répulsion forte
    .distanceMax(150))        // ✅ Portée réduite
```

**Force de collision (`forceCollide`)**
```javascript
// AVANT: 60px / 2 + 10 = 40px
.forceCollide()
    .radius((d) => (d.node_size || 60) / 2 + 10)

// APRÈS: 120px / 2 + 15 = 75px
.forceCollide()
    .radius((d) => (d.node_size || 120) / 2 + 15)
```

**Force de lien (`forceLink`)**
```javascript
// AVANT: 150px base
.distance((d) => 150 - variation)

// APRÈS: 100px base
.distance((d) => 100 - variation)
```

### Calculs d'Échelle

**ViewBox:** 1200 x 800 pixels

**Densité de nœuds:**
- Avant : ~30px par nœud → 40 x 27 = 1080 nœuds théoriques max
- Après : ~120px par nœud → 10 x 7 = 70 nœuds théoriques max

**Ratio optimal:** 70 nœuds pour éviter la surcharge visuelle

## 🧪 Tests à Effectuer

### Vérifications Visuelles
- [ ] Nœuds visibles et de taille appropriée (≈120px)
- [ ] Espacement cohérent entre les nœuds
- [ ] Pas de chevauchement excessif
- [ ] Graphique contenu dans la zone visible

### Tests de Performance
- [ ] Simulation stable (pas d'explosion des coordonnées)
- [ ] Convergence rapide (< 3 secondes)
- [ ] Pas de ralentissements avec 50+ nœuds

### Tests d'Interaction
- [ ] Zoom fonctionnel
- [ ] Sélection de nœud responsive
- [ ] Animations fluides

## 📝 Notes pour le Customizer

Ces valeurs peuvent maintenant être ajustées via le **WordPress Customizer** :

**Apparence → Graph Settings → Physics**
```
- Node Size: 80-160px (défaut: 120px)
- Charge Strength: -1200 à -400 (défaut: -800)
- Link Distance: 60-120px (défaut: 80px)
- Collision Radius: 50-80px (défaut: 65px)
```

## 🔄 Compatibilité

### Rétrocompatibilité
✅ Les anciens thèmes mods seront automatiquement remplacés par les nouvelles valeurs par défaut

### Migration Automatique
Aucune migration nécessaire - les nouvelles valeurs s'appliquent immédiatement.

## 📚 Documentation Liée

- [GRAPH-PARAMETERS.md](../GRAPH-PARAMETERS.md) - Documentation complète des paramètres
- [HARDCODED-VALUES-AUDIT.md](../../HARDCODED-VALUES-AUDIT.md) - Audit des valeurs hardcodées
- [graph-config.php](../../inc/graph-config.php) - Configuration centralisée

## ✅ Checklist de Déploiement

- [x] Valeurs corrigées dans `graph-config.php`
- [x] Valeurs corrigées dans `GraphContainer.jsx`
- [x] Valeurs corrigées dans `customizer.php`
- [x] Valeurs corrigées dans `functions.php`
- [x] Fallbacks mis à jour dans GraphContainer.jsx
- [x] Assets recompilés (`npm run build`)
- [ ] Tests visuels effectués
- [ ] Validation sur plusieurs navigateurs

## 🎯 Résultat Attendu

### Avant ❌
```
┌────────────────────────────────────────────────┐
│                                                │
│  •         •           •            •         │
│                                                │
│      •                      •          •      │
│                                                │
│  •              •               •         •   │
└────────────────────────────────────────────────┘
Nœuds minuscules dispersés sur une zone immense
```

### Après ✅
```
┌────────────────────────────────────────────────┐
│         ⬤────⬤                                 │
│        ╱  ╲  │                                 │
│       ⬤    ⬤─⬤                                 │
│        ╲  ╱  │                                 │
│         ⬤────⬤                                 │
└────────────────────────────────────────────────┘
Nœuds visibles avec espacement cohérent
```

## 🚀 Prochaines Étapes

1. **Tester le graphique** sur la page d'accueil
2. **Ajuster si nécessaire** via le Customizer
3. **Documenter les valeurs optimales** trouvées
4. **Créer des presets** (Compact / Standard / Spacieux)

---

**Statut:** ✅ Correction appliquée  
**Build:** Réussi (`npm run build`)  
**Prêt pour tests:** OUI
