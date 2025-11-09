<?php
/**
 * Sample Data Generator Admin Page
 * 
 * Creates an admin page to generate sample architectural projects and illustrations
 * 
 * @package Archi-Graph
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add admin menu page
 */
function archi_sample_data_admin_menu() {
    add_management_page(
        __('Générateur de Contenu', 'archi-graph'),
        __('Générateur de Contenu', 'archi-graph'),
        'manage_options',
        'archi-sample-data',
        'archi_sample_data_page'
    );
}
add_action('admin_menu', 'archi_sample_data_admin_menu');

/**
 * Sample data configuration
 */
function archi_get_sample_data_config() {
    return [
        'project_types' => [
            'Résidentiel' => ['Maison individuelle', 'Immeuble collectif', 'Villa', 'Logement social'],
            'Commercial' => ['Centre commercial', 'Boutique', 'Restaurant', 'Hôtel'],
            'Culturel' => ['Musée', 'Bibliothèque', 'Théâtre', 'Centre culturel'],
            'Éducatif' => ['École', 'Université', 'Crèche', 'Centre de formation'],
            'Santé' => ['Hôpital', 'Clinique', 'Maison de retraite', 'Cabinet médical'],
            'Industriel' => ['Usine', 'Entrepôt', 'Atelier', 'Zone logistique'],
        ],
        'project_statuses' => ['Étude', 'Conception', 'En cours de construction', 'Terminé'],
        'locations' => [
            'Paris', 'Lyon', 'Marseille', 'Toulouse', 'Bordeaux', 'Nantes', 
            'Strasbourg', 'Montpellier', 'Lille', 'Rennes', 'Nice', 'Grenoble'
        ],
        'clients' => [
            'Ville de', 'Conseil Régional', 'Ministère de la Culture',
            'Promoteur Immobilier', 'Société Privée', 'Association',
            'Établissement Public', 'Entreprise Locale'
        ],
        'certifications' => [
            'HQE (Haute Qualité Environnementale)',
            'BREEAM Excellent',
            'LEED Gold',
            'BBC (Bâtiment Basse Consommation)',
            'Passivhaus',
            'E+C- Niveau 2',
            'NF Habitat HQE',
            'RT 2012 +20%',
        ],
        'illustration_types' => [
            'Croquis Architecturaux',
            'Rendus 3D',
            'Explorations Graphiques',
            'Photomontages',
            'Diagrammes',
            'Analyses Urbaines',
            'Détails Constructifs',
            'Ambiances',
        ],
        'techniques' => [
            'Modélisation 3D' => ['SketchUp', 'Rhino', '3ds Max', 'Blender'],
            'Rendu' => ['V-Ray', 'Lumion', 'Enscape', 'Corona'],
            'Dessin à la main' => ['Aquarelle', 'Encre', 'Crayon', 'Feutres'],
            'CAO/DAO' => ['AutoCAD', 'ArchiCAD', 'Revit', 'Vectorworks'],
            'Retouche photo' => ['Photoshop', 'GIMP', 'Affinity Photo'],
            'Illustration vectorielle' => ['Illustrator', 'InDesign', 'Affinity Designer'],
        ],
        'categories' => [
            'Architecture Durable' => 'Projets et réflexions sur l\'architecture durable',
            'Patrimoine' => 'Restauration et valorisation du patrimoine',
            'Innovation' => 'Nouvelles technologies et matériaux',
            'Urbain' => 'Projets d\'aménagement urbain',
            'Paysage' => 'Architecture de paysage',
            'Intérieur' => 'Design d\'intérieur et aménagements',
            'Recherche' => 'Études et recherches architecturales',
        ],
    ];
}

/**
 * Helper: Create or get category
 */
function archi_sample_get_or_create_category($name, $description = '') {
    $term = get_term_by('name', $name, 'category');
    
    if (!$term) {
        $result = wp_insert_term($name, 'category', [
            'description' => $description,
            'slug' => sanitize_title($name)
        ]);
        
        if (!is_wp_error($result)) {
            return $result['term_id'];
        }
    }
    
    return $term ? $term->term_id : null;
}

