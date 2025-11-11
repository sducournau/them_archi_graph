# 🎨 Personnalisation du Graphique D3.js - Guide Développeur

## ✅ Implémentation Complète

### Ce qui a été fait

#### 1. Backend PHP (inc/customizer.php)
✅ **17 nouveaux paramètres** ajoutés dans la section "Graphique D3.js" :

**Animations & Effets (5)**
- `archi_graph_animation_mode` - Mode d'animation d'entrée
- `archi_graph_transition_speed` - Vitesse des transitions (ms)
- `archi_graph_hover_effect` - Effet de survol

**Liens & Connexions (6)**
- `archi_graph_link_color` - Couleur des liens
- `archi_graph_link_width` - Épaisseur des liens
- `archi_graph_link_opacity` - Opacité des liens
- `archi_graph_link_style` - Style (solid/dashed/curved)
- `archi_graph_show_arrows` - Flèches directionnelles
- `archi_graph_link_animation` - Animation des liens

**Couleurs par Catégorie (3)**
- `archi_graph_category_colors_enabled` - Activer/désactiver
- `archi_graph_category_palette` - Choix de la palette (7 disponibles)
- `archi_graph_show_category_legend` - Afficher la légende

**Fonctions Helper PHP**
```php
archi_get_category_color_palette($palette_name)  // Retourne tableau de 10 couleurs
archi_get_category_color($category_id, $palette) // Couleur pour une catégorie
archi_localize_graph_settings()                  // Expose au JavaScript
```

#### 2. Preview en Temps Réel (assets/js/customizer-preview.js)
✅ **14 handlers de preview** pour chaque paramètre
✅ Mise à jour instantanée sans rechargement
✅ Fonction `getCategoryPaletteColors()` pour les palettes

#### 3. Utilitaires JavaScript (assets/js/utils/graph-settings-helper.js)
✅ Module complet avec fonctions helper :
- `getGraphSettings()` - Récupérer tous les paramètres
- `applyNodeEntryAnimation()` - Appliquer animations d'entrée
- `applyHoverEffect()` - Gérer les effets de survol
- `configureLinkStyle()` - Configurer l'apparence des liens
- `applyLinkAnimation()` - Animer les liens
- `getNodeColor()` - Obtenir couleur selon catégorie
- `createCategoryLegend()` - Créer la légende

#### 4. Documentation
✅ `docs/GRAPH-CUSTOMIZER-ADVANCED.md` - Guide utilisateur complet
✅ `docs/GRAPH-CUSTOMIZER-SUMMARY.md` - Récapitulatif technique
✅ `docs/GRAPH-INTEGRATION-EXAMPLE.jsx` - Exemple d'intégration React

---

## 🚀 Intégration dans GraphContainer.jsx

### Étape 1 : Importer les utilitaires

```javascript
import {
    getGraphSettings,
    applyNodeEntryAnimation,
    applyHoverEffect,
    configureLinkStyle,
    applyLinkAnimation,
    getNodeColor,
    createCategoryLegend
} from '../utils/graph-settings-helper';
```

### Étape 2 : Récupérer les paramètres

```javascript
const GraphContainer = () => {
    const [graphSettings, setGraphSettings] = useState(getGraphSettings());
    
    // Écouter les mises à jour du Customizer
    useEffect(() => {
        const handleUpdate = (event) => {
            setGraphSettings(event.detail);
            updateGraph(event.detail);
        };
        
        window.addEventListener('graphSettingsUpdated', handleUpdate);
        return () => window.removeEventListener('graphSettingsUpdated', handleUpdate);
    }, []);
    
    // ...
};
```

### Étape 3 : Appliquer au rendu du graph

```javascript
const renderGraph = (svg, data, settings) => {
    // 1. Créer les liens
    const link = svg.selectAll('.links line').data(links);
    configureLinkStyle(link, settings);
    
    if (settings.linkAnimation !== 'none') {
        applyLinkAnimation(link, settings);
    }
    
    // 2. Créer les nœuds
    const node = svg.selectAll('.nodes g').data(nodes);
    
    node.select('circle')
        .attr('r', d => d.size)
        .style('fill', d => getNodeColor(d, settings));
    
    // 3. Appliquer animations
    if (settings.animationMode !== 'none') {
        applyNodeEntryAnimation(node, settings);
    }
    
    if (settings.hoverEffect !== 'none') {
        applyHoverEffect(node, settings);
    }
    
    // 4. Ajouter la légende si nécessaire
    if (settings.categoryColorsEnabled && settings.showCategoryLegend) {
        const legend = createCategoryLegend(categories, settings);
        if (legend) {
            document.querySelector('#graph-container').appendChild(legend);
        }
    }
};
```

### Étape 4 : Gérer les mises à jour dynamiques

```javascript
const updateGraph = (newSettings) => {
    const svg = d3.select('#graph-container svg');
    
    // Mettre à jour les liens
    const links = svg.selectAll('.links line');
    configureLinkStyle(links, newSettings);
    
    // Mettre à jour les couleurs des nœuds
    if (newSettings.categoryColorsEnabled) {
        const nodes = svg.selectAll('.nodes g circle');
        nodes.transition()
            .duration(newSettings.transitionSpeed)
            .style('fill', d => getNodeColor(d, newSettings));
    }
    
    // Mettre à jour la légende
    updateLegend(newSettings);
};
```

---

## 🎨 Palettes de Couleurs Disponibles

