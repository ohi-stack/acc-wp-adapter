<?php
/**
 * Templates admin screen for Algonquian Offer Generator.
 *
 * @package Algq_Offer_Generator
 */

if (!defined('ABSPATH')) {
    exit;
}

$templates = [
    'purchase-agreement' => [
        'title' => __('Purchase Agreement', 'algq-offer-generator'),
        'description' => __('Execution-oriented purchase document with buyer, seller, property, price, earnest money, closing date, and signature blocks.', 'algq-offer-generator'),
        'fields' => ['seller_name', 'property_address', 'purchase_price', 'earnest_money', 'closing_date', 'buyer_name'],
    ],
    'letter-of-intent' => [
        'title' => __('Letter of Intent', 'algq-offer-generator'),
        'description' => __('Negotiation document summarizing proposed acquisition terms before final contract preparation.', 'algq-offer-generator'),
        'fields' => ['seller_name', 'property_address', 'purchase_price', 'closing_date', 'buyer_name'],
    ],
    'seller-financing' => [
        'title' => __('Seller Financing Offer', 'algq-offer-generator'),
        'description' => __('Creative finance offer using down payment, loan amount, interest rate, term, monthly payment, and balloon terms.', 'algq-offer-generator'),
        'fields' => ['seller_name', 'property_address', 'down_payment', 'loan_amount', 'interest_rate', 'term_years', 'monthly_payment', 'balloon_year'],
    ],
];
?>

<div class="algq-offer-admin">
    <div class="algq-offer-hero">
        <span class="algq-badge"><?php echo esc_html__('Template Library', 'algq-offer-generator'); ?></span>
        <h1><?php echo esc_html__('Offer Templates', 'algq-offer-generator'); ?></h1>
        <p><?php echo esc_html__('Manage the core document templates used to generate acquisition offers and deal records.', 'algq-offer-generator'); ?></p>
    </div>

    <div class="algq-grid" style="grid-template-columns:repeat(3,minmax(0,1fr));">
        <?php foreach ($templates as $slug => $template) : ?>
            <div class="algq-card">
                <h2><?php echo esc_html($template['title']); ?></h2>
                <p class="algq-muted"><?php echo esc_html($template['description']); ?></p>
                <div class="algq-template-preview">
                    <strong><?php echo esc_html__('Merge Fields', 'algq-offer-generator'); ?></strong>
                    <ul>
                        <?php foreach ($template['fields'] as $field) : ?>
                            <li><code><?php echo esc_html('{{' . $field . '}}'); ?></code></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="algq-actions">
                    <a class="algq-button secondary" href="<?php echo esc_url(admin_url('admin.php?page=algq-offer-generator&document_type=' . rawurlencode($slug))); ?>"><?php echo esc_html__('Use Template', 'algq-offer-generator'); ?></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
