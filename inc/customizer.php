<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sk_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'sk_contact', array(
		'title'    => 'Kontaktangaben',
		'priority' => 30,
	) );

	$wp_customize->add_section( 'sk_copy', array(
		'title'       => 'CTA-Button',
		'priority'    => 31,
		'description' => 'Text und Link des Angebots-Buttons, der auf jeder Seite erscheint. Überschriften der einzelnen Seitenabschnitte werden direkt im jeweiligen Block bearbeitet.',
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
			'sk_linkedin_url'   => array( 'label' => 'LinkedIn URL (leer = ausblenden)', 'default' => '' ),
			'sk_facebook_url'   => array( 'label' => 'Facebook URL (leer = ausblenden)', 'default' => '' ),
			'sk_map_address'    => array( 'label' => 'Kartenadresse (leer = Strasse und Ort von oben)', 'default' => '' ),
			'sk_map_link'       => array( 'label' => 'Link „Grössere Karte anzeigen“, z. B. Google Maps (leer = OpenStreetMap)', 'default' => '' ),
		),
		'sk_copy' => array(
			'sk_cta_title'    => array( 'label' => 'CTA-Button Titel', 'default' => 'Kostenlose Offerte' ),
			'sk_cta_subtitle' => array( 'label' => 'CTA-Button Untertitel', 'default' => 'in nur 2 Minuten' ),
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

	$wp_customize->add_setting( 'sk_cta_url', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'sk_cta_url', array(
		'label'       => 'CTA-Button Link',
		'description' => 'Leer = Kontaktseite. Volle URL (https://…) oder Pfad (/offerte/).',
		'section'     => 'sk_copy',
		'type'        => 'text',
	) );

	$wp_customize->add_section( 'sk_colors', array(
		'title'       => 'Farben',
		'priority'    => 32,
		'description' => 'Die Farben der Website. Sie stehen auch im Block-Editor zur Auswahl.',
	) );
	foreach ( sk_colors() as $slug => $color ) {
		$wp_customize->add_setting( "sk_color_{$slug}", array(
			'default'           => $color['default'],
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "sk_color_{$slug}", array(
			'label'       => $color['name'],
			'description' => $color['use'],
			'section'     => 'sk_colors',
		) ) );
	}
}
add_action( 'customize_register', 'sk_customize_register' );

/**
 * The site's palette, in the order of theme.json. Defaults match theme.json.
 */
function sk_colors() {
	return array(
		'dark'   => array( 'name' => 'Anthrazit', 'default' => '#383e42', 'use' => 'Text, Kopf- und Fusszeile, Kartenbalken, graue Produkt-Titel' ),
		'orange' => array( 'name' => 'Orange', 'default' => '#ff6a00', 'use' => 'Akzente: Offerte-Button, Hover, Kartenmarker, orange Produkt-Titel' ),
		'panel'  => array( 'name' => 'Hellgrau', 'default' => '#eef2f2', 'use' => 'Hintergrund von Flächen und Karten' ),
		'white'  => array( 'name' => 'Weiss', 'default' => '#ffffff', 'use' => 'Seitenhintergrund, Text auf dunklen Flächen' ),
	);
}

/**
 * Feed the Customizer colors into the theme.json palette, so the CSS variables
 * (--wp--preset--color--*) and the editor's color picker both use them.
 */
function sk_theme_json_colors( $theme_json ) {
	$palette = array();
	foreach ( sk_colors() as $slug => $color ) {
		$value     = get_theme_mod( "sk_color_{$slug}", $color['default'] );
		$palette[] = array(
			'slug'  => $slug,
			'name'  => $color['name'],
			'color' => $value ? $value : $color['default'],
		);
	}
	return $theme_json->update_with( array(
		'version'  => 2,
		'settings' => array( 'color' => array( 'palette' => $palette ) ),
	) );
}
add_filter( 'wp_theme_json_data_theme', 'sk_theme_json_colors' );

// The generated styles are cached; rebuild them once new colors are saved.
add_action( 'customize_save_after', 'wp_clean_theme_json_cache' );
