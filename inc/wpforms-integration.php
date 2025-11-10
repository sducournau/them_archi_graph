<?php
/**
 * Intégration de WPForms pour créer des formulaires de soumission
 * Génère automatiquement des formulaires pour chaque type de contenu
 * 
 * NOTE: The deprecated 'archi_article' post type has been removed.
 * Use 'post' for standard articles or 'archi_illustration' for illustrated content.
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Vérifier si WPForms est activé
 */
function archi_check_wpforms() {
    if (!class_exists('WPForms')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>';
            echo __('Le thème Archi Graph nécessite WPForms pour fonctionner correctement. ', 'archi-graph');
            echo '<a href="' . admin_url('plugin-install.php?s=wpforms&tab=search&type=term') . '">';
            echo __('Installer WPForms', 'archi-graph');
            echo '</a></p></div>';
        });
        return false;
    }
    return true;
}
add_action('admin_init', 'archi_check_wpforms');

/**
 * Créer les types de contenu personnalisés
 */
function archi_register_post_types() {
    // Type de contenu : Projets d'architecture
    register_post_type('archi_project', [
        'labels' => [
            'name' => __('Projets Architecture', 'archi-graph'),
            'singular_name' => __('Projet Architecture', 'archi-graph'),
            'add_new' => __('Ajouter un projet', 'archi-graph'),
            'add_new_item' => __('Ajouter un nouveau projet', 'archi-graph'),
            'edit_item' => __('Modifier le projet', 'archi-graph'),
            'new_item' => __('Nouveau projet', 'archi-graph'),
            'view_item' => __('Voir le projet', 'archi-graph'),
            'search_items' => __('Rechercher des projets', 'archi-graph'),
            'not_found' => __('Aucun projet trouvé', 'archi-graph'),
            'not_found_in_trash' => __('Aucun projet dans la corbeille', 'archi-graph'),
            'menu_name' => __('Projets Archi', 'archi-graph')
        ],
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'projet'],
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-building',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields'],
        'show_in_rest' => true,
        'taxonomies' => ['archi_project_type', 'archi_project_status']
    ]);
}
add_action('init', 'archi_register_post_types');

/**
 * Créer les taxonomies personnalisées
 */
function archi_register_taxonomies() {
    // Taxonomies pour les projets d'architecture
    register_taxonomy('archi_project_type', 'archi_project', [
        'labels' => [
            'name' => __('Types de Projet', 'archi-graph'),
            'singular_name' => __('Type de Projet', 'archi-graph'),
            'add_new_item' => __('Ajouter un nouveau type', 'archi-graph'),
            'edit_item' => __('Modifier le type', 'archi-graph'),
            'update_item' => __('Mettre à jour le type', 'archi-graph'),
            'search_items' => __('Rechercher des types', 'archi-graph'),
            'not_found' => __('Aucun type trouvé', 'archi-graph')
        ],
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'type-projet']
    ]);

    register_taxonomy('archi_project_status', 'archi_project', [
        'labels' => [
            'name' => __('Statuts de Projet', 'archi-graph'),
            'singular_name' => __('Statut de Projet', 'archi-graph'),
            'add_new_item' => __('Ajouter un nouveau statut', 'archi-graph'),
            'edit_item' => __('Modifier le statut', 'archi-graph')
        ],
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'statut-projet']
    ]);
}
add_action('init', 'archi_register_taxonomies');

/**
 * Créer les termes par défaut pour les taxonomies
 */
function archi_create_default_terms() {
    // Termes pour les catégories d'illustration
    $illustration_categories = [
        'Explorations Graphiques' => 'Recherches et expérimentations visuelles',
        'Croquis Architecturaux' => 'Dessins préparatoires et études de projet',
        'Rendus 3D' => 'Visualisations tridimensionnelles de projets',
        'Photomontages' => 'Compositions et intégrations photographiques',
        'Diagrammes' => 'Schémas explicatifs et concepts',
        'Analyses Urbaines' => 'Études de sites et contextes urbains',
        'Détails Constructifs' => 'Illustrations techniques et matériaux',
        'Ambiances' => 'Atmosphères et perceptions spatiales'
    ];
    
    foreach ($illustration_categories as $term_name => $description) {
        if (!term_exists($term_name, 'archi_illustration_category')) {
            wp_insert_term($term_name, 'archi_illustration_category', [
                'description' => $description,
                'slug' => sanitize_title($term_name)
            ]);
        }
    }
    
    // Termes pour les techniques
    $techniques = [
        'Dessin à la main' => 'Techniques traditionnelles de dessin',
        'Logiciels CAO/DAO' => 'AutoCAD, ArchiCAD, Revit',
        'Modélisation 3D' => 'SketchUp, Rhino, 3ds Max',
        'Rendu' => 'V-Ray, Lumion, Enscape',
        'Retouche photo' => 'Photoshop, GIMP',
        'Illustration vectorielle' => 'Illustrator, InDesign',
        'Mixte' => 'Combinaison de plusieurs techniques'
    ];
    
    foreach ($techniques as $term_name => $description) {
        if (!term_exists($term_name, 'archi_technique')) {
            wp_insert_term($term_name, 'archi_technique', [
                'description' => $description,
                'slug' => sanitize_title($term_name)
            ]);
        }
    }
}
add_action('after_switch_theme', 'archi_create_default_terms');