### Code des Palettes
```javascript
const palettes = {
    'default': ['#3498db', '#2980b9', '#5dade2', ...],  // Bleus professionnels
    'warm': ['#e74c3c', '#c0392b', '#ec7063', ...],     // Rouges/oranges
    'cool': ['#16a085', '#1abc9c', '#48c9b0', ...],     // Bleus/verts
    'vibrant': ['#e74c3c', '#3498db', '#9b59b6', ...],  // Multicolore
    'pastel': ['#aed6f1', '#f9e79f', '#abebc6', ...],   // Couleurs douces
    'nature': ['#27ae60', '#229954', '#52be80', ...],   // Tons naturels
    'monochrome': ['#2c3e50', '#34495e', '#566573', ...] // Nuances de gris
};
```

### Attribution Automatique
La couleur est assignée selon : `categoryId % paletteColors.length`

---

## 🔧 API JavaScript

### Objet Global `archiGraphSettings`

```javascript
window.archiGraphSettings = {
    // Nœuds
    defaultNodeColor: '#3498db',
    defaultNodeSize: 60,
    clusterStrength: 0.1,
    
    // Affichage
    popupTitleOnly: false,
    showComments: true,
    
    // Animations
    animationMode: 'fade-in',      // none | fade-in | scale-up | slide-in | bounce
    transitionSpeed: 500,           // 200-2000ms
    hoverEffect: 'highlight',       // none | highlight | scale | glow | pulse
    
    // Liens
    linkColor: '#999999',
    linkWidth: 1.5,                 // 0.5-5px
    linkOpacity: 0.6,               // 0.1-1
    linkStyle: 'solid',             // solid | dashed | curved
    showArrows: false,
    linkAnimation: 'none',          // none | pulse | flow | glow
    
    // Catégories
    categoryColorsEnabled: false,
    categoryPalette: 'default',
    showCategoryLegend: true,
    categoryColors: [...]           // Tableau des 10 couleurs
};
```

### Fonction de Mise à Jour
```javascript
// Appeler depuis le Customizer preview
window.updateGraphSettings({
    linkColor: '#ff0000',
    hoverEffect: 'scale',
    categoryPalette: 'vibrant'
});

// Écouter les changements
window.addEventListener('graphSettingsUpdated', (event) => {
    const newSettings = event.detail;
    // Mettre à jour le graph
});
```

---

## 📝 Checklist d'Intégration

### Backend (✅ Fait)
- [x] Paramètres ajoutés dans `inc/customizer.php`
- [x] Fonctions helper créées
- [x] Sanitization correcte
- [x] Valeurs par défaut définies
- [x] Localisation des settings via `wp_localize_script`

### Frontend JavaScript (✅ Fait)
- [x] Preview en temps réel dans `customizer-preview.js`
- [x] Utilitaires dans `graph-settings-helper.js`
- [x] Fonction globale `updateGraphSettings()`
- [x] Événement `graphSettingsUpdated`

### Composant React (🔲 À faire)
- [ ] Importer les utilitaires
- [ ] Récupérer `archiGraphSettings` au montage
- [ ] Écouter l'événement `graphSettingsUpdated`
- [ ] Appliquer les animations d'entrée
- [ ] Appliquer les effets de survol
- [ ] Configurer les styles de liens
- [ ] Implémenter les couleurs par catégorie
- [ ] Créer et afficher la légende
- [ ] Gérer les mises à jour dynamiques

### Tests (🔲 À faire)
- [ ] Tester chaque paramètre individuellement
- [ ] Vérifier le preview en temps réel
- [ ] Tester toutes les combinaisons de palettes
- [ ] Valider les animations sur différents navigateurs
- [ ] Tester les performances avec beaucoup de nœuds

---

## 🎯 Exemple Minimal

```javascript
// Dans GraphContainer.jsx - Version minimale

import React, { useEffect, useState } from 'react';
import { getGraphSettings } from '../utils/graph-settings-helper';

const GraphContainer = () => {
    const [settings, setSettings] = useState(getGraphSettings());
    
    useEffect(() => {
        // Écouter les changements
        const handler = (e) => setSettings(e.detail);
        window.addEventListener('graphSettingsUpdated', handler);
        return () => window.removeEventListener('graphSettingsUpdated', handler);
    }, []);
    
    useEffect(() => {
        // Render/Update graph avec settings
        renderGraph(settings);
    }, [settings]);
    
    return <div id="graph-container" />;
};
```

---

## 🐛 Troubleshooting

### Preview ne fonctionne pas
1. Vérifier que `customizer-preview.js` est enqueued
2. Ouvrir la console pour voir les erreurs
3. S'assurer d'être sur la page d'accueil (où le graph s'affiche)

### Paramètres non disponibles
1. Vérifier que `archi_localize_graph_settings()` est appelé
2. Vérifier le hook `wp_enqueue_scripts` avec priorité 20
3. Inspecter `window.archiGraphSettings` dans la console

### Couleurs par catégorie ne s'appliquent pas
1. Activer l'option dans le Customizer
2. Vérifier que les articles ont des catégories
3. S'assurer que `getNodeColor()` est appelé pour chaque nœud

---

## 📚 Références

### Fichiers Modifiés
- `inc/customizer.php` - Backend + Settings
- `assets/js/customizer-preview.js` - Preview en temps réel
- `assets/js/utils/graph-settings-helper.js` - Utilitaires

### Documentation
- `docs/GRAPH-CUSTOMIZER-ADVANCED.md` - Guide utilisateur
- `docs/GRAPH-CUSTOMIZER-SUMMARY.md` - Récapitulatif
- `docs/GRAPH-INTEGRATION-EXAMPLE.jsx` - Exemple complet

### WordPress Hooks
- `customize_register` - Enregistrement des settings
- `wp_enqueue_scripts` (priorité 20) - Localisation des paramètres
- `customize_preview_init` - Preview JavaScript

---

**Version** : 1.0  
**Date** : Novembre 2025  
**Status** : ✅ Backend complet | 🔲 Intégration React à finaliser
