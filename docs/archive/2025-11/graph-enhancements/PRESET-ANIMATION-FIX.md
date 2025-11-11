# Fix: Application des Presets aux Animations

**Date**: 2025-01-10  
**Issue**: Le changement de preset (ex: Rich) ne modifiait pas les animations du graph  
**Status**: ✅ Résolu

## Problème Identifié

Le système de configuration par presets était bien créé et transmis au frontend, mais les méthodes d'animation ne l'utilisaient pas :

1. **`applyPerNodeAnimations()`** : Utilisait uniquement `d.animation` des nodes individuels
   - Pas de mapping de `config.animationType` ('fade', 'slide', 'bounce', 'zoom')
   - Fallback hardcodé sur `enterFrom = "center"` au lieu du type global

2. **`applyPerNodeHoverEffects()`** : Utilisait uniquement `d.hover` des nodes
   - Ignorait `config.hoverScale` et `config.hoverEffect`
   - Pas de respect de `config.hoverEnabled`

## Solution Implémentée

### 1. Refactorisation de `applyPerNodeAnimations()` (lignes 507-565)

**Changements** :
- ✅ Vérification de `config.animationEnabled` avant toute animation
- ✅ Utilisation de `config.animationType` comme fallback
- ✅ Création de `getInitialStateByType()` pour mapper les types d'animation
- ✅ Application de `config.animationDuration` et `config.staggerDelay`
- ✅ Easing automatique "bounce" pour le type `animationType: 'bounce'`

**Mapping des types d'animation** :
```javascript
// 'fade' → Apparition sur place (opacity 0 → 1)
// 'slide' → Glissement depuis un bord (top/bottom/left/right/center/random)
// 'bounce' → Apparition avec scale 0 → 1 + easing bounce
// 'zoom' → Zoom sur place avec scale 0 → 1
```

**Priorité des paramètres** :
1. `d.animation.type` (node individuel) - priorité maximale
2. `this.config.animationType` (preset global) - fallback
3. `'slide'` - défaut hardcodé

### 2. Nouvelle méthode `getInitialStateByType()` (lignes 568-625)

Remplace l'ancienne `getInitialState()` avec :
- Support des 4 types d'animation
- Calcul dynamique de la position initiale selon le type
- Flag `scale: true/false` pour indiquer si animation de rayon nécessaire
- Direction aléatoire pour `slide` avec `enterFrom: 'center'`

### 3. Refactorisation de `applyPerNodeHoverEffects()` (lignes 672-781)

**Changements** :
- ✅ Early return si `config.hoverEnabled === false`
- ✅ Utilisation de `config.hoverScale` comme fallback (au lieu de `1.15` hardcodé)
- ✅ Support de `config.hoverEffect` pour déterminer les effets (scale/glow/multi/none)
- ✅ Intensité adaptative selon le scale :
  - `hoverScale > 1.2` (Rich) → Halo 4px, label 15px, shine 0.7
  - `hoverScale ≤ 1.2` (Standard) → Halo 3px, label 14px, shine 0.6

**Priorité des paramètres** :
1. `d.hover.scale` (node individuel) - priorité maximale
2. `this.config.hoverScale` (preset global) - fallback
3. `1.15` - défaut hardcodé

### 4. Séparation du handler de click (lignes 785-843)

Création de `handleNodeClick()` séparée pour :
- Meilleure modularité du code
- Éviter la chaîne `.on().on().on()` trop longue
- Faciliter les futures modifications

**Appel dans `applyAnimations()`** (ligne 498) :
```javascript
this.applyPerNodeHoverEffects();
this.handleNodeClick();
this.applyContinuousEffects();
```

## Résultat par Preset

### Minimal
- **Animation** : `fade` (apparition sur place, 400ms)
- **Hover** : Scale 1.1x, halo 3px, label 14px, shine 0.6

### Standard
- **Animation** : `slide` (glissement depuis bord, 600ms)
- **Hover** : Scale 1.15x, halo 3px, label 14px, shine 0.6

### Rich
- **Animation** : `bounce` (bounce + scale, 800ms, easing bounce)
- **Hover** : Scale 1.25x, halo 4px, label 15px, shine 0.7, glow activé

### Performance
- **Animation** : Désactivées (`animationEnabled: false`)
- **Hover** : Désactivé (`hoverEnabled: false`)

## Tests de Validation

1. ✅ Sélectionner preset "Rich" dans Apparence → Graph Settings
2. ✅ Recharger la page d'accueil avec le graph
3. ✅ Observer les nodes qui "rebondissent" à l'apparition
4. ✅ Survol des nodes avec effet fort (1.25x scale)
5. ✅ Répéter avec les autres presets pour comparer

## Fichiers Modifiés

- `assets/js/utils/GraphManager.js` :
  - `applyPerNodeAnimations()` - lignes 507-565 (refactorisation complète)
  - `getInitialStateByType()` - lignes 568-625 (nouvelle méthode)
  - `applyPerNodeHoverEffects()` - lignes 672-781 (refactorisation)
  - `handleNodeClick()` - lignes 785-843 (extraction)
  - `applyAnimations()` - ligne 498 (ajout appel handleNodeClick)

## Impact sur la Performance

**Positif** :
- Early return si animations/hover désactivés → gain en mode Performance
- Pas de calculs inutiles si `animationEnabled: false`

**Neutre** :
- Overhead minimal pour lire `this.config.animationType` au lieu de hardcode
- Mapping des types d'animation léger (switch statement)

## Compatibilité

**Rétrocompatibilité** : ✅ Complète
- Nodes avec `d.animation.type` défini : comportement identique
- Nodes sans `d.animation` : utilisent maintenant le preset global
- API REST inchangée
- Aucune modification de base de données

**Mise à niveau** : Aucune action requise
- Vidage du cache WordPress recommandé
- Rechargement de la page suffit

## Notes Techniques

### Ordre de priorité (animations)
```javascript
const animationType = 
  animation.type ||           // 1. Node individuel
  this.config.animationType || // 2. Preset global
  'slide';                    // 3. Fallback hardcodé
```

### Ordre de priorité (hover)
```javascript
const scale = 
  hover.scale ||           // 1. Node individuel
  this.config.hoverScale || // 2. Preset global
  1.15;                    // 3. Fallback hardcodé
```

### Easing bounce automatique
```javascript
let easingName = animation.easing || this.config.animationEasing || "ease-out";
if (!animation.easing && animationType === 'bounce') {
  easingName = 'bounce'; // Force bounce si type = bounce
}
```

## Prochaines Étapes

### Court terme
- ✅ Tester tous les presets visuellement
- ✅ Valider sur différents navigateurs
- ✅ Documentation utilisateur mise à jour

### Moyen terme
- Ajouter preview des presets dans l'admin
- Créer des presets personnalisés
- Enregistrer les favoris utilisateur

### Long terme
- Interface visuelle pour créer des presets
- Export/import de configurations
- Animations personnalisées par catégorie
