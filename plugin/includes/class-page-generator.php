<?php
/**
 * Automatic page generation for Algonquian Real Estate Platform.
 *
 * @package AlgonquianRealEstate
 */

if (!defined('ABSPATH')) {
    exit;
}

final class Algq_Platform_Page_Generator {
    const OPTION_KEY = 'algq_platform_generated_pages';

    public static function get_pages() {
        return [
            'sell-your-property' => [
                'title' => 'Sell Your Property',
                'shortcode' => '[algq_seller_intake]',
                'content' => "[vc_column_text]\n[algq_seller_intake]\n[/vc_column_text]",
            ],
            'mao-calculator' => [
                'title' => 'MAO Calculator',
                'shortcode' => '[algq_mao_calculator]',
                'content' => "[vc_column_text]\n[algq_mao_calculator]\n[/vc_column_text]",
            ],
            'buyer-registration' => [
                'title' => 'Buyer Registration',
                'shortcode' => '[algq_buyer_registration]',
                'content' => "[vc_column_text]\n[algq_buyer_registration]\n[/vc_column_text]",
            ],
            'dashboard' => [
                'title' => 'Algonquian Dashboard',
                'shortcode' => '[algq_admin_dashboard]',
                'content' => "[vc_column_text]\n[algq_admin_dashboard]\n[/vc_column_text]",
            ],
            'plugin-offer-generator' => [
                'title' => 'Offer Generator',
                'shortcode' => '[algq_offer_generator]',
                'content' => "[vc_column_text]\n[algq_offer_generator]\n[/vc_column_text]",
            ],
        ];
    }

    public static function create_pages() {
        $created = get_option(self::OPTION_KEY, []);
        if (!is_array($created)) {
            $created = [];
        }

        foreach (self::get_pages() as $slug => $page) {
            $existing = get_page_by_path($slug, OBJECT, 'page');

            if ($existing instanceof WP_Post) {
                $created[$slug] = absint($existing->ID);
                continue;
            }

            $page_id = wp_insert_post([
                'post_title'   => sanitize_text_field($page['title']),
                'post_name'    => sanitize_title($slug),
                'post_content' => wp_kses_post($page['content']),
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ], true);

            if (!is_wp_error($page_id)) {
                $created[$slug] = absint($page_id);
            }
        }

        update_option(self::OPTION_KEY, $created, false);
        return $created;
    }
}
