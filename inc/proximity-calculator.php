<?php
/**
 * Proximity Calculator - PHP Version
 * Calcul avancé de proximité entre contenus avec analyse sémantique
 * 
 * @package Archi-Graph
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe pour le calcul avancé de proximité
 * 
 * @since 1.0.0
 */
class Archi_Proximity_Calculator {
    
    /**
     * Poids des différents facteurs de proximité
     */
    const WEIGHTS = [
        'shared_category' => 40,
        'shared_tag' => 25,
        'same_primary_category' => 20,
        'same_post_type' => 15,
        'date_proximity' => 10,
        'content_similarity' => 15,        // Augmenté
        'title_similarity' => 20,          // Nouveau
        'location_match' => 25,            // Nouveau (projets)
        'client_match' => 30,              // Nouveau (projets)
        'technique_match' => 20,           // Illustrations
        'software_match' => 20,            // Illustrations
        'project_illustration_link' => 50, // Lien direct
        'custom_relationship' => 30,       // Manuel
        'author_match' => 10,              // Nouveau
        'budget_similarity' => 15,         // Nouveau (projets)
        'named_entity_match' => 30,        // Entités nommées communes
        'taxonomy_similarity' => 35,       // Taxonomies personnalisées communes
        'excerpt_similarity' => 18,        // Similarité des extraits
    ];
    
    /**
     * Entités nommées à détecter automatiquement
     */
    const NAMED_ENTITIES = [
        'software' => ['sketchup', 'revit', 'archicad', 'autocad', 'rhino', '3ds max', 'blender', 
                       'vray', 'lumion', 'enscape', 'photoshop', 'illustrator', 'indesign'],
        'materials' => ['béton', 'bois', 'acier', 'verre', 'pierre', 'brique', 'aluminium', 
                        'zinc', 'cuivre', 'terre', 'paille', 'bambou'],
        'techniques' => ['modélisation', 'rendu', '3d', 'croquis', 'dessin', 'maquette', 
                         'photoréaliste', 'photomontage', 'collage', 'aquarelle'],
        'styles' => ['moderne', 'contemporain', 'minimaliste', 'industriel', 'écologique', 
                     'traditionnel', 'baroque', 'art déco', 'brutaliste', 'high-tech'],
        'building_types' => ['villa', 'immeuble', 'école', 'musée', 'bibliothèque', 'hôpital',
                            'théâtre', 'stade', 'gare', 'aéroport', 'centre', 'complexe']
    ];
    
    /**
     * Cache pour les calculs de similarité
     */
    private static $similarity_cache = [];
    
