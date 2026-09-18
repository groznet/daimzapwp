/**
 * Catalog interactions: the mobile filter drawer and auto-submitting controls.
 *
 * Filtering itself is server-side — every filter is a real link or a form
 * submit, so the catalog works fully without this file.
 */
( function () {
	'use strict';

	/**
	 * Turn the sidebar into an off-canvas drawer on small screens.
	 */
	function initFilterDrawer() {
		var sidebar = document.querySelector( '[data-dz-filters]' );
		var overlay = document.querySelector( '[data-dz-overlay]' );
		var openers = document.querySelectorAll( '[data-dz-filters-open]' );
		var closers = document.querySelectorAll( '[data-dz-filters-close]' );

		if ( ! sidebar || ! openers.length ) {
			return;
		}

		function setState( open ) {
			sidebar.classList.toggle( 'is-open', open );

			if ( overlay ) {
				overlay.classList.toggle( 'is-open', open );
			}

			document.body.classList.toggle( 'dz-locked', open );

			Array.prototype.forEach.call( openers, function ( opener ) {
				opener.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			} );
		}

		Array.prototype.forEach.call( openers, function ( opener ) {
			opener.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				setState( ! sidebar.classList.contains( 'is-open' ) );
			} );
		} );

		Array.prototype.forEach.call( closers, function ( closer ) {
			closer.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				setState( false );
			} );
		} );

		if ( overlay ) {
			overlay.addEventListener( 'click', function () {
				setState( false );
			} );
		}

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key ) {
				setState( false );
			}
		} );
	}

	/**
	 * Submit the sorting form as soon as a new option is chosen.
	 *
	 * The form keeps its submit button for the no-JavaScript case; it is hidden
	 * only once this runs.
	 */
	function initAutoSubmit() {
		var forms = document.querySelectorAll( '[data-dz-auto-submit]' );

		Array.prototype.forEach.call( forms, function ( form ) {
			var button = form.querySelector( '[data-dz-submit]' );

			if ( button ) {
				button.hidden = true;
			}

			Array.prototype.forEach.call( form.querySelectorAll( 'select' ), function ( select ) {
				select.addEventListener( 'change', function () {
					form.submit();
				} );
			} );
		} );
	}

	/**
	 * Filter long make/model lists in place.
	 */
	function initFilterSearch() {
		var inputs = document.querySelectorAll( '[data-dz-filter-search]' );

		Array.prototype.forEach.call( inputs, function ( input ) {
			var listId = input.getAttribute( 'data-dz-filter-search' );
			var list = document.getElementById( listId );

			if ( ! list ) {
				return;
			}

			var items = list.querySelectorAll( 'li' );

			input.addEventListener( 'input', function () {
				var needle = input.value.trim().toLowerCase();

				Array.prototype.forEach.call( items, function ( item ) {
					var text = ( item.textContent || '' ).trim().toLowerCase();
					item.hidden = needle.length > 0 && text.indexOf( needle ) === -1;
				} );
			} );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		initFilterDrawer();
		initAutoSubmit();
		initFilterSearch();
	} );
}() );
