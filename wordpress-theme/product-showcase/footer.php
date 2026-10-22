<footer class="site-footer">
    <div class="wrap footer-inner">
        <a class="footer-brand" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(get_bloginfo('name')); ?></a>
        <p><?php echo esc_html(get_bloginfo('description')); ?></p>
        <p>&copy; <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html(get_bloginfo('name')); ?></p>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>