<?php
/**
 * Called via AJAX.
 * Generates metadata through our RobHub API for a post and returns it as JSON.
 *
 * @since 1.0
 */

if (!defined("ABSPATH")) {
    exit;
}


// ___________________________________________________________________________________________ \\
// === PREPARE =============================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

// set false in production
$ai4seo_debug = false;

// set content type to json
if (!$ai4seo_debug) {
    header("Content-Type: application/json");
    ob_start();
}


// === CHECK PARAMETER: POST ID =========================================================== \\

// get sanitized post id parameter
$ai4seo_post_id = absint($_REQUEST["ai4seo_post_id"] ?? 0);

if ($ai4seo_post_id <= 0) {
    ai4seo_return_error_as_json("Post id is invalid.", 34127323);
}


// === CHECK PARAMETER: CONTENT ========================================================== \\

// get sanitized content parameter
$ai4seo_post_content = sanitize_textarea_field($_REQUEST["ai4seo_content"] ?? "");


// === CHECK PARAMETER: OLD VALUES =========================================================== \\

// get sanitized old values parameter
$ai4seo_generation_input_values = ai4seo_deep_sanitize($_REQUEST["ai4seo_generation_input_values"] ?? array());


// ___________________________________________________________________________________________ \\
// === INIT API COMMUNICATOR ================================================================= \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

if (!ai4seo_robhub_api() instanceof Ai4Seo_RobHubApiCommunicator) {
    ai4seo_return_error_as_json("Could not initialize API communicator. Please contact the plugin developer.", 12127323);
}

// check if credentials are set
if (!ai4seo_robhub_api()->init_credentials()) {
    ai4seo_return_error_as_json("Could not initialize API credentials. Please check your settings or contact the plugin developer.", 13127323);
}

$ai4seo_credits_balance = ai4seo_robhub_api()->get_credits_balance();


// ___________________________________________________________________________________________ \\
// === CHECK/COMPARE OLD VALUES ============================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

