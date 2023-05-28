<?php
add_action('add_meta_boxes_staff_member', 'create_staff_meta_box');

function create_staff_meta_box() {
    add_meta_box(
        'staff_meta_box',                 // Unique ID
        'Staff Member Information',       // Box title
        'staff_meta_box_callback',        // Content callback
        'staff_member',                   // Post type
        'normal',                         // Context
        'high'                            // Priority
    );
}

function staff_meta_box_callback($post) {
    // Enqueue the Vue app
    wp_enqueue_script('msmp-staff-edit-script', MSMP_PLUGIN_URL . 'dist/admin_staff-edit-bundle.js', array(), '1.0', true);
    wp_enqueue_style('msmp-style', MSMP_PLUGIN_URL . 'dist/admin_staff-edit-bundle.css', array(), '1.0');

    // Pass data to the Vue app
    wp_localize_script('msmp-staff-edit-script', 'wpData', array(
        'nonce' => wp_create_nonce('wp_rest'),
        'postId' => $post->ID,
        'postType' => $post->post_type,
        'socialLinks' => get_post_meta($post->ID, 'social_links', true),
    ));
    
    // Retrieve the post content value
    $staff_post_content = isset($post) ? $post->post_content : '';
    ?>
<!-- Add this hidden input field in your custom meta box -->
<input type="hidden" id="staff_post_content" name="staff_post_content"
    value="<?php echo esc_attr($staff_post_content); ?>" ref="postContentInput" />
<div id="admin_staff-edit-app"></div>
<?php
}

function staff_update_post_content($data, $postarr) {
    // Check if the staff_post_content field is set and the post type is your custom post type
    if (isset($_POST['staff_post_content']) && $data['post_type'] === 'staff_member') {
        // Update the post_content field with the staff_post_content value
        $data['post_content'] = $_POST['staff_post_content'];
    }
    return $data;
}
add_filter('wp_insert_post_data', 'staff_update_post_content', 10, 2);
?>