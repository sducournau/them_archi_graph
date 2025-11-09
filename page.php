<?php
/**
 * Template par défaut pour les pages
 * 
 * @package Archi_Graph
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="page-container">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
                <header class="page-header">
                    <h1 class="page-title"><?php the_title(); ?></h1>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="page-featured-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>

                <div class="page-content-area">
                    <?php the_content(); ?>
                </div>

                <?php
                // Si les commentaires sont ouverts ou s'il y a au moins un commentaire
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
                ?>
            </article>
        <?php
        endwhile;
        ?>
    </div>
</main>

<?php
get_footer();
