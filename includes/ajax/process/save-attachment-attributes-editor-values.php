<?php
/**
 * Returns the result of the attachment attributes form-processing as JSON. Called via AJAX.
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

// set false in production
$ai4seo_debug = false;

// set content type to json
if (!$ai4seo_debug) {
    header("Content-Type: application/json");
}

if (!isset($ai4seo_attachments_attributes_details)) {
    ai4seo_return_error_as_json("Media attributes details are not defined.", 201722824);
}


// === LOAD WORDPRESS FILES ================================================================== \\

// prevent outputting of errors or warnings or notices or whatever from now on
if (!$ai4seo_debug) {
    ob_start();
}

// === PROCESS METADATA FORM ================================================================= \\

// Make sure that ai4seo_input_values exists in $_REQUEST
if (!isset($_REQUEST["ai4seo_input_values"]) || !$_REQUEST["ai4seo_input_values"]) {
    ai4seo_return_error_as_json("No input values given.", 211722824);
}

// sanitize every element of the form-data
$ai4seo_users_inputs = ai4seo_deep_sanitize($_REQUEST["ai4seo_input_values"]);


// === CHECK PARAMETER: ATTACHMENT POST ID ============================================================== \\

// Get sanitized post id parameter
$ai4seo_this_attachment_post_id = (int) ($ai4seo_users_inputs["ai4seo-editor-modal-post-id"] ?? 0);

if (!$ai4seo_this_attachment_post_id) {
    ai4seo_return_error_as_json("No media post id given", 221722824);
}

if (!is_numeric($ai4seo_this_attachment_post_id) || $ai4seo_this_attachment_post_id < 1) {
    ai4seo_return_error_as_json("Media post id is not numeric.", 231722824);
}


// === READ OLD DATA ================================================================================= \\

$ai4seo_old_this_attachment_attributes = ai4seo_read_attachment_attributes($ai4seo_this_attachment_post_id);


// ___________________________________________________________________________________________ \\
// === PROCESS =============================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

$ai4seo_attachment_attributes_updates = array();

foreach ($ai4seo_attachments_attributes_details as $ai4seo_this_attachment_attribute_identifier => $ai4seo_this_attachment_attribute_details) {
    $ai4seo_this_input_id = "ai4seo-attachment-attribute-" . $ai4seo_this_attachment_attribute_identifier;

    // Make sure that this key exists in $ai4seo_users_inputs, skip if not
    if (!isset($ai4seo_users_inputs[$ai4seo_this_input_id])) {
        ai4seo_return_error_as_json("No input value given for " . $ai4seo_this_input_id, 241722824);
    }

    $ai4seo_attachment_attributes_updates[$ai4seo_this_attachment_attribute_identifier] = $ai4seo_users_inputs[$ai4seo_this_input_id];
}

ai4seo_update_active_attachment_attributes($ai4seo_this_attachment_post_id, $ai4seo_attachment_attributes_updates, true);

// Refresh the attachment attributes coverage
ai4seo_refresh_one_posts_attachment_attributes_coverage($ai4seo_this_attachment_post_id);
ai4seo_remove_post_ids_from_all_generation_status_options($ai4seo_this_attachment_post_id);


// === BUILD SUCCESS RESPONSE ========================================================================== \\

$ai4seo_response = array(
    "success" => true,
    "data" => array(
        "post_id" => $ai4seo_this_attachment_post_id,
    ),
);

ai4seo_return_success_as_json($ai4seo_response);