<?php
/* Template Name: Über Uns */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) : the_post();
	$sk_subtitle = get_the_excerpt() ?: 'Seit über 12 Jahren realisieren wir Photovoltaikanlagen für Privathaushalte, Landwirtschaft und Gewerbe im Kanton Zürich – unkompliziert, transparent und aus einer Hand.';
	$sk_image    = get_the_post_thumbnail_url( get_the_ID(), 'sk-hero' ) ?: get_template_directory_uri() . '/assets/images/hero-ueber-uns.jpg';

	get_template_part( 'template-parts/page-hero', null, array(
		'title'    => get_the_title(),
		'subtitle' => $sk_subtitle,
		'image'    => $sk_image,
	) );
	?>

	<div class="sk-page-content">
		<?php the_content(); ?>
	</div>
	<?php
endwhile;

get_footer();
