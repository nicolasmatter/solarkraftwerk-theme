<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Search engine and sharing data, built from what editors already maintain
 * (Kontaktangaben, Öffnungszeiten, products, team, projects):
 * - JSON-LD (schema.org) for search engines and AI agents: the business, the site,
 *   the page and – where a page shows them – the offer catalog, the team and the projects.
 * - Meta description and Open Graph / Twitter tags for search results and link previews.
 *
 * With an SEO plugin (Yoast, Rank Math, …) that plugin owns the meta tags and the
 * site/page data; the theme then only adds the business and what the page shows.
 */

function sk_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

/* -------------------------------------------------------------------------
 * Opening hours
 * ---------------------------------------------------------------------- */

function sk_weekdays() {
	return array(
		'mo' => array( 'name' => 'Montag', 'schema' => 'Monday' ),
		'tu' => array( 'name' => 'Dienstag', 'schema' => 'Tuesday' ),
		'we' => array( 'name' => 'Mittwoch', 'schema' => 'Wednesday' ),
		'th' => array( 'name' => 'Donnerstag', 'schema' => 'Thursday' ),
		'fr' => array( 'name' => 'Freitag', 'schema' => 'Friday' ),
		'sa' => array( 'name' => 'Samstag', 'schema' => 'Saturday' ),
		'su' => array( 'name' => 'Sonntag', 'schema' => 'Sunday' ),
	);
}

/**
 * Time ranges in a text like "08:00–12:00, 13:30–17:00" as array( array( '08:00', '12:00' ), … ).
 */
function sk_parse_time_ranges( $text ) {
	preg_match_all( '/(\d{1,2})[:.](\d{2})\s*[-–—]\s*(\d{1,2})[:.](\d{2})/u', (string) $text, $m, PREG_SET_ORDER );
	$ranges = array();
	foreach ( $m as $r ) {
		if ( $r[1] > 24 || $r[3] > 24 || $r[2] > 59 || $r[4] > 59 ) continue;
		$ranges[] = array( sprintf( '%02d:%s', $r[1], $r[2] ), sprintf( '%02d:%s', $r[3], $r[4] ) );
	}
	return $ranges;
}

/**
 * Keeps only valid time ranges, written as "08:00-12:00, 13:30-17:00".
 */
function sk_sanitize_time_ranges( $value ) {
	return implode( ', ', array_map( function ( $r ) {
		return $r[0] . '-' . $r[1];
	}, sk_parse_time_ranges( $value ) ) );
}

/**
 * Opening hours per weekday key, e.g. array( 'mo' => array( array( '08:00', '17:00' ) ), … ).
 * From the Customizer fields; while none is filled in, read from the displayed
 * "Öffnungszeiten" text (e.g. "Mo–Fr 08:00–17:00 Uhr") as far as it can be understood.
 */
function sk_opening_hours() {
	$hours = array();
	foreach ( array_keys( sk_weekdays() ) as $day ) {
		$ranges = sk_parse_time_ranges( get_theme_mod( "sk_hours_{$day}", '' ) );
		if ( $ranges ) $hours[ $day ] = $ranges;
	}
	return $hours ? $hours : sk_parse_opening_hours_text( get_theme_mod( 'sk_hours', 'Mo–Fr 08:00–17:00 Uhr' ) );
}