/**
 * Helper: Create or get taxonomy term
 */
function archi_sample_get_or_create_term($name, $taxonomy) {
    $term = get_term_by('name', $name, $taxonomy);
    
    if (!$term) {
        $result = wp_insert_term($name, $taxonomy, [
            'slug' => sanitize_title($name)
        ]);
        
        if (!is_wp_error($result)) {
            return $result['term_id'];
        }
    }
    
    return $term ? $term->term_id : null;
}

/**
 * Helper: Generate random date
 */
function archi_sample_random_date($start_date, $end_date) {
    $min = strtotime($start_date);
    $max = strtotime($end_date);
    $val = rand($min, $max);
    return date('Y-m-d', $val);
}

/**
 * Helper: Generate project description
 */
function archi_sample_generate_project_description($type, $location, $surface) {
    $descriptions = [
        "Ce projet architectural s'inscrit dans une démarche d'excellence environnementale et de qualité spatiale. Situé à {$location}, il répond aux enjeux contemporains de l'architecture durable.",
        "L'objectif de ce projet est de créer un espace fonctionnel et esthétique qui s'intègre harmonieusement dans son contexte urbain. Avec ses {$surface}m², il propose une réponse innovante aux besoins du programme.",
        "Ce bâtiment de {$surface}m² à {$location} illustre notre approche de l'architecture comme art de l'espace et du bien-être. Le projet met l'accent sur la qualité des ambiances et la performance énergétique.",
        "Pensé comme un dialogue entre tradition et modernité, ce projet architectural propose une réinterprétation contemporaine des codes architecturaux locaux. La conception privilégie les matériaux biosourcés et les énergies renouvelables.",
    ];
    
    return $descriptions[array_rand($descriptions)];
}

/**
 * Helper: Generate illustration description
 */
function archi_sample_generate_illustration_description($type, $technique) {
    $descriptions = [
        "Cette illustration explore les possibilités de représentation architecturale à travers une approche {$technique}. Elle met en lumière les qualités spatiales et atmosphériques du projet.",
        "Réalisée en {$technique}, cette image témoigne d'une recherche graphique visant à transmettre l'essence du projet architectural. L'accent est mis sur l'ambiance et la matérialité.",
        "Cette représentation architecturale combine rigueur technique et sensibilité artistique. Le rendu {$technique} permet de valoriser les intentions conceptuelles du projet.",
        "Exploration visuelle questionnant les codes de la représentation architecturale. Cette illustration en {$technique} révèle une approche singulière de l'espace et de la lumière.",
    ];
    
    return $descriptions[array_rand($descriptions)];
}


/**
 * Generate guestbook entry comments
 */
