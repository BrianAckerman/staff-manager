<?php
function staff_members_plugin_menu() {
    // Remove original Staff Members menu item
    remove_menu_page( 'edit.php?post_type=staff_member' );

    // Add custom Staff Members menu item with submenus
    add_menu_page(
        'My Staff Members Plugin',
        'Staff Members',
        'manage_options',
        'manage-staff',
        'staff_members_plugin_page',
        'dashicons-id',
        20
    );

    add_submenu_page(
        'manage-staff',
        'Add New Staff Member',
        'Add New',
        'manage_options',
        'post-new.php?post_type=staff_member'
    );

   add_submenu_page(
        'manage-staff',
        'All Staff Members',
        'All Staff Members',
        'manage_options',
        'edit.php?post_type=staff_member'
    );
}
add_action( 'admin_menu', 'staff_members_plugin_menu' );

function staff_manager_enqueue_scripts($hook_suffix) {
    // Check if the current page is the list page
    if ($hook_suffix !== 'toplevel_page_manage-staff') {
        return;
    }

    // Enqueue scripts and styles
    wp_enqueue_script( 'vue', 'https://unpkg.com/vue@3/dist/vue.global.js', array(), '1.0');
    wp_enqueue_script('msmp-script', MSMP_PLUGIN_URL . 'dist/staff-list-bundle.js', array('vue'), '1.0', true);
    wp_enqueue_style('msmp-style', MSMP_PLUGIN_URL . 'dist/staff-list-bundle.css', array(), '1.0');

    // Localize user capability
    wp_localize_script('msmp-script', 'wpData', array(
        'nonce' => wp_create_nonce('wp_rest'),
        'canEditPosts' => current_user_can('edit_posts')
    ));
}
add_action('admin_enqueue_scripts', 'staff_manager_enqueue_scripts');


function staff_members_plugin_page() {  
?>

<div id="staff-members">
    <h1>Staff Members</h1>
    <div id="app"></div>
</div>

<?php
}
add_action( 'admin_menu', 'staff_members_plugin_menu' );
?>