<?php
/**
 * Plugin Name: OneGodian Contributors Membership
 * Plugin URI: https://onegodian.org
 * Description: Contributors and membership system for ONEGODIAN™ including contributor dashboards, contribution CTAs, AI-hosted video sections, contributor categories, and support infrastructure.
 * Version: 1.0.0
 * Author: ONEGODIAN, LLC
 */

if (!defined('ABSPATH')) {
    exit;
}

class OneGodianContributorsMembership {

    public function __construct() {
        add_shortcode('onegodian_contributors_hero', [$this, 'contributors_hero']);
        add_shortcode('onegodian_contributor_categories', [$this, 'contributor_categories']);
        add_shortcode('onegodian_support_cta', [$this, 'support_cta']);
        add_shortcode('onegodian_ai_video_section', [$this, 'ai_video_section']);
        add_shortcode('onegodian_member_dashboard', [$this, 'member_dashboard']);

        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function admin_menu() {
        add_menu_page(
            'OneGodian Contributors',
            'Contributors',
            'manage_options',
            'onegodian-contributors',
            [$this, 'settings_page'],
            'dashicons-groups',
            25
        );
    }

    public function register_settings() {
        register_setting('onegodian_contributors_group', 'onegodian_contributor_video_prompt');
        register_setting('onegodian_contributors_group', 'onegodian_support_url');
        register_setting('onegodian_contributors_group', 'onegodian_contributor_message');
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>ONEGODIAN™ Contributors Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('onegodian_contributors_group'); ?>
                <?php do_settings_sections('onegodian_contributors_group'); ?>

                <table class="form-table">
                    <tr>
                        <th>Contributor Message</th>
                        <td>
                            <textarea name="onegodian_contributor_message" rows="5" cols="60"><?php echo esc_textarea(get_option('onegodian_contributor_message')); ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <th>Support URL</th>
                        <td>
                            <input type="text" name="onegodian_support_url" value="<?php echo esc_attr(get_option('onegodian_support_url')); ?>" size="60">
                        </td>
                    </tr>

                    <tr>
                        <th>AI Video Prompt</th>
                        <td>
                            <textarea name="onegodian_contributor_video_prompt" rows="12" cols="80"><?php echo esc_textarea(get_option('onegodian_contributor_video_prompt')); ?></textarea>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function contributors_hero() {
        return '
        <section class="onegodian-contributors-hero">
            <h1>Build The Future Together</h1>
            <p>Your contributions help support infrastructure, education, technology, media, and community systems for generations to come.</p>
            <a class="onegodian-btn" href="' . esc_url(get_option('onegodian_support_url')) . '">Become A Contributor</a>
        </section>';
    }

    public function contributor_categories() {
        return '
        <div class="onegodian-categories-grid">
            <div><h3>Infrastructure Contributors</h3><p>Supporting APIs, servers, cloud systems, and platform growth.</p></div>
            <div><h3>Education Contributors</h3><p>Helping expand learning systems and digital education.</p></div>
            <div><h3>Media Contributors</h3><p>Supporting video, music, archives, and storytelling.</p></div>
            <div><h3>Founding Contributors</h3><p>Early supporters helping establish long-term infrastructure.</p></div>
        </div>';
    }

    public function support_cta() {
        return '
        <section class="onegodian-support-cta">
            <h2>Support The Mission</h2>
            <p>Contributions help build the ONEGODIAN™ ecosystem through voluntary participation, education, and infrastructure development.</p>
            <a class="onegodian-btn" href="' . esc_url(get_option('onegodian_support_url')) . '">Contribute Now</a>
        </section>';
    }

    public function ai_video_section() {
        return '
        <section class="onegodian-ai-video">
            <h2>AI Hosted Contributors Overview</h2>
            <pre>' . esc_html(get_option('onegodian_contributor_video_prompt')) . '</pre>
        </section>';
    }

    public function member_dashboard() {
        $user = wp_get_current_user();

        return '
        <section class="onegodian-member-dashboard">
            <h2>Contributor Dashboard</h2>
            <p>Welcome, ' . esc_html($user->display_name) . '</p>
            <ul>
                <li>Contributor Status: Active</li>
                <li>Membership Access: Enabled</li>
                <li>Community Participation: Verified</li>
            </ul>
        </section>';
    }
}

new OneGodianContributorsMembership();
