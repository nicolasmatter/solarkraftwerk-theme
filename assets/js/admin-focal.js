// Focus point picker in the media library (inc/focal-point.php). Changing the hidden
// input saves it: the media modal saves its fields on "change", the attachment edit
// screen with the form.
( function ( $ ) {
	function set( $field, value, x, y ) {
		var $dot = $field.find( '.sk-focal__dot' );
		if ( value ) {
			$dot.css( { left: x + '%', top: y + '%' } ).prop( 'hidden', false );
		} else {
			$dot.prop( 'hidden', true );
		}
		$field.find( '.sk-focal__input' ).val( value ).trigger( 'change' );
	}

	$( document ).on( 'click', '.sk-focal__frame', function ( event ) {
		var rect = this.getBoundingClientRect();
		var x = Math.round( Math.min( 100, Math.max( 0, ( event.clientX - rect.left ) / rect.width * 100 ) ) );
		var y = Math.round( Math.min( 100, Math.max( 0, ( event.clientY - rect.top ) / rect.height * 100 ) ) );
		set( $( this ).closest( '.sk-focal' ), x + ',' + y, x, y );
	} );

	$( document ).on( 'click', '.sk-focal__reset', function () {
		set( $( this ).closest( '.sk-focal' ), '' );
	} );
} )( jQuery );
