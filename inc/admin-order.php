<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Drag-and-drop ordering in the wp-admin lists of products, projects and people.
 * It sets the same "Reihenfolge" (menu_order) the sections on the site sort by.
 */

function sk_sortable_post_types() {
	return array( 'product', 'project', 'team_member' );
}

/**
 * Only the plain list can be sorted: not while it is sorted by a column, searched or filtered.
 */
function sk_order_list_is_sortable( $screen ) {
	if ( ! $screen || 'edit' !== $screen->base || ! in_array( $screen->post_type, sk_sortable_post_types(), true ) ) return false;
	foreach ( array( 'orderby', 's', 'm', 'post_status', 'author' ) as $arg ) {
		if ( ! empty( $_GET[ $arg ] ) && 'all' !== $_GET[ $arg ] ) return false;
	}
	return true;
}

function sk_order_admin_assets() {
	if ( ! sk_order_list_is_sortable( get_current_screen() ) ) return;

	$screen   = get_current_screen();
	$per_page = (int) get_user_option( "edit_{$screen->post_type}_per_page" );
	$paged    = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
	wp_enqueue_script( 'sk-admin-order', get_template_directory_uri() . '/assets/js/admin-order.js', array( 'jquery', 'jquery-ui-sortable' ), SK_THEME_VERSION, true );
	wp_localize_script( 'sk-admin-order', 'skOrder', array(
		'nonce'  => wp_create_nonce( 'sk_save_order' ),
		'offset' => ( $paged - 1 ) * ( $per_page ? $per_page : 20 ),
		'error'  => 'Die Reihenfolge konnte nicht gespeichert werden. Bitte Seite neu laden.',
	) );
	wp_register_style( 'sk-admin-order', false, array(), SK_THEME_VERSION );
	wp_enqueue_style( 'sk-admin-order' );
	wp_add_inline_style( 'sk-admin-order', '
		.sk-sortable #the-list tr:not(.inline-editor) { cursor: grab; }
		.sk-sortable #the-list tr.ui-sortable-helper { cursor: grabbing; background: #fff; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); }
		.sk-sortable #the-list tr.sk-order-placeholder { visibility: visible !important; background: #f0f6fc; }
		.sk-sortable.is-saving #the-list { opacity: 0.6; pointer-events: none; }
	' );
}
add_action( 'admin_enqueue_scripts', 'sk_order_admin_assets' );

function sk_order_notice() {
	$screen = get_current_screen();
	if ( $screen && 'edit' === $screen->base && in_array( $screen->post_type, sk_sortable_post_types(), true ) ) {
		$text = sk_order_list_is_sortable( $screen )
			? 'Reihenfolge ändern: Zeilen mit der Maus an die gewünschte Stelle ziehen. Die Website übernimmt sie sofort.'
			: 'Zum Sortieren per Drag-and-drop die Suche, Filter und Spaltensortierung aufheben (Menüpunkt erneut anklicken).';
		printf( '<div class="notice notice-info"><p>%s</p></div>', esc_html( $text ) );
	}
}
add_action( 'admin_notices', 'sk_order_notice' );

/**
 * Save a new order for the rows on the current list page.
 */
function sk_order_save() {
	check_ajax_referer( 'sk_save_order', 'nonce' );

	$ids = isset( $_POST['ids'] ) ? array_values( array_filter( array_map( 'absint', (array) $_POST['ids'] ) ) ) : array();
	$offset = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
	if ( ! $ids ) wp_send_json_error();

	foreach ( $ids as $id ) {
		$post = get_post( $id );
		if ( ! $post || ! in_array( $post->post_type, sk_sortable_post_types(), true ) || ! current_user_can( 'edit_post', $id ) ) {
			wp_send_json_error();
		}
	}

	// Number from the list page's start, so rows on later pages stay after these.
	// Only the order changes, so skip wp_update_post() and its save hooks and revisions.
	global $wpdb;
	foreach ( $ids as $i => $id ) {
		if ( (int) get_post_field( 'menu_order', $id ) !== $offset + $i ) {
			$wpdb->update( $wpdb->posts, array( 'menu_order' => $offset + $i ), array( 'ID' => $id ) );
			clean_post_cache( $id );
		}
	}
	wp_send_json_success();
}
add_action( 'wp_ajax_sk_save_order', 'sk_order_save' );
