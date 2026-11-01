<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * OpenStreetMap for the contact section, drawn with Leaflet. Editors set a plain
 * address in the Customizer; it is geocoded once via Nominatim and the coordinates
 * are kept, as Nominatim's usage policy asks (https://operations.osmfoundation.org/policies/nominatim/).
 */

/**
 * The address the map shows: the "Kartenadresse" field, else the contact address.
 */
function sk_map_address() {
	$address = trim( get_theme_mod( 'sk_map_address', '' ) );
	if ( '' === $address ) {
		$address = trim( get_theme_mod( 'sk_address_street', 'Musterstrasse 12' ) . ', ' . get_theme_mod( 'sk_address_city', '8000 Zürich' ), ', ' );
	}
	return $address;
}

/**
 * Coordinates for an address as array( lat, lon ), or null if it can't be found.
 * Pass $fetch = false to only use what is already stored.
 */
function sk_map_geocode( $address, $fetch = true ) {
	if ( '' === $address ) return null;

	$stored = get_option( 'sk_map_geocode', array() );
	if ( isset( $stored['address'] ) && $stored['address'] === $address ) {
		return $stored['coords'];
	}
	// Don't ask Nominatim again on every page view while an address can't be found or the service is down.
	$failed_key = 'sk_map_failed_' . md5( $address );
	if ( ! $fetch || get_transient( $failed_key ) ) return null;

	$response = wp_remote_get(
		add_query_arg( array(
			'q'               => $address,
			'format'          => 'jsonv2',
			'limit'           => 1,
			'accept-language' => 'de',
		), 'https://nominatim.openstreetmap.org/search' ),
		array(
			'timeout'    => 5,
			// Nominatim requires an identifying user agent.
			'user-agent' => 'Solarkraftwerk-Theme/' . SK_THEME_VERSION . ' (' . home_url( '/' ) . ')',
		)
	);
	$results = is_wp_error( $response ) ? null : json_decode( wp_remote_retrieve_body( $response ), true );

	if ( empty( $results[0]['lat'] ) || empty( $results[0]['lon'] ) ) {
		set_transient( $failed_key, 1, HOUR_IN_SECONDS );
		return null;
	}

	$coords = array( (float) $results[0]['lat'], (float) $results[0]['lon'] );
	update_option( 'sk_map_geocode', array( 'address' => $address, 'coords' => $coords ), false );
	return $coords;
}

/**
 * Where "Grössere Karte anzeigen" leads: the link set in the Customizer
 * (e.g. the Google Maps listing), else the spot on openstreetmap.org.
 */
function sk_map_link_url( $coords ) {
	$link = trim( get_theme_mod( 'sk_map_link', '' ) );
	if ( $link ) return $link;
	list( $lat, $lon ) = $coords;
	return sprintf( 'https://www.openstreetmap.org/?mlat=%1$s&mlon=%2$s#map=17/%1$s/%2$s', round( $lat, 6 ), round( $lon, 6 ) );
}

/**
 * The map's markup; assets/js/map.js draws it with Leaflet. Nothing if the address can't be found.
 */
function sk_map( $fetch = true ) {
	$coords = sk_map_geocode( sk_map_address(), $fetch );
	if ( ! $coords ) return;

	wp_enqueue_style( 'sk-leaflet', get_template_directory_uri() . '/assets/vendor/leaflet/leaflet.css', array(), '1.9.4' );
	wp_enqueue_script( 'sk-leaflet', get_template_directory_uri() . '/assets/vendor/leaflet/leaflet.js', array(), '1.9.4', true );
	wp_enqueue_script( 'sk-map', get_template_directory_uri() . '/assets/js/map.js', array( 'sk-leaflet' ), SK_THEME_VERSION, true );

	printf(
		'<div class="sk-map" data-lat="%s" data-lon="%s" role="region" aria-label="%s"></div>',
		esc_attr( $coords[0] ),
		esc_attr( $coords[1] ),
		esc_attr( 'Karte: ' . sk_map_address() )
	);
	printf(
		'<a class="sk-map__link" href="%s" target="_blank" rel="noopener">Grössere Karte anzeigen</a>',
		esc_url( sk_map_link_url( $coords ) )
	);
}

/**
 * Look the address up right after it is saved, so no visitor waits for it.
 */
function sk_map_geocode_on_save() {
	sk_map_geocode( sk_map_address() );
}
add_action( 'customize_save_after', 'sk_map_geocode_on_save' );
