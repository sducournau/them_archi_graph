# Guide de Personnalisation - Archi Graph Theme

Ce guide explique comment personnaliser l'apparence et les métadonnées de votre site utilisant le thème Archi Graph.

## Table des Matières

1. [Configuration de Base](#configuration-de-base)
2. [Personnalisation Visuelle](#personnalisation-visuelle)
3. [SEO et Métadonnées](#seo-et-métadonnées)
4. [Réseaux Sociaux](#réseaux-sociaux)
5. [Footer](#footer)
6. [Favicon et Logo](#favicon-et-logo)

---

## Configuration de Base

### Accéder aux Paramètres

1. Dans votre administration WordPress, allez dans **Apparence > Graphique Archi**
2. Vous trouverez plusieurs sections de personnalisation

### Description du Site

La description du site est utilisée pour :

- Les meta tags de description
- Les aperçus sur les réseaux sociaux (Open Graph)
- Le référencement SEO

**Emplacement:** `Personnalisation du Site > Description du site`

**Exemple:**

```
Découvrez une architecture de contenu interactive avec notre graphique de connaissances visualisant les relations entre les articles.
```

---

## Personnalisation Visuelle

### Couleur du Thème

Cette couleur est utilisée par les navigateurs mobiles pour colorer la barre d'adresse.

**Emplacement:** `Personnalisation du Site > Couleur du thème`

**Usage:**

```html
<meta name="theme-color" content="#667eea" />
```

### Logo Personnalisé

Le thème supporte les logos personnalisés via le système WordPress natif.

**Configuration:**

1. Allez dans **Apparence > Personnaliser > Identité du site**
2. Cliquez sur **Sélectionner un logo**
3. Téléchargez votre logo (format recommandé: PNG avec fond transparent)
4. Dimensions recommandées: 400x100px (flexible)

---

## SEO et Métadonnées

### Favicon

Le favicon est l'icône qui apparaît dans l'onglet du navigateur.

**Emplacement:** `Personnalisation du Site > Favicon`

**Spécifications:**

- Format: PNG, ICO ou SVG
- Taille recommandée: 32x32px ou 64x64px
- Fond transparent recommandé

**Code généré:**

```html
<link rel="icon" type="image/png" href="votre-favicon.png" />
<link rel="apple-touch-icon" href="votre-favicon.png" />
```

### Image Open Graph (Réseaux Sociaux)

Cette image est affichée lorsque votre site est partagé sur les réseaux sociaux.

**Emplacement:** `Personnalisation du Site > Image par défaut (Open Graph)`

**Spécifications:**

- Format: JPG ou PNG
- Dimensions: 1200x630px (recommandé Facebook/LinkedIn)
- Poids: < 8MB
- Texte visible et lisible même en miniature

**Note:** Si un article a une image à la une, celle-ci sera utilisée en priorité.

### Meta Description

Définie automatiquement selon le contexte :

- **Page d'accueil:** Utilise la description du site
- **Articles/Pages:** Utilise l'extrait ou les premiers mots du contenu

**Code généré:**

```html
<meta name="description" content="Description de la page" />
<meta property="og:description" content="Description de la page" />
<meta name="twitter:description" content="Description de la page" />
```

---

## Réseaux Sociaux

### Configuration Twitter/X

**Identifiant Twitter:**
Utilisé pour les Twitter Cards et l'attribution.

**Emplacement:** `Réseaux Sociaux > Identifiant Twitter`

**Format:** Sans le @ (ex: `votrenom` et non `@votrenom`)

**Type de Twitter Card:**

- **Résumé:** Petite image carrée
- **Résumé avec grande image:** Image rectangulaire large (recommandé)

**Code généré:**

```html
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="@votrenom" />
<meta name="twitter:creator" content="@votrenom" />
```

### Liens vers les Réseaux Sociaux

Ajoutez les liens vers vos profils sociaux :

**Plateformes supportées:**

- Facebook
- Twitter / X
- LinkedIn
- Instagram
- GitHub
- YouTube

**Emplacement:** `Réseaux Sociaux > [Nom du réseau]`

**Format:** URL complète (ex: `https://twitter.com/votrenom`)

**Affichage:**
Les icônes apparaissent dans le footer avec :

- Icônes SVG vectorielles
- Effets de survol
- Ouverture dans un nouvel onglet
- Attributs `rel="noopener noreferrer"` pour la sécurité

---

## Footer

### Texte Personnalisé

Personnalisez le texte affiché dans le pied de page.

**Emplacement:** `Personnalisation du Site > Texte du pied de page`

**Par défaut:**

```
© 2025 Nom du Site. Tous droits réservés.
```

**Exemple de personnalisation:**

```html
© 2025 <strong>Archi Graph</strong>. Créé avec passion.
<br />
<a href="/mentions-legales">Mentions légales</a> |
<a href="/confidentialite">Confidentialité</a>
```

**Note:** Le HTML basique est autorisé (liens, gras, italique, sauts de ligne).

### Menu Footer

Ajoutez un menu de navigation dans le footer :

**Configuration:**

1. Allez dans **Apparence > Menus**
2. Créez un nouveau menu ou modifiez-en un existant
3. Dans "Réglages du menu", cochez **Menu Pied de page**
4. Ajoutez vos liens (pages, articles, liens personnalisés)

**Style:** Liens horizontaux centrés

### Widgets Footer

Zone de widgets disponible dans le footer :

**Configuration:**

1. Allez dans **Apparence > Widgets**
2. Trouvez la zone **Footer**
3. Glissez-déposez vos widgets

**Widgets recommandés:**

- Texte personnalisé
- Derniers articles
- Liens utiles
- Formulaire de newsletter

---

## Favicon et Logo

### Structure de l'En-tête

Le header affiche automatiquement :

**Si un logo personnalisé existe:**

```html
<div class="site-logo">
  <a href="/">
    <img src="votre-logo.png" alt="Nom du site" />
  </a>
</div>
```

**Sinon:**

```html
<h1 class="site-title">
  <a href="/">Nom du Site</a>
</h1>
```

---

## Meta Tags Générés

### Structure Complète

Le thème génère automatiquement tous les meta tags nécessaires :

```html
<!-- Meta Description -->
<meta name="description" content="..." />

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website" />
<meta property="og:url" content="..." />
<meta property="og:title" content="..." />
<meta property="og:description" content="..." />
<meta property="og:image" content="..." />
<meta property="og:site_name" content="..." />

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:url" content="..." />
<meta name="twitter:title" content="..." />
<meta name="twitter:description" content="..." />
<meta name="twitter:image" content="..." />
<meta name="twitter:site" content="@..." />

<!-- Favicon -->
<link rel="icon" type="image/png" href="..." />
<link rel="apple-touch-icon" href="..." />

<!-- Theme Color -->
<meta name="theme-color" content="#667eea" />
```

### Fonction PHP de Génération

La fonction `archi_render_meta_tags()` dans `functions.php` gère :

- Détection automatique du contexte (accueil, article, page)
- Utilisation intelligente des images (featured image en priorité)
- Nettoyage et échappement de toutes les données
- Compatibilité avec tous les réseaux sociaux

---

## Bonnes Pratiques

### Images

**Favicon:**

- Utilisez un design simple et reconnaissable
- Testez sur fond clair et sombre
- Format PNG avec transparence recommandé

**Open Graph Image:**

- Incluez du texte lisible (taille min: 40px)
- Évitez les détails trop fins
- Testez avec l'outil [Facebook Debugger](https://developers.facebook.com/tools/debug/)
- Testez avec [Twitter Card Validator](https://cards-dev.twitter.com/validator)

### SEO

**Description:**

- Longueur idéale: 150-160 caractères
- Incluez vos mots-clés principaux
- Soyez descriptif et attractif
- Évitez le keyword stuffing

### Réseaux Sociaux

**Cohérence:**

- Utilisez les mêmes identifiants sur toutes les plateformes si possible
- Vérifiez que tous les liens fonctionnent
- Mettez à jour régulièrement

**Sécurité:**

- Le thème ajoute automatiquement `rel="noopener noreferrer"`
- Tous les liens externes s'ouvrent dans un nouvel onglet

---

## Testez Votre Configuration

### Outils de Test

**SEO et Meta Tags:**

- [Google Rich Results Test](https://search.google.com/test/rich-results)
- [Screaming Frog SEO Spider](https://www.screamingfrog.co.uk/seo-spider/)

**Réseaux Sociaux:**

- [Facebook Sharing Debugger](https://developers.facebook.com/tools/debug/)
- [Twitter Card Validator](https://cards-dev.twitter.com/validator)
- [LinkedIn Post Inspector](https://www.linkedin.com/post-inspector/)

**Performance:**

- [Google PageSpeed Insights](https://pagespeed.web.dev/)
- [GTmetrix](https://gtmetrix.com/)

---

## Support et Personnalisation Avancée

### Hooks Disponibles

Le thème offre plusieurs hooks pour personnalisation avancée :

**Filtres:**

```php
// Modifier la description meta
add_filter('archi_meta_description', function($description) {
    return $description . ' - Votre texte personnalisé';
});

// Modifier les liens sociaux
add_filter('archi_social_links', function($links) {
    $links['custom'] = 'https://exemple.com';
    return $links;
});
```

**Actions:**

```php
// Ajouter du contenu dans le footer
add_action('archi_footer_content', function() {
    echo '<div class="custom-footer">Contenu personnalisé</div>';
});
```

### Fichiers à Modifier

**Styles du footer:** `/assets/css/footer.css`
**Meta tags:** `functions.php` - fonction `archi_render_meta_tags()`
**Template footer:** `footer.php`
**Template header:** `header.php`

---

## Changelog

### Version 1.0.0

- ✅ Support complet des meta tags (SEO, Open Graph, Twitter Cards)
- ✅ Gestion du favicon et logo personnalisé
- ✅ Intégration réseaux sociaux (6 plateformes)
- ✅ Footer personnalisable avec widgets et menus
- ✅ Couleur de thème pour navigateurs mobiles
- ✅ Interface d'administration complète

---

## Questions Fréquentes

**Q: Puis-je utiliser plusieurs images Open Graph différentes ?**
R: Oui, chaque article peut avoir sa propre image via l'image à la une.

**Q: Le favicon n'apparaît pas ?**
R: Videz le cache de votre navigateur (Ctrl+F5) et rechargez la page.

**Q: Comment désactiver les liens sociaux ?**
R: Décochez "Afficher les liens sociaux" dans les paramètres.

**Q: Puis-je ajouter d'autres réseaux sociaux ?**
R: Oui, modifiez la fonction `archi_render_social_links()` dans `functions.php`.

---

Pour plus d'informations, consultez les autres documents de la documentation :

- [README.md](../README.md) - Vue d'ensemble du thème
- [setup.md](setup.md) - Installation et configuration
- [features.md](features.md) - Fonctionnalités complètes
