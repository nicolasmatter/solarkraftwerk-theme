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
