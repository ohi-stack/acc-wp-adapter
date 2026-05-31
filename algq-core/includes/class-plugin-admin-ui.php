<?php
if (!defined('ABSPATH')) { exit; }

class ALGQ_Plugin_Admin_UI {
    public static function header($title, $version = '1.0.0') {
        echo '<div class="algq-admin-header">';
        echo '<div><h1>' . esc_html($title) . '</h1><p>Algonquian Real Estate LLC | Version ' . esc_html($version) . '</p></div>';
        echo '</div>';
    }

    public static function card($label, $value) {
        echo '<div class="algq-kpi-card">';
        echo '<span>' . esc_html($label) . '</span>';
        echo '<strong>' . esc_html($value) . '</strong>';
        echo '</div>';
    }
}
