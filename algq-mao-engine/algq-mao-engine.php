<?php
/**
 * Plugin Name: Algonquian MAO Engine
 * Description: Calculates maximum allowable offer, spread, projected profit, and risk flags for acquisition underwriting.
 * Version: 1.0.0
 * Author: Onegodian | Algonquian Real Estate
 * Text Domain: algq-mao-engine
 */

if (!defined('ABSPATH')) { exit; }

define('ALGQ_MAO_ENGINE_VERSION', '1.0.0');
define('ALGQ_MAO_ENGINE_FILE', __FILE__);
define('ALGQ_MAO_ENGINE_DIR', plugin_dir_path(__FILE__));
define('ALGQ_MAO_ENGINE_URL', plugin_dir_url(__FILE__));

require_once ALGQ_MAO_ENGINE_DIR . 'includes/class-calculator.php';
require_once ALGQ_MAO_ENGINE_DIR . 'includes/class-shortcodes.php';
require_once ALGQ_MAO_ENGINE_DIR . 'includes/class-admin.php';
require_once ALGQ_MAO_ENGINE_DIR . 'includes/class-page-installer.php';

final class ALGQ_MAO_Engine_Plugin {
    public static function init() {
        ALGQ_MAO_Shortcodes::init();
        ALGQ_MAO_Admin::init();
    }

    public static function activate() {
        ALGQ_MAO_Page_Installer::install();
        flush_rewrite_rules();
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }
}

add_action('plugins_loaded', array('ALGQ_MAO_Engine_Plugin', 'init'));
register_activation_hook(__FILE__, array('ALGQ_MAO_Engine_Plugin', 'activate'));
register_deactivation_hook(__FILE__, array('ALGQ_MAO_Engine_Plugin', 'deactivate'));
