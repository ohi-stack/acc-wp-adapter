<?php
/**
 * Plugin Name: Algonquian Deal Intake
 * Description: Captures, normalizes, scores, and routes real estate acquisition leads for Algonquian Real Estate.
 * Version: 1.0.0
 * Author: Onegodian | Algonquian Real Estate
 * Text Domain: algq-deal-intake
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ALGQ_DEAL_INTAKE_VERSION', '1.0.0');
define('ALGQ_DEAL_INTAKE_FILE', __FILE__);
define('ALGQ_DEAL_INTAKE_DIR', plugin_dir_path(__FILE__));
define('ALGQ_DEAL_INTAKE_URL', plugin_dir_url(__FILE__));

require_once ALGQ_DEAL_INTAKE_DIR . 'includes/class-helpers.php';
require_once ALGQ_DEAL_INTAKE_DIR . 'includes/class-deal-cpt.php';
require_once ALGQ_DEAL_INTAKE_DIR . 'includes/class-autotagging.php';
require_once ALGQ_DEAL_INTAKE_DIR . 'includes/class-activity-log.php';
require_once ALGQ_DEAL_INTAKE_DIR . 'includes/class-shortcodes.php';
require_once ALGQ_DEAL_INTAKE_DIR . 'includes/class-admin.php';
require_once ALGQ_DEAL_INTAKE_DIR . 'includes/class-rest-api.php';
require_once ALGQ_DEAL_INTAKE_DIR . 'includes/class-export.php';
require_once ALGQ_DEAL_INTAKE_DIR . 'includes/class-roles.php';

final class ALGQ_Deal_Intake_Plugin {
    public static function init() {
        ALGQ_Deal_CPT::init();
        ALGQ_Deal_Shortcodes::init();
        ALGQ_Deal_Admin::init();
        ALGQ_Deal_REST_API::init();
        ALGQ_Deal_Export::init();
    }

    public static function activate() {
        ALGQ_Deal_CPT::init();
        ALGQ_Deal_Roles::add_roles();
        flush_rewrite_rules();
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }
}

add_action('plugins_loaded', array('ALGQ_Deal_Intake_Plugin', 'init'));
register_activation_hook(__FILE__, array('ALGQ_Deal_Intake_Plugin', 'activate'));
register_deactivation_hook(__FILE__, array('ALGQ_Deal_Intake_Plugin', 'deactivate'));
