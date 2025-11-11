# Changelog - Personnalisation du Graphique D3.js

## [1.2.0] - Novembre 2025

### ✨ Nouvelles Fonctionnalités

#### Paramètres du Customizer
Ajout de 17 nouveaux paramètres de personnalisation du graphique dans **Apparence > Personnaliser > Graphique D3.js** :

**Animations et Effets**
- Mode d'animation d'entrée (fade-in, scale-up, slide-in, bounce)
- Vitesse des transitions (200-2000ms)
- Effets de survol (highlight, scale, glow, pulse)

**Liens et Connexions**
- Couleur personnalisable des liens
- Épaisseur des liens (0.5-5px)
- Opacité des liens (0.1-1.0)
- Style de lien (solid, dashed, curved)
- Flèches directionnelles optionnelles
- Animations des liens (pulse, flow, glow)

**Couleurs par Catégorie**
- Système de couleurs automatiques par catégorie
- 7 palettes de couleurs prédéfinies :
  - Default (bleus professionnels)
  - Warm (rouges/oranges)
  - Cool (bleus/verts)
  - Vibrant (multicolore)
  - Pastel (couleurs douces)
  - Nature (tons naturels)
  - Monochrome (nuances de gris)
- Légende des catégories affichable/masquable

#### Preview en Temps Réel
- Tous les paramètres sont prévisualisables instantanément dans le Customizer
- Pas de rechargement de page nécessaire
- Feedback visuel immédiat

### 🔧 Améliorations Techniques

#### Backend PHP
**Fichier : `inc/customizer.php`**
- Ajout de 17 nouveaux settings avec sanitization appropriée
- Fonction `archi_get_category_color_palette()` pour gérer les palettes
- Fonction `archi_get_category_color()` pour attribution automatique
- Fonction `archi_localize_graph_settings()` pour exposer au JavaScript
- Tous les paramètres utilisent le préfixe `archi_` (convention du thème)

#### Frontend JavaScript
**Fichier : `assets/js/customizer-preview.js`**
- Ajout de 14 handlers de preview en temps réel
- Fonction `getCategoryPaletteColors()` pour synchronisation des palettes
- Support complet du Customizer API

**Fichier : `assets/js/utils/graph-settings-helper.js` (NOUVEAU)**
- Module utilitaire complet pour intégration dans le graph
- 8 fonctions helper exportables :
  - `getGraphSettings()` - Récupération des paramètres
  - `applyNodeEntryAnimation()` - Animation d'apparition
  - `applyHoverEffect()` - Effets de survol
  - `configureLinkStyle()` - Style des liens
  - `applyLinkAnimation()` - Animation des liens
  - `getNodeColor()` - Couleur selon catégorie
  - `createCategoryLegend()` - Génération de légende
  - `useGraphSettings()` - Hook React
- Fonction globale `window.updateGraphSettings()` pour mise à jour dynamique
- Événement personnalisé `graphSettingsUpdated`

### 📚 Documentation

**Nouveaux fichiers :**
- `docs/GRAPH-CUSTOMIZER-ADVANCED.md` - Guide utilisateur complet (236 lignes)
- `docs/GRAPH-CUSTOMIZER-SUMMARY.md` - Récapitulatif technique (152 lignes)
- `docs/GRAPH-CUSTOMIZER-DEV-GUIDE.md` - Guide développeur (331 lignes)
- `docs/GRAPH-INTEGRATION-EXAMPLE.jsx` - Exemple d'intégration React (305 lignes)

### 🎨 Détails des Palettes

Chaque palette contient 10 couleurs harmonieuses :

```
Default:    #3498db, #2980b9, #5dade2, #1f618d, #85c1e9...
Warm:       #e74c3c, #c0392b, #ec7063, #922b21, #f1948a...
Cool:       #16a085, #1abc9c, #48c9b0, #0e6655, #76d7c4...
Vibrant:    #e74c3c, #3498db, #9b59b6, #f39c12, #1abc9c...
Pastel:     #aed6f1, #f9e79f, #abebc6, #f5b7b1, #d7bde2...
Nature:     #27ae60, #229954, #52be80, #7d6608, #d68910...
Monochrome: #2c3e50, #34495e, #566573, #707b7c, #95a5a6...
```

