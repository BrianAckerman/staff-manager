<?php 

// Create quick contacts tables
function create_quick_contact_tables() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'quick_contacts';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Create the separate table for the association with staff members
    $table_name_staff_members = $wpdb->prefix . 'quick_contact_staff_members';
    $sql_staff_members = "CREATE TABLE $table_name_staff_members (
        quick_contact_id BIGINT UNSIGNED NOT NULL,
        staff_member_id BIGINT UNSIGNED NOT NULL,
        PRIMARY KEY (quick_contact_id, staff_member_id),
        FOREIGN KEY (quick_contact_id) REFERENCES $table_name(id),
        FOREIGN KEY (staff_member_id) REFERENCES {$wpdb->prefix}staff_members(id)
    ) $charset_collate;";

    dbDelta($sql_staff_members);
}

add_action('init', 'create_quick_contact_tables');

?>