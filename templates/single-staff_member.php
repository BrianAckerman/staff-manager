<?php
/**
 * Template Name: Custom Staff Member Template
 */
get_header();
?>
<style>
.staff_member {
    display: grid;
    grid-template-columns: 300px 1fr 1fr;
    gap: 2.5%;
}
</style>
<div id="primary" class="content-area">
    <main id="main" class="site-main staff_member">
        <?php
        if (has_post_thumbnail()) {
            echo '<div class="staff_photo">';
            the_post_thumbnail();
            echo '</div>';
        }
        ?>
        <div class="staff_information">
            <?php
            // Start the loop.
            while (have_posts()) {
                the_post();
                $post_content = get_the_content();
                $parsed_data = json_decode($post_content, true);

                $fullName = $parsed_data['fullName'];
                $jobTitle = $parsed_data['jobTitle'];
                $email = $parsed_data['email'];
                $officePhone = $parsed_data['officePhone'];
                $cellPhone = $parsed_data['cellPhone'];
                $about = $parsed_data['about'];
                $about_sanitized = wp_kses_post($about);
                $staffLinks = $parsed_data['staffLinks'];

                echo '<h1>';
                if (isset($fullName)) {
                    echo '<div class="staff_heading">' . $fullName . '</div>';
                }
                if (isset($jobTitle)) {
                    echo '<div class="sub_heading">' . $jobTitle . '</div>';
                }
                echo '</h1>';
                echo '<address>';
                if (isset($officePhone)) {
                    echo '<div class="staff_phone"><span class="staff_address_label">Office: </span><a href="tel:' . $officePhone . '">' . $officePhone . '</a></div>';
                }
                if (isset($cellPhone)) {
                    echo '<div class="staff_phone"><span class="staff_address_label">Cell: </span><a href="tel:' . $cellPhone . '">' . $cellPhone . '</a></div>';
                }
                if (isset($email)) {
                    echo '<div class="staff_phone"><span class="staff_address_label">Email: </span><a href="mailto:' . $email . '">' . $email . '</a></div>';
                }
                if (isset($staffLinks) && is_array($staffLinks)) {
                    echo '<div class="staff_social">';
                    foreach ($staffLinks as $link) {
                        $type = $link['type'];
                        $url = $link['url'];

                        echo $type . ' ' . $url . '</br>';
                    }
                    echo '</div>';
                }
                if(isset($about_sanitized)) {
                    echo '</address>';
                    echo '<div class="staff_body">';
                    echo $about_sanitized;
                    echo '</div>';
                }
            }
            // End the loop.
            ?>
        </div>
    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>