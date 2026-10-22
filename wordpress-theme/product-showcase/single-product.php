<?php get_header(); ?>
<main id="main" class="page-main">
    <div class="wrap">
        <?php while (have_posts()) : the_post(); ?>
            <article class="single-product">
                <div class="single-product__image">
                    <?php
                    $image_url = product_showcase_image_url(get_the_ID());
                    if ($image_url) :
                        ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" fetchpriority="high">
                    <?php else : ?>
                        <div class="product-card__placeholder" aria-hidden="true">S</div>
                    <?php endif; ?>
                </div>
                <div class="single-product__details">
                    <p class="eyebrow"><?php esc_html_e('Product details', 'product-showcase'); ?></p>
                    <h1><?php the_title(); ?></h1>
                    <?php $price = product_showcase_price(get_the_ID()); if ($price) : ?>
                        <p class="single-product__price"><?php echo esc_html($price); ?></p>
                    <?php endif; ?>
                    <?php $terms = get_the_terms(get_the_ID(), 'product_category'); if ($terms && !is_wp_error($terms)) : ?>
                        <p class="single-product__categories"><?php echo esc_html(implode(' / ', wp_list_pluck($terms, 'name'))); ?></p>
                    <?php endif; ?>
                    <div class="single-product__content"><?php the_content(); ?></div>
                    <?php $cta_url = get_post_meta(get_the_ID(), '_ps_cta_url', true); if ($cta_url) : ?>
                        <a class="button" href="<?php echo esc_url($cta_url); ?>" rel="nofollow sponsored"><?php esc_html_e('Contact to purchase', 'product-showcase'); ?> &rarr;</a>
                    <?php endif; ?>
                    <p style="margin-top: 24px"><a class="text-link" href="<?php echo esc_url(get_post_type_archive_link('product')); ?>">&larr; <?php esc_html_e('Back to products', 'product-showcase'); ?></a></p>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer(); ?>