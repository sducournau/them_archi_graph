# ⚠️ BLOC "COUVERTURE IMAGE + TEXTE" INVISIBLE

## Le bloc existe mais n'apparaît pas dans l'éditeur

---

## ✅ SOLUTION : Forcer le Rechargement

### Étape 1 : Vider le Cache du Navigateur
1. Dans l'éditeur WordPress, appuyez sur **Ctrl+Shift+Delete** (Windows/Linux) ou **Cmd+Shift+Delete** (Mac)
2. Cochez **"Images et fichiers en cache"**
3. Sélectionnez **"Dernière heure"**
4. Cliquez sur **"Effacer les données"**

### Étape 2 : Hard Refresh de l'Éditeur
1. Fermez complètement l'onglet de l'éditeur WordPress
2. Allez dans **Articles → Ajouter** (ou **Pages → Ajouter**)
3. Une fois l'éditeur chargé, appuyez sur **Ctrl+Shift+R** (Windows/Linux) ou **Cmd+Shift+R** (Mac)

### Étape 3 : Vider les Permaliens WordPress
1. Allez dans **Réglages → Permaliens**
2. Cliquez sur **"Enregistrer les modifications"** (sans rien changer)
3. Retournez dans l'éditeur et rechargez la page

---

## 🔍 VÉRIFICATION : Le Bloc Est-il Chargé ?

### Ouvrir la Console JavaScript
1. Dans l'éditeur, appuyez sur **F12**
2. Allez dans l'onglet **"Console"**
3. Cherchez des erreurs en rouge

### Vérifier le Chargement du JavaScript
1. Dans la console (F12), tapez :
```javascript
wp.blocks.getBlockTypes().filter(b => b.name.includes('cover'))
```
2. Appuyez sur **Entrée**
3. Vous devriez voir un objet avec `name: "archi-graph/cover-block"`

### Si le Bloc N'Apparaît Pas dans la Liste
Cela signifie que le JavaScript n'est pas chargé. Vérifiez :

1. **Onglet "Réseau" (Network) dans F12**
2. Filtrez par **"JS"**
3. Cherchez **"cover-block.bundle.js"**
4. Statut doit être **200 OK** (vert)
5. Si **404 Not Found** (rouge) → Le fichier n'est pas accessible

---

## 🛠️ SOLUTIONS AVANCÉES

### Solution 1 : Recompiler les Assets
```bash
cd /mnt/c/wamp64/www/wordpress/wp-content/themes/archi-graph-template
npm run build
```

Attendez que la compilation se termine, puis rechargez l'éditeur (**Ctrl+Shift+R**).

### Solution 2 : Vérifier les Permissions du Fichier
```bash
ls -lh /mnt/c/wamp64/www/wordpress/wp-content/themes/archi-graph-template/dist/js/cover-block.bundle.js
```

Le fichier doit être **lisible** (permissions 644 ou rwxrwxrwx).