/**
 * Créer automatiquement les formulaires WPForms
 */
function archi_create_wpforms() {
    if (!class_exists('WPForms')) {
        return;
    }

    // Vérifier si les formulaires existent déjà
    $project_form_id = get_option('archi_project_form_id');

    if (!$project_form_id) {
        $project_form_id = archi_create_project_form();
        if ($project_form_id) {
            update_option('archi_project_form_id', $project_form_id);
        }
    }
}
add_action('init', 'archi_create_wpforms', 15);

/**
 * Créer le formulaire pour les projets d'architecture
 */
function archi_create_project_form() {
    $form_data = [
        'settings' => [
            'form_title' => __('Projet d\'Architecture', 'archi-graph'),
            'form_desc' => __('Formulaire de soumission pour les projets d\'architecture', 'archi-graph'),
            'submit_text' => __('Soumettre le projet', 'archi-graph'),
            'submit_text_processing' => __('Envoi en cours...', 'archi-graph'),
            'form_class' => 'archi-project-form',
            'notification_enable' => '1',
            'notifications' => [
                '1' => [
                    'notification_name' => __('Notification Administrateur', 'archi-graph'),
                    'email' => '{admin_email}',
                    'subject' => __('Nouveau projet d\'architecture soumis', 'archi-graph'),
                    'sender_name' => '{field_id="1"}',
                    'sender_address' => '{field_id="2"}',
                    'message' => archi_get_project_notification_template()
                ]
            ]
        ],
        'fields' => [
            // Informations de base
            '0' => [
                'id' => '0',
                'type' => 'divider',
                'label' => __('Informations du Projet', 'archi-graph'),
                'description' => __('Renseignez les informations de base du projet', 'archi-graph')
            ],
            '1' => [
                'id' => '1',
                'type' => 'name',
                'label' => __('Nom du Projet', 'archi-graph'),
                'required' => '1',
                'size' => 'large'
            ],
            '2' => [
                'id' => '2',
                'type' => 'email',
                'label' => __('Email de Contact', 'archi-graph'),
                'required' => '1',
                'size' => 'large'
            ],
            '3' => [
                'id' => '3',
                'type' => 'textarea',
                'label' => __('Description du Projet', 'archi-graph'),
                'required' => '1',
                'size' => 'large',
                'limit_count' => '1',
                'limit_mode' => 'characters',
                'limit_characters' => '500'
            ],
            
            // Détails techniques
            '10' => [
                'id' => '10',
                'type' => 'divider',
                'label' => __('Détails Techniques', 'archi-graph')
            ],
            '11' => [
                'id' => '11',
                'type' => 'number',
                'label' => __('Surface (m²)', 'archi-graph'),
                'required' => '1',
                'size' => 'medium'
            ],
            '12' => [
                'id' => '12',
                'type' => 'number',
                'label' => __('Coût Estimé (€)', 'archi-graph'),
                'size' => 'medium'
            ],
            '13' => [
                'id' => '13',
                'type' => 'select',
                'label' => __('Type de Projet', 'archi-graph'),
                'required' => '1',
                'choices' => [
                    '1' => __('Résidentiel', 'archi-graph'),
                    '2' => __('Commercial', 'archi-graph'),
                    '3' => __('Industriel', 'archi-graph'),
                    '4' => __('Culturel', 'archi-graph'),
                    '5' => __('Éducatif', 'archi-graph'),
                    '6' => __('Santé', 'archi-graph'),
                    '7' => __('Autre', 'archi-graph')
                ]
            ],
            '14' => [
                'id' => '14',
                'type' => 'text',
                'label' => __('Maîtrise d\'Ouvrage', 'archi-graph'),
                'size' => 'large'
            ],
            '15' => [
                'id' => '15',
                'type' => 'text',
                'label' => __('BET (Bureau d\'Études Techniques)', 'archi-graph'),
                'size' => 'large'
            ],
            '16' => [
                'id' => '16',
                'type' => 'select',
                'label' => __('Statut du Projet', 'archi-graph'),
                'required' => '1',
                'choices' => [
                    '1' => __('Étude', 'archi-graph'),
                    '2' => __('Conception', 'archi-graph'),
                    '3' => __('En cours de construction', 'archi-graph'),
                    '4' => __('Terminé', 'archi-graph'),
                    '5' => __('Suspendu', 'archi-graph')
                ]
            ],
            
            // Localisation et dates
            '20' => [
                'id' => '20',
                'type' => 'divider',
                'label' => __('Localisation et Planning', 'archi-graph')
            ],
            '21' => [
                'id' => '21',
                'type' => 'address',
                'label' => __('Adresse du Projet', 'archi-graph'),
                'required' => '1'
            ],
            '22' => [
                'id' => '22',
                'type' => 'date-time',
                'label' => __('Date de Début Prévue', 'archi-graph'),
                'date_format' => 'd/m/Y',
                'date_type' => 'datepicker'
            ],
            '23' => [
                'id' => '23',
                'type' => 'date-time',
                'label' => __('Date de Fin Prévue', 'archi-graph'),
                'date_format' => 'd/m/Y',
                'date_type' => 'datepicker'
            ],
            
            // Certification et durabilité
            '30' => [
                'id' => '30',
                'type' => 'divider',
                'label' => __('Certification et Durabilité', 'archi-graph')
            ],
            '31' => [
                'id' => '31',
                'type' => 'checkbox',
                'label' => __('Certifications Environnementales', 'archi-graph'),
                'choices' => [
                    '1' => __('HQE (Haute Qualité Environnementale)', 'archi-graph'),
                    '2' => __('BREEAM', 'archi-graph'),
                    '3' => __('LEED', 'archi-graph'),
                    '4' => __('BBC (Bâtiment Basse Consommation)', 'archi-graph'),
                    '5' => __('Passivhaus', 'archi-graph'),
                    '6' => __('E+C- (Énergie Positive & Réduction Carbone)', 'archi-graph')
                ]
            ],
            '32' => [
                'id' => '32',
                'type' => 'textarea',
                'label' => __('Objectifs de Développement Durable', 'archi-graph'),
                'size' => 'large'
            ],
            
            // Médias
            '40' => [
                'id' => '40',
                'type' => 'divider',
                'label' => __('Médias et Documentation', 'archi-graph')
            ],
            '41' => [
                'id' => '41',
                'type' => 'file-upload',
                'label' => __('Images du Projet', 'archi-graph'),
                'extensions' => 'jpg,jpeg,png,gif,webp',
                'max_file_number' => '10',
                'max_file_size' => '15'
            ],
            '42' => [
                'id' => '42',
                'type' => 'file-upload',
                'label' => __('Plans et Documents Techniques', 'archi-graph'),
                'extensions' => 'pdf,dwg,dxf',
                'max_file_number' => '5',
                'max_file_size' => '15'
            ]
        ]
    ];

    return wpforms()->form->add(
        __('Projet d\'Architecture', 'archi-graph'),
        $form_data
    );
}

