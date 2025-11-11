# 🏗️ Architecture du Système Livre d'Or

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    SYSTÈME LIVRE D'OR - ARCHITECTURE                    │
└─────────────────────────────────────────────────────────────────────────┘

                        ┌─────────────────────┐
                        │   VISITEUR WEB      │
                        └──────────┬──────────┘
                                   │
                                   │ Visite
                                   ▼
                ┌──────────────────────────────────────────┐
                │     page-guestbook.php                   │
                │  ┌────────────────────────────────────┐  │
                │  │  1. Header & Description           │  │
                │  │  2. Formulaire WPForms             │  │
                │  │  3. Liste des témoignages publiés │  │
                │  │  4. Pagination                     │  │
                │  └────────────────────────────────────┘  │
                └──────────────────┬───────────────────────┘
                                   │
                                   │ Soumission
                                   ▼
        ┌──────────────────────────────────────────────────────┐
        │         WPForms Processing                           │
        │  archi_process_guestbook_form()                      │
        │                                                       │
        │  1. Validation des champs                            │
        │  2. Sanitization des données                         │
        │  3. Création du post (statut: pending)               │
        │  4. Sauvegarde des métadonnées                       │
        │  5. Invalidation du cache                            │
        │  6. Email de notification                            │
        └──────────────────────┬───────────────────────────────┘
                               │
                               │ Sauvegarde
                               ▼
        ┌──────────────────────────────────────────────────────┐
        │              BASE DE DONNÉES                         │
        │                                                       │
        │  wp_posts                                            │
        │  ├── ID                                              │
        │  ├── post_title                                      │
        │  ├── post_content                                    │
        │  ├── post_type: 'archi_guestbook'                   │
        │  ├── post_status: 'pending' → 'publish'             │
        │  └── post_author                                     │
        │                                                       │
        │  wp_postmeta                                         │
        │  ├── _archi_guestbook_author_name                   │
        │  ├── _archi_guestbook_author_email                  │
        │  ├── _archi_guestbook_author_company                │
        │  ├── _archi_linked_articles                         │
        │  ├── _archi_show_in_graph                           │
        │  ├── _archi_node_color                              │
        │  ├── _archi_node_size                               │
        │  ├── _archi_priority_level                          │
        │  └── _archi_wpforms_entry_id                        │
        └──────────────────────┬───────────────────────────────┘
                               │
                ┌──────────────┴──────────────┐
                │                             │
                │ Modération                  │ Accès API
                ▼                             ▼
    ┌───────────────────────┐    ┌──────────────────────────┐
    │  ADMIN WORDPRESS      │    │   REST API               │
    │                       │    │  /wp-json/archi/v1/      │
    │  • Liste des entrées  │    │  articles                │
    │  • Meta-boxes         │    │                          │
    │  • Publication        │    │  Inclut:                 │
    │  • Modification       │    │  • Type: archi_guestbook │
    │  • Suppression        │    │  • Métadonnées           │
    │  • Colonnes custom    │    │  • guestbook_meta{}      │
    └───────────────────────┘    │  • Relations             │
                                 └─────────┬────────────────┘
                                           │
                                           │ Consommation
                                           ▼
                          ┌────────────────────────────────┐
                          │    GRAPHE D3.JS                │
                          │                                │
                          │  Nœuds Livre d'Or:            │
                          │  • Couleur: #2ecc71 (vert)    │
                          │  • Taille: 50px               │
                          │  • Relations vers articles    │
                          │  • Priorité: low              │
                          └────────────────────────────────┘


┌─────────────────────────────────────────────────────────────────────────┐
│                        FLUX DE TRAITEMENT                               │
└─────────────────────────────────────────────────────────────────────────┘

Visiteur → Formulaire → WPForms → archi_process_guestbook_form()
                                            ↓
                                   wp_insert_post()
                                            ↓
                                   update_post_meta() × 9
                                            ↓
                                   delete_transient('archi_graph_articles')
                                            ↓
                                   Notification email admin
                                            ↓
                        ┌───────────────────┴───────────────────┐
                        ↓                                       ↓
            Admin modère & publie                    Cache invalidé
                        ↓                                       ↓
            Visible sur page-guestbook.php           API REST mis à jour
                        ↓                                       ↓
            Accessible via single-archi_guestbook.php  Graphe D3 mis à jour


┌─────────────────────────────────────────────────────────────────────────┐
│                        FICHIERS IMPLIQUÉS                               │
└─────────────────────────────────────────────────────────────────────────┘

TEMPLATES
├── page-guestbook.php ........................... Liste des témoignages
├── single-archi_guestbook.php ................... Détail d'un témoignage
└── template-parts/ .............................. Composants réutilisables

LOGIQUE BACKEND
├── inc/custom-post-types.php .................... CPT archi_guestbook
├── inc/meta-boxes.php ........................... Meta-boxes & save
├── inc/wpforms-integration.php .................. Formulaire & traitement
├── inc/rest-api.php ............................. Exposition API
├── inc/sample-data-generator.php ................ Génération de tests
└── inc/graph-meta-registry.php .................. Enregistrement métadonnées

ASSETS
├── assets/css/guestbook.css ..................... Styles dédiés
└── assets/js/ ................................... (Pas de JS custom nécessaire)

