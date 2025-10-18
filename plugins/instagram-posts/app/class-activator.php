<?php
namespace Instagram_Posts\App;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Instagram_Posts
 * @subpackage Instagram_Posts/App
 * @author     Your Name <email@example.com>
 */
class Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public function activate() {
		$this->instagram_posts_create_db();
		$this->instagram_log_create_db();
	}

	/**
	 * Create custom table for reservations
	 */
	private function instagram_posts_create_db() {
		
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->base_prefix . 'instagram_posts';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			instagram_id varchar(50) NOT NULL,
			caption text DEFAULT '',
			media_type varchar(255) NULL,
			media_url text DEFAULT '',
			permalink varchar(255) NULL,
			thumbnail_url text DEFAULT '',
			ig_timestamp varchar(32) NULL,
			username varchar(255) NULL,
			children text DEFAULT '',
			post_id int NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
	}

	/**
	 * Create custom table for series
	 */
	private function instagram_log_create_db() {
		
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->base_prefix . 'instagram_log';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			synctime datetime NULL,
			posts int NOT NULL,
			medias int NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
	}

}
