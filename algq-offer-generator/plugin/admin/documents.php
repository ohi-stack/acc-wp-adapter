<?php
/**
 * Documents admin screen for Algonquian Offer Generator.
 *
 * @package Algq_Offer_Generator
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$documents_table = $wpdb->prefix . 'algq_documents';
$documents = $wpdb->get_results("SELECT id, deal_id, offer_id, document_type, title, status, version, file_url, file_checksum, generated_by, created_at, rendered_at FROM {$documents_table} ORDER BY created_at DESC LIMIT 100", ARRAY_A);
?>

<div class="algq-offer-admin">
    <div class="algq-offer-hero">
        <span class="algq-badge"><?php echo esc_html__('Document Center', 'algq-offer-generator'); ?></span>
        <h1><?php echo esc_html__('Generated Offer Documents', 'algq-offer-generator'); ?></h1>
        <p><?php echo esc_html__('Review generated offers, PDF status, document versions, and deal-linked file records.', 'algq-offer-generator'); ?></p>
    </div>

    <div class="algq-card">
        <table class="algq-table">
            <thead>
                <tr>
                    <th><?php echo esc_html__('ID', 'algq-offer-generator'); ?></th>
                    <th><?php echo esc_html__('Deal', 'algq-offer-generator'); ?></th>
                    <th><?php echo esc_html__('Type', 'algq-offer-generator'); ?></th>
                    <th><?php echo esc_html__('Version', 'algq-offer-generator'); ?></th>
                    <th><?php echo esc_html__('Status', 'algq-offer-generator'); ?></th>
                    <th><?php echo esc_html__('Created', 'algq-offer-generator'); ?></th>
                    <th><?php echo esc_html__('PDF', 'algq-offer-generator'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($documents)) : foreach ($documents as $document) : ?>
                    <tr>
                        <td><?php echo esc_html((string) absint($document['id'])); ?></td>
                        <td><?php echo esc_html((string) absint($document['deal_id'])); ?></td>
                        <td><?php echo esc_html($document['title'] ?: ucwords(str_replace('-', ' ', $document['document_type']))); ?></td>
                        <td><?php echo esc_html('v' . absint($document['version'])); ?></td>
                        <td><span class="algq-status <?php echo esc_attr($document['status']); ?>"><?php echo esc_html($document['status']); ?></span></td>
                        <td><?php echo esc_html($document['created_at']); ?></td>
                        <td>
                            <?php if (!empty($document['file_url'])) : ?>
                                <a class="algq-button secondary" href="<?php echo esc_url($document['file_url']); ?>" target="_blank" rel="noopener"><?php echo esc_html__('Open PDF', 'algq-offer-generator'); ?></a>
                            <?php else : ?>
                                <span class="algq-muted"><?php echo esc_html__('Pending', 'algq-offer-generator'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="7"><?php echo esc_html__('No documents have been generated yet.', 'algq-offer-generator'); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
