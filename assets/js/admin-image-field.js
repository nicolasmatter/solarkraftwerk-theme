/**
 * Media-library picker for the image fields in the theme's meta boxes (sk_image_field()).
 */
( function ( $ ) {
	$( document ).on( 'click', '.sk-image-field__select', function () {
		var field = $( this ).closest( '.sk-image-field' );
		var frame = wp.media( { title: 'Bild wählen', library: { type: 'image' }, multiple: false } );

		frame.on( 'select', function () {
			var image = frame.state().get( 'selection' ).first().toJSON();
			var preview = ( image.sizes && image.sizes.medium ) || image;
			field.find( 'input' ).val( image.id );
			field.find( '.sk-image-field__preview' ).html( $( '<img>', { src: preview.url, alt: '', style: 'max-width:100%;height:auto;' } ) );
			field.find( '.sk-image-field__remove' ).prop( 'hidden', false );
		} );

		frame.open();
	} );

	$( document ).on( 'click', '.sk-image-field__remove', function () {
		var field = $( this ).closest( '.sk-image-field' );
		field.find( 'input' ).val( '' );
		field.find( '.sk-image-field__preview' ).empty();
		$( this ).prop( 'hidden', true );
	} );
} )( jQuery );
