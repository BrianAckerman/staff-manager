<?php
/**
 * Template Name: Custom Staff Member Template
 */
get_header();
if (has_post_thumbnail()) {
    $phone_col = '300px';
} else {
    $phone_col = '';
}
$post_id = get_the_ID();
$quick_links = get_associated_quick_contacts($post_id);
$quick_links_col = isset($quick_links) && is_array($quick_links) && !empty($quick_links) ? '300px' : '';

function get_icon_file_name($link_type) {
    $icon_map = array(
        'facebook' =>"facebook_logo.svg",
        '𝕏'=> "twitx_logo.svg",
        'instagram'=> "instagram_logo.svg",
        'youtube'=> "youtube_logo.svg",
        'linkedin'=> "linkedin_logo.svg",
    );

    return isset($icon_map[strtolower($link_type)]) ? $icon_map[strtolower($link_type)] : 'other-link_logo.svg'; // default_icon.svg is used when no match is found
}

?>
<style>
.staffh_staff_member {
    display: grid;
    grid-template-columns: <?php echo $phone_col ?> 1fr <?php echo $quick_links_col ?>;
    gap: 2.5%;
}

.staffh_staff_social ul {
    display: flex;
    justify-content: flex-start;
    list-style: none;
    margin: 0;
    padding: 0;
}

.staffh_staff_social ul li img {
    width: 100%;
    max-width: 100%;
    height: 30px;
}

.staffh_quick-links ul {
    list-style: none;
    padding: 0;
    margin: 0;
}


.staffh_quick-links li {
    margin-bottom: 1em;
}

.staffh_quick-links h4 {
    font-weight: bold;
    line-height: 1.2;
    margin: 0;
}

.staffh_quick-links h5 {
    font-weight: normal;
    line-height: 1.2;
    margin: 0;
}

.staffh_quick-links p {
    margin: 0;
}

.staffh_quick-links a[href*=tel] {
    color: currentcolor;
}

.staffh_quick-links a[href*=mailto] {
    text-decoration: underline;
}

.staffh_staff_information h1 {
    font-weight: bold;
    line-height: 1.2;
}

.staffh_staff_information h1 .staffh_sub_heading {
    font-size: 0.5em;
}
</style>
<div id="primary" class="content-area">
    <main id="main" class="site-main staffh_staff_member">
        <?php
        if (has_post_thumbnail()) {
            echo '<div class="staffh_staff_photo">';
            the_post_thumbnail();
            echo '</div>';
        }
        ?>
        <div class="staffh_staff_information">
            <?php
            // Start the loop.
            while (have_posts()) {
                the_post();
                $post_content = get_the_content();
                $parsed_data = json_decode($post_content, true);

                // Check and set fullName
                if (isset($parsed_data['fullName'])) {
                    $fullName = $parsed_data['fullName'];
                } else {
                    $fullName = null;
                }

                // Check and set jobTitle
                if (isset($parsed_data['jobTitle'])) {
                    $jobTitle = $parsed_data['jobTitle'];
                } else {
                    $jobTitle = null;
                }

                // Check and set email
                if (isset($parsed_data['email'])) {
                    $email = $parsed_data['email'];
                } else {
                    $email = null;
                }

                // Check and set officePhone
                if (isset($parsed_data['officePhone'])) {
                    $officePhone = $parsed_data['officePhone'];
                } else {
                    $officePhone = null;
                }

                // Check and set cellPhone
                if (isset($parsed_data['cellPhone'])) {
                    $cellPhone = $parsed_data['cellPhone'];
                } else {
                    $cellPhone = null;
                }

                // Check and set about
                if (isset($parsed_data['about'])) {
                    $about = $parsed_data['about'];
                    $about_sanitized = wp_kses_post($about); // Sanitize the 'about' field
                } else {
                    $about = null;
                    $about_sanitized = null;
                }

                // Check and set staffLinks
                if (isset($parsed_data['staffLinks'])) {
                    $staffLinks = $parsed_data['staffLinks'];
                } else {
                    $staffLinks = array(); // or null if you prefer
                }


                echo '<h1>';
                if (isset($fullName)) {
                    echo '<div class="staffh_staff_heading">' . $fullName . '</div>';
                }
                if (isset($jobTitle)) {
                    echo '<div class="staffh_sub_heading">' . $jobTitle . '</div>';
                }
                echo '</h1>';
                echo '<address>';
                if (isset($officePhone)) {
                    echo '<div class="staffh_staff_phone"><span class="staffh_staff_address_label">Office: </span><a href="tel:' . $officePhone . '">' . $officePhone . '</a></div>';
                }
                if (isset($cellPhone)) {
                    echo '<div class="staffh_staff_phone"><span class="staffh_staff_address_label">Cell: </span><a href="tel:' . $cellPhone . '">' . $cellPhone . '</a></div>';
                }
                if (isset($email)) {
                    echo '<div class="staffh_staff_email"><span class="staffh_staff_address_label">Email: </span><a href="mailto:' . $email . '">' . $email . '</a></div>';
                }
                if (isset($staffLinks) && is_array($staffLinks)) {
                    echo '<div class="staffh_staff_social"><ul>';
                    foreach ($staffLinks as $link) {
                        $type = $link['type'];
                        $url = $link['url'];
                        $icon_file_name = get_icon_file_name($type);

                        // Now, $icon_file_name contains the correct filename, based on the link type.
                        // You can use it to generate the img tag for the icon.
                        $img_url = plugins_url('../img/' . $icon_file_name, __FILE__);
                        echo '<li><a href="' . esc_url($url) . '">';
                        echo '<img src="' . esc_url($img_url) . '" alt="' . esc_attr($type) . '" height="25px" />';
                        echo '</a></li>';
                    }
                    echo '</ul></div>';
                }
                if(isset($about_sanitized)) {
                    echo '</address>';
                    echo '<div class="staffh_staff_body">';
                    echo $about_sanitized;
                    echo '</div>';
                }
            }
            // End the loop.
            ?>
        </div>

        <?php
            if(isset($quick_links) && is_array($quick_links) && !empty($quick_links)) {
                echo '<div>';
                echo '<div class="staffh_quick-links">';
                echo '<h3>Quick Contacts</h3>';
                echo '<ul>';
                foreach($quick_links as $link) {
                    $id = $link['id'];
                    $status = $link['status'];
                    // Only active quick contacts
                    if($status) {
                        $name = $link['name'];
                        $title = $link['title'];
                        $phone = $link['phone'];
                        $email = $link['email'];
                        echo '<li>';
                        echo '<h4>' . $title . '</h4>';
                        echo '<h5>' . $name . '</h5>';
                        echo '<p><a href="tel:' . $phone . '">' . $phone . '</a>';
                        echo '<p><a href="mailto:' . $email . '">' . $email . '</a>';
                        echo '</li>';
                    }
                }
                echo '</ul>';
                echo '</div>';
                echo '</div>';
            }
        ?>
</div>
</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>