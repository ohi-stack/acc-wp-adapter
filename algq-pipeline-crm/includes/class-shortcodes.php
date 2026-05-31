<?php
if (!defined('ABSPATH')) { exit; }

class ALGQ_Pipeline_Shortcodes {
    public static function init() {
        add_shortcode('algq_pipeline_board', array(__CLASS__, 'board'));
        add_shortcode('algq_pipeline_reports', array(__CLASS__, 'reports'));
    }

    public static function board($atts = array()) {
        if (class_exists('ALGQ_Pipeline_Board')) {
            return ALGQ_Pipeline_Board::render($atts);
        }
        return '<div class="algq-pipeline-notice">Pipeline board is unavailable.</div>';
    }

    public static function reports($atts = array()) {
        if (class_exists('ALGQ_Pipeline_Reports')) {
            return ALGQ_Pipeline_Reports::render($atts);
        }
        return '<div class="algq-pipeline-notice">Pipeline reports are unavailable.</div>';
    }
}
