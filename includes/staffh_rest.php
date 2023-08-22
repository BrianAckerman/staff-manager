<?php

#$ HOOK
add_action('rest_api_init', 'register_test_reest_route');
##########################

## ROUTES
##########################

function register_test_reest_route() {

    // Test route
    register_rest_route('staff-hero/v1', '/test-rest/', array(
        'methods' => 'GET',
        'callback' => 'test_rest',
        'permission_callback' => '__return_true', // Allow public access
    ));

    ##### STAFF MEMBERS #####
    #########################

    // UPDATE a staff member
    register_rest_route('staff-hero/v1', '/staff-hero/(?P<id>\d+)', array(
        'methods' => 'POST',
        'callback' => 'update_staff_member',
        'permission_callback' => function() {
            return current_user_can('edit_others_posts');
        },
        'args' => array(
            'id' => array(
                'validate_callback' => function($param, $request, $key) {
                    return is_numeric($param);
                }
            ),
        ),
    ));

    // GET counts
    register_rest_route('staff-hero/v1', '/counts/', array(
        'methods' => 'GET',
        'callback' => 'get_staff_member_counts',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        },
    ));

    ##### QUICK CONTACTS #####
    ##########################

    // GET the quick contacts
    register_rest_route('staff-hero/v1', '/get-quick-contacts/', array(
        'methods' => 'GET',
        'callback' => 'get_quick_contacts_data',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        },
    ));

    // GET the staff associated with a particular quick contact
    register_rest_route('staff-hero/v1', '/get-contacts-associated-staff/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_associated_staff',
        'permission_callback' => '__return_true', // Allow public access
        /* 'permission_callback' => function() {
            return current_user_can('edit_others_posts');
        }, */
    ));

    // POST Toggle quick contact status 
    register_rest_route('staff-hero/v1', '/toggle-quick-contact-status/(?P<id>\d+)', array(
        'methods'  => 'POST',
        'callback' => 'toggle_quick_contact_status',
        'args'     => array(
            'id' => array(
                'type' => 'integer',
                'validate_callback' => 'rest_validate_request_arg',
            ),
        ),
        'permission_callback' => function ($request) {
            return current_user_can('edit_posts');
        },
    ));

    // POST UPDATE an existing quick contact
    register_rest_route('staff-hero/v1', '/update-quick-contact/(?P<id>\d+)', array(
        'methods'  => 'POST',
        'callback' => 'update_quick_contact',
        'args'     => array(
            'id' => array(
                'type' => 'integer',
                'validate_callback' => 'rest_validate_request_arg',
            ),
            'name' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'rest_validate_request_arg',
            ),
            'title' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'rest_validate_request_arg',
            ),
            'email' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_email',
                'validate_callback' => 'rest_validate_request_arg',
            ),
            'phone' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'rest_validate_request_arg',
            ),
            'priority' => array(
                'type' => 'integer',
                'validate_callback' => 'rest_validate_request_arg',
            ),
            'status' => array(
                'type' => 'boolean',
                'sanitize_callback' => 'absint',
                'validate_callback' => 'rest_validate_request_arg',
            ),
            'staff_members' => array(
                'type' => 'array',
                'validate_callback' => 'rest_validate_request_arg',
            ),
        ),
        'permission_callback' => function ($request) {
            return current_user_can('edit_posts');
        },
    ));

    // POST Add a new quick contact
    register_rest_route('staff-hero/v1', '/new-quick-contact/', array(
        'methods'  => 'POST',
        'callback' => 'new_quick_contact',
        'args'     => array(
            'id' => array(
                'type' => 'integer',
                'validate_callback' => 'rest_validate_request_arg',
            ),
            'name' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'rest_validate_request_arg',
            ),
            'title' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'rest_validate_request_arg',
            ),
            'email' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_email',
                'validate_callback' => 'rest_validate_request_arg',
            ),
            'phone' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'rest_validate_request_arg',
            ),
            'priority' => array(
                'type' => 'integer',
                'validate_callback' => 'rest_validate_request_arg',
            ),
            'status' => array(
                'type' => 'boolean',
                'sanitize_callback' => 'absint',
                'validate_callback' => 'rest_validate_request_arg',
            ),
            'staff_members' => array(
                'type' => 'array',
                'validate_callback' => 'rest_validate_request_arg',
            ),
        ),
        'permission_callback' => function ($request) {
            return current_user_can('edit_posts');
        },
    ));

    // DELETE a quick contact
    register_rest_route('staff-hero/v1', '/delete-quick-contact/(?P<id>\d+)', array(
        'methods'  => 'DELETE',
        'callback' => 'delete_quick_contact',
        'args'     => array(
            'id' => array(
                'type' => 'integer',
                'validate_callback' => 'rest_validate_request_arg',
            ),
        ),
        'permission_callback' => function ($request) {
            return current_user_can('edit_posts');
        },
    ));
}


