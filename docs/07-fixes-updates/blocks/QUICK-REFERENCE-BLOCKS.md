# Référence Rapide - Blocs Gutenberg v1.2.0

## 🎯 Ce qui a changé

**Version 1.2.0** - Tous les blocs WordPress Core sont maintenant autorisés !

- **Avant :** 3 blocs seulement (paragraphe + 2 blocs personnalisés)
- **Après :** 78+ blocs (60 WordPress Core + 18 Archi Graph)

## 📦 Types de posts concernés

| Type | Avant | Après | Status |
|------|-------|-------|--------|
| Articles (`post`) | 3 blocs | 78+ blocs | ✅ Activé |
| Projets (`archi_project`) | 3 blocs | 78+ blocs | ✅ Activé |
| Illustrations (`archi_illustration`) | 3 blocs | 78+ blocs | ✅ Activé |
| Pages (`page`) | Tous | Tous | ✅ Déjà actif |

## 🔧 Fichier modifié

**`inc/block-templates.php`**
- Fonction `archi_allowed_block_types()` étendue
- `template_lock = false` ajouté pour les articles

## 📚 Documentation

| Document | Description | Chemin |
|----------|-------------|--------|
| Guide utilisateur | Comment utiliser les blocs | `docs/02-features/guide-utilisation-blocs.md` |
| Documentation technique | Détails des modifications | `docs/07-fixes-updates/2025-01-04-tous-blocs-autorises.md` |
| Changelog | Historique des versions | `docs/changelog.md` |
| Résumé complet | Vue d'ensemble | `SUMMARY-BLOCKS-UPDATE-2025-01-04.md` |

## 🧪 Test rapide

```bash
# Exécuter le script de test
cd /chemin/vers/theme
./utilities/testing/test-blocks-authorization.sh

# Résultat attendu
✅ Tests terminés avec succès !
📊 60 blocs core + 18 blocs personnalisés = 78 blocs
```

## 🎨 Blocs les plus utiles (par catégorie)

**Texte :** paragraph, heading, list, quote

**Média :** image, gallery, video, media-text, cover

**Design :** columns, group, buttons, separator

**Embed :** youtube, vimeo, instagram, spotify

**Archi Graph :** article-manager, project-specs, timeline, before-after

## 💡 Cas d'usage communs

### Article de blog
`Couverture` → `Paragraphe` → `Image` → `Liste` → `Citation` → `Boutons`

### Projet architectural
`Article Manager` → `Project Specs` → `Couverture` → `Timeline` → `Galerie` → `Avant/Après`

### Illustration
`Article Manager` → `Illustration Specs` → `Image` → `Média & Texte` → `Galerie`

## 🚀 Démarrage rapide

1. Créer/Modifier un article, projet ou illustration
2. Cliquer sur le bouton **"+"** dans l'éditeur
3. Tous les blocs WordPress sont disponibles !
4. Les templates initiaux sont toujours présents

## ⚙️ Configuration technique

```php
// Dans inc/block-templates.php

// Tous les blocs disponibles
$all_blocks = array_merge($core_blocks, $archi_blocks);

// Application aux types de posts
case 'archi_project':
case 'archi_illustration':
case 'post':
    return $all_blocks; // 78+ blocs
```

## 📊 Statistiques

- **Blocs WordPress Core :** 60
- **Blocs Archi Graph :** 18
- **Total :** 78+
- **Amélioration :** +2500% (de 3 à 78+)

## ✅ Checklist de vérification

- [x] Fichier `inc/block-templates.php` modifié
- [x] Fonction `archi_allowed_block_types()` étendue
- [x] `template_lock = false` pour articles
- [x] Documentation créée (4 fichiers)
- [x] Changelog mis à jour
- [x] README mis à jour
- [x] Script de test créé et validé
- [x] Tests réussis (78+ blocs disponibles)

## 🔗 Liens rapides

- [Guide complet](docs/02-features/guide-utilisation-blocs.md)
- [Changelog](docs/changelog.md)
- [README](README.md)
- [Doc technique](docs/07-fixes-updates/2025-01-04-tous-blocs-autorises.md)

---

**Version :** 1.2.0  
**Date :** 4 janvier 2025  
**Statut :** ✅ Actif et testé
