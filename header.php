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
        <?php 
        // Get header customizer settings
        $header_sticky = get_theme_mod('archi_header_sticky', true);
        $header_transparent = get_theme_mod('archi_header_transparent', false);
        $header_sticky_behavior = get_theme_mod('archi_header_sticky_behavior', 'always');
        
        // Build header classes
        $header_classes = 'site-header';
        if ($header_sticky) {
            $header_classes .= ' sticky-header';
        }
        if ($header_transparent) {
            $header_classes .= ' transparent-header';
        }
        ?>
        <header id="site-header" class="<?php echo esc_attr($header_classes); ?>" data-sticky-behavior="<?php echo esc_attr($header_sticky_behavior); ?>">
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
                            'fallback_cb'    => 'archi_fallback_menu',
                        ]);
                    } else {
                        archi_fallback_menu();
                    }
                    ?>
                </nav>
            </div>
        </header>

        <main id="site-content" class="site-content">