function sk_parse_opening_hours_text( $text ) {
	$keys  = array_keys( sk_weekdays() );
	$codes = array( 'mo' => 0, 'di' => 1, 'mi' => 2, 'do' => 3, 'fr' => 4, 'sa' => 5, 'so' => 6 );
	// Only whole day names or their abbreviations, so "Mittag" or "Sommer" are no days.
	$day   = '\b(?:Mo(?:ntag)?|Di(?:enstag)?|Mi(?:ttwoch)?|Do(?:nnerstag)?|Fr(?:eitag)?|Sa(?:mstag)?|So(?:nntag)?)\b\.?';
	$range = '(?:\s*(?:[-–—]|bis)\s*)';
	$time  = '\d{1,2}[:.]\d{2}\s*[-–—]\s*\d{1,2}[:.]\d{2}';
	$hours = array();

	// "Mo–Fr 08:00–12:00, 13:30–17:00; Sa 09:00–12:00": day groups, each followed by its times.
	preg_match_all( "/((?:{$day}\s*(?:[-–—]|bis|,|und|&|\/)?\s*)+):?\s*((?:{$time}\s*(?:Uhr)?\s*(?:,|und|&|\/)?\s*)+)/iu", (string) $text, $groups, PREG_SET_ORDER );
	foreach ( $groups as $group ) {
		$ranges = sk_parse_time_ranges( $group[2] );
		preg_match_all( "/({$day})(?:{$range}({$day}))?/iu", $group[1], $spans, PREG_SET_ORDER );
		foreach ( $spans as $span ) {
			$from = $codes[ strtolower( substr( $span[1], 0, 2 ) ) ];
			$to   = isset( $span[2] ) && '' !== $span[2] ? $codes[ strtolower( substr( $span[2], 0, 2 ) ) ] : $from;
			for ( $i = $from; $i <= $to; $i++ ) {
				$hours[ $keys[ $i ] ] = array_merge( isset( $hours[ $keys[ $i ] ] ) ? $hours[ $keys[ $i ] ] : array(), $ranges );
			}
		}
	}
	return $hours;
}

/**
 * openingHoursSpecification entries, one per set of days with the same hours.
 */
function sk_opening_hours_schema() {
	$weekdays = sk_weekdays();
	$by_time  = array();
	foreach ( sk_opening_hours() as $day => $ranges ) {
		foreach ( $ranges as $range ) {
			$by_time[ implode( '-', $range ) ][] = 'https://schema.org/' . $weekdays[ $day ]['schema'];
		}
	}
	$specs = array();
	foreach ( $by_time as $range => $days ) {
		list( $opens, $closes ) = explode( '-', $range );
		$specs[] = array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => $days,
			'opens'     => $opens,
			'closes'    => $closes,
		);
	}
	return $specs;
}

/* -------------------------------------------------------------------------
 * Page data: blocks, description, image
 * ---------------------------------------------------------------------- */

/**
 * All blocks of a post, including nested ones.
 */
function sk_seo_blocks( $post ) {
	$found = array();
	$walk  = function ( $blocks ) use ( &$walk, &$found ) {
		foreach ( $blocks as $block ) {
			if ( ! empty( $block['blockName'] ) ) $found[] = $block;
			if ( ! empty( $block['innerBlocks'] ) ) $walk( $block['innerBlocks'] );
		}
	};
	$walk( parse_blocks( $post->post_content ) );
	return $found;
}

/**
 * The first block of a type on the page, or null.
 */
function sk_seo_find_block( $post, $name, $where = array() ) {
	foreach ( sk_seo_blocks( $post ) as $block ) {
		if ( $block['blockName'] !== $name ) continue;
		foreach ( $where as $attr => $value ) {
			$actual = isset( $block['attrs'][ $attr ] ) ? $block['attrs'][ $attr ] : null;
			if ( $actual !== $value ) continue 2;
		}
		return $block;
	}
	return null;
}

/**
 * The page the request shows: the queried page, or the static front page.
 */
function sk_seo_post() {
	if ( is_singular() ) return get_queried_object();
	if ( is_front_page() && 'page' === get_option( 'show_on_front' ) ) return get_post( get_option( 'page_on_front' ) );
	return null;
}

function sk_seo_text( $html, $length = 160 ) {
	$text = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( (string) $html ), ENT_QUOTES, 'UTF-8' ) ) );
	if ( mb_strlen( $text ) <= $length ) return $text;
	$cut = mb_substr( $text, 0, $length - 1 );
	$cut = preg_replace( '/\s+\S*$/u', '', $cut );
	return rtrim( $cut, ' ,;:–-' ) . '…';
}

/**
 * Text of structured content as sentences: "Module: Mittlere Leistung, Schwarzer Rahmen. Wechselrichter: …".
 */
