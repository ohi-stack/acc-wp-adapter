<?php
/**
 * Plugin Name: Algonquian Real Estate Platform
 * Plugin URI: https://algonquianrealestate.com
 * Description: Acquisition, underwriting, document automation, buyer marketplace, and transaction management platform.
 * Version: 1.0.0-rc1
 * Author: Onegodian
 * Text Domain: algq-platform
 * Requires at least: 6.6
 * Requires PHP: 8.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALGQ_PLATFORM_VERSION', '1.0.0-rc1' );
define( 'ALGQ_PLATFORM_FILE', __FILE__ );
define( 'ALGQ_PLATFORM_PATH', trailingslashit( dirname( __DIR__ ) ) );
define( 'ALGQ_PLATFORM_URL', plugin_dir_url( __FILE__ ) );

require_once ALGQ_PLATFORM_PATH . 'includes/class-security.php';
require_once ALGQ_PLATFORM_PATH . 'includes/class-database.php';
require_once ALGQ_PLATFORM_PATH . 'includes/class-activator.php';
require_once ALGQ_PLATFORM_PATH . 'includes/class-page-generator.php';
require_once ALGQ_PLATFORM_PATH . 'includes/class-shortcodes.php';
require_once ALGQ_PLATFORM_PATH . 'includes/class-admin.php';

register_activation_hook( __FILE__, array( 'ALGQ_Activator', 'activate' ) );

add_action(
	'plugins_loaded',
	function () {
		load_plugin_textdomain( 'algq-platform', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		new ALGQ_Shortcodes();
		new ALGQ_Admin();
	}
);
