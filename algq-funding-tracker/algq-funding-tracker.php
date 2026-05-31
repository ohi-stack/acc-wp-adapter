<?php
/**
 * Plugin Name: Algonquian Funding Tracker
 * Description: Tracks lenders, private money, JV capital, commitments, loan terms, and funding status by deal.
 * Version: 1.0.0
 * Author: Onegodian | Algonquian Real Estate
 * Text Domain: algq-funding-tracker
 */

if (!defined('ABSPATH')) { exit; }

define('ALGQ_FUNDING_TRACKER_VERSION', '1.0.0');
define('ALGQ_FUNDING_TRACKER_FILE', __FILE__);
define('ALGQ_FUNDING_TRACKER_DIR', plugin_dir_path(__FILE__));
define('ALGQ_FUNDING_TRACKER_URL', plugin_dir_url(__FILE__));

require_once ALGQ_FUNDING_TRACKER_DIR . 'includes/class-capital-source-cpt.php';
require_once ALGQ_FUNDING_TRACKER_DIR . 'includes/class-funding-record-cpt.php';
require_once ALGQ_FUNDING_TRACKER_DIR . 'includes/class-shortcodes.php';
require_once ALGQ_FUNDING_TRACKER_DIR . 'includes/class-admin.php';
require_once ALGQ_FUNDING_TRACKER_DIR . 'includes/class-rest-api.php';
require_once ALGQ_FUNDING_TRACKER_DIR . 'includes/class-reports.php';
require_once ALGQ_FUNDING_TRACKER_DIR . 'includes/class-page-installer.php';

final class ALGQ_Funding_Tracker_Plugin {
    public static function init() {
        ALGQ_Capital_Source_CPT::init();
        ALGQ_Funding_Record_CPT::init();
        ALGQ_Funding_Shortcodes::init();
        ALGQ_Funding_Admin::init();
        ALGQ_Funding_REST_API::init();
    }

    public static function activate() {
        ALGQ_Capital_Source_CPT::init();
        ALGQ_Funding_Record_CPT::init();
        ALGQ_Funding_Page_Installer::install();
        flush_rewrite_rules();
    }

    public static function deactivate() { flush_rewrite_rules(); }
}

add_action('plugins_loaded', array('ALGQ_Funding_Tracker_Plugin', 'init'));
register_activation_hook(__FILE__, array('ALGQ_Funding_Tracker_Plugin', 'activate'));
register_deactivation_hook(__FILE__, array('ALGQ_Funding_Tracker_Plugin', 'deactivate'));