/**
 * Fonction archi_create_article_form supprimée
 * Type de contenu archi_article retiré
 * Pour créer des articles/illustrations, utiliser le type 'post' ou 'archi_illustration' à la place
 */
/*
function archi_create_article_form() {
    $form_data = [
        'settings' => [
            'form_title' => __('Illustration/Exploration Graphique', 'archi-graph'),
            'form_desc' => __('Formulaire de soumission pour les illustrations et explorations graphiques', 'archi-graph'),
            'submit_text' => __('Soumettre l\'illustration', 'archi-graph'),
            'submit_text_processing' => __('Envoi en cours...', 'archi-graph'),
            'form_class' => 'archi-illustration-form',
            'notification_enable' => '1',
            'notifications' => [
                '1' => [
                    'notification_name' => __('Notification Administrateur', 'archi-graph'),
                    'email' => '{admin_email}',
                    'subject' => __('Nouvelle illustration/exploration graphique soumise', 'archi-graph'),
                    'sender_name' => '{field_id="1"}',
                    'sender_address' => '{field_id="2"}',
                    'message' => archi_get_article_notification_template()
                ]
            ]
        ],
        'fields' => [
            // Informations de base
            '0' => [
                'id' => '0',
                'type' => 'divider',
                'label' => __('Informations de l\'Illustration', 'archi-graph'),
                'description' => __('Renseignez les informations de base de l\'article', 'archi-graph')
            ],
            '1' => [
                'id' => '1',
                'type' => 'name',
                'label' => __('Titre de l\'Article', 'archi-graph'),
                'required' => '1',
                'size' => 'large'
            ],
            '2' => [
                'id' => '2',
                'type' => 'email',
                'label' => __('Email de l\'Auteur', 'archi-graph'),
                'required' => '1',
                'size' => 'large'
            ],
            '3' => [
                'id' => '3',
                'type' => 'text',
                'label' => __('Nom de l\'Auteur', 'archi-graph'),
                'required' => '1',
                'size' => 'large'
            ],
            '4' => [
                'id' => '4',
                'type' => 'textarea',
                'label' => __('Résumé de l\'Article', 'archi-graph'),
                'required' => '1',
                'size' => 'large',
                'limit_count' => '1',
                'limit_mode' => 'characters',
                'limit_characters' => '300'
            ],
            '5' => [
                'id' => '5',
                'type' => 'textarea',
                'label' => __('Contenu Principal', 'archi-graph'),
                'required' => '1',
                'size' => 'large',
                'limit_count' => '1',
                'limit_mode' => 'words',
                'limit_words' => '2000'
            ],
            
            // Catégorisation
            '10' => [
                'id' => '10',
                'type' => 'divider',
                'label' => __('Catégorisation et Tags', 'archi-graph')
            ],
            '11' => [
                'id' => '11',
                'type' => 'select',
                'label' => __('Technique Principale', 'archi-graph'),
                'required' => '1',
                'choices' => [
                    '1' => __('Construction Traditionnelle', 'archi-graph'),
                    '2' => __('Construction Bois', 'archi-graph'),
                    '3' => __('Construction Métallique', 'archi-graph'),
                    '4' => __('Béton Armé', 'archi-graph'),
                    '5' => __('Construction Écologique', 'archi-graph'),
                    '6' => __('Préfabrication', 'archi-graph'),
                    '7' => __('Rénovation/Restauration', 'archi-graph'),
                    '8' => __('Autre', 'archi-graph')
                ]
            ],
            '12' => [
                'id' => '12',
                'type' => 'checkbox',
                'label' => __('Techniques Secondaires', 'archi-graph'),
                'choices' => [
                    '1' => __('Isolation Thermique', 'archi-graph'),
                    '2' => __('Étanchéité', 'archi-graph'),
                    '3' => __('Ventilation', 'archi-graph'),
                    '4' => __('Chauffage/Climatisation', 'archi-graph'),
                    '5' => __('Éclairage', 'archi-graph'),
                    '6' => __('Domotique', 'archi-graph'),
                    '7' => __('Énergies Renouvelables', 'archi-graph')
                ]
            ],
            '13' => [
                'id' => '13',
                'type' => 'select',
                'label' => __('Lieu/Région', 'archi-graph'),
                'choices' => [
                    '1' => __('Île-de-France', 'archi-graph'),
                    '2' => __('Auvergne-Rhône-Alpes', 'archi-graph'),
                    '3' => __('Nouvelle-Aquitaine', 'archi-graph'),
                    '4' => __('Occitanie', 'archi-graph'),
                    '5' => __('Hauts-de-France', 'archi-graph'),
                    '6' => __('Provence-Alpes-Côte d\'Azur', 'archi-graph'),
                    '7' => __('Grand Est', 'archi-graph'),
                    '8' => __('Pays de la Loire', 'archi-graph'),
                    '9' => __('Bretagne', 'archi-graph'),
                    '10' => __('Normandie', 'archi-graph'),
                    '11' => __('Bourgogne-Franche-Comté', 'archi-graph'),
                    '12' => __('Centre-Val de Loire', 'archi-graph'),
                    '13' => __('Corse', 'archi-graph'),
                    '14' => __('International', 'archi-graph')
                ]
            ],
            '14' => [
                'id' => '14',
                'type' => 'checkbox',
                'label' => __('Thèmes Abordés', 'archi-graph'),
                'choices' => [
                    '1' => __('Durabilité', 'archi-graph'),
                    '2' => __('Innovation', 'archi-graph'),
                    '3' => __('Patrimoine', 'archi-graph'),
                    '4' => __('Urbain', 'archi-graph'),
                    '5' => __('Rural', 'archi-graph'),
                    '6' => __('Logement Social', 'archi-graph'),
                    '7' => __('Architecture Bioclimatique', 'archi-graph'),
                    '8' => __('Design', 'archi-graph'),
                    '9' => __('Accessibilité', 'archi-graph'),
                    '10' => __('Réglementation', 'archi-graph')
                ]
            ],
            
            // Médias et illustrations
            '20' => [
                'id' => '20',
                'type' => 'divider',
                'label' => __('Illustrations et Médias', 'archi-graph')
            ],
            '21' => [
                'id' => '21',
                'type' => 'file-upload',
                'label' => __('Images Principales', 'archi-graph'),
                'required' => '1',
                'extensions' => 'jpg,jpeg,png,gif,webp',
                'max_file_number' => '15',
                'max_file_size' => '15'
            ],
            '22' => [
                'id' => '22',
                'type' => 'file-upload',
                'label' => __('Schémas et Dessins Techniques', 'archi-graph'),
                'extensions' => 'jpg,jpeg,png,gif,svg,pdf',
                'max_file_number' => '10',
                'max_file_size' => '15'
            ],
            '23' => [
                'id' => '23',
                'type' => 'url',
                'label' => __('Lien Vidéo (YouTube/Vimeo)', 'archi-graph')
            ],
            
            // Métadonnées
            '30' => [
                'id' => '30',
                'type' => 'divider',
                'label' => __('Informations Complémentaires', 'archi-graph')
            ],
            '31' => [
                'id' => '31',
                'type' => 'text',
                'label' => __('Mots-clés', 'archi-graph'),
                'description' => __('Séparez les mots-clés par des virgules', 'archi-graph'),
                'size' => 'large'
            ],
            '32' => [
                'id' => '32',
                'type' => 'select',
                'label' => __('Niveau de Difficulté Technique', 'archi-graph'),
                'choices' => [
                    '1' => __('Débutant', 'archi-graph'),
                    '2' => __('Intermédiaire', 'archi-graph'),
                    '3' => __('Avancé', 'archi-graph'),
                    '4' => __('Expert', 'archi-graph')
                ]
            ],
            '33' => [
                'id' => '33',
                'type' => 'textarea',
                'label' => __('Sources et Références', 'archi-graph'),
                'size' => 'medium'
            ]
        ]
    ];

    return wpforms()->form->add(
        __('Article Illustré', 'archi-graph'),
        $form_data
    );
}
*/