function archi_sample_generate_guestbook_comment($author_type) {
    $comments = [
        'client' => [
            "Nous sommes ravis du travail accompli. L'équipe a su comprendre nos besoins et les traduire en un projet qui dépasse nos attentes.",
            "Un accompagnement professionnel tout au long du projet. La qualité d'écoute et la créativité ont fait toute la différence.",
            "Excellent travail sur notre projet. Les délais ont été respectés et le résultat est magnifique. Je recommande vivement.",
            "Une approche innovante et respectueuse de l'environnement. Le projet reflète parfaitement nos valeurs.",
            "Merci pour votre expertise et votre disponibilité. Le processus s'est déroulé dans d'excellentes conditions.",
            "Projet mené avec rigueur et créativité. L'équipe a su transformer notre vision en réalité.",
            "Très satisfait de la collaboration. La qualité architecturale et technique est au rendez-vous.",
            "Un résultat qui allie fonctionnalité et esthétique. Bravo pour ce beau projet !",
            "L'attention portée aux détails et la qualité d'exécution sont remarquables.",
            "Une expérience enrichissante avec des professionnels passionnés et compétents.",
        ],
        'professional' => [
            "Belle collaboration sur ce projet. L'approche architecturale est pertinente et bien pensée.",
            "Un projet exemplaire en termes de développement durable. Félicitations à l'équipe.",
            "J'ai eu le plaisir de collaborer sur plusieurs projets. Le professionnalisme et la créativité sont constants.",
            "Travail de qualité qui montre une vraie maîtrise des enjeux contemporains de l'architecture.",
            "Une approche technique rigoureuse combinée à une vision créative inspirante.",
            "Partenariat enrichissant qui démontre une excellente capacité d'innovation.",
            "Les solutions proposées témoignent d'une réelle expertise et d'une sensibilité architecturale.",
            "Collaboration fluide et professionnelle. Les échanges ont été constructifs et productifs.",
            "Projet remarquable qui pose de nouvelles références dans le domaine.",
            "Une méthodologie de travail exemplaire et des résultats à la hauteur des ambitions.",
        ],
        'visitor' => [
            "Magnifique portfolio qui témoigne d'une grande diversité de projets et d'une vraie sensibilité architecturale.",
            "Bravo pour la qualité des réalisations présentées. Cela donne envie d'en savoir plus !",
            "Des projets inspirants qui montrent une belle vision de l'architecture contemporaine.",
            "J'ai découvert ce site par hasard et je suis impressionné par la qualité du travail présenté.",
            "Portfolio très intéressant avec des projets variés et innovants. Merci pour le partage.",
            "Les rendus et illustrations sont superbes. On sent la passion et l'expertise.",
            "Très belle présentation de vos travaux. L'approche durable est particulièrement appréciable.",
            "Des réalisations qui allient créativité et respect de l'environnement. Inspirant !",
            "J'ai beaucoup aimé la façon dont les projets sont présentés. Très professionnel.",
            "Site très complet qui donne une belle vision de votre travail. Félicitations !",
        ],
    ];
    
    return $comments[$author_type][array_rand($comments[$author_type])];
}

/**
 * Generate sample guestbook entries
 */