    /**
     * Calculer le score de proximité enrichi
     * 
     * @param array $article_a Premier article
     * @param array $article_b Deuxième article
     * @return array Score et détails
     * @since 1.0.0
     */
    public static function calculate_proximity($article_a, $article_b) {
        $score = 0;
        $details = [
            'factors' => [],
            'shared_categories' => [],
            'shared_tags' => [],
            'metadata_matches' => [],
        ];
        
        // 1. Catégories partagées (base existante)
        $shared_cats = self::find_shared_terms($article_a['categories'], $article_b['categories']);
        if (!empty($shared_cats)) {
            $cat_score = self::WEIGHTS['shared_category'] * count($shared_cats);
            $score += $cat_score;
            $details['shared_categories'] = $shared_cats;
            $details['factors']['categories'] = $cat_score;
        }
        
        // 2. Tags partagés (base existante)
        $shared_tags = self::find_shared_terms($article_a['tags'], $article_b['tags']);
        if (!empty($shared_tags)) {
            $tag_score = self::WEIGHTS['shared_tag'] * count($shared_tags);
            $score += $tag_score;
            $details['shared_tags'] = $shared_tags;
            $details['factors']['tags'] = $tag_score;
        }
        
        // 3. NOUVEAU : Similarité sémantique du titre
        $title_sim = self::calculate_text_similarity(
            $article_a['title'],
            $article_b['title']
        );
        if ($title_sim > 0.3) { // Seuil de 30%
            $title_score = round(self::WEIGHTS['title_similarity'] * $title_sim);
            $score += $title_score;
            $details['factors']['title_similarity'] = $title_score;
            $details['title_similarity_percent'] = round($title_sim * 100);
        }
        
        // 4. NOUVEAU : Similarité sémantique du contenu
        if (!empty($article_a['excerpt']) && !empty($article_b['excerpt'])) {
            $content_sim = self::calculate_text_similarity(
                $article_a['excerpt'],
                $article_b['excerpt']
            );
            if ($content_sim > 0.2) { // Seuil de 20%
                $content_score = round(self::WEIGHTS['content_similarity'] * $content_sim);
                $score += $content_score;
                $details['factors']['content_similarity'] = $content_score;
                $details['content_similarity_percent'] = round($content_sim * 100);
            }
        }
        
        // 5. Même type de post
        if ($article_a['post_type'] === $article_b['post_type']) {
            $score += self::WEIGHTS['same_post_type'];
            $details['factors']['same_post_type'] = self::WEIGHTS['same_post_type'];
        }
        
        // 6. Même auteur
        $author_a = get_post_field('post_author', $article_a['id']);
        $author_b = get_post_field('post_author', $article_b['id']);
        if ($author_a === $author_b) {
            $score += self::WEIGHTS['author_match'];
            $details['factors']['author_match'] = self::WEIGHTS['author_match'];
        }
        
        // 7. NOUVEAU : Métadonnées spécifiques aux projets architecturaux
        if ($article_a['post_type'] === 'archi_project' && $article_b['post_type'] === 'archi_project') {
            $project_matches = self::calculate_project_proximity($article_a['id'], $article_b['id']);
            if (!empty($project_matches['score'])) {
                $score += $project_matches['score'];
                $details['metadata_matches'] = array_merge($details['metadata_matches'], $project_matches['matches']);
                $details['factors'] = array_merge($details['factors'], $project_matches['factors']);
            }
        }
        
        // 8. NOUVEAU : Métadonnées spécifiques aux illustrations
        if ($article_a['post_type'] === 'archi_illustration' && $article_b['post_type'] === 'archi_illustration') {
            $illust_matches = self::calculate_illustration_proximity($article_a, $article_b);
            if (!empty($illust_matches['score'])) {
                $score += $illust_matches['score'];
                $details['metadata_matches'] = array_merge($details['metadata_matches'], $illust_matches['matches']);
                $details['factors'] = array_merge($details['factors'], $illust_matches['factors']);
            }
        }
        
        // 9. Lien direct illustration -> projet
        if (($article_a['post_type'] === 'archi_illustration' && $article_b['post_type'] === 'archi_project') ||
            ($article_b['post_type'] === 'archi_illustration' && $article_a['post_type'] === 'archi_project')) {
            
            $illust_id = $article_a['post_type'] === 'archi_illustration' ? $article_a['id'] : $article_b['id'];
            $project_id = $article_a['post_type'] === 'archi_project' ? $article_a['id'] : $article_b['id'];
            
            $project_link = get_post_meta($illust_id, '_archi_illustration_project_link', true);
            if (!empty($project_link)) {
                $linked_id = self::extract_post_id_from_url($project_link);
                if ($linked_id == $project_id) {
                    $score += self::WEIGHTS['project_illustration_link'];
                    $details['factors']['project_illustration_link'] = self::WEIGHTS['project_illustration_link'];
                    $details['direct_link'] = true;
                }
            }
        }
        
        // 10. Relations manuelles
        $related_ids = get_post_meta($article_a['id'], '_archi_related_articles', true);
        if (is_array($related_ids) && in_array($article_b['id'], $related_ids)) {
            $score += self::WEIGHTS['custom_relationship'];
            $details['factors']['custom_relationship'] = self::WEIGHTS['custom_relationship'];
            $details['manual_relation'] = true;
        }
        
        // 11. Proximité temporelle
        if (!empty($article_a['date']) && !empty($article_b['date'])) {
            $date_score = self::calculate_date_proximity($article_a['date'], $article_b['date']);
            if ($date_score > 0) {
                $score += $date_score;
                $details['factors']['date_proximity'] = $date_score;
            }
        }
        
        // 12. NOUVEAU : Détection d'entités nommées communes
        $entities_match = self::detect_common_named_entities($article_a, $article_b);
        if (!empty($entities_match['score'])) {
            $score += $entities_match['score'];
            $details['named_entities'] = $entities_match['entities'];
            $details['factors']['named_entities'] = $entities_match['score'];
        }
        
        // 13. NOUVEAU : Taxonomies personnalisées communes
        $taxonomy_score = self::calculate_custom_taxonomy_similarity($article_a, $article_b);
        if ($taxonomy_score > 0) {
            $score += $taxonomy_score;
            $details['factors']['custom_taxonomies'] = $taxonomy_score;
        }
        
        // 14. NOUVEAU : Similarité des extraits (plus précis que le contenu complet)
        if (!empty($article_a['excerpt']) && !empty($article_b['excerpt'])) {
            $excerpt_sim = self::calculate_text_similarity($article_a['excerpt'], $article_b['excerpt']);
            if ($excerpt_sim > 0.25) {
                $excerpt_score = round(self::WEIGHTS['excerpt_similarity'] * $excerpt_sim);
                $score += $excerpt_score;
                $details['factors']['excerpt_similarity'] = $excerpt_score;
                $details['excerpt_similarity_percent'] = round($excerpt_sim * 100);
            }
        }
        
        // Déterminer la force
        $strength = self::determine_strength($score);
        
        return [
            'score' => round($score),
            'strength' => $strength,
            'details' => $details,
            'timestamp' => current_time('timestamp')
        ];
    }
    
