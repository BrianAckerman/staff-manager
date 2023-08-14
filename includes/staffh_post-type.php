<?php
// Register the "staff_member" custom post type
function staffh_register_staff_member_post_type()
{
    // Get the slug from the settings page or use the default 'staff-members'
    $slug = get_option('staffh_archive_slug', 'staff-members');

    // Register the post type with the 'has_archive' parameter based on the settings
    $has_archive = get_option('staffh_disable_archive_page');

    $labels = array(
        'name'               => __('Staff', 'staffh'),
        'singular_name'      => __('Staff', 'staffh'),
        'add_new'            => __('Add New', 'staffh'),
        'add_new_item'       => __('Add New Staff Member', 'staffh'),
        'edit_item'          => __('Edit Staff Member', 'staffh'),
        'new_item'           => __('New Staff Member', 'staffh'),
        'view_item'          => __('View Staff Member', 'staffh'),
        'search_items'       => __('Search Staff Members', 'staffh'),
        'not_found'          => __('No staff Found', 'staffh'),
        'not_found_in_trash' => __('No staff found in trash', 'staffh'),
        'menu_name'          => __('Staff', 'staffh'),
    );
    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'menu_icon'           => 'dashicons-groups',
        'rewrite' => array(
            'slug' => $slug,
            'with_front' => false,
        ),
        'supports'            => array('title', 'thumbnail', 'revisions'),
        'has_archive'         => $has_archive,
        'show_in_rest'        => true,
        'rest_base'           => 'staff-members',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
    );
    register_post_type('staff_member', $args);  
}

add_action('init', 'staffh_register_staff_member_post_type');

function disable_block_editor_for_staff_member($use_block_editor, $post_type) {
    if ($post_type === 'staff_member') {
        return false;
    }
    return $use_block_editor;
}
add_filter('use_block_editor_for_post_type', 'disable_block_editor_for_staff_member', 10, 2);

function remove_slug_meta_box() {
    remove_meta_box('slugdiv', 'staff_member', 'normal');
}
add_action('admin_menu', 'remove_slug_meta_box');