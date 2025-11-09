# Guide de Test - Consolidation Backend

## 🚀 Tests Rapides (5 minutes)

### 1. Accéder à la nouvelle interface admin

1. Connectez-vous à WordPress admin
2. Dans le menu latéral, cherchez **"Archi Graph"** avec icône 🕸️
3. Cliquez dessus

**Résultat attendu:**
- Page s'ouvre avec 5 onglets: Dashboard, Graphique, Contenus, Blocs, Outils
- Dashboard affiche 4 cartes colorées avec statistiques
- Pas d'erreurs PHP

---

### 2. Tester les paramètres du graphique

1. Cliquez sur l'onglet **"Graphique"** 🎨
2. Modifiez quelques paramètres:
   - Cochez "Ajout automatique"
   - Changez la "Force de liaison" (slider)
   - Modifiez la "Couleur par défaut" (color picker)
3. Cliquez **"Enregistrer les paramètres"** en bas

**Résultat attendu:**
- Sliders montrent la valeur en temps réel
- Après save: message de succès en haut
- Rechargez la page: valeurs sont conservées

---

### 3. Tester les outils

1. Allez dans l'onglet **"Outils"** 🔧
2. Cliquez **"Vider le cache"**
3. Confirmez dans la popup
4. Attendez ~1 seconde

**Résultat attendu:**
- Notification verte apparaît: "Cache vidé avec succès"
- Notification disparaît après 5 secondes
- Pas d'erreur console

---

### 4. Tester métadonnées (Quick Test)

Dans une nouvelle fenêtre/onglet:

1. Allez dans **Articles > Tous les articles**
2. Survolez un article, clic sur **"Modification rapide"**
3. Cherchez le champ "Graphique" avec dropdown
4. Changez entre "Afficher" / "Masquer"
5. Cliquez **"Mettre à jour"**

**Résultat attendu:**
- Changement sauvegardé sans erreur
- Message de confirmation

---

### 5. Tester éditeur Gutenberg (Animations)

1. Créez ou éditez un article
2. Ajoutez un bloc Archi (ex: "Gestionnaire d'Article")
3. Cliquez sur le bloc pour le sélectionner

**Résultat attendu:**
- Bloc s'anime légèrement (slide up)
- Outline bleu pulse autour du bloc
- InspectorControls (panneau droit) s'ouvre avec animation

---

## 🧪 Tests Approfondis (15 minutes)

### Test Dashboard

**Vérifications:**
- [ ] Statistiques affichent des nombres corrects
- [ ] 4 cartes avec gradients de couleur
- [ ] "Actions rapides" ont 4 boutons fonctionnels
- [ ] Section "Santé du système" montre 3 checks verts ✓

**Console browser:**
```javascript
// Ouvrir console (F12), taper:
console.log(archiAdmin);
// Devrait afficher un objet avec ajaxUrl, nonce, etc.
```

---

### Test Validation

1. Onglet **Graphique**
2. Mettez "Distance minimale" à `999` (max = 300)
3. Essayez de sauvegarder

**Résultat attendu:**
- Champ devient rouge (shake animation)
- Message d'erreur en dessous
- Sauvegarde bloquée

---

### Test API Métadonnées

Dans **Outils > Inspecteur de code** (F12), console:

```php
// Dans PHP (ex: functions.php temporaire):
$post_id = 1; // ID d'un post existant

// Test get
$color = archi_get_graph_meta($post_id, '_archi_node_color');
var_dump($color); // Devrait afficher une couleur HEX ou '#3498db'

// Test update avec validation
$result = archi_update_graph_meta($post_id, '_archi_node_color', '#INVALID');
var_dump($result); // Devrait être WP_Error

$result = archi_update_graph_meta($post_id, '_archi_node_color', '#FF5733');
var_dump($result); // Devrait être true
```

---

### Test Responsive

1. Ouvrez l'interface admin
2. Redimensionnez la fenêtre à < 782px (mobile)

**Vérifications:**
- [ ] Onglets wrap sur plusieurs lignes
- [ ] Dashboard: cartes empilées verticalement (1 colonne)
- [ ] Formulaires restent lisibles
- [ ] Pas de scroll horizontal

---

## ❌ Problèmes Connus & Solutions

### "Cannot find archiAdmin"

**Symptôme:** Erreur console JS  
**Cause:** Assets JS pas chargés  
**Solution:** Vider cache WordPress (admin bar > Purge cache)

### "Headers already sent"

**Symptôme:** Erreur PHP lors AJAX  
**Cause:** Espace/newline avant `<?php` dans nouveau fichier  
**Solution:** Vérifier début de `metadata-manager.php` et `admin-unified-settings.php`

### Menu "Archi Graph" n'apparaît pas

**Symptôme:** Menu absent dans admin  
**Cause:** Fichier non inclus  
**Solution:** Vérifier dans `functions.php`:
```php
require_once ARCHI_THEME_DIR . '/inc/admin-unified-settings.php';
```

### Statistiques affichent "0"

**Symptôme:** Dashboard vide  
**Cause:** Aucun contenu publié avec métadonnées  
**Solution:** 
1. Créer un projet
2. Meta box "Graphique": cocher "Afficher"
3. Publier
4. Recharger dashboard

---

## 🎯 Checklist Complète

### Phase 1: Interface Admin ✅
- [x] Page "Archi Graph" accessible
- [x] 5 onglets fonctionnels
- [x] Dashboard avec stats
- [x] Formulaire graphique sauvegarde
- [x] Outils (cache, relations) AJAX

### Phase 2: Métadonnées ✅
- [x] Classe `Archi_Metadata_Manager` chargée
- [x] Validation automatique
- [x] API simplifiée (`archi_get_*_meta`)
- [x] Backward compatible (anciens posts)

### Phase 3: UX Gutenberg ✅
- [x] Animations bloc actif
- [x] Feedback toggles
- [x] Hover effects
- [x] Loading states

### Phase 4: Documentation ✅
- [x] Audit complet (AUDIT.md)
- [x] Résumé changements (SUMMARY.md)
- [x] Guide de test (ce fichier)

---

## 📊 Métriques de Performance

### Temps de chargement admin
- **Avant:** ~800ms
- **Objectif:** < 400ms
- **À tester:** Network tab (F12)

### Requêtes DB dashboard
- **Optimum:** < 10 requêtes
- **À tester:** Plugin Query Monitor

---

## 🐛 Reporter un Bug

Si vous trouvez un problème:

1. **Console browser** (F12) → Copier erreurs JS
2. **PHP errors** → Voir `wp-content/debug.log` si `WP_DEBUG` activé
3. **Reproduire** → Noter étapes exactes
4. **Screenshots** → Capturer interface

**Template bug report:**
```
**Environnement:**
- WP version: 
- PHP version:
- Browser:

**Étapes:**
1. 
2. 
3. 

**Résultat attendu:**


**Résultat actuel:**


**Erreurs:**
[copier console/logs]
```

---

## ✅ Tests Réussis = Prêt pour Production

Si tous les tests passent:
- ✅ Commiter les changements
- ✅ Tester sur environnement staging
- ✅ Backup base de données
- ✅ Déployer sur production

**Temps test total:** ~20 minutes  
**Tests critiques:** ~5 minutes

---

**Bon test! 🚀**
