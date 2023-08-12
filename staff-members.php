<?php

/**
 * Plugin Name: Staff Manager
 * Plugin URI: https://example.com/
 * Description: Manage staff and staff pages
 * Version: 1.0.0
 * Author: Brian Ackerman
 * Author URI: https://www.thebackerman.me/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: staff-plugin
 */

// Define constants for plugin paths and URLs
define('STAFFH_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('STAFFH_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the database file
require_once STAFFH_PLUGIN_DIR . 'includes/database.php';

 // Include the custom admin page file
require_once STAFFH_PLUGIN_DIR . 'includes/staff-members-admin.php';

// Include the files that define custom post types, meta boxes, and shortcodes
require_once STAFFH_PLUGIN_DIR . 'includes/post_type-staff-member.php';
require_once STAFFH_PLUGIN_DIR . 'includes/admin_single.php';
require_once STAFFH_PLUGIN_DIR . 'includes/admin_quickcontacts.php';
require_once STAFFH_PLUGIN_DIR . 'includes/admin_settings.php';
require_once STAFFH_PLUGIN_DIR . 'includes/admin_settings-test.php';
require_once STAFFH_PLUGIN_DIR . 'includes/rest.php';
require_once STAFFH_PLUGIN_DIR . 'includes/enqueue-scripts.php';

// Define single template
function staffh_custom_single_template($template) {
    if (is_singular('staff_member')) {
        $new_template = STAFFH_PLUGIN_DIR . 'templates/single-staff_member.php';
        if (file_exists($new_template)) {
            return $new_template;
        }
    }
    return $template;
}

add_filter('template_include', 'staffh_custom_single_template');

// Helper Function(s)
function get_icon_file_name($link_type) {
    $icon_map = array(
        'facebook' => "facebook-f.svg",
        'twitterx'=> "twitterx.svg",
        'instagram'=> "instagram.svg",
        'youtube'=> "youtube.svg",
        'linkedin'=> "linkedin-in.svg",
    );
    return $icon_map[strtolower($link_type)] ?? 'link.svg';
}
?>