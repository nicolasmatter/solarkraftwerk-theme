<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$mode  = isset( $attributes['mode'] ) ? $attributes['mode'] : 'teaser';
$limit = isset( $attributes['limit'] ) ? (int) $attributes['limit'] : 3;
$ids   = isset( $attributes['ids'] ) ? $attributes['ids'] : array();

// Projects have no detail pages; teaser cards jump to their row in the overview.
$overview_url = sk_block_link_url( $attributes, 'referenzen' );

sk_section_open( $attributes );

if ( 'list' === $mode ) :
	?>
	<div class="sk-project-list">
		<?php
		$sk_q = new WP_Query( sk_section_query_args( 'project', array(), $limit ) );
		if ( $sk_q->have_posts() ) :
			while ( $sk_q->have_posts() ) : $sk_q->the_post();
				$id       = get_the_ID();
				$subtitle = get_post_meta( $id, '_sk_subtitle', true );
				$power    = get_post_meta( $id, '_sk_power_kwp', true );
				$year     = get_post_meta( $id, '_sk_year', true );
				$modules  = get_post_meta( $id, '_sk_modules', true );
				?>
				<article class="sk-project-row" id="projekt-<?php echo esc_attr( $id ); ?>">
					<div class="sk-project-row__image">
						<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'sk-card-portrait' ); ?>
					</div>
					<div class="sk-project-row__details">
						<h3><?php the_title(); ?></h3>
						<?php if ( $subtitle ) : ?><p class="sk-project-row__subtitle"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
						<dl class="sk-project-row__stats">
							<?php if ( $power ) : ?><div><dt>Leistung:</dt><dd><?php echo esc_html( $power ); ?></dd></div><?php endif; ?>
							<?php if ( $year ) : ?><div><dt>Umsetzungsjahr:</dt><dd><?php echo esc_html( $year ); ?></dd></div><?php endif; ?>
							<?php if ( $modules ) : ?><div><dt>Anzahl Module:</dt><dd><?php echo esc_html( $modules ); ?></dd></div><?php endif; ?>
						</dl>
					</div>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
		else :
			?>
			<p>Noch keine Projekte veröffentlicht.</p>
			<?php
		endif;
		?>
	</div>
	<?php
else :
	?>
	<div class="sk-grid sk-grid--3">
		<?php
		$sk_q = new WP_Query( sk_section_query_args( 'project', $ids, $limit ?: 3 ) );
		if ( $sk_q->have_posts() ) :
			while ( $sk_q->have_posts() ) : $sk_q->the_post();
				$year = get_post_meta( get_the_ID(), '_sk_year', true );
				?>
				<a class="sk-card sk-card--project" href="<?php echo esc_url( $overview_url . '#projekt-' . get_the_ID() ); ?>">
					<div class="sk-card__image sk-card__image--tall">
						<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'sk-card-portrait' ); ?>
					</div>
					<div class="sk-card__label">
						<h3><?php the_title(); ?></h3>
						<div class="sk-card__meta"><span><?php echo esc_html( $year ); ?></span><span aria-hidden="true">&rarr;</span></div>
					</div>
				</a>
				<?php
			endwhile;
			wp_reset_postdata();
		else :
			?>
			<p>Noch keine Projekte veröffentlicht.</p>
			<?php
		endif;
		?>
	</div>
	<?php
endif;

sk_section_close( $attributes, 'list' === $mode ? '' : 'referenzen' );
