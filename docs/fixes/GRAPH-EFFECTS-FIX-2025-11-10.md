# Fix: Paramètres d'effet des nodes dans le graphe

**Date:** 10 Novembre 2025  
**Type:** Bug Fix  
**Fichier modifié:** `assets/js/utils/GraphManager.js`  
**Statut:** ✅ Résolu

---

## 🐛 Problème Identifié

Les paramètres d'animation et d'effet de survol (hover) configurés dans l'éditeur WordPress n'étaient pas appliqués aux nodes du graphe, car il y avait une **incompatibilité de structure de données** entre l'API REST et le GraphManager.

### Symptômes

- Les animations personnalisées (type, durée, délai, direction) n'étaient pas prises en compte
- Les effets de survol (scale, pulse, glow) ne fonctionnaient pas
- Tous les nodes utilisaient les paramètres par défaut globaux

---

## 🔍 Analyse Technique

### Structure de données attendue vs reçue

**Ce que l'API REST envoyait (structure plate):**

```javascript
{
  id: 123,
  title: "Article",
  node_color: "#3498db",
  node_size: 60,
  // Paramètres PLATS au niveau racine
  animation_type: "fadeIn",
  animation_duration: 800,
  animation_delay: 100,
  animation_easing: "ease-out",
  enter_from: "center",
  hover_scale: 1.15,
  pulse_effect: true,
  glow_effect: false
}
```

**Ce que le GraphManager attendait (structure imbriquée):**

```javascript
{
  id: 123,
  title: "Article",
  node_color: "#3498db",
  node_size: 60,
  // Structures IMBRIQUÉES
  animation: {
    type: "fadeIn",
    duration: 800,
    delay: 100,
    easing: "ease-out",
    enterFrom: "center"
  },
  hover: {
    scale: 1.15,
    pulse: true,
    glow: false
  }
}
```

### Code problématique

**GraphManager.js - méthode `applyPerNodeAnimations()` ligne 407:**
```javascript
const animation = d.animation || {}; // ❌ d.animation était undefined
const duration = animation.duration || this.settings.animationDuration;
```

**GraphManager.js - méthode `applyPerNodeHoverEffects()` ligne 483:**
```javascript
const hover = d.hover || {}; // ❌ d.hover était undefined
const scale = hover.scale || 1.15;
```

---

## ✅ Solution Implémentée

### Transformation des données dans `loadData()`

Une transformation intermédiaire a été ajoutée dans la méthode `loadData()` pour restructurer les données plates de l'API en structures imbriquées attendues par le GraphManager.

**Code ajouté (lignes 94-111):**

```javascript
// ✅ Transform flat structure to nested structure for effects
this.nodes = this.nodes.map(node => {
  // Create animation object from flat parameters
  const animation = {
    type: node.animation_type || "fadeIn",
    duration: node.animation_duration || this.settings.animationDuration,
    delay: node.animation_delay || 0,
    easing: node.animation_easing || "ease-out",
    enterFrom: node.enter_from || "center"
  };

  // Create hover object from flat parameters
  const hover = {
    scale: node.hover_scale || 1.15,
    pulse: node.pulse_effect || false,
    glow: node.glow_effect || false
  };

  // Return node with nested structures
  return {
    ...node,
    animation,
    hover
  };
});
```

---

## 📊 Paramètres Supportés

### Paramètres d'animation

| Paramètre API | Propriété imbriquée | Type | Défaut | Description |
|---------------|---------------------|------|--------|-------------|
| `animation_type` | `animation.type` | string | "fadeIn" | Type d'animation d'entrée |
| `animation_duration` | `animation.duration` | int | 800 | Durée en ms |
| `animation_delay` | `animation.delay` | int | 0 | Délai avant animation en ms |
| `animation_easing` | `animation.easing` | string | "ease-out" | Fonction d'easing |
| `enter_from` | `animation.enterFrom` | string | "center" | Direction d'entrée (top/bottom/left/right/center) |

### Paramètres de survol

| Paramètre API | Propriété imbriquée | Type | Défaut | Description |
|---------------|---------------------|------|--------|-------------|
| `hover_scale` | `hover.scale` | float | 1.15 | Facteur d'agrandissement au survol |
| `pulse_effect` | `hover.pulse` | bool | false | Effet de pulsation continue |
| `glow_effect` | `hover.glow` | bool | false | Effet de lueur (glow) |

---

## 🎯 Avantages de cette approche

1. **Compatibilité arrière maintenue** - L'API REST n'a pas besoin d'être modifiée
2. **Séparation des préoccupations** - La transformation est faite une seule fois au chargement
3. **Code propre** - Les méthodes d'animation gardent leur logique métier claire
4. **Performance** - Transformation en O(n) au chargement uniquement
5. **Maintenabilité** - Point unique de transformation facile à déboguer

---

## 🧪 Tests Recommandés

1. **Test animation:** Configurer différents types d'animation sur plusieurs nodes
2. **Test hover:** Activer pulse/glow et vérifier les effets au survol
3. **Test délai:** Configurer des délais différents pour créer un effet de cascade
4. **Test direction:** Tester toutes les directions d'entrée (top, bottom, left, right, center)
5. **Test scale:** Vérifier que le facteur de scale personnalisé fonctionne

---

## 📝 Prochaines Améliorations Possibles

1. **Validation des valeurs** - Ajouter des contrôles de validité des paramètres
2. **Presets d'animation** - Créer des presets prédéfinis pour faciliter la configuration
3. **Animation de sortie** - Ajouter des animations de sortie/disparition
4. **Transitions entre états** - Animer les changements de propriétés des nodes
5. **Debug mode** - Ajouter un mode debug pour visualiser les paramètres appliqués

---

## 🔗 Fichiers Concernés

- `assets/js/utils/GraphManager.js` - Transformation et application des effets
- `inc/graph-meta-registry.php` - Définition des métadonnées
- `inc/rest-api.php` - API REST qui envoie les données plates
- `inc/meta-boxes.php` - Interface d'édition des paramètres

---

## ✨ Résultat

Les paramètres d'animation et d'effet de survol configurés dans l'éditeur WordPress sont maintenant **correctement appliqués** aux nodes du graphe. Chaque node peut avoir son propre comportement visuel personnalisé.
