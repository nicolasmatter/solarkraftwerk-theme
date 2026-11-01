<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Focus point per image ("Fokuspunkt" in the media library). Wherever an image is
 * cropped – the cropped image sizes (sk-card, sk-hero, …) and the CSS "cover" boxes –
 * the crop keeps this point in view instead of the centre.
 *
 * Stored as attachment meta "x,y" in percent of the image; each re-cut image size
 * also remembers where the point lies in its own file (sizes[…]['sk_focal']).
 */

/**
 * The focus point as array( x, y ) in percent, or null if none is set.
 */
function sk_focal_point( $attachment_id ) {
	return sk_focal_parse( get_post_meta( $attachment_id, '_sk_focal', true ) );
}

function sk_focal_parse( $value ) {
	if ( ! is_string( $value ) || ! preg_match( '/^\s*(\d{1,3}(?:\.\d+)?)\s*,\s*(\d{1,3}(?:\.\d+)?)\s*$/', $value, $m ) ) return null;
	return array( min( 100, (float) $m[1] ), min( 100, (float) $m[2] ) );
}

/**
 * CSS position ("30% 20%") of the focus point within the file used for $size, or '' if none is set.
 */
function sk_focal_position( $attachment_id, $size = 'full' ) {
	$point = sk_focal_point( $attachment_id );
	if ( ! $point ) return '';
	if ( is_string( $size ) ) {
		$meta = wp_get_attachment_metadata( $attachment_id );
		if ( isset( $meta['sizes'][ $size ]['sk_focal'] ) ) {
			$point = $meta['sizes'][ $size ]['sk_focal'];
		}
	}
	return round( $point[0], 1 ) . '% ' . round( $point[1], 1 ) . '%';
}

/**
 * Keep the focus point in view in every <img> printed by WordPress.
 */
