<?php
/**
 * Renders the content of the submenu page for the AI for SEO settings page.
 *
 * @since 1.2.0
 */

if (!defined("ABSPATH")) {
    exit;
}


// ___________________________________________________________________________________________ \\
// === PREPARE =============================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

global $ai4seo_settings;
global $ai4seo_boolean_format_setting_names;
global $ai4seo_fallback_allowed_user_roles;
global $ai4seo_metadata_details;
global $ai4seo_attachments_attributes_details;

// Prepare variable for the user-roles
$ai4seo_allowed_user_roles = ai4seo_get_all_possible_user_roles();

// fallback for user-roles
if (!$ai4seo_allowed_user_roles) {
    $ai4seo_allowed_user_roles = $ai4seo_fallback_allowed_user_roles;
}

// handle known and allowed changeable setting names
$ai4seo_all_known_changeable_settings_names = array(
    AI4SEO_SETTING_META_TAG_OUTPUT_MODE,
    AI4SEO_SETTING_APPLY_CHANGES_TO_THIRD_PARTY_SEO_PLUGINS,
    AI4SEO_SETTING_ALLOWED_USER_ROLES,
    AI4SEO_SETTING_METADATA_GENERATION_LANGUAGE,
    AI4SEO_SETTING_ATTACHMENT_ATTRIBUTES_GENERATION_LANGUAGE,
    AI4SEO_SETTING_VISIBLE_META_TAGS,
    AI4SEO_SETTING_OVERWRITE_EXISTING_METADATA,
    AI4SEO_SETTING_OVERWRITE_EXISTING_ATTACHMENT_ATTRIBUTES,
);

$ai4seo_my_changeable_settings_names = $ai4seo_all_known_changeable_settings_names;

# remove settings that are not available in the current version
# $ai4seo_my_changeable_settings_names = array_diff($ai4seo_my_changeable_settings_names, array(AI4SEO_SETTING_XXXXXXXXXXX));

// Variable for the plugin settings with boolean-values -> used for a workaround to ensure that all settings are saved
$ai4seo_boolean_format_setting_names = array();

// Variable for the plugin settings with array base boolean-values -> used for a workaround to ensure that all settings are saved
$ai4seo_array_based_boolean_format_setting_names = array(AI4SEO_SETTING_VISIBLE_META_TAGS, AI4SEO_SETTING_ALLOWED_USER_ROLES,
    AI4SEO_SETTING_APPLY_CHANGES_TO_THIRD_PARTY_SEO_PLUGINS, AI4SEO_SETTING_OVERWRITE_EXISTING_METADATA,
    AI4SEO_SETTING_OVERWRITE_EXISTING_ATTACHMENT_ATTRIBUTES);

$ai4seo_wordpress_language = ai4seo_get_wordpress_language();


// ___________________________________________________________________________________________ \\
// === PROCESS FORM ========================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

