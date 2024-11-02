<?php
/**
 * Renders the content of the submenu page for the AI for SEO overview page.
 *
 * @since 1.0
 */

if (!defined("ABSPATH")) {
    exit;
}

// ___________________________________________________________________________________________ \\
// === PREPARE =============================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

$ai4seo_plugin_name = ai4seo_get_plugins_official_name();
$ai4seo_dashboard_url = ai4seo_get_admin_url("dashboard");
$ai4seo_is_dashboard_url = ai4seo_is_tab_open("dashboard");
$ai4seo_settings_url = ai4seo_get_admin_url("settings");
$ai4seo_attachment_url = ai4seo_get_admin_url("media");
$ai4seo_help_url = ai4seo_get_admin_url("help");

$ai4seo_current_content_tab = ai4seo_get_current_tab();
$ai4seo_current_post_type = ai4seo_get_current_post_type();

$ai4seo_supported_post_types = ai4seo_get_supported_post_types();

$ai4seo_tab_icons_by_post_type = array(
    "default" => '<i class="dashicons dashicons-text-page ai4seo-nav-tab-icon"></i>',
    "page" => '<i class="dashicons dashicons-admin-page ai4seo-nav-tab-icon"></i>',
    "post" => '<i class="dashicons dashicons-admin-post ai4seo-nav-tab-icon"></i>',
    "category" => '<i class="dashicons dashicons-admin-category ai4seo-nav-tab-icon"></i>',
    "product" => '<i class="dashicons dashicons-products ai4seo-nav-tab-icon"></i>',
    "product-category" => '<i class="dashicons dashicons-products ai4seo-nav-tab-icon"></i>',
    "portfolio" => '<i class="dashicons dashicons-portfolio ai4seo-nav-tab-icon"></i>',
    "attachment" => '<i class="dashicons dashicons-admin-media ai4seo-nav-tab-icon"></i>',
    "rss" => '<i class="dashicons dashicons-rss ai4seo-nav-tab-icon"></i>',
    "rss-feed" => '<i class="dashicons dashicons-rss ai4seo-nav-tab-icon"></i>',
    "rss_feed" => '<i class="dashicons dashicons-rss ai4seo-nav-tab-icon"></i>',
);


// === CHECK ROBHUB API COMMUNICATOR ========================================================== \\

if (!ai4seo_robhub_api() instanceof Ai4Seo_RobHubApiCommunicator) {
    echo "<div class='wrap'>";
        ai4seo_echo_error_notice(esc_html__("Could not initialize API communicator. Please contact the plugin developer.", "ai-for-seo") . "#101012523", false);
    echo "</div>";
    return;
}


// ___________________________________________________________________________________________ \\
// === OUTPUT ================================================================================ \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

