<?php
/**
 * Main Plugin File
 *
 * @since             1.0.1
 * @package           Instagram_Posts
 *
 * @wordpress-plugin
 * Plugin Name:       Instagram Posts
 * Description:       Imortieren von Instagram Posts 
 * Version:           1.0.2
 * Author:            WEB-ID
 * Author URI:        https://web-id.ch/
 * License:           Proprietär
 * License URI:       
 * Text Domain:       instagram-posts
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Creates/Maintains the object of Requirements Checker Class
 *
 * @return \Instagram_Posts\Includes\Requirements_Checker
 * @since 1.0.0
 */
function plugin_requirements_checker() {
	static $requirements_checker = null;

	if ( null === $requirements_checker ) {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-requirements-checker.php';
		$requirements_conf = apply_filters( 'instagram_posts_minimum_requirements', include_once( plugin_dir_path( __FILE__ ) . 'requirements-config.php' ) );
		$requirements_checker = new Instagram_Posts\Includes\Requirements_Checker( $requirements_conf );
	}

	return $requirements_checker;
}

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_instagram_posts() {

	// If Plugins Requirements are not met.
	if ( ! plugin_requirements_checker()->requirements_met() ) {
		add_action( 'admin_notices', [ plugin_requirements_checker(), 'show_requirements_errors' ] );

		// Deactivate plugin immediately if requirements are not met.
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		deactivate_plugins( plugin_basename( __FILE__ ) );

		return;
	}

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and frontend-facing site hooks.
	 */
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-instagram-posts.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	$router_class_name = apply_filters( 'instagram_posts_router_class_name', '\Instagram_Posts\Core\Router' );
	$routes = apply_filters( 'instagram_posts_routes_file', plugin_dir_path( __FILE__ ) . 'routes.php' );
	$GLOBALS['instagram_posts'] = new Instagram_Posts( $router_class_name, $routes );

	register_activation_hook( __FILE__, [ new Instagram_Posts\App\Activator(), 'activate' ] );
	register_deactivation_hook( __FILE__, [ new Instagram_Posts\App\Deactivator(), 'deactivate' ] );

	/**
	 * Scheduler Action to check for Microsoft 365 clientsecret. 
	 */
	
	 add_action( 'wp', 	'instagram_posts_setup_schedule' );
	 /**
	  * On an early action hook, check if the hook is scheduled - if not, schedule it.
	  */
	 function instagram_posts_setup_schedule() {
		 if ( ! wp_next_scheduled( 'instagram_posts_hourly_event' ) ) {
			 wp_schedule_event( time(), 'hourly', 'instagram_posts_hourly_event');
		 }
	 }
 
	 add_action( 'instagram_posts_hourly_event', 'instagram_posts_do_this_hourly' );
	 /**
	  * On the scheduled action hook, run a function.
	  */
	 function instagram_posts_do_this_hourly() {
		$instagram = Instagram_Posts\App\Controllers\Frontend\Instagram_Controller::get_instance('Instagram_Posts\App\Models\Frontend\Instagram_Posts' );
		$instagram->load_instagram_posts();
	 }
	
}

run_instagram_posts();
