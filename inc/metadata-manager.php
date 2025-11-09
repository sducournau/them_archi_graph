<?php
/**
 * Gestionnaire centralisé des métadonnées
 * Validation, sanitization et gestion cohérente des metadata
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe centrale de gestion des métadonnées
 */
class Archi_Metadata_Manager {
    
    /**
     * Définitions des métadonnées par catégorie
     */
    private static $meta_definitions = [
        // Métadonnées du graphique (tous post types)
        'graph' => [
            '_archi_show_in_graph' => [
                'type' => 'boolean',
                'default' => '0',
                'sanitize' => 'absint',
                'validate' => ['in' => ['0', '1']],
                'label' => 'Afficher dans le graphique'
            ],
            '_archi_node_color' => [
                'type' => 'color',
                'default' => '#3498db',
                'sanitize' => 'sanitize_hex_color',
                'validate' => ['pattern' => '/^#[0-9A-F]{6}$/i'],
                'label' => 'Couleur du nœud'
            ],
            '_archi_node_size' => [
                'type' => 'number',
                'default' => 60,
                'sanitize' => 'absint',
                'validate' => ['min' => 40, 'max' => 120],
                'label' => 'Taille du nœud'
            ],
            '_archi_priority_level' => [
                'type' => 'select',
                'default' => 'normal',
                'sanitize' => 'sanitize_text_field',
                'validate' => ['in' => ['low', 'normal', 'high', 'featured']],
                'label' => 'Niveau de priorité'
            ],
            '_archi_graph_position' => [
                'type' => 'array',
                'default' => ['x' => 0, 'y' => 0],
                'sanitize' => 'archi_sanitize_position',
                'validate' => 'archi_validate_position',
                'label' => 'Position dans le graphe'
            ],
            '_archi_related_articles' => [
                'type' => 'array',
                'default' => [],
                'sanitize' => 'archi_sanitize_post_ids',
                'validate' => 'archi_validate_post_ids',
                'label' => 'Articles reliés manuellement'
            ]
        ],
        
        // Métadonnées spécifiques aux projets
        'project' => [
            '_archi_project_surface' => [
                'type' => 'number',
                'default' => 0,
                'sanitize' => 'floatval',
                'validate' => ['min' => 0],
                'label' => 'Surface (m²)',
                'unit' => 'm²'
            ],
            '_archi_project_cost' => [
                'type' => 'number',
                'default' => 0,
                'sanitize' => 'absint',
                'validate' => ['min' => 0],
                'label' => 'Coût estimatif',
                'unit' => '€'
            ],
            '_archi_project_client' => [
                'type' => 'text',
                'default' => '',
                'sanitize' => 'sanitize_text_field',
                'validate' => ['max_length' => 200],
                'label' => 'Client'
            ],
            '_archi_project_location' => [
                'type' => 'text',
                'default' => '',
                'sanitize' => 'sanitize_text_field',
                'validate' => ['max_length' => 200],
                'label' => 'Localisation'
            ],
            '_archi_project_year' => [
                'type' => 'number',
                'default' => 0,
                'sanitize' => 'absint',
                'validate' => ['min' => 1900, 'max' => 2100],
                'label' => 'Année de réalisation'
            ],
            '_archi_project_duration' => [
                'type' => 'text',
                'default' => '',
                'sanitize' => 'sanitize_text_field',
                'validate' => ['max_length' => 100],
                'label' => 'Durée des travaux'
            ],
            '_archi_project_team' => [
                'type' => 'text',
                'default' => '',
                'sanitize' => 'sanitize_textarea_field',
                'validate' => ['max_length' => 500],
                'label' => 'Équipe du projet'
            ]
        ],
        
        // Métadonnées spécifiques aux illustrations
        'illustration' => [
            '_archi_illustration_technique' => [
                'type' => 'text',
                'default' => '',
                'sanitize' => 'sanitize_text_field',
                'validate' => ['max_length' => 100],
                'label' => 'Technique utilisée'
            ],
            '_archi_illustration_software' => [
                'type' => 'text',
                'default' => '',
                'sanitize' => 'sanitize_text_field',
                'validate' => ['max_length' => 100],
                'label' => 'Logiciel'
            ],
            '_archi_illustration_dimensions' => [
                'type' => 'text',
                'default' => '',
                'sanitize' => 'sanitize_text_field',
                'validate' => ['max_length' => 50],
                'label' => 'Dimensions'
            ],
            '_archi_illustration_year' => [
                'type' => 'number',
                'default' => 0,
                'sanitize' => 'absint',
                'validate' => ['min' => 1900, 'max' => 2100],
                'label' => 'Année de création'
            ]
        ]
    ];
    
