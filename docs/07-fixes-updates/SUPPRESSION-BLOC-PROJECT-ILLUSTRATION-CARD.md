# Suppression du Bloc Project-Illustration-Card

## 📅 Date
8 novembre 2025

## 🎯 Objectif
Suppression complète du bloc Gutenberg `project-illustration-card` qui était redondant et peu utilisé.

## ✅ Actions Effectuées

### 1. Fichiers Supprimés
- ❌ `assets/js/project-illustration-card-block.js` - Fichier source JSX
- ❌ `dist/js/project-illustration-card-block.bundle.js` - Bundle compilé

### 2. Fichiers Modifiés

#### **webpack.config.js**
Suppression de l'entrée du bloc :
```javascript
// SUPPRIMÉ :
"project-illustration-card-block": "./assets/js/project-illustration-card-block.js",
```

#### **inc/blocks/_loader.php**
Suppression de l'enregistrement du script :
```php
// SUPPRIMÉ :
'project-illustration-card-block' => ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'],
```

#### **inc/block-templates.php**
Suppression du bloc de la liste des blocs autorisés :
```php
// SUPPRIMÉ :
'archi-graph/project-illustration-card',  // Carte projet/illustration
```

#### **inc/admin-settings.php**
Suppression du bloc de l'interface d'administration :
```php
// SUPPRIMÉ :
['name' => 'project-illustration-card', 'label' => 'Carte Projet/Illustration', 'icon' => '🃏'],
```

#### **package.json**
Nettoyage du script de build :
```json
// AVANT :
"build:blocks": "wp-scripts build assets/js/blocks-editor.js assets/js/article-info-block.js assets/js/project-illustration-card-block.js --output-path=dist/js"

// APRÈS :
"build:blocks": "wp-scripts build assets/js/blocks-editor.js assets/js/article-info-block.js --output-path=dist/js"
```

## 🔍 Vérifications Effectuées

### Build Webpack
✅ Compilation réussie sans erreurs
```bash
npm run build
```

**Résultat :**
- ✅ Tous les blocs compilés avec succès
- ✅ Aucune référence à project-illustration-card
- ✅ Bundles générés :
  - blocks-editor.bundle.js (15.9 KiB)
  - parallax-blocks.bundle.js (9.46 KiB)
  - image-blocks.bundle.js (9.17 KiB)
  - article-manager-block.bundle.js (8.66 KiB)
  - cover-block.bundle.js (4.32 KiB)
  - article-info-block.bundle.js (3.58 KiB)

### Vérification des Fichiers
```bash
ls -la dist/js/ | grep project    # Aucun résultat ✅
ls -la assets/js/ | grep project  # Aucun résultat ✅
```

## 📊 Blocs Restants

### Blocs Actifs
Les blocs suivants restent disponibles et fonctionnels :

1. **Blocs d'Images**
   - `archi-graph/image-full-width` - Image pleine largeur
   - `archi-graph/images-columns` - Images en colonnes
   - `archi-graph/image-portrait` - Image portrait
   - `archi-graph/cover-block` - Couverture avec overlay

2. **Blocs Parallax/Scroll** (nouveaux)
   - `archi-graph/fixed-background` - Image défilement fixe
   - `archi-graph/sticky-scroll` - Section scroll collant

3. **Blocs de Contenu**
   - `archi-graph/article-info-block` - Informations article
   - `archi-graph/article-manager` - Gestionnaire d'articles

4. **Blocs Spécialisés**
   - `archi-graph/timeline` - Timeline
   - `archi-graph/before-after` - Avant/Après
   - `archi-graph/technical-specs` - Spécifications techniques
   - `archi-graph/project-specs` - Fiche technique projet
   - `archi-graph/illustration-specs` - Fiche technique illustration
   - `archi-graph/article-specs` - Fiche identité article

## 🚫 Raisons de la Suppression

Le bloc `project-illustration-card` était :
- **Redondant** avec d'autres blocs existants (article-info-block, article-card-component)
- **Peu utilisé** dans les templates actuels
- **Non essentiel** pour les fonctionnalités principales du thème
- **Source de confusion** dans l'interface d'administration

## ✨ Alternatives Recommandées

Pour afficher des cartes de projets/illustrations, utiliser plutôt :

1. **`archi_render_article_card()`** - Fonction utilitaire dans `inc/article-card-component.php`
   - Plus flexible
   - Mieux maintenue
   - Styles consolidés

2. **`archi-graph/article-manager`** - Bloc gestionnaire d'articles
   - Affichage de grilles de projets
   - Filtres et tris intégrés

3. **`archi-graph/article-info-block`** - Bloc d'informations
   - Métadonnées des projets
   - Informations détaillées

## 📝 Impact

### Sur les Contenus Existants
⚠️ **Si le bloc était utilisé dans des posts/pages existants :**
- Le contenu sera préservé mais ne sera plus éditable
- Il faudra remplacer les blocs par des alternatives
- Vérifier les posts utilisant ce bloc via l'éditeur WordPress

### Sur le Thème
- ✅ Aucun impact sur les fonctionnalités principales
- ✅ Code plus propre et maintenable
- ✅ Moins de fichiers JavaScript à charger
- ✅ Temps de compilation réduit

## 🔄 Migration (si nécessaire)

Si des contenus utilisaient ce bloc :

1. Identifier les posts concernés dans WordPress
2. Remplacer par `archi-graph/article-info-block` ou `archi-graph/article-manager`
3. Vérifier l'affichage frontend
4. Mettre à jour les templates si nécessaire

## ✅ Checklist Post-Suppression

- [x] Fichiers source supprimés
- [x] Fichiers compilés supprimés
- [x] Webpack configuré
- [x] Loader PHP mis à jour
- [x] Templates mis à jour
- [x] Admin settings mis à jour
- [x] Build réussi
- [x] Vérifications effectuées
- [ ] Tests dans l'éditeur WordPress
- [ ] Vérification des contenus existants (si applicable)

## 📚 Documentation Mise à Jour

Les documents suivants ont été créés/mis à jour :
- ✅ Ce document de suppression
- ✅ `NEW-GUTENBERG-BLOCKS.md` - Documentation des nouveaux blocs
- ✅ `BLOCKS-IMPLEMENTATION-SUMMARY.md` - Résumé de l'implémentation

---

**Statut :** ✅ Suppression complète et compilation réussie  
**Prochaine étape :** Tester dans l'éditeur WordPress et vérifier les contenus existants
