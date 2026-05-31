<?php
if (!defined('ABSPATH')) { exit; }

class ALGQ_Pipeline_Reports {
    public static function init() {}

    public static function metrics() {
        $stages = ALGQ_Stage_Manager::get_stages();
        $counts = array();
        $total = 0;

        foreach ($stages as $key => $label) {
            $query = new WP_Query(array(
                'post_type' => 'algq_pipeline_deal',
                'post_status' => 'publish',
                'fields' => 'ids',
                'posts_per_page' => 1,
                'meta_key' => '_algq_pipeline_stage',
                'meta_value' => $key
            ));
            $counts[$key] = array('label' => $label, 'count' => (int) $query->found_posts);
            $total += (int) $query->found_posts;
        }

        return array(
            'total_deals' => $total,
            'by_stage' => $counts,
            'generated_at' => current_time('mysql')
        );
    }

    public static function render($atts = array()) {
        $metrics = self::metrics();
        ob_start();
        include ALGQ_PIPELINE_CRM_DIR . 'templates/reports.php';
        return ob_get_clean();
    }
}
