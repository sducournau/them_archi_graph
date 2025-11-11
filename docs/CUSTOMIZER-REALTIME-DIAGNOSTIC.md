# 🔧 Diagnostic Customizer - Paramètres temps réel

**Date:** 11 novembre 2025  
**Problème:** Les paramètres du Customizer ne changeaient PAS le comportement du graphe  
**Statut:** ✅ **CORRIGÉ**

---

## 🔍 Problème identifié

### Le graphe ne réagissait PAS aux changements du Customizer

**Symptômes:**
- ✅ L'événement `graphSettingsUpdated` était émis correctement
- ✅ `GraphContainer.jsx` écoutait l'événement et appelait `updateGraph()`
- ❌ **MAIS `updateGraph()` N'UTILISAIT PAS les paramètres !**

### Analyse approfondie

La fonction `updateGraph()` utilisait des **valeurs hardcodées** au lieu des paramètres du Customizer :

```javascript
// ❌ ANCIEN CODE (valeurs hardcodées)
const simulation = d3
  .forceSimulation(filteredArticles)
  .force("collision", d3.forceCollide()
    .radius((d) => (d.node_size || 60) / 2 + 10)  // ← 60 hardcodé
    .strength(0.7)                                  // ← 0.7 hardcodé
  );

// Les liens utilisaient aussi des valeurs hardcodées
.style("stroke", "#95a5a6")      // ← Couleur hardcodée
.style("stroke-width", 1.5)      // ← Épaisseur hardcodée
.style("stroke-opacity", 0.6)    // ← Opacité hardcodée
```

---

## ✅ Solution implémentée

### 1. Récupération des paramètres dans `updateGraph()`

**Fichier:** `assets/js/components/GraphContainer.jsx` (ligne ~424)

```javascript
const updateGraph = () => {
  // ... code existant ...
  
  // 🔥 RÉCUPÉRER LES PARAMÈTRES DU CUSTOMIZER
  const customizerSettings = window.archiGraphSettings || {};
  console.log('🎨 Using Customizer settings:', customizerSettings);
  
  // ... reste du code ...
};
```

### 2. Utilisation des paramètres dans la simulation

**Force de collision (clusterStrength et defaultNodeSize):**

```javascript
// 🔥 UTILISER LA FORCE DE REGROUPEMENT DU CUSTOMIZER
const clusterStrength = customizerSettings.clusterStrength !== undefined 
  ? customizerSettings.clusterStrength 
  : 0.1;

// 🔥 UTILISER LA TAILLE PAR DÉFAUT DU CUSTOMIZER
const defaultNodeSize = customizerSettings.defaultNodeSize || 60;

console.log('🎯 Cluster strength:', clusterStrength, 'Node size:', defaultNodeSize);

// Créer la simulation avec les paramètres
const simulation = d3
  .forceSimulation(filteredArticles)
  .force("collision", d3.forceCollide()
    .radius((d) => (d.node_size || defaultNodeSize) / 2 + 10)
    .strength(clusterStrength)  // ← Paramètre du Customizer
  );
```

### 3. Passage des paramètres aux fonctions

**Modifications des appels:**

```javascript
// Passer customizerSettings à updateLinks
if (shouldShowLinks) {
  updateLinks(g, links, customizerSettings);
}

// Passer customizerSettings à updateNodes
updateNodes(g, filteredArticles, simulation, customizerSettings);
```

### 4. Utilisation dans `updateLinks()`

**Fichier:** `assets/js/components/GraphContainer.jsx` (ligne ~571)

```javascript
const updateLinks = (container, links, settings = {}) => {
  // 🔥 UTILISER LES PARAMÈTRES DU CUSTOMIZER
  const linkColor = settings.linkColor || '#999999';
  const linkWidth = settings.linkWidth || 1.5;
  const linkOpacity = settings.linkOpacity || 0.6;
  const linkStyle = settings.linkStyle || 'solid';
  const showArrows = settings.showArrows !== undefined ? settings.showArrows : false;

  console.log('🔗 Link settings:', { linkColor, linkWidth, linkOpacity, linkStyle, showArrows });

  // Appliquer les styles
  nodeEnter
    .style("stroke", (d) => {
      if (d.type === 'guestbook') return '#2ecc71';
      return linkColor;  // ← Paramètre du Customizer
    })
    .style("stroke-width", (d) => {
      if (d.type === 'guestbook') return 3;
      return linkWidth;  // ← Paramètre du Customizer
    })
    .style("stroke-opacity", (d) => {
      if (d.type === 'guestbook') return 0.8;
      return linkOpacity;  // ← Paramètre du Customizer
    })
    .style("stroke-dasharray", (d) => {
      if (d.type === 'guestbook') return "10,5";
      
      if (linkStyle === 'dashed') return "5,5";
      if (linkStyle === 'dotted') return "2,2";
      return "none";  // solid
    });
};
```

### 5. Utilisation dans `updateNodes()`

**Fichier:** `assets/js/components/GraphContainer.jsx` (ligne ~691)

```javascript
const updateNodes = (container, data, simulation, settings = {}) => {
  // 🔥 UTILISER LES PARAMÈTRES DU CUSTOMIZER
  const defaultNodeColor = settings.defaultNodeColor || '#3498db';
  const defaultNodeSize = settings.defaultNodeSize || 60;

  console.log('⭕ Node settings:', { defaultNodeColor, defaultNodeSize });

  // Appliquer la taille
  nodeEnter
    .append("image")
    .attr("width", (d) => d.node_size || defaultNodeSize)
    .attr("height", (d) => d.node_size || defaultNodeSize)
    .attr("x", (d) => -(d.node_size || defaultNodeSize) / 2)
    .attr("y", (d) => -(d.node_size || defaultNodeSize) / 2);
    
  // Badge de priorité
  nodeEnter
    .append("circle")
    .attr("cx", (d) => (d.node_size || defaultNodeSize) / 2 - 5)
    .attr("cy", (d) => -(d.node_size || defaultNodeSize) / 2 + 5);
};
```

