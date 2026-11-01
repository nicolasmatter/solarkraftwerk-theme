<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$image_id = isset( $attributes['imageId'] ) ? absint( $attributes['imageId'] ) : 0;
// An untouched text placeholder still saves an empty <p>.
$has_text = '' !== trim( strip_tags( $content, '<img><video><iframe><svg>' ) );
if ( ! $image_id && ! $has_text ) return;
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'sk-section sk-block-section sk-team-photo' ) ); ?>>
	<div class="sk-container">
		<?php if ( $image_id ) : ?>
			<div class="sk-team-photo__image">
				<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'sizes' => '(max-width: 1484px) 100vw, 1388px' ) ); ?>
			</div>
		<?php endif; ?>
		<?php if ( $has_text ) : ?>
			<div class="sk-team-photo__text"><?php echo $content; ?></div>
		<?php endif; ?>
	</div>
</section>
