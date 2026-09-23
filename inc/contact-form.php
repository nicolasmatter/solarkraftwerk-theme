<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sk_handle_contact_form() {
	if ( ! isset( $_POST['sk_contact_nonce'] ) || ! wp_verify_nonce( $_POST['sk_contact_nonce'], 'sk_contact_form' ) ) {
		wp_die( 'Sicherheitsprüfung fehlgeschlagen.' );
	}

	// Honeypot.
	if ( ! empty( $_POST['website'] ) ) {
		wp_safe_redirect( add_query_arg( 'sk_contact', 'ok', wp_get_referer() ) );
		exit;
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	$status = 'ok';
	if ( empty( $name ) || ! is_email( $email ) || empty( $message ) ) {
		$status = 'error';
	} else {
		$body  = "Name: {$name}\nE-Mail: {$email}\nTelefon: {$phone}\n\nNachricht:\n{$message}";
		$sent  = wp_mail( get_option( 'admin_email' ), 'Neue Kontaktanfrage – Solarkraftwerk', $body, array( 'Reply-To: ' . $email ) );
		$status = $sent ? 'ok' : 'error';
	}

	wp_safe_redirect( add_query_arg( 'sk_contact', $status, wp_get_referer() ) );
	exit;
}
add_action( 'admin_post_nopriv_sk_contact_form', 'sk_handle_contact_form' );
add_action( 'admin_post_sk_contact_form', 'sk_handle_contact_form' );
