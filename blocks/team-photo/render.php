<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$image_id = isset( $attributes['imageId'] ) ? absint( $attributes['imageId'] ) : 0;
// An untouched text placeholder still saves an empty <p>.
$has_text = '' !== trim( strip_tags( $content, '<img><video><iframe><svg>' ) );
if ( ! $image_id && ! $has_text ) return;
// With a link text the block is a teaser: photo and link lead to the team page.
$has_link = '' !== trim( wp_strip_all_tags( isset( $attributes['linkText'] ) ? $attributes['linkText'] : '' ) );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-section sk-block-section sk-team-photo' ) ); ?>>
	<div class="sk-container">
		<?php
		if ( $image_id ) :
			$image = wp_get_attachment_image( $image_id, 'full', false, array( 'sizes' => '(max-width: 1484px) 100vw, 1388px' ) );
			if ( $has_link ) :
				// The text link below is the one for keyboards and screen readers; the photo is just a bigger click target.
				?>
				<a class="sk-team-photo__image" href="<?php echo esc_url( sk_block_link_url( $attributes, 'ueber-uns' ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo $image; ?></a>
			<?php else : ?>
				<div class="sk-team-photo__image"><?php echo $image; ?></div>
				<?php
			endif;
		endif;
		?>
		<?php if ( $has_text ) : ?>
			<div class="sk-team-photo__text"><?php echo $content; ?></div>
		<?php endif; ?>
		<?php if ( $has_link ) : ?>
			<div class="sk-team-photo__more"><?php sk_more_link( $attributes, 'ueber-uns' ); ?></div>
		<?php endif; ?>
	</div>
</section>
