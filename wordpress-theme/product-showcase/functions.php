<?php

if (!defined('ABSPATH')) {
    exit;
}

function product_showcase_setup(): void
{
    load_theme_textdomain('product-showcase', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', ['height' => 80, 'width' => 240, 'flex-height' => true, 'flex-width' => true]);
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    register_nav_menus(['primary' => __('Primary menu', 'product-showcase')]);
}
add_action('after_setup_theme', 'product_showcase_setup');

function product_showcase_enqueue_assets(): void
{
    $theme = wp_get_theme();
    wp_enqueue_style('product-showcase-style', get_stylesheet_uri(), [], $theme->get('Version'));
}
add_action('wp_enqueue_scripts', 'product_showcase_enqueue_assets');

function product_showcase_register_content(): void
{
    register_post_type('product', [
        'labels' => [
            'name' => __('Products', 'product-showcase'),
            'singular_name' => __('Product', 'product-showcase'),
            'add_new_item' => __('Add product', 'product-showcase'),
            'edit_item' => __('Edit product', 'product-showcase'),
            'view_item' => __('View product', 'product-showcase'),
            'search_items' => __('Search products', 'product-showcase'),
            'not_found' => __('No products found', 'product-showcase'),
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'san-pham'],
        'menu_icon' => 'dashicons-products',
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
    ]);

    register_taxonomy('product_category', ['product'], [
        'labels' => [
            'name' => __('Product categories', 'product-showcase'),
            'singular_name' => __('Product category', 'product-showcase'),
        ],
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'danh-muc-san-pham'],
    ]);

    register_post_meta('product', '_ps_price', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => static fn(bool $allowed, string $meta_key, int $post_id): bool => current_user_can('edit_post', $post_id),
    ]);
    register_post_meta('product', '_ps_cta_url', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'esc_url_raw',
        'auth_callback' => static fn(bool $allowed, string $meta_key, int $post_id): bool => current_user_can('edit_post', $post_id),
    ]);
    register_post_meta('product', '_ps_image_url', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'esc_url_raw',
        'auth_callback' => static fn(bool $allowed, string $meta_key, int $post_id): bool => current_user_can('edit_post', $post_id),
    ]);
}
add_action('init', 'product_showcase_register_content', 5);

function product_showcase_add_product_meta_box(): void
{
    add_meta_box('product-showcase-details', __('Product details', 'product-showcase'), 'product_showcase_render_product_meta_box', 'product', 'normal', 'high');
}
add_action('add_meta_boxes_product', 'product_showcase_add_product_meta_box');

function product_showcase_render_product_meta_box(WP_Post $post): void
{
    wp_nonce_field('product_showcase_save_product', 'product_showcase_nonce');
    $price = get_post_meta($post->ID, '_ps_price', true);
    $cta_url = get_post_meta($post->ID, '_ps_cta_url', true);
    $image_url = get_post_meta($post->ID, '_ps_image_url', true);
    ?>
    <p>
        <label for="ps-price"><strong><?php esc_html_e('Price (VND)', 'product-showcase'); ?></strong></label><br>
        <input id="ps-price" name="ps_price" type="number" min="0" step="1000" value="<?php echo esc_attr($price); ?>" class="widefat">
    </p>
    <p>
        <label for="ps-cta-url"><strong><?php esc_html_e('Purchase or contact URL', 'product-showcase'); ?></strong></label><br>
        <input id="ps-cta-url" name="ps_cta_url" type="url" value="<?php echo esc_attr($cta_url); ?>" class="widefat">
    </p>
    <p>
        <label for="ps-image-url"><strong><?php esc_html_e('Fallback image URL (optional)', 'product-showcase'); ?></strong></label><br>
        <input id="ps-image-url" name="ps_image_url" type="url" value="<?php echo esc_attr($image_url); ?>" class="widefat">
        <span class="description"><?php esc_html_e('A featured image takes priority over this URL.', 'product-showcase'); ?></span>
    </p>
    <?php
}

