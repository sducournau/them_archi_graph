# Suppression du Bloc Article-Info-Block

## 📅 Date
8 novembre 2025

## 🎯 Objectif
Suppression complète du bloc Gutenberg `article-info-block` pour simplifier l'architecture et utiliser uniquement le bloc `article-manager` plus complet.

## ✅ Actions Effectuées

### 1. Fichiers Supprimés
- ❌ `assets/js/article-info-block.js` - Fichier source JavaScript
- ❌ `assets/css/article-info-block.css` - Fichier CSS associé
- ❌ `dist/js/article-info-block.bundle.js` - Bundle compilé

### 2. Fichiers Modifiés

#### **webpack.config.js**
Suppression de l'entrée du bloc :
```javascript
// SUPPRIMÉ :
"article-info-block": "./assets/js/article-info-block.js",
```

#### **inc/blocks/_loader.php**
Suppression de l'enregistrement du script :
```php
// SUPPRIMÉ :
'article-info-block' => ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'],
```

#### **inc/block-templates.php**
Suppression du bloc de la liste des blocs autorisés :
```php
// SUPPRIMÉ :
'archi-graph/article-info',  // Informations article
```

#### **inc/admin-settings.php**
Suppression du bloc de l'interface d'administration :
```php
// SUPPRIMÉ :
['name' => 'article-info', 'label' => 'Info Article', 'icon' => '📄'],
```

#### **package.json**
Nettoyage du script de build :
```json
// AVANT :
"build:blocks": "wp-scripts build assets/js/blocks-editor.js assets/js/article-info-block.js --output-path=dist/js"

// APRÈS :
"build:blocks": "wp-scripts build assets/js/blocks-editor.js --output-path=dist/js"
```

## 🔍 Vérifications Effectuées

### Build Webpack
✅ Compilation réussie sans erreurs
```bash
npm run build
```

**Résultat :**
- ✅ Tous les blocs compilés avec succès
- ✅ Aucune référence à article-info-block
- ✅ Bundles générés :
  - blocks-editor.bundle.js (15.9 KiB)
  - parallax-blocks.bundle.js (9.46 KiB)
  - image-blocks.bundle.js (9.17 KiB)
  - article-manager-block.bundle.js (8.66 KiB) ✓ Conservé
  - cover-block.bundle.js (4.32 KiB)

### Vérification des Fichiers
```bash
ls -la dist/js/ | grep article
# Résultat : Seul article-manager-block.bundle.js reste (correct) ✅
```

## 📊 Blocs Restants

### Blocs Actifs (10 blocs)
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
   - `archi-graph/article-manager` - Gestionnaire d'articles ✓ **Recommandé**

4. **Blocs Spécialisés**
   - `archi-graph/timeline` - Timeline
   - `archi-graph/before-after` - Avant/Après
   - `archi-graph/technical-specs` - Spécifications techniques

## 🚫 Raisons de la Suppression

Le bloc `article-info-block` était :
- **Redondant** avec `article-manager` qui offre plus de fonctionnalités
- **Limité** dans ses capacités d'affichage
- **Moins flexible** que les alternatives disponibles
- **Source de confusion** avec d'autres blocs similaires

## ✨ Alternative Recommandée

### Utiliser `archi-graph/article-manager`
Le bloc **Article Manager** remplace complètement article-info-block avec :

**Avantages :**
- ✅ Affichage de grilles d'articles/projets/illustrations
- ✅ Filtres et tris intégrés
- ✅ Gestion des métadonnées complète
- ✅ Options de mise en page multiples
- ✅ Mieux maintenu et plus robuste
- ✅ Interface utilisateur plus intuitive

**Utilisation :**
1. Insérer le bloc "Gestionnaire Articles" dans l'éditeur
2. Configurer le type de contenu (articles, projets, illustrations)
3. Choisir les options d'affichage
4. Ajouter des filtres si nécessaire

## 📝 Impact

### Sur les Contenus Existants
⚠️ **Si le bloc article-info était utilisé dans des posts/pages :**
- Le contenu sera préservé mais ne sera plus éditable
- Remplacer par `archi-graph/article-manager`
- Vérifier les posts concernés dans l'éditeur WordPress

### Sur le Thème
- ✅ Architecture simplifiée
- ✅ Moins de fichiers à maintenir
- ✅ Code plus cohérent
- ✅ Moins de confusion pour les utilisateurs

## 🔄 Migration (si nécessaire)

Si des contenus utilisaient article-info-block :

1. **Identifier les posts concernés** dans WordPress
2. **Éditer chaque post/page** avec le bloc
3. **Supprimer le bloc article-info**
4. **Ajouter le bloc article-manager**
5. **Configurer les options** selon les besoins
6. **Publier** et vérifier l'affichage

## ✅ Checklist Post-Suppression

- [x] Fichiers JS/CSS supprimés
- [x] Fichiers compilés supprimés
- [x] Webpack configuré
- [x] Loader PHP mis à jour
- [x] Templates mis à jour
- [x] Admin settings mis à jour
- [x] Build réussi
- [x] Vérifications effectuées
- [ ] Tests dans l'éditeur WordPress
- [ ] Migration des contenus existants (si applicable)

## 📚 Documentation

Documents mis à jour :
- ✅ Ce document de suppression
- ✅ Documentation des blocs restants disponible
- ✅ Guide d'utilisation d'article-manager

## 📈 Résumé des Suppressions

Au total, depuis le début de la session :

1. ❌ **project-illustration-card-block** - Supprimé (redondant)
2. ❌ **article-info-block** - Supprimé (limité)

✅ **Nouveaux blocs ajoutés :**
1. ✅ **fixed-background** - Parallax fixe
2. ✅ **sticky-scroll** - Scroll collant

**Bilan net :** -2 blocs obsolètes + 2 blocs modernes = Architecture améliorée ! 🎉

---

**Statut :** ✅ Suppression complète et compilation réussie  
**Prochaine étape :** Tester dans l'éditeur WordPress et migrer les contenus si nécessaire  
**Bloc recommandé :** `archi-graph/article-manager` pour toutes les fonctionnalités d'articles
