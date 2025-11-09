<?php
/**
 * Template pour les projets architecturaux - Version simplifiée
 */

get_header(); ?>

<div class="project-container">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('single-project'); ?>>
            
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
                
                $thumbnail_id = get_post_thumbnail_id();
                $thumbnail_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
            ?>
                <!-- Hero Fullscreen pour projet -->
                <div class="<?php echo esc_attr(implode(' ', $hero_classes)); ?>" data-parallax="<?php echo esc_attr($parallax); ?>">
                    <?php ?>
                    <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" 
                         alt="<?php echo esc_attr($thumbnail_alt ?: get_the_title()); ?>" 
                         class="hero-media">
                    
                    <div class="hero-overlay" style="opacity: <?php echo esc_attr($overlay_opacity); ?>;"></div>
                    
                    <div class="hero-content">
                        <?php
                        $categories = get_the_terms(get_the_ID(), 'archi_project_type');
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
                        
                        <?php 
                        $location = get_post_meta(get_the_ID(), '_archi_project_location', true);
                        if ($location) : ?>
                            <div class="hero-subtitle">
                                <?php echo esc_html($location); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Indicateur de scroll -->
                    <div class="archi-scroll-indicator">
                        <div class="scroll-icon"></div>
                        <span class="scroll-text"><?php _e('Défiler', 'archi-graph'); ?></span>
                    </div>
                </div>
            <?php else : ?>
                <header class="project-header-simple">
                    <h1 class="project-title-simple"><?php the_title(); ?></h1>
                    <?php 
                    $location = get_post_meta(get_the_ID(), '_archi_project_location', true);
                    if ($location) : ?>
                        <p class="project-location-simple"><?php echo esc_html($location); ?></p>
                    <?php endif; ?>
                </header>
            <?php endif; ?>
            
            <!-- Contenu principal centré -->
            <div class="archi-content-section">
                <div class="project-content">
                    <?php the_content(); ?>
                </div>
                
                <?php
                // Pagination
                wp_link_pages([
                    'before' => '<div class="page-links">' . __('Pages:', 'archi-graph'),
                    'after' => '</div>',
                ]);
                ?>
                
                <!-- Informations du projet (style simplifié intégré au contenu) -->
                <?php
                $surface = get_post_meta(get_the_ID(), '_archi_project_surface', true);
                $cost = get_post_meta(get_the_ID(), '_archi_project_cost', true);
                $client = get_post_meta(get_the_ID(), '_archi_project_client', true);
                $location = get_post_meta(get_the_ID(), '_archi_project_location', true);
                $start_date = get_post_meta(get_the_ID(), '_archi_project_start_date', true);
                $end_date = get_post_meta(get_the_ID(), '_archi_project_end_date', true);
                
                if ($surface || $cost || $client || $location || $start_date) : ?>
                    <div class="project-specs-grid">
                        <?php if ($location) : ?>
                        <div class="spec-item">
                            <div class="spec-label"><?php _e('Localisation :', 'archi-graph'); ?></div>
                            <div class="spec-value"><?php echo esc_html($location); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($start_date) : ?>
                        <div class="spec-item">
                            <div class="spec-label"><?php _e('Année :', 'archi-graph'); ?></div>
                            <div class="spec-value"><?php echo date_i18n('Y', strtotime($start_date)); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($client) : ?>
                        <div class="spec-item">
                            <div class="spec-label"><?php _e('Maître d\'ouvrage :', 'archi-graph'); ?></div>
                            <div class="spec-value"><?php echo esc_html($client); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($cost) : ?>
                        <div class="spec-item">
                            <div class="spec-label"><?php _e('Coût :', 'archi-graph'); ?></div>
                            <div class="spec-value">
                                <?php 
                                if ($cost > 0) {
                                    echo number_format($cost, 0, ',', ' ') . ' €';
                                } else {
                                    echo 'nc';
                                }
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($surface) : ?>
                        <div class="spec-item">
                            <div class="spec-label"><?php _e('Surface :', 'archi-graph'); ?></div>
                            <div class="spec-value"><?php echo number_format($surface, 0, ',', ' '); ?> m²</div>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            
            <?php
            // Projets similaires - Style simplifié
            $related = get_posts([
                'post_type' => 'archi_project',
                'numberposts' => 3,
                'post__not_in' => [get_the_ID()],
                'tax_query' => [
                    [
                        'taxonomy' => 'archi_project_type',
                        'field' => 'term_id',
                        'terms' => wp_get_post_terms(get_the_ID(), 'archi_project_type', ['fields' => 'ids'])
                    ]
                ]
            ]);
            
            if ($related) : ?>
                <aside class="related-projects-simple">
                    <h2 class="related-title-simple"><?php _e('Projets Similaires', 'archi-graph'); ?></h2>
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
                                        <?php
                                        $related_location = get_post_meta($related_post->ID, '_archi_project_location', true);
                                        if ($related_location) : ?>
                                            <p class="related-location-simple"><?php echo esc_html($related_location); ?></p>
                                        <?php endif; ?>
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

<?php get_footer(); ?>
