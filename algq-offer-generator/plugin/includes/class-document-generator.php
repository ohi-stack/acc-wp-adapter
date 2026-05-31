<?php
if (!defined('ABSPATH')) { exit; }

final class Algq_Offer_Document_Generator {
    public static function generate($deal_id, $document_type = 'letter-of-intent') {
        global $wpdb;
        $deal_id = absint($deal_id);
        $document_type = sanitize_key($document_type);
        $template = self::load_template($document_type);
        $data = class_exists('Algq_Offer_Merge_Engine') ? Algq_Offer_Merge_Engine::get_deal_data($deal_id) : [];
        $html = class_exists('Algq_Offer_Merge_Engine') ? Algq_Offer_Merge_Engine::merge($template, $data) : $template;
        $now = current_time('mysql');

        $wpdb->insert($wpdb->prefix . 'algq_offers', [
            'deal_id' => $deal_id,
            'document_type' => $document_type,
            'status' => 'draft',
            'version' => 1,
            'created_by' => get_current_user_id(),
            'created_at' => $now,
            'updated_at' => $now,
        ], ['%d','%s','%s','%d','%d','%s','%s']);
        $offer_id = absint($wpdb->insert_id);

        $wpdb->insert($wpdb->prefix . 'algq_documents', [
            'deal_id' => $deal_id,
            'offer_id' => $offer_id,
            'document_type' => $document_type,
            'title' => ucwords(str_replace('-', ' ', $document_type)),
            'html_content' => wp_kses_post($html),
            'version' => 1,
            'status' => 'draft',
            'generated_by' => get_current_user_id(),
            'created_at' => $now,
            'updated_at' => $now,
        ], ['%d','%d','%s','%s','%s','%d','%s','%d','%s','%s']);
        $document_id = absint($wpdb->insert_id);

        if (class_exists('Algq_Offer_Audit_Log')) {
            Algq_Offer_Audit_Log::record($deal_id, 'document_generated', ['document_id' => $document_id, 'document_type' => $document_type]);
        }
        do_action('algq_offer_document_generated', $document_id, $deal_id, $document_type);
        return ['offer_id' => $offer_id, 'document_id' => $document_id, 'deal_id' => $deal_id, 'document_type' => $document_type, 'status' => 'draft'];
    }

    public static function load_template($document_type) {
        $map = [
            'purchase-agreement' => 'purchase-agreement.php',
            'letter-of-intent' => 'letter-of-intent.php',
            'seller-financing' => 'seller-financing.php',
            'cash-offer-summary' => 'letter-of-intent.php',
        ];
        $file = ALGQ_OFFER_DIR . 'templates/' . ($map[$document_type] ?? 'letter-of-intent.php');
        if (!file_exists($file)) { return '<h1>{{document_type}}</h1><p>{{property_address}}</p>'; }
        ob_start();
        include $file;
        return ob_get_clean();
    }
}
