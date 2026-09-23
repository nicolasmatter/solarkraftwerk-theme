( function ( wp ) {
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var registerBlockType = wp.blocks.registerBlockType;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var ServerSideRender = wp.serverSideRender;

	registerBlockType( 'sk/contact', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;

			return el( Fragment, {},
				el( InspectorControls, {},
					el( PanelBody, { title: 'Einstellungen' },
						el( TextControl, {
							label: 'Überschrift',
							value: attributes.heading,
							onChange: function ( value ) { setAttributes( { heading: value } ); },
						} ),
						el( TextControl, {
							label: 'Überschrift Kontaktdaten',
							value: attributes.dataHeading,
							onChange: function ( value ) { setAttributes( { dataHeading: value } ); },
						} ),
						el( TextControl, {
							label: 'Überschrift Formular',
							value: attributes.formHeading,
							onChange: function ( value ) { setAttributes( { formHeading: value } ); },
						} )
					)
				),
				el( ServerSideRender, { block: 'sk/contact', attributes: attributes } )
			);
		},
	} );
} )( window.wp );
