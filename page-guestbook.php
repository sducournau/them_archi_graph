<?php
/**
 * Template Name: Page Livre d'Or
 * Description: Affiche le livre d'or avec formulaire de soumission et liste des entrées
 */

get_header();
?>

<main id="main" class="site-main page-guestbook">
    <div class="guestbook-container">
        <header class="page-header">
            <h1 class="page-title"><?php _e('Livre d\'Or', 'archi-graph'); ?></h1>
            <p class="page-description">
                <?php _e('Partagez vos impressions, vos retours et vos suggestions sur nos projets et réalisations.', 'archi-graph'); ?>
            </p>
        </header>
        
        <!-- Formulaire de soumission -->
        <section class="guestbook-form-section">
            <h2><?php _e('Laissez votre commentaire', 'archi-graph'); ?></h2>
            <?php
            $guestbook_form_id = get_option('archi_guestbook_form_id');
            if ($guestbook_form_id && function_exists('wpforms_display')) {
                wpforms_display($guestbook_form_id);
            } else {
                echo '<p>' . __('Le formulaire de livre d\'or n\'est pas encore configuré.', 'archi-graph') . '</p>';
            }
            ?>
        </section>
        
        <!-- Liste des entrées publiées -->
        <section class="guestbook-entries-section">
            <h2><?php _e('Témoignages', 'archi-graph'); ?></h2>
            
            <?php
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            
            $guestbook_query = new WP_Query([
                'post_type' => 'archi_guestbook',
                'post_status' => 'publish',
                'posts_per_page' => 10,
                'paged' => $paged,
                'orderby' => 'date',
                'order' => 'DESC'
            ]);
            
            if ($guestbook_query->have_posts()):
                ?>
                <div class="guestbook-entries">
                    <?php
                    while ($guestbook_query->have_posts()):
                        $guestbook_query->the_post();
                        
                        $author_name = get_post_meta(get_the_ID(), '_archi_guestbook_author_name', true);
                        $author_company = get_post_meta(get_the_ID(), '_archi_guestbook_author_company', true);
                        $linked_articles = get_post_meta(get_the_ID(), '_archi_linked_articles', true) ?: [];
                        $show_in_graph = get_post_meta(get_the_ID(), '_archi_show_in_graph', true);
                        ?>
                        
                        <article class="guestbook-entry-card">
                            <div class="entry-header">
                                <div class="author-info">
                                    <div class="author-avatar">
                                        <?php echo strtoupper(substr($author_name, 0, 1)); ?>
                                    </div>
                                    <div class="author-details">
                                        <h3 class="author-name"><?php echo esc_html($author_name); ?></h3>
                                        <?php if ($author_company): ?>
                                            <p class="author-company"><?php echo esc_html($author_company); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="entry-date">
                                    <?php echo get_the_date(); ?>
                                </div>
                            </div>
                            
                            <div class="entry-content">
                                <?php the_content(); ?>
                            </div>
                            
                            <div class="entry-footer">
                                <?php if (!empty($linked_articles)): ?>
                                    <div class="linked-articles-tags">
                                        <span class="tag-label"><?php _e('Concernant:', 'archi-graph'); ?></span>
                                        <?php foreach ($linked_articles as $article_id):
                                            $linked_post = get_post($article_id);
                                            if (!$linked_post) continue;
                                            ?>
                                            <a href="<?php echo get_permalink($article_id); ?>" class="article-tag">
                                                <?php echo esc_html($linked_post->post_title); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="entry-actions">
                                    <?php if ($show_in_graph === '1'): ?>
                                        <span class="graph-badge" title="<?php _e('Visible dans le graphique', 'archi-graph'); ?>">
                                            <i class="dashicons dashicons-networking"></i>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <a href="<?php the_permalink(); ?>" class="read-more">
                                        <?php _e('Voir détails', 'archi-graph'); ?>
                                    </a>
                                </div>
                            </div>
                        </article>
                        
                    <?php endwhile; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($guestbook_query->max_num_pages > 1): ?>
                    <nav class="guestbook-pagination">
                        <?php
                        echo paginate_links([
                            'total' => $guestbook_query->max_num_pages,
                            'current' => $paged,
                            'prev_text' => __('&laquo; Précédent', 'archi-graph'),
                            'next_text' => __('Suivant &raquo;', 'archi-graph')
                        ]);
                        ?>
                    </nav>
                <?php endif; ?>
                
            <?php else: ?>
                <p class="no-entries"><?php _e('Aucun témoignage pour le moment. Soyez le premier à laisser votre commentaire !', 'archi-graph'); ?></p>
            <?php endif;
            
            wp_reset_postdata();
            ?>
        </section>
    </div>
