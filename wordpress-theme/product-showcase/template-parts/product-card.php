<article <?php post_class('product-card'); ?>>
    <a class="product-card__image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
        <?php
        $image_url = product_showcase_image_url(get_the_ID());
        if ($image_url) :
            ?>
            <img src="<?php echo esc_url($image_url); ?>" alt="" loading="lazy">
        <?php else : ?>
            <span class="product-card__placeholder">S</span>
        <?php endif; ?>
    </a>
    <div class="product-card__body">
        <?php $terms = get_the_terms(get_the_ID(), 'product_category'); if ($terms && !is_wp_error($terms)) : ?>
            <p class="product-card__category"><?php echo esc_html($terms[0]->name); ?></p>
        <?php endif; ?>
        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <p class="product-card__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
        <div class="product-card__foot">
            <?php $price = product_showcase_price(get_the_ID()); if ($price) : ?>
                <span class="product-price"><?php echo esc_html($price); ?></span>
            <?php else : ?>
                <span class="product-price"><?php esc_html_e('Ask for price', 'product-showcase'); ?></span>
            <?php endif; ?>
            <a class="product-arrow" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(sprintf(__('View %s', 'product-showcase'), get_the_title())); ?>">&rarr;</a>
        </div>
    </div>
</article>