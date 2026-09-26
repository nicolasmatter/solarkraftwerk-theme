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

	registerBlockType( 'sk/projects', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var isList = attributes.mode === 'list';
			var blockProps = be.useBlockProps( { className: 'sk-section sk-block-section' } );

			var controls = [
				el( SelectControl, {
					key: 'mode',
					label: 'Ansicht',
					value: attributes.mode,
					options: [
						{ label: 'Vorschau-Grid (Teaser)', value: 'teaser' },
						{ label: 'Vollständige Liste mit Details', value: 'list' },
					],
					onChange: function ( value ) { setAttributes( { mode: value } ); },
					__nextHasNoMarginBottom: true,
				} ),
			];

			if ( ! isList ) {
				controls.push( el( sk.PostPicker, {
					key: 'ids',
					postType: 'project',
					label: 'Projekte auswählen',
					help: 'Ohne Auswahl werden die ersten Projekte gemäss „Reihenfolge“ gezeigt.',
					value: attributes.ids,
					onChange: function ( value ) { setAttributes( { ids: value } ); },
				} ) );
			}

			if ( isList || ! attributes.ids.length ) {
				controls.push( el( RangeControl, {
					key: 'limit',
					label: isList ? 'Anzahl (0 = alle)' : 'Anzahl Projekte',
					value: attributes.limit,
					min: isList ? 0 : 1,
					max: 12,
					onChange: function ( value ) { setAttributes( { limit: value } ); },
					__nextHasNoMarginBottom: true,
				} ) );
			}

			if ( ! isList ) {
				controls.push( sk.linkUrlControl( attributes, setAttributes, 'Seite mit der Projektliste. Leer lassen für „Referenzen“.' ) );
			}

			return el( Fragment, {},
				el( be.InspectorControls, {}, el( PanelBody, { title: 'Einstellungen' }, controls ) ),
				el( 'section', blockProps,
					el( 'div', { className: 'sk-container' },
						sk.heading( attributes, setAttributes ),
						el( ServerSideRender, {
							block: 'sk/projects',
							attributes: { mode: attributes.mode, limit: attributes.limit, ids: attributes.ids, linkUrl: attributes.linkUrl, editorPreview: true },
						} ),
						isList ? null : sk.moreLink( attributes, setAttributes )
					)
				)
			);
		},
	} );
} )( window.wp );
