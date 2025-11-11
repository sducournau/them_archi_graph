# Guide de Diagnostic des Paramètres Customizer

## 🎯 Objectif

Ce guide explique comment identifier et résoudre les problèmes de persistance des paramètres du Customizer, en particulier ceux du header qui ne semblent pas fonctionner correctement.

## 📋 Outils de Diagnostic Disponibles

### 1. **test-customizer-debug.php** - Diagnostic Complet
**URL:** `http://your-site.local/wp-content/themes/archi-graph-template/test-customizer-debug.php`

**Ce qu'il teste:**
- ✅ Tous les paramètres définis dans `inc/customizer.php`
- ✅ Vérification des types de données (bool, int, float, string, color)
- ✅ Valeurs en base de données vs valeurs récupérées
- ✅ Hooks WordPress enregistrés
- ✅ CSS généré par le Customizer

**Utilisation:**
1. Accédez au script dans votre navigateur
2. Consultez chaque section pour identifier les problèmes
3. Les problèmes sont mis en évidence en rouge/orange

### 2. **test-header-params.php** - Test Spécifique du Header
**URL:** `http://your-site.local/wp-content/themes/archi-graph-template/test-header-params.php`

**Ce qu'il teste:**
- ✅ Tous les paramètres du header uniquement
- ✅ Classes CSS appliquées au header HTML
- ✅ CSS généré pour le header
- ✅ Styles calculés par le navigateur (via JavaScript)

**Utilisation:**
1. Accédez au script
2. Ouvrez la console du navigateur (F12)
3. Vérifiez que les styles calculés correspondent aux valeurs du Customizer

### 3. **test-customizer-persistence.php** - Test de Persistance
**URL:** `http://your-site.local/wp-content/themes/archi-graph-template/test-customizer-persistence.php`

**Ce qu'il teste:**
- ✅ Si les valeurs sont sauvegardées en base de données
- ✅ Si les hooks sont correctement enregistrés
- ✅ Comparaison valeurs par défaut vs valeurs personnalisées

## 🔍 Procédure de Diagnostic

### Étape 1: Identifier les Paramètres Problématiques

1. Ouvrez le Customizer et modifiez quelques paramètres du header:
   ```
   - Changez la hauteur du header
   - Modifiez la couleur de fond
   - Activez/désactivez le header transparent
   - Changez la position du logo
   ```

2. Cliquez sur "Publier" pour sauvegarder

3. Accédez à `test-customizer-debug.php`

4. Vérifiez la section "Problèmes Détectés" - elle listera automatiquement:
   - Les paramètres avec des types incorrects
   - Les valeurs non trouvées en base de données
   - Les hooks manquants

### Étape 2: Vérifier la Persistance en Base de Données

Dans `test-customizer-debug.php`, regardez la colonne "En BD" du tableau:
- ✅ **Valeur présente** = Le paramètre est bien sauvegardé
- ❌ **null** = Le paramètre n'est PAS sauvegardé (PROBLÈME)

**Causes possibles si null:**
- Callback `sanitize_callback` incorrect
- Problème de permissions WordPress
- Valeur identique au défaut (WordPress ne stocke pas les valeurs par défaut)

### Étape 3: Vérifier les Types de Données

Les types de données doivent correspondre:

| Type Attendu | Valeurs Valides | Fonction Sanitize |
|-------------|-----------------|-------------------|
| `bool` | `true` ou `false` | `archi_sanitize_checkbox` |
| `int` | `16`, `60`, `100` | `absint` |
| `float` | `0.95`, `1.5` | `archi_sanitize_float` |
| `string` | `'normal'`, `'left'` | `sanitize_text_field` |
| `color` | `'#3498db'` | `sanitize_hex_color` |

**Problème courant:** Les booléens stockés comme strings `'1'` ou `'0'` au lieu de `true`/`false`

### Étape 4: Vérifier le CSS Généré

1. Dans `test-header-params.php`, comparez:
   - Les valeurs récupérées du Customizer
   - Le CSS généré théoriquement
   - Les styles calculés par le navigateur (console)

2. Si les valeurs du Customizer sont correctes MAIS le CSS ne s'applique pas:
   - Vérifiez la priorité du hook `wp_head`
   - Vérifiez qu'il n'y a pas de conflit CSS avec d'autres fichiers
   - Utilisez `!important` en dernier recours

## 🛠️ Solutions aux Problèmes Courants

### Problème 1: Valeurs Non Persistées

**Symptôme:** Après avoir modifié et publié, les valeurs reviennent au défaut.

