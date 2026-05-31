<?php
/**
 * Shared shortcode fallbacks for Algonquian Real Estate generated pages.
 *
 * @package AlgonquianRealEstate
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('ALGQ_Shortcode_Fallbacks')) {
    class ALGQ_Shortcode_Fallbacks {
        public static function register(array $shortcodes) {
            foreach ($shortcodes as $tag => $config) {
                $tag = sanitize_key($tag);
                if ('' === $tag || shortcode_exists($tag)) {
                    continue;
                }
                add_shortcode($tag, function ($atts = array()) use ($config) {
                    $atts = shortcode_atts(array(), (array) $atts, isset($config['tag']) ? $config['tag'] : 'algq_fallback');
                    $title = isset($config['title']) ? sanitize_text_field($config['title']) : __('Algonquian Real Estate', 'algq-shared');
                    $body  = isset($config['body']) ? wp_kses_post($config['body']) : __('This module is installed and awaiting configuration.', 'algq-shared');
                    ob_start();
                    ?>
                    <section class="algq-generated-page algq-shortcode-fallback">
                        <h1><?php echo esc_html($title); ?></h1>
                        <div class="algq-generated-page__body"><?php echo wp_kses_post(wpautop($body)); ?></div>
                    </section>
                    <?php
                    return ob_get_clean();
                });
            }
        }
    }
}
