<?php
namespace um_ext\um_gtranslate\core;

defined( 'ABSPATH' ) || exit;

/**
 * Common functionality.
 *
 * Get an instance this way: UM()->GTranslate()->core()
 *
 * @package um_ext\um_gtranslate\core
 */
class Init {


	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->mail();
		$this->permalinks();
	}


	/**
	 * Localize email templates.
	 *
	 * @since 1.0.0
	 *
	 * @return Mail
	 */
	public function mail() {
		if ( empty( UM()->classes['um_gtranslate_core_mail'] ) ) {
			require_once um_gtranslate_path . 'includes/core/class-mail.php';
			UM()->classes['um_gtranslate_core_mail'] = new Mail();
		}
		return UM()->classes['um_gtranslate_core_mail'];
	}


	/**
	 * Localize links.
	 *
	 * @since 1.0.0
	 *
	 * @return Permalinks
	 */
	public function permalinks() {
		if ( empty( UM()->classes['um_gtranslate_core_permalinks'] ) ) {
			require_once um_gtranslate_path . 'includes/core/class-permalinks.php';
			UM()->classes['um_gtranslate_core_permalinks'] = new Permalinks();
		}
		return UM()->classes['um_gtranslate_core_permalinks'];
	}

}
