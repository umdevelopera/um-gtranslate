<?php
namespace um_ext\um_gtranslate\core;

defined( 'ABSPATH' ) || exit;

/**
 * Localize translated email templates.
 *
 * Get an instance this way: UM()->GTranslate()->core()->mail()
 *
 * @package um_ext\um_gtranslate\core
 */
class Mail {


	/**
	 * Class constructor.
	 */
	public function __construct() {

		// Localize Subject.
		add_filter( 'um_email_send_subject', array( &$this, 'localize_email_subject' ), 10, 2 );

		// Localize Template.
		add_filter( 'um_change_email_template_file', array( &$this, 'change_email_template_file' ), 10, 1 );
		add_filter( 'um_locate_email_template', array( &$this, 'locate_email_template' ), 10, 2 );

		// Set current language.
		add_action( 'um_before_email_notification_sending', array( $this, 'set_user_lang' ), 10, 3 );
	}


	/**
	 * Change email template for searching in the theme folder.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $template The email template slug.
	 * @return string
	 */
	public function change_email_template_file( $template ) {
		if ( ! UM()->GTranslate()->is_default() ) {
			$template = UM()->GTranslate()->get_current( 'locale' ) . '/' . $template;
		}
		return $template;
	}


	/**
	 * Replace email Subject with translated value on email send.
	 *
	 * Example: change 'welcome_email_sub' to 'welcome_email_sub_de_DE'
	 *
	 * @since 1.0.0
	 *
	 * @param  string $subject  Default subject.
	 * @param  string $template Email template slug.
	 * @return string
	 */
	public function localize_email_subject( $subject, $template ) {
		$option_key    = $template . '_sub' . ( UM()->GTranslate()->is_default() ? '' : '_' . UM()->GTranslate()->get_current( 'locale' ) );
		$value         = UM()->options()->get( $option_key );
		$value_default = UM()->options()->get( $template . '_sub' );
		return empty( $value ) ? $value_default : $value;
	}


	/**
	 * Change email template path.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $template      The email template path.
	 * @param  string $template_name The email template slug.
	 * @return string
	 */
	public function locate_email_template( $template, $template_name ) {
		$blog_id = is_multisite() ? trailingslashit( get_current_blog_id() ) : '';
		$locale  = UM()->GTranslate()->is_default() ? '' : trailingslashit( UM()->GTranslate()->get_current( 'locale' ) );

		// check if there is a template in the theme folder.
		$template = locate_template(
			array(
				trailingslashit( 'ultimate-member/email' ) . $blog_id . $locale . $template_name . '.php',
				trailingslashit( 'ultimate-member/email' ) . $blog_id . $template_name . '.php',
				trailingslashit( 'ultimate-member/email' ) . $template_name . '.php',
			)
		);

		// if there isn't template at theme folder get template file from plugin dir.
		if ( ! $template ) {
			$path     = empty( UM()->mail()->path_by_slug[ $template_name ] ) ? um_path . 'templates/email' : UM()->mail()->path_by_slug[ $template_name ];
			$template = trailingslashit( $path ) . $template_name . '.php';
		}

		return wp_normalize_path( $template );
	}


	/**
	 * Set current language.
	 *
	 * @since 1.0.1
	 *
	 * @param type $email
	 * @param type $template
	 * @param type $args
	 */
	public function set_user_lang( $email, $template, $args ) {
		$user = get_user_by( 'email', $email );
		if ( $user && ! empty( $user->locale ) ) {
			$_GET['lang'] = substr( $user->locale, 0, 2 );
		}
	}

}
