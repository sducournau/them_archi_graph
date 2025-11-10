<?php
/**
 * Template pour les articles individuels - Version simplifiée et moderne
 */

get_header(); ?>

<div class="article-container">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('single-article'); ?>>
            
            <?php if (has_post_thumbnail()) : 
                // Récupérer les options d'image featured
                $fullscreen = get_post_meta(get_the_ID(), '_archi_featured_image_fullscreen', true);
                $parallax = get_post_meta(get_the_ID(), '_archi_featured_image_parallax', true) ?: 'none';
                $overlay_opacity = get_post_meta(get_the_ID(), '_archi_featured_image_overlay_opacity', true) ?: 0.3;
                
                if ($fullscreen === '1' || $fullscreen === 1 || $fullscreen === true) :
                    // Mode fullscreen hero
                    // Classes CSS conditionnelles
                    $hero_classes = ['archi-hero-fullscreen'];
                    if ($parallax === 'scroll') {
                        $hero_classes[] = 'parallax-scroll';
                    } elseif ($parallax === 'fixed') {
                        $hero_classes[] = 'parallax-fixed';
                    } elseif ($parallax === 'zoom') {
                        $hero_classes[] = 'parallax-zoom';
                    }
                    
                    $thumbnail_id = archi_get_fullscreen_image_id();
                    $thumbnail_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
                ?>
                <!-- Hero Fullscreen avec image en vedette -->
                <div class="<?php echo esc_attr(implode(' ', $hero_classes)); ?>" data-parallax="<?php echo esc_attr($parallax); ?>">
                    <?php ?>
                    <img src="<?php echo esc_url(archi_get_fullscreen_image_url(get_the_ID(), 'full')); ?>" 
                         alt="<?php echo esc_attr($thumbnail_alt ?: get_the_title()); ?>" 
                         class="hero-media">
                    
                    <div class="hero-overlay" style="opacity: <?php echo esc_attr($overlay_opacity); ?>;"></div>
                    
                    <div class="hero-content">
                        <?php 
                        $categories = get_the_category();
                        if ($categories) : ?>
                            <div class="hero-categories">
                                <?php foreach ($categories as $category) : ?>
                                    <span class="hero-category-badge">
                                        <?php echo esc_html($category->name); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <h1 class="hero-title"><?php the_title(); ?></h1>
                    </div>
                    
                    <!-- Indicateur de scroll -->
                    <div class="archi-scroll-indicator">
                        <div class="scroll-icon"></div>
                        <span class="scroll-text"><?php _e('Défiler', 'archi-graph'); ?></span>
                    </div>
                </div>
            <?php else : ?>
                <!-- Header standard avec image à la une (non fullscreen) -->
                <header class="article-header">
                    <?php 
                    $categories = get_the_category();
                    if ($categories) : ?>
                        <div class="article-categories">
                            <?php foreach ($categories as $category) : ?>
                                <span class="category-badge">
                                    <?php echo esc_html($category->name); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <h1 class="article-title"><?php the_title(); ?></h1>
                    
                    <div class="article-featured-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                </header>
            <?php endif; ?>
            <?php else : ?>
                <!-- Header simple si pas d'image -->
                <header class="article-header-simple">
                    <?php 
                    $categories = get_the_category();
                    if ($categories) : ?>
                        <div class="article-categories-simple">
                            <?php foreach ($categories as $category) : ?>
                                <span class="category-badge-simple">
                                    <?php echo esc_html($category->name); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <h1 class="article-title-simple"><?php the_title(); ?></h1>
                </header>
            <?php endif; ?>
            
            <!-- Contenu principal centré -->
            <div class="archi-content-section">
                <div class="article-content">
                    <?php the_content(); ?>
                </div>
                
                <?php 
                // Pagination pour les articles avec <!--nextpage-->
                wp_link_pages([
                    'before' => '<div class="page-links">' . __('Pages:', 'archi-graph'),
                    'after' => '</div>',
                ]);
                ?>
                
            <?php
            // Articles similaires - Style simplifié
            $related = get_posts([
                'category__in' => wp_get_post_categories($post->ID),
                'numberposts' => 3,
                'post__not_in' => [$post->ID],
                'post_type' => get_post_type()
            ]);
            
            if ($related) : ?>
                <aside class="related-articles-simple">
                    <h2 class="related-title-simple"><?php _e('Articles Similaires', 'archi-graph'); ?></h2>
                    <div class="related-grid-simple">
                        <?php foreach ($related as $related_post) : ?>
                            <article class="related-card-simple">
                                <a href="<?php echo get_permalink($related_post->ID); ?>" class="related-link-simple">
                                    <?php if (has_post_thumbnail($related_post->ID)) : ?>
                                        <div class="related-image-simple">
                                            <?php echo get_the_post_thumbnail($related_post->ID, 'large'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="related-content-simple">
                                        <h3 class="related-card-title-simple">
                                            <?php echo esc_html($related_post->post_title); ?>
                                        </h3>
                                    </div>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </aside>
            <?php endif; ?>
            </div><!-- .archi-content-section -->
            
        </article>
    <?php endwhile; ?>
</div>

<?php get_footer();