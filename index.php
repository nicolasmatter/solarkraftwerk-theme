<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<section class="sk-section">
	<div class="sk-container sk-container--narrow">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="sk-content"><?php the_excerpt(); ?></div>
			</article>
		<?php endwhile; else : ?>
			<p>Keine Inhalte gefunden.</p>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>
