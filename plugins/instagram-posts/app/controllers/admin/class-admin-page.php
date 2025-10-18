<?php
namespace Instagram_Posts\App\Controllers\Admin;

use Instagram_Posts\App\Controllers\Admin\Base_Controller;
use Instagram_Posts as Instagram_Posts;

if ( ! class_exists( __NAMESPACE__ . '\\' . 'Admin_Page' ) ) {

	/**
	 * Controller class that implements Plugin Admin Settings configurations
	 *
	 * @since      1.0.0
	 * @package    Instagram_Posts
	 * @subpackage Instagram_Posts/controllers/admin
	 */
	class Admin_Page extends Base_Controller {
	
		/**
		 * Holds suffix for dynamic add_action called on settings page.
		 *
		 * @var string
		 * @since 1.0.0
		 */
		private static $hook_suffix = 'admin_page_' . Instagram_Posts::PLUGIN_ID;

		/**
		 * Slug of the Settings Page
		 *
		 * @since    1.0.0
		 */
		const PAGE_SLUG = Instagram_Posts::PLUGIN_ID . '_admin';

		/**
		 * Slug of the New page
		 */
		const NEW_PAGE_SLUG = Instagram_Posts::PLUGIN_ID . '_admin_new';

		/**
		 * Slug of the Edit page
		 */
		const EDIT_PAGE_SLUG = Instagram_Posts::PLUGIN_ID . '_admin_edit';

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
			add_action( 'admin_menu',				[ $this, 'plugin_menu'	   ], 1 );
			add_action( 'admin_enqueue_scripts',	[ $this, 'enqueue_scripts' ] );
			add_action( 'admin_enqueue_scripts',	[ $this, 'enqueue_styles'  ] );
		}

		/**
		 * Create menu for Plugin inside Settings menu
		 *
		 * @since    1.0.0
		 */
		public function plugin_menu() {
			// @codingStandardsIgnoreStart.
			add_menu_page(
				__( Instagram_Posts::PLUGIN_NAME, Instagram_Posts::PLUGIN_ID ),        // Page Title.
				__( Instagram_Posts::PLUGIN_NAME, Instagram_Posts::PLUGIN_ID ),        // Menu Title.
				static::REQUIRED_CAPABILITY,           // Capability.
				static::PAGE_SLUG,             // Menu URL.
				[$this, 'markup_admin_page'], // Callback.
				'dashicons-schedule',
				6
			);
			// @codingStandardsIgnoreEnd.
		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_script(
				Instagram_Posts::PLUGIN_ID . '_admin-js',
				Instagram_Posts::get_plugin_url() . 'assets/js/admin/instagram-posts.js',
				[ 'jquery' ],
				Instagram_Posts::PLUGIN_VERSION,
				true
			);
		}

		/**
		 * Register Bootstrap for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles() {
			wp_enqueue_style(
				Instagram_Posts::PLUGIN_ID . '_admin-css',
				Instagram_Posts::get_plugin_url() . 'assets/css/admin/instagram-posts.css',
				[],
				Instagram_Posts::PLUGIN_VERSION,
				'all'
			);
		}


		/**
		 * Creates the markup for the Admin page
		 *
		 * @since    1.0.0
		 */
		public function markup_admin_page() {
			if ( current_user_can( static::REQUIRED_CAPABILITY ) ) {
				$data = [];

				$fetched_posts = $this->get_model()->get_json();
				$data['fetched_posts'] = $fetched_posts;
				

				

				echo $this->view->render_instagram($data);

			} else {
				wp_die( __( 'Access denied.', Instagram_Posts::PLUGIN_ID ) );
			}
		}



	}

}
