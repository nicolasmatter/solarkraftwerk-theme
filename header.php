<?php if ( ! defined( 'ABSPATH' ) ) exit; ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="sk-header">
	<div class="sk-header__inner">
		<a class="sk-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo.png' ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="147" height="52">
			<?php endif; ?>
		</a>

		<div class="sk-header__contact">
			<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', get_theme_mod( 'sk_phone', '+41 234 43 21' ) ) ); ?>">Tel: <?php echo esc_html( get_theme_mod( 'sk_phone', '+41 234 43 21' ) ); ?></a>
			<a href="mailto:<?php echo esc_attr( get_theme_mod( 'sk_email', 'info@solarkraftwerk.ch' ) ); ?>"><?php echo esc_html( get_theme_mod( 'sk_email', 'info@solarkraftwerk.ch' ) ); ?></a>
		</div>

		<?php // Hidden until the visitor scrolls, then takes over from the hero's button (see main.js). ?>
		<?php get_template_part( 'template-parts/cta-button', null, array( 'modifier' => 'header' ) ); ?>

		<button class="sk-header__toggle" id="sk-nav-toggle" aria-expanded="false" aria-controls="sk-primary-menu">
			<span></span><span></span><span></span>
			<span class="sk-visually-hidden">Menü</span>
		</button>

		<nav class="sk-header__nav" id="sk-primary-menu">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'sk-nav',
				'fallback_cb'    => 'sk_default_menu',
			) );
			?>
		</nav>
	</div>
</header>

<?php
function sk_default_menu() {
	$items = array(
		home_url( '/' )              => 'Home',
		home_url( '/uber-uns/' )     => 'Über Uns',
		home_url( '/referenzen/' )   => 'Referenzen',
		home_url( '/kontakt/' )      => 'Kontakt',
	);
	echo '<ul class="sk-nav">';
	foreach ( $items as $url => $label ) {
		echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}
?>
