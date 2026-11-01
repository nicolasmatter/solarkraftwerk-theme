// Contact map (sk_map() in inc/map.php): OpenStreetMap tiles via Leaflet, tinted in
// main.css, with the logo's four cells as the marker.
( function ( L ) {
	if ( ! L ) return;

	var MARKER = '<svg viewBox="0 0 40 52" width="40" height="52" aria-hidden="true">'
		+ '<path d="M20 51C20 51 38 32.5 38 20A18 18 0 0 0 2 20C2 32.5 20 51 20 51Z" fill="#ff6a00" stroke="#fff" stroke-width="2"/>'
		+ '<rect x="11" y="11" width="8" height="8" rx="2" fill="#fff"/><rect x="21" y="11" width="8" height="8" rx="2" fill="#fff"/>'
		+ '<rect x="11" y="21" width="8" height="8" rx="2" fill="#fff"/><rect x="21" y="21" width="8" height="8" rx="2" fill="#fff"/>'
		+ '</svg>';

	document.querySelectorAll( '.sk-map[data-lat]' ).forEach( function ( el ) {
		var point = [ parseFloat( el.dataset.lat ), parseFloat( el.dataset.lon ) ];

		var map = L.map( el, {
			center: point,
			zoom: 15,
			// Never trap the page's scrolling: no wheel zoom, and no panning on touch screens
			// (the zoom buttons still work there).
			scrollWheelZoom: false,
			dragging: ! L.Browser.mobile,
			tap: false,
		} );

		L.tileLayer( 'https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 19,
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>',
		} ).addTo( map );

		L.marker( point, {
			icon: L.divIcon( { className: 'sk-map__marker', html: MARKER, iconSize: [ 40, 52 ], iconAnchor: [ 20, 51 ] } ),
			keyboard: false,
		} ).addTo( map );

		map.attributionControl.setPrefix( false );
	} );
} )( window.L );
