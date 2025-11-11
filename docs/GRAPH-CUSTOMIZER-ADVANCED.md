# Personnalisation Avancée du Graphique D3.js

## 📋 Vue d'ensemble

Ce document détaille les nouvelles options de personnalisation du graphique de relations D3.js disponibles dans le **Customizer WordPress** (Apparence > Personnaliser > 🔗 Graphique D3.js).

## 🎨 Catégories de Paramètres

### 1. Paramètres de Base
- **Couleur des nœuds** : Couleur par défaut pour tous les nœuds
- **Taille des nœuds** : Taille en pixels (40-120px)
- **Regroupement par catégorie** : Intensité du clustering automatique (0-0.5)

### 2. Effets et Animations ✨

#### Animation d'Entrée
Contrôle l'effet d'apparition des nœuds au chargement du graphique :
- **Aucune** : Apparition instantanée
- **Fondu progressif** : Apparition en fade-in (par défaut)
- **Zoom progressif** : Les nœuds grossissent depuis le centre
- **Glissement** : Les nœuds glissent depuis les bords
- **Rebond** : Animation avec effet de rebond

#### Vitesse des Transitions
Durée en millisecondes des animations (200-2000ms). Valeur par défaut : 500ms.

#### Effet de Survol
Réaction visuelle au passage de la souris sur un nœud :
- **Aucun** : Pas d'effet particulier
- **Mise en surbrillance** : Le nœud s'illumine (par défaut)
- **Agrandissement** : Le nœud grossit légèrement
- **Halo lumineux** : Un halo apparaît autour du nœud
- **Pulsation** : Le nœud pulse doucement

### 3. Liens et Connexions 🔗

#### Apparence des Liens
- **Couleur des liens** : Couleur des lignes de connexion (défaut : #999999)
- **Épaisseur des liens** : Largeur en pixels (0.5-5px, défaut : 1.5px)
- **Opacité des liens** : Transparence (0.1-1, défaut : 0.6)

#### Style de Lien
- **Ligne continue** : Trait plein standard (par défaut)
- **Ligne pointillée** : Trait en pointillés
- **Ligne courbe** : Courbes de Bézier pour un effet plus organique

#### Flèches Directionnelles
Active/désactive l'affichage de flèches indiquant le sens des relations entre articles.

#### Animation des Liens
- **Aucune** : Liens statiques (par défaut)
- **Pulsation** : Les liens pulsent doucement
- **Flux directionnel** : Animation de flux le long des liens
- **Lueur** : Effet de lueur sur les liens

### 4. Couleurs par Catégorie 🎨

#### Activation
Cochez **"Couleurs par catégorie"** pour attribuer automatiquement des couleurs différentes aux nœuds selon leur catégorie WordPress.

#### Palettes Disponibles

**Par défaut (Bleus)** 🔵
```
Palette professionnelle avec nuances de bleu
Idéale pour sites corporate et architecturaux
```

**Chaude (Rouges/Oranges)** 🔥
```
Tons chauds et énergiques
Parfait pour projets créatifs et dynamiques
```

**Froide (Bleus/Verts)** ❄️
```
Palette apaisante et naturelle
Recommandée pour projets écologiques
```

**Vibrante (Multicolore)** 🌈
```
Couleurs variées et contrastées
Excellent pour portfolios diversifiés
```

**Pastel (Doux)** 🎀
```
Couleurs douces et subtiles
Parfait pour sites élégants et minimalistes
```

**Nature (Terre/Vert)** 🌿
```
Tons terreux et organiques
Idéal pour projets liés à la nature
```

**Monochrome (Nuances de gris)** ⚫
```
Élégance sobre et professionnelle
Parfait pour sites minimalistes
```

#### Légende des Catégories
Active/désactive l'affichage d'une légende visuelle sur le graphique montrant la correspondance entre couleurs et catégories.

### 5. Options d'Affichage 👁️

- **Popup : titre uniquement** : N'affiche que le titre dans la popup de survol (sans l'extrait)
- **Afficher les commentaires** : Inclut les commentaires dans le panneau latéral d'information

## 🚀 Utilisation

### Accès au Customizer
1. Allez dans **Apparence > Personnaliser**
2. Cliquez sur **🔗 Graphique D3.js**
3. Modifiez les paramètres en direct avec preview instantané

