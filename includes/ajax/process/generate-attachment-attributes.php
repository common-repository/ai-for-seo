<?php
/**
 * Called via AJAX.
 * Generates attachment attributes through our RobHub API for a post and returns it as JSON.
 *
 * @since 1.2
 */

if (!defined("ABSPATH")) {
    exit;
}

// ___________________________________________________________________________________________ \\
// === PREPARE =============================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

global $ai4seo_allowed_attachment_mime_types;

// set false in production
$ai4seo_debug = false;

// set content type to json
if (!$ai4seo_debug) {
    header("Content-Type: application/json");
    ob_start();
}


// ___________________________________________________________________________________________ \\
// === INIT API COMMUNICATOR ================================================================= \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

if (!ai4seo_robhub_api() instanceof Ai4Seo_RobHubApiCommunicator) {
    ai4seo_return_error_as_json("Could not initialize API communicator. Please contact the plugin developer.", 221823824);
}

// check if credentials are set
if (!ai4seo_robhub_api()->init_credentials()) {
    ai4seo_return_error_as_json("Could not initialize API credentials. Please check your settings or contact the plugin developer.", 231823824);
}

$ai4seo_credits_balance = ai4seo_robhub_api()->get_credits_balance();


// ___________________________________________________________________________________________ \\
// === CHECK PARAMETER ======================================================================= \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

// === CHECK PARAMETER: ATTACHMENT POST ID =========================================================== \\

// get sanitized post id parameter
$ai4seo_this_attachment_post_id = absint($_REQUEST["ai4seo_post_id"] ?? 0);

if ($ai4seo_this_attachment_post_id <= 0) {
    ai4seo_return_error_as_json("Media post id is invalid.", 211823824);
}


// === CHECK ATTACHMENT ======================================================================= \\

$ai4seo_use_base64_image = false;

// first, let's get the wp_post entry for more checks
$ai4seo_this_attachment_post = get_post($ai4seo_this_attachment_post_id);

// check if it's an attachment
if (!$ai4seo_this_attachment_post || $ai4seo_this_attachment_post->post_type !== "attachment") {
    ai4seo_return_error_as_json("Post is not a media.", 221823824);
}

// check if it's one of the allowed mime types
if (!in_array($ai4seo_this_attachment_post->post_mime_type, $ai4seo_allowed_attachment_mime_types)) {
    ai4seo_return_error_as_json("Media mime type is not allowed.", 231823824);
}

// check url of the attachment
$ai4seo_this_attachment_url = wp_get_attachment_url($ai4seo_this_attachment_post_id);

if (!$ai4seo_this_attachment_url) {
    ai4seo_return_error_as_json("Media url not found.", 241823824);
}

// check if the url is valid -> if not we will try to use the image as base64
if (!filter_var($ai4seo_this_attachment_url, FILTER_VALIDATE_URL)) {
    $ai4seo_use_base64_image = true;
}

if (ai4seo_robhub_api()->support_localhost_mode && $_SERVER["SERVER_NAME"] === "localhost") {
    $ai4seo_use_base64_image = true;
}

if (!$ai4seo_use_base64_image) {
    // check if the attachment url is accessible
    $ai4seo_this_attachment_url_headers = get_headers($ai4seo_this_attachment_url);

    if (!$ai4seo_this_attachment_url_headers || !is_array($ai4seo_this_attachment_url_headers) || !isset($ai4seo_this_attachment_url_headers[0])) {
        $ai4seo_use_base64_image = true;
    }

    if (strpos($ai4seo_this_attachment_url_headers[0], "200") === false) {
        $ai4seo_use_base64_image = true;
    }
}

