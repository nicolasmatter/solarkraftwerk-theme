<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<footer class="sk-footer">
	<div class="sk-footer__overlay">
		<div class="sk-footer__inner">
			<div class="sk-footer__top">
				<address class="sk-footer__address">
					<?php echo esc_html( get_theme_mod( 'sk_address_street', 'Musterstrasse 12' ) ); ?><br>
					<?php echo esc_html( get_theme_mod( 'sk_address_city', '8000 Zürich' ) ); ?><br>
					<?php echo esc_html( get_theme_mod( 'sk_address_canton', 'Zürich' ) ); ?>
				</address>
				<nav class="sk-footer__legal">
					<a href="<?php echo esc_url( home_url( '/datenschutzbestimmungen/' ) ); ?>">Datenschutzbestimmungen</a>
					<a href="<?php echo esc_url( home_url( '/impressum/' ) ); ?>">Impressum</a>
				</nav>
			</div>

			<div class="sk-footer__bottom">
				<div class="sk-footer__social">
					<a href="<?php echo esc_url( get_theme_mod( 'sk_linkedin_url', 'https://linkedin.com' ) ); ?>" aria-label="LinkedIn" target="_blank" rel="noopener">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.86 0-2.14 1.45-2.14 2.94v5.67H9.34V9h3.41v1.56h.05c.48-.9 1.63-1.85 3.36-1.85 3.6 0 4.27 2.37 4.27 5.45v6.29zM5.34 7.43a2.07 2.07 0 1 1 0-4.13 2.07 2.07 0 0 1 0 4.13zM7.12 20.45H3.56V9h3.56v11.45z"/></svg>
					</a>
					<a href="<?php echo esc_url( get_theme_mod( 'sk_facebook_url', 'https://facebook.com' ) ); ?>" aria-label="Facebook" target="_blank" rel="noopener">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12z"/></svg>
					</a>
				</div>
				<p class="sk-footer__copy">© <?php echo esc_html( date( 'Y' ) ); ?> <?php echo esc_html( get_theme_mod( 'sk_company_legal', 'Netzwerk Nagel GmbH, Solarkraftwerk' ) ); ?></p>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
