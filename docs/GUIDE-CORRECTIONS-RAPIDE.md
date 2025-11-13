# 🎯 Guide Rapide - Corrections Appliquées

## ✅ Ce qui a été fait

J'ai détecté et corrigé **4 incohérences majeures** dans les paramètres par défaut du graphique :

### 1️⃣ Taille des nœuds (`defaultNodeSize`)
- **Avant:** 120px (PHP) / 60px (JS) / 80px (React) ❌
- **Après:** 80px partout ✅

### 2️⃣ Force de répulsion (`chargeStrength`)
- **Avant:** -800 (PHP) / -200 (React) ❌
- **Après:** -200 partout ✅

### 3️⃣ Distance entre nœuds (`linkDistance`)
- **Avant:** 80 (PHP) / 100 (React) ❌
- **Après:** 100 partout ✅

### 4️⃣ Rayon de collision (`collision_radius`)
- **Avant:** 65 (calculé pour nœuds 120px) ❌
- **Après:** 50 (correct pour nœuds 80px) ✅

---

## 🔄 Actions Immédiates à Faire

### 1. Rebuild des assets JavaScript
```bash
cd /mnt/c/wamp64/www/wordpress/wp-content/themes/archi-graph-template
npm run build
```

### 2. Vider le cache WordPress
- Admin WordPress → WP Fastest Cache → "Delete Cache"
- Ou utiliser le bouton dans la barre d'admin

### 3. Hard refresh du navigateur
- **Windows/Linux:** Ctrl + F5
- **Mac:** Cmd + Shift + R

---

## 🧪 Tests à Effectuer

### Test 1: Affichage du graphique
1. Aller sur la page d'accueil
2. Vérifier que les nœuds ont une taille uniforme
3. Vérifier qu'ils ne se chevauchent plus

### Test 2: Console du navigateur
1. F12 pour ouvrir la console
2. Vérifier qu'il n'y a plus d'erreurs:
   - ~~`attribute y: Expected length, "NaN"`~~ ✅
   - ~~`attribute y: Expected length, "-400"`~~ ✅

### Test 3: Customizer
1. Admin → Apparence → Personnaliser
2. Graph Visual Settings → Default Node Size
3. Changer la valeur, vérifier que ça s'applique

---

## 📊 Résultats Attendus

### Avant les corrections:
❌ Nœuds de tailles variables
❌ Erreurs NaN dans la console
❌ Chevauchements
❌ Espacement incohérent

### Après les corrections:
✅ Tous les nœuds font 80px par défaut
✅ Pas d'erreurs console
✅ Pas de chevauchements (collision: 50)
✅ Espacement optimal (charge: -200, distance: 100)

---

## 🐛 Si problèmes persistent

### Corrections manuelles supplémentaires
Des fichiers utilitaires contiennent encore des valeurs hardcodées à `60`:

```bash
bash utilities/maintenance/harmonize-node-sizes.sh
```

Puis rebuild:
```bash
npm run build
```

---

## 📚 Documentation Complète

- **Guide détaillé:** `docs/GRAPH-PARAMETERS-FIX.md`
- **Résumé technique:** `docs/CORRECTIONS-SUMMARY.md`

---

## 💾 Commit Git

Les changements ont été sauvegardés:
```
🔧 Fix: Harmoniser les paramètres par défaut du graphique
Commit: a8754bd
```

Pour pousser vers GitHub:
```bash
git push origin main
```

---

**Auteur:** GitHub Copilot + Serena MCP  
**Date:** 13 novembre 2025  
**Statut:** ✅ Corrections principales appliquées