if ($ai4seo_use_base64_image) {
    // Use wp_safe_remote_get instead of file_get_contents for fetching remote files
    $ai4seo_remote_get_response = wp_safe_remote_get($ai4seo_this_attachment_url, array(
        'decompress' => true // Enable automatic decompression
    ));

    if (is_wp_error($ai4seo_remote_get_response)) {
        ai4seo_return_error_as_json("Could not fetch media contents.", 391024824);
    }

    $response_code = wp_remote_retrieve_response_code($ai4seo_remote_get_response);

    if ($response_code !== 200) {
        ai4seo_return_error_as_json("Error fetching the media: HTTP Code $response_code", 401024824);
    }

    $ai4seo_this_attachment_contents = wp_remote_retrieve_body($ai4seo_remote_get_response);

    if (!$ai4seo_this_attachment_contents) {
        ai4seo_return_error_as_json("Could not get media contents.", 411024824);
    }

    $ai4seo_this_attachment_base64 = ai4seo_smart_image_base64_encode($ai4seo_this_attachment_contents);
    unset($ai4seo_this_attachment_contents);

    if (!$ai4seo_this_attachment_base64) {
        ai4seo_return_error_as_json("Could not encode media contents.", 421024824);
    }
}


// === CHECK PARAMETER: OLD VALUES =========================================================== \\

// get sanitized old values parameter
$ai4seo_generation_input_values = ai4seo_deep_sanitize($_REQUEST["ai4seo_generation_input_values"] ?? array());


// ___________________________________________________________________________________________ \\
// === CHECK/COMPARE OLD VALUES ============================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

// if we have fresh input values, lets compare them with the old data. If we have old data, and if we see a difference
// to the current data, then just return the old data. If we do not have old data or if the data is the same, then we
// can continue with the generation process.
if ($ai4seo_generation_input_values) {
    $ai4seo_old_generated_values = ai4seo_read_generated_data_from_post_meta($ai4seo_this_attachment_post_id);

    if ($ai4seo_old_generated_values) {
        $ai4seo_old_generated_values = ai4seo_deep_sanitize($ai4seo_old_generated_values);

        foreach ($ai4seo_generation_input_values AS $ai4seo_generation_input_key => $ai4seo_generation_input_value) {
            $ai4seo_generation_input_value = stripslashes($ai4seo_generation_input_value);

            if (!isset($ai4seo_old_generated_values[$ai4seo_generation_input_key])) {
                continue;
            }

            if ($ai4seo_generation_input_value !== $ai4seo_old_generated_values[$ai4seo_generation_input_key]) {
                $ai4seo_response = array(
                    "success" => true,
                    "data" => $ai4seo_old_generated_values,
                    "credits-consumed" => 0,
                    "new-credits-balance" => $ai4seo_credits_balance,
                );

                ai4seo_return_success_as_json($ai4seo_response);
            }
        }
    }
}


// ___________________________________________________________________________________________ \\
// === CHECK EXISTING ATTACHMENT ATTRIBUTES ================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/*$ai4seo_this_post_attachment_attributes_fully_covered = ai4seo_are_attachment_attributes_fully_covered($ai4seo_this_attachment_post_id);

if ($ai4seo_this_post_attachment_attributes_fully_covered) {
    $ai4seo_this_post_attachment_attributes = ai4seo_read_attachment_attributes($ai4seo_this_attachment_post_id);

    if ($ai4seo_this_post_attachment_attributes) {
        $ai4seo_response = array(
            "success" => true,
            "data" => $ai4seo_this_post_attachment_attributes,
            "credits-consumed" => 0,
            "new-credits-balance" => $ai4seo_credits_balance,
        );

        ai4seo_return_success_as_json($ai4seo_response);
    }
}*/


// ___________________________________________________________________________________________ \\
// === EXECUTE API CALL ====================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

$ai4seo_attachment_attributes_generation_language = sanitize_text_field(ai4seo_get_setting(AI4SEO_SETTING_ATTACHMENT_ATTRIBUTES_GENERATION_LANGUAGE));

if ($ai4seo_attachment_attributes_generation_language == "auto") {
    // todo: determine language by context (attachment surroundings / usings)

    // fallback: WordPress language
    $ai4seo_attachment_attributes_generation_language = sanitize_text_field(ai4seo_get_wordpress_language());
}

$ai4seo_api_call_parameters = array(
    "language" => $ai4seo_attachment_attributes_generation_language
);

