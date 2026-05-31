<?php
if (!defined('ABSPATH')) { exit; }

class ALGQ_Deal_Assignment {
    public static function init() {}

    public static function assign($deal_id, $user_id) {
        $deal_id = absint($deal_id);
        $user_id = absint($user_id);

        if (!$deal_id || !$user_id) {
            return false;
        }

        update_post_meta($deal_id, '_algq_pipeline_assigned_to', $user_id);

        if (class_exists('ALGQ_Pipeline_Activity_Log')) {
            ALGQ_Pipeline_Activity_Log::add($deal_id, 'Deal assigned', array(
                'user_id' => $user_id
            ));
        }

        do_action('algq_pipeline_deal_assigned', $deal_id, $user_id);
        return true;
    }

    public static function get_assignee($deal_id) {
        return absint(get_post_meta(absint($deal_id), '_algq_pipeline_assigned_to', true));
    }
}
