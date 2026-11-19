<?php
if (!defined('ABSPATH')) { exit; }
$fs_points = function_exists('fsj_get_points') ? fsj_get_points() : [];
$fs_count = count($fs_points);
$fs_cities = [];
foreach ($fs_points as $p) { $fs_cities[$p['c']][] = $p; }
$fs_city_count = count($fs_cities);
$fs_plural = static function (int $n, string $one, string $few, string $many): string {
    if ($n === 1) { return $one; }
    return ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 12 || $n % 100 > 14)) ? $few : $many;
};
$fs_fold = static fn(string $s): string => mb_strtolower(remove_accents($s));
get_header();
?>
<main id="main">
  <section class="fs-map-page" aria-labelledby="fs-title"><div class="wrap">
    <div class="directory-header"><div><span class="label">Mapa jadłodzielni</span><h1 id="fs-title">Jadłodzielnie <em>w Polsce.</em></h1><p>Lodówki i szafki, w których możesz zostawić jedzenie albo się nim poczęstować. Wpisz miasto lub ulicę, a mapa pokaże punkty w okolicy.</p></div>
      <div class="fs-stats"><div><strong><?php echo (int) $fs_count; ?></strong><span><?php echo esc_html($fs_plural($fs_count, 'punkt', 'punkty', 'punktów')); ?></span></div><div><strong><?php echo (int) $fs_city_count; ?></strong><span><?php echo esc_html($fs_plural($fs_city_count, 'miejscowość', 'miejscowości', 'miejscowości')); ?></span></div></div></div>
    <div class="directory-controls fs-controls">
      <label for="fs-search">Szukaj miasta lub adresu</label><input id="fs-search" type="search" placeholder="Np. Kraków lub Grójecka" autocomplete="off">
      <fieldset class="fs-type-filter"><legend>Rodzaj punktu</legend><label><input type="checkbox" value="foodsharing" checked> <span class="fs-dot fs-dot-f" aria-hidden="true"></span> Foodsharing</label><label><input type="checkbox" value="inne" checked> <span class="fs-dot fs-dot-i" aria-hidden="true"></span> Podobne inicjatywy</label></fieldset>
      <button type="button" class="fs-near" id="fs-near">⌖ Najbliżej mnie</button>
      <span id="fs-count" role="status"><?php echo (int) $fs_count . ' ' . esc_html($fs_plural($fs_count, 'punkt', 'punkty', 'punktów')) . ' w ' . (int) $fs_city_count . ' ' . ($fs_city_count === 1 ? 'miejscowości' : 'miejscowościach'); ?></span>
      <p class="fs-near-status" id="fs-near-status" aria-live="polite"></p>
    </div>
    <div class="fs-map-shell"><div id="fs-map" role="region" aria-label="Mapa jadłodzielni w Polsce" data-src="<?php echo esc_url(rest_url('foodsharing/v1/punkty')); ?>"></div></div>
    <p class="fs-map-note">Mapa: © współtwórcy OpenStreetMap. Lokalizacja z przycisku „Najbliżej mnie” jest używana tylko w Twojej przeglądarce i nie trafia do nas.</p>
    <div class="districts fs-cities">
    <?php foreach ($fs_cities as $city => $items) : $n = count($items); ?>
      <details class="district" data-city="<?php echo esc_attr($city); ?>"><summary><span><?php echo esc_html($city); ?></span><span class="district-count"><?php echo (int) $n . ' ' . esc_html($fs_plural($n, 'punkt', 'punkty', 'punktów')); ?></span><span class="district-chevron" aria-hidden="true">⌄</span></summary>
        <?php if ($city === 'Warszawa') : ?><p class="fs-city-note">Spis według dzielnic znajdziesz też na <a href="<?php echo esc_url(home_url('/warszawa/')); ?>">stronie Foodsharing Warszawa</a>.</p><?php endif; ?>
        <ul class="fs-place-list">
        <?php foreach ($items as $p) :
            $type = $p['t'] === 'i' ? 'inne' : 'foodsharing';
            $search = $fs_fold(implode(' ', [$p['c'], $p['n'], $p['r'], $p['ad'], $p['d']]));
            $route = 'https://www.google.com/maps/dir/?api=1&destination=' . $p['a'] . ',' . $p['o']; ?>
          <li class="fs-place" data-id="<?php echo (int) $p['id']; ?>" data-type="<?php echo esc_attr($type); ?>" data-search="<?php echo esc_attr($search); ?>">
            <?php if (!empty($p['img'])) : ?><img class="fs-place-photo" src="<?php echo esc_url($p['img']); ?>" alt="<?php echo esc_attr('Jadłodzielnia: ' . $p['c'] . ', ' . $p['n']); ?>" width="96" height="96" loading="lazy" decoding="async"><?php else : ?><span class="fs-place-photo fs-place-nophoto" aria-hidden="true">✳</span><?php endif; ?>
            <div class="fs-place-body">
              <strong><?php echo esc_html($p['n']); ?></strong>
              <?php if ($p['ad'] !== '' && $p['ad'] !== $p['n']) : ?><span class="fs-addr"><?php echo esc_html($p['ad'] . ($p['r'] ? ' · ' . $p['r'] : '')); ?></span><?php elseif ($p['r']) : ?><span class="fs-addr"><?php echo esc_html($p['r']); ?></span><?php endif; ?>
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
    <p class="directory-source">Źródła: mapa jadłodzielni Foodsharing Polska oraz <a href="https://warszawa19115.pl/-/jadlodzielnie" target="_blank" rel="noopener noreferrer">wykaz Warszawa 19115</a> (Warszawa). Godziny otwarcia mogą się zmieniać, więc przed wizytą sprawdź profil lokalnej grupy.</p>
  </div></section>

  <section class="fs-report"><div class="wrap fs-report-grid"><div><span class="label">Pomóż nam aktualizować mapę</span><h2>Znasz punkt, którego <em>tu nie ma?</em></h2></div><div><p>Napisz, jeśli jadłodzielnia zmieniła adres, godziny lub przestała działać. Chętnie dodamy też nowe miejsca.</p><p><a href="mailto:polska@foodsharing.pl?subject=Mapa%20jad%C5%82odzielni%20%E2%80%93%20zmiana">polska@foodsharing.pl ↗</a></p><p>Chcesz założyć jadłodzielnię? <a href="mailto:nowajadlodzielnia@foodsharing.pl">nowajadlodzielnia@foodsharing.pl ↗</a></p></div></div></section>
</main>
<?php get_footer(); ?>
