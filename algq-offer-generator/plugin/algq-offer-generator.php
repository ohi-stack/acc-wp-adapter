<?php
/**
 * Plugin Name: Algonquian Offer Generator
 * Plugin URI: https://algonquianrealestate.com
 * Description: Generates acquisition offers, LOIs, seller financing proposals, PDFs, and document records from deal data.
 * Version: 1.0.0
 * Author: Onegodian | Algonquian Real Estate
 * Text Domain: algq-offer-generator
 * Requires at least: 6.4
 * Requires PHP: 7.4
 *
 * @package Algq_Offer_Generator
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ALGQ_OFFER_VERSION', '1.0.0');
define('ALGQ_OFFER_FILE', __FILE__);
define('ALGQ_OFFER_DIR', plugin_dir_path(__FILE__));
define('ALGQ_OFFER_URL', plugin_dir_url(__FILE__));

require_once ALGQ_OFFER_DIR . 'includes/class-database.php';
require_once ALGQ_OFFER_DIR . 'includes/class-audit-log.php';
require_once ALGQ_OFFER_DIR . 'includes/class-merge-engine.php';
require_once ALGQ_OFFER_DIR . 'includes/class-document-generator.php';
require_once ALGQ_OFFER_DIR . 'includes/class-pdf-engine.php';
require_once ALGQ_OFFER_DIR . 'includes/class-rest-api.php';
require_once ALGQ_OFFER_DIR . 'includes/class-automation.php';

final class Algq_Offer_Generator_Plugin {
    const CAPABILITY = 'manage_options';

    private static $instance = null;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'register_shortcodes']);
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('admin_post_algq_generate_offer', [$this, 'handle_generate_offer']);
        Algq_Offer_REST_API::init();
        Algq_Offer_Automation::init();
    }

    public static function activate() {
        Algq_Offer_Database::install();
        self::create_pages();
        update_option('algq_offer_generator_version', ALGQ_OFFER_VERSION, false);
    }

    public function register_shortcodes() {
        add_shortcode('algq_offer_generator', [$this, 'shortcode_offer_generator']);
    }

    public function register_admin_menu() {
        add_menu_page(
            esc_html__('Offer Generator', 'algq-offer-generator'),
            esc_html__('Offer Generator', 'algq-offer-generator'),
            self::CAPABILITY,
            'algq-offer-generator',
            [$this, 'render_dashboard'],
            'dashicons-media-document',
            27
        );

        add_submenu_page('algq-offer-generator', esc_html__('Templates', 'algq-offer-generator'), esc_html__('Templates', 'algq-offer-generator'), self::CAPABILITY, 'algq-offer-templates', [$this, 'render_templates']);
        add_submenu_page('algq-offer-generator', esc_html__('Documents', 'algq-offer-generator'), esc_html__('Documents', 'algq-offer-generator'), self::CAPABILITY, 'algq-offer-documents', [$this, 'render_documents']);
        add_submenu_page('algq-offer-generator', esc_html__('Settings', 'algq-offer-generator'), esc_html__('Settings', 'algq-offer-generator'), self::CAPABILITY, 'algq-offer-settings', [$this, 'render_settings']);
    }

    public function enqueue_admin_assets($hook) {
        if (false === strpos((string) $hook, 'algq-offer')) {
            return;
        }
        wp_enqueue_style('algq-offer-admin', ALGQ_OFFER_URL . 'assets/css/admin.css', [], ALGQ_OFFER_VERSION);
        wp_enqueue_script('algq-offer-admin', ALGQ_OFFER_URL . 'assets/js/admin.js', ['jquery'], ALGQ_OFFER_VERSION, true);
    }

    public function shortcode_offer_generator($atts = []) {
        $atts = shortcode_atts(['deal_id' => 0], $atts, 'algq_offer_generator');
        ob_start();
        include ALGQ_OFFER_DIR . 'admin/dashboard.php';
        return ob_get_clean();
    }

    public function render_dashboard() {
        if (!current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('Insufficient permissions.', 'algq-offer-generator'));
        }
        echo '<div class="wrap">';
        include ALGQ_OFFER_DIR . 'admin/dashboard.php';
        echo '</div>';
    }

    public function render_templates() {
        if (!current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('Insufficient permissions.', 'algq-offer-generator'));
        }
        echo '<div class="wrap">';
        include ALGQ_OFFER_DIR . 'admin/templates.php';
        echo '</div>';
    }

    public function render_documents() {
        if (!current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('Insufficient permissions.', 'algq-offer-generator'));
        }
        echo '<div class="wrap">';
        include ALGQ_OFFER_DIR . 'admin/documents.php';
        echo '</div>';
    }

    public function render_settings() {
        if (!current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('Insufficient permissions.', 'algq-offer-generator'));
        }
        echo '<div class="wrap">';
        include ALGQ_OFFER_DIR . 'admin/settings.php';
        echo '</div>';
    }

    public function handle_generate_offer() {
        if (!current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('Insufficient permissions.', 'algq-offer-generator'));
        }
        check_admin_referer('algq_generate_offer', 'algq_nonce');
        $deal_id = isset($_POST['deal_id']) ? absint(wp_unslash($_POST['deal_id'])) : 0;
        $document_type = isset($_POST['document_type']) ? sanitize_key(wp_unslash($_POST['document_type'])) : 'letter-of-intent';
        Algq_Offer_Document_Generator::generate($deal_id, $document_type);
        wp_safe_redirect(admin_url('admin.php?page=algq-offer-documents&generated=1'));
        exit;
    }

    private static function create_pages() {
        $pages = [
            'plugin/offer-generator' => ['Offer Generator', "[vc_column_text]\n[algq_offer_generator]\n[/vc_column_text]"],
            'plugin/offer-generator/start' => ['Offer Generator - Getting Started', "[vc_column_text]\n<h2>Offer Generator Getting Started</h2>\n<p>Create or upload templates, connect deal data, then generate your first offer.</p>\n[/vc_column_text]"],
            'plugin/offer-generator/docs' => ['Offer Generator Documentation', "[vc_column_text]\n<h2>Offer Generator Documentation</h2>\n<p>Use merge fields, templates, PDF export, and document version history.</p>\n[/vc_column_text]"],
            'plugin/offer-generator/templates' => ['Offer Generator Templates', "[vc_column_text]\n<p>Manage Purchase Agreement, LOI, and Seller Financing templates.</p>\n[/vc_column_text]"],
        ];
        $created = [];
        foreach ($pages as $path => $page) {
            $slug = sanitize_title(basename($path));
            if (get_page_by_path($path, OBJECT, 'page')) {
                continue;
            }
            $created[$path] = wp_insert_post([
                'post_title' => sanitize_text_field($page[0]),
                'post_name' => $slug,
                'post_content' => wp_kses_post($page[1]),
                'post_status' => 'publish',
                'post_type' => 'page',
            ]);
        }
        update_option('algq_offer_generated_pages', $created, false);
    }
}

register_activation_hook(__FILE__, ['Algq_Offer_Generator_Plugin', 'activate']);
Algq_Offer_Generator_Plugin::instance();
