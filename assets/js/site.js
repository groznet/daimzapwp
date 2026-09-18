/**
 * Site chrome: mobile drawer, submenu toggles, mobile search.
 *
 * No dependencies, no framework. Every interactive control already works
 * without JavaScript (submenus open on hover/focus, the drawer menu duplicates
 * links that exist in the footer), so this file only upgrades the experience.
 */
( function () {
	'use strict';

	var body = document.body;

	/**
	 * Open or close an off-canvas panel and its overlay.
	 *
	 * @param {HTMLElement} panel   Panel element.
	 * @param {HTMLElement} overlay Overlay element.
	 * @param {boolean}     open    Desired state.
	 */
	function setPanel( panel, overlay, open ) {
		if ( ! panel ) {
			return;
		}

		panel.classList.toggle( 'is-open', open );
		panel.setAttribute( 'aria-hidden', open ? 'false' : 'true' );

		if ( overlay ) {
			overlay.classList.toggle( 'is-open', open );
		}

		body.classList.toggle( 'dz-locked', open );
	}

	/**
	 * Wire a trigger/panel/close trio together.
	 *
	 * @param {string} triggerSelector Selector for the elements that open the panel.
	 * @param {string} panelSelector   Selector for the panel.
	 * @param {string} closeSelector   Selector for the elements that close the panel.
	 */
	function initPanel( triggerSelector, panelSelector, closeSelector ) {
		var panel = document.querySelector( panelSelector );
		var overlay = document.querySelector( '[data-dz-overlay]' );

		if ( ! panel ) {
			return;
		}

		var triggers = document.querySelectorAll( triggerSelector );
		var closers = panel.querySelectorAll( closeSelector );

		if ( ! triggers.length ) {
			return;
		}

		Array.prototype.forEach.call( triggers, function ( trigger ) {
			trigger.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				var open = ! panel.classList.contains( 'is-open' );
				setPanel( panel, overlay, open );
				trigger.setAttribute( 'aria-expanded', open ? 'true' : 'false' );

				if ( open ) {
					var focusable = panel.querySelector( 'a, button, input' );

					if ( focusable ) {
						focusable.focus();
					}
				}
			} );
		} );

		Array.prototype.forEach.call( closers, function ( closer ) {
			closer.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				setPanel( panel, overlay, false );

				Array.prototype.forEach.call( triggers, function ( trigger ) {
					trigger.setAttribute( 'aria-expanded', 'false' );
				} );

				if ( triggers[ 0 ] ) {
					triggers[ 0 ].focus();
				}
			} );
		} );

		if ( overlay ) {
			overlay.addEventListener( 'click', function () {
				setPanel( panel, overlay, false );

				Array.prototype.forEach.call( triggers, function ( trigger ) {
					trigger.setAttribute( 'aria-expanded', 'false' );
				} );
			} );
		}

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key && panel.classList.contains( 'is-open' ) ) {
				setPanel( panel, overlay, false );

				if ( triggers[ 0 ] ) {
					triggers[ 0 ].focus();
				}
			}
		} );
	}

	/**
	 * Submenu toggles inside the mobile drawer.
	 *
	 * Desktop submenus are handled in CSS; the toggle only takes over where the
	 * menu is rendered as a stacked list.
	 */
	function initSubmenus() {
		var toggles = document.querySelectorAll( '[data-dz-submenu-toggle]' );

		Array.prototype.forEach.call( toggles, function ( toggle ) {
			toggle.addEventListener( 'click', function () {
				var submenu = toggle.parentNode.querySelector( '.dz-menu__submenu' );

				if ( ! submenu ) {
					return;
				}

				var open = 'true' !== toggle.getAttribute( 'aria-expanded' );
				toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
				submenu.classList.toggle( 'is-open', open );
			} );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		initPanel( '[data-dz-menu-open]', '[data-dz-drawer]', '[data-dz-menu-close]' );
		initSubmenus();
	} );
}() );
