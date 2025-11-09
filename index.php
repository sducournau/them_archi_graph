<?php
/**
 * Template principal - Liste des articles
 */

get_header(); ?>

<div class="posts-container">
    <?php if (have_posts()) : ?>
        <div class="posts-list">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>" class="post-thumbnail-link">
                            <div class="post-thumbnail">
                                <?php the_post_thumbnail('medium'); ?>
                            </div>
                        </a>
                    <?php endif; ?>
                    
                    <div class="post-content-wrapper">
                        <header class="post-header">
                            <?php 
                            $categories = get_the_category();
                            if ($categories) : ?>
                                <div class="post-categories">
                                    <?php foreach (array_slice($categories, 0, 2) as $category) : ?>
                                        <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" 
                                           class="category-badge">
                                            <?php echo esc_html($category->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <h2 class="post-title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                            
                            <div class="post-meta">
                                <time datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date('j F Y'); ?>
                                </time>
                                <span class="meta-separator">•</span>
                                <span class="post-author">
                                    <?php echo get_the_author(); ?>
                                </span>
                            </div>
                        </header>
                        
                        <div class="post-excerpt">
                            <?php echo wp_trim_words(get_the_excerpt(), 30); ?>
                        </div>
                        
                        <a href="<?php the_permalink(); ?>" class="read-more">
                            Lire la suite →
                        </a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        
        <?php 
        // Pagination
        the_posts_pagination([
            'mid_size' => 2,
            'prev_text' => '← Précédent',
            'next_text' => 'Suivant →',
        ]);
        ?>
        
    <?php else : ?>
        <div class="no-posts">
            <h2>Aucun article trouvé</h2>
            <p>Désolé, aucun article ne correspond à votre recherche.</p>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
