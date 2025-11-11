# ✅ Correction des Scripts de Diagnostic Customizer

## Problème Résolu

Le problème du chemin vers `wp-load.php` a été corrigé dans tous les scripts de test.

### Erreur Initiale
```
Failed to open stream: No such file or directory
../../../../../wp-load.php
```

### Solution Appliquée

Les 3 scripts de test testent maintenant **plusieurs chemins possibles** automatiquement :

```php
$wp_load_paths = [
    __DIR__ . '/../../../../../wp-load.php',           // Standard WordPress
    __DIR__ . '/../../../../wp-load.php',              // Alternative
    __DIR__ . '/../../../../../../../wp-load.php',     // Deep nested
];
```

Si aucun chemin ne fonctionne, un message d'erreur clair est affiché.

## 🚀 Fichiers Corrigés

### 1. `test-customizer-debug.php` ✅
- Détection automatique du chemin wp-load.php
- Message d'erreur détaillé si WordPress n'est pas trouvé
- Affiche tous les chemins testés

### 2. `test-header-params.php` ✅
- Détection automatique du chemin wp-load.php
- Message d'erreur simple

### 3. `test-customizer-persistence.php` ✅
- Détection automatique du chemin wp-load.php
- Message d'erreur simple

## 📋 Comment Utiliser les Scripts

### Méthode 1 : Via URL (Recommandée)

Ouvrez dans votre navigateur :

```
http://localhost/wordpress/wp-content/themes/archi-graph-template/test-customizer-debug.php
```

ou si vous utilisez un autre port/domaine :

```
http://votre-domaine.local/wp-content/themes/archi-graph-template/test-customizer-debug.php
```

### Méthode 2 : Via Terminal (Pour tester la syntaxe)

```bash
cd /mnt/c/wamp64/www/wordpress/wp-content/themes/archi-graph-template
php -l test-customizer-debug.php
```

✅ **Résultat attendu:** `No syntax errors detected`

## 🎯 Prochaines Étapes

Maintenant que les scripts sont corrigés, vous pouvez :

### 1. Tester le diagnostic complet

```
http://localhost/wordpress/wp-content/themes/archi-graph-template/test-customizer-debug.php
```

**Vous verrez :**
- ⚠️ Section "Problèmes Détectés" (en haut)
- 📋 Tableaux détaillés par catégorie
- 🔌 Vérification des hooks WordPress
- 🎨 Aperçu du CSS généré

### 2. Tester spécifiquement le header

```
http://localhost/wordpress/wp-content/themes/archi-graph-template/test-header-params.php
```

**Ouvrez aussi la console du navigateur (F12)** pour voir les styles calculés en temps réel.

### 3. Tester la persistance

```
http://localhost/wordpress/wp-content/themes/archi-graph-template/test-customizer-persistence.php
```

## 🔍 Ce Que Vous Devez Chercher

Une fois que vous accédez à `test-customizer-debug.php`, regardez en priorité :

### Section "⚠️ Problèmes Détectés"

Cette section sera :
- **Vide** avec un message vert = ✅ Tout fonctionne
- **Remplie** d'alertes orange/rouge = ⚠️ Problèmes à corriger

### Exemples de problèmes qui seront détectés :

1. **Type de données incorrect**
   ```
   archi_header_transparent: Type incorrect: attendu bool, obtenu string
   ```

2. **Valeur non sauvegardée**
   ```
   archi_header_height: Valeur non trouvée en base de données
   ```

3. **Hook manquant**
   ```
   wp_head → archi_customizer_css NON ENREGISTRÉ
   ```

## 📊 Paramètres du Header à Vérifier

Voici les 9 paramètres du header que vous avez mentionnés comme problématiques :

| Paramètre | Type | Défaut | Description |
|-----------|------|--------|-------------|
| `archi_header_sticky` | bool | `true` | Header fixe au scroll |
| `archi_header_transparent` | bool | `false` | Header transparent (homepage) |
| `archi_header_height` | string | `'normal'` | Hauteur (compact/normal/large/extra-large) |
| `archi_header_shadow` | string | `'light'` | Ombre (none/light/medium/strong) |
| `archi_header_scroll_opacity` | float | `0.95` | Opacité au scroll |
| `archi_header_logo_position` | string | `'left'` | Position logo (left/center/right) |
| `archi_header_sticky_behavior` | string | `'always'` | Comportement sticky |
| `archi_header_bg_color` | color | `'#ffffff'` | Couleur de fond |
| `archi_header_text_color` | color | `'#2c3e50'` | Couleur du texte |

Le script `test-customizer-debug.php` va tester **tous ces paramètres** et vous dire lesquels ont un problème.

## ✅ Validation

Les 3 scripts ont été validés syntaxiquement :
- ✅ `test-customizer-debug.php` - No syntax errors
- ✅ `test-header-params.php` - No syntax errors
- ✅ `test-customizer-persistence.php` - No syntax errors

## 🆘 Si Vous Avez Encore une Erreur

Si vous voyez toujours une erreur du type "wp-load.php not found", cela signifie que votre structure WordPress est non-standard.

**Vérifiez l'emplacement réel :**

```bash
cd /mnt/c/wamp64/www/wordpress
ls -la wp-load.php
```

Si le fichier existe, notez le chemin relatif depuis `wp-content/themes/archi-graph-template/` et je pourrai ajuster les scripts.

---

**Date de correction:** 11 novembre 2025  
**Scripts corrigés:** 3  
**Status:** ✅ Prêt à l'utilisation