### Preview en Temps Réel
Tous les changements sont prévisualisés instantanément dans le customizer. Vous pouvez :
- Tester différentes combinaisons de couleurs
- Ajuster les animations en direct
- Voir l'impact des modifications de liens
- Expérimenter avec les palettes de catégories

### Publication des Changements
Une fois satisfait de votre configuration :
1. Cliquez sur **Publier** en haut du Customizer
2. Les modifications seront appliquées sur votre site

## 💡 Conseils d'Utilisation

### Performance
- **Animations** : Les animations trop rapides (<300ms) peuvent sembler saccadées
- **Transitions** : Une vitesse de 500ms offre un bon équilibre
- **Liens** : Une opacité de 0.6 rend les liens visibles sans surcharger

### Esthétique
- **Couleurs par catégorie** : Activez uniquement si vous avez plusieurs catégories bien définies
- **Animations de liens** : Utilisez avec modération pour ne pas distraire l'utilisateur
- **Effet de survol** : "Mise en surbrillance" ou "Agrandissement" sont les plus lisibles

### Accessibilité
- **Contraste** : Assurez-vous que les couleurs des nœuds contrastent avec le fond
- **Épaisseur des liens** : Minimum 1px pour une bonne visibilité
- **Opacité** : Ne descendez pas en dessous de 0.4 pour les liens

## 🔧 Intégration Technique

### Variables JavaScript Disponibles
Les paramètres sont exposés via l'objet global `archiGraphSettings` :

```javascript
window.archiGraphSettings = {
    // Nœuds
    defaultNodeColor: '#3498db',
    defaultNodeSize: 60,
    clusterStrength: 0.1,
    
    // Animations
    animationMode: 'fade-in',
    transitionSpeed: 500,
    hoverEffect: 'highlight',
    
    // Liens
    linkColor: '#999999',
    linkWidth: 1.5,
    linkOpacity: 0.6,
    linkStyle: 'solid',
    showArrows: false,
    linkAnimation: 'none',
    
    // Catégories
    categoryColorsEnabled: false,
    categoryPalette: 'default',
    showCategoryLegend: true,
    categoryColors: [...]
}
```

### Fonction de Mise à Jour Dynamique
Pour mettre à jour le graphique dynamiquement :

```javascript
if (typeof window.updateGraphSettings === 'function') {
    window.updateGraphSettings({
        linkColor: '#ff0000',
        linkWidth: 2,
        hoverEffect: 'scale'
    });
}
```

## 📚 Références

### Fonctions PHP Ajoutées
- `archi_get_category_color_palette($palette_name)` : Retourne un tableau de couleurs pour une palette
- `archi_get_category_color($category_id, $palette)` : Retourne la couleur pour une catégorie spécifique
- `archi_localize_graph_settings()` : Expose les paramètres au JavaScript

### Fichiers Modifiés
- `inc/customizer.php` : Ajout des nouveaux settings et controls
- `assets/js/customizer-preview.js` : Preview en temps réel des modifications

### Hooks WordPress Utilisés
- `customize_register` : Enregistrement des paramètres
- `wp_enqueue_scripts` : Exposition des paramètres au front-end
- `customize_preview_init` : Activation du preview en temps réel

## 🐛 Dépannage

### Le Preview ne Fonctionne Pas
- Vérifiez que `customizer-preview.js` est bien chargé
- Ouvrez la console JavaScript pour voir les erreurs éventuelles
- Assurez-vous d'être sur la page d'accueil (où le graph est affiché)

### Les Couleurs par Catégorie ne S'appliquent Pas
- Vérifiez que "Couleurs par catégorie" est bien activé
- Assurez-vous que vos articles ont des catégories assignées
- Le composant React doit appeler `archiGraphSettings.categoryColors`

### Les Animations sont Saccadées
- Réduisez la vitesse des transitions (augmentez la durée)
- Désactivez les animations de liens si vous avez beaucoup de nœuds
- Vérifiez les performances de votre navigateur

## 📝 Notes de Version

**Version 1.0** (Novembre 2025)
- ✅ Ajout des effets et animations personnalisables
- ✅ Personnalisation complète des liens et connexions
- ✅ Système de couleurs par catégorie avec 7 palettes
- ✅ Preview en temps réel dans le Customizer
- ✅ API JavaScript pour intégration dynamique

---

**Auteur** : Archi-Graph Theme
**Dernière mise à jour** : Novembre 2025
