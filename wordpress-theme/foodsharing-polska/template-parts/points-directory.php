<?php
/**
 * Mapa + spis jadłodzielni z danych wtyczki (fsj_get_points).
 */
if (!defined('ABSPATH')) { exit; }
$fs_points = function_exists('fsj_get_points') ? fsj_get_points() : [];
$fs_groups = [];
foreach ($fs_points as $p) {
    $fs_groups[$p['c']][] = $p;
}
$fs_count = count($fs_points);
$fs_group_count = count($fs_groups);
$fs_plural = static function (int $n, string $one, string $few, string $many): string {
    if ($n === 1) { return $one; }
    return ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 12 || $n % 100 > 14)) ? $few : $many;
};
$fs_unit = ['miejscowości', 'miejscowościach'];
$fs_fold = static fn(string $s): string => mb_strtolower(remove_accents($s));
?>
    <div class="directory-controls fs-controls">
      <label for="fs-search">Szukaj miasta, dzielnicy lub adresu</label><input id="fs-search" type="search" placeholder="Np. Kraków, Mokotów lub Grójecka" autocomplete="off">
      <fieldset class="fs-type-filter"><legend>Rodzaj punktu</legend><label><input type="checkbox" value="foodsharing" checked> <span class="fs-dot fs-dot-f" aria-hidden="true"></span> Foodsharing</label><label><input type="checkbox" value="inne" checked> <span class="fs-dot fs-dot-i" aria-hidden="true"></span> Podobne inicjatywy</label></fieldset>
      <button type="button" class="fs-near" id="fs-near">⌖ Najbliżej mnie</button>
      <span id="fs-count" role="status" data-unit-one="<?php echo esc_attr($fs_unit[0]); ?>" data-unit-many="<?php echo esc_attr($fs_unit[1]); ?>"><?php echo (int) $fs_count . ' ' . esc_html($fs_plural($fs_count, 'punkt', 'punkty', 'punktów')) . ' w ' . (int) $fs_group_count . ' ' . esc_html($fs_group_count === 1 ? $fs_unit[0] : $fs_unit[1]); ?></span>
      <p class="fs-near-status" id="fs-near-status" aria-live="polite"></p>
    </div>
    <div class="fs-map-shell"><div id="fs-map" role="region" aria-label="Mapa jadłodzielni w Polsce" data-src="<?php echo esc_url(rest_url('foodsharing/v1/punkty')); ?>"></div></div>
    <p class="fs-map-note">Mapa: © współtwórcy OpenStreetMap. Lokalizacja z przycisku „Najbliżej mnie” jest używana tylko w Twojej przeglądarce i nie trafia do nas.</p>
    <div class="districts fs-cities">
    <?php foreach ($fs_groups as $group => $items) : $n = count($items); ?>
      <details class="district" data-city="<?php echo esc_attr($group); ?>"><summary><span><?php echo esc_html($group); ?></span><span class="district-count"><?php echo (int) $n . ' ' . esc_html($fs_plural($n, 'punkt', 'punkty', 'punktów')); ?></span><span class="district-chevron" aria-hidden="true">⌄</span></summary>
        <ul class="fs-place-list">
        <?php foreach ($items as $p) :
            $type = $p['t'] === 'i' ? 'inne' : 'foodsharing';
            $search = $fs_fold(implode(' ', [$p['c'], $p['n'], $p['r'], $p['ad'], $p['d']]));
            $route = 'https://www.google.com/maps/dir/?api=1&destination=' . $p['a'] . ',' . $p['o'];
            $sub = $p['r']; ?>
          <li class="fs-place" data-id="<?php echo (int) $p['id']; ?>" data-type="<?php echo esc_attr($type); ?>" data-search="<?php echo esc_attr($search); ?>">
            <?php if (!empty($p['img'])) : ?><img class="fs-place-photo" src="<?php echo esc_url($p['img']); ?>" alt="<?php echo esc_attr('Jadłodzielnia: ' . $p['c'] . ', ' . $p['n']); ?>" width="96" height="96" loading="lazy" decoding="async"><?php else : ?><span class="fs-place-photo fs-place-nophoto" aria-hidden="true">✳</span><?php endif; ?>
            <div class="fs-place-body">
              <strong><?php echo esc_html($p['n']); ?></strong>
              <?php if ($p['ad'] !== '' && $p['ad'] !== $p['n']) : ?><span class="fs-addr"><?php echo esc_html($p['ad'] . ($sub ? ' · ' . $sub : '')); ?></span><?php elseif ($sub) : ?><span class="fs-addr"><?php echo esc_html($sub); ?></span><?php endif; ?>
              <span class="fs-hours"><?php echo $p['h'] !== '' ? '<span aria-hidden="true">◷</span> ' . esc_html($p['h']) : 'Godziny: brak danych'; ?></span>
              <?php if ($p['d'] !== '') : ?><p><?php echo esc_html($p['d']); ?></p><?php endif; ?>
              <span class="fs-type fs-type-<?php echo esc_attr($type); ?>"><?php echo $type === 'inne' ? 'Podobna inicjatywa' : 'Jadłodzielnia Foodsharing'; ?></span>
            </div>
            <div class="fs-place-actions"><button type="button" class="fs-show" data-id="<?php echo (int) $p['id']; ?>">Na mapie</button><a href="<?php echo esc_url($route); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr('Trasa: ' . $p['c'] . ', ' . ($p['ad'] ?: $p['n'])); ?>">Trasa ↗</a></div>
          </li>
        <?php endforeach; ?>
        </ul></details>
    <?php endforeach; ?>
    </div>