if (isset($_POST["ai4seo-submit"])) {
    if (isset($_POST['ai4seo_settings_nonce'])) {
        $_POST['ai4seo_settings_nonce'] = sanitize_text_field($_POST['ai4seo_settings_nonce']);
    }

    if (!isset($_POST['ai4seo_settings_nonce']) || !wp_verify_nonce($_POST['ai4seo_settings_nonce'], 'ai4seo_settings_action')) {
        wp_die(__('Invalid request. Please try again.', 'ai-for-seo'));
    }

    // Checkboxes / Boolean Format Workaround: Loop through all settings with boolean-values and make sure that they exist in $_POST
    // If a setting doesn't exist in $_POST then save the value as "0" to ensure that the default-value of "1" can be overwritten
    foreach (array_merge($ai4seo_boolean_format_setting_names, $ai4seo_array_based_boolean_format_setting_names) as $ai4seo_this_boolean_format_setting_name) {
        $ai4seo_this_setting_input_name = "ai4seo_" . $ai4seo_this_boolean_format_setting_name;

        if (isset($_POST[$ai4seo_this_setting_input_name])) {
            continue;
        }

        if (in_array($ai4seo_this_boolean_format_setting_name, $ai4seo_my_changeable_settings_names)) {
            // set value to "0" as a fallback, if the server doesn't send the value
            $_POST[$ai4seo_this_setting_input_name] = "0";
        }

        if (in_array($ai4seo_this_boolean_format_setting_name, $ai4seo_array_based_boolean_format_setting_names)) {
            // set value to an empty array as a fallback, if the server doesn't send the value
            $_POST[$ai4seo_this_setting_input_name] = array();
        }
    }

    // apply changes to the settings
    $ai4seo_upcoming_setting_changes = array();

    foreach ($ai4seo_my_changeable_settings_names as $ai4seo_this_setting_name) {
        $ai4seo_this_setting_input_name = "ai4seo_" . $ai4seo_this_setting_name;

        // Check if _$_POST-entry exists for this setting
        if (!isset($_POST[$ai4seo_this_setting_input_name])) {
            error_log("Setting '{$ai4seo_this_setting_name}' not found in POST.");
            continue;
        }

        // sanitize first
        $_POST[$ai4seo_this_setting_input_name] = ai4seo_deep_sanitize($_POST[$ai4seo_this_setting_input_name]);
        $ai4seo_upcoming_setting_changes[$ai4seo_this_setting_name] = $_POST[$ai4seo_this_setting_input_name];

        // workaround: always add "administrator" to the allowed user roles
        if ($ai4seo_this_setting_name == AI4SEO_SETTING_ALLOWED_USER_ROLES && !in_array("administrator", $_POST[$ai4seo_this_setting_input_name])) {
            $ai4seo_upcoming_setting_changes[$ai4seo_this_setting_name][] = "administrator";
        }
    }

    // bulk update settings
    if ($ai4seo_upcoming_setting_changes) {
        // Update this plugin-setting
        $ai4seo_successfully_updated_setting = ai4seo_bulk_update_settings($ai4seo_upcoming_setting_changes);

        if (!$ai4seo_successfully_updated_setting) {
            error_log("Could not bulk update settings");

            // Display error message
            echo "<div class='notice notice-error is-dismissible'>";
                echo "<p>" . esc_html__("Could not save the settings. Please try again or contact the plugin developer.", "ai-for-seo") . "</p>";
            echo "</div>";
        }
    }
}


// ___________________________________________________________________________________________ \\
// === OUTPUT ================================================================================ \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

