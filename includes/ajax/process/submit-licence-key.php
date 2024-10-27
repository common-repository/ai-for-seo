<?php
/**
 * Updates the licence key. Called via AJAX.
 *
 * @since 1.1
 */

if (!defined("ABSPATH")) {
    exit;
}


// ___________________________________________________________________________________________ \\
// === PREPARE =============================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

// === CHECK PARAMETER: licence_key ============================================================== \\

// Get sanitized context parameter
$ai4seo_new_robhub_licence_key = sanitize_key($_REQUEST["licence_key"]);

if (!$ai4seo_new_robhub_licence_key || !preg_match("/^[a-zA-Z0-9]+$/", $ai4seo_new_robhub_licence_key)) {
    ai4seo_return_error_as_json("No or malformed licence key $ai4seo_new_robhub_licence_key.", 371222324);
}

if (!ai4seo_robhub_api() instanceof Ai4Seo_RobHubApiCommunicator) {
    ai4seo_return_error_as_json("Could not initialize API communicator. Please contact the plugin developer.", 401222324);
}


// ___________________________________________________________________________________________ \\
// === READ OLD AUTH DATA ==================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

$ai4seo_robhub_auth_data = get_option(AI4SEO_ROBHUB_AUTH_DATA_OPTION_NAME);

if (!$ai4seo_robhub_auth_data) {
    ai4seo_return_error_as_json("Could not read old licence key.", 411222324);
}

$ai4seo_robhub_auth_data = json_decode($ai4seo_robhub_auth_data);

if (!is_array($ai4seo_robhub_auth_data) || count($ai4seo_robhub_auth_data) !== 2) {
    ai4seo_return_error_as_json("Could not read old licence key.", 421222324);
}

$ai4seo_robhub_auth_data = array_map("sanitize_key", $ai4seo_robhub_auth_data);

$ai4seo_robhub_client_id = $ai4seo_robhub_auth_data[0];
$ai4seo_old_robhub_licence_key = $ai4seo_robhub_auth_data[1];


// ___________________________________________________________________________________________ \\
// === UPDATE DATABASE ======================================================================= \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

$ai4seo_encoded_new_auth_data = wp_json_encode(array($ai4seo_robhub_client_id, $ai4seo_new_robhub_licence_key));
update_option(AI4SEO_ROBHUB_AUTH_DATA_OPTION_NAME, $ai4seo_encoded_new_auth_data);

$ai4seo_save_credentials_success = ai4seo_robhub_api()->save_credentials($ai4seo_robhub_client_id, $ai4seo_new_robhub_licence_key);

if (!$ai4seo_save_credentials_success) {
    ai4seo_return_error_as_json("Could not save new licence key.", 381222324);
}


// ___________________________________________________________________________________________ \\
// === TEST NEW CREDENTIALS ================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

$ai4seo_robhub_api_response = ai4seo_robhub_api()->call("client/credits-balance");

if (!isset($ai4seo_robhub_api_response["success"]) || $ai4seo_robhub_api_response["success"] !== true) {
    // restore old licence key
    $ai4seo_encoded_old_auth_data = wp_json_encode(array($ai4seo_robhub_client_id, $ai4seo_old_robhub_licence_key));
    update_option(AI4SEO_ROBHUB_AUTH_DATA_OPTION_NAME, $ai4seo_encoded_old_auth_data);

    ai4seo_return_error_as_json("Could not verify new licence key.", 391222324);
}


// ___________________________________________________________________________________________ \\
// === RESPONSE ============================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

// unset option _ai4seo_last_credit_balance_check, so we can check the balance again
delete_option("_ai4seo_last_credit_balance_check");

$ai4seo_response = array(
    "success" => true,
);

ai4seo_return_success_as_json($ai4seo_response);