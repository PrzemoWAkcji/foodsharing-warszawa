<?php if (!defined('ABSPATH')) { exit; } $warsaw = is_page('warszawa'); ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#faf8f1">
  <link rel="icon" href="<?php echo esc_url(get_template_directory_uri() . '/assets/foodsharing-polska.png'); ?>" type="image/png">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#main">Przejdź do treści</a>
<header class="header" id="top"><div class="wrap header-inner">
  <a href="<?php echo esc_url(home_url('/')); ?>" class="brand" aria-label="Foodsharing Polska — strona główna"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/foodsharing-polska.png'); ?>" alt="Foodsharing Polska" width="184" height="63"></a>
  <button class="menu-toggle" type="button" aria-label="Otwórz menu" aria-controls="main-nav" aria-expanded="false"><span></span><span></span><span></span></button>
  <nav class="nav" id="main-nav" aria-label="Menu główne">
    <?php foodsharing_menus($warsaw ? 'warszawa' : 'home'); ?>
    <?php if ($warsaw) : ?><a class="nav-social" href="https://www.facebook.com/FoodsharingWarszawa" target="_blank" rel="noopener noreferrer">Bądź na bieżąco ↗</a><?php else : ?><a class="nav-social" href="<?php echo esc_url(home_url('/#dolacz')); ?>">Dołącz do nas ↗</a><?php endif; ?>
  </nav>
</div></header>