function archi_generate_sample_guestbook($count = 10) {
    $stats = [
        'guestbook' => 0,
        'errors' => []
    ];
    
    // Get all published projects and illustrations for linking
    $all_posts = get_posts([
        'post_type' => ['post', 'archi_project', 'archi_illustration'],
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids'
    ]);
    
    // Sample authors data
    $author_types = [
        'client' => [
            'names' => [
                'Marie Dubois', 'Jean Martin', 'Sophie Lefebvre', 'Pierre Durant',
                'Claire Moreau', 'Laurent Bernard', 'Isabelle Petit', 'Nicolas Roux',
                'Élise Girard', 'Thomas Blanc', 'Camille Lambert', 'Alexandre Simon'
            ],
            'companies' => [
                'Ville de Bordeaux', 'Conseil Régional Île-de-France', 'SCI Patrimoine',
                'Groupe Immobilier Moderne', 'Association Les Bâtisseurs', 'Mairie de Lyon',
                'Promoteur Urbain SA', 'Foncière Développement', 'Office HLM Métropole'
            ]
        ],
        'professional' => [
            'names' => [
                'Architecte DPLG Anne Rousseau', 'Ingénieur BET Michel Fournier', 'Paysagiste DPLG Sarah Cohen',
                'Urbaniste David Mercier', 'Designer Juliette Faure', 'Bureau d\'études Techniques',
                'Architecte d\'intérieur Marc Lefèvre', 'Consultant HQE Nathalie Bonnet'
            ],
            'companies' => [
                'Atelier d\'Architecture Contemporaine', 'BET Structures Innovantes', 
                'Cabinet d\'Urbanisme et Paysage', 'Agence Design & Espaces',
                'Bureau d\'Études Environnement', 'Conseil en Architecture Durable',
                'Atelier de Recherche Urbaine', 'Studio d\'Architecture Intérieure'
            ]
        ],
        'visitor' => [
            'names' => [
                'Étudiant en Architecture', 'Passionné d\'Architecture', 'Amateur de Design',
                'Curieux', 'Visiteur', 'Explorateur Urbain', 'Amoureux du Patrimoine',
                'Observateur Attentif', 'Admirateur', 'Découvreur de Talents'
            ],
            'companies' => [null, null, null] // Visitors often don't have companies
        ]
    ];
    
    $emails = [
        'contact@exemple.fr', 'info@architecture.fr', 'studio@design.fr',
        'atelier@creation.fr', 'bureau@projet.fr', 'hello@architecture.com',
        'bonjour@agence.fr', 'contact@atelier.fr', 'info@cabinet.fr'
    ];
    
    for ($i = 1; $i <= $count; $i++) {
        // Random author type
        $type_keys = array_keys($author_types);
        $author_type = $type_keys[array_rand($type_keys)];
        
        $author_name = $author_types[$author_type]['names'][array_rand($author_types[$author_type]['names'])];
        $author_email = $emails[array_rand($emails)];
        $author_company = $author_types[$author_type]['companies'][array_rand($author_types[$author_type]['companies'])];
        
        $comment = archi_sample_generate_guestbook_comment($author_type);
        
        // Random date in the past year
        $post_date = archi_sample_random_date('-1 year', 'now');
        
        $post_data = [
            'post_title' => 'Témoignage de ' . $author_name,
            'post_content' => wpautop($comment),
            'post_status' => 'publish',
            'post_type' => 'archi_guestbook',
            'post_author' => get_current_user_id(),
            'post_date' => $post_date,
        ];
        
        $guestbook_id = wp_insert_post($post_data);
        
        if ($guestbook_id && !is_wp_error($guestbook_id)) {
            // Add author metadata
            update_post_meta($guestbook_id, '_archi_guestbook_author_name', $author_name);
            update_post_meta($guestbook_id, '_archi_guestbook_author_email', $author_email);
            if ($author_company) {
                update_post_meta($guestbook_id, '_archi_guestbook_author_company', $author_company);
            }
            
            // Link to random articles (30% chance)
            if (!empty($all_posts) && rand(1, 10) <= 3) {
                $linked_count = rand(1, 3);
                $linked_posts = [];
                for ($j = 0; $j < $linked_count; $j++) {
                    $random_post = $all_posts[array_rand($all_posts)];
                    if (!in_array($random_post, $linked_posts)) {
                        $linked_posts[] = $random_post;
                    }
                }
                if (!empty($linked_posts)) {
                    update_post_meta($guestbook_id, '_archi_linked_articles', $linked_posts);
                }
            }
            
            // Graph metadata (50% chance to show in graph)
            $show_in_graph = rand(0, 1) ? '1' : '0';
            update_post_meta($guestbook_id, '_archi_show_in_graph', $show_in_graph);
            update_post_meta($guestbook_id, '_archi_node_color', '#2ecc71'); // Green for guestbook
            update_post_meta($guestbook_id, '_archi_node_size', 50);
            
            $priorities = ['low', 'normal', 'normal', 'high'];
            update_post_meta($guestbook_id, '_archi_priority_level', $priorities[array_rand($priorities)]);
            
            $stats['guestbook']++;
        } else {
            $stats['errors'][] = 'Erreur lors de la création du témoignage : ' . $author_name;
        }
    }
    
    return $stats;
}

/**
 * Generate sample content
 */
