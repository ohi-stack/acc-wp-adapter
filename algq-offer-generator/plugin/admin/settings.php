<?php
/**
 * Settings admin screen for Algonquian Offer Generator.
 *
 * @package Algq_Offer_Generator
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="algq-offer-admin">
    <div class="algq-offer-hero">
        <span class="algq-badge"><?php echo esc_html__('Configuration', 'algq-offer-generator'); ?></span>
        <h1><?php echo esc_html__('Offer Generator Settings', 'algq-offer-generator'); ?></h1>
        <p><?php echo esc_html__('Configure document generation defaults, automation behavior, and integration settings.', 'algq-offer-generator'); ?></p>
    </div>

    <div class="algq-card">
        <form method="post" action="options.php">
            <?php
            settings_fields('algq_offer_settings');
            do_settings_sections('algq_offer_settings');
            ?>

            <div class="algq-form-row">
                <label for="algq_offer_buyer_name"><?php echo esc_html__('Default Buyer Name', 'algq-offer-generator'); ?></label>
                <input id="algq_offer_buyer_name" type="text" name="algq_offer_buyer_name" value="<?php echo esc_attr(get_option('algq_offer_buyer_name', 'Algonquian Real Estate LLC')); ?>">
            </div>

            <div class="algq-form-row">
                <label for="algq_offer_default_document_type"><?php echo esc_html__('Default Document Type', 'algq-offer-generator'); ?></label>
                <select id="algq_offer_default_document_type" name="algq_offer_default_document_type">
                    <option value="letter-of-intent" <?php selected(get_option('algq_offer_default_document_type', 'letter-of-intent'), 'letter-of-intent'); ?>><?php echo esc_html__('Letter of Intent', 'algq-offer-generator'); ?></option>
                    <option value="purchase-agreement" <?php selected(get_option('algq_offer_default_document_type', 'letter-of-intent'), 'purchase-agreement'); ?>><?php echo esc_html__('Purchase Agreement', 'algq-offer-generator'); ?></option>
                    <option value="seller-financing" <?php selected(get_option('algq_offer_default_document_type', 'letter-of-intent'), 'seller-financing'); ?>><?php echo esc_html__('Seller Financing Offer', 'algq-offer-generator'); ?></option>
                </select>
            </div>

            <div class="algq-form-row">
                <label for="algq_offer_auto_generate_after_underwriting"><?php echo esc_html__('Auto Generate After Underwriting', 'algq-offer-generator'); ?></label>
                <select id="algq_offer_auto_generate_after_underwriting" name="algq_offer_auto_generate_after_underwriting">
                    <option value="no" <?php selected(get_option('algq_offer_auto_generate_after_underwriting', 'no'), 'no'); ?>><?php echo esc_html__('Disabled', 'algq-offer-generator'); ?></option>
                    <option value="yes" <?php selected(get_option('algq_offer_auto_generate_after_underwriting', 'no'), 'yes'); ?>><?php echo esc_html__('Enabled', 'algq-offer-generator'); ?></option>
                </select>
            </div>

            <?php submit_button(__('Save Settings', 'algq-offer-generator')); ?>
        </form>
    </div>
</div>