function sk_focal_image_attributes( $attr, $attachment, $size ) {
	$position = sk_focal_position( $attachment->ID, $size );
	if ( $position ) {
		$attr['style'] = trim( ( isset( $attr['style'] ) ? rtrim( $attr['style'], '; ' ) . '; ' : '' ) . 'object-position: ' . $position . ';' );
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'sk_focal_image_attributes', 10, 3 );

/**
 * When WordPress (re)creates an image's sizes, cut them around its focus point.
 */
function sk_focal_on_generate( $meta, $attachment_id ) {
	$point = sk_focal_point( $attachment_id );
	return $point ? sk_focal_crop_sizes( $meta, $attachment_id, $point ) : $meta;
}
add_filter( 'wp_generate_attachment_metadata', 'sk_focal_on_generate', 10, 2 );

/**
 * Cut the cropped image sizes around $point (WordPress cuts them from the centre).
 * New files get new names, so browsers and caches don't keep showing the old crop.
 */
function sk_focal_crop_sizes( $meta, $attachment_id, $point ) {
	$file = get_attached_file( $attachment_id );
	if ( empty( $meta['sizes'] ) || empty( $meta['width'] ) || ! $file || ! file_exists( $file ) ) return $meta;

	$registered = wp_get_registered_image_subsizes();
	$width      = (int) $meta['width'];
	$height     = (int) $meta['height'];
	$dir        = dirname( $file );
	$info       = pathinfo( $file );

	foreach ( $meta['sizes'] as $name => $size ) {
		if ( empty( $registered[ $name ]['crop'] ) ) continue;
		$w = (int) $size['width'];
		$h = (int) $size['height'];

		// The largest area of the image with the size's proportions, centred on the focus point.
		$scale  = max( $w / $width, $h / $height );
		$crop_w = min( $width, $w / $scale );
		$crop_h = min( $height, $h / $scale );
		$x      = max( 0, min( $width - $crop_w, $point[0] / 100 * $width - $crop_w / 2 ) );
		$y      = max( 0, min( $height - $crop_h, $point[1] / 100 * $height - $crop_h / 2 ) );

		$editor = wp_get_image_editor( $file );
		if ( is_wp_error( $editor ) || is_wp_error( $editor->crop( (int) round( $x ), (int) round( $y ), (int) round( $crop_w ), (int) round( $crop_h ), $w, $h ) ) ) continue;

		$saved = $editor->save( sprintf( '%s/%s-%dx%d-f%d-%d.%s', $dir, $info['filename'], $w, $h, round( $point[0] ), round( $point[1] ), $info['extension'] ) );
		if ( is_wp_error( $saved ) ) continue;

		sk_focal_delete_unused( $meta, $name, $dir );
		$meta['sizes'][ $name ]['file']     = $saved['file'];
		$meta['sizes'][ $name ]['sk_focal'] = array(
			$crop_w < $width ? sk_focal_percent( $point[0] / 100 * $width - $x, $crop_w ) : $point[0],
			$crop_h < $height ? sk_focal_percent( $point[1] / 100 * $height - $y, $crop_h ) : $point[1],
		);
		if ( isset( $saved['filesize'] ) ) {
			$meta['sizes'][ $name ]['filesize'] = $saved['filesize'];
		}
	}
	return $meta;
}

function sk_focal_percent( $offset, $length ) {
	return round( max( 0, min( 100, $offset / $length * 100 ) ), 1 );
}

/**
 * Remove the file a size is replacing, unless another size still uses it.
 */
function sk_focal_delete_unused( $meta, $name, $dir ) {
	$old = $meta['sizes'][ $name ]['file'];
	foreach ( $meta['sizes'] as $other => $size ) {
		if ( $other !== $name && $size['file'] === $old ) return;
	}
	wp_delete_file( $dir . '/' . $old );
}

/**
 * The picker in the media library: click the most important spot of the image.
 */
function sk_focal_field( $fields, $post ) {
	if ( ! wp_attachment_is_image( $post ) ) return $fields;

	$point = sk_focal_point( $post->ID );
	$src   = wp_get_attachment_image_url( $post->ID, 'medium_large' );
	$fields['sk_focal'] = array(
		'label' => 'Fokuspunkt',
		'input' => 'html',
		'html'  => sprintf(
			'<div class="sk-focal"><div class="sk-focal__frame"><img src="%1$s" alt=""><span class="sk-focal__dot"%2$s></span></div>'
			. '<input type="hidden" class="sk-focal__input" name="attachments[%3$d][sk_focal]" value="%4$s">'
			. '<button type="button" class="button-link sk-focal__reset">Zurücksetzen (Mitte)</button></div>',
			esc_url( $src ),
			$point ? sprintf( ' style="left:%s%%;top:%s%%"', $point[0], $point[1] ) : ' hidden',
			$post->ID,
			$point ? esc_attr( $point[0] . ',' . $point[1] ) : ''
		),
		'helps' => 'Auf die wichtigste Stelle im Bild klicken. Wo das Bild zugeschnitten wird, bleibt sie sichtbar.',
	);
	return $fields;
}
add_filter( 'attachment_fields_to_edit', 'sk_focal_field', 10, 2 );

function sk_focal_save( $post, $attachment ) {
	if ( ! isset( $attachment['sk_focal'] ) ) return $post;

	$id    = (int) $post['ID'];
	$point = sk_focal_parse( $attachment['sk_focal'] );
	$value = $point ? round( $point[0] ) . ',' . round( $point[1] ) : '';
	if ( get_post_meta( $id, '_sk_focal', true ) === $value ) return $post;

	if ( $value ) {
		update_post_meta( $id, '_sk_focal', $value );
	} else {
		delete_post_meta( $id, '_sk_focal' );
	}
	// Without a focus point the sizes are cut from the middle again, as WordPress does.
	$meta = sk_focal_crop_sizes( wp_get_attachment_metadata( $id ), $id, $point ? $point : array( 50, 50 ) );
	wp_update_attachment_metadata( $id, $meta );

	return $post;
}
add_filter( 'attachment_fields_to_save', 'sk_focal_save', 10, 2 );

function sk_focal_admin_assets() {
	wp_enqueue_script( 'sk-focal', get_template_directory_uri() . '/assets/js/admin-focal.js', array( 'jquery' ), SK_THEME_VERSION, true );
	wp_register_style( 'sk-focal', false, array(), SK_THEME_VERSION );
	wp_enqueue_style( 'sk-focal' );
	wp_add_inline_style( 'sk-focal', '
		.sk-focal__frame { position: relative; display: inline-block; max-width: 100%; cursor: crosshair; line-height: 0; }
		.sk-focal__frame img { display: block; max-width: 100%; height: auto; }
		.sk-focal__dot { position: absolute; width: 16px; height: 16px; margin: -10px 0 0 -10px; border: 2px solid #fff; border-radius: 50%; background: #ff6a00; box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.5); pointer-events: none; }
		.sk-focal__dot[hidden] { display: none; }
		.sk-focal__reset { margin-top: 6px; }
	' );
}
add_action( 'admin_enqueue_scripts', 'sk_focal_admin_assets' );