echo "<form method='post' class='ai4seo-form'>";

    // Display the nonce field
    echo wp_nonce_field('ai4seo_settings_action', 'ai4seo_settings_nonce');


    // ___________________________________________________________________________________________ \\
    // === METADATA ============================================================================== \\
    // ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

    echo "<div class='card ai4seo-form-section'>";
        // Headline
        echo "<h2>";
            echo '<i class="dashicons dashicons-admin-site ai4seo-nav-tab-icon"></i>';
            echo esc_html__("Metadata", "ai-for-seo");
        echo "</h2>";


        // === AI4SEO_SETTING_VISIBLE_META_TAGS ========================================================== \\

        $ai4seo_this_setting_name = AI4SEO_SETTING_VISIBLE_META_TAGS;

        if (in_array($ai4seo_this_setting_name, $ai4seo_my_changeable_settings_names)) {
            $ai4seo_this_setting_input_name = "ai4seo_{$ai4seo_this_setting_name}";
            $ai4seo_this_setting_input_value = ai4seo_get_setting($ai4seo_this_setting_name);
            $ai4seo_this_setting_description = __("With this selection, you can choose whether to include or exclude specific meta tags from being output in the header by our plugin. This setting does not affect meta tags generated by other plugins. For example, if you already use a plugin that generates meta titles and prefer those, you may want to exclude them here.", "ai-for-seo");

            // Divider
            #echo "<hr class='ai4seo-form-item-divider'>";

            // Display form elements
            echo "<div class='ai4seo-form-item'>";
                echo "<label for='" . esc_attr($ai4seo_this_setting_input_name) . "'>";
                echo esc_html__("Meta Tag Inclusion:", "ai-for-seo") ;
                echo "</label>";

                echo "<div class='ai4seo-form-item-input-wrapper'>";
                    // Define variable for the selected user-roles based on plugin-settings
                    $ai4seo_this_checked_values = ($ai4seo_this_setting_input_value && is_array($ai4seo_this_setting_input_value) ? $ai4seo_this_setting_input_value : array());

                    // add a select / un select all checkbox
                    echo ai4seo_wp_kses(ai4seo_get_select_all_checkbox($ai4seo_this_setting_input_name));
                    echo "<div class='ai4seo-medium-gap'></div>";

                    // Loop through all available user-roles and display checkboxes for each of them
                    foreach ($ai4seo_metadata_details as $ai4seo_this_metadata_identifier => $ai4seo_this_metadata_details) {
                        $ai4seo_this_translated_checkbox_label = $ai4seo_this_metadata_details["name"] ?? $ai4seo_this_metadata_identifier;
                        $ai4seo_this_checkbox_id = "{$ai4seo_this_setting_input_name}-{$ai4seo_this_metadata_identifier}";

                        // Determine whether this role is supported
                        $ai4seo_is_this_checkbox_checked = in_array($ai4seo_this_metadata_identifier, $ai4seo_this_checked_values);

                        echo "<label for='" . esc_attr($ai4seo_this_checkbox_id) . "' class='ai4seo-form-multiple-inputs'>";
                        echo "<input type='checkbox' id='" . esc_attr($ai4seo_this_checkbox_id) . "' name='" . esc_attr($ai4seo_this_setting_input_name) . "[]' value='" . esc_attr($ai4seo_this_metadata_identifier) . "'" . ($ai4seo_is_this_checkbox_checked ? " checked='checked'" : "") . "/> ";
                        echo esc_html($ai4seo_this_translated_checkbox_label);

                        if ($ai4seo_this_metadata_identifier == "meta-title") {
                            echo ai4seo_get_icon_with_tooltip_tag(__("<strong>We encourage you to read this information carefully before activating the AI-generated meta title output to ensure you understand the benefits.</strong><br><br>The meta title appears in your <strong>browser's title or tab</strong>.<br><br>If it's not specified, WordPress will default to using a combination of the page, post, or product title and your website's name. While this might seem natural for visitors, search engines could consider it duplicate content, which can <strong>negatively impact SEO</strong>. Our AI-generated meta titles are optimized with additional keywords and context, which can help improve your search rankings.", "ai-for-seo"));
                        }

                        echo "<br>";
                        echo "</label>";
                    }

                    echo "<p class='ai4seo-form-item-description'>";
                        echo ai4seo_wp_kses($ai4seo_this_setting_description);
                    echo "</p>";
                echo "</div>";
            echo "</div>";
        }


        // === AI4SEO_SETTING_METADATA_GENERATION_LANGUAGE =========================================== \\

        $ai4seo_this_setting_name = AI4SEO_SETTING_METADATA_GENERATION_LANGUAGE;

        if (in_array($ai4seo_this_setting_name, $ai4seo_my_changeable_settings_names)) {
            $ai4seo_this_setting_input_name = "ai4seo_{$ai4seo_this_setting_name}";
            $ai4seo_this_setting_input_value = ai4seo_get_setting($ai4seo_this_setting_name);
            $ai4seo_this_setting_description = __("If you are targeting a specific language or region, please select the appropriate language. For multi-language websites, we do not recommend setting a fixed language. Choose 'Automatic' to let the AI determine the best language for each content entry (recommended).", "ai-for-seo");

            // Divider
            echo "<hr class='ai4seo-form-item-divider'>";

            echo "<div class='ai4seo-form-item'>";
                echo "<label for='" . esc_attr($ai4seo_this_setting_input_name) . "'>";
                    echo esc_html__("Language for Metadata Generation", "ai-for-seo") . ":";
                echo "</label>";

                echo "<div class='ai4seo-form-item-input-wrapper'>";
                    echo "<select id='" . esc_attr($ai4seo_this_setting_input_name) . "' name='" . esc_attr($ai4seo_this_setting_input_name) . "'>";
                        echo ai4seo_wp_kses(ai4seo_get_generation_language_select_options_html($ai4seo_this_setting_input_value));
                    echo "</select>";

                    echo "<p class='ai4seo-form-item-description'>";
                        echo ai4seo_wp_kses($ai4seo_this_setting_description);
                    echo "</p>";
                echo "</div>";
            echo "</div>";
        }


        // === AI4SEO_SETTING_APPLY_CHANGES_TO_THIRD_PARTY_SEO_PLUGIN ============================================= \\

        $ai4seo_this_setting_name = AI4SEO_SETTING_APPLY_CHANGES_TO_THIRD_PARTY_SEO_PLUGINS;

        if (in_array($ai4seo_this_setting_name, $ai4seo_my_changeable_settings_names)) {
            $ai4seo_active_third_party_seo_plugin_details = ai4seo_get_active_third_party_seo_plugin_details();
            $ai4seo_uses_workarounds_for_third_party_seo_plugins = false;

            foreach ($ai4seo_active_third_party_seo_plugin_details AS $ai4seo_active_third_party_seo_plugin_identifier => $ai4seo_active_third_party_seo_plugin_detail) {
                if (in_array($ai4seo_active_third_party_seo_plugin_identifier, array(AI4SEO_THIRD_PARTY_PLUGIN_SLIM_SEO, AI4SEO_THIRD_PARTY_PLUGIN_ALL_IN_ONE_SEO, AI4SEO_THIRD_PARTY_PLUGIN_SQUIRRLY_SEO, AI4SEO_THIRD_PARTY_PLUGIN_BLOG2SOCIAL))) {
                    $ai4seo_uses_workarounds_for_third_party_seo_plugins = true;
                    break;
                }
            }

            $ai4seo_this_setting_input_name = "ai4seo_{$ai4seo_this_setting_name}";
            $ai4seo_this_setting_input_value = ai4seo_get_setting($ai4seo_this_setting_name);
            $ai4seo_this_setting_description = __("Activating one of the checkboxes ensures that any changes you make to a post's metadata or keyphrase within the 'AI for SEO' plugin are also applied to the selected SEO plugin(s). This allows you to further analyze SEO results using your favorite SEO plugin.", "ai-for-seo");

            if ($ai4seo_uses_workarounds_for_third_party_seo_plugins) {
                $ai4seo_this_setting_description .= "<br><br>";
                $ai4seo_this_setting_description .= __("<strong>IMPORTANT:</strong> Syncing data to the selected SEO plugins may result in permanent changes to their content. We strongly recommend backing up your data before enabling this feature.", "ai-for-seo");
            }


            // Divider
            echo "<hr class='ai4seo-form-item-divider'>";

            // Form element
            echo "<div class='ai4seo-form-item'>";
                echo "<label for='" . esc_attr($ai4seo_this_setting_input_name) . "'>";
                    echo esc_html__("Sync 'AI for SEO' Changes:", "ai-for-seo");
                echo "</label>";

                echo "<div class='ai4seo-form-item-input-wrapper'>";

                    if ($ai4seo_active_third_party_seo_plugin_details) {
                        // Define variable for the selected user-roles based on plugin-settings
                        $ai4seo_this_checked_values = ($ai4seo_this_setting_input_value && is_array($ai4seo_this_setting_input_value) ? $ai4seo_this_setting_input_value : array());

                        // add a select / un select all checkbox
                        if (count($ai4seo_active_third_party_seo_plugin_details) > 1) {
                            echo ai4seo_wp_kses(ai4seo_get_select_all_checkbox($ai4seo_this_setting_input_name));
                            echo "<div class='ai4seo-medium-gap'></div>";
                        }

                        // Loop through all available user-roles and display checkboxes for each of them
                        foreach ($ai4seo_active_third_party_seo_plugin_details as $ai4seo_this_third_party_seo_plugin_identifier => $ai4seo_this_third_party_seo_plugin_details) {
                            // Determine whether this role is supported
                            $ai4seo_is_this_checkbox_checked = in_array($ai4seo_this_third_party_seo_plugin_identifier, $ai4seo_this_checked_values);
                            $ai4seo_this_checkbox_id = "{$ai4seo_this_setting_input_name}-{$ai4seo_this_third_party_seo_plugin_identifier}";

                            echo "<label for='" . esc_attr($ai4seo_this_checkbox_id) . "' class='ai4seo-form-multiple-inputs'>";
                                echo "<input type='checkbox' id='" . esc_attr($ai4seo_this_checkbox_id) . "' name='" . esc_attr($ai4seo_this_setting_input_name) . "[]' value='" . esc_attr($ai4seo_this_third_party_seo_plugin_identifier) . "'" . ($ai4seo_is_this_checkbox_checked ? " checked='checked'" : "") . "/> ";

                                // Display the icon
                                if (!empty($ai4seo_this_third_party_seo_plugin_details["icon"])) {
                                    $ai4seo_this_icon_css_class = "ai4seo-large-icon";

                                    if (!empty($ai4seo_this_third_party_seo_plugin_details["icon-css-class"])) {
                                        $ai4seo_this_icon_css_class .= " " . $ai4seo_this_third_party_seo_plugin_details["icon-css-class"];
                                    }

                                    echo ai4seo_wp_kses(ai4seo_get_svg_tag($ai4seo_this_third_party_seo_plugin_details["icon"], $ai4seo_this_third_party_seo_plugin_details["mame"] ?? "", $ai4seo_this_icon_css_class)) . " ";
                                }

                                // Display the name
                                echo esc_html($ai4seo_this_third_party_seo_plugin_details["name"] ?? $ai4seo_this_third_party_seo_plugin_identifier);
                                echo "<br>";
                            echo "</label>";
                        }

                        echo "<p class='ai4seo-form-item-description'>";
                            echo ai4seo_wp_kses($ai4seo_this_setting_description);
                        echo "</p>";
                    } else {
                        echo "<i>" . esc_html__("No supported and active third-party SEO plugins found.", "ai-for-seo") . "</i>";
                    }
                echo "</div>";
            echo "</div>";
        }

        // === AI4SEO_SETTING_OVERWRITE_EXISTING_META_TAGS ========================================================== \\

        $ai4seo_this_setting_name = AI4SEO_SETTING_OVERWRITE_EXISTING_METADATA;

        if (in_array($ai4seo_this_setting_name, $ai4seo_my_changeable_settings_names)) {
            $ai4seo_this_setting_input_name = "ai4seo_{$ai4seo_this_setting_name}";
            $ai4seo_this_setting_input_value = ai4seo_get_setting($ai4seo_this_setting_name);
            $ai4seo_this_setting_description = __("You can choose to overwrite existing metadata when using <u>bulk generation</u>. If this option is not selected, only missing metadata will be generated.", "ai-for-seo");
            $ai4seo_this_setting_description .= "<br><br>";
            $ai4seo_this_setting_description .= __("<strong>IMPORTANT:</strong> Existing data, including data from the selected third-party SEO plugins (see 'Sync' settings above), will be permanently overwritten if you choose to overwrite. We strongly recommend backing up your data before proceeding with automatic bulk generation.", "ai-for-seo");

            // Divider
            echo "<hr class='ai4seo-form-item-divider'>";

            // Display form elements
            echo "<div class='ai4seo-form-item'>";
                echo "<label for='" . esc_attr($ai4seo_this_setting_input_name) . "'>";
                echo esc_html__("Overwrite Existing Metadata:", "ai-for-seo") ;
                echo "</label>";

                echo "<div class='ai4seo-form-item-input-wrapper'>";
                    // Define variable for the selected user-roles based on plugin-settings
                    $ai4seo_this_checked_values = ($ai4seo_this_setting_input_value && is_array($ai4seo_this_setting_input_value) ? $ai4seo_this_setting_input_value : array());

                    // add a select / un select all checkbox
                    echo ai4seo_wp_kses(ai4seo_get_select_all_checkbox($ai4seo_this_setting_input_name));
                    echo "<div class='ai4seo-medium-gap'></div>";

                    // Loop through all available user-roles and display checkboxes for each of them
                    foreach ($ai4seo_metadata_details as $ai4seo_this_metadata_identifier => $ai4seo_this_metadata_details) {
                        $ai4seo_this_translated_checkbox_label = $ai4seo_this_metadata_details["name"] ?? $ai4seo_this_metadata_identifier;
                        $ai4seo_this_checkbox_id = "{$ai4seo_this_setting_input_name}-{$ai4seo_this_metadata_identifier}";

                        // Determine whether this role is supported
                        $ai4seo_is_this_checkbox_checked = in_array($ai4seo_this_metadata_identifier, $ai4seo_this_checked_values);

                        echo "<label for='" . esc_attr($ai4seo_this_checkbox_id) . "' class='ai4seo-form-multiple-inputs'>";
                        echo "<input type='checkbox' id='" . esc_attr($ai4seo_this_checkbox_id) . "' name='" . esc_attr($ai4seo_this_setting_input_name) . "[]' value='" . esc_attr($ai4seo_this_metadata_identifier) . "'" . ($ai4seo_is_this_checkbox_checked ? " checked='checked'" : "") . "/> ";
                        echo esc_html($ai4seo_this_translated_checkbox_label);

                        echo "<br>";
                        echo "</label>";
                    }

                    echo "<p class='ai4seo-form-item-description'>";
                        echo ai4seo_wp_kses($ai4seo_this_setting_description);
                    echo "</p>";
                echo "</div>";
            echo "</div>";
        }


        // === AI4SEO_SETTING_META_TAG_OUTPUT_MODE ================================================================================= \\

        $ai4seo_this_setting_name = AI4SEO_SETTING_META_TAG_OUTPUT_MODE;

        if (in_array($ai4seo_this_setting_name, $ai4seo_my_changeable_settings_names)) {
            $ai4seo_this_setting_input_name = "ai4seo_{$ai4seo_this_setting_name}";
            $ai4seo_this_setting_input_value = ai4seo_get_setting($ai4seo_this_setting_name);

            // Divider
            echo "<hr class='ai4seo-form-item-divider'>";

            echo "<div class='ai4seo-form-item'>";
                echo "<label for='" . esc_attr($ai4seo_this_setting_input_name) . "'>";
                    echo esc_html__("Meta Tag Output Mode", "ai-for-seo");
                echo "</label>";
                echo "<div class='ai4seo-form-item-input-wrapper'>";
                    echo "<select id='" . esc_attr($ai4seo_this_setting_input_name) . "' name='" . esc_attr($ai4seo_this_setting_input_name) . "'>";
                        foreach (AI4SEO_SETTING_META_TAG_OUTPUT_MODE_ALLOWED_VALUES AS $ai4seo_this_option_value => $ai4seo_this_option_label) {
                            echo "<option value='" . esc_attr($ai4seo_this_option_value) . "'" . ($ai4seo_this_setting_input_value == $ai4seo_this_option_value ? " selected='selected'" : "") . ">" . esc_html($ai4seo_this_option_label) . "</option>";
                        }
                    echo "</select>";

                    echo "<p class='ai4seo-form-item-description'>";
                        // Disable 'AI for SEO' Meta Tags
                        echo ai4seo_wp_kses(__("<strong>Disable 'AI for SEO' Meta Tags</strong>: This option disables all meta tags generated by the plugin, essentially functioning as if all options in the 'Meta Tag Inclusion' setting were deselected. It is useful when using another SEO plugin while syncing changes to that plugin (see 'Sync Changes' setting), especially if the 'Overwrite Existing Metadata' setting is enabled.", "ai-for-seo")) . "<br><br>";

                        // Force 'AI for SEO' Meta Tags
                        echo ai4seo_wp_kses(__("<strong>Force 'AI for SEO' Meta Tags</strong>: Outputs the meta tags generated by this plugin, regardless of other SEO plugins. May result in duplicate meta tags. Useful when no other SEO plugins are in use or for troubleshooting potential output issues.", "ai-for-seo")) . "<br><br>";

                        // Replace Existing Meta Tags
                        echo ai4seo_wp_kses(__("<strong>Replace Existing Meta Tags</strong>: Recommended. Replaces existing meta tags with 'AI for SEO's tags, placing them inside a single block at the top of the header for better search engine recognition. Helps clean up your HTML header. Prevents duplicate entries in your HTML header. No need to sync AI for SEO data with other SEO plugins.", "ai-for-seo")) . "<br><br>";

                        // Complement Existing Meta Tags
                        echo ai4seo_wp_kses(__("<strong>Complement Existing Meta Tags</strong>: Adds missing meta tags generated by this plugin without overwriting existing ones. Keeps current meta tags intact. Recommended only when syncing and overwriting metadata from other SEO plugins (see settings below).", "ai-for-seo"));
                    echo "</p>";
                echo "</div>";
            echo "</div>";
        }

    echo "</div>";


    // ___________________________________________________________________________________________ \\
    // === MEDIA ATTRIBUTES ====================================================================== \\
    // ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

    echo "<div class='card ai4seo-form-section'>";
        // Headline
        echo "<h2>";
        echo '<i class="dashicons dashicons-admin-media ai4seo-nav-tab-icon"></i>';
        echo esc_html__("Media attributes", "ai-for-seo");
        echo "</h2>";


        // === AI4SEO_SETTING_ATTACHMENT_ATTRIBUTES_GENERATION_LANGUAGE ============================== \\

        $ai4seo_this_setting_name = AI4SEO_SETTING_ATTACHMENT_ATTRIBUTES_GENERATION_LANGUAGE;

        if (in_array($ai4seo_this_setting_name, $ai4seo_my_changeable_settings_names)) {
            $ai4seo_this_setting_input_name = "ai4seo_{$ai4seo_this_setting_name}";
            $ai4seo_this_setting_input_value = ai4seo_get_setting($ai4seo_this_setting_name);
            $ai4seo_this_setting_description = __("If you are targeting a specific language or region, please select the appropriate language. Choose 'Automatic' to let the AI use your WordPress language (%s). Currently, 'AI for SEO' cannot determine the context in which the media is used; therefore, fully automatic language selection, as provided for metadata generation, is not available yet but will be in a future release of this plugin.", "ai-for-seo");
            $ai4seo_this_setting_description = sprintf($ai4seo_this_setting_description, $ai4seo_wordpress_language);

            // Form element
            echo "<div class='ai4seo-form-item'>";
                echo "<label for='" . esc_attr($ai4seo_this_setting_input_name) . "'>";
                    echo esc_html__("Language for Media Attributes Generation", "ai-for-seo") . ":";
                echo "</label>";
                echo "<div class='ai4seo-form-item-input-wrapper'>";
                    echo "<select id='" . esc_attr($ai4seo_this_setting_input_name) . "' name='" . esc_attr($ai4seo_this_setting_input_name) . "'>";
                        echo ai4seo_wp_kses(ai4seo_get_generation_language_select_options_html($ai4seo_this_setting_input_value));
                    echo "</select>";

                    echo "<p class='ai4seo-form-item-description'>";
                        echo ai4seo_wp_kses($ai4seo_this_setting_description);
                    echo "</p>";
                echo "</div>";
            echo "</div>";
        }


        // === AI4SEO_SETTING_OVERWRITE_EXISTING_ATTACHMENT_ATTRIBUTES ========================================================== \\

        $ai4seo_this_setting_name = AI4SEO_SETTING_OVERWRITE_EXISTING_ATTACHMENT_ATTRIBUTES;

        if (in_array($ai4seo_this_setting_name, $ai4seo_my_changeable_settings_names)) {
            $ai4seo_this_setting_input_name = "ai4seo_{$ai4seo_this_setting_name}";
            $ai4seo_this_setting_input_value = ai4seo_get_setting($ai4seo_this_setting_name);
            $ai4seo_this_setting_description = __("You can choose to overwrite existing media attributes when using <u>bulk generation</u>. If this option is not selected, only missing media attributes will be generated.", "ai-for-seo");
            $ai4seo_this_setting_description .= "<br><br>";
            $ai4seo_this_setting_description .= __("<strong>IMPORTANT:</strong> Existing data will be permanently overwritten if you choose to overwrite. We strongly recommend backing up your data before proceeding with automatic bulk generation.", "ai-for-seo");

            // Divider
            echo "<hr class='ai4seo-form-item-divider'>";

            // Display form elements
            echo "<div class='ai4seo-form-item'>";
                echo "<label for='" . esc_attr($ai4seo_this_setting_input_name) . "'>";
                echo esc_html__("Overwrite Existing Media Attributes:", "ai-for-seo") ;
                echo "</label>";

                echo "<div class='ai4seo-form-item-input-wrapper'>";
                    // Define variable for the selected user-roles based on plugin-settings
                    $ai4seo_this_checked_values = ($ai4seo_this_setting_input_value && is_array($ai4seo_this_setting_input_value) ? $ai4seo_this_setting_input_value : array());

                    // add a select / un select all checkbox
                    echo ai4seo_wp_kses(ai4seo_get_select_all_checkbox($ai4seo_this_setting_input_name));
                    echo "<div class='ai4seo-medium-gap'></div>";

                    // Loop through all available user-roles and display checkboxes for each of them
                    foreach ($ai4seo_attachments_attributes_details as $ai4seo_attachment_attribute_name => $ai4seo_attachment_attribute_details) {
                        $ai4seo_this_translated_checkbox_label = $ai4seo_attachment_attribute_details["name"] ?? $ai4seo_attachment_attribute_name;
                        $ai4seo_this_checkbox_id = "{$ai4seo_this_setting_input_name}-{$ai4seo_attachment_attribute_name}";

                        // Determine whether this role is supported
                        $ai4seo_is_this_checkbox_checked = in_array($ai4seo_attachment_attribute_name, $ai4seo_this_checked_values);

                        echo "<label for='" . esc_attr($ai4seo_this_checkbox_id) . "' class='ai4seo-form-multiple-inputs'>";
                        echo "<input type='checkbox' id='" . esc_attr($ai4seo_this_checkbox_id) . "' name='" . esc_attr($ai4seo_this_setting_input_name) . "[]' value='" . esc_attr($ai4seo_attachment_attribute_name) . "'" . ($ai4seo_is_this_checkbox_checked ? " checked='checked'" : "") . "/> ";
                        echo esc_html($ai4seo_this_translated_checkbox_label);

                        echo "<br>";
                        echo "</label>";
                    }

                    echo "<p class='ai4seo-form-item-description'>";
                        echo ai4seo_wp_kses($ai4seo_this_setting_description);
                    echo "</p>";
                echo "</div>";
            echo "</div>";
        }
    echo "</div>";


    // ___________________________________________________________________________________________ \\
    // === USER MANAGEMENT ======================================================================= \\
    // ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

    $ai4seo_this_setting_name = AI4SEO_SETTING_ALLOWED_USER_ROLES;
    
    if (in_array($ai4seo_this_setting_name, $ai4seo_my_changeable_settings_names)) {
        $ai4seo_this_setting_input_name = "ai4seo_{$ai4seo_this_setting_name}";
        $ai4seo_this_setting_input_value = ai4seo_get_setting($ai4seo_this_setting_name);
        $ai4seo_this_setting_description = __("Please select the user roles that should have access to this plugin. Only roles with the 'edit_posts' capability will be listed.", "ai-for-seo");

        echo "<div class='card ai4seo-form-section'>";
            // Headline
            echo "<h2>";
            echo '<i class="dashicons dashicons-admin-users ai4seo-nav-tab-icon"></i>';
            echo esc_html__("User Management", "ai-for-seo");
            echo "</h2>";

            // Display form elements
            echo "<div class='ai4seo-form-item'>";
                echo "<label for='" . esc_attr($ai4seo_this_setting_input_name) . "'>";
                    echo esc_html__("Allowed User Roles:", "ai-for-seo") ;
                echo "</label>";

                echo "<div class='ai4seo-form-item-input-wrapper'>";
                    // Define variable for the selected user-roles based on plugin-settings
                    $ai4seo_this_checked_values = ($ai4seo_this_setting_input_value && is_array($ai4seo_this_setting_input_value) ? $ai4seo_this_setting_input_value : array());

                    // add a select / un select all checkbox
                    echo ai4seo_wp_kses(ai4seo_get_select_all_checkbox($ai4seo_this_setting_input_name));
                    echo "<div class='ai4seo-medium-gap'></div>";

                    // Loop through all available user-roles and display checkboxes for each of them
                    foreach ($ai4seo_allowed_user_roles as $ai4seo_this_user_role_identifier => $ai4seo_this_user_role) {
                        $ai4seo_this_translated_checkbox_label = translate_user_role($ai4seo_this_user_role);

                        if ($ai4seo_this_translated_checkbox_label) {
                            $ai4seo_this_user_role = $ai4seo_this_translated_checkbox_label;
                        }

                        $ai4seo_this_checkbox_id = "{$ai4seo_this_setting_input_name}-{$ai4seo_this_user_role_identifier}";

                        // Determine whether this role is supported
                        $ai4seo_is_this_checkbox_checked = (in_array($ai4seo_this_user_role_identifier, $ai4seo_this_checked_values) || $ai4seo_this_user_role_identifier == "administrator");

                        echo "<label for='" . esc_attr($ai4seo_this_checkbox_id) . "' class='ai4seo-form-multiple-inputs'>";
                            echo "<input type='checkbox' id='" . esc_attr($ai4seo_this_checkbox_id) . "' name='" . esc_attr($ai4seo_this_setting_input_name) . "[]' value='" . esc_attr($ai4seo_this_user_role_identifier) . "'" . ($ai4seo_is_this_checkbox_checked ? " checked='checked'" : "") . ($ai4seo_this_user_role_identifier == "administrator" ? " class='ai4seo-disabled-form-input' disabled='disabled'" : "") . " /> ";
                            echo esc_html($ai4seo_this_user_role);
                            echo "<br>";
                        echo "</label>";
                    }

                    echo "<p class='ai4seo-form-item-description'>";
                        echo ai4seo_wp_kses($ai4seo_this_setting_description);
                    echo "</p>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    }


    // === Container for debugging and collaboration settings ==================================== \\

    /*echo "<div class='card ai4seo-form-section'>";
        // Headline
        echo "<h2>" . esc_html__("Debugging and Collaboration", "ai-for-seo") . "</h2>";

    echo "</div>";*/

    // Submit button
    submit_button("", "primary", "ai4seo-submit");
echo "</form>";