<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sk_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'sk_contact', array(
		'title'    => 'Kontaktangaben',
		'priority' => 30,
	) );

	$fields = array(
		'sk_phone'          => array( 'label' => 'Telefon', 'default' => '+41 234 43 21' ),
		'sk_email'          => array( 'label' => 'E-Mail', 'default' => 'info@solarkraftwerk.ch' ),
		'sk_address_street' => array( 'label' => 'Strasse & Nr.', 'default' => 'Musterstrasse 12' ),
		'sk_address_city'   => array( 'label' => 'PLZ & Ort', 'default' => '8000 Zürich' ),
		'sk_address_canton' => array( 'label' => 'Kanton', 'default' => 'Zürich' ),
		'sk_hours'          => array( 'label' => 'Öffnungszeiten', 'default' => 'Mo–Fr 08:00–17:00 Uhr' ),
		'sk_company_legal'  => array( 'label' => 'Firma (für Copyright)', 'default' => 'Netzwerk Nagel GmbH, Solarkraftwerk' ),
		'sk_linkedin_url'   => array( 'label' => 'LinkedIn URL', 'default' => 'https://linkedin.com' ),
		'sk_facebook_url'   => array( 'label' => 'Facebook URL', 'default' => 'https://facebook.com' ),
		'sk_maps_embed'     => array( 'label' => 'Google Maps Embed-URL (optional)', 'default' => '' ),
	);

	foreach ( $fields as $id => $field ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $field['default'],
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $field['label'],
			'section' => 'sk_contact',
			'type'    => 'text',
		) );
	}
}
add_action( 'customize_register', 'sk_customize_register' );
