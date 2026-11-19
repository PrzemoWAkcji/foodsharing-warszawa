<?php
/**
 * Plugin Name: Foodsharing – Jadłodzielnie
 * Description: Baza jadłodzielni do mapy na stronie /jadlodzielnie/. Dodawanie i edycja w kokpicie: Jadłodzielnie → Dodaj nową.
 * Version: 1.0.0
 * Author: IT TIMING
 * Author URI: https://it-timing.eu/
 * Requires at least: 6.4
 * Requires PHP: 8.0
 * Text Domain: foodsharing-jadlodzielnie
 */

if (!defined('ABSPATH')) { exit; }

const FSJ_VERSION = '1.0.0';
const FSJ_CPT = 'fs_jadlodzielnia';
const FSJ_CACHE = 'fsj_points_v1';

/** Pola meta: klucz => [etykieta, typ]. */
function fsj_fields(): array {
    return [
        'city'     => ['Miejscowość', 'text'],
        'district' => ['Dzielnica / osiedle', 'text'],
        'address'  => ['Adres', 'text'],
        'hours'    => ['Godziny otwarcia', 'text'],
        'type'     => ['Rodzaj', 'type'],
        'desc'     => ['Jak trafić / opis', 'textarea'],
        'links'    => ['Linki (Facebook, strona) – jeden w linii', 'textarea'],
        'lat'      => ['Szerokość geograficzna', 'coord'],
        'lng'      => ['Długość geograficzna', 'coord'],
        'status'   => ['Status', 'status'],
        'check'    => ['Wymaga sprawdzenia', 'check'],
        'verified' => ['Data ostatniej weryfikacji', 'date'],
    ];
}

function fsj_meta(int $post_id, string $key): string {
    return (string) get_post_meta($post_id, '_fsj_' . $key, true);
}

/* ---------- Typ treści ---------- */

