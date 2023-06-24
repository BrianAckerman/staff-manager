<?php

// Render the staff options admin page
function staff_manager_options() {
    // Check if the form is submitted and the user has permission
    if (isset($_POST['submit']) && current_user_can('manage_options')) {
        // Process the form submission
        save_staff_manager_options();
    }

    // Get the current staff options
    $staff_css = get_option('staff_css', '');
    $staff_layout = get_option('staff_layout', '');
    $quick_contacts = get_option('quick_contacts', array());

    ?>
<div class="wrap">
    <h1>Staff Options</h1>

    <!-- Display a success message if the options were saved -->
    <?php if (isset($_POST['submit'])) : ?>
    <div id="message" class="updated notice is-dismissible">
        <p>Staff options saved successfully.</p>
        <button type="button" class="notice-dismiss"></button>
    </div>
    <?php endif; ?>

    <!-- Staff CSS section -->
    <form method="post" action="">
        <label for="">Staff CSS</label>
        <textarea id="css-editor" name="staff_css" rows="6" cols="50"><?php echo esc_textarea($staff_css); ?></textarea>
        <p class="description">Enter custom CSS styles for staff members.</p>

        <!-- Add more staff options sections here -->

        <?php wp_nonce_field('staff_options', 'staff_options_nonce'); ?>
        <p><input type="submit" name="submit" value="Save" class="button button-primary" /></p>
    </form>
</div>
<?php
}

// Save staff options when the admin page is submitted
function save_staff_manager_options() {
    // Verify the nonce for security
    if (!isset($_POST['staff_options_nonce']) || !wp_verify_nonce($_POST['staff_options_nonce'], 'staff_options')) {
        return;
    }

    // Save staff CSS option
    if (isset($_POST['staff_css'])) {
        $staff_css = sanitize_textarea_field($_POST['staff_css']);
        update_option('staff_css', $staff_css);
    }
    
    // Save other staff options here
    
    // Redirect to the staff options page with a success message
    $redirect_url = add_query_arg('updated', 'true', $_SERVER['REQUEST_URI']);
    wp_redirect($redirect_url);
}