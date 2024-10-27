<?php
/**
 * Renders the content of the submenu page for the AI for SEO help page.
 *
 * @since 1.2.1
 */

if (!defined("ABSPATH")) {
    exit;
}


// ___________________________________________________________________________________________ \\
// === PREPARE =============================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

// Variable for the subject-options for the contact form
$ai4seo_contact_subject_options = array(
    "0" => __("Please select", "ai-for-seo"),
    "Request a Feature" => __("Request a Feature", "ai-for-seo"),
    "Report a bug" => __("Report a bug", "ai-for-seo"),
    "Get a quote" => __("Get a quote", "ai-for-seo"),
    "General question" => __("General question", "ai-for-seo"),
    "I need help with my SEO" => __("I need help with my SEO", "ai-for-seo"),
    "Other" => __("Other", "ai-for-seo"),
);

$ai4seo_free_plan_credits = ai4seo_get_plan_credits("free");
$ai4seo_s_plan_credits = ai4seo_get_plan_credits("s");
$ai4seo_m_plan_credits = ai4seo_get_plan_credits("m");
$ai4seo_l_plan_credits = ai4seo_get_plan_credits("l");


// ___________________________________________________________________________________________ \\
// === PROCESS CONTACT FORM ================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

do {
    if (isset($_POST["ai4seo-contact-form-submit"])) {
        // check if the nonce is valid
        if (!isset($_POST['ai4seo-contact-form-nonce']) || !wp_verify_nonce($_POST['ai4seo-contact-form-nonce'], 'ai4seo-contact-form')) {
            // Invalid nonce, stop the script.
            echo "<div class='notice notice-error is-dismissible'>";
                echo "<p>" . esc_html__("Security check failed. Please try again.", "ai-for-seo") . "</p>";
            echo "</div>";
            break;
        }

        // Stop script if the user tries to submit the form within 60 seconds after the last submit
        $ai4seo_last_contact_form_submit_time = (int) get_transient('ai4seo_last_contact_form_submit_timestamp');
        
        if ($ai4seo_last_contact_form_submit_time && (time() - $ai4seo_last_contact_form_submit_time) < 60) {
            echo "<div class='notice notice-error is-dismissible'>";
                echo "<p>" . esc_html__("You can submit only one request every 60 seconds. Please wait a moment and try again.", "ai-for-seo") . "</p>";
            echo "</div>";
            break;
        }

        // Sanitize
        $ai4seo_contact_form_name = sanitize_text_field($_POST["ai4seo-contact-form-name"] ?? "");
        $ai4seo_contact_form_email = sanitize_email($_POST["ai4seo-contact-form-email"] ?? "");
        $ai4seo_contact_form_subject = sanitize_text_field($_POST["ai4seo-contact-form-subject"] ?? "");
        $ai4seo_contact_form_message = sanitize_textarea_field($_POST["ai4seo-contact-form-message"] ?? "");

        // shorten name and message
        $ai4seo_contact_form_name = substr($ai4seo_contact_form_name, 0, 64);
        $ai4seo_contact_form_message = substr($ai4seo_contact_form_message, 0, 5000);

        // Make sure that name exists
        if (!$ai4seo_contact_form_name || !preg_match("/^[\p{L} '-]+$/u", $ai4seo_contact_form_name)) {
            echo "<div class='notice notice-error is-dismissible'>";
                echo "<p>" . esc_html__("Please enter a valid name!", "ai-for-seo") . "</p>";
            echo "</div>";
            break;
        }

        // Make sure that email exists
        if (!$ai4seo_contact_form_email || !filter_var($ai4seo_contact_form_email, FILTER_VALIDATE_EMAIL)) {
            echo "<div class='notice notice-error is-dismissible'>";
                echo "<p>" . esc_html__("Please enter a valid email-address!", "ai-for-seo") . "</p>";
            echo "</div>";
            break;
        }

        // Make sure that subject exists
        if (!$ai4seo_contact_form_subject || $ai4seo_contact_form_subject == "0" || !isset($ai4seo_contact_subject_options[$ai4seo_contact_form_subject])) {
            echo "<div class='notice notice-error is-dismissible'>";
                echo "<p>" . esc_html__("Please enter a valid subject!", "ai-for-seo") . "</p>";
            echo "</div>";
            break;
        }

        // Make sure that message exists
        if (!$ai4seo_contact_form_message) {
            echo "<div class='notice notice-error is-dismissible'>";
                echo "<p>" . esc_html__("Please enter a message!", "ai-for-seo") . "</p>";
            echo "</div>";
            break;
        }

        // Prepare send email
        $ai4seo_email_recipient = esc_html(sanitize_email(AI4SEO_SUPPORT_EMAIL));
        $ai4seo_contact_form_subject = esc_html($ai4seo_contact_form_subject);
        $ai4seo_contact_form_message = nl2br(esc_textarea($ai4seo_contact_form_message));

        // Prepare headers
        $ai4seo_email_headers = array(
            "MIME-Version: 1.0",
            "Content-type: text/html; charset=utf-8",
            "From: " . esc_html($ai4seo_contact_form_name) . " <" . esc_html($ai4seo_contact_form_email) . ">",
        );

        // Send email
        $ai4seo_sent_email_successfully = wp_mail($ai4seo_email_recipient, $ai4seo_contact_form_subject, $ai4seo_contact_form_message, $ai4seo_email_headers);

        // Display success message
        if ($ai4seo_sent_email_successfully) {
            echo "<div class='notice notice-success is-dismissible'>";
                echo "<p>" . esc_html__("Thank you for your message. We have successfully received your email and will be in touch with you shortly.", "ai-for-seo") . "</p>";
            echo "</div>";

            set_transient('ai4seo_last_contact_form_submit_timestamp', time(), 60);
        }

        // Display error message
        else {
            echo "<div class='notice notice-error is-dismissible'>";
            echo "<p>" . sprintf(
                    esc_html__(
                        "Your system encountered an issue sending the email. Please try again later, or feel free to contact us directly if the problem persists: %s.",
                        "ai-for-seo"
                    ),
                    esc_html(AI4SEO_SUPPORT_EMAIL)
                ) . "</p>";
            echo "</div>";
        }
    }
} while (0);


