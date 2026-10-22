<?php get_header(); ?>
<main id="main" class="page-main">
    <div class="wrap">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class('section'); ?>>
                    <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                    <div><?php the_content(); ?></div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <p class="empty-state"><?php esc_html_e('Nothing to display yet.', 'product-showcase'); ?></p>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>