document.addEventListener( 'DOMContentLoaded', function () {
	var toggle = document.getElementById( 'sk-nav-toggle' );
	var nav = document.getElementById( 'sk-primary-menu' );
	if ( ! toggle || ! nav ) return;

	toggle.addEventListener( 'click', function () {
		var isOpen = nav.classList.toggle( 'is-open' );
		toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
	} );
} );
