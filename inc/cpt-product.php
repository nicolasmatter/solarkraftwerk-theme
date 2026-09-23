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
		'supports'     => array( 'title', 'editor', 'thumbnail' ),
		'show_in_rest' => true,
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
	) );
}
add_action( 'init', 'sk_register_product_cpt' );

function sk_product_default_group( $post_id, $post, $update ) {
	if ( $update || wp_is_post_revision( $post_id ) ) return;
	if ( ! has_term( '', 'product_group', $post_id ) ) {
		wp_set_object_terms( $post_id, 'Basic', 'product_group' );
	}
}
add_action( 'save_post_product', 'sk_product_default_group', 10, 3 );
