<?php
/* Template Name: Produkte */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) : the_post();
	$sk_subtitle = get_the_excerpt() ?: 'Von der ersten Beratung bis zur schlüsselfertigen Installation – alles aus einer Hand.';
	$sk_image    = get_the_post_thumbnail_url( get_the_ID(), 'sk-hero' ) ?: get_template_directory_uri() . '/assets/images/hero-home.jpg';

	get_template_part( 'template-parts/page-hero', null, array(
		'title'    => get_the_title(),
		'subtitle' => $sk_subtitle,
		'image'    => $sk_image,
	) );
	?>

	<section class="sk-section">
		<div class="sk-container">
			<?php
			$sk_groups = get_terms( array( 'taxonomy' => 'product_group', 'hide_empty' => true ) );
			if ( ! is_wp_error( $sk_groups ) && $sk_groups ) :
				foreach ( $sk_groups as $sk_group ) :
					?>
					<h2><?php echo esc_html( $sk_group->name ); ?></h2>
					<div class="sk-grid sk-grid--3">
						<?php
						$sk_products = new WP_Query( array(
							'post_type'      => 'product',
							'posts_per_page' => -1,
							'tax_query'      => array( array(
								'taxonomy' => 'product_group',
								'field'    => 'term_id',
								'terms'    => $sk_group->term_id,
							) ),
						) );
						while ( $sk_products->have_posts() ) : $sk_products->the_post();
							?>
							<a class="sk-card" href="<?php the_permalink(); ?>">
								<div class="sk-card__image">
									<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'sk-card' ); ?>
								</div>
								<div class="sk-card__label sk-card__label--panel"><h3><?php the_title(); ?></h3></div>
							</a>
							<?php
						endwhile;
						wp_reset_postdata();
						?>
					</div>
					<?php
				endforeach;
			else :
				?>
				<h2>Basic</h2>
				<p>Noch keine Produkte veröffentlicht. Lege welche unter „Produkte“ im Admin-Menü an.</p>
				<?php
			endif;
			?>
		</div>
	</section>
	<?php
endwhile;

get_footer();
