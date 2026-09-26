<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) : the_post();
	if ( sk_uses_sections() ) :
		// Built from theme sections: the blocks bring their own hero, headings and spacing.
		?>
		<div class="sk-page-content">
			<?php the_content(); ?>
		</div>
		<?php
	else :
		// Plain text pages (Impressum, Datenschutz, …).
		?>
		<section class="sk-section">
			<div class="sk-container sk-container--narrow">
				<h1><?php the_title(); ?></h1>
				<div class="sk-content"><?php the_content(); ?></div>
			</div>
		</section>
		<?php
	endif;
endwhile;

get_footer();
