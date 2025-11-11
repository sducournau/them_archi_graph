# Image Fullscreen Personnalisée

## Vue d'ensemble

Cette fonctionnalité permet de définir une image différente de l'image à la une pour l'affichage en hero fullscreen à l'ouverture des articles, projets architecturaux et illustrations.

## Fonctionnement

### Interface Admin

Dans l'éditeur d'article/projet/illustration, une nouvelle option est disponible dans la meta-box **"Options de l'image à la une"** :

- **Image fullscreen personnalisée** : Permet de sélectionner une image depuis la bibliothèque multimédia
- Si une image est sélectionnée, elle sera utilisée pour l'affichage fullscreen à la place de l'image à la une
- Si aucune image n'est sélectionnée, l'image à la une sera utilisée (comportement par défaut)

### Utilisation

1. Ouvrir un article, projet ou illustration en édition
2. Scroller jusqu'à la meta-box **"Options de l'image à la une"**
3. Cliquer sur **"Choisir une image"**
4. Sélectionner l'image souhaitée depuis la bibliothèque multimédia
5. Cliquer sur **"Utiliser cette image"**
6. L'aperçu de l'image s'affiche
7. Sauvegarder l'article

Pour retirer l'image personnalisée :
- Cliquer sur **"Retirer l'image"**
- L'image à la une sera de nouveau utilisée

## Détails Techniques

### Métadonnées

- **Clé meta** : `_archi_custom_fullscreen_image`
- **Valeur** : ID de l'attachement (image) depuis la bibliothèque WordPress

### Fonctions Helper

Deux nouvelles fonctions ont été ajoutées dans `functions.php` :

#### `archi_get_fullscreen_image_url($post_id, $size)`

Récupère l'URL de l'image fullscreen (personnalisée ou featured).

**Paramètres :**
- `$post_id` (int, optionnel) : ID de l'article (par défaut : `get_the_ID()`)
- `$size` (string, optionnel) : Taille de l'image (par défaut : `'full'`)

**Retour :**
- `string|false` : URL de l'image ou `false` si aucune image

**Exemple :**
```php
$image_url = archi_get_fullscreen_image_url(get_the_ID(), 'full');
if ($image_url) {
    echo '<img src="' . esc_url($image_url) . '" alt="Hero">';
}
```

#### `archi_get_fullscreen_image_id($post_id)`

Récupère l'ID de l'image fullscreen (personnalisée ou featured).

**Paramètres :**
- `$post_id` (int, optionnel) : ID de l'article (par défaut : `get_the_ID()`)

**Retour :**
- `int|false` : ID de l'attachement ou `false` si aucune image

**Exemple :**
```php
$image_id = archi_get_fullscreen_image_id();
$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
```

### Templates Modifiés

Les templates suivants ont été mis à jour pour utiliser les nouvelles fonctions :

- `single.php` (ligne 28-34)
- `single-archi_project.php` (ligne 28-34)
- `single-archi_illustration.php` (ligne 28-34)

### Meta-box

La meta-box a été modifiée dans `inc/meta-boxes.php` :

- **Fonction callback** : `archi_featured_image_meta_box_callback` (lignes 691-865)
- **Fonction de sauvegarde** : `archi_save_meta_box_data` (lignes 920-935)

Le JavaScript inline utilise l'API WordPress Media pour la sélection d'images.

## Cas d'Usage

### Pourquoi utiliser une image personnalisée ?

1. **Recadrage différent** : L'image à la une peut être carrée/portrait pour les vignettes, mais pour le fullscreen on veut un format paysage panoramique
2. **Composition spécifique** : Besoin d'une composition différente avec plus d'espace pour le titre et les éléments superposés
3. **Qualité optimisée** : Image en haute résolution spécifique pour l'affichage grand écran
4. **Storytelling** : Image d'ambiance différente de l'image de couverture standard

### Exemple Pratique

Pour un projet architectural :
- **Image à la une** : Façade du bâtiment (format 4:3) pour les listings et cartes
- **Image fullscreen** : Vue panoramique du site (format 21:9) pour l'en-tête hero

## Compatibilité

Cette fonctionnalité est compatible avec :
- ✅ Tous les types de posts (post, archi_project, archi_illustration)
- ✅ Toutes les options d'affichage existantes (parallax, overlay, etc.)
- ✅ Mode fullscreen activé/désactivé
- ✅ Rétrocompatibilité : si aucune image personnalisée n'est définie, l'image à la une est utilisée

## Notes de Développement

- Le champ utilise `wp.media` pour la sélection d'images (API WordPress standard)
- La sauvegarde inclut la validation de l'ID d'attachement (`absint`)
- Si l'ID est 0 ou vide, la métadonnée est supprimée (`delete_post_meta`)
- Les fonctions helper vérifient d'abord l'image personnalisée, puis fallback sur l'image à la une
- Aucun impact sur les performances : pas de requêtes supplémentaires si l'image personnalisée n'est pas utilisée

## Changelog

### Version 1.0.5 (9 novembre 2025)
- ✨ Ajout de la fonctionnalité d'image fullscreen personnalisée
- ✨ Ajout des fonctions helper `archi_get_fullscreen_image_url()` et `archi_get_fullscreen_image_id()`
- 🔧 Modification des templates single pour utiliser les nouvelles fonctions
- 📝 Documentation de la fonctionnalité
