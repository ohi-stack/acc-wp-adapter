<?php
/**
 * Admin dashboard for Algonquian Offer Generator.
 *
 * @package Algq_Offer_Generator
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$offers_table    = $wpdb->prefix . 'algq_offers';
$documents_table = $wpdb->prefix . 'algq_documents';
$audit_table     = $wpdb->prefix . 'algq_offer_audit_log';

$offers_count    = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$offers_table}");
$documents_count = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$documents_table}");
$pdf_count       = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$documents_table} WHERE status = %s", 'rendered'));
$draft_count     = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$documents_table} WHERE status = %s", 'draft'));

$recent_documents = $wpdb->get_results("SELECT id, deal_id, document_type, title, status, version, created_at, file_url FROM {$documents_table} ORDER BY created_at DESC LIMIT 8", ARRAY_A);
$recent_activity  = $wpdb->get_results("SELECT deal_id, action, metadata, created_at FROM {$audit_table} ORDER BY created_at DESC LIMIT 8", ARRAY_A);
?>

<div class="algq-offer-admin">
    <div class="algq-offer-hero">
        <span class="algq-badge"><?php echo esc_html__('Production Candidate', 'algq-offer-generator'); ?></span>
        <h1><?php echo esc_html__('Algonquian Offer Generator', 'algq-offer-generator'); ?></h1>
        <p><?php echo esc_html__('Generate acquisition offers, purchase agreements, LOIs, seller financing proposals, PDFs, and deal-linked document records from one controlled workspace.', 'algq-offer-generator'); ?></p>
        <div class="algq-actions">
            <a class="algq-button gold" href="<?php echo esc_url(admin_url('admin.php?page=algq-offer-templates')); ?>"><?php echo esc_html__('Manage Templates', 'algq-offer-generator'); ?></a>
            <a class="algq-button secondary" href="<?php echo esc_url(admin_url('admin.php?page=algq-offer-documents')); ?>"><?php echo esc_html__('View Documents', 'algq-offer-generator'); ?></a>
            <a class="algq-button secondary" href="<?php echo esc_url(admin_url('admin.php?page=algq-offer-settings')); ?>"><?php echo esc_html__('Settings', 'algq-offer-generator'); ?></a>
        </div>
    </div>

    <div class="algq-grid">
        <div class="algq-card"><h3><?php echo esc_html__('Offers Generated', 'algq-offer-generator'); ?></h3><div class="algq-metric"><?php echo esc_html((string) $offers_count); ?></div><p class="algq-muted"><?php echo esc_html__('Total offer records.', 'algq-offer-generator'); ?></p></div>
        <div class="algq-card"><h3><?php echo esc_html__('Documents Generated', 'algq-offer-generator'); ?></h3><div class="algq-metric"><?php echo esc_html((string) $documents_count); ?></div><p class="algq-muted"><?php echo esc_html__('Deal-linked document records.', 'algq-offer-generator'); ?></p></div>
        <div class="algq-card"><h3><?php echo esc_html__('PDFs Rendered', 'algq-offer-generator'); ?></h3><div class="algq-metric"><?php echo esc_html((string) $pdf_count); ?></div><p class="algq-muted"><?php echo esc_html__('Documents converted to PDF.', 'algq-offer-generator'); ?></p></div>
        <div class="algq-card"><h3><?php echo esc_html__('Draft Queue', 'algq-offer-generator'); ?></h3><div class="algq-metric"><?php echo esc_html((string) $draft_count); ?></div><p class="algq-muted"><?php echo esc_html__('Draft documents awaiting review.', 'algq-offer-generator'); ?></p></div>
    </div>

    <div class="algq-card">
        <h2><?php echo esc_html__('Generate Document', 'algq-offer-generator'); ?></h2>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('algq_generate_offer', 'algq_nonce'); ?>
            <input type="hidden" name="action" value="algq_generate_offer">
            <div class="algq-form-row">
                <label for="algq_deal_id"><?php echo esc_html__('Deal ID', 'algq-offer-generator'); ?></label>
                <input id="algq_deal_id" name="deal_id" type="number" min="0" value="0">
            </div>
            <div class="algq-form-row">
                <label for="algq_document_type"><?php echo esc_html__('Document Type', 'algq-offer-generator'); ?></label>
                <select id="algq_document_type" name="document_type">
                    <option value="purchase-agreement"><?php echo esc_html__('Purchase Agreement', 'algq-offer-generator'); ?></option>
                    <option value="letter-of-intent"><?php echo esc_html__('Letter of Intent', 'algq-offer-generator'); ?></option>
                    <option value="seller-financing"><?php echo esc_html__('Seller Financing Offer', 'algq-offer-generator'); ?></option>
                    <option value="cash-offer-summary"><?php echo esc_html__('Cash Offer Summary', 'algq-offer-generator'); ?></option>
                </select>
            </div>
            <?php submit_button(__('Generate Offer Document', 'algq-offer-generator')); ?>
        </form>
    </div>

    <div class="algq-grid" style="grid-template-columns:2fr 2fr;">
        <div class="algq-card">
            <h2><?php echo esc_html__('Recent Documents', 'algq-offer-generator'); ?></h2>
            <table class="algq-table">
                <thead><tr><th><?php echo esc_html__('Type', 'algq-offer-generator'); ?></th><th><?php echo esc_html__('Deal', 'algq-offer-generator'); ?></th><th><?php echo esc_html__('Status', 'algq-offer-generator'); ?></th><th><?php echo esc_html__('PDF', 'algq-offer-generator'); ?></th></tr></thead>
                <tbody>
                <?php if (!empty($recent_documents)) : foreach ($recent_documents as $document) : ?>
                    <tr>
                        <td><?php echo esc_html($document['title'] ?: ucwords(str_replace('-', ' ', $document['document_type']))); ?></td>
                        <td><?php echo esc_html((string) absint($document['deal_id'])); ?></td>
                        <td><span class="algq-status <?php echo esc_attr($document['status']); ?>"><?php echo esc_html($document['status']); ?></span></td>
                        <td><?php echo !empty($document['file_url']) ? '<a href="' . esc_url($document['file_url']) . '" target="_blank" rel="noopener">' . esc_html__('Open', 'algq-offer-generator') . '</a>' : esc_html__('Pending', 'algq-offer-generator'); ?></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="4"><?php echo esc_html__('No documents generated yet.', 'algq-offer-generator'); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="algq-card">
            <h2><?php echo esc_html__('Recent Activity', 'algq-offer-generator'); ?></h2>
            <table class="algq-table">
                <thead><tr><th><?php echo esc_html__('Action', 'algq-offer-generator'); ?></th><th><?php echo esc_html__('Deal', 'algq-offer-generator'); ?></th><th><?php echo esc_html__('Date', 'algq-offer-generator'); ?></th></tr></thead>
                <tbody>
                <?php if (!empty($recent_activity)) : foreach ($recent_activity as $activity) : ?>
                    <tr>
                        <td><?php echo esc_html($activity['action']); ?></td>
                        <td><?php echo esc_html((string) absint($activity['deal_id'])); ?></td>
                        <td><?php echo esc_html($activity['created_at']); ?></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="3"><?php echo esc_html__('No activity recorded yet.', 'algq-offer-generator'); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
