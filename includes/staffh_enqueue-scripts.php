<?php


// Staff single template styles
function staffh_enqueue_single_styles() {

    $current_post_type = get_post_type();
    if ($current_post_type && $current_post_type === 'staff_member') {
        wp_enqueue_style('staffh-styles', STAFFH_PLUGIN_URL . 'css/single-staff_member.css', array(), '1.0');
    }
}
add_action('wp_enqueue_scripts', 'staffh_enqueue_single_styles');

?>