# Guide Rapide : Images Pleine Page

## 🎯 En Bref

Créez des images spectaculaires pleine largeur comme sur le site Rivière Architecte avec 3 modes de hauteur : Normal (70vh), Pleine hauteur (100vh), ou Demi-hauteur (50vh).

---

## ⚡ Utilisation Rapide

### 1. Insérer le Bloc
- Cliquez **+** dans Gutenberg
- Cherchez **"Image Pleine Largeur"**
- Sélectionnez votre image

### 2. Choisir la Hauteur
**Barre latérale droite > Hauteur de l'image :**
- ☑️ **Normale (70vh)** - Image principale (défaut)
- ☑️ **Pleine hauteur (100vh)** - Impact maximal (hero)
- ☑️ **Demi-hauteur (50vh)** - Détails secondaires

### 3. Ajouter une Légende (optionnel)
- Zone "Légende" dans les paramètres
- S'affiche centrée sous l'image

### 4. Texte Alternatif (important)
- Champ "Texte alternatif"
- Décrivez l'image (SEO + accessibilité)

---

## 💡 Exemples d'Usage

### Scénario 1 : Article de Projet

```
[Titre + Introduction]
↓
Image Pleine Hauteur (100vh) - Vue principale spectaculaire
↓
[Texte centré - Description du projet]
↓
Image Normale (70vh) - Façade
↓
[Texte centré - Détails techniques]
↓
Image Demi-hauteur (50vh) - Plan
Image Demi-hauteur (50vh) - Coupe
↓
[Conclusion]
```

### Scénario 2 : Série Photos

```
Texte introductif
Image Normale (70vh) + légende
Texte explicatif
Image Normale (70vh) + légende
Texte explicatif
Image Normale (70vh) + légende
```

---

## 📱 Responsive

| Écran | Normal | Pleine | Demi |
|-------|--------|--------|------|
| Desktop | 70vh | 100vh | 50vh |
| Tablette | 60vh | 100vh | 45vh |
| Mobile | 50vh | 70vh | 40vh |

*Hauteurs minimales garanties sur tous les appareils*

---

## ✅ Bonnes Pratiques

**Format d'image :**
- Paysage 16:9 ou 21:9
- 1920x1080px minimum
- Optimisée (<300KB)

**Usage des modes :**
- **100vh** : Max 1-2 images par page
- **70vh** : 3-5 images principales
- **50vh** : Illimité pour détails

**Légendes :**
- Courtes (1-2 lignes)
- Contexte : lieu, date, crédit

---

## ⚠️ À Éviter

❌ Trop d'images 100vh (lourd visuellement)  
❌ Images portrait (déformation)  
❌ Poids >1MB (lenteur)  
❌ Oublier le texte alternatif

---

## 🎨 Classes CSS

```css
/* Image pleine largeur normale */
.archi-image-full-width img {
    height: 70vh;
}

/* Pleine hauteur */
.archi-image-full-width.full-viewport img {
    height: 100vh;
}

/* Demi-hauteur */
.archi-image-full-width.half-viewport img {
    height: 50vh;
}
```

---

## 📚 Documentation Complète

Voir : `docs/02-features/images-pleine-page.md`

---

**Inspiré par :** [Rivière Architecte](https://www.riviere-architecte.fr/maison-s/)  
**Mis à jour :** 8 novembre 2024
