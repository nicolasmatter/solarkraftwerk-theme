<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) : the_post();
	$sk_home_image = get_the_post_thumbnail_url( get_the_ID(), 'sk-hero' ) ?: get_template_directory_uri() . '/assets/images/hero-home.jpg';
	?>
	<section class="sk-home-hero" style="background-image:url('<?php echo esc_url( $sk_home_image ); ?>');">
		<div class="sk-home-hero__overlay">
			<?php get_template_part( 'template-parts/cta-button' ); ?>
		</div>
	</section>

	<div class="sk-page-content">
		<?php the_content(); ?>
	</div>
	<?php
endwhile;

get_footer();
