<?php
/**
 * Shared automatic page generator for Algonquian Real Estate plugins.
 *
 * @package AlgonquianRealEstate
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('ALGQ_Page_Generator')) {
    /**
     * Creates or updates WordPress pages with shortcode content.
     */
    class ALGQ_Page_Generator {
        /**
         * Create or update a page with a shortcode wrapped for WPBakery.
         *
         * @param string $slug Page path, e.g. plugin/pipeline-crm.
         * @param string $title Page title.
         * @param string $shortcode Shortcode string including brackets.
         * @param string $option_key Optional option name to store the generated ID.
         * @param string $status Post status.
         * @return int|WP_Error
         */
        public static function create_or_update_page($slug, $title, $shortcode, $option_key = '', $status = 'publish') {
            $slug      = trim(sanitize_title_with_dashes($slug), '/');
            $title     = sanitize_text_field($title);
            $shortcode = trim(wp_kses_post($shortcode));
            $status    = in_array($status, array('publish', 'draft', 'private'), true) ? $status : 'publish';

            if ('' === $slug || '' === $title || '' === $shortcode) {
                return new WP_Error('algq_invalid_page_args', __('Invalid generated page arguments.', 'algq-shared'));
            }

            $content = "[vc_column_text]\n" . $shortcode . "\n[/vc_column_text]";
            $existing = get_page_by_path($slug, OBJECT, 'page');

            if ($existing instanceof WP_Post) {
                if (false === strpos($existing->post_content, $shortcode)) {
                    wp_update_post(array(
                        'ID'           => absint($existing->ID),
                        'post_content' => $content,
                    ));
                }
                if ('' !== $option_key) {
                    update_option(sanitize_key($option_key), absint($existing->ID));
                }
                return absint($existing->ID);
            }

            $parts       = explode('/', $slug);
            $post_name   = array_pop($parts);
            $parent_id   = 0;
            $parent_path = '';

            foreach ($parts as $part) {
                $parent_path = '' === $parent_path ? $part : $parent_path . '/' . $part;
                $parent = get_page_by_path($parent_path, OBJECT, 'page');
                if (!$parent instanceof WP_Post) {
                    $parent_id = wp_insert_post(array(
                        'post_title'   => ucwords(str_replace('-', ' ', $part)),
                        'post_name'    => $part,
                        'post_status'  => 'publish',
                        'post_type'    => 'page',
                        'post_parent'  => absint($parent_id),
                        'post_content' => '',
                    ));
                } else {
                    $parent_id = absint($parent->ID);
                }
            }

            $page_id = wp_insert_post(array(
                'post_title'   => $title,
                'post_name'    => $post_name,
                'post_content' => $content,
                'post_status'  => $status,
                'post_type'    => 'page',
                'post_parent'  => absint($parent_id),
            ));

            if (!is_wp_error($page_id) && '' !== $option_key) {
                update_option(sanitize_key($option_key), absint($page_id));
            }

            return $page_id;
        }

        /**
         * Create all pages in a manifest.
         *
         * @param array  $pages Manifest rows with slug, title, shortcode.
         * @param string $option_prefix Option prefix.
         * @return array
         */
        public static function create_pages(array $pages, $option_prefix = 'algq_generated_page') {
            $created = array();
            foreach ($pages as $page) {
                if (empty($page['slug']) || empty($page['title']) || empty($page['shortcode'])) {
                    continue;
                }
                $key = sanitize_key($option_prefix . '_' . str_replace(array('/', '-'), '_', $page['slug']));
                $created[$page['slug']] = self::create_or_update_page(
                    $page['slug'],
                    $page['title'],
                    $page['shortcode'],
                    $key,
                    isset($page['status']) ? $page['status'] : 'publish'
                );
            }
            return $created;
        }
    }
}
