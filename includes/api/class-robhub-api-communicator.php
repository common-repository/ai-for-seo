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
    private bool $does_user_need_to_accept_tos_toc_and_pp = false;

    public const ENVIRONMENTAL_VARIABLE_AUTH_DATA = "auth_data";
    public const ENVIRONMENTAL_VARIABLE_CREDITS_BALANCE = "credits_balance";
    public const ENVIRONMENTAL_VARIABLE_LAST_CREDIT_BALANCE_CHECK = "last_credit_balance_check";

    private string $environmental_variables_option_name = "robhub_environmental_variables";
    private const DEFAULT_ENVIRONMENTAL_VARIABLES = array(
        self::ENVIRONMENTAL_VARIABLE_AUTH_DATA => array(),
        self::ENVIRONMENTAL_VARIABLE_CREDITS_BALANCE => 0,
        self::ENVIRONMENTAL_VARIABLE_LAST_CREDIT_BALANCE_CHECK => 0,
    );
    private array $environmental_variables = self::DEFAULT_ENVIRONMENTAL_VARIABLES;

    private array $allowed_endpoints = array(
        "ai4seo/generate-all-metadata",
        "ai4seo/generate-all-attachment-attributes",

        "client/get-free-account",
        "client/credits-balance",
        "client/subscription",
        "client/accept-terms"
    );

    private array $free_endpoints = array(
        "client/get-free-account",
        "client/credits-balance",
        "client/subscription",
        "client/accept-terms"
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
        if ($this->does_user_need_to_accept_tos_toc_and_pp) {
            return $this->respond_error("Terms of Service have to be accepted first.", 2411301024);
        }

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
            $decoded_response = $this->deep_sanitize($decoded_response);
        } else {
            // check if response is html
            if (strpos($response, "<html") !== false || strpos($response, "html>") !== false) {
                // if it contains "One moment, please", then the request was blocked by cloudflare
                if (strpos($response, "One moment, please") !== false) {
                    return $this->respond_error("Failed to connect to our servers. It’s possible that your request was blocked by our server provider's security system, which may occur if your IP address has been flagged as suspicious. Please try again later. If this error persists, please contact our support team.", 4314181024);
                } else if (strpos($response, "<title>Maintenance</title>") !== false) {
                    return $this->respond_error("Our servers are currently undergoing maintenance. Please try again later.", 401211124);
                } else {
                    return $this->respond_error("There was an error receiving a proper response from our server. Please try again later.", 4414181024);
                }
            }

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
            $this->update_environmental_variable(self::ENVIRONMENTAL_VARIABLE_CREDITS_BALANCE, $decoded_response["new-credits-balance"]);
        }

        return $this->respond_success($decoded_response);
    }

    // =========================================================================================== \\

    function set_does_user_need_to_accept_tos_toc_and_pp(bool $does_user_need_to_accept_tos_toc_and_pp): void {
        $this->does_user_need_to_accept_tos_toc_and_pp = $does_user_need_to_accept_tos_toc_and_pp;
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
            return $this->use_this_credentials($client_id, $client_secret);
        }

        // credentials already saved previously? -> skip
        if ($this->has_credentials()) {
            return true;
        }

        // read robhub auth data from json data in wp_options
        $auth_data = $this->read_auth_data();

        // we do not have any auth data? ask for free account
        if (empty($auth_data)) {
            return $this->init_free_account();
        }

        // otherwise, try to use the saved credentials
        $auth_data = $this->deep_sanitize($auth_data);

        if (isset($auth_data[0]) && isset($auth_data[1])) {
            return $this->use_this_credentials($auth_data[0], $auth_data[1]);
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

        if (!$this->use_this_credentials($free_client_id, $free_client_secret)) {
            return false;
        }

        // retrieve our real credentials
        $response = $this->call("client/get-free-account");

        // check response
        if (!isset($response["success"]) || !$response["success"] || !isset($response["data"]["client_id"]) || !isset($response["data"]["client_secret"])) {
            $this->client_id = "";
            $this->client_secret = "";
            return false;
        }

        // try save new credentials
        if (!$this->use_this_credentials($response["data"]["client_id"], $response["data"]["client_secret"], true)) {
            $this->client_id = "";
            $this->client_secret = "";
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
     * @param $update_in_database bool If true, the credentials will be saved in the database.
     * @return bool True if credentials are valid and saved, false otherwise.
     */
    function use_this_credentials(string $client_id, string $client_secret, bool $update_in_database = false): bool {
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

        if ($update_in_database) {
            return $this->update_auth_data($client_id, $client_secret);
        }

        return true;
    }

    // =========================================================================================== \\

    /**
     * Function to read the auth data from the environmental variables.
     * @return array The auth data.
     */
    function read_auth_data(): array {
        $auth_data = $this->read_environmental_variable(self::ENVIRONMENTAL_VARIABLE_AUTH_DATA);

        if (!is_array($auth_data) || count($auth_data) !== 2) {
            return array();
        }

        return $auth_data;
    }

    // =========================================================================================== \\

    /**
     * Function to update the auth data in the environmental variables
     * @param string $client_id The client id to save.
     * @param string $client_secret The client secret to save.
     */
    function update_auth_data(string $client_id, string $client_secret): bool {
        return $this->update_environmental_variable(self::ENVIRONMENTAL_VARIABLE_AUTH_DATA, array($client_id, $client_secret));
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
     * Return a fully sanitized array, using custom sanitize functions for both keys and values.
     *
     * @param array|string $data The array or value to be sanitized.
     * @param string $sanitize_value_function_name The custom sanitize function for the values (default: sanitize_text_field).
     * @param string $sanitize_key_function_name The custom sanitize function for the keys (default: sanitize_key).
     * @return array|string The sanitized array or value.
     */
    function deep_sanitize($data, string $sanitize_value_function_name = 'sanitize_text_field', string $sanitize_key_function_name = 'sanitize_key') {
        if (is_array($data)) {
            $sanitized_data = array();
            foreach ($data as $key => $value) {
                // Sanitize the key using the key sanitize function
                $sanitized_key = $sanitize_key_function_name($key);

                // Recursively sanitize the value if it's an array, or sanitize the value using the value sanitize function
                if (is_array($value)) {
                    $sanitized_data[$sanitized_key] = ai4seo_deep_sanitize($value, $sanitize_value_function_name, $sanitize_key_function_name);
                } else {
                    if (is_bool($value)) {
                        $sanitized_data[$sanitized_key] = $value;
                    } else {
                        $sanitized_data[$sanitized_key] = $sanitize_value_function_name($value);
                    }
                }
            }
            return $sanitized_data;
        } else {
            if (is_bool($data)) {
                return $data;
            }

            // If it's not an array, sanitize the value directly
            return $sanitize_value_function_name($data);
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
        $last_credits_balance_check_timestamp = (int) $this->read_environmental_variable(self::ENVIRONMENTAL_VARIABLE_LAST_CREDIT_BALANCE_CHECK);

        // if the last credit balance check is older than $creditsBalanceCacheLifetime hours,
        // call the robhub api to get the credits balance
        if ($last_credits_balance_check_timestamp < time() - $this->credits_balance_cache_lifetime) {
            // if the option is empty, initialize the credentials and call the robhub api to get the credits balance
            // if it fails, return the current credits balance from the option
            if (!$this->init_credentials()) {
                return (int) $this->read_environmental_variable(self::ENVIRONMENTAL_VARIABLE_CREDITS_BALANCE);
            }

            // call robhub api to get the credits balance
            $robhub_api_response = $this->call("client/credits-balance");
            $credits_balance = (int) ($robhub_api_response["new-credits-balance"] ?? 0);

            // update the option _robhub_current_credits_balance
            $this->update_environmental_variable(self::ENVIRONMENTAL_VARIABLE_CREDITS_BALANCE, $credits_balance);

            // update
            $this->update_environmental_variable(self::ENVIRONMENTAL_VARIABLE_LAST_CREDIT_BALANCE_CHECK, time());
        } else {
            // get the current credits balance from the option
            $credits_balance = (int) $this->read_environmental_variable(self::ENVIRONMENTAL_VARIABLE_CREDITS_BALANCE);
        }

        return $credits_balance;
    }

    // =========================================================================================== \\

    /**
     * Function to unset the last credit balance check option.
     */
    function reset_last_credit_balance_check(): void {
        $this->update_environmental_variable(self::ENVIRONMENTAL_VARIABLE_LAST_CREDIT_BALANCE_CHECK, 0);
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


    // ___________________________________________________________________________________________ \\
    // === ROBHUB ENVIRONMENTAL VARIABLES ======================================================== \\
    // ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

    function set_environmental_variables_option_name(string $environmental_variables_option_name): void {
        $this->environmental_variables_option_name = $environmental_variables_option_name;
    }

    // =========================================================================================== \\

    /**
     * Function to retrieve all robhub environmental variables
     * @return array All RobHub environmental variables
     */
    function read_all_environmental_variables(): array {
        if ($this->environmental_variables !== self::DEFAULT_ENVIRONMENTAL_VARIABLES) {
            return $this->environmental_variables;
        }

        $current_environmental_variables = get_option($this->environmental_variables_option_name);
        $current_environmental_variables = maybe_unserialize($current_environmental_variables);

        // fallback to existing environmental variables
        if (!is_array($current_environmental_variables)) {
            return $this->environmental_variables;
        }

        // go through each environmental variable and check if it is valid
        foreach ($current_environmental_variables as $environmental_variable_name => $environmental_variable_value) {
            // if this environmental variable is not known, remove it
            if (!isset(self::DEFAULT_ENVIRONMENTAL_VARIABLES[$environmental_variable_name])) {
                unset($current_environmental_variables[$environmental_variable_name]);
                continue;
            }

            if (!$this->validate_environmental_variable_value($environmental_variable_name, $environmental_variable_value)) {
                error_log("ROBHUB: Invalid value for environmental variable '" . $environmental_variable_name . "'. #5415181024");
                $current_environmental_variables[$environmental_variable_name] = self::DEFAULT_ENVIRONMENTAL_VARIABLES[$environmental_variable_name];
            }
        }

        $this->environmental_variables = $current_environmental_variables;

        return $current_environmental_variables;
    }

    // =========================================================================================== \\

    /**
     * Function to retrieve a specific robhub environmental variable
     * @param string $environmental_variable_name The name of the robhub environmental variable
     * @return mixed The value of the environmental variable
     */
    function read_environmental_variable(string $environmental_variable_name) {
        // Make sure that $environmental_variable_name-parameter has content
        if (!$environmental_variable_name) {
            error_log("ROBHUB: Environmental variable name is empty. #3515181024");
            return "";
        }

        $current_environmental_variables = $this->read_all_environmental_variables();

        // Check if the $environmental_variable_name-parameter exists in environmental variables-array
        if (!isset($current_environmental_variables[$environmental_variable_name])) {
            // check for a default value
            if (isset(self::DEFAULT_ENVIRONMENTAL_VARIABLES[$environmental_variable_name])) {
                return self::DEFAULT_ENVIRONMENTAL_VARIABLES[$environmental_variable_name];
            } else {
                error_log("ROBHUB: Unknown environmental variable name: " . $environmental_variable_name . ". #3615181024");
            }
            return "";
        }

        return $current_environmental_variables[$environmental_variable_name];
    }

    // =========================================================================================== \\

    /**
     * Function to update a specific robhub environmental variable
     * @param string $environmental_variable_name The name of the robhub environmental variable
     * @param mixed $new_environmental_variable_value The new value of the robhub environmental variable
     * @return bool True if the robhub environmental variable was updated successfully, false if not
     */
    function update_environmental_variable(string $environmental_variable_name, $new_environmental_variable_value): bool {
        // Make sure that the new value of the environmental variable is valid
        if (!$this->validate_environmental_variable_value($environmental_variable_name, $new_environmental_variable_value)) {
            error_log("ROBHUB: Invalid value for environmental variable '" . $environmental_variable_name . "'. #3715181024");
            return false;
        }

        // sanitize
        $new_environmental_variable_value = $this->deep_sanitize($new_environmental_variable_value);

        // overwrite entry in $current_environmental_variables-array
        $current_environmental_variables = $this->read_all_environmental_variables();
        $current_environmental_variables[$environmental_variable_name] = $new_environmental_variable_value;

        // update the class parameter as well
        $this->environmental_variables = $current_environmental_variables;

        // Save updated environmental variables to database
        return update_option($this->environmental_variables_option_name, $current_environmental_variables, true);
    }

    // =========================================================================================== \\

    /**
     * Validate value of an robhub environmental variable
     * @param string $environmental_variable_name The name of the robhub environmental variable
     * @param mixed $environmental_variable_value The value of the robhub environmental variable
     */
    function validate_environmental_variable_value(string $environmental_variable_name, $environmental_variable_value): bool {
        switch ($environmental_variable_name) {
            case self::ENVIRONMENTAL_VARIABLE_AUTH_DATA:
                // array, contains of two elements, each of them contains only of alphanumeric characters
                if (!is_array($environmental_variable_value)) {
                    return false;
                }

                // empty array is allowed
                if (count($environmental_variable_value) === 0) {
                    return true;
                }

                if (count($environmental_variable_value) !== 2) {
                    return false;
                }

                if (!preg_match("/^[a-z0-9_\-]{5,48}$/", $environmental_variable_value[0])) {
                    return false;
                }

                if (!preg_match("/^[a-z0-9_\-]{48}$/", $environmental_variable_value[1])) {
                    return false;
                }

                return true;
            case self::ENVIRONMENTAL_VARIABLE_CREDITS_BALANCE:
            case self::ENVIRONMENTAL_VARIABLE_LAST_CREDIT_BALANCE_CHECK:
                // contains only of numbers
                return is_numeric($environmental_variable_value) && $environmental_variable_value >= 0;
            default:
                return false;
        }
    }
}