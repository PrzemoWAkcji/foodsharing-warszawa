<?php if (!defined('ABSPATH')) { exit; } $warsaw = is_page('warszawa'); $privacy_page = get_page_by_path('polityka-prywatnosci'); $privacy_url = get_privacy_policy_url() ?: ($privacy_page ? get_permalink($privacy_page) : ''); ?>
<footer class="footer" id="kontakt"><div class="wrap footer-grid"><div>
  <a href="<?php echo esc_url(home_url('/')); ?>" class="brand" aria-label="Foodsharing Polska — strona główna"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/foodsharing-polska.png'); ?>" alt="Foodsharing Polska" width="184" height="63"></a>
  <p><?php echo $warsaw ? 'Dobre jedzenie lepiej smakuje, kiedy się nim dzielimy.' : 'Nie wyrzucaj. Podziel się.'; ?></p>
  <?php if (!$warsaw) : ?><div class="footer-social"><a href="https://www.facebook.com/FoodsharingPolska" target="_blank" rel="noopener noreferrer">Facebook ↗</a><a href="https://www.instagram.com/explore/tags/foodsharingpolska/" target="_blank" rel="noopener noreferrer">Instagram ↗</a></div><?php endif; ?>
</div><div class="contact"><h2>Kontakt</h2>
  <p>Sprawy ogólne<br><a href="mailto:polska@foodsharing.pl">polska@foodsharing.pl</a></p>
  <p>Media, warsztaty i edukacja<br><a href="mailto:media@foodsharing.pl">media@foodsharing.pl</a></p>
  <p>Pomoc w założeniu jadłodzielni<br><a href="mailto:nowajadlodzielnia@foodsharing.pl">nowajadlodzielnia@foodsharing.pl</a></p>
  <?php if ($privacy_url) : ?><p><a href="<?php echo esc_url($privacy_url); ?>">Polityka prywatności</a></p><?php endif; ?>
</div></div><div class="wrap footer-bottom"><span><?php echo $warsaw ? 'Foodsharing Warszawa' : 'Foodsharing Polska'; ?></span><span class="footer-credit">Projekt i wykonanie: <a href="https://it-timing.eu/" target="_blank" rel="noopener noreferrer">IT TIMING</a></span><a href="#top">Wróć na górę ↑</a></div></footer>
<?php wp_footer(); ?>
</body>
</html>