/**
 * Template de notification pour les projets
 */
function archi_get_project_notification_template() {
    return __('Un nouveau projet d\'architecture a été soumis :

Nom du projet : {field_id="1"}
Email de contact : {field_id="2"}
Description : {field_id="3"}

Détails techniques :
- Surface : {field_id="11"} m²
- Coût estimé : {field_id="12"} €
- Type : {field_id="13"}
- Maîtrise d\'ouvrage : {field_id="14"}
- BET : {field_id="15"}
- Statut : {field_id="16"}

Localisation : {field_id="21"}
Dates : Du {field_id="22"} au {field_id="23"}

Certifications : {field_id="31"}
Objectifs durables : {field_id="32"}

Consultez l\'administration WordPress pour examiner cette soumission.', 'archi-graph');
}

/**
 * Fonction archi_get_article_notification_template supprimée
 * Type de contenu archi_article retiré
 */

/**
 * Traitement des soumissions de formulaire
 */
function archi_process_form_entries($fields, $entry, $form_data, $entry_id) {
    $form_id = $form_data['id'];
    $project_form_id = get_option('archi_project_form_id');

    if ($form_id == $project_form_id) {
        archi_process_project_submission($fields, $entry, $entry_id);
    }
}
add_action('wpforms_process_complete', 'archi_process_form_entries', 10, 4);

