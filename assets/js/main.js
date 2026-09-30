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

// Scroll reveal (styles under "Motion" in main.css): mark blocks as they enter the viewport.
( function () {
	if ( ! document.documentElement.classList.contains( 'sk-motion' ) ) return;

	// Keep in sync with the scroll-reveal selector list in main.css.
	var REVEAL = '.sk-section h2, .sk-back-link, .sk-product__main, .sk-product__content > *, .sk-grid > *, .sk-project-row, .sk-team-member, .sk-link';
	var targets = document.querySelectorAll( REVEAL + ', .sk-specs' );

	if ( ! ( 'IntersectionObserver' in window ) ) {
		targets.forEach( function ( el ) { el.classList.add( 'is-in' ); } );
		return;
	}

	var observer = new IntersectionObserver( function ( entries ) {
		entries.forEach( function ( entry ) {
			if ( ! entry.isIntersecting ) return;
			entry.target.classList.add( 'is-in' );
			observer.unobserve( entry.target );
		} );
	}, { rootMargin: '0px 0px -10% 0px' } );

	targets.forEach( function ( el ) { observer.observe( el ); } );
} )();
