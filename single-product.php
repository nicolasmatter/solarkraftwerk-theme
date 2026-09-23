<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
while ( have_posts() ) : the_post();
	?>
	<section class="sk-section">
		<div class="sk-container sk-container--narrow">
			<h1><?php the_title(); ?></h1>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="sk-single-image"><?php the_post_thumbnail( 'sk-hero' ); ?></div>
			<?php endif; ?>
			<div class="sk-content"><?php the_content(); ?></div>
		</div>
	</section>
	<?php
endwhile;
get_footer();
