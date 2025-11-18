# Optimisation du Rendu du Graphe - 18 Novembre 2025

## 🎯 Problème Identifié

Les nodes étaient bien placées au départ au centre, mais étaient **brutalement repoussées vers les 4 coins d'un polygone** après le placement initial.

### Cause Racine

La fonction `createClusterCenters` positionnait les centres des clusters en **grille 2x2** quand il y avait ≤4 catégories, créant ainsi 4 points d'attraction aux coins du canvas. Combiné avec une force de clustering trop forte, cela causait une dispersion brutale.

## 🔥 Optimisations Appliquées

### 1. **Disposition Circulaire Centrale** (`createClusterCenters`)

```javascript
// ❌ AVANT: Grille 2x2 pour ≤4 catégories (4 coins)
if (categories.length <= 4) {
  const cols = 2;
  x = padding + (col + 0.5) * (usableWidth / cols); // Corners!
  y = padding + (row + 0.5) * (usableHeight / ...);
}

// ✅ APRÈS: TOUJOURS disposition circulaire centrale
const angle = (index / categories.length) * 2 * Math.PI;
const radius = Math.min(usableWidth, usableHeight) / 6; // ULTRA-COMPACT
x = width / 2 + Math.cos(angle) * radius;
y = height / 2 + Math.sin(angle) * radius;
```

**Impact**: Les centres de clusters sont maintenant toujours concentrés au centre en cercle compact.

### 2. **Force de Clustering Ultra-Réduite** (`createForceSimulation`)

| Paramètre | Avant | Après | Impact |
|-----------|-------|-------|--------|
| `clusterStrength` | 0.20 | **0.08** | 🔥 -60% |
| Multiplicateur | 0.4 | **0.10** | 🔥 -75% |
| **Force finale** | 0.08 | **0.008** | 🔥 **-90%** |

**Impact**: Les nodes ne sont presque plus attirées vers les centres de clusters, permettant une superposition naturelle.

### 3. **Placement Initial Ultra-Compact**

```javascript
// Rayon de placement initial
const centerRadius = Math.min(width, height) * 0.12; // 🔥 RÉDUIT de 15% à 12%

// Vélocité initiale minimale
node.vx = (Math.random() - 0.5) * 3; // 🔥 RÉDUIT de 5 à 3
```

**Impact**: Les nodes démarrent plus concentrées et avec moins d'énergie cinétique.

### 4. **Force de Répulsion Ultra-Faible**

| Paramètre | Avant | Après | Impact |
|-----------|-------|-------|--------|
| `baseStrength` | -50 | **-42** | 🔥 -16% |
| `distanceMax` | 250px | **200px** | Influence très locale |
| `distanceMin` | 35px | **30px** | Proximité extrême autorisée |
| Réduction max | 50% | **60%** | Plus de réduction pour nodes connectées |

**Impact**: Permet une densité maximale et une superposition naturelle des clusters.

### 5. **Force de Liens Renforcée**

| Paramètre | Avant | Après | Impact |
|-----------|-------|-------|--------|
| `linkStrength` | 0.25 | **0.30** | 🔥 +20% |
| `baseDistance` | 100px | **85px** | 🔥 -15% |
| Strength min/max | 0.35-0.65 | **0.40-0.70** | 🔥 Plus fort |
| `minProximityScore` | 35 | **30** | Plus de connexions |
| `maxLinksPerNode` | 10 | **12** | Meilleure densité |

**Impact**: Les nodes connectées restent beaucoup plus proches, créant des groupes cohésifs.

### 6. **Collision Ultra-Souple**

```javascript
// Marge de sécurité
const safetyMargin = 5; // 🔥 RÉDUIT de 8 à 5

// Force de collision
.strength(0.45) // 🔥 RÉDUIT de 0.60 à 0.45

// Itérations
.iterations(1) // 🔥 RÉDUIT de 2 à 1
```

**Impact**: Permet une superposition partielle des nodes pour un rendu plus organique.

### 7. **Convergence Optimisée**

| Paramètre | Avant | Après | Impact |
|-----------|-------|-------|--------|
| `alpha` | 0.7 | **0.6** | Démarrage plus doux |
| `alphaDecay` | 0.028 | **0.022** | Convergence plus lente |
| `alphaMin` | 0.002 | **0.001** | Arrêt ultra-précis |
| `velocityDecay` | 0.62 | **0.70** | Freinage plus fort |

**Impact**: Animation plus fluide et stable, sans mouvements brusques.

### 8. **Réduction Force Cluster pour Nodes Connectées** (`forceCluster`)

```javascript
// ❌ AVANT
const clusterReduction = Math.min(linkCount / 8, 0.75); // Max 75%

// ✅ APRÈS
const clusterReduction = Math.min(linkCount / 6, 0.85); // 🔥 Max 85%
```

**Impact**: Les nodes avec beaucoup de liens ignorent presque complètement le clustering.

## 📊 Résultats Attendus

### Avant
- ❌ Placement initial correct au centre
- ❌ Dispersion brutale vers les 4 coins après quelques ticks
- ❌ Formation d'un polygone avec nodes aux coins
- ❌ Clusters séparés artificiellement

### Après
- ✅ Placement initial ultra-compact au centre
- ✅ **Maintien de la concentration centrale**
- ✅ Superposition naturelle et organique des clusters
- ✅ Les liens maintiennent les groupes cohésifs
- ✅ Animation fluide sans mouvements brusques
- ✅ Clusters se mélangent naturellement

## 🎨 Caractéristiques du Nouveau Rendu

1. **Concentration Centrale**: Tous les clusters gravitent autour du centre
2. **Superposition Organique**: Les clusters se chevauchent naturellement
3. **Cohésion des Groupes**: Les nodes connectées restent proches
4. **Densité Maximale**: Utilisation optimale de l'espace central
5. **Mouvement Fluide**: Pas de sauts ou déplacements brusques
6. **Stabilité**: Convergence douce vers une disposition harmonieuse

## 🧪 Tests Recommandés

1. Rafraîchir la page d'accueil avec le graphe
2. Observer le placement initial (doit être compact au centre)
3. Observer l'évolution pendant la simulation (doit rester central)
4. Vérifier qu'il n'y a pas de dispersion vers les coins
5. Vérifier la superposition naturelle des clusters
6. Tester avec différents nombres de catégories (2, 3, 4, 5+)

## 📁 Fichiers Modifiés

- `assets/js/utils/graphHelpers.js`
  - `createForceSimulation()` - Paramètres de simulation optimisés
  - `createClusterCenters()` - Disposition circulaire centrale toujours
  - `forceCluster()` - Réduction du clustering pour nodes connectées

## 🔄 Prochaines Étapes Possibles

Si le rendu nécessite encore des ajustements:

1. **Plus de densité**: Réduire encore `clusterStrength` à 0.05
2. **Plus de liberté**: Réduire `linkStrength` légèrement
3. **Plus de séparation**: Augmenter légèrement `distanceMin` dans charge
4. **Plus de vitesse**: Augmenter `alphaDecay` pour convergence plus rapide
5. **Plus de stabilité**: Augmenter `velocityDecay` pour freinage plus fort

## 📝 Notes Techniques

- Les paramètres sont maintenant optimisés pour **favoriser la superposition**
- La force de clustering est quasi-nulle pour les nodes très connectées
- Le placement initial est crucial pour éviter les mouvements brusques
- La convergence lente assure une stabilisation harmonieuse
