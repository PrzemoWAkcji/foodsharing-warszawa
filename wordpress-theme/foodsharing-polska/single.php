<?php if (!defined('ABSPATH')) { exit; } get_header(); ?>
<main id="main" class="content-page"><div class="wrap content-wrap">
<?php while (have_posts()) : the_post(); ?>
  <nav class="breadcrumbs" aria-label="Ścieżka nawigacji"><a href="<?php echo esc_url(home_url('/')); ?>">Strona główna</a> <span aria-hidden="true">/</span> <span><?php the_title(); ?></span></nav>
  <article id="post-<?php the_ID(); ?>" <?php post_class('content-article'); ?>>
    <header class="content-header"><p class="content-meta"><?php echo esc_html(get_the_date()); ?></p><h1><?php the_title(); ?></h1></header>
    <?php if (has_post_thumbnail()) : ?><figure class="content-featured"><?php the_post_thumbnail('large', ['loading' => 'eager']); ?></figure><?php endif; ?>
    <div class="entry-content"><?php the_content(); ?></div>
  </article>
  <nav class="post-navigation" aria-label="Inne wpisy"><?php previous_post_link('%link', '← %title'); ?><?php next_post_link('%link', '%title →'); ?></nav>
<?php endwhile; ?>
</div></main>
<?php get_footer(); ?>