## CALLBACKS
##########################

// Test
/**
 * Test REST callback
 *
 * @return WP_REST_Response Response object
 */
function test_rest() {
    $response = array(
        'message' => 'Success',
    );
    return new WP_REST_Response($response, 200);
}

// UPDATE staff member
/**
 * Update staff member callback
 *
 * @param WP_REST_Request $request REST request object
 * @return WP_REST_Response Response object
 */
function update_staff_member(WP_REST_Request $request) {
    $post_id = $request['id'];
    $status = $request['status'];

    $updated_post = wp_update_post(array(
        'ID' => $post_id,
        'post_status' => $status,
    ), true);

    if (is_wp_error($updated_post)) {
        return new WP_REST_Response(array('error' => $updated_post->get_error_message()), 400);
    }

    return new WP_REST_Response(get_post($post_id), 200);
}

// GET counts
/**
 * Get staff member counts callback
 *
 * @return WP_REST_Response Response object
 */
function get_staff_member_counts() {
    $counts = array(
        'publish' => 0,
        'draft' => 0,
        'trash' => 0,
    );

    $statuses = array('publish', 'draft', 'trash');

    foreach ($statuses as $status) {
        $query = new WP_Query(array(
            'post_type' => 'staff_member',
            'post_status' => $status,
            'posts_per_page' => -1,
        ));
        $counts[$status] = $query->found_posts;
    }

    return new WP_REST_Response($counts, 200);
}

/**
 * GET Callback function to fetch the quick contacts data from the database.
 *
 * @return WP_REST_Response The REST response containing the quick contacts data.
 */
function get_quick_contacts_data() {
    // Retrieve the quick contacts data from the database
    // fetch_quick_contacts_data lives in staffh_admin_quickcontacts.php
    $contacts = fetch_quick_contacts_data();

    // Return the quick contacts data as the REST response
    return rest_ensure_response($contacts);
}

/**
 * GET Callback function to fetch the staff associated with particular contact from the database.
 *
 * @return WP_REST_Response The REST response containing the staff data for a quick contact.
 */
function get_associated_staff($request) {
    global $wpdb;
    
    $contact_id = $request->get_param('id'); // Retrieve the contact ID from the request parameter

    $table_name = $wpdb->prefix . 'postmeta';
    $meta_key = 'associated_contacts';

    // Check the contact_id as an integer and as a string in the meta_value
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
        $post = get_post($post_id);
        
        if ($post) {
            $staff_member = array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'status' => $post->post_status,
            );
            
            $staff_members[] = $staff_member;
        }
    }

    // Return the staff data as the REST response
    return rest_ensure_response($staff_members);
}


// POST quick contact status
/**
 * Toggle the quick contact's status callback
 *
 * @return WP_REST_Response Response object
 */
