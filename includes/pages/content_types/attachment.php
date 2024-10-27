<?php
/**
 * Renders the content of the submenu media for the "AI for SEO" page.
 *
 * @since 1.2.0
 */

if (!defined("ABSPATH")) {
    exit;
}


// ___________________________________________________________________________________________ \\
// === PREPARE =============================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

global $wpdb;
global $ai4seo_allowed_attachment_mime_types;

$ai4seo_post_type = "attachment";
$ai4seo_nice_post_type = "media";
$ai4seo_current_credits_balance = ai4seo_robhub_api()->get_credits_balance();

$ai4seo_media_label_singular = _n("media", "media", 1, "ai-for-seo");
$ai4seo_media_label_plural = _n("media", "media", 2, "ai-for-seo");
$ai4seo_total_pages = 1;
$ai4seo_current_page = absint($_REQUEST["ai4seo-page"] ?? 1);

if ($ai4seo_current_page < 1) {
    $ai4seo_current_page = 1;
}

// check if the cron job should be executed sooner
if (isset($_GET["ai4seo-execute-cron-job-sooner"]) && $_GET["ai4seo-execute-cron-job-sooner"]) {
    ai4seo_inject_additional_cronjob_call("ai4seo_automated_generation_cron_job", 0);
}

// check if the user wants to reset all failed metadata generation
if (isset($_GET["ai4seo-reset-failed-attachment-attributes-generation"]) && $_GET["ai4seo-reset-failed-attachment-attributes-generation"]) {
    update_option(AI4SEO_FAILED_ATTACHMENT_ATTRIBUTES_POST_IDS, json_encode(array()));
    ai4seo_refresh_all_posts_generation_status_summary();
}


// ___________________________________________________________________________________________ \\
// === READ ================================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

// Prepare arguments for the wp-query
$ai4seo_posts_query_arguments = array(
    "post_status" => array('publish', 'future', 'private', 'pending', 'inherit'),
    "post_type" => $ai4seo_post_type,
    "post_mime_type" => $ai4seo_allowed_attachment_mime_types,
    "posts_per_page" => 20,
    "orderby" => "ID",
    "order" => "DESC",
    "paged" => $ai4seo_current_page,
);

// Fire query
$ai4seo_post_data = new WP_Query($ai4seo_posts_query_arguments);

// Define variable for the posts that were found
$ai4seo_all_attachment_posts = $ai4seo_post_data->posts ?? array();
$ai4seo_total_pages = (int) ($ai4seo_post_data->max_num_pages ?? 1);

unset($ai4seo_post_data);

// fetch all post ids from this page
$ai4seo_current_attachment_post_ids = array_map(function ($ai4seo_attachment_post) {
    return (int) $ai4seo_attachment_post->ID;
}, $ai4seo_all_attachment_posts);


// read attributes coverage
$ai4seo_attachment_attributes_coverage = ai4seo_read_and_analyse_attachment_attributes_coverage($ai4seo_current_attachment_post_ids);
$ai4seo_attachment_attributes_coverage_summary = ai4seo_get_attachment_attributes_coverage_summary($ai4seo_attachment_attributes_coverage);
$ai4seo_num_total_attachment_attributes = ai4seo_get_num_attachment_attributes();

// get value for automation toggle checkbox
$ai4seo_is_automation_activated = ai4seo_is_automated_generation_enabled($ai4seo_post_type);
$ai4seo_is_checked_phrase = ($ai4seo_is_automation_activated ? "checked" : "");

// cronjob
$ai4seo_last_cron_job_call = ai4seo_get_last_cron_job_call_time("ai4seo_automated_generation_cron_job");
$ai4seo_next_cron_job_call = wp_next_scheduled("ai4seo_automated_generation_cron_job");
$ai4seo_last_cron_job_call_diff = ($ai4seo_last_cron_job_call ? time() - $ai4seo_last_cron_job_call : 9999999);
$ai4seo_next_cron_job_call_diff = ($ai4seo_next_cron_job_call ? $ai4seo_next_cron_job_call - time() : 9999999);
$ai4seo_next_cron_job_call_diff_in_minutes = ceil($ai4seo_next_cron_job_call_diff / MINUTE_IN_SECONDS);