function sk_seo_outline( $html, $length = 500 ) {
	preg_match_all( '#<(h[1-6]|li|p)\b[^>]*>(.*?)</\1>#is', (string) $html, $parts, PREG_SET_ORDER );
	if ( ! $parts ) return sk_seo_text( $html, $length );

	$text = '';
	$open = '';
	foreach ( $parts as $part ) {
		$value = sk_seo_text( $part[2], 1000 );
		if ( '' === $value ) continue;
		if ( 'li' === strtolower( $part[1] ) ) {
			$text .= ( 'list' === $open ? ', ' : '' ) . $value;
			$open  = 'list';
			continue;
		}
		$text .= ( $text ? '. ' : '' ) . rtrim( $value, '.:' );
		$open  = 'p' === strtolower( $part[1] ) ? '' : 'heading';
		if ( 'heading' === $open ) $text .= ': ';
	}
	$text = preg_replace( '/:\s*\./', '.', rtrim( $text, ' :' ) );
	return sk_seo_text( preg_replace( '/([^.!?])$/u', '$1.', $text ), $length );
}

/**
 * Meta description, never empty: the page's own field, else its hero subtitle, else its
 * text, else a summary of its sections, else the site's default description.
 */
function sk_seo_description( $post = null ) {
	if ( $post ) {
		$own = get_post_meta( $post->ID, '_sk_seo_description', true );
		if ( $own ) return sk_seo_text( $own );

		$hero = sk_seo_find_block( $post, 'sk/hero' );
		if ( $hero && ! empty( $hero['attrs']['subtitle'] ) ) return sk_seo_text( $hero['attrs']['subtitle'] );

		$text = sk_seo_text( preg_replace( '/<!--.*?-->/s', '', $post->post_content ) );
		if ( $text ) return $text;

		// The start page stands for the whole site, so it gets the site's description instead.
		$summary = (int) get_option( 'page_on_front' ) === $post->ID ? '' : sk_seo_section_summary( $post );
		if ( $summary ) return sk_seo_text( $summary );
	}
	return sk_seo_default_description();
}

/**
 * The site's description for pages without their own: "Standard-Beschreibung" in the
 * Customizer, else the tagline, else a general sentence about the business.
 */
function sk_seo_default_description() {
	foreach ( array( get_theme_mod( 'sk_seo_description', '' ), get_bloginfo( 'description' ) ) as $text ) {
		if ( trim( $text ) ) return sk_seo_text( $text );
	}
	$canton = trim( get_theme_mod( 'sk_address_canton', 'Zürich' ) );
	return sk_seo_text( sprintf(
		'%s: Photovoltaikanlagen und Batteriespeicher für Privathaushalte, Landwirtschaft und Gewerbe%s – von der Beratung bis zur Wartung.',
		get_bloginfo( 'name' ),
		$canton ? ' im Kanton ' . $canton : ''
	) );
}

/**
 * A description from what a page's sections list, for pages made of section blocks only.
 */
function sk_seo_section_summary( $post ) {
	$titles = function ( $posts ) {
		return implode( ', ', array_map( function ( $p ) {
			return wp_strip_all_tags( get_the_title( $p ) );
		}, $posts ) );
	};

	if ( sk_seo_find_block( $post, 'sk/products' ) ) {
		$by_type = array();
		foreach ( get_posts( sk_section_query_args( 'product' ) ) as $product ) {
			$by_type[ sk_product_type( $product->ID ) ][] = $product;
		}
		$parts = array();
		if ( ! empty( $by_type['product'] ) ) $parts[] = 'Photovoltaik-Pakete ' . $titles( $by_type['product'] );
		if ( ! empty( $by_type['service'] ) ) $parts[] = 'Services ' . $titles( $by_type['service'] );
		if ( $parts ) return sprintf( 'Produkte und Leistungen von %s: %s.', get_bloginfo( 'name' ), implode( '; ', $parts ) );
	}

	if ( sk_seo_find_block( $post, 'sk/projects' ) ) {
		$projects = get_posts( sk_section_query_args( 'project', array(), 6 ) );
		if ( $projects ) return sprintf( 'Referenzprojekte von %s: %s.', get_bloginfo( 'name' ), $titles( $projects ) );
	}

	if ( sk_seo_find_block( $post, 'sk/contact' ) ) {
		return sprintf(
			'Kontakt zu %s: %s, %s. Telefon %s, E-Mail %s. %s',
			get_bloginfo( 'name' ),
			get_theme_mod( 'sk_address_street', 'Musterstrasse 12' ),
			get_theme_mod( 'sk_address_city', '8000 Zürich' ),
			get_theme_mod( 'sk_phone', '+41 234 43 21' ),
			get_theme_mod( 'sk_email', 'info@solarkraftwerk.ch' ),
			get_theme_mod( 'sk_hours', 'Mo–Fr 08:00–17:00 Uhr' )
		);
	}

	if ( sk_seo_find_block( $post, 'sk/team' ) ) {
		$people = get_posts( array( 'post_type' => 'team_member', 'numberposts' => 6, 'meta_key' => '_sk_group', 'meta_value' => 'team', 'orderby' => array( 'menu_order' => 'ASC' ) ) );
		if ( $people ) return sprintf( 'Das Team von %s: %s.', get_bloginfo( 'name' ), $titles( $people ) );
	}
	return '';
}

