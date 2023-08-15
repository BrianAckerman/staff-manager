<?php

/**
 * Plugin Name: Carrington Staff Manager
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
require_once STAFFH_PLUGIN_DIR . 'includes/staffh_database.php';

 // Include the custom admin page file
require_once STAFFH_PLUGIN_DIR . 'includes/staffh_admin.php';

// Include the files that define custom post types, meta boxes, and shortcodes
require_once STAFFH_PLUGIN_DIR . 'includes/staffh_post-type.php';
require_once STAFFH_PLUGIN_DIR . 'includes/staffh_admin_single-edit.php';
require_once STAFFH_PLUGIN_DIR . 'includes/staffh_admin_quickcontacts.php';
require_once STAFFH_PLUGIN_DIR . 'includes/staffh_admin_settings.php';
require_once STAFFH_PLUGIN_DIR . 'includes/staffh_rest.php';
require_once STAFFH_PLUGIN_DIR . 'includes/staffh_enqueue-scripts.php';

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

// If Oxygen Builder is in use we want to use our template still
include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if (is_plugin_active('oxygen/functions.php')) { // Make sure the plugin path is correct
    // Oxygen Builder is active
    function staffh_custom_content($content) {
        if (get_post_type() === 'staff_member') {
            ob_start(); // Start output buffering
            
            // Include your custom template file
            include_once(STAFFH_PLUGIN_DIR . 'templates/single-oxy-staff_member.php');
            
            $content = ob_get_clean(); // Get the buffered output and clean the buffer
        }
        return $content;
    }

    add_filter('the_content', 'staffh_custom_content');
} else {
    add_filter('template_include', 'staffh_custom_single_template');
}

// Default options
if ( false === get_option( 'staffh_disable_archive_page' ) ) {
    // Add the option with the default value.
    add_option( 'staffh_disable_archive_page', '1' );
}

if ( false === get_option( 'staffh_include_archive_in_permalink' ) ) {
    // Add the option with the default value.
    add_option( 'staffh_include_archive_in_permalink', '1' );
}

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