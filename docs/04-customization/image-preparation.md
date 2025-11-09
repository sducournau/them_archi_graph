# Guide : Préparation des Images PNG pour le Graphique

## 🎯 Objectif

Les images doivent apparaître **en entier** dans le graphique, sans être coupées, déformées ou cachées. Ce guide explique comment préparer correctement vos images PNG.

## ✅ Caractéristiques Requises

### Format de Fichier
- **Type :** PNG (format .png)
- **Transparence :** Canal alpha activé (fond transparent)
- **Profondeur :** 24-bit ou 32-bit

### Dimensions Recommandées

#### Pour les Articles Normaux
- **Taille idéale :** 150 x 150 pixels
- **Ratio :** Carré de préférence (1:1)
- **Taille minimale :** 100 x 100 px
- **Taille maximale :** 300 x 300 px

#### Pour les Projets Architecturaux
- **Taille idéale :** 200 x 200 pixels
- **Ratio :** Carré ou légèrement rectangulaire
- **Taille minimale :** 150 x 150 px
- **Taille maximale :** 400 x 400 px

### Composition de l'Image

```
┌─────────────────────────┐
│  [Marge 10-20px]        │
│                         │
│      ┌─────────┐       │
│      │         │       │
│      │ CONTENU │       │  ← Zone visible
│      │ CENTRAL │       │
│      │         │       │
│      └─────────┘       │
│                         │
│  [Marge 10-20px]        │
└─────────────────────────┘
```

**Points importants :**
- ✅ Laisser une marge de 10-20px sur les bords
- ✅ Centrer le contenu principal
- ✅ Fond transparent (pas de blanc, pas de couleur)
- ✅ Contenu bien contrasté pour la lisibilité

## 🎨 Création avec Photoshop

### Méthode 1 : Nouveau Document

1. **Fichier → Nouveau**
   - Largeur : 200 px
   - Hauteur : 200 px
   - Résolution : 72 ppi
   - ✅ Cocher "Fond transparent"

2. **Créer votre design**
   - Importer/dessiner votre contenu
   - S'assurer qu'il reste dans la zone centrale

3. **Exporter**
   - Fichier → Exporter → Enregistrer pour le Web (hérité)
   - Format : PNG-24
   - ✅ Cocher "Transparence"
   - Qualité : 100%

### Méthode 2 : Détourer une Photo

1. **Ouvrir la photo**
2. **Sélectionner le sujet**
   - Outil Sélection rapide
   - Ou : Sélection → Sujet (IA)
3. **Inverser et supprimer le fond**
   - Sélection → Inverser
   - Supprimer
4. **Recadrer au besoin**
   - Image → Taille de la zone de travail
   - 200 x 200 px, centré
5. **Exporter en PNG-24**

## 🖌️ Création avec GIMP (Gratuit)

1. **Fichier → Nouvelle image**
   - 200 x 200 px
   - Options avancées → Remplir avec : Transparence

2. **Créer votre design**
   - Ajouter calques et contenu

3. **Exporter**
   - Fichier → Exporter sous
   - Sélectionner : PNG
   - ✅ Cocher "Enregistrer le canal alpha"

## 🎭 Création avec Figma

1. **Créer un Frame**
   - F (raccourci)
   - 200 x 200 px

2. **Designer votre contenu**
   - Rester dans la zone centrale

3. **Exporter**
   - Sélectionner le Frame
   - Panneau Export (en bas à droite)
   - Format : PNG
   - Échelle : 1x ou 2x
   - Exporter

## 🤖 Création avec IA

### Avec DALL-E, Midjourney, ou Stable Diffusion

**Prompt suggéré :**
```
[votre sujet], icon style, minimal, centered on white background, 
no shadows, clean vector style, suitable for cutting out
```

**Puis :**
1. Télécharger l'image générée
2. Utiliser remove.bg ou Photoshop pour enlever le fond
3. Recentrer et recadrer à 200x200px

## 🔧 Outils en Ligne

### Remove.bg (Enlever le fond)
1. Aller sur https://remove.bg
2. Upload votre image
3. Télécharger le PNG avec fond transparent
4. Redimensionner si nécessaire

### Canva (Design graphique)
1. Créer un design 200x200px
2. Utiliser les éléments et cliparts
3. Télécharger → PNG avec fond transparent (Pro)

### Photopea (Photoshop en ligne gratuit)
1. Aller sur https://www.photopea.com
2. Créer nouveau → 200x200px → Transparent
3. Designer
4. Fichier → Export as → PNG

## ✏️ Types de Contenu Recommandés

### Excellents pour le Graphique

✅ **Icônes vectorielles**
- Lignes nettes
- Couleurs simples
- Bien reconnaissables

