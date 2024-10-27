<?php
/**
 * Returns the result of the metadata form-processing as JSON. Called via AJAX.
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

// set false in production
$ai4seo_debug = false;

// set content type to json
if (!$ai4seo_debug) {
    header("Content-Type: application/json");
}

if (!isset($ai4seo_metadata_details)) {
    ai4seo_return_error_as_json("SEO input fields are not defined.", 1307230533);
}


// === LOAD WORDPRESS FILES ================================================================== \\

// prevent outputting of errors or warnings or notices or whatever from now on
if (!$ai4seo_debug) {
    ob_start();
}

// === PROCESS METADATA FORM ================================================================= \\

// Make sure that ai4seo_input_values exists in $_REQUEST
if (!isset($_REQUEST["ai4seo_input_values"]) || !$_REQUEST["ai4seo_input_values"]) {
    ai4seo_return_error_as_json("No input values given.", 1307230532);
}

// sanitize every element of the form-data
$ai4seo_users_inputs = ai4seo_deep_sanitize($_REQUEST["ai4seo_input_values"]);


// === CHECK PARAMETER: POST ID ============================================================== \\

// Get sanitized post id parameter
$ai4seo_post_id = (int) ($ai4seo_users_inputs["ai4seo-editor-modal-post-id"] ?? 0);

if (!$ai4seo_post_id) {
    ai4seo_return_error_as_json("No post id given", 1307230528);
}

if (!is_numeric($ai4seo_post_id) || $ai4seo_post_id < 1) {
    ai4seo_return_error_as_json("Post id is not numeric.", 1307230529);
}


// === READ OLD DATA ================================================================================= \\

$ai4seo_old_metadata = get_post_custom($ai4seo_post_id);


// === PREPARE CHANGES ============================================================================ \\

$ai4seo_upcoming_changes = array();

// remove "ai4seo-" prefix from meta-keys, so ai4seo_update_active_metadata() can handle them
foreach ($ai4seo_users_inputs as $ai4seo_key => $ai4seo_value) {
    $ai4seo_key_without_prefix = str_replace("ai4seo-", "", $ai4seo_key);
    $ai4seo_upcoming_changes[$ai4seo_key_without_prefix] = $ai4seo_value;
}


// ___________________________________________________________________________________________ \\
// === PROCESS =============================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

ai4seo_update_active_metadata($ai4seo_post_id, $ai4seo_upcoming_changes, true);

// Refresh the metadata coverage
ai4seo_refresh_one_posts_metadata_coverage_status($ai4seo_post_id);
ai4seo_remove_post_ids_from_all_generation_status_options($ai4seo_post_id);


// === BUILD SUCCESS RESPONSE ========================================================================== \\

$ai4seo_response = array(
    "success" => true,
    "data" => array(
        "post_id" => $ai4seo_post_id,
    ),
);

ai4seo_return_success_as_json($ai4seo_response);