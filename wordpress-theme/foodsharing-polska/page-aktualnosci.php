<?php
/** Lista wpisów, gdy strona „Aktualności” nie jest ustawiona jako strona wpisów. */
if (!defined('ABSPATH')) { exit; }
get_header();
$paged = max(1, (int) get_query_var('paged'));
$news = new WP_Query([
    'post_type' => 'post', 'post_status' => 'publish', 'category_name' => 'aktualnosci',
    'posts_per_page' => (int) get_option('posts_per_page'), 'paged' => $paged,
]);
?>
<main id="main" class="content-page"><div class="wrap content-wrap">
  <header class="content-header"><h1>Aktualności</h1></header>
  <?php if ($news->have_posts()) : ?><div class="news-grid">
    <?php while ($news->have_posts()) : $news->the_post(); ?><a class="news-card" href="<?php the_permalink(); ?>">
      <?php echo foodsharing_article_image(get_the_ID()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes markup. ?>
      <span class="news-tag"><?php echo esc_html(get_the_date()); ?></span><h2><?php the_title(); ?></h2>
      <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 18)); ?></p><span class="news-read">Czytaj więcej ↗</span>
    </a><?php endwhile; wp_reset_postdata(); ?>
  </div><?php $pagination = paginate_links(['total' => $news->max_num_pages, 'current' => $paged]); if ($pagination) { echo wp_kses_post($pagination); } ?>
  <?php else : ?><p>Brak wpisów.</p><?php endif; ?>
</div></main>
<?php get_footer(); ?>
