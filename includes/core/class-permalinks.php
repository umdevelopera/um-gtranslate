<?php
namespace um_ext\um_gtranslate\core;

defined( 'ABSPATH' ) || exit;

/**
 * Localize links.
 *
 * Get an instance this way: UM()->GTranslate()->core()->permalinks()
 *
 * @package um_ext\um_gtranslate\core
 */
class Permalinks {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		
		// Links in emails.
		add_action( 'um_before_email_notification_sending', array( $this, 'before_email' ), 20 );
	}


	/**
	 * Before email notification sending.
	 */
	public function before_email() {

		// Localize {account_activation_link}.
		add_filter( 'um_activate_url', array( $this, 'localize_activate_url' ), 10, 1 );

		// Localize {password_reset_link}.
		add_filter( 'um_get_core_page_filter', array( $this, 'localize_reset_url' ), 10, 3 );
	}


	/**
	 * Localize account activation link - {account_activation_link}.
	 * Hook: um_activate_url
	 *
	 * @see \um\core\Permalinks
	 *
	 * @since 1.0.0
	 *
	 * @param string $url Account activation link.
	 * @return string Localized account activation link.
	 */
	public function localize_activate_url( $url ) {
		if ( ! UM()->GTranslate()->is_default() ) {
			$url = add_query_arg( 'lang', UM()->GTranslate()->get_current(), $url );
		}
		return $url;
	}


	/**
	 * Localize password reset link - {password_reset_link}.
	 * Hook: um_get_core_page_filter
	 *
	 * @see \um\core\Password
	 *
	 * @since 1.0.0
	 *
	 * @param string $url     UM Page URL.
	 * @param string $slug    UM Page slug.
	 * @param bool   $updated Additional parameter.
	 * @return string Password reset page URL.
	 */
	public function localize_reset_url( $url, $slug, $updated ) {
		if ( 'password-reset' === $slug && false === $updated ) {
			if ( ! UM()->GTranslate()->is_default() ) {
				$url = add_query_arg( 'lang', UM()->GTranslate()->get_current(), $url );
			}
		}
		return $url;
	}

}
