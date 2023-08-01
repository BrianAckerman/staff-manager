<?php
// custom-settings-page.php

function custom_settings_page() {
    ?>
<div class="wrap">
    <h1>Custom Settings</h1>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php?action=custom_settings_submit')); ?>">
        <?php
            // Add a nonce field for security
            wp_nonce_field('custom_settings_nonce', 'custom_settings_nonce');
            ?>

        <label for="custom_setting_field">Custom Setting:</label>
        <input type="text" id="custom_setting_field" name="custom_setting_field"
            value="<?php echo esc_attr(get_option('custom_setting_field', '')); ?>">

        <?php
            // Output the submit button
            submit_button('Save Settings');
            ?>
    </form>
</div>
<?php
}

function custom_settings_submit() {
    // Verify the nonce field for security
    if (!isset($_POST['custom_settings_nonce']) || !wp_verify_nonce($_POST['custom_settings_nonce'], 'custom_settings_nonce')) {
        wp_die('Invalid nonce');
    }

    // Sanitize and save the custom setting value
    if (isset($_POST['custom_setting_field'])) {
        $custom_setting_value = sanitize_text_field($_POST['custom_setting_field']);
        update_option('custom_setting_field', $custom_setting_value);
    }

    // Redirect back to the settings page with a success query parameter
    wp_safe_redirect(admin_url('options-general.php?page=custom_settings_page&settings-updated=true'));
    exit;
}

// Handle form submission
add_action('admin_post_custom_settings_submit', 'custom_settings_submit');


// Register the admin page and form handling action
function custom_settings_init() {
    add_menu_page(
        'Custom Settings',          // Page title
        'Custom Settings',          // Menu title
        'manage_options',           // Capability required to access the page
        'custom-settings',          // Menu slug
        'custom_settings_page',     // Callback function to render the page
        'dashicons-admin-generic'   // Icon URL or Dashicon class
    );

    add_action('admin_post_custom_settings_save', 'custom_settings_save');
}
add_action('admin_menu', 'custom_settings_init');