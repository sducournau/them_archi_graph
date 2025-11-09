# Blocs Gutenberg pour Images - Style Centré

## Vue d'ensemble

Ce système de blocs permet de créer des articles et projets avec un contenu centré (max 800px) et des images qui s'étendent en pleine largeur, similaire au style du site [Rivière Architecte](https://www.riviere-architecte.fr/maison-s/).

## Structure du Contenu

### Contenu par défaut (centré)

Par défaut, tout le contenu des articles, projets et illustrations est centré avec une largeur maximale de 800px :
- Textes
- Paragraphes
- Titres
- Images standard

### Sorties en pleine largeur

Les blocs d'images spéciaux s'étendent sur toute la largeur de l'écran pour créer un effet visuel impactant.

## Blocs Disponibles

### 1. Image Pleine Largeur

**Nom du bloc :** `archi-graph/image-full-width`

**Description :** Affiche une image qui s'étend sur toute la largeur de l'écran.

**Utilisation :**
1. Dans l'éditeur Gutenberg, cliquez sur le `+` pour ajouter un bloc
2. Recherchez "Image Pleine Largeur"
3. Sélectionnez votre image depuis la médiathèque
4. (Optionnel) Ajoutez un texte alternatif et une légende dans le panneau de droite

**Options :**
- **Texte alternatif** : Pour l'accessibilité
- **Légende** : Texte affiché sous l'image en italique

**Exemple d'usage :**
```
Paragraphe de texte centré...

[Bloc Image Pleine Largeur - Photo du bâtiment]

Suite du texte centré...
```

### 2. Images en Colonnes

**Nom du bloc :** `archi-graph/images-columns`

**Description :** Affiche 2 ou 3 images côte à côte en pleine largeur.

**Utilisation :**
1. Ajoutez le bloc "Images en Colonnes"
2. Dans le panneau de droite, choisissez le nombre de colonnes (2 ou 3)
3. Sélectionnez vos images depuis la médiathèque (mode galerie)
4. (Optionnel) Ajoutez des légendes individuelles pour chaque image

**Options :**
- **Nombre de colonnes** : 2 ou 3
- **Légendes individuelles** : Pour chaque image

**Conseils :**
- Pour 2 colonnes : Images en format paysage (4:3)
- Pour 3 colonnes : Images en format carré (1:1) recommandé

**Exemple :**
```
[Bloc Images en Colonnes - 2 colonnes]
Image 1 : Vue extérieure | Image 2 : Vue intérieure
```

### 3. Image Portrait

**Nom du bloc :** `archi-graph/image-portrait`

**Description :** Image verticale centrée avec une largeur limitée à 600px.

**Utilisation :**
1. Ajoutez le bloc "Image Portrait"
2. Sélectionnez une image verticale
3. (Optionnel) Ajoutez texte alternatif et légende

**Idéal pour :**
- Photos verticales
- Détails architecturaux
- Plans verticaux

## Mise en Page Recommandée

### Structure type d'un article/projet

```
[Image à la une - Hero fullscreen]

[Titre et métadonnées]

Paragraphe d'introduction (centré, 800px)

Paragraphe de texte (centré)

[Bloc Image Pleine Largeur]
Légende de l'image

Suite du texte (centré)

[Bloc Images en Colonnes - 2 colonnes]
Légende image 1 | Légende image 2

Paragraphe de conclusion (centré)

[Bloc Images en Colonnes - 3 colonnes]
Détails architecturaux

Texte final (centré)
```

## Classes CSS

Les blocs utilisent les classes CSS suivantes (déjà stylées) :

- `.archi-image-full-width` - Image pleine largeur
- `.archi-images-columns-2` - Grid 2 colonnes
- `.archi-images-columns-3` - Grid 3 colonnes
- `.archi-image-portrait` - Image verticale centrée
- `.article-content` - Conteneur du contenu centré (800px max)

## Responsive

Le système est entièrement responsive :

**Mobile (< 768px) :**
- Images pleine largeur conservées
- Colonnes 2 → 1 colonne
- Colonnes 3 → 2 colonnes

**Très petit mobile (< 480px) :**
- Toutes les colonnes → 1 colonne

## Personnalisation

### Modifier la largeur du contenu centré

Dans `assets/css/centered-content.css` :

```css
.article-content,
.project-main-content .project-content {
    max-width: 800px; /* Modifier ici */
}
```

### Modifier les gaps entre les colonnes

```css
.archi-images-columns-2 {
    gap: 20px; /* Modifier ici */
}
```

### Changer les ratios d'aspect

```css
.archi-images-columns-2 img {
    aspect-ratio: 4/3; /* Modifier ici */
}
```

## Exemples d'Utilisation

### Article de Blog Architectural

1. Titre et extrait (centré)
2. Paragraphe d'introduction
3. **Image Pleine Largeur** - Vue d'ensemble du projet
4. Texte de description
5. **Images en Colonnes (2)** - Détails extérieurs
6. Texte technique
7. **Images en Colonnes (3)** - Détails intérieurs
8. Conclusion

### Projet Architectural

1. Hero avec image à la une
2. Description du projet (centrée)
3. **Image Pleine Largeur** - Vue panoramique
4. Concept architectural
5. **Images en Colonnes (2)** - Plans et élévations
6. Détails techniques
7. **Image Portrait** - Coupe verticale
8. Matériaux et finitions
9. **Images en Colonnes (3)** - Détails de construction

## Dépannage

### Les blocs n'apparaissent pas dans l'éditeur

1. Vérifiez que webpack a compilé : `npm run build`
2. Vérifiez que le fichier existe : `dist/js/image-blocks.bundle.js`
3. Videz le cache du navigateur
4. Vérifiez la console JavaScript pour les erreurs

### Les styles ne s'appliquent pas

1. Vérifiez que `centered-content.css` est bien chargé
2. Inspectez l'élément pour voir les classes appliquées
3. Videz le cache WordPress si activé

### Images floues ou déformées

1. Utilisez des images haute résolution (min 1920px de large)
2. Optimisez avant upload (WebP recommandé)
3. Vérifiez les ratios d'aspect dans le CSS

## Fichiers Modifiés/Créés

- `assets/css/centered-content.css` - Styles du système centré
- `assets/js/blocks/image-blocks.jsx` - Blocs React Gutenberg
- `inc/blocks/content/image-blocks.php` - Rendu côté serveur
- `functions.php` - Enregistrement du CSS
- `webpack.config.js` - Configuration de compilation
- `inc/blocks/_loader.php` - Chargement des assets JS

## Support Navigateurs

- Chrome/Edge : ✅ Full support
- Firefox : ✅ Full support
- Safari : ✅ Full support
- IE11 : ❌ Non supporté (grids CSS)

## Performances

- Images lazy loading activé par défaut
- CSS minifié en production
- JavaScript compilé et optimisé par webpack
- Support WebP recommandé pour les images

## À Venir

- [ ] Bloc vidéo pleine largeur
- [ ] Bloc galerie avec lightbox
- [ ] Bloc comparaison avant/après
- [ ] Options de couleurs d'overlay
- [ ] Animations au scroll
