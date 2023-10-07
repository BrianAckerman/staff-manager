<?php

function staff_members_settings_menu() {
    add_submenu_page(
        'edit.php?post_type=staff_member',      // Parent menu slug
        'Settings',                             // Page title
        'Settings',                             // Menu title
        'manage_options',                       // Required capability
        'staffh-settings',                      // Submenu slug
        'staffh_settings'                       // Callback function
    );
}
add_action( 'admin_menu', 'staff_members_settings_menu' );

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
    $disable_archive = get_option('staffh_disable_archive_page');
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

    // Handle simple text options
    $options = [
        'staffh_cta_primary_bg',
        'staffh_cta_primary_text',
        'staffh_cta_secondary_bg',
        'staffh_cta_secondary_text',
        'staffh_cta_tertiary_bg',
        'staffh_cta_tertiary_text',
    ];

    foreach ($options as $option) {
        staffh_update_option_if_set($option, $option);
    }

    // Handle social defaults array
    $social_links = [];
    $social_options = [
        'staffh_default_social_facebook_text' => 'Facebook',
        'staffh_default_social_twitterX_text' => 'TwitterX',
        'staffh_default_social_instagram_text' => 'Instagram',
        'staffh_default_social_linkedin_text' => 'LinkedIn'
    ];

    foreach ($social_options as $input_name => $type) {
        if (isset($_POST[$input_name]) && !empty($_POST[$input_name])) {
            $social_links[] = [
                'type' => $type,
                'url' => $_POST[$input_name]
            ];
        }
    }

    update_option('staffh_default_social_links', $social_links);


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
    $primary_bg = get_option('staffh_cta_primary_bg');

    echo '<input type="text" class="color-picker" name="staffh_cta_primary_bg" value="' . $primary_bg . '" />';
}

function staffh_cta_primary_text_callback() {
    $primary_text = get_option('staffh_cta_primary_text');

    echo '<input type="text" class="color-picker" name="staffh_cta_primary_text" value="' . $primary_text . '" />';
}

function staffh_cta_secondary_bg_callback() {
    $secondary_bg = get_option('staffh_cta_secondary_bg');

    echo '<input type="text" class="color-picker" name="staffh_cta_secondary_bg" value="' . $secondary_bg . '" />';
}

function staffh_cta_secondary_text_callback() {
    $secondary_text = get_option('staffh_cta_secondary_text');

    echo '<input type="text" class="color-picker" name="staffh_cta_secondary_text" value="' . $secondary_text . '" />';
}

function staffh_cta_tertiary_bg_callback() {
    $tertiary_bg = get_option('staffh_cta_tertiary_bg');

    echo '<input type="text" class="color-picker" name="staffh_cta_tertiary_bg" value="' . $tertiary_bg . '" />';
}

function staffh_cta_tertiary_text_callback() {
    $tertiary_text = get_option('staffh_cta_tertiary_text');

    echo '<input type="text" class="color-picker" name="staffh_cta_tertiary_text" value="' . $tertiary_text . '" />';
}


