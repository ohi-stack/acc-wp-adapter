<?php
if (!defined('ABSPATH')) {
    exit;
}

class ALGQ_Pipeline_Activity_Log {
    public static function init() {
        add_action('algq_stage_changed', array(__CLASS__, 'log_stage_change'), 10, 3);
    }

    public static function add($deal_id, $action, $context = array()) {
        $deal_id = absint($deal_id);
        if (!$deal_id || empty($action)) {
            return false;
        }

        $logs = get_post_meta($deal_id, '_algq_pipeline_activity_log', true);
        if (!is_array($logs)) {
            $logs = array();
        }

        $entry = array(
            'timestamp' => current_time('mysql'),
            'user_id'   => get_current_user_id(),
            'action'    => sanitize_text_field($action),
            'context'   => self::sanitize_context($context),
        );

        $logs[] = $entry;
        update_post_meta($deal_id, '_algq_pipeline_activity_log', $logs);

        return $entry;
    }

    public static function get($deal_id, $limit = 25) {
        $logs = get_post_meta(absint($deal_id), '_algq_pipeline_activity_log', true);
        if (!is_array($logs)) {
            return array();
        }

        $logs = array_reverse($logs);
        return array_slice($logs, 0, absint($limit));
    }

    public static function log_stage_change($deal_id, $old_stage, $new_stage) {
        self::add($deal_id, 'Stage changed', array(
            'old_stage' => sanitize_key($old_stage),
            'new_stage' => sanitize_key($new_stage),
        ));
    }

    private static function sanitize_context($context) {
        if (!is_array($context)) {
            return array();
        }

        $clean = array();
        foreach ($context as $key => $value) {
            $clean[sanitize_key($key)] = is_scalar($value) ? sanitize_text_field((string) $value) : '';
        }
        return $clean;
    }
}