add_action('init', function (): void {
    register_post_type(FSJ_CPT, [
        'labels' => [
            'name' => 'Jadłodzielnie', 'singular_name' => 'Jadłodzielnia', 'menu_name' => 'Jadłodzielnie',
            'add_new' => 'Dodaj nową', 'add_new_item' => 'Dodaj jadłodzielnię', 'edit_item' => 'Edytuj jadłodzielnię',
            'new_item' => 'Nowa jadłodzielnia', 'view_item' => 'Zobacz jadłodzielnię', 'search_items' => 'Szukaj jadłodzielni',
            'not_found' => 'Nie znaleziono jadłodzielni', 'not_found_in_trash' => 'Brak jadłodzielni w koszu',
            'all_items' => 'Wszystkie jadłodzielnie', 'featured_image' => 'Zdjęcie jadłodzielni',
            'set_featured_image' => 'Ustaw zdjęcie', 'remove_featured_image' => 'Usuń zdjęcie', 'use_featured_image' => 'Użyj jako zdjęcia',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => false,
        'menu_position' => 21,
        'menu_icon' => 'dashicons-location-alt',
        'supports' => ['title', 'thumbnail'],
        'capability_type' => 'post',
        'map_meta_cap' => true,
    ]);
});

add_filter('enter_title_here', function (string $text, WP_Post $post): string {
    return $post->post_type === FSJ_CPT ? 'Nazwa miejsca, np. „Rada Osiedla Złote Łany” albo „ul. Stawowa 29”' : $text;
}, 10, 2);

/* ---------- Edycja w kokpicie ---------- */

add_action('add_meta_boxes', function (): void {
    add_meta_box('fsj_details', 'Dane jadłodzielni', 'fsj_render_box', FSJ_CPT, 'normal', 'high');
});

function fsj_render_box(WP_Post $post): void {
    wp_nonce_field('fsj_save', 'fsj_nonce');
    $v = [];
    foreach (array_keys(fsj_fields()) as $k) { $v[$k] = fsj_meta($post->ID, $k); }
    if ($v['type'] === '') { $v['type'] = 'foodsharing'; }
    if ($v['status'] === '') { $v['status'] = 'active'; }
    ?>
    <div class="fsj-grid">
      <p><label for="fsj_city"><strong>Miejscowość</strong> *</label><input type="text" id="fsj_city" name="fsj[city]" value="<?php echo esc_attr($v['city']); ?>" required list="fsj-cities"></p>
      <p><label for="fsj_district">Dzielnica / osiedle</label><input type="text" id="fsj_district" name="fsj[district]" value="<?php echo esc_attr($v['district']); ?>"></p>
      <p class="fsj-wide"><label for="fsj_address"><strong>Adres</strong> *</label><input type="text" id="fsj_address" name="fsj[address]" value="<?php echo esc_attr($v['address']); ?>" placeholder="np. ul. Stawowa 29 (obok stołówki)" required></p>
      <p class="fsj-wide"><label for="fsj_hours"><strong>Godziny otwarcia</strong></label><input type="text" id="fsj_hours" name="fsj[hours]" value="<?php echo esc_attr($v['hours']); ?>" placeholder="np. całodobowo albo pon.–pt. 8:00–18:00, sob. 9:00–14:00"></p>
      <p><label for="fsj_type">Rodzaj</label><select id="fsj_type" name="fsj[type]"><option value="foodsharing" <?php selected($v['type'], 'foodsharing'); ?>>Jadłodzielnia Foodsharing</option><option value="inne" <?php selected($v['type'], 'inne'); ?>>Podobna inicjatywa</option></select></p>
      <p><label for="fsj_status">Status</label><select id="fsj_status" name="fsj[status]"><option value="active" <?php selected($v['status'], 'active'); ?>>Czynna – widoczna na mapie</option><option value="paused" <?php selected($v['status'], 'paused'); ?>>Tymczasowo nieczynna – ukryta</option></select></p>
      <p class="fsj-wide"><label for="fsj_desc">Jak trafić / opis</label><textarea id="fsj_desc" name="fsj[desc]" rows="3"><?php echo esc_textarea($v['desc']); ?></textarea></p>
      <p class="fsj-wide"><label for="fsj_links">Linki (Facebook, strona) – jeden w linii</label><textarea id="fsj_links" name="fsj[links]" rows="2"><?php echo esc_textarea($v['links']); ?></textarea></p>
    </div>
    <h4 class="fsj-h">Położenie na mapie *</h4>
    <p class="description">Kliknij „Znajdź po adresie” albo kliknij w mapę. Pinezkę można przeciągnąć dokładnie na miejsce.</p>
    <p><button type="button" class="button" id="fsj-geocode">⌖ Znajdź po adresie</button> <span id="fsj-geo-status" aria-live="polite"></span></p>
    <div id="fsj-map" data-lat="<?php echo esc_attr($v['lat']); ?>" data-lng="<?php echo esc_attr($v['lng']); ?>"></div>
    <div class="fsj-grid">
      <p><label for="fsj_lat">Szerokość</label><input type="text" id="fsj_lat" name="fsj[lat]" value="<?php echo esc_attr($v['lat']); ?>" inputmode="decimal" required></p>
      <p><label for="fsj_lng">Długość</label><input type="text" id="fsj_lng" name="fsj[lng]" value="<?php echo esc_attr($v['lng']); ?>" inputmode="decimal" required></p>
    </div>
    <h4 class="fsj-h">Weryfikacja</h4>
    <div class="fsj-grid">
      <p><label><input type="checkbox" name="fsj[check]" value="1" <?php checked($v['check'], '1'); ?>> Adres lub godziny wymagają sprawdzenia</label></p>
      <p><label for="fsj_verified">Data ostatniej weryfikacji</label><input type="date" id="fsj_verified" name="fsj[verified]" value="<?php echo esc_attr($v['verified']); ?>"> <button type="button" class="button-link" id="fsj-today">dziś</button></p>
    </div>
    <datalist id="fsj-cities"><?php foreach (fsj_cities() as $c) { echo '<option value="' . esc_attr($c) . '">'; } ?></datalist>
    <?php
}

function fsj_cities(): array {
    global $wpdb;
    $rows = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value <> '' ORDER BY meta_value", '_fsj_city'));
    return array_map('strval', $rows ?: []);
}

add_action('save_post_' . FSJ_CPT, function (int $post_id): void {
    if (!isset($_POST['fsj_nonce']) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST['fsj_nonce'])), 'fsj_save')) { return; }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return; }
    if (!current_user_can('edit_post', $post_id)) { return; }
    $in = isset($_POST['fsj']) && is_array($_POST['fsj']) ? wp_unslash($_POST['fsj']) : [];
    fsj_save_fields($post_id, $in);
});

