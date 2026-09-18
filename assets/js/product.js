/**
 * Single product page: gallery switching and the quantity stepper.
 *
 * The gallery renders every image in the markup and hides all but the active
 * one, so without JavaScript the first image still shows and the thumbnails
 * remain plain links to the full-size files.
 */
( function () {
	'use strict';

	/**
	 * Thumbnail-driven gallery.
	 */
	function initGallery() {
		var gallery = document.querySelector( '[data-dz-gallery]' );

		if ( ! gallery ) {
			return;
		}

		var thumbs = gallery.querySelectorAll( '[data-dz-gallery-thumb]' );
		var slides = gallery.querySelectorAll( '[data-dz-gallery-slide]' );

		if ( thumbs.length < 2 ) {
			return;
		}

		function activate( index ) {
			Array.prototype.forEach.call( slides, function ( slide, i ) {
				slide.hidden = i !== index;
			} );

			Array.prototype.forEach.call( thumbs, function ( thumb, i ) {
				thumb.setAttribute( 'aria-current', i === index ? 'true' : 'false' );
			} );
		}

		Array.prototype.forEach.call( thumbs, function ( thumb, index ) {
			thumb.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				activate( index );
			} );
		} );
	}

	/**
	 * Plus/minus buttons around the WooCommerce quantity input.
	 *
	 * The buttons are injected here rather than rendered server-side: they are
	 * useless without JavaScript, and the native number input already works.
	 */
	function initQuantity() {
		var wrappers = document.querySelectorAll( '.dz-product__summary .quantity' );

		Array.prototype.forEach.call( wrappers, function ( wrapper ) {
			var input = wrapper.querySelector( 'input.qty' );

			if ( ! input || wrapper.classList.contains( 'dz-qty' ) ) {
				return;
			}

			wrapper.classList.add( 'dz-qty' );

			var minus = document.createElement( 'button' );
			minus.type = 'button';
			minus.className = 'dz-qty__btn';
			minus.textContent = '−';
			minus.setAttribute( 'aria-label', 'Уменьшить количество' );

			var plus = document.createElement( 'button' );
			plus.type = 'button';
			plus.className = 'dz-qty__btn';
			plus.textContent = '+';
			plus.setAttribute( 'aria-label', 'Увеличить количество' );

			wrapper.insertBefore( minus, input );
			wrapper.appendChild( plus );

			function step( direction ) {
				var stepValue = parseFloat( input.getAttribute( 'step' ) ) || 1;
				var min = parseFloat( input.getAttribute( 'min' ) );
				var max = parseFloat( input.getAttribute( 'max' ) );
				var value = parseFloat( input.value ) || 0;
				var next = value + direction * stepValue;

				if ( ! isNaN( min ) && next < min ) {
					next = min;
				}

				if ( ! isNaN( max ) && max > 0 && next > max ) {
					next = max;
				}

				input.value = next;
				input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			}

			minus.addEventListener( 'click', function () {
				step( -1 );
			} );

			plus.addEventListener( 'click', function () {
				step( 1 );
			} );
		} );
	}

	/**
	 * Tab panels below the product summary.
	 */
	function initTabs() {
		var container = document.querySelector( '[data-dz-tabs]' );

		if ( ! container ) {
			return;
		}

		var tabs = container.querySelectorAll( '[role="tab"]' );

		if ( tabs.length < 2 ) {
			return;
		}

		function select( index ) {
			Array.prototype.forEach.call( tabs, function ( tab, i ) {
				var panel = document.getElementById( tab.getAttribute( 'aria-controls' ) );
				var selected = i === index;

				tab.setAttribute( 'aria-selected', selected ? 'true' : 'false' );
				tab.setAttribute( 'tabindex', selected ? '0' : '-1' );

				if ( panel ) {
					panel.hidden = ! selected;
				}
			} );
		}

		Array.prototype.forEach.call( tabs, function ( tab, index ) {
			tab.addEventListener( 'click', function () {
				select( index );
			} );

			tab.addEventListener( 'keydown', function ( event ) {
				var next = null;

				if ( 'ArrowRight' === event.key ) {
					next = ( index + 1 ) % tabs.length;
				} else if ( 'ArrowLeft' === event.key ) {
					next = ( index - 1 + tabs.length ) % tabs.length;
				}

				if ( null !== next ) {
					event.preventDefault();
					select( next );
					tabs[ next ].focus();
				}
			} );
		} );

		select( 0 );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		initGallery();
		initQuantity();
		initTabs();
	} );
}() );
