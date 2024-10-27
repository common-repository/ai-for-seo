<?php
/**
 * The RobHub Api Communicator. Is used to get client data or to use its AI tools. Called via AJAX.
 *
 * @since 1.0
 */

if (!defined("ABSPATH")) {
    exit;
}

class Ai4Seo_RobHubApiCommunicator {
    public bool $support_localhost_mode = false; // todo: set to false in production (check prior to release)

    private $version = "v1";
    private $url = "https://api.robhub.ai";
    private $client_id;
    private $client_secret;
    private $free_account_client_secret = "_get-free-account-with-some-credits-to-play-with";
    private $default_product = "ai4seo";
    private $min_credits_balance = 5; # todo: will be replaced by the users settings based on the quality of the ai generations
    private $credits_balance_cache_lifetime = 86400; // 24 hours

    private $auth_data_option_name = "robhub_auth_data";
    private $credits_balance_option_name = "_robhub_credits_balance";
    private $last_credits_balance_check_option_name = "_robhub_last_credit_balance_check";

    private array $allowed_endpoints = array(
        "ai4seo/generate-all-metadata",
        "ai4seo/generate-all-attachment-attributes",

        "client/get-free-account",
        "client/credits-balance",
        "client/subscription"
    );

    private $free_endpoints = array(
        "client/get-free-account",
        "client/credits-balance",
        "client/subscription"
    );

    function __construct() {

    }

    // =========================================================================================== \\

    /**
     * Function to call the API.
     * @param $endpoint string The endpoint to check.
     * @param $parameters array Additional parameters to send to the API.
     * @param $request_method string The request method to use. Can be GET, POST, PUT or DELETE.
     * @return array|mixed|string The response from the API.
     */
    function call(string $endpoint, array $parameters = array(), string $request_method = "GET") {
        # only for testing via localhost -> if I'm localhost, then set url to localhost
        if ($this->support_localhost_mode && $_SERVER["SERVER_NAME"] === "localhost") {
            $this->url = "http://localhost:8081";
        }

        // check if endpoint is allowed
        if (!$this->is_endpoint_allowed($endpoint)) {
            return $this->respond_error("Endpoint " . $endpoint . " is not allowed.", 201313823);
        }

        // check request method
        if (!in_array($request_method, array("GET", "POST", "PUT", "DELETE"))) {
            return $this->respond_error("Request method " . $request_method . " is not allowed.", 211313823);
        }

        // check for client_id and client_secret
        if (!$this->has_credentials()) {
            return $this->respond_error("Missing client_id or client_secret", 2113111223);
        }

        // if this is not a free endpoint, check credits balance
        if (!in_array($endpoint, $this->free_endpoints)) {
            $credits_balance = $this->get_credits_balance();

            if ($credits_balance < $this->min_credits_balance) {
                return $this->respond_error("No credits left. Please buy more credits.", 1115424);
            }
        }

        $request_method = sanitize_text_field($request_method);

        // separate input parameter
        if (isset($parameters["input"])) {
            $input = $parameters["input"];
            unset($parameters["input"]);
        } else {
            $input = "";
        }

        // add product parameter
        $parameters["product"] = $this->default_product;

        // build url
        if ($parameters) {
            // Specify the character encoding as UTF-8
            $encoded_parameters = http_build_query($parameters, null, '&', PHP_QUERY_RFC3986);
            $curl_url = $this->url . "/" . $this->version . "/" . $endpoint . "?" . $encoded_parameters;
        } else {
            $curl_url = $this->url . "/" . $this->version . "/" . $endpoint;
        }

        // sanitize url
        $curl_url = esc_url_raw($curl_url);

        // validate url
        $curl_url = filter_var($curl_url, FILTER_VALIDATE_URL);

        if (!$curl_url) {
            return $this->respond_error("Invalid URL", 1913111223);
        }

        // Prepare headers for basic auth
        $headers = array(
            'Authorization' => 'Basic ' . base64_encode($this->client_id . ':' . $this->client_secret)
        );

        // Create an array of arguments for wp_safe_remote_request
        $args = array(
            'headers'     => $headers,
            'body'        => array("input" => $input),
            'method'      => $request_method,
            'timeout'     => 300  // Timeout in seconds (5 minutes)
        );

        // Make the request
        try {
            $response = wp_safe_remote_request($curl_url, $args);
        } catch(TypeError $e) {
            return $this->respond_error($e->getMessage(), 2313111223);
        } catch(Exception $e) {
            return $this->respond_error($e->getMessage(), 2413111223);
        }

        // Check for WP Error
        if (is_wp_error($response)) {
            $response = $response->get_error_message();
            $http_status = 999;
        } else {
            $http_status = wp_remote_retrieve_response_code($response);
            $response = wp_remote_retrieve_body($response);
        }

        // check the response status
        if ($http_status !== 200) {
            return $this->respond_error("API request failed with HTTP status " . $http_status .  " - response: " . $response, 221313823);
        }

        // decode the json response
        $decoded_response = json_decode($response, true);

        // sanitize the response
        if (is_array($decoded_response)) {
            array_walk_recursive($decoded_response, 'Ai4Seo_RobHubApiCommunicator::sanitize_array');
        } else {
            return $this->respond_error("API request failed with unknown error: " . print_r($response, true), 231313823);
        }

        if (!isset($decoded_response["success"]) || $decoded_response["success"] !== "true") {
            if (isset($decoded_response["error"])) {
                return $this->respond_error("#" . $decoded_response["code"] . ": " . $decoded_response["message"], 241313823);
            } else {
                return $this->respond_error("API request failed with unknown error: " . print_r($response, true), 251313823);
            }
        }

        // update new credits balance
        if (isset($decoded_response["new-credits-balance"]) && is_numeric($decoded_response["new-credits-balance"])) {
            // update $this->credits_balance_option_name option
            update_option($this->credits_balance_option_name, $decoded_response["new-credits-balance"]);
        }

        return $this->respond_success($decoded_response);
    }

