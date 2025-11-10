<?php
/**
 * Meta boxes pour les paramètres du graphique
 * 
 * NOTE: Meta registration is handled by inc/graph-meta-registry.php
 * This file only contains the UI for meta boxes
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ajouter les meta boxes pour les paramètres du graphique
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        // Masquer les liens de cet article dans le graphique
        register_post_meta($post_type, '_archi_hide_links', [
            'type' => 'string',
            'single' => true,
            'default' => '0',
            'show_in_rest' => true,
            'sanitize_callback' => function($value) {
                return $value === '1' ? '1' : '0';
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
    }
}
// DISABLED - Meta registration now in inc/graph-meta-registry.php
// add_action('init', 'archi_register_graph_meta');

/**
 * Ajouter les meta boxes
 */
function archi_add_meta_boxes() {
    $post_types = ['post', 'archi_project', 'archi_illustration'];
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'archi-graph-settings',
            __('Paramètres du graphique', 'archi-graph'),
            'archi_graph_meta_box_callback',
            $post_type,
            'side',
            'high'
        );
        
        add_meta_box(
            'archi-graph-relationships',
            __('Relations dans le graphique', 'archi-graph'),
            'archi_relationships_meta_box_callback',
            $post_type,
            'normal',
            'default'
        );
    }
    
    // Meta box spécifique pour le livre d'or
    add_meta_box(
        'archi-guestbook-details',
        __('Détails de l\'entrée', 'archi-graph'),
        'archi_guestbook_meta_box_callback',
        'archi_guestbook',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'archi_add_meta_boxes');

/**
 * Callback pour la meta box du graphique
 */
