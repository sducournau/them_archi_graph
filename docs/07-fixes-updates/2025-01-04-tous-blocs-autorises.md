# Autorisation de tous les blocs Gutenberg

**Date:** 4 janvier 2025  
**Type:** Amélioration / Feature  
**Fichiers modifiés:** `inc/block-templates.php`

## 📋 Résumé

Ajout de l'autorisation complète de tous les blocs WordPress core et personnalisés pour les articles, projets architecturaux et illustrations.

## 🎯 Objectif

Permettre aux utilisateurs d'utiliser l'ensemble des blocs Gutenberg disponibles dans WordPress, en plus des blocs personnalisés du thème, pour créer du contenu plus riche et varié.

## ✅ Modifications effectuées

### 1. Extension de la fonction `archi_allowed_block_types()`

**Avant:** Seuls quelques blocs essentiels étaient autorisés (paragraphe + blocs personnalisés spécifiques)

**Après:** Tous les blocs sont maintenant disponibles :

#### Blocs WordPress Core ajoutés

**Texte:**
- `core/paragraph` (Paragraphe)
- `core/heading` (Titre)
- `core/list` (Liste)
- `core/quote` (Citation)
- `core/code` (Code)
- `core/preformatted` (Préformaté)
- `core/pullquote` (Citation en exergue)
- `core/table` (Tableau)
- `core/verse` (Vers)

**Média:**
- `core/image` (Image)
- `core/gallery` (Galerie)
- `core/audio` (Audio)
- `core/video` (Vidéo)
- `core/file` (Fichier)
- `core/media-text` (Média & Texte)
- `core/cover` (Couverture)

**Design:**
- `core/button` (Bouton)
- `core/buttons` (Boutons)
- `core/columns` (Colonnes)
- `core/group` (Groupe)
- `core/row` (Ligne)
- `core/stack` (Pile)
- `core/separator` (Séparateur)
- `core/spacer` (Espacement)

**Widgets:**
- `core/shortcode` (Shortcode)
- `core/archives` (Archives)
- `core/calendar` (Calendrier)
- `core/categories` (Catégories)
- `core/html` (HTML personnalisé)
- `core/latest-comments` (Derniers commentaires)
- `core/latest-posts` (Derniers articles)
- `core/page-list` (Liste de pages)
- `core/rss` (Flux RSS)
- `core/search` (Recherche)
- `core/social-links` (Liens sociaux)
- `core/tag-cloud` (Nuage d'étiquettes)

**Thème:**
- `core/navigation` (Navigation)
- `core/query` (Requête)
- `core/post-title` (Titre de l'article)
- `core/post-content` (Contenu de l'article)
- `core/post-date` (Date de l'article)
- `core/post-excerpt` (Extrait de l'article)
- `core/post-featured-image` (Image mise en avant)
- `core/post-terms` (Termes de l'article)
- Et plus...

**Embed:**
- `core/embed` (Intégration générique)
- `core-embed/youtube` (YouTube)
- `core-embed/vimeo` (Vimeo)
- `core-embed/twitter` (Twitter)
- `core-embed/instagram` (Instagram)
- `core-embed/facebook` (Facebook)
- `core-embed/spotify` (Spotify)
- `core-embed/soundcloud` (SoundCloud)

#### Blocs personnalisés du thème

Tous les blocs Archi Graph restent disponibles :
- `archi-graph/interactive-graph` - Graphique interactif
- `archi-graph/project-showcase` - Vitrine de projets
- `archi-graph/illustration-grid` - Grille d'illustrations
- `archi-graph/category-filter` - Filtre par catégorie
- `archi-graph/featured-projects` - Projets en vedette
- `archi-graph/timeline` - Timeline
- `archi-graph/before-after` - Avant/Après
- `archi-graph/technical-specs` - Spécifications techniques
- `archi-graph/project-illustration-card` - Carte projet/illustration
- `archi-graph/article-info` - Informations article
- `archi-graph/article-manager` - Gestionnaire d'article
- `archi-graph/project-specs` - Fiche technique projet
- `archi-graph/illustration-specs` - Fiche technique illustration
- `archi-graph/article-specs` - Fiche identité article

### 2. Ajout du `template_lock` pour les articles

Ajout de `$post_type_object->template_lock = false;` pour les articles (posts), cohérent avec les projets et illustrations, permettant :
- L'ajout libre de nouveaux blocs
- La suppression de blocs existants
- La réorganisation des blocs

## 📊 Impact

### Types de posts concernés

- ✅ **Articles** (`post`) - Tous les blocs disponibles
- ✅ **Projets architecturaux** (`archi_project`) - Tous les blocs disponibles
- ✅ **Illustrations** (`archi_illustration`) - Tous les blocs disponibles
- ✅ **Pages** (`page`) - Comportement par défaut (tous les blocs)

### Avantages

1. **Flexibilité accrue** - Les utilisateurs peuvent créer du contenu plus riche et varié
2. **Expérience utilisateur améliorée** - Accès à tous les outils d'édition WordPress
3. **Compatibilité** - Support complet de l'écosystème Gutenberg
4. **Créativité** - Possibilité d'utiliser des layouts complexes avec colonnes, groupes, etc.
5. **Média enrichi** - Support complet pour images, galeries, vidéos, audio, etc.

### Template initial conservé

Malgré l'autorisation de tous les blocs, les templates initiaux sont conservés :
- Chaque type de post démarre avec ses blocs par défaut (article-manager, specs)
- L'utilisateur peut ensuite ajouter librement d'autres blocs

## 🔧 Configuration technique

```php
// Tous les blocs disponibles
$all_blocks = array_merge($core_blocks, $archi_blocks);

switch ($post_type) {
    case 'archi_project':
    case 'archi_illustration':
    case 'post':
        // Articles, projets et illustrations : tous les blocs disponibles
        return $all_blocks;
    
    case 'page':
        // Les pages ont accès à tous les blocs (comportement par défaut)
        return true;
    
    default:
        return $allowed_blocks;
}
```

## 📝 Notes

- Le filtre `allowed_block_types_all` est utilisé (compatible WordPress 5.8+)
- Les blocs sont listés explicitement pour une meilleure maintenabilité
- La liste peut être facilement étendue si de nouveaux blocs sont ajoutés
- Les pages conservent le comportement par défaut (`true`) pour une flexibilité maximale

## 🔗 Références

- Fichier modifié: `inc/block-templates.php`
- Fonction principale: `archi_allowed_block_types()`
- Filtre WordPress: `allowed_block_types_all`

## ✨ Utilisation

Les utilisateurs peuvent désormais :
1. Créer des layouts complexes avec colonnes et groupes
2. Ajouter des médias riches (galeries, vidéos, audio)
3. Intégrer du contenu externe (YouTube, Vimeo, etc.)
4. Utiliser tous les widgets WordPress
5. Personnaliser le design avec boutons, séparateurs, espacements
6. Structurer le contenu avec tableaux et listes avancées

Le template initial reste présent pour guider l'utilisateur, mais peut être complété librement.