/**
 * Traiter les soumissions de projet
 */
function archi_process_project_submission($fields, $entry, $entry_id) {
    // Créer un brouillon de post pour le projet
    $post_data = [
        'post_title' => sanitize_text_field($fields['1']['value'] ?? ''),
        'post_content' => wp_kses_post($fields['3']['value'] ?? ''),
        'post_status' => 'pending',
        'post_type' => 'archi_project',
        'meta_input' => [
            '_archi_wpforms_entry_id' => $entry_id,
            '_archi_surface' => intval($fields['11']['value'] ?? 0),
            '_archi_cost' => intval($fields['12']['value'] ?? 0),
            '_archi_client' => sanitize_text_field($fields['14']['value'] ?? ''),
            '_archi_bet' => sanitize_text_field($fields['15']['value'] ?? ''),
            '_archi_contact_email' => sanitize_email($fields['2']['value'] ?? ''),
            '_archi_address' => sanitize_textarea_field($fields['21']['value'] ?? ''),
            '_archi_start_date' => sanitize_text_field($fields['22']['value'] ?? ''),
            '_archi_end_date' => sanitize_text_field($fields['23']['value'] ?? ''),
            '_archi_certifications' => maybe_serialize($fields['31']['value'] ?? []),
            '_archi_sustainable_goals' => sanitize_textarea_field($fields['32']['value'] ?? '')
        ]
    ];

    $post_id = wp_insert_post($post_data);

    if ($post_id && !is_wp_error($post_id)) {
        // Assigner les taxonomies
        if (!empty($fields['13']['value'])) {
            $project_type = archi_get_taxonomy_term_by_choice('archi_project_type', $fields['13']['value']);
            if ($project_type) {
                wp_set_post_terms($post_id, [$project_type], 'archi_project_type');
            }
        }

        if (!empty($fields['16']['value'])) {
            $project_status = archi_get_taxonomy_term_by_choice('archi_project_status', $fields['16']['value']);
            if ($project_status) {
                wp_set_post_terms($post_id, [$project_status], 'archi_project_status');
            }
        }

        // Traiter les fichiers uploadés
        archi_process_uploaded_files($fields, $post_id, ['41', '42']);
        
        // Send confirmation email with status link
        archi_send_submission_confirmation_email($post_id, $entry_id, $fields['2']['value']);
    }
}

/**
 * Fonction archi_process_article_submission supprimée
 * Type de contenu archi_article retiré
 */

/**
 * Fonction archi_assign_article_taxonomies supprimée
 * Type de contenu archi_article retiré
 */

/**
 * Obtenir ou créer un terme de taxonomie basé sur le choix du formulaire
 */
function archi_get_taxonomy_term_by_choice($taxonomy, $choice_value) {
    // Mapping des valeurs de choix vers les noms de termes
    $mappings = archi_get_taxonomy_mappings();
    
    if (!isset($mappings[$taxonomy][$choice_value])) {
        return false;
    }
    
    $term_name = $mappings[$taxonomy][$choice_value];
    
    // Vérifier si le terme existe
    $term = get_term_by('name', $term_name, $taxonomy);
    
    if (!$term) {
        // Créer le terme s'il n'existe pas
        $result = wp_insert_term($term_name, $taxonomy);
        if (!is_wp_error($result)) {
            return $result['term_id'];
        }
    } else {
        return $term->term_id;
    }
    
    return false;
}

/**
 * Mappings des choix de formulaire vers les termes de taxonomie
 */
