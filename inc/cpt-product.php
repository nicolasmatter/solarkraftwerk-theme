<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Product types, in the order the overview lists them.
 */
function sk_product_types() {
	return array(
		'product' => 'Produkt',
		'service' => 'Service',
	);
}

function sk_product_type( $post_id ) {
	$type = get_post_meta( $post_id, '_sk_product_type', true );
	return isset( sk_product_types()[ $type ] ) ? $type : 'product';
}

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
		// Products only appear in the product sections, never on pages of their own.
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'show_in_rest' => true,
		'rewrite'      => false,
		'menu_icon'    => 'dashicons-admin-tools',
		'supports'     => array( 'title', 'editor', 'page-attributes' ),
		// The editor content is the card's text: a short label per group, then its points.
		'template'     => array(
			array( 'core/heading', array( 'level' => 3, 'placeholder' => 'Gruppe (z. B. Module)' ) ),
			array( 'core/list' ),
		),
	) );
}
add_action( 'init', 'sk_register_product_cpt' );

/**
 * The card text only knows labels, paragraphs and lists.
 */
function sk_product_allowed_blocks( $allowed, $context ) {
	if ( isset( $context->post ) && 'product' === $context->post->post_type ) {
		return array( 'core/heading', 'core/paragraph', 'core/list', 'core/list-item' );
	}
	return $allowed;
}
add_filter( 'allowed_block_types_all', 'sk_product_allowed_blocks', 10, 2 );

function sk_product_meta_box() {
	add_meta_box( 'sk_product_details', 'Art', 'sk_render_product_meta_box', 'product', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'sk_product_meta_box' );

function sk_render_product_meta_box( $post ) {
	wp_nonce_field( 'sk_save_product_meta', 'sk_product_nonce' );
	$current = sk_product_type( $post->ID );
	foreach ( sk_product_types() as $value => $label ) {
		printf(
			'<p><label><input type="radio" name="_sk_product_type" value="%s"%s> %s</label></p>',
			esc_attr( $value ),
			checked( $current, $value, false ),
			esc_html( $label )
		);
	}
}

function sk_save_product_meta( $post_id ) {
	if ( ! isset( $_POST['sk_product_nonce'] ) || ! wp_verify_nonce( $_POST['sk_product_nonce'], 'sk_save_product_meta' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	$type = isset( $_POST['_sk_product_type'] ) ? sanitize_key( $_POST['_sk_product_type'] ) : '';
	if ( isset( sk_product_types()[ $type ] ) ) {
		update_post_meta( $post_id, '_sk_product_type', $type );
	}
}
add_action( 'save_post_product', 'sk_save_product_meta' );

/**
 * Products used to have pages under /produkte/<name>/. Send those links to the overview.
 */
function sk_redirect_old_product_urls() {
	if ( ! is_404() ) return;
	$path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
	if ( preg_match( '#(^|/)produkte/[^/]+$#', $path ) ) {
		wp_safe_redirect( sk_page_url( 'produkte' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'sk_redirect_old_product_urls' );