/**
 * Share image as array( url, width, height ): the page's hero, else its featured image,
 * else the front page's hero, else the theme's default hero.
 */
function sk_seo_image( $post = null ) {
	$candidates = array( $post );
	if ( 'page' === get_option( 'show_on_front' ) ) $candidates[] = get_post( get_option( 'page_on_front' ) );

	foreach ( array_filter( $candidates ) as $candidate ) {
		$hero = sk_seo_find_block( $candidate, 'sk/hero' );
		$id   = $hero && ! empty( $hero['attrs']['imageId'] ) ? (int) $hero['attrs']['imageId'] : (int) get_post_thumbnail_id( $candidate );
		$src  = $id ? wp_get_attachment_image_src( $id, 'sk-hero' ) : false;
		if ( $src ) return array( $src[0], $src[1], $src[2] );
	}
	return array( get_template_directory_uri() . '/assets/images/hero-home.jpg', 0, 0 );
}

function sk_seo_url( $post = null ) {
	if ( is_front_page() ) return home_url( '/' );
	if ( $post ) {
		$url = wp_get_canonical_url( $post );
		return $url ? $url : get_permalink( $post );
	}
	global $wp;
	return home_url( add_query_arg( array(), $wp->request ) );
}

/* -------------------------------------------------------------------------
 * Meta description and Open Graph
 * ---------------------------------------------------------------------- */

function sk_seo_meta_tags() {
	if ( sk_seo_plugin_active() || is_404() ) return;

	$post        = sk_seo_post();
	$description = sk_seo_description( $post );
	$image       = sk_seo_image( $post );
	$title       = $post && ! is_front_page() ? get_the_title( $post ) : get_bloginfo( 'name' );

	$tags = array(
		array( 'name', 'description', $description ),
		array( 'property', 'og:type', 'website' ),
		array( 'property', 'og:locale', 'de_CH' ),
		array( 'property', 'og:site_name', get_bloginfo( 'name' ) ),
		array( 'property', 'og:title', wp_strip_all_tags( $title ) ),
		array( 'property', 'og:description', $description ),
		array( 'property', 'og:url', sk_seo_url( $post ) ),
		array( 'property', 'og:image', $image[0] ),
		array( 'property', 'og:image:width', $image[1] ),
		array( 'property', 'og:image:height', $image[2] ),
		array( 'name', 'twitter:card', 'summary_large_image' ),
	);
	foreach ( $tags as $tag ) {
		if ( '' === (string) $tag[2] || 0 === $tag[2] ) continue;
		printf( '<meta %s="%s" content="%s">' . "\n", $tag[0], esc_attr( $tag[1] ), esc_attr( $tag[2] ) );
	}
}
add_action( 'wp_head', 'sk_seo_meta_tags', 2 );

/* -------------------------------------------------------------------------
 * JSON-LD
 * ---------------------------------------------------------------------- */

function sk_seo_id( $fragment ) {
	return home_url( '/' ) . '#' . $fragment;
}

/**
 * Where the business works: its canton.
 */
function sk_schema_area() {
	$canton = trim( get_theme_mod( 'sk_address_canton', 'Zürich' ) );
	return $canton ? array( '@type' => 'AdministrativeArea', 'name' => 'Kanton ' . $canton ) : '';
}

/**
 * The business, from Kontaktangaben and Öffnungszeiten.
 */
