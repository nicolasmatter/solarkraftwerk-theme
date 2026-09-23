<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
$sk_home_image = get_the_post_thumbnail_url( get_the_ID(), 'sk-hero' ) ?: get_template_directory_uri() . '/assets/images/hero-home.jpg';
?>

<section class="sk-home-hero" style="background-image:url('<?php echo esc_url( $sk_home_image ); ?>');">
	<div class="sk-home-hero__overlay">
		<?php get_template_part( 'template-parts/cta-button' ); ?>
	</div>
</section>

<section class="sk-section">
	<div class="sk-container">
		<h2><?php echo esc_html( get_theme_mod( 'sk_home_products_heading', 'Produkte und Leistungen' ) ); ?></h2>
		<div class="sk-grid sk-grid--2">
			<?php
			$sk_products = new WP_Query( array(
				'post_type'      => 'product',
				'posts_per_page' => 2,
				'orderby'        => 'menu_order date',
				'order'          => 'ASC',
			) );
			if ( $sk_products->have_posts() ) :
				while ( $sk_products->have_posts() ) : $sk_products->the_post();
					?>
					<a class="sk-card" href="<?php the_permalink(); ?>">
						<div class="sk-card__image">
							<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'sk-card' ); ?>
						</div>
						<div class="sk-card__label"><h3><?php the_title(); ?></h3></div>
					</a>
					<?php
				endwhile;
				wp_reset_postdata();
			else :
				?>
				<a class="sk-card" href="#">
					<div class="sk-card__image"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/product-pv.jpg' ); ?>" alt="Photovoltaikanlagen"></div>
					<div class="sk-card__label"><h3>Photovoltaikanlagen</h3></div>
				</a>
				<a class="sk-card" href="#">
					<div class="sk-card__image"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/product-battery.jpg' ); ?>" alt="Batterie-Speicher"></div>
					<div class="sk-card__label"><h3>Batterie-Speicher</h3></div>
				</a>
				<?php
			endif;
			?>
		</div>
		<a class="sk-link" href="<?php echo esc_url( get_post_type_archive_link( 'product' ) ?: home_url( '/produkte/' ) ); ?>">Mehr sehen</a>
	</div>
</section>

<section class="sk-section">
	<div class="sk-container">
		<h2><?php echo esc_html( get_theme_mod( 'sk_home_projects_heading', 'Ausgewählte Projekte' ) ); ?></h2>
		<div class="sk-grid sk-grid--3">
			<?php
			$sk_projects = new WP_Query( array(
				'post_type'      => 'project',
				'posts_per_page' => 3,
			) );
			if ( $sk_projects->have_posts() ) :
				while ( $sk_projects->have_posts() ) : $sk_projects->the_post();
					$sk_year = get_post_meta( get_the_ID(), '_sk_year', true );
					?>
					<a class="sk-card sk-card--project" href="<?php the_permalink(); ?>">
						<div class="sk-card__image sk-card__image--tall">
							<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'sk-card-portrait' ); ?>
						</div>
						<div class="sk-card__label">
							<h3><?php the_title(); ?></h3>
							<div class="sk-card__meta">
								<span><?php echo esc_html( $sk_year ? $sk_year : 'Jahr' ); ?></span>
								<span aria-hidden="true">&rarr;</span>
							</div>
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
		<a class="sk-link" href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ?: home_url( '/referenzen/' ) ); ?>">Mehr sehen</a>
	</div>
</section>

<?php get_footer(); ?>
