<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
while ( have_posts() ) : the_post();
	$id       = get_the_ID();
	$subtitle = get_post_meta( $id, '_sk_subtitle', true );
	$power    = get_post_meta( $id, '_sk_power_kwp', true );
	$year     = get_post_meta( $id, '_sk_year', true );
	$modules  = get_post_meta( $id, '_sk_modules', true );
	?>
	<section class="sk-section">
		<div class="sk-container sk-container--narrow">
			<h1><?php the_title(); ?></h1>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="sk-single-image"><?php the_post_thumbnail( 'sk-hero' ); ?></div>
			<?php endif; ?>
			<?php if ( $subtitle ) : ?><p class="sk-project-row__subtitle"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
			<dl class="sk-project-row__stats">
				<?php if ( $power ) : ?><div><dt>Leistung:</dt><dd><?php echo esc_html( $power ); ?></dd></div><?php endif; ?>
				<?php if ( $year ) : ?><div><dt>Umsetzungsjahr:</dt><dd><?php echo esc_html( $year ); ?></dd></div><?php endif; ?>
				<?php if ( $modules ) : ?><div><dt>Anzahl Module:</dt><dd><?php echo esc_html( $modules ); ?></dd></div><?php endif; ?>
			</dl>
			<div class="sk-content"><?php the_content(); ?></div>
		</div>
	</section>
	<?php
endwhile;
get_footer();