function sk_schema_business() {
	$city = trim( get_theme_mod( 'sk_address_city', '8000 Zürich' ) );
	preg_match( '/^(\d{4})\s+(.+)$/u', $city, $plz );

	$address = array_filter( array(
		'@type'           => 'PostalAddress',
		'streetAddress'   => get_theme_mod( 'sk_address_street', 'Musterstrasse 12' ),
		'postalCode'      => $plz ? $plz[1] : '',
		'addressLocality' => $plz ? $plz[2] : $city,
		'addressRegion'   => get_theme_mod( 'sk_address_canton', 'Zürich' ),
		'addressCountry'  => 'CH',
	) );

	$logo_id = (int) get_theme_mod( 'custom_logo' );
	$logo    = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : get_template_directory_uri() . '/assets/images/logo.png';
	$coords  = sk_map_geocode( sk_map_address(), false );

	$business = array(
		'@type'                     => 'HomeAndConstructionBusiness',
		'@id'                       => sk_seo_id( 'business' ),
		'name'                      => get_bloginfo( 'name' ),
		'legalName'                 => get_theme_mod( 'sk_company_legal', '' ),
		'description'               => get_bloginfo( 'description' ),
		'url'                       => home_url( '/' ),
		'telephone'                 => get_theme_mod( 'sk_phone', '+41 234 43 21' ),
		'email'                     => get_theme_mod( 'sk_email', 'info@solarkraftwerk.ch' ),
		'address'                   => $address,
		'logo'                      => array( '@type' => 'ImageObject', 'url' => $logo ),
		'image'                     => $logo,
		'areaServed'                => sk_schema_area(),
		'openingHoursSpecification' => sk_opening_hours_schema(),
		'sameAs'                    => array_values( array_filter( array( get_theme_mod( 'sk_linkedin_url', '' ), get_theme_mod( 'sk_facebook_url', '' ) ) ) ),
		'knowsAbout'                => array( 'Photovoltaik', 'Solaranlagen', 'Batteriespeicher', 'Wechselrichter' ),
	);
	if ( $coords ) {
		$business['geo']    = array( '@type' => 'GeoCoordinates', 'latitude' => $coords[0], 'longitude' => $coords[1] );
		$business['hasMap'] = sk_map_link_url( $coords );
	}
	if ( get_posts( array( 'post_type' => 'product', 'numberposts' => 1, 'fields' => 'ids' ) ) ) {
		$business['hasOfferCatalog'] = array( '@id' => sk_seo_id( 'catalog' ) );
	}
	return sk_schema_clean( $business );
}

/**
 * Products and services as an OfferCatalog, grouped by type like the overview.
 */
function sk_schema_catalog( $block, $page_url ) {
	$attrs    = isset( $block['attrs'] ) ? $block['attrs'] : array();
	$headings = array(
		'product' => isset( $attrs['productsHeading'] ) ? $attrs['productsHeading'] : 'Photovoltaik-Produkte',
		'service' => isset( $attrs['servicesHeading'] ) ? $attrs['servicesHeading'] : 'Service-Produkte',
	);

	$groups = array();
	foreach ( get_posts( sk_section_query_args( 'product' ) ) as $product ) {
		$groups[ sk_product_type( $product->ID ) ][] = array(
			'@type'       => 'Offer',
			'itemOffered' => sk_schema_clean( array(
				'@type'       => 'Service',
				'name'        => get_the_title( $product ),
				'description' => sk_seo_outline( $product->post_content ),
				'url'         => $page_url . '#produkt-' . $product->ID,
				'provider'    => array( '@id' => sk_seo_id( 'business' ) ),
				'areaServed'  => sk_schema_area(),
			) ),
		);
	}
	if ( ! $groups ) return null;

	$lists = array();
	foreach ( sk_product_types() as $type => $label ) {
		if ( empty( $groups[ $type ] ) ) continue;
		$lists[] = array(
			'@type'           => 'OfferCatalog',
			'name'            => wp_strip_all_tags( $headings[ $type ] ? $headings[ $type ] : $label ),
			'itemListElement' => $groups[ $type ],
		);
	}
	return array(
		'@type'           => 'OfferCatalog',
		'@id'             => sk_seo_id( 'catalog' ),
		'name'            => 'Produkte und Leistungen',
		'url'             => $page_url,
		'itemListElement' => $lists,
	);
}

/**
 * The people of a team block's group (team members, not partners) as employees.
 */
