<?php
function enqueue_archive_assets($posts) {
    if (empty($posts)) {
        return $posts;
    }

    $shortcode_found = false;

    // Search for the 'staff_archive' shortcode in the posts' content
    foreach ($posts as $post) {
        if (has_shortcode($post->post_content, 'staff_archive')) {
            $shortcode_found = true;
            break;
        }
    }

    // If the shortcode is found, enqueue the assets
    if ($shortcode_found) {
        wp_enqueue_script( 'vue', 'https://unpkg.com/vue@3/dist/vue.global.js', array(), '1.0');
        wp_enqueue_script('archive_staff-bundle', MSMP_PLUGIN_URL . 'dist/archive_staff-bundle.js', array(), '1.0.0', true);
        wp_enqueue_style('archive_staff-bundle', MSMP_PLUGIN_URL . 'dist/archive_staff-bundle.css', array(), '1.0.0');
    }

    return $posts;
}
add_filter('the_posts', 'enqueue_archive_assets');


// Register the [staff_archive] shortcode
function msmp_register_staff_members_shortcode()
{
    add_shortcode('staff_archive', 'msmp_render_staff_members_shortcode');
}
add_action('init', 'msmp_register_staff_members_shortcode');

// Render the [staff_archive] shortcode
function msmp_render_staff_members_shortcode($atts)
{
      // Fetch all staff posts
  $args = array(
    'post_type' => 'staff_member', // Replace this with your custom post type name
    'posts_per_page' => -1, // Retrieve all posts
  );
  $staff_posts = new WP_Query($args);

  // Initialize an empty array to store the JSON data
  $staff_data = array();

  // Loop through staff posts and extract the JSON data and featured image URL
  if ($staff_posts->have_posts()) :
    while ($staff_posts->have_posts()) : $staff_posts->the_post();
      $json_data = get_the_content();
      $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
      $staff_data[] = array_merge(json_decode($json_data, true), array('featured_image_url' => $featured_image_url));
    endwhile;
  endif;

  // Reset the global $post variable
  wp_reset_postdata();

  // Start output buffering
  ob_start();

  // Output a container for the Vue app
  echo '<div id="staff-archive-app"></div>';

  // Output the JSON data as a JavaScript variable
  echo '<script>';
  echo 'var staffData = ' . json_encode($staff_data) . ';';
  echo '</script>';

  // Return the buffered output
  return ob_get_clean();
}