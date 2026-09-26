<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sk_register_project_cpt() {
	register_post_type( 'project', array(
		'labels' => array(
			'name'               => 'Projekte',
			'singular_name'      => 'Projekt',
			'add_new_item'       => 'Neues Projekt hinzufügen',
			'edit_item'          => 'Projekt bearbeiten',
			'all_items'          => 'Alle Projekte',
			'menu_name'          => 'Referenzen',
		),
		// Projects only appear in the project sections, never on pages of their own.
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'show_in_rest' => true,
		'rewrite'      => false,
		'menu_icon'    => 'dashicons-admin-home',
		'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
	) );
}
add_action( 'init', 'sk_register_project_cpt' );

function sk_project_meta_box() {
	add_meta_box( 'sk_project_details', 'Projektdetails', 'sk_render_project_meta_box', 'project', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'sk_project_meta_box' );

function sk_render_project_meta_box( $post ) {
	wp_nonce_field( 'sk_save_project_meta', 'sk_project_nonce' );
	sk_meta_field( $post, '_sk_subtitle', 'Untertitel (z. B. Schrägdach - Novotegra Einlegesystem)' );
	sk_meta_field( $post, '_sk_power_kwp', 'Leistung (z. B. 17.86 kWp)' );
	sk_meta_field( $post, '_sk_year', 'Umsetzungsjahr' );
	sk_meta_field( $post, '_sk_modules', 'Anzahl Module' );
}

function sk_save_project_meta( $post_id ) {
	if ( ! isset( $_POST['sk_project_nonce'] ) || ! wp_verify_nonce( $_POST['sk_project_nonce'], 'sk_save_project_meta' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	foreach ( array( '_sk_subtitle', '_sk_power_kwp', '_sk_year', '_sk_modules' ) as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}
}
add_action( 'save_post_project', 'sk_save_project_meta' );
