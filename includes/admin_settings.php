<?php

// Enqueue color picker script
function enqueue_color_picker() {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
}
add_action('admin_enqueue_scripts', 'enqueue_color_picker');

// This section groups all functions related to settings and their callbacks
function radio_button($name, $value, $checked_value, $display_label = null) {
    // Use the value as the label if no display label is provided
    if ($display_label === null) {
        $display_label = $value;
    }

    $checked = checked($value, $checked_value, false);
    echo "<label><input type='radio' name='{$name}' value='{$value}' {$checked} disabled> {$display_label}</label><br>";
}

function staffh_archive_slug_callback() {
    $slug = get_option('staffh_archive_slug', 'staff-members');
    $page_exists = get_page_by_path($slug) ? 'The page ' . $slug . ' already exists' : '';
    $post_exists = get_page_by_path($slug, OBJECT, 'post') ? 'The post ' . $slug . ' already exists' : '';

    echo "<p>$page_exists</p>";
    echo "<p>$post_exists</p>";
    echo sprintf('<input type="text" name="staffh_archive_slug" value="%s" />', esc_attr($slug));
}

function staffh_disable_callback() {
    $disable_archive = get_option('staffh_disable_archive_page', 0);
    radio_button('staffh_disable_archive_page', '1', $disable_archive, "Yes");
    radio_button('staffh_disable_archive_page', '0', $disable_archive, "No");
}

function staffh_settings_nonce_callback() {
    wp_nonce_field('staffh_settings_nonce');
}

function staffh_settings_conflict_notice() {
    echo '<div class="error notice"><p>';
    esc_html_e('The selected permalink slug conflicts with an existing page or post. Please choose a different slug.', 'staff-hero');
    echo '</p></div>';
}

function staffh_update_option_if_set($post_key, $option_name) {
    if (isset($_POST[$post_key]) && !empty($_POST[$post_key])) {
        update_option($option_name, $_POST[$post_key]);
    }
}

function staffh_boolean_option_update($post_key, $option_name) {
    $value = isset($_POST[$post_key]) && $_POST[$post_key] === '0' ? 0 : 1;
    update_option($option_name, $value);
}

function staffh_settings_save() {
    // Check the nonce for security
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'staffh_settings_nonce')) {
        wp_die('Invalid request.');
    }

    // Handle booleans
    staffh_boolean_option_update('staffh_include_archive_in_permalink', 'staffh_include_archive_in_permalink');
    staffh_boolean_option_update('staffh_disable_archive_page', 'staffh_disable_archive_page');

    // Handle colors
    staffh_update_option_if_set('staffh_cta_primary_bg', 'staffh_cta_primary_bg');
    staffh_update_option_if_set('staffh_cta_primary_text', 'staffh_cta_primary_text');
    staffh_update_option_if_set('staffh_cta_secondary_bg', 'staffh_cta_secondary_bg');
    staffh_update_option_if_set('staffh_cta_secondary_text', 'staffh_cta_secondary_text');
    staffh_update_option_if_set('staffh_cta_tertiary_bg', 'staffh_cta_tertiary_bg');
    staffh_update_option_if_set('staffh_cta_tertiary_text', 'staffh_cta_tertiary_text');

    // Archive slug
    if (isset($_POST['staffh_archive_slug'])) {
        $new_slug = sanitize_text_field($_POST['staffh_archive_slug']);
        $page_exists = get_page_by_path($new_slug) ? true : false;
        $post_exists = get_page_by_path($new_slug, OBJECT, 'post') ? true : false;

        if (!($page_exists || $post_exists)) {        
            update_option('staffh_archive_slug', $new_slug);
        } else {
            wp_safe_redirect(admin_url('admin.php?page=staffh-settings&page-already-exists=true&slug=' . $new_slug));
            exit;
        }
    }

    flush_rewrite_rules();
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

function staffh_cta_primary_bg_callback() {
    $primary_bg = get_option('staffh_cta_primary_bg', "#000000");

    echo '<input type="text" class="color-picker" name="staffh_cta_primary_bg" value="' . $primary_bg . '" />';
}

function staffh_cta_primary_text_callback() {
    $primary_text = get_option('staffh_cta_primary_text', "#FFFFFF");

    echo '<input type="text" class="color-picker" name="staffh_cta_primary_text" value="' . $primary_text . '" />';
}

