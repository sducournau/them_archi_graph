# Bloc Couverture Image + Texte - Guide d'Utilisation

**Date :** 8 novembre 2024  
**Inspiré par :** https://www.riviere-architecte.fr/maison-s/  
**Classes WordPress :** `wp-block-cover`, `wp-block-cover__background`, `wp-block-cover__inner-container`

---

## 📖 Vue d'Ensemble

Le **Bloc Couverture Image + Texte** permet de créer des sections visuelles impactantes avec une image de fond, un overlay sombre ajustable, et du texte superposé. Compatible avec les classes WordPress standard du bloc Cover natif.

### Caractéristiques

✅ **Image de fond pleine largeur**  
✅ **Overlay personnalisable** (couleur + opacité 0-100%)  
✅ **Position du texte** (Haut, Centre, Bas)  
✅ **Effet parallax** optionnel  
✅ **Hauteur ajustable** (200px - 800px)  
✅ **Texte éditable** (Titre + Sous-titre)

---

## 🎨 Utilisation dans Gutenberg

### 1. Insérer le Bloc

1. Cliquez sur **+** dans l'éditeur Gutenberg
2. Cherchez **"Couverture Image + Texte"**
3. Dans la catégorie **"Archi Graph"**
4. Cliquez pour insérer

### 2. Ajouter une Image de Fond

- Cliquez sur le placeholder
- Sélectionnez depuis la bibliothèque médias
- **OU** glissez-déposez une image

**Format recommandé :**
- Paysage 21:9 ou 16:9
- 1920x800px minimum
- Optimisée (<500KB)

### 3. Éditer le Texte

**Titre (H2) :**
- Cliquez sur "Titre de la couverture..."
- Tapez votre titre
- Style : 2.5rem, gras, blanc avec ombre

**Sous-titre (Paragraphe) :**
- Cliquez sur "Sous-titre optionnel..."
- Ajoutez un texte descriptif
- Style : 1.25rem, léger, blanc avec ombre

### 4. Paramètres de l'Overlay

**Barre latérale droite > Paramètres de l'overlay**

**Opacité de l'overlay (0-100%) :**
- `0%` - Aucun overlay, image visible
- `50%` - Par défaut, équilibré
- `80%` - Très sombre, texte très lisible
- `100%` - Complètement noir

