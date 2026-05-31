<?php
if (!defined('ABSPATH')) { exit; }

class ALGQ_Pipeline_REST_API {
    public static function init() {
        add_action('rest_api_init', array(__CLASS__, 'register_routes'));
    }

    public static function register_routes() {
        register_rest_route('pipeline/v1', '/deals', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'list_deals'),
            'permission_callback' => array(__CLASS__, 'can_manage_pipeline')
        ));

        register_rest_route('pipeline/v1', '/stage', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'update_stage'),
            'permission_callback' => array(__CLASS__, 'can_manage_pipeline')
        ));

        register_rest_route('pipeline/v1', '/activity/(?P<deal_id>\d+)', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'activity'),
            'permission_callback' => array(__CLASS__, 'can_manage_pipeline')
        ));
    }

    public static function can_manage_pipeline() {
        return is_user_logged_in() && current_user_can('edit_posts');
    }

    public static function list_deals($request) {
        $stage = sanitize_key($request->get_param('stage'));
        $args = array(
            'post_type' => 'algq_pipeline_deal',
            'post_status' => 'publish',
            'posts_per_page' => 100,
            'orderby' => 'modified',
            'order' => 'DESC'
        );

        if ($stage) {
            $args['meta_key'] = '_algq_pipeline_stage';
            $args['meta_value'] = $stage;
        }

        $posts = get_posts($args);
        $items = array();

        foreach ($posts as $post) {
            $items[] = array(
                'id' => $post->ID,
                'title' => get_the_title($post),
                'stage' => get_post_meta($post->ID, '_algq_pipeline_stage', true),
                'priority' => get_post_meta($post->ID, '_algq_pipeline_priority', true),
                'assigned_to' => get_post_meta($post->ID, '_algq_pipeline_assigned_to', true),
                'modified' => get_the_modified_date('c', $post)
            );
        }

        return rest_ensure_response($items);
    }

    public static function update_stage($request) {
        $deal_id = absint($request->get_param('deal_id'));
        $new_stage = sanitize_key($request->get_param('stage'));

        if (!$deal_id || !$new_stage) {
            return new WP_Error('algq_invalid_stage_request', 'Missing deal_id or stage.', array('status' => 400));
        }

        if (!array_key_exists($new_stage, ALGQ_Stage_Manager::get_stages())) {
            return new WP_Error('algq_invalid_stage', 'Invalid pipeline stage.', array('status' => 400));
        }

        $old_stage = get_post_meta($deal_id, '_algq_pipeline_stage', true);
        update_post_meta($deal_id, '_algq_pipeline_stage', $new_stage);
        do_action('algq_stage_changed', $deal_id, $old_stage, $new_stage);

        return rest_ensure_response(array(
            'success' => true,
            'deal_id' => $deal_id,
            'old_stage' => $old_stage,
            'new_stage' => $new_stage
        ));
    }

    public static function activity($request) {
        $deal_id = absint($request['deal_id']);
        return rest_ensure_response(ALGQ_Pipeline_Activity_Log::get($deal_id));
    }
}
