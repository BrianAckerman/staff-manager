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

function staff_create_affiliate_callback($post) { $isAffiliate_field_value = get_post_meta($post->ID, 'isAffiliate', true);
?>
<label for="isAffiliate">Is this an affiliate?</label>
<input type="checkbox" id="isAffiliate" name="isAffiliate" <?php checked($isAffiliate_field_value, 1); ?> />
<?php
}

function staff_save_meta_data($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    $isAffiliate_value = isset($_POST['isAffiliate']) ? 1 : 0;
    update_post_meta($post_id, 'isAffiliate', $isAffiliate_value);
}
add_action('save_post_staff_member', 'staff_save_meta_data');
?>