<?php
/**
 * Attributs partagés entre les blocs
 * Évite la duplication de code
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe d'attributs partagés
 */
class Archi_Shared_Block_Attributes {
    
    /**
     * Attributs communs pour l'affichage
     */
    public static function get_display_attributes() {
        return [
            'align' => [
                'type' => 'string',
                'default' => ''
            ],
            'className' => [
                'type' => 'string',
                'default' => ''
            ]
        ];
    }
    
    /**
     * Attributs pour les couleurs
     */
    public static function get_color_attributes() {
        return [
            'backgroundColor' => [
                'type' => 'string',
                'default' => ''
            ],
            'textColor' => [
                'type' => 'string',
                'default' => ''
            ],
            'customBackgroundColor' => [
                'type' => 'string',
                'default' => ''
            ],
            'customTextColor' => [
                'type' => 'string',
                'default' => ''
            ]
        ];
    }
    
    /**
     * Attributs pour les images
     */
    public static function get_image_attributes() {
        return [
            'imageId' => [
                'type' => 'number',
                'default' => 0
            ],
            'imageUrl' => [
                'type' => 'string',
                'default' => ''
            ],
            'imageAlt' => [
                'type' => 'string',
                'default' => ''
            ],
            'imageSize' => [
                'type' => 'string',
                'default' => 'medium'
            ]
        ];
    }
    
    /**
     * Attributs pour les layouts
     */
    public static function get_layout_attributes() {
        return [
            'layout' => [
                'type' => 'string',
                'default' => 'grid'
            ],
            'columns' => [
                'type' => 'number',
                'default' => 3
            ],
            'gap' => [
                'type' => 'number',
                'default' => 20
            ]
        ];
    }
    
    /**
     * Attributs pour les filtres
     */
    public static function get_filter_attributes() {
        return [
            'categories' => [
                'type' => 'array',
                'default' => []
            ],
            'postTypes' => [
                'type' => 'array',
                'default' => ['post']
            ],
            'tags' => [
                'type' => 'array',
                'default' => []
            ],
            'maxItems' => [
                'type' => 'number',
                'default' => 10
            ],
            'orderBy' => [
                'type' => 'string',
                'default' => 'date'
            ],
            'order' => [
                'type' => 'string',
                'default' => 'DESC'
            ]
        ];
    }
    
    /**
     * Attributs pour les options d'affichage
     */
    public static function get_visibility_attributes() {
        return [
            'showTitle' => [
                'type' => 'boolean',
                'default' => true
            ],
            'showExcerpt' => [
                'type' => 'boolean',
                'default' => true
            ],
            'showDate' => [
                'type' => 'boolean',
                'default' => true
            ],
            'showAuthor' => [
                'type' => 'boolean',
                'default' => true
            ],
            'showCategories' => [
                'type' => 'boolean',
                'default' => true
            ],
            'showTags' => [
                'type' => 'boolean',
                'default' => false
            ],
            'showFeaturedImage' => [
                'type' => 'boolean',
                'default' => true
            ]
        ];
    }
    
    /**
     * Combiner plusieurs ensembles d'attributs
     */
    public static function merge_attributes(...$attribute_sets) {
        $merged = [];
        
        foreach ($attribute_sets as $set) {
            if (is_callable($set)) {
                $set = call_user_func($set);
            }
            
            if (is_array($set)) {
                $merged = array_merge($merged, $set);
            }
        }
        
        return $merged;
    }
}
