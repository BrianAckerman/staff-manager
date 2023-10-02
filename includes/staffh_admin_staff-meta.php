<?php

# Social Links
/*add_action('add_meta_boxes_staff_member', 'create_staff_social_meta_box');

function create_staff_social_meta_box() {
    add_meta_box(
        'staffh_social_meta_box',                   // Unique ID
        'Social',                                   // Box title
        'staffh_social_meta_box',                   // Content callback
        'staff_member',                             // Post type
        'side',                                     // Context
        'default'                                   // Priority
    );
    add_meta_box(
        'staffh_quick_contacts_box',                // Unique ID
        'Quick Contacts',                           // Box title
        'staffh_quick_contacts_meta_box',           // Content callback
        'staff_member',                             // Post type
        'side',                                     // Context
        'default'                                   // Priority
    );
} */

function staffh_social_meta_box($post) {
    // Add a placeholder element for the Social Links Vue app
     echo '<div id="social-links-app"><social-links></social-links></div>';
}

function staffh_quick_contacts_meta_box($post) {
    // Add a placeholder element for the Quick Contacts Vue app
     echo '<div id="quick-contacts-app"><quick-contacts></quick-contacts></div>';
}

function staffh_save_meta_data($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    $isAffiliate_value = isset($_POST['isAffiliate']) ? 1 : 0;
    update_post_meta($post_id, 'isAffiliate', $isAffiliate_value);
}
add_action('save_post_staff_member', 'staffh_save_meta_data');
?>