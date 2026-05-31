<?php
if (!defined('ABSPATH')) {
    exit;
}

class ALGQ_Pipeline_CPT {
    public static function init() {
        add_action('init', array(__CLASS__, 'register_pipeline_cpt'));
    }

    public static function register_pipeline_cpt() {
        register_post_type('algq_pipeline_deal', array(
            'labels' => array(
                'name' => __('Pipeline Deals', 'algq-pipeline-crm'),
                'singular_name' => __('Pipeline Deal', 'algq-pipeline-crm'),
                'add_new_item' => __('Add Pipeline Deal', 'algq-pipeline-crm'),
                'edit_item' => __('Edit Pipeline Deal', 'algq-pipeline-crm'),
                'view_item' => __('View Pipeline Deal', 'algq-pipeline-crm'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-networking',
            'supports' => array('title', 'editor', 'author'),
            'capability_type' => 'post',
            'show_in_rest' => true,
        ));
    }
}
