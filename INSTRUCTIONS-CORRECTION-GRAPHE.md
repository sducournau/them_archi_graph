# 🔧 Instructions pour corriger l'affichage du graphe

## Problème actuel
Les nodes du graphe se superposent et les polygones de catégories ne sont pas visibles.

## ✅ Corrections appliquées au code

### 1. Configuration PHP (`inc/graph-config.php`)
- Force de répulsion réduite : `-200` → **`-80`**
- Distance de collision augmentée : **`+15px padding`**
- Force de centrage augmentée : `0.05` → **`0.08`**
- Force de clustering augmentée : `0.1` → **`0.15`**

### 2. Code JavaScript (`assets/js/components/GraphContainer.jsx`)
- Polygones de catégories **réactivés** (fonction `updateClusters`)
- Logs de debug ajoutés pour diagnostiquer les valeurs chargées
- Valeurs par défaut optimisées

### 3. Compilation
- ✅ Fichiers JavaScript recompilés avec webpack

---

## 🚨 ÉTAPES OBLIGATOIRES POUR APPLIQUER LES CHANGEMENTS

### Étape 1 : Vider le cache PHP/WordPress

**Option A : Utiliser le script de nettoyage (recommandé)**

1. Ouvrir dans le navigateur : 
   ```
   http://localhost/wordpress/wp-content/themes/archi-graph-template/clear-graph-cache.php
   ```

2. Le script va :
   - Supprimer tous les transients du graphe
   - Vider le cache de configuration
   - Afficher les valeurs actuellement chargées
   
3. **⚠️ IMPORTANT : Supprimer ce fichier après utilisation** pour la sécurité

**Option B : Via WordPress Admin**

1. Aller dans **Réglages → WP Fastest Cache** (si installé)
2. Cliquer sur "Vider tout le cache"

**Option C : Via le terminal**
```bash
# Vider les transients WordPress
cd /mnt/c/wamp64/www/wordpress
wp transient delete --all
```

### Étape 2 : Rafraîchir le navigateur

1. Ouvrir la page du graphe
2. Forcer le rechargement : **`Ctrl + F5`** (Windows/Linux) ou **`Cmd + Shift + R`** (Mac)
3. Vider le cache du navigateur si nécessaire (Ctrl+Shift+Suppr)

### Étape 3 : Vérifier dans la console

1. Ouvrir les DevTools du navigateur (**F12**)
2. Aller dans l'onglet **Console**
3. Chercher le log : **`🎯 Graph Physics Settings`**
4. Vérifier que les valeurs sont :
   ```javascript
   {
     chargeStrength: -80,        // ← Doit être -80 (pas -200)
     chargeDistance: 300,        // ← Doit être 300
     collisionPadding: 15,       // ← Doit être 15
     centerStrength: 0.08,       // ← Doit être 0.08
     clusterStrength: 0.15,      // ← Doit être 0.15
     defaultNodeSize: 80,        // ← Doit être 80
     alphaValue: 0.3,            // ← Doit être 0.3
     velocityDecayValue: 0.4     // ← Doit être 0.4
   }
   ```

---

## 🔍 Diagnostic si le problème persiste

### Si les valeurs dans la console sont incorrectes

1. **Vérifier que `window.archiGraphSettings` est bien chargé** :
   ```javascript
   // Dans la console du navigateur
   console.log(window.archiGraphSettings);
   ```

2. **Vérifier le fichier qui charge les settings** :
   - Fichier : `inc/customizer.php` ligne ~982
   - Chercher : `wp_localize_script('archi-app', 'archiGraphSettings', ...)`

3. **Redémarrer le serveur PHP** :
   ```bash
   # Dans WAMP, redémarrer Apache
   # Ou si vous utilisez WP-CLI :
   wp cache flush
   ```

### Si les nodes se superposent toujours

1. **Réduire encore la force de répulsion** :
   - Modifier `inc/graph-config.php` ligne 66
   - Changer `'charge_strength' => -80` par **`-50`** ou même **`-30`**

2. **Augmenter la collision** :
   - Modifier `inc/graph-config.php` ligne 70
   - Changer `'collision_padding' => 15` par **`20`** ou **`25`**

### Si les polygones ne sont pas visibles

1. **Vérifier dans le code HTML** (F12 → Éléments) :
   - Chercher : `<g class="clusters">`
   - Vérifier que des éléments `<path class="cluster-hull">` existent
   - Vérifier l'attribut `fill-opacity` (doit être > 0)

2. **Vérifier le CSS** :
   - Fichier : `assets/css/organic-islands.css`
   - La classe `.cluster-hull` ne doit pas avoir `display: none`

---

## 🎨 Ajustements via le Customizer WordPress (futur)

Une fois le graphe fonctionnel, vous pourrez ajuster les valeurs en temps réel via :
- **Apparence → Personnaliser → Graphe**

Les paramètres disponibles :
- Force de répulsion (`chargeStrength`)
- Distance de collision (`collisionPadding`)
- Force de clustering (`clusterStrength`)
- Et bien d'autres...

---

## 📞 Si rien ne fonctionne

Partagez le résultat de ces commandes :

```javascript
// Dans la console du navigateur
console.log('Settings:', window.archiGraphSettings);
console.log('Nodes count:', d3.selectAll('.graph-node').size());
console.log('Clusters:', d3.selectAll('.category-cluster').size());
```

Et le résultat du script `clear-graph-cache.php` (section "Configuration actuelle du graphe").