function sk_schema_people( $group ) {
	$args               = sk_section_query_args( 'team_member' );
	$args['meta_key']   = '_sk_group';
	$args['meta_value'] = $group;

	$people = array();
	foreach ( get_posts( $args ) as $person ) {
		$people[] = sk_schema_clean( array(
			'@type'       => 'Person',
			'@id'         => sk_seo_id( 'person-' . $person->ID ),
			'name'        => get_the_title( $person ),
			'jobTitle'    => get_post_meta( $person->ID, '_sk_role', true ),
			'description' => sk_seo_text( get_post_meta( $person->ID, '_sk_description', true ), 300 ),
			'image'       => get_the_post_thumbnail_url( $person, 'sk-card' ),
			'worksFor'    => array( '@id' => sk_seo_id( 'business' ) ),
		) );
	}
	return $people;
}

/**
 * Projects of the Referenzen list as an ItemList of finished installations.
 */
function sk_schema_projects( $page_url ) {
	$items = array();
	foreach ( get_posts( sk_section_query_args( 'project' ) ) as $i => $project ) {
		$facts = array_filter( array(
			get_post_meta( $project->ID, '_sk_power_kwp', true ),
			get_post_meta( $project->ID, '_sk_modules', true ) ? get_post_meta( $project->ID, '_sk_modules', true ) . ' Module' : '',
		) );
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'item'     => sk_schema_clean( array(
				'@type'       => 'CreativeWork',
				'name'        => get_the_title( $project ),
				'description' => sk_seo_text( trim( get_post_meta( $project->ID, '_sk_subtitle', true ) . ( $facts ? ' – ' . implode( ', ', $facts ) : '' ), ' –' ), 300 ),
				'dateCreated' => get_post_meta( $project->ID, '_sk_year', true ),
				'image'       => get_the_post_thumbnail_url( $project, 'large' ),
				'url'         => $page_url . '#projekt-' . $project->ID,
				'creator'     => array( '@id' => sk_seo_id( 'business' ) ),
			) ),
		);
	}
	return $items ? array( '@type' => 'ItemList', 'name' => 'Referenzprojekte', 'itemListElement' => $items ) : null;
}

/**
 * Home › parent pages › page.
 */
function sk_schema_breadcrumb( $post, $page_url ) {
	$trail = array( array( 'Startseite', home_url( '/' ) ) );
	foreach ( array_reverse( get_post_ancestors( $post ) ) as $ancestor ) {
		$trail[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
	}
	$trail[] = array( get_the_title( $post ), $page_url );

	$items = array();
	foreach ( $trail as $i => $crumb ) {
		$items[] = array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => wp_strip_all_tags( $crumb[0] ), 'item' => $crumb[1] );
	}
	return array( '@type' => 'BreadcrumbList', '@id' => $page_url . '#breadcrumb', 'itemListElement' => $items );
}

/**
 * Drop empty values so the output only states what is known.
 */
function sk_schema_clean( $data ) {
	return array_filter( $data, function ( $value ) {
		return ! ( '' === $value || null === $value || false === $value || array() === $value );
	} );
}

