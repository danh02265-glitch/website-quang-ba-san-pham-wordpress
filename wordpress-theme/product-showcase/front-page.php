<?php get_header(); ?>
<main id="main">
    <section class="hero">
        <div class="wrap hero-inner">
            <p class="eyebrow"><?php esc_html_e('A considered collection', 'product-showcase'); ?></p>
            <h1><?php echo esc_html(get_bloginfo('name')); ?></h1>
            <p class="hero-copy"><?php echo esc_html(get_bloginfo('description') ?: __('Discover useful products selected for everyday life.', 'product-showcase')); ?></p>
            <div class="hero-actions">
                <a class="button" href="<?php echo esc_url(get_post_type_archive_link('product')); ?>"><?php esc_html_e('Browse the collection', 'product-showcase'); ?></a>
                <a class="button button--light" href="#about"><?php esc_html_e('Our point of view', 'product-showcase'); ?></a>
            </div>
        </div>
    </section>

    <section class="section section--white" aria-labelledby="featured-heading">
        <div class="wrap">
            <div class="section-heading">
                <div><p class="eyebrow"><?php esc_html_e('Selected for you', 'product-showcase'); ?></p><h2 id="featured-heading"><?php esc_html_e('Featured products', 'product-showcase'); ?></h2></div>
                <a class="text-link" href="<?php echo esc_url(get_post_type_archive_link('product')); ?>"><?php esc_html_e('View all products', 'product-showcase'); ?> &rarr;</a>
            </div>
            <?php
            $featured_products = new WP_Query(['post_type' => 'product', 'posts_per_page' => 3, 'post_status' => 'publish']);
            if ($featured_products->have_posts()) :
                ?>
                <div class="product-grid">
                    <?php while ($featured_products->have_posts()) : $featured_products->the_post(); ?>
                        <?php get_template_part('template-parts/product-card'); ?>
                    <?php endwhile; ?>
                </div>
                <?php
                wp_reset_postdata();
            else :
                ?>
                <p class="empty-state"><?php esc_html_e('No products yet. Add a product in the WordPress dashboard to begin.', 'product-showcase'); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <section class="section" id="about">
        <div class="wrap story-band">
            <div><p class="eyebrow"><?php esc_html_e('The idea', 'product-showcase'); ?></p><h2><?php esc_html_e('Useful by design. Chosen with care.', 'product-showcase'); ?></h2></div>
            <p><?php echo esc_html(get_bloginfo('description') ?: __('We bring together products with a clear purpose, thoughtful details and lasting value. Explore the collection and find the right fit for your everyday.', 'product-showcase')); ?></p>
        </div>
    </section>
</main>
<?php get_footer(); ?>