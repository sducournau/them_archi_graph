# Mise à jour - Templates Simplifiés
**Date :** 8 novembre 2024  
**Version :** 1.0.0  
**Objectif :** Simplifier les templates d'articles, projets et illustrations en supprimant les sidebars et en adoptant un design centré épuré

---

## 📋 Résumé des Changements

Tous les templates de contenu unique (`single.php`, `single-archi_project.php`, `single-archi_illustration.php`) ont été simplifiés pour adopter un style moderne, épuré et centré, inspiré du site Rivière Architecte :
- ✅ **Suppression totale des sidebars**
- ✅ **Contenu centré** avec max-width: 800px
- ✅ **Intégration des métadonnées** comme blocs centrés (plus de colonnes latérales)
- ✅ **Section "Articles/Projets/Illustrations Similaires" simplifiée** avec grille 3 colonnes
- ✅ **Hero fullscreen** conservé pour l'impact visuel
- ✅ **Responsive design** optimisé (desktop 3 colonnes → tablette 2 colonnes → mobile 1 colonne)

---

## 📝 Fichiers Modifiés

### 1. Templates PHP Simplifiés

#### `single.php` (Articles de blog)
**Taille :** 5.3K  
**Changements :**
- ❌ Supprimé : Sidebar avec widgets, métadonnées complexes, navigation article précédent/suivant, section commentaires
- ✅ Ajouté : Header simple `.article-header-simple` pour articles sans image
- ✅ Ajouté : Section `.related-articles-simple` avec grille 3 colonnes
- ✅ Conservé : Hero fullscreen, contenu centré `.article-content`, catégories

**Structure finale :**
```
Hero Fullscreen (si image à la une)
  └─ Overlay + Titre + Catégories + Indicateur scroll
OU Header Simple (si pas d'image)
  └─ Titre + Catégories
Contenu Centré
  └─ the_content() + Pagination
Articles Similaires
  └─ Grille 3 colonnes (même catégorie)
```

#### `single-archi_project.php` (Projets architecturaux)
**Taille :** 9.1K  
**Changements :**
- ❌ Supprimé : Sidebar entière avec projet-info-card, project-details (technique, coût, client, etc.), project-tags, action-buttons
- ✅ Ajouté : `.project-specs-grid` intégré dans le contenu centré
- ✅ Ajouté : `.related-projects-simple` avec grille 3 colonnes
- ✅ Conservé : Hero fullscreen, taxonomies (type de projet, statut)

**Spécifications affichées (project-specs-grid) :**
- 📍 Localisation (`_archi_project_location`)
- 📅 Année (`_archi_project_year`)
- 👤 Client (`_archi_project_client`)
- 💰 Coût (`_archi_project_cost`)
- 📐 Surface (`_archi_project_surface`)
- 🏗️ Statut projet (`archi_project_status` taxonomy)

**Structure finale :**
```
Hero Fullscreen (si image à la une)
  └─ Overlay + Titre + Type de projet + Statut + Indicateur scroll
OU Header Simple (si pas d'image)
  └─ Titre + Type de projet + Statut
Contenu Centré
  └─ the_content() + Pagination
Specs Grid (intégré)
  └─ Localisation, Année, Client, Coût, Surface, Statut
Projets Similaires
  └─ Grille 3 colonnes (même type de projet)
```

#### `single-archi_illustration.php` (Illustrations)
**Taille :** 7.3K (réduit de 21K → 7.3K, gain de 66%)  
**Changements :**
- ❌ Supprimé : Sidebar entière avec illustration-info-card, illustration-details (technique, dimensions, logiciels), illustration-tags, action-buttons (download, share)
- ✅ Ajouté : `.project-specs-grid` pour afficher technique, dimensions, logiciels
- ✅ Ajouté : `.related-illustrations-simple` avec grille 3 colonnes
- ✅ Conservé : Hero fullscreen, taxonomie illustration_type

**Spécifications affichées (project-specs-grid) :**
- 🎨 Technique (`_archi_illustration_technique`)
- 📏 Dimensions (`_archi_illustration_dimensions`)
- 💻 Logiciels (`_archi_illustration_software`)

