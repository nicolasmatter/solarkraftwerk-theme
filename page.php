<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<section class="sk-section">
	<div class="sk-container sk-container--narrow">
		<?php while ( have_posts() ) : the_post(); ?>
			<h1><?php the_title(); ?></h1>
			<div class="sk-content"><?php the_content(); ?></div>
		<?php endwhile; ?>
	</div>
</section>
<?php get_footer(); ?>
