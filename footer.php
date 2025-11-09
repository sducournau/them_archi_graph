        </main>
        
        <footer id="site-footer" class="site-footer">
            <div class="footer-inner">
                <?php if (is_active_sidebar('footer-1')) : ?>
                    <div class="footer-widgets">
                        <?php dynamic_sidebar('footer-1'); ?>
                    </div>
                <?php endif; ?>
                
                <div class="footer-info">
                    <div class="footer-text">
                        <?php 
                        $footer_text = get_option('archi_footer_text', '');
                        if ($footer_text) {
                            echo wp_kses_post($footer_text);
                        } else {
                            echo '&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '. ' . __('Tous droits réservés.', 'archi-graph');
                        }
                        ?>
                    </div>
                    
                    <?php if (get_option('archi_show_social_links', true)) : ?>
                        <div class="footer-social">
                            <?php archi_render_social_links(); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if (has_nav_menu('footer')) : ?>
                    <nav class="footer-navigation">
                        <?php
                        wp_nav_menu([
                            'theme_location' => 'footer',
                            'container' => false,
                            'menu_class' => 'footer-menu',
                            'depth' => 1
                        ]);
                        ?>
                    </nav>
                <?php endif; ?>
            </div>
        </footer>
    </div>

    <?php wp_footer(); ?>
</body>
</html>
