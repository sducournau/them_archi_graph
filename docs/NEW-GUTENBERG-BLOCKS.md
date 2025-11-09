# Nouveaux Blocs Gutenberg - Effets de Défilement

## 📅 Date d'implémentation
8 novembre 2025

## 🎯 Vue d'ensemble

Ce document décrit les nouveaux blocs Gutenberg personnalisés ajoutés au thème Archi-Graph pour créer des effets de défilement avancés et des présentations architecturales immersives.

## 📦 Blocs Implémentés

### 1. Image à Défilement Fixe (Fixed Background)
**Nom du bloc:** `archi-graph/fixed-background`  
**Fichiers:**
- JSX: `assets/js/blocks/parallax-blocks.jsx`
- PHP: `inc/blocks/content/parallax-blocks.php`
- CSS: `assets/css/parallax-blocks.css`

**Fonctionnalités:**
- ✅ Image de fond avec effet parallax (background-attachment: fixed)
- ✅ Contrôle de la hauteur minimale (300px - 1000px)
- ✅ Overlay avec contrôle d'opacité (0% - 100%)
- ✅ Sélecteur de couleur pour l'overlay
- ✅ Contenu texte optionnel avec RichText
- ✅ Position du contenu configurable (haut, centre, bas)
- ✅ Option pour activer/désactiver l'effet parallax
- ✅ Responsive - désactive l'effet parallax sur mobile pour les performances
- ✅ Support du mode sombre

**Cas d'usage:**
- Sections de héros avec images architecturales
- Séparateurs visuels entre sections de contenu
- Présentations de projets avec images de couverture

**Attributs:**
```javascript
{
  imageUrl: string,
  imageId: number,
  minHeight: number (défaut: 500),
  overlayOpacity: number (défaut: 0),
  overlayColor: string (défaut: '#000000'),
  content: string,
  contentPosition: 'top' | 'center' | 'bottom',
  enableParallax: boolean (défaut: true)
}
```

---

### 2. Section Scroll Collant (Sticky Scroll)
**Nom du bloc:** `archi-graph/sticky-scroll`  
**Fichiers:**
- JSX: `assets/js/blocks/parallax-blocks.jsx`
- PHP: `inc/blocks/content/parallax-blocks.php`
- CSS: `assets/css/parallax-blocks.css`

**Fonctionnalités:**
- ✅ Image collante (sticky) qui reste fixée pendant le défilement
- ✅ Contenu qui défile à côté de l'image
- ✅ Position de l'image configurable (gauche/droite)
- ✅ Titre et introduction
- ✅ Liste d'éléments avec animations fadeInUp
- ✅ Gestion dynamique des éléments (ajouter/supprimer)
- ✅ Responsive - passe en colonne unique sur mobile
- ✅ Effets de survol sur les éléments
- ✅ Support du mode sombre

**Cas d'usage:**
- Présentation détaillée de projets architecturaux
- Storytelling avec image fixe et contenu narratif
- Listes de caractéristiques ou d'étapes de projet
- Portfolios avec descriptions détaillées

**Attributs:**
```javascript
{
  imageUrl: string,
  imageId: number,
  imagePosition: 'left' | 'right',
  title: string,
  content: string,
  items: [
    {
      title: string,
      description: string
    }
  ]
}
```

---

## 🎨 Styles et Animations

### CSS Principal
**Fichier:** `assets/css/parallax-blocks.css`

**Animations incluses:**
- `fadeInUp` - Révélation progressive des éléments avec translation verticale
- Effets de survol sur les cartes d'éléments
- Transitions fluides pour les images sticky
- Support du `backdrop-filter` pour les navigateurs compatibles

**Points de rupture responsive:**
- `@media (max-width: 1024px)` - Tablettes
- `@media (max-width: 768px)` - Mobiles

---

## 🔧 Configuration Technique

### Webpack Configuration
**Fichier mis à jour:** `webpack.config.js`

Nouveau point d'entrée ajouté:
```javascript
"parallax-blocks": "./assets/js/blocks/parallax-blocks.jsx"
```

### Block Loader
**Fichier mis à jour:** `inc/blocks/_loader.php`

- Ajout de `archi-parallax-blocks` dans la liste des scripts
- Enqueue automatique du CSS `parallax-blocks.css`
- Gestion des dépendances WordPress (wp-blocks, wp-element, wp-block-editor, etc.)

---

## 📋 Blocs Existants (Référence)

### Blocs d'images déjà présents:
1. **Image Pleine Largeur** (`archi-graph/image-full-width`)
   - Hauteurs configurables: normale (70vh), pleine (100vh), demi (50vh)
   
2. **Images en Colonnes** (`archi-graph/images-columns`)
   - 2 ou 3 colonnes
   - Légendes individuelles

3. **Image Portrait** (`archi-graph/image-portrait`)
   - Centré avec largeur limitée
   - Optimal pour images verticales

