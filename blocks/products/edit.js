( function ( wp ) {
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var registerBlockType = wp.blocks.registerBlockType;
	var be = wp.blockEditor;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var RangeControl = wp.components.RangeControl;
	var ServerSideRender = wp.serverSideRender;
	var sk = window.skEditor;

	registerBlockType( 'sk/products', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var isTeaser = attributes.mode !== 'catalog';
			var blockProps = be.useBlockProps( { className: 'sk-section sk-block-section' } );

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
					__nextHasNoMarginBottom: true,
				} ),
			];

			if ( isTeaser ) {
				controls.push(
					el( sk.PostPicker, {
						key: 'ids',
						postType: 'product',
						label: 'Produkte auswählen',
						help: 'Ohne Auswahl werden die ersten Produkte gemäss „Reihenfolge“ gezeigt.',
						value: attributes.ids,
						onChange: function ( value ) { setAttributes( { ids: value } ); },
					} ),
					attributes.ids.length ? null : el( RangeControl, {
						key: 'limit',
						label: 'Anzahl Produkte',
						value: attributes.limit,
						min: 1,
						max: 6,
						onChange: function ( value ) { setAttributes( { limit: value } ); },
						__nextHasNoMarginBottom: true,
					} ),
					sk.linkUrlControl( attributes, setAttributes, 'Leer lassen für die Seite „Produkte“.' )
				);
			}

			return el( Fragment, {},
				el( be.InspectorControls, {}, el( PanelBody, { title: 'Einstellungen' }, controls ) ),
				el( 'section', blockProps,
					el( 'div', { className: 'sk-container' },
						isTeaser ? sk.heading( attributes, setAttributes ) : null,
						el( ServerSideRender, {
							block: 'sk/products',
							attributes: { mode: attributes.mode, limit: attributes.limit, ids: attributes.ids, editorPreview: true },
						} ),
						isTeaser ? sk.moreLink( attributes, setAttributes ) : null
					)
				)
			);
		},
	} );
} )( window.wp );