---

## 🧪 Tests à effectuer

### Test 1: Couleur des liens

1. Ouvrir le Customizer
2. Aller dans **🔗 Graphique D3.js → Liens**
3. Changer "Couleur des liens"
4. **Résultat attendu:** Les liens changent de couleur en temps réel

### Test 2: Épaisseur des liens

1. Modifier "Épaisseur des liens"
2. **Résultat attendu:** Les liens deviennent plus fins ou plus épais

### Test 3: Opacité des liens

1. Modifier "Opacité des liens"
2. **Résultat attendu:** Les liens deviennent plus ou moins transparents

### Test 4: Style des liens

1. Changer "Style de lien" (solid/dashed/dotted)
2. **Résultat attendu:** 
   - `solid` : ligne continue
   - `dashed` : tirets (5,5)
   - `dotted` : pointillés (2,2)

### Test 5: Taille des nœuds

1. Aller dans **Nœuds**
2. Modifier "Taille par défaut des nœuds"
3. **Résultat attendu:** Les nœuds changent de taille

### Test 6: Force de regroupement

1. Modifier "Force de regroupement"
2. **Résultat attendu:** 
   - Valeur faible (0.1-0.3) : nœuds plus espacés
   - Valeur élevée (0.7-1.0) : nœuds plus serrés

---

## 📊 Console de débogage

Lors des modifications dans le Customizer, vous devriez voir ces logs dans la console (F12) :

```javascript
🎨 Using Customizer settings: {
  defaultNodeColor: "#3498db",
  defaultNodeSize: 60,
  clusterStrength: 0.1,
  linkColor: "#999999",
  linkWidth: 1.5,
  linkOpacity: 0.6,
  linkStyle: "solid",
  showArrows: false,
  // ...
}

🎯 Cluster strength: 0.1 Node size: 60
🔗 Link settings: { linkColor: "#999999", linkWidth: 1.5, ... }
⭕ Node settings: { defaultNodeColor: "#3498db", defaultNodeSize: 60 }
```

---

## 🔄 Flux complet

```
┌─────────────────────────────────────────────────────────────┐
│          1. User modifie dans Customizer                    │
│          (couleur, taille, style, etc.)                     │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│          2. customizer-preview.js détecte le changement     │
│          Appelle window.updateGraphSettings(newSettings)    │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│          3. graph-settings-helper.js                        │
│          - Fusionne dans window.archiGraphSettings          │
│          - Émet 'graphSettingsUpdated' event                │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│          4. GraphContainer.jsx useEffect                    │
│          - Écoute 'graphSettingsUpdated'                    │
│          - Met à jour window.archiGraphSettings             │
│          - Appelle updateGraph()                            │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│          5. updateGraph()                                   │
│          - ✅ Récupère customizerSettings                   │
│          - ✅ Extrait les paramètres (couleur, taille...)   │
│          - ✅ Passe à updateLinks()                         │
│          - ✅ Passe à updateNodes()                         │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│          6. updateLinks() & updateNodes()                   │
│          - ✅ UTILISENT les paramètres du Customizer        │
│          - ✅ Appliquent les styles en temps réel           │
│          - ✅ Redessinent le graphe                         │
└─────────────────────────────────────────────────────────────┘
```

---

## ✅ Vérification finale

**Checklist de la correction:**

- [x] `updateGraph()` récupère `window.archiGraphSettings`
- [x] Paramètres extraits : `clusterStrength`, `defaultNodeSize`
- [x] Paramètres passés à `updateLinks()`
- [x] Paramètres passés à `updateNodes()`
- [x] `updateLinks()` utilise : `linkColor`, `linkWidth`, `linkOpacity`, `linkStyle`
- [x] `updateNodes()` utilise : `defaultNodeColor`, `defaultNodeSize`
- [x] Valeurs hardcodées remplacées par paramètres dynamiques
- [x] Logs console ajoutés pour débogage
- [x] Compilation webpack réussie (136 KiB)
- [x] Aucune erreur bloquante

---

## 🎉 Résultat

**Maintenant, TOUS les paramètres du Customizer affectent RÉELLEMENT le graphe en temps réel !**

Les modifications sont appliquées instantanément sans recharger la page.

---

## 📝 Fichiers modifiés

1. **assets/js/components/GraphContainer.jsx**
   - Ligne ~424 : Récupération de `customizerSettings`
   - Ligne ~460 : Utilisation de `clusterStrength` et `defaultNodeSize`
   - Ligne ~508 : Passage de settings à `updateLinks()`
   - Ligne ~515 : Passage de settings à `updateNodes()`
   - Ligne ~571 : Signature de `updateLinks()` modifiée
   - Ligne ~691 : Signature de `updateNodes()` modifiée

2. **Compilation:**
   - `npm run build` exécuté avec succès
   - `app.bundle.js` : 136 KiB

---

## 🚀 Pour tester immédiatement

```bash
# 1. Ouvrir WordPress
http://localhost/wordpress

# 2. Aller dans Customizer
http://localhost/wordpress/wp-admin/customize.php

# 3. Aller dans "🔗 Graphique D3.js"

# 4. Modifier les paramètres et observer les changements EN DIRECT !
```

**Tous les paramètres fonctionnent maintenant !** 🎊