**Structure finale :**
```
Hero Fullscreen (si image à la une)
  └─ Overlay + Titre + Type d'illustration + Indicateur scroll
OU Header Simple (si pas d'image)
  └─ Titre + Type d'illustration
Contenu Centré
  └─ the_content() + Pagination
Specs Grid (intégré)
  └─ Technique, Dimensions, Logiciels
Illustrations Similaires
  └─ Grille 3 colonnes (même type d'illustration)
```

**Note :** Backup créé : `single-archi_illustration.php.backup`

---

### 2. Nouveaux Fichiers CSS

#### `assets/css/simplified-templates.css`
**Taille :** 7.4K  
**Description :** Styles pour les nouveaux composants des templates simplifiés

**Sections principales :**

##### Headers Simples (sans image)
```css
.article-header-simple
.project-header-simple
.illustration-header-simple
  └─ Background gradient + bordure + centré
  └─ .article-title-simple (2.5rem, bold)
  └─ .category-badge-simple (badges bleus, uppercase)
```

##### Grille de Spécifications
```css
.project-specs-grid
  └─ Grid auto-fit minmax(250px, 1fr)
  └─ Background #f8f9fa + bordure bleue gauche
  └─ .spec-item > .spec-label + .spec-value
```

##### Articles/Projets/Illustrations Similaires
```css
.related-articles-simple
.related-projects-simple
.related-illustrations-simple
  └─ .related-title-simple (titre centré)
  └─ .related-grid-simple (grid 3 colonnes)
      └─ .related-card-simple
          └─ .related-image-simple (240px hauteur)
          └─ .related-content-simple
              └─ .related-card-title-simple
```

