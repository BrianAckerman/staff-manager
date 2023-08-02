<?php
/**
 * Template Name: Custom Staff Member Template
 */
get_header();
$phone_col = has_post_thumbnail() ? '300px' : '';
$post_id = get_the_ID();
$quick_links = get_associated_quick_contacts($post_id);
$quick_links_col = !empty($quick_links) && is_array($quick_links) ? '300px' : '';

function get_icon_file_name($link_type) {
    $icon_map = array(
        'facebook' => "facebook-f.svg",
        'twitterx'=> "twitterx.svg",
        'instagram'=> "instagram.svg",
        'youtube'=> "youtube.svg",
        'linkedin'=> "linkedin-in.svg",
    );
    return $icon_map[strtolower($link_type)] ?? 'link.svg';
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

.staffh_staff_social a {
    width: 40px;
    display: block;
    height: 40px;
    padding: 10px;
    border-radius: 100px;
    background: #ddd;
    margin: 5px;
    line-height: 1;
}

.staffh_staff_social ul li img {
    aspect-ratio: 1/1;
    filter: invert(1);
}

.staffh_staff_social a.facebook {
    background-color: #3b5998;
}

.staffh_staff_social a.instagram {
    background-color: #c32aa3;
}

.staffh_staff_social a.twitterx {
    background-color: #000;
}

.staffh_staff_social a.youtube {
    background-color: #ff0000;
}

.staffh_staff_social a.linkedin {
    background-color: #007bb6;
}

.staffh_staff_social a.other img {
    filter: invert(0);
}

.staffh_quick-links ul,
.staffh_calls-to-action ul,
.staffh_quick-links li,
.staffh_quick-links p {
    list-style: none;
    padding: 0;
    margin: 0;
}

.staffh_quick-links li {
    margin-bottom: 1em;
}

.staffh_quick-links h4,
.staffh_staff_information h1 {
    font-weight: bold;
    line-height: 1.2;
    margin: 0;
}

.staffh_staff_information h1 {
    margin-bottom: 10px;
}

.staffh_quick-links h5 {
    font-weight: normal;
    line-height: 1.2;
    margin: 0;
}

.staffh_quick-links a[href*=tel] {
    color: currentcolor;
}

.staffh_quick-links a[href*=mailto] {
    text-decoration: underline;
}

.staffh_staff_information address {
    font-style: normal;
}

.staffh_staff_information h1 .staffh_sub_heading {
    font-size: 0.5em;
}

.staffh_staff_social {
    padding: 10px 0;
}

.staffh_calls-to-action {
    margin-bottom: 1em;
    padding-bottom: 1em;
    border-bottom: solid 1px;
}

.staffh_calls-to-action a[role="button"] {
    display: block;
    padding: 1.25em 10px;
    background: #ccc;
    margin-bottom: 2px;
    text-align: center;
    line-height: 1.2;
    text-decoration: none;
}
</style>
<div id="primary" class="content-area">
    <main id="main" class="site-main staffh_staff_member">
        <?php if (has_post_thumbnail()) : ?>
        <div class="staffh_staff_photo"><?php the_post_thumbnail(); ?></div>
        <?php endif; ?>

        <div class="staffh_staff_information">
            <?php
            while (have_posts()) {
                the_post();
                $parsed_data = json_decode(get_the_content(), true);

                $fullName       = $parsed_data['fullName'] ?? null;
                $jobTitle       = $parsed_data['jobTitle'] ?? null;
                $email          = $parsed_data['email'] ?? null;
                $officePhone    = $parsed_data['officePhone'] ?? null;
                $cellPhone      = $parsed_data['cellPhone'] ?? null;
                $about          = $parsed_data['about'] ?? null;
                $staffLinks     = $parsed_data['staffLinks'] ?? [];
                $callsToAction  = $parsed_data['callsToAction'] ?? [];

                $about_sanitized = $about ? wp_kses_post($about) : null;

                echo '<h1>';
                echo $fullName ? "<div class=\"staffh_staff_heading\">{$fullName}</div>" : '';
                echo $jobTitle ? "<div class=\"staffh_sub_heading\">{$jobTitle}</div>" : '';
                echo '</h1>';
                echo '<address>';
                foreach (['Office' => $officePhone, 'Cell' => $cellPhone] as $label => $phone) {
                    if ($phone) {
                        echo "<div class=\"staffh_staff_phone\"><span class=\"staffh_staff_address_label\">{$label}: </span><a href=\"tel:{$phone}\">{$phone}</a></div>";
                    }
                }
                echo $email ? "<div class=\"staffh_staff_email\"><span class=\"staffh_staff_address_label\">Email: </span><a href=\"mailto:{$email}\">{$email}</a></div>" : '';
                
                if ($staffLinks) {
                    echo '<div class="staffh_staff_social"><ul>';
                    foreach ($staffLinks as $link) {
                        $type = $link['type'];
                        $url = $link['url'];
                        $icon_file_name = get_icon_file_name($type);
                        $img_url = plugins_url('../img/fontawesome/' . $icon_file_name, __FILE__);
                        echo "<li><a class=\"" . strtolower($type) . "\" href=\"" . esc_url($url) . "\" target=\"_blank\">";
                        echo "<img src=\"" . esc_url($img_url) . "\" alt=\"" . esc_attr($type) . "\" height=\"25px\" />";
                        echo "</a></li>";
                    }
                    echo '</ul></div>';
                }
                
                if ($about_sanitized) {
                    echo '</address>';
                    echo "<div class=\"staffh_staff_body\">{$about_sanitized}</div>";
                }
            }
            ?>
        </div>

        <?php
        if ((!empty($callsToAction) && is_array($callsToAction)) || (!empty($quick_links) && is_array($quick_links) && !empty($quick_links))) {
            echo '<div>';

            if (!empty($callsToAction) && is_array($callsToAction)) {
                echo '<div class="staffh_calls-to-action"><ul>';
                foreach ($callsToAction as $button) {
                    echo '<li>';
                    echo '<a role="button" href="' . esc_url($button['url']) . '" class="' . esc_attr($button['type']) . '">';
                    echo '<span>' . esc_attr($button['label']) . '</span>';
                    echo '</a>';
                    echo '</li>';
                }
                echo '</ul></div>';
            }

                if(!empty($quick_links) && isset($quick_links) && is_array($quick_links) && !empty($quick_links)) {
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
                }

                echo '</div>';
            }
        ?>
    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>