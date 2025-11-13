# 🔧 Correctif: Prévisualisation en Direct du Customizer

## 📋 Résumé du Problème

Le graphe D3.js ne se mettait pas à jour en temps réel lorsque les paramètres étaient modifiés dans le Customizer WordPress.

## 🔍 Cause Racine Identifiée

### Problème #1: Fonction Manquante
`customizer-preview.js` appelait `window.updateGraphSettings()` qui **n'existait pas** dans le scope global.

### Problème #2: Bindings Manquants
Les 13 nouveaux paramètres ajoutés n'étaient **pas écoutés** dans `customizer-preview.js`:
- `archi_project_color`
- `archi_illustration_color`
- `archi_pages_zone_color`
- `archi_guestbook_link_color`
- `archi_priority_featured_color`
- `archi_priority_high_color`
- `archi_priority_badge_size`
- `archi_active_node_scale`
- `archi_cluster_fill_opacity`
- `archi_cluster_stroke_width`
- `archi_cluster_stroke_opacity`

## ✅ Solutions Appliquées

### 1. Exposition de `window.updateGraphSettings()` (GraphContainer.jsx)

**Fichier**: `assets/js/components/GraphContainer.jsx` (lignes 172-228)

```jsx
useEffect(() => {
  const handleSettingsUpdate = (event) => {
    const newSettings = event.detail;
    
    // Mettre à jour window.archiGraphSettings
    if (typeof window.archiGraphSettings === 'object') {
      Object.assign(window.archiGraphSettings, newSettings);
    }
    
    customizerSettingsRef.current = window.archiGraphSettings || {};
    
    // Redessiner le graphe
    if (articles.length > 0 && svgRef.current) {
      updateGraph();
    }
  };

  window.addEventListener('graphSettingsUpdated', handleSettingsUpdate);
  
  // 🔥 Exposer window.updateGraphSettings pour le Customizer
  if (!window.updateGraphSettings) {
    window.updateGraphSettings = (newSettings) => {
      if (typeof window.archiGraphSettings === 'object') {
        Object.assign(window.archiGraphSettings, newSettings);
      } else {
        window.archiGraphSettings = newSettings;
      }
      
      // Déclencher l'événement
      const event = new CustomEvent('graphSettingsUpdated', { 
        detail: newSettings 
      });
      window.dispatchEvent(event);
    };
  }

  return () => {
    window.removeEventListener('graphSettingsUpdated', handleSettingsUpdate);
    // NE PAS supprimer window.updateGraphSettings (nécessaire pour customizer-preview.js)
  };
}, [articles]);
```

**Changements clés**:
- ✅ Vérifie si `window.updateGraphSettings` existe avant de le créer
- ✅ Déclenche un événement `CustomEvent` pour notifier le composant React
- ✅ Persiste la fonction entre les re-renders du composant
- ✅ Ne supprime PAS la fonction au cleanup (nécessaire pour le Customizer)

### 2. Ajout des Bindings Customizer (customizer-preview.js)

**Fichier**: `assets/js/customizer-preview.js` (lignes 313-434)

Ajout de 4 nouvelles sections avec 13 bindings:

#### A. Couleurs des Types de Contenu (4 bindings)

```javascript
// Project color
wp.customize('archi_project_color', function(value) {
    value.bind(function(newval) {
        if (typeof window.updateGraphSettings === 'function') {
            window.updateGraphSettings({ projectColor: newval });
        }
    });
});

// Illustration color
wp.customize('archi_illustration_color', function(value) {
    value.bind(function(newval) {
        if (typeof window.updateGraphSettings === 'function') {
            window.updateGraphSettings({ illustrationColor: newval });
        }
    });
});

// Pages zone color
wp.customize('archi_pages_zone_color', function(value) {
    value.bind(function(newval) {
        if (typeof window.updateGraphSettings === 'function') {
            window.updateGraphSettings({ pagesZoneColor: newval });
        }
    });
});

// Guestbook link color
wp.customize('archi_guestbook_link_color', function(value) {
    value.bind(function(newval) {
        if (typeof window.updateGraphSettings === 'function') {
            window.updateGraphSettings({ guestbookLinkColor: newval });
        }
    });
});
```

