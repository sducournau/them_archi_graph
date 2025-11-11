# Guide de Test : Effets Visuels du Graphe

**Version:** 1.3.1  
**Date:** 2025-11-10

## 🎯 Test Rapide des Nouveaux Effets

### 1. Test de Survol (Hover)

**Objectif:** Vérifier que le halo apparaît et que le node s'agrandit au survol

**Étapes:**
1. Ouvrir la page d'accueil avec le graphe
2. Passer la souris sur différents nodes
3. Observer les effets visuels

**✅ Résultat attendu:**
- Un halo lumineux apparaît autour du node (2px stroke, opacity 0.4)
- Le cercle s'agrandit selon le paramètre `hover_scale` (défaut: 1.1x)
- Le label devient gras (font-weight: 600)
- Le node passe au premier plan (z-index simulé)

**Console test:**
```javascript
// Vérifier les éléments créés
document.querySelectorAll('.node-halo').length // Devrait égaler le nombre de nodes
document.querySelectorAll('.node-circle').length // Idem
document.querySelectorAll('.node-shine').length // Idem
```

---

### 2. Test du Clic (Shockwave + Toggle State)

**Objectif:** Vérifier que le clic crée une onde de choc et toggle l'état actif/inactif

**Étapes:**
1. Cliquer sur un node actif
2. Observer l'animation
3. Cliquer à nouveau pour réactiver

**✅ Résultat attendu:**
- Une onde de choc se propage (cercle qui grandit de 30px à 90px)
- Le node passe en état inactif (opacity réduite, grayscale)
- Animation de rebond (scale down puis up)
- Nouveau clic réactive le node (opacity normale, couleurs vives)

**Console test:**
```javascript
// Compter les nodes inactifs
document.querySelectorAll('.node-inactive').length

// Déclencher un clic programmatique
const firstNode = document.querySelector('.graph-node');
firstNode.dispatchEvent(new MouseEvent('click', { bubbles: true }));

// Vérifier le changement d'état
document.querySelectorAll('.node-inactive').length // Devrait avoir changé de +1 ou -1
```

---

### 3. Test de Pulsation (Breathing)

**Objectif:** Vérifier que les nodes inactifs "respirent" doucement

**Étapes:**
1. Cliquer sur plusieurs nodes pour les rendre inactifs
2. Attendre 2-3 secondes
3. Observer l'animation subtile

**✅ Résultat attendu:**
- Les nodes inactifs pulsent lentement (cycle de 2 secondes)
- L'opacité varie entre 0.3 et 0.4
- Le halo pulse aussi (stroke 0 ↔ 2px)
- L'animation est continue et fluide

**Console test:**
```javascript
// Vérifier les transitions actives
const inactiveCircle = document.querySelector('.node-inactive .node-circle');
getComputedStyle(inactiveCircle).opacity // Devrait varier entre 0.3 et 0.4
```

---

### 4. Test de la Structure SVG

**Objectif:** Vérifier que chaque node a bien 4 éléments

**Console test:**
```javascript
const firstNode = document.querySelector('.graph-node');

// Compter les enfants
firstNode.children.length // Devrait être 4

// Vérifier les classes
Array.from(firstNode.children).map(child => child.className.baseVal)
// Résultat attendu: ["node-halo", "node-circle", "node-shine", "node-label"]
```

---

### 5. Test du CSS

**Objectif:** Vérifier que le fichier graph-effects.css est chargé

**Console test:**
```javascript
// Lister les stylesheets
Array.from(document.styleSheets)
  .map(sheet => sheet.href)
  .filter(href => href && href.includes('graph-effects'))
// Devrait retourner un array avec l'URL du fichier CSS
```

---

### 6. Test de Performance

**Objectif:** Vérifier que les animations sont fluides (60 FPS)

**Étapes:**
1. Ouvrir les DevTools → Performance
2. Démarrer l'enregistrement
3. Survoler plusieurs nodes rapidement
4. Cliquer sur plusieurs nodes
5. Arrêter l'enregistrement