function archi_graph_meta_box_callback($post) {
    // Nonce pour la sécurité
    wp_nonce_field('archi_graph_meta_box', 'archi_graph_meta_box_nonce');
    
    // Récupérer les valeurs existantes
    $show_in_graph = get_post_meta($post->ID, '_archi_show_in_graph', true);
    $hide_links = get_post_meta($post->ID, '_archi_hide_links', true);
    $node_color = get_post_meta($post->ID, '_archi_node_color', true) ?: '#3498db';
    $node_size = get_post_meta($post->ID, '_archi_node_size', true) ?: 60;
    $graph_position = get_post_meta($post->ID, '_archi_graph_position', true);
    $priority_level = get_post_meta($post->ID, '_archi_priority_level', true) ?: 'normal';
    
    ?>
    <table class="form-table">
        <tr>
            <td>
                <label for="archi_show_in_graph">
                    <input type="checkbox" 
                           id="archi_show_in_graph" 
                           name="archi_show_in_graph" 
                           value="1" 
                           <?php checked($show_in_graph, '1'); ?>>
                    <?php _e('Afficher dans le graphique', 'archi-graph'); ?>
                </label>
                <p class="description">
                    <?php _e('Cochez pour inclure cet article dans le graphique de la page d\'accueil', 'archi-graph'); ?>
                </p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label for="archi_hide_links">
                    <input type="checkbox" 
                           id="archi_hide_links" 
                           name="archi_hide_links" 
                           value="1" 
                           <?php checked($hide_links, '1'); ?>>
                    <?php _e('Masquer les liens de cet article', 'archi-graph'); ?>
                </label>
                <p class="description">
                    <?php _e('Les liens de proximité vers/depuis cet article seront masqués dans le graphique', 'archi-graph'); ?>
                </p>
            </td>
        </tr>
        
        <!-- ✅ NEW: Comments Node Feature -->
        <tr>
            <td>
                <label for="archi_show_comments_node">
                    <input type="checkbox" 
                           id="archi_show_comments_node" 
                           name="archi_show_comments_node" 
                           value="1" 
                           <?php checked(get_post_meta($post->ID, '_archi_show_comments_node', true), '1'); ?>>
                    <?php _e('💬 Afficher les commentaires comme nœud', 'archi-graph'); ?>
                </label>
                <p class="description">
                    <?php 
                    $comments_count = get_comments_number($post->ID);
                    printf(
                        _n(
                            'Créer un nœud séparé pour afficher le commentaire de cet article',
                            'Créer un nœud séparé pour afficher les %s commentaires de cet article',
                            $comments_count,
                            'archi-graph'
                        ),
                        $comments_count
                    );
                    ?>
                </p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label for="archi_comment_node_color">
                    <?php _e('Couleur du nœud commentaires', 'archi-graph'); ?>
                </label><br>
                <input type="color" 
                       id="archi_comment_node_color" 
                       name="archi_comment_node_color" 
                       value="<?php echo esc_attr(get_post_meta($post->ID, '_archi_comment_node_color', true) ?: '#16a085'); ?>"
                       class="archi-color-picker">
                <p class="description">
                    <?php _e('Couleur pour le nœud des commentaires (par défaut: vert turquoise)', 'archi-graph'); ?>
                </p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label for="archi_node_color">
                    <?php _e('Couleur du nœud', 'archi-graph'); ?>
                </label><br>
                <input type="color" 
                       id="archi_node_color" 
                       name="archi_node_color" 
                       value="<?php echo esc_attr($node_color); ?>"
                       class="archi-color-picker">
                <p class="description">
                    <?php _e('Couleur personnalisée pour ce nœud (optionnel)', 'archi-graph'); ?>
                </p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label for="archi_node_size">
                    <?php _e('Taille du nœud', 'archi-graph'); ?>
                </label><br>
                <?php
                // Taille maximale augmentée à 500px pour tous les types
                $min_size = 60;
                $max_size = 500;
                $step = 20;
                ?>
                <input type="range" 
                       id="archi_node_size" 
                       name="archi_node_size" 
                       class="archi-node-size-slider"
                       min="<?php echo $min_size; ?>" 
                       max="<?php echo $max_size; ?>" 
                       step="<?php echo $step; ?>"
                       value="<?php echo esc_attr($node_size); ?>"
                       oninput="this.nextElementSibling.textContent = this.value + 'px'"
                       onchange="this.nextElementSibling.textContent = this.value + 'px'">
                <span id="node-size-value" class="archi-node-size-display"><?php echo esc_html($node_size); ?>px</span>
                <p class="description">
                    <?php _e('Taille du nœud dans le graphique (60-500px pour tous les types)', 'archi-graph'); ?>
                </p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label for="archi_priority_level">
                    <?php _e('Niveau de priorité', 'archi-graph'); ?>
                </label><br>
                <select id="archi_priority_level" name="archi_priority_level">
                    <option value="low" <?php selected($priority_level, 'low'); ?>>
                        <?php _e('Faible', 'archi-graph'); ?>
                    </option>
                    <option value="normal" <?php selected($priority_level, 'normal'); ?>>
                        <?php _e('Normal', 'archi-graph'); ?>
                    </option>
                    <option value="high" <?php selected($priority_level, 'high'); ?>>
                        <?php _e('Élevé', 'archi-graph'); ?>
                    </option>
                    <option value="featured" <?php selected($priority_level, 'featured'); ?>>
                        <?php _e('Vedette', 'archi-graph'); ?>
                    </option>
                </select>
                <p class="description">
                    <?php _e('Articles prioritaires apparaissent plus prominents', 'archi-graph'); ?>
                </p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label for="archi_animation_level">
                    <?php _e('Niveau d\'animation', 'archi-graph'); ?>
                </label><br>
                <select id="archi_animation_level" name="archi_animation_level">
                    <?php
                    $animation_level = get_post_meta($post->ID, '_archi_animation_level', true);
                    if (empty($animation_level)) {
                        $animation_level = 'normal';
                    }
                    ?>
                    <option value="none" <?php selected($animation_level, 'none'); ?>>
                        <?php _e('Aucune animation', 'archi-graph'); ?>
                    </option>
                    <option value="subtle" <?php selected($animation_level, 'subtle'); ?>>
                        <?php _e('Subtil', 'archi-graph'); ?>
                    </option>
                    <option value="normal" <?php selected($animation_level, 'normal'); ?>>
                        <?php _e('Normal', 'archi-graph'); ?>
                    </option>
                    <option value="intense" <?php selected($animation_level, 'intense'); ?>>
                        <?php _e('Intense', 'archi-graph'); ?>
                    </option>
                </select>
                <p class="description">
                    <?php _e('Contrôle l\'intensité des animations (hover, pulse, etc.)', 'archi-graph'); ?>
                </p>
            </td>
        </tr>
        
        <!-- Advanced Animation Controls -->
        <tr>
            <td colspan="2">
                <h4 style="margin: 15px 0 10px; padding-top: 15px; border-top: 1px solid #ddd;">
                    <?php _e('⚡ Contrôles d\'Animation Avancés', 'archi-graph'); ?>
                </h4>
            </td>
        </tr>
        
        <tr>
            <td>
                <label for="archi_animation_duration">
                    <?php _e('Durée d\'animation (ms):', 'archi-graph'); ?>
                </label><br>
                <?php
                $animation_duration = get_post_meta($post->ID, '_archi_animation_duration', true);
                $animation_duration = $animation_duration ?: 800;
                ?>
                <input type="number" 
                       id="archi_animation_duration" 
                       name="archi_animation_duration" 
                       value="<?php echo esc_attr($animation_duration); ?>"
                       min="0"
                       max="5000"
                       step="100"
                       style="width: 100px;">
                <span id="archi_animation_duration_display"><?php echo esc_html($animation_duration); ?>ms</span>
                <p class="description">
                    <?php _e('Durée de l\'animation d\'entrée (0-5000ms)', 'archi-graph'); ?>
                </p>
            </td>
            <td>
                <label for="archi_animation_delay">
                    <?php _e('Délai d\'animation (ms):', 'archi-graph'); ?>
                </label><br>
                <?php
                $animation_delay = get_post_meta($post->ID, '_archi_animation_delay', true);
                $animation_delay = $animation_delay ?: 0;
                ?>
                <input type="number" 
                       id="archi_animation_delay" 
                       name="archi_animation_delay" 
                       value="<?php echo esc_attr($animation_delay); ?>"
                       min="0"
                       max="5000"
                       step="100"
                       style="width: 100px;">
                <span id="archi_animation_delay_display"><?php echo esc_html($animation_delay); ?>ms</span>
                <p class="description">
                    <?php _e('Délai avant le début de l\'animation (0-5000ms)', 'archi-graph'); ?>
                </p>
            </td>
        </tr>
        
        <tr>
            <td>
                <label for="archi_animation_easing">
                    <?php _e('Fonction d\'easing:', 'archi-graph'); ?>
                </label><br>
                <?php
                $animation_easing = get_post_meta($post->ID, '_archi_animation_easing', true);
                $animation_easing = $animation_easing ?: 'ease-out';
                ?>
                <select id="archi_animation_easing" name="archi_animation_easing">
                    <option value="linear" <?php selected($animation_easing, 'linear'); ?>>Linear</option>
                    <option value="ease" <?php selected($animation_easing, 'ease'); ?>>Ease</option>
                    <option value="ease-in" <?php selected($animation_easing, 'ease-in'); ?>>Ease In</option>
                    <option value="ease-out" <?php selected($animation_easing, 'ease-out'); ?>>Ease Out</option>
                    <option value="ease-in-out" <?php selected($animation_easing, 'ease-in-out'); ?>>Ease In-Out</option>
                    <option value="elastic" <?php selected($animation_easing, 'elastic'); ?>>Elastic</option>
                    <option value="bounce" <?php selected($animation_easing, 'bounce'); ?>>Bounce</option>
                </select>
                <p class="description">
                    <?php _e('Courbe d\'animation', 'archi-graph'); ?>
                </p>
            </td>
            <td>
                <label for="archi_enter_from">
                    <?php _e('Direction d\'entrée:', 'archi-graph'); ?>
                </label><br>
                <?php
                $enter_from = get_post_meta($post->ID, '_archi_enter_from', true);
                $enter_from = $enter_from ?: 'center';
                ?>
                <select id="archi_enter_from" name="archi_enter_from">
                    <option value="center" <?php selected($enter_from, 'center'); ?>>
                        <?php _e('Centre', 'archi-graph'); ?>
                    </option>
                    <option value="top" <?php selected($enter_from, 'top'); ?>>
                        <?php _e('Haut', 'archi-graph'); ?>
                    </option>
                    <option value="bottom" <?php selected($enter_from, 'bottom'); ?>>
                        <?php _e('Bas', 'archi-graph'); ?>
                    </option>
                    <option value="left" <?php selected($enter_from, 'left'); ?>>
                        <?php _e('Gauche', 'archi-graph'); ?>
                    </option>
                    <option value="right" <?php selected($enter_from, 'right'); ?>>
                        <?php _e('Droite', 'archi-graph'); ?>
                    </option>
                </select>
                <p class="description">
                    <?php _e('Direction depuis laquelle le nœud entre', 'archi-graph'); ?>
                </p>
            </td>
        </tr>
        
        <tr>
            <td colspan="2">
                <h4 style="margin: 15px 0 10px;">
                    <?php _e('🎨 Effets de Survol', 'archi-graph'); ?>
                </h4>
            </td>
        </tr>
        
        <tr>
            <td>
                <label for="archi_hover_scale">
                    <?php _e('Échelle au survol:', 'archi-graph'); ?>
                </label><br>
                <?php
                $hover_scale = get_post_meta($post->ID, '_archi_hover_scale', true);
                $hover_scale = $hover_scale ?: 1.15;
                ?>
                <input type="number" 
                       id="archi_hover_scale" 
                       name="archi_hover_scale" 
                       value="<?php echo esc_attr($hover_scale); ?>"
                       min="1.0"
                       max="2.0"
                       step="0.05"
                       style="width: 100px;">
                <span id="archi_hover_scale_display">×<?php echo esc_html($hover_scale); ?></span>
                <p class="description">
                    <?php _e('Facteur d\'agrandissement au survol (1.0-2.0)', 'archi-graph'); ?>
                </p>
            </td>
            <td>
                <label>
                    <input type="checkbox" 
                           id="archi_pulse_effect" 
                           name="archi_pulse_effect" 
                           value="1"
                           <?php checked(get_post_meta($post->ID, '_archi_pulse_effect', true), '1'); ?>>
                    <?php _e('Effet de pulsation continue', 'archi-graph'); ?>
                </label>
                <br><br>
                <label>
                    <input type="checkbox" 
                           id="archi_glow_effect" 
                           name="archi_glow_effect" 
                           value="1"
                           <?php checked(get_post_meta($post->ID, '_archi_glow_effect', true), '1'); ?>>
                    <?php _e('Halo lumineux au survol', 'archi-graph'); ?>
                </label>
            </td>
        </tr>
        
        <?php if (!empty($graph_position)) : ?>
        <tr>
            <td>
                <label><?php _e('Position dans le graphique', 'archi-graph'); ?></label><br>
                <code>
                    X: <?php echo esc_html(round($graph_position['x'], 2)); ?>, 
                    Y: <?php echo esc_html(round($graph_position['y'], 2)); ?>
                </code>
                <p class="description">
                    <?php _e('Position sauvegardée automatiquement', 'archi-graph'); ?>
                </p>
                <button type="button" 
                        class="button button-small" 
                        onclick="archiResetPosition(<?php echo $post->ID; ?>)">
                    <?php _e('Réinitialiser position', 'archi-graph'); ?>
                </button>
            </td>
        </tr>
        <?php endif; ?>
    </table>
    
    <style>
    .archi-color-picker {
        width: 50px;
        height: 30px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }
    #archi_node_size {
        width: 100%;
        margin: 5px 0;
    }
    .archi-node-size-display {
        display: inline-block;
        margin-left: 10px;
        font-weight: bold;
        color: #0073aa;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Update live display for animation duration
        $('#archi_animation_duration').on('input', function() {
            $('#archi_animation_duration_display').text($(this).val() + 'ms');
        });
        
        // Update live display for animation delay
        $('#archi_animation_delay').on('input', function() {
            $('#archi_animation_delay_display').text($(this).val() + 'ms');
        });
        
        // Update live display for hover scale
        $('#archi_hover_scale').on('input', function() {
            $('#archi_hover_scale_display').text('×' + $(this).val());
        });
    });
    </script>
    
    <script>
    // Fonction pour réinitialiser la position
    function archiResetPosition(postId) {
        if (confirm('<?php echo esc_js(__('Êtes-vous sûr de vouloir réinitialiser la position ?', 'archi-graph')); ?>')) {
            fetch('<?php echo esc_url(home_url('/wp-json/archi/v1/reset-position/')); ?>' + postId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                }
            }).then(() => {
                location.reload();
            });
        }
    }
    
    // Initialiser le slider de taille au chargement du DOM
    jQuery(document).ready(function($) {
        var slider = $('#archi_node_size');
        var display = $('#node-size-value');
        
        if (slider.length && display.length) {
            // Mise à jour lors du mouvement du slider
            slider.on('input change', function() {
                display.text(this.value + 'px');
            });
            
            // Initialiser la valeur affichée
            display.text(slider.val() + 'px');
        }
    });
    </script>
    <?php
}

