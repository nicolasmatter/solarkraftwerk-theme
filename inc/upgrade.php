<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * One-time data migrations, run on the first request after a theme update.
 */
function sk_maybe_upgrade() {
	$schema = (int) get_option( 'sk_theme_schema', 0 );
	if ( $schema >= 1 ) return;

	sk_migrate_page_heroes();
	// Projects lost their public URLs.
	flush_rewrite_rules( false );

	update_option( 'sk_theme_schema', 1 );
}
add_action( 'init', 'sk_maybe_upgrade', 20 );

/**
 * Heroes used to come from page templates (featured image + excerpt). Prepend an
 * equivalent sk/hero block to each such page so the hero becomes editable content.
 */
function sk_migrate_page_heroes() {
	$legacy = array(
		'template-ueber-uns.php'  => array( 'cta' => false, 'subtitle' => 'Seit über 12 Jahren realisieren wir Photovoltaikanlagen für Privathaushalte, Landwirtschaft und Gewerbe im Kanton Zürich – unkompliziert, transparent und aus einer Hand.' ),
		'template-produkte.php'   => array( 'cta' => true, 'subtitle' => 'Von der ersten Beratung bis zur schlüsselfertigen Installation – alles aus einer Hand.' ),
		'template-referenzen.php' => array( 'cta' => true, 'subtitle' => 'Von der ersten Beratung bis zur schlüsselfertigen Installation – alles aus einer Hand.' ),
		'template-kontakt.php'    => array( 'cta' => false, 'subtitle' => 'Wir freuen uns auf Ihre Anfrage – unverbindlich und kostenlos.' ),
	);
	$front_id = 'page' === get_option( 'show_on_front' ) ? (int) get_option( 'page_on_front' ) : 0;

	// Block comments must survive untouched even though no user is logged in here.
	$had_kses = false !== has_filter( 'content_save_pre', 'wp_filter_post_kses' );
	kses_remove_filters();

	foreach ( get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'numberposts' => -1 ) ) as $page ) {
		$template = get_page_template_slug( $page );
		$is_front = $page->ID === $front_id;
		if ( ( ! $is_front && ! isset( $legacy[ $template ] ) ) || has_block( 'sk/hero', $page ) ) continue;

		$attrs = array();
		if ( has_post_thumbnail( $page ) ) {
			$attrs['imageId'] = (int) get_post_thumbnail_id( $page );
		}
		if ( $is_front ) {
			$attrs['size'] = 'large';
		} else {
			$attrs['title']    = esc_html( $page->post_title );
			$attrs['subtitle'] = esc_html( $page->post_excerpt ? $page->post_excerpt : $legacy[ $template ]['subtitle'] );
			if ( ! $legacy[ $template ]['cta'] ) {
				$attrs['showCta'] = false;
			}
		}

		$hero = serialize_block( array(
			'blockName'    => 'sk/hero',
			'attrs'        => $attrs,
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		) );

		// wp_update_post() unslashes its input; slash it so the block JSON's escapes survive.
		wp_update_post( wp_slash( array(
			'ID'           => $page->ID,
			'post_content' => $hero . "\n\n" . $page->post_content,
		) ) );
		if ( $template ) {
			delete_post_meta( $page->ID, '_wp_page_template' );
		}
	}

	if ( $had_kses ) kses_init_filters();
}
