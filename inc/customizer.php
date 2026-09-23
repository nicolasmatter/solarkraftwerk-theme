<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sk_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'sk_contact', array(
		'title'    => 'Kontaktangaben',
		'priority' => 30,
	) );

	$wp_customize->add_section( 'sk_copy', array(
		'title'    => 'Seitentexte',
		'priority' => 31,
		'description' => 'Überschriften und Button-Texte, die auf mehreren Seiten erscheinen. Seitentitel, Hero-Untertitel und Hero-Bilder werden direkt auf der jeweiligen Seite (Titel, Auszug, Beitragsbild) bearbeitet.',
	) );

	$sections = array(
		'sk_contact' => array(
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
		),
		'sk_copy' => array(
			'sk_cta_title'                => array( 'label' => 'CTA-Button Titel', 'default' => 'Kostenlose Offerte' ),
			'sk_cta_subtitle'             => array( 'label' => 'CTA-Button Untertitel', 'default' => 'in nur 2 Minuten' ),
			'sk_home_products_heading'    => array( 'label' => 'Startseite: Überschrift Produkte', 'default' => 'Produkte und Leistungen' ),
			'sk_home_projects_heading'    => array( 'label' => 'Startseite: Überschrift Projekte', 'default' => 'Ausgewählte Projekte' ),
			'sk_referenzen_list_heading'  => array( 'label' => 'Referenzen: Überschrift Liste', 'default' => 'Projekte' ),
			'sk_team_heading'             => array( 'label' => 'Über Uns: Überschrift Team', 'default' => 'Unser Team' ),
			'sk_partner_heading'          => array( 'label' => 'Über Uns: Überschrift Partner', 'default' => 'Unsere Partner' ),
			'sk_kontakt_info_heading'     => array( 'label' => 'Kontakt: Überschrift', 'default' => 'Kontakt' ),
			'sk_kontakt_data_heading'     => array( 'label' => 'Kontakt: Überschrift Kontaktdaten', 'default' => 'Kontaktdaten' ),
			'sk_kontakt_form_heading'     => array( 'label' => 'Kontakt: Überschrift Formular', 'default' => 'Kontaktformular' ),
		),
	);

	foreach ( $sections as $section_id => $fields ) {
		foreach ( $fields as $id => $field ) {
			$wp_customize->add_setting( $id, array(
				'default'           => $field['default'],
				'sanitize_callback' => 'sanitize_text_field',
			) );
			$wp_customize->add_control( $id, array(
				'label'   => $field['label'],
				'section' => $section_id,
				'type'    => 'text',
			) );
		}
	}
}
add_action( 'customize_register', 'sk_customize_register' );
