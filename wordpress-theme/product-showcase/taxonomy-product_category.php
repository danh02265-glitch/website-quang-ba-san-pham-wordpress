<?php get_header(); ?>
<main id="main" class="page-main">
    <div class="wrap">
        <header class="page-heading">
            <div><p class="eyebrow"><?php esc_html_e('Product category', 'product-showcase'); ?></p><h1><?php single_term_title(); ?></h1></div>
            <?php $description = term_description(); if ($description) : ?>
                <div><?php echo wp_kses_post($description); ?></div>
            <?php else : ?>
                <p><?php esc_html_e('Explore products in this category.', 'product-showcase'); ?></p>
            <?php endif; ?>
        </header>
        <?php if (have_posts()) : ?>
            <div class="product-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/product-card'); ?>
                <?php endwhile; ?>
            </div>
            <nav class="pagination" aria-label="<?php esc_attr_e('Category product pages', 'product-showcase'); ?>">
                <?php the_posts_pagination(['mid_size' => 1]); ?>
            </nav>
        <?php else : ?>
            <p class="empty-state"><?php esc_html_e('There are no products in this category yet.', 'product-showcase'); ?></p>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>