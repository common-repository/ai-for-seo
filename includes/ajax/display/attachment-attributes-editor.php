<?php
/**
 * Displays the metadata editor. Called via AJAX.
 *
 * @since 1.0
 */

if (!defined("ABSPATH")) {
    exit;
}

// ___________________________________________________________________________________________ \\
// === PREPARE =============================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

global $ai4seo_attachments_attributes_details;
global $ai4seo_allowed_image_mime_types;

$ai4seo_plugins_official_name = ai4seo_get_plugins_official_name();


// === CHECK PARAMETER ============================================== \\

// Make sure that input-fields exist
if (!isset($ai4seo_attachments_attributes_details) || !$ai4seo_attachments_attributes_details) {
    ai4seo_return_error_as_json("An error occurred! Please check your settings or contact the plugin developer.", 221920824);
}

// Get sanitized post id parameter
$ai4seo_this_attachment_post_id = absint($_REQUEST["post_id"] ?? 0);

// validate post id
if ($ai4seo_this_attachment_post_id <= 0) {
    ai4seo_return_error_as_json("Post id is invalid.", 291920824);
}


// === GET ADDITIONAL DETAILS ===================================================================== \\

$ai4seo_this_post_attachment_attributes = ai4seo_read_attachment_attributes($ai4seo_this_attachment_post_id);
$ai4seo_all_buttons_on_event = "ai4seo_enable_modal_submit_button();";

// Check if we have an image, by using $ai4seo_allowed_image_mime_types
$ai4seo_this_attachment_mime_type = get_post_mime_type($ai4seo_this_attachment_post_id);

$ai4seo_this_attachment_is_an_image = false;

foreach ($ai4seo_allowed_image_mime_types as $ai4seo_this_allowed_image_mime_type) {
    if (strpos($ai4seo_this_attachment_mime_type, $ai4seo_this_allowed_image_mime_type) !== false) {
        $ai4seo_this_attachment_is_an_image = true;
        break;
    }
}

$ai4seo_this_attachment_url = wp_get_attachment_url($ai4seo_this_attachment_post_id);


// ___________________________________________________________________________________________ \\
// === OUTPUT ================================================================================ \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

echo "<h1 class='ai4seo-headline'>";
echo "<img src='" . esc_url(ai4seo_get_ai_for_seo_logo_url("64x64")) . "'>";
echo esc_html($ai4seo_plugins_official_name) . " - " . esc_html__("Media Attributes Editor", "ai-for-seo");
echo "</h1>";

echo "<h2>" . ai4seo_wp_kses(sprintf(__("Manage media attributes for <b>%s</b> (#%d)", "ai-for-seo"), $ai4seo_this_post_attachment_attributes["title"], $ai4seo_this_attachment_post_id)) . "</h2>";

// add an left floating image of the attachment
if ($ai4seo_this_attachment_is_an_image) {
    echo "<div class='ai4seo-attachment-editor-image-preview'>";
    echo "<img src='" . esc_url($ai4seo_this_attachment_url) . "' />";
    echo "</div>";
}

// GENERATE ALL BUTTON
echo "<div id='ai4seo-generate-all-attachment-attributes-button-hook'></div>";

// small gap
echo "<div class='ai4seo-clear-both'></div>";

