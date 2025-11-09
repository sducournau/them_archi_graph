# Images Pleine Page - Guide d'Utilisation

**Date :** 8 novembre 2024  
**Version :** 1.0.0  
**Inspiré par :** https://www.riviere-architecte.fr/maison-s/

---

## 📖 Vue d'Ensemble

Le système d'images pleine page permet d'afficher des images qui s'étendent sur toute la largeur de l'écran avec des hauteurs ajustables pour créer un impact visuel fort, similaire au site Rivière Architecte.

### Caractéristiques

✅ **3 modes de hauteur**
- Normale (70vh) - Par défaut, équilibré
- Pleine hauteur (100vh) - Impact maximal
- Demi-hauteur (50vh) - Images secondaires

✅ **Responsive automatique**
- Ajustement intelligent sur mobile/tablette
- Hauteurs minimales garanties
- Performance optimisée (lazy loading)

✅ **Centrage des légendes**
- Texte centré sous l'image
- Max-width 800px pour lisibilité
- Style italique élégant

---

## 🎨 Utilisation dans Gutenberg

### 1. Ajouter un Bloc Image Pleine Largeur

1. Dans l'éditeur Gutenberg, cliquez sur **+** pour ajouter un bloc
2. Cherchez **"Image Pleine Largeur"** dans la catégorie "Archi Graph"
3. Cliquez pour insérer le bloc

### 2. Sélectionner une Image

Deux méthodes :
- **Méthode 1 :** Cliquez sur le placeholder et sélectionnez depuis la bibliothèque
- **Méthode 2 :** Glissez-déposez une image directement

### 3. Configurer la Hauteur

Dans la barre latérale droite (InspectorControls) :

**Paramètres de l'image > Hauteur de l'image**

- **Normale (70vh)** - Par défaut
  - Idéal pour images principales
  - Bon équilibre hauteur/contenu
  - Min: 500px, Max: 900px

- **Pleine hauteur (100vh)**
  - Pour images exceptionnelles
  - Impact visuel maximal
  - Occupe tout l'écran

- **Demi-hauteur (50vh)**
  - Images secondaires/détails
  - Moins imposant
  - Min: 400px, Max: 600px

### 4. Ajouter une Légende (Optionnel)

Dans les paramètres :
1. Zone de texte **"Légende"**
2. Entrez votre description
3. S'affiche centré sous l'image en italique

### 5. Texte Alternatif (Accessibilité)

⚠️ **Important pour le SEO et l'accessibilité**

Dans les paramètres :
1. Champ **"Texte alternatif"**
2. Décrivez l'image pour les lecteurs d'écran
3. Utilisé si l'image ne charge pas

---

## 💻 Exemples d'Utilisation

### Exemple 1 : Image Héroïque Principale

```
Mode : Pleine hauteur (100vh)
Image : Vue extérieure spectaculaire d'un bâtiment
Légende : "Façade principale - Vue depuis le jardin, été 2023"
Alt : "Maison moderne avec grandes baies vitrées entourée de végétation"
```

**Rendu :**
- Image occupe tout l'écran (100% largeur, 100vh hauteur)
- Fort impact visuel au scroll
- Légende discrète en bas

### Exemple 2 : Série d'Images de Détails

```
1ère image - Mode : Normale (70vh)
   Alt : "Détail de la charpente en bois apparent"
   Légende : "Charpente traditionnelle restaurée"

2ème image - Mode : Demi-hauteur (50vh)
   Alt : "Escalier intérieur en métal et bois"
   Légende : "Escalier sur mesure - Métallier local"

3ème image - Mode : Demi-hauteur (50vh)
   Alt : "Vue du salon depuis la mezzanine"
```

**Rendu :**
- 1ère image impose le ton (70vh)
- Images suivantes plus petites (50vh)
- Rythme visuel varié

### Exemple 3 : Alternance Texte/Image

```
[Paragraphe de texte centré - 800px]

[Image Pleine Largeur - Normale 70vh]
Légende : "Coupe longitudinale du bâtiment"

[Paragraphe de texte centré - 800px]

[Image Pleine Largeur - Demi-hauteur 50vh]
Légende : "Plan du rez-de-chaussée"

[Paragraphe de texte centré - 800px]
```

**Rendu :**
- Alternance contenu centré / images pleine largeur
- Lecture agréable avec breaks visuels
- Style Rivière Architecte

---

## 📐 Spécifications Techniques

### Hauteurs par Appareil

| Mode | Desktop (>1024px) | Tablette (≤1024px) | Mobile (≤768px) | Petit Mobile (≤480px) |
|------|-------------------|--------------------|-----------------|-----------------------|
| **Normale** | 70vh (500-900px) | 60vh (400-700px) | 50vh (300-500px) | 40vh (250-400px) |
| **Pleine hauteur** | 100vh (no max) | 100vh (no max) | 70vh (no max) | 70vh (no max) |
| **Demi-hauteur** | 50vh (400-600px) | 45vh (350px min) | 40vh (280px min) | 35vh (220px min) |