echo "<div class='wrap'>";

    // Main headline
    echo "<h1 class='ai4seo-headline'>";
        echo "<img src='" . esc_url(ai4seo_get_ai_for_seo_logo_url("64x64")) . "' alt='" . esc_attr($ai4seo_plugin_name) . "' class='ai4seo-logo' />";
        echo esc_html($ai4seo_plugin_name);
        echo " <span class='ai4seo-headline-version-number'>v" . esc_html(AI4SEO_PLUGIN_VERSION_NUMBER) . "</span>";
    echo "</h1>";

    flush();

    // === CHECK FOR NEW TOS ===================================================================== \\

    // set parameter to false, so we definitely don't output anything, if tos was not accepted
    if (ai4seo_does_user_need_to_accept_tos_toc_and_pp()) {
        // show message to accept tos and offer a reload button
        echo "<center>";
            echo "<div class='ai4seo-tos-notice'>";
                echo "<p>" . esc_html__("Please accept our Terms of Service to proceed with using this plugin.", "ai-for-seo") . "</p>";
                echo "<a href='" . esc_url(ai4seo_get_admin_url()) . "' class='button ai4seo-button ai4seo-success-button'>" . esc_html__("Show terms of service", "ai-for-seo") . "</a>";
            echo "</div>";
        echo "</div>";
        return;
    }

    echo "<nav class='nav-tab-wrapper ai4seo-nav-tab-wrapper'>";
        // Dashboard tab
        echo "<a href='" . esc_url($ai4seo_dashboard_url) . "' class='nav-tab ai4seo-nav-tab" . ($ai4seo_is_dashboard_url ? " nav-tab-active ai4seo-nav-tab-active" : "") . "'>";
            echo '<i class="dashicons dashicons-dashboard ai4seo-nav-tab-icon"></i>';
            echo esc_html__("Dashboard", "ai-for-seo");
        echo "</a>";

        // Tabs for supported post-types
        foreach ($ai4seo_supported_post_types AS $ai4seo_post_type) {
            $ai4seo_this_tab_label = ai4seo_get_post_type_translation($ai4seo_post_type, true);
            $ai4seo_this_tab_label = ai4seo_get_nice_label($ai4seo_this_tab_label);
            $ai4seo_this_tab_icon = $ai4seo_tab_icons_by_post_type[$ai4seo_post_type] ?? $ai4seo_tab_icons_by_post_type['default'];
            $ai4seo_is_current_tab = ($ai4seo_current_post_type == $ai4seo_post_type);
            $ai4seo_this_tab_url = ai4seo_get_post_type_url($ai4seo_post_type);

            echo "<a href='" . esc_url($ai4seo_this_tab_url) . "' class='nav-tab ai4seo-nav-tab" . ($ai4seo_is_current_tab ? " nav-tab-active ai4seo-nav-tab-active" : "") . "'>";
                echo ai4seo_wp_kses($ai4seo_this_tab_icon);
                echo esc_html($ai4seo_this_tab_label);
            echo "</a>";
        }

        // Media tab
        echo "<a href='" . esc_url($ai4seo_attachment_url) . "' class='nav-tab ai4seo-nav-tab" . ($ai4seo_current_content_tab == "media" ? " nav-tab-active ai4seo-nav-tab-active" : "") . "'>";
            echo ai4seo_wp_kses($ai4seo_tab_icons_by_post_type["attachment"]);
            echo esc_html(_n("Media", "Media", 2, "ai-for-seo"));
        echo "</a>";

        // Settings tab
        echo "<a href='" . esc_url($ai4seo_settings_url) . "' class='nav-tab ai4seo-nav-tab" . ($ai4seo_current_content_tab == "settings" ? " nav-tab-active ai4seo-nav-tab-active" : "") . "'>";
            echo '<i class="dashicons dashicons-admin-generic ai4seo-nav-tab-icon"></i>';
            echo esc_html__("Settings", "ai-for-seo");
        echo "</a>";

        // Help tab
        echo "<a href='" . esc_url($ai4seo_help_url) . "' class='nav-tab ai4seo-nav-tab" . ($ai4seo_current_content_tab == "help" ? " nav-tab-active ai4seo-nav-tab-active" : "") . "'>";
            echo '<i class="dashicons dashicons-editor-help ai4seo-nav-tab-icon"></i>';
            echo esc_html__("Help", "ai-for-seo");
        echo "</a>";
    echo "</nav>";

    echo "<div class='tab-content ai4seo-tab-content'>";

        switch($ai4seo_current_content_tab) {
            case "":
            case "dashboard":
                require_once(ai4seo_get_includes_pages_path("dashboard.php"));
                break;
            case "settings":
                require_once(ai4seo_get_includes_pages_path("settings.php"));
                break;
            case "post":
                require_once(ai4seo_get_includes_pages_content_types_path("post.php"));
                break;
            case "media":
                require_once(ai4seo_get_includes_pages_content_types_path("attachment.php"));
                break;
            case "help":
                require_once(ai4seo_get_includes_pages_path("help.php"));
                break;
            default:
                return "An error occurred. Please contact the plugin developer. #2406232005";
        }

    echo "</div>";
echo "</div>";