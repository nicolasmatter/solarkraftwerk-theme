// Drag-and-drop ordering of the wp-admin list (inc/admin-order.php): saves the rows'
// new order as "Reihenfolge" right after each drop.
( function ( $ ) {
	var $list = $( '#the-list' );
	if ( ! $list.length || ! $list.children( 'tr[id^="post-"]' ).length ) return;

	var $wrap = $( '.wp-list-table' ).addClass( 'sk-sortable' );

	$list.sortable( {
		items: '> tr[id^="post-"]',
		cancel: 'input, button, a, .row-actions, .inline-editor',
		axis: 'y',
		cursor: 'grabbing',
		placeholder: 'sk-order-placeholder',
		// Keep the cells' widths while a row is lifted out of the table.
		helper: function ( event, $row ) {
			$row.children().each( function () {
				$( this ).width( $( this ).width() );
			} );
			return $row;
		},
		start: function ( event, ui ) {
			ui.placeholder.html( '<td colspan="' + ui.item.children( ':visible' ).length + '">&nbsp;</td>' );
		},
		stop: function ( event, ui ) {
			ui.item.children().css( 'width', '' );
		},
		update: function () {
			var ids = $list.children( 'tr[id^="post-"]' ).map( function () {
				return this.id.replace( 'post-', '' );
			} ).get();

			$wrap.addClass( 'is-saving' );
			$.post( window.ajaxurl, { action: 'sk_save_order', nonce: skOrder.nonce, offset: skOrder.offset, ids: ids } )
				.done( function ( response ) {
					if ( ! response || ! response.success ) {
						window.alert( skOrder.error );
					}
				} )
				.fail( function () {
					window.alert( skOrder.error );
				} )
				.always( function () {
					$wrap.removeClass( 'is-saving' );
				} );
		},
	} );
} )( jQuery );