function fsj_save_fields(int $post_id, array $in): void {
    foreach (fsj_fields() as $k => [, $type]) {
        $raw = $in[$k] ?? '';
        switch ($type) {
            case 'textarea':
                $val = sanitize_textarea_field((string) $raw);
                if ($k === 'links') {
                    $urls = array_filter(array_map(static fn($u) => esc_url_raw(trim($u), ['http', 'https']), preg_split('/\R/', $val)));
                    $val = implode("\n", $urls);
                }
                break;
            case 'coord':
                $num = str_replace(',', '.', trim((string) $raw));
                $val = is_numeric($num) ? (string) round((float) $num, 6) : '';
                break;
            case 'type':
                $val = $raw === 'inne' ? 'inne' : 'foodsharing';
                break;
            case 'status':
                $val = $raw === 'paused' ? 'paused' : 'active';
                break;
            case 'check':
                $val = $raw ? '1' : '';
                break;
            case 'date':
                $val = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $raw) ? (string) $raw : '';
                break;
            default:
                $val = sanitize_text_field((string) $raw);
        }
        update_post_meta($post_id, '_fsj_' . $k, $val);
    }
}

add_action('admin_enqueue_scripts', function (string $hook): void {
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== FSJ_CPT) { return; }
    $url = plugin_dir_url(__FILE__);
    wp_enqueue_style('fsj-admin', $url . 'assets/admin.css', [], FSJ_VERSION);
    if (in_array($hook, ['post.php', 'post-new.php'], true)) {
        wp_enqueue_style('fsj-leaflet', $url . 'assets/vendor/leaflet/leaflet.css', [], '1.9.4');
        wp_enqueue_script('fsj-leaflet', $url . 'assets/vendor/leaflet/leaflet.js', [], '1.9.4', true);
        wp_enqueue_script('fsj-admin', $url . 'assets/admin.js', ['fsj-leaflet'], FSJ_VERSION, true);
    }
});

/* ---------- Lista w kokpicie ---------- */

add_filter('manage_' . FSJ_CPT . '_posts_columns', function (array $cols): array {
    return [
        'cb' => $cols['cb'], 'fsj_photo' => 'Zdjęcie', 'title' => 'Nazwa', 'fsj_city' => 'Miejscowość',
        'fsj_address' => 'Adres', 'fsj_hours' => 'Godziny', 'fsj_type' => 'Rodzaj', 'fsj_state' => 'Stan', 'date' => $cols['date'],
    ];
});

add_action('manage_' . FSJ_CPT . '_posts_custom_column', function (string $col, int $id): void {
    switch ($col) {
        case 'fsj_photo':
            echo has_post_thumbnail($id) ? get_the_post_thumbnail($id, [60, 60]) : '<span class="fsj-nophoto">brak</span>';
            break;
        case 'fsj_city':
            echo esc_html(fsj_meta($id, 'city')) . (fsj_meta($id, 'district') ? '<br><small>' . esc_html(fsj_meta($id, 'district')) . '</small>' : '');
            break;
        case 'fsj_address':
            echo esc_html(fsj_meta($id, 'address'));
            break;
        case 'fsj_hours':
            echo fsj_meta($id, 'hours') !== '' ? esc_html(fsj_meta($id, 'hours')) : '<span class="fsj-warn">brak danych</span>';
            break;
        case 'fsj_type':
            echo fsj_meta($id, 'type') === 'inne' ? 'Podobna inicjatywa' : 'Foodsharing';
            break;
        case 'fsj_state':
            $out = [];
            $out[] = fsj_meta($id, 'status') === 'paused' ? '<span class="fsj-paused">nieczynna</span>' : '<span class="fsj-ok">czynna</span>';
            if (fsj_meta($id, 'check') === '1') { $out[] = '<span class="fsj-warn">do sprawdzenia</span>'; }
            if (fsj_meta($id, 'verified')) { $out[] = '<small>sprawdzono ' . esc_html(mysql2date('d.m.Y', fsj_meta($id, 'verified'))) . '</small>'; }
            echo implode('<br>', $out); // Wartości powyżej są escapowane.
            break;
    }
}, 10, 2);

