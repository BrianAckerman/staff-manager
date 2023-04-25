<?php

# Social Links
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


# Affiliate Status
add_action('add_meta_boxes_staff_member', 'create_affiliate_meta_box');

function create_affiliate_meta_box() {
    add_meta_box(
        'staff_affiliate_meta_box',         // Unique ID
        'Affiliate Status',                 // Box title
        'staff_create_affiliate_callback',  // Content callback
        'staff_member',                     // Post type
        'side',                             // Context
        'default'                           // Priority
    );
}

function staff_create_affiliate_callback($post) {
     echo '<form>
        <label for="isAffiliate">Is this an affiliate?</label>
        <input type="checkbox" name="isAffiliate" />
        </form>
     ';
}
?>