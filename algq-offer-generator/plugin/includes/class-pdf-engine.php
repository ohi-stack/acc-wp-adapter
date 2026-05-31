<?php
/**
 * PDF rendering and storage layer for Algonquian Offer Generator.
 *
 * This class is intentionally adapter-friendly. If DOMPDF, mPDF, or a hosted
 * renderer is available, the renderer can be connected through the
 * algq_offer_pdf_render_binary filter.
 *
 * @package Algq_Offer_Generator
 */

if (!defined('ABSPATH')) {
    exit;
}

class Algq_Offer_PDF_Engine {
    const STATUS_RENDERED = 'rendered';
    const STATUS_FAILED   = 'failed';

    /**
     * Render a document record into a PDF file.
     *
     * @param int   $document_id Document record ID.
     * @param array $options     Optional rendering options.
     * @return array|WP_Error
     */
    public static function render_document($document_id, $options = []) {
        global $wpdb;

        $document_id = absint($document_id);
        if (!$document_id) {
            return new WP_Error('algq_pdf_invalid_document', 'A valid document ID is required.', ['status' => 400]);
        }

        $documents_table = $wpdb->prefix . 'algq_documents';
        $document = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$documents_table} WHERE id = %d", $document_id), ARRAY_A);

        if (!$document) {
            return new WP_Error('algq_pdf_document_not_found', 'Document not found.', ['status' => 404]);
        }

        $html = isset($document['html_content']) ? $document['html_content'] : '';
        if (!$html && !empty($document['content'])) {
            $html = $document['content'];
        }

        if (!$html) {
            return new WP_Error('algq_pdf_missing_html', 'Document does not contain renderable HTML.', ['status' => 422]);
        }

        $deal_id       = isset($document['deal_id']) ? absint($document['deal_id']) : 0;
        $document_type = isset($document['document_type']) ? sanitize_key($document['document_type']) : 'document';
        $version       = self::next_version($deal_id, $document_type);

        $path_data = self::build_storage_path($deal_id, $document_type, $version);
        if (is_wp_error($path_data)) {
            return $path_data;
        }

        $pdf_binary = self::render_pdf_binary($html, $options);
        if (is_wp_error($pdf_binary)) {
            self::mark_failed($document_id, $pdf_binary->get_error_message());
            return $pdf_binary;
        }

        $written = file_put_contents($path_data['absolute_path'], $pdf_binary);
        if (false === $written) {
            self::mark_failed($document_id, 'Unable to write PDF file to storage.');
            return new WP_Error('algq_pdf_write_failed', 'Unable to write PDF file to storage.', ['status' => 500]);
        }

        $checksum = hash_file('sha256', $path_data['absolute_path']);

        $wpdb->update(
            $documents_table,
            [
                'file_path'      => $path_data['relative_path'],
                'file_url'       => $path_data['url'],
                'file_checksum'  => $checksum,
                'version'        => $version,
                'status'         => self::STATUS_RENDERED,
                'rendered_at'    => current_time('mysql'),
                'updated_at'     => current_time('mysql'),
            ],
            ['id' => $document_id],
            ['%s', '%s', '%s', '%d', '%s', '%s', '%s'],
            ['%d']
        );

        do_action('algq_offer_pdf_rendered', $document_id, $deal_id, $path_data['url']);

        if (class_exists('Algq_Offer_Audit_Log')) {
            Algq_Offer_Audit_Log::record($deal_id, 'pdf_rendered', [
                'document_id' => $document_id,
                'document_type' => $document_type,
                'version' => $version,
                'checksum' => $checksum,
            ]);
        }

