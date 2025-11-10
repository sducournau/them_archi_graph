# Guide de Test - Paramètres d'Effets du Graphe

**Date:** 10 Novembre 2025  
**Version:** 1.1.0  
**Fichier testé:** GraphManager.js  

---

## 🎯 Objectif

Vérifier que les paramètres d'animation et d'effet de survol configurés dans l'éditeur WordPress sont correctement appliqués aux nodes du graphe.

---

## ✅ Tests Manuels à Effectuer

### Test 1: Animation d'Entrée Personnalisée

**Objectif:** Vérifier que chaque node peut avoir son propre type d'animation

**Étapes:**
1. Dans l'admin WordPress, éditer un article/projet
2. Dans la meta box "Graph Parameters", section "🎬 Animation Settings":
   - Définir "Animation Type" : `fadeIn`
   - Définir "Animation Duration" : `1500` ms
   - Définir "Animation Delay" : `500` ms
   - Définir "Enter From" : `left`
3. Sauvegarder l'article
4. Afficher la page avec le graphe
5. Observer l'animation du node

**Résultat attendu:**
- ✅ Le node doit apparaître depuis la gauche
- ✅ L'animation doit durer 1,5 secondes
- ✅ L'animation doit commencer après un délai de 0,5 seconde

---

### Test 2: Directions d'Entrée

**Objectif:** Tester toutes les directions d'animation

**Étapes:**
Configurer 5 articles différents avec les directions:
1. Article 1: `top` (entre par le haut)
2. Article 2: `bottom` (entre par le bas)
3. Article 3: `left` (entre par la gauche)
4. Article 4: `right` (entre par la droite)
5. Article 5: `center` (grossit depuis le centre)

**Résultat attendu:**
- ✅ Chaque node doit entrer depuis la direction configurée
- ✅ Les animations doivent être visuellement distinctes

---

### Test 3: Effet de Survol (Hover Scale)

**Objectif:** Vérifier l'agrandissement au survol

**Étapes:**
1. Éditer un article
2. Dans "🎨 Hover Effects":
   - Définir "Hover Scale" : `1.5` (agrandissement 150%)
3. Sauvegarder
4. Sur le graphe, passer la souris sur le node

**Résultat attendu:**
- ✅ Le node doit s'agrandir de 50% au survol
- ✅ Le retour à la taille normale doit être fluide
- ✅ Le label doit devenir en gras

---

### Test 4: Effet Pulse

**Objectif:** Vérifier l'effet de pulsation continue

**Étapes:**
1. Éditer un article
2. Dans "🎨 Hover Effects":
   - Cocher "Pulse Effect"
3. Sauvegarder
4. Observer le node sur le graphe

**Résultat attendu:**
- ✅ Le node doit pulser continuellement (grossir/rétrécir)
- ✅ L'animation doit être douce et régulière
- ✅ Le cycle doit durer environ 2 secondes (1s croissance + 1s réduction)

---

### Test 5: Effet Glow

**Objectif:** Vérifier l'effet de lueur

**Étapes:**
1. Éditer un article
2. Dans "🎨 Hover Effects":
   - Cocher "Glow Effect"
3. Sauvegarder
4. Observer le node sur le graphe

**Résultat attendu:**
- ✅ Le node doit avoir un halo lumineux permanent
- ✅ La lueur doit être visible sur fond sombre
- ✅ L'effet doit persister même sans survol

---

### Test 6: Combinaison d'Effets

**Objectif:** Vérifier que plusieurs effets peuvent coexister

**Étapes:**
1. Éditer un article
2. Configurer:
   - Animation: `fadeIn`, durée 1200ms, depuis `top`
   - Hover Scale: `1.3`
   - Pulse Effect: `activé`
   - Glow Effect: `activé`
3. Sauvegarder
4. Observer le comportement complet

**Résultat attendu:**
- ✅ Le node entre depuis le haut en 1,2 secondes
- ✅ Une fois affiché, il pulse continuellement
- ✅ Il a un effet de lueur permanent
- ✅ Au survol, il s'agrandit de 30% en plus

---

### Test 7: Valeurs par Défaut

**Objectif:** Vérifier que les nodes sans configuration utilisent les valeurs par défaut

**Étapes:**
1. Créer un nouvel article
2. Ne configurer AUCUN paramètre d'effet
3. Publier l'article
4. Observer sur le graphe

**Résultat attendu:**
- ✅ Animation type: `fadeIn`
- ✅ Animation duration: `800ms`
- ✅ Animation delay: `0ms`
- ✅ Enter from: `center`
- ✅ Hover scale: `1.15`
- ✅ Pulse: `désactivé`
- ✅ Glow: `désactivé`

