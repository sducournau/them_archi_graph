<?php
/**
 * Template pour afficher une entrée du livre d'or
 */

get_header();
?>

<main id="main" class="site-main single-guestbook">
    <?php
    while (have_posts()) :
        the_post();
        
        $author_name = get_post_meta(get_the_ID(), '_archi_guestbook_author_name', true);
        $author_email = get_post_meta(get_the_ID(), '_archi_guestbook_author_email', true);
        $author_company = get_post_meta(get_the_ID(), '_archi_guestbook_author_company', true);
        $linked_articles = get_post_meta(get_the_ID(), '_archi_linked_articles', true) ?: [];
        $show_in_graph = get_post_meta(get_the_ID(), '_archi_show_in_graph', true);
        ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('guestbook-entry'); ?>>
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
                
                <div class="entry-meta">
                    <div class="guestbook-author-info">
                        <?php if ($author_name): ?>
                            <span class="author-name">
                                <i class="dashicons dashicons-admin-users"></i>
                                <strong><?php echo esc_html($author_name); ?></strong>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($author_company): ?>
                            <span class="author-company">
                                <i class="dashicons dashicons-building"></i>
                                <?php echo esc_html($author_company); ?>
                            </span>
                        <?php endif; ?>
                        
                        <span class="entry-date">
                            <i class="dashicons dashicons-calendar"></i>
                            <?php echo get_the_date(); ?>
                        </span>
                        
                        <?php if ($show_in_graph === '1'): ?>
                            <span class="in-graph-badge">
                                <i class="dashicons dashicons-networking"></i>
                                <?php _e('Visible dans le graphique', 'archi-graph'); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </header>
            
            <div class="entry-content">
                <?php the_content(); ?>
            </div>
            
            <?php if (!empty($linked_articles)): ?>
                <aside class="linked-articles">
                    <h2><?php _e('Articles mentionnés', 'archi-graph'); ?></h2>
                    <div class="linked-articles-grid">
                        <?php foreach ($linked_articles as $article_id):
                            $linked_post = get_post($article_id);
                            if (!$linked_post) continue;
                            
                            $thumbnail = get_the_post_thumbnail_url($article_id, 'medium');
                            $post_type_label = '';
                            switch ($linked_post->post_type) {
                                case 'archi_project':
                                    $post_type_label = __('Projet', 'archi-graph');
                                    break;
                                case 'archi_illustration':
                                    $post_type_label = __('Illustration', 'archi-graph');
                                    break;
                                default:
                                    $post_type_label = __('Article', 'archi-graph');
                            }
                            ?>
                            <div class="linked-article-card">
                                <?php if ($thumbnail): ?>
                                    <div class="card-thumbnail">
                                        <a href="<?php echo get_permalink($article_id); ?>">
                                            <img src="<?php echo esc_url($thumbnail); ?>" 
                                                 alt="<?php echo esc_attr($linked_post->post_title); ?>">
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div class="card-content">
                                    <span class="post-type-label"><?php echo esc_html($post_type_label); ?></span>
                                    <h3>
                                        <a href="<?php echo get_permalink($article_id); ?>">
                                            <?php echo esc_html($linked_post->post_title); ?>
                                        </a>
                                    </h3>
                                    <?php if ($linked_post->post_excerpt): ?>
                                        <p class="excerpt"><?php echo esc_html($linked_post->post_excerpt); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </aside>
            <?php endif; ?>
            
            <footer class="entry-footer">
                <div class="back-to-guestbook">
                    <a href="<?php echo esc_url(home_url('/livre-or')); ?>" class="button">
                        <?php _e('← Retour au livre d\'or', 'archi-graph'); ?>
                    </a>
                </div>
            </footer>
        </article>
        
    <?php endwhile; ?>
</main>

<style>
.single-guestbook {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.guestbook-entry {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    padding: 40px;
}

.guestbook-author-info {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: center;
    margin: 20px 0;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
}

.guestbook-author-info span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.guestbook-author-info .dashicons {
    font-size: 18px;
    width: 18px;
    height: 18px;
}

.in-graph-badge {
    background: #2ecc71;
    color: white;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 14px;
}

.linked-articles {
    margin-top: 40px;
    padding-top: 40px;
    border-top: 2px solid #e0e0e0;
}

.linked-articles h2 {
    font-size: 24px;
    margin-bottom: 20px;
}

.linked-articles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.linked-article-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.linked-article-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.linked-article-card .card-thumbnail img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.linked-article-card .card-content {
    padding: 15px;
}

.post-type-label {
    display: inline-block;
    background: #3498db;
    color: white;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 12px;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.linked-article-card h3 {
    font-size: 18px;
    margin: 10px 0;
}

.linked-article-card h3 a {
    color: #333;
    text-decoration: none;
}

.linked-article-card h3 a:hover {
    color: #3498db;
}

.linked-article-card .excerpt {
    font-size: 14px;
    color: #666;
    margin-top: 10px;
}

.entry-footer {
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
}

.back-to-guestbook .button {
    display: inline-block;
    padding: 12px 24px;
    background: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background 0.2s;
}

.back-to-guestbook .button:hover {
    background: #2980b9;
}

@media (max-width: 768px) {
    .guestbook-entry {
        padding: 20px;
    }
    
    .guestbook-author-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .linked-articles-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
get_footer();