### Solution 3 : Mode Debug WordPress
Ajouter dans `wp-config.php` (temporairement) :
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('SCRIPT_DEBUG', true);
```

Rechargez l'éditeur et consultez `/wp-content/debug.log` pour voir les erreurs.

### Solution 4 : Désactiver/Réactiver le Thème
1. **Apparence → Thèmes**
2. Activer **Twenty Twenty-Four** (ou un autre thème)
3. Réactiver **Archi Graph**
4. Cela force le rechargement de tous les hooks

---

## 🎯 TEST FINAL

### Chercher le Bloc Manuellement
1. Dans l'éditeur, cliquez sur **+** (Ajouter un bloc)
2. Tapez dans la recherche : **"couverture"**
3. Le bloc devrait apparaître avec le titre **"Couverture Image + Texte"**

### Si le Bloc Apparaît
1. Cliquez dessus pour l'insérer
2. Sélectionnez une image
3. Éditez le titre et le sous-titre
4. Ajustez les paramètres dans la barre latérale :
   - Opacité overlay (0-100%)
   - Couleur overlay
   - Hauteur minimale (200-800px)
   - Position du contenu (Haut/Centre/Bas)
   - Effet parallax (on/off)

### Résultat Attendu
Le bloc doit s'insérer et afficher :
- L'image de fond en plein écran
- Un overlay coloré avec opacité réglable
- Le titre et sous-titre centrés
- Tous les paramètres fonctionnels

---

## 📊 DIAGNOSTIC TECHNIQUE

### Fichiers à Vérifier

```bash
# Vérifier que tous les fichiers existent
ls -lh /mnt/c/wamp64/www/wordpress/wp-content/themes/archi-graph-template/inc/blocks/content/cover-block.php
ls -lh /mnt/c/wamp64/www/wordpress/wp-content/themes/archi-graph-template/assets/js/blocks/cover-block.jsx
ls -lh /mnt/c/wamp64/www/wordpress/wp-content/themes/archi-graph-template/dist/js/cover-block.bundle.js
ls -lh /mnt/c/wamp64/www/wordpress/wp-content/themes/archi-graph-template/assets/css/cover-block.css
```

Tous les fichiers doivent exister :
- `cover-block.php` : ~4.4K
- `cover-block.jsx` : ~7.4K
- `cover-block.bundle.js` : ~4.4K
- `cover-block.css` : ~6.3K

### Vérifier le Contenu du Bundle
```bash
head -5 /mnt/c/wamp64/www/wordpress/wp-content/themes/archi-graph-template/dist/js/cover-block.bundle.js
```

Doit commencer par : `(()=>{"use strict";const e=wp.blocks...`

### Vérifier l'Enregistrement dans WordPress
Dans la console WordPress (F12), tapez :
```javascript
wp.data.select('core/blocks').getBlockTypes().filter(b => b.name === 'archi-graph/cover-block')
```

Doit retourner un tableau avec 1 élément contenant :
```javascript
{
  name: "archi-graph/cover-block",
  title: "Couverture Image + Texte",
  category: "archi-graph",
  ...
}
```

Si le tableau est vide `[]`, le bloc n'est **pas enregistré**.

---

## 🚨 SI RIEN NE FONCTIONNE

### Vérifier la Version de WordPress
Le bloc nécessite WordPress **6.0+** pour fonctionner correctement.

### Vérifier les Conflits de Plugins
1. Désactivez **tous les plugins** temporairement
2. Rechargez l'éditeur
3. Cherchez le bloc "Couverture Image + Texte"
4. Si le bloc apparaît → **conflit de plugin**
5. Réactivez les plugins un par un pour identifier le coupable

### Plugins Connus pour Causer des Conflits
- **Gutenberg** (plugin) - Si installé, peut causer des conflits avec les blocs custom
- **Classic Editor** - Désactive Gutenberg complètement
- **Disable Gutenberg** - Désactive Gutenberg
- **WP Rocket** (cache agressif) - Vider le cache
- **Autoptimize** - Peut minifier/casser le JavaScript

### Tester avec un Autre Navigateur
- Testez avec **Chrome**, **Firefox**, **Edge**, **Safari**
- Si le bloc apparaît dans un navigateur mais pas l'autre → **problème de cache navigateur**

---

## ✅ RÉSUMÉ DES ACTIONS

1. ✅ Vider le cache du navigateur (Ctrl+Shift+Delete)
2. ✅ Hard refresh de l'éditeur (Ctrl+Shift+R)
3. ✅ Vider les permaliens WordPress (Réglages → Permaliens → Enregistrer)
4. ✅ Recompiler les assets (`npm run build`)
5. ✅ Vérifier la console JavaScript (F12) pour les erreurs
6. ✅ Chercher "couverture" dans la barre de recherche de blocs
7. ✅ Désactiver temporairement les plugins pour tester
8. ✅ Tester avec un autre navigateur

**Le bloc devrait maintenant être visible !** 🎉

---

## 📞 AIDE SUPPLÉMENTAIRE

Si après toutes ces étapes le bloc n'apparaît toujours pas :

1. Envoyez une capture d'écran de la **console JavaScript** (F12)
2. Envoyez le résultat de la commande :
```bash
ls -lh /mnt/c/wamp64/www/wordpress/wp-content/themes/archi-graph-template/dist/js/*.bundle.js
```
3. Envoyez le résultat de la commande dans la console JavaScript :
```javascript
wp.blocks.getBlockTypes().map(b => b.name)
```

Cela permettra d'identifier précisément le problème.