    // =========================================================================================== \\

    function set_option_names(string $auth_data_option_name, string $credits_balance_option_name, string $last_credits_balance_check_option_name): void {
        $this->auth_data_option_name = $auth_data_option_name;
        $this->credits_balance_option_name = $credits_balance_option_name;
        $this->last_credits_balance_check_option_name = $last_credits_balance_check_option_name;
    }

    // =========================================================================================== \\

    /**
     * Function to either get client credentials from wp_options or to create a free account and save the credentials.
     * @param $client_id string (optional) If given, this client id will be used instead of the one from wp_options.
     * @param $client_secret string (optional) If given, this client secret will be used instead of the one from wp_options.
     * @return bool True if credentials are valid, false otherwise.
     */
    function init_credentials($client_id = false, $client_secret = false): bool {
        // use given auth data
        if ($client_id !== false && $client_secret !== false) {
            return $this->save_credentials($client_id, $client_secret);
        }

        // credentials already saved previously? -> skip
        if ($this->has_credentials()) {
            return true;
        }

        // read robhub auth data from json data in wp_options
        $robhub_auth_data = get_option($this->auth_data_option_name);

        // we do not have any auth data? ask for free account
        if ($robhub_auth_data === false) {
            return $this->init_free_account();
        }

        // otherwise, try to use the saved credentials
        $robhub_auth_data = sanitize_text_field($robhub_auth_data);
        $robhub_auth_data = json_decode($robhub_auth_data, false);

        if (isset($robhub_auth_data[0]) && isset($robhub_auth_data[1])) {
            return $this->save_credentials($robhub_auth_data[0], $robhub_auth_data[1]);
        }

        return false;
    }

    // =========================================================================================== \\

    /**
     * Function to create a free account and save the credentials.
     * @return bool True if free account was successfully created, false otherwise.
     */
    function init_free_account(): bool {
        // build pseudo client id and secret first
        $free_client_id = $this->build_client_id();
        $free_client_secret = $this->free_account_client_secret;

        if (!$this->save_credentials($free_client_id, $free_client_secret)) {
            return false;
        }

        // retrieve our real credentials
        $response = $this->call("client/get-free-account");

        // check response
        if (!isset($response["success"]) || !$response["success"] || !isset($response["data"]["client_id"]) || !isset($response["data"]["client_secret"])) {
            return false;
        }

        // try save new credentials
        if (!$this->save_credentials($response["data"]["client_id"], $response["data"]["client_secret"])) {
            return false;
        }

        // save to wp_options
        $success = update_option($this->auth_data_option_name, wp_json_encode(array($this->client_id, $this->client_secret)));

        if (!$success) {
            return false;
        }

        // everything went fine
        return true;
    }

    // =========================================================================================== \\

