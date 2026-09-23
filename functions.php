<?php
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SK_THEME_VERSION', '1.0.0' );

function sk_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'automatic-feed-links' );

	register_nav_menus( array(
		'primary' => __( 'Hauptmenü', 'solarkraftwerk' ),
	) );

	add_image_size( 'sk-card', 680, 450, true );
	add_image_size( 'sk-card-portrait', 447, 450, true );
	add_image_size( 'sk-hero', 1600, 600, true );

	// Lets editors set a hero subtitle per page via the native Excerpt panel.
	add_post_type_support( 'page', 'excerpt' );
}
add_action( 'after_setup_theme', 'sk_setup' );

function sk_scripts() {
	wp_enqueue_style( 'sk-google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap', array(), null );
	wp_enqueue_style( 'sk-main', get_template_directory_uri() . '/assets/css/main.css', array(), SK_THEME_VERSION );
	wp_enqueue_script( 'sk-main', get_template_directory_uri() . '/assets/js/main.js', array(), SK_THEME_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'sk_scripts' );

require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/cpt-project.php';
require get_template_directory() . '/inc/cpt-product.php';
require get_template_directory() . '/inc/cpt-team.php';
require get_template_directory() . '/inc/contact-form.php';

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
