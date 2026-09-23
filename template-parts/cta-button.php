<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$sk_kontakt = get_page_by_path( 'kontakt' );
$sk_kontakt_url = $sk_kontakt ? get_permalink( $sk_kontakt ) : home_url( '/kontakt/' );
?>
<a class="sk-cta" href="<?php echo esc_url( $sk_kontakt_url ); ?>">
	<span class="sk-cta__title">Kostenlose Offerte</span>
	<span class="sk-cta__subtitle">in nur 2 Minuten</span>
</a>
