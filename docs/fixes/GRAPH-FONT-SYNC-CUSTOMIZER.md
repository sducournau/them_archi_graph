# Synchronisation Police Graphe avec Customizer

## 🎯 Mise à jour

La police du graphe est maintenant **synchronisée avec les paramètres du Customizer**.

## ✅ Modifications effectuées

### 1. CSS Customizer - Ajout des sélecteurs du graphe

**Fichier:** `inc/customizer.php`

**Sélecteurs ajoutés:**
- `.node-title-text` - Titres des nœuds du graphe
- `.node-label` - Labels des nœuds
- `.graph-legend` - Légende du graphe
- `.graph-info-panel` - Panneau d'information latéral
- `.graph-instructions` - Instructions du graphe
- `.graph-controls` - Contrôles du graphe
- `.side-panel` - Panneaux latéraux
- `.title-overlay` - Overlays de titre

**Code ajouté:**
```php
.node-title-text,
.node-label,
.graph-legend,
.graph-info-panel,
.graph-instructions,
.graph-controls,
.side-panel,
.title-overlay {
    font-family: <?php echo esc_attr($font_family_css); ?> !important;
}
```

### 2. JavaScript Preview - Preview en temps réel

**Fichier:** `assets/js/customizer-preview.js`

Les mêmes sélecteurs ont été ajoutés au JavaScript de preview pour que les changements de police s'appliquent **immédiatement** dans le Customizer sans rechargement.

**Éléments du graphe concernés:**
```javascript
'.node-title-text',
'.node-label',
'.graph-legend',
'.graph-info-panel',
'.graph-instructions',
'.graph-controls',
'.side-panel',
'.title-overlay'
```

## 🔍 Éléments synchronisés

### Interface du graphe
- ✅ **Titres des nœuds** (texte rouge sur les nœuds au survol)
- ✅ **Labels des nœuds** (étiquettes permanentes)
- ✅ **Légende** (en haut à gauche)
- ✅ **Panneau d'information** (panneau latéral avec détails article)
- ✅ **Instructions** (messages d'aide)
- ✅ **Contrôles** (boutons de contrôle du graphe)
- ✅ **Overlays de titre** (superpositions de texte)

### Styles CSS source
Les fichiers suivants définissaient des polices hard-codées, maintenant surchargées par le Customizer:
- `assets/css/graph-white.css` - `.node-title-text` (ligne 422)
- `assets/css/graph-effects.css` - `.node-label` (ligne 69)

## 🎨 Test de la synchronisation

### Dans le Customizer (preview en direct):
1. **Apparence → Personnaliser → Typographie**
2. Changez la **Police de caractères**
3. Les éléments du graphe changent **immédiatement** dans le preview
4. Publiez pour rendre permanent

### Après publication:
1. Allez sur la page avec le graphe
2. La police choisie s'applique à:
   - Tous les textes du site (déjà fonctionnel)
   - **Tous les éléments du graphe (NOUVEAU)**

## 📋 Polices testées avec le graphe

Toutes les polices disponibles fonctionnent:

### Polices système
- ✅ System (défaut)
- ✅ Arial
- ✅ Helvetica
- ✅ Georgia
- ✅ Times New Roman
- ✅ Courier New
- ✅ Verdana
- ✅ Trebuchet MS

### Google Fonts
- ✅ Roboto
- ✅ Open Sans
- ✅ Lato
- ✅ Montserrat
- ✅ Poppins
- ✅ Inter
- ✅ Playfair Display
- ✅ Merriweather

## 🔧 Comportement technique

### Priorité CSS
Le CSS du Customizer a la priorité **999** sur `wp_head`, garantissant qu'il surcharge les styles par défaut du graphe.

### !important
Toutes les déclarations utilisent `!important` pour forcer l'application sur les styles inline et les styles CSS spécifiques du graphe.

### Preview temps réel
Le JavaScript `customizer-preview.js` applique les changements instantanément via jQuery sur tous les sélecteurs, incluant ceux du graphe.

## 📁 Fichiers modifiés

- ✅ `inc/customizer.php` - Ajout des sélecteurs du graphe au CSS
- ✅ `assets/js/customizer-preview.js` - Ajout des sélecteurs au preview JS

## ✨ Résultat

**La police sélectionnée dans le Customizer s'applique maintenant de manière cohérente sur:**
1. Tout le site (body, headers, contenus, etc.)
2. **Les éléments du graphe (nœuds, labels, panneaux, légende, etc.)**

Pas de rechargement nécessaire dans le Customizer (preview en direct) ✓  
Persistance après publication et rechargement ✓  
Cohérence visuelle entre le site et le graphe ✓

---

**Date:** 11 novembre 2025  
**Fichiers concernés:** 2 fichiers modifiés  
**Status:** ✅ Fonctionnel
