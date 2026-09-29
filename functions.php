<?php
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SK_THEME_VERSION', '0.1.0' );

function sk_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'automatic-feed-links' );

	register_nav_menus( array(
		'primary' => __( 'Hauptmenü', 'solarkraftwerk' ),
		'footer'  => __( 'Footer (Rechtliches)', 'solarkraftwerk' ),
	) );

	add_image_size( 'sk-card', 680, 450, true );
	add_image_size( 'sk-card-portrait', 447, 450, true );
	add_image_size( 'sk-hero', 1600, 600, true );

	// Sections (hero, products, …) span the full width; everything else follows theme.json layout.
	add_theme_support( 'align-wide' );

	// Keep the inserter focused on the theme's own sections instead of WordPress.org patterns.
	remove_theme_support( 'core-block-patterns' );

	// So the block editor's Server-Side-Render previews use our real styling.
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/main.css' );
}
add_action( 'after_setup_theme', 'sk_setup' );

function sk_scripts() {
	wp_enqueue_style( 'sk-main', get_template_directory_uri() . '/assets/css/main.css', array(), SK_THEME_VERSION );
	wp_enqueue_script( 'sk-main', get_template_directory_uri() . '/assets/js/main.js', array(), SK_THEME_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'sk_scripts' );

require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/cpt-project.php';
require get_template_directory() . '/inc/cpt-product.php';
require get_template_directory() . '/inc/cpt-team.php';
require get_template_directory() . '/inc/contact-form.php';
require get_template_directory() . '/inc/blocks.php';
require get_template_directory() . '/inc/upgrade.php';
require get_template_directory() . '/inc/updater.php';

add_filter( 'should_load_remote_block_patterns', '__return_false' );

/**
 * Footer links until a menu is assigned to the "Footer (Rechtliches)" location.
 */
function sk_default_footer_menu() {
	echo '<ul>';
	foreach ( array( 'datenschutzbestimmungen' => 'Datenschutzbestimmungen', 'impressum' => 'Impressum' ) as $slug => $label ) {
		echo '<li><a href="' . esc_url( sk_page_url( $slug ) ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

/**
 * List products, projects and people in wp-admin in the same order as on the site.
 */
function sk_admin_menu_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || $query->get( 'orderby' ) ) return;
	if ( in_array( $query->get( 'post_type' ), array( 'product', 'project', 'team_member' ), true ) ) {
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
}
add_action( 'pre_get_posts', 'sk_admin_menu_order' );

/**
 * Small helper to output a text/textarea meta field inside a meta box.
 */
function sk_meta_field( $post, $key, $label, $type = 'text' ) {
	$value = get_post_meta( $post->ID, $key, true );
	echo '<p><label for="' . esc_attr( $key ) . '"><strong>' . esc_html( $label ) . '</strong></label><br>';
	if ( 'textarea' === $type ) {
		echo '<textarea style="width:100%;" rows="3" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">' . esc_textarea( $value ) . '</textarea>';
	} else {
		echo '<input type="text" style="width:100%;" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '">';
	}
	echo '</p>';
}
