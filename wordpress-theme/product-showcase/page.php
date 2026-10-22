<?php get_header(); ?>
<main id="main" class="page-main">
    <div class="wrap">
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class(); ?>>
                <header class="page-heading"><h1><?php the_title(); ?></h1></header>
                <div class="single-product__content"><?php the_content(); ?></div>
            </article>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer(); ?>