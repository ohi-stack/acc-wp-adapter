<?php
/**
 * Plugin Name: Algonquian Pipeline CRM
 * Plugin URI: https://algonquianrealestate.com/plugin/pipeline-crm
 * Description: Manages the full acquisition lifecycle from lead to close with pipeline stages, Kanban board, status movement, internal notes, activity logs, assignment tracking, and close tracking.
 * Version: 1.0.0
 * Author: Onegodian
 * Text Domain: algq-pipeline-crm
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ALGQ_PIPELINE_CRM_VERSION', '1.0.0');
define('ALGQ_PIPELINE_CRM_PATH', plugin_dir_path(__FILE__));
define('ALGQ_PIPELINE_CRM_URL', plugin_dir_url(__FILE__));

require_once ALGQ_PIPELINE_CRM_PATH . 'includes/class-algq-pipeline-crm.php';
require_once ALGQ_PIPELINE_CRM_PATH . 'includes/class-algq-pipeline-crm-activator.php';

register_activation_hook(__FILE__, array('ALGQ_Pipeline_CRM_Activator', 'activate'));

add_action('plugins_loaded', function () {
    ALGQ_Pipeline_CRM::instance();
});
