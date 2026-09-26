( function ( wp ) {
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var registerBlockType = wp.blocks.registerBlockType;
	var be = wp.blockEditor;
	var PanelBody = wp.components.PanelBody;
	var ExternalLink = wp.components.ExternalLink;
	var sk = window.skEditor;
	var contact = sk.data.contact;

	function detail( label, lines ) {
		var children = [ el( 'strong', { key: 'label' }, label ) ];
		lines.forEach( function ( line, index ) {
			children.push( el( 'br', { key: 'br' + index } ), line );
		} );
		return el( 'p', {}, children );
	}

	function field( label, placeholder, tag ) {
		return el( Fragment, {},
			el( 'label', {}, label ),
			el( tag || 'input', { placeholder: placeholder, disabled: true, rows: tag ? 5 : undefined } )
		);
	}

	registerBlockType( 'sk/contact', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = be.useBlockProps( { className: 'sk-section sk-block-section' } );

			return el( Fragment, {},
				el( be.InspectorControls, {},
					el( PanelBody, { title: 'Kontaktangaben' },
						el( 'p', {}, 'Adresse, Telefon, E-Mail, Öffnungszeiten und Karte gelten für die ganze Website und werden im Customizer gepflegt.' ),
						el( ExternalLink, { href: sk.data.customizeUrl + '?autofocus[section]=sk_contact' }, 'Kontaktangaben bearbeiten' )
					)
				),
				el( 'section', blockProps,
					el( 'div', { className: 'sk-container' },
						sk.heading( attributes, setAttributes ),
						el( 'div', { className: 'sk-grid sk-grid--2 sk-kontakt-grid' },
							el( 'div', {},
								sk.heading( attributes, setAttributes, 'dataHeading', 'h3' ),
								detail( 'Adresse:', [ contact.street + ',', contact.city ] ),
								detail( 'Telefon:', [ contact.phone ] ),
								detail( 'E-Mail:', [ contact.email ] ),
								detail( 'Öffnungszeiten:', [ contact.hours ] ),
								el( 'div', { className: 'sk-map' },
									el( 'span', {}, contact.maps ? 'Google Maps (wird auf der Website angezeigt)' : 'Kartenausschnitt (Google Maps)' )
								)
							),
							el( 'div', {},
								sk.heading( attributes, setAttributes, 'formHeading', 'h3' ),
								el( 'div', { className: 'sk-form' },
									field( 'Name', 'Ihr Name' ),
									field( 'E-Mail', 'ihre@email.ch' ),
									field( 'Telefon', '+41 79 000 00 00' ),
									field( 'Nachricht', 'Ihre Nachricht', 'textarea' ),
									el( be.RichText, {
										tagName: 'span',
										className: 'sk-button',
										value: attributes.submitLabel,
										allowedFormats: [],
										placeholder: 'Absenden',
										onChange: function ( value ) { setAttributes( { submitLabel: value } ); },
									} )
								)
							)
						)
					)
				)
			);
		},
	} );
} )( window.wp );
