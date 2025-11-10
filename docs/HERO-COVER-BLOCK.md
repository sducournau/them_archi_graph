# Bloc Hero Cover - Guide d'utilisation

## Vue d'ensemble

Le bloc **Hero Cover** (`archi-graph/hero-cover`) est un bloc de couverture pleine page spécialement conçu pour les en-têtes d'articles, de projets et d'illustrations. Il offre un rendu professionnel avec image de fond, overlay personnalisable, titre, extrait, métadonnées et effet parallax optionnel.

## Fonctionnalités principales

### Image de fond
- **Image à la une automatique** : Utilise automatiquement l'image featured du post
- **Image personnalisée** : Possibilité de sélectionner une image différente depuis la médiathèque
- **Chargement optimisé** : `loading="eager"` pour affichage immédiat de l'en-tête

### Dimensions flexibles
- **Plein écran (100vh)** : Hauteur viewport complète (défaut) - idéal pour les landing pages
- **Hauteur personnalisée** : De 400px à 1200px par tranches de 50px - pour un contrôle précis

### Overlay personnalisable
- **Couleur** : Sélecteur de couleur intégré (défaut: noir #000000)
- **Opacité** : Réglage de 0% à 100% par tranches de 5% (défaut: 40%)
- **Améliore la lisibilité** : Contraste optimal pour le texte sur n'importe quelle image

### Contenu dynamique
Le bloc affiche automatiquement les informations du post actuel :
- **Catégories** : Badges avec background translucide et backdrop-filter
- **Titre du post** : Typographie imposante (4rem → responsive)
- **Extrait** : Description du post avec limite de 800px de largeur
- **Métadonnées** : Auteur et date avec icônes SVG
- **Indicateur de scroll** : Animation de souris avec effet bounce

Chaque élément peut être activé/désactivé individuellement dans les paramètres.

### Positionnement et alignement
- **Position verticale** : Haut / Centre / Bas
- **Alignement du texte** : Gauche / Centre / Droite
- **Couleur du texte** : Sélecteur de couleur (défaut: blanc #ffffff)
- **Adaptation responsive** : Ajustements automatiques pour tablettes et mobiles

### Effet parallax
- **Activation optionnelle** : On/Off via toggle
- **Vitesse réglable** : De 0.1 à 1.0 (défaut: 0.5)
- **Performance optimisée** : Utilise `requestAnimationFrame` et `will-change`
- **Respect de l'accessibilité** : Désactivé automatiquement si `prefers-reduced-motion`

## Utilisation recommandée

### Pour les articles (posts)
```
1. Créer/éditer un article
2. Ajouter le bloc "Hero Cover" en première position
3. Laisser "Utiliser l'image à la une" activé
4. Configurer l'overlay (couleur noire, opacité 40-50%)
5. Activer : Catégories, Titre, Extrait, Métadonnées
6. Position : Centre, Alignement : Centre
7. Activer le parallax pour un effet dynamique
```

**Résultat** : Une introduction immersive et professionnelle qui capte l'attention du lecteur.

### Pour les projets architecturaux (archi_project)
```
1. Ouvrir le projet dans l'éditeur
2. Insérer le bloc Hero Cover
3. Configurer en hauteur personnalisée (800-1000px) si nécessaire
4. Overlay plus foncé (60-70%) pour mettre en valeur le texte
5. Position : Bas, Alignement : Gauche (style éditorial)
6. Désactiver l'extrait si les specs sont affichées après
```

**Résultat** : Une présentation élégante du projet avec focus sur l'image architecturale.

### Pour les illustrations (archi_illustration)
```
1. Éditer l'illustration
2. Ajouter Hero Cover
3. Utiliser l'image à la une (dessin/rendu)
4. Overlay léger (20-30%) pour préserver les couleurs
5. Position : Centre ou Haut
6. Activer uniquement Titre et Catégories (minimaliste)
7. Désactiver l'indicateur de scroll si le contenu est court
```

**Résultat** : Mise en valeur maximale de l'illustration avec interface épurée.

## Paramètres détaillés

### Panel "Image"
- **Utiliser l'image à la une** : Récupère automatiquement le featured image
  - ✅ Recommandé : Cohérence avec le système WordPress
- **Sélectionner une image** : Choix manuel depuis la médiathèque
  - Cas d'usage : Image différente pour le hero vs thumbnail

### Panel "Dimensions"
- **Hauteur**
  - `Plein écran (100vh)` : Occupe toute la hauteur visible
    - Mobile : Utilise `100svh` (safe viewport height)
  - `Personnalisée` : Slider de 400px à 1200px
    - Fallback : `min-height` pour sécurité

### Panel "Overlay"
- **Couleur de l'overlay** : Color picker RGB
  - Noir (#000000) : Classique et élégant
  - Bleu foncé (#001f3f) : Atmosphère corporative
  - Bordeaux (#4a1a1a) : Chaleureux et artistique
- **Opacité (%)** : 0-100 par pas de 5
  - 0-20% : Très transparent, image dominante
  - 30-50% : Équilibré (recommandé)
  - 60-80% : Focus sur le texte
  - 90-100% : Image en arrière-plan subtil

### Panel "Contenu à afficher"
Tous les toggles sont indépendants :
- **Afficher les catégories** : Badges arrondis en haut
- **Afficher le titre** : H1 du post avec text-shadow
- **Afficher l'extrait** : Description 1.25rem
- **Afficher les métadonnées** : Ligne auteur + date avec icônes
- **Afficher l'indicateur de défilement** : Animation souris en bas

### Panel "Position et alignement"
- **Position verticale**
  - `Haut` : `padding-top: 120px` → Navbar espace
  - `Centre` : `align-items: center` → Équilibré
  - `Bas` : `padding-bottom: 120px` → Style magazine
- **Alignement du texte** : `text-align: left|center|right`
  - Affecte aussi `justify-content` des catégories et métadonnées
- **Couleur du texte** : Color picker
  - Blanc (#ffffff) : Standard pour overlays sombres
  - Noir (#000000) : Pour overlays clairs/transparents

### Panel "Effet parallax"
- **Activer le parallax** : Toggle on/off
  - Image scale(1.1) pour permettre le mouvement
  - Script inline avec `requestAnimationFrame`
- **Vitesse du parallax** : 0.1-1.0 par pas de 0.1
  - 0.1-0.3 : Subtil (élégant)
  - 0.4-0.6 : Standard (recommandé)
  - 0.7-1.0 : Prononcé (dynamique)

## Styles et animations

### Animations incluses
```css
@keyframes fadeInUp
  - Titre : 0.8s delay 0s
  - Extrait : 0.8s delay 0.2s
  - Meta : 0.8s delay 0.4s

@keyframes fadeIn
  - Indicateur scroll : 1s delay 1s

@keyframes bounce
  - Indicateur scroll : 2s infinite

@keyframes scroll-wheel
  - Roue souris : 2s infinite
```

### Breakpoints responsive
- **Desktop** : Tailles complètes
- **≤1024px (Tablettes)** : 
  - Titre : 3rem (au lieu de 4rem)
  - Padding réduit à 40px/30px
- **≤768px (Mobile)** :
  - Titre : 2.25rem
  - Extrait : 1rem
  - Meta : 14px, gap 16px
- **≤480px (Petit mobile)** :
  - Titre : 1.75rem
  - Tout ajusté pour petits écrans

### Accessibilité
- **`prefers-reduced-motion`** : Désactive toutes les animations
- **`prefers-contrast: high`** : Renforce les bordures et text-shadow
- **Text-shadow** : Lisibilité garantie sur toutes les images
- **Alt text** : Support complet pour images
- **Semantic HTML** : H1 pour titre, sections appropriées

## Performance

### Optimisations CSS
- `will-change: transform` pour le parallax
- `object-fit: cover` hardware-accelerated
- `backdrop-filter: blur(10px)` sur les catégories (moderne)

### Optimisations JavaScript
- Script inline minime (~30 lignes)
- Passive event listeners (`{ passive: true }`)
- `requestAnimationFrame` pour le scroll
- Calcul uniquement si le hero est visible (`rect.bottom > 0`)

### Chargement
- CSS enqueue conditionnel via `has_block()`
- Image `loading="eager"` (above the fold)
- Server-side rendering (pas de CLS)

## Comparaison avec image-block

| Fonctionnalité | Hero Cover | Image Block (mode cover) |
|----------------|------------|--------------------------|
| **Usage** | En-tête d'article | Image inline |
| **Contenu dynamique** | Titre, extrait, meta du post | Manuel uniquement |
| **Hauteur 100vh** | ✅ Optimisé | ⚠️ Possible mais basique |
| **Métadonnées automatiques** | ✅ Auteur, date, catégories | ❌ Non |
| **Indicateur de scroll** | ✅ Avec animation | ❌ Non |
| **Position verticale** | ✅ 3 options (haut/centre/bas) | ⚠️ Basique |
| **Performance** | ✅ Enqueue conditionnel | ⚠️ Toujours chargé |

**Conclusion** : Hero Cover est spécialisé pour les en-têtes d'articles, tandis que image-block est polyvalent pour les images inline.

## Exemples de code

### Enregistrement (automatique via _loader.php)
```php
// inc/blocks/content/hero-cover.php
archi_register_hero_cover_block();
```

### Vérification de présence
```php
if (has_block('archi-graph/hero-cover', $post_id)) {
    // Le post utilise le hero cover
}
```

### Forcer l'enqueue CSS (si nécessaire)
```php
wp_enqueue_style('archi-hero-cover');
```

### Récupérer les attributs (pour customisation)
```php
$blocks = parse_blocks($post->post_content);
foreach ($blocks as $block) {
    if ($block['blockName'] === 'archi-graph/hero-cover') {
        $overlay_color = $block['attrs']['overlayColor'] ?? '#000000';
        $show_title = $block['attrs']['showTitle'] ?? true;
        // ... utilisation custom
    }
}
```

## Troubleshooting

### Le bloc n'apparaît pas dans l'éditeur
1. Vérifier que webpack a compilé : `npm run build`
2. Vérifier le bundle : `dist/js/hero-cover.bundle.js` existe
3. Vérifier le register dans `_loader.php` : `'hero-cover'` présent
4. Vider le cache navigateur (Ctrl+Shift+R)

### L'image ne s'affiche pas
1. Vérifier que le post a une image à la une
2. Si image personnalisée : vérifier la sélection dans les paramètres
3. Inspecter l'élément : URL de l'image correcte ?
4. Permissions des fichiers média

### Le parallax ne fonctionne pas
1. Vérifier que "Activer le parallax" est coché
2. Inspecter la console : erreurs JavaScript ?
3. Vérifier `data-parallax="true"` sur le container
4. Tester sur un appareil sans `prefers-reduced-motion`

### Le texte n'est pas lisible
1. Augmenter l'opacité de l'overlay (60-80%)
2. Changer la couleur de l'overlay (noir pour images claires)
3. Changer la couleur du texte (blanc/noir selon overlay)
4. Vérifier les text-shadow (peuvent être augmentés en CSS)

### Le responsive ne marche pas
1. Vérifier les media queries dans `hero-cover.css`
2. Tester avec les DevTools responsive (F12 → toggle device toolbar)
3. Vider le cache CSS
4. Sur mobile réel : vérifier `100svh` supporté

## Fichiers du bloc

```
inc/blocks/content/hero-cover.php        # Enregistrement + rendu PHP
assets/js/blocks/hero-cover.jsx          # Interface éditeur React
assets/css/hero-cover.css                # Styles frontend + editor
dist/js/hero-cover.bundle.js             # Bundle compilé (7.72 KB)
```

## Prochaines améliorations possibles

- [ ] Support de vidéo en background (HTML5 video)
- [ ] Dégradé d'overlay multi-couleurs
- [ ] Animations d'entrée configurables (slide, fade, zoom)
- [ ] Bouton CTA configurable en bas du hero
- [ ] Support de plusieurs images (slider)
- [ ] Breadcrumbs automatiques pour la navigation
- [ ] Temps de lecture estimé dans les métadonnées
- [ ] Mode dark/light avec switch automatique

## Support

Pour toute question ou bug :
1. Vérifier cette documentation
2. Consulter les logs navigateur (F12 → Console)
3. Tester avec le thème Twenty Twenty-Three pour isoler
4. Vérifier `docs/NEW-GUTENBERG-BLOCKS.md` pour les patterns généraux

---

**Bloc créé le** : Novembre 2025  
**Version** : 1.0.0  
**Compatibilité** : WordPress 6.0+, PHP 7.4+  
**License** : GPL v2+
