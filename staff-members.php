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

 // Include the custom admin page file
include( plugin_dir_path( __FILE__ ) . 'includes/staff-members-admin.php' );

// Define constants for plugin paths and URLs
define('MSMP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MSMP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the files that define custom post types, meta boxes, and shortcodes
require_once MSMP_PLUGIN_DIR . 'includes/custom-post-types.php';
require_once MSMP_PLUGIN_DIR . 'includes/admin_staff-edit.php';
require_once MSMP_PLUGIN_DIR . 'includes/admin_staff-meta.php';
require_once MSMP_PLUGIN_DIR . 'includes/shortcode.php';
// require_once MSMP_PLUGIN_DIR . 'includes/quick-contacts.php';

// Define single template
function msmp_custom_single_template($template) {
    if (is_singular('staff_member')) {
        $new_template = MSMP_PLUGIN_DIR . 'templates/single-staff_member.php';
        if (file_exists($new_template)) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'msmp_custom_single_template');


// Routes and REST
function register_staff_member_rest_routes() {
    register_rest_route(
        'staff-members/v1',
        '/staff-members/(?P<id>\d+)',
        array(
            'methods' => 'POST',
            'callback' => 'update_staff_member',
            'permission_callback' => function() {
                return current_user_can('edit_others_posts');
            },
            'args' => array(
                'id' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric($param);
                    }
                ),
            ),
        )
    );
}

function register_staff_member_count_rest_route() {
    register_rest_route('staff-members/v1', '/counts/', array(
        'methods' => 'GET',
        'callback' => 'get_staff_member_counts',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        },
    ));
}

add_action('rest_api_init', 'register_staff_member_rest_routes');
add_action('rest_api_init', 'register_staff_member_count_rest_route');

function update_staff_member(WP_REST_Request $request) {
    $post_id = $request['id'];
    $status = $request['status'];

    $updated_post = wp_update_post(array(
        'ID' => $post_id,
        'post_status' => $status,
    ), true);

    if (is_wp_error($updated_post)) {
        return new WP_REST_Response(array('error' => $updated_post->get_error_message()), 400);
    }

    return new WP_REST_Response(get_post($post_id), 200);
}

function get_staff_member_counts() {
    $counts = array(
        'publish' => 0,
        'draft' => 0,
        'trash' => 0,
    );

    $statuses = array('publish', 'draft', 'trash');

    foreach ($statuses as $status) {
        $query = new WP_Query(array(
            'post_type' => 'staff_member',
            'post_status' => $status,
            'posts_per_page' => -1,
        ));
        $counts[$status] = $query->found_posts;
    }

    return new WP_REST_Response($counts, 200);
}


/*
// Register the activation hook.
register_activation_hook( __FILE__, 'create_quick_contacts_table' );

// Register the filter.
add_filter( 'quick_contact_enable', 'disable_quick_contact_if_draft' );

// Register the action.
add_action( 'delete_post', 'delete_quick_contact_if_deleted' ); */

?>