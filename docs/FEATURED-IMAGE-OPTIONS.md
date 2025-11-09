# Options d'Image à la Une - Documentation

## Vue d'ensemble

Le thème Archi-Graph offre maintenant des options avancées pour personnaliser l'affichage des images à la une (featured images) sur les articles, projets architecturaux et illustrations.

## Nouvelles Options

### 1. Mode Plein Écran (Fullscreen)
**Champ**: `_archi_featured_image_fullscreen`

Active l'affichage de l'image à la une en mode hero fullscreen en haut de la page.

- ✅ **Activé** : L'image occupe tout l'écran avec overlay et titre superposé
- ❌ **Désactivé** : Affichage standard de l'image (comportement par défaut)

### 2. Effet Parallax
**Champ**: `_archi_featured_image_parallax`

Ajoute un effet parallax dynamique à l'image à la une.

#### Options disponibles :

- **Aucun effet** (`none`) : Pas d'effet parallax (par défaut)
- **Parallax Scroll** (`scroll`) : L'image se déplace plus lentement que le contenu lors du scroll, créant un effet de profondeur
- **Parallax Fixed** (`fixed`) : L'image reste fixe pendant que le contenu scrolle par-dessus
- **Zoom progressif** (`zoom`) : L'image zoome progressivement au fur et à mesure du scroll

### 3. Opacité de l'Overlay
**Champ**: `_archi_featured_image_overlay_opacity`

Contrôle l'intensité de l'overlay sombre sur l'image (valeur de 0 à 1).

- **0** : Pas d'overlay (image complètement visible)
- **0.3** : Overlay léger (valeur par défaut)
- **1** : Overlay complet (image très sombre)

## Utilisation

### Dans l'Éditeur WordPress

1. Ouvrez un article, projet ou illustration pour l'édition
2. Dans la barre latérale droite, trouvez la meta-box **"Options Image Mise en Avant"**
3. Cochez **"🖼️ Afficher en plein écran"** si vous voulez le mode hero fullscreen
4. Sélectionnez l'**Effet Parallax** désiré dans le menu déroulant
5. Ajustez l'**Opacité de l'overlay** avec le slider (0-100%)
6. Cliquez sur **Mettre à jour** ou **Publier**

### Recommandations

#### Parallax Scroll
- ✅ **Idéal pour** : Articles avec beaucoup de contenu à scroller
- ⚠️ **Note** : Désactivé automatiquement sur mobile pour les performances

#### Parallax Fixed
- ✅ **Idéal pour** : Créer un effet "cinématique" où l'image reste en arrière-plan
- ⚠️ **Attention** : Peut être désactivé sur certains navigateurs mobiles

#### Zoom Progressif
- ✅ **Idéal pour** : Mettre l'accent sur les détails d'une image
- ⚠️ **Note** : Zoom limité à 1.2x pour éviter la pixellisation

## Accessibilité

Les effets parallax respectent automatiquement la préférence utilisateur `prefers-reduced-motion`. Si un utilisateur a activé la réduction de mouvement dans ses paramètres système, les effets parallax seront désactivés.

## Performance

### Optimisations Intégrées

1. **Mobile** : Parallax désactivé sur écrans ≤ 768px
2. **GPU Acceleration** : Utilisation de `transform` et `will-change` pour des animations fluides
3. **RequestAnimationFrame** : Synchronisation avec le refresh rate du navigateur
4. **Passive Event Listeners** : Amélioration du scroll performance

## Classes CSS

### Classes Appliquées

```html
<!-- Parallax Scroll -->
<div class="archi-hero-fullscreen parallax-scroll" data-parallax="scroll">

<!-- Parallax Fixed -->
<div class="archi-hero-fullscreen parallax-fixed" data-parallax="fixed">

<!-- Zoom Progressif -->
<div class="archi-hero-fullscreen parallax-zoom" data-parallax="zoom">
```

### Personnalisation CSS

Vous pouvez personnaliser les effets dans votre CSS enfant :

```css
/* Ajuster la vitesse du parallax scroll */
.archi-hero-fullscreen.parallax-scroll .hero-media {
    /* Modifiez via JavaScript dans featured-image-parallax.js */
}

/* Modifier l'overlay */
.archi-hero-fullscreen .hero-overlay {
    background: linear-gradient(to bottom, rgba(0,0,0,0.5), rgba(0,0,0,0.8));
}

/* Ajuster le zoom maximum */
.archi-hero-fullscreen.parallax-zoom .hero-media {
    /* Modifiez MAX_ZOOM dans featured-image-parallax.js */
}
```

## Fichiers Modifiés/Créés

### PHP
- `inc/meta-boxes.php` - Ajout de la meta-box et sauvegarde des options
- `single.php` - Support des nouvelles options pour les articles
- `single-archi_project.php` - Support pour les projets
- `single-archi_illustration.php` - Support pour les illustrations
- `functions.php` - Enqueue du nouveau script JavaScript

### CSS
- `assets/css/featured-image-header.css` - Styles pour les effets parallax

### JavaScript
- `assets/js/featured-image-parallax.js` - Logique des effets parallax

## Compatibilité

- ✅ WordPress 5.0+
- ✅ Gutenberg
- ✅ Navigateurs modernes (Chrome, Firefox, Safari, Edge)
- ✅ Responsive (avec désactivation intelligente sur mobile)
- ✅ Accessible (respect de `prefers-reduced-motion`)

## Support

Pour toute question ou problème, consultez :
- La documentation principale dans `/docs`
- Les instructions Copilot dans `.github/copilot-instructions.md`

## Changelog

### Version 1.1.0 (2025-01-09)
- ✨ Ajout des options d'image à la une
- ✨ Support de 4 modes parallax (none, scroll, fixed, zoom)
- ✨ Contrôle de l'opacité de l'overlay
- ⚡ Optimisations performance mobile
- ♿ Support accessibilité avec prefers-reduced-motion