### Classes CSS Utilisées

```css
/* Bloc principal */
.archi-image-full-width {
    width: 100vw;
    position: relative;
    left: 50%;
    margin-left: -50vw;
    margin-top: 3em;
    margin-bottom: 3em;
    overflow: hidden;
}

/* Image normale (par défaut) */
.archi-image-full-width img {
    width: 100%;
    height: 70vh;
    min-height: 500px;
    max-height: 900px;
    object-fit: cover;
}

/* Modificateur pleine hauteur */
.archi-image-full-width.full-viewport img {
    height: 100vh;
    max-height: none;
}

/* Modificateur demi-hauteur */
.archi-image-full-width.half-viewport img {
    height: 50vh;
    min-height: 400px;
    max-height: 600px;
}

/* Légende */
.archi-image-full-width figcaption {
    max-width: 800px;
    margin: 1.5em auto 0;
    text-align: center;
    font-size: 15px;
    color: #777;
    font-style: italic;
}
```

---

## 🎯 Bonnes Pratiques

### ✅ Recommandations

1. **Format d'image**
   - Préférez le format **paysage** (16:9 ou 21:9)
   - Résolution minimum : **1920x1080px**
   - Optimisez le poids (WebP recommandé, <300KB)

2. **Choix du mode de hauteur**
   - **100vh** : 1-2 images maximum par page (hero)
   - **70vh** : Images principales (3-5 par page)
   - **50vh** : Détails, plans, croquis (illimité)

3. **Alternance avec le contenu**
   ```
   Texte centré (800px)
   Image pleine largeur
   Texte centré (800px)
   Image pleine largeur
   ```

4. **Légendes**
   - Courtes et descriptives (1-2 lignes)
   - Contexte : lieu, date, crédit photo
   - Optionnel mais recommandé

5. **Accessibilité**
   - **Toujours** remplir le texte alternatif
   - Décrire ce qu'on voit, pas "photo de..."
   - Concis mais informatif

### ❌ À Éviter

1. ❌ Trop d'images 100vh (max 2)
2. ❌ Images portrait en pleine largeur (déformation)
3. ❌ Images floues ou mal cadrées
4. ❌ Poids trop lourd (>1MB non optimisé)
5. ❌ Oublier le texte alternatif

---

## 🔧 Dépannage

### Problème : L'image ne s'affiche pas en pleine largeur

**Cause :** Conteneur parent avec max-width  
**Solution :** Le bloc gère automatiquement le débordement avec `left: 50%; margin-left: -50vw`

### Problème : L'image est déformée

**Cause :** Ratio d'image incompatible  
**Solution :** 
- Utilisez `object-fit: cover` (déjà activé)
- Vérifiez le ratio de l'image source (préférez 16:9)
- Recadrez l'image avant upload

### Problème : La hauteur semble incorrecte

**Cause :** Hauteurs min/max en conflit  
**Solution :**
- Vérifiez le mode sélectionné (Normal/Pleine/Demi)
- Sur mobile, les hauteurs sont automatiquement réduites
- Testez en mode responsive dans le navigateur

### Problème : La légende n'est pas centrée

**Cause :** CSS personnalisé qui override  
**Solution :**
- Vérifiez `centered-content.css` est bien chargé
- Inspectez avec DevTools pour conflits CSS
- Max-width 800px devrait être appliqué automatiquement

---

## 🚀 Améliorations Futures

### Court terme
- [ ] Mode galerie (lightbox au clic)
- [ ] Parallax sur images pleine hauteur
- [ ] Overlay avec texte/titre sur l'image

### Moyen terme
- [ ] Lazy loading avancé (intersection observer)
- [ ] Préchargement intelligent
- [ ] Support vidéo pleine largeur

### Long terme
- [ ] Slider/carrousel pleine largeur
- [ ] Mode comparaison avant/après
- [ ] Intégration avec graph de relations

---

## 📚 Fichiers Concernés

### CSS
- `assets/css/centered-content.css` - Styles principaux des images pleine largeur

### JavaScript
- `assets/js/blocks/image-blocks.jsx` - Définition du bloc Gutenberg

### PHP
- `inc/blocks/content/image-blocks.php` - Rendu serveur du bloc

---

## 🎓 Ressources

### Exemples Inspirants
- https://www.riviere-architecte.fr/maison-s/
- https://www.riviere-architecte.fr/extension-et-renovation-dune-ancienne-maison/

### Documentation WordPress
- [Gutenberg Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [Image Optimization](https://developer.wordpress.org/advanced-administration/performance/optimization/)

### CSS Viewport Units
- [MDN: CSS Viewport Units](https://developer.mozilla.org/en-US/docs/Web/CSS/length#viewport-percentage_lengths)
- [Object-fit Property](https://developer.mozilla.org/en-US/docs/Web/CSS/object-fit)

---

**Mis à jour le :** 8 novembre 2024  
**Prochaine révision :** Après tests utilisateurs  
**Contact :** Support technique via GitHub Issues
