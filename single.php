<?php
/**
 * Template unifié pour tous les articles individuels
 * Gère automatiquement : posts, archi_project, archi_illustration, archi_guestbook
 * 
 * @package Archi_Graph
 */

get_header(); 

$post_type = get_post_type();
$container_class = 'archi-single-container archi-single-' . esc_attr($post_type);
?>

<div class="<?php echo $container_class; ?>">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('archi-single-article'); ?>>
            
            <!-- Contenu principal centré -->
            <div class="archi-content-section">
                
                <?php 
                /**
                 * Hook avant le contenu de l'article
                 * @param int $post_id L'ID du post courant
                 * @param string $post_type Le type de post
                 */
                do_action('archi_before_single_content', get_the_ID(), $post_type); 
                ?>
                
                <!-- Contenu principal -->
                <div class="archi-article-content">
                    <?php the_content(); ?>
                </div>
                
                <?php 
                // Pagination pour les articles avec <!--nextpage-->
                wp_link_pages([
                    'before' => '<div class="archi-page-links">' . __('Pages :', 'archi-graph'),
                    'after' => '</div>',
                    'link_before' => '<span class="page-number">',
                    'link_after' => '</span>',
                ]);
                ?>
                
                <?php 
                /**
                 * Affichage des métadonnées spécifiques au type de post
                 * Utilise archi_display_post_metadata() de inc/single-post-helpers.php
                 */
                archi_display_post_metadata(get_the_ID()); 
                ?>
                
                <?php 
                /**
                 * Hook après le contenu et les métadonnées
                 * @param int $post_id L'ID du post courant
                 * @param string $post_type Le type de post
                 */
                do_action('archi_after_single_content', get_the_ID(), $post_type); 
                ?>
                
                <?php 
                /**
                 * Affichage des articles similaires
                 * Utilise archi_display_related_posts() de inc/single-post-helpers.php
                 * Gère automatiquement la logique selon le type de post
                 */
                archi_display_related_posts(get_the_ID(), 3); 
                ?>
                
            </div><!-- .archi-content-section -->
            
        </article>
    <?php endwhile; ?>
</div><!-- .archi-single-container -->

<?php get_footer();