function archi_get_taxonomy_mappings() {
    return [
        'archi_project_type' => [
            '1' => __('Résidentiel', 'archi-graph'),
            '2' => __('Commercial', 'archi-graph'),
            '3' => __('Industriel', 'archi-graph'),
            '4' => __('Culturel', 'archi-graph'),
            '5' => __('Éducatif', 'archi-graph'),
            '6' => __('Santé', 'archi-graph'),
            '7' => __('Autre', 'archi-graph')
        ],
        'archi_project_status' => [
            '1' => __('Étude', 'archi-graph'),
            '2' => __('Conception', 'archi-graph'),
            '3' => __('En cours de construction', 'archi-graph'),
            '4' => __('Terminé', 'archi-graph'),
            '5' => __('Suspendu', 'archi-graph')
        ],
        'archi_technique' => [
            '1' => __('Construction Traditionnelle', 'archi-graph'),
            '2' => __('Construction Bois', 'archi-graph'),
            '3' => __('Construction Métallique', 'archi-graph'),
            '4' => __('Béton Armé', 'archi-graph'),
            '5' => __('Construction Écologique', 'archi-graph'),
            '6' => __('Préfabrication', 'archi-graph'),
            '7' => __('Rénovation/Restauration', 'archi-graph'),
            '8' => __('Autre', 'archi-graph')
        ],
        'archi_location' => [
            '1' => __('Île-de-France', 'archi-graph'),
            '2' => __('Auvergne-Rhône-Alpes', 'archi-graph'),
            '3' => __('Nouvelle-Aquitaine', 'archi-graph'),
            '4' => __('Occitanie', 'archi-graph'),
            '5' => __('Hauts-de-France', 'archi-graph'),
            '6' => __('Provence-Alpes-Côte d\'Azur', 'archi-graph'),
            '7' => __('Grand Est', 'archi-graph'),
            '8' => __('Pays de la Loire', 'archi-graph'),
            '9' => __('Bretagne', 'archi-graph'),
            '10' => __('Normandie', 'archi-graph'),
            '11' => __('Bourgogne-Franche-Comté', 'archi-graph'),
            '12' => __('Centre-Val de Loire', 'archi-graph'),
            '13' => __('Corse', 'archi-graph'),
            '14' => __('International', 'archi-graph')
        ],
        'archi_theme' => [
            '1' => __('Durabilité', 'archi-graph'),
            '2' => __('Innovation', 'archi-graph'),
            '3' => __('Patrimoine', 'archi-graph'),
            '4' => __('Urbain', 'archi-graph'),
            '5' => __('Rural', 'archi-graph'),
            '6' => __('Logement Social', 'archi-graph'),
            '7' => __('Architecture Bioclimatique', 'archi-graph'),
            '8' => __('Design', 'archi-graph'),
            '9' => __('Accessibilité', 'archi-graph'),
            '10' => __('Réglementation', 'archi-graph')
        ]
    ];
}

/**
 * Traiter les fichiers uploadés via WPForms
 */
function archi_process_uploaded_files($fields, $post_id, $field_ids) {
    $first_image_set = false;
    
    foreach ($field_ids as $field_id) {
        if (!empty($fields[$field_id]['value'])) {
            $files = is_array($fields[$field_id]['value']) ? $fields[$field_id]['value'] : [$fields[$field_id]['value']];
            
            foreach ($files as $file_url) {
                if (!empty($file_url)) {
                    // Télécharger et attacher le fichier au post
                    $attachment_id = archi_attach_uploaded_file($file_url, $post_id);
                    
                    // Set the first successful upload as featured image
                    if ($attachment_id && !$first_image_set && !has_post_thumbnail($post_id)) {
                        set_post_thumbnail($post_id, $attachment_id);
                        $first_image_set = true;
                    }
                }
            }
        }
    }
}

/**
 * Attacher un fichier uploadé au post
 */
function archi_attach_uploaded_file($file_url, $post_id) {
    if (empty($file_url)) {
        return false;
    }

    // Télécharger le fichier
    $tmp = download_url($file_url);
    if (is_wp_error($tmp)) {
        return false;
    }

    // Informations du fichier
    $file_array = [
        'name' => basename($file_url),
        'tmp_name' => $tmp
    ];

    // Télécharger dans la médiathèque
    $attachment_id = media_handle_sideload($file_array, $post_id);

    // Nettoyer le fichier temporaire
    if (file_exists($tmp)) {
        unlink($tmp);
    }

    if (is_wp_error($attachment_id)) {
        return false;
    }

    return $attachment_id;
}

/**
 * Send confirmation email to submitter with status tracking link
 */
