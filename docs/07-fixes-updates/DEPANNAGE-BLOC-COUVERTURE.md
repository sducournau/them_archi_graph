## Guide de Dépannage - Bloc "Couverture Image + Texte" Non Visible

### Problème
Le bloc "Couverture Image + Texte" n'apparaît pas dans l'éditeur Gutenberg.

---

## ✅ Solutions Rapides

### 1. Vider le Cache WordPress

**Via WP-CLI (Terminal) :**
```bash
cd /mnt/c/wamp64/www/wordpress
wp cache flush
```

**Via l'Admin WordPress :**
1. Allez dans **Réglages → Permaliens**
2. Cliquez sur **Enregistrer** (sans rien changer)
3. Cela force le rechargement des blocs

**Si vous utilisez un plugin de cache :**
- WP Super Cache : Supprimer le cache
- W3 Total Cache : Vider tous les caches
- WP Rocket : Vider le cache

### 2. Recharger Complètement l'Éditeur

1. Dans l'éditeur Gutenberg, appuyez sur **Ctrl+Shift+R** (Windows/Linux) ou **Cmd+Shift+R** (Mac)
2. Cela force un rechargement complet sans cache

### 3. Vérifier que le JavaScript est Chargé

**Ouvrir la Console du Navigateur :**
1. **F12** ou **Clic droit → Inspecter**
2. Onglet **Console**
3. Cherchez des erreurs en rouge

**Vérifier que le fichier est chargé :**
1. Onglet **Réseau** (Network)
2. Filtrer par **JS**
3. Cherchez `cover-block.bundle.js`
4. Doit être **200 OK** (vert)

### 4. Recompiler les Assets

```bash
cd /mnt/c/wamp64/www/wordpress/wp-content/themes/archi-graph-template
npm run build
```

---

## 🔍 Diagnostic Avancé

### Vérifier que le Bloc est Enregistré

**Ajouter ce code temporaire dans `functions.php` :**

```php
// DIAGNOSTIC - À retirer après test
add_action('admin_notices', function() {
    if (get_current_screen()->is_block_editor()) {
        $blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
        if (isset($blocks['archi-graph/cover-block'])) {
            echo '<div class="notice notice-success"><p>✅ Bloc cover-block enregistré !</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>❌ Bloc cover-block NON enregistré</p></div>';
        }
    }
});
```

### Vérifier le Chargement du CSS

**Dans le code source de la page (Ctrl+U) :**

Cherchez :
```html
<link rel="stylesheet" href=".../assets/css/cover-block.css" />
```

Si absent, le CSS n'est pas chargé.

### Vérifier le Chargement du JS

**Dans le code source de la page (Ctrl+U) :**

Cherchez :
```html
<script src=".../dist/js/cover-block.bundle.js"></script>
```

Si absent, le JS n'est pas chargé.

---

## 🛠️ Solutions Approfondies

### Problème : Bloc Enregistré mais Pas Visible

**Vérifier la catégorie :**

Le bloc doit apparaître dans la catégorie **"Archi Graph"**. Si la catégorie n'existe pas, vérifiez dans `functions.php` :

```php
function archi_register_block_category($categories) {
    return array_merge(
        $categories,
        [
            [
                'slug'  => 'archi-graph',
                'title' => __('Archi Graph', 'archi-graph'),
                'icon'  => 'admin-home',
            ],
        ]
    );
}
add_filter('block_categories_all', 'archi_register_block_category', 10, 1);
```

**Rechercher le bloc manuellement :**

Dans l'éditeur Gutenberg :
1. Cliquez sur **+** pour ajouter un bloc
2. Tapez dans la recherche : **"Couverture"**
3. Le bloc devrait apparaître même si la catégorie n'est pas visible

### Problème : Erreur JavaScript dans la Console

**Erreurs communes :**

1. **"React is not defined"**
   - Le bloc essaie d'utiliser React mais il n'est pas chargé
   - Solution : Vérifier les `externals` dans `webpack.config.js`

2. **"wp.blocks is undefined"**
   - WordPress blocks API non chargé
   - Solution : Ajouter `wp-blocks` dans les dépendances

3. **"Unexpected token <"**
   - Le fichier JS n'est pas compilé ou corrompu
   - Solution : Recompiler avec `npm run build`

### Problème : Bloc Apparaît mais Ne Fonctionne Pas

**Vérifier les attributs :**

Dans `inc/blocks/content/cover-block.php`, les attributs doivent correspondre exactement à ceux dans `cover-block.jsx`.

**Vérifier le rendu :**

Ajouter un `error_log()` dans la fonction de rendu :

```php
function archi_render_cover_block($attributes) {
    error_log('Cover block render called with: ' . print_r($attributes, true));
    // ... reste du code
}
```

