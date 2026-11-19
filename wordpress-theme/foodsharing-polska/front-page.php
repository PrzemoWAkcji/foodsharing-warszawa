<?php if (!defined('ABSPATH')) { exit; }
$posts_page = (int) get_option('page_for_posts');
$posts_url = $posts_page ? get_permalink($posts_page) : home_url('/#aktualnosci');
get_header(); ?>
<main id="main">
    <section class="home-hero" aria-labelledby="home-title"><div class="wrap home-hero-grid">
      <div class="home-hero-copy"><span class="eyebrow"><span class="dot"></span> Foodsharing Polska</span><h1 id="home-title">Nie wyrzucaj.<br><em>Podziel się.</em></h1><p>Pełnowartościowe jedzenie może trafić do ludzi zamiast do kosza. Razem ratujemy żywność, dzielimy się nią i tworzymy miejsca dostępne dla każdego.</p><div class="actions"><a class="btn btn-primary" href="#jak-to-dziala">Zobacz, jak to działa <span aria-hidden="true">↗</span></a><a class="text-link" href="#miasta">Znajdź jadłodzielnię ↓</a></div><span class="home-hero-caption">Dobro wraca. Jedzenie też może krążyć.</span></div>
      <div class="home-hero-visual" aria-hidden="true"><div class="home-halo"></div><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/foodsharing-bag.svg'); ?>" alt="" width="620" height="620"><div class="hero-stamp">DZIEL SIĘ<br>TYM, CO DOBRE <span>✳</span></div><span class="hero-spark spark-one">✳</span><span class="hero-spark spark-two">✳</span></div>
    </div></section>

    <div class="home-ribbon" aria-hidden="true"><div class="wrap"><span>weź, jeśli potrzebujesz</span><span>✳</span><span>zostaw, jeśli możesz</span><span>✳</span><span>jedzenie łączy ludzi</span></div></div>

    <section class="section home-about" id="o-nas" aria-labelledby="about-title"><div class="wrap"><div class="home-section-top"><span class="label">01 / O nas</span><h2 id="about-title">Dobre jedzenie.<br><em>Dobry obieg.</em></h2><p>Ratujemy żywność przed zmarnowaniem. Zachęcamy do dzielenia się jedzeniem wszystkich ze wszystkimi i budujemy poczucie odpowiedzialności za ograniczenie marnowania.</p></div><div class="home-action-grid"><article><span class="action-mark">✳</span><span class="action-num">01</span><h3>Edukujemy</h3><p>Prowadzimy warsztaty i bierzemy udział w spotkaniach poświęconych niemarnowaniu jedzenia. Jesteśmy otwarci na współpracę.</p></article><article><span class="action-mark">↗</span><span class="action-num">02</span><h3>Ratujemy żywność</h3><p>Działamy jako wolontariusze. Nie pobieramy opłat za pozyskane jedzenie — dzielimy się nim i dostarczamy je do jadłodzielni.</p></article><article><span class="action-mark">♡</span><span class="action-num">03</span><h3>Otwieramy jadłodzielnie</h3><p>Tworzymy przestrzenie, w których każdy może zostawić żywność dobrą do spożycia lub bezpłatnie się nią poczęstować.</p></article></div></div></section>

    <section class="section home-how" id="jak-to-dziala" aria-labelledby="how-title"><div class="wrap home-how-grid"><div><span class="label">02 / Jak to działa</span><h2 id="how-title">Podzielić się jest <em>prościej, niż myślisz.</em></h2><p>Jadłodzielnia to oznaczona lodówka lub szafka. Możesz przynieść do niej jedzenie, którego nie wykorzystasz, albo wziąć to, czego potrzebujesz.</p><a class="btn btn-dark" href="<?php echo esc_url(home_url('/warszawa/#lista-jadlodzielni')); ?>">Znajdź punkt w Warszawie <span aria-hidden="true">↗</span></a></div><div class="home-how-list"><div><span>01</span><div><h3>Wybierz dobre jedzenie</h3><p>Przynieś produkty, które sam mógłbyś bez obaw zjeść. Zapakuj je szczelnie i zadbaj o opis domowych wyrobów.</p></div></div><div><span>02</span><div><h3>Zostaw lub weź</h3><p>Możesz podzielić się nadwyżką albo po prostu poczęstować się jedzeniem. Korzystanie z jadłodzielni jest bezpłatne.</p></div></div><div><span>03</span><div><h3>Dbajmy o siebie nawzajem</h3><p>Nie zostawiaj produktów ze śladami zepsucia ani takich, co do których bezpieczeństwa masz wątpliwości.</p></div></div></div></div></section>

    <section class="home-quote" aria-label="Idea Foodsharingu"><div class="wrap"><span aria-hidden="true">“</span><blockquote>Jadłodzielnia jest dla <em>wszystkich.</em></blockquote><p>Możesz przynieść jedzenie. Możesz je zabrać. Możesz zrobić jedno i drugie.</p></div></section>

    <section class="section home-cities" id="miasta" aria-labelledby="cities-title"><div class="wrap"><div class="home-section-top"><span class="label">03 / Miasta</span><h2 id="cities-title">Foodsharing <em>blisko Ciebie.</em></h2><p>Zobacz lokalną stronę, sprawdź adresy jadłodzielni i dowiedz się, co dzieje się w Twojej okolicy.</p></div><div class="city-grid"><a class="city-card" href="<?php echo esc_url(home_url('/warszawa/')); ?>"><div class="city-card-art" aria-hidden="true"><span class="city-ring"></span><span class="city-art-text">WARSZAWA</span><span class="city-art-star">✳</span></div><div class="city-card-bottom"><div><span>Strona lokalna</span><h3>Warszawa</h3><p>Adresy jadłodzielni, zasady i warszawska społeczność.</p></div><span class="city-arrow" aria-hidden="true">↗</span></div></a><div class="city-invite"><span class="label">Twoja okolica</span><h3>Chcesz założyć jadłodzielnię?</h3><p>Jeśli myślisz o stworzeniu miejsca do dzielenia się jedzeniem, napisz do nas. Pomożemy Ci zacząć.</p><a href="mailto:nowajadlodzielnia@foodsharing.pl">nowajadlodzielnia@foodsharing.pl ↗</a></div></div></div></section>

    <section class="section home-news" id="aktualnosci" aria-labelledby="news-title"><div class="wrap"><div class="news-heading"><div><span class="label">04 / Z życia Foodsharingu</span><h2 id="news-title">Aktualności <em>i historie.</em></h2></div><a href="<?php echo esc_url($posts_url); ?>">Wszystkie wpisy ↗</a></div>
      <div class="news-grid">
      <?php
      $rules_post = get_page_by_path('regulamin', OBJECT, 'post');
      $news = new WP_Query([
          'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 3,
          'ignore_sticky_posts' => true, 'post__not_in' => $rules_post ? [$rules_post->ID] : [],
      ]);
      ?>
      <?php if ($news->have_posts()) : while ($news->have_posts()) : $news->the_post(); ?>
        <a class="news-card" href="<?php the_permalink(); ?>">
          <?php echo foodsharing_article_image(get_the_ID()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes markup. ?>
          <span class="news-tag"><?php $cats = get_the_category(); echo esc_html($cats ? $cats[0]->name : 'Aktualności'); ?></span>
          <h3><?php the_title(); ?></h3><p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 18)); ?></p><span class="news-read">Czytaj więcej ↗</span>
        </a>
      <?php endwhile; wp_reset_postdata(); else : ?>
        <p>Wkrótce pojawią się tu nowe wiadomości.</p>
      <?php endif; ?>
      </div></div></section>

    <section class="home-join" id="dolacz"><div class="wrap home-join-grid"><div><span class="label">Dołącz do ruchu</span><h2>Razem możemy<br><em>uratować więcej.</em></h2></div><div><p>Chcesz podzielić się jedzeniem, współpracować lub dowiedzieć się więcej? Zacznij od swojej lokalnej społeczności albo napisz do nas.</p><div class="home-join-actions"><a class="btn btn-light" href="<?php echo esc_url(home_url('/warszawa/')); ?>">Foodsharing Warszawa ↗</a><a class="join-email" href="mailto:polska@foodsharing.pl">polska@foodsharing.pl ↗</a></div></div></div></section>
  </main>
<?php get_footer(); ?>
