<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sk_register_blocks() {
	$editor_deps = array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n' );
	$blocks_dir  = get_template_directory() . '/blocks';

	foreach ( glob( $blocks_dir . '/*', GLOB_ONLYDIR ) as $dir ) {
		$slug   = basename( $dir );
		$handle = "sk-block-{$slug}-editor";
		wp_register_script( $handle, get_template_directory_uri() . "/blocks/{$slug}/edit.js", $editor_deps, SK_THEME_VERSION, true );
		register_block_type( $dir );
	}
}
add_action( 'init', 'sk_register_blocks' );

function sk_block_category( $categories ) {
	return array_merge(
		array( array( 'slug' => 'solarkraftwerk', 'title' => 'Solar Kraftwerk' ) ),
		$categories
	);
}
add_filter( 'block_categories_all', 'sk_block_category' );
