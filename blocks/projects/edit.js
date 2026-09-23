( function ( wp ) {
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var registerBlockType = wp.blocks.registerBlockType;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var SelectControl = wp.components.SelectControl;
	var RangeControl = wp.components.RangeControl;
	var ServerSideRender = wp.serverSideRender;

	registerBlockType( 'sk/projects', {
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
							label: 'Ansicht',
							value: attributes.mode,
							options: [
								{ label: 'Vorschau-Grid (Teaser)', value: 'teaser' },
								{ label: 'Vollständige Liste mit Details', value: 'list' },
							],
							onChange: function ( value ) { setAttributes( { mode: value } ); },
						} ),
						el( RangeControl, {
							label: attributes.mode === 'list' ? 'Anzahl (0 = alle)' : 'Anzahl Projekte',
							value: attributes.limit,
							min: attributes.mode === 'list' ? 0 : 1,
							max: 12,
							onChange: function ( value ) { setAttributes( { limit: value } ); },
						} )
					)
				),
				el( ServerSideRender, { block: 'sk/projects', attributes: attributes } )
			);
		},
	} );
} )( window.wp );