function sk_schema_graph() {
	$post     = sk_seo_post();
	$page_url = sk_seo_url( $post );
	$graph    = array( sk_schema_business() );
	$plugin   = sk_seo_plugin_active();

	if ( ! $plugin ) {
		$graph[] = array(
			'@type'      => 'WebSite',
			'@id'        => sk_seo_id( 'website' ),
			'url'        => home_url( '/' ),
			'name'       => get_bloginfo( 'name' ),
			'inLanguage' => 'de-CH',
			'publisher'  => array( '@id' => sk_seo_id( 'business' ) ),
		);
	}
	if ( ! $post ) return $graph;

	$page_type = 'WebPage';
	$extra     = array();

	$catalog_block = sk_seo_find_block( $post, 'sk/products', array( 'mode' => 'catalog' ) );
	if ( $catalog_block && ( $catalog = sk_schema_catalog( $catalog_block, $page_url ) ) ) {
		$graph[]             = $catalog;
		$extra['mainEntity'] = array( '@id' => sk_seo_id( 'catalog' ) );
	}

	if ( sk_seo_find_block( $post, 'sk/projects', array( 'mode' => 'list' ) ) && ( $projects = sk_schema_projects( $page_url ) ) ) {
		$projects['@id']     = $page_url . '#projects';
		$graph[]             = $projects;
		$extra['mainEntity'] = array( '@id' => $page_url . '#projects' );
		$page_type           = 'CollectionPage';
	}

	$team_block = sk_seo_find_block( $post, 'sk/team' );
	if ( $team_block ) {
		$people = sk_schema_people( isset( $team_block['attrs']['group'] ) ? $team_block['attrs']['group'] : 'team' );
		if ( $people && ( ! isset( $team_block['attrs']['group'] ) || 'team' === $team_block['attrs']['group'] ) ) {
			$graph     = array_merge( $graph, $people );
			$page_type = 'AboutPage';
		}
	}

	if ( sk_seo_find_block( $post, 'sk/contact' ) ) {
		$page_type = 'ContactPage';
	}
	if ( is_front_page() || 'AboutPage' === $page_type || 'ContactPage' === $page_type ) {
		$extra['about'] = array( '@id' => sk_seo_id( 'business' ) );
	}

	if ( ! $plugin ) {
		$image   = sk_seo_image( $post );
		$webpage = array(
			'@type'       => $page_type,
			'@id'         => $page_url . '#webpage',
			'url'         => $page_url,
			'name'        => wp_strip_all_tags( is_front_page() ? get_bloginfo( 'name' ) : get_the_title( $post ) ),
			'description' => sk_seo_description( $post ),
			'inLanguage'  => 'de-CH',
			'isPartOf'    => array( '@id' => sk_seo_id( 'website' ) ),
			'primaryImageOfPage' => array( '@type' => 'ImageObject', 'url' => $image[0] ),
			'datePublished' => get_the_date( 'c', $post ),
			'dateModified'  => get_the_modified_date( 'c', $post ),
		) + $extra;
		if ( ! is_front_page() ) {
			$graph[]               = sk_schema_breadcrumb( $post, $page_url );
			$webpage['breadcrumb'] = array( '@id' => $page_url . '#breadcrumb' );
		}
		$graph[] = sk_schema_clean( $webpage );
	}

	return $graph;
}

function sk_schema_print() {
	if ( is_404() || is_search() ) return;
	$data = array(
		'@context' => 'https://schema.org',
		'@graph'   => sk_schema_graph(),
	);
	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG )
	);
}
add_action( 'wp_head', 'sk_schema_print', 20 );

/* -------------------------------------------------------------------------
 * Editor field: Beschreibung für Suchmaschinen
 * ---------------------------------------------------------------------- */

function sk_seo_meta_box() {
	if ( sk_seo_plugin_active() ) return;
	add_meta_box( 'sk_seo', 'Suchmaschinen & Teilen', 'sk_render_seo_meta_box', array( 'page', 'post' ), 'side', 'low' );
}
add_action( 'add_meta_boxes', 'sk_seo_meta_box' );

function sk_render_seo_meta_box( $post ) {
	wp_nonce_field( 'sk_save_seo', 'sk_seo_nonce' );
	printf(
		'<p><label for="sk-seo-description"><strong>Beschreibung</strong></label></p>'
		. '<textarea id="sk-seo-description" name="_sk_seo_description" rows="5" style="width:100%%" maxlength="300">%s</textarea>'
		. '<p class="description">Erscheint in Google unter dem Seitentitel und in Link-Vorschauen (z. B. WhatsApp, LinkedIn). Ideal sind 120–160 Zeichen. Leer = automatisch aus dem Untertitel des Hero-Bereichs bzw. dem Inhalt der Seite.</p>',
		esc_textarea( get_post_meta( $post->ID, '_sk_seo_description', true ) )
	);
}

function sk_save_seo_meta( $post_id ) {
	if ( ! isset( $_POST['sk_seo_nonce'] ) || ! wp_verify_nonce( $_POST['sk_seo_nonce'], 'sk_save_seo' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) || ! isset( $_POST['_sk_seo_description'] ) ) return;

	$value = sanitize_textarea_field( wp_unslash( $_POST['_sk_seo_description'] ) );
	if ( '' === $value ) {
		delete_post_meta( $post_id, '_sk_seo_description' );
	} else {
		update_post_meta( $post_id, '_sk_seo_description', $value );
	}
}
add_action( 'save_post', 'sk_save_seo_meta' );