    /**
     * NOUVEAU : Détecter les entités nommées communes entre deux articles
     * 
     * @param array $article_a Premier article
     * @param array $article_b Deuxième article
     * @return array Score et entités trouvées
     */
    private static function detect_common_named_entities($article_a, $article_b) {
        $entities = [];
        $score = 0;
        
        // Combiner titre + excerpt pour l'analyse
        $text_a = mb_strtolower($article_a['title'] . ' ' . ($article_a['excerpt'] ?? ''));
        $text_b = mb_strtolower($article_b['title'] . ' ' . ($article_b['excerpt'] ?? ''));
        
        foreach (self::NAMED_ENTITIES as $entity_type => $entity_list) {
            $found_in_both = [];
            
            foreach ($entity_list as $entity) {
                if (strpos($text_a, $entity) !== false && strpos($text_b, $entity) !== false) {
                    $found_in_both[] = $entity;
                }
            }
            
            if (!empty($found_in_both)) {
                $entities[$entity_type] = $found_in_both;
                // Plus d'entités communes = score plus élevé
                $score += self::WEIGHTS['named_entity_match'] * (count($found_in_both) * 0.5);
            }
        }
        
        return [
            'score' => min($score, self::WEIGHTS['named_entity_match'] * 2), // Plafonner
            'entities' => $entities
        ];
    }
    
    /**
     * NOUVEAU : Calculer la similarité des taxonomies personnalisées
     * 
     * @param array $article_a Premier article
     * @param array $article_b Deuxième article
     * @return int Score
     */
    private static function calculate_custom_taxonomy_similarity($article_a, $article_b) {
        $score = 0;
        
        // Taxonomies de projet
        if ($article_a['post_type'] === 'archi_project' && $article_b['post_type'] === 'archi_project') {
            $type_a = wp_get_post_terms($article_a['id'], 'archi_project_type', ['fields' => 'ids']);
            $type_b = wp_get_post_terms($article_b['id'], 'archi_project_type', ['fields' => 'ids']);
            $common_types = array_intersect($type_a, $type_b);
            
            if (!empty($common_types)) {
                $score += self::WEIGHTS['taxonomy_similarity'];
            }
            
            $status_a = wp_get_post_terms($article_a['id'], 'archi_project_status', ['fields' => 'ids']);
            $status_b = wp_get_post_terms($article_b['id'], 'archi_project_status', ['fields' => 'ids']);
            $common_status = array_intersect($status_a, $status_b);
            
            if (!empty($common_status)) {
                $score += round(self::WEIGHTS['taxonomy_similarity'] * 0.5);
            }
        }
        
        // Taxonomies d'illustration
        if ($article_a['post_type'] === 'archi_illustration' && $article_b['post_type'] === 'archi_illustration') {
            $type_a = wp_get_post_terms($article_a['id'], 'illustration_type', ['fields' => 'ids']);
            $type_b = wp_get_post_terms($article_b['id'], 'illustration_type', ['fields' => 'ids']);
            $common_types = array_intersect($type_a, $type_b);
            
            if (!empty($common_types)) {
                $score += self::WEIGHTS['taxonomy_similarity'];
            }
        }
        
        return $score;
    }
    