// localhost workaround -> send image as base64
if ($ai4seo_use_base64_image) {
    $base64_image_encoded = sanitize_text_field("data:{$ai4seo_this_attachment_post->post_mime_type};base64,{$ai4seo_this_attachment_base64}");
    $ai4seo_api_call_parameters["input"] = $base64_image_encoded;
} else {
    $ai4seo_api_call_parameters["attachment_url"] = $ai4seo_this_attachment_url;
}

$ai4seo_robhub_endpoint = "ai4seo/generate-all-attachment-attributes";

try {
    $ai4seo_results = ai4seo_robhub_api()->call($ai4seo_robhub_endpoint, $ai4seo_api_call_parameters, "POST");
} catch (Exception $e) {
    ai4seo_return_error_as_json("Could not execute API call: " . $e->getMessage(), 261823824);
}


// ___________________________________________________________________________________________ \\
// === CHECK RESULTS ========================================================================= \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

if ($ai4seo_results === false) {
    ai4seo_return_error_as_json("Could not execute API call.", 271823824);
}

if (!is_array($ai4seo_results)) {
    ai4seo_return_error_as_json("API call did not return an array.", 281823824);
}

if (empty($ai4seo_results)) {
    ai4seo_return_error_as_json("API call returned an empty array.", 291823824);
}

if (!isset($ai4seo_results["success"])) {
    ai4seo_return_error_as_json("API call did not return a success value", 301823824);
}

if ($ai4seo_results["success"] === false) {
    ai4seo_return_error_as_json($ai4seo_results["message"] . " (Error-code: #" . $ai4seo_results["code"] . ")", 311823824);
}

if ($ai4seo_results["success"] !== true && $ai4seo_results["success"] !== "true") {
    ai4seo_return_error_as_json("API call returned an invalid success value.", 321823824);
}

// check if data is set
if (!isset($ai4seo_results["data"])) {
    ai4seo_return_error_as_json("API call did not return data.", 331823824);
}

// sanitize data
$ai4seo_results["data"] = wp_kses_post($ai4seo_results["data"]);

if (empty($ai4seo_results["data"])) {
    ai4seo_return_error_as_json("API call returned an empty data array.", 341823824);
}

if (!ai4seo_is_json($ai4seo_results["data"])) {
    ai4seo_return_error_as_json("API call returned an invalid data array: " . print_r($ai4seo_results["data"], true), 351823824);
}

$ai4seo_generated_data = json_decode($ai4seo_results["data"], true);

if (!$ai4seo_generated_data) {
    ai4seo_return_error_as_json("API call returned an invalid data array: " . print_r($ai4seo_results["data"], true), 361823824);
}

// === SAVE GENERATED DATA TO DATABASE ================================================================= \\

ai4seo_save_generated_data_to_postmeta($ai4seo_this_attachment_post_id, $ai4seo_generated_data);


// === PREPARE RETURN DATA ================================================================================= \\

// go through each final data entry and use html_entity_decode
foreach ($ai4seo_generated_data as $ai4seo_final_data_key => $ai4seo_final_data_value) {
    $ai4seo_generated_data[$ai4seo_final_data_key] = html_entity_decode($ai4seo_final_data_value);
}

// check if credits are set
if (!isset($ai4seo_results["credits-consumed"])) {
    ai4seo_return_error_as_json("API call did not return consumed credits.", 371823824);
}

// sanitize credits
$ai4seo_results["credits-consumed"] = (int) $ai4seo_results["credits-consumed"];

// check if new credits balance is set
if (!isset($ai4seo_results["new-credits-balance"])) {
    ai4seo_return_error_as_json("API call did not return new credits balance.", 381823824);
}

// sanitize new credits balance
$ai4seo_results["new-credits-balance"] = (int) $ai4seo_results["new-credits-balance"];


// === BUILD SUCCESS RESPONSE ========================================================================== \\

$ai4seo_response = array(
    "success" => true,
    "data" => $ai4seo_generated_data,
    "credits-consumed" => $ai4seo_results["credits-consumed"],
    "new-credits-balance" => $ai4seo_results["new-credits-balance"],
);

ai4seo_return_success_as_json($ai4seo_response);