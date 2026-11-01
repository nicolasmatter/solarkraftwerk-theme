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

/**
 * Background colors for a product's title bar. Without a choice it follows the type.
 */
function sk_product_colors() {
	return array(
		'orange' => 'Orange',
		'dark'   => 'Grau',
	);
}

function sk_product_color( $post_id ) {
	$color = get_post_meta( $post_id, '_sk_product_color', true );
	if ( isset( sk_product_colors()[ $color ] ) ) return $color;
	return 'service' === sk_product_type( $post_id ) ? 'dark' : 'orange';
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
		// Edited like projects: a title and one text field, no block editor.
		'supports'     => array( 'title', 'page-attributes' ),
	) );
}
add_action( 'init', 'sk_register_product_cpt' );

function sk_product_meta_box() {
	add_meta_box( 'sk_product_text', 'Text', 'sk_render_product_text_box', 'product', 'normal', 'high' );
	add_meta_box( 'sk_product_details', 'Art und Farbe', 'sk_render_product_meta_box', 'product', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'sk_product_meta_box' );

/**
 * The card's text, kept as the post content: short labels ("Zwischentitel") and their points.
 * The field is named "content", so WordPress saves it like a normal editor.
 */
function sk_render_product_text_box( $post ) {
	wp_editor( $post->post_content, 'skproducttext', array(
		'textarea_name' => 'content',
		'textarea_rows' => 14,
		'media_buttons' => false,
		'teeny'         => true,
		'quicktags'     => false,
		'tinymce'       => array(
			'toolbar1'      => 'formatselect,bold,italic,underline,bullist,numlist,link,unlink,undo,redo',
			'block_formats' => 'Absatz=p;Zwischentitel=h3',
			'content_style' => 'body { margin: 12px 16px; } h3 { font-size: 1.15em; margin: 1em 0 0.25em; } ul { list-style: disc; } ul, ol { padding-left: 1.5em; }',
		),
	) );
}

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

	$color = get_post_meta( $post->ID, '_sk_product_color', true );
	echo '<p><strong>Titel-Hintergrund</strong></p>';
	foreach ( array( '' => 'Automatisch (Produkt: Orange, Service: Grau)' ) + sk_product_colors() as $value => $label ) {
		printf(
			'<p><label><input type="radio" name="_sk_product_color" value="%s"%s> %s</label></p>',
			esc_attr( $value ),
			checked( isset( sk_product_colors()[ $color ] ) ? $color : '', $value, false ),
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

	$color = isset( $_POST['_sk_product_color'] ) ? sanitize_key( $_POST['_sk_product_color'] ) : '';
	if ( isset( sk_product_colors()[ $color ] ) ) {
		update_post_meta( $post_id, '_sk_product_color', $color );
	} else {
		delete_post_meta( $post_id, '_sk_product_color' );
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
