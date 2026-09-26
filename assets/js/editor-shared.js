/**
 * Editor helpers shared by the Solar Kraftwerk section blocks.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var RichText = wp.blockEditor.RichText;
	var URLInput = wp.blockEditor.URLInput;
	var CheckboxControl = wp.components.CheckboxControl;
	var BaseControl = wp.components.BaseControl;
	var Spinner = wp.components.Spinner;
	var useSelect = wp.data.useSelect;
	var decodeEntities = wp.htmlEntities.decodeEntities;

	function heading( attributes, setAttributes, key, tagName, placeholder ) {
		key = key || 'heading';
		return el( RichText, {
			tagName: tagName || 'h2',
			value: attributes[ key ],
			allowedFormats: [],
			placeholder: placeholder || 'Überschrift (optional)',
			onChange: function ( value ) {
				var next = {};
				next[ key ] = value;
				setAttributes( next );
			},
		} );
	}

	function moreLink( attributes, setAttributes ) {
		return el( RichText, {
			tagName: 'span',
			className: 'sk-link',
			value: attributes.linkText,
			allowedFormats: [],
			placeholder: 'Linktext (leer = kein Link)',
			onChange: function ( value ) { setAttributes( { linkText: value } ); },
		} );
	}

	function linkUrlControl( attributes, setAttributes, help ) {
		return el( BaseControl, { key: 'linkUrl', label: 'Link-Ziel', help: help, __nextHasNoMarginBottom: true },
			el( URLInput, {
				value: attributes.linkUrl,
				onChange: function ( value ) { setAttributes( { linkUrl: value } ); },
			} )
		);
	}

	/**
	 * Checkbox list of published posts of a type, in "Reihenfolge" order.
	 */
	function PostPicker( props ) {
		var records = useSelect( function ( select ) {
			return select( 'core' ).getEntityRecords( 'postType', props.postType, {
				per_page: -1,
				status: 'publish',
				orderby: 'menu_order',
				order: 'asc',
				_fields: 'id,title',
			} );
		}, [ props.postType ] );
		var value = props.value || [];

		var body;
		if ( ! records ) {
			body = el( Spinner );
		} else if ( ! records.length ) {
			body = el( 'p', {}, 'Noch keine Einträge veröffentlicht.' );
		} else {
			body = records.map( function ( record ) {
				return el( CheckboxControl, {
					key: record.id,
					label: decodeEntities( record.title.rendered ) || '(ohne Titel)',
					checked: value.indexOf( record.id ) !== -1,
					__nextHasNoMarginBottom: true,
					onChange: function ( checked ) {
						props.onChange( checked
							? value.concat( record.id )
							: value.filter( function ( id ) { return id !== record.id; } ) );
					},
				} );
			} );
		}

		return el( BaseControl, { label: props.label, help: props.help, __nextHasNoMarginBottom: true }, body );
	}

	window.skEditor = {
		data: window.skEditorData || {},
		heading: heading,
		moreLink: moreLink,
		linkUrlControl: linkUrlControl,
		PostPicker: PostPicker,
	};
} )( window.wp );
