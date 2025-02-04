<?php
defined( 'ABSPATH' ) || exit;

/**
 * The "Ultimate Member - GTranslate" extension initialization.
 *
 * How to call: UM()->GTranslate()
 *
 * @package um_ext\um_gtranslate
 */
class UM_GTranslate {

	/**
	 * GTranslate options.
	 *
	 * @var array
	 */
	public $data;


	/**
	 * An instance of the class.
	 *
	 * @var UM_GTranslate
	 */
	private static $instance;


	/**
	 * Creates an instance of the class.
	 *
	 * @return UM_GTranslate
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->data = get_option('GTranslate');

		$this->core();
		if ( UM()->is_request( 'admin' ) ) {
			$this->admin();
		}
		if ( UM()->is_request( 'frontend' ) ) {
			$this->set_user_lang();
		}
	}


	/**
	 * Extends wp-admin features.
	 *
	 * @since 1.0.0
	 *
	 * @return um_ext\um_gtranslate\admin\Init()
	 */
	public function admin() {
		if ( empty( UM()->classes['um_gtranslate_admin'] ) ) {
			require_once um_gtranslate_path . 'includes/admin/class-init.php';
			UM()->classes['um_gtranslate_admin'] = new um_ext\um_gtranslate\admin\Init();
		}
		return UM()->classes['um_gtranslate_admin'];
	}


	/**
	 * Common functionality.
	 *
	 * @since 1.0.0
	 *
	 * @return um_ext\um_gtranslate\core\Init()
	 */
	public function core() {
		if ( empty( UM()->classes['um_gtranslate_core'] ) ) {
			require_once um_gtranslate_path . 'includes/core/class-init.php';
			UM()->classes['um_gtranslate_core'] = new um_ext\um_gtranslate\core\Init();
		}
		return UM()->classes['um_gtranslate_core'];
	}


	/**
	 * Returns the current language.
	 *
	 * @link https://docs.gtranslate.io/en/articles/1349939-how-to-detect-current-selected-language How to detect current selected language?
	 *
	 * @since 1.0.0
	 *
	 * @return string Language code.
	 */
	public function get_current( $field = 'slug' ) {
		if ( isset( $_COOKIE['googtrans'] ) ) {
			$googtrans = sanitize_text_field( $_COOKIE['googtrans'] );
			$lang_code = explode( '/', trim( $googtrans, '/\\ ' ) );
			if ( 2 === count( $lang_code ) ) {
				$lang = $lang_code[1];
			}
		}
		if ( isset( $_SERVER['HTTP_X_GT_LANG'] ) ) {
			$lang = sanitize_key( $_SERVER['HTTP_X_GT_LANG'] );
		}
		if ( isset( $_GET['lang'] ) ) {
			$lang = sanitize_key( $_GET['lang'] );
		}
		if ( empty( $lang ) || 'all' === $lang ) {
			$locale = determine_locale();
			$lang   = substr( $locale, 0, 2 );
		}
		return 'locale' === $field ? $this->get_translation( $lang )->language : $lang;
	}


	/**
	 * Returns the default language.
	 *
	 * @since 1.0.0
	 *
	 * @return string Language code.
	 */
	public function get_default( $field = 'slug' ) {
		if ( isset( $this->data['default_language'] ) ) {
			$lang = $this->data['default_language'];
		}
		if ( empty( $lang ) || 'all' === $lang ) {
			$lang = substr( get_locale(), 0, 2 );
		}
		return 'locale' === $field ? $this->get_translation( $lang )->language : $lang;
	}


	/**
	 * Returns an object with the language details.
	 *
	 * @since 1.0.0
	 *
	 * @param string $lang Language code.
	 * @return object Language info.
	 */
	public function get_translation( $lang ) {
		require_once ABSPATH . 'wp-admin/includes/translation-install.php';
		$translations = wp_get_available_translations();

		switch( $lang ) {
			case 'ca': $locale = 'en_CA'; break;
			case 'en': $locale = 'en_GB'; break;
			case 'us': $locale = 'en_US'; break;
			case 'ar': $locale = 'es_AR'; break;
			case 'co': $locale = 'es_CO'; break;
			case 'mx': $locale = 'es_MX'; break;
			case 'br': $locale = 'pt_BR'; break;
			default: $locale = $lang . '_' . strtoupper( $lang ); break;
		}

		if ( array_key_exists( $lang, $translations ) ) {
			$translation = $translations[ $lang ];
		} elseif ( array_key_exists( $locale, $translations ) ) {
			$translation = $translations[ $locale ];
		} else {
			foreach ( $translations as $t ) {
				if ( in_array( $lang, $t['iso'], true ) ) {
					$translation = $t;
					break;
				}
			}
		}
		return empty( $translation ) ? false : (object) $translation;
	}


	/**
	 * Returns the list of available languages.
	 *
	 * @since 1.0.0
	 *
	 * @return array Languages.
	 */
	public function get_languages_list() {
		return empty( $this->data['fincl_langs'] ) ? array( $this->get_default() ) : (array) $this->data['fincl_langs'];
	}


	/**
	 * Check if GTranslate is active.
	 *
	 * @since   1.0.0
	 *
	 * @return boolean
	 */
	public function is_active() {
		return class_exists( 'GTranslate' );
	}


	/**
	 * Check if the default language is chosen.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function is_default() {
		return $this->get_current() === $this->get_default();
	}


	/**
	 * Set current language cookie and update user locale.
	 */
	public function set_user_lang() {
		if ( ! headers_sent() && isset( $_GET['lang'] ) ) {
			$lang   = sanitize_key( $_GET['lang'] );
			$cookie = '/' . $this->get_default() . '/' . $lang;
			setrawcookie( 'googtrans', $cookie, time() + HOUR_IN_SECONDS, '/' );
		}

		if ( is_user_logged_in() ) {
			global $current_user;

			/**
			 * Hook: um_gtranslate_update_user_locale
			 * Type: filter
			 * Description: Turn on/off updating the user locale. Default on.
			 *
			 * @since 1.0.1
			 *
			 * @param bool $update Update locale or not.
			 */
			$update = apply_filters( 'um_gtranslate_update_user_locale', true );
			$locale = $this->get_current( 'locale' );
			if ( $update && $current_user->locale !== $locale ) {
				update_user_meta( $current_user->ID, 'locale', $locale );
			}
		}
	}

}