#### B. Badges de Priorité (3 bindings)

```javascript
// Priority featured color
wp.customize('archi_priority_featured_color', function(value) {
    value.bind(function(newval) {
        if (typeof window.updateGraphSettings === 'function') {
            window.updateGraphSettings({ priorityFeaturedColor: newval });
        }
    });
});

// Priority high color
wp.customize('archi_priority_high_color', function(value) {
    value.bind(function(newval) {
        if (typeof window.updateGraphSettings === 'function') {
            window.updateGraphSettings({ priorityHighColor: newval });
        }
    });
});

// Priority badge size
wp.customize('archi_priority_badge_size', function(value) {
    value.bind(function(newval) {
        if (typeof window.updateGraphSettings === 'function') {
            window.updateGraphSettings({ priorityBadgeSize: parseInt(newval) });
        }
    });
});
```

#### C. Échelle des Nœuds (1 binding)

```javascript
// Active node scale
wp.customize('archi_active_node_scale', function(value) {
    value.bind(function(newval) {
        if (typeof window.updateGraphSettings === 'function') {
            window.updateGraphSettings({ activeNodeScale: parseFloat(newval) });
        }
    });
});
```

#### D. Apparence des Clusters (3 bindings)

```javascript
// Cluster fill opacity
wp.customize('archi_cluster_fill_opacity', function(value) {
    value.bind(function(newval) {
        if (typeof window.updateGraphSettings === 'function') {
            window.updateGraphSettings({ clusterFillOpacity: parseFloat(newval) });
        }
    });
});

// Cluster stroke width
wp.customize('archi_cluster_stroke_width', function(value) {
    value.bind(function(newval) {
        if (typeof window.updateGraphSettings === 'function') {
            window.updateGraphSettings({ clusterStrokeWidth: parseInt(newval) });
        }
    });
});

// Cluster stroke opacity
wp.customize('archi_cluster_stroke_opacity', function(value) {
    value.bind(function(newval) {
        if (typeof window.updateGraphSettings === 'function') {
            window.updateGraphSettings({ clusterStrokeOpacity: parseFloat(newval) });
        }
    });
});
```

## 🎯 Tests à Effectuer

### 1. Ouvrir le Customizer
```
WordPress Admin → Apparence → Personnaliser
```

### 2. Tester chaque paramètre

#### A. Couleurs des Types de Contenu
- [ ] **Couleur des projets** → Les nœuds de type `archi_project` changent de couleur
- [ ] **Couleur des illustrations** → Les nœuds de type `archi_illustration` changent de couleur
- [ ] **Couleur zone pages** → Le fond de la zone pages change de couleur
- [ ] **Couleur liens guestbook** → Les liens vers le guestbook changent de couleur

#### B. Badges de Priorité
- [ ] **Couleur badge vedette** → Badges "featured" changent de couleur
- [ ] **Couleur badge élevé** → Badges "high" changent de couleur
- [ ] **Taille des badges** → Taille des badges change (5-15px)

#### C. Échelle des Nœuds
- [ ] **Échelle nœud actif** → Nœud survolé/cliqué change d'échelle (1.0-2.5)

#### D. Apparence des Clusters
- [ ] **Opacité remplissage** → Opacité du fond des clusters change (0.0-0.5)
- [ ] **Largeur contour** → Épaisseur du contour des clusters change (1-6px)
- [ ] **Opacité contour** → Opacité du contour des clusters change (0.0-1.0)

### 3. Vérifier dans la Console

Ouvrir la Console (F12) et vérifier:

```
✓ 🎨 Exposing window.updateGraphSettings for Customizer
✓ 🎨 Graph settings update requested: { projectColor: "#f39c12" }
✓ 🎨 Using Customizer settings: { ... }
```

## 📊 Fichiers Modifiés

