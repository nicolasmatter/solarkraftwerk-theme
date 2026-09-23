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

	registerBlockType( 'sk/products', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;

			var controls = [
				el( SelectControl, {
					key: 'mode',
					label: 'Ansicht',
					value: attributes.mode,
					options: [
						{ label: 'Vorschau (Teaser)', value: 'teaser' },
						{ label: 'Vollständiger Katalog', value: 'catalog' },
					],
					onChange: function ( value ) { setAttributes( { mode: value } ); },
				} ),
			];

			if ( attributes.mode === 'teaser' ) {
				controls.push(
					el( TextControl, {
						key: 'heading',
						label: 'Überschrift',
						value: attributes.heading,
						onChange: function ( value ) { setAttributes( { heading: value } ); },
					} ),
					el( RangeControl, {
						key: 'limit',
						label: 'Anzahl Produkte',
						value: attributes.limit,
						min: 1,
						max: 6,
						onChange: function ( value ) { setAttributes( { limit: value } ); },
					} )
				);
			}

			return el( Fragment, {},
				el( InspectorControls, {}, el( PanelBody, { title: 'Einstellungen' }, controls ) ),
				el( ServerSideRender, { block: 'sk/products', attributes: attributes } )
			);
		},
	} );
} )( window.wp );
