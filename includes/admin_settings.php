<?php
// This section groups all functions related to settings and their callbacks

function radio_button($name, $value, $checked_value, $display_label = null) {
    // Use the value as the label if no display label is provided
    if ($display_label === null) {
        $display_label = $value;
    }

    $checked = checked($value, $checked_value, false);

    echo "<label><input type='radio' name='{$name}' value='{$value}' {$checked}> {$display_label}</label><br>";
}

function staffh_archive_slug_callback() {
    $slug = get_option('staffh_archive_slug', 'staff-members');
    $page_exists = get_page_by_path($slug) ? 'The page ' . $slug . ' already exists' : '';
    $post_exists = get_page_by_path($slug, OBJECT, 'post') ? 'The post ' . $slug . ' already exists' : '';

    echo "<p>$page_exists</p>";
    echo "<p>$post_exists</p>";
    echo sprintf('<input type="text" name="staffh_archive_slug" value="%s" />', esc_attr($slug));
}

function staffh_disable_archive_callback() {
    $disable_archive = get_option('staffh_disable_archive_page', 0);
    radio_button('staffh_disable_archive_page', '1', $disable_archive, "Yes");
    radio_button('staffh_disable_archive_page', '0', $disable_archive, "No");
}

function staffh_archive_settings_nonce_callback() {
    wp_nonce_field('staffh_archive_settings_nonce');
}

function staffh_archive_settings_conflict_notice() {
    echo '<div class="error notice"><p>';
    esc_html_e('The selected permalink slug conflicts with an existing page or post. Please choose a different slug.', 'staff-hero');
    echo '</p></div>';
}

function staffh_archive_settings_save() {
    // Check the nonce for security
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'staffh_archive_settings_nonce')) {
        wp_die('Invalid request.');
    }

    // The value from the form (yes/no) to include the archive page path in the permalink, e.g. ~/archive/staff-member or ~/staff-member and save the value to the option 'staffh_include_archive_in_permalink'
    $staffh_include_archive_in_permalink = isset($_POST['staffh_include_archive_in_permalink']) && $_POST['staffh_include_archive_in_permalink'] === '0' ? 0 : 1;
    update_option('staffh_include_archive_in_permalink', $staffh_include_archive_in_permalink);

    // Enable or disable the archive page based on the 'staffh_disable_archive_page' option & save the value to the option 'staffh_disable_archive_page'
    $disable_archive = isset($_POST['staffh_disable_archive_page']) && $_POST['staffh_disable_archive_page'] === '0' ? 0 : 1;
    update_option('staffh_disable_archive_page', $disable_archive);

    // Archive slug
    if (isset($_POST['staffh_archive_slug'])) {
        $new_slug = sanitize_text_field($_POST['staffh_archive_slug']);
        
        // Check if there are no pages or posts with the new slug
        $page_exists = get_page_by_path($new_slug) ? true : false;
        $post_exists = get_page_by_path($new_slug, OBJECT, 'post') ? true : false;

        // If there are no conflicts
        if (!($page_exists || $post_exists)) {        
            // Save the value to the option 'staffh_archive_slug'
            update_option('staffh_archive_slug', $new_slug);
        } else {
            // Redirect back to the settings page with a failure query parameter
            wp_safe_redirect(admin_url('admin.php?page=staffh-settings&page-already-exists=true&slug=' . $new_slug));
            exit;
        }
    }

    // Flush the rewrite rules after saving the settings
    flush_rewrite_rules();

    // Redirect back to the settings page with a success query parameter
    wp_safe_redirect(admin_url('admin.php?page=staffh-settings&settings-updated=true'));
    exit;
}

add_action('register_post_type_args', 'staffh_update_post_type_args', 10, 2);

