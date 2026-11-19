<?php if (!defined('ABSPATH')) { exit; } get_header(); ?>
<main id="main" class="content-page"><div class="wrap content-wrap">
<?php while (have_posts()) : the_post(); ?>
  <nav class="breadcrumbs" aria-label="Ścieżka nawigacji"><a href="<?php echo esc_url(home_url('/')); ?>">Strona główna</a> <span aria-hidden="true">/</span> <span><?php the_title(); ?></span></nav>
  <article id="post-<?php the_ID(); ?>" <?php post_class('content-article'); ?>>
    <header class="content-header"><h1><?php the_title(); ?></h1></header>
    <div class="entry-content"><?php the_content(); ?></div>
  </article>
<?php endwhile; ?>
</div></main>
<?php get_footer(); ?>