/**
 * Callback pour la meta box des relations
 */
function archi_relationships_meta_box_callback($post) {
    // Nonce pour la sécurité
    wp_nonce_field('archi_relationships_meta_box', 'archi_relationships_meta_box_nonce');
    
    // Récupérer les relations existantes
    $related_articles = get_post_meta($post->ID, '_archi_related_articles', true);
    if (!is_array($related_articles)) {
        $related_articles = [];
    }
    
    // Récupérer tous les articles pour la sélection
    $all_posts = get_posts([
        'post_type' => ['post', 'archi_project', 'archi_illustration'],
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'exclude' => [$post->ID],
        'orderby' => 'title',
        'order' => 'ASC'
    ]);
    
    ?>
    <div class="archi-relationships-box">
        <p class="description">
            <?php _e('Définissez des relations manuelles entre cet article et d\'autres articles du graphique. Ces relations créeront des liens visibles plus forts dans le graphique.', 'archi-graph'); ?>
        </p>
        
        <table class="widefat striped">
            <thead>
                <tr>
                    <th style="width: 30px;"><?php _e('Lié', 'archi-graph'); ?></th>
                    <th><?php _e('Article', 'archi-graph'); ?></th>
                    <th><?php _e('Type', 'archi-graph'); ?></th>
                    <th><?php _e('Catégories', 'archi-graph'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_posts as $article): 
                    $categories = get_the_category($article->ID);
                    $cat_names = array_map(function($cat) { return $cat->name; }, $categories);
                    $post_type_obj = get_post_type_object($article->post_type);
                    $is_related = in_array($article->ID, $related_articles);
                ?>
                <tr class="<?php echo $is_related ? 'archi-related-row' : ''; ?>">
                    <td style="text-align: center;">
                        <input type="checkbox" 
                               name="archi_related_articles[]" 
                               value="<?php echo $article->ID; ?>"
                               <?php checked($is_related); ?>>
                    </td>
                    <td>
                        <strong><?php echo esc_html($article->post_title); ?></strong>
                        <div class="row-actions">
                            <span><a href="<?php echo get_permalink($article->ID); ?>" target="_blank"><?php _e('Voir', 'archi-graph'); ?></a></span>
                        </div>
                    </td>
                    <td>
                        <span class="archi-post-type-badge archi-type-<?php echo $article->post_type; ?>">
                            <?php echo esc_html($post_type_obj->labels->singular_name); ?>
                        </span>
                    </td>
                    <td><?php echo esc_html(implode(', ', $cat_names)); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p class="description" style="margin-top: 15px;">
            <strong><?php _e('Relations actuelles:', 'archi-graph'); ?></strong> 
            <span id="archi-relations-count"><?php echo count($related_articles); ?></span>
            <?php _e('article(s) lié(s)', 'archi-graph'); ?>
        </p>
        
        <div class="archi-search-box" style="margin-top: 15px;">
            <input type="text" 
                   id="archi-search-articles" 
                   placeholder="<?php _e('Rechercher un article...', 'archi-graph'); ?>"
                   style="width: 100%; padding: 8px;">
        </div>
    </div>
    
    <style>
    .archi-relationships-box {
        padding: 10px 0;
    }
    
    .archi-relationships-box table {
        margin-top: 15px;
    }
    
    .archi-related-row {
        background-color: #e8f5e9 !important;
    }
    
    .archi-post-type-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .archi-type-post {
        background-color: #3498db;
        color: white;
    }
    
    .archi-type-archi_project {
        background-color: #e67e22;
        color: white;
    }
    
    .archi-type-archi_illustration {
        background-color: #9b59b6;
        color: white;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Recherche dans les articles
        $('#archi-search-articles').on('keyup', function() {
            var searchTerm = $(this).val().toLowerCase();
            $('.archi-relationships-box table tbody tr').each(function() {
                var articleTitle = $(this).find('td:nth-child(2) strong').text().toLowerCase();
                var categories = $(this).find('td:nth-child(4)').text().toLowerCase();
                
                if (articleTitle.indexOf(searchTerm) > -1 || categories.indexOf(searchTerm) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
        // Compter les relations
        $('input[name="archi_related_articles[]"]').on('change', function() {
            var count = $('input[name="archi_related_articles[]"]:checked').length;
            $('#archi-relations-count').text(count);
            
            // Mettre à jour la couleur de la ligne
            if ($(this).is(':checked')) {
                $(this).closest('tr').addClass('archi-related-row');
            } else {
                $(this).closest('tr').removeClass('archi-related-row');
            }
        });
    });
    </script>
    <?php
}

/**
 * Sauvegarder les données des meta boxes
 */
function archi_save_meta_box_data($post_id) {
    // Vérifications de sécurité pour la meta box des paramètres
    if (isset($_POST['archi_graph_meta_box_nonce']) && 
        wp_verify_nonce($_POST['archi_graph_meta_box_nonce'], 'archi_graph_meta_box')) {
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Sauvegarder les champs
        $show_in_graph = isset($_POST['archi_show_in_graph']) ? '1' : '0';
        update_post_meta($post_id, '_archi_show_in_graph', $show_in_graph);
        
        $hide_links = isset($_POST['archi_hide_links']) ? '1' : '0';
        update_post_meta($post_id, '_archi_hide_links', $hide_links);
        
        if (isset($_POST['archi_node_color'])) {
            $color = sanitize_hex_color($_POST['archi_node_color']);
            update_post_meta($post_id, '_archi_node_color', $color);
        }
        
        if (isset($_POST['archi_node_size'])) {
            $size = absint($_POST['archi_node_size']);
            
            // Validation selon le type de post
            $post_type = get_post_type($post_id);
            $min_size = 40;
            $max_size = 500; // Augmenté de 120 à 500 pour tous les types
            
            if ($post_type === 'archi_project') {
                // Projets architecturaux : plage étendue
                $min_size = 60;
                $max_size = 500; // Augmenté de 200 à 500
            }
            
            // Debug logging (à retirer en production)
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log(sprintf(
                    'Archi Node Size Save - Post ID: %d, Type: %s, Size: %d, Min: %d, Max: %d, Valid: %s',
                    $post_id,
                    $post_type,
                    $size,
                    $min_size,
                    $max_size,
                    ($size >= $min_size && $size <= $max_size) ? 'YES' : 'NO'
                ));
            }
            
            if ($size >= $min_size && $size <= $max_size) {
                update_post_meta($post_id, '_archi_node_size', $size);
            }
        }
        
        if (isset($_POST['archi_priority_level'])) {
            $priority = sanitize_text_field($_POST['archi_priority_level']);
            $allowed_priorities = ['low', 'normal', 'high', 'featured'];
            if (in_array($priority, $allowed_priorities)) {
                update_post_meta($post_id, '_archi_priority_level', $priority);
            }
        }
        
        if (isset($_POST['archi_animation_level'])) {
            $animation_level = sanitize_text_field($_POST['archi_animation_level']);
            $allowed_levels = ['none', 'subtle', 'normal', 'intense'];
            if (in_array($animation_level, $allowed_levels)) {
                update_post_meta($post_id, '_archi_animation_level', $animation_level);
            }
        }
        
        // Advanced animation controls
        if (isset($_POST['archi_animation_duration'])) {
            $duration = absint($_POST['archi_animation_duration']);
            if ($duration >= 0 && $duration <= 5000) {
                update_post_meta($post_id, '_archi_animation_duration', $duration);
            }
        }
        
        if (isset($_POST['archi_animation_delay'])) {
            $delay = absint($_POST['archi_animation_delay']);
            if ($delay >= 0 && $delay <= 5000) {
                update_post_meta($post_id, '_archi_animation_delay', $delay);
            }
        }
        
        if (isset($_POST['archi_animation_easing'])) {
            $easing = sanitize_text_field($_POST['archi_animation_easing']);
            $allowed_easings = ['linear', 'ease', 'ease-in', 'ease-out', 'ease-in-out', 'elastic', 'bounce'];
            if (in_array($easing, $allowed_easings)) {
                update_post_meta($post_id, '_archi_animation_easing', $easing);
            }
        }
        
        if (isset($_POST['archi_enter_from'])) {
            $enter_from = sanitize_text_field($_POST['archi_enter_from']);
            $allowed_directions = ['center', 'top', 'bottom', 'left', 'right'];
            if (in_array($enter_from, $allowed_directions)) {
                update_post_meta($post_id, '_archi_enter_from', $enter_from);
            }
        }
        
        if (isset($_POST['archi_hover_scale'])) {
            $scale = floatval($_POST['archi_hover_scale']);
            if ($scale >= 1.0 && $scale <= 2.0) {
                update_post_meta($post_id, '_archi_hover_scale', $scale);
            }
        }
        
        // Checkboxes (pulse and glow effects)
        update_post_meta($post_id, '_archi_pulse_effect', isset($_POST['archi_pulse_effect']) ? '1' : '0');
        update_post_meta($post_id, '_archi_glow_effect', isset($_POST['archi_glow_effect']) ? '1' : '0');
        
        // ✅ NEW: Save comments node settings
        update_post_meta($post_id, '_archi_show_comments_node', isset($_POST['archi_show_comments_node']) ? '1' : '0');
        
        if (isset($_POST['archi_comment_node_color'])) {
            $comment_color = sanitize_hex_color($_POST['archi_comment_node_color']);
            if ($comment_color) {
                update_post_meta($post_id, '_archi_comment_node_color', $comment_color);
            }
        }
    }
    
    // Vérifications de sécurité pour la meta box des relations
    if (isset($_POST['archi_relationships_meta_box_nonce']) && 
        wp_verify_nonce($_POST['archi_relationships_meta_box_nonce'], 'archi_relationships_meta_box')) {
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Sauvegarder les relations
        $related_articles = isset($_POST['archi_related_articles']) && is_array($_POST['archi_related_articles']) 
            ? array_map('absint', $_POST['archi_related_articles']) 
            : [];
        
        update_post_meta($post_id, '_archi_related_articles', $related_articles);
    }
    
    // Invalider le cache
    delete_transient('archi_graph_articles');
}
add_action('save_post', 'archi_save_meta_box_data');

/**
 * Ajouter des colonnes à la liste des articles dans l'admin
 */
function archi_add_admin_columns($columns) {
    $columns['archi_graph'] = __('Dans le graphique', 'archi-graph');
    $columns['archi_priority'] = __('Priorité', 'archi-graph');
    return $columns;
}
add_filter('manage_posts_columns', 'archi_add_admin_columns');

/**
 * Afficher le contenu des colonnes personnalisées
 */
function archi_display_admin_columns($column, $post_id) {
    switch ($column) {
        case 'archi_graph':
            $show_in_graph = get_post_meta($post_id, '_archi_show_in_graph', true);
            if ($show_in_graph === '1') {
                echo '<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>';
            } else {
                echo '<span class="dashicons dashicons-minus" style="color: #ddd;"></span>';
            }
            break;
            
        case 'archi_priority':
            $priority = get_post_meta($post_id, '_archi_priority_level', true) ?: 'normal';
            $priority_labels = [
                'low' => __('Faible', 'archi-graph'),
                'normal' => __('Normal', 'archi-graph'),
                'high' => __('Élevé', 'archi-graph'),
                'featured' => __('Vedette', 'archi-graph')
            ];
            
            $colors = [
                'low' => '#999',
                'normal' => '#333',
                'high' => '#0073aa',
                'featured' => '#d63638'
            ];
            
            echo '<span style="color: ' . $colors[$priority] . ';">' . $priority_labels[$priority] . '</span>';
            break;
    }
}
add_action('manage_posts_custom_column', 'archi_display_admin_columns', 10, 2);

/**
 * Rendre les colonnes triables
 */
function archi_make_columns_sortable($columns) {
    $columns['archi_graph'] = 'archi_graph';
    $columns['archi_priority'] = 'archi_priority';
    return $columns;
}
add_filter('manage_edit-post_sortable_columns', 'archi_make_columns_sortable');

/**
 * Gérer le tri des colonnes personnalisées
 */
function archi_handle_column_sorting($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    $orderby = $query->get('orderby');
    
    if ($orderby === 'archi_graph') {
        $query->set('meta_key', '_archi_show_in_graph');
        $query->set('orderby', 'meta_value');
    }
    
    if ($orderby === 'archi_priority') {
        $query->set('meta_key', '_archi_priority_level');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'archi_handle_column_sorting');

/**
 * Meta box pour les catégories (couleurs personnalisées)
 */
function archi_add_category_meta_fields($term) {
    $color = get_term_meta($term->term_id, '_archi_category_color', true) ?: '#3498db';
    ?>
    <tr class="form-field">
        <th scope="row">
            <label for="archi_category_color"><?php _e('Couleur dans le graphique', 'archi-graph'); ?></label>
        </th>
        <td>
            <input type="color" 
                   id="archi_category_color" 
                   name="archi_category_color" 
                   value="<?php echo esc_attr($color); ?>">
            <p class="description">
                <?php _e('Couleur utilisée pour cette catégorie dans le graphique', 'archi-graph'); ?>
            </p>
        </td>
    </tr>
    <?php
}
add_action('category_edit_form_fields', 'archi_add_category_meta_fields');

/**
 * Sauvegarder les métadonnées des catégories
 */
function archi_save_category_meta($term_id) {
    if (isset($_POST['archi_category_color'])) {
        $color = sanitize_hex_color($_POST['archi_category_color']);
        update_term_meta($term_id, '_archi_category_color', $color);
        
        // Invalider le cache
        delete_transient('archi_graph_articles');
        delete_transient('archi_graph_categories');
    }
}
add_action('edit_category', 'archi_save_category_meta');

/**
 * Callback pour la meta box spécifique du livre d'or
 */
function archi_guestbook_meta_box_callback($post) {
    // Nonce pour la sécurité
    wp_nonce_field('archi_guestbook_meta_box', 'archi_guestbook_meta_box_nonce');
    
    // Récupérer les valeurs existantes
    $author_name = get_post_meta($post->ID, '_archi_guestbook_author_name', true);
    $author_email = get_post_meta($post->ID, '_archi_guestbook_author_email', true);
    $author_company = get_post_meta($post->ID, '_archi_guestbook_author_company', true);
    $linked_articles = get_post_meta($post->ID, '_archi_linked_articles', true) ?: [];
    $wpforms_entry_id = get_post_meta($post->ID, '_archi_wpforms_entry_id', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="archi_guestbook_author_name"><?php _e('Nom de l\'auteur:', 'archi-graph'); ?></label></th>
            <td>
                <input type="text" 
                       id="archi_guestbook_author_name" 
                       name="archi_guestbook_author_name" 
                       value="<?php echo esc_attr($author_name); ?>" 
                       class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="archi_guestbook_author_email"><?php _e('Email:', 'archi-graph'); ?></label></th>
            <td>
                <input type="email" 
                       id="archi_guestbook_author_email" 
                       name="archi_guestbook_author_email" 
                       value="<?php echo esc_attr($author_email); ?>" 
                       class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="archi_guestbook_author_company"><?php _e('Entreprise/Organisation:', 'archi-graph'); ?></label></th>
            <td>
                <input type="text" 
                       id="archi_guestbook_author_company" 
                       name="archi_guestbook_author_company" 
                       value="<?php echo esc_attr($author_company); ?>" 
                       class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label><?php _e('Articles liés:', 'archi-graph'); ?></label></th>
            <td>
                <div id="archi-linked-articles-container">
                    <?php
                    // Récupérer tous les posts disponibles
                    $available_posts = get_posts([
                        'post_type' => ['post', 'archi_project', 'archi_illustration'],
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'orderby' => 'title',
                        'order' => 'ASC'
                    ]);
                    
                    if (!empty($available_posts)) {
                        ?>
                        <select name="archi_linked_articles[]" multiple size="10" style="width: 100%; max-width: 500px;">
                            <?php foreach ($available_posts as $available_post): ?>
                                <option value="<?php echo $available_post->ID; ?>" 
                                        <?php selected(in_array($available_post->ID, (array)$linked_articles)); ?>>
                                    <?php 
                                    $type_label = '';
                                    switch ($available_post->post_type) {
                                        case 'archi_project':
                                            $type_label = '[Projet] ';
                                            break;
                                        case 'archi_illustration':
                                            $type_label = '[Illustration] ';
                                            break;
                                        default:
                                            $type_label = '[Article] ';
                                    }
                                    echo esc_html($type_label . $available_post->post_title); 
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">
                            <?php _e('Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs articles.', 'archi-graph'); ?>
                        </p>
                        <?php
                    } else {
                        echo '<p>' . __('Aucun article disponible.', 'archi-graph') . '</p>';
                    }
                    ?>
                </div>
            </td>
        </tr>
        <?php if ($wpforms_entry_id): ?>
        <tr>
            <th><?php _e('ID Entrée WPForms:', 'archi-graph'); ?></th>
            <td>
                <code><?php echo esc_html($wpforms_entry_id); ?></code>
            </td>
        </tr>
        <?php endif; ?>
    </table>
    <?php
}

/**
 * Sauvegarder les métadonnées du livre d'or
 */
function archi_save_guestbook_meta($post_id) {
    // Vérifier le nonce
    if (!isset($_POST['archi_guestbook_meta_box_nonce']) || 
        !wp_verify_nonce($_POST['archi_guestbook_meta_box_nonce'], 'archi_guestbook_meta_box')) {
        return;
    }
    
    // Vérifier l'autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Vérifier les permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Sauvegarder les métadonnées
    if (isset($_POST['archi_guestbook_author_name'])) {
        update_post_meta($post_id, '_archi_guestbook_author_name', sanitize_text_field($_POST['archi_guestbook_author_name']));
    }
    
    if (isset($_POST['archi_guestbook_author_email'])) {
        update_post_meta($post_id, '_archi_guestbook_author_email', sanitize_email($_POST['archi_guestbook_author_email']));
    }
    
    if (isset($_POST['archi_guestbook_author_company'])) {
        update_post_meta($post_id, '_archi_guestbook_author_company', sanitize_text_field($_POST['archi_guestbook_author_company']));
    }
    
    // Sauvegarder les articles liés
    if (isset($_POST['archi_linked_articles'])) {
        $linked_articles = array_map('intval', $_POST['archi_linked_articles']);
        update_post_meta($post_id, '_archi_linked_articles', $linked_articles);
    } else {
        update_post_meta($post_id, '_archi_linked_articles', []);
    }
    
    // Invalider le cache du graphique
    delete_transient('archi_graph_articles');
}
add_action('save_post_archi_guestbook', 'archi_save_guestbook_meta');

