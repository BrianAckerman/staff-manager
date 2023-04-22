<?php
add_action('add_meta_boxes_staff_member', 'create_staff_social_meta_box');

function create_staff_social_meta_box() {
    add_meta_box(
        'staff_social_meta_box',          // Unique ID
        'Social',                         // Box title
        'staff_social_meta_box_callback', // Content callback
        'staff_member',                   // Post type
        'side',                           // Context
        'default'                         // Priority
    );
}

function staff_social_meta_box_callback($post) {
    // Add a placeholder element for the Vue app
     echo '<div id="social-links-app"><social-links></social-links></div>';
}
?>