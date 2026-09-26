( function ( wp ) {
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var registerBlockType = wp.blocks.registerBlockType;
	var be = wp.blockEditor;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var ToggleControl = wp.components.ToggleControl;
	var ToolbarButton = wp.components.ToolbarButton;
	var Button = wp.components.Button;
	var useSelect = wp.data.useSelect;
	var data = window.skEditor.data;

	registerBlockType( 'sk/hero', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;

			var media = useSelect( function ( select ) {
				return attributes.imageId ? select( 'core' ).getMedia( attributes.imageId ) : null;
			}, [ attributes.imageId ] );

			var imageUrl = data.heroFallback;
			if ( media ) {
				var sizes = media.media_details && media.media_details.sizes;
				imageUrl = ( sizes && sizes[ 'sk-hero' ] && sizes[ 'sk-hero' ].source_url ) || media.source_url;
			}

			var blockProps = be.useBlockProps( {
				className: 'sk-hero' + ( attributes.size === 'large' ? ' sk-hero--large' : '' ),
				style: { backgroundImage: 'url(' + imageUrl + ')' },
			} );

			function imagePicker( render ) {
				return el( be.MediaUploadCheck, {},
					el( be.MediaUpload, {
						allowedTypes: [ 'image' ],
						value: attributes.imageId,
						onSelect: function ( image ) { setAttributes( { imageId: image.id } ); },
						render: render,
					} )
				);
			}

			return el( Fragment, {},
				el( be.BlockControls, { group: 'other' },
					imagePicker( function ( picker ) {
						return el( ToolbarButton, { icon: 'format-image', onClick: picker.open }, 'Bild ändern' );
					} )
				),
				el( be.InspectorControls, {},
					el( PanelBody, { title: 'Einstellungen' },
						el( SelectControl, {
							label: 'Höhe',
							value: attributes.size,
							options: [
								{ label: 'Standard (Unterseiten)', value: 'default' },
								{ label: 'Gross (Startseite)', value: 'large' },
							],
							onChange: function ( value ) { setAttributes( { size: value } ); },
							__nextHasNoMarginBottom: true,
						} ),
						el( ToggleControl, {
							label: 'Offerte-Button anzeigen',
							checked: attributes.showCta,
							onChange: function ( value ) { setAttributes( { showCta: value } ); },
							__nextHasNoMarginBottom: true,
						} ),
						el( 'div', { style: { display: 'flex', gap: '8px', flexWrap: 'wrap' } },
							imagePicker( function ( picker ) {
								return el( Button, { variant: 'secondary', onClick: picker.open }, 'Bild wählen' );
							} ),
							attributes.imageId ? el( Button, {
								variant: 'tertiary',
								isDestructive: true,
								onClick: function () { setAttributes( { imageId: 0 } ); },
							}, 'Standardbild verwenden' ) : null
						)
					)
				),
				el( 'section', blockProps,
					el( 'div', { className: 'sk-hero__overlay' },
						el( 'div', { className: 'sk-hero__content' },
							window.skEditor.heading( attributes, setAttributes, 'title', 'h1', 'Seitentitel (optional)' ),
							window.skEditor.heading( attributes, setAttributes, 'subtitle', 'p', 'Untertitel (optional)' )
						),
						attributes.showCta ? el( 'span', { className: 'sk-cta' },
							el( 'span', { className: 'sk-cta__title' }, data.cta.title ),
							el( 'span', { className: 'sk-cta__subtitle' }, data.cta.subtitle )
						) : null
					)
				)
			);
		},
	} );
} )( window.wp );