**Solution:**
```php
// Dans inc/customizer.php, vérifiez la fonction sanitize
'archi_header_transparent' => [
    'default' => false,
    'transport' => 'refresh',
    'sanitize_callback' => 'archi_sanitize_checkbox' // ← Doit être correct
]

// La fonction de sanitization doit exister:
function archi_sanitize_checkbox($value) {
    return (bool) $value; // Convertir en vrai booléen
}
```

### Problème 2: Types Incorrects

**Symptôme:** Le diagnostic montre "Type incorrect: attendu bool, obtenu string"

**Solution:**
```php
// Mauvais - retourne string '1' ou '0'
function archi_sanitize_checkbox_bad($value) {
    return $value ? '1' : '0';
}

// Bon - retourne bool true ou false
function archi_sanitize_checkbox($value) {
    return (bool) $value;
}
```

### Problème 3: CSS Non Appliqué

**Symptôme:** Les valeurs sont correctes en base, mais le CSS ne change pas.

**Solutions possibles:**

1. **Vérifier la priorité du hook:**
```php
// Dans inc/customizer.php (ligne ~1034)
add_action('wp_head', 'archi_customizer_css', 999); // Priorité élevée
```

2. **Vérifier les sélecteurs CSS:**
```php
// Le sélecteur doit correspondre au HTML
.site-header {  // ← Doit matcher l'ID ou la classe réelle
    height: <?php echo esc_attr($header_height_value); ?>;
}
```

3. **Forcer le rechargement du CSS:**
- Videz le cache WordPress
- Videz le cache du navigateur (Ctrl+Shift+R)
- Vérifiez qu'il n'y a pas de cache de plugin actif

### Problème 4: Paramètres avec transport='postMessage' non mis à jour

**Symptôme:** Les changements ne sont visibles qu'après rechargement complet de la page.

**Solution:**
```javascript
// Vérifier que assets/js/customizer-preview.js existe et contient:
wp.customize('archi_header_height', function(value) {
    value.bind(function(newval) {
        // Mettre à jour le CSS en temps réel
        const heights = {
            'compact': '60px',
            'normal': '80px',
            'large': '100px',
            'extra-large': '120px'
        };
        $('.site-header').css('height', heights[newval]);
    });
});
```

## 🔄 Procédure de Test Complète

1. **Réinitialiser le Customizer:**
   ```php
   // Dans wp-admin, aller dans Apparence > Customizer
   // Modifier plusieurs paramètres
   // Cliquer sur "Publier"
   ```

2. **Test immédiat:**
   ```
   → Ouvrir test-customizer-debug.php
   → Vérifier qu'il n'y a pas d'erreurs dans "Problèmes Détectés"
   → Vérifier que les valeurs en BD correspondent aux valeurs actuelles
   ```

3. **Test visuel:**
   ```
   → Ouvrir test-header-params.php
   → Ouvrir la console navigateur (F12)
   → Vérifier que les styles calculés correspondent aux valeurs du Customizer
   ```

4. **Test en production:**
   ```
   → Visiter la page d'accueil
   → Inspecter le header (clic droit > Inspecter)
   → Vérifier les styles appliqués dans l'onglet "Styles" de DevTools
   ```

## 📊 Checklist de Vérification

- [ ] Tous les hooks sont enregistrés (test-customizer-debug.php)
- [ ] Les fonctions de sanitization existent et sont correctes
- [ ] Les types de données correspondent (bool, int, float, string)
- [ ] Les valeurs sont sauvegardées en base de données
- [ ] Le CSS est généré dans `<head>` avec priorité élevée
- [ ] Les sélecteurs CSS correspondent au HTML
- [ ] Pas de conflit avec d'autres CSS
- [ ] Le script customizer-preview.js est chargé pour postMessage

## 🚀 Prochaines Étapes

Si tous les tests passent mais que certains paramètres ne fonctionnent toujours pas:

1. Vérifiez le fichier `header.php` - les classes doivent être appliquées correctement
2. Vérifiez `assets/css/header.css` - pas de règles qui écrasent le Customizer CSS
3. Utilisez l'onglet "Network" de DevTools pour voir si le CSS est bien chargé
4. Vérifiez les erreurs JavaScript dans la console

## 📝 Rapport de Bug

Si un problème persiste, créez un rapport avec:
- URL du script de test (capture d'écran)
- Section "Problèmes Détectés" complète
- Console navigateur (erreurs JS)
- Onglet "Styles" de DevTools pour le header

---

**Dernière mise à jour:** <?php echo date('d/m/Y H:i:s'); ?>