// Social Callbacks
function staffh_default_social_callback($args) {
    $default_social_links = get_option('staffh_default_social_links', []);
    $link = '';
    foreach ($default_social_links as $social_link) {
        if ($social_link['type'] === $args['label_for']) {
            $link = $social_link['url'];
            break;
        }
    }
    echo "<input type='text' name='{$args['option_name']}' value='$link' />";
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
    <h1>Carrington Staff Settings</h1>

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
        'social' => 'staffh_social_settings_section',
        'settings' => 'staffh_settings_section',
    ];

    foreach ($sections as $section) {
        add_settings_section($section, '', '', 'staffh_settings');
    }

    $fields = [
        'staffh_archive_slug' => [
            'title' => 'Archive Slug',
            'callback' => 'staffh_archive_slug_callback',
            'section' => 'archive',
            'setting_name' => 'staffh_archive_slug',
            'setting_type' => null,
            'default_value' => null
        ],
        'staffh_disable_archive' => [
            'title' => 'Disable the archive?',
            'callback' => 'staffh_disable_callback',
            'section' => 'archive',
            'setting_name' => 'staffh_disable_archive_page',
            'setting_type' => 'boolean',
            'default_value' => 1
        ],
        'staffh_cta_primary_bg' => [
            'title' => 'Primary CTA Background Color',
            'callback' => 'staffh_cta_primary_bg_callback',
            'section' => 'ctas',
            'setting_name' => 'staffh_cta_primary_bg',
            'setting_type' => null,
            'default_value' => null
        ],
        'staffh_cta_primary_text' => [
            'title' => 'Primary CTA Text Color',
            'callback' => 'staffh_cta_primary_text_callback',
            'section' => 'ctas',
            'setting_name' => 'staffh_cta_primary_text',
            'setting_type' => null,
            'default_value' => null
        ],
        'staffh_cta_secondary_bg' => [
            'title' => 'Secondary CTA Background Color',
            'callback' => 'staffh_cta_secondary_bg_callback',
            'section' => 'ctas',
            'setting_name' => 'staffh_cta_secondary_bg',
            'setting_type' => null,
            'default_value' => null
        ],
        'staffh_cta_secondary_text' => [
            'title' => 'Secondary CTA Text Color',
            'callback' => 'staffh_cta_secondary_text_callback',
            'section' => 'ctas',
            'setting_name' => 'staffh_cta_secondary_text',
            'setting_type' => null,
            'default_value' => null
        ],
        'staffh_cta_tertiarty_bg' => [
            'title' => 'Tertiary CTA Background Color',
            'callback' => 'staffh_cta_tertiary_bg_callback',
            'section' => 'ctas',
            'setting_name' => 'staffh_cta_tertiary_bg',
            'setting_type' => null,
            'default_value' => null
        ],
        'staffh_cta_tertiary_text' => [
            'title' => 'Tertiary CTA Text Color',
            'callback' => 'staffh_cta_tertiary_text_callback',
            'section' => 'ctas',
            'setting_name' => 'staffh_cta_tertiary_text',
            'setting_type' => null,
            'default_value' => null
        ],
        'staffh_default_social_facebook' => [
            'title' => 'Facebook',
            'callback' => 'staffh_default_social_callback',
            'section' => 'social',
            'setting_name' => 'staffh_default_social_facebook_text',
            'setting_type' => null,
            'default_value' => null,
            'network' => 'Facebook'
        ],
        'staffh_default_social_instagram' => [
            'title' => 'Instagram',
            'callback' => 'staffh_default_social_callback',
            'section' => 'social',
            'setting_name' => 'staffh_default_social_instagram_text',
            'setting_type' => null,
            'default_value' => null,
            'network' => 'Instagram'
        ],
        'staffh_default_social_twitterX' => [
            'title' => 'TwitterX',
            'callback' => 'staffh_default_social_callback',
            'section' => 'social',
            'setting_name' => 'staffh_default_social_twitterX_text',
            'setting_type' => null,
            'default_value' => null,
            'network' => 'TwitterX'
        ],
        'staffh_default_social_linkedin' => [
            'title' => 'LinkedIn',
            'callback' => 'staffh_default_social_callback',
            'section' => 'social',
            'setting_name' => 'staffh_default_social_linkedin_text',
            'setting_type' => null,
            'default_value' => null,
            'network' => 'LinkedIn'
        ],
        'staffh_settings_nonce' => [
            'title' => '',
            'callback' => 'staffh_settings_nonce_callback',
            'section' => 'settings',
            'setting_name' => null,
            'setting_type' => null,
            'default_value' => null
        ]
    ];

    foreach ($fields as $field_id => $details) {
        $args = [
            'label_for' => $details['title'],
            'option_name' => $details['setting_name']
        ];
        add_settings_field($field_id, $details['title'], $details['callback'], 'staffh_settings', $sections[$details['section']], $args);

        if (isset($details['setting_name'])) {
            $register_args = ['type' => $details['setting_type'] ?? 'string', 'default' => $details['default_value'] ?? null];
            register_setting('staffh_settings_group', $details['setting_name'], $register_args);
        }
    }

}

add_action('admin_init', 'staffh_settings_init');
add_action('admin_post_staffh_settings_save', 'staffh_settings_save');