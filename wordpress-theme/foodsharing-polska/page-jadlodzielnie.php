<?php
if (!defined('ABSPATH')) { exit; }
$fs_dir = get_template_directory();
$fs_uri = get_template_directory_uri();
$fs_stats = json_decode((string) file_get_contents($fs_dir . '/parts/jadlodzielnie-stats.json'), true) ?: ['count' => 0, 'cities' => 0, 'updated' => ''];
$fs_list = (string) file_get_contents($fs_dir . '/parts/jadlodzielnie-lista.html');
$fs_list = str_replace('href="/warszawa/"', 'href="' . esc_url(home_url('/warszawa/')) . '"', $fs_list);
$fs_plural = static function (int $n, string $one, string $few, string $many): string {
    if ($n === 1) { return $one; }
    return ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 12 || $n % 100 > 14)) ? $few : $many;
};
get_header();
?>
<main id="main">
  <section class="fs-map-page" aria-labelledby="fs-title"><div class="wrap">
    <div class="directory-header"><div><span class="label">Mapa jadłodzielni</span><h1 id="fs-title">Jadłodzielnie <em>w Polsce.</em></h1><p>Lodówki i szafki, w których możesz zostawić jedzenie albo się nim poczęstować. Wpisz miasto lub ulicę, a mapa pokaże punkty w okolicy.</p></div>
      <div class="fs-stats"><div><strong><?php echo (int) $fs_stats['count']; ?></strong><span><?php echo esc_html($fs_plural((int) $fs_stats['count'], 'punkt', 'punkty', 'punktów')); ?></span></div><div><strong><?php echo (int) $fs_stats['cities']; ?></strong><span>miejscowości</span></div></div></div>
    <div class="directory-controls fs-controls">
      <label for="fs-search">Szukaj miasta lub adresu</label><input id="fs-search" type="search" placeholder="Np. Kraków lub Grójecka" autocomplete="off">
      <fieldset class="fs-type-filter"><legend>Rodzaj punktu</legend><label><input type="checkbox" value="foodsharing" checked> <span class="fs-dot fs-dot-f" aria-hidden="true"></span> Foodsharing</label><label><input type="checkbox" value="inne" checked> <span class="fs-dot fs-dot-i" aria-hidden="true"></span> Podobne inicjatywy</label></fieldset>
      <button type="button" class="fs-near" id="fs-near">⌖ Najbliżej mnie</button>
      <span id="fs-count" role="status"><?php echo (int) $fs_stats['count'] . ' ' . esc_html($fs_plural((int) $fs_stats['count'], 'punkt', 'punkty', 'punktów')) . ' w ' . (int) $fs_stats['cities'] . ' ' . ((int) $fs_stats['cities'] === 1 ? 'miejscowości' : 'miejscowościach'); ?></span>
      <p class="fs-near-status" id="fs-near-status" aria-live="polite"></p>
    </div>
    <div class="fs-map-shell"><div id="fs-map" role="region" aria-label="Mapa jadłodzielni w Polsce" data-src="<?php echo esc_url($fs_uri . '/assets/jadlodzielnie.json?v=' . filemtime($fs_dir . '/assets/jadlodzielnie.json')); ?>"></div></div>
    <p class="fs-map-note">Mapa: © współtwórcy OpenStreetMap. Lokalizacja z przycisku „Najbliżej mnie” jest używana tylko w Twojej przeglądarce i nie trafia do nas.</p>
    <?php echo $fs_list; // Wygenerowane i zescapowane przez tools/build_jadlodzielnie.py ?>
    <p class="directory-source">Źródła: mapa jadłodzielni Foodsharing Polska oraz <a href="https://warszawa19115.pl/-/jadlodzielnie" target="_blank" rel="noopener noreferrer">wykaz Warszawa 19115</a> (Warszawa, aktualizacja 22.10.2026). Stan danych: <?php echo esc_html($fs_stats['updated']); ?>. Godziny otwarcia mogą się zmieniać, więc przed wizytą sprawdź profil lokalnej grupy.</p>
  </div></section>

  <section class="fs-report"><div class="wrap fs-report-grid"><div><span class="label">Pomóż nam aktualizować mapę</span><h2>Znasz punkt, którego <em>tu nie ma?</em></h2></div><div><p>Napisz, jeśli jadłodzielnia zmieniła adres, godziny lub przestała działać. Chętnie dodamy też nowe miejsca.</p><p><a href="mailto:polska@foodsharing.pl?subject=Mapa%20jad%C5%82odzielni%20%E2%80%93%20zmiana">polska@foodsharing.pl ↗</a></p><p>Chcesz założyć jadłodzielnię? <a href="mailto:nowajadlodzielnia@foodsharing.pl">nowajadlodzielnia@foodsharing.pl ↗</a></p></div></div></section>
</main>
<?php get_footer(); ?>
