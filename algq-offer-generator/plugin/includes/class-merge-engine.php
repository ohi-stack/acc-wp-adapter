<?php
/**
 * Merge engine for Algonquian Offer Generator.
 *
 * @package Algq_Offer_Generator
 */

if (!defined('ABSPATH')) {
    exit;
}

final class Algq_Offer_Merge_Engine {
    public static function merge($template, $data) {
        $template = (string) $template;
        $data = is_array($data) ? $data : [];

        foreach ($data as $key => $value) {
            $tag = '{{' . sanitize_key($key) . '}}';
            $template = str_replace($tag, esc_html((string) $value), $template);
        }

        return preg_replace('/{{[a-zA-Z0-9_\-]+}}/', '', $template);
    }

    public static function get_deal_data($deal_id) {
        global $wpdb;
        $deal_id = absint($deal_id);

        $deal = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}algq_deals WHERE id = %d", $deal_id), ARRAY_A);
        $terms = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}algq_offer_terms WHERE deal_id = %d ORDER BY id DESC LIMIT 1", $deal_id), ARRAY_A);

        $data = [
            'deal_id' => $deal_id,
            'offer_date' => date_i18n(get_option('date_format')),
            'buyer_name' => get_option('algq_offer_buyer_name', 'Algonquian Real Estate LLC'),
            'buyer_address' => get_option('algq_offer_buyer_address', 'Waterbury, Connecticut'),
        ];

        if (is_array($deal)) {
            $data = array_merge($data, $deal);
            $data['seller_name'] = $deal['seller_name'] ?? '';
            $data['seller_email'] = $deal['seller_email'] ?? '';
            $data['seller_phone'] = $deal['seller_phone'] ?? '';
            $data['property_address'] = $deal['property_address'] ?? '';
        }

        if (is_array($terms)) {
            $data = array_merge($data, $terms);
        }

        return apply_filters('algq_offer_merge_data', $data, $deal_id);
    }
}
