# Mise à Jour : Éditeur Gutenberg avec Mise en Page Centrée

## 📝 Résumé des Changements

Ce commit synchronise l'éditeur Gutenberg avec la mise en page centrée du frontend pour offrir une expérience WYSIWYG (What You See Is What You Get) cohérente lors de l'édition d'articles, de projets et d'illustrations.

## ✨ Objectif

Les utilisateurs voient maintenant exactement la même mise en page dans l'éditeur Gutenberg que sur le frontend :
- Contenu centré avec largeur maximale de 800px
- Images par défaut centrées
- Typographie identique (18px, line-height 1.8)
- Titres H2 et H3 avec les mêmes tailles et espacements

## 📁 Fichiers Modifiés

### 1. `functions.php`

#### Ajout des styles éditeur dans `archi_theme_setup()`

```php
// Charger les styles pour l'éditeur Gutenberg
add_editor_style('assets/css/editor-style.css');
add_editor_style('assets/css/centered-content.css');
```

**Pourquoi ?** Ces deux fichiers CSS sont maintenant chargés dans l'éditeur Gutenberg via `add_editor_style()`, ce qui permet d'appliquer les mêmes styles que le frontend.

#### Nouvelle fonction `archi_enqueue_block_editor_assets()`

```php
/**
 * Charger les styles pour l'éditeur de blocs Gutenberg
 */
function archi_enqueue_block_editor_assets() {
    // Styles pour l'éditeur de blocs (preview des blocs)
    wp_enqueue_style(
        'archi-blocks-editor',
        ARCHI_THEME_URI . '/assets/css/blocks-editor.css',
        [],
        ARCHI_THEME_VERSION
    );
}
add_action('enqueue_block_editor_assets', 'archi_enqueue_block_editor_assets');
```

**Pourquoi ?** Cette fonction charge `blocks-editor.css` spécifiquement pour l'interface de l'éditeur de blocs, permettant de styliser les previews des blocs personnalisés.

### 2. `assets/css/editor-style.css`

#### Ajout de la section "Contenu Centré dans l'Éditeur"

```css
/* ====================
   CONTENU CENTRÉ DANS L'ÉDITEUR
   Aligné avec centered-content.css du frontend
   ==================== */

/* Conteneur de l'éditeur avec largeur centrée comme sur le frontend */
.editor-styles-wrapper .block-editor-block-list__layout {
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
    padding-left: 20px;
    padding-right: 20px;
}

/* Typographie du contenu alignée avec frontend */
.editor-styles-wrapper {
    font-size: 18px;
    line-height: 1.8;
    color: #333;
}

.editor-styles-wrapper p {
    margin-bottom: 1.5em;
    text-align: justify;
}
```

**Pourquoi ?**
- `.block-editor-block-list__layout` : Cible le conteneur principal des blocs dans l'éditeur
- `max-width: 800px` : Applique la même largeur centrée que le frontend
- Font-size et line-height : Identiques à `centered-content.css` pour cohérence visuelle

#### Mise à jour de la typographie des titres

```css
.editor-styles-wrapper h2 { 
    font-size: 28px;
    font-weight: 600;
    margin-top: 2em;
    margin-bottom: 1em;
    color: #222;
}
.editor-styles-wrapper h3 { 
    font-size: 22px;
    font-weight: 600;
    margin-top: 1.5em;
    margin-bottom: 0.75em;
    color: #333;
}
```

**Pourquoi ?** Ces styles correspondent exactement à ceux définis dans `centered-content.css` pour les titres H2 et H3.

#### Ajout du style des images par défaut

```css
/* Images par défaut dans l'éditeur (centrées comme le frontend) */
.editor-styles-wrapper img:not(.archi-full-width):not(.archi-column-image) {
    max-width: 100%;
    height: auto;
    display: block;
    margin: 2em auto;
}
```

**Pourquoi ?** Les images standard (non pleine largeur) sont centrées et espacées comme sur le frontend.

### 3. `assets/css/blocks-editor.css`

#### Ajout de la section "Contenu centré dans l'éditeur"

```css
/* ==========================================================================
   Contenu centré dans l'éditeur - Aligné avec centered-content.css
   ========================================================================== */

/* Conteneur principal de l'éditeur avec largeur centrée */
.editor-styles-wrapper .wp-block {
    max-width: 800px;
}

/* Les blocs pleine largeur doivent ignorer la contrainte */
.editor-styles-wrapper .wp-block[data-align="full"],
.editor-styles-wrapper .wp-block.alignfull,
.editor-styles-wrapper .archi-image-full-width,
.editor-styles-wrapper .archi-images-columns-2,
.editor-styles-wrapper .archi-images-columns-3 {
    max-width: none;
}
```

**Pourquoi ?**
- Tous les blocs ont une largeur max de 800px par défaut
- Les blocs explicitement pleine largeur (`alignfull`, `archi-image-full-width`, etc.) ignorent cette contrainte et s'étendent sur toute la largeur disponible
- Cela reproduit exactement le comportement du frontend

## 🎨 Résultat Visuel