---

### Test 8: Types d'Animation Différents

**Objectif:** Tester les différents types d'easing

**Étapes:**
Configurer 5 articles avec différents types d'easing:
1. Article 1: `ease-out` (par défaut)
2. Article 2: `bounce` (rebondit)
3. Article 3: `elastic` (élastique)
4. Article 4: `linear` (linéaire)
5. Article 5: `ease-in-out` (accélère puis ralentit)

**Résultat attendu:**
- ✅ Chaque animation doit avoir un comportement visuel distinct
- ✅ Les effets doivent être cohérents avec leur nom

---

## 🐛 Vérifications de Régression

### Vérifier que les fonctionnalités existantes fonctionnent toujours

- ✅ Les liens entre nodes basés sur les catégories fonctionnent
- ✅ Le drag & drop des nodes fonctionne
- ✅ Le zoom et le pan fonctionnent
- ✅ Les polygones de catégories s'affichent correctement
- ✅ Les couleurs et tailles personnalisées sont respectées
- ✅ Le paramètre `hide_links` cache bien les liens

---

## 🔍 Tests Console du Navigateur

### Vérifier la transformation des données

1. Ouvrir la console du navigateur (F12)
2. Sur la page du graphe, exécuter:

```javascript
// Récupérer les données du graphe
fetch('/wp-json/archi/v1/articles')
  .then(r => r.json())
  .then(data => {
    console.log('Données API brutes:', data.articles[0]);
    
    // Vérifier la structure
    const node = data.articles[0];
    console.log('Paramètres plats présents:');
    console.log('- animation_type:', node.animation_type);
    console.log('- animation_duration:', node.animation_duration);
    console.log('- hover_scale:', node.hover_scale);
    console.log('- pulse_effect:', node.pulse_effect);
    console.log('- glow_effect:', node.glow_effect);
  });
```

**Résultat attendu:**
- ✅ Les paramètres plats doivent être présents dans la réponse API
- ✅ Les valeurs doivent correspondre à ce qui a été configuré

### Vérifier la transformation dans GraphManager

```javascript
// Après le chargement du graphe, dans la console
if (window.graphManagerInstance) {
  const node = window.graphManagerInstance.nodes[0];
  console.log('Node après transformation:', node);
  console.log('Structure animation:', node.animation);
  console.log('Structure hover:', node.hover);
}
```

**Résultat attendu:**
- ✅ Chaque node doit avoir un objet `animation` imbriqué
- ✅ Chaque node doit avoir un objet `hover` imbriqué
- ✅ Les valeurs dans ces objets doivent correspondre aux paramètres configurés

---

## 📊 Checklist Complète

- [ ] Test 1: Animation d'entrée personnalisée
- [ ] Test 2: Toutes les directions d'entrée
- [ ] Test 3: Hover scale personnalisé
- [ ] Test 4: Effet pulse
- [ ] Test 5: Effet glow
- [ ] Test 6: Combinaison d'effets
- [ ] Test 7: Valeurs par défaut
- [ ] Test 8: Différents types d'easing
- [ ] Vérification de régression: liens
- [ ] Vérification de régression: drag & drop
- [ ] Vérification de régression: zoom/pan
- [ ] Vérification de régression: polygones
- [ ] Vérification de régression: couleurs/tailles
- [ ] Test console: données API
- [ ] Test console: transformation

---

## 🎨 Tests Visuels Recommandés

### Configuration de Test Idéale

Créer un environnement de test avec:

1. **Node "Classique"** - paramètres par défaut
2. **Node "Spectaculaire"** - tous les effets activés
3. **Node "Discret"** - animations subtiles
4. **Node "Featured"** - pulse + glow pour attirer l'attention
5. **Node "Rapide"** - animation très courte (200ms)
6. **Node "Lent"** - animation très longue (3000ms)

Cela permettra de voir tous les cas d'usage en un seul coup d'œil.

---

## 📝 Rapport de Bug

Si un test échoue, noter:

1. **Test échoué:** (numéro et nom)
2. **Comportement observé:** (ce qui se passe)
3. **Comportement attendu:** (ce qui devrait se passer)
4. **Configuration utilisée:** (valeurs des paramètres)
5. **Console errors:** (copier les erreurs JavaScript éventuelles)
6. **Navigateur:** (Chrome, Firefox, Safari, etc.)

---

## ✅ Validation Finale

Le fix est considéré comme réussi si:

- ✅ Tous les tests manuels passent
- ✅ Aucune régression n'est détectée
- ✅ Les transformations console affichent les bonnes structures
- ✅ Les performances du graphe ne sont pas dégradées
- ✅ Le code est compatible avec tous les navigateurs modernes