function archi_send_submission_confirmation_email($post_id, $entry_id, $email) {
    $post = get_post($post_id);
    
    if (inc/wpforms-integration.phppost || inc/wpforms-integration.phpemail) {
        return false;
    }
    
    $post_type_obj = get_post_type_object($post->post_type);
    
    // Get status page URL
    $status_page = get_page_by_path('submission-status');
    
    if ($status_page) {
        $status_url = add_query_arg([
            'entry' => $entry_id,
            'email' => urlencode($email)
        ], get_permalink($status_page->ID));
    } else {
        $status_url = home_url('/submission-status/?entry=' . $entry_id . '&email=' . urlencode($email));
    }
    
    $subject = sprintf(__('Confirmation de votre soumission #%d', 'archi-graph'), $entry_id);
    
    $message = sprintf(__('Bonjour,

Merci pour votre soumission "%s".

📋 Numéro de référence : #%d
🔍 Suivre votre soumission : %s

Cordialement,
%s', 'archi-graph'),
        $post->post_title, $entry_id, $status_url, get_bloginfo('name')
    );
    
    $headers = ['Content-Type: text/plain; charset=UTF-8'];
    
    return wp_mail($email, $subject, $message, $headers);
}

/**
 * Notify when submission is published
 */
function archi_notify_on_publish($new_status, $old_status, $post) {
    if (!in_array($post->post_type, ['post', 'archi_project', 'archi_illustration'])) {
        return;
    }
    
    if ($new_status !== 'publish' || !in_array($old_status, ['pending', 'draft'])) {
        return;
    }
    
    $entry_id = get_post_meta($post->ID, '_archi_wpforms_entry_id', true);
    $contact_email = get_post_meta($post->ID, '_archi_contact_email', true);
    
    if (inc/wpforms-integration.phpentry_id || inc/wpforms-integration.phpcontact_email) {
        return;
    }
    
    $subject = sprintf(__('✅ Votre soumission #%d est publiée !', 'archi-graph'), $entry_id);
    $message = sprintf(__('Votre soumission "%s" est maintenant en ligne : %s', 'archi-graph'), 
        $post->post_title, get_permalink($post->ID));
    
    wp_mail($contact_email, $subject, $message);
}
add_action('transition_post_status', 'archi_notify_on_publish', 10, 3);

/**
 * Clean up when attachments are deleted
 * Prevents 404 errors in the editor when referenced media is deleted
 */
function archi_cleanup_deleted_attachment($attachment_id) {
    // Check if this was a featured image for any post
    $posts_with_thumbnail = get_posts([
        'post_type' => ['post', 'archi_project', 'archi_illustration'],
        'post_status' => 'any',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => '_thumbnail_id',
                'value' => $attachment_id,
                'compare' => '='
            ]
        ]
    ]);
    
    foreach ($posts_with_thumbnail as $post) {
        delete_post_meta($post->ID, '_thumbnail_id');
    }
}
add_action('delete_attachment', 'archi_cleanup_deleted_attachment');

/**
 * Créer le formulaire de livre d'or
 */
function archi_create_guestbook_form() {
    $form_data = [
        'settings' => [
            'form_title' => __('Livre d\'Or', 'archi-graph'),
            'form_desc' => __('Partagez vos impressions et commentaires', 'archi-graph'),
            'submit_text' => __('Envoyer mon commentaire', 'archi-graph'),
            'submit_text_processing' => __('Envoi en cours...', 'archi-graph'),
            'form_class' => 'archi-guestbook-form',
            'notification_enable' => '1',
            'notifications' => [
                '1' => [
                    'notification_name' => __('Notification Administrateur', 'archi-graph'),
                    'email' => '{admin_email}',
                    'subject' => __('Nouveau commentaire dans le livre d\'or', 'archi-graph'),
                    'sender_name' => '{field_id="1"}',
                    'sender_address' => '{field_id="2"}',
                    'message' => __('Nouvelle entrée dans le livre d\'or.<br><br>Nom: {field_id="1"}<br>Email: {field_id="2"}<br>Entreprise: {field_id="3"}<br><br>Commentaire:<br>{field_id="4"}<br><br>Articles liés: {field_id="5"}', 'archi-graph')
                ]
            ]
        ],
        'fields' => [
            '1' => [
                'id' => '1',
                'type' => 'name',
                'label' => __('Votre nom', 'archi-graph'),
                'required' => '1',
                'size' => 'large',
                'format' => 'simple'
            ],
            '2' => [
                'id' => '2',
                'type' => 'email',
                'label' => __('Votre email', 'archi-graph'),
                'required' => '1',
                'size' => 'large'
            ],
            '3' => [
                'id' => '3',
                'type' => 'text',
                'label' => __('Entreprise/Organisation (optionnel)', 'archi-graph'),
                'required' => '0',
                'size' => 'large'
            ],
            '4' => [
                'id' => '4',
                'type' => 'textarea',
                'label' => __('Votre commentaire', 'archi-graph'),
                'required' => '1',
                'size' => 'large',
                'placeholder' => __('Partagez vos impressions, vos retours ou vos suggestions...', 'archi-graph'),
                'limit_count' => '1',
                'limit_mode' => 'characters',
                'limit_characters' => '1000'
            ],
            '5' => [
                'id' => '5',
                'type' => 'select',
                'label' => __('Article(s) lié(s) (optionnel)', 'archi-graph'),
                'choices' => archi_get_articles_for_select(),
                'required' => '0',
                'multiple' => '1',
                'size' => 'large',
                'placeholder' => __('Sélectionnez un ou plusieurs articles', 'archi-graph')
            ],
            '6' => [
                'id' => '6',
                'type' => 'divider',
                'label' => __('Paramètres de visualisation', 'archi-graph')
            ],
            '7' => [
                'id' => '7',
                'type' => 'checkbox',
                'label' => __('Afficher dans le graphique', 'archi-graph'),
                'choices' => [
                    '1' => [
                        'label' => __('Oui, afficher cette entrée dans le graphique de relations', 'archi-graph'),
                        'value' => '1'
                    ]
                ],
                'required' => '0'
            ],
            '8' => [
                'id' => '8',
                'type' => 'text',
                'label' => __('Couleur du nœud (optionnel)', 'archi-graph'),
                'required' => '0',
                'size' => 'small',
                'default_value' => '#9b59b6',
                'placeholder' => '#9b59b6'
            ]
        ]
    ];
    
    $form_id = wpforms()->form->add(__('Livre d\'Or', 'archi-graph'), $form_data);
    
    if ($form_id) {
        update_option('archi_guestbook_form_id', $form_id);
        return $form_id;
    }
    
    return false;
}