</main>

<style>
.page-guestbook {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.guestbook-container {
    background: #fff;
}

.page-header {
    text-align: center;
    margin-bottom: 60px;
    padding-bottom: 30px;
    border-bottom: 2px solid #e0e0e0;
}

.page-title {
    font-size: 48px;
    margin-bottom: 15px;
    color: #2c3e50;
}

.page-description {
    font-size: 18px;
    color: #7f8c8d;
    max-width: 700px;
    margin: 0 auto;
}

.guestbook-form-section {
    background: #f8f9fa;
    padding: 40px;
    border-radius: 8px;
    margin-bottom: 60px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.guestbook-form-section h2 {
    font-size: 28px;
    margin-bottom: 25px;
    color: #2c3e50;
}

.guestbook-entries-section h2 {
    font-size: 32px;
    margin-bottom: 30px;
    color: #2c3e50;
    border-left: 4px solid #3498db;
    padding-left: 15px;
}

.guestbook-entries {
    display: grid;
    gap: 25px;
}

.guestbook-entry-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 30px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.guestbook-entry-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.entry-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #ecf0f1;
}

.author-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.author-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3498db, #2ecc71);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: bold;
}

.author-details h3 {
    margin: 0;
    font-size: 18px;
    color: #2c3e50;
}

.author-company {
    margin: 3px 0 0;
    font-size: 14px;
    color: #7f8c8d;
}

.entry-date {
    font-size: 14px;
    color: #95a5a6;
}

.entry-content {
    margin: 20px 0;
    line-height: 1.7;
    color: #34495e;
}

.entry-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #ecf0f1;
}

.linked-articles-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

.tag-label {
    font-size: 13px;
    color: #7f8c8d;
    font-weight: 600;
}

.article-tag {
    display: inline-block;
    background: #ecf0f1;
    color: #34495e;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 13px;
    text-decoration: none;
    transition: background 0.2s;
}

.article-tag:hover {
    background: #3498db;
    color: white;
}

.entry-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.graph-badge {
    display: flex;
    align-items: center;
    color: #2ecc71;
    font-size: 20px;
}

.graph-badge .dashicons {
    width: 20px;
    height: 20px;
}

.read-more {
    color: #3498db;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: color 0.2s;
}

.read-more:hover {
    color: #2980b9;
}

.no-entries {
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
    font-size: 18px;
    background: #f8f9fa;
    border-radius: 8px;
}

.guestbook-pagination {
    margin-top: 40px;
    text-align: center;
}

.guestbook-pagination .page-numbers {
    display: inline-block;
    padding: 10px 15px;
    margin: 0 5px;
    background: #ecf0f1;
    color: #34495e;
    text-decoration: none;
    border-radius: 5px;
    transition: background 0.2s;
}

.guestbook-pagination .page-numbers:hover,
.guestbook-pagination .page-numbers.current {
    background: #3498db;
    color: white;
}

@media (max-width: 768px) {
    .page-title {
        font-size: 32px;
    }
    
    .guestbook-form-section {
        padding: 20px;
    }
    
    .guestbook-entry-card {
        padding: 20px;
    }
    
    .entry-header {
        flex-direction: column;
        gap: 10px;
    }
    
    .entry-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
}
</style>

<?php
get_footer();
