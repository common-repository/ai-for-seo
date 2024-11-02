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

global $ai4seo_metadata_details;

$ai4seo_plugins_official_name = ai4seo_get_plugins_official_name();


// === CHECK PARAMETER ============================================== \\

// Make sure that input-fields exist
if (!isset($ai4seo_metadata_details) || !$ai4seo_metadata_details) {
    ai4seo_return_error_as_json("An error occurred! Please check your settings or contact the plugin developer.", 2306230642);
}

$ai4seo_read_page_content_via_js = isset($_REQUEST["read_page_content_via_js"]) && $_REQUEST["read_page_content_via_js"] == "true" ? "true" : "false";

// Get sanitized post id parameter
$ai4seo_post_id = absint($_REQUEST["post_id"] ?? 0);

// validate post id
if ($ai4seo_post_id <= 0) {
    ai4seo_return_error_as_json("Post id is invalid.", 2306230638);
}


// === GET ADDITIONAL DETAILS ===================================================================== \\

// Read post- or page-title and post custom fields
$ai4seo_this_post_title = get_the_title($ai4seo_post_id);
$ai4seo_all_buttons_on_event = "ai4seo_enable_modal_submit_button();";

// read all metadata values for this post
$ai4seo_this_metadata = ai4seo_read_active_metadata_values_by_post_ids(array($ai4seo_post_id));

if ($ai4seo_this_metadata) {
    $ai4seo_this_metadata = $ai4seo_this_metadata[$ai4seo_post_id] ?? array();
}


// ___________________________________________________________________________________________ \\
// === OUTPUT ================================================================================ \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

// HEADLINE
echo "<h1 class='ai4seo-headline'>";
echo "<img src='" . esc_url(ai4seo_get_ai_for_seo_logo_url("64x64")) . "'>";
echo esc_html($ai4seo_plugins_official_name) . " - " . esc_html__("Metadata Editor", "ai-for-seo");
echo "</h1>";

echo "<h2>" . ai4seo_wp_kses(sprintf(__("Manage metadata for <b>%s</b> (#%d)", "ai-for-seo"), $ai4seo_this_post_title, $ai4seo_post_id)) . "</h2>";

// GENERATE ALL BUTTON
echo "<div id='ai4seo-generate-all-metadata-button-hook'></div>";

// Form
echo "<div class='ai4seo-form ai4seo-editor-form'>";

    // === GO THROUGH EACH FIELD ================================================================================= \\

    foreach ($ai4seo_metadata_details as $ai4seo_this_metadata_identifier => $ai4seo_this_metadata_details) {
        // Make sure that required value-entries exist
        if (!isset($ai4seo_this_metadata_details["name"]) || !isset($ai4seo_this_metadata_details["input"]) || !isset($ai4seo_this_metadata_details["hint"])) {
            continue;
        }

        // get the value of the post meta entry for the input-field
        $ai4seo_this_metadata_field_value = $ai4seo_this_metadata[$ai4seo_this_metadata_identifier] ?? "";
        $ai4seo_this_metadata_input_identifier = 'ai4seo-' . $ai4seo_this_metadata_identifier;

        // form item
        echo "<div class='ai4seo-form-item'>";

            // Name
            echo "<label for='" . esc_attr($ai4seo_this_metadata_input_identifier) . "'>";
                if (isset($ai4seo_this_metadata_details["icon"])) {
                    echo ai4seo_wp_kses(ai4seo_get_svg_tag($ai4seo_this_metadata_details["icon"], "", "ai4seo-24x24-icon ai4seo-gray-icon")) . " ";
                }

                echo esc_html($ai4seo_this_metadata_details["name"]);

                // Tooltip
                echo ai4seo_wp_kses(ai4seo_get_icon_with_tooltip_tag($ai4seo_this_metadata_details["hint"]));
            echo "</label>";

            // Input
            echo "<div class='ai4seo-form-item-input-wrapper ai4seo-form-input-wrapper-with-generate-button'>";

                // Text field
                if ($ai4seo_this_metadata_details["input"] == "textfield") {
                    echo "<input type='text' class='ai4seo-editor-textfield' id='" . esc_attr($ai4seo_this_metadata_input_identifier) . "' onkeyup='" . esc_js($ai4seo_all_buttons_on_event) . "' onchange='" . esc_js($ai4seo_all_buttons_on_event) . "' value='" . esc_attr($ai4seo_this_metadata_field_value) . "'/>";
                }

                // Textarea
                else if ($ai4seo_this_metadata_details["input"] == "textarea") {
                    echo "<textarea class='ai4seo-editor-textarea' id='" . esc_attr($ai4seo_this_metadata_input_identifier) . "' onkeyup='" . esc_js($ai4seo_all_buttons_on_event) . "' onchange='" . esc_js($ai4seo_all_buttons_on_event) . "'>" . esc_textarea($ai4seo_this_metadata_field_value) . "</textarea>";
                }
            echo "</div>";

        echo "</div>";
    }

    // put the post id into a hidden field, so we have access to it after the form is submitted
    echo "<input type='hidden' id='ai4seo-editor-modal-post-id' value='" . esc_attr($ai4seo_post_id) . "' />";
    echo "<input type='hidden' id='ai4seo-read-page-content-via-js' value='" . esc_attr($ai4seo_read_page_content_via_js) . "' />";

    // Buttons
    echo "<div class='ai4seo-modal-buttons'>";
        // Left button
        echo "<div id='ai4seo-modal-left-button' class='ai4seo-modal-left-button'>";
            echo "<button type='button' onclick='ai4seo_hide_ajax_modal();' class='button ai4seo-button ai4seo-abort-button'>" . esc_html__("Abort", "ai-for-seo") . "</button>";
        echo "</div>";

        // Right button
        echo "<div id='ai4seo-modal-right-button' class='ai4seo-modal-right-button'>";
            echo "<button type='button' onclick='ai4seo_submit_ajax_modal(\"ai4seo_save_metadata_editor_values\");' class='button ai4seo-button ai4seo-success-button' id='ai4seo-modal-submit' disabled='disabled'>" . esc_html__("Submit", "ai-for-seo") . "</button>";
        echo "</div>";

        echo "<div class='ai4seo-clear'></div>";
    echo "</div>";

echo "</div>";