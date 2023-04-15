<?php
add_action('add_meta_boxes_staff_member', 'create_custom_staff_meta_box');

function create_custom_staff_meta_box() {
    add_meta_box(
        'custom_staff_meta_box',          // Unique ID
        'Staff Member Information',       // Box title
        'custom_staff_meta_box_callback', // Content callback
        'staff_member',                   // Post type
        'normal',                         // Context
        'high'                            // Priority
    );
}

function custom_staff_meta_box_callback($post) {
    // Enqueue the Vue app
    wp_enqueue_script('msmp-script', MSMP_PLUGIN_URL . 'dist/staff-edit-bundle.js', array(), '1.0', true);
    wp_enqueue_style('msmp-style', MSMP_PLUGIN_URL . 'dist/staff-edit-bundle.css', array(), '1.0');

    // Pass data to the Vue app
    wp_localize_script('msmp-script', 'wpData', array(
        'nonce' => wp_create_nonce('wp_rest'),
        'postId' => $post->ID,
        'postType' => $post->post_type,
    ));

    // Add a placeholder element for the Vue app
    echo '<div id="app"></div>';
}
?>