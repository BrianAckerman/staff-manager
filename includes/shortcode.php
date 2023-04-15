<?php
// Register the [staff_members] shortcode
function msmp_register_staff_members_shortcode()
{
    add_shortcode('staff_members', 'msmp_render_staff_members_shortcode');
}
add_action('init', 'msmp_register_staff_members_shortcode');

// Render the [staff_members] shortcode
function msmp_render_staff_members_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'count' => -1,
        'order' => 'ASC',
        'orderby' => 'title',
    ), $atts, 'staff_members');

    $args = array(
        'post_type' => 'staff_member',
        'posts_per_page' => $atts['count'],
        'order' => $atts['order'],
        'orderby' => $atts['orderby'],
    );

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
?>
<div class="staff-member">
    <h3 class="staff-member-name"><?php the_title(); ?></h3>
    <?php
    $title_prefix = get_post_meta(get_the_ID(), '_msmp_staff_member_title_prefix', true);
    if ($title_prefix) {
        echo '<p class="staff-member-title-prefix">' . esc_html($title_prefix) . '</p>';
    }
    ?>
    <div class="staff-member-bio"><?php the_content(); ?></div>
</div>
<?php
        endwhile;
    endif;

    wp_reset_postdata();

    $output = ob_get_clean();

    return $output;
}