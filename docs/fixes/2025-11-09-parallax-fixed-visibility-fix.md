# Fix : Mode Parallax Fixed ne s'affiche pas

**Date** : 9 novembre 2025  
**Type** : Bug Fix  
**Priorité** : Haute  
**Statut** : ✅ Résolu

## 🐛 Problème

Le mode `parallax-fixed` du bloc Image Universelle ne s'affichait pas : le conteneur était présent dans le DOM mais l'image avec `background-image` n'était pas visible.

### Symptômes

```html
<div class="image-block parallax-fixed" 
     role="img" 
     style="background-image: url(...); 
            background-attachment: scroll; 
            background-position: center calc(50% + 107.871px);"
     data-parallax-speed="0.5" 
     data-parallax-mode="fixed">
</div>
```

- ✅ Le HTML était correct
- ✅ Le JavaScript parallax fonctionnait (background-position changeait)
- ❌ **L'image n'était pas visible** (div sans hauteur)

### Analyse

Un `<div>` avec `background-image` mais **sans hauteur définie** n'est pas visible en CSS. Le problème venait de l'héritage de la hauteur qui ne fonctionnait pas correctement avec `min-height: inherit`.

## 🔧 Solution Appliquée

### Modification du fichier CSS

**Fichier** : `assets/css/image-block.css`

**Changements** :

```css
/* AVANT - Hauteurs non explicites */
.archi-image-block.display-mode-parallax-fixed.height-auto .image-block-container {
  height: 70vh;
}

.archi-image-block.display-mode-parallax-fixed .image-block {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  min-height: inherit; /* ❌ Ne fonctionnait pas */
}
```

```css
/* APRÈS - Hauteurs explicites à tous les niveaux */
.archi-image-block.display-mode-parallax-fixed.height-auto .image-block-container {
  height: 70vh;
  min-height: 70vh; /* ✅ Ajout de min-height */
}

.archi-image-block.display-mode-parallax-fixed .image-block {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  min-height: 100%; /* ✅ Changé de inherit à 100% */
}

/* ✅ Ajout de règles explicites pour chaque mode de hauteur */
.archi-image-block.display-mode-parallax-fixed.height-auto .image-wrapper,
.archi-image-block.display-mode-parallax-fixed.height-auto .image-block {
  height: 70vh;
  min-height: 70vh;
}

.archi-image-block.display-mode-parallax-fixed.height-full-viewport .image-wrapper,
.archi-image-block.display-mode-parallax-fixed.height-full-viewport .image-block {
  height: 100vh;
  min-height: 100vh;
}

.archi-image-block.display-mode-parallax-fixed.height-custom .image-wrapper {
  min-height: inherit;
}
```

### Cascade de hauteurs

La solution garantit que la hauteur est propagée à tous les niveaux :

```
.archi-image-block.display-mode-parallax-fixed (min-height: 70vh par défaut)
  └─ .image-block-container (height: 70vh + min-height: 70vh)
      └─ .image-wrapper (height: 70vh + min-height: 70vh)
          └─ .image-block.parallax-fixed (height: 70vh + min-height: 70vh)
```

## ✅ Résultat

### Mode `height-auto` (défaut)
- Conteneur : **70vh**
- Image visible avec hauteur définie

### Mode `height-full-viewport`
- Conteneur : **100vh**
- Image en plein écran

### Mode `height-custom`
- Conteneur : **Hauteur personnalisée** (ex: 600px)
- Image avec hauteur inline depuis PHP

## 🧪 Tests Effectués

- [x] Mode parallax-fixed avec height-auto → ✅ Visible (70vh)
- [x] Mode parallax-fixed avec height-full-viewport → ✅ Visible (100vh)
- [x] Mode parallax-fixed avec height-custom → ✅ Visible (hauteur custom)
- [x] Effet parallax au scroll → ✅ Fonctionne
- [x] Alignement full/wide → ✅ OK
- [x] Overlay par-dessus → ✅ Superposition correcte
- [x] Texte superposé → ✅ Positionné correctement
- [x] Responsive mobile → ✅ Hauteurs adaptées

## 📝 Détails Techniques

### Pourquoi `min-height: inherit` ne fonctionnait pas ?

En CSS, `inherit` hérite de la valeur **calculée** du parent. Or :
- Le parent avait `min-height: inherit`
- Qui héritait aussi de son parent avec `min-height: 70vh`
- Mais la cascade ne fonctionnait pas correctement avec `position: absolute`

### Solution : Hauteurs explicites

Au lieu d'utiliser l'héritage, on définit explicitement la hauteur à chaque niveau avec des sélecteurs spécifiques combinant :
- Le mode d'affichage (`.display-mode-parallax-fixed`)
- Le mode de hauteur (`.height-auto`, `.height-full-viewport`, `.height-custom`)
- L'élément (`.image-block-container`, `.image-wrapper`, `.image-block`)

## 🔄 Compatibilité

- ✅ Tous les navigateurs modernes
- ✅ Mobile responsive
- ✅ Pas de régression sur les autres modes
- ✅ JavaScript parallax toujours fonctionnel

## 📦 Fichiers Modifiés

| Fichier | Type | Description |
|---------|------|-------------|
| `assets/css/image-block.css` | CSS | Ajout hauteurs explicites pour parallax-fixed |

## 🎯 Points d'Attention

1. **Ne pas revenir à `min-height: inherit`** - Utiliser des valeurs explicites
2. **Tester tous les modes de hauteur** après modification
3. **Vérifier la cascade** : parent → container → wrapper → block
4. **Mobile** : Les hauteurs 70vh/100vh sont adaptées en responsive

## 🚀 Déploiement

1. Le CSS a été modifié directement (pas de compilation nécessaire)
2. Vider le cache du navigateur pour voir les changements
3. Tester sur différentes tailles d'écran

---

**Fix confirmé et testé** ✅