function toggle_quick_contact_status($request) {
    global $wpdb;

    $contact_id = $request->get_param('id');

    // Fetch the contact from the database
    $table_name = $wpdb->prefix . 'staffh_quickcontacts';
    $contact = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $contact_id));

    if (!$contact) {
        $response = array(
            'success' => false,
            'message' => 'Contact not found.',
        );
        return rest_ensure_response($response);
    }

    // Toggle the contact's status
    $status = $contact->status ? 0 : 1;

    // Perform the database update to update the contact's status
    $updated_rows = $wpdb->update(
        $table_name,
        array('status' => $status),
        array('id' => $contact_id)
    );

    if ($updated_rows === false) {
        $response = array(
            'success' => false,
            'message' => 'Failed to update contact\'s status.',
        );
    } else {
        $response = array(
            'success' => true,
            'message' => 'Contact\'s status updated successfully.',
        );
    }

    return rest_ensure_response($response);
}

/**
 * Callback to handle quick contact update request
 *
 * @param WP_REST_Request $request The REST API request object.
 *
 * @return WP_REST_Response The REST API response object.
 */
function update_quick_contact($request) {
    global $wpdb;

    $contact_id = $request->get_param('id');
    $updated_contact_data = $request->get_json_params();
    $staff_members = isset($updated_contact_data['staff_members']) ? $updated_contact_data['staff_members'] : array();

    // Create an array to hold just the post_ids for easy checking later.
    $staff_member_ids = array_map(function($member) { return $member['id']; }, $staff_members);

    // Update the existing contact in the database
    $table_name = $wpdb->prefix . 'staffh_quickcontacts';

    $updated_rows = $wpdb->update(
        $table_name,
        array(
            'name'   => sanitize_text_field($updated_contact_data['name']),
            'status' => $updated_contact_data['status'],
            'title'  => sanitize_text_field($updated_contact_data['title']),
            'email'  => sanitize_email($updated_contact_data['email']),
            'phone'  => sanitize_text_field($updated_contact_data['phone']),
            'priority' => $updated_contact_data['priority'],
        ),
        array('id' => $contact_id)
    );

    if ($updated_rows === false) {
        $response = array(
            'success' => false,
            'message' => 'Failed to update contact.',
        );
        return rest_ensure_response($response);
    }

    // Fetch all postmeta records with the 'associated_contacts' meta key.
    $associations = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $wpdb->postmeta WHERE meta_key = %s",
            'associated_contacts'
        ), ARRAY_A
    );

    foreach ($associations as $record) {
        // Decode the array of contact IDs.
        $contact_ids = json_decode($record['meta_value'], true);

        // Check if the current quick contact is in the array.
        $key = array_search($contact_id, $contact_ids);

        // If the post ID is in the staff_members array and the contact ID is not already in the contact_ids, add it.
        if (in_array($record['post_id'], $staff_member_ids) && $key === false) {
            $contact_ids[] = $contact_id;
        } 
        // If the post ID is not in the staff_members array and the contact ID is in the contact_ids, remove it.
        else if (!in_array($record['post_id'], $staff_member_ids) && $key !== false) {
            unset($contact_ids[$key]);
        }

        // Re-index the array
        $contact_ids = array_values($contact_ids);

        // If the array is empty, delete the post meta, else update it.
        if (empty($contact_ids)) {
            delete_post_meta($record['post_id'], 'associated_contacts');
        } else {
            $contact_ids = json_encode($contact_ids);
            update_post_meta($record['post_id'], 'associated_contacts', $contact_ids);
        }
    }

    // Add a new association for any posts in staff_members that didn't already have an 'associated_contacts' meta field.
    foreach ($staff_member_ids as $post_id) {
        // Retrieve current array of associated contacts for the post.
        $current_contact_ids = get_post_meta($post_id, 'associated_contacts', true);
        $current_contact_ids = json_decode($current_contact_ids, true);

        // Check if the result is an array. If not, initialize as an empty array.
        if (!is_array($current_contact_ids)) {
            $current_contact_ids = array();
        }

        // If the contact_id isn't already in the array, add it.
        if (!in_array($contact_id, $current_contact_ids)) {
            $current_contact_ids[] = $contact_id;
            update_post_meta($post_id, 'associated_contacts', json_encode($current_contact_ids));
        }
    }

    // Prepare the success response
    $response = array(
        'success' => true,
        'message' => 'Contact saved successfully.',
    );

    return rest_ensure_response($response);
}


