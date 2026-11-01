( function ( wp ) {
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var registerBlockType = wp.blocks.registerBlockType;
	var be = wp.blockEditor;
	var PanelBody = wp.components.PanelBody;
	var ToolbarButton = wp.components.ToolbarButton;
	var Button = wp.components.Button;
	var useSelect = wp.data.useSelect;
	var sk = window.skEditor;

	var TEMPLATE = [
		[ 'core/paragraph', { placeholder: 'Text unter dem Foto – frei formatierbar …' } ],
	];

	registerBlockType( 'sk/team-photo', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;

			var media = useSelect( function ( select ) {
				return attributes.imageId ? select( 'core' ).getMedia( attributes.imageId ) : null;
			}, [ attributes.imageId ] );

			var blockProps = be.useBlockProps( { className: 'sk-section sk-block-section sk-team-photo' } );

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

			var image = attributes.imageId
				? el( 'div', { className: 'sk-team-photo__image' },
					media ? el( 'img', { src: media.source_url, alt: media.alt_text || '' } ) : null
				)
				: el( be.MediaPlaceholder, {
					icon: 'format-image',
					labels: { title: 'Foto', instructions: 'Breites Bild, z. B. ein Teamfoto.' },
					accept: 'image/*',
					allowedTypes: [ 'image' ],
					onSelect: function ( image ) { setAttributes( { imageId: image.id } ); },
				} );

			return el( Fragment, {},
				attributes.imageId ? el( be.BlockControls, { group: 'other' },
					imagePicker( function ( picker ) {
						return el( ToolbarButton, { icon: 'format-image', onClick: picker.open }, 'Bild ändern' );
					} )
				) : null,
				el( be.InspectorControls, {},
					attributes.imageId ? el( PanelBody, { title: 'Foto' },
						el( Button, {
							variant: 'tertiary',
							isDestructive: true,
							onClick: function () { setAttributes( { imageId: 0 } ); },
						}, 'Bild entfernen' )
					) : null,
					el( PanelBody, { title: 'Link' },
						sk.linkUrlControl( attributes, setAttributes, 'Wohin Foto und Link führen. Leer lassen für „Über uns“. Ohne Linktext gibt es keinen Link.' )
					)
				),
				el( 'section', blockProps,
					el( 'div', { className: 'sk-container' },
						image,
						el( 'div', { className: 'sk-team-photo__text' },
							el( be.InnerBlocks, { template: TEMPLATE } )
						),
						el( 'div', { className: 'sk-team-photo__more' }, sk.moreLink( attributes, setAttributes ) )
					)
				)
			);
		},
		save: function () {
			return el( be.InnerBlocks.Content );
		},
		variations: [
			{
				name: 'team-teaser',
				title: 'Team-Teaser',
				description: 'Teamfoto mit Text und einem Link zur Team-Seite, z. B. für die Startseite.',
				icon: 'groups',
				attributes: { linkText: 'Mehr über uns' },
				scope: [ 'inserter' ],
			},
		],
	} );
} )( window.wp );
