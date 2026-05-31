<?php
/**
 * Plugin Name: Algonquian Offer Generator
 * Description: Generates acquisition offers, LOIs, purchase summaries, and seller-financing offer documents from deal data.
 * Version: 1.0.0
 * Author: Onegodian | Algonquian Real Estate
 * Text Domain: algq-offer-generator
 */

if (!defined('ABSPATH')) { exit; }

define('ALGQ_OFFER_GENERATOR_VERSION', '1.0.0');
define('ALGQ_OFFER_GENERATOR_FILE', __FILE__);
define('ALGQ_OFFER_GENERATOR_DIR', plugin_dir_path(__FILE__));
define('ALGQ_OFFER_GENERATOR_URL', plugin_dir_url(__FILE__));

require_once ALGQ_OFFER_GENERATOR_DIR . 'includes/class-offer-cpt.php';
require_once ALGQ_OFFER_GENERATOR_DIR . 'includes/class-template-registry.php';
require_once ALGQ_OFFER_GENERATOR_DIR . 'includes/class-merge-fields.php';
require_once ALGQ_OFFER_GENERATOR_DIR . 'includes/class-offer-builder.php';
require_once ALGQ_OFFER_GENERATOR_DIR . 'includes/class-shortcodes.php';
require_once ALGQ_OFFER_GENERATOR_DIR . 'includes/class-admin.php';
require_once ALGQ_OFFER_GENERATOR_DIR . 'includes/class-rest-api.php';
require_once ALGQ_OFFER_GENERATOR_DIR . 'includes/class-page-installer.php';

final class ALGQ_Offer_Generator_Plugin {
    public static function init() {
        ALGQ_Offer_CPT::init();
        ALGQ_Offer_Shortcodes::init();
        ALGQ_Offer_Admin::init();
        ALGQ_Offer_REST_API::init();
    }

    public static function activate() {
        ALGQ_Offer_CPT::init();
        ALGQ_Offer_Page_Installer::install();
        flush_rewrite_rules();
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }
}

add_action('plugins_loaded', array('ALGQ_Offer_Generator_Plugin', 'init'));
register_activation_hook(__FILE__, array('ALGQ_Offer_Generator_Plugin', 'activate'));
register_deactivation_hook(__FILE__, array('ALGQ_Offer_Generator_Plugin', 'deactivate'));
