<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Expects $args = [ 'title' => '', 'subtitle' => '', 'image' => url ]
 */
$sk_title    = $args['title'] ?? '';
$sk_subtitle = $args['subtitle'] ?? '';
$sk_image    = $args['image'] ?? get_template_directory_uri() . '/assets/images/hero-home.jpg';
?>
<section class="sk-page-hero" style="background-image:url('<?php echo esc_url( $sk_image ); ?>');">
	<div class="sk-page-hero__overlay">
		<div class="sk-page-hero__content">
			<h1><?php echo esc_html( $sk_title ); ?></h1>
			<?php if ( $sk_subtitle ) : ?><p><?php echo esc_html( $sk_subtitle ); ?></p><?php endif; ?>
		</div>
		<?php get_template_part( 'template-parts/cta-button' ); ?>
	</div>
</section>
