<?php
namespace um_ext\um_gtranslate\admin;

defined( 'ABSPATH' ) || exit;

/**
 * Extends wp-admin features.
 *
 * Get an instance this way: UM()->GTranslate()->admin()
 *
 * @package um_ext\um_gtranslate\admin
 */
class Init {


	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->mail();
	}


	/**
	 * Translate email templates.
	 *
	 * @return Mail
	 */
	public function mail() {
		if ( empty( UM()->classes['um_gtranslate_admin_mail'] ) ) {
			require_once um_gtranslate_path . 'includes/admin/class-mail.php';
			UM()->classes['um_gtranslate_admin_mail'] = new Mail();
		}
		return UM()->classes['um_gtranslate_admin_mail'];
	}

}
