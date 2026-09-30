<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sk_register_product_cpt() {
	register_post_type( 'product', array(
		'labels' => array(
			'name'          => 'Produkte',
			'singular_name' => 'Produkt',
			'add_new_item'  => 'Neues Produkt hinzufügen',
			'edit_item'     => 'Produkt bearbeiten',
			'all_items'     => 'Alle Produkte',
			'menu_name'     => 'Produkte',
		),
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => array( 'slug' => 'produkte' ),
		'menu_icon'    => 'dashicons-admin-tools',
		'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		'show_in_rest' => true,
		// The editor content is the detail page's "Das ist enthalten" part.
		'template'     => array(
			array( 'core/heading', array( 'content' => 'Das ist enthalten' ) ),
			array( 'core/paragraph', array( 'placeholder' => 'Beschreibung des Produkts …' ) ),
			array( 'core/list' ),
		),
	) );

	register_taxonomy( 'product_group', 'product', array(
		'labels' => array(
			'name'          => 'Produktgruppen',
			'singular_name' => 'Produktgruppe',
		),
		'hierarchical'      => true,
		'public'            => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'produktgruppe' ),
		// Products without a group land here; editors can rename it under Produktgruppen.
		'default_term'      => array( 'name' => 'Basic', 'slug' => 'basic' ),
	) );
}
add_action( 'init', 'sk_register_product_cpt' );

function sk_product_meta_box() {
	add_meta_box( 'sk_product_details', 'Produktdetails', 'sk_render_product_meta_box', 'product', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'sk_product_meta_box' );

function sk_render_product_meta_box( $post ) {
	wp_nonce_field( 'sk_save_product_meta', 'sk_product_nonce' );
	sk_image_field( $post, '_sk_hero_image', 'Titelbild im Seitenkopf (leer = Standardbild). Das Produktbild ist das Beitragsbild.' );
	sk_meta_field( $post, '_sk_intro', 'Intro im Seitenkopf (z. B. „Die solide Photovoltaik-Grundausstattung für Ihr Einfamilienhaus.“)', 'textarea' );
	echo '<h4>Technische Daten</h4>';
	sk_meta_field( $post, '_sk_subtitle', 'Untertitel (z. B. Schrägdach - Novotegra Einlegesystem)' );
	sk_meta_field( $post, '_sk_power_kwp', 'Leistung (z. B. 17.86 kWp)' );
	sk_meta_field( $post, '_sk_warranty', 'Garantie (z. B. 25 Jahre)' );
	sk_meta_field( $post, '_sk_modules', 'Anzahl Module' );
}

function sk_save_product_meta( $post_id ) {
	if ( ! isset( $_POST['sk_product_nonce'] ) || ! wp_verify_nonce( $_POST['sk_product_nonce'], 'sk_save_product_meta' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	foreach ( array( '_sk_subtitle', '_sk_power_kwp', '_sk_warranty', '_sk_modules' ) as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}
	if ( isset( $_POST['_sk_intro'] ) ) {
		update_post_meta( $post_id, '_sk_intro', sanitize_textarea_field( wp_unslash( $_POST['_sk_intro'] ) ) );
	}
	if ( isset( $_POST['_sk_hero_image'] ) ) {
		update_post_meta( $post_id, '_sk_hero_image', absint( $_POST['_sk_hero_image'] ) );
	}
}
add_action( 'save_post_product', 'sk_save_product_meta' );

/**
 * The "Technische Daten" list shown on the detail page and the "Weitere Produkte" cards.
 */
function sk_product_specs( $post_id ) {
	$subtitle = get_post_meta( $post_id, '_sk_subtitle', true );
	$rows     = array(
		'Leistung:'      => get_post_meta( $post_id, '_sk_power_kwp', true ),
		'Garantie:'      => get_post_meta( $post_id, '_sk_warranty', true ),
		'Anzahl Module:' => get_post_meta( $post_id, '_sk_modules', true ),
	);
	if ( ! $subtitle && ! array_filter( $rows ) ) return;

	echo '<div class="sk-specs">';
	if ( $subtitle ) {
		echo '<p class="sk-specs__subtitle">' . esc_html( $subtitle ) . '</p>';
	}
	echo '<dl>';
	foreach ( $rows as $label => $value ) {
		if ( '' === $value ) continue;
		echo '<div><dt>' . esc_html( $label ) . '</dt><dd>' . esc_html( $value ) . '</dd></div>';
	}
	echo '</dl></div>';
}
