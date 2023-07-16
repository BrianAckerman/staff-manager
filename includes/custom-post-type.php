<?php
// Register the "staff_member" custom post type
function staffh_register_staff_member_post_type()
{
    $labels = array(
        'name'               => __('Staff Members', 'staffh'),
        'singular_name'      => __('Staff Member', 'staffh'),
        'add_new'            => __('Add New', 'staffh'),
        'add_new_item'       => __('Add New Staff Member', 'staffh'),
        'edit_item'          => __('Edit Staff Member', 'staffh'),
        'new_item'           => __('New Staff Member', 'staffh'),
        'view_item'          => __('View Staff Member', 'staffh'),
        'search_items'       => __('Search Staff Members', 'staffh'),
        'not_found'          => __('No staff members found', 'staffh'),
        'not_found_in_trash' => __('No staff members found in trash', 'staffh'),
        'menu_name'          => __('Staff Members', 'staffh'),
    );
    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'menu_icon'           => 'dashicons-groups',
        'rewrite'             => array('slug' => 'staff-members'),
        'supports'            => array('title', 'thumbnail', 'revisions'),
        'has_archive'         => true,
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