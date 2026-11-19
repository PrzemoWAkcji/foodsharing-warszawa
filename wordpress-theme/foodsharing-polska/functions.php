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
    if (is_page(['jadlodzielnie', 'warszawa'])) {
        $vendor = $uri . '/assets/vendor/leaflet';
        wp_enqueue_style('leaflet', $vendor . '/leaflet.css', [], '1.9.4');
        wp_enqueue_style('leaflet-markercluster', $vendor . '/MarkerCluster.css', ['leaflet'], '1.5.3');
        wp_enqueue_style('foodsharing-map', $uri . '/jadlodzielnie.css', ['foodsharing-style', 'leaflet'], filemtime($path . '/jadlodzielnie.css'));
        wp_enqueue_script('leaflet', $vendor . '/leaflet.js', [], '1.9.4', ['in_footer' => true, 'strategy' => 'defer']);
        wp_enqueue_script('leaflet-markercluster', $vendor . '/leaflet.markercluster.js', ['leaflet'], '1.5.3', ['in_footer' => true, 'strategy' => 'defer']);
        wp_enqueue_script('foodsharing-map', $uri . '/jadlodzielnie.js', ['leaflet', 'leaflet-markercluster'], filemtime($path . '/jadlodzielnie.js'), ['in_footer' => true, 'strategy' => 'defer']);
    }
}
add_action('wp_enqueue_scripts', 'foodsharing_assets');

function foodsharing_preload_font(): void {
    $font = get_template_directory_uri() . '/fonts/barlow-800-latin.woff2';
    echo '<link rel="preload" href="' . esc_url($font) . '" as="font" type="font/woff2" crossorigin>' . "\n";
    if (is_page(['jadlodzielnie', 'warszawa'])) {
        echo '<link rel="preconnect" href="https://tile.openstreetmap.org" crossorigin>' . "\n";
    }
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
            [home_url('/jadlodzielnie/'), 'Mapa jadłodzielni'], [$prefix . '#aktualnosci', 'Aktualności'], [$prefix . '#kontakt', 'Kontakt'],
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

/* ---------- SEO: tytuł, opis, canonical, Open Graph, JSON-LD ---------- */

function foodsharing_seo_plugin_active(): bool {
    return defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION') || defined('SEOPRESS_VERSION') || class_exists('The_SEO_Framework\Load');
}

function foodsharing_title_parts(array $parts): array {
    if (is_front_page() && !foodsharing_seo_plugin_active()) {
        $parts['tagline'] = 'jadłodzielnie i dzielenie się jedzeniem';
    }
    return $parts;
}
add_filter('document_title_parts', 'foodsharing_title_parts');

function foodsharing_meta_description(): string {
    if (is_front_page()) {
        return 'Foodsharing Polska: dzielimy się jedzeniem, zamiast je wyrzucać. Znajdź jadłodzielnię, poznaj zasady i dołącz do lokalnej społeczności.';
    }
    if (is_page('warszawa')) {
        return 'Jadłodzielnie w Warszawie: adresy według dzielnic, mapa punktów i zasady dzielenia się jedzeniem. Sprawdź, gdzie zostawić lub zabrać jedzenie.';
    }
    if (is_page('jadlodzielnie')) {
        return 'Mapa jadłodzielni w Polsce: adresy lodówek i szafek społecznych w ponad 60 miejscowościach. Znajdź punkt najbliżej siebie i podziel się jedzeniem.';
    }
    if (is_page('aktualnosci') || is_home()) {
        return 'Aktualności Foodsharing Polska: nowe jadłodzielnie, wydarzenia i historie ludzi, którzy ratują jedzenie przed wyrzuceniem.';
    }
    if (is_singular()) {
        $post = get_queried_object();
        $text = has_excerpt($post) ? get_the_excerpt($post) : wp_strip_all_tags(strip_shortcodes((string) $post->post_content));
        $text = trim(preg_replace('/\s+/u', ' ', $text));
        if ($text !== '') {
            return mb_strlen($text) > 158 ? rtrim(mb_substr($text, 0, 155)) . '…' : $text;
        }
    }
    if (is_category()) {
        $desc = trim(wp_strip_all_tags(category_description()));
        if ($desc !== '') { return $desc; }
    }
    return '';
}

function foodsharing_seo_head(): void {
    if (foodsharing_seo_plugin_active()) { return; }
    $desc = foodsharing_meta_description();
    $title = wp_get_document_title();
    if (is_front_page()) {
        $url = home_url('/');
        if (!is_singular()) echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
    } elseif (is_singular()) {
        $url = (string) wp_get_canonical_url();
    } else {
        $url = '';
    }
    if ($desc !== '') {
        echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
    }
    $image = get_template_directory_uri() . '/assets/og-foodsharing.jpg';
    $w = 1200; $h = 630;
    if (is_singular() && !is_front_page() && has_post_thumbnail()) {
        $src = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
        if ($src) { [$image, $w, $h] = $src; }
    }
    $og = [
        'og:locale' => 'pl_PL',
        'og:site_name' => get_bloginfo('name'),
        'og:type' => is_singular('post') ? 'article' : 'website',
        'og:title' => $title,
        'og:description' => $desc,
        'og:url' => $url,
        'og:image' => $image,
        'og:image:width' => (string) $w,
        'og:image:height' => (string) $h,
    ];
    foreach ($og as $property => $content) {
        if ($content !== '') {
            echo '<meta property="' . esc_attr($property) . '" content="' . esc_attr($content) . '">' . "\n";
        }
    }
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

    if (is_front_page()) {
        $data = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => home_url('/#organizacja'),
                    'name' => 'Foodsharing Polska',
                    'url' => home_url('/'),
                    'logo' => get_template_directory_uri() . '/assets/foodsharing-polska.png',
                    'email' => 'polska@foodsharing.pl',
                    'sameAs' => ['https://www.facebook.com/FoodsharingPolska'],
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => home_url('/#witryna'),
                    'name' => 'Foodsharing Polska',
                    'url' => home_url('/'),
                    'inLanguage' => 'pl-PL',
                    'publisher' => ['@id' => home_url('/#organizacja')],
                ],
            ],
        ];
        echo '<script type="application/ld+json">' . wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}
add_action('wp_head', 'foodsharing_seo_head', 2);
