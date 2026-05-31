<?php
/**
 * Plugin Name: Algonquian Real Estate Platform
 * Plugin URI: https://algonquianrealestate.com
 * Description: Core WordPress platform plugin for Algonquian Real Estate deal intake, underwriting, buyer registration, and administrative dashboard workflows.
 * Version: 1.0.0
 * Author: Onegodian | Algonquian Real Estate
 * Text Domain: algonquian-real-estate
 * Requires at least: 6.4
 * Requires PHP: 7.4
 *
 * @package AlgonquianRealEstate
 */

if (!defined('ABSPATH')) {
    exit;
}

final class Algonquian_Real_Estate_Platform {
    const VERSION = '1.0.0';
    const OPTION_VERSION = 'algq_platform_version';
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
        add_action('admin_post_algq_save_settings', [$this, 'handle_save_settings']);
    }

    public static function activate() {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $deals_table = $wpdb->prefix . 'algq_deals';
        $buyers_table = $wpdb->prefix . 'algq_buyers';
        $settings_table = $wpdb->prefix . 'algq_platform_events';

        $sql = "CREATE TABLE {$deals_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            seller_name VARCHAR(190) NOT NULL DEFAULT '',
            seller_email VARCHAR(190) NOT NULL DEFAULT '',
            seller_phone VARCHAR(60) NOT NULL DEFAULT '',
            property_address TEXT NULL,
            asking_price DECIMAL(15,2) NULL,
            status VARCHAR(60) NOT NULL DEFAULT 'lead_captured',
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY seller_email (seller_email)
        ) {$charset_collate};

        CREATE TABLE {$buyers_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            buyer_name VARCHAR(190) NOT NULL DEFAULT '',
            buyer_email VARCHAR(190) NOT NULL DEFAULT '',
            buyer_phone VARCHAR(60) NOT NULL DEFAULT '',
            investment_criteria TEXT NULL,
            status VARCHAR(60) NOT NULL DEFAULT 'registered',
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            KEY buyer_email (buyer_email),
            KEY status (status)
        ) {$charset_collate};

        CREATE TABLE {$settings_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            event_type VARCHAR(120) NOT NULL DEFAULT '',
            event_data LONGTEXT NULL,
            user_id BIGINT UNSIGNED NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            KEY event_type (event_type),
            KEY user_id (user_id)
        ) {$charset_collate};";

        dbDelta($sql);

        update_option(self::OPTION_VERSION, self::VERSION, false);
        add_option('algq_platform_release_status', 'release_ready', '', false);
    }

    public static function deactivate() {
        // Reserved for future scheduled-event cleanup. Data is intentionally retained.
    }

    public function register_shortcodes() {
        add_shortcode('algq_seller_intake', [$this, 'shortcode_seller_intake']);
        add_shortcode('algq_mao_calculator', [$this, 'shortcode_mao_calculator']);
        add_shortcode('algq_buyer_registration', [$this, 'shortcode_buyer_registration']);
        add_shortcode('algq_admin_dashboard', [$this, 'shortcode_admin_dashboard']);
    }

    public function register_admin_menu() {
        add_menu_page(
            esc_html__('Algonquian RE', 'algonquian-real-estate'),
            esc_html__('Algonquian RE', 'algonquian-real-estate'),
            self::CAPABILITY,
            'algq-platform',
            [$this, 'render_admin_dashboard'],
            'dashicons-building',
            26
        );

        add_submenu_page(
            'algq-platform',
            esc_html__('Settings', 'algonquian-real-estate'),
            esc_html__('Settings', 'algonquian-real-estate'),
            self::CAPABILITY,
            'algq-platform-settings',
            [$this, 'render_settings_page']
        );
    }

    public function shortcode_seller_intake($atts = []) {
        $atts = shortcode_atts(['source' => 'website'], $atts, 'algq_seller_intake');
        ob_start();
        ?>
        <form class="algq-form algq-seller-intake" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('algq_seller_intake_submit', 'algq_nonce'); ?>
            <input type="hidden" name="action" value="algq_save_settings" />
            <input type="hidden" name="algq_form_type" value="seller_intake" />
            <input type="hidden" name="lead_source" value="<?php echo esc_attr(sanitize_text_field($atts['source'])); ?>" />
            <p><label>Seller Name<br><input type="text" name="seller_name" required></label></p>
            <p><label>Email<br><input type="email" name="seller_email"></label></p>
            <p><label>Phone<br><input type="text" name="seller_phone"></label></p>
            <p><label>Property Address<br><textarea name="property_address" rows="3"></textarea></label></p>
            <p><label>Asking Price<br><input type="number" step="0.01" name="asking_price"></label></p>
            <p><button type="submit">Submit Property</button></p>
        </form>
        <?php
        return ob_get_clean();
    }

    public function shortcode_mao_calculator() {
        ob_start();
        ?>
        <div class="algq-mao-calculator" data-algq-component="mao-calculator">
            <h3><?php echo esc_html__('MAO Calculator', 'algonquian-real-estate'); ?></h3>
            <p><?php echo esc_html__('Formula: ARV × 70% − Repairs − Fees = Maximum Allowable Offer.', 'algonquian-real-estate'); ?></p>
            <label>ARV <input type="number" class="algq-mao-arv" step="0.01"></label>
            <label>Repairs <input type="number" class="algq-mao-repairs" step="0.01"></label>
            <label>Fees <input type="number" class="algq-mao-fees" step="0.01"></label>
            <button type="button" class="algq-mao-run">Calculate</button>
            <output class="algq-mao-result"></output>
        </div>
        <?php
        return ob_get_clean();
    }

    public function shortcode_buyer_registration() {
        ob_start();
        ?>
        <form class="algq-form algq-buyer-registration" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('algq_buyer_registration_submit', 'algq_nonce'); ?>
            <input type="hidden" name="action" value="algq_save_settings" />
            <input type="hidden" name="algq_form_type" value="buyer_registration" />
            <p><label>Buyer Name<br><input type="text" name="buyer_name" required></label></p>
            <p><label>Email<br><input type="email" name="buyer_email" required></label></p>
            <p><label>Phone<br><input type="text" name="buyer_phone"></label></p>
            <p><label>Investment Criteria<br><textarea name="investment_criteria" rows="4"></textarea></label></p>
            <p><button type="submit">Register Buyer</button></p>
        </form>
        <?php
        return ob_get_clean();
    }

    public function shortcode_admin_dashboard() {
        if (!current_user_can(self::CAPABILITY)) {
            return esc_html__('Access restricted.', 'algonquian-real-estate');
        }

        ob_start();
        $this->render_dashboard_markup();
        return ob_get_clean();
    }

    public function render_admin_dashboard() {
        if (!current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('Insufficient permissions.', 'algonquian-real-estate'));
        }
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Algonquian Real Estate Platform', 'algonquian-real-estate') . '</h1>';
        $this->render_dashboard_markup();
        echo '</div>';
    }

    private function render_dashboard_markup() {
        global $wpdb;
        $deals = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}algq_deals");
        $buyers = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}algq_buyers");
        ?>
        <div class="algq-dashboard">
            <p><strong><?php echo esc_html__('Release Status:', 'algonquian-real-estate'); ?></strong> <?php echo esc_html(get_option('algq_platform_release_status', 'release_ready')); ?></p>
            <ul>
                <li><?php echo esc_html__('Deals:', 'algonquian-real-estate'); ?> <?php echo esc_html((string) absint($deals)); ?></li>
                <li><?php echo esc_html__('Buyers:', 'algonquian-real-estate'); ?> <?php echo esc_html((string) absint($buyers)); ?></li>
                <li><?php echo esc_html__('Version:', 'algonquian-real-estate'); ?> <?php echo esc_html(self::VERSION); ?></li>
            </ul>
        </div>
        <?php
    }

    public function render_settings_page() {
        if (!current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('Insufficient permissions.', 'algonquian-real-estate'));
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Algonquian Platform Settings', 'algonquian-real-estate'); ?></h1>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('algq_platform_settings_save', 'algq_nonce'); ?>
                <input type="hidden" name="action" value="algq_save_settings" />
                <input type="hidden" name="algq_form_type" value="settings" />
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="algq_company_name">Company Name</label></th>
                        <td><input id="algq_company_name" name="company_name" class="regular-text" value="<?php echo esc_attr(get_option('algq_company_name', 'Algonquian Real Estate LLC')); ?>"></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function handle_save_settings() {
        $form_type = isset($_POST['algq_form_type']) ? sanitize_key(wp_unslash($_POST['algq_form_type'])) : '';

        if ('seller_intake' === $form_type) {
            $this->handle_seller_intake();
            return;
        }

        if ('buyer_registration' === $form_type) {
            $this->handle_buyer_registration();
            return;
        }

        if (!current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('Insufficient permissions.', 'algonquian-real-estate'));
        }

        check_admin_referer('algq_platform_settings_save', 'algq_nonce');
        $company_name = isset($_POST['company_name']) ? sanitize_text_field(wp_unslash($_POST['company_name'])) : '';
        update_option('algq_company_name', $company_name, false);
        wp_safe_redirect(admin_url('admin.php?page=algq-platform-settings&updated=1'));
        exit;
    }

    private function handle_seller_intake() {
        check_admin_referer('algq_seller_intake_submit', 'algq_nonce');
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'algq_deals',
            [
                'seller_name'      => isset($_POST['seller_name']) ? sanitize_text_field(wp_unslash($_POST['seller_name'])) : '',
                'seller_email'     => isset($_POST['seller_email']) ? sanitize_email(wp_unslash($_POST['seller_email'])) : '',
                'seller_phone'     => isset($_POST['seller_phone']) ? sanitize_text_field(wp_unslash($_POST['seller_phone'])) : '',
                'property_address' => isset($_POST['property_address']) ? sanitize_textarea_field(wp_unslash($_POST['property_address'])) : '',
                'asking_price'     => isset($_POST['asking_price']) ? floatval(wp_unslash($_POST['asking_price'])) : null,
                'status'           => 'lead_captured',
                'created_at'       => current_time('mysql'),
                'updated_at'       => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%s', '%f', '%s', '%s', '%s']
        );

        wp_safe_redirect(wp_get_referer() ? wp_get_referer() : home_url('/'));
        exit;
    }

    private function handle_buyer_registration() {
        check_admin_referer('algq_buyer_registration_submit', 'algq_nonce');
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'algq_buyers',
            [
                'buyer_name'          => isset($_POST['buyer_name']) ? sanitize_text_field(wp_unslash($_POST['buyer_name'])) : '',
                'buyer_email'         => isset($_POST['buyer_email']) ? sanitize_email(wp_unslash($_POST['buyer_email'])) : '',
                'buyer_phone'         => isset($_POST['buyer_phone']) ? sanitize_text_field(wp_unslash($_POST['buyer_phone'])) : '',
                'investment_criteria' => isset($_POST['investment_criteria']) ? sanitize_textarea_field(wp_unslash($_POST['investment_criteria'])) : '',
                'status'              => 'registered',
                'created_at'          => current_time('mysql'),
                'updated_at'          => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        wp_safe_redirect(wp_get_referer() ? wp_get_referer() : home_url('/'));
        exit;
    }
}

register_activation_hook(__FILE__, ['Algonquian_Real_Estate_Platform', 'activate']);
register_deactivation_hook(__FILE__, ['Algonquian_Real_Estate_Platform', 'deactivate']);

Algonquian_Real_Estate_Platform::instance();
