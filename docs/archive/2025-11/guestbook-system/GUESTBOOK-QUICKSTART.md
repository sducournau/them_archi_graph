# Livre d'Or - Guide de démarrage rapide

## 🎯 Qu'est-ce que c'est ?

Un système complet de livre d'or qui permet aux visiteurs de poster des commentaires et témoignages. Ces entrées peuvent être visualisées comme des nœuds dans le graphe de relations du thème, connectées aux articles, projets ou illustrations mentionnés.

## 🚀 Installation rapide

### 1. Créer la page livre d'or

1. **WordPress Admin** → **Pages** → **Ajouter**
2. Titre : `Livre d'Or`
3. **Attributs de page** → **Modèle** : Sélectionner `Page Livre d'Or`
4. **Publier**
5. Visiter : `https://votre-site.com/livre-or/`

### 2. Vérifier le formulaire

Le formulaire WPForms est créé automatiquement. Pour le vérifier :
- **WordPress Admin** → **WPForms** → **Tous les formulaires**
- Rechercher "Livre d'Or"
- ID stocké dans `archi_guestbook_form_id`

## 📝 Utilisation

### Pour les visiteurs

1. Aller sur la page Livre d'Or
2. Remplir le formulaire :
   - Nom (requis)
   - Email (requis)
   - Entreprise (optionnel)
   - Commentaire (requis)
   - Sélectionner des articles liés (optionnel)
   - Cocher "Afficher dans le graphique" si souhaité
3. Soumettre
4. L'entrée est créée en statut "En attente"

### Pour les administrateurs

1. **WordPress Admin** → **Livre d'Or**
2. Réviser les nouvelles entrées (statut "En attente")
3. **Publier** pour approuver ou **Corbeille** pour rejeter
4. Modifier les métadonnées :
   - Articles liés
   - Couleur du nœud
   - Visibilité dans le graphe

## 🎨 Personnalisation

### Modifier les couleurs du graphe

Dans `inc/rest-api.php`, ligne ~155 :

```php
elseif ($post->post_type === 'archi_guestbook') {
    $default_color = '#2ecc71'; // Changer ici
}
```

### Modifier la taille des nœuds

Dans `inc/wpforms-integration.php`, ligne ~1035 :

```php
'_archi_node_size' => 50, // Valeur entre 40 et 120
```

### Personnaliser les styles

Fichier CSS : `assets/css/guestbook.css`

Chargé automatiquement sur :
- `page-guestbook.php`
- `single-archi_guestbook.php`

## 🔗 Intégration au graphe

Les entrées du livre d'or apparaissent automatiquement dans le graphe D3.js si :

1. **Visibilité activée** : `_archi_show_in_graph = '1'`
2. **Statut publié** : `post_status = 'publish'`
3. **Articles liés** : Connexions vers les posts dans `_archi_linked_articles`