Ensuite, consultez le fichier de log WordPress (`/wp-content/debug.log` si `WP_DEBUG_LOG` est activé).

---

## 📋 Checklist de Vérification

Cochez au fur et à mesure :

### Fichiers
- [ ] `inc/blocks/content/cover-block.php` existe (4.4K)
- [ ] `assets/js/blocks/cover-block.jsx` existe (7.4K)
- [ ] `dist/js/cover-block.bundle.js` existe (4.4K)
- [ ] `assets/css/cover-block.css` existe (6.3K)

### Configuration
- [ ] `webpack.config.js` contient `"cover-block": "./assets/js/blocks/cover-block.jsx"`
- [ ] `inc/blocks/_loader.php` enqueue `cover-block.bundle.js`
- [ ] `functions.php` enqueue `cover-block.css`
- [ ] `functions.php` enregistre la catégorie `archi-graph`

### Compilation
- [ ] `npm run build` exécuté sans erreurs
- [ ] Aucune erreur dans la console du navigateur

### WordPress
- [ ] Cache vidé (permaliens sauvegardés)
- [ ] Éditeur rechargé (Ctrl+Shift+R)
- [ ] Utilisateur a les droits d'édition

---

## 🎯 Test Final

### Étapes pour Tester le Bloc

1. **Créer/Éditer un article ou une page**
2. **Cliquer sur + pour ajouter un bloc**
3. **Chercher "Archi Graph" dans les catégories** OU **Taper "Couverture" dans la recherche**
4. **Cliquer sur "Couverture Image + Texte"**
5. **Sélectionner une image**
6. **Éditer le titre et sous-titre**
7. **Ajuster les paramètres dans la barre latérale droite** :
   - Opacité overlay
   - Couleur overlay
   - Hauteur minimale
   - Position du contenu
   - Effet parallax
8. **Prévisualiser**
9. **Publier/Mettre à jour**

### Rendu Attendu

```html
<div class="wp-block-cover archi-cover-block is-position-center-center" style="min-height: 400px;">
    <span class="wp-block-cover__background has-background-dim has-background-dim-50" style="background-color: #000000;"></span>
    <img class="wp-block-cover__image-background" src="..." />
    <div class="wp-block-cover__inner-container is-layout-flow wp-block-cover-is-layout-flow">
        <h2 class="wp-block-heading has-text-align-center cover-title">Votre Titre</h2>
        <p class="has-text-align-center cover-subtitle">Votre Sous-titre</p>
    </div>
</div>
```

---

## 💡 Astuce : Recherche de Bloc

Si la catégorie ne s'affiche pas, utilisez la **barre de recherche** :
- Tapez : `couverture`
- Tapez : `cover`
- Tapez : `image`
- Tapez : `archi`

Le bloc apparaîtra dans les résultats même si la catégorie n'est pas visible.

---

## 🆘 Si Rien Ne Fonctionne

### Option 1 : Réinstaller le Bloc

```bash
cd /mnt/c/wamp64/www/wordpress/wp-content/themes/archi-graph-template

# Supprimer le fichier compilé
rm dist/js/cover-block.bundle.js

# Recompiler
npm run build
```

### Option 2 : Vérifier les Permissions

```bash
# Donner les bonnes permissions (Linux/Mac)
chmod 644 dist/js/cover-block.bundle.js
chmod 644 assets/css/cover-block.css
chmod 644 inc/blocks/content/cover-block.php
```

### Option 3 : Désactiver/Réactiver le Thème

1. Admin WordPress → **Apparence → Thèmes**
2. Activer un autre thème (ex: Twenty Twenty-Four)
3. Réactiver **Archi Graph**
4. Cela force le rechargement de tous les hooks et blocs

### Option 4 : Mode Debug WordPress

Ajouter dans `wp-config.php` :

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
```

Puis consulter `/wp-content/debug.log` pour voir les erreurs.

---

## 📞 Support

Si le problème persiste après avoir essayé toutes ces solutions :

1. **Vérifier la version de WordPress** : Minimum 6.0+
2. **Vérifier la version de PHP** : Minimum 7.4+
3. **Désactiver tous les plugins** temporairement pour tester
4. **Tester avec un autre navigateur** (Chrome, Firefox, Safari)

---

## ✅ Solution Trouvée ?

Une fois le bloc fonctionnel, n'oubliez pas de :
- [ ] Retirer le code de diagnostic dans `functions.php`
- [ ] Désactiver le mode debug si activé
- [ ] Tester sur différents types de contenu (article, page, projet)
- [ ] Vérifier le responsive (mobile, tablette)

**Le bloc devrait maintenant être visible et fonctionnel !** 🎉
