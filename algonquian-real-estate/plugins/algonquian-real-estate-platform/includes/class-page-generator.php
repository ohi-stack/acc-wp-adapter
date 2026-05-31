<?php
/**
 * Automatic page generation for Algonquian Real Estate Platform.
 *
 * @package AlgonquianRealEstatePlatform
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates and tracks WordPress pages required by platform shortcodes.
 */
final class ALGQ_Page_Generator {

	/**
	 * Option key storing generated page IDs.
	 */
	const OPTION_KEY = 'algq_generated_pages';

	/**
	 * Return required pages and shortcode payloads.
	 *
	 * @return array<string,array<string,string>>
	 */
	public static function get_pages() {
		return array(
			'seller_intake'      => array(
				'title'     => 'Sell Your Property',
				'slug'      => 'sell-your-property',
				'shortcode' => '[algq_seller_intake]',
			),
			'mao_calculator'     => array(
				'title'     => 'MAO Calculator',
				'slug'      => 'mao-calculator',
				'shortcode' => '[algq_mao_calculator]',
			),
			'buyer_registration' => array(
				'title'     => 'Buyer Registration',
				'slug'      => 'buyer-registration',
				'shortcode' => '[algq_buyer_registration]',
			),
			'admin_dashboard'    => array(
				'title'     => 'Algonquian Dashboard',
				'slug'      => 'dashboard',
				'shortcode' => '[algq_admin_dashboard]',
			),
		);
	}

	/**
	 * Create all required pages if missing.
	 *
	 * @return array<string,int>
	 */
	public static function create_required_pages() {
		$stored = get_option( self::OPTION_KEY, array() );
		$pages  = self::get_pages();

		foreach ( $pages as $key => $page ) {
			$existing_id = isset( $stored[ $key ] ) ? absint( $stored[ $key ] ) : 0;

			if ( $existing_id && 'trash' !== get_post_status( $existing_id ) ) {
				continue;
			}

			$found = get_page_by_path( $page['slug'] );

			if ( $found && 'trash' !== get_post_status( $found->ID ) ) {
				$stored[ $key ] = (int) $found->ID;
				continue;
			}

			$content = self::build_page_content( $page['shortcode'] );

			$page_id = wp_insert_post(
				array(
					'post_title'   => sanitize_text_field( $page['title'] ),
					'post_name'    => sanitize_title( $page['slug'] ),
					'post_content' => $content,
					'post_status'  => 'publish',
					'post_type'    => 'page',
				),
				true
			);

			if ( ! is_wp_error( $page_id ) ) {
				$stored[ $key ] = (int) $page_id;
			}
		}

		update_option( self::OPTION_KEY, $stored );

		return $stored;
	}

	/**
	 * Build WPBakery-safe page body.
	 *
	 * @param string $shortcode Shortcode string.
	 * @return string
	 */
	private static function build_page_content( $shortcode ) {
		$shortcode = trim( $shortcode );

		return "[vc_row][vc_column][vc_column_text]\n" . $shortcode . "\n[/vc_column_text][/vc_column][/vc_row]";
	}
}