// Form
echo "<div class='ai4seo-form ai4seo-editor-form'>";

    // === GO THROUGH EACH FIELD ================================================================================= \\

    foreach ($ai4seo_attachments_attributes_details as $ai4seo_this_attachment_attribute_identifier => $ai4seo_this_attachment_attribute_details) {
        // Make sure that required value-entries exist
        if (!isset($ai4seo_this_attachment_attribute_details["name"]) || !isset($ai4seo_this_attachment_attribute_details["input-type"]) || !isset($ai4seo_this_attachment_attribute_details["hint"])) {
            error_log("AI for SEO: Missing required details for media attribute: " . $ai4seo_this_attachment_attribute_identifier . " - post id: " . $ai4seo_this_attachment_post_id);
            continue;
        }

        if (!isset($ai4seo_this_post_attachment_attributes[$ai4seo_this_attachment_attribute_identifier])) {
            error_log("AI for SEO: Media Attributes: Missing value for attribute: " . $ai4seo_this_attachment_attribute_identifier . " - post id: " . $ai4seo_this_attachment_post_id);
            continue;
        }

        $ai4seo_this_attachment_attribute_value = $ai4seo_this_post_attachment_attributes[$ai4seo_this_attachment_attribute_identifier];
        $ai4seo_this_attachment_attribute_input_id = "ai4seo-attachment-attribute-" . $ai4seo_this_attachment_attribute_identifier;

        // form item
        echo "<div class='ai4seo-form-item'>";

            // Headline
            echo "<label for='" . esc_attr($ai4seo_this_attachment_attribute_input_id) . "'>";
                // Icon
                if (isset($ai4seo_this_attachment_attribute_details["icon"])) {
                    echo ai4seo_wp_kses(ai4seo_get_svg_tag($ai4seo_this_attachment_attribute_details["icon"], "", "ai4seo-24x24-icon ai4seo-gray-icon")) . " ";
                }

                // Name
                echo esc_html($ai4seo_this_attachment_attribute_details["name"]);

                // Tooltip
                echo ai4seo_wp_kses(ai4seo_get_icon_with_tooltip_tag($ai4seo_this_attachment_attribute_details["hint"]));
            echo "</label>";

            // Input
            echo "<div class='ai4seo-form-item-input-wrapper ai4seo-form-input-wrapper-with-generate-button'>";

                // Text field
                if ($ai4seo_this_attachment_attribute_details["input-type"] == "textfield") {
                    echo "<input type='text' class='ai4seo-editor-textfield' id='" . esc_attr($ai4seo_this_attachment_attribute_input_id) . "' onkeyup='" . esc_js($ai4seo_all_buttons_on_event) . "' onchange='" . esc_js($ai4seo_all_buttons_on_event) . "' value='" . esc_attr($ai4seo_this_attachment_attribute_value) . "'/>";
                }

                // Textarea
                else if ($ai4seo_this_attachment_attribute_details["input-type"] == "textarea") {
                    echo "<textarea class='ai4seo-editor-textarea' id='" . esc_attr($ai4seo_this_attachment_attribute_input_id) . "' onkeyup='" . esc_js($ai4seo_all_buttons_on_event) . "' onchange='" . esc_js($ai4seo_all_buttons_on_event) . "'>" . esc_textarea($ai4seo_this_attachment_attribute_value) . "</textarea>";
                }

            echo "</div>";
        echo "</div>";
    }

    // put the post id into a hidden field, so we have access to it after the form is submitted
    echo "<input type='hidden' id='ai4seo-editor-modal-post-id' value='" . esc_attr($ai4seo_this_attachment_post_id) . "' />";

    // Buttons
    echo "<div class='ai4seo-ajax-modal-buttons'>";
        // Left button
        echo "<div id='ai4seo-ajax-modal-left-button' class='ai4seo-ajax-modal-left-button'>";
            echo "<button type='button' onclick='ai4seo_hide_ajax_modal();' class='button ai4seo-button ai4seo-abort-button'>" . esc_html__("Abort", "ai-for-seo") . "</button>";
        echo "</div>";

        // Right button
        echo "<div id='ai4seo-ajax-modal-right-button' class='ai4seo-ajax-modal-right-button'>";
            echo "<button type='button' onclick='ai4seo_submit_ajax_modal(\"ai4seo_save_attachment_attributes_editor_values\");' class='button ai4seo-button ai4seo-success-button' id='ai4seo-modal-submit' disabled='disabled'>" . esc_html__("Submit", "ai-for-seo") . "</button>";
        echo "</div>";

        echo "<div class='ai4seo-clear'></div>";
    echo "</div>";
echo "</div>";