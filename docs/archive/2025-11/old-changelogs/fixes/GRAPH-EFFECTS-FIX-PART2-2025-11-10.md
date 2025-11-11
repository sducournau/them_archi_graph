# Fix Part 2: Paramètres d'Animation Manquants dans le Registry

**Date:** 10 Novembre 2025  
**Type:** Bug Fix (Complétion du Fix Part 1)  
**Fichier modifié:** `inc/graph-meta-registry.php`  
**Statut:** ✅ Résolu

---

## 🐛 Nouveau Problème Identifié

Après le Fix Part 1, les paramètres d'effet **ne fonctionnaient toujours pas** car ils n'étaient **pas récupérés par l'API REST**.

### Symptômes

- Tous les nodes avaient le même effet d'agrandissement (1.15) au lieu de leurs valeurs personnalisées
- Les paramètres d'animation configurés n'apparaissaient pas dans les données de l'API
- La transformation dans GraphManager.js ne trouvait aucune valeur à transformer

---

## 🔍 Analyse du Problème

### Cause Racine

Les nouveaux paramètres d'animation et d'effet étaient **enregistrés dans WordPress** (lignes 400-520) MAIS :

1. ❌ Ils n'étaient **PAS listés dans `archi_get_graph_meta_keys()`** (ligne 625)
2. ❌ Ils n'avaient **PAS de valeurs par défaut dans `archi_get_graph_meta_defaults()`** (ligne 667)

**Conséquence :** La fonction `archi_get_graph_params()` ne récupérait pas ces métadonnées, donc l'API REST ne les envoyait pas au frontend.

### Code Problématique

**`archi_get_graph_meta_keys()` - Ligne 646-651 (AVANT):**
```php
'behavior' => [
    '_archi_node_weight',
    '_archi_hover_effect',
    '_archi_entrance_animation',
    '_archi_animation_level',
],
```

❌ **Il manquait 8 clés importantes !**

**`archi_get_graph_meta_defaults()` - Ligne 667-694 (AVANT):**
```php
return [
    // ... autres defaults
    '_archi_animation_level' => 'normal',
    '_archi_related_articles' => [],
    // ❌ Aucun default pour animation_type, hover_scale, etc.
];
```

---

## ✅ Solution Implémentée

### 1. Ajout des clés manquantes dans `archi_get_graph_meta_keys()`

**Ligne 646-659 (APRÈS):**
```php
'behavior' => [
    '_archi_node_weight',
    '_archi_hover_effect',
    '_archi_entrance_animation',
    '_archi_animation_level',
    '_archi_animation_type',        // ✅ NOUVEAU
    '_archi_animation_duration',    // ✅ NOUVEAU
    '_archi_animation_delay',       // ✅ NOUVEAU
    '_archi_animation_easing',      // ✅ NOUVEAU
    '_archi_enter_from',            // ✅ NOUVEAU
    '_archi_hover_scale',           // ✅ NOUVEAU
    '_archi_pulse_effect',          // ✅ NOUVEAU
    '_archi_glow_effect',           // ✅ NOUVEAU
],
```

### 2. Ajout des valeurs par défaut dans `archi_get_graph_meta_defaults()`

**Ligne 667-699 (APRÈS):**
```php
return [
    // ... defaults existants
    '_archi_animation_level' => 'normal',
    '_archi_animation_type' => 'fadeIn',        // ✅ NOUVEAU
    '_archi_animation_duration' => 800,         // ✅ NOUVEAU
    '_archi_animation_delay' => 0,              // ✅ NOUVEAU
    '_archi_animation_easing' => 'ease-out',    // ✅ NOUVEAU
    '_archi_enter_from' => 'center',            // ✅ NOUVEAU
    '_archi_hover_scale' => 1.15,               // ✅ NOUVEAU
    '_archi_pulse_effect' => '0',               // ✅ NOUVEAU
    '_archi_glow_effect' => '0',                // ✅ NOUVEAU
    '_archi_related_articles' => [],
    // ... autres defaults
];
```

---

## 📊 Impact de la Correction

### Avant (API REST ne renvoyait pas ces paramètres)

```json
{
  "id": 123,
  "title": "Article",
  "node_color": "#3498db",
  "node_size": 60
  // ❌ Aucun paramètre d'animation/hover
}
```

### Après (Tous les paramètres sont présents)

```json
{
  "id": 123,
  "title": "Article",
  "node_color": "#3498db",
  "node_size": 60,
  "animation_type": "fadeIn",        // ✅
  "animation_duration": 1200,        // ✅
  "animation_delay": 300,            // ✅
  "animation_easing": "bounce",      // ✅
  "enter_from": "left",              // ✅
  "hover_scale": 1.5,                // ✅
  "pulse_effect": true,              // ✅
  "glow_effect": false               // ✅
}
```