### Avant
- Contenu éditeur utilisant toute la largeur disponible
- Typographie différente entre éditeur et frontend
- Images non centrées dans l'éditeur

### Après
- ✅ Contenu centré à 800px comme sur le frontend
- ✅ Typographie identique (18px, line-height 1.8)
- ✅ Images centrées par défaut
- ✅ Titres H2 (28px) et H3 (22px) avec espacements corrects
- ✅ Blocs pleine largeur fonctionnent correctement

## 📱 Compatibilité

- **WordPress** : 5.0+ (Gutenberg natif)
- **Navigateurs** : Tous les navigateurs modernes
- **Types de contenu** : Articles (`post`), Projets (`archi_project`), Illustrations (`archi_illustration`)

## 🔧 Fonctionnement Technique

### Chaîne de Chargement des Styles

1. **`add_editor_style()`** dans `functions.php`
   - Charge `editor-style.css` et `centered-content.css`
   - Appliqué automatiquement par WordPress à l'iframe de l'éditeur
   - Styles préfixés automatiquement avec `.editor-styles-wrapper`

2. **`enqueue_block_editor_assets`** hook
   - Charge `blocks-editor.css` dans l'interface de l'éditeur
   - Permet de styliser les previews des blocs personnalisés
   - S'applique à l'interface globale de l'éditeur (pas seulement l'iframe)

### Sélecteurs CSS Utilisés

- `.editor-styles-wrapper` : Conteneur de l'iframe éditeur (WordPress natif)
- `.block-editor-block-list__layout` : Liste des blocs dans l'éditeur
- `.wp-block` : Chaque bloc individuel
- `.wp-block[data-align="full"]` : Blocs avec alignement pleine largeur
- `.alignfull` : Classe WordPress standard pour pleine largeur

## ✅ Tests Effectués

- [x] Vérification de la largeur centrée dans l'éditeur
- [x] Test des blocs pleine largeur (images, colonnes)
- [x] Test de la typographie (paragraphes, titres)
- [x] Test des images standard (centrées automatiquement)
- [x] Test sur articles, projets et illustrations
- [x] Vérification de la cohérence éditeur ↔ frontend

## 🎯 Avantages

1. **Expérience WYSIWYG** : Ce que vous voyez dans l'éditeur = ce que vous voyez sur le site
2. **Productivité** : Plus besoin de prévisualiser constamment pour vérifier la mise en page
3. **Confort d'édition** : Largeur centrée améliore la lisibilité pendant l'écriture
4. **Cohérence** : Mêmes espacements, mêmes tailles, mêmes marges

## 📝 Notes pour les Développeurs

### Ajouter un Nouveau Bloc avec Largeur Centrée

Si vous créez un nouveau bloc qui doit respecter la largeur centrée :

```jsx
// Le bloc utilisera automatiquement max-width: 800px
```

### Créer un Bloc Pleine Largeur

Si vous créez un nouveau bloc qui doit ignorer la largeur centrée :

```jsx
// Ajouter la classe ou l'attribut d'alignement
<div className="archi-custom-fullwidth">
  {/* Contenu pleine largeur */}
</div>
```

Puis dans `blocks-editor.css` :

```css
.editor-styles-wrapper .archi-custom-fullwidth {
    max-width: none;
}
```

## 🔗 Fichiers Liés

- `centered-content.css` : Styles frontend pour contenu centré
- `CENTERED-CONTENT-UPDATE.md` : Documentation de la mise en page centrée frontend
- `docs/02-features/blocs-images-centrees.md` : Documentation des blocs images

## 🚀 Prochaines Étapes

Les utilisateurs peuvent maintenant :

1. Éditer du contenu avec la même largeur que le rendu final
2. Ajouter des images qui seront automatiquement centrées
3. Utiliser les blocs pleine largeur pour varier la mise en page
4. Avoir une vraie expérience WYSIWYG

## ⚡ Impact sur les Performances

- **Aucun** : Les styles CSS sont légers et chargés uniquement dans l'éditeur
- Les styles n'affectent pas le frontend (déjà en place via `centered-content.css`)
- Pas de JavaScript supplémentaire requis

## 🐛 Dépannage

### Le contenu n'est pas centré dans l'éditeur

1. Vérifier que `add_editor_style()` est bien appelé dans `functions.php`
2. Vider le cache du navigateur (Ctrl+F5)
3. Recharger l'éditeur Gutenberg

### Les blocs pleine largeur ne fonctionnent pas

1. Vérifier que la classe ou l'attribut d'alignement est correct
2. S'assurer que le bloc est listé dans les exceptions de `blocks-editor.css`
3. Inspecter l'élément pour voir si `max-width: none` est appliqué

### Différences entre éditeur et frontend

1. Comparer les valeurs CSS dans `editor-style.css` et `centered-content.css`
2. S'assurer que les deux fichiers ont les mêmes `max-width`, `font-size`, `line-height`
3. Tester avec l'inspecteur de navigateur pour identifier les différences

---

**Date** : 8 novembre 2025  
**Version** : 1.0.0  
**Auteur** : Équipe Archi-Graph  
**Statut** : ✅ Production Ready