function archi_generate_sample_content($projects_count, $illustrations_count) {
    $sample_data = archi_get_sample_data_config();
    $stats = [
        'projects' => 0,
        'illustrations' => 0,
        'categories' => 0,
        'relationships' => 0,
        'errors' => []
    ];
    
    // Create categories
    foreach ($sample_data['categories'] as $name => $description) {
        $cat_id = archi_sample_get_or_create_category($name, $description);
        if ($cat_id) {
            $stats['categories']++;
        }
    }
    
    // Create project taxonomies
    foreach ($sample_data['project_types'] as $type => $subtypes) {
        archi_sample_get_or_create_term($type, 'archi_project_type');
    }
    
    foreach ($sample_data['project_statuses'] as $status) {
        archi_sample_get_or_create_term($status, 'archi_project_status');
    }
    
    // Create illustration taxonomies
    foreach ($sample_data['illustration_types'] as $type) {
        archi_sample_get_or_create_term($type, 'illustration_type');
    }
    
    // Generate projects
    $project_ids = [];
    for ($i = 1; $i <= $projects_count; $i++) {
        $type_category = array_rand($sample_data['project_types']);
        $subtypes = $sample_data['project_types'][$type_category];
        $subtype = $subtypes[array_rand($subtypes)];
        $location = $sample_data['locations'][array_rand($sample_data['locations'])];
        $status = $sample_data['project_statuses'][array_rand($sample_data['project_statuses'])];
        $client = $sample_data['clients'][array_rand($sample_data['clients'])] . ' ' . $location;
        
        $surface = rand(100, 5000);
        $cost = rand(200000, 5000000);
        $start_date = archi_sample_random_date('-2 years', 'now');
        $end_date = archi_sample_random_date('now', '+2 years');
        
        $post_data = [
            'post_title' => $subtype . ' - ' . $location . ' ' . $i,
            'post_content' => wpautop(archi_sample_generate_project_description($subtype, $location, $surface) . "\n\n" . 
                "Le projet s'étend sur {$surface} m² et mobilise une équipe pluridisciplinaire d'architectes, d'ingénieurs et de paysagistes. " .
                "La livraison est prévue pour " . date('Y', strtotime($end_date)) . "."),
            'post_excerpt' => archi_sample_generate_project_description($subtype, $location, $surface),
            'post_status' => 'publish',
            'post_type' => 'archi_project',
            'post_author' => get_current_user_id(),
            'post_date' => archi_sample_random_date('-1 year', 'now'),
        ];
        
        $project_id = wp_insert_post($post_data);
        
        if ($project_id && !is_wp_error($project_id)) {
            // Add metadata
            update_post_meta($project_id, '_archi_project_surface', $surface);
            update_post_meta($project_id, '_archi_project_cost', $cost);
            update_post_meta($project_id, '_archi_project_client', $client);
            update_post_meta($project_id, '_archi_project_location', $location);
            update_post_meta($project_id, '_archi_project_start_date', $start_date);
            update_post_meta($project_id, '_archi_project_end_date', $end_date);
            update_post_meta($project_id, '_archi_project_bet', 'Bureau d\'Études Technique ' . substr($location, 0, 3));
            
            $cert_count = rand(1, 3);
            $certifications = array_rand(array_flip($sample_data['certifications']), $cert_count);
            if (!is_array($certifications)) {
                $certifications = [$certifications];
            }
            update_post_meta($project_id, '_archi_project_certifications', implode(', ', $certifications));
            
            // Graph metadata
            update_post_meta($project_id, '_archi_show_in_graph', '1');
            update_post_meta($project_id, '_archi_node_color', '#e67e22');
            update_post_meta($project_id, '_archi_node_size', rand(50, 80));
            $priorities = ['low', 'normal', 'normal', 'high'];
            update_post_meta($project_id, '_archi_priority_level', $priorities[array_rand($priorities)]);
            
            // Taxonomies
            $type_term = get_term_by('name', $type_category, 'archi_project_type');
            if ($type_term) {
                wp_set_post_terms($project_id, [$type_term->term_id], 'archi_project_type');
            }
            
            $status_term = get_term_by('name', $status, 'archi_project_status');
            if ($status_term) {
                wp_set_post_terms($project_id, [$status_term->term_id], 'archi_project_status');
            }
            
            // Categories
            $category_names = array_keys($sample_data['categories']);
            $selected_cats = array_rand(array_flip($category_names), rand(1, 2));
            if (!is_array($selected_cats)) {
                $selected_cats = [$selected_cats];
            }
            $cat_ids = [];
            foreach ($selected_cats as $cat_name) {
                $cat_id = archi_sample_get_or_create_category($cat_name, $sample_data['categories'][$cat_name]);
                if ($cat_id) {
                    $cat_ids[] = $cat_id;
                }
            }
            if (!empty($cat_ids)) {
                wp_set_post_categories($project_id, $cat_ids);
            }
            
            $project_ids[] = $project_id;
            $stats['projects']++;
        } else {
            $stats['errors'][] = 'Error creating project: ' . $post_data['post_title'];
        }
    }
    
    // Generate illustrations
    $illustration_ids = [];
    for ($i = 1; $i <= $illustrations_count; $i++) {
        $illus_type = $sample_data['illustration_types'][array_rand($sample_data['illustration_types'])];
        $technique_category = array_keys($sample_data['techniques']);
        $technique = $technique_category[array_rand($technique_category)];
        $software_list = $sample_data['techniques'][$technique];
        $software = $software_list[array_rand($software_list)];
        
        $dimensions_options = [
            '1920x1080px',
            '3840x2160px (4K)',
            'A3 (297x420mm)',
            'A2 (420x594mm)',
            '60x40cm',
            '100x70cm',
        ];
        $dimensions = $dimensions_options[array_rand($dimensions_options)];
        
        $post_data = [
            'post_title' => $illus_type . ' ' . $i . ' - ' . $technique,
            'post_content' => wpautop(archi_sample_generate_illustration_description($illus_type, $technique) . "\n\n" .
                "Technique utilisée : {$technique}\n" .
                "Logiciel : {$software}\n" .
                "Dimensions : {$dimensions}"),
            'post_excerpt' => archi_sample_generate_illustration_description($illus_type, $technique),
            'post_status' => 'publish',
            'post_type' => 'archi_illustration',
            'post_author' => get_current_user_id(),
            'post_date' => archi_sample_random_date('-1 year', 'now'),
        ];
        
        $illustration_id = wp_insert_post($post_data);
        
        if ($illustration_id && !is_wp_error($illustration_id)) {
            update_post_meta($illustration_id, '_archi_illustration_technique', $technique);
            update_post_meta($illustration_id, '_archi_illustration_dimensions', $dimensions);
            update_post_meta($illustration_id, '_archi_illustration_software', $software);
            
            if (!empty($project_ids) && rand(0, 1)) {
                $linked_project_id = $project_ids[array_rand($project_ids)];
                $linked_project_url = get_permalink($linked_project_id);
                update_post_meta($illustration_id, '_archi_illustration_project_link', $linked_project_url);
            }
            
            update_post_meta($illustration_id, '_archi_show_in_graph', '1');
            update_post_meta($illustration_id, '_archi_node_color', '#9b59b6');
            update_post_meta($illustration_id, '_archi_node_size', rand(40, 60));
            $priorities = ['low', 'normal', 'normal', 'high'];
            update_post_meta($illustration_id, '_archi_priority_level', $priorities[array_rand($priorities)]);
            
            $type_term = get_term_by('name', $illus_type, 'illustration_type');
            if ($type_term) {
                wp_set_post_terms($illustration_id, [$type_term->term_id], 'illustration_type');
            }
            
            $category_names = array_keys($sample_data['categories']);
            $selected_cats = array_rand(array_flip($category_names), rand(1, 2));
            if (!is_array($selected_cats)) {
                $selected_cats = [$selected_cats];
            }
            $cat_ids = [];
            foreach ($selected_cats as $cat_name) {
                $cat_id = archi_sample_get_or_create_category($cat_name, $sample_data['categories'][$cat_name]);
                if ($cat_id) {
                    $cat_ids[] = $cat_id;
                }
            }
            if (!empty($cat_ids)) {
                wp_set_post_categories($illustration_id, $cat_ids);
            }
            
            $illustration_ids[] = $illustration_id;
            $stats['illustrations']++;
        } else {
            $stats['errors'][] = 'Error creating illustration: ' . $post_data['post_title'];
        }
    }
    
    // Create relationships
    $all_ids = array_merge($project_ids, $illustration_ids);
    foreach ($all_ids as $post_id) {
        $relationship_count = rand(2, 4);
        $related = [];
        
        for ($j = 0; $j < $relationship_count; $j++) {
            $related_id = $all_ids[array_rand($all_ids)];
            if ($related_id != $post_id && !in_array($related_id, $related)) {
                $related[] = $related_id;
            }
        }
        
        if (!empty($related)) {
            update_post_meta($post_id, '_archi_related_articles', $related);
            $stats['relationships'] += count($related);
        }
    }
    
    return $stats;
}

