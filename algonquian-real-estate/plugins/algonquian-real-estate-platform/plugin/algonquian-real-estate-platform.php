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
define( 'ALGQ_PLATFORM_PATH', plugin_dir_path( __FILE__ ) );
define( 'ALGQ_PLATFORM_URL', plugin_dir_url( __FILE__ ) );

final class ALGQ_Platform {

	public function __construct() {
		register_activation_hook( __FILE__, array( __CLASS__, 'activate' ) );
		add_action( 'init', array( $this, 'register_shortcodes' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'admin_post_algq_save_settings', array( $this, 'handle_save_settings' ) );
	}

	public static function activate() {
		global $wpdb;

		add_option( 'algq_platform_version', ALGQ_PLATFORM_VERSION );
		add_option( 'algq_company_name', 'Algonquian Real Estate LLC' );
		add_option( 'algq_release_status', '1.0.0 Release Candidate' );

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$deals_table = $wpdb->prefix . 'algq_deals';
		$logs_table  = $wpdb->prefix . 'algq_activity_logs';

		$deals_sql = "CREATE TABLE {$deals_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			deal_uid VARCHAR(64) NOT NULL,
			property_address TEXT NOT NULL,
			seller_name VARCHAR(255) NULL,
			seller_email VARCHAR(255) NULL,
			seller_phone VARCHAR(100) NULL,
			asking_price DECIMAL(14,2) NULL,
			arv DECIMAL(14,2) NULL,
			repair_estimate DECIMAL(14,2) NULL,
			status VARCHAR(50) NOT NULL DEFAULT 'lead',
			created_at DATETIME NOT NULL,
			updated_at DATETIME NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY deal_uid (deal_uid),
			KEY status (status)
		) {$charset_collate};";

		$logs_sql = "CREATE TABLE {$logs_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			object_type VARCHAR(100) NOT NULL,
			object_id BIGINT UNSIGNED NULL,
			action VARCHAR(100) NOT NULL,
			message TEXT NULL,
			user_id BIGINT UNSIGNED NULL,
			created_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY object_lookup (object_type, object_id),
			KEY action (action)
		) {$charset_collate};";

		dbDelta( $deals_sql );
		dbDelta( $logs_sql );
	}

	public function register_shortcodes() {
		add_shortcode( 'algq_seller_intake', array( $this, 'render_seller_intake' ) );
		add_shortcode( 'algq_mao_calculator', array( $this, 'render_mao_calculator' ) );
		add_shortcode( 'algq_buyer_registration', array( $this, 'render_buyer_registration' ) );
		add_shortcode( 'algq_admin_dashboard', array( $this, 'render_admin_dashboard' ) );
	}

	public function register_admin_menu() {
		add_menu_page(
			esc_html__( 'Algonquian Real Estate', 'algq-platform' ),
			esc_html__( 'Algonquian RE', 'algq-platform' ),
			'manage_options',
			'algq-platform',
			array( $this, 'render_admin_page' ),
			'dashicons-building',
			30
		);
	}

	public function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized.', 'algq-platform' ) );
		}

		$company_name = get_option( 'algq_company_name', 'Algonquian Real Estate LLC' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Algonquian Real Estate Platform', 'algq-platform' ); ?></h1>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="algq_save_settings" />
				<?php wp_nonce_field( 'algq_save_settings_action', 'algq_settings_nonce' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="algq_company_name"><?php echo esc_html__( 'Company Name', 'algq-platform' ); ?></label></th>
						<td><input name="algq_company_name" id="algq_company_name" type="text" class="regular-text" value="<?php echo esc_attr( $company_name ); ?>" /></td>
					</tr>
				</table>
				<?php submit_button( esc_html__( 'Save Settings', 'algq-platform' ) ); ?>
			</form>
		</div>
		<?php
	}

	public function handle_save_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized.', 'algq-platform' ) );
		}

		if ( ! isset( $_POST['algq_settings_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['algq_settings_nonce'] ) ), 'algq_save_settings_action' ) ) {
			wp_die( esc_html__( 'Invalid request.', 'algq-platform' ) );
		}

		$company_name = isset( $_POST['algq_company_name'] ) ? sanitize_text_field( wp_unslash( $_POST['algq_company_name'] ) ) : 'Algonquian Real Estate LLC';
		update_option( 'algq_company_name', $company_name );

		wp_safe_redirect( admin_url( 'admin.php?page=algq-platform&updated=1' ) );
		exit;
	}

	public function render_seller_intake() {
		ob_start();
		?>
		<form class="algq-seller-intake" method="post">
			<?php wp_nonce_field( 'algq_seller_intake_action', 'algq_seller_intake_nonce' ); ?>
			<p><label><?php echo esc_html__( 'Property Address', 'algq-platform' ); ?><br><input type="text" name="property_address" required></label></p>
			<p><label><?php echo esc_html__( 'Seller Name', 'algq-platform' ); ?><br><input type="text" name="seller_name"></label></p>
			<p><label><?php echo esc_html__( 'Seller Email', 'algq-platform' ); ?><br><input type="email" name="seller_email"></label></p>
			<p><label><?php echo esc_html__( 'Asking Price', 'algq-platform' ); ?><br><input type="number" step="0.01" name="asking_price"></label></p>
			<p><button type="submit"><?php echo esc_html__( 'Submit Property', 'algq-platform' ); ?></button></p>
		</form>
		<?php
		return ob_get_clean();
	}

	public function render_mao_calculator() {
		ob_start();
		?>
		<div class="algq-mao-calculator">
			<h3><?php echo esc_html__( 'MAO Calculator', 'algq-platform' ); ?></h3>
			<p><?php echo esc_html__( 'Enter ARV, repairs, and costs to calculate maximum allowable offer.', 'algq-platform' ); ?></p>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_buyer_registration() {
		ob_start();
		?>
		<form class="algq-buyer-registration" method="post">
			<?php wp_nonce_field( 'algq_buyer_registration_action', 'algq_buyer_registration_nonce' ); ?>
			<p><label><?php echo esc_html__( 'Buyer Name', 'algq-platform' ); ?><br><input type="text" name="buyer_name" required></label></p>
			<p><label><?php echo esc_html__( 'Buyer Email', 'algq-platform' ); ?><br><input type="email" name="buyer_email" required></label></p>
			<p><button type="submit"><?php echo esc_html__( 'Register Buyer', 'algq-platform' ); ?></button></p>
		</form>
		<?php
		return ob_get_clean();
	}

	public function render_admin_dashboard() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return esc_html__( 'Unauthorized.', 'algq-platform' );
		}

		ob_start();
		?>
		<div class="algq-admin-dashboard">
			<h3><?php echo esc_html__( 'Acquisitions Dashboard', 'algq-platform' ); ?></h3>
			<p><?php echo esc_html__( 'Deal counts, pipeline activity, documents, and buyer activity will appear here.', 'algq-platform' ); ?></p>
		</div>
		<?php
		return ob_get_clean();
	}
}

new ALGQ_Platform();
