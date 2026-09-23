( function ( wp ) {
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var registerBlockType = wp.blocks.registerBlockType;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var SelectControl = wp.components.SelectControl;
	var ServerSideRender = wp.serverSideRender;

	registerBlockType( 'sk/team', {
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
						el( SelectControl, {
							label: 'Gruppe',
							value: attributes.group,
							options: [
								{ label: 'Team', value: 'team' },
								{ label: 'Partner', value: 'partner' },
							],
							onChange: function ( value ) { setAttributes( { group: value } ); },
						} )
					)
				),
				el( ServerSideRender, { block: 'sk/team', attributes: attributes } )
			);
		},
	} );
} )( window.wp );
