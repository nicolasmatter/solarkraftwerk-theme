<?php
/* Template Name: Über Uns */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

function sk_render_team_group( $group_key, $heading ) {
	$query = new WP_Query( array(
		'post_type'      => 'team_member',
		'posts_per_page' => -1,
		'meta_key'       => '_sk_group',
		'meta_value'     => $group_key,
	) );
	if ( ! $query->have_posts() ) return;
	?>
	<div class="sk-team-group">
		<h2><?php echo esc_html( $heading ); ?></h2>
		<?php while ( $query->have_posts() ) : $query->the_post();
			$role  = get_post_meta( get_the_ID(), '_sk_role', true );
			$phone = get_post_meta( get_the_ID(), '_sk_phone', true );
			$email = get_post_meta( get_the_ID(), '_sk_email', true );
			?>
			<div class="sk-team-member">
				<div class="sk-team-member__photo">
					<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'sk-card' ); else : ?>
						<svg viewBox="0 0 100 100" aria-hidden="true"><circle cx="50" cy="38" r="22" fill="#c7cccc"/><path d="M12 96c4-24 26-34 38-34s34 10 38 34" fill="#c7cccc"/></svg>
					<?php endif; ?>
				</div>
				<div class="sk-team-member__info">
					<h3><?php the_title(); ?></h3>
					<?php if ( $role ) : ?><p><?php echo esc_html( $role ); ?></p><?php endif; ?>
					<?php if ( $phone ) : ?><p><?php echo esc_html( $phone ); ?></p><?php endif; ?>
					<?php if ( $email ) : ?><p><?php echo esc_html( $email ); ?></p><?php endif; ?>
				</div>
			</div>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
	<?php
}

while ( have_posts() ) : the_post();
	$sk_subtitle = get_the_excerpt() ?: 'Seit über 12 Jahren realisieren wir Photovoltaikanlagen für Privathaushalte, Landwirtschaft und Gewerbe im Kanton Zürich – unkompliziert, transparent und aus einer Hand.';
	$sk_image    = get_the_post_thumbnail_url( get_the_ID(), 'sk-hero' ) ?: get_template_directory_uri() . '/assets/images/hero-ueber-uns.jpg';

	get_template_part( 'template-parts/page-hero', null, array(
		'title'    => get_the_title(),
		'subtitle' => $sk_subtitle,
		'image'    => $sk_image,
	) );
	?>

	<section class="sk-section">
		<div class="sk-container">
			<?php
			sk_render_team_group( 'team', get_theme_mod( 'sk_team_heading', 'Unser Team' ) );
			sk_render_team_group( 'partner', get_theme_mod( 'sk_partner_heading', 'Unsere Partner' ) );

			if ( ! ( new WP_Query( array( 'post_type' => 'team_member', 'posts_per_page' => 1 ) ) )->have_posts() ) {
				echo '<p>Noch niemand erfasst. Lege Team- und Partnerprofile unter „Über Uns“ im Admin-Menü an.</p>';
			}
			?>
		</div>
	</section>
	<?php
endwhile;

get_footer();
