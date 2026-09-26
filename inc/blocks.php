<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sk_register_blocks() {
	$theme_uri = get_template_directory_uri();

	wp_register_script(
		'sk-editor-shared',
		$theme_uri . '/assets/js/editor-shared.js',
		array( 'wp-element', 'wp-components', 'wp-block-editor', 'wp-data', 'wp-core-data', 'wp-html-entities' ),
		SK_THEME_VERSION,
		true
	);
	wp_add_inline_script( 'sk-editor-shared', 'window.skEditorData = ' . wp_json_encode( sk_editor_data() ) . ';', 'before' );

	$editor_deps = array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-data', 'sk-editor-shared' );
	$blocks_dir  = get_template_directory() . '/blocks';

	foreach ( glob( $blocks_dir . '/*', GLOB_ONLYDIR ) as $dir ) {
		$slug   = basename( $dir );
		$handle = "sk-block-{$slug}-editor";
		wp_register_script( $handle, $theme_uri . "/blocks/{$slug}/edit.js", $editor_deps, SK_THEME_VERSION, true );
		register_block_type( $dir );
	}
}
add_action( 'init', 'sk_register_blocks' );

/**
 * Site-wide values the editor previews need but blocks don't store themselves.
 */
function sk_editor_data() {
	return array(
		'heroFallback'  => get_template_directory_uri() . '/assets/images/hero-home.jpg',
		'customizeUrl'  => admin_url( 'customize.php' ),
		'cta'           => array(
			'title'    => get_theme_mod( 'sk_cta_title', 'Kostenlose Offerte' ),
			'subtitle' => get_theme_mod( 'sk_cta_subtitle', 'in nur 2 Minuten' ),
		),
		'contact'       => array(
			'street' => get_theme_mod( 'sk_address_street', 'Musterstrasse 12' ),
			'city'   => get_theme_mod( 'sk_address_city', '8000 Zürich' ),
			'phone'  => get_theme_mod( 'sk_phone', '+41 234 43 21' ),
			'email'  => get_theme_mod( 'sk_email', 'info@solarkraftwerk.ch' ),
			'hours'  => get_theme_mod( 'sk_hours', 'Mo–Fr 08:00–17:00 Uhr' ),
			'maps'   => get_theme_mod( 'sk_maps_embed', '' ),
		),
	);
}

function sk_block_category( $categories ) {
	return array_merge(
		array( array( 'slug' => 'solarkraftwerk', 'title' => 'Solar Kraftwerk' ) ),
		$categories
	);
}
add_filter( 'block_categories_all', 'sk_block_category' );

/**
 * Prints a heading edited inline via RichText; skipped when the editor cleared it.
 */
function sk_block_heading( $html, $tag = 'h2' ) {
	if ( '' === trim( wp_strip_all_tags( (string) $html ) ) ) return;
	printf( '<%1$s>%2$s</%1$s>', tag_escape( $tag ), wp_kses_post( $html ) );
}

/**
 * Whether the current post is laid out with the theme's section blocks.
 */
function sk_uses_sections( $post = null ) {
	$post = get_post( $post );
	return $post && false !== strpos( $post->post_content, '<!-- wp:sk/' );
}

function sk_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( "/{$slug}/" );
}

/**
 * The block's link target, falling back to the page with the given slug.
 */
function sk_block_link_url( $attributes, $fallback_slug ) {
	return ! empty( $attributes['linkUrl'] ) ? $attributes['linkUrl'] : sk_page_url( $fallback_slug );
}

function sk_more_link( $attributes, $fallback_slug ) {
	$text = isset( $attributes['linkText'] ) ? $attributes['linkText'] : '';
	if ( '' === trim( wp_strip_all_tags( $text ) ) ) return;
	printf( '<a class="sk-link" href="%s">%s</a>', esc_url( sk_block_link_url( $attributes, $fallback_slug ) ), wp_kses_post( $text ) );
}

/**
 * Query args shared by the section blocks: hand-picked posts if any, else the
 * first $limit posts, both following the "Reihenfolge" field.
 */
function sk_section_query_args( $post_type, $ids = array(), $limit = -1 ) {
	$args = array(
		'post_type'      => $post_type,
		'posts_per_page' => $limit ? $limit : -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	);
	$ids = array_filter( array_map( 'absint', (array) $ids ) );
	if ( $ids ) {
		$args['post__in']       = $ids;
		$args['posts_per_page'] = -1;
	}
	return $args;
}

/**
 * Wraps a section block's output. In the editor the heading and link are edited
 * inline, so the server-side preview only returns the body.
 */
function sk_section_open( $attributes ) {
	if ( ! empty( $attributes['editorPreview'] ) ) return;
	echo '<section ' . get_block_wrapper_attributes( array( 'class' => 'sk-section sk-block-section' ) ) . '><div class="sk-container">';
	sk_block_heading( isset( $attributes['heading'] ) ? $attributes['heading'] : '' );
}

function sk_section_close( $attributes, $fallback_slug = '' ) {
	if ( ! empty( $attributes['editorPreview'] ) ) return;
	if ( $fallback_slug ) sk_more_link( $attributes, $fallback_slug );
	echo '</div></section>';
}
