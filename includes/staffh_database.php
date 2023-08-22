<?php

define('STAFFH_SCHEMA_VERSION', '1.1');  // Updated version

// Create quick contacts tables
function create_quick_contact_tables() {
    global $wpdb;
    $current_version = get_option('staffh_schema_version', '1.0');

    $table_name_quick_contacts = $wpdb->prefix . 'staffh_quickcontacts';
    $table_name_staff_associations = $wpdb->prefix . 'staffh_quickcontact_associations';

    $charset_collate = $wpdb->get_charset_collate();

    $should_update_version = false;

    // Check if the tables already exist
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name_quick_contacts'") !== null;
    $associations_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name_staff_associations'") !== null;

    if (!$table_exists || version_compare($current_version, STAFFH_SCHEMA_VERSION, '<')) {
        // Create the quick contacts table
        $sql = "CREATE TABLE $table_name_quick_contacts (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            status TINYINT(1) DEFAULT 0,
            name VARCHAR(255) NOT NULL,
            title VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            priority TINYINT(1) DEFAULT 2,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

         $should_update_version = true;
    }

    if (!$associations_table_exists  || version_compare($current_version, STAFFH_SCHEMA_VERSION, '<')) {
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

         $should_update_version = true;
    }

    if ($should_update_version) {
        update_option('staffh_schema_version', STAFFH_SCHEMA_VERSION);
    }

    // Get staff member post IDs
    $staff_member_ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = %s",
            'staff_member'
        )
    );
}
add_action('plugins_loaded', 'create_quick_contact_tables');

// Plugin activation check
function plugin_activation_check() {
    $is_plugin_activated = get_option('staffh_plugin_activated', false);
    if (!$is_plugin_activated) {
        // Run your activation code here
        create_quick_contact_tables();

        // Set the plugin activation flag to true
        update_option('staffh_plugin_activated', true);
    }
}
add_action('activated_plugin', 'plugin_activation_check');
?>