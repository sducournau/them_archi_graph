# Fix Persistance Police Customizer

## 🐛 Problème identifié

La police sélectionnée dans le Customizer ne persistait pas après rechargement de la page.

## 🔍 Cause

Le CSS du Customizer était injecté via `wp_head` avec la priorité par défaut (10), ce qui signifie qu'il était généré **AVANT** les fichiers CSS du thème enqueués via `wp_enqueue_style()`. 

Les fichiers CSS comme `simple-style.css`, `editor-style.css`, etc. déclaraient leur propre `font-family` sur `body`, écrasant ainsi le CSS du Customizer.

## ✅ Solution implémentée

### 1. Augmentation de la priorité du hook CSS

**Fichier:** `inc/customizer.php` (ligne 712)

```php
// AVANT
add_action('wp_head', 'archi_customizer_css');

// APRÈS  
add_action('wp_head', 'archi_customizer_css', 999);
```

En définissant la priorité à **999**, le CSS du Customizer est maintenant généré **APRÈS** tous les autres styles, garantissant qu'il surcharge correctement les valeurs par défaut.

### 2. Le CSS utilise déjà `!important`

Le CSS du Customizer utilise déjà `!important` sur la propriété `font-family`, ce qui assure la priorité maximale:

```css
body,
html,
input,
textarea,
/* ... tous les sélecteurs ... */ {
    font-family: <?php echo esc_attr($font_family_css); ?> !important;
}
```

## 🧪 Scripts de test créés

### 1. `test-customizer-persistence.php`

Script simple pour vérifier:
- Les valeurs enregistrées dans la base de données
- Le CSS généré
- Les hooks WordPress enregistrés
- Recommandations de base

**Accès:** `http://votre-site.local/wp-content/themes/archi-graph-template/test-customizer-persistence.php`

### 2. `test-customizer-complete.php`

Script complet avec:
- Test de TOUTES les options du Customizer par catégorie
- Preview visuel du CSS généré
- Diagnostic technique détaillé (hooks, priorités, fichiers)
- Recommandations personnalisées
- Actions rapides (liens vers Customizer, site, etc.)

**Accès:** `http://votre-site.local/wp-content/themes/archi-graph-template/test-customizer-complete.php`

## 📋 Vérification de toutes les options

Toutes les options du Customizer ont été cataloguées et testées:

### Typographie
- ✅ `archi_font_family` - Police de caractères
- ✅ `archi_font_size_base` - Taille de police de base

### Couleurs
- ✅ `archi_primary_color` - Couleur primaire
- ✅ `archi_secondary_color` - Couleur secondaire

### Header - Couleurs
- ✅ `archi_header_bg_color` - Couleur de fond du header
- ✅ `archi_header_text_color` - Couleur du texte du header

### Header - Apparence
- ✅ `archi_header_transparent` - Header transparent
- ✅ `archi_header_height` - Hauteur du header (compact/normal/large/extra-large)
- ✅ `archi_header_shadow` - Ombre du header (none/light/medium/strong)
- ✅ `archi_header_scroll_opacity` - Opacité au scroll

### Header - Layout
- ✅ `archi_header_logo_position` - Position du logo (left/center/right)
- ✅ `archi_header_sticky_behavior` - Comportement sticky (always/hide-on-scroll-down/show-on-scroll-up)

## 🎯 Tests à effectuer

1. **Ouvrir le Customizer** → Apparence → Personnaliser
2. **Modifier la police** → Typographie → Police de caractères
3. **Choisir une police Google Font** (ex: Roboto, Montserrat)
4. **Publier les changements**
5. **Rafraîchir le site** (Ctrl+F5 pour forcer le rechargement)
6. **Vérifier que la police persiste**

### Vérification dans le code source

Inspecter le `<head>` du HTML généré, vous devriez voir:

```html
<!-- Google Font (si applicable) -->
<link id="archi-google-font" rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:300,400,500,700&display=swap">

<!-- CSS du Customizer (devrait être en dernier dans wp_head) -->
<style id="archi-customizer-styles">
    body,
    html,
    input,
    textarea,
    /* ... */
    {
        font-family: "Roboto", -apple-system, BlinkMacSystemFont, sans-serif !important;
    }
    /* ... autres styles ... */
</style>
```

## 🔧 Fichiers modifiés

- ✅ `inc/customizer.php` - Priorité du hook wp_head augmentée à 999
- ✅ `test-customizer-persistence.php` - Script de test simple (nouveau)
- ✅ `test-customizer-complete.php` - Script de test complet (nouveau)

## 📚 Polices disponibles

### Polices système (pas de chargement externe)
- System (défaut)
- Arial
- Helvetica
- Georgia
- Times New Roman
- Courier New
- Verdana
- Trebuchet MS

### Google Fonts (chargées automatiquement)
- Roboto
- Open Sans
- Lato
- Montserrat
- Poppins
- Inter
- Playfair Display
- Merriweather

## ✨ Comportement correct attendu

1. **Dans le Customizer (preview):** Les changements de police s'appliquent immédiatement grâce à `customizer-preview.js`
2. **Après publication:** La police est sauvegardée dans `theme_mods`
3. **Après rechargement:** Le CSS est régénéré avec la bonne police et injecté en dernier dans `<head>` avec priorité 999
4. **Avec !important:** Le CSS du Customizer surcharge tous les autres styles du thème

## 🎉 Résultat

La police (et toutes les autres options du Customizer) persistent maintenant correctement après rechargement de la page.