// look for attachment post ids that are scheduled by the cron jobs to process attributes
$ai4seo_pending_attributes_attachment_post_ids = ai4seo_get_post_ids_from_option(AI4SEO_PENDING_ATTACHMENT_ATTRIBUTES_POST_IDS);
$ai4seo_processing_attributes_attachment_post_ids = ai4seo_get_post_ids_from_option(AI4SEO_PROCESSING_ATTACHMENT_ATTRIBUTES_POST_IDS);

// look for failed to fill post ids
$ai4seo_failed_attributes_attachment_post_ids = ai4seo_get_post_ids_from_option(AI4SEO_FAILED_ATTACHMENT_ATTRIBUTES_POST_IDS);

// remove entries from $ai4seo_all_failed_to_fill_attributes_attachment_post_ids that are not on this page
$ai4seo_current_page_failed_to_fill_attachment_post_ids = array();

if ($ai4seo_all_attachment_posts) {
    foreach ($ai4seo_all_attachment_posts AS $ai4seo_this_attachment_post) {
        if (in_array($ai4seo_this_attachment_post->ID, $ai4seo_failed_attributes_attachment_post_ids)) {
            $ai4seo_current_page_failed_to_fill_attachment_post_ids[] = $ai4seo_this_attachment_post->ID;
        }
    }
}
$ai4seo_failed_attributes_attachment_post_ids = $ai4seo_current_page_failed_to_fill_attachment_post_ids;

// collect some admin links and buttons
$ai4seo_this_admin_tab_url = ai4seo_get_admin_url($ai4seo_nice_post_type, array("ai4seo-page" => $ai4seo_current_page));
$ai4seo_refresh_text_link = ai4seo_get_small_button_tag($ai4seo_this_admin_tab_url, "rotate", __("Refresh page", "ai-for-seo"));

// execute cron job sooner link
$ai4seo_execute_sooner_text_link_url = ai4seo_get_admin_url($ai4seo_nice_post_type, array("ai4seo-execute-cron-job-sooner" => true, "ai4seo-page" => $ai4seo_current_page));
$ai4seo_execute_sooner_text_link = ai4seo_get_small_button_tag($ai4seo_execute_sooner_text_link_url, "bolt", __("Execute sooner!", "ai-for-seo"));

// Define variable for the label of the failed-attributes-generations-link
$ai4seo_retry_all_failed_attachment_attributes_generations_link_label = "<span class='ai4seo-retry-failed'>" . __("Retry all failed media attributes generations", "ai-for-seo") . "</span><span class='ai4seo-retry-failed-mobile'>" . __("Retry all failed", "ai-for-seo") . "</span>";

// retry all failed attachment attributes generations link
$ai4seo_retry_all_failed_attachment_attributes_generations_link_url = ai4seo_get_admin_url($ai4seo_nice_post_type, array("ai4seo-reset-failed-attachment-attributes-generation" => true, "ai4seo-page" => $ai4seo_current_page));
$ai4seo_retry_all_failed_attachment_attributes_generations_link_tag = ai4seo_get_small_button_tag($ai4seo_retry_all_failed_attachment_attributes_generations_link_url, "rotate", $ai4seo_retry_all_failed_attachment_attributes_generations_link_label);

$ai4seo_consider_purchasing_more_credits_link_url = ai4seo_get_admin_url();
$ai4seo_consider_purchasing_more_credits_link_tag = ai4seo_get_small_button_tag($ai4seo_consider_purchasing_more_credits_link_url, "circle-plus", __("Purchase more credits", "ai-for-seo"), "ai4seo-success-button");



// ___________________________________________________________________________________________ \\
// === OUTPUT ================================================================================ \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

// === OPTION TO TOGGLE AUTOMATION ============================================================ \\