    /**
     * Obtenir la définition d'une métadonnée
     */
    public static function get_definition($meta_key, $category = null) {
        // Si catégorie spécifiée, chercher dedans
        if ($category && isset(self::$meta_definitions[$category][$meta_key])) {
            return self::$meta_definitions[$category][$meta_key];
        }
        
        // Sinon chercher dans toutes les catégories
        foreach (self::$meta_definitions as $cat => $metas) {
            if (isset($metas[$meta_key])) {
                return $metas[$meta_key];
            }
        }
        
        return null;
    }
    
    /**
     * Obtenir toutes les définitions d'une catégorie
     */
    public static function get_category_definitions($category) {
        return self::$meta_definitions[$category] ?? [];
    }
    
    /**
     * Valider une valeur selon sa définition
     */
    public static function validate($meta_key, $value, $category = null) {
        $definition = self::get_definition($meta_key, $category);
        
        if (!$definition) {
            return new WP_Error('invalid_meta', sprintf(__('Métadonnée inconnue: %s', 'archi-graph'), $meta_key));
        }
        
        $validation = $definition['validate'] ?? null;
        
        if (!$validation) {
            return true; // Pas de validation définie
        }
        
        // Validation personnalisée (fonction)
        if (is_callable($validation)) {
            return call_user_func($validation, $value);
        }
        
        // Validation par règles
        if (is_array($validation)) {
            // Valeurs autorisées (enum)
            if (isset($validation['in'])) {
                if (!in_array($value, $validation['in'], true)) {
                    return new WP_Error('invalid_value', sprintf(
                        __('Valeur invalide pour %s. Valeurs autorisées: %s', 'archi-graph'),
                        $meta_key,
                        implode(', ', $validation['in'])
                    ));
                }
            }
            
            // Valeur minimum
            if (isset($validation['min'])) {
                if (is_numeric($value) && $value < $validation['min']) {
                    return new WP_Error('value_too_small', sprintf(
                        __('La valeur doit être >= %d', 'archi-graph'),
                        $validation['min']
                    ));
                }
            }
            
            // Valeur maximum
            if (isset($validation['max'])) {
                if (is_numeric($value) && $value > $validation['max']) {
                    return new WP_Error('value_too_large', sprintf(
                        __('La valeur doit être <= %d', 'archi-graph'),
                        $validation['max']
                    ));
                }
            }
            
            // Longueur maximum (chaînes)
            if (isset($validation['max_length'])) {
                if (is_string($value) && strlen($value) > $validation['max_length']) {
                    return new WP_Error('value_too_long', sprintf(
                        __('La valeur ne doit pas dépasser %d caractères', 'archi-graph'),
                        $validation['max_length']
                    ));
                }
            }
            
            // Pattern (regex)
            if (isset($validation['pattern'])) {
                if (!preg_match($validation['pattern'], $value)) {
                    return new WP_Error('invalid_format', __('Format invalide', 'archi-graph'));
                }
            }
        }
        
        return true;
    }
    
    /**
     * Sanitizer une valeur selon sa définition
     */
    public static function sanitize($meta_key, $value, $category = null) {
        $definition = self::get_definition($meta_key, $category);
        
        if (!$definition) {
            return sanitize_text_field($value); // Fallback
        }
        
        $sanitize_fn = $definition['sanitize'] ?? 'sanitize_text_field';
        
        if (is_callable($sanitize_fn)) {
            return call_user_func($sanitize_fn, $value);
        }
        
        return sanitize_text_field($value);
    }
    