Couleur distinctive : **Vert (#2ecc71)** par défaut

## 📊 Structure des données

### Custom Post Type

```
Type : archi_guestbook
Slug : livre-or
Supports : title, editor, custom-fields, author
Hierarchical : Non
```

### Métadonnées principales

```
_archi_guestbook_author_name      string
_archi_guestbook_author_email     string
_archi_guestbook_author_company   string
_archi_linked_articles            array
_archi_show_in_graph             '0' | '1'
_archi_node_color                 hex color
_archi_node_size                  int (40-120)
_archi_wpforms_entry_id           int
```

### API REST

**Endpoint** : `/wp-json/archi/v1/articles`

Inclut automatiquement les entrées `archi_guestbook` avec métadonnées.

## 🛠️ Fichiers modifiés/créés

### Fichiers créés
- ✅ `single-archi_guestbook.php` - Template entrée individuelle
- ✅ `page-guestbook.php` - Template page liste
- ✅ `assets/css/guestbook.css` - Styles dédiés
- ✅ `docs/GUESTBOOK-SYSTEM.md` - Documentation complète

### Fichiers modifiés
- ✅ `inc/custom-post-types.php` - Ajout CPT et colonnes admin
- ✅ `inc/meta-boxes.php` - Meta-boxes et sauvegarde
- ✅ `inc/wpforms-integration.php` - Formulaire et traitement
- ✅ `inc/rest-api.php` - Intégration API (3 occurrences)
- ✅ `inc/graph-meta-registry.php` - Enregistrement métadonnées
- ✅ `functions.php` - Enqueue CSS

## 🎭 Exemples de code

### Afficher 3 témoignages aléatoires

```php
<?php
$testimonials = get_posts([
    'post_type' => 'archi_guestbook',
    'post_status' => 'publish',
    'posts_per_page' => 3,
    'orderby' => 'rand'
]);

foreach ($testimonials as $testimonial) {
    $author = get_post_meta($testimonial->ID, '_archi_guestbook_author_name', true);
    $company = get_post_meta($testimonial->ID, '_archi_guestbook_author_company', true);
    ?>
    <div class="testimonial">
        <p><?php echo wp_kses_post($testimonial->post_content); ?></p>
        <cite>
            <?php echo esc_html($author); ?>
            <?php if ($company): ?>
                <span class="company"><?php echo esc_html($company); ?></span>
            <?php endif; ?>
        </cite>
    </div>
    <?php
}
wp_reset_postdata();
?>
```

### Widget témoignage du jour

```php
function daily_testimonial_widget() {
    $today = date('Y-m-d');
    $cached = get_transient('daily_testimonial_' . $today);
    
    if ($cached) {
        return $cached;
    }
    
    $testimonials = get_posts([
        'post_type' => 'archi_guestbook',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'orderby' => 'rand'
    ]);
    
    if (empty($testimonials)) return '';
    
    $testimonial = $testimonials[0];
    $author = get_post_meta($testimonial->ID, '_archi_guestbook_author_name', true);
    
    ob_start();
    ?>
    <aside class="daily-testimonial">
        <h3>Témoignage du jour</h3>
        <blockquote>
            <?php echo wp_kses_post($testimonial->post_content); ?>
            <footer>— <?php echo esc_html($author); ?></footer>
        </blockquote>
    </aside>
    <?php
    $output = ob_get_clean();
    
    set_transient('daily_testimonial_' . $today, $output, DAY_IN_SECONDS);
    return $output;
}
```

## 🔒 Sécurité

- ✅ Toutes les entrées utilisateur sont sanitizées
- ✅ Toutes les sorties sont échappées
- ✅ Vérification des nonces WPForms
- ✅ Modération par défaut (statut "pending")
- ✅ Permissions WordPress respectées

## 📱 Responsive

- ✅ Design mobile-first
- ✅ Breakpoints : 1024px, 768px, 640px
- ✅ Grilles flexibles
- ✅ Images adaptatives

## ⚡ Performance

- Cache invalidé automatiquement lors de modifications
- Requêtes optimisées avec index de métadonnées
- CSS chargé uniquement sur pages concernées
- Lazy loading des images

## 🐛 Dépannage

### Le formulaire ne s'affiche pas
→ Vérifier que WPForms est activé
→ Vérifier `get_option('archi_guestbook_form_id')`

### Les entrées n'apparaissent pas
→ Vérifier le statut de publication (doit être "publish")
→ Vérifier dans Admin > Livre d'Or

### Pas visible dans le graphe
→ Vérifier `_archi_show_in_graph = '1'`
→ Vider le cache : `delete_transient('archi_graph_articles')`

### Couleur non personnalisée
→ S'assurer que `_archi_node_color` contient une valeur hex valide
→ Valeur par défaut : #2ecc71

## 📚 Documentation complète

Voir `docs/GUESTBOOK-SYSTEM.md` pour :
- Architecture détaillée
- API reference
- Hooks et filtres
- Personnalisation avancée
- Exemples d'intégration

## 🎉 C'est prêt !

Le système de livre d'or est maintenant opérationnel. Les visiteurs peuvent :
1. ✅ Poster des commentaires via le formulaire
2. ✅ Lier leurs commentaires à vos projets/articles
3. ✅ Apparaître dans le graphe de relations
4. ✅ Voir leurs témoignages publiés après modération

---

**Support** : Voir documentation complète dans `/docs/GUESTBOOK-SYSTEM.md`