// echo checkbox to toggle automation
echo "<div class='ai4seo-automation-toggle-container'>";
echo "<label for='ai4seo-toggle-automated-generation-checkbox'>";
echo "<input type='checkbox' id='ai4seo-toggle-automated-generation-checkbox' onchange='ai4seo_toggle_automated_generation(\"" . esc_js($ai4seo_post_type) . "\", this.checked)' " . esc_attr($ai4seo_is_checked_phrase) . "/>";
printf(esc_html__("Fill missing media attributes automatically (also when creating new %s).", "ai-for-seo"), esc_html($ai4seo_media_label_plural));
echo " ";
printf(esc_html__("Approximate processing speed: %.1f %s/min.", "ai-for-seo"), esc_html(AI4SEO_APPROXIMATE_ATTACHMENT_ATTRIBUTES_GENERATION_SPEED), esc_html($ai4seo_media_label_plural));

if ($ai4seo_last_cron_job_call && $ai4seo_is_automation_activated) {
    echo " " . esc_html__("Last check", "ai-for-seo") . ": " . date("Y-m-d H:i:s", $ai4seo_last_cron_job_call) . " UTC.";
}

echo "</label>";
echo "</div>";


// === ERROR NOTICE IF THE CRONJOB DID NOT START AT LEAST 10 MINUTES AGO ===================== \\

if ($ai4seo_is_automation_activated && $ai4seo_last_cron_job_call < (time() - MINUTE_IN_SECONDS * 10)) {
    // Your cron jobs do not seem to work properly. Automation is currently not possible. Please check the cronjob settings.
    ai4seo_echo_error_notice(esc_html__("Your cron jobs do not seem to be functioning properly. \"AI for SEO\" automation is currently not possible. Please verify the cron job settings."));
}

// Stop script if no posts have been found -> show message and stop page rendering
if (!$ai4seo_all_attachment_posts) {
    echo '<p>' . esc_html__('No relevant media found.', "ai-for-seo") . '</p>';
}