**✅ Résultat attendu:**
- Framerate constant à ~60 FPS
- Pas de baisse majeure de performance
- Animations smooth sans saccades

**Console test:**
```javascript
// Compter les transitions actives
const transitionCount = Array.from(document.querySelectorAll('.graph-node *'))
  .filter(el => getComputedStyle(el).transition !== 'all 0s ease 0s').length;
console.log(`${transitionCount} éléments avec transitions actives`);
```

---

### 7. Test Accessibilité

**Objectif:** Vérifier que les effets respectent prefers-reduced-motion

**Console test:**
```javascript
// Simuler reduced motion
const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
console.log('Reduced motion:', mediaQuery.matches);

// Vérifier si les animations sont désactivées
const firstCircle = document.querySelector('.node-circle');
getComputedStyle(firstCircle).animation // Devrait être 'none' si reduced motion
```

---

### 8. Test Multi-Browser

**Navigateurs à tester:**
- ✅ Chrome/Edge (Chromium)
- ✅ Firefox
- ✅ Safari (macOS)
- ✅ Mobile Safari (iOS)
- ✅ Mobile Chrome (Android)

**Points de vigilance:**
- Halo visible correctement
- Shockwave smooth
- Pulsation fluide
- Pas de glitches visuels

---

## 🐛 Debugging

### Si le halo n'apparaît pas:

```javascript
// Vérifier les attributs du halo
const halo = document.querySelector('.node-halo');
console.log({
  r: halo.getAttribute('r'),
  strokeWidth: halo.getAttribute('stroke-width'),
  strokeOpacity: halo.getAttribute('stroke-opacity'),
  fill: halo.getAttribute('fill'),
  stroke: halo.getAttribute('stroke')
});
```

### Si le shockwave ne fonctionne pas:

```javascript
// Vérifier si l'élément est créé puis supprimé
const observer = new MutationObserver(mutations => {
  mutations.forEach(mutation => {
    mutation.addedNodes.forEach(node => {
      if (node.classList?.contains('node-shockwave')) {
        console.log('Shockwave créé:', node);
      }
    });
  });
});

observer.observe(document.querySelector('svg'), { childList: true, subtree: true });

// Cliquer sur un node et voir si "Shockwave créé" apparaît
```

### Si la pulsation ne marche pas:

```javascript
// Vérifier si la méthode est appelée
const graphManager = window.graphManager; // Si exposé globalement
console.log(typeof graphManager?.applyInactivePulse); // Devrait être 'function'

// Vérifier les nodes inactifs
document.querySelectorAll('.node-inactive').length // > 0 ?
```

---

## 📊 Résumé des Tests

| Test | Objectif | Status |
|------|----------|--------|
| Hover | Halo + agrandissement | ⏳ À tester |
| Click | Shockwave + toggle | ⏳ À tester |
| Breathing | Pulsation continue | ⏳ À tester |
| Structure SVG | 4 éléments par node | ⏳ À tester |
| CSS | Fichier chargé | ⏳ À tester |
| Performance | 60 FPS maintenu | ⏳ À tester |
| Accessibilité | Reduced motion | ⏳ À tester |
| Multi-browser | Tous navigateurs | ⏳ À tester |

**Statuts:**
- ⏳ À tester
- ✅ Passé
- ❌ Échoué
- ⚠️ Problème mineur

---

## 🎉 Test Complet Réussi Si:

1. ✅ Halo apparaît au survol avec animation fluide
2. ✅ Circle s'agrandit correctement (custom hover_scale)
3. ✅ Shockwave se propage au clic
4. ✅ État toggle entre actif/inactif
5. ✅ Nodes inactifs pulsent doucement
6. ✅ Z-index fonctionne (node au premier plan)
7. ✅ Performance maintenue (60 FPS)
8. ✅ Accessibilité respectée

---

**Pour signaler un bug:** Copier la console output et les détails visuels observés.
