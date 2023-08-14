<?php

// Create quick contacts tables
function create_quick_contact_tables() {
    global $wpdb;
    $table_name_quick_contacts = $wpdb->prefix . 'staffh_quickcontacts';
    $table_name_staff_associations = $wpdb->prefix . 'staffh_quickcontact_associations';

    $charset_collate = $wpdb->get_charset_collate();

    // Check if the tables already exist
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name_quick_contacts'") !== null;
    $associations_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name_staff_associations'") !== null;

    if (!$table_exists) {
        // Create the quick contacts table
        $sql = "CREATE TABLE $table_name_quick_contacts (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            status TINYINT(1) DEFAULT 0,
            name VARCHAR(255) NOT NULL,
            title VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    if (!$associations_table_exists) {
        // Create the staff associations table
        $sql_staff_associations = "CREATE TABLE $table_name_staff_associations (
            quick_contact_id BIGINT UNSIGNED NOT NULL,
            staff_member_id BIGINT UNSIGNED NOT NULL,
            PRIMARY KEY (quick_contact_id, staff_member_id),
            FOREIGN KEY (quick_contact_id) REFERENCES $table_name_quick_contacts(id),
            FOREIGN KEY (staff_member_id) REFERENCES {$wpdb->prefix}posts(ID) ON DELETE CASCADE
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_staff_associations);
    }

    // Get staff member post IDs
    $staff_member_ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = %s",
            'staff_member'
        )
    );
}

// Plugin activation check
function plugin_activation_check() {
    $is_plugin_activated = get_option('my_plugin_activated', false);
    if (!$is_plugin_activated) {
        // Run your activation code here
        create_quick_contact_tables();

        // Set the plugin activation flag to true
        update_option('my_plugin_activated', true);
    }
}
add_action('activated_plugin', 'plugin_activation_check');
?>