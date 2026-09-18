<?php
/**
 * Wholesale application form.
 *
 * The real approval workflow (account roles, price tiers, 1C-side checks) is
 * deliberately out of scope — the project documentation says it is decided
 * later. What lives here is the front-end structure and a clean integration
 * point: every valid submission fires `daimzap_wholesale_application` with the
 * sanitized data, so a plugin can take over persistence and approval without
 * touching the theme.
 *
 * @package DaimZap
 */

defined( 'ABSPATH' ) || exit;

/**
 * Nonce action for the wholesale form.
 */
const DAIMZAP_WHOLESALE_NONCE = 'daimzap_wholesale_application';

/**
 * Fields collected by the wholesale form.
 *
 * @return array[] Keyed by field name.
 */
function daimzap_wholesale_fields() {
	return array(
		'company' => array(
			'label'    => __( 'Название компании или ИП', 'daimzap' ),
			'type'     => 'text',
			'required' => true,
			'autocomplete' => 'organization',
		),
		'name'    => array(
			'label'    => __( 'Контактное лицо', 'daimzap' ),
			'type'     => 'text',
			'required' => true,
			'autocomplete' => 'name',
		),
		'phone'   => array(
			'label'    => __( 'Телефон', 'daimzap' ),
			'type'     => 'tel',
			'required' => true,
			'autocomplete' => 'tel',
		),
		'email'   => array(
			'label'    => __( 'E-mail', 'daimzap' ),
			'type'     => 'email',
			'required' => true,
			'autocomplete' => 'email',
		),
		'city'    => array(
			'label'    => __( 'Город', 'daimzap' ),
			'type'     => 'text',
			'required' => false,
			'autocomplete' => 'address-level2',
		),
		'message' => array(
			'label'    => __( 'Какие запчасти вас интересуют', 'daimzap' ),
			'type'     => 'textarea',
			'required' => false,
			'autocomplete' => '',
		),
	);
}

/**
 * Handle a wholesale application submission.
 *
 * @return void
 */
function daimzap_handle_wholesale_application() {
	$redirect = wp_get_referer();

	if ( ! $redirect ) {
		$redirect = daimzap_wholesale_url();
	}

	if ( ! isset( $_POST['daimzap_wholesale_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['daimzap_wholesale_nonce'] ) ), DAIMZAP_WHOLESALE_NONCE ) ) {
		daimzap_wholesale_redirect( $redirect, 'nonce' );
	}

	// Honeypot: real people leave this hidden field empty.
	if ( ! empty( $_POST['daimzap_website'] ) ) {
		daimzap_wholesale_redirect( $redirect, 'sent' );
	}

	if ( daimzap_wholesale_is_throttled() ) {
		daimzap_wholesale_redirect( $redirect, 'throttled' );
	}

	$data   = array();
	$errors = array();

	foreach ( daimzap_wholesale_fields() as $key => $field ) {
		$raw = isset( $_POST[ 'daimzap_' . $key ] ) ? wp_unslash( $_POST[ 'daimzap_' . $key ] ) : '';

		if ( 'email' === $field['type'] ) {
			$value = sanitize_email( $raw );

			if ( $value && ! is_email( $value ) ) {
				$errors[] = $key;
				$value    = '';
			}
		} elseif ( 'textarea' === $field['type'] ) {
			$value = sanitize_textarea_field( $raw );
		} else {
			$value = sanitize_text_field( $raw );
		}

		if ( $field['required'] && '' === $value ) {
			$errors[] = $key;
		}

		$data[ $key ] = $value;
	}

	if ( $errors ) {
		daimzap_wholesale_redirect( $redirect, 'invalid' );
	}

	daimzap_wholesale_throttle();

	/**
	 * Fires when a wholesale application passes validation.
	 *
	 * Integration point for the eventual approval workflow: a plugin can create
	 * the customer account, store the application as a CRM record, or push it to
	 * 1C from here.
	 *
	 * @param array $data Sanitized application data.
	 */
	do_action( 'daimzap_wholesale_application', $data );

	daimzap_notify_wholesale_application( $data );
	daimzap_wholesale_redirect( $redirect, 'sent' );
}
add_action( 'admin_post_daimzap_wholesale', 'daimzap_handle_wholesale_application' );
add_action( 'admin_post_nopriv_daimzap_wholesale', 'daimzap_handle_wholesale_application' );

/**
 * Redirect back to the form with a status flag and stop.
 *
 * @param string $redirect Destination URL.
 * @param string $status   Status key.
 * @return void
 */
function daimzap_wholesale_redirect( $redirect, $status ) {
	wp_safe_redirect( add_query_arg( 'wholesale', rawurlencode( $status ), remove_query_arg( 'wholesale', $redirect ) ) . '#wholesale-form' );
	exit;
}

/**
 * Whether this visitor submitted the form very recently.
 *
 * @return bool
 */
function daimzap_wholesale_is_throttled() {
	return (bool) get_transient( daimzap_wholesale_throttle_key() );
}

/**
 * Mark this visitor as having just submitted the form.
 *
 * @return void
 */
function daimzap_wholesale_throttle() {
	set_transient( daimzap_wholesale_throttle_key(), 1, MINUTE_IN_SECONDS );
}

/**
 * Throttle transient key for the current visitor.
 *
 * @return string
 */
function daimzap_wholesale_throttle_key() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';

	return 'daimzap_wholesale_' . md5( $ip );
}

/**
 * Email the store about a new wholesale application.
 *
 * @param array $data Sanitized application data.
 * @return void
 */
function daimzap_notify_wholesale_application( $data ) {
	$to = apply_filters( 'daimzap_wholesale_notification_email', get_option( 'admin_email' ), $data );

	if ( ! $to || ! is_email( $to ) ) {
		return;
	}

	$lines = array();

	foreach ( daimzap_wholesale_fields() as $key => $field ) {
		if ( ! empty( $data[ $key ] ) ) {
			$lines[] = $field['label'] . ': ' . $data[ $key ];
		}
	}

	$subject = sprintf(
		/* translators: %s: site name. */
		__( '[%s] Новая заявка на оптовое сотрудничество', 'daimzap' ),
		wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES )
	);

	wp_mail( $to, $subject, implode( "\n", $lines ) );
}

/**
 * Status of the last wholesale submission for the current request.
 *
 * @return string One of `sent`, `invalid`, `throttled`, `nonce` or an empty string.
 */
function daimzap_wholesale_status() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display-only status flag; the submission itself is nonce-checked.
	$status = isset( $_GET['wholesale'] ) ? sanitize_key( wp_unslash( $_GET['wholesale'] ) ) : '';

	return in_array( $status, array( 'sent', 'invalid', 'throttled', 'nonce' ), true ) ? $status : '';
}