✅ **Logos**
- Version simplifiée
- Fond transparent
- Contraste élevé

✅ **Illustrations minimalistes**
- Style flat design
- Couleurs vives
- Contours nets

✅ **Photos détourées**
- Sujet centré
- Détourage propre
- Bien contrasté

### À Éviter

❌ **Photos avec fond flou** - Difficile à détourer proprement
❌ **Texte trop petit** - Illisible dans le graphique
❌ **Dégradés complexes** - Perte de qualité à petite taille
❌ **Trop de détails** - Se perdent à la réduction

## 📏 Vérification Avant Upload

### Checklist

- [ ] Format PNG (pas JPG)
- [ ] Fond transparent (canal alpha)
- [ ] Dimensions entre 150-400px
- [ ] Ratio carré ou proche du carré
- [ ] Contenu centré avec marges
- [ ] Taille de fichier < 100 Ko
- [ ] Test sur fond blanc ET fond noir
- [ ] Visible à petite taille (prévisualiser à 60px)

### Test Rapide

1. **Ouvrir l'image dans un navigateur**
2. **Zoom arrière à 25%**
3. **Questions :**
   - Le contenu est-il reconnaissable ?
   - Le fond est-il bien transparent ?
   - L'image est-elle centrée ?
   - Les bords sont-ils nets ?

## 🚀 Upload dans WordPress

1. **Aller dans l'éditeur du projet**
2. **Image mise en avant** (sidebar droite)
   - Cliquer sur "Définir l'image mise en avant"
   - Upload votre PNG transparent
3. **Paramètres du graphique** (sidebar droite)
   - ✅ Cocher "Afficher dans le graphique"
   - Ajuster la taille (60-200px pour projets)
4. **Publier / Mettre à jour**

## 🎨 Exemples de Bonnes Pratiques

### Projet Architectural

```
┌───────────────────┐
│   [marge 15px]    │
│                   │
│    🏗️            │  ← Icône de bâtiment
│   MAISON XYZ      │  ← Titre lisible
│                   │
│   [marge 15px]    │
└───────────────────┘
```

### Article Blog

```
┌───────────────────┐
│   [marge 20px]    │
│                   │
│      💡          │  ← Emoji ou icône
│                   │
│   [marge 20px]    │
└───────────────────┘
```

### Illustration

```
┌───────────────────┐
│                   │
│   🎨 [artwork]   │  ← Illustration complète
│                   │  centrée
│                   │
└───────────────────┘
```

## 💡 Conseils Pro

### Pour une Meilleure Visibilité

1. **Utiliser des couleurs vives** - Meilleur contraste dans le graphique
2. **Simplifier au maximum** - Plus lisible à petite taille
3. **Tester sur différentes tailles** - Vérifier à 60px, 120px, 200px
4. **Ajouter un léger contour** - Si l'image est très claire
5. **Optimiser le poids** - Utiliser TinyPNG.com si > 100 Ko

### Pour les Séries de Projets

- **Garder un style cohérent** - Même style graphique
- **Utiliser la même palette** - Harmonie visuelle
- **Dimensions identiques** - Meilleur alignement
- **Template réutilisable** - Gagne du temps

## 🆘 Dépannage

### L'image apparaît coupée
➡️ Vérifier que l'image source a des marges suffisantes
➡️ Recompiler les assets : `npm run build`
➡️ Vider le cache du navigateur (Ctrl+Shift+R)

### Le fond n'est pas transparent
➡️ Vérifier le format (doit être PNG, pas JPG)
➡️ Vérifier le canal alpha dans Photoshop/GIMP
➡️ Réexporter avec "Transparence" cochée

### L'image est floue
➡️ Augmenter la résolution source (min 150px)
➡️ Exporter en 2x (400px) puis laisser WordPress redimensionner
➡️ Vérifier la qualité d'export (100%)

### L'image est trop lourde
➡️ Utiliser TinyPNG.com pour compresser
➡️ Réduire les dimensions si > 400px
➡️ Simplifier les détails inutiles

## 📚 Ressources

### Outils Gratuits
- **remove.bg** - Enlever le fond automatiquement
- **Photopea** - Alternative gratuite à Photoshop
- **GIMP** - Logiciel de retouche gratuit
- **TinyPNG** - Compression PNG

### Banques d'Images
- **Unsplash** - Photos gratuites HD
- **Flaticon** - Icônes vectorielles
- **Undraw** - Illustrations SVG
- **The Noun Project** - Icônes simples

### Tutoriels
- YouTube : "How to create transparent PNG"
- YouTube : "Remove background Photoshop"
- Documentation WordPress : Images mises en avant

---

**Besoin d'aide ?** Consultez la documentation complète dans `docs/graph-png-transparent-images.md`