    /**
     * NOUVEAU : Calculer la proximité entre deux projets architecturaux
     * 
     * @param int $project_a_id ID du premier projet
     * @param int $project_b_id ID du deuxième projet
     * @return array Score et détails
     */
    private static function calculate_project_proximity($project_a_id, $project_b_id) {
        $score = 0;
        $matches = [];
        $factors = [];
        
        // Localisation similaire
        $location_a = get_post_meta($project_a_id, '_archi_project_location', true);
        $location_b = get_post_meta($project_b_id, '_archi_project_location', true);
        
        if (!empty($location_a) && !empty($location_b)) {
            $location_sim = self::calculate_text_similarity($location_a, $location_b);
            if ($location_sim > 0.5) { // 50% de similarité
                $loc_score = round(self::WEIGHTS['location_match'] * $location_sim);
                $score += $loc_score;
                $matches[] = 'Localisation similaire : ' . $location_a;
                $factors['location_match'] = $loc_score;
            }
        }
        
        // Client identique ou similaire
        $client_a = get_post_meta($project_a_id, '_archi_project_client', true);
        $client_b = get_post_meta($project_b_id, '_archi_project_client', true);
        
        if (!empty($client_a) && !empty($client_b)) {
            if (strtolower($client_a) === strtolower($client_b)) {
                $score += self::WEIGHTS['client_match'];
                $matches[] = 'Même client : ' . $client_a;
                $factors['client_match'] = self::WEIGHTS['client_match'];
            }
        }
        
        // Budget/coût similaire
        $cost_a = get_post_meta($project_a_id, '_archi_project_cost', true);
        $cost_b = get_post_meta($project_b_id, '_archi_project_cost', true);
        
        if (!empty($cost_a) && !empty($cost_b) && $cost_a > 0 && $cost_b > 0) {
            $cost_ratio = min($cost_a, $cost_b) / max($cost_a, $cost_b);
            if ($cost_ratio > 0.5) { // Budget dans le même ordre de grandeur
                $budget_score = round(self::WEIGHTS['budget_similarity'] * $cost_ratio);
                $score += $budget_score;
                $matches[] = 'Budget similaire';
                $factors['budget_similarity'] = $budget_score;
            }
        }
        
        // Surface similaire
        $surface_a = get_post_meta($project_a_id, '_archi_project_surface', true);
        $surface_b = get_post_meta($project_b_id, '_archi_project_surface', true);
        
        if (!empty($surface_a) && !empty($surface_b) && $surface_a > 0 && $surface_b > 0) {
            $surface_ratio = min($surface_a, $surface_b) / max($surface_a, $surface_b);
            if ($surface_ratio > 0.6) { // Surface similaire
                $surface_score = 10; // Bonus fixe
                $score += $surface_score;
                $matches[] = 'Surface similaire';
                $factors['surface_similarity'] = $surface_score;
            }
        }
        
        return [
            'score' => $score,
            'matches' => $matches,
            'factors' => $factors
        ];
    }
    
