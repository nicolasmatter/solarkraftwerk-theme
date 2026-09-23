<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$heading = isset( $attributes['heading'] ) ? $attributes['heading'] : 'Produkte und Leistungen';
$mode    = isset( $attributes['mode'] ) ? $attributes['mode'] : 'teaser';
$limit   = isset( $attributes['limit'] ) ? (int) $attributes['limit'] : 2;

$produkte_page = get_page_by_path( 'produkte' );
$produkte_url  = $produkte_page ? get_permalink( $produkte_page ) : home_url( '/produkte/' );
?>
<section class="sk-section sk-block-section">
	<div class="sk-container">
		<?php if ( 'catalog' === $mode ) : ?>
			<?php
			$sk_groups = get_terms( array( 'taxonomy' => 'product_group', 'hide_empty' => true ) );
			if ( ! is_wp_error( $sk_groups ) && $sk_groups ) :
				foreach ( $sk_groups as $sk_group ) :
					?>
					<h2><?php echo esc_html( $sk_group->name ); ?></h2>
					<div class="sk-grid sk-grid--3">
						<?php
						$sk_q = new WP_Query( array(
							'post_type'      => 'product',
							'posts_per_page' => -1,
							'tax_query'      => array( array(
								'taxonomy' => 'product_group',
								'field'    => 'term_id',
								'terms'    => $sk_group->term_id,
							) ),
						) );
						while ( $sk_q->have_posts() ) : $sk_q->the_post();
							?>
							<a class="sk-card" href="<?php the_permalink(); ?>">
								<div class="sk-card__image"><?php if ( has_post_thumbnail() ) the_post_thumbnail( 'sk-card' ); ?></div>
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
				<p>Noch keine Produkte veröffentlicht.</p>
				<?php
			endif;
			?>
		<?php else : ?>
			<h2><?php echo esc_html( $heading ); ?></h2>
			<div class="sk-grid sk-grid--2">
				<?php
				$sk_q = new WP_Query( array( 'post_type' => 'product', 'posts_per_page' => $limit ?: 2 ) );
				if ( $sk_q->have_posts() ) :
					while ( $sk_q->have_posts() ) : $sk_q->the_post();
						?>
						<a class="sk-card" href="<?php the_permalink(); ?>">
							<div class="sk-card__image"><?php if ( has_post_thumbnail() ) the_post_thumbnail( 'sk-card' ); ?></div>
							<div class="sk-card__label"><h3><?php the_title(); ?></h3></div>
						</a>
						<?php
					endwhile;
					wp_reset_postdata();
				else :
					?>
					<p>Noch keine Produkte veröffentlicht.</p>
					<?php
				endif;
				?>
			</div>
			<a class="sk-link" href="<?php echo esc_url( $produkte_url ); ?>">Mehr sehen</a>
		<?php endif; ?>
	</div>
</section>
