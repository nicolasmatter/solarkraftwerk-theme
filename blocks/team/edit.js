( function ( wp ) {
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var registerBlockType = wp.blocks.registerBlockType;
	var be = wp.blockEditor;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var ServerSideRender = wp.serverSideRender;
	var sk = window.skEditor;

	registerBlockType( 'sk/team', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = be.useBlockProps( { className: 'sk-section sk-block-section' } );

			return el( Fragment, {},
				el( be.InspectorControls, {},
					el( PanelBody, { title: 'Einstellungen' },
						el( SelectControl, {
							label: 'Gruppe',
							value: attributes.group,
							options: [
								{ label: 'Team', value: 'team' },
								{ label: 'Partner', value: 'partner' },
							],
							help: 'Personen und ihre Reihenfolge werden unter „Über Uns“ im Menü verwaltet.',
							onChange: function ( value ) { setAttributes( { group: value } ); },
							__nextHasNoMarginBottom: true,
						} )
					)
				),
				el( 'section', blockProps,
					el( 'div', { className: 'sk-container' },
						sk.heading( attributes, setAttributes ),
						el( ServerSideRender, {
							block: 'sk/team',
							attributes: { group: attributes.group, editorPreview: true },
						} )
					)
				)
			);
		},
	} );
} )( window.wp );