add_filter('manage_edit-' . FSJ_CPT . '_sortable_columns', function (array $cols): array {
    $cols['fsj_city'] = 'fsj_city';
    return $cols;
});

add_action('restrict_manage_posts', function (string $post_type): void {
    if ($post_type !== FSJ_CPT) { return; }
    $cur = isset($_GET['fsj_filter']) ? sanitize_key(wp_unslash($_GET['fsj_filter'])) : '';
    $opts = ['' => 'Wszystkie', 'check' => 'Do sprawdzenia', 'nohours' => 'Bez godzin', 'nophoto' => 'Bez zdjęcia', 'paused' => 'Nieczynne'];
    echo '<select name="fsj_filter">';
    foreach ($opts as $k => $label) { echo '<option value="' . esc_attr($k) . '"' . selected($cur, $k, false) . '>' . esc_html($label) . '</option>'; }
    echo '</select>';
    $city = isset($_GET['fsj_city']) ? sanitize_text_field(wp_unslash($_GET['fsj_city'])) : '';
    echo '<select name="fsj_city"><option value="">Wszystkie miejscowości</option>';
    foreach (fsj_cities() as $c) { echo '<option value="' . esc_attr($c) . '"' . selected($city, $c, false) . '>' . esc_html($c) . '</option>'; }
    echo '</select>';
});

add_action('pre_get_posts', function (WP_Query $q): void {
    if (!is_admin() || !$q->is_main_query() || $q->get('post_type') !== FSJ_CPT) { return; }
    $meta = [];
    $filter = isset($_GET['fsj_filter']) ? sanitize_key(wp_unslash($_GET['fsj_filter'])) : '';
    if ($filter === 'check') { $meta[] = ['key' => '_fsj_check', 'value' => '1']; }
    if ($filter === 'paused') { $meta[] = ['key' => '_fsj_status', 'value' => 'paused']; }
    if ($filter === 'nohours') { $meta[] = ['relation' => 'OR', ['key' => '_fsj_hours', 'value' => ''], ['key' => '_fsj_hours', 'compare' => 'NOT EXISTS']]; }
    if ($filter === 'nophoto') { $meta[] = ['key' => '_thumbnail_id', 'compare' => 'NOT EXISTS']; }
    $city = isset($_GET['fsj_city']) ? sanitize_text_field(wp_unslash($_GET['fsj_city'])) : '';
    if ($city !== '') { $meta[] = ['key' => '_fsj_city', 'value' => $city]; }
    if ($meta) { $q->set('meta_query', $meta); }
    if ($q->get('orderby') === 'fsj_city' || !$q->get('orderby')) {
        $q->set('meta_key', '_fsj_city');
        $q->set('orderby', ['meta_value' => $q->get('order') === 'desc' ? 'DESC' : 'ASC', 'title' => 'ASC']);
    }
});

/* ---------- Dane dla strony ---------- */

/**
 * Czynne jadłodzielnie w formacie dla mapy i listy (z pamięcią podręczną).
 * Klucze: id, c (miasto), r (dzielnica), n (nazwa), ad (adres), h (godziny), d (opis), t (f|i),
 * a/o (szer./dł.), l (linki), img/imgw/imgh (miniatura), full (duże zdjęcie), v (data weryfikacji).
 */
