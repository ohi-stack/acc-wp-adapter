<?php
if (!defined('ABSPATH')) {
    exit;
}

class ALGQ_Stage_Manager {

    public static function init() {}

    public static function get_stages() {
        return array(
            'lead_captured' => 'Lead Captured',
            'underwriting' => 'Underwriting',
            'offer_sent' => 'Offer Sent',
            'negotiation' => 'Negotiation',
            'under_contract' => 'Under Contract',
            'buyer_assigned' => 'Buyer Assigned',
            'funding' => 'Funding',
            'closed' => 'Closed',
            'dead_deal' => 'Dead Deal'
        );
    }

    public static function update_stage($deal_id, $new_stage) {
        update_post_meta($deal_id, '_algq_pipeline_stage', $new_stage);

        do_action(
            'algq_stage_changed',
            $deal_id,
            get_post_meta($deal_id, '_algq_pipeline_stage', true),
            $new_stage
        );
    }
}
