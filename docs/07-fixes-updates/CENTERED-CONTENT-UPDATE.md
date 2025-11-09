# Mise à Jour : Style Centré avec Blocs d'Images

## 📝 Résumé des Changements

Ce commit ajoute un système de contenu centré inspiré du site Rivière Architecte (https://www.riviere-architecte.fr/maison-s/) avec des blocs Gutenberg pour images en pleine largeur.

## ✨ Nouvelles Fonctionnalités

### Style de Contenu Centré

- Le contenu des articles, projets et illustrations est maintenant centré avec une largeur maximale de 800px
- Amélioration de la lisibilité avec une typographie optimisée
- Support complet pour le responsive

### Nouveaux Blocs Gutenberg

**1. Bloc Image Pleine Largeur**
- Image qui s'étend sur toute la largeur de l'écran
- Supporte les légendes
- Optimisé avec lazy loading

**2. Bloc Images en Colonnes**
- Affiche 2 ou 3 images côte à côte en pleine largeur
- Légendes individuelles pour chaque image
- Responsive automatique (colonnes → lignes sur mobile)

**3. Bloc Image Portrait**
- Image verticale centrée avec largeur limitée (600px)
- Idéal pour les photos verticales et détails

## 📁 Fichiers Créés

```
assets/css/centered-content.css           # Styles du système centré
assets/js/blocks/image-blocks.jsx         # Blocs React Gutenberg
inc/blocks/content/image-blocks.php       # Rendu côté serveur des blocs
docs/02-features/blocs-images-centrees.md # Documentation complète
```

## 📝 Fichiers Modifiés

```
functions.php                             # Enregistrement du nouveau CSS
webpack.config.js                         # Ajout de l'entrée image-blocks
inc/blocks/_loader.php                    # Chargement des assets JS
```

## 🚀 Utilisation

### Dans l'Éditeur Gutenberg

1. Ouvrez un article, projet ou illustration
2. Cliquez sur le `+` pour ajouter un bloc
3. Recherchez "Image Pleine Largeur", "Images en Colonnes" ou "Image Portrait"
4. Sélectionnez vos images et ajoutez vos légendes

### Structure Recommandée

```
[Titre et introduction - Centrés 800px]

Paragraphe de texte (centré)

[Image Pleine Largeur - s'étend sur tout l'écran]

Suite du texte (centré)

[Images en Colonnes 2 ou 3 - pleine largeur]

Paragraphe de conclusion (centré)
```

## 🎨 Styles Appliqués

- **Contenu centré** : `max-width: 800px; margin: 0 auto;`
- **Images pleine largeur** : `width: 100vw;`
- **Typography** : `font-size: 18px; line-height: 1.8;`
- **Gaps colonnes** : `20px` (2 cols) / `15px` (3 cols)

## 📱 Responsive

- **Desktop** : Contenu centré 800px, images pleine largeur
- **Tablet (< 768px)** : 2 colonnes → 1 colonne
- **Mobile (< 480px)** : 3 colonnes → 1 colonne

## 🔧 Compilation

Les blocs sont compilés avec webpack :

```bash
npm run build
```

Le fichier compilé : `dist/js/image-blocks.bundle.js` (9.15 KiB minifié)

## 📖 Documentation

Documentation complète disponible dans :
`docs/02-features/blocs-images-centrees.md`

Inclut :
- Guide d'utilisation détaillé
- Exemples de mise en page
- Options de personnalisation
- Dépannage

## ✅ Tests

- [x] Compilation webpack réussie
- [x] CSS chargé correctement
- [x] Blocs disponibles dans Gutenberg
- [x] Responsive testé sur différentes tailles d'écran
- [x] Performance : lazy loading activé
- [x] Compatibilité navigateurs (Chrome, Firefox, Safari)

## 🎯 Objectif Atteint

Le style des articles et projets correspond maintenant à l'exemple fourni :
- Contenu principal centré et lisible
- Images en pleine largeur pour impact visuel maximal
- Flexibilité avec colonnes 2 ou 3 pour galleries
- Expérience utilisateur améliorée

## 🔗 Référence

Style inspiré de : https://www.riviere-architecte.fr/maison-s/

## 📝 Notes Techniques

- Utilise CSS Grid pour les colonnes
- Blocs enregistrés côté serveur pour performance
- React JSX pour l'interface éditeur
- Externals WordPress pour optimisation du bundle
- Support HTML5 figure/figcaption pour accessibilité

## 🚦 Prochaines Étapes

Les templates existants (`single.php`, `single-archi_project.php`, `single-archi_illustration.php`) utilisent déjà le nouveau système de style centré. Les utilisateurs peuvent maintenant :

1. Créer du contenu texte (automatiquement centré)
2. Insérer les nouveaux blocs d'images pour varier la mise en page
3. Combiner texte centré et images pleine largeur pour un effet professionnel

## ⚠️ Compatibilité

- Nécessite WordPress 5.0+ (Gutenberg)
- Node.js et npm pour compilation
- Navigateurs modernes (pas de support IE11)