4. **Couverture Image + Texte** (`archi-graph/cover-block`)
   - Similaire au bloc Cover WordPress
   - Overlay, parallax, positionnement du contenu

---

## 🚀 Utilisation dans l'Éditeur WordPress

### Bloc Fixed Background
1. Ajouter le bloc "Image Défilement Fixe" depuis la catégorie "Archi-Graph"
2. Sélectionner une image
3. Configurer dans le panneau latéral:
   - Hauteur minimale
   - Opacité et couleur de l'overlay
   - Position du contenu
   - Activer/désactiver le parallax
4. Ajouter du texte optionnel dans le contenu

### Bloc Sticky Scroll
1. Ajouter le bloc "Section Scroll Collant" depuis la catégorie "Archi-Graph"
2. Sélectionner une image qui restera fixée
3. Remplir le titre et le contenu introductif
4. Ajouter des éléments avec le bouton "Ajouter un élément"
5. Configurer la position de l'image (gauche/droite) dans le panneau latéral

---

## 🔒 Sécurité

Toutes les fonctions suivent les bonnes pratiques WordPress:
- ✅ Vérification de `ABSPATH`
- ✅ Échappement des sorties avec `esc_url()`, `esc_attr()`, `wp_kses_post()`
- ✅ Sanitization des entrées
- ✅ Nonces pour les requêtes AJAX (si nécessaire)
- ✅ Vérification des capacités utilisateur via WordPress

---

## 📱 Responsive Design

Les deux blocs sont entièrement responsive:

**Desktop (> 1024px):**
- Grille 2 colonnes pour sticky scroll
- Effet parallax activé
- Animations complètes

**Tablette (768px - 1024px):**
- Espacement réduit
- Tailles de police adaptées

**Mobile (< 768px):**
- Grille 1 colonne pour sticky scroll
- Parallax désactivé (background-attachment: scroll)
- Image sticky devient relative
- Padding réduit

---

## 🎨 Personnalisation

### Modifier les couleurs
Éditer `assets/css/parallax-blocks.css`:
```css
.archi-sticky-scroll-item {
  border-left-color: #3498db; /* Couleur de l'accent */
}
```

### Modifier les animations
Changer les durées dans `parallax-blocks.css`:
```css
.archi-sticky-scroll-item {
  animation: fadeInUp 0.6s ease forwards;
}
```

### Ajouter des délais d'animation
Les éléments ont déjà des délais progressifs:
```css
.archi-sticky-scroll-item:nth-child(1) { animation-delay: 0.1s; }
.archi-sticky-scroll-item:nth-child(2) { animation-delay: 0.2s; }
/* etc... */
```

---

## 🐛 Debugging

### Activer les logs
Éditer `inc/blocks/_loader.php` - les logs sont déjà en place:
```php
if (WP_DEBUG) {
    error_log('Archi Block loaded: ' . $block_name);
}
```

### Vérifier l'enregistrement des blocs
Dans la console du navigateur:
```javascript
wp.blocks.getBlockTypes().filter(b => b.name.includes('archi-graph'))
```

---

## 📝 Notes de Développement

### Compilation
```bash
npm run build
```

### Mode développement (watch)
```bash
npm run dev
```

### Structure des fichiers
```
assets/
  js/blocks/
    parallax-blocks.jsx       # Définition React des blocs
  css/
    parallax-blocks.css       # Styles frontend et éditeur
inc/blocks/content/
  parallax-blocks.php         # Rendu serveur et enregistrement
```

---

## 🔄 Prochaines Améliorations Possibles

1. **Scroll-triggered animations** - Animer les éléments au scroll avec Intersection Observer
2. **Lazy loading avancé** - Charger les images en différé pour meilleures performances
3. **Variantes de mise en page** - Plus d'options de disposition pour sticky scroll
4. **Intégration vidéo** - Support de vidéos en background pour fixed-background
5. **Préréglages de couleurs** - Palette de couleurs prédéfinies pour l'overlay

---

## ✅ Checklist de Vérification

Avant de mettre en production:
- [x] Build webpack réussi
- [x] CSS enqueued correctement
- [x] Blocs visibles dans l'éditeur WordPress
- [x] Server-side rendering fonctionne
- [x] Responsive sur mobile/tablette/desktop
- [x] Échappement et sécurité vérifiés
- [x] Pas d'erreurs console navigateur
- [x] Compatible avec le thème existant
- [ ] Testé sur différents navigateurs (Chrome, Firefox, Safari)
- [ ] Testé avec contenu réel
- [ ] Validé par le client/utilisateur final

---

## 📞 Support

Pour toute question sur ces blocs:
1. Consulter ce document
2. Vérifier les fichiers source commentés
3. Consulter la documentation WordPress sur les blocs Gutenberg
4. Vérifier les logs WordPress en mode debug

---

**Dernière mise à jour:** 8 novembre 2025  
**Version du thème:** Compatible avec la structure actuelle d'Archi-Graph Template
