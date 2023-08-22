<?php
// oxygen_staff_template.php
function render_staffh_oxygen_staff_template() {

    if ( have_posts() ) : 

    $post_id = get_the_ID();
    $quick_links = get_associated_quick_contacts($post_id);
    // Check if any active links exist in the $quick_links array
    $has_active_links = false;
    foreach ($quick_links as $link) {
        if ($link['status']) {
            $has_active_links = true;
            break;
        }
    }

    $cta_colors = [
        'primary' => [
            'bg' => get_option('staffh_cta_primary_bg'),
            'text' => get_option('staffh_cta_primary_text'),
        ],
        'secondary' => [
            'bg' => get_option('staffh_cta_secondary_bg'),
            'text' => get_option('staffh_cta_secondary_text'),
        ],
        'tertiary' => [
            'bg' => get_option('staffh_cta_tertiary_bg'),
            'text' => get_option('staffh_cta_tertiary_text'),
        ],
    ];

    ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php
                    $staff = [
                        'fullName' => '',
                        'jobTitle' => '',
                        'email' => '',
                        'officePhone' => '',
                        'cellPhone' => '',
                        'about' => '',
                        'staffLinks' => [],
                        'callsToAction' => [],
                    ];
                    while (have_posts()): the_post();
                        global $post;
                        $parsed_data = json_decode($post->post_content, true);

                        $staff = [
                            'fullName' => $parsed_data['fullName'] ?? '',
                            'jobTitle' => $parsed_data['jobTitle'] ?? '',
                            'email' => $parsed_data['email'] ?? '',
                            'officePhone' => $parsed_data['officePhone'] ?? '',
                            'cellPhone' => $parsed_data['cellPhone'] ?? '',
                            'about' => $parsed_data['about'] ? wp_kses_post($parsed_data['about']) : '',
                            'staffLinks' => $parsed_data['staffLinks'] ?? [],
                            'callsToAction' => $parsed_data['callsToAction'] ?? [],
                        ];

                        $class_hasimage = '';
                        $class_hassidebar = '';

                        if (  has_post_thumbnail() ) {
                            $class_hasimage = 'has-staffh-image';
                        }
                        if ( $quick_links || $staff['callsToAction'] ) {
                            $class_hassidebar = 'has-staffh-sidebar';
                        }
                    ?>
        <div class="staffh_staff_member <?php echo $class_hasimage ?> <?php echo $class_hassidebar ?>">

            <?php if (has_post_thumbnail()): ?>
            <div class="staffh_staff_photo"><?php the_post_thumbnail(); ?></div>
            <?php endif; ?>

            <div class="staffh_staff_information">
                <?php 
                            // Display name and job title
                            if ($staff['fullName'] || $staff['jobTitle']) {
                                echo '<h1>';
                                if ($staff['fullName']) echo "<div class=\"staffh_staff_heading\">{$staff['fullName']}</div>";
                                if ($staff['jobTitle']) echo "<div class=\"staffh_sub_heading\">{$staff['jobTitle']}</div>";
                                echo '</h1>';
                            }

                            // Display contact details
                            echo '<address>';
                            $phone_details = [
                                'Office' => $staff['officePhone'],
                                'Cell' => $staff['cellPhone']
                            ];

                            foreach ($phone_details as $label => $phone) {
                                if (!empty($phone)) {
                                    echo "<div><span>" . esc_html($label) . ": </span><a href=\"tel:" . esc_attr($phone) . "\">" . esc_html($phone) . "</a></div>";
                                }
                            }

                            if (!empty($staff['email'])) {
                                echo "<div>Email: <a href=\"mailto:" . esc_attr($staff['email']) . "\">" . esc_html($staff['email']) . "</a></div>";
                            }

                            // Display social links
                            if ($staff['staffLinks']) {
                                echo '<div class="staffh_staff_social"><ul>';
                                foreach ($staff['staffLinks'] as $link) {
                                    $icon_file_name = get_icon_file_name($link['type']);
                                    $img_url = plugins_url('../img/fontawesome/' . $icon_file_name, __FILE__);
                                    $type = strtolower($link['type']);
                                    echo "<li><a class=\"{$type}\" href=\"" . esc_url($link['url']) . "\" target=\"_blank\">";
                                    echo "<img src=\"" . esc_url($img_url) . "\" alt=\"" . esc_attr($link['type']) . "\" height=\"25px\" />";
                                    echo "</a></li>";
                                }
                                echo '</ul></div>';
                            }

                            // Display about
                            if ($staff['about']) echo "<div class=\"staffh_staff_body\">{$staff['about']}</div>";
                        endwhile;
                        ?>
            </div>

            <?php
                if(!empty($staff['callsToAction']) || $quick_links ) {
                    echo '<div class="staffh_sidebar">';
                    // Display calls to action
                    if ($staff['callsToAction']) {
                        echo '<div class="staffh_calls-to-action"><ul>';
                        foreach ($staff['callsToAction'] as $button) {
                            $colors = $cta_colors[$button['type']] ?? ['bg' => '#ccc', 'text' => '#000'];
                            echo "<li><a role=\"button\" style=\"background-color: {$colors['bg']}; color: {$colors['text']}\" href=\"" . esc_url($button['url']) . "\">{$button['label']}</a></li>";
                        }
                        echo '</ul></div>';
                    }

                        // Custom comparison function for usort
                    function sort_by_priority_and_title($a, $b) {
                        // If the priorities are different, sort by priority
                        if ($a['priority'] != $b['priority']) {
                            return $a['priority'] - $b['priority'];
                        }
                        
                        // If the priorities are the same, sort by name
                        return strcmp($a['title'], $b['title']);
                    }

                    // Sort the quick_links by priority and then name
                    usort($quick_links, 'sort_by_priority_and_title');

                    // Display quick links
                    if (!empty($quick_links) && $has_active_links) {
                        echo '<div class="staffh_quick-links"><h3>Quick Contacts</h3><ul>';
                        foreach ($quick_links as $link) {
                            if ($link['status']) {
                                echo "<li>";
                                echo "<h4>{$link['title']}</h4>";
                                echo "<h5>{$link['name']}</h5>";
                                echo "<p><a href=\"tel:{$link['phone']}\">{$link['phone']}</a></p>";
                                echo "<p><a href=\"mailto:{$link['email']}\">{$link['email']}</a></p>";
                                echo "</li>";
                            }
                        }
                        echo '</ul></div>';
                    }
                    echo '</div>';
                }
            ?>
        </div>
    </main>
</div>
<?php
    endif;
}
?>