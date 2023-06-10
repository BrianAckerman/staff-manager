<?php 

/**
 * Create the Quick Contacts table.
 */
function create_quick_contacts_table() {

    // Get the database object.
    $db = get_database_object();

    // Create the table schema.
    $sql = <<<SQL
    CREATE TABLE quick_contacts (
        id INT NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        phone_number VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        post_id INT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (post_id) REFERENCES posts (ID)
    )
    SQL;

    // Execute the SQL statement.
    dbDelta( $sql );

}

/**
 * Delete a Quick Contact if the corresponding Staff Member post is deleted.
 */
function delete_quick_contact_if_deleted( $post_id ) {

    // Get all Quick Contact records that are linked to the deleted post.
    $quick_contacts = get_quick_contacts_by_post_id( $post_id );

    // Loop through the Quick Contact records and delete them.
    foreach ( $quick_contacts as $quick_contact ) {
        delete_quick_contact( $quick_contact->id );
    }

}

/**
 * Disable a Quick Contact if the corresponding Staff Member post is set to draft.
 */
function disable_quick_contact_if_draft( $quick_contact_id ) {

    // Get the Quick Contact record.
    $quick_contact = get_quick_contact( $quick_contact_id );

    // Get the Staff Member post record.
    $staff_member_post = get_post( $quick_contact->post_id );

    // If the Staff Member post is set to draft, disable the Quick Contact.
    if ( $staff_member_post->post_status === 'draft' ) {
        update_quick_contact( $quick_contact_id, [ 'enabled' => false ] );
    }

}

?>