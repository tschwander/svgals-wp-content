<?php
// file: `example-me/app/views/frontend/class-instagram-posts.php`

namespace Instagram_Posts\App\Views\Admin;

use \Instagram_Posts\Core\View;
use \Instagram_Posts as Instagram_Posts;

if ( ! class_exists( __NAMESPACE__ . '\\' . 'Admin_Overview' ) ) {
	class Admin_Overview extends View {

		/**
		 * Render FullCalendar View in admin area
		 */
		public function render_instagram($data)
		{
			return $this->render_template(
				'admin/instagram-posts-overview.php', $data);
		}

	}
}