function product_showcase_save_product_meta(int $post_id): void
{
    if (!isset($_POST['product_showcase_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['product_showcase_nonce'])), 'product_showcase_save_product')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $price = isset($_POST['ps_price']) ? sanitize_text_field(wp_unslash($_POST['ps_price'])) : '';
    $cta_url = isset($_POST['ps_cta_url']) ? esc_url_raw(wp_unslash($_POST['ps_cta_url'])) : '';
    $image_url = isset($_POST['ps_image_url']) ? esc_url_raw(wp_unslash($_POST['ps_image_url'])) : '';
    update_post_meta($post_id, '_ps_price', $price);
    update_post_meta($post_id, '_ps_cta_url', $cta_url);
    update_post_meta($post_id, '_ps_image_url', $image_url);
}
add_action('save_post_product', 'product_showcase_save_product_meta');

function product_showcase_filter_product_search(WP_Query $query): void
{
    if (!is_admin() && $query->is_main_query() && $query->is_search()) {
        $query->set('post_type', 'product');
    }
}
add_action('pre_get_posts', 'product_showcase_filter_product_search');

function product_showcase_seed_demo_products(): void
{
    if (get_option('product_showcase_demo_seeded')) {
        return;
    }

    if ((int) wp_count_posts('product')->publish === 0) {
        $products = [
            [
                'title' => 'Tai nghe Studio One',
                'category' => 'Âm thanh',
                'price' => '1890000',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1200&q=85',
                'excerpt' => 'Âm thanh cân bằng, thiết kế gọn nhẹ cho nhịp sống mỗi ngày.',
                'content' => 'Mẫu demo cho danh mục sản phẩm. Hãy thay mô tả, giá, ảnh và liên kết mua hàng bằng thông tin sản phẩm thực tế của bạn.',
            ],
            [
                'title' => 'Đồng hồ Field No. 02',
                'category' => 'Phụ kiện',
                'price' => '2450000',
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=1200&q=85',
                'excerpt' => 'Một điểm nhấn tối giản, dễ phối trong công việc lẫn cuối tuần.',
                'content' => 'Mẫu demo cho danh mục sản phẩm. Hãy thay mô tả, giá, ảnh và liên kết mua hàng bằng thông tin sản phẩm thực tế của bạn.',
            ],
            [
                'title' => 'Giày chạy Tempo',
                'category' => 'Thể thao',
                'price' => '1590000',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1200&q=85',
                'excerpt' => 'Êm nhẹ và linh hoạt cho những buổi chạy đầu ngày.',
                'content' => 'Mẫu demo cho danh mục sản phẩm. Hãy thay mô tả, giá, ảnh và liên kết mua hàng bằng thông tin sản phẩm thực tế của bạn.',
            ],
        ];

        foreach ($products as $product) {
            $post_id = wp_insert_post([
                'post_type' => 'product',
                'post_status' => 'publish',
                'post_title' => $product['title'],
                'post_excerpt' => $product['excerpt'],
                'post_content' => $product['content'],
            ]);
            if (is_wp_error($post_id)) {
                continue;
            }
            update_post_meta($post_id, '_ps_price', $product['price']);
            update_post_meta($post_id, '_ps_image_url', $product['image']);
            wp_set_object_terms($post_id, $product['category'], 'product_category', false);
        }
    }
    update_option('product_showcase_demo_seeded', 1, false);
}
add_action('init', 'product_showcase_seed_demo_products', 20);

function product_showcase_image_url(int $post_id): string
{
    if (has_post_thumbnail($post_id)) {
        return (string) get_the_post_thumbnail_url($post_id, 'large');
    }
    return (string) get_post_meta($post_id, '_ps_image_url', true);
}

function product_showcase_price(int $post_id): string
{
    $price = get_post_meta($post_id, '_ps_price', true);
    if ($price === '' || !is_numeric($price)) {
        return '';
    }
    return number_format_i18n((float) $price, 0) . ' ₫';
}

function product_showcase_menu_fallback(): void
{
    echo '<ul><li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'product-showcase') . '</a></li><li><a href="' . esc_url(get_post_type_archive_link('product')) . '">' . esc_html__('Products', 'product-showcase') . '</a></li></ul>';
}

function product_showcase_flush_rewrites(): void
{
    product_showcase_register_content();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'product_showcase_flush_rewrites');