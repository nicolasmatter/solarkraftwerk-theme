<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$mode  = isset( $attributes['mode'] ) ? $attributes['mode'] : 'teaser';
$limit = isset( $attributes['limit'] ) ? (int) $attributes['limit'] : 2;
$ids   = isset( $attributes['ids'] ) ? $attributes['ids'] : array();

// The catalog is headed by its product groups, so it has no section heading or link.
if ( 'catalog' === $mode ) {
	$attributes['heading'] = '';
}

sk_section_open( $attributes );

if ( 'catalog' === $mode ) :
	$sk_groups = get_terms( array( 'taxonomy' => 'product_group', 'hide_empty' => true ) );
	if ( ! is_wp_error( $sk_groups ) && $sk_groups ) :
		foreach ( $sk_groups as $sk_group ) :
			?>
			<h2><?php echo esc_html( $sk_group->name ); ?></h2>
			<div class="sk-grid sk-grid--3">
				<?php
				$sk_args              = sk_section_query_args( 'product' );
				$sk_args['tax_query'] = array( array(
					'taxonomy' => 'product_group',
					'field'    => 'term_id',
					'terms'    => $sk_group->term_id,
				) );
				$sk_q = new WP_Query( $sk_args );
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
		<p>Noch keine Produkte veröffentlicht.</p>
		<?php
	endif;
else :
	?>
	<div class="sk-grid sk-grid--2">
		<?php
		$sk_q = new WP_Query( sk_section_query_args( 'product', $ids, $limit ?: 2 ) );
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
	<?php
endif;

sk_section_close( $attributes, 'catalog' === $mode ? '' : 'produkte' );