function staffh_update_post_type_args($args, $post_type) {
    if ('staff_member' === $post_type) {
        $staffh_include_archive_in_permalink = get_option('staffh_include_archive_in_permalink', 0);
        $disable_archive = get_option('staffh_disable_archive_page', 0);

        if ($staffh_include_archive_in_permalink) {
            $args['rewrite']['slug'] = get_option('staffh_archive_slug', 'staff-members');
        } else {
            $args['rewrite']['slug'] = '';
        }

        $args['has_archive'] = boolval(!$disable_archive); // Use the inverted value to enable/disable
    }

    return $args;
}


// This section groups all functions related to admin initialization and settings display
function staffh_admin_init() {
    flush_rewrite_rules();
}

function staffh_archive_settings() {
    // Check if the form is submitted and handle the save action
    if (isset($_POST['submit'])) {
        // Call the custom save function to handle the form submission
        staffh_archive_settings_save();
    }
    ?>
<div class="wrap">
    <h1>Archive Settings</h1>

    <?php
        if (isset($_GET['settings-updated'])) {
            // Display the confirmation message after saving the settings
            ?>
    <div class="notice notice-success is-dismissible">
        <p><?php echo esc_html__('Settings saved successfully!', 'staffh'); ?></p>
    </div>
    <?php
        }
        if (isset($_GET['page-already-exists'])) {
            // Display the confirmation message after saving the settings
            ?>
    <div class="notice notice-error is-dismissible">
        <p><?php echo 'A page or post exists with the slug <strong>' . $_GET['slug'] . '</strong>. Please choose something different.'; ?>
        </p>
    </div>
    <?php
        }
        ?>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <?php
            // Output the form fields manually using settings_fields and do_settings_sections
            settings_fields('staffh_archive_settings_group');
            do_settings_sections('staffh_archive_settings');
            ?>
        <input type="hidden" name="action" value="staffh_archive_settings_save">
        <!-- Add this hidden field to identify the custom action -->
        <?php submit_button(); ?>
    </form>
</div>
<?php
}

// This section contains all register_setting and add_settings_field calls

function staffh_archive_settings_init() {

    // Add the "Archive" section
    add_settings_section(
        'staffh_archive_settings_section',                  // Section ID
        '',                                                 // Section title
        '',                                                 // Callback function to render the section content
        'staffh_archive_settings'                           // Settings page slug
    );

    // Add the "Archive Slug" field
    add_settings_field(
        'staffh_archive_slug',                              // Field ID
        'Archive Slug',                                     // Field title
        'staffh_archive_slug_callback',                     // Callback function to render the field
        'staffh_archive_settings',                          // Settings page slug
        'staffh_archive_settings_section'                   // Section ID where the field should be added
    );

    // Register the "staffh_archive_slug" setting
    register_setting(
        'staffh_archive_settings_group',                            // Group ID (not related to sections)
        'staffh_archive_slug'                               // Setting name (option name in the database)
    );

    // Add the "Disable the archive?" field
    add_settings_field(
        'staffh_disable_archive',                           // Field ID
        'Disable the archive?',                             // Field title
        'staffh_disable_archive_callback',                  // Callback function to render the field
        'staffh_archive_settings',                                  // Settings page slug
        'staffh_archive_settings_section'                           // Section ID where the field should be added
    );

    // Register the "staffh_disable_archive_page" setting
    register_setting(
        'staffh_archive_settings_group',                            // Group ID (not related to sections)
        'staffh_disable_archive_page',                      // Setting name (option name in the database)
        array(
            'default' => 0,                                 // Default value for the setting
            'type' => 'boolean',                            // Type of the setting (boolean in this case)
        )
    );

    // Add a nonce field to the form for security (optional but recommended)
    add_settings_field(
        'staffh_archive_settings_nonce',                           // Field ID
        '',                                                // Field title (leave it empty)
        'staffh_archive_settings_nonce_callback',                  // Callback function to render the field
        'staffh_archive_settings',                                 // Settings page slug
        'staffh_archive_settings_section'                          // Section ID where the field should be added
    );
}

add_action('admin_init', 'staffh_admin_init');
add_action('admin_init', 'staffh_archive_settings_init');
add_action('admin_post_staffh_archive_settings_save', 'staffh_archive_settings_save');