// ___________________________________________________________________________________________ \\
// === JAVASCRIPT ============================================================================ \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

?><script type="text/javascript">
    jQuery(document).ready(function() {
        // Function to perform the search
        jQuery("#ai4seo-help-search").on("keyup", function() {
            var search_text = jQuery(this).val().toLowerCase();
            var faq_section_holder_element = jQuery(".ai4seo-faq-section-holder");
            var faq_entry_holder_element = jQuery(".ai4seo-accordion-holder");
            var no_results_notice_holder = jQuery("#ai4seo-help-faq-search-notice");
            var has_results = false;

            if (search_text.length >= 3) {
                // Hide all faq-holders once the minimum of 3 characters have been added to the search field
                faq_entry_holder_element.hide();

                // Loop through each faq-holder to check for a match
                faq_entry_holder_element.each(function() {
                    var headline = jQuery(this).find(".ai4seo-accordion-headline").text().toLowerCase();
                    var content = jQuery(this).find(".ai4seo-accordion-content").text().toLowerCase();

                    // Check if the search_text is found in either the headline or the content
                    if (headline.includes(search_text) || content.includes(search_text)) {
                        // Show this faq-entry if a match was found
                        jQuery(this).show();
                        has_results = true;
                    }
                });

                // Loop through each faq-section-holder to check if there are still faq-entries in this section
                faq_section_holder_element.each(function() {
                    if (jQuery(this).find(".ai4seo-accordion-headline:visible").length !== 0) {
                        jQuery(this).show();
                    } else {
                        jQuery(this).hide();
                    }
                });

                // Toggle the no results message based on whether matches have been found
                if (has_results) {
                    no_results_notice_holder.hide();
                } else {
                    no_results_notice_holder.show();
                }
            } else {
                // Show all accordion holders and hide the no results message if less than 3 characters are entered
                faq_entry_holder_element.show();
                faq_section_holder_element.show();
                no_results_notice_holder.hide();
            }
        });

        // check for any anchor in the url and click the corresponding button
        var ai4seo_location_hash = window.location.hash;

        if (ai4seo_location_hash) {
            jQuery("a[href='" + ai4seo_location_hash + "']").children().click();
        }
    });
</script><?php


// ___________________________________________________________________________________________ \\
// === OUTPUT ================================================================================ \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

// === CONTAINERS FOR THE BUTTONS TO THE HELP-AREAS ========================================== \\

// Getting started
echo "<a href='#ai4seo-getting-started-section'><div class='ai4seo-help-preview-selection' onclick='jQuery(\".ai4seo-help-content\").hide();jQuery(\"#ai4seo-help-getting-started\").show();'>";
    echo ai4seo_wp_kses(ai4seo_get_svg_tag("rocket"));
    echo "<span>" . esc_html__("Getting started", "ai-for-seo") . "</span>";
echo "</div></a>";

// FAQ
echo "<a href='#ai4seo-faq-section'><div class='ai4seo-help-preview-selection' onclick='jQuery(\".ai4seo-help-content\").hide();jQuery(\"#ai4seo-help-faq\").show();'>";
    echo "<i class='dashicons dashicons-editor-help'></i>";
    echo "<span>" . esc_html__("F.A.Q.", "ai-for-seo") . "</span>";
echo "</div></a>";

// Contact
#echo "<a href='#ai4seo-contact-section'><div class='ai4seo-help-preview-selection'  onclick='jQuery(\".ai4seo-help-content\").hide();jQuery(\"#ai4seo-help-contact\").show();'>";
# V1.2.1: workaround: send user to robhubs contact form for now
echo "<a href='" . esc_attr(AI4SEO_OFFICIAL_WEBPAGE) . "/contact' target='_blank'><div class='ai4seo-help-preview-selection'>";
    echo "<i class='dashicons dashicons-email'></i>";
    echo "<span>" . esc_html__("Contact", "ai-for-seo") . "</span>";
echo "</div></a>";

// Useful links
echo "<a href='#ai4seo-links-section'><div class='ai4seo-help-preview-selection'  onclick='jQuery(\".ai4seo-help-content\").hide();jQuery(\"#ai4seo-help-links\").show();'>";
    echo "<i class='dashicons dashicons-admin-links'></i>";
    echo "<span>" . esc_html__("Useful links", "ai-for-seo") . "</span>";
echo "</div></a>";

echo "<div class='ai4seo-clear'></div>";


// === GETTING STARTED ======================================================================= \\

