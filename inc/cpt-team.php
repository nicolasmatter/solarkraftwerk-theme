<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sk_register_team_cpt() {
	register_post_type( 'team_member', array(
		'labels' => array(
			'name'          => 'Team & Partner',
			'singular_name' => 'Teammitglied',
			'add_new_item'  => 'Neue Person hinzufügen',
			'edit_item'     => 'Person bearbeiten',
			'all_items'     => 'Team & Partner',
			'menu_name'     => 'Über Uns',
		),
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => array( 'slug' => 'team' ),
		'menu_icon'    => 'dashicons-groups',
		'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'sk_register_team_cpt' );

function sk_team_meta_box() {
	add_meta_box( 'sk_team_details', 'Personendetails', 'sk_render_team_meta_box', 'team_member', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'sk_team_meta_box' );

function sk_render_team_meta_box( $post ) {
	wp_nonce_field( 'sk_save_team_meta', 'sk_team_nonce' );
	sk_meta_field( $post, '_sk_role', 'Rolle (z. B. Inhaber & Geschäftsführer – 15 Jahre Erfahrung)' );
	sk_meta_field( $post, '_sk_phone', 'Telefon' );
	sk_meta_field( $post, '_sk_email', 'E-Mail' );

	$group = get_post_meta( $post->ID, '_sk_group', true );
	if ( ! $group ) $group = 'team';
	echo '<p><label><strong>Gruppe</strong></label><br>';
	echo '<select name="_sk_group">';
	echo '<option value="team"' . selected( $group, 'team', false ) . '>Unser Team</option>';
	echo '<option value="partner"' . selected( $group, 'partner', false ) . '>Unsere Partner</option>';
	echo '</select></p>';
}

function sk_save_team_meta( $post_id ) {
	if ( ! isset( $_POST['sk_team_nonce'] ) || ! wp_verify_nonce( $_POST['sk_team_nonce'], 'sk_save_team_meta' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	foreach ( array( '_sk_role', '_sk_phone', '_sk_email' ) as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}
	if ( isset( $_POST['_sk_group'] ) ) {
		update_post_meta( $post_id, '_sk_group', sanitize_key( $_POST['_sk_group'] ) );
	}
}
add_action( 'save_post_team_member', 'sk_save_team_meta' );
