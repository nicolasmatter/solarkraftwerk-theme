<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
while ( have_posts() ) : the_post();
	$sk_id = get_the_ID();

	// Same hero as the section pages, filled from the product's fields.
	echo render_block( array(
		'blockName'    => 'sk/hero',
		'attrs'        => array(
			'imageId'  => absint( get_post_meta( $sk_id, '_sk_hero_image', true ) ),
			'title'    => get_the_title(),
			'subtitle' => esc_html( get_post_meta( $sk_id, '_sk_intro', true ) ),
		),
		'innerBlocks'  => array(),
		'innerHTML'    => '',
		'innerContent' => array(),
	) );
	?>
	<section class="sk-section sk-product">
		<div class="sk-container">
			<div>
				<a class="sk-back-link" href="<?php echo esc_url( sk_page_url( 'produkte' ) ); ?>">&larr; Alle Produkte</a>
				<div class="sk-product__main">
					<div class="sk-product__image"><?php if ( has_post_thumbnail() ) the_post_thumbnail( 'sk-product' ); ?></div>
					<div class="sk-product__panel">
						<h2>Technische Daten</h2>
						<?php sk_product_specs( $sk_id ); ?>
						<a class="sk-button sk-button--large" href="<?php echo esc_url( sk_page_url( 'kontakt' ) ); ?>">Offerte anfragen</a>
					</div>
				</div>
			</div>

			<?php if ( '' !== trim( get_the_content() ) ) : ?>
				<div class="sk-content sk-product__content"><?php the_content(); ?></div>
			<?php endif; ?>

			<?php
			$sk_args                 = sk_section_query_args( 'product', array(), 2 );
			$sk_args['post__not_in'] = array( $sk_id );
			$sk_q                    = new WP_Query( $sk_args );
			if ( $sk_q->have_posts() ) :
				?>
				<div>
					<h2>Weitere Produkte</h2>
					<div class="sk-grid sk-grid--2">
						<?php while ( $sk_q->have_posts() ) : $sk_q->the_post(); ?>
							<a class="sk-product-card" href="<?php the_permalink(); ?>">
								<div class="sk-card__image"><?php if ( has_post_thumbnail() ) the_post_thumbnail( 'sk-card' ); ?></div>
								<div class="sk-product-card__panel">
									<h3><?php the_title(); ?></h3>
									<?php sk_product_specs( get_the_ID() ); ?>
								</div>
							</a>
						<?php endwhile; ?>
					</div>
				</div>
				<?php
				wp_reset_postdata();
			endif;
			?>
		</div>
	</section>
	<?php
endwhile;
get_footer();
