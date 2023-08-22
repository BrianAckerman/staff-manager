<?php
function add_staffh_quickcontacts_menu() {
    add_submenu_page(
        'edit.php?post_type=staff_member',      // Parent menu slug
        'Quick Contacts',                       // Page title
        'Quick Contacts',                       // Menu title
        'manage_options',                       // Required capability
        'staffh_quick_contacts',                // Submenu slug
        'staffh_quick_contacts'                 // Callback function
    );
}
add_action( 'admin_menu', 'add_staffh_quickcontacts_menu' );

function staffh_quick_contacts() {
    // Enqueue scripts and styles
    wp_enqueue_script('vue', 'https://unpkg.com/vue@3/dist/vue.global.js', array(), '3.2.21', true);
    wp_enqueue_script('staffh_script', STAFFH_PLUGIN_URL . 'dist/admin_quickcontacts.js', array('vue'), '1.0', true);
    wp_enqueue_style('staffh_style', STAFFH_PLUGIN_URL . 'dist/admin_quickcontacts.css', array(), '1.0');

    // Localize user capability and quick contacts data
    $quick_contacts = fetch_quick_contacts_data(); // Fetch the quick contacts data from the database
    wp_localize_script('staffh_script', 'wpData', array(
        'nonce' => wp_create_nonce('wp_rest'),
        'canEditPosts' => current_user_can('edit_posts'),
        'quickContacts' => $quick_contacts, // Pass the quick contacts data for Vue app
    ));

    echo '<div id="quick_contact_app"></div>';
}

// Fetch the quick contacts data from the database
function fetch_quick_contacts_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'staffh_quickcontacts';

    $contacts = $wpdb->get_results("SELECT * FROM $table_name");

    $formatted_contacts = array();

    foreach ($contacts as $contact) {
        $contact_id = $contact->id;
        $table_name = $wpdb->prefix . 'postmeta';
        $meta_key = 'associated_contacts';

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT post_id
                FROM {$wpdb->prefix}postmeta
                WHERE meta_key = %s
                AND (JSON_CONTAINS(meta_value, %s) OR JSON_CONTAINS(meta_value, %s))",
                $meta_key,
                json_encode((int)$contact_id), // Cast to integer
                json_encode((string)$contact_id) // Cast to string
            )
        );
        
        $staff_members = array();

        foreach ($results as $result) {
            $post_id = $result->post_id;
            $post = get_post($post_id, OBJECT, 'any');
            
            if ($post) {
                $staff_member = array(
                    'id' => $post->ID,
                    'name' => $post->post_title,
                    'status' => $post->post_status,
                );
                
                $staff_members[] = $staff_member;
            }
        }

        $formatted_contacts[] = array(
            'id' => $contact->id,
            'status' => $contact->status,
            'name' => $contact->name,
            'title' => $contact->title,
            'email' => $contact->email,
            'phone' => $contact->phone,
            'priority' => $contact->priority,
            'staff_members'=> $staff_members,
        );
    }

    return $formatted_contacts;
}


// Fetch the quick contacts data associated to a particular post from the database
function get_associated_quick_contacts($post_id) {
    $associated_contact_ids_string = get_post_meta($post_id, 'associated_contacts', true);

    $formatted_contacts = array();

    if ($associated_contact_ids_string) {
        // Decode the JSON string into an array
        $associated_contact_ids = json_decode($associated_contact_ids_string, true);

        foreach ($associated_contact_ids as $contact_id) {
            $contact = get_quick_contact_by_id($contact_id); // Custom function to retrieve quick contact data by ID

            if ($contact) {
                $formatted_contacts[] = array(
                    'id' => $contact_id,
                    'status' => $contact['status'],
                    'name' => $contact['name'],
                    'title' => $contact['title'],
                    'email' => $contact['email'],
                    'phone' => $contact['phone'],
                    'priority' => $contact['priority'],
                );
            }
        }
    }

    return $formatted_contacts;
}


// Get a quick contact by its ID
function get_quick_contact_by_id($contact_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'staffh_quickcontacts';

    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $contact_id);
    $contact = $wpdb->get_row($query, ARRAY_A);

    return $contact;
}

?>