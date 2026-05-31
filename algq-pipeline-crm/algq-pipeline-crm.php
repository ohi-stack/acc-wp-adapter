<?php
/**
 * Plugin Name: Algonquian Pipeline CRM
 * Description: Manages the acquisition lifecycle from captured lead through underwriting, offer, buyer assignment, funding, and close.
 * Version: 1.0.0
 * Author: Onegodian | Algonquian Real Estate
 * Text Domain: algq-pipeline-crm
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ALGQ_PIPELINE_CRM_VERSION', '1.0.0');
define('ALGQ_PIPELINE_CRM_FILE', __FILE__);
define('ALGQ_PIPELINE_CRM_DIR', plugin_dir_path(__FILE__));
define('ALGQ_PIPELINE_CRM_URL', plugin_dir_url(__FILE__));

require_once ALGQ_PIPELINE_CRM_DIR . 'includes/class-pipeline-cpt.php';
require_once ALGQ_PIPELINE_CRM_DIR . 'includes/class-pipeline-board.php';
require_once ALGQ_PIPELINE_CRM_DIR . 'includes/class-stage-manager.php';
require_once ALGQ_PIPELINE_CRM_DIR . 'includes/class-deal-assignment.php';
require_once ALGQ_PIPELINE_CRM_DIR . 'includes/class-activity-log.php';
require_once ALGQ_PIPELINE_CRM_DIR . 'includes/class-rest-api.php';
require_once ALGQ_PIPELINE_CRM_DIR . 'includes/class-shortcodes.php';
require_once ALGQ_PIPELINE_CRM_DIR . 'includes/class-permissions.php';
require_once ALGQ_PIPELINE_CRM_DIR . 'includes/class-automation-hooks.php';
require_once ALGQ_PIPELINE_CRM_DIR . 'includes/class-reports.php';

final class ALGQ_Pipeline_CRM_Plugin {
    public static function init() {
        ALGQ_Pipeline_CPT::init();
        ALGQ_Pipeline_Board::init();
        ALGQ_Stage_Manager::init();
        ALGQ_Deal_Assignment::init();
        ALGQ_Pipeline_Activity_Log::init();
        ALGQ_Pipeline_REST_API::init();
        ALGQ_Pipeline_Shortcodes::init();
        ALGQ_Pipeline_Permissions::init();
        ALGQ_Pipeline_Automation_Hooks::init();
        ALGQ_Pipeline_Reports::init();
    }

    public static function activate() {
        ALGQ_Pipeline_CPT::init();
        ALGQ_Pipeline_Permissions::add_capabilities();
        flush_rewrite_rules();
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }
}

add_action('plugins_loaded', array('ALGQ_Pipeline_CRM_Plugin', 'init'));
register_activation_hook(__FILE__, array('ALGQ_Pipeline_CRM_Plugin', 'activate'));
register_deactivation_hook(__FILE__, array('ALGQ_Pipeline_CRM_Plugin', 'deactivate'));
