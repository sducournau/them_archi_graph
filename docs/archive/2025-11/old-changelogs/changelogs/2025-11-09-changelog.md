# Changelog - 9 novembre 2025

## 🎨 Améliorations de l'Éditeur

### Bloc Image Universelle - Preview Améliorée

**Nouvelles fonctionnalités** :
- ✅ Système de badges multi-informations (mode, alignement, hauteur, overlay, vitesse)
- ✅ Indicateurs visuels par mode avec icônes et couleurs
- ✅ Preview interactive avec effet zoom au hover
- ✅ Messages d'aide contextuels pour chaque mode
- ✅ Animations CSS élégantes (fadeIn, pulse, slide)
- ✅ Dégradés de fond thématiques selon le mode
- ✅ Preview améliorée du mode comparaison avec slider simulé
- ✅ Infos techniques affichées sur la preview

**Fichiers modifiés** :
- `assets/js/blocks/image-block.jsx` - Logique des badges et previews
- `assets/css/blocks-editor.css` - Styles et animations pour l'éditeur

**Documentation** :
- `docs/IMAGE-BLOCK-EDITOR-ENHANCEMENTS.md` - Documentation technique
- `docs/changelogs/2025-11-09-image-block-editor-improvements.md` - Changelog détaillé

---

## 🐛 Corrections de Bugs

### Bloc Image Universelle - Mode Parallax Fixed

**Problème résolu** :
- ❌ Le mode `parallax-fixed` ne s'affichait pas (div sans hauteur visible)

**Solution** :
- ✅ Ajout de hauteurs explicites à tous les niveaux de la cascade CSS
- ✅ Remplacement de `min-height: inherit` par des valeurs explicites (70vh, 100vh, etc.)
- ✅ Règles CSS spécifiques pour chaque mode de hauteur (auto, full-viewport, custom)

**Modes de hauteur supportés** :
- `height-auto` : 70vh par défaut
- `height-full-viewport` : 100vh (plein écran)
- `height-custom` : Hauteur personnalisée

**Fichiers modifiés** :
- `assets/css/image-block.css` - Correction des hauteurs pour parallax-fixed

**Documentation** :
- `docs/fixes/2025-11-09-parallax-fixed-visibility-fix.md` - Documentation du fix

---

## 📊 Résumé des Changements

| Catégorie | Fichiers modifiés | Lignes ajoutées | Impact |
|-----------|-------------------|-----------------|--------|
| **Améliorations UI** | 2 | ~350 | Éditeur Gutenberg |
| **Corrections CSS** | 1 | ~25 | Frontend (Parallax) |
| **Documentation** | 4 | ~600 | Documentation |
| **Total** | **7** | **~975** | - |

---

## 🧪 Tests Effectués

### Éditeur
- [x] Badges informatifs affichés correctement
- [x] Indicateurs visuels par mode
- [x] Effet zoom interactif
- [x] Messages d'aide contextuels
- [x] Animations CSS fluides
- [x] Preview mode comparaison

### Frontend (Parallax Fixed)
- [x] Mode height-auto (70vh) → Visible
- [x] Mode height-full-viewport (100vh) → Visible
- [x] Mode height-custom → Visible avec hauteur custom
- [x] Effet parallax au scroll → Fonctionne
- [x] Overlay et texte → Superposition correcte
- [x] Responsive mobile → OK

---

## 🚀 Déploiement

### Étapes
1. ✅ Modifications CSS appliquées (cache navigateur à vider)
2. ✅ Assets JavaScript compilés (webpack build réussi)
3. ✅ Documentation créée
4. ⏳ Tests utilisateurs à effectuer

### Compatibilité
- ✅ WordPress 6.0+
- ✅ Gutenberg Editor
- ✅ Navigateurs modernes (Chrome, Firefox, Safari, Edge)
- ✅ Mobile responsive

---

**Version du thème** : 1.1.0  
**Date de build** : 9 novembre 2025
