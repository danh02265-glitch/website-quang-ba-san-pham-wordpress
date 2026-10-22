<?php get_header(); ?>
<main id="main" class="page-main">
    <div class="wrap">
        <header class="page-heading">
            <div><p class="eyebrow"><?php esc_html_e('Search results', 'product-showcase'); ?></p><h1><?php printf(esc_html__('Results for: %s', 'product-showcase'), esc_html(get_search_query())); ?></h1></div>
        </header>
        <?php if (have_posts()) : ?>
            <div class="product-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/product-card'); ?>
                <?php endwhile; ?>
            </div>
            <nav class="pagination" aria-label="<?php esc_attr_e('Search result pages', 'product-showcase'); ?>">
                <?php the_posts_pagination(['mid_size' => 1]); ?>
            </nav>
        <?php else : ?>
            <p class="empty-state"><?php esc_html_e('No products matched your search.', 'product-showcase'); ?></p>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>