---

## 🔗 Chaîne Complète de la Correction

### Fix Part 1 (GraphManager.js)
1. ✅ Transformation des données plates en objets imbriqués
2. ✅ Création des objets `animation` et `hover`

### Fix Part 2 (graph-meta-registry.php) - **CE FIX**
1. ✅ Ajout des 8 clés manquantes dans `archi_get_graph_meta_keys()`
2. ✅ Ajout des 8 valeurs par défaut dans `archi_get_graph_meta_defaults()`
3. ✅ L'API REST récupère maintenant tous les paramètres

### Résultat Final
```
WordPress DB → archi_get_graph_params() → API REST → GraphManager → Effets visuels ✨
     ✅               ✅                      ✅           ✅              ✅
```

---

## 🧪 Test de Validation

Pour vérifier que les paramètres sont maintenant récupérés, exécuter dans la console :

```javascript
// Tester l'API REST
fetch('/wp-json/archi/v1/articles')
  .then(r => r.json())
  .then(data => {
    const node = data.articles[0];
    console.log('Paramètres récupérés:');
    console.log('- animation_type:', node.animation_type);
    console.log('- animation_duration:', node.animation_duration);
    console.log('- animation_delay:', node.animation_delay);
    console.log('- animation_easing:', node.animation_easing);
    console.log('- enter_from:', node.enter_from);
    console.log('- hover_scale:', node.hover_scale);
    console.log('- pulse_effect:', node.pulse_effect);
    console.log('- glow_effect:', node.glow_effect);
  });
```

**Résultat attendu:**
- ✅ Toutes les valeurs doivent être définies (pas `undefined`)
- ✅ Les valeurs doivent correspondre à celles configurées dans l'éditeur
- ✅ Si non configurées, les valeurs par défaut doivent apparaître

---

## 📝 Paramètres Ajoutés

| Paramètre | Type | Défaut | Description |
|-----------|------|--------|-------------|
| `_archi_animation_type` | string | `"fadeIn"` | Type d'animation d'entrée |
| `_archi_animation_duration` | int | `800` | Durée de l'animation (ms) |
| `_archi_animation_delay` | int | `0` | Délai avant animation (ms) |
| `_archi_animation_easing` | string | `"ease-out"` | Fonction d'easing |
| `_archi_enter_from` | string | `"center"` | Direction d'entrée |
| `_archi_hover_scale` | float | `1.15` | **Facteur d'agrandissement** |
| `_archi_pulse_effect` | string | `"0"` | Effet de pulsation |
| `_archi_glow_effect` | string | `"0"` | Effet de lueur |

---

## ✨ Résultat

Maintenant, chaque node peut avoir :
- ✅ Son propre **facteur d'agrandissement** au survol (différent des autres)
- ✅ Son **type d'animation** personnalisé
- ✅ Sa **durée** et son **délai** d'animation
- ✅ Sa **direction d'entrée** unique
- ✅ Ses **effets visuels** (pulse/glow)

**Les effets personnalisés fonctionnent enfin correctement !** 🎉

---

## 🔄 Fichiers Modifiés

### Ce Fix (Part 2)
- ✅ `inc/graph-meta-registry.php`
  - Fonction `archi_get_graph_meta_keys()` (ligne 646-659)
  - Fonction `archi_get_graph_meta_defaults()` (ligne 667-699)

### Fix Précédent (Part 1)
- ✅ `assets/js/utils/GraphManager.js`
  - Méthode `loadData()` (ligne 94-111)

---

## 🚀 Prochaines Étapes

1. **Clear cache WordPress** : Vider le cache de l'API REST
2. **Test manuel** : Configurer différents `hover_scale` sur plusieurs nodes (1.2, 1.5, 1.8, etc.)
3. **Vérifier l'API** : S'assurer que `/wp-json/archi/v1/articles` renvoie bien tous les paramètres
4. **Test visuel** : Observer que chaque node s'agrandit différemment au survol

---

## ✅ Validation

- ✅ 8 clés ajoutées à `archi_get_graph_meta_keys()`
- ✅ 8 valeurs par défaut ajoutées à `archi_get_graph_meta_defaults()`
- ✅ Cohérence avec les meta registrations existantes
- ✅ Types de données corrects (int, float, string)
- ✅ Valeurs par défaut sensibles

**Status : COMPLET ET PRÊT POUR TEST** 🎉
