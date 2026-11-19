<?php
/** Foodsharing Polska theme setup. */
if (!defined('ABSPATH')) { exit; }

function foodsharing_setup(): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('responsive-embeds');
    register_nav_menus(['primary' => 'Menu główne']);
    add_image_size('foodsharing-card', 768, 384, false);
}
add_action('after_setup_theme', 'foodsharing_setup');

function foodsharing_assets(): void {
    $uri = get_template_directory_uri();
    $path = get_template_directory();
    wp_enqueue_style('foodsharing-fonts', $uri . '/fonts.css', [], filemtime($path . '/fonts.css'));
    wp_enqueue_style('foodsharing-style', get_stylesheet_uri(), ['foodsharing-fonts'], filemtime($path . '/style.css'));
    if (is_front_page() || is_home() || is_archive() || is_search() || is_page('aktualnosci')) {
        wp_enqueue_style('foodsharing-home', $uri . '/home.css', ['foodsharing-style'], filemtime($path . '/home.css'));
    }
    wp_enqueue_script('foodsharing-theme', $uri . '/theme.js', [], filemtime($path . '/theme.js'), true);
}
add_action('wp_enqueue_scripts', 'foodsharing_assets');

function foodsharing_preload_font(): void {
    $font = get_template_directory_uri() . '/fonts/barlow-800-latin.woff2';
    echo '<link rel="preload" href="' . esc_url($font) . '" as="font" type="font/woff2" crossorigin>' . "\n";
}
add_action('wp_head', 'foodsharing_preload_font', 1);

function foodsharing_menus(string $location): void {
    if (has_nav_menu('primary')) {
        wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'items_wrap' => '<ul class="nav-menu">%3$s</ul>', 'fallback_cb' => false]);
        return;
    }
    $home = home_url('/');
    $prefix = is_front_page() ? '' : $home;
    if ($location === 'warszawa') {
        $links = [
            [$home, 'Strona główna'], ['#jak-dzialamy', 'Jak działamy'],
            ['#lista-jadlodzielni', 'Jadłodzielnie'], ['#zasady', 'Zasady'], ['#kontakt', 'Kontakt'],
        ];
    } else {
        $links = [
            [$prefix . '#o-nas', 'O nas'], [$prefix . '#jak-to-dziala', 'Jak to działa'],
            [$prefix . '#miasta', 'Miasta'], [$prefix . '#aktualnosci', 'Aktualności'], [$prefix . '#kontakt', 'Kontakt'],
        ];
    }
    foreach ($links as [$url, $label]) {
        echo '<a href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
    }
}

function foodsharing_article_image(int $post_id): string {
    if (has_post_thumbnail($post_id)) {
        return (string) get_the_post_thumbnail($post_id, 'foodsharing-card', [
            'class' => 'news-image', 'loading' => 'lazy',
        ]);
    }
    $fallbacks = [
        'mobilna-jadlodzielnia-w-warszawie' => 'aktualnosc-mobilna-jadlodzielnia.png',
        'dziel-sie-zamiast-wyrzucac' => 'aktualnosc-dziel-sie.png',
        'bylismy-na-dniu-sasiada-w-mal-surma' => 'aktualnosc-dzien-sasiada.jpg',
    ];
    $slug = get_post_field('post_name', $post_id);
    if (isset($fallbacks[$slug])) {
        $image_url = get_template_directory_uri() . '/assets/' . $fallbacks[$slug];
    } else {
        $content = (string) get_post_field('post_content', $post_id);
        if (!preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches)) { return ''; }
        $image_url = $matches[1];
    }
    return '<img class="news-image" src="' . esc_url($image_url) . '" alt="' . esc_attr(get_the_title($post_id)) . '" width="768" height="384" loading="lazy">';
}

function foodsharing_filter_posts(WP_Query $query): void {
    if (!is_admin() && $query->is_main_query() && $query->is_home()) {
        $query->set('category_name', 'aktualnosci');
    }
}
add_action('pre_get_posts', 'foodsharing_filter_posts');
