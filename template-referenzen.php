<?php
/* Template Name: Referenzen */
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
			<h2><?php echo esc_html( get_theme_mod( 'sk_referenzen_list_heading', 'Projekte' ) ); ?></h2>
			<div class="sk-project-list">
				<?php
				$sk_projects = new WP_Query( array(
					'post_type'      => 'project',
					'posts_per_page' => -1,
				) );
				if ( $sk_projects->have_posts() ) :
					while ( $sk_projects->have_posts() ) : $sk_projects->the_post();
						$id       = get_the_ID();
						$subtitle = get_post_meta( $id, '_sk_subtitle', true );
						$power    = get_post_meta( $id, '_sk_power_kwp', true );
						$year     = get_post_meta( $id, '_sk_year', true );
						$modules  = get_post_meta( $id, '_sk_modules', true );
						?>
						<a class="sk-project-row" href="<?php the_permalink(); ?>">
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
						</a>
						<?php
					endwhile;
					wp_reset_postdata();
				else :
					?>
					<p>Noch keine Projekte veröffentlicht. Lege welche unter „Referenzen“ im Admin-Menü an.</p>
					<?php
				endif;
				?>
			</div>
		</div>
	</section>
	<?php
endwhile;

get_footer();