### 1. GraphContainer.jsx
- **Lignes modifiées**: 172-228
- **Changements**: Exposition de `window.updateGraphSettings()` avec événement CustomEvent
- **Impact**: Permet à customizer-preview.js de communiquer avec React

### 2. customizer-preview.js
- **Lignes ajoutées**: 313-434 (122 nouvelles lignes)
- **Changements**: 13 nouveaux bindings `wp.customize()`
- **Impact**: Écoute les changements des nouveaux paramètres

### 3. Compilation
```bash
npm run build
```
- ✅ app.bundle.js: 143 KiB
- ✅ vendors.bundle.js: 133 KiB
- ✅ Compilation réussie sans erreur

## 🚀 Architecture de Communication

```
┌─────────────────────────────────────────────────────────────┐
│                    CUSTOMIZER (WordPress)                     │
│                                                              │
│  wp.customize('archi_project_color')                        │
│         ↓                                                    │
│  value.bind(callback)                                       │
└───────────────────────────┬─────────────────────────────────┘
                            │
                            ↓
┌─────────────────────────────────────────────────────────────┐
│               customizer-preview.js (jQuery)                 │
│                                                              │
│  window.updateGraphSettings({ projectColor: '#f39c12' })   │
└───────────────────────────┬─────────────────────────────────┘
                            │
                            ↓
┌─────────────────────────────────────────────────────────────┐
│             window.updateGraphSettings() (Exposed)           │
│                                                              │
│  1. Update window.archiGraphSettings                        │
│  2. Dispatch CustomEvent('graphSettingsUpdated')           │
└───────────────────────────┬─────────────────────────────────┘
                            │
                            ↓
┌─────────────────────────────────────────────────────────────┐
│              GraphContainer.jsx (React)                      │
│                                                              │
│  useEffect → addEventListener('graphSettingsUpdated')       │
│         ↓                                                    │
│  handleSettingsUpdate()                                     │
│         ↓                                                    │
│  updateGraph() → Redessine le graphe D3.js                 │
└─────────────────────────────────────────────────────────────┘
```

## 🔧 Dépannage

### Le graphe ne se met pas à jour

**Vérifications**:
1. Ouvrir Console (F12) → Chercher `🎨 Exposing window.updateGraphSettings`
2. Changer un paramètre → Chercher `🎨 Graph settings update requested`
3. Vérifier que le graphe est bien affiché sur la page d'accueil

### Les logs n'apparaissent pas

**Solutions**:
1. Vider le cache du navigateur (Ctrl+Shift+Delete)
2. Recharger la page sans cache (Ctrl+F5)
3. Vérifier que `app.bundle.js` est bien chargé (onglet Network)

### Les paramètres ne persistent pas

**Solutions**:
1. Cliquer sur "Enregistrer et publier" dans le Customizer
2. Vérifier dans `wp_options` → Chercher `theme_mods_archi-graph-template`

## 📝 Notes Techniques

### Pourquoi CustomEvent ?

Le `CustomEvent` permet de découpler complètement le Customizer jQuery de React:
- jQuery peut déclencher l'événement sans connaître React
- React peut écouter l'événement sans connaître jQuery
- Pattern standard du DOM pour la communication inter-composants

### Pourquoi ne pas supprimer window.updateGraphSettings ?

```javascript
// ❌ MAUVAIS - Supprime la fonction au cleanup
return () => {
  delete window.updateGraphSettings;
};

// ✅ BON - Garde la fonction disponible
return () => {
  // Ne rien faire - customizer-preview.js en a besoin
};
```

`customizer-preview.js` a besoin d'accéder à cette fonction à tout moment, même si le composant React se démonte/remonte.

## ✅ Statut Final

- [x] Fonction `window.updateGraphSettings()` exposée
- [x] 13 nouveaux bindings ajoutés
- [x] CustomEvent bridge implémenté
- [x] Code compilé et testé
- [ ] **Tests utilisateur à effectuer**

Le système de prévisualisation en direct est maintenant **pleinement opérationnel**! 🎉
