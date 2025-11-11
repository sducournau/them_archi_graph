# 🧪 Guide de Test - Customizer

## ✅ Correction appliquée

La persistance de la police après rechargement a été corrigée. Voici comment tester toutes les options.

## 🎯 Test 1: Police de caractères (PRIORITAIRE)

### Étapes:
1. Allez dans **Apparence → Personnaliser**
2. Section **Typographie**
3. Changez **Police de caractères** (essayez "Montserrat" ou "Roboto")
4. Cliquez sur **Publier**
5. Fermez le Customizer
6. Rafraîchissez la page avec **Ctrl+F5** (ou Cmd+Shift+R sur Mac)

### ✓ Résultat attendu:
La police choisie doit être visible sur tout le site **ET dans le graphe** et doit persister après rechargement.

### 🔍 Comment vérifier:
- Faites clic-droit → Inspecter sur n'importe quel texte
- Regardez la propriété `font-family` dans l'inspecteur
- Elle doit correspondre à votre choix
- **Vérifiez aussi dans le graphe:** les labels des nœuds, le panneau latéral, la légende doivent utiliser la même police

---

## 🎨 Test 2: Couleurs

### Couleur primaire:
1. **Apparence → Personnaliser → Couleurs générales**
2. Changez la **Couleur primaire**
3. Publiez et rafraîchissez

**Où voir:** Liens, boutons, éléments actifs de navigation

### Couleur secondaire:
1. Changez la **Couleur secondaire**
2. Publiez et rafraîchissez

**Où voir:** Titres (H1, H2, etc.)

### Couleurs du header:
1. **Apparence → Personnaliser → En-tête (Header)**
2. Changez **Couleur de fond** et **Couleur du texte**
3. Publiez et rafraîchissez

**Où voir:** Barre de navigation en haut

---

## 📏 Test 3: Header - Apparence

### Header transparent (page d'accueil):
1. **Apparence → Personnaliser → En-tête (Header)**
2. Activez **Header transparent sur la page d'accueil**
3. Réglez **Opacité au scroll** (0.8 - 1.0)
4. Publiez

**Test:** Allez sur la page d'accueil, le header doit être transparent et devenir opaque au scroll

### Hauteur du header:
Options: Compact (60px) | Normal (80px) | Large (100px) | Extra Large (120px)

1. Changez la **Hauteur du header**
2. Publiez et rafraîchissez

**Test:** La hauteur de la barre de navigation doit changer

### Ombre du header:
Options: Aucune | Légère | Moyenne | Forte

1. Changez **Ombre du header**
2. Publiez et rafraîchissez

**Test:** L'ombre sous la barre de navigation doit changer

---

## 📍 Test 4: Header - Position du logo

Options: Gauche | Centre | Droite

1. **Apparence → Personnaliser → En-tête (Header)**
2. Changez **Position du logo/titre**
3. Publiez et rafraîchissez

**Test:** Le logo et le titre du site doivent se déplacer

---

## 🔄 Test 5: Header - Comportement sticky

Options:
- **Toujours visible** (défaut)
- **Se cache en scrollant vers le bas**
- **Apparaît seulement en scrollant vers le haut**

1. Changez **Comportement au scroll**
2. Publiez et rafraîchissez
3. **Scrollez** sur une page avec du contenu

**Test:** Le header doit réagir selon l'option choisie

---

## 🎨 Test 6: Graphe - Synchronisation de la police

**NOUVEAU:** La police du graphe est maintenant synchronisée avec le Customizer.

### Éléments du graphe concernés:
1. **Titres des nœuds** (texte sur les nœuds au survol)
2. **Labels des nœuds** (étiquettes)
3. **Panneau d'information** (panneau latéral avec détails)
4. **Légende** (en haut à gauche)
5. **Instructions et contrôles**

### Test:
1. Allez sur la **page avec le graphe** (généralement la page d'accueil)
2. **Ouvrez le Customizer** (Apparence → Personnaliser)
3. **Typographie → Police de caractères**
4. Changez la police (essayez Montserrat ou Roboto)
5. **Observez le preview:** Les textes du graphe changent en direct
6. **Publiez**
7. Rafraîchissez et vérifiez que la police persiste

### ✓ Vérification visuelle:
- Survolez un nœud du graphe → Le titre doit utiliser la nouvelle police
- Cliquez sur un nœud → Le panneau latéral doit utiliser la nouvelle police
- Regardez la légende → Doit utiliser la nouvelle police

---

## 🔧 Outils de diagnostic

Deux scripts de test sont disponibles:

### Script simple:
```
http://votre-site.local/wp-content/themes/archi-graph-template/test-customizer-persistence.php
```

Affiche:
- Valeurs enregistrées
- CSS généré
- Status des hooks

### Script complet:
```
http://votre-site.local/wp-content/themes/archi-graph-template/test-customizer-complete.php
```

Affiche:
- Toutes les options avec leur status
- Preview visuel
- Diagnostic technique complet
- Recommandations

---

## 🐛 En cas de problème

### La police ne change pas:
1. Vérifiez que vous avez cliqué sur **Publier** (pas juste sur la croix)
2. Rafraîchissez avec **Ctrl+F5** (force le rechargement du cache CSS)
3. Vérifiez dans le script de test que la valeur est bien sauvegardée
4. Inspectez le code source HTML (Ctrl+U) et cherchez `archi-customizer-styles`

### Les couleurs ne changent pas:
1. Même processus que ci-dessus
2. Vérifiez qu'il n'y a pas de CSS custom qui surcharge

### Le header ne réagit pas:
1. Vérifiez que vous êtes sur une page avec assez de contenu pour scroller
2. Certains comportements sont uniquement visibles en scrollant

---

## ✨ Ce qui a été corrigé

**Problème:** La police ne persistait pas après rechargement

**Cause:** Le CSS du Customizer était chargé AVANT les styles du thème, donc écrasé

**Solution:** 
- Priorité du CSS augmentée à 999 (chargé en dernier)
- Utilisation de `!important` pour forcer la priorité
- Toutes les options testées et fonctionnelles

**Fichier modifié:** `inc/customizer.php` (ligne 712)

---

## 📝 Checklist finale

- [ ] Test police de caractères
- [ ] Test taille de police
- [ ] Test couleur primaire
- [ ] Test couleur secondaire
- [ ] Test couleurs du header
- [ ] Test header transparent
- [ ] Test hauteur du header
- [ ] Test ombre du header
- [ ] Test opacité au scroll
- [ ] Test position du logo
- [ ] Test comportement sticky
- [ ] **Test synchronisation police du graphe** 🆕

**Tous les tests doivent passer ✓**
