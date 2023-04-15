<?php
// Register the "staff_member" custom post type
function msmp_register_staff_member_post_type()
{
    $labels = array(
        'name'               => __('Staff Members', 'msmp'),
        'singular_name'      => __('Staff Member', 'msmp'),
        'add_new'            => __('Add New', 'msmp'),
        'add_new_item'       => __('Add New Staff Member', 'msmp'),
        'edit_item'          => __('Edit Staff Member', 'msmp'),
        'new_item'           => __('New Staff Member', 'msmp'),
        'view_item'          => __('View Staff Member', 'msmp'),
        'search_items'       => __('Search Staff Members', 'msmp'),
        'not_found'          => __('No staff members found', 'msmp'),
        'not_found_in_trash' => __('No staff members found in trash', 'msmp'),
        'menu_name'          => __('Staff Members', 'msmp'),
    );
    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'menu_icon'           => 'dashicons-groups',
        'rewrite'             => array('slug' => 'staff-members'),
        'supports'            => array('title', 'thumbnail'),
        'has_archive'         => true,
        'show_in_rest'        => true,
        'rest_base'           => 'staff-members',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
    );
    register_post_type('staff_member', $args);
}
add_action('init', 'msmp_register_staff_member_post_type');

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