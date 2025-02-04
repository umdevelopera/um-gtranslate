<?php
/**
 * Plugin Name: Ultimate Member - GTranslate
 * Plugin URI:  https://github.com/umdevelopera/um-gtranslate
 * Description: Integrates Ultimate Member with GTranslate
 * Author:      umdevelopera
 * Author URI:  https://github.com/umdevelopera
 * Text Domain: um-gtranslate
 * Domain Path: /languages
 *
 * Requires Plugins: ultimate-member, gtranslate
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * UM version: 2.9.2
 * Version: 1.0.1
 *
 * @package um_ext\um_gtranslate
 */

defined( 'ABSPATH' ) || exit;

require_once ABSPATH . 'wp-admin/includes/plugin.php';

$plugin_data = get_plugin_data( __FILE__, true, false );

define( 'um_gtranslate_url', plugin_dir_url( __FILE__ ) );
define( 'um_gtranslate_path', plugin_dir_path( __FILE__ ) );
define( 'um_gtranslate_plugin', plugin_basename( __FILE__ ) );
define( 'um_gtranslate_extension', $plugin_data['Name'] );
define( 'um_gtranslate_version', $plugin_data['Version'] );
define( 'um_gtranslate_textdomain', 'um-gtranslate' );


// Check dependencies.
if ( ! function_exists( 'um_gtranslate_check_dependencies' ) ) {
	function um_gtranslate_check_dependencies() {
		if ( ! defined( 'um_path' ) || ! function_exists( 'UM' ) || ! UM()->dependencies()->ultimatemember_active_check() ) {
			// Ultimate Member is not active.
			add_action(
				'admin_notices',
				function () {
					// translators: %s - plugin name.
					echo '<div class="error"><p>' . wp_kses_post( sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-gtranslate' ), um_gtranslate_extension ) ) . '</p></div>';
				}
			);
		} elseif ( ! class_exists( 'GTranslate' ) ) {
			// GTranslate is not active.
			add_action(
				'admin_notices',
				function () {
					// translators: %s - plugin name.
					echo '<div class="error"><p>' . wp_kses_post( sprintf( __( 'The <strong>%s</strong> extension requires the GTranslate plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/gtranslate/">here</a>', 'um-gtranslate' ), um_gtranslate_extension ) ) . '</p></div>';
				}
			);
		} else {
			require_once 'includes/class-um-gtranslate.php';
			UM()->set_class( 'GTranslate', true );
		}
	}
}
add_action( 'init', 'um_gtranslate_check_dependencies', 1 );
