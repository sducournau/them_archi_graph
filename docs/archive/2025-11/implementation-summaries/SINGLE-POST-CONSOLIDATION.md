# Consolidation et Harmonisation des Templates d'Articles

**Date :** 10 novembre 2025  
**Statut :** ✅ Complété

## 🎯 Objectif

Simplifier et harmoniser l'affichage de tous les types d'articles (posts standards, projets architecturaux, illustrations, livre d'or) en utilisant un seul template unifié au lieu de templates séparés redondants.

## 🔄 Changements Réalisés

### 1. Nouveau Système de Helpers (`inc/single-post-helpers.php`)

Création d'un fichier centralisé contenant des fonctions réutilisables :

#### **`archi_get_post_metadata($post_id)`**
Récupère intelligemment les métadonnées selon le type de post :
- **Articles standards** : Pas de métadonnées spécifiques
- **Projets (`archi_project`)** : Localisation, Année, Maître d'ouvrage, Coût, Surface
- **Illustrations (`archi_illustration`)** : Technique, Dimensions, Logiciels
- **Livre d'or (`archi_guestbook`)** : Auteur, Organisation, Email

**Retour :** Tableau associatif structuré avec `label`, `value`, et `icon` (dashicon)

#### **`archi_display_post_metadata($post_id)`**
Affiche les métadonnées dans une grille responsive `.archi-specs-grid`

#### **`archi_get_related_posts($post_id, $count = 3)`**
Récupère les articles similaires intelligemment :
1. **Relations manuelles** d'abord (via `_archi_related_articles`)
2. **Relations automatiques** selon le type :
   - Projets : Même `archi_project_type`
   - Illustrations : Même `illustration_type`
   - Livre d'or : Articles liés via `_archi_guestbook_linked_articles`
   - Posts : Même catégorie

#### **`archi_display_related_posts($post_id, $count = 3)`**
Affiche les articles similaires dans une grille moderne avec :
- Badge du type de post (Projet, Illustration, Article, etc.)
- Image à la une avec effet hover
- Titre cliquable
- Métadonnée contextuelle (ex: localisation pour projets)

#### **`archi_get_post_type_label($post_type)`**
Retourne le nom d'affichage localisé du type de post

### 2. Template Unifié (`single.php`)

**Ancien système :**
- `single.php` pour articles standards
- `single-archi_project.php` pour projets
- `single-archi_illustration.php` pour illustrations
- `single-archi_guestbook.php` conservé (cas spécifique)

**Nouveau système :**
- **Un seul fichier** `single.php` gère tous les types
- Détection automatique du type de post
- Classes CSS dynamiques : `.archi-single-container`, `.archi-single-{post_type}`
- Hooks personnalisables : `archi_before_single_content`, `archi_after_single_content`

**Structure du template :**
```php
<div class="archi-single-container archi-single-{post_type}">
    <article class="archi-single-article">
        <div class="archi-content-section">
            <!-- Hook : archi_before_single_content -->
            
            <!-- Contenu principal -->
            <div class="archi-article-content">
                <?php the_content(); ?>
            </div>
            
            <!-- Pagination -->
            <div class="archi-page-links">...</div>
            
            <!-- Métadonnées spécifiques au type -->
            <?php archi_display_post_metadata(); ?>
            
            <!-- Hook : archi_after_single_content -->
            
            <!-- Articles similaires -->
            <?php archi_display_related_posts(); ?>
        </div>
    </article>
</div>
```

### 3. Styles Harmonisés (`assets/css/single-post.css`)

Nouveau fichier CSS unifié avec :

#### **Variables de couleur par type de post :**
- **Articles standards** : Bleu `#3498db`
- **Projets** : Rouge `#e74c3c`
- **Illustrations** : Violet `#9b59b6`
- **Livre d'or** : Vert `#27ae60`

#### **Composants stylisés :**
- **`.archi-specs-grid`** : Grille responsive pour métadonnées
  - Bordure gauche colorée selon le type de post
  - Icons dashicons colorés
  - Layout adaptatif (auto-fit, min 250px)

- **`.archi-related-section`** : Section articles similaires
  - Grille responsive (auto-fit, min 280px)
  - Cards avec effet hover (transform + shadow)
  - Badge de type de post
  - Images avec zoom au survol

- **`.archi-page-links`** : Pagination améliorée
  - Numéros de page cliquables avec hover effects

#### **Responsive Design :**
- **Mobile** (max-width: 768px) : 1 colonne, padding réduit
- **Petit mobile** (max-width: 480px) : Tailles de police ajustées

#### **Mode sombre :**
- Support `@media (prefers-color-scheme: dark)`
- Palette de couleurs inversée

#### **Animations :**
- Fade-in au chargement (`.archi-single-article`)
- Décalage progressif des cards (`:nth-child` delays)

### 4. Fichiers Supprimés

✅ **Supprimés avec succès :**
- `single-archi_project.php` (77 lignes → consolidé)
- `single-archi_illustration.php` (72 lignes → consolidé)

**Conservé :**
- `single-archi_guestbook.php` (logique spécifique du livre d'or préservée)

### 5. Intégration dans `functions.php`

```php
// Ajout de l'include
require_once ARCHI_THEME_DIR . '/inc/single-post-helpers.php';

// Enqueue des styles unifiés
if (is_single()) {
    wp_enqueue_style(
        'archi-single-post',
        ARCHI_THEME_URI . '/assets/css/single-post.css',
        [],
        ARCHI_THEME_VERSION
    );
}
```

## 📊 Bénéfices

### ✅ Réduction de la duplication
- **Avant :** 3 templates similaires (~220 lignes de code dupliqué)
- **Après :** 1 template + 1 fichier de helpers (code unique et réutilisable)

### ✅ Maintenabilité
- Modifications centralisées dans `inc/single-post-helpers.php`
- Ajout d'un nouveau type de post : une seule fonction à modifier
- Styles harmonisés dans un seul fichier CSS

### ✅ Cohérence
- Même mise en page pour tous les types d'articles
- Logique unifiée pour les articles similaires
- Design system cohérent avec variantes par type

### ✅ Extensibilité
- Hooks WordPress standard : `archi_before_single_content`, `archi_after_single_content`
- Filtres personnalisables : `archi_post_metadata`, `archi_related_posts`
- Classes CSS modulaires pour personnalisation

## 🎨 Architecture des Styles

```
.archi-single-container               → Container principal
  └─ .archi-single-{post_type}        → Variante par type de post
      └─ .archi-single-article        → Conteneur de l'article
          └─ .archi-content-section   → Section centrée (max-width: 900px)
              ├─ .archi-article-content     → Contenu principal
              ├─ .archi-page-links          → Pagination
              ├─ .archi-specs-grid          → Métadonnées
              │   └─ .spec-item
              │       ├─ .spec-label
              │       └─ .spec-value
              └─ .archi-related-section     → Articles similaires
                  ├─ .archi-related-title
                  └─ .archi-related-grid
                      └─ .archi-related-card
                          ├─ .archi-related-image
                          └─ .archi-related-content
                              ├─ .archi-post-type-badge
                              ├─ .archi-related-card-title
                              └─ .archi-related-meta
```

## 🔧 Utilisation des Fonctions Helper

### Exemple : Afficher les métadonnées

```php
// Dans un template personnalisé
<?php archi_display_post_metadata(get_the_ID()); ?>

// Ou récupérer les données brutes
<?php 
$metadata = archi_get_post_metadata(get_the_ID());
foreach ($metadata as $meta) {
    echo $meta['label'] . ': ' . $meta['value'];
}
?>
```

### Exemple : Articles similaires personnalisés

```php
// Afficher 5 articles similaires au lieu de 3
<?php archi_display_related_posts(get_the_ID(), 5); ?>

// Récupérer les données sans affichage
<?php 
$related = archi_get_related_posts(get_the_ID(), 3);
foreach ($related as $post) {
    // Affichage personnalisé
}
?>
```

### Exemple : Filtrer les métadonnées

```php
// Dans functions.php ou un plugin
add_filter('archi_post_metadata', function($metadata, $post_id, $post_type) {
    if ($post_type === 'archi_project') {
        // Ajouter une métadonnée personnalisée
        $metadata[] = [
            'label' => __('Certification', 'archi-graph'),
            'value' => get_post_meta($post_id, '_custom_certification', true),
            'icon' => 'awards'
        ];
    }
    return $metadata;
}, 10, 3);
```

### Exemple : Modifier les articles similaires

```php
// Dans functions.php ou un plugin
add_filter('archi_related_posts', function($related, $post_id, $count) {
    // Filtrer par métadonnée personnalisée
    return array_filter($related, function($post) {
        return get_post_meta($post->ID, '_featured', true) === '1';
    });
}, 10, 3);
```

## 🧪 Tests à Effectuer

- [x] ✅ Article standard s'affiche correctement
- [x] ✅ Projet architectural affiche ses métadonnées (surface, coût, etc.)
- [x] ✅ Illustration affiche ses métadonnées (technique, logiciels, etc.)
- [x] ✅ Articles similaires fonctionnent pour tous les types
- [x] ✅ Relations manuelles prioritaires sur automatiques
- [x] ✅ Responsive design (mobile, tablette, desktop)
- [ ] Mode sombre (si activé dans le thème)
- [ ] Livre d'or conserve son comportement spécifique

## 📝 Prochaines Étapes (Optionnelles)

1. **Ajouter des tests unitaires** pour les fonctions helper
2. **Créer un widget Gutenberg** utilisant `archi_get_related_posts()`
3. **Améliorer le livre d'or** pour utiliser les nouvelles fonctions
4. **Ajouter un shortcode** : `[archi_related_posts count="5"]`
5. **Internationalisation** : Vérifier toutes les chaînes avec `_e()` et `__()`

## 🎓 Conventions de Code Respectées

✅ Tous les noms de fonctions préfixés par `archi_`  
✅ Tous les noms de classes CSS préfixés par `archi-`  
✅ Texte domain `archi-graph` utilisé partout  
✅ Sanitization des inputs (esc_html, esc_attr, esc_url)  
✅ Hooks WordPress standard respectés  
✅ Code conforme aux standards WordPress Coding Standards  
✅ Documentation PHPDoc pour chaque fonction  

## 📚 Références

- **Fichiers modifiés :**
  - `single.php` (réécrit)
  - `functions.php` (ajout de l'include + enqueue CSS)
  - `inc/single-post-helpers.php` (nouveau)
  - `assets/css/single-post.css` (nouveau)

- **Fichiers supprimés :**
  - `single-archi_project.php`
  - `single-archi_illustration.php`

- **Fichiers conservés :**
  - `single-archi_guestbook.php` (logique spécifique préservée)

---

**✨ Résultat : Un système unifié, maintenable et extensible pour l'affichage de tous les types d'articles !**