echo "<div class='ai4seo-display-none ai4seo-help-content' id='ai4seo-help-getting-started'>";
    // Headline
    echo "<h1 id='ai4seo-getting-started-section'>";
        echo esc_html__("Getting started", "ai-for-seo");
    echo "</h1>";

    // === FIRST STEPS =========================================================================== \\

    $ai4seo_this_accordion_content = "<p><b>1.</b> " . __("<b>Dashboard</b>: Take a look at the statistics. These show which parts of your website have been optimized and which parts have not.", "ai-for-seo") . "</p>";
    $ai4seo_this_accordion_content .= "<p><b>2.</b> " . sprintf(
        __("<b>Dashboard -> Credits used</b>: In this section you will find an overview of the available credits and the number of credits that have been consumed so far. Right below you will see how many content-entries you will be able to optimize with the remaining credits. Each standard generation consumes %u credits per page or media file. The %u credits of the free plan therefore cover a total of %u entries.", "ai-for-seo"),
        esc_html(AI4SEO_CREDITS_FLAT_COST),
        esc_html($ai4seo_free_plan_credits),
        esc_html(ai4seo_get_num_credits_amount_based_generations($ai4seo_free_plan_credits)),
    ) . "</p>";
    $ai4seo_this_accordion_content .= "<p><b>3.</b> " . __("<b>Dashboard -> Current plan</b>: In this section you will see the plan you are currently using. Next to the plan you will either see an upgrade button which you can use to purchase more credits or you will see a button to manage your existing plan.", "ai-for-seo") . "</p>";
    $ai4seo_this_accordion_content .= "<img src='" . esc_url(ai4seo_get_assets_images_url("help-screenshots/first-steps-1.jpg")) . "' style='width: 100%;' />";
    $ai4seo_this_accordion_content .= "<p><b>4.</b> " . __("<b>Settings</b>: Checkout the plugins default-settings listed in the \"Settings\" tab. Make sure that those settings meet your needs and adjust them accordingly if necessary.", "ai-for-seo") . "</p>";
    $ai4seo_this_accordion_content .= "<p><b>5.</b> " . __("<b>Content tabs</b>: On the plugin-page you will find tabs for each supported content-type (i.e. Page, Post, etc.). Click on any content tab and open the metadata editor by clicking on the button on the right-hand side of an entry.", "ai-for-seo") . "</p>";
    $ai4seo_this_accordion_content .= "<img src='" . esc_url(ai4seo_get_assets_images_url("help-screenshots/first-steps-2.jpg")) . "' style='width: 100%;' />";
    $ai4seo_this_accordion_content .= "<p><b>6.</b> " . sprintf(
        __("<b>Metadata editor</b>: After clicking on the button to open the metadata editor you will find the \"Generate all SEO\" button at the top of the editor. Click this button to generate all metadata for the selected content-entry for only %u credits. Additionally you will find the \"Generate with AI\"-button underneath every input-field which allows you to generate the metadata for a single metadata-input-field. After the new data has been generated you can either save the changes as generated or you can edit them if you would like to apply any changes and save afterwards.", "ai-for-seo"),
        esc_html(AI4SEO_CREDITS_FLAT_COST),
    ) . "</p>";
    $ai4seo_this_accordion_content .= "<img src='" . esc_url(ai4seo_get_assets_images_url("help-screenshots/first-steps-3.jpg")) . "' style='width: 100%;' />";
    $ai4seo_this_accordion_content .= "<p><b>7.</b> " . __("<b>Media tab</b>: After clicking on the media-tab you can open the media attribute editor by clicking on the button on the right-hand side of each media-entry.", "ai-for-seo") . "</p>";
    $ai4seo_this_accordion_content .= "<p><b>8.</b> " . sprintf(
        __("<b>Media attribute editor</b>: After clicking on the button to open the media attribute editor you will find the \"Generate all SEO\" button at the top of the editor. Click this button to generate all media attributes for the selected media-entry for only %u credits. Additionally you will find the \"Generate with AI\"-button underneath every input-field which allows you to generate the media attributes for a single attributes-input-field. After the new data has been generated you can either save the changes as generated or you can edit them if you would like to apply any changes and save afterwards.", "ai-for-seo"),
        esc_html(AI4SEO_CREDITS_FLAT_COST),
    ) . "</p>";
    $ai4seo_this_accordion_content .= "<img src='" . esc_url(ai4seo_get_assets_images_url("help-screenshots/first-steps-4.jpg")) . "' style='width: 100%;' />";
    $ai4seo_this_accordion_content .= "<p><b>9.</b> " . __("<b>Automatic generation</b>: You can activate the automatic generation of metadata or media attributes by activating the checkbox \"Automatically generate all metadata/media attributes\" which you will find at the top of the page to each tab (Pages, Posts, Media, etc.). After activating this checkbox you can check the progress on the dashboard-statistics within a few minutes time.", "ai-for-seo") . "</p>";
    $ai4seo_this_accordion_content .= "<p><b>10.</b> " . __("<b>Contact us</b>: If you have any questions, suggestions, need further support or require a particularly large number of credits, feel free to contact us via Help > Contact.", "ai-for-seo") . "</p>";

    echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("First steps", "ai-for-seo"), $ai4seo_this_accordion_content));

    // === How to edit specific page or post ===================================================== \\

    // First step: Navigate to the page you want to edit
    $ai4seo_this_accordion_content = "<p><b>1.</b> " . esc_html__("Navigate to the page or post you want to edit. You can do this through the normal editor, using a page builder like Elementor, or by opening the page directly in the frontend.", "ai-for-seo") . "</p>";

    // Second step: Open the "AI for SEO" tool
    $ai4seo_this_accordion_content .= "<p><b>2.</b> " . esc_html__("Click on the \"AI for SEO\" button located in the top admin-bar. This will open up the Metadata Editor.", "ai-for-seo") . "</p>";
    $ai4seo_this_accordion_content .= "<img src='" . esc_url(ai4seo_get_assets_images_url("faq-screenshots/screenshot-page-post-1.jpg")) . "' style='width: 100%;' />";

    // Third step: Modify or generate SEO content
    $ai4seo_this_accordion_content .= "<p><b>3.</b> " . esc_html__("Edit existing SEO content or generate new content using our \"Generate with AI\" buttons.", "ai-for-seo") . "</p>";
    $ai4seo_this_accordion_content .= "<img src='" . esc_url(ai4seo_get_assets_images_url("faq-screenshots/screenshot-page-post-2.jpg")) . "' style='width: 100%;' />";

    // Additional information: Alternate way to access the "SEO Metadata Editor"
    $ai4seo_this_accordion_content .= "<p>" . __("<b>Alternatively,</b> you can go to the \"Pages\" or \"Posts\" tab within the \"AI for SEO\" plugin. From there, you can browse through your pages and posts, and choose the ones you want to edit.", "ai-for-seo") . "</p>";
    $ai4seo_this_accordion_content .= "<img src='" . esc_url(ai4seo_get_assets_images_url("faq-screenshots/screenshot-page-post-3.jpg")) . "' style='width: 100%;' />";

    echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("How to generate or edit SEO-relevant metadata for a specific page or post", "ai-for-seo"), $ai4seo_this_accordion_content));


    // === Hot to edit specific media-entry ====================================================== \\

    // First step: Select the Relevant Section
    $ai4seo_this_accordion_content = "<p><b>1.</b> " . esc_html__("Click on the \"Media\"-link in the main admin-menu of your WordPress-backend", "ai-for-seo") . "</p>";

    // Second step: Activate the Automation Feature
    $ai4seo_this_accordion_content .= "<p><b>2.1</b> " . esc_html__("If your media-page is using the grid view, click on the specific image file for which you would like to add/edit the attributes for.", "ai-for-seo") . "<br />";
    $ai4seo_this_accordion_content .= esc_html__("Once the media-modal is opened you will see the \"AI for SEO\"-generate-buttons on the right side above and within the form. Then click on the button to generate the content for each attribute.", "ai-for-seo") . "</p>";

    $ai4seo_this_accordion_content .= "<p><b>2.2</b> " . esc_html__("If your media-page is using the table view, click on the edit-button which will appear once you hover over the entry of the specific image file for which you would like to add/edit the attributes for.", "ai-for-seo") . "<br />";
    $ai4seo_this_accordion_content .= esc_html__("Once the edit-media-page is opened you will see the \"AI for SEO\"-generate-buttons within the form of the page. Then click on the button to generate the content for each attribute.", "ai-for-seo") . "</p>";

    $ai4seo_this_accordion_content .= "<p>" . __("<b>Alternatively,</b> you can go to the \"Media\" tab within the \"AI for SEO\" plugin. From there, you can browse through your media-entries, and choose the ones you want to edit.", "ai-for-seo") . "</p>";

    echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("How to add alt-text, captions, titles and descriptions for media files", "ai-for-seo"), $ai4seo_this_accordion_content));


    // === Automate filling of missing metadata ======================================================== \\

    // First step: Select the Relevant Section
    $ai4seo_this_accordion_content = "<p><b>1.</b> " . esc_html__("Click on either the \"Pages\", \"Posts\", \"Products\" or \"Media\" tab within the \"AI for SEO\" plugin, depending on where you want to apply the SEO automation. This selection allows you to narrow down the automation to specific parts of your site.", "ai-for-seo") . "</p>";
    $ai4seo_this_accordion_content .= "<img src='" . esc_url(ai4seo_get_assets_images_url("faq-screenshots/screenshot-automate-1.jpg")) . "' style='width: 100%;' />";

    // Second step: Activate the Automation Feature
    $ai4seo_this_accordion_content .= "<p><b>2.</b> " . esc_html__("Click on the option \"Fill missing metadata automatically (also when creating new pages).\" This will enable a feature that intelligently fills in missing SEO metadata, both for existing content and new pages/posts/products/media as they're created.", "ai-for-seo") . "</p>";
    $ai4seo_this_accordion_content .= "<img src='" . esc_url(ai4seo_get_assets_images_url("faq-screenshots/screenshot-automate-2.jpg")) . "' style='width: 100%;' />";

    echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("How to fill missing metadata automatically", "ai-for-seo"), $ai4seo_this_accordion_content));


    // === Credits ======================================================================== \\

    // Explanation of how credits are consumed
    $ai4seo_this_accordion_content = "<p>" . sprintf(
        __("Credits are consumed when the AI generates metadata for your content entries (posts, pages, products, etc.). Each generation costs %u credits, regardless of the word count of the content.", "ai-for-seo"),
        esc_html(AI4SEO_CREDITS_FLAT_COST),
    ) . "</p>";
    $ai4seo_this_accordion_content .= "<p>" . esc_html__("Please note that credits are not consumed when you manually edit the metadata. Also, credits-wise, it does not make a difference if you only generate metadata for a single field (e.g., the title) or for all fields at once.", "ai-for-seo") . "</p>";
    $ai4seo_this_accordion_content .= "<p>" . esc_html__("On the other hand, if you do not make any changes to the content, you can generate the metadata again without consuming additional credits. However, if you make changes to the content, you will need to generate the metadata again, which will consume additional credits.", "ai-for-seo") . "</p>";

    // Details on credits available with different plans
    $ai4seo_this_accordion_content .= "<p>" . sprintf(
        __("The <b>free plan</b> provides you with %u credits, allowing you to experiment with AI-generated SEO content without any cost. In addition, we provide you with %u free credits every day.", "ai-for-seo"),
        esc_html($ai4seo_free_plan_credits),
        esc_html(AI4SEO_DAILY_FREE_CREDITS_AMOUNT),
    ) . "</p>";
    $ai4seo_this_accordion_content .= "<p>" . sprintf(
        __("With the <b>basic plan</b> you receive %u credits per month: Ideal for smaller websites or blogs.", "ai-for-seo"),
        esc_html($ai4seo_s_plan_credits)
    ) . "</p>";
    $ai4seo_this_accordion_content .= "<p>" . sprintf(
        __("With the <b>pro plan</b> you receive %u credits per month: Ideal for professionals who need more extensive SEO support.", "ai-for-seo"),
        esc_html($ai4seo_m_plan_credits)
    ) . "</p>";
    $ai4seo_this_accordion_content .= "<p>" . sprintf(
        __("With the <b>premium plan</b> you receive %u credits per month: Ideal for businesses that require substantial SEO support and features.", "ai-for-seo"),
        esc_html($ai4seo_l_plan_credits)
    ) . "</p>";

    echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("How do credits work?", "ai-for-seo"), $ai4seo_this_accordion_content));


    // === "Yoast SEO" elements ======================================================================== \\

    if (ai4seo_is_plugin_or_theme_active(AI4SEO_THIRD_PARTY_PLUGIN_YOAST_SEO)) {
        // First step: Open the Page or Post
        $ai4seo_this_accordion_content = "<p><b>1.</b> " . esc_html__("Begin by navigating to the page or blog post you want to edit.", "ai-for-seo") . "</p>";

        // Second step: Initiate AI Generation
        $ai4seo_this_accordion_content .= "<p><b>2.</b> " . esc_html__("Click on the \"Generate with AI\" button, which you'll find within the SEO-form.", "ai-for-seo") . "</p>";
        $ai4seo_this_accordion_content .= "<img src='" . esc_url(ai4seo_get_assets_images_url("faq-screenshots/screenshot-yoast-1.jpg")) . "' style='width: 100%;' />";

        // Third step: Review AI-Generated Description
        $ai4seo_this_accordion_content .= "<p><b>3.</b> " . esc_html__("\"AI for SEO\" will generate a SEO-relevant description based on the content of the selected page or blog post. Review this description to ensure it aligns with your content and objectives.", "ai-for-seo") . "</p>";
        $ai4seo_this_accordion_content .= "<img src='" . esc_url(ai4seo_get_assets_images_url("faq-screenshots/screenshot-yoast-2.jpg")) . "' style='width: 100%;' />";

        // Fourth step: Apply AI-Generated SEO to All Fields
        $ai4seo_this_accordion_content .= "<p><b>4.</b> " . esc_html__("To streamline the process, click the \"AI Generate all SEO\" button. This will apply the AI-generated descriptions to all corresponding input fields, enhancing the SEO across the entire page or post.", "ai-for-seo") . "</p>";
        $ai4seo_this_accordion_content .= "<img src='" . esc_url(ai4seo_get_assets_images_url("faq-screenshots/screenshot-yoast-3.jpg")) . "' style='width: 100%;' />";

        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Yoast integration", "ai-for-seo"), $ai4seo_this_accordion_content));
    }

    // === Elementor-elements ==================================================================== \\

    if (ai4seo_is_plugin_or_theme_active(AI4SEO_THIRD_PARTY_PLUGIN_ELEMENTOR)) {
        // First step: Open the Elementor Editor
        $ai4seo_this_accordion_content = "<p><b>1.</b> " . esc_html__("Open the page or post you want to edit in Elementor.", "ai-for-seo") . "</p>";

        // Second step: Access the Elementor Sidebar
        $ai4seo_this_accordion_content .= "<p><b>2.</b> " . esc_html__("Click on the bars-button located at the top-left of the Elementor sidebar. This will reveal additional options and settings.", "ai-for-seo") . "</p>";

        // Third step: Open "AI for SEO" layer
        $ai4seo_this_accordion_content .= "<p><b>3.</b> " . esc_html__("In the settings section, click on the \"Show all SEO settings\" button to open the \"AI for SEO\" metadata editor. Here, you can adjust the metadata using our AI-driven algorithms.", "ai-for-seo") . "</p>";
        $ai4seo_this_accordion_content .= "<img src='" . esc_url(ai4seo_get_assets_images_url("faq-screenshots/screenshot-elementor-1.jpg")) . "' style='width: 100%;' />";

        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Elementor integration", "ai-for-seo"), $ai4seo_this_accordion_content));
    }

    // === Be-Builder-elements =================================================================== \\

    if (ai4seo_is_plugin_or_theme_active(AI4SEO_THIRD_PARTY_PLUGIN_BETHEME)) {
        // First step: Open the BeBuilder Editor
        $ai4seo_this_accordion_content = "<p><b>1.</b> " . esc_html__("Open the page or post you want to edit in BeBuilder, the page-building tool within the BeTheme framework.", "ai-for-seo") . "</p>";

        // Second step: Access SEO Section in BeBuilder
        $ai4seo_this_accordion_content .= "<p><b>2.</b> " . esc_html__("Click on the page-options-button on the left side of the BeBuilder navigation, then scroll down to the SEO section. This will display all SEO-related settings for the current page.", "ai-for-seo") . "</p>";

        // Third step: Open "AI for SEO" metadata editor
        $ai4seo_this_accordion_content .= "<p><b>3.</b> " . esc_html__("Click on the \"Show all SEO settings\" button within the SEO section to open the \"AI for SEO\" metadata editor. Here, you can access and manipulate metadata using our AI-driven algorithms.", "ai-for-seo") . "</p>";
        $ai4seo_this_accordion_content .= "<img src='" . esc_url(ai4seo_get_assets_images_url("faq-screenshots/screenshot-be-builder-1.jpg")) . "' style='width: 100%;' />";

        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Be-Builder integration", "ai-for-seo"), $ai4seo_this_accordion_content));
    }
