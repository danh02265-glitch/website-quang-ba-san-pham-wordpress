<?php get_header(); ?>
<main id="main" class="page-main">
    <div class="wrap">
        <header class="page-heading">
            <div><p class="eyebrow"><?php esc_html_e('The collection', 'product-showcase'); ?></p><h1><?php post_type_archive_title(); ?></h1></div>
            <p><?php esc_html_e('Browse the collection, explore a category or search for a product.', 'product-showcase'); ?></p>
        </header>
        <div class="catalog-tools">
            <?php
            $categories = get_terms(['taxonomy' => 'product_category', 'hide_empty' => true]);
            if (!is_wp_error($categories) && $categories) :
                ?>
                <ul class="category-list" aria-label="<?php esc_attr_e('Product categories', 'product-showcase'); ?>">
                    <?php foreach ($categories as $category) : ?>
                        <li><a href="<?php echo esc_url(get_term_link($category)); ?>"><?php echo esc_html($category->name); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <form class="search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <label class="screen-reader-text" for="product-search"><?php esc_html_e('Search products', 'product-showcase'); ?></label>
                <input id="product-search" type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="<?php esc_attr_e('Search products', 'product-showcase'); ?>">
                <button type="submit"><?php esc_html_e('Search', 'product-showcase'); ?></button>
            </form>
        </div>
        <?php if (have_posts()) : ?>
            <div class="product-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/product-card'); ?>
                <?php endwhile; ?>
            </div>
            <nav class="pagination" aria-label="<?php esc_attr_e('Product pages', 'product-showcase'); ?>">
                <?php the_posts_pagination(['mid_size' => 1, 'prev_text' => __('Previous', 'product-showcase'), 'next_text' => __('Next', 'product-showcase')]); ?>
            </nav>
        <?php else : ?>
            <p class="empty-state"><?php esc_html_e('No products match this view. Try another search or category.', 'product-showcase'); ?></p>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>