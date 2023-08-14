<?php

function staff_manager_enqueue_scripts($hook_suffix) {
    // Check if the current page is the list page
   if ($hook_suffix !== 'toplevel_page_manage-staff') {
        wp_enqueue_style('staff-admin-list-stylesheet', STAFFH_PLUGIN_URL . 'css/admin-list.css', array(), '1.0');
        return;
    }
    
    // Localize user capability
    wp_localize_script('staffh_script', 'wpData', array(
        'nonce' => wp_create_nonce('wp_rest'),
        'canEditPosts' => current_user_can('edit_posts')
    ));
}
add_action('admin_enqueue_scripts', 'staff_manager_enqueue_scripts');

// Add custom columns
function add_custom_columns($columns) {
    // Define the desired order of the columns
    $new_columns = array(
        'cb' => $columns['cb'], // Checkbox column
        'featured_image' => __('Photo'),
        'title' => $columns['title'],
        'last_modified' => __('Last Modified'),
        // Add more columns here
    );

    return $new_columns;
}
add_filter('manage_staff_member_posts_columns', 'add_custom_columns');

// Display content for custom columns
function display_custom_columns($column, $post_id) {
    switch ($column) {
        case 'last_modified':
            $last_modified = get_the_modified_time('F j, Y', $post_id);
            echo $last_modified;
            break;
        case 'featured_image':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, array(50, 50));
            } else {
                echo __('No Image');
            }
            break;
        // Add more column cases here if needed
    }
}
add_action('manage_staff_member_posts_custom_column', 'display_custom_columns', 10, 2);

// Modify the query for sorting
function modify_staff_member_query($query) {
    if (is_admin() && $query->is_main_query() && $query->get('orderby') === 'last_modified') {
        $query->set('orderby', 'modified');
    }
}
add_action('pre_get_posts', 'modify_staff_member_query');

// Make the "Last Modified" column sortable
function make_last_modified_column_sortable($columns) {
    $columns['last_modified'] = 'modified';
    return $columns;
}
add_filter('manage_edit-staff_member_sortable_columns', 'make_last_modified_column_sortable');

?>