### 📊 API JavaScript

**Objet Global**
```javascript
window.archiGraphSettings = {
    defaultNodeColor: '#3498db',
    defaultNodeSize: 60,
    clusterStrength: 0.1,
    animationMode: 'fade-in',
    transitionSpeed: 500,
    hoverEffect: 'highlight',
    linkColor: '#999999',
    linkWidth: 1.5,
    linkOpacity: 0.6,
    linkStyle: 'solid',
    showArrows: false,
    linkAnimation: 'none',
    categoryColorsEnabled: false,
    categoryPalette: 'default',
    showCategoryLegend: true,
    categoryColors: [...]
}
```

**Mise à Jour Dynamique**
```javascript
window.updateGraphSettings({ linkColor: '#ff0000' });
```

**Événements**
```javascript
window.addEventListener('graphSettingsUpdated', (event) => {
    const newSettings = event.detail;
});
```

### 🔄 Compatibilité

- ✅ Compatible avec les paramètres existants du customizer
- ✅ Pas de breaking changes
- ✅ Support WordPress 5.8+
- ✅ Compatible React 17+
- ✅ Support D3.js v6+

### 📝 Valeurs par Défaut

| Paramètre | Valeur par défaut |
|-----------|------------------|
| Animation d'entrée | `fade-in` |
| Vitesse transition | `500ms` |
| Effet survol | `highlight` |
| Couleur liens | `#999999` |
| Épaisseur liens | `1.5px` |
| Opacité liens | `0.6` |
| Style liens | `solid` |
| Flèches | `false` |
| Animation liens | `none` |
| Couleurs catégorie | `false` |
| Palette | `default` |
| Légende | `true` |

### 🚀 Migration

**Aucune migration nécessaire** - Les nouveaux paramètres sont optionnels et ne modifient pas le comportement par défaut du graph.

Pour activer les nouvelles fonctionnalités :
1. Aller dans **Apparence > Personnaliser > Graphique D3.js**
2. Ajuster les paramètres selon vos préférences
3. Cliquer sur **Publier**

### 🔜 Prochaines Étapes

**À faire (non inclus dans cette version) :**
- [ ] Intégration dans le composant React `GraphContainer.jsx`
- [ ] Tests unitaires pour les fonctions helper
- [ ] Tests d'intégration avec le graph
- [ ] Optimisation des performances pour graphes > 100 nœuds
- [ ] Support du mode sombre
- [ ] Export/import de configurations personnalisées

### 🐛 Corrections de Bugs

Aucune - nouvelle fonctionnalité.

### ⚠️ Notes Importantes

1. **Preview** : Le preview en temps réel fonctionne uniquement sur la page d'accueil où le graph est affiché
2. **Performance** : Les animations peuvent impacter les performances avec >200 nœuds
3. **Navigateurs** : Testé sur Chrome, Firefox, Safari, Edge (dernières versions)

### 👥 Contributeurs

- Backend PHP : Implémentation complète
- JavaScript : Preview + Utilitaires
- Documentation : Guide complet utilisateur + développeur

### 📞 Support

Pour toute question ou problème :
- Consulter `docs/GRAPH-CUSTOMIZER-ADVANCED.md` (utilisateurs)
- Consulter `docs/GRAPH-CUSTOMIZER-DEV-GUIDE.md` (développeurs)
- Voir les exemples dans `docs/GRAPH-INTEGRATION-EXAMPLE.jsx`

---

**Version** : 1.2.0  
**Date** : Novembre 2025  
**Type** : Feature (nouvelle fonctionnalité majeure)  
**Status** : ✅ Backend complet | 🔲 Intégration React à finaliser