/**
 * Obtenir la liste des articles pour le select du formulaire
 */
function archi_get_articles_for_select() {
    $choices = [];
    
    $posts = get_posts([
        'post_type' => ['post', 'archi_project', 'archi_illustration'],
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC'
    ]);
    
    foreach ($posts as $post) {
        $type_label = '';
        switch ($post->post_type) {
            case 'archi_project':
                $type_label = '[Projet] ';
                break;
            case 'archi_illustration':
                $type_label = '[Illustration] ';
                break;
            default:
                $type_label = '[Article] ';
        }
        
        $choices[$post->ID] = [
            'label' => $type_label . $post->post_title,
            'value' => $post->ID
        ];
    }
    
    return $choices;
}

/**
 * Traitement du formulaire livre d'or
 */
function archi_process_guestbook_form($fields, $entry, $form_data, $entry_id) {
    $form_id = $form_data['id'];
    $guestbook_form_id = get_option('archi_guestbook_form_id');
    
    if ($form_id != $guestbook_form_id) {
        return;
    }
    
    // Extraire les données du formulaire
    $author_name = sanitize_text_field($fields['1']['value'] ?? '');
    $author_email = sanitize_email($fields['2']['value'] ?? '');
    $author_company = sanitize_text_field($fields['3']['value'] ?? '');
    $comment = wp_kses_post($fields['4']['value'] ?? '');
    $linked_articles_raw = $fields['5']['value'] ?? '';
    $show_in_graph = isset($fields['7']['value']['1']) ? '1' : '0';
    $node_color = sanitize_hex_color($fields['8']['value'] ?? '#9b59b6') ?: '#9b59b6';
    
    // Traiter les articles liés
    $linked_articles = [];
    if (!empty($linked_articles_raw)) {
        if (is_array($linked_articles_raw)) {
            $linked_articles = array_map('intval', $linked_articles_raw);
        } else {
            $linked_articles = [intval($linked_articles_raw)];
        }
    }
    
    // Créer le titre à partir du nom de l'auteur
    $post_title = sprintf(__('Commentaire de %s', 'archi-graph'), $author_name);
    
    // Créer l'entrée
    $post_data = [
        'post_title' => $post_title,
        'post_content' => $comment,
        'post_status' => 'pending', // En attente de modération
        'post_type' => 'archi_guestbook',
        'post_author' => 1, // Admin par défaut
        'meta_input' => [
            '_archi_wpforms_entry_id' => $entry_id,
            '_archi_guestbook_author_name' => $author_name,
            '_archi_guestbook_author_email' => $author_email,
            '_archi_guestbook_author_company' => $author_company,
            '_archi_linked_articles' => $linked_articles,
            '_archi_show_in_graph' => $show_in_graph,
            '_archi_node_color' => $node_color,
            '_archi_node_size' => 50, // Taille par défaut pour le livre d'or
            '_archi_priority_level' => 'low' // Priorité basse par défaut
        ]
    ];
    
    $post_id = wp_insert_post($post_data);
    
    if (is_wp_error($post_id)) {
        if (WP_DEBUG && WP_DEBUG_LOG) {
            error_log('Archi: Failed to create guestbook entry - ' . $post_id->get_error_message());
        }
        return;
    }
    
    // Invalider le cache
    delete_transient('archi_graph_articles');
}
add_action('wpforms_process_complete', 'archi_process_guestbook_form', 10, 4);

/**
 * Créer tous les formulaires au moment de l'activation du thème
 */
function archi_create_all_forms() {
    if (!archi_check_wpforms()) {
        return;
    }
    
    // Créer le formulaire de livre d'or si nécessaire
    if (!get_option('archi_guestbook_form_id')) {
        archi_create_guestbook_form();
    }
}
add_action('after_switch_theme', 'archi_create_all_forms');