function fsj_get_points(): array {
    $cached = get_transient(FSJ_CACHE);
    if (is_array($cached)) { return $cached; }
    $ids = get_posts([
        'post_type' => FSJ_CPT, 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids',
        'meta_query' => [['relation' => 'OR', ['key' => '_fsj_status', 'value' => 'active'], ['key' => '_fsj_status', 'compare' => 'NOT EXISTS']]],
        'no_found_rows' => true,
    ]);
    $out = [];
    foreach ($ids as $id) {
        $lat = fsj_meta($id, 'lat'); $lng = fsj_meta($id, 'lng');
        if (!is_numeric($lat) || !is_numeric($lng)) { continue; }
        $p = [
            'id' => $id, 'c' => fsj_meta($id, 'city'), 'r' => fsj_meta($id, 'district'), 'n' => get_the_title($id),
            'ad' => fsj_meta($id, 'address'), 'h' => fsj_meta($id, 'hours'), 'd' => fsj_meta($id, 'desc'),
            't' => fsj_meta($id, 'type') === 'inne' ? 'i' : 'f', 'a' => (float) $lat, 'o' => (float) $lng,
            'l' => array_values(array_filter(explode("\n", fsj_meta($id, 'links')))), 'v' => fsj_meta($id, 'verified'),
        ];
        $thumb = get_post_thumbnail_id($id);
        if ($thumb) {
            $s = wp_get_attachment_image_src($thumb, 'medium');
            $l = wp_get_attachment_image_src($thumb, 'large');
            if ($s) { $p['img'] = $s[0]; $p['imgw'] = $s[1]; $p['imgh'] = $s[2]; }
            if ($l) { $p['full'] = $l[0]; }
        }
        $out[] = $p;
    }
    $collator = class_exists('Collator') ? new Collator('pl_PL') : null;
    usort($out, static function ($x, $y) use ($collator) {
        $a = $x['c'] . '|' . $x['n']; $b = $y['c'] . '|' . $y['n'];
        return $collator ? $collator->compare($a, $b) : strcmp(remove_accents($a), remove_accents($b));
    });
    set_transient(FSJ_CACHE, $out, DAY_IN_SECONDS);
    return $out;
}

function fsj_flush(): void {
    delete_transient(FSJ_CACHE);
}
add_action('save_post_' . FSJ_CPT, 'fsj_flush', 20);
add_action('deleted_post', 'fsj_flush');
add_action('trashed_post', 'fsj_flush');
add_action('untrashed_post', 'fsj_flush');
add_action('updated_post_meta', static function ($mid, $post_id, $key) { if ($key === '_thumbnail_id' && get_post_type($post_id) === FSJ_CPT) { fsj_flush(); } }, 10, 3);
add_action('added_post_meta', static function ($mid, $post_id, $key) { if ($key === '_thumbnail_id' && get_post_type($post_id) === FSJ_CPT) { fsj_flush(); } }, 10, 3);
add_action('deleted_post_meta', static function ($mids, $post_id, $key) { if ($key === '_thumbnail_id' && get_post_type($post_id) === FSJ_CPT) { fsj_flush(); } }, 10, 3);

add_action('rest_api_init', function (): void {
    register_rest_route('foodsharing/v1', '/punkty', [
        'methods' => 'GET',
        'permission_callback' => '__return_true',
        'callback' => static function () {
            $res = new WP_REST_Response(fsj_get_points());
            // private: proxy hostingu (DataHouse) myliło publiczną odpowiedź z adresem /jadlodzielnie/.
            $res->header('Cache-Control', 'private, max-age=300');
            return $res;
        },
    ]);
});

/* ---------- Kokpit: skrót na pulpicie ---------- */

add_action('wp_dashboard_setup', function (): void {
    if (!current_user_can('edit_posts')) { return; }
    wp_add_dashboard_widget('fsj_dashboard', 'Jadłodzielnie', static function (): void {
        $counts = wp_count_posts(FSJ_CPT);
        $check = (new WP_Query(['post_type' => FSJ_CPT, 'post_status' => 'publish', 'meta_key' => '_fsj_check', 'meta_value' => '1', 'fields' => 'ids', 'posts_per_page' => 1]))->found_posts;
        $base = admin_url('edit.php?post_type=' . FSJ_CPT);
        echo '<p><strong>' . (int) ($counts->publish ?? 0) . '</strong> opublikowanych · <a href="' . esc_url($base . '&fsj_filter=check') . '">' . (int) $check . ' do sprawdzenia</a></p>';
        echo '<p><a class="button button-primary" href="' . esc_url(admin_url('post-new.php?post_type=' . FSJ_CPT)) . '">Dodaj jadłodzielnię</a> <a class="button" href="' . esc_url($base) . '">Wszystkie</a> <a class="button" href="' . esc_url(home_url('/jadlodzielnie/')) . '" target="_blank" rel="noopener">Zobacz mapę ↗</a></p>';
    });
});