    /**
     * NOUVEAU : Calculer la proximité entre deux illustrations
     * 
     * @param array $illust_a Première illustration
     * @param array $illust_b Deuxième illustration
     * @return array Score et détails
     */
    private static function calculate_illustration_proximity($illust_a, $illust_b) {
        $score = 0;
        $matches = [];
        $factors = [];
        
        // Technique identique ou similaire
        $tech_a = isset($illust_a['illustration_meta']['technique']) ? $illust_a['illustration_meta']['technique'] : '';
        $tech_b = isset($illust_b['illustration_meta']['technique']) ? $illust_b['illustration_meta']['technique'] : '';
        
        if (!empty($tech_a) && !empty($tech_b)) {
            $tech_sim = self::calculate_text_similarity($tech_a, $tech_b);
            if ($tech_sim > 0.5) {
                $tech_score = round(self::WEIGHTS['technique_match'] * $tech_sim);
                $score += $tech_score;
                $matches[] = 'Technique similaire : ' . $tech_a;
                $factors['technique_match'] = $tech_score;
            }
        }
        
        // Logiciels communs
        $soft_a = isset($illust_a['illustration_meta']['software']) ? $illust_a['illustration_meta']['software'] : '';
        $soft_b = isset($illust_b['illustration_meta']['software']) ? $illust_b['illustration_meta']['software'] : '';
        
        if (!empty($soft_a) && !empty($soft_b)) {
            $common_software = self::find_common_words($soft_a, $soft_b);
            if (!empty($common_software)) {
                $soft_score = self::WEIGHTS['software_match'];
                $score += $soft_score;
                $matches[] = 'Logiciels communs : ' . implode(', ', $common_software);
                $factors['software_match'] = $soft_score;
            }
        }
        
        return [
            'score' => $score,
            'matches' => $matches,
            'factors' => $factors
        ];
    }
    
    /**
     * NOUVEAU : Calculer la similarité sémantique entre deux textes
     * Utilise plusieurs méthodes : mots-clés, n-grams, Levenshtein
     * 
     * @param string $text_a Premier texte
     * @param string $text_b Deuxième texte
     * @return float Score de similarité (0-1)
     */
    private static function calculate_text_similarity($text_a, $text_b) {
        // Vérifier le cache
        $cache_key = md5($text_a . $text_b);
        if (isset(self::$similarity_cache[$cache_key])) {
            return self::$similarity_cache[$cache_key];
        }
        
        // Normaliser les textes
        $text_a = mb_strtolower(trim($text_a));
        $text_b = mb_strtolower(trim($text_b));
        
        if (empty($text_a) || empty($text_b)) {
            return 0;
        }
        
        // Méthode 1 : Mots-clés communs (Jaccard)
        $words_a = self::tokenize($text_a);
        $words_b = self::tokenize($text_b);
        
        $intersection = array_intersect($words_a, $words_b);
        $union = array_unique(array_merge($words_a, $words_b));
        
        $jaccard = count($union) > 0 ? count($intersection) / count($union) : 0;
        
        // Méthode 2 : Distance de Levenshtein normalisée (pour textes courts)
        $max_len = max(strlen($text_a), strlen($text_b));
        if ($max_len < 200) {
            $levenshtein = 1 - (levenshtein(substr($text_a, 0, 200), substr($text_b, 0, 200)) / $max_len);
        } else {
            $levenshtein = 0;
        }
        
        // Méthode 3 : Tri-grams (pour détecter des phrases similaires)
        $trigrams_a = self::generate_ngrams($text_a, 3);
        $trigrams_b = self::generate_ngrams($text_b, 3);
        
        $trigram_intersection = array_intersect($trigrams_a, $trigrams_b);
        $trigram_union = array_unique(array_merge($trigrams_a, $trigrams_b));
        
        $trigram_score = count($trigram_union) > 0 ? count($trigram_intersection) / count($trigram_union) : 0;
        
        // Combiner les scores (poids : Jaccard 50%, Trigrams 30%, Levenshtein 20%)
        $final_score = ($jaccard * 0.5) + ($trigram_score * 0.3) + ($levenshtein * 0.2);
        
        // Mettre en cache
        self::$similarity_cache[$cache_key] = $final_score;
        
        return $final_score;
    }
    
    /**
     * Tokeniser un texte en mots significatifs
     * 
     * @param string $text Texte à tokeniser
     * @return array Mots
     */
    private static function tokenize($text) {
        // Supprimer la ponctuation et convertir en minuscules
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        // Supprimer les mots vides (stop words français)
        $stopwords = ['le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'et', 'ou', 'mais', 
                      'dans', 'pour', 'avec', 'sur', 'par', 'à', 'au', 'aux', 'en', 'ce', 'ces',
                      'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for'];
        
        $words = array_filter($words, function($word) use ($stopwords) {
            return strlen($word) > 2 && !in_array($word, $stopwords);
        });
        
        return array_values($words);
    }
    
