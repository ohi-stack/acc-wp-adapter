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
        $created = get_option('algq_offer_generated_pages', []);
        if (!is_array($created)) {
            $created = [];
        }

        $plugin_parent_id = self::ensure_page('plugin', 'Plugins', '[vc_column_text]\n[algq_offer_generator]\n[/vc_column_text]', 0);
        $offer_parent_id  = self::ensure_page('offer-generator', 'Offer Generator', '[vc_column_text]\n[algq_offer_generator]\n[/vc_column_text]', $plugin_parent_id);

        $created['plugin'] = $plugin_parent_id;
        $created['plugin/offer-generator'] = $offer_parent_id;

        $child_pages = [
            'start' => [
                'title' => 'Offer Generator - Getting Started',
                'content' => "[vc_column_text]\n<h2>Offer Generator Getting Started</h2>\n<p>Create or upload templates, connect deal data, and generate the first offer.</p>\n[algq_offer_generator]\n[/vc_column_text]",
            ],
            'docs' => [
                'title' => 'Offer Generator Documentation',
                'content' => "[vc_column_text]\n<h2>Offer Generator Documentation</h2>\n<p>Use merge fields, templates, PDF export, version history, and deal-linked document records.</p>\n[algq_offer_generator]\n[/vc_column_text]",
            ],
            'templates' => [
                'title' => 'Offer Generator Templates',
                'content' => "[vc_column_text]\n<h2>Offer Generator Templates</h2>\n<p>Manage Purchase Agreement, Letter of Intent, and Seller Financing templates.</p>\n[algq_offer_generator]\n[/vc_column_text]",
            ],
        ];

        foreach ($child_pages as $slug => $page) {
            $page_id = self::ensure_page($slug, $page['title'], $page['content'], $offer_parent_id);
            $created['plugin/offer-generator/' . $slug] = $page_id;
        }

        update_option('algq_offer_generated_pages', $created, false);
    }

    private static function ensure_page($slug, $title, $content, $parent_id = 0) {
        $slug = sanitize_title($slug);
        $parent_id = absint($parent_id);
        $existing = get_page_by_path(self::build_page_path($slug, $parent_id), OBJECT, 'page');

        if (!$existing && $parent_id) {
            $children = get_pages([
                'post_type' => 'page',
                'post_status' => ['publish', 'draft', 'private'],
                'post_parent' => $parent_id,
                'name' => $slug,
                'number' => 1,
            ]);
            $existing = !empty($children) ? $children[0] : null;
        }

        if ($existing instanceof WP_Post) {
            if (false === strpos((string) $existing->post_content, '[algq_offer_generator]')) {
                wp_update_post([
                    'ID' => absint($existing->ID),
                    'post_content' => wp_kses_post($content),
                ]);
            }
            return absint($existing->ID);
        }

        $page_id = wp_insert_post([
            'post_title' => sanitize_text_field($title),
            'post_name' => $slug,
            'post_content' => wp_kses_post($content),
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_parent' => $parent_id,
        ], true);

        return is_wp_error($page_id) ? 0 : absint($page_id);
    }

    private static function build_page_path($slug, $parent_id) {
        if (!$parent_id) {
            return sanitize_title($slug);
        }

        $parent = get_post($parent_id);
        if (!$parent instanceof WP_Post) {
            return sanitize_title($slug);
        }

        return trim(get_page_uri($parent_id) . '/' . sanitize_title($slug), '/');
    }
}

register_activation_hook(__FILE__, ['Algq_Offer_Generator_Plugin', 'activate']);
Algq_Offer_Generator_Plugin::instance();
