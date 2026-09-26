<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$image_id = isset( $attributes['imageId'] ) ? absint( $attributes['imageId'] ) : 0;
$image    = $image_id ? wp_get_attachment_image_url( $image_id, 'sk-hero' ) : '';
if ( ! $image ) {
	$image = get_template_directory_uri() . '/assets/images/hero-home.jpg';
}
$title    = isset( $attributes['title'] ) ? $attributes['title'] : '';
$subtitle = isset( $attributes['subtitle'] ) ? $attributes['subtitle'] : '';
$is_large = isset( $attributes['size'] ) && 'large' === $attributes['size'];
$show_cta = ! isset( $attributes['showCta'] ) || $attributes['showCta'];

$wrapper = get_block_wrapper_attributes( array(
	'class' => $is_large ? 'sk-hero sk-hero--large' : 'sk-hero',
	'style' => "background-image:url('" . esc_url( $image ) . "');",
) );
?>
<section <?php echo $wrapper; ?>>
	<div class="sk-hero__overlay">
		<?php if ( trim( wp_strip_all_tags( $title . $subtitle ) ) ) : ?>
			<div class="sk-hero__content">
				<?php sk_block_heading( $title, 'h1' ); ?>
				<?php sk_block_heading( $subtitle, 'p' ); ?>
			</div>
		<?php endif; ?>
		<?php if ( $show_cta ) get_template_part( 'template-parts/cta-button' ); ?>
	</div>
</section>
