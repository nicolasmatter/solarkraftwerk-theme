document.addEventListener( 'DOMContentLoaded', function () {
	var toggle = document.getElementById( 'sk-nav-toggle' );
	var nav = document.getElementById( 'sk-primary-menu' );
	if ( ! toggle || ! nav ) return;

	toggle.addEventListener( 'click', function () {
		var isOpen = nav.classList.toggle( 'is-open' );
		toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
	} );
} );

// Swap the hero's offer button for the header one as soon as the visitor scrolls.
( function () {
	var root = document.documentElement;
	var ticking = false;

	function update() {
		root.classList.toggle( 'sk-scrolled', window.scrollY > 40 );
		ticking = false;
	}

	window.addEventListener( 'scroll', function () {
		if ( ticking ) return;
		ticking = true;
		window.requestAnimationFrame( update );
	}, { passive: true } );

	// Pages reloaded mid-scroll start in the scrolled state.
	update();
} )();
