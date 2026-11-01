( function ( wp ) {
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var registerBlockType = wp.blocks.registerBlockType;
	var be = wp.blockEditor;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var TextControl = wp.components.TextControl;
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
						{ label: 'Vollständige Übersicht', value: 'catalog' },
					],
					help: isTeaser ? 'Zeigt alle Produkte; ein Klick führt zum Produkt in der Übersicht.' : null,
					onChange: function ( value ) { setAttributes( { mode: value } ); },
					__nextHasNoMarginBottom: true,
				} ),
			];

			if ( isTeaser ) {
				controls.push( sk.linkUrlControl( attributes, setAttributes, 'Leer lassen für die Seite „Produkte“.' ) );
			} else {
				controls.push(
					el( TextControl, {
						key: 'productsHeading',
						label: 'Überschrift Produkte',
						value: attributes.productsHeading,
						onChange: function ( value ) { setAttributes( { productsHeading: value } ); },
						__nextHasNoMarginBottom: true,
					} ),
					el( TextControl, {
						key: 'servicesHeading',
						label: 'Überschrift Services',
						value: attributes.servicesHeading,
						onChange: function ( value ) { setAttributes( { servicesHeading: value } ); },
						__nextHasNoMarginBottom: true,
					} )
				);
			}

			return el( Fragment, {},
				el( be.InspectorControls, {}, el( PanelBody, { title: 'Einstellungen' }, controls ) ),
				el( 'section', blockProps,
					el( 'div', { className: 'sk-container' },
						isTeaser ? sk.heading( attributes, setAttributes ) : null,
						el( ServerSideRender, {
							block: 'sk/products',
							attributes: {
								mode: attributes.mode,
								productsHeading: attributes.productsHeading,
								servicesHeading: attributes.servicesHeading,
								editorPreview: true,
							},
						} ),
						isTeaser ? sk.moreLink( attributes, setAttributes ) : null
					)
				)
			);
		},
	} );
} )( window.wp );
