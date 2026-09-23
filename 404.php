<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<section class="sk-section">
	<div class="sk-container sk-container--narrow">
		<h1>Seite nicht gefunden</h1>
		<p>Die gesuchte Seite existiert nicht. <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Zurück zur Startseite</a></p>
	</div>
</section>
<?php get_footer(); ?>