**Couleur de l'overlay :**
- Sélecteur de couleur
- Par défaut : Noir (#000000)
- Essayez : Bleu (#001f3f), Vert foncé (#004d00)

### 5. Mise en Page

**Hauteur minimale (200-800px) :**
- `300px` - Petit bandeau
- `400px` - Par défaut, standard
- `600px` - Grand impact visuel

**Position du contenu :**
- ☑️ **Haut** - Texte en haut
- ☑️ **Centre** - Par défaut, centré
- ☑️ **Bas** - Texte en bas

**Effet parallax :**
- ☑️ Activer pour effet de profondeur au scroll
- ⚠️ Désactiver sur mobile (performances)

---

## 💡 Exemples d'Usage

### Exemple 1 : Hero Section (Haut de Page)

```
Bloc Couverture
├─ Image : Vue panoramique du projet
├─ Hauteur : 600px
├─ Position : Centre
├─ Overlay : 50% noir
├─ Titre : "Rénovation d'un ancien corps de ferme"
└─ Sous-titre : "Réhabilitation et rénovation - Champagnac le vieux (43)"
```

**Rendu :**
- Section d'introduction spectaculaire
- Texte bien lisible sur l'image
- Premier élément après le header

### Exemple 2 : Séparation de Section

```
[Contenu texte/images]
↓
Bloc Couverture
├─ Image : Détail architectural
├─ Hauteur : 400px
├─ Position : Bas
├─ Overlay : 70% bleu foncé (#001f3f)
└─ Titre : "Phase 2 : Extension"
↓
[Suite du contenu]
```

**Rendu :**
- Break visuel entre sections
- Introduit nouvelle partie du projet
- Couleur thématique

### Exemple 3 : Call-to-Action

```
Bloc Couverture
├─ Image : Photo d'équipe ou bureau
├─ Hauteur : 500px
├─ Position : Centre
├─ Overlay : 80% noir
├─ Titre : "Démarrez votre projet avec nous"
└─ Sous-titre : "Contactez-nous pour un rendez-vous gratuit"
```

**Avec bouton en dessous (bloc Button) :**
```html
[Contact] [Portfolio]
```

---

## 🎯 Classes CSS (WordPress Standard)

### Structure HTML Générée

```html
<div class="wp-block-cover archi-cover-block is-position-center-center" style="min-height: 400px;">
    <!-- Overlay -->
    <span 
        class="wp-block-cover__background has-background-dim has-background-dim-50" 
        style="background-color: #000000;"
    ></span>
    
    <!-- Image de fond -->
    <img 
        class="wp-block-cover__image-background" 
        src="image.jpg" 
        style="object-fit: cover;"
    />
    
    <!-- Contenu -->
    <div class="wp-block-cover__inner-container is-layout-flow wp-block-cover-is-layout-flow">
        <h2 class="wp-block-heading has-text-align-center cover-title">
            Titre
        </h2>
        <p class="has-text-align-center cover-subtitle">
            Sous-titre
        </p>
    </div>
</div>
```

### Classes Principales

**Conteneur :**
- `.wp-block-cover` - Bloc principal
- `.has-parallax` - Effet parallax actif
- `.is-position-center-center` - Texte centré (défaut)
- `.is-position-top-center` - Texte en haut
- `.is-position-bottom-center` - Texte en bas

**Overlay :**
- `.wp-block-cover__background` - Overlay coloré
- `.has-background-dim` - Opacité 50% (défaut)
- `.has-background-dim-{0-100}` - Opacité spécifique (ex: `has-background-dim-80`)

**Contenu :**
- `.wp-block-cover__inner-container` - Conteneur du texte
- `.is-layout-flow` - Layout flexbox WordPress
- `.has-text-align-center` - Texte centré

---

## 📱 Responsive

| Écran | Hauteur Min | Taille Titre | Padding |
|-------|-------------|--------------|---------|
| Desktop (>768px) | Selon config | 2.5rem | 2em |
| Tablette (≤768px) | 350px | 2rem | 1.5em |
| Mobile (≤480px) | 280px | 1.5rem | 1em |

**Adaptations automatiques :**
- Texte réduit progressivement
- Padding ajusté
- Hauteurs minimales garanties

---

## ✅ Bonnes Pratiques

### Images

1. **Format paysage large** (21:9 ou 16:9)
2. **Haute résolution** (1920px largeur minimum)
3. **Optimisée** (<500KB avec compression)
4. **Bonne composition** (sujet principal au centre ou tiers)
5. **Contraste suffisant** avec le texte (sinon augmenter overlay)

### Texte

1. **Titre court** (5-10 mots maximum)
2. **Sous-titre descriptif** (1-2 phrases)
3. **Éviter les paragraphes longs**
4. **Texte blanc** fonctionne sur la plupart des overlays
5. **Tester la lisibilité** sur mobile

### Overlay

1. **Noir 50%** - Bon point de départ
2. **Augmenter si texte illisible** (60-80%)
3. **Couleurs thématiques** pour branding
4. **Bleu foncé** (#001f3f) - Professionnel
5. **Vert foncé** (#004d00) - Écologique

### Positionnement

1. **Centre** - Standard, sûr
2. **Haut** - Hero section, introduction
3. **Bas** - Attribution, légende photo
4. **Éviter haut/bas** si sujet au centre de l'image

---

## ⚠️ Limitations et Considérations

### Performance

- ❌ **Éviter effet parallax sur mobile** (consommation ressources)
- ❌ **Images trop lourdes** ralentissent le chargement
- ✅ **Lazy loading** activé par défaut
- ✅ **Optimiser les images** avant upload

### Accessibilité

- ⚠️ **Contraste texte/fond** doit être suffisant (WCAG AA)
- ⚠️ **Éviter texte essentiel** uniquement dans l'image
- ✅ **Texte alternatif** sur l'image de fond
- ✅ **Navigation clavier** possible

### SEO

- ⚠️ **Texte dans image** moins indexé
- ✅ **Utiliser vraies balises H2/P** (pas images texte)
- ✅ **Alt text descriptif** pour l'image
- ✅ **Contenu structuré** avec balises sémantiques

---

## 🔧 Personnalisation Avancée

### Modifier les Couleurs de Texte

Par défaut, texte blanc. Pour changer :

```css
.wp-block-cover__inner-container .cover-title {
    color: #ffcc00; /* Jaune */
}
```

### Ajouter un Bouton dans le Bloc

Insérer un bloc **Button** après le sous-titre :
1. À l'intérieur du bloc Couverture
2. Cliquez + entre le titre et la fin
3. Ajoutez un bloc Button
4. Style recommandé : Outline, blanc

### Créer des Variantes de Hauteur

Classes personnalisées (ajoutez dans CSS) :

```css
.wp-block-cover.is-height-small {
    min-height: 300px;
}

.wp-block-cover.is-height-large {
    min-height: 600px;
}

.wp-block-cover.is-height-full {
    min-height: 100vh;
}
```

---

## 📚 Fichiers du Bloc

### JavaScript
- `assets/js/blocks/cover-block.jsx` - Composant React Gutenberg

### PHP
- `inc/blocks/content/cover-block.php` - Rendu serveur

### CSS
- `assets/css/cover-block.css` - Styles frontend

### Compilation
- `webpack.config.js` - Configuration build
- `dist/js/cover-block.bundle.js` - Fichier compilé

---

## 🆚 Différence avec Bloc Cover WordPress Natif

| Fonctionnalité | Bloc Natif | Notre Bloc |
|----------------|------------|------------|
| **Classes CSS** | ✅ Identiques | ✅ Identiques |
| **Image de fond** | ✅ | ✅ |
| **Overlay** | ✅ | ✅ Personnalisable |
| **Opacité overlay** | 50% fixe | 0-100% ajustable |
| **Couleur overlay** | Thème | Sélecteur couleur |
| **Position texte** | Centre | Haut/Centre/Bas |
| **Effet parallax** | Non | ✅ Oui |
| **Titre + Sous-titre** | Blocs séparés | ✅ Intégré |

**Avantage :** Notre bloc offre plus de contrôle avec une interface simplifiée.

---

## 🎓 Ressources

### Documentation WordPress
- [Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [Cover Block](https://wordpress.org/support/article/cover-block/)

### Inspiration Design
- https://www.riviere-architecte.fr/maison-s/
- https://www.riviere-architecte.fr/extension-et-renovation-dune-ancienne-maison/

### CSS Object-fit
- [MDN: object-fit](https://developer.mozilla.org/en-US/docs/Web/CSS/object-fit)

---

**Mis à jour le :** 8 novembre 2024  
**Version :** 1.0.0  
**Compatibilité :** WordPress 6.0+, Gutenberg
