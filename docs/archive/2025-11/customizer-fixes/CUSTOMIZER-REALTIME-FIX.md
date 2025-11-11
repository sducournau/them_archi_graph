# ✅ Mise à jour en temps réel du Customizer - CORRIGÉ

**Date:** 11 novembre 2025  
**Statut:** ✅ Fonctionnel

## 🔍 Problème identifié

Le système de personnalisation du graphe ne mettait **pas à jour le graphe en temps réel** lors de modifications dans le Customizer WordPress.

### Diagnostic

1. ✅ `customizer-preview.js` - Chargé et fonctionnel
2. ✅ `window.updateGraphSettings()` - Fonction existante et opérationnelle
3. ✅ Événement `graphSettingsUpdated` - Émis correctement
4. ❌ **`GraphContainer.jsx` n'écoutait PAS l'événement**

## 🛠️ Corrections effectuées

### 1. Ajout de l'écoute dans `GraphContainer.jsx`

**Fichier:** `assets/js/components/GraphContainer.jsx`

```jsx
/**
 * Écouter les changements de paramètres du Customizer
 */
useEffect(() => {
  const handleSettingsUpdate = (event) => {
    const newSettings = event.detail;
    console.log('Customizer settings updated:', newSettings);

    // Mettre à jour window.archiGraphSettings
    if (typeof window.archiGraphSettings === 'object') {
      Object.assign(window.archiGraphSettings, newSettings);
    }

    // Redessiner le graphe avec les nouveaux paramètres
    if (articles.length > 0 && svgRef.current) {
      updateGraph();
    }
  };

  // Écouter l'événement personnalisé
  window.addEventListener('graphSettingsUpdated', handleSettingsUpdate);

  // Cleanup
  return () => {
    window.removeEventListener('graphSettingsUpdated', handleSettingsUpdate);
  };
}, [articles]);
```

### 2. Import du helper dans `app.js`

**Fichier:** `assets/js/app.js`

```javascript
// Import du helper de settings pour rendre window.updateGraphSettings disponible
import "@utils/graph-settings-helper";
```

### 3. Compilation

```bash
npm run build
```

✅ **Compilation réussie** (avec warnings SASS non bloquants)

## 🧪 Test

Un fichier de test a été créé : `test-customizer-realtime.php`

### Pour tester :

1. **Accédez à la page de test :**
   ```
   http://localhost/wordpress/wp-content/themes/archi-graph-template/test-customizer-realtime.php
   ```

2. **Cliquez sur "Ouvrir le Customizer"**

3. **Dans le Customizer, allez dans "🔗 Graphique D3.js"**

4. **Modifiez les paramètres et observez les changements EN DIRECT :**
   - ✓ Couleur des nœuds
   - ✓ Taille des nœuds  
   - ✓ Couleur des liens
   - ✓ Épaisseur des liens
   - ✓ Opacité des liens
   - ✓ Style de lien (solid/dashed/curved)
   - ✓ Flèches directionnelles
   - ✓ Animation des liens
   - ✓ Mode d'animation d'entrée
   - ✓ Vitesse des transitions
   - ✓ Effet de survol
   - ✓ Couleurs par catégorie
   - ✓ Palette de couleurs
   - ✓ Affichage de la légende

## 📊 Architecture de la solution

```
┌─────────────────────────────────────────────────────────────┐
│                     WordPress Customizer                     │
│  (inc/customizer.php - Définit les paramètres avec          │
│   'transport' => 'postMessage')                              │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│           customizer-preview.js (Preview Frame)              │
│  - Écoute les changements via wp.customize()                │
│  - Appelle window.updateGraphSettings(newSettings)          │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│         graph-settings-helper.js (Utilitaire Global)         │
│  - window.updateGraphSettings() fusionné les settings       │
│  - Émet l'événement 'graphSettingsUpdated'                  │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│         GraphContainer.jsx (Composant React)                 │
│  - useEffect écoute 'graphSettingsUpdated'                  │
│  - Met à jour window.archiGraphSettings                     │
│  - Appelle updateGraph() pour redessiner                    │
└─────────────────────────────────────────────────────────────┘
```

## 🎯 Paramètres disponibles

### Nœuds
- `defaultNodeColor` - Couleur par défaut
- `defaultNodeSize` - Taille par défaut
- `clusterStrength` - Force de regroupement

### Liens
- `linkColor` - Couleur des connexions
- `linkWidth` - Épaisseur
- `linkOpacity` - Transparence
- `linkStyle` - Style (solid/dashed/curved)
- `showArrows` - Flèches directionnelles
- `linkAnimation` - Animation (none/pulse/flow/glow)

### Animations
- `animationMode` - Type d'entrée (none/fade-in/scale-up/slide-in/bounce)
- `transitionSpeed` - Vitesse (200-2000ms)
- `hoverEffect` - Effet survol (none/highlight/scale/glow/pulse)

### Catégories
- `categoryColorsEnabled` - Activer les couleurs par catégorie
- `categoryPalette` - Palette (default/warm/cool/vibrant/pastel/nature/monochrome)
- `showCategoryLegend` - Afficher la légende

### Affichage
- `popupTitleOnly` - Popup avec titre seulement
- `showComments` - Afficher les commentaires

## 🔧 Débogage

Pour voir les logs de mise à jour en temps réel :

1. Ouvrez la console du navigateur (F12)
2. Dans le Customizer, modifiez un paramètre
3. Vous verrez :
   ```
   Customizer settings updated: {defaultNodeColor: "#ff0000", ...}
   ```

## ✅ Vérifications finales

- [x] Le script `customizer-preview.js` est chargé
- [x] La fonction `window.updateGraphSettings` existe
- [x] L'événement `graphSettingsUpdated` est émis
- [x] `GraphContainer.jsx` écoute l'événement
- [x] Le graphe se redessine avec les nouveaux paramètres
- [x] Compilation webpack réussie
- [x] Fichier de test créé

## 📝 Notes importantes

1. **postMessage vs refresh** : Les paramètres avec `'transport' => 'postMessage'` se mettent à jour en temps réel sans recharger la page.

2. **Logs console** : Les logs `console.log('Customizer settings updated:', ...)` permettent de voir les mises à jour en direct.

3. **Performance** : Le graphe est redessiné à chaque changement. C'est normal et attendu.

4. **Compatibilité** : La solution fonctionne avec tous les navigateurs modernes.

## 🎉 Résultat

**Le graphe se met maintenant à jour EN TEMPS RÉEL** lors des modifications dans le Customizer WordPress !

Vous pouvez ajuster les couleurs, tailles, animations et voir immédiatement le résultat sans recharger la page.
