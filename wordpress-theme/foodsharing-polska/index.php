<?php if (!defined('ABSPATH')) { exit; } get_header(); ?>
<main id="main" class="content-page"><div class="wrap content-wrap">
  <header class="content-header"><h1><?php echo is_home() ? 'Aktualności' : esc_html(get_the_archive_title()); ?></h1></header>
  <?php if (have_posts()) : ?><div class="news-grid">
    <?php while (have_posts()) : the_post(); ?><a class="news-card" href="<?php the_permalink(); ?>">
      <?php echo foodsharing_article_image(get_the_ID()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes markup. ?>
      <span class="news-tag"><?php echo esc_html(get_the_date()); ?></span><h2><?php the_title(); ?></h2>
      <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 18)); ?></p><span class="news-read">Czytaj więcej ↗</span>
    </a><?php endwhile; ?>
  </div><?php the_posts_pagination(['mid_size' => 1, 'prev_text' => '← Poprzednie', 'next_text' => 'Następne →']); ?>
  <?php else : ?><p>Brak wpisów.</p><?php endif; ?>
</div></main>
<?php get_footer(); ?>