    /**
     * Checks if we got valid credentials already
     * @return bool True if credentials are set, false otherwise.
     */
    function has_credentials(): bool {
        return isset($this->client_id) && $this->client_id && isset($this->client_secret) && $this->client_secret;
    }

    // =========================================================================================== \\

    /**
     * Saves the given credentials to the corresponding variables.
     * @param $client_id string The client id to save.
     * @param $client_secret string The client secret to save.
     * @return bool True if credentials are valid and saved, false otherwise.
     */
    function save_credentials(string $client_id, string $client_secret): bool {
        $client_id = sanitize_key($client_id);
        $client_secret = sanitize_key($client_secret);

        // validate client id (lowercase, alphanumeric, dashes, underscores, 5-48 characters)
        if (!preg_match("/^[a-z0-9_\-]{5,48}$/", $client_id)) {
            return false;
        }

        // validate client secret (alphanumeric, exactly 48 characters)
        if (!preg_match("/^[a-z0-9_\-]{48}$/", $client_secret)) {
            return false;
        }

        $this->client_id = $client_id;
        $this->client_secret = $client_secret;

        return true;
    }

    // =========================================================================================== \\

    /**
     * We build a client id for a free account based on the domain name.
     * @return string The client id.
     */
    function build_client_id(): string {
        $server_name = sanitize_url($_SERVER["SERVER_NAME"]);
        // remove schema
        $server_name = str_replace("http://", "", $server_name);
        $server_name = str_replace("https://", "", $server_name);
        // remove port
        $server_name = explode(":", $server_name)[0];
        // remove www.
        $server_name = str_replace("www.", "", $server_name);

        // replace dots with dashes
        $domain_name = str_replace(".", "-", $server_name);

        // remove all non-alphanumeric characters
        $domain_name = preg_replace("/[^a-zA-Z0-9\-]/", "", $domain_name);

        return strtolower($domain_name);
    }

    // =========================================================================================== \\

    /**
     * This creates and error array response with message and code
     * @param $message string The message to return as an error.
     * @param $code int The error code to return.
     * @return array The error response.
     */
    function respond_error(string $message, int $code): array {
        if (strlen($message) > 256) {
            $message = substr($message, 0, 256) . "...";
        }

        return array(
            "success" => false,
            "message" => wp_kses_post($message),
            "code" => $code
        );
    }

    // =========================================================================================== \\

    /**
     * This function converts the given data to a normalized success response.
     * @param $data array|string The data to return as a success response.
     * @return array|mixed The success response.
     */
    function respond_success($data) {
        if (is_array($data)) {
            $data["success"] = true;
        } else {
            $data = array(
                "success" => true,
                "data" => wp_kses_post($data)
            );
        }

        return $data;
    }

    // =========================================================================================== \\

    /**
     * Sanitize the given array or string.
     * @param $array array|string The array or string to sanitize.
     * @param $key
     * @return void
     */
    static function sanitize_array(&$array, $key) {
        if (is_array($array)) {
            $array[$key] = wp_kses_post($array[$key]);
        } else {
            $array = wp_kses_post($array);
        }
    }

    // =========================================================================================== \\

    /**
     * Checks if this endpoint is allowed.
     * @param $endpoint string The endpoint to check.
     * @return bool True if the endpoint is allowed, false otherwise.
     */
    function is_endpoint_allowed(string $endpoint): bool {
        return in_array($endpoint, $this->allowed_endpoints);
    }

    // =========================================================================================== \\

    /**
     * Returns the client-id if credentials are initialized.
     * @return string The client-id or an empty string if credentials are not initialized.
     */
    function get_client_id(): string {
        // Make sure that credentials are initialized
        if (!$this->has_credentials()) {
            // Init credentials
            $this->init_credentials();
        }

        // Make sure that client-id is not empty
        if (!$this->client_id) {
            return "";
        }

        // Return client-id
        return $this->client_id;
    }

    // =========================================================================================== \\

    /**
     * Returns the client-secret if credentials are initialized.
     * @return string The client-id or an empty string if credentials are not initialized.
     */
    function get_client_secret(): string {
        // Make sure that credentials are initialized
        if (!$this->has_credentials()) {
            // Init credentials
            $this->init_credentials();
        }

        // Make sure that client-secret is not empty
        if (!$this->client_secret) {
            return "";
        }

        // Return client-secret
        return $this->client_secret;
    }