/**
 * Admin page content
 */
function archi_sample_data_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('Vous n\'avez pas les permissions nécessaires.', 'archi-graph'));
    }
    
    $generated = false;
    $stats = null;
    
    // Handle form submission
    if (isset($_POST['archi_generate_sample']) && check_admin_referer('archi_sample_data_generate', 'archi_sample_nonce')) {
        $projects_count = absint($_POST['projects_count']);
        $illustrations_count = absint($_POST['illustrations_count']);
        
        $stats = archi_generate_sample_content($projects_count, $illustrations_count);
        $generated = true;
    }
    
    // Handle guestbook generation
    if (isset($_POST['archi_generate_guestbook']) && check_admin_referer('archi_guestbook_generate', 'archi_guestbook_nonce')) {
        $guestbook_count = absint($_POST['guestbook_count']);
        
        $stats = archi_generate_sample_guestbook($guestbook_count);
        $generated = true;
    }
    
    ?>
    <div class="wrap">
        <h1>🏗️ <?php _e('Générateur de Contenu Archi-Graph', 'archi-graph'); ?></h1>
        
        <?php if ($generated && $stats): ?>
            <div class="notice notice-success is-dismissible">
                <h2><?php _e('✅ Génération terminée avec succès!', 'archi-graph'); ?></h2>
                
                <?php if (isset($stats['projects'])): ?>
                    <p><strong><?php echo $stats['projects']; ?></strong> <?php _e('projets architecturaux créés', 'archi-graph'); ?></p>
                    <p><strong><?php echo $stats['illustrations']; ?></strong> <?php _e('illustrations créées', 'archi-graph'); ?></p>
                    <p><strong><?php echo $stats['categories']; ?></strong> <?php _e('catégories créées', 'archi-graph'); ?></p>
                    <p><strong><?php echo $stats['relationships']; ?></strong> <?php _e('relations créées', 'archi-graph'); ?></p>
                <?php endif; ?>
                
                <?php if (isset($stats['guestbook'])): ?>
                    <p><strong><?php echo $stats['guestbook']; ?></strong> <?php _e('témoignages créés', 'archi-graph'); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($stats['errors'])): ?>
                    <div style="margin-top: 15px; color: #856404;">
                        <strong><?php _e('Avertissements:', 'archi-graph'); ?></strong>
                        <ul>
                            <?php foreach ($stats['errors'] as $error): ?>
                                <li><?php echo esc_html($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div style="margin-top: 15px;">
                    <?php if (isset($stats['projects'])): ?>
                        <a href="<?php echo admin_url('edit.php?post_type=archi_project'); ?>" class="button button-primary">
                            <?php _e('Voir les projets', 'archi-graph'); ?>
                        </a>
                        <a href="<?php echo admin_url('edit.php?post_type=archi_illustration'); ?>" class="button button-primary">
                            <?php _e('Voir les illustrations', 'archi-graph'); ?>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (isset($stats['guestbook'])): ?>
                        <a href="<?php echo admin_url('edit.php?post_type=archi_guestbook'); ?>" class="button button-primary">
                            <?php _e('Voir les témoignages', 'archi-graph'); ?>
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?php echo home_url(); ?>" class="button button-secondary">
                        <?php _e('Voir le graphique', 'archi-graph'); ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Projects & Illustrations Generator -->
        <div class="card" style="max-width: 800px;">
            <h2><?php _e('Générer du contenu d\'exemple', 'archi-graph'); ?></h2>
            <p><?php _e('Ce générateur crée automatiquement des projets architecturaux et des illustrations avec toutes leurs métadonnées, taxonomies et relations pour le graphique.', 'archi-graph'); ?></p>
            
            <form method="post" action="">
                <?php wp_nonce_field('archi_sample_data_generate', 'archi_sample_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="projects_count"><?php _e('Nombre de projets', 'archi-graph'); ?></label>
                        </th>
                        <td>
                            <input type="number" name="projects_count" id="projects_count" value="15" min="1" max="100" class="small-text">
                            <p class="description"><?php _e('Nombre de projets architecturaux à créer (1-100)', 'archi-graph'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="illustrations_count"><?php _e('Nombre d\'illustrations', 'archi-graph'); ?></label>
                        </th>
                        <td>
                            <input type="number" name="illustrations_count" id="illustrations_count" value="20" min="1" max="100" class="small-text">
                            <p class="description"><?php _e('Nombre d\'illustrations à créer (1-100)', 'archi-graph'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="archi_generate_sample" class="button button-primary button-large" value="<?php _e('🚀 Générer le contenu', 'archi-graph'); ?>">
                </p>
            </form>
            
            <hr>
            
            <h3><?php _e('📋 Ce qui sera créé:', 'archi-graph'); ?></h3>
            <ul style="list-style: disc; margin-left: 20px;">
                <li><?php _e('Projets architecturaux avec métadonnées complètes (surface, coût, client, lieu, dates, certifications)', 'archi-graph'); ?></li>
                <li><?php _e('Illustrations avec techniques, logiciels et dimensions', 'archi-graph'); ?></li>
                <li><?php _e('Catégories thématiques (Architecture Durable, Patrimoine, Innovation, etc.)', 'archi-graph'); ?></li>
                <li><?php _e('Taxonomies (types de projets, statuts, types d\'illustrations)', 'archi-graph'); ?></li>
                <li><?php _e('Métadonnées pour le graphique (couleurs, tailles, positions)', 'archi-graph'); ?></li>
                <li><?php _e('Relations manuelles entre les contenus', 'archi-graph'); ?></li>
            </ul>
            
            <div class="notice notice-info inline" style="margin: 20px 0;">
                <p><strong><?php _e('Note:', 'archi-graph'); ?></strong> <?php _e('Vous pouvez exécuter ce générateur plusieurs fois. Les catégories et taxonomies ne seront pas dupliquées.', 'archi-graph'); ?></p>
            </div>
        </div>
        
        <!-- Guestbook Generator -->
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2>💬 <?php _e('Générer des témoignages du livre d\'or', 'archi-graph'); ?></h2>
            <p><?php _e('Ce générateur crée des témoignages réalistes de clients, professionnels et visiteurs avec leurs métadonnées et liens vers vos contenus.', 'archi-graph'); ?></p>
            
            <form method="post" action="">
                <?php wp_nonce_field('archi_guestbook_generate', 'archi_guestbook_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="guestbook_count"><?php _e('Nombre de témoignages', 'archi-graph'); ?></label>
                        </th>
                        <td>
                            <input type="number" name="guestbook_count" id="guestbook_count" value="10" min="1" max="50" class="small-text">
                            <p class="description"><?php _e('Nombre de témoignages à créer (1-50)', 'archi-graph'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="archi_generate_guestbook" class="button button-primary button-large" value="<?php _e('💬 Générer les témoignages', 'archi-graph'); ?>">
                </p>
            </form>
            
            <hr>
            
            <h3><?php _e('📋 Ce qui sera créé:', 'archi-graph'); ?></h3>
            <ul style="list-style: disc; margin-left: 20px;">
                <li><?php _e('Témoignages variés de clients satisfaits', 'archi-graph'); ?></li>
                <li><?php _e('Commentaires de professionnels du secteur', 'archi-graph'); ?></li>
                <li><?php _e('Retours de visiteurs et passionnés d\'architecture', 'archi-graph'); ?></li>
                <li><?php _e('Métadonnées complètes (nom, email, entreprise)', 'archi-graph'); ?></li>
                <li><?php _e('Liens vers vos projets et illustrations (30% des témoignages)', 'archi-graph'); ?></li>
                <li><?php _e('Intégration au graphique (50% des témoignages)', 'archi-graph'); ?></li>
            </ul>
            
            <div class="notice notice-warning inline" style="margin: 20px 0;">
                <p><strong><?php _e('Important:', 'archi-graph'); ?></strong> <?php _e('Pour des liens pertinents, générez d\'abord des projets et illustrations avant de créer des témoignages.', 'archi-graph'); ?></p>
            </div>
        </div>
    </div>
    
    <style>
        .wrap .card {
            padding: 20px;
            margin-top: 20px;
        }
        .wrap h2 {
            margin-top: 0;
        }
    </style>
    <?php
}
