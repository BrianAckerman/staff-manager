<?php
add_action('add_meta_boxes_staff_member', 'create_staff_meta_box');

function create_staff_meta_box() {
    add_meta_box(
        'staff_meta_box',                           // Unique ID
        'Staff Member Information',                 // Box title
        'staff_profile_callback',                  // Content callback
        'staff_member',                             // Post type
        'normal',                                   // Context
        'high'                                      // Priority
    );
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
}

function staff_profile_callback($post) {
    // Enqueue the Vue app
    wp_enqueue_script('staffh_staff-edit-script', STAFFH_PLUGIN_URL . 'dist/admin_staff-edit-bundle.js', array(), '1.0', true);
    wp_enqueue_style('staffh_style', STAFFH_PLUGIN_URL . 'dist/admin_staff-edit-bundle.css', array(), '1.0');

    // Pass data to the Vue app
    $available_quick_contacts = fetch_quick_contacts_data(); // Fetch all available quick contacts
    $associated_quick_contacts = get_associated_quick_contacts($post->ID); // Fetch quick contacts associated with the post
    $icons_base_url = plugins_url('../img', __FILE__);

    // Pass data to the Vue app
    wp_localize_script('staffh_staff-edit-script', 'wpData', array(
        'nonce' => wp_create_nonce('wp_rest'),
        'postId' => $post->ID,
        'postType' => $post->post_type,
        'socialLinks' => get_post_meta($post->ID, 'social_links', true),
        'availableQuickContacts' => $available_quick_contacts,
        'associatedQuickContacts' => $associated_quick_contacts,
        'staffh_img_url' => $icons_base_url

    ));
    
    // Retrieve the post content value
    $staff_post_content = isset($post) ? $post->post_content : '';
?>

<!-- Add this hidden input field in your custom meta box -->
<input type="hidden" id="staff_post_content" name="post_content" value="<?php echo esc_attr($staff_post_content); ?>"
    ref="postContentInput" />
<div id="admin_staff-edit-app"></div>

<?php
}

function staffh_social_meta_box($post) {
    // Add a placeholder element for the Social Links Vue app
     echo '<div id="social-links-app"><social-links></social-links></div>';
}

function staffh_quick_contacts_meta_box($post) {
    // Fetch the existing quick contacts from the database. Not sure this is needed
    $quick_contacts = fetch_quick_contacts_data();

    // Get the associated contacts of the staff member
    $associated_contacts = get_post_meta($post->ID, 'associated_contacts', true);
    if (empty($associated_contacts)) {
        $associated_contacts = array(); // Initialize as an empty array if no associated contacts are found
    }
    
    // Add a placeholder element for the Quick Contacts Vue app
    echo '<div id="quick-contact-associations"></div>';

    // Hidden input field to store the selected tags
    echo '<input type="hidden" id="associated_contacts" name="associated_contacts">';
}

function save_staff_member_quick_contacts($post_id) {
    $associated_contacts = $_POST['associated_contacts'] ?? null;

    // Check if the value is an empty array and delete the post meta
    if ($associated_contacts==='[]' || empty($associated_contacts)) {
        delete_post_meta($post_id, 'associated_contacts');
    } else {
        update_post_meta($post_id, 'associated_contacts', $associated_contacts);
    }
}
add_action('save_post_staff_member', 'save_staff_member_quick_contacts');



function staffh_save_meta_data($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
}

add_action('save_post_staff_member', 'staffh_save_meta_data');

function staff_update_post_content($data, $postarr) {
    // Check if the staff_post_content field is set and the post type is your custom post type
    if (isset($_POST['staff_post_content']) && $data['post_type'] === 'staff_member') {
        // Update the post_content field with the staff_post_content value
        // $data['post_content'] = $_POST['staff_post_content'];
    } elseif (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE && $data['post_type'] === 'staff_member') {
        // Handle autosave: Retrieve the content from the appropriate source and update the post_content field
        // $data['post_content'] = $_POST['staff_post_content'];
    }
    return $data;
}

add_filter('wp_insert_post_data', 'staff_update_post_content', 10, 2);
?>