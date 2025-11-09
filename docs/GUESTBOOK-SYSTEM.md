# Livre d'Or - Documentation

## Vue d'ensemble

Le système de livre d'or permet aux visiteurs de poster des commentaires et témoignages qui peuvent être représentés comme des nœuds dans le graphe de relations, connectés aux articles, projets ou illustrations mentionnés.

## Fonctionnalités

### 1. Custom Post Type `archi_guestbook`

Un nouveau type de contenu dédié aux entrées du livre d'or avec :
- **Post Type** : `archi_guestbook`
- **Slug** : `livre-or`
- **Icône** : Livre (`dashicons-book-alt`)
- **Support** : Titre, contenu, champs personnalisés, auteur

### 2. Métadonnées

Chaque entrée du livre d'or possède les métadonnées suivantes :

#### Informations auteur
- `_archi_guestbook_author_name` : Nom de l'auteur du commentaire
- `_archi_guestbook_author_email` : Email de l'auteur
- `_archi_guestbook_author_company` : Entreprise/Organisation (optionnel)

#### Relations et graphe
- `_archi_linked_articles` : Array d'IDs de posts liés (projets, articles, illustrations)
- `_archi_show_in_graph` : Visibilité dans le graphique ('0' ou '1')
- `_archi_node_color` : Couleur du nœud (par défaut : #2ecc71 - vert)
- `_archi_node_size` : Taille du nœud (par défaut : 50px)
- `_archi_priority_level` : Priorité d'affichage (par défaut : 'low')

#### Traçabilité
- `_archi_wpforms_entry_id` : ID de l'entrée WPForms source

### 3. Formulaire WPForms

Un formulaire public permet aux visiteurs de soumettre leurs commentaires :

**Champs du formulaire :**
1. **Nom** (requis) - Nom du visiteur
2. **Email** (requis) - Email du visiteur
3. **Entreprise/Organisation** (optionnel)
4. **Commentaire** (requis) - Texte du témoignage (max 1000 caractères)
5. **Article(s) lié(s)** (optionnel) - Sélection multiple des articles concernés
6. **Afficher dans le graphique** (optionnel) - Checkbox pour affichage graphe
7. **Couleur du nœud** (optionnel) - Couleur personnalisée (défaut #9b59b6)

**Traitement du formulaire :**
- Les entrées sont créées avec le statut "pending" (en attente de modération)
- Notification email envoyée à l'administrateur
- Les données sont sauvegardées comme métadonnées
- Le cache du graphique est invalidé

### 4. Templates d'affichage

#### `page-guestbook.php`
Template de page pour afficher le livre d'or complet avec :
- Formulaire de soumission en haut
- Liste paginée des témoignages publiés (10 par page)
- Cartes d'entrées avec avatar, informations auteur, tags d'articles liés
- Design responsive avec animations CSS

**Utilisation :**
```php
// Créer une page WordPress et sélectionner le template "Page Livre d'Or"
```

#### `single-archi_guestbook.php`
Template pour afficher une entrée individuelle avec :
- Informations complètes de l'auteur
- Badge de visibilité graphique
- Grille des articles liés avec thumbnails
- Bouton de retour au livre d'or

### 5. Intégration au graphe

Les entrées du livre d'or apparaissent dans le graphe D3.js avec :

**REST API :**
- Endpoint : `/wp-json/archi/v1/articles`
- Inclut le type `archi_guestbook` dans les résultats
- Métadonnées spécifiques dans `guestbook_meta`

**Relations :**
- Connexions automatiques vers les articles mentionnés dans `_archi_linked_articles`
- Les nœuds du livre d'or ont une couleur distinctive (vert par défaut)
- Taille et priorité personnalisables

**Exemple de données API :**
```json
{
  "id": 123,
  "type": "archi_guestbook",
  "title": "Commentaire de Jean Dupont",
  "content": "Excellent travail sur ce projet...",
  "guestbook_meta": {
    "author_name": "Jean Dupont",
    "author_email": "jean@example.com",
    "author_company": "Entreprise XYZ",
    "linked_articles": [45, 67, 89]
  },
  "graph_params": {
    "show_in_graph": "1",
    "node_color": "#2ecc71",
    "node_size": 50,
    "priority_level": "low"
  }
}
```

### 6. Styles CSS

Fichier dédié : `assets/css/guestbook.css`

**Styles inclus :**
- Formulaire WPForms personnalisé
- Cartes de témoignages avec avatars générés
- Badges et tags d'articles liés
- Animations d'entrée et effets de survol
- Responsive design complet
- Support du mode sombre
- Styles d'impression

**Chargement automatique :**
```php
// Sur page-guestbook.php ou single-archi_guestbook.php
wp_enqueue_style('archi-guestbook', ...)
```

## Installation et Configuration

### 1. Activation du système

Le système s'active automatiquement au changement de thème :
```php
add_action('after_switch_theme', 'archi_create_all_forms');
```

Cela crée :
- Le formulaire WPForms
- Les options nécessaires dans la base de données

### 2. Création de la page livre d'or

1. Aller dans **Pages > Ajouter**
2. Titre : "Livre d'Or"
3. **Template** : Sélectionner "Page Livre d'Or"
4. Slug recommandé : `livre-or`
5. Publier

### 3. Configuration du formulaire

Le formulaire est créé automatiquement. Pour le personnaliser :
1. Aller dans **WPForms > Tous les formulaires**
2. Trouver "Livre d'Or"
3. Modifier les champs selon besoins
4. Sauvegarder

### 4. Modération des entrées

1. Aller dans **Livre d'Or** dans le menu admin
2. Les nouvelles entrées sont en statut "En attente"
3. Réviser le contenu
4. Publier ou rejeter

## Intégration personnalisée

### Afficher les témoignages ailleurs

```php
<?php
$guestbook_entries = get_posts([
    'post_type' => 'archi_guestbook',
    'post_status' => 'publish',
    'posts_per_page' => 5,
    'orderby' => 'date',
    'order' => 'DESC'
]);

foreach ($guestbook_entries as $entry) {
    $author_name = get_post_meta($entry->ID, '_archi_guestbook_author_name', true);
    $author_company = get_post_meta($entry->ID, '_archi_guestbook_author_company', true);
    
    echo '<div class="testimonial">';
    echo '<h3>' . esc_html($author_name) . '</h3>';
    if ($author_company) {
        echo '<p class="company">' . esc_html($author_company) . '</p>';
    }
    echo '<div class="content">' . wp_kses_post($entry->post_content) . '</div>';
    echo '</div>';
}
?>
```

### Widget de témoignages aléatoires

```php
// Dans functions.php ou un plugin
function archi_random_testimonial_shortcode() {
    $entry = get_posts([
        'post_type' => 'archi_guestbook',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'orderby' => 'rand'
    ]);
    
    if (empty($entry)) return '';
    
    $entry = $entry[0];
    $author = get_post_meta($entry->ID, '_archi_guestbook_author_name', true);
    
    ob_start();
    ?>
    <blockquote class="random-testimonial">
        <?php echo wp_kses_post($entry->post_content); ?>
        <cite>— <?php echo esc_html($author); ?></cite>
    </blockquote>
    <?php
    return ob_get_clean();
}
add_shortcode('testimonial_random', 'archi_random_testimonial_shortcode');
```

Utilisation : `[testimonial_random]`

## Personnalisation avancée

### Couleurs des nœuds par type

Dans `inc/rest-api.php`, les couleurs par défaut :
```php
'archi_guestbook' => '#2ecc71' // Vert
'archi_project' => '#e67e22'   // Orange
'archi_illustration' => '#9b59b6' // Violet
'post' => '#3498db' // Bleu
```

### Taille et priorité

Modifier dans le handler du formulaire (`inc/wpforms-integration.php`) :
```php
'_archi_node_size' => 50,           // Taille du nœud (40-120)
'_archi_priority_level' => 'low'    // low|normal|high|featured
```

### Filtre de modération automatique

```php
// Auto-approuver certains emails
add_filter('wp_insert_post_data', function($data, $postarr) {
    if ($data['post_type'] === 'archi_guestbook') {
        $trusted_domains = ['@votreentreprise.com', '@partenaire.com'];
        $email = get_post_meta($postarr['ID'], '_archi_guestbook_author_email', true);
        
        foreach ($trusted_domains as $domain) {
            if (strpos($email, $domain) !== false) {
                $data['post_status'] = 'publish';
                break;
            }
        }
    }
    return $data;
}, 10, 2);
```

## Sécurité

Le système implémente les bonnes pratiques WordPress :

1. **Sanitization** : Tous les inputs sont nettoyés
   - `sanitize_text_field()` pour textes
   - `sanitize_email()` pour emails
   - `wp_kses_post()` pour contenu HTML

2. **Escaping** : Tous les outputs sont échappés
   - `esc_html()` pour texte simple
   - `esc_attr()` pour attributs HTML
   - `esc_url()` pour URLs

3. **Nonces** : Vérification des formulaires
4. **Capabilities** : Contrôle des permissions
5. **Modération** : Statut "pending" par défaut

## Support et maintenance

### Logs

Les erreurs sont enregistrées dans le log WordPress :
```php
error_log("Entrée livre d'or créée avec succès: ID $post_id");
```

### Cache

Le cache du graphique est invalidé automatiquement :
```php
delete_transient('archi_graph_articles');
```

### Base de données

Tables utilisées :
- `wp_posts` : Entrées principales
- `wp_postmeta` : Métadonnées
- WPForms tables : Données formulaires

## Compatibilité

- **WordPress** : 5.0+
- **PHP** : 7.4+
- **WPForms** : Requis (gratuit ou Pro)
- **Navigateurs** : Modernes (Chrome, Firefox, Safari, Edge)

## Changelog

### Version 1.0.0 (Novembre 2025)
- ✅ Création du custom post type `archi_guestbook`
- ✅ Formulaire WPForms public
- ✅ Templates d'affichage responsive
- ✅ Intégration au graphe D3.js
- ✅ Système de modération
- ✅ Relations avec articles/projets/illustrations
- ✅ Styles CSS dédiés avec animations