        return [
            'document_id' => $document_id,
            'deal_id' => $deal_id,
            'document_type' => $document_type,
            'version' => $version,
            'status' => self::STATUS_RENDERED,
            'file_url' => $path_data['url'],
            'file_path' => $path_data['relative_path'],
            'checksum' => $checksum,
        ];
    }

    /**
     * Render arbitrary HTML to PDF and store it as a document.
     *
     * @param int    $deal_id       Deal ID.
     * @param string $document_type Document type.
     * @param string $html          Rendered HTML.
     * @param array  $options       Options.
     * @return array|WP_Error
     */
    public static function render_pdf($deal_id, $document_type, $html, $options = []) {
        global $wpdb;

        $deal_id = absint($deal_id);
        $document_type = sanitize_key($document_type);

        if (!$deal_id || !$document_type || empty($html)) {
            return new WP_Error('algq_pdf_missing_fields', 'deal_id, document_type, and html are required.', ['status' => 400]);
        }

        $documents_table = $wpdb->prefix . 'algq_documents';
        $wpdb->insert(
            $documents_table,
            [
                'deal_id'       => $deal_id,
                'document_type' => $document_type,
                'html_content'  => wp_kses_post($html),
                'status'        => 'draft',
                'created_at'    => current_time('mysql'),
                'updated_at'    => current_time('mysql'),
            ],
            ['%d', '%s', '%s', '%s', '%s', '%s']
        );

        $document_id = absint($wpdb->insert_id);
        return self::render_document($document_id, $options);
    }

    protected static function render_pdf_binary($html, $options = []) {
        $html = self::wrap_html($html, $options);

        /**
         * Allows DOMPDF/mPDF/service adapter to provide a real PDF binary.
         *
         * Returning null falls back to a minimal PDF placeholder so development
         * environments can test storage/versioning without a renderer installed.
         */
        $binary = apply_filters('algq_offer_pdf_render_binary', null, $html, $options);

        if (is_wp_error($binary)) {
            return $binary;
        }

        if (is_string($binary) && strlen($binary) > 0) {
            return $binary;
        }

        return self::minimal_pdf_placeholder($html);
    }

    protected static function wrap_html($html, $options = []) {
        $title = isset($options['title']) ? sanitize_text_field($options['title']) : 'Algonquian Offer Document';

        return '<!doctype html><html><head><meta charset="utf-8"><title>' . esc_html($title) . '</title>' .
            '<style>body{font-family:Arial,sans-serif;font-size:12px;line-height:1.45;color:#111}.algq-document{max-width:800px;margin:0 auto}.algq-header{border-bottom:2px solid #b08d2c;margin-bottom:18px;padding-bottom:8px}.algq-title{font-size:22px;font-weight:700}.algq-section{margin:18px 0}.algq-signature{margin-top:42px;border-top:1px solid #222;padding-top:8px}</style>' .
            '</head><body><div class="algq-document">' . $html . '</div></body></html>';
    }

    protected static function minimal_pdf_placeholder($html) {
        $text = wp_strip_all_tags($html);
        $text = substr(preg_replace('/\s+/', ' ', $text), 0, 1800);
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);

        $stream = "BT /F1 10 Tf 50 760 Td (Algonquian Offer Generator PDF Placeholder) Tj 0 -18 Td (" . $text . ") Tj ET";
        $len = strlen($stream);

        return "%PDF-1.4\n" .
            "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n" .
            "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n" .
            "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj\n" .
            "4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj\n" .
            "5 0 obj << /Length {$len} >> stream\n{$stream}\nendstream endobj\n" .
            "trailer << /Root 1 0 R >>\n%%EOF";
    }

    protected static function build_storage_path($deal_id, $document_type, $version) {
        $uploads = wp_upload_dir();
        if (!empty($uploads['error'])) {
            return new WP_Error('algq_pdf_upload_dir_error', $uploads['error'], ['status' => 500]);
        }

        $deal_segment = $deal_id ? 'deal-' . $deal_id : 'unassigned';
        $dir = trailingslashit($uploads['basedir']) . 'algq/offers/' . $deal_segment . '/' . sanitize_key($document_type);
        $url_base = trailingslashit($uploads['baseurl']) . 'algq/offers/' . $deal_segment . '/' . sanitize_key($document_type);

        if (!wp_mkdir_p($dir)) {
            return new WP_Error('algq_pdf_directory_failed', 'Unable to create PDF storage directory.', ['status' => 500]);
        }

        $filename = sanitize_file_name($document_type . '-v' . absint($version) . '.pdf');
        $absolute_path = trailingslashit($dir) . $filename;
        $relative_path = 'algq/offers/' . $deal_segment . '/' . sanitize_key($document_type) . '/' . $filename;

        return [
            'absolute_path' => $absolute_path,
            'relative_path' => $relative_path,
            'url' => trailingslashit($url_base) . $filename,
        ];
    }

    protected static function next_version($deal_id, $document_type) {
        global $wpdb;
        $table = $wpdb->prefix . 'algq_documents';
        $max = $wpdb->get_var($wpdb->prepare(
            "SELECT MAX(version) FROM {$table} WHERE deal_id = %d AND document_type = %s",
            absint($deal_id),
            sanitize_key($document_type)
        ));

        return absint($max) + 1;
    }

    protected static function mark_failed($document_id, $message) {
        global $wpdb;
        $table = $wpdb->prefix . 'algq_documents';
        $wpdb->update(
            $table,
            [
                'status' => self::STATUS_FAILED,
                'error_message' => sanitize_text_field($message),
                'updated_at' => current_time('mysql'),
            ],
            ['id' => absint($document_id)],
            ['%s', '%s', '%s'],
            ['%d']
        );
    }
}