// Display table with entries
echo "<div class='ai4seo-posts-table-container'>";
echo "<table class='widefat striped table-view-list attachments ai4seo-posts-table'>";
    echo "<tr>";
        echo "<th>" . esc_html__("ID", "ai-for-seo") . "</th>";
        echo "<th></th>";
        echo "<th>" . esc_html__("Title", "ai-for-seo") . "</th>";
        echo "<th>" . esc_html__("SEO Coverage", "ai-for-seo") . " <span style='font-size: smaller'>(" . esc_html__("Alt Text, Title, Caption, Description") . ")</span>";

        // RESET ALL FAILED ATTACHMENT ATTRIBUTES GENERATION
        # todo: do ajax instead of reloading the page
        if (count($ai4seo_failed_attributes_attachment_post_ids) && $ai4seo_is_automation_activated) {
            echo "<div class='ai4seo-table-title-button'>";
            echo ai4seo_wp_kses($ai4seo_retry_all_failed_attachment_attributes_generations_link_tag);
            echo "</div>";
        }

        echo "</th>";
        echo "<th>" . esc_html__("Actions", "ai-for-seo") . "</th>";
    echo "</tr>";

    // Loop through entries and display table-row for each entry
    foreach ($ai4seo_all_attachment_posts as $ai4seo_this_attachment) {
        // Prepare variables
        $ai4seo_this_post_attachment_id = (int) $ai4seo_this_attachment->ID;
        $ai4seo_this_attachment_title = $ai4seo_this_attachment->post_title;
        $ai4seo_this_mime_type = get_post_mime_type($ai4seo_this_post_attachment_id);
        $ai4seo_this_post_link = get_edit_post_link($ai4seo_this_post_attachment_id);

        // this attachment attributes coverage
        $ai4seo_this_attachment_attribute_coverage_summary = $ai4seo_attachment_attributes_coverage_summary[$ai4seo_this_post_attachment_id] ?? 0;
        $ai4seo_this_attachment_attribute_coverage_percentage = round(($ai4seo_this_attachment_attribute_coverage_summary / $ai4seo_num_total_attachment_attributes) * 100, 2);
        $ai4seo_this_attachment_attributes_is_not_covered = ($ai4seo_this_attachment_attribute_coverage_percentage < 100);

        $ai4seo_is_attachment_post_failed = in_array($ai4seo_this_post_attachment_id, $ai4seo_current_page_failed_to_fill_attachment_post_ids);
        // check if filling is in progress
        $ai4seo_is_attachment_post_pending = false;
        $ai4seo_is_attachment_post_waiting_to_get_scheduled = false;
        $ai4seo_is_insufficient_credits = false;
        $ai4seo_is_attachment_post_waiting_for_cron_job = false;

        // if the post is not covered, but pending
        if ($ai4seo_this_attachment_attributes_is_not_covered) {
            if (in_array($ai4seo_this_post_attachment_id, $ai4seo_pending_attributes_attachment_post_ids) || in_array($ai4seo_this_post_attachment_id, $ai4seo_processing_attributes_attachment_post_ids)) {
                $ai4seo_is_attachment_post_pending = true;
            } else if ($ai4seo_is_automation_activated) {
                $ai4seo_is_attachment_post_waiting_to_get_scheduled = true;
            }
        }

        // if the post is not covered and the filling process is not in progress
        if ($ai4seo_is_attachment_post_failed) {
            $ai4seo_is_attachment_post_pending = false;
            $ai4seo_is_attachment_post_waiting_to_get_scheduled = false;
        }

        // if the user has not enough credits to fill the attachment attributes
        if ($ai4seo_is_attachment_post_waiting_to_get_scheduled && $ai4seo_current_credits_balance < AI4SEO_MIN_CREDITS_BALANCE) {
            $ai4seo_is_attachment_post_pending = false;
            $ai4seo_is_attachment_post_waiting_to_get_scheduled = false;
            $ai4seo_is_insufficient_credits = true;
        }

        // if the last cron job call was 10 or more seconds ago, than the filling in progress is a false alert
        if ($ai4seo_is_attachment_post_pending & $ai4seo_last_cron_job_call_diff > 10) {
            $ai4seo_is_attachment_post_pending = false;
            $ai4seo_is_attachment_post_waiting_to_get_scheduled = true;
        }

        // if we are waiting for the cron job to start the next filling process
        if (!$ai4seo_is_attachment_post_pending && $ai4seo_is_attachment_post_waiting_to_get_scheduled && $ai4seo_last_cron_job_call_diff > 60) {
            $ai4seo_is_attachment_post_waiting_for_cron_job = true;
        }

        if (in_array($ai4seo_this_mime_type, array("image/jpeg", "image/png", "image/gif", "image/bmp", "image/webp"))) {
            $ai4seo_preview_image_url = wp_get_attachment_image_url($ai4seo_this_post_attachment_id, array(48, 48));
        } else {
            $ai4seo_preview_image_url = ai4seo_get_assets_images_url("icons/document-question-48x48.png"); # todo: replace with more variants like pdf, doc, etc.
        }

        echo "<tr>";
            // Post-ID
            echo "<td>";
            echo esc_html($ai4seo_this_post_attachment_id);
            echo "</td>";

            // Image or File Preview
            echo "<td class='ai4seo-attachment-list-image-preview'>"; # todo: add css class
                echo "<a href='" . esc_url($ai4seo_this_post_link) . "' target='_blank'>";
                echo "<img src='" . esc_url($ai4seo_preview_image_url) . "' alt='" . esc_html__("No image preview available") . "'/>";
                echo "</a>";
            echo "</td>";

            // Title
            echo "<td class='title column-title has-row-actions column-primary post-title'>";
                echo "<strong>";
                echo "<a href='" . esc_url($ai4seo_this_post_link) . "' target='_blank'>";
                echo esc_html($ai4seo_this_attachment_title);
                echo "</a>";
                echo "</strong>";
            echo "</td>";

            // Generation Coverage
            echo "<td class='ai4seo-generation-coverage'>";
                // output progress bar
                echo "<div id='ai4seo-seo-coverage-progress-bar-" . esc_attr($ai4seo_this_post_attachment_id) . "' class='ai4seo-seo-coverage-progress-bar" . ($ai4seo_is_attachment_post_pending ? " ai4seo-green-animated-progress-bar" : ($ai4seo_is_attachment_post_waiting_to_get_scheduled ? " ai4seo-gray-animated-progress-bar" : "")) . ($ai4seo_this_attachment_attributes_is_not_covered ? " ai4seo-progress-bar-not-finished" : " ai4seo-progress-bar-finished") . "'>";
                echo "<div class='ai4seo-seo-coverage-inner-progress-bar' style='width: " . esc_attr($ai4seo_this_attachment_attribute_coverage_percentage) . "%'></div>";
                echo "</div>";

                if ($ai4seo_is_attachment_post_waiting_to_get_scheduled || $ai4seo_is_attachment_post_waiting_for_cron_job) {
                    if ($ai4seo_is_attachment_post_waiting_for_cron_job) {
                        echo "<div class='ai4seo-sub-info'>" . esc_html__("Pending", "ai-for-seo") . "... ";
                        if ($ai4seo_next_cron_job_call_diff_in_minutes >= 1) {
                            echo sprintf(esc_html__("Task is scheduled to execute in less than %u minute.", "ai-for-seo"), esc_html($ai4seo_next_cron_job_call_diff_in_minutes));
                        } else {
                            echo esc_html__("Task is scheduled to execute any moment.", "ai-for-seo");
                        }

                        // execute sooner link
                        if ($ai4seo_next_cron_job_call_diff_in_minutes >= 2) {
                            echo " " . ai4seo_wp_kses($ai4seo_execute_sooner_text_link);
                        } else {
                            echo " " . ai4seo_wp_kses($ai4seo_refresh_text_link);
                        }

                        echo "</div>";
                    } else {
                        echo "<div class='ai4seo-sub-info'>";
                        echo esc_html__("Pending", "ai-for-seo") . "... ";
                        echo "(" . esc_html__("I'm probably optimizing a different entry.", "ai-for-seo") . ") ";
                        echo ai4seo_wp_kses($ai4seo_refresh_text_link);
                        echo "</div>";
                    }
                } else if ($ai4seo_is_attachment_post_pending) {
                    echo "<div class='ai4seo-sub-info'>";
                    echo esc_html__("Processing", "ai-for-seo") . "... ";
                    echo ai4seo_wp_kses($ai4seo_refresh_text_link);
                    echo "</div>";
                } else if ($ai4seo_is_insufficient_credits) {
                    echo "<div class='ai4seo-sub-info ai4seo-red-message'>";
                    echo esc_html__("Insufficient credits", "ai-for-seo") . ". ";
                    echo ai4seo_wp_kses($ai4seo_consider_purchasing_more_credits_link_tag);
                    echo "</div>";
                } else if ($ai4seo_is_attachment_post_failed && $ai4seo_this_attachment_attributes_is_not_covered) {
                    echo "<div class='ai4seo-seo-data-not-covered-message'>";
                    echo "<span>" . esc_html__("Failed to automatically fill media attributes.", "ai-for-seo") . "</span> ";
                    echo ai4seo_wp_kses(ai4seo_get_small_button_tag("#", "arrow-up-right-from-square", __("Try it manually", "ai-for-seo"), "", "ai4seo_open_attachment_attributes_editor_modal(\"" . esc_html($ai4seo_this_post_attachment_id) . "\");"));
                    echo "</div>";
                }
            echo "</td>";

            // Actions
            echo "<td>";
                echo ai4seo_wp_kses(ai4seo_get_edit_attachment_attributes_button($ai4seo_this_attachment->ID));
            echo "</td>";
        echo "</tr>";
    }
echo "</table>";
echo "</div>";

// Pagination
$ai4seo_pagination_base_argument = ai4seo_get_admin_url($ai4seo_nice_post_type, array("ai4seo-page" => "%#%"));
$ai4seo_pagination_arguments = array(
    "base" => $ai4seo_pagination_base_argument,
    "total" => $ai4seo_total_pages,
    "current" => $ai4seo_current_page,
    "show_all" => false,
    "end_size" => 2,
    "mid_size" => 0,
    "prev_text" => "&larr; " . __("Previous", "ai-for-seo"),
    "next_text" => __("Next", "ai-for-seo") . " &rarr;",
    "add_args" => false,
);

echo "<div class='ai4seo-pagination'>";
    echo wp_kses_post(paginate_links($ai4seo_pagination_arguments));
echo "</div>";