    /**
     * Générer des n-grams depuis un texte
     * 
     * @param string $text Texte
     * @param int $n Taille des n-grams
     * @return array N-grams
     */
    private static function generate_ngrams($text, $n = 3) {
        $text = preg_replace('/\s+/', '', $text); // Supprimer espaces
        $ngrams = [];
        $length = mb_strlen($text);
        
        for ($i = 0; $i <= $length - $n; $i++) {
            $ngrams[] = mb_substr($text, $i, $n);
        }
        
        return $ngrams;
    }
    
    /**
     * Trouver les mots communs entre deux chaînes
     * 
     * @param string $str_a Première chaîne
     * @param string $str_b Deuxième chaîne
     * @return array Mots communs
     */
    private static function find_common_words($str_a, $str_b) {
        $words_a = preg_split('/[\s,;]+/', mb_strtolower($str_a), -1, PREG_SPLIT_NO_EMPTY);
        $words_b = preg_split('/[\s,;]+/', mb_strtolower($str_b), -1, PREG_SPLIT_NO_EMPTY);
        
        $words_a = array_filter($words_a, function($w) { return strlen($w) > 2; });
        $words_b = array_filter($words_b, function($w) { return strlen($w) > 2; });
        
        return array_values(array_intersect($words_a, $words_b));
    }
    
    /**
     * Trouver les termes partagés entre deux listes
     * 
     * @param array $terms_a Première liste
     * @param array $terms_b Deuxième liste
     * @return array Termes partagés
     */
    private static function find_shared_terms($terms_a, $terms_b) {
        if (empty($terms_a) || empty($terms_b)) {
            return [];
        }
        
        $ids_b = array_column($terms_b, 'id');
        return array_filter($terms_a, function($term) use ($ids_b) {
            return in_array($term['id'], $ids_b);
        });
    }
    
    /**
     * Calculer le score de proximité temporelle
     * 
     * @param string $date_a Première date
     * @param string $date_b Deuxième date
     * @return int Score
     */
    private static function calculate_date_proximity($date_a, $date_b) {
        $timestamp_a = strtotime($date_a);
        $timestamp_b = strtotime($date_b);
        $days_diff = abs(($timestamp_a - $timestamp_b) / (60 * 60 * 24));
        
        if ($days_diff <= 7) {
            return self::WEIGHTS['date_proximity'];
        } elseif ($days_diff <= 30) {
            return round(self::WEIGHTS['date_proximity'] * 0.5);
        } elseif ($days_diff <= 90) {
            return round(self::WEIGHTS['date_proximity'] * 0.25);
        }
        
        return 0;
    }
    
    /**
     * Extraire l'ID d'un post depuis une URL
     * 
     * @param string $url URL
     * @return int|null ID du post
     */
    private static function extract_post_id_from_url($url) {
        // Essayer ?p=123
        if (preg_match('/[?&]p=(\d+)/', $url, $matches)) {
            return intval($matches[1]);
        }
        
        // Essayer /projet/123
        if (preg_match('/\/projet\/(\d+)/', $url, $matches)) {
            return intval($matches[1]);
        }
        
        // Essayer url_to_postid
        return url_to_postid($url);
    }
    
    /**
     * Déterminer la force d'un lien selon le score
     * 
     * @param int $score Score
     * @return string Force
     */
    private static function determine_strength($score) {
        if ($score >= 100) return 'very-strong';
        if ($score >= 70) return 'strong';
        if ($score >= 40) return 'medium';
        if ($score >= 20) return 'weak';
        return 'very-weak';
    }
}

// ============================================================================
// Public API Functions
// ============================================================================

/**
 * Fonction principale pour calculer la proximité entre deux articles
 * 
 * @param array $article_a Premier article
 * @param array $article_b Deuxième article
 * @return array Score et détails
 * @since 1.0.0
 */
function archi_calculate_proximity($article_a, $article_b) {
    return Archi_Proximity_Calculator::calculate_proximity($article_a, $article_b);
}
