<?php
/**
 * Audit log support for Algonquian Offer Generator.
 *
 * @package Algq_Offer_Generator
 */

if (!defined('ABSPATH')) {
    exit;
}

final class Algq_Offer_Audit_Log {
    public static function record($deal_id, $action, $metadata = []) {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'algq_offer_audit_log',
            [
                'deal_id'    => absint($deal_id),
                'action'     => sanitize_key($action),
                'metadata'   => wp_json_encode($metadata),
                'user_id'    => get_current_user_id(),
                'created_at' => current_time('mysql'),
            ],
            ['%d', '%s', '%s', '%d', '%s']
        );
    }

    public static function recent($limit = 20) {
        global $wpdb;
        $limit = absint($limit) ?: 20;
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}algq_offer_audit_log ORDER BY created_at DESC LIMIT %d",
                $limit
            ),
            ARRAY_A
        );
    }
}