    /**
     * Obtenir une métadonnée avec valeur par défaut
     */
    public static function get_meta($post_id, $meta_key, $category = null) {
        $definition = self::get_definition($meta_key, $category);
        $default = $definition['default'] ?? '';
        
        $value = get_post_meta($post_id, $meta_key, true);
        
        // Si vide, retourner valeur par défaut
        if ($value === '' || $value === false) {
            return $default;
        }
        
        return $value;
    }
    
    /**
     * Mettre à jour une métadonnée avec validation et sanitization
     */
    public static function update_meta($post_id, $meta_key, $value, $category = null) {
        // Sanitizer
        $sanitized_value = self::sanitize($meta_key, $value, $category);
        
        // Valider
        $validation_result = self::validate($meta_key, $sanitized_value, $category);
        
        if (is_wp_error($validation_result)) {
            return $validation_result;
        }
        
        // Mettre à jour
        update_post_meta($post_id, $meta_key, $sanitized_value);
        
        return true;
    }
    
    /**
     * Obtenir toutes les métadonnées d'un post avec leurs définitions
     */
    public static function get_all_meta($post_id, $category = null) {
        $categories = $category ? [$category] : array_keys(self::$meta_definitions);
        $result = [];
        
        foreach ($categories as $cat) {
            $definitions = self::get_category_definitions($cat);
            foreach ($definitions as $key => $definition) {
                $result[$key] = [
                    'value' => self::get_meta($post_id, $key, $cat),
                    'definition' => $definition
                ];
            }
        }
        
        return $result;
    }
}

/**
 * Fonctions helper de sanitization personnalisées
 */

function archi_sanitize_position($value) {
    if (!is_array($value)) {
        return ['x' => 0, 'y' => 0];
    }
    
    return [
        'x' => isset($value['x']) ? intval($value['x']) : 0,
        'y' => isset($value['y']) ? intval($value['y']) : 0
    ];
}

function archi_validate_position($value) {
    if (!is_array($value) || !isset($value['x']) || !isset($value['y'])) {
        return new WP_Error('invalid_position', __('Position invalide. Format: ["x" => int, "y" => int]', 'archi-graph'));
    }
    
    return true;
}

function archi_sanitize_post_ids($value) {
    if (!is_array($value)) {
        return [];
    }
    
    return array_map('absint', $value);
}

function archi_validate_post_ids($value) {
    if (!is_array($value)) {
        return new WP_Error('invalid_post_ids', __('Les IDs doivent être un tableau', 'archi-graph'));
    }
    
    foreach ($value as $id) {
        if (!is_numeric($id) || $id <= 0) {
            return new WP_Error('invalid_post_id', sprintf(__('ID de post invalide: %s', 'archi-graph'), $id));
        }
    }
    
    return true;
}

/**
 * API simplifiée pour l'utilisation courante
 */

function archi_get_graph_meta($post_id, $meta_key) {
    return Archi_Metadata_Manager::get_meta($post_id, $meta_key, 'graph');
}

function archi_update_graph_meta($post_id, $meta_key, $value) {
    return Archi_Metadata_Manager::update_meta($post_id, $meta_key, $value, 'graph');
}

function archi_get_project_meta($post_id, $meta_key) {
    return Archi_Metadata_Manager::get_meta($post_id, $meta_key, 'project');
}

function archi_update_project_meta($post_id, $meta_key, $value) {
    return Archi_Metadata_Manager::update_meta($post_id, $meta_key, $value, 'project');
}

function archi_get_illustration_meta($post_id, $meta_key) {
    return Archi_Metadata_Manager::get_meta($post_id, $meta_key, 'illustration');
}

function archi_update_illustration_meta($post_id, $meta_key, $value) {
    return Archi_Metadata_Manager::update_meta($post_id, $meta_key, $value, 'illustration');
}
