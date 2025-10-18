<?php

namespace Instagram_Posts\App\Controllers\Frontend;

use Instagram_Posts\App\Controllers\Frontend\Base_Controller;
use Instagram_Posts as Instagram_Posts;

if ( ! class_exists( __NAMESPACE__ . '\\' . 'Instagram_Controller' ) ) {
	/**
	 * Class that handles the Beach-Reservation on the frontend
	 *
	 * @since      1.0.0
	 * @package    Instagram_Controller
	 * @subpackage Instagram_Controller/Controllers/Frontend
	 */
	class Instagram_Controller extends Base_Controller {

		/**
		 * Register hooks
		 */
		public function register_hook_callbacks(){
	
		}



		/**
		 * Creates the markup for the Admin page
		 *
		 * @since    1.0.0
		 */
		public function load_instagram_posts() {
			$this->get_model()->load_posts();
		}

	}	
}