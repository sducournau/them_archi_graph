<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <?php 
    // Custom meta tags and SEO
    if (function_exists('archi_render_meta_tags')) {
        archi_render_meta_tags(); 
    }
    ?>
    
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    
    <div id="page-wrapper">
        <header id="site-header" class="site-header">
            <div class="header-inner">
                <div class="site-branding">
                    <?php if (has_custom_logo()) : ?>
                        <div class="site-logo">
                            <?php the_custom_logo(); ?>
                        </div>
                    <?php else : ?>
                        <h1 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                    <?php endif; ?>
                    
                    <?php if (get_bloginfo('description')) : ?>
                        <p class="site-description"><?php bloginfo('description'); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php 
                // Custom header image
                if (get_header_image()) : ?>
                    <div class="header-image">
                        <img src="<?php header_image(); ?>" 
                             height="<?php echo get_custom_header()->height; ?>" 
                             width="<?php echo get_custom_header()->width; ?>" 
                             alt="<?php bloginfo('name'); ?>" />
                    </div>
                <?php endif; ?>
                
                <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e('Primary Menu', 'archi-graph'); ?>">
                    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                        <span class="menu-toggle-icon">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                        <span class="screen-reader-text"><?php esc_html_e('Menu', 'archi-graph'); ?></span>
                    </button>
                    
                    <?php
                    if (has_nav_menu('primary')) {
                        wp_nav_menu([
                            'theme_location' => 'primary',
                            'menu_id'        => 'primary-menu',
                            'menu_class'     => 'nav-menu',
                            'container'      => 'div',
                            'container_class' => 'menu-primary-container',
                            'fallback_cb'    => false,
                        ]);
                    } else {
                        // Fallback menu if no menu is assigned
                        echo '<div class="menu-primary-container">';
                        echo '<ul id="primary-menu" class="nav-menu">';
                        echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'archi-graph') . '</a></li>';
                        wp_list_pages([
                            'title_li' => '',
                            'depth' => 1,
                        ]);
                        
                        // Add guestbook link if page exists
                        $guestbook_page = get_page_by_path('livre-dor');
                        if (!$guestbook_page) {
                            $guestbook_page = get_page_by_path('guestbook');
                        }
                        if ($guestbook_page) {
                            echo '<li class="guestbook-menu-item"><a href="' . esc_url(get_permalink($guestbook_page->ID)) . '">' . esc_html__('Livre d\'or', 'archi-graph') . '</a></li>';
                        }
                        
                        echo '</ul>';
                        echo '</div>';
                    }
                    ?>
                </nav>
            </div>
        </header>

        <main id="site-content" class="site-content">
