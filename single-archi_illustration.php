<?php
/**
 * Template pour les illustrations - Version simplifiée
 */

get_header(); ?>

<div class="illustration-container">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('single-illustration'); ?>>
            
            <?php if (has_post_thumbnail()) : 
                // Récupérer les options d'image featured
                $fullscreen = get_post_meta(get_the_ID(), '_archi_featured_image_fullscreen', true);
                $parallax = get_post_meta(get_the_ID(), '_archi_featured_image_parallax', true) ?: 'none';
                $overlay_opacity = get_post_meta(get_the_ID(), '_archi_featured_image_overlay_opacity', true) ?: 0.3;
                
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
                <!-- Hero Fullscreen pour illustration -->
                <div class="<?php echo esc_attr(implode(' ', $hero_classes)); ?>" data-parallax="<?php echo esc_attr($parallax); ?>">
                    <?php ?>
                    <img src="<?php echo esc_url(archi_get_fullscreen_image_url(get_the_ID(), 'full')); ?>" 
                         alt="<?php echo esc_attr($thumbnail_alt ?: get_the_title()); ?>" 
                         class="hero-media">
                    
                    <div class="hero-overlay" style="opacity: <?php echo esc_attr($overlay_opacity); ?>;"></div>
                    
                    <div class="hero-content">
                        <?php
                        $categories = get_the_terms(get_the_ID(), 'illustration_type');
                        if ($categories && !is_wp_error($categories)) : ?>
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
                <!-- Header simple si pas d'image -->
                <header class="illustration-header-simple">
                    <h1 class="illustration-title-simple"><?php the_title(); ?></h1>
                    <?php
                    $categories = get_the_terms(get_the_ID(), 'illustration_type');
                    if ($categories) : ?>
                        <div class="illustration-categories-simple">
                            <?php foreach ($categories as $category) : ?>
                                <span class="category-badge-simple">
                                    <?php echo esc_html($category->name); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </header>
            <?php endif; ?>
            
            <--- Contenu principal centré -->
            <div class="archi-content-section">
                <div class="illustration-content">
                    <?php the_content(); ?>
                </div>
                
                <?php
                // Pagination
                wp_link_pages([
                    'before' => '<div class="page-links">' . __('Pages:', 'archi-graph'),
                    'after' => '</div>',
                ]);
                ?>
                
                <--- Informations techniques (style simplifié intégré au contenu) -->
                <?php
                $technique = get_post_meta(get_the_ID(), '_archi_illustration_technique', true);
                $dimensions = get_post_meta(get_the_ID(), '_archi_illustration_dimensions', true);
                $software = get_post_meta(get_the_ID(), '_archi_illustration_software', true);
                
                if ($technique || $dimensions || $software) : ?>
                    <div class="project-specs-grid">
                        <?php if ($technique) : ?>
                        <div class="spec-item">
                            <div class="spec-label"><?php _e('Technique :', 'archi-graph'); ?></div>
                            <div class="spec-value"><?php echo esc_html($technique); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($dimensions) : ?>
                        <div class="spec-item">
                            <div class="spec-label"><?php _e('Dimensions :', 'archi-graph'); ?></div>
                            <div class="spec-value"><?php echo esc_html($dimensions); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($software) : ?>
                        <div class="spec-item">
                            <div class="spec-label"><?php _e('Logiciels :', 'archi-graph'); ?></div>
                            <div class="spec-value"><?php echo esc_html($software); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            
            <?php
            // Illustrations similaires - Style simplifié
            $related = get_posts([
                'post_type' => 'archi_illustration',
                'numberposts' => 3,
                'post__not_in' => [get_the_ID()],
                'tax_query' => [
                    [
                        'taxonomy' => 'illustration_type',
                        'field' => 'term_id',
                        'terms' => wp_get_post_terms(get_the_ID(), 'illustration_type', ['fields' => 'ids'])
                    ]
                ]
            ]);
            
            if ($related) : ?>
                <aside class="related-illustrations-simple">
                    <h2 class="related-title-simple"><?php _e('Illustrations Similaires', 'archi-graph'); ?></h2>
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
            </div><--- .archi-content-section -->
            
        </article>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