/**
 * Callback to handle adding a new quick contact request
 *
 * @param WP_REST_Request $request The REST API request object.
 *
 * @return WP_REST_Response The REST API response object.
 */
function new_quick_contact($request) {
    global $wpdb;

    $updated_contact_data = $request->get_json_params();
    $staff_members = isset($updated_contact_data['staff_members']) ? $updated_contact_data['staff_members'] : array();

    // Insert the new contact into the database
    $table_name = $wpdb->prefix . 'staffh_quickcontacts';

    $inserted_rows = $wpdb->insert(
        $table_name,
        array(
            'name'   => $updated_contact_data['name'],
            'status' => 1, // Set the status to 1
            'title'  => $updated_contact_data['title'],
            'email'  => $updated_contact_data['email'],
            'phone'  => $updated_contact_data['phone'],
        )
    );

    if ($inserted_rows === false) {
        $response = array(
            'success' => false,
            'message' => 'Failed to insert contact.',
        );
        return rest_ensure_response($response);
    }

    // Staff associations
    // Get the ID of the inserted contact
    $contact_id = $wpdb->insert_id;

    // Update associated contacts for each staff member
    foreach ($staff_members as $staff_member) {
        $staff_member_id = $staff_member['id'];

        // Get existing associated contacts
        $existing_ids_json = get_post_meta($staff_member_id, 'associated_contacts', true);
        $existing_ids_array = $existing_ids_json ? json_decode($existing_ids_json, true) : array();

        // Add the contact ID if it doesn't already exist
        if (!in_array($contact_id, $existing_ids_array)) {
            $existing_ids_array[] = $contact_id;
        }
        
        // Convert the array to JSON
        $existing_ids_json = wp_json_encode($existing_ids_array);

        update_post_meta($staff_member_id, 'associated_contacts', $existing_ids_json);
    }

    // Prepare the response
    $response = array(
        'success' => true,
        'message' => 'Contact added successfully.',
    );

    return rest_ensure_response($response);
}


/**
 * Callback to handle deleting a quick contact.
 *
 * @param WP_REST_Request $request The request object.
 * @return WP_REST_Response The response object.
 */
function delete_quick_contact($request) {
    global $wpdb;

    $contact_id = $request->get_param('id');

    // Delete the contact from the database
    $table_name = $wpdb->prefix . 'staffh_quickcontacts';
    $deleted_rows = $wpdb->delete($table_name, array('id' => $contact_id));

    if ($deleted_rows === false) {
        $response = array(
            'success' => false,
            'message' => 'Failed to delete contact.',
        );
        return rest_ensure_response($response);
    } 

    // Get all postmeta records with the 'associated_contacts' meta key
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT post_id, meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = %s",
            'associated_contacts'
        )
    );

    foreach ($results as $result) {
        $current_contact_ids = json_decode($result->meta_value, true);

        // Check if the contact_id is in the array, and if so, remove it
        if(($key = array_search($contact_id, $current_contact_ids)) !== false) {
            unset($current_contact_ids[$key]);

            // If the array is now empty, delete the record, otherwise update it
            if (empty($current_contact_ids)) {
                delete_post_meta($result->post_id, 'associated_contacts');
            } else {
                // Re-index the array
                $current_contact_ids = array_values($current_contact_ids);
                update_post_meta($result->post_id, 'associated_contacts', json_encode($current_contact_ids));
            }
        }
    }

    $response = array(
        'success' => true,
        'message' => 'Contact deleted successfully.',
    );

    return rest_ensure_response($response);
}

?>