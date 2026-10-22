<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e('Skip to content', 'product-showcase'); ?></a>
<header class="site-header">
    <div class="wrap header-inner">
        <div class="brand">
            <span class="brand-mark" aria-hidden="true">S</span>
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a class="brand-name" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(get_bloginfo('name')); ?></a>
            <?php endif; ?>
        </div>
        <nav class="primary-nav" aria-label="<?php esc_attr_e('Primary navigation', 'product-showcase'); ?>">
            <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'fallback_cb' => 'product_showcase_menu_fallback']); ?>
        </nav>
        <a class="header-action" href="<?php echo esc_url(get_post_type_archive_link('product')); ?>"><?php esc_html_e('Explore products', 'product-showcase'); ?></a>
    </div>
</header>