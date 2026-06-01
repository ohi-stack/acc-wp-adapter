<?php
/**
 * Automation hooks for Algonquian Offer Generator.
 *
 * @package Algq_Offer_Generator
 */

if (!defined('ABSPATH')) {
    exit;
}

final class Algq_Offer_Automation {
    public static function init() {
        add_action('algq_pipeline_status_changed', [__CLASS__, 'handle_pipeline_status_changed'], 10, 3);
        add_action('algq_deal_underwriting_complete', [__CLASS__, 'handle_underwriting_complete'], 10, 1);
    }

    public static function handle_pipeline_status_changed($deal_id, $old_status, $new_status) {
        $deal_id = absint($deal_id);
        $new_status = sanitize_key($new_status);

        if (!$deal_id) {
            return;
        }

        if ('offer_sent' === $new_status || 'offer-ready' === $new_status) {
            self::generate_default_offer($deal_id, 'letter-of-intent');
        }

        if (class_exists('Algq_Offer_Audit_Log')) {
            Algq_Offer_Audit_Log::record($deal_id, 'pipeline_status_changed', [
                'old_status' => sanitize_key($old_status),
                'new_status' => $new_status,
            ]);
        }
    }

    public static function handle_underwriting_complete($deal_id) {
        $deal_id = absint($deal_id);
        if (!$deal_id) {
            return;
        }

        if (get_option('algq_offer_auto_generate_after_underwriting', 'no') === 'yes') {
            self::generate_default_offer($deal_id, get_option('algq_offer_default_document_type', 'letter-of-intent'));
        }
    }

    public static function generate_default_offer($deal_id, $document_type = 'letter-of-intent') {
        $deal_id = absint($deal_id);
        $document_type = sanitize_key($document_type);

        if (!$deal_id || !class_exists('Algq_Offer_Document_Generator')) {
            return false;
        }

        $result = Algq_Offer_Document_Generator::generate($deal_id, $document_type);

        if (class_exists('Algq_Offer_Audit_Log')) {
            Algq_Offer_Audit_Log::record($deal_id, 'automation_generated_offer', [
                'document_type' => $document_type,
                'result' => $result,
            ]);
        }

        return $result;
    }
}
