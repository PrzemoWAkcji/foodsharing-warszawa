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
get_header();
?>
<main id="main">
  <section class="fs-map-page" aria-labelledby="fs-title"><div class="wrap">
    <div class="directory-header"><div><span class="label">Mapa jadłodzielni</span><h1 id="fs-title">Jadłodzielnie <em>w Polsce.</em></h1><p>Lodówki i szafki, w których możesz zostawić jedzenie albo się nim poczęstować. Wpisz miasto lub ulicę, a mapa pokaże punkty w okolicy.</p></div>
      <div class="fs-stats"><div><strong><?php echo (int) $fs_count; ?></strong><span><?php echo esc_html($fs_plural($fs_count, 'punkt', 'punkty', 'punktów')); ?></span></div><div><strong><?php echo (int) $fs_city_count; ?></strong><span><?php echo esc_html($fs_plural($fs_city_count, 'miejscowość', 'miejscowości', 'miejscowości')); ?></span></div></div></div>
    <?php get_template_part('template-parts/points-directory'); ?>
    <p class="directory-source">Źródła: mapa jadłodzielni Foodsharing Polska oraz <a href="https://warszawa19115.pl/-/jadlodzielnie" target="_blank" rel="noopener noreferrer">wykaz Warszawa 19115</a> (Warszawa). Godziny otwarcia mogą się zmieniać, więc przed wizytą sprawdź profil lokalnej grupy.</p>
  </div></section>

  <section class="fs-report"><div class="wrap fs-report-grid"><div><span class="label">Pomóż nam aktualizować mapę</span><h2>Znasz punkt, którego <em>tu nie ma?</em></h2></div><div><p>Napisz, jeśli jadłodzielnia zmieniła adres, godziny lub przestała działać. Chętnie dodamy też nowe miejsca.</p><p><a href="mailto:polska@foodsharing.pl?subject=Mapa%20jad%C5%82odzielni%20%E2%80%93%20zmiana">polska@foodsharing.pl ↗</a></p><p>Chcesz założyć jadłodzielnię? <a href="mailto:nowajadlodzielnia@foodsharing.pl">nowajadlodzielnia@foodsharing.pl ↗</a></p></div></div></section>
</main>
<?php get_footer(); ?>
