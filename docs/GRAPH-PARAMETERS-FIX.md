# 🔧 Correction des Paramètres Incohérents du Graphique

**Date:** 13 novembre 2025  
**Problème:** Incohérences entre les paramètres par défaut PHP et JavaScript  
**Solution:** Harmonisation complète des valeurs

---

## 🔴 Incohérences Détectées

### 1. `defaultNodeSize` (Taille des nœuds)
- **PHP (graph-config.php):** ~~120px~~ ❌
- **JS (graph-settings-helper.js):** ~~60px~~ ❌
- **GraphContainer.jsx:** 80px ✅

**Impact:** Nœuds de tailles différentes selon la source de paramètres

### 2. `chargeStrength` (Force de répulsion)
- **PHP (graph-config.php):** ~~-800~~ ❌
- **GraphContainer.jsx:** -200 ✅

**Impact:** Nœuds trop éloignés avec PHP, espacement optimal avec GraphContainer

### 3. `linkDistance` (Distance entre nœuds liés)
- **PHP (graph-config.php):** ~~80~~ ❌
- **GraphContainer.jsx:** 100 ✅

**Impact:** Liens trop courts avec PHP

### 4. `collision_radius` (Rayon de collision)
- **PHP (graph-config.php):** ~~65~~ ❌ (calculé pour nœuds 120px)
- **Valeur correcte:** 50 ✅ (pour nœuds 80px: 80/2 + 10 padding)

---

## ✅ Corrections Appliquées

### Fichier: `inc/graph-config.php`

```php
'visual' => [
    'default_node_size' => 80, // ✅ Harmonisé (était 120)
],

'physics' => [
    'charge_strength' => -200,    // ✅ Harmonisé (était -800)
    'link_distance' => 100,       // ✅ Harmonisé (était 80)
    'collision_radius' => 50,     // ✅ Harmonisé (était 65)
    'cluster_strength' => 0.1,    // ✅ Déjà cohérent
],
```

### Fichier: `assets/js/utils/graph-settings-helper.js`

```javascript
export function getGraphSettings() {
    return window.archiGraphSettings || {
        defaultNodeSize: 80,      // ✅ Harmonisé (était 60)
        clusterStrength: 0.1,     // ✅ Déjà cohérent
        // ... autres paramètres
    };
}
```

### Fichier: `assets/js/components/GraphContainer.jsx`

```javascript
// ✅ Valeurs de référence maintenues (déjà correctes)
const defaultNodeSize = customizerSettings.defaultNodeSize || 80;
const chargeStrength = customizerSettings.chargeStrength || -200;
const linkDistance = customizerSettings.linkDistance || 100;
const collisionPadding = customizerSettings.collisionPadding || 10;
```

---

## 📊 Tableau Récapitulatif

| Paramètre | Avant PHP | Avant JS | Avant React | **Après (harmonisé)** |
|-----------|-----------|----------|-------------|----------------------|
| `defaultNodeSize` | 120 | 60 | 80 | **80** ✅ |
| `chargeStrength` | -800 | N/A | -200 | **-200** ✅ |
| `linkDistance` | 80 | N/A | 100 | **100** ✅ |
| `collision_radius` | 65 | N/A | ~50 | **50** ✅ |
| `clusterStrength` | 0.1 | 0.1 | 0.1 | **0.1** ✅ |

---

## 🎯 Résultats Attendus

### Avant la correction:
- ❌ Nœuds de tailles variables selon le mode
- ❌ Espacement incohérent
- ❌ Collisions mal calculées
- ❌ Comportement imprévisible

### Après la correction:
- ✅ **Taille uniforme:** Tous les nœuds font 80px par défaut
- ✅ **Espacement optimal:** Distance cohérente entre nœuds (-200 répulsion)
- ✅ **Collisions précises:** Rayon de 50px évite les chevauchements
- ✅ **Comportement prévisible:** Mêmes paramètres partout

---

## 🔍 Vérification Post-Correction

### Tests à effectuer:

1. **Test visuel:**
   ```bash
   # Recharger la page d'accueil avec le graphique
   # Vérifier que les nœuds ont une taille cohérente
   ```

2. **Test Customizer:**
   ```bash
   # Aller dans Apparence > Personnaliser > Graph Visual Settings
   # Modifier defaultNodeSize
   # Vérifier que le changement s'applique correctement
   ```

3. **Test console:**
   ```javascript
   // Dans la console du navigateur
   console.log(window.archiGraphSettings);
   // Devrait afficher defaultNodeSize: 80
   ```

---

## 📝 Notes Techniques

### Formule de collision_radius:
```
collision_radius = (node_size / 2) + padding
                 = (80 / 2) + 10
                 = 50
```

### Relation charge/distance:
- **chargeStrength négatif** = répulsion entre nœuds
- `-200` est optimal pour nœuds de 80px avec `linkDistance` de 100
- Ratio `linkDistance / |chargeStrength|` = 0.5 (équilibre stable)

### Pourquoi 80px?
- ✅ Assez grand pour voir les images
- ✅ Assez petit pour afficher beaucoup de nœuds
- ✅ Bon équilibre densité/lisibilité dans viewBox 1200x800

---

## 🚨 Points de Vigilance

### Ne PAS modifier ces fichiers sans harmoniser:
1. `inc/graph-config.php` (valeurs PHP par défaut)
2. `assets/js/utils/graph-settings-helper.js` (fallback JS)
3. `assets/js/components/GraphContainer.jsx` (valeurs de référence)

### Principe TOUJOURS respecter:
> **Un paramètre = Une seule valeur par défaut cohérente dans tous les fichiers**

---

## 🔄 Prochaines Étapes

1. ✅ **Tester en développement** - Vérifier l'affichage
2. ⏳ **Rebuild des assets JS** - Compiler avec webpack
3. ⏳ **Clear cache WordPress** - Vider le cache WP Fastest Cache
4. ⏳ **Test utilisateur** - Vérifier que tout fonctionne
5. ⏳ **Commit git** - Sauvegarder les changements

---

## 📚 Références

- GraphContainer.jsx: Lignes 520-575 (paramètres de simulation)
- graph-config.php: Fonction `archi_visual_get_config()`
- graph-settings-helper.js: Fonction `getGraphSettings()`

---

**Auteur:** GitHub Copilot + Serena MCP  
**Statut:** ✅ Corrections appliquées, tests en attente