DOCUMENTATION
├── docs/GUESTBOOK-SYSTEM.md ..................... Documentation technique
├── docs/GUESTBOOK-QUICKSTART.md ................. Guide rapide
├── docs/GUESTBOOK-SAMPLE-DATA.md ................ Génération de tests
├── docs/GUESTBOOK-AUDIT-REPORT.md ............... Rapport d'audit complet
└── docs/GUESTBOOK-AUDIT-SUMMARY.md .............. Résumé d'audit


┌─────────────────────────────────────────────────────────────────────────┐
│                      MÉTADONNÉES - STRUCTURE                            │
└─────────────────────────────────────────────────────────────────────────┘

INFORMATIONS AUTEUR
_archi_guestbook_author_name ......... string  ......... Nom complet
_archi_guestbook_author_email ........ string  ......... Email (privé)
_archi_guestbook_author_company ...... string  ......... Entreprise

RELATIONS
_archi_linked_articles ............... array   ......... IDs des posts liés
_archi_wpforms_entry_id .............. int     ......... ID entrée WPForms

GRAPHE
_archi_show_in_graph ................. '0'|'1' ......... Visibilité
_archi_node_color .................... #hex    ......... Couleur du nœud
_archi_node_size ..................... int     ......... 40-120px
_archi_priority_level ................ string  ......... low/normal/high


┌─────────────────────────────────────────────────────────────────────────┐
│                      SÉCURITÉ - LAYERS                                  │
└─────────────────────────────────────────────────────────────────────────┘

INPUT (Sanitization)
├── sanitize_text_field() ........................ Textes simples
├── sanitize_email() ............................. Emails
├── wp_kses_post() ............................... Contenu HTML
├── sanitize_hex_color() ......................... Couleurs
└── array_map('intval', ...) ..................... Arrays d'IDs

PROCESSING (Validation)
├── wp_verify_nonce() ............................ Vérification nonce
├── current_user_can() ........................... Permissions admin
├── defined('DOING_AUTOSAVE') .................... Protection autosave
└── Status 'pending' par défaut .................. Modération

OUTPUT (Escaping)
├── esc_html() ................................... Texte HTML
├── esc_attr() ................................... Attributs HTML
├── esc_url() .................................... URLs
├── get_permalink() .............................. URLs WordPress
└── wp_kses_post() ............................... Contenu rich text


┌─────────────────────────────────────────────────────────────────────────┐
│                      PERFORMANCE - OPTIMISATIONS                        │
└─────────────────────────────────────────────────────────────────────────┘

CACHE
├── Transient 'archi_graph_articles' ............. Cache API (1 heure)
├── Invalidation automatique ..................... Après save/publish
└── WP Object Cache .............................. WordPress natif

REQUÊTES
├── Pagination (10 posts/page) ................... Limite résultats
├── post_status = 'publish' ...................... Filtre pré-requête
├── Index WordPress natifs ....................... post_type, post_status
└── WP_Query optimisé ............................ Pas de query directe

ASSETS
├── CSS chargé conditionnellement ................ Pages guestbook only
├── WPForms gère son propre JS ................... Pas de JS custom
└── Pas de dépendances externes .................. Autonome


┌─────────────────────────────────────────────────────────────────────────┐
│                      WORKFLOW UTILISATEUR                               │
└─────────────────────────────────────────────────────────────────────────┘

VISITEUR
1. Visite page /livre-or/
2. Remplit formulaire
3. Soumet (validation côté client WPForms)
4. Voit message de confirmation
5. Email de confirmation envoyé (optionnel)

ADMIN
1. Reçoit notification email
2. Va dans Admin > Livre d'Or
3. Voit liste avec statut "En attente"
4. Clique sur l'entrée
5. Révise contenu et métadonnées
6. Modifie si nécessaire
7. Clique "Publier"

PUBLIC
1. Entrée apparaît sur /livre-or/
2. Visible dans single-archi_guestbook.php
3. Si activé, apparaît dans le graphe D3.js
4. Relations visibles avec articles liés


┌─────────────────────────────────────────────────────────────────────────┐
│                      HOOKS & FILTRES                                    │
└─────────────────────────────────────────────────────────────────────────┘

ACTIONS
├── init ......................................... register_post_type()
├── add_meta_boxes ............................... Meta-boxes admin
├── save_post_archi_guestbook .................... Sauvegarde métadonnées
├── wpforms_process_complete ..................... Traitement formulaire
├── after_switch_theme ........................... Création auto formulaire
└── rest_api_init ................................ Enregistrement routes

FILTRES
├── manage_archi_guestbook_posts_columns ......... Colonnes admin
└── post_type_link ............................... URLs personnalisées (opt)


┌─────────────────────────────────────────────────────────────────────────┐
│                      TESTS & VALIDATION                                 │
└─────────────────────────────────────────────────────────────────────────┘

✅ Fonctionnels
   ├── Soumission formulaire
   ├── Validation des champs
   ├── Modération admin
   └── Affichage public

✅ Sécurité
   ├── XSS Prevention
   ├── CSRF Protection
   ├── SQL Injection Protection
   └── Permission Checks

✅ Compatibilité
   ├── WordPress 5.0+
   ├── PHP 7.4+
   ├── Browsers modernes
   └── Responsive design

✅ Performance
   ├── Cache fonctionnel
   ├── Requêtes optimisées
   └── Assets conditionnels
```

---

**Légende** :
- `→` : Flux de données principal
- `↓` : Étape suivante
- `├──` : Élément d'une liste
- `└──` : Dernier élément d'une liste
- `✅` : Validé/Testé
- `🟢` : Priorité basse
- `🟡` : Priorité moyenne
- `🔴` : Priorité haute