echo "</div>";


// === FAQ =================================================================================== \\

echo "<div class='ai4seo-display-none ai4seo-help-content' id='ai4seo-help-faq'>";
    // Headline
    echo "<h1 id='ai4seo-faq-section'>";
        echo esc_html__("F.A.Q.", "ai-for-seo");
    echo "</h1>";


    // === SEARCH ================================================================================ \\

    // Input for the search
    echo "<div class='ai4seo-help-search-wrapper'>";
        echo ai4seo_wp_kses(ai4seo_get_svg_tag("magnifying-glass"));
        echo "<input type='text' class='ai4seo-help-search' id='ai4seo-help-search' placeholder='" . esc_attr__("Search F.A.Q. (enter min.3 characters)", "ai-for-seo") . "' />";
    echo "</div>";

    // Container with the message that no entries could be found based on the search-input
    echo "<div class='ai4seo-help-search-notice ai4seo-display-none' id='ai4seo-help-faq-search-notice'>";
        echo "<p>" . esc_html__("No results could be found based on your search. Please try a different search term.", "ai-for-seo") . "</p>";
    echo "</div>";

    echo "<div class='ai4seo-gap'></div>";

    // === GENERAL =============================================================================== \\

    echo "<div class='ai4seo-faq-section-holder'>";
        // Headline
        echo "<h3>" . esc_html__("General", "ai-for-seo") . "</h3>";

        $ai4seo_this_accordion_content = __("Providing SEO metadata helps search engines better understand your content, which can lead to higher rankings in search results. This improvement in visibility can drive more clicks, visitors, sales, and leads to your website.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("What are the benefits of providing SEO metadata for my website?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("Including image metadata helps search engines better interpret your images, potentially boosting your rankings in image search results and the overall quality of your website. In addition, it also shows your commitment to accessibility, catering to a wider audience and complying with accessibility standards.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("What are the benefits of providing alt text, titles, captions, and descriptions for images on my website?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("Currently, Google's stance on the use of AI or automation in content creation is generally permissive, as indicated in a Google Developers blog post from February 2023. They state that appropriate use of AI or automation is not against their guidelines. More information can be found at <a target='_blank' href='https://developers.google.com/search/blog/2023/02/google-search-and-ai-content'>https://developers.google.com/search/blog/2023/02/google-search-and-ai-content.</a>", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Is AI-generated SEO-content harmful to my SEO-ranking?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("No, the plugin is only active in the backend of your website. The plugin does not affect the frontend of your website.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Will 'AI for SEO' slow down my website?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("No, the plugin does not support Multi-Site installations. If you would like to use the plugin on a Multi-Site installation, you will need to purchase credits for each site individually.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Does 'AI for SEO' support Multi-Site installations?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = sprintf(
            __("We are always open to suggestions and feedback. Please email us at %s with your suggestions.", "ai-for-seo"),
            esc_html(AI4SEO_SUPPORT_EMAIL)
        );
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Can you add feature x, y or z to the plugin?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("Yes, the AI tool will generate SEO-relevant data based on the content of the selected page, blog post or media file.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Will the AI tool automatically analyze my content?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("We currently support Elementor, BeTheme (Muffin-Builder / Be-Builder) and the standard editor. We are working on supporting more editors.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("What editors are supported?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("We are currently working on fixing this issue. As a workaround, we recommend highlighting the text you want to delete and starting with regular letter inputs (without using the delete key).", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("I can't remove the AI-generated text within the Yoast SEO plugin", "ai-for-seo"), $ai4seo_this_accordion_content));
    echo "</div>";

    echo "<div class='ai4seo-gap'></div>";


    // === AUTOMATION ============================================================================ \\

    echo "<div class='ai4seo-faq-section-holder'>";
        // Headline
        echo "<h3>" . esc_html__("Automation", "ai-for-seo") . "</h3>";

        $ai4seo_this_accordion_content = __("You can automate the generation of missing SEO data by clicking on any content tab ('Pages,' 'Posts,' 'Products,' etc.) within the 'AI for SEO' plugin and then activating the 'Fill missing metadata automatically' option.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("How can I automate the process of filling in missing SEO data for my website?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("You can automate missing image metadata by clicking on the 'Media' tab within the 'AI for SEO' plugin and then activating the 'Fill missing media attributes automatically' option.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("How can I automate the process of filling in missing alt text, titles, captions, and descriptions for images on my website?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("Yes, just deactivate the 'Fill missing metadata automatically' option to stop generating new SEO metadata automatically. The same applies to the 'Fill missing media attributes automatically' option for image metadata.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Can I turn off the automation feature after I've activated it?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("Yes, the 'Fill missing metadata automatically' option will intelligently fill in missing SEO metadata, both for existing content and new entries as they're created.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Does this automation feature work for new content?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("The automation feature does not affect metadata that is already been edited or created by the user, so it does not overwrite existing data.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Will this automation affect the SEO data that I have already manually edited?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("In the settings, we provide an option to include or exclude specific meta tags from the output in the header by our plugin. This allows you to customize the automation process to suit your needs.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Is there any way to control what metadata is automatically filled?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("You can find the 'Retry all failed' button in any content tab. This allows you to retry all failed metadata generations with a single click, saving you time and effort.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("How can I retry all failed metadata generations with just one click?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("We use WordPress's internal task scheduler to manage automatic generation efficiently. This helps prevent overloading your server with too many simultaneous tasks.", "ai-for-seo") . "<br /><br />";
        $ai4seo_this_accordion_content .= __("If you see a 'Pending' status in the 'SEO coverage' column, it means one of two things:", "ai-for-seo") . "<br /><br />";
        $ai4seo_this_accordion_content .= __("1. The plugin is waiting for the next scheduled task to run (typically within 1-5 minutes).", "ai-for-seo") . "<br />";
        $ai4seo_this_accordion_content .= __("2. The plugin is currently generating data for other entries.", "ai-for-seo") . "<br /><br />";
        $ai4seo_this_accordion_content .= __("You can check the 'AI for SEO' dashboard to see if any generation is in progress for other items. If many items are pending, it may take longer to process them all.", "ai-for-seo") . "<br /><br />";
        $ai4seo_this_accordion_content .= __("Rest assured, the plugin will automatically generate data for all pending items over time. If you need immediate results, you can use the manual generation option for specific items.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Why is the automatic metadata or media attributes generation pending?", "ai-for-seo"), $ai4seo_this_accordion_content));
    echo "</div>";

    echo "<div class='ai4seo-gap'></div>";


    // === NAVIGATION ============================================================================ \\

    echo "<div class='ai4seo-faq-section-holder'>";
        // Headline
        echo "<h3>" . esc_html__("Navigation", "ai-for-seo") . "</h3>";

        $ai4seo_this_accordion_content = __("You can navigate to the page or post you want to edit through the normal editor, using a page builder like Elementor, or by opening the page directly in the frontend.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("How do I navigate to the page I want to edit for SEO?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("You can find the 'AI for SEO' button in the top admin-bar. Clicking on this will open up the metadata editor where you can modify or generate metadata.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Where is the 'AI for SEO' metadata editor located?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("After opening the 'AI for SEO' metadata editor, you can either edit the existing metadata manually or generate new metadata using the 'Generate with AI' buttons.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("How can I generate or edit metadata?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("Yes, alternatively, you can go to the 'Pages' or 'Posts' tab within the 'AI for SEO' plugin. From there, you can browse through your pages and posts, and choose the ones you want to edit.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Is there an alternate way to access the SEO Metadata Editor?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("1. Open the page or post you want to edit in Elementor.", "ai-for-seo") . "<br />";
        $ai4seo_this_accordion_content .= __("2. Click on the bars-button located at the top-left of the Elementor sidebar to reveal additional options and settings.", "ai-for-seo") . "<br />";
        $ai4seo_this_accordion_content .= __("3. In the settings section, click on the 'Show all SEO settings' button to open the 'AI for SEO' metadata editor. Here, you can adjust the metadata manually or generate new metadata using AI-driven algorithms.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("How do I edit metadata in Elementor with 'AI for SEO'?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("1. Open the page or post you want to edit in BeBuilder, the page-building tool within the BeTheme framework.", "ai-for-seo") . "<br />";
        $ai4seo_this_accordion_content .= __("2. Click on the page-options-button on the left side of the BeBuilder navigation, then scroll down to the SEO section.", "ai-for-seo") . "<br />";
        $ai4seo_this_accordion_content .= __("3. Click on the 'Show all SEO settings' button within the SEO section to open the 'AI for SEO' metadata editor. Here, you can access and manipulate the metadata using AI-driven algorithms.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("How do I edit SEO settings in BeBuilder with 'AI for SEO'?", "ai-for-seo"), $ai4seo_this_accordion_content));
    echo "</div>";

    echo "<div class='ai4seo-gap'></div>";


    // === PLANS / SUBSCRIPTIONS ================================================================= \\

    echo "<div class='ai4seo-faq-section-holder'>";
        // Headline
        echo "<h3>" . esc_html__("Plans / Subscriptions", "ai-for-seo") . "</h3>";

        $ai4seo_this_accordion_content = sprintf(
            __("The free plan renews on a daily basis. You will receive %u free credits every day, allowing you to continue using the basic features of the plugin at no cost.", "ai-for-seo"),
            esc_html(AI4SEO_DAILY_FREE_CREDITS_AMOUNT)
        );
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Are credits renewed in the Free plan?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("You don't need to do anything—they'll be automatically added to your account every day. Just make sure the plugin remains active on your website.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("What do I need to do to receive the free daily credits?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("Yes, you'll continue to receive the free daily credits even if you're on a paid plan. These credits are in addition to those included in your plan.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Will I still receive the free daily credits if I'm on a paid plan?", "ai-for-seo"), $ai4seo_this_accordion_content));


        $ai4seo_this_accordion_content = sprintf(
            __("Credits are consumed when the AI generates metadata or media attributes for your content entries. Each generation costs %u credits, regardless of the content's word count or image size.", "ai-for-seo") . "<br />",
            esc_html(AI4SEO_CREDITS_FLAT_COST),
        );
        $ai4seo_this_accordion_content .= __("Please note that credits are not consumed when you manually edit the metadata or media attributes. Also, credits-wise, it does not make a difference if you only generate metadata for a single field (e.g., the title) or for all fields at once.", "ai-for-seo") . "<br />";
        $ai4seo_this_accordion_content .= __("On the other hand, if you do not make any changes to the content, you can generate the metadata again without consuming additional credits. However, if you make changes to the content, you will need to generate the metadata again, which will consume additional credits.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("How are credits consumed in the 'AI for SEO' plugin?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = sprintf(
            __("Yes, the free plan provides you with %u credits, allowing you to experiment with AI-generated SEO content without any cost. In addition, we provide you with %u free credits every day.", "ai-for-seo"),
            esc_html($ai4seo_free_plan_credits),
            esc_html(AI4SEO_DAILY_FREE_CREDITS_AMOUNT),
        );
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Can I try the 'AI for SEO' plugin without purchasing a plan?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = sprintf(
            "- " . __("The basic plan grants you %u credits per month, suitable for smaller websites or blogs. It covers up to ~%u content posts/pages/products/images/etc. per month.", "ai-for-seo") . "<br />",
            esc_html($ai4seo_s_plan_credits),
            esc_html(ai4seo_get_num_credits_amount_based_generations($ai4seo_s_plan_credits)),
        );
        $ai4seo_this_accordion_content .= sprintf(
            "- " . __("The pro plan grants you %u credits per month, designed for professionals who need more extensive SEO capabilities. It covers up to ~%u posts/pages/products/images/etc. per month.", "ai-for-seo") . "<br />",
            esc_html($ai4seo_m_plan_credits),
            esc_html(ai4seo_get_num_credits_amount_based_generations($ai4seo_m_plan_credits)),
        );
        $ai4seo_this_accordion_content .= sprintf(
            "- " . __("The premium plan grants you %u credits per month, ideal for businesses that require substantial SEO support and features. It covers up to ~%u posts/pages/products/images/etc. per month.", "ai-for-seo"),
            esc_html($ai4seo_l_plan_credits),
            esc_html(ai4seo_get_num_credits_amount_based_generations($ai4seo_l_plan_credits)),
        );
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("How many credits do I get with the basic, pro, and premium plans?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("You should choose a plan based on the number of credits you need for generating SEO content:", "ai-for-seo") . "<br />";
        $ai4seo_this_accordion_content .= sprintf(
            "- " . __("The free plan is great for experimentation. It covers up to ~%u posts/pages/products/images/etc.", "ai-for-seo") . "<br />",
            esc_html(ai4seo_get_num_credits_amount_based_generations($ai4seo_free_plan_credits)),
        );
        $ai4seo_this_accordion_content .= sprintf(
            "- " . __("The basic plan is suitable for smaller websites or blogs. It covers up to ~%u posts/pages/products/images/etc. per month.", "ai-for-seo") . "<br />",
            esc_html(ai4seo_get_num_credits_amount_based_generations($ai4seo_s_plan_credits)),
        );
        $ai4seo_this_accordion_content .= sprintf(
            "- " . __("The pro plan is designed for professionals who need more extensive SEO capabilities. It covers up to ~%u posts/pages/products/images/etc. per month.", "ai-for-seo") . "<br />",
            esc_html(ai4seo_get_num_credits_amount_based_generations($ai4seo_m_plan_credits)),
        );
        $ai4seo_this_accordion_content .= sprintf(
            "- " . __("The premium plan is ideal for businesses that require substantial SEO support and features. It covers up to ~%u posts/pages/products/images/etc. per month.", "ai-for-seo") . "<br />",
            esc_html(ai4seo_get_num_credits_amount_based_generations($ai4seo_l_plan_credits)),
        );
        $ai4seo_this_accordion_content .= "- " . __("If you have a shop with many products or a large blog, please contact us to discuss a custom plan tailored to your needs at " . esc_html(AI4SEO_SUPPORT_EMAIL) . ". We are currently offering a 30% discount on all custom plans!", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("How do I choose the right plan for my needs?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = sprintf(
            __("You can change or cancel your subscription at any time by going to the 'AI for SEO' dashboard page and clicking on the 'Manage Plan' button, or by following this link: %s.", "ai-for-seo") . "<br />",
            "<a target='_blank' href='" . esc_html(AI4SEO_OFFICIAL_WEBPAGE) . "/cancel-plan'>" . esc_html(AI4SEO_OFFICIAL_WEBPAGE) . "/cancel-plan</a>",
        );
        $ai4seo_this_accordion_content .= __("You'll be redirected to Stripe, our invoice partner. Please follow the instructions on the Stripe website to change or cancel your subscription.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("How do I change or cancel my subscription?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = sprintf(
            __("Yes, you can upgrade or downgrade your plan at any time by going to the 'AI for SEO' dashboard page and clicking on the 'Manage Plan' button, or by following this link: %s.", "ai-for-seo") . "<br />",
            "<a target='_blank' href='" . esc_html(AI4SEO_STRIPE_BILLING_URL) . "'>" . esc_html(AI4SEO_STRIPE_BILLING_URL) . "</a>",
        );
        $ai4seo_this_accordion_content .= __("You'll be redirected to Stripe, our invoice partner. Please follow the instructions on the Stripe website to change your plan.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Can I upgrade or downgrade my plan?", "ai-for-seo"), $ai4seo_this_accordion_content));

        $ai4seo_this_accordion_content = __("No, you can carry over any unused credits to the next month, allowing you to make the most of your credits beyond the current month.", "ai-for-seo");
        echo ai4seo_wp_kses(ai4seo_get_accordion_element("> " . esc_html__("Do leftover credits expire after a month?", "ai-for-seo"), $ai4seo_this_accordion_content));
    echo "</div>";
echo "</div>";


// === CONTACT FORM ========================================================================== \\

echo "<div class='ai4seo-display-none ai4seo-help-content' id='ai4seo-help-contact'>";
    // Headline
    echo "<h1 id='ai4seo-contact-section'>";
        echo esc_html__("Contact the makers of this plugin", "ai-for-seo");
    echo "</h1>";

    // Description
    echo "<p>";
        echo esc_html__("You can contact us (Space Codes) directly through this page, and we will respond to the email address you provide.", "ai-for-seo");
    echo "</p>";

    // Form
    echo "<form method='post' class='ai4seo-form ai4seo-contact-form' id='ai4seo-contact-form' name='ai4seo-contact-form'>";

        // Nonce
        wp_nonce_field('ai4seo-contact-form', 'ai4seo-contact-form-nonce');

        // Name
        echo "<div class='ai4seo-form-item'>";
            echo "<label for='ai4seo-contact-form-name'>" . esc_html__("Your name", "ai-for-seo") . ":</label>";
            echo "<div class='ai4seo-form-item-input-wrapper'>";
                echo "<input type='text' class='ai4seo-editor-textfield' id='ai4seo-contact-form-name' name='ai4seo-contact-form-name' placeholder='" . esc_attr__("Your name", "ai-for-seo") . "' value='' required />";
            echo "</div>";
        echo "</div>";

        // get default email address
        $ai4seo_default_email = sanitize_email(get_option('admin_email'));

        // Email
        echo "<div class='ai4seo-form-item'>";
            echo "<label for='ai4seo-contact-form-email'>" . esc_html__("Email", "ai-for-seo") . ":</label>";
            echo "<div class='ai4seo-form-item-input-wrapper'>";
                echo "<input type='email' class='ai4seo-editor-textfield' id='ai4seo-contact-form-email' name='ai4seo-contact-form-email' placeholder='" . esc_attr("example@page.com") . "' value='" . esc_attr($ai4seo_default_email) . "' required />";
            echo "</div>";
        echo "</div>";

        // Subject
        echo "<div class='ai4seo-form-item'>";
            echo "<label for='ai4seo-contact-form-subject'>" . esc_html__("Subject", "ai-for-seo") . ":</label>";
            echo "<div class='ai4seo-form-item-input-wrapper'>";
                echo "<select class='ai4seo-editor-select' name='ai4seo-contact-form-subject' id='ai4seo-contact-form-subject' required>";
                    foreach ($ai4seo_contact_subject_options as $ai4seo_this_option_key => $ai4seo_this_option_value) {
                        echo "<option value='" . esc_attr($ai4seo_this_option_key) . "'>" . esc_attr($ai4seo_this_option_value) . "</option>";
                    }
                echo "</select>";
            echo "</div>";
        echo "</div>";

        // Message
        echo "<div class='ai4seo-form-item'>";
            echo "<label for='ai4seo-contact-form-message'>" . esc_html__("Message", "ai-for-seo") . ":</label>";
            echo "<div class='ai4seo-form-item-input-wrapper'>";
                echo "<textarea class='ai4seo-editor-textarea' id='ai4seo-contact-form-message' name='ai4seo-contact-form-message' required></textarea>";
            echo "</div>";
        echo "</div>";

        // Submit button
        submit_button(esc_attr__("Send us an email"), "primary", "ai4seo-contact-form-submit");
    echo "</form>";
echo "</div>";


// === USEFUL LINKS ========================================================================== \\

echo "<div class='ai4seo-display-none ai4seo-help-content' id='ai4seo-help-links'>";
    // Headline
    echo "<h1 id='ai4seo-links-section'>";
        echo esc_html__("Useful links", "ai-for-seo");
    echo "</h1>";

    // Plugin website
    echo "<p>";
        echo "<i class='dashicons dashicons-admin-links'></i> ";
        echo "<b>" . esc_html__("Plugin's website", "ai-for-seo") . "</b><br />";
        echo "<a href='" . esc_attr(AI4SEO_OFFICIAL_WEBPAGE) . "' target='_blank'>" . esc_html__("Check out our website to learn more about our plugin!", "ai-for-seo") . "</a>";
    echo "</p>";

    // WordPress plugin-page
    echo "<p>";
        echo "<i class='dashicons dashicons-admin-links'></i> ";
        echo "<b>" . esc_html__("WordPress plugin-page", "ai-for-seo") . "</b><br />";
        echo "<a href='https://wordpress.org/plugins/ai-for-seo/' target='_blank'>" . esc_html__("Check out our plugin directly on WordPress!", "ai-for-seo") . "</a>";
    echo "</p>";

    // WordPress.org Support forum
    echo "<p>";
        echo "<i class='dashicons dashicons-admin-links'></i> ";
        echo "<b>" . esc_html__("Plugin support-forum", "ai-for-seo") . "</b><br />";
        echo "<a href='https://wordpress.org/support/plugin/ai-for-seo/' target='_blank'>" . esc_html__("Do you need assistance? Check out our support-forum!", "ai-for-seo") . "</a>";
    echo "</p>";

    // Space Codes website
    echo "<p>";
        echo "<i class='dashicons dashicons-admin-links'></i> ";
        echo "<b>" . esc_html__("The makers of AI for SEO", "ai-for-seo") . "</b><br />";
        echo "<a href='https://spa.ce.codes' target='_blank'>" . esc_html__("Do you want to learn more about the makers of \"AI for SEO\"? Then this is the right place for you!", "ai-for-seo") . "</a>";
    echo "</p>";
echo "</div>";