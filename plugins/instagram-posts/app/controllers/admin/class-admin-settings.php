<?php
namespace Instagram_Posts\App\Controllers\Admin;



use Instagram_Posts\App\Controllers\Admin\Base_Controller;
use Instagram_Posts as Instagram_Posts;


if ( ! class_exists( __NAMESPACE__ . '\\' . 'Admin_Settings' ) ) {

	/**
	 * Controller class that implements Plugin Admin Settings configurations
	 *
	 * @since      1.0.0
	 * @package    Instagram_Posts
	 * @subpackage Instagram_Posts/controllers/admin
	 */
	class Admin_Settings extends Base_Controller {

		/**
		 * Holds suffix for dynamic add_action called on settings page.
		 *
		 * @var string
		 * @since 1.0.0
		 */
		private static $hook_suffix = Instagram_Posts::PLUGIN_ID."_settings";

		/**
		 * Slug of the Settings Page
		 *
		 * @since    1.0.0
		 */
		const SETTINGS_PAGE_SLUG = Instagram_Posts::PLUGIN_ID."_settings";

		/**
		 * Capability required to access settings page
		 *
		 * @since 1.0.0
		 */
		const REQUIRED_CAPABILITY = 'manage_options';

		/**
		 * Register callbacks for actions and filters
		 *
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {
			// Create Menu.
			add_action( 'admin_menu', [ $this, 'plugin_menu' ] , 9);

			// Enqueue Styles & Scripts.
			add_action( 'admin_print_scripts-' . static::$hook_suffix,	[ $this, 'enqueue_scripts' ], 10 );
			add_action( 'admin_print_styles-' . static::$hook_suffix, 	[ $this, 'enqueue_styles' ], 10 );

			// Register Fields.
			add_action( 'admin_init', 									[ $this, 'register_fields' ], 10 );

			// Register Settings.
			add_action( 'admin_init', 									[ $this->get_model(), 'register_settings' ], 10 );

			// Settings Link on Plugin's Page.
			add_filter( 'plugin_action_links_' . Instagram_Posts::PLUGIN_ID . '/' . Instagram_Posts::PLUGIN_ID . '.php',
																		[ $this, 'add_plugin_action_links' ], 10
			);
		}

		/**
		 * Create menu for Plugin inside Settings menu
		 *
		 * @since    1.0.0
		 */
		public function plugin_menu() {
			// @codingStandardsIgnoreStart.
			static::$hook_suffix = add_submenu_page(
				Instagram_Posts::PLUGIN_ID . '_admin',        // parent_slug.
				__( Instagram_Posts::PLUGIN_NAME, Instagram_Posts::PLUGIN_ID ),        // Menu Title.
				'Einstellungen',
				static::REQUIRED_CAPABILITY,           // Capability.
				static::SETTINGS_PAGE_SLUG,             // Menu URL.
				[ $this, 'markup_settings_page' ], // Callback.
				10
			);
			// @codingStandardsIgnoreEnd.
		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {

			/**
			 * This function is provided for demonstration purposes only.
			 */

			wp_enqueue_script(
				Instagram_Posts::PLUGIN_ID . '_admin-js',
				Instagram_Posts::get_plugin_url() . 'assets/js/admin/instagram-posts.js',
				[ 'jquery' ],
				Instagram_Posts::PLUGIN_VERSION,
				true
			);
		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles() {

			/**
			 * This function is provided for demonstration purposes only.
			 */

			wp_enqueue_style(
				Instagram_Posts::PLUGIN_ID . '_admin-css',
				Instagram_Posts::get_plugin_url() . 'assets/css/admin/instagram-posts.css',
				[],
				Instagram_Posts::PLUGIN_VERSION,
				'all'
			);
		}
		
		/**
		 * Creates the markup for the Settings page
		 *
		 * @since    1.0.0
		 */
		/*
		public function markup_settings_page() {
			if ( current_user_can( static::REQUIRED_CAPABILITY ) ) {
				GraphHelper::initializeGraphForUserAuth($this->get_model()->get_setting('beachin_graph_access_token'));

				if ( isset ( $_REQUEST['code'] ) ) {
					$params = stripslashes_deep( $_REQUEST );
					GraphHelper::exchangeCodeForAccessToken( $params[ 'code'] );
				}

				$refresh_token = false;
				$calendars = false;
				if ( isset ( $_REQUEST['refresh_token'] ) ) {
					$params = stripslashes_deep( $_REQUEST );
					$refresh_token = $params[ 'refresh_token'];
				}

				if ( isset ( $_REQUEST['get_calendars'] ) ) {

					if( $this->get_model()->get_setting('beachin_graph_access_token')){
						$calendars = $this->displayCalendars();
					}
				}

				$this->view->admin_settings_page(
					[
						'page_title'    => Instagram_Posts::PLUGIN_NAME,
						'settings_name' => $this->get_model()->get_plugin_settings_option_key(),
						'authurl' => GraphHelper::getAuthUrl(),
						'calendarurl' => $this->getCalendarUrl(),
						'refresh_token' => $refresh_token,
						'calendars' => $calendars
					]
				);
			} else {
				wp_die( __( 'Access denied.', Instagram_Posts::PLUGIN_ID ) ); // WPCS: XSS OK.
			}
		}
		*/
		/**
		 * Registers settings sections and fields
		 *
		 * @since    1.0.0
		 */
		public function register_fields() {
			// Add Settings Page Section.
			add_settings_section(
				'instagram_posts_section',                    // Section ID.
				__( 'Settings', Instagram_Posts::PLUGIN_ID ), // Section Title.
				[ $this, 'markup_section_headers' ], // Section Callback.
				static::SETTINGS_PAGE_SLUG                 // Page URL.
			);

			// Add Product Settings Page Field.
			add_settings_field(
				'beachin_prod',                                // Field ID.
				__( 'BeachIN Produkt:', Instagram_Posts::PLUGIN_ID ), // Field Title.
				[ $this, 'markup_fields' ],                    // Field Callback.
				static::SETTINGS_PAGE_SLUG,                          // Page.
				'instagram_posts_section',                              // Section ID.
				[                                              // Field args.
					'id'        => 'beachin_prod',
					'label_for' => 'beachin_prod',
				]
			);

			// Add Product Settings Page Field.
			add_settings_field(
				'beachin_graph_access_token',                                // Field ID.
				__( 'Microsoft Refresh Token:', Instagram_Posts::PLUGIN_ID ), // Field Title.
				[ $this, 'markup_fields' ],                    // Field Callback.
				static::SETTINGS_PAGE_SLUG,                          // Page.
				'instagram_posts_section',                              // Section ID.
				[                                              // Field args.
					'id'        => 'beachin_graph_access_token',
					'label_for' => 'beachin_graph_access_token',
				]
			);
		}

		/**
		 * Adds the section introduction text to the Settings page
		 *
		 * @param array $section Array containing information Section Id, Section
		 *                       Title & Section Callback.
		 *
		 * @since    1.0.0
		 */
		public function markup_section_headers( $section ) {
			$this->view->section_headers(
				[
					'section'      => $section,
					'text_example' => __( 'This is a text example for section header', Instagram_Posts::PLUGIN_ID ),
				]
			);
		}

		/**
		 * Delivers the markup for settings fields
		 *
		 * @param array $field_args Field arguments passed in `add_settings_field`
		 *                          function.
		 *
		 * @since    1.0.0
		 */
		public function markup_fields( $field_args ) {
			$field_id = $field_args['id'];
			$settings_value = $this->get_model()->get_setting( $field_id );
			$this->view->markup_fields(
				[
					'field_id'       => esc_attr( $field_id ),
					'settings_name'  => $this->get_model()->get_plugin_settings_option_key(),
					'settings_value' => ! empty( $settings_value ) ? esc_attr( $settings_value ) : '',
				]
			);
		}

		/**
		 * Adds links to the plugin's action link section on the Plugins page
		 *
		 * @param array $links The links currently mapped to the plugin.
		 * @return array
		 *
		 * @since    1.0.0
		 */
		public function add_plugin_action_links( $links ) {
			$settings_link = '<a href="admin.php?page=' . static::SETTINGS_PAGE_SLUG . '">' . __( 'Settings', Instagram_Posts::PLUGIN_ID ) . '</a>';
			array_unshift( $links, $settings_link );

			return $links;
		}

		function getCalendarUrl(){
			return admin_url( 'admin.php?page=' . static::SETTINGS_PAGE_SLUG . '&get_calendars=true', 'https' );
		}
		/*
		function displayCalendars() {
			try {
				$calendars = GraphHelper::getCalendars();
				return $calendars;
			} catch (Exception $e) {
				return 'Error getting access token: '.$e->getMessage();
			}
		}
		
		function displayAccessToken() {
			try {
				$token = GraphHelper::getUserToken();
				return 'User token: '.$token;
			} catch (Exception $e) {
				return 'Error getting access token: '.$e->getMessage();
			}
		}
		*/
	}

}