    // =========================================================================================== \\

    /**
     * Returns the credits balance of the client.
     * @return int The credits balance of the client.
     */
    function get_credits_balance(): int {
        // check _ai4seo_last_credit_balance_check option, if it's empty or older than 24 hours, call the robhub api to get the credits balance
        $last_credits_balance_check_timestamp = get_option($this->last_credits_balance_check_option_name, 0);

        // if the last credit balance check is older than $creditsBalanceCacheLifetime hours,
        // call the robhub api to get the credits balance
        if ($last_credits_balance_check_timestamp < time() - $this->credits_balance_cache_lifetime) {
            // if the option is empty, initialize the credentials and call the robhub api to get the credits balance
            // if it fails, return the current credits balance from the option
            if (!$this->init_credentials()) {
                return (int) get_option($this->credits_balance_option_name);
            }

            // call robhub api to get the credits balance
            $robhub_api_response = $this->call("client/credits-balance");
            $credits_balance = (int) ($robhub_api_response["new-credits-balance"] ?? 0);

            // update the option _robhub_current_credits_balance
            update_option($this->credits_balance_option_name, $credits_balance);

            // update the option _robhub_last_credit_balance_check
            update_option($this->last_credits_balance_check_option_name, time());
        } else {
            // get the current credits balance from the option
            $credits_balance = (int) get_option($this->credits_balance_option_name);
        }

        return $credits_balance;
    }

    // =========================================================================================== \\

    /**
     * Function to unset the last credit balance check option.
     */
    function reset_last_credit_balance_check(): void {
        delete_option($this->last_credits_balance_check_option_name);
    }

    // =========================================================================================== \\

    /**
     * Function returns the users formatted time, based on the robhub server timestamp
     */
    function get_formatted_time($robhub_timestamp, $format = 'Y-m-d H:i') {
        // Get the WordPress timezone
        $timezone = get_option('timezone_string');

        // If no valid timezone is set, default to UTC
        if (!$timezone) {
            return gmdate($format, $robhub_timestamp); // Use UTC format as fallback
        }

        // Create a DateTime object with the UTC timestamp
        $datetime = new DateTime("@$robhub_timestamp"); // The @ symbol treats the timestamp as UNIX time

        try {
            $datetime->setTimezone(new DateTimeZone($timezone)); // Set to WordPress timezone
        } catch (Exception $e) {
            return gmdate($format, $robhub_timestamp); // Use UTC format as fallback
        }

        // Format and return the time in the desired format
        return $datetime->format($format);
    }

    // =========================================================================================== \\

    /**
     * Calculate the difference in seconds between the current user timestamp and a given UTC timestamp.
     *
     * @param int $robhub_timestamp The UTC timestamp to compare.
     * @return int The difference in seconds. Positive if the UTC timestamp is in the future, negative if in the past.
     */
    function get_time_difference_in_seconds(int $robhub_timestamp): int {
        // Get the current timestamp in WordPress timezone
        $timezone = get_option('timezone_string');
        $current_time = current_time('timestamp'); // Current time in WordPress timezone

        // If a valid timezone is set, convert UTC timestamp to WordPress timezone
        if ($timezone) {
            $datetime_utc = new DateTime("@$robhub_timestamp");
            try {
                $datetime_utc->setTimezone(new DateTimeZone($timezone)); // Convert to WordPress timezone
            } catch (Exception $e) {
                return $robhub_timestamp - $current_time; // return the difference in seconds if timezone is invalid
            }
            $utc_timestamp_local = strtotime($datetime_utc->format('Y-m-d H:i:s')); // Convert to timestamp
        } else {
            $utc_timestamp_local = $robhub_timestamp; // Default to UTC if no timezone is set
        }

        // Calculate the difference in seconds
        $difference = $utc_timestamp_local - $current_time;

        return $difference;
    }

    // =========================================================================================== \\

    /**
     * Convert seconds into HH:MM:SS format.
     *
     * @param int $seconds The total number of seconds to convert.
     * @return string The formatted time in HH:MM:SS.
     */
    function format_seconds_to_hhmmss($seconds): string {
        // Ensure the seconds are non-negative
        $seconds = max(0, $seconds);

        // Calculate hours, minutes, and seconds
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remaining_seconds = $seconds % 60;

        // Format the result as HH:MM:SS
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $remaining_seconds);
    }
}