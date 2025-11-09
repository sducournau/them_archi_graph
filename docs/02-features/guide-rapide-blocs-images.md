# Guide Rapide : Utilisation des Nouveaux Blocs d'Images

## 🎯 Comment Créer un Article dans le Style Rivière Architecte

### Étape 1 : Créer l'Article

1. WordPress Admin → Articles → Ajouter
2. Ajoutez votre titre
3. Définissez une image à la une (pour le hero fullscreen)

### Étape 2 : Structure de Base

#### Texte d'Introduction (Automatiquement Centré)

Tapez votre introduction directement. Le texte sera automatiquement centré avec une largeur maximale de 800px.

```
Exemple :
"Ce projet de rénovation d'un ancien corps de ferme transforme un bâtiment 
en ruine en une maison de vacances moderne et lumineuse..."
```

### Étape 3 : Ajouter une Image Pleine Largeur

1. Cliquez sur le `+` pour ajouter un bloc
2. Tapez "Image Pleine" dans la recherche
3. Sélectionnez **"Image Pleine Largeur"**
4. Choisissez votre image (min 1920px de large recommandé)
5. Dans le panneau de droite :
   - Ajoutez le **texte alternatif** (ex: "Vue extérieure de la maison")
   - Ajoutez une **légende** (ex: "Façade sud après rénovation")

**Résultat :** L'image s'étendra sur toute la largeur de l'écran.

### Étape 4 : Continuer avec du Texte

Ajoutez un paragraphe de texte normal après l'image. Il sera automatiquement centré.

### Étape 5 : Ajouter des Images en Colonnes

#### Option A : 2 Colonnes (Recommandé pour détails)

1. Ajoutez le bloc **"Images en Colonnes"**
2. Dans le panneau de droite, sélectionnez **2 colonnes**
3. Cliquez sur "Sélectionner les images"
4. Choisissez 2 images (Ctrl+Clic pour sélection multiple)
5. Ajoutez des légendes individuelles :
   - Image 1 : "Salon avec vue panoramique"
   - Image 2 : "Cuisine ouverte sur la salle à manger"

**Images recommandées :** Format paysage 4:3

#### Option B : 3 Colonnes (Idéal pour détails techniques)

1. Bloc **"Images en Colonnes"**
2. Panneau de droite : **3 colonnes**
3. Sélectionnez 3 images
4. Ajoutez des légendes

**Images recommandées :** Format carré 1:1

### Étape 6 : Image Portrait (Optionnel)

Pour les images verticales (plans, coupes, photos de détails) :

1. Bloc **"Image Portrait"**
2. Sélectionnez votre image verticale
3. L'image sera centrée avec max-width: 600px

## 📋 Template Complet Exemple

```
[Image à la Une - Hero Fullscreen automatique]

# Titre du Projet (H1)

## Réhabilitation et rénovation (H2)

Paragraphe d'introduction : Cet ancien bâtiment tombait en ruine lorsque 
la famille décide de le reprendre pour faire de cette ancienne ferme une 
maison de vacances...

[Image Pleine Largeur]
Légende : Vue d'ensemble du projet

Suite du texte décrivant le concept architectural...

### Les espaces de vie (H3)

Description des espaces...

[Images en Colonnes - 2 colonnes]
Image 1 : Salon | Image 2 : Cuisine

Texte sur les matériaux utilisés...

[Images en Colonnes - 3 colonnes]
Image 1 : Détail fenêtre | Image 2 : Détail escalier | Image 3 : Détail sol

Conclusion du projet...
```

## 💡 Conseils d'Utilisation

### Pour les Textes

- **Longueur idéale des paragraphes** : 3-5 lignes
- **Utilisez des titres H2 et H3** pour structurer
- **Justification** : Le texte est justifié automatiquement

### Pour les Images

**Image Pleine Largeur :**
- ✅ Photos panoramiques
- ✅ Vues d'ensemble du projet
- ✅ Photos d'ambiance
- Résolution minimum : 1920px de large

**Images en 2 Colonnes :**
- ✅ Avant/Après
- ✅ Intérieur/Extérieur
- ✅ Jour/Nuit
- Format recommandé : 4:3 (paysage)

**Images en 3 Colonnes :**
- ✅ Détails techniques
- ✅ Matériaux
- ✅ Série thématique
- Format recommandé : 1:1 (carré)

**Image Portrait :**
- ✅ Plans verticaux
- ✅ Coupes de bâtiment
- ✅ Photos de détails verticaux
- Format : Vertical (2:3 ou 9:16)

## ⚙️ Paramètres Disponibles

### Pour Toutes les Images

- **Texte alternatif** : Important pour l'accessibilité et le SEO
- **Légende** : Texte descriptif sous l'image

### Pour Images en Colonnes

- **Nombre de colonnes** : 2 ou 3
- **Légendes individuelles** : Une par image
- **Ordre des images** : Définissable à la sélection

## 🎨 Personnalisation Avancée

Si vous souhaitez modifier l'apparence :

### Changer la largeur du contenu centré

Fichier : `assets/css/centered-content.css`

```css
.article-content {
    max-width: 800px; /* Modifier cette valeur */
}
```

### Changer l'espacement entre les colonnes

```css
.archi-images-columns-2 {
    gap: 20px; /* Modifier l'espace entre les images */
}
```

## 📱 Comportement Mobile

Le système s'adapte automatiquement :

- **Texte** : Reste centré avec padding réduit
- **Images Pleine Largeur** : Conservent leur pleine largeur
- **2 Colonnes** : Deviennent 1 colonne sur mobile
- **3 Colonnes** : Deviennent 2 colonnes sur tablette, 1 sur mobile

## ❓ Questions Fréquentes

### Les blocs n'apparaissent pas ?

1. Vérifiez que webpack a compilé : `npm run build`
2. Videz le cache WordPress
3. Rechargez l'éditeur (F5)

### Images floues ?

Uploadez des images en haute résolution :
- Pleine largeur : min 1920px
- Colonnes : min 800px par image
- Format WebP recommandé pour performance

### Modifier une image déjà insérée ?

1. Cliquez sur le bloc image
2. Dans la barre d'outils, cliquez "Remplacer l'image"
3. Ou dans le panneau de droite, modifiez les paramètres

### Supprimer un bloc ?

1. Sélectionnez le bloc
2. Cliquez sur les 3 points verticaux (⋮)
3. Choisissez "Supprimer le bloc"
4. Ou appuyez sur Suppr/Delete

## 🚀 Raccourcis Clavier Utiles

- `/ + "image"` : Recherche rapide de blocs
- `Ctrl + Z` : Annuler
- `Ctrl + Shift + Z` : Rétablir
- `Alt + F10` : Focus sur la barre d'outils
- `Ctrl + S` : Sauvegarder

## ✅ Checklist Avant Publication

- [ ] Image à la une définie (pour le hero)
- [ ] Textes alternatifs ajoutés sur toutes les images
- [ ] Légendes pertinentes
- [ ] Titres H2/H3 pour la structure
- [ ] Preview sur mobile/tablette
- [ ] Vérification des images (qualité/poids)
- [ ] Catégories et tags définis
- [ ] Extrait rédigé (optionnel)

## 📧 Support

Pour toute question ou problème, consultez la documentation complète :
`docs/02-features/blocs-images-centrees.md`