// if we have fresh input values, lets compare them with the old data. If we have old data, and if we see a difference
// to the current data, then just return the old data. If we do not have old data or if the data is the same, then we
// can continue with the generation process.
if ($ai4seo_generation_input_values) {
    $ai4seo_old_generated_values = ai4seo_read_generated_data_from_post_meta($ai4seo_post_id);

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
// === GET POST CONTENT FROM DATABASE ======================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

$ai4seo_post_content_length = (int) mb_strlen($ai4seo_post_content);

if (!$ai4seo_post_content || $ai4seo_post_content_length < AI4SEO_TOO_SHORT_CONTENT_LENGTH) {
    // get post content
    $ai4seo_post_content_from_database = ai4seo_get_post_content_summary($ai4seo_post_id);

    if (!$ai4seo_post_content_from_database) {
        if ($ai4seo_post_content_length == 0) {
            ai4seo_return_error_as_json(__("This entry contains no content.", "ai-for-seo"), 22127323);
        } else if ($ai4seo_post_content_length < AI4SEO_TOO_SHORT_CONTENT_LENGTH) {
            ai4seo_return_error_as_json(__("Determined length:", "ai-for-seo") . " $ai4seo_post_content_length / " . AI4SEO_TOO_SHORT_CONTENT_LENGTH, 54155424);
        } else {
            ai4seo_return_error_as_json(__("Could not read the content of this entry.", "ai-for-seo"), 21127323);
        }
    }

    $ai4seo_post_content_length = (int) mb_strlen($ai4seo_post_content_from_database);

    if ($ai4seo_post_content_length < AI4SEO_TOO_SHORT_CONTENT_LENGTH) {
        // short error description, as the rest is added dynamically on the other end
        ai4seo_return_error_as_json(__("Provided length:", "ai-for-seo") . " $ai4seo_post_content_length / " . AI4SEO_TOO_SHORT_CONTENT_LENGTH, 491320823);
    }

    $ai4seo_post_content_summary = $ai4seo_post_content_from_database;
    unset($ai4seo_post_content_from_database);
} else {
    ai4seo_condense_raw_post_content($ai4seo_post_content);
    $ai4seo_post_content_summary = $ai4seo_post_content;
    unset($ai4seo_post_content);
}

// check if content is too large
if (strlen($ai4seo_post_content_summary) > AI4SEO_MAX_TOTAL_CONTENT_SIZE) {
    ai4seo_return_error_as_json("Content is too large.", 361229323);
}


// ___________________________________________________________________________________________ \\
// === CHECK EXISTING CONTENT SUMMARY (COMPARE SIMILARITY) =================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

// dont compare if debug is enabled
/*if (!$ai4seo_debug) {
    $ai4seo_existing_post_content_summary = ai4seo_read_post_content_summary_from_post_meta($ai4seo_post_id);

    if ($ai4seo_existing_post_content_summary && ai4seo_are_post_content_summaries_similar($ai4seo_post_content_summary, $ai4seo_existing_post_content_summary)) {
        $ai4seo_existing_generated_data = ai4seo_read_generated_data_from_post_meta($ai4seo_post_id);

        if ($ai4seo_existing_generated_data) {
            $ai4seo_response = array(
                "success" => true,
                "data" => $ai4seo_existing_generated_data,
                "credits-consumed" => 0,
                "new-credits-balance" => $ai4seo_credits_balance,
            );

            ai4seo_return_success_as_json($ai4seo_response);
        }
    }
}*/


// ___________________________________________________________________________________________ \\
// === PREPARE POST CONTENT ================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

// check for a key phrase
$ai4seo_keyphrase = sanitize_text_field(ai4seo_get_any_third_party_seo_plugin_keyphrase($ai4seo_post_id));


// ___________________________________________________________________________________________ \\
// === EXECUTE API CALL ====================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

$ai4seo_metadata_generation_language = sanitize_text_field(ai4seo_get_setting(AI4SEO_SETTING_METADATA_GENERATION_LANGUAGE));

$ai4seo_api_call_parameters = array(
    "input" => $ai4seo_post_content_summary,
    "language" => $ai4seo_metadata_generation_language
);

if ($ai4seo_keyphrase) {
    $ai4seo_api_call_parameters["keyphrase"] = $ai4seo_keyphrase;
}


$ai4seo_results = ai4seo_robhub_api()->call("ai4seo/generate-all-metadata", $ai4seo_api_call_parameters, "POST");

// release semaphore
#sem_release($ai4seo_semaphore);

// destroy semaphore
#sem_remove($ai4seo_semaphore);


// ___________________________________________________________________________________________ \\
// === CHECK RESULTS ========================================================================= \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

if ($ai4seo_results === false) {
    ai4seo_return_error_as_json("Could not execute API call.", 28127323);
}

if (!is_array($ai4seo_results)) {
    ai4seo_return_error_as_json("API call did not return an array.", 29127323);
}

if (empty($ai4seo_results)) {
    ai4seo_return_error_as_json("API call returned an empty array.", 30127323);
}

if (!isset($ai4seo_results["success"])) {
    ai4seo_return_error_as_json("API call did not return a success value", 31127323);
}

if ($ai4seo_results["success"] === false) {
    ai4seo_return_error_as_json("API call returned an error: #" . $ai4seo_results["code"] . ": "  . $ai4seo_results["message"], 32127323);
}

if ($ai4seo_results["success"] !== true && $ai4seo_results["success"] !== "true") {
    ai4seo_return_error_as_json("API call returned an invalid success value.", 47127323);
}

// check if data is set
if (!isset($ai4seo_results["data"])) {
    ai4seo_return_error_as_json("API call did not return data.", 48127323);
}

// sanitize data
$ai4seo_results["data"] = wp_kses_post($ai4seo_results["data"]);

if (empty($ai4seo_results["data"])) {
    ai4seo_return_error_as_json("API call returned an empty data array.", 49127323);
}

if (!ai4seo_is_json($ai4seo_results["data"])) {
    ai4seo_return_error_as_json("API call returned an invalid data array: " . print_r($ai4seo_results["data"], true), 52127323);
}

$ai4seo_generated_data = json_decode($ai4seo_results["data"], true);

if (!$ai4seo_generated_data) {
    ai4seo_return_error_as_json("API call returned an invalid data array: " . print_r($ai4seo_results["data"], true), 331711823);
}


// === SAVE GENERATED DATA TO DATABASE ================================================================= \\

ai4seo_save_generated_data_to_postmeta($ai4seo_post_id, $ai4seo_generated_data);


// === PREPARE RESPONSE =============================================================================== \\

// go through each final data entry and use html_entity_decode
foreach ($ai4seo_generated_data as $ai4seo_final_data_key => $ai4seo_final_data_value) {
    $ai4seo_generated_data[$ai4seo_final_data_key] = html_entity_decode($ai4seo_final_data_value);
}

// check if credits are set
if (!isset($ai4seo_results["credits-consumed"])) {
    ai4seo_return_error_as_json("API call did not return consumed credits.", 50127323);
}

// sanitize credits
$ai4seo_results["credits-consumed"] = (int) $ai4seo_results["credits-consumed"];

// check if new credits balance is set
if (!isset($ai4seo_results["new-credits-balance"])) {
    ai4seo_return_error_as_json("API call did not return new credits balance.", 51127323);
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