function staffh_cta_secondary_bg_callback() {
    $secondary_bg = get_option('staffh_cta_secondary_bg', "#CCCCCC");

    echo '<input type="text" class="color-picker" name="staffh_cta_secondary_bg" value="' . $secondary_bg . '" />';
}

function staffh_cta_secondary_text_callback() {
    $secondary_text = get_option('staffh_cta_secondary_text', "#FFFFFF");

    echo '<input type="text" class="color-picker" name="staffh_cta_secondary_text" value="' . $secondary_text . '" />';
}

function staffh_cta_tertiary_bg_callback() {
    $tertiary_bg = get_option('staffh_cta_tertiary_bg', "#DDDDDD");

    echo '<input type="text" class="color-picker" name="staffh_cta_tertiary_bg" value="' . $tertiary_bg . '" />';
}

function staffh_cta_tertiary_text_callback() {
    $tertiary_text = get_option('staffh_cta_tertiary_text', "#000000");

    echo '<input type="text" class="color-picker" name="staffh_cta_tertiary_text" value="' . $tertiary_text . '" />';
}

// This section groups all functions related to admin initialization and settings display
function staffh_settings() {
    // Check if the form is submitted and handle the save action
    if (isset($_POST['submit'])) {
        // Call the custom save function to handle the form submission
        staffh_settings_save();
    }
    ?>
<div class="wrap">
    <h1>Settings</h1>

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
            settings_fields('staffh_settings_group');
            do_settings_sections('staffh_settings');
            ?>
        <input type="hidden" name="action" value="staffh_settings_save">
        <!-- Add this hidden field to identify the custom action -->
        <?php submit_button(); ?>
    </form>
</div>
<style>
.wp-picker-holder {
    position: absolute;
}
</style>
<script>
jQuery(document).ready(function($) {
    $('.color-picker').wpColorPicker();
});
</script>
<?php
}

function staffh_settings_init() {

    // Flush rewrite becuase we're dealing with permalinks
    flush_rewrite_rules();

    $sections = [
        'archive' => 'staffh_archive_settings_section',
        'ctas' => 'staffh_ctas_settings_section',
        'settings' => 'staffh_settings_section',
    ];

    $fields = [
        // 'Field ID' => [ 'title', 'callback', 'section', 'setting name', 'setting type', 'default value']
        'staffh_archive_slug' => ['Archive Slug', 'staffh_archive_slug_callback', 'archive', 'staffh_archive_slug', null, null],
        'staffh_disable_archive' => ['Disable the archive?', 'staffh_disable_callback', 'archive', 'staffh_disable_archive_page', 'boolean', 0],
        'staffh_cta_primary_bg' => ['Primary CTA Background Color', 'staffh_cta_primary_bg_callback', 'ctas', 'staffh_cta_primary_bg', null, null],
        'staffh_cta_primary_text' => ['Primary CTA Text Color', 'staffh_cta_primary_text_callback', 'ctas', 'staffh_cta_primary_text', null, null],
        'staffh_cta_secondary_bg' => ['Secondary CTA Background Color', 'staffh_cta_secondary_bg_callback', 'ctas', 'staffh_cta_secondary_bg', null, null],
        'staffh_cta_secondary_text' => ['Secondary CTA Text Color', 'staffh_cta_secondary_text_callback', 'ctas', 'staffh_cta_secondary_text', null, null],
        'staffh_cta_tertiary_bg' => ['Tertiary CTA Background Color', 'staffh_cta_tertiary_bg_callback', 'ctas', 'staffh_cta_tertiary_bg', null, null],
        'staffh_cta_tertiary_text' => ['Tertiary CTA Text Color', 'staffh_cta_tertiary_text_callback', 'ctas', 'staffh_cta_tertiary_text', null, null],
        'staffh_settings_nonce' => ['', 'staffh_settings_nonce_callback', 'settings', null, null, null]
    ];

    foreach ($sections as $section) {
        add_settings_section($section, '', '', 'staffh_settings');
    }

    foreach ($fields as $field_id => $details) {
        add_settings_field($field_id, $details[0], $details[1], 'staffh_settings', $sections[$details[2]]);

        if (isset($details[3])) {
            $args = ['type' => $details[4] ?? 'string', 'default' => $details[5] ?? null];
            register_setting('staffh_settings_group', $details[3], $args);
        }
    }
}

add_action('admin_init', 'staffh_settings_init');
add_action('admin_post_staffh_settings_save', 'staffh_settings_save');