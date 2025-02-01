<?php
namespace um_ext\um_gtranslate\admin;
use GTranslate;

defined( 'ABSPATH' ) || exit;

/**
 * Translate email templates.
 *
 * Get an instance this way: UM()->GTranslate()->admin()->mail()
 *
 * @package um_ext\um_gtranslate\admin
 */
class Mail {

	/**
	 * Flag size.
	 *
	 * @var int
	 */
	private $flag_size = 24;


	/**
	 * Class constructor.
	 */
	public function __construct() {
		$data            = get_option('GTranslate');
		$this->flag_size = $data['flag_size'];

		// Email table.
		add_action( 'um_settings_page_before_email__content', array( $this, 'settings_email' ), 8 );

		// Email settings.
		add_filter( 'um_admin_settings_email_section_fields', array( &$this, 'email_subject' ), 10, 2 );
		add_filter( 'um_change_settings_before_save', array( &$this, 'email_template' ), 8, 1 );
	}


	/**
	 * Adding locale suffix to the "Subject Line" field.
	 *
	 * Example: change 'welcome_email_sub' to 'welcome_email_sub_de_DE'
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $section_fields Email template fields.
	 * @param  string $template       Email template slug.
	 * @return array
	 */
	public function email_subject( $section_fields, $template ) {
		$option_key    = $template . '_sub' . ( UM()->GTranslate()->is_default() ? '' : '_' . UM()->GTranslate()->get_current( 'locale' ) );
		$value         = UM()->options()->get( $option_key );
		$value_default = UM()->options()->get( $template . '_sub' );

		$section_fields[2]['id']    = $option_key;
		$section_fields[2]['value'] = empty( $value ) ? $value_default : $value;

		return $section_fields;
	}


	/**
	 * Create email template file in the theme folder.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $settings Input data.
	 * @return array
	 */
	public function email_template( $settings ) {
		if ( isset( $settings['um_email_template'] ) ) {
			$template      = $settings['um_email_template'];
			$template_path = UM()->mail()->get_template_file( 'theme', $template );

			if ( ! file_exists( $template_path ) ) {
				$template_dir = dirname( $template_path );
				if ( wp_mkdir_p( $template_dir ) ) {
					file_put_contents( $template_path, '' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
				}
			}
		}
		return $settings;
	}


	/**
	 * Add column to the "Email notifications" table in settings.
	 *
	 * Hook: um_settings_page_before_email__content - 8.
	 */
	public function settings_email() {
		add_filter( 'um_email_templates_columns', array( &$this, 'thead_th' ), 10, 1 );
		//add_filter( 'um_email_notifications', array( &$this, 'tbody_td' ), 10, 1 );
		$this->tbody_td( UM()->config()->email_notifications );
	}


	/**
	 * Add header for the column 'translations' in the Email table.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $columns The Email table headers.
	 * @return array
	 */
	public function thead_th( $columns ) {
		$languages = UM()->GTranslate()->get_languages_list();
		if ( count( $languages ) > 1 ) {

			$flags = '';
			foreach ( $languages as $lang_code ) {
				$flags .= '<span class="um-flag" style="margin:2px"><img src="' . esc_attr( GTranslate::get_flag_src( $lang_code ) ) . '" width="' . esc_attr( $this->flag_size ) . '" height="' . esc_attr( $this->flag_size ) . '" alt="' . esc_attr( $lang_code ) . '" loading="lazy"></span>';
			}

			$new_columns = array();
			foreach ( $columns as $column_key => $column_content ) {
				$new_columns[ $column_key ] = $column_content;
				if ( 'email' === $column_key && ! isset( $new_columns['translations'] ) ) {
					$new_columns['translations'] = $flags;
				}
			}

			$columns = $new_columns;
		}
		return $columns;
	}


	/**
	 * Add cell for the column 'translations' in the Email table.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $email_notifications Email templates data.
	 * @return string
	 */
	public function tbody_td( &$email_notifications ) {
		$languages = UM()->GTranslate()->get_languages_list();
		if ( count( $languages ) > 1 ) {
			foreach ( $email_notifications as &$email_notification ) {
				$cell = '';
				foreach ( $languages as $lang ) {
					$cell .= $this->tbody_td_a( $email_notification['key'], $lang );
				}
				$email_notification['translations'] = $cell;
			}
		}
		return $email_notifications;
	}


	/**
	 * Get a link to Add/Edit email template for a certain language.
	 *
	 * @since 1.1.0
	 *
	 * @param  string $template The email template slug.
	 * @param  string $lang     Slug or locale of the queried language.
	 * @return string
	 */
	public function tbody_td_a( $template, $lang ) {

		$language = UM()->GTranslate()->get_translation( $lang );
		$default  = UM()->GTranslate()->get_default();
		$locale   = $lang === $default ? '' : trailingslashit( $language->language );

		// theme location.
		$template_path = get_stylesheet_directory() . '/ultimate-member/email/' . $locale . $template . '.php';

		// plugin location for default language.
		if ( empty( $locale ) && ! file_exists( $template_path ) ) {
			$template_path = UM()->mail()->get_template_file( 'plugin', $template );
		}

		$name = trim( preg_replace( '/\s?\(.*\)/i', '', $language->english_name ) );
		$link = add_query_arg(
			array(
				'email' => $template,
				'lang'  => $lang,
			)
		);

		if ( file_exists( $template_path ) ) {

			// translators: %s - language name.
			$title = sprintf( __( 'Edit the translation in %s', 'um-gtranslate' ), $name );

			// translators: %1$s - URL, %2$s - text, %3$d - width.
			$icon_html = sprintf(
				'<a href="%1$s" title="%2$s" style="display:inline-block; margin: 2px; width: %3$dpx"><span class="um_tooltip dashicons dashicons-edit" ><span class="screen-reader-text">%2$s</span></span></a>',
				esc_url( $link ),
				esc_html( $title ),
				absint( $this->flag_size )
			);
		} else {

			// translators: %s - language name.
			$title = sprintf( __( 'Add a translation in %s', 'um-gtranslate' ), $name );

			// translators: %1$s - URL, %2$s - text, %3$d - width.
			$icon_html = sprintf(
				'<a href="%1$s" title="%2$s" style="display:inline-block; margin: 2px; width: %3$dpx"><span class="um_tooltip dashicons dashicons-plus"><span class="screen-reader-text">%2$s</span></span></a>',
				esc_url( $link ),
				esc_attr( $title ),
				absint( $this->flag_size )
			);
		}

		return $icon_html;
	}

}
