<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$sk_kontakt = get_page_by_path( 'kontakt' );
$sk_kontakt_url = $sk_kontakt ? get_permalink( $sk_kontakt ) : home_url( '/kontakt/' );
$sk_cta_class   = 'sk-cta' . ( ! empty( $args['modifier'] ) ? ' sk-cta--' . sanitize_html_class( $args['modifier'] ) : '' );
?>
<a class="<?php echo esc_attr( $sk_cta_class ); ?>" href="<?php echo esc_url( $sk_kontakt_url ); ?>">
	<span class="sk-cta__title"><?php echo esc_html( get_theme_mod( 'sk_cta_title', 'Kostenlose Offerte' ) ); ?></span>
	<span class="sk-cta__subtitle"><?php echo esc_html( get_theme_mod( 'sk_cta_subtitle', 'in nur 2 Minuten' ) ); ?></span>
</a>