**Effets interactifs :**
- ✅ Hover : Carte remonte de 8px + ombre agrandie
- ✅ Hover : Image zoom 1.08x
- ✅ Hover : Titre change de couleur (#2c3e50 → #3498db)
- ✅ Focus : Outline bleu pour accessibilité

**Responsive :**
- 📱 **Mobile (≤480px) :** 1 colonne, hauteur image 180px
- 📱 **Tablette (≤768px) :** 2 colonnes, hauteur image 200px
- 🖥️ **Desktop (>768px) :** 3 colonnes, hauteur image 240px

---

### 3. Fichiers Modifiés

#### `functions.php`
**Ligne ajoutée (~138) :**
```php
// Simplified templates styles (related articles, specs grid, simple headers)
wp_enqueue_style(
    'archi-simplified-templates',
    ARCHI_THEME_URI . '/assets/css/simplified-templates.css',
    [],
    ARCHI_THEME_VERSION
);
```

**Effet :** Charge `simplified-templates.css` globalement sur toutes les pages

---

## 🎨 Classes CSS Principales

### Headers (sans image à la une)
```html
<header class="article-header-simple">
    <h1 class="article-title-simple">Titre</h1>
    <div class="article-categories-simple">
        <span class="category-badge-simple">Catégorie</span>
    </div>
</header>
```

### Grille de Spécifications
```html
<div class="project-specs-grid">
    <div class="spec-item">
        <div class="spec-label">Localisation :</div>
        <div class="spec-value">Paris, France</div>
    </div>
    <div class="spec-item">
        <div class="spec-label">Surface :</div>
        <div class="spec-value">150 m²</div>
    </div>
</div>
```

### Section Articles Similaires
```html
<aside class="related-articles-simple">
    <h2 class="related-title-simple">Articles Similaires</h2>
    <div class="related-grid-simple">
        <article class="related-card-simple">
            <a href="..." class="related-link-simple">
                <div class="related-image-simple">
                    <img src="..." alt="...">
                </div>
                <div class="related-content-simple">
                    <h3 class="related-card-title-simple">Titre</h3>
                </div>
            </a>
        </article>
    </div>
</aside>
```

---

## 🔄 Migration et Compatibilité

### Contenu Existant
✅ **Totalement compatible** - Aucune modification requise dans l'éditeur Gutenberg  
✅ Les blocs images existants (Image Full Width, Images in Columns, Image Portrait) continuent de fonctionner  
✅ Les métadonnées (_archi_project_*, _archi_illustration_*) restent inchangées

### Anciens Templates
⚠️ **Backup automatique créé :** `single-archi_illustration.php.backup`  
💡 **Conseil :** Si besoin de revenir en arrière, restaurer depuis le backup

### Cache WordPress
🔧 **Action recommandée après déploiement :**
```bash
# Vider le cache WordPress
wp cache flush

# Ou depuis l'admin WordPress :
# Réglages → Permaliens → Enregistrer (sans rien changer)
```

---

## 📱 Responsive Design

| Appareil | Breakpoint | Grille Related | Hauteur Image | Titre |
|----------|-----------|----------------|---------------|--------|
| 🖥️ Desktop | >768px | 3 colonnes | 240px | 1.125rem |
| 📱 Tablette | ≤768px | 2 colonnes | 200px | 1rem |
| 📱 Mobile | ≤480px | 1 colonne | 180px | 0.9375rem |

---

## 🧪 Tests Recommandés

### 1. Test Visuel
- [ ] Article avec image à la une → Hero fullscreen affiché
- [ ] Article sans image → Header simple affiché
- [ ] Projet avec toutes les métadonnées → Specs grid complet
- [ ] Illustration avec technique/dimensions/software → Specs grid affiché
- [ ] Articles similaires affichés (3 maximum)
- [ ] Hover sur cartes similaires → Effets d'animation

### 2. Test Responsive
- [ ] Desktop (1920px) → 3 colonnes
- [ ] Tablette (768px) → 2 colonnes
- [ ] Mobile (375px) → 1 colonne
- [ ] Rotation paysage/portrait

### 3. Test Accessibilité
- [ ] Navigation au clavier (Tab) sur les cartes similaires
- [ ] Outline visible au focus
- [ ] Balises alt sur les images
- [ ] Contraste couleurs suffisant (WCAG AA)

### 4. Test Performance
- [ ] Temps de chargement < 2 secondes
- [ ] Images optimisées (WebP si possible)
- [ ] CSS minifié en production

---

## 🐛 Problèmes Connus et Solutions

### Problème : Articles similaires vides
**Cause :** Aucun article dans la même catégorie/taxonomie  
**Solution :** Ajouter des articles dans les mêmes catégories/types

### Problème : Specs grid vide
**Cause :** Métadonnées non renseignées  
**Solution :** Remplir les champs personnalisés dans l'admin WordPress

### Problème : Hero ne s'affiche pas
**Cause :** Pas d'image à la une définie  
**Solution :** Définir une image à la une OU le header simple s'affiche automatiquement

---

## 📚 Documentation Complémentaire

### Fichiers de référence
- `docs/02-features/blocs-images-centrees.md` - Documentation technique des blocs Gutenberg
- `docs/02-features/guide-rapide-blocs-images.md` - Guide rapide utilisateur
- `CENTERED-CONTENT-UPDATE.md` - Mise à jour du système de contenu centré

### Styles CSS liés
- `assets/css/centered-content.css` - Système de contenu centré (max-width: 800px)
- `assets/css/simplified-templates.css` - Styles des templates simplifiés (ce document)
- `assets/css/hero-fullscreen-scroll.css` - Styles du hero fullscreen

---

## 🎯 Prochaines Étapes

### Court terme
- [ ] Tester en production avec du contenu réel
- [ ] Vérifier les performances sur mobile
- [ ] Optimiser les images des articles similaires (lazy loading)

### Moyen terme
- [ ] Ajouter un système de filtres pour les articles similaires
- [ ] Implémenter le partage social simplifié
- [ ] Créer des variantes de cartes (avec/sans date, avec/sans extrait)

### Long terme
- [ ] Migration vers Full Site Editing (FSE)
- [ ] Création de patterns Gutenberg pour les sections similaires
- [ ] Système de recommandations intelligent basé sur l'IA

---

## 👥 Contribution

Pour toute suggestion d'amélioration ou bug report :
1. Créer une issue sur le repository
2. Décrire le problème avec captures d'écran
3. Indiquer le navigateur/appareil concerné
4. Proposer une solution si possible

---

**Auteur :** GitHub Copilot  
**Licence :** Héritée du thème parent  
**Support :** Via documentation et issues GitHub
