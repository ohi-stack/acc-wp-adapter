<?php
/**
 * REST API layer for Algonquian Offer Generator.
 *
 * @package Algq_Offer_Generator
 */

if (!defined('ABSPATH')) {
    exit;
}

class Algq_Offer_REST_API {
    const NAMESPACE = 'algq-offer-generator/v1';

    public static function init() {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    public static function register_routes() {
        register_rest_route(self::NAMESPACE, '/offers', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [__CLASS__, 'list_offers'],
                'permission_callback' => [__CLASS__, 'can_manage_offers'],
            ],
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [__CLASS__, 'create_offer'],
                'permission_callback' => [__CLASS__, 'can_manage_offers'],
            ],
        ]);

        register_rest_route(self::NAMESPACE, '/offers/(?P<id>\d+)', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [__CLASS__, 'get_offer'],
            'permission_callback' => [__CLASS__, 'can_manage_offers'],
        ]);

        register_rest_route(self::NAMESPACE, '/documents/(?P<id>\d+)/pdf', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [__CLASS__, 'render_pdf'],
            'permission_callback' => [__CLASS__, 'can_manage_offers'],
        ]);

        register_rest_route(self::NAMESPACE, '/documents/(?P<id>\d+)/signature', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [__CLASS__, 'send_signature'],
            'permission_callback' => [__CLASS__, 'can_manage_offers'],
        ]);
    }

    public static function can_manage_offers() {
        return current_user_can('manage_options') || current_user_can('edit_posts');
    }

    public static function list_offers(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'algq_offers';
        $deal_id = absint($request->get_param('deal_id'));
        $where = $deal_id ? $wpdb->prepare('WHERE deal_id = %d', $deal_id) : '';
        $rows = $wpdb->get_results("SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT 100", ARRAY_A);
        return rest_ensure_response($rows ?: []);
    }

    public static function create_offer(WP_REST_Request $request) {
        $deal_id = absint($request->get_param('deal_id'));
        $document_type = sanitize_key($request->get_param('document_type'));

        if (!$deal_id || !$document_type) {
            return new WP_Error('algq_offer_missing_fields', 'deal_id and document_type are required.', ['status' => 400]);
        }

        if (!class_exists('Algq_Offer_Document_Generator')) {
            return new WP_Error('algq_offer_generator_missing', 'Document generator class is unavailable.', ['status' => 500]);
        }

        $result = Algq_Offer_Document_Generator::generate($deal_id, $document_type);
        return rest_ensure_response($result);
    }

    public static function get_offer(WP_REST_Request $request) {
        global $wpdb;
        $id = absint($request['id']);
        $table = $wpdb->prefix . 'algq_offers';
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);

        if (!$row) {
            return new WP_Error('algq_offer_not_found', 'Offer not found.', ['status' => 404]);
        }

        return rest_ensure_response($row);
    }

    public static function render_pdf(WP_REST_Request $request) {
        $document_id = absint($request['id']);
        if (!class_exists('Algq_Offer_PDF_Engine')) {
            return new WP_Error('algq_pdf_engine_missing', 'PDF engine class is unavailable.', ['status' => 500]);
        }
        return rest_ensure_response(Algq_Offer_PDF_Engine::render_document($document_id));
    }

    public static function send_signature(WP_REST_Request $request) {
        $document_id = absint($request['id']);
        $signers = $request->get_param('signers');
        if (!is_array($signers)) {
            $signers = [];
        }
        if (!class_exists('Algq_Offer_Signature_Engine')) {
            return new WP_Error('algq_signature_engine_missing', 'Signature engine class is unavailable.', ['status' => 500]);
        }
        return rest_ensure_response(Algq_Offer_Signature_Engine::send_for_signature($document_id, $signers));
    }
}
