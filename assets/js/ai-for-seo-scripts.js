// Prepare variables
var ai4seo_post_outputs = {};
var ai4seo_consumed_credits = 0;
var ai4seo_remaining_credits = 0;
var ai4seo_selector_mapping = {
    // Yoast elements
    '#yoast-google-preview-title-metabox > div > div > div': {'endpoint': 'meta-title', 'additional_selectors': ["#yoast_wpseo_title"], 'key_by_key': true, "processing-context": "metadata"},
    '#yoast-google-preview-description-metabox > div > div > div': {'endpoint': 'meta-description', 'additional_selectors': ["#yoast_wpseo_metadesc"], 'key_by_key': false, "processing-context": "metadata"},

    '#facebook-title-input-metabox > div > div > div': {'endpoint': 'social-media-title', 'key_by_key': false, "processing-context": "metadata"},
    '#facebook-description-input-metabox > div > div > div': {'endpoint': 'social-media-description', 'additional_selectors': ["#yoast_wpseo_opengraph-description"], 'key_by_key': false, "processing-context": "metadata"},

    '#twitter-title-input-metabox > div > div > div': {'endpoint': 'social-media-title', 'key_by_key': false, "processing-context": "metadata"},
    '#twitter-description-input-metabox > div > div > div': {'endpoint': 'social-media-description', 'additional_selectors': ["#yoast_wpseo_twitter-description"], 'key_by_key': false, "processing-context": "metadata"},

    '#yoast-google-preview-title-modal > div > div > div': {'endpoint': 'meta-title', 'additional_selectors': ["#yoast_wpseo_title"], 'key_by_key': true, "processing-context": "metadata"},
    '#yoast-google-preview-description-modal > div > div > div': {'endpoint': 'meta-description', 'additional_selectors': ["#yoast_wpseo_metadesc"], 'key_by_key': false, "processing-context": "metadata"},

    '#facebook-title-input-modal > div > div > div': {'endpoint': 'social-media-title', 'additional_selectors': ["#yoast_wpseo_opengraph-title"], 'key_by_key': false, "processing-context": "metadata"},
    '#facebook-description-input-modal > div > div > div': {'endpoint': 'social-media-description', 'additional_selectors': ["#yoast_wpseo_opengraph-description"], 'key_by_key': false, "processing-context": "metadata"},

    '#twitter-title-input-modal > div > div > div': {'endpoint': 'social-media-title', 'additional_selectors': ["#yoast_wpseo_twitter-title"], 'key_by_key': false, "processing-context": "metadata"},
    '#twitter-description-input-modal > div > div > div': {'endpoint': 'social-media-description', 'additional_selectors': ["#yoast_wpseo_twitter-description"], 'key_by_key': false, "processing-context": "metadata"},

    // "AI for SEO" Metadata Editor modal-elements
    '#ai4seo-meta-title': {'endpoint': 'meta-title', 'key_by_key': true, "processing-context": "metadata"},
    '#ai4seo-meta-description': {'endpoint': 'meta-description', 'key_by_key': false, "processing-context": "metadata"},

    '#ai4seo-facebook-title': {'endpoint': 'social-media-title', 'key_by_key': false, "processing-context": "metadata"},
    '#ai4seo-facebook-description': {'endpoint': 'social-media-description', 'key_by_key': false, "processing-context": "metadata"},

    '#ai4seo-twitter-title': {'endpoint': 'social-media-title', 'key_by_key': false, "processing-context": "metadata"},
    '#ai4seo-twitter-description': {'endpoint': 'social-media-description', 'key_by_key': false, "processing-context": "metadata"},

    // "AI for SEO" Attachment Attributes Editor modal-elements
    '#ai4seo-attachment-attribute-title': {'endpoint': 'title', 'key_by_key': false, "processing-context": "attachment-attributes"},
    '#ai4seo-attachment-attribute-alt-text': {'endpoint': 'alt-text', 'key_by_key': false, "processing-context": "attachment-attributes"},
    '#ai4seo-attachment-attribute-caption': {'endpoint': 'caption', 'key_by_key': false, "processing-context": "attachment-attributes"},
    '#ai4seo-attachment-attribute-description': {'endpoint': 'description', 'key_by_key': false, "processing-context": "attachment-attributes"},

    // Be-Builder elements
    '.preview-mfn-meta-seo-titleinput': {'endpoint': 'meta-title', 'key_by_key': true, "processing-context": "metadata"},
    '.preview-mfn-meta-seo-descriptioninput': {'endpoint': 'meta-description', 'key_by_key': false, "processing-context": "metadata"},
    'input[name=mfn-meta-seo-title]': {'endpoint': 'meta-title', 'key_by_key': true, "processing-context": "metadata"},
    'input[name=mfn-meta-seo-description]': {'endpoint': 'meta-description', 'key_by_key': false, "processing-context": "metadata"},

    '#social-title-input-modal > div > div > div': {'endpoint': 'social-media-title', 'key_by_key': false, "processing-context": "metadata"},
    '#social-description-input-modal > div > div > div': {'endpoint': 'social-media-description', 'additional_selectors': ["#yoast_wpseo_twitter-description"], 'key_by_key': false, "processing-context": "metadata"},

    '#x-title-input-modal > div > div > div': {'endpoint': 'social-media-title', 'key_by_key': false, "processing-context": "metadata"},
    '#x-description-input-modal > div > div > div': {'endpoint': 'social-media-description', 'additional_selectors': ["#yoast_wpseo_twitter-description"], 'key_by_key': false, "processing-context": "metadata"},

    // Attachments
    '.post-type-attachment #title[name=post_title]': {'endpoint': 'title', 'key_by_key': false, 'css-class': 'ai4seo-attachment-generate-attributes-button', "processing-context": "attachment-attributes"},
    '.post-type-attachment #attachment_alt[name=_wp_attachment_image_alt]': {'endpoint': 'alt-text', 'key_by_key': false, 'css-class': 'ai4seo-attachment-generate-attributes-button', "processing-context": "attachment-attributes"},
    '.post-type-attachment #attachment_caption[name=excerpt]': {'endpoint': 'caption', 'key_by_key': false, 'css-class': 'ai4seo-attachment-generate-attributes-button', "processing-context": "attachment-attributes"},
    '.post-type-attachment #attachment_content[name=content]': {'endpoint': 'description', 'key_by_key': false, 'css-class': 'ai4seo-attachment-generate-attributes-button', "processing-context": "attachment-attributes"},
    '.attachment-info .setting #attachment-details-two-column-alt-text': {'endpoint': 'alt-text', 'key_by_key': false, 'css-class': 'ai4seo-attachment-generate-attributes-button', "processing-context": "attachment-attributes"},
    '.attachment-info .setting #attachment-details-two-column-title': {'endpoint': 'title', 'key_by_key': false, 'css-class': 'ai4seo-attachment-generate-attributes-button', "processing-context": "attachment-attributes"},
    '.attachment-info .setting #attachment-details-two-column-caption': {'endpoint': 'caption', 'key_by_key': false, 'css-class': 'ai4seo-attachment-generate-attributes-button', "processing-context": "attachment-attributes"},
    '.attachment-info .setting #attachment-details-two-column-description': {'endpoint': 'description', 'key_by_key': false, 'css-class': 'ai4seo-attachment-generate-attributes-button', "processing-context": "attachment-attributes"},
};

var ai4seo_content_containers = [
    ".wp-block-post-title", ".editor-post-excerpt__textarea textarea", ".wp-block-paragraph", // Gutenberg
    "header h1.title", ".elementor-widget-container", ".item-preview-content", // Elementor
    ".mce-content-body", ".mcb-wrap-inner", ".the_content_wrapper", // Be-Builder
    "#titlediv > #titlewrap > input", ".wp-editor-area", // WooCommerce products
];

let ai4seo_generate_all_button_selectors = {
    "metadata": ["#wpseo-metabox-root", "#ai4seo-generate-all-metadata-button-hook"],
    "attachment-attributes": [".edit-attachment-frame .media-frame-content .attachment-info .details", ".post-type-attachment .wp_attachment_details.edit-form-section", "#ai4seo-generate-all-attachment-attributes-button-hook"],
}

var ai4seo_error_codes_and_messages = {
    "12127323": wp.i18n.__("Could not initialize connection to AI for SEO server. Please contact the plugin developer.", "ai-for-seo"),
    "13127323": wp.i18n.__("Could not initialize AI for SEO server credentials. Please check your settings or contact the plugin developer.", "ai-for-seo"),
    "21127323": wp.i18n.__("Could not read post content.", "ai-for-seo"),
    "22127323": wp.i18n.__("Posts content is empty.", "ai-for-seo"),
    "351229323": wp.i18n.__("Posts content is empty.", "ai-for-seo"),
    "491320823": wp.i18n.__("Posts content is too short.", "ai-for-seo"),
    "54155424": wp.i18n.__("Posts content is too short.", "ai-for-seo"),
    "28127323": wp.i18n.__("Could not execute API call.", "ai-for-seo"),
    "31127323": wp.i18n.__("AI for SEO server call did not return a success value. Please try again.", "ai-for-seo"),
    "47127323": wp.i18n.__("AI for SEO server call returned an invalid success value. Please try again.", "ai-for-seo"),
    "48127323": wp.i18n.__("AI for SEO server call did not return data. Please try again.", "ai-for-seo"),
    "49127323": wp.i18n.__("AI for SEO server call returned an empty data array. Please try again.", "ai-for-seo"),
    "50127323": wp.i18n.__("AI for SEO server call did not return consumed credits. Please try again.", "ai-for-seo"),
    "51127323": wp.i18n.__("AI for SEO server call did not return new credits balance. Please try again.", "ai-for-seo"),
    "52127323": wp.i18n.__("AI for SEO server call returned an invalid data array. Please try again.", "ai-for-seo"),
    "291215624": wp.i18n.__("AI for SEO server call returned an invalid data array. Please try again.", "ai-for-seo"),
    "301215624": wp.i18n.__("AI for SEO server call returned an invalid data array. Please try again.", "ai-for-seo"),
    "311215624": wp.i18n.__("AI for SEO server call returned an invalid data array. Please try again.", "ai-for-seo"),
    "1115424": wp.i18n.__("Your AI for SEO account does not contain sufficient credits. Please add credits to your account.", "ai-for-seo") + "<br /><br /><a href='/wp-admin/admin.php?page=ai-for-seo' target='_blank'>" + wp.i18n.__("Click here to add credits", "ai-for-seo") + "</a>",
};

var ai4seo_robhub_api_response_error_codes = [32127323, 18197323, 311823824];

var ai4seo_robhub_api_response_error_codes_and_messages = {
    "client secret is invalid. Api-Error-Code: 351816823": wp.i18n.__("Could not initialize AI for SEO server credentials. Please check your settings or contact the plugin developer.", "ai-for-seo"),
    "client is not active. Api-Error-Code: 361816823": wp.i18n.__("Could not initialize AI for SEO server credentials. Please check your settings or contact the plugin developer.", "ai-for-seo"),
    "could not create client. Api-Error-Code: 571931823": wp.i18n.__("Could not initialize AI for SEO server credentials. Please check your settings or contact the plugin developer.", "ai-for-seo"),
    ": client not found. Api-Error-Code: 581931823": wp.i18n.__("Could not initialize AI for SEO server credentials. Please check your settings or contact the plugin developer.", "ai-for-seo"),
    "client has insufficient credits": wp.i18n.__("Your AI for SEO account does not contain sufficient credits. Please add credits to your account.", "ai-for-seo") + "<br /><br /><a href='/wp-admin/admin.php?page=ai-for-seo' target='_blank'>" + wp.i18n.__("Click here to add credits", "ai-for-seo") + "</a>",
    "No credits left. Please buy more credits.": wp.i18n.__("Your AI for SEO account does not contain sufficient credits. Please add credits to your account.", "ai-for-seo") + "<br /><br /><a href='/wp-admin/admin.php?page=ai-for-seo' target='_blank'>" + wp.i18n.__("Click here to add credits", "ai-for-seo") + "</a>",
    "Too Many Requests. Api-Error-Code: 381816823": wp.i18n.__("Maximum number of requests reached. Please try again later.", "ai-for-seo"),
    "Too Many Requests. Api-Error-Code: 591931823": wp.i18n.__("Maximum number of requests reached. Please try again later.", "ai-for-seo"),
    "input parameter is too short": wp.i18n.__("The provided content length insufficient for optimal SEO performance.", "ai-for-seo"),
    "We detected inappropriate content": wp.i18n.__("The provided post or media file contains inappropriate content. Please adjust your content and try again.", "ai-for-seo"),
    "client blocked from using this service": wp.i18n.__("Your AI for SEO account has been blocked from using this service due to suspicious activity. Please contact the plugin developer if you believe this is an error.", "ai-for-seo"),
};

var ai4seo_context = ai4seo_get_context();

var ai4seo_click_function_containers = [
    "#yoast-google-preview-modal-open-button",
    "#yoast-facebook-preview-modal-open-button",
    "#yoast-twitter-preview-modal-open-button",

    "#yoast-search-appearance-modal-open-button",
    "#yoast-social-appearance-modal-open-button",
    ".sc-gKPRtg",
    ".attachment-preview > .thumbnail",
    ".media-modal .edit-media-header button.left.dashicons",
    ".media-modal .edit-media-header button.right.dashicons",
];

var ai4seo_version_number = ai4seo_get_version_number();

var ai4seo_js_file_path = ai4seo_get_ai4seo_plugin_directory_url() + "/assets/js/ai-for-seo-scripts.js?ver=" + ai4seo_version_number;
var ai4seo_js_file_id = "ai-for-seo-scripts-js";

var ai4seo_css_file_path = ai4seo_get_ai4seo_plugin_directory_url() + "/assets/css/ai-for-seo-styles.css?ver=" + ai4seo_version_number;
var ai4seo_css_file_id = "ai-for-seo-styles-css";

var ai4seo_admin_ajax_url = ai4seo_get_full_domain() + "/wp-admin/admin-ajax.php";

var ai4seo_just_clicked_modal_wrapper = false;
var ai4seo_min_content_length = 75;

var ai4seo_supported_mime_types = ["image/jpeg", "JPEG", "image/jpg", "JPG", "image/png", "PNG", "image/gif", "GIF", "image/bmp", "BMP", "image/webp", "WEBP"];

var ai4seo_attachment_mime_type_selectors = [".media-frame-content .attachment-info .details .file-type", "#minor-publishing #misc-publishing-actions .misc-pub-filetype"];

// allowed ajax function (also change in ai-for-seo.php file)
let ai4seo_allowed_ajax_actions = [
    "ai4seo_show_metadata_editor", "ai4seo_show_attachment_attributes_editor",
    "ai4seo_generate_metadata", "ai4seo_generate_attachment_attributes",
    "ai4seo_save_metadata_editor_values", "ai4seo_save_attachment_attributes_editor_values",
    "ai4seo_decline_tos", "ai4seo_accept_tos", "ai4seo_show_terms_of_service"
];


// ___________________________________________________________________________________________ \\
// === INIT ================================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

if (typeof jQuery === 'function') {
    // Call above function for each editor element
    jQuery(document).ready(function(){
        /**
         * Initialize page load time
         */
        if (typeof window.ai4seo_page_load_time === 'undefined') {
            window.ai4seo_page_load_time = Date.now();
        }

        setTimeout(function() {
            // init html element
            ai4seo_init_html_elements();
        }, 100);

        // Init html elements within the media-modal
        ai4seo_init_html_elements_for_media_modal()

        // Add click-functions to parent-window for ai4seo_click_function_containers-elements if they exist
        if (ai4seo_click_function_containers.length > 0) {
            // Loop through all click-function-containers
            for (var i = 0; i < ai4seo_click_function_containers.length; i++) {
                // Add click-function to parent-window
                jQuery("body", window.parent.document).on("click", ai4seo_click_function_containers[i], function() {
                    setTimeout(function() {
                        // Call function to load js-file to main-window
                        ai4seo_load_js_file(ai4seo_js_file_path, ai4seo_js_file_id);

                        // Call function to load css-file to main-window
                        ai4seo_load_css_file(ai4seo_css_file_path, ai4seo_css_file_id);

                        // Call function to load ai4seo_localization-object to main-window
                        ai4seo_load_localization();

                        // Init buttons
                        setTimeout(function() {
                            ai4seo_init_html_elements();
                        }, 200);
                    }, 100);
                });
            }
        }

        // Add click-function to be-builder-page-options-button
        jQuery("body", window.parent.document).on("click", "#page-options-tab", function() {
            setTimeout(function() {
                // Call function to load js-file to main-window
                ai4seo_load_js_file(ai4seo_js_file_path, ai4seo_js_file_id);

                // Call function to load css-file to main-window
                ai4seo_load_css_file(ai4seo_css_file_path, ai4seo_css_file_id);

                // Call function to load ai4seo_localization-object to main-window
                ai4seo_load_localization();

                // Init buttons
                setTimeout(function() {
                    ai4seo_init_html_elements();
                }, 200);
            }, 100);
        });

        // Add click-function to elementor-panel-header-menu-button
        jQuery("body", window.parent.document).on("click", "#elementor-panel-header-menu-button", function() {
            setTimeout(function() {
                // Call function to load js-file to main-window
                ai4seo_load_js_file(ai4seo_js_file_path, ai4seo_js_file_id);

                // Call function to load css-file to main-window
                ai4seo_load_css_file(ai4seo_css_file_path, ai4seo_css_file_id);

                // Call function to load ai4seo_localization-object to main-window
                ai4seo_load_localization();

                // Init buttons
                setTimeout(function() {
                    ai4seo_init_html_elements();
                }, 200);
            }, 100);
        });
    });
}

// =========================================================================================== \\

function ai4seo_load_js_file(url, script_id = false, callback = false) {
    // Stop script if no url is given
    if (!url) {
        return;
    }

    // Check if script is already loaded
    if (ai4seo_exists("#" + script_id)) {
        return;
    }

    // Define variable for the script-element
    var script = window.top.document.createElement("script");

    // Set type-attribute for the script-element
    script.type = "text/javascript";

    // Set src-attribute for the script-element
    script.src = url;

    // Set id-attribute for the script-element if an id is given
    if (script_id) {
        script.id = script_id;
    }

    // Add callback-function to the script-element if a callback is needed after the script is loaded
    if (callback) {
        script.onload = callback;
    }

    // Add script-element to the head-element of the parent window
    window.top.document.head.appendChild(script);
}

// =========================================================================================== \\

function ai4seo_load_css_file(url, script_id = false, callback = false) {
    // Stop script if no url is given
    if (!url) {
        return;
    }

    // Check if script is already loaded
    if (ai4seo_exists("#" + script_id)) {
        return;
    }

    // Define variable for the link-element
    var link = window.top.document.createElement("link");

    // Set type-attribute for the link-element
    link.type = "text/css";

    // Set rel-attribute for the link-element
    link.rel = "stylesheet";

    // Set href-attribute for the link-element
    link.href = url;

    // Set media-attribute for the link-element
    link.media = "all";

    // Set id-attribute for the link-element if an id is given
    if (script_id) {
        link.id = script_id;
    }

    // Add callback-function to the link-element if a callback is needed after the link is loaded
    if (callback) {
        link.onload = callback;
    }

    // Add link-element to the head-element of the parent window
    window.top.document.head.appendChild(link);
}

// =========================================================================================== \\

function ai4seo_load_localization() {
    // check if ai4seo_localization exists -> should be defined through wp_localize_script
    if (typeof ai4seo_localization === "undefined") {
        return;
    }
    window.top.ai4seo_localization = ai4seo_localization;
}

// =========================================================================================== \\

function ai4seo_init_html_elements() {
    // Add tooltip functionality
    ai4seo_init_tooltips();

    // Add countdown functionality
    ai4seo_init_countdown_elements();

    // Add select all / unselect all checkbox functionality
    ai4seo_init_select_all_checkboxes();

    // init modals
    ai4seo_init_modals();

    if (ai4seo_does_user_need_to_accept_tos_toc_and_pp()) {
        // stop script if user needs to accept TOS, TOC and PP
        return;
    }

    // Init 'Generate with AI' buttons
    ai4seo_init_generate_buttons();

    // Add 'Generate all with AI' buttons
    ai4seo_init_generate_all_button();

    // Add open-layer-button to edit-page-header
    ai4seo_add_open_edit_metadata_modal_button_to_edit_page_header();

    // Add open-layer-button to be-builder-navigation
    ai4seo_add_open_edit_metadata_modal_button_to_be_builder_navigation();

    // Add open-layer-button to elementor-navigation
    ai4seo_add_open_edit_metadata_modal_button_to_elementor_navigation();
}

// =========================================================================================== \\

function ai4seo_init_html_elements_for_media_modal() {
    // Prepare variables
    var max_attempts = 10;
    var attempts = 0;
    var interval = 500;

    function ai4seo_check_visibility() {
        attempts++;

        // Check if the media-modal-element is visible
        if (jQuery(".media-modal.wp-core-ui").length) {
            // Call function to init html elements
            ai4seo_init_html_elements();
            return;
        }

        // Stop function if the maximum number of attempts has been reached
        if (attempts >= max_attempts) {
            return;
        }

        // Continue checking after the specified interval
        setTimeout(ai4seo_check_visibility, interval);
    }

    // Start the checking process
    ai4seo_check_visibility();
}

// =========================================================================================== \\

function ai4seo_init_modals() {
    document.querySelectorAll('.ai4seo-modal-wrapper').forEach(function(element) {
        element.addEventListener('click', function(event) {
            ai4seo_close_modal_on_outside_click(event);
        });
    });

    // Code to move ai4seo-layer-code from iframe to body of parent window
    const ai4seo_ajax_modal_wrapper_selector = document.querySelector('#ai4seo-ajax-modal-wrapper');
    if (ai4seo_ajax_modal_wrapper_selector && window.top !== window.self) {
        ai4seo_ajax_modal_wrapper_selector.style.display = "none";
        window.top.document.body.appendChild(ai4seo_ajax_modal_wrapper_selector.cloneNode(true));
        ai4seo_ajax_modal_wrapper_selector.remove();
    }

    // Code to move ai4seo-layer-code from iframe to body of parent window
    const ai4seo_notification_modal_wrapper_selector = document.querySelector('.ai4seo-notification-modal-wrapper');
    if (ai4seo_notification_modal_wrapper_selector && window.top !== window.self) {
        window.top.document.body.appendChild(ai4seo_notification_modal_wrapper_selector.cloneNode(true));
        ai4seo_notification_modal_wrapper_selector.remove();
    }

    // Add mousedown-function for the ai4seo-metadata-editor-modal in order to only close on outside click
    jQuery("#ai4seo-modal-wrapper", window.parent.document).mousedown(function(event) {
        ai4seo_just_clicked_modal_wrapper = jQuery(event.target, window.parent.document);
    });
}

// =========================================================================================== \\

function ai4seo_init_generate_buttons() {
    // Check if current page is attachment-page
    if (ai4seo_is_attachment_post_type()) {
        // Stop script if the current attachment doesn't contain supported mime type
        if (!ai4seo_is_attachment_mime_type_supported()) {
            return;
        }
    }

    if (ai4seo_exists(".ai4seo-generate-button")) {
        ai4seo_jQuery(".ai4seo-generate-button").remove();
    }

    // Loop through mapping and call function to add button-element
    jQuery.each(ai4seo_selector_mapping, function(selector, value) {
        // check if a jquery element exists for the selector
        if (!ai4seo_exists(selector)) {
            return;
        }

        // YOAST SEO INTEGRATION
        // Call function to add button to yoast-seo-input-label if element is yoast-element
        if (ai4seo_is_yoast_element(selector)) {
            ai4seo_add_link_element_to_yoast_seo_input_label(selector);
        }

        // Call function to add button to input-element if element is other element
        else {
            ai4seo_add_generate_button_to_input(selector);
        }
    });
}

// =========================================================================================== \\

function ai4seo_is_attachment_post_type() {
    return jQuery("body").hasClass("post-type-attachment");
}

// =========================================================================================== \\

function ai4seo_is_attachment_mime_type_supported() {
    // Define boolean to determine whether supported mime-type has been found
    var has_supported_mime_type = false;

    // Loop through attachment-mime-type-selector-elements
    jQuery.each(ai4seo_attachment_mime_type_selectors, function(key, selector) {
        if (!ai4seo_exists(selector)) {
            return;
        }

        // Make sure that mime-type-selector is jQuery-element
        selector = ai4seo_jQuery(selector);

        // Check if this selector-element exists on the current page
        if (selector.length) {
            // Get the content of the selector
            var selector_content = selector.text();

            // Skip this entry if this selector doesn't have any content
            if (!selector_content) {
                return;
            }

            // Loop through ai4seo_supported_mime_types and check if mime-type exists in selector-content
            jQuery.each(ai4seo_supported_mime_types, function(mimeTypeKey, mimeTypeValue) {
                if (selector_content.indexOf(mimeTypeValue) > -1) {
                    has_supported_mime_type = true;
                }
            });
        }
    });

    return has_supported_mime_type;
}

// =========================================================================================== \\

/**
 * Init all our tooltips on this page
 */
function ai4seo_init_tooltips() {
    if (typeof jQuery !== 'function') {
        return;
    }

    let ai4seo_tooltip_holder = jQuery('.ai4seo-tooltip-holder');

    // add tooltips functionality
    ai4seo_tooltip_holder.hover(
        function (event) {
            let ai4seo_tooltip = jQuery(this).find('.ai4seo-tooltip');
            ai4seo_show_tooltip(ai4seo_tooltip, event);
        },
        function () {
            jQuery(this).find('.ai4seo-tooltip').fadeOut(200);
        }
    );

    ai4seo_tooltip_holder.click(function (event) {
        event.stopPropagation(); // Prevent the event from propagating to the document
        let ai4seo_tooltip = jQuery(this).find('.ai4seo-tooltip');
        jQuery('.ai4seo-tooltip').hide(); // Hide other tooltips

        if (ai4seo_tooltip.is(':visible')) {
            ai4seo_hide_tooltip(ai4seo_tooltip);
        } else {
            ai4seo_show_tooltip(ai4seo_tooltip, event);
        }
    });

    // Click event on the document to close all tooltips
    jQuery(document).click(function () {
        jQuery('.ai4seo-tooltip').hide(); // Hide all tooltips
    });
}

// =========================================================================================== \\

/**
 * Init all our "ai4seo-countdown" elements
 */
function ai4seo_init_countdown_elements() {
    jQuery(".ai4seo-countdown").each(function() {
        ai4seo_init_countdown(jQuery(this));
    });
}

// =========================================================================================== \\

/**
 * Apply a continuous countdown to the given element
 */
function ai4seo_init_countdown(element) {
    if (typeof jQuery !== 'function') {
        return;
    }

    let time_text = element.text(); // Get time as string hh:mm:ss
    let total_seconds = ai4seo_parse_time(time_text);

    if (isNaN(total_seconds) || total_seconds <= 0) {
        return;
    }

    let interval = setInterval(function () {
        total_seconds--;

        if (total_seconds <= 0) {
            clearInterval(interval);
            element.text('00:00:00');

            // Check if page has been open for at least 10 seconds
            let time_since_load = Date.now() - window.ai4seo_page_load_time;
            if (time_since_load >= 10000) { // 10000 milliseconds = 10 seconds
                let trigger_function_name = element.data('trigger');
                if (typeof window[trigger_function_name] === 'function') {
                    window[trigger_function_name]();
                }
            }
        } else {
            let time_str = ai4seo_format_time(total_seconds);
            element.text(time_str);
        }
    }, 1000);
}

// =========================================================================================== \\

/**
 * Parse a time string in hh:mm:ss format into total seconds
 */
function ai4seo_parse_time(time_text) {
    let parts = time_text.split(':');
    if (parts.length !== 3) {
        return NaN;
    }
    let hours = parseInt(parts[0], 10);
    let minutes = parseInt(parts[1], 10);
    let seconds = parseInt(parts[2], 10);

    if (isNaN(hours) || isNaN(minutes) || isNaN(seconds)) {
        return NaN;
    }

    return hours * 3600 + minutes * 60 + seconds;
}

// =========================================================================================== \\

/**
 * Format total seconds into a time string hh:mm:ss
 */
function ai4seo_format_time(total_seconds) {
    let hours = Math.floor(total_seconds / 3600);
    let minutes = Math.floor((total_seconds % 3600) / 60);
    let seconds = total_seconds % 60;

    return (
        String(hours).padStart(2, '0') +
        ':' +
        String(minutes).padStart(2, '0') +
        ':' +
        String(seconds).padStart(2, '0')
    );
}

// =========================================================================================== \\

/**
 * Init all our select all / unselect all checkboxes
 */
function ai4seo_init_select_all_checkboxes() {
    // pre-check any select all checkbox, depending on the state of the checkboxes it controls (only if all child  checkboxes are checked, then the select all checkbox is checked)
    jQuery('.ai4seo-select-all-checkbox').each(function() {
        const this_select_all_checkbox_element = jQuery(this);

        const target_checkbox_name = this_select_all_checkbox_element.data('target');

        // if no target-checkbox-name is set, then skip this element
        if (!target_checkbox_name) {
            console.log('AI4SEO: No target-checkbox-name found for select-all-checkbox');
            return;
        }

        const all_target_checkbox_elements = jQuery("input[type='checkbox'][name='" + target_checkbox_name + "[]']:not(:disabled)");

        // if no target-checkbox-elements are found, then skip this element
        if (!ai4seo_exists(all_target_checkbox_elements)) {
            console.log('AI4SEO: No target-checkbox-elements found for select-all-checkbox with target-checkbox-name: ' + target_checkbox_name);
            return;
        }

        // refresh the current state of the select all / unselect all checkbox
        ai4seo_refresh_select_all_checkbox_state(this_select_all_checkbox_element, all_target_checkbox_elements);

        // add change event to all target-checkbox-elements
        this_select_all_checkbox_element.on('change', function() {
            // Get the checked status of the "Select All / Unselect All" checkbox
            const is_checked = jQuery(this).prop('checked');

            // Get all checkboxes with the specified name and apply the checked status
            all_target_checkbox_elements.prop('checked', is_checked);
        });

        // add change event to all target-checkbox-elements to refresh the state of the select all / unselect all checkbox
        all_target_checkbox_elements.on('change', function() {
            ai4seo_refresh_select_all_checkbox_state(this_select_all_checkbox_element, all_target_checkbox_elements);
        });
    });
}

// =========================================================================================== \\

/**
 * Refresh the current state of the select all / unselect all checkbox
 */
function ai4seo_refresh_select_all_checkbox_state(select_all_checkbox_element, all_target_checkbox_elements) {
    // set the initial state of the select all checkbox
    const num_checked_target_checkbox_elements = all_target_checkbox_elements.filter(':checked').length;
    const num_all_target_checkbox_elements = all_target_checkbox_elements.length;

    // if there are more checked checkboxes, than unchecked checkboxes, then the "select all checkbox" is checked as well
    select_all_checkbox_element.prop('checked', num_all_target_checkbox_elements - (num_checked_target_checkbox_elements * 2) < 0);
}


// ___________________________________________________________________________________________ \\
// === ELEMENTS ============================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

// Function to show tooltip based on its position relative to the screen
function ai4seo_show_tooltip(ai4seo_tooltip, event) {
    var screen_width = jQuery(window).width();
    var screen_height = jQuery(window).height();
    var mouse_x = event.pageX;
    var mouse_y = event.pageY;
    var tooltip_width = ai4seo_tooltip.outerWidth();
    var tooltip_height = ai4seo_tooltip.outerHeight();
    var tooltip_half_width = tooltip_width / 2;
    var tooltip_top = mouse_y + 10; // 10px offset from mouse pointer
    var tooltip_bottom = screen_height - mouse_y + 10; // 10px offset from mouse pointer
    var vertical_buffer_zone = 30;
    var horizontal_buffer_zone = 30;
    var scroll_height = jQuery(window).scrollTop();
    var relative_mouse_y = mouse_y - scroll_height;
    var tooltip_buffer_zoned_half_width = tooltip_half_width + horizontal_buffer_zone;

    // Calculate left position ensuring tooltip doesn't go out of bounds
    var left_position = 0;

    // tooltip is overlapping with left screen border
    if (mouse_x - tooltip_half_width < 0) {
        left_position = tooltip_half_width - (mouse_x - horizontal_buffer_zone);

    // tooltip is overlapping with right screen border
    } else if (mouse_x + tooltip_half_width > screen_width) {
        left_position = -tooltip_half_width + (screen_width - mouse_x - horizontal_buffer_zone);
    }

    // check if ai4seo_tooltip is inside a modal (ai4seo-ajax-modal) -> apply workarounds
    var ajax_modal = ai4seo_tooltip.closest('#ai4seo-ajax-modal');

    if (ai4seo_exists(ajax_modal)) {
        // modal left position
        var modal_left_position = ajax_modal.offset().left;
        var modal_right_position = modal_left_position + ajax_modal.outerWidth();
        var modal_padding_left = parseInt(ajax_modal.css('padding-left').replace('px', ''));
        var modal_padding_right = parseInt(ajax_modal.css('padding-right').replace('px', ''));
        var mouse_distance_to_left_modal_border = mouse_x - modal_left_position;
        var mouse_distance_to_right_modal_border = modal_right_position - mouse_x;

        // if mouse position is too close to modal left border, move tooltip on the right
        if (mouse_distance_to_left_modal_border < tooltip_buffer_zoned_half_width) {
            left_position += (tooltip_buffer_zoned_half_width - mouse_distance_to_left_modal_border);
        }

        // if mouse position is too close to modal right border, move tooltip on the left
        if (mouse_distance_to_right_modal_border < tooltip_buffer_zoned_half_width) {
            left_position -= (tooltip_buffer_zoned_half_width - mouse_distance_to_right_modal_border);
        }
    }

    // tooltip is overlapping with top screen border
    if (relative_mouse_y <= vertical_buffer_zone + tooltip_height) {
        // Enough space below, show tooltip below
        ai4seo_tooltip.css({
            top: '100%',
            bottom: 'auto',
            left: left_position + 'px',
            marginTop: '10px',
            marginBottom: '0',
            transform: 'translateX(-50%)'
        });
        ai4seo_tooltip.find('::after').css({
            top: '100%',
            bottom: 'auto',
            transform: 'translateX(-50%)'
        });
    } else {
        // tooltip is overlapping with bottom screen border or all other cases
        ai4seo_tooltip.css({
            top: 'auto',
            bottom: '100%',
            left: left_position + 'px',
            marginBottom: '10px',
            marginTop: '0',
            transform: 'translateX(-50%)'
        });
        ai4seo_tooltip.find('::after').css({
            top: 'auto',
            bottom: '100%',
            transform: 'translateX(-50%)'
        });
    }


    ai4seo_tooltip.fadeIn(100);
}

function ai4seo_hide_tooltip(ai4seo_tooltip) {
    ai4seo_tooltip.fadeOut(100);
}


// ___________________________________________________________________________________________ \\
// === HELPER FUNCTIONS ====================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

function ai4seo_get_input_val(element) {
    element = ai4seo_jQuery(element);

    // Make sure that element can be found
    if (!element.length) {
        return false;
    }

    // Check if element is input-field
    if (element.is("input")) {
        return element.val();
    }

    // Check if element is textarea
    else if (element.is("textarea")) {
        return element.val();
    }

    // Check if element is select
    else if (element.is("select")) {
        return element.find('option').filter(':selected').val();
    }

    // check if element is a div or a span
    else if (element.is("div") || element.is("span")) {
        return element.text();
    }
}

// =========================================================================================== \\

function ai4seo_array_unique(array){
    return array.filter(function(el, index, arr) {
        return index === arr.indexOf(el);
    });
}

// =========================================================================================== \\

function ai4seo_jQuery(selector, context) {
    if (!selector) {
        return null;
    }

    if (!context) {
        context = window.parent.document;
    }

    let jquery_object = jQuery(selector, context);

    // console.log if no jquery_object could be found
    if (jquery_object.length === 0) {
        console.log("No jquery object found for selector: " + selector);
    }

    return jquery_object;
}

// =========================================================================================== \\

function ai4seo_exists(selector, context) {
    if (!context) {
        context = window.parent.document;
    }

    return jQuery(selector, context).length > 0;
}

// =========================================================================================== \\

function ai4seo_get_post_id() {
    // first look for the post id in the ajax modal
    let post_id = ai4seo_context.find("#ai4seo-editor-modal-post-id").val();

    if (post_id && !isNaN(post_id)) {
        return parseInt(post_id);
    }
    
    // then look for the post-id in the footer
    post_id = ai4seo_context.find("#ai4seo-footer-post-id").text();

    // Make sure that post_id could be found and is a number
    if (post_id && !isNaN(post_id)) {
        return parseInt(post_id);
    }

    // Check if "media-modal"-element exists
    if (ai4seo_exists(".media-modal")) {
        // Read current url-parameters
        var current_url_parameters = new URLSearchParams(window.location.search);

        // Read item-parameter from current-url-parameters
        post_id = current_url_parameters.get("item");

        // Check if item-id could be found and is valid
        if (post_id && !isNaN(post_id)) {
            return parseInt(post_id);
        }

        // If the post_id could not be read from the url of the page then try to access wp.media.frame
        else {
            // Access the wp.media frame
            var mediaFrame = wp.media.frame;

            // Check if the attachment-id exists within model.id
            if (mediaFrame.model && mediaFrame.model.id) {
                post_id = mediaFrame.model.id;

                if (post_id && !isNaN(post_id)) {
                    return parseInt(post_id);
                }
            }
        }
    }

    return false;
}

// =========================================================================================== \\

function ai4seo_get_version_number() {
    // Define variable for the version-number
    let version_number = ai4seo_context.find("#ai4seo-plugin-version-number").text();

    // Make sure that version_number could be found
    if (!version_number) {
        return "";
    }

    return version_number;
}

// =========================================================================================== \\

function ai4seo_get_full_domain() {
    // Check if ai4seo_localization.ai4seo_site_url exists
    if (typeof ai4seo_localization.ai4seo_site_url !== "undefined") {
        return ai4seo_localization.ai4seo_site_url;
    }

    let protocol = window.location.protocol;
    let host = window.location.host;
    return protocol + "//" + host;
}

// =========================================================================================== \\

function ai4seo_get_ai4seo_plugin_directory_url() {
    // Make sure that ai4seo_localization.ai4seo_plugin_directory_url exists
    if (typeof ai4seo_localization.ai4seo_plugin_directory_url === "undefined") {
        return false;
    }

    return ai4seo_localization.ai4seo_plugin_directory_url;
}

// =========================================================================================== \\

function ai4seo_is_json_string( string ) {
    try {
        JSON.parse(string);
    } catch (e) {
        return false;
    }

    return true;
}

// =========================================================================================== \\

function ai4seo_is_object( object ) {
    return object === Object(object);
}

// =========================================================================================== \\

function ai4seo_is_chrome_browser() {
    return navigator.userAgent.indexOf("Chrome") !== -1;
}

// =========================================================================================== \\

function ai4seo_reload_page_with_parameter(parameterName, parameterValue) {
    // Get current URL parameters
    var searchParams = new URLSearchParams(window.location.search);

    // Set or update the parameter
    searchParams.set(parameterName, parameterValue);

    // Create the new URL with updated parameters
    // Reload the page with the new URL
    window.location.href = window.location.pathname + '?' + searchParams.toString() + window.location.hash;
}

// =========================================================================================== \\

function ai4seo_is_yoast_element(element_selector) {
    // Define variable for element
    var element = ai4seo_jQuery(element_selector);

    // Check if element is found
    if (element.length === 0) {
        return false;
    }

    // Check if element is a yoast-element
    if (element.closest(".yst-replacevar__editor").length === 0) {
        return false;
    }

    return true;
}


// ___________________________________________________________________________________________ \\
// === AI GENERATION ========================================================================= \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

// Function to make an ajax call to generate-metadata.php to get the post details
function ai4seo_generate_with_ai(ajax_action, post_id = false, only_this_selector = false, try_read_page_content_via_js = false ) {
    // check action
    if (!ai4seo_allowed_ajax_actions.includes(ajax_action)) {
        ai4seo_show_notification_modal(wp.i18n.__("An error occurred! Please check your settings or contact the plugin developer.", "ai-for-seo") + " (" + wp.i18n.__("Error-code", "ai-for-seo") + ": #141823824)");
        return;
    }

    // Read post-id from hidden container if not defined
    if (!post_id) {
        post_id = ai4seo_get_post_id();
    }

    if (!post_id || isNaN(post_id)) {
        ai4seo_show_notification_modal(wp.i18n.__("Could not read post ID. Please check your settings or contact the plugin developer.", "ai-for-seo") + " (" + wp.i18n.__("Error-code", "ai-for-seo") + ": #132120824)");
        return;
    }

    // collect data
    let ajax_data = {
        ai4seo_post_id: post_id,
        action: ajax_action,
    };

    // check if we should try to read the page content via js
    if (try_read_page_content_via_js) {
        // Define variable for the content based on ai4seo_get_post_content()
        // add content as ai4seo_content to data
        ajax_data.ai4seo_content = ai4seo_get_post_content();
    }

    // try to determine all current input values
    let current_generation_input_values = ai4seo_fetch_generation_input_values(only_this_selector);

    if (current_generation_input_values) {
        ajax_data.ai4seo_generation_input_values = current_generation_input_values;
    }

    // Replace button-label with loading-html
    ai4seo_add_loading_html_to_element(".ai4seo-generate-button");
    ai4seo_add_loading_html_to_element(".ai4seo-generate-all-button");

    // Make the ajax call and await the json response
    jQuery.post( ai4seo_admin_ajax_url, ajax_data, function( response ) {
        // Remove loading-html from button-label
        ai4seo_remove_loading_html_from_element(".ai4seo-generate-button");
        ai4seo_remove_loading_html_from_element(".ai4seo-generate-all-button");

        // un-comment for debugging
        // console.log(response);

        if (!response) {
            ai4seo_show_notification_modal(wp.i18n.__("An error occurred! Please check your settings or contact the plugin developer.", "ai-for-seo") + " (" + wp.i18n.__("Error-code", "ai-for-seo") + ": #1104232359)");
            return;
        }

        // check if response is a json-string
        if (ai4seo_is_json_string(response)) {
            response = JSON.parse( response );
        }

        // check if the response is valid
        if (!ai4seo_check_response(response)) {
            return;
        }

        ai4seo_fill_post_outputs( response, only_this_selector );
    });
}

// =========================================================================================== \\

// Function to go through the content containers and grab with .text() and put everything into a big string
function ai4seo_get_post_content() {
    let post_content = "";

    for (let i = 0; i < ai4seo_content_containers.length; i++) {
        let this_content_container = ai4seo_content_containers[i];

        let this_content_containers_child_elements = ai4seo_context.find(this_content_container);

        // Make sure that child-elements could be found
        if (!this_content_containers_child_elements) {
            continue;
        }

        // Loop through child-elements and add their text to the content
        this_content_containers_child_elements.each(function() {
            let additional_post_content = "";

            // add text of the element to the content
            // if it's an input or textarea, use val() instead of text()
            if (ai4seo_jQuery(this).is('input') || ai4seo_jQuery(this).is('textarea')) {
                additional_post_content = ai4seo_jQuery(this).val();
            } else {
                additional_post_content = ai4seo_jQuery(this).text();
            }

            additional_post_content = ai4seo_add_dot_to_string(additional_post_content);

            // add additional post content to the post content, adding a space in between, if post content is not empty
            if (post_content) {
                post_content += " ";
            }

            post_content += additional_post_content;
        });
    }

    // for debugging: look what we got
    //console.log(post_content);

    return post_content;
}

// =========================================================================================== \\

/**
 * Function to add a dot at the end of the string if not already there
 * @param {string} string
 * @returns {string}
 */
function ai4seo_add_dot_to_string(string) {
    // trim string
    string = string.trim();

    // Return if the string is not longer than 1 character
    if (string.length <= 1) {
        return string;
    }

    // Return if the last character is already a dot
    if (string[string.length - 1] === ".") {
        return string;
    }

    // Add a dot if none of the above conditions were met
    string += ".";

    return string;
}

// =========================================================================================== \\

// Function to check response
function ai4seo_check_response( response, error_list = {}, show_generic_error = true) {
    if (!response) {
        ai4seo_show_notification_modal(wp.i18n.__("An error occurred! Please check your settings or contact the plugin developer.", "ai-for-seo") + " (" + wp.i18n.__("Error-code", "ai-for-seo") + ": #1104232360)");
        return false;
    }

    // if response is a json-string, parse it so we can work with it
    if (ai4seo_is_json_string(response)) {
        response = JSON.parse( response );
    }

    // check if we have a success key in the response
    if (typeof response.success === 'undefined') {
        ai4seo_show_notification_modal(wp.i18n.__("An error occurred! Please check your settings or contact the plugin developer.", "ai-for-seo") + " (" + wp.i18n.__("Error-code", "ai-for-seo") + ": #1104232361)");
        return false;
    }

    // if success is true -> return true
    if (response.success) {
        return true;
    }

    // otherwise we have an error
    console.log(response);

    // Check if response.code (cast to int) is in ai4seo_robhub_api_response_error_codes
    if (ai4seo_robhub_api_response_error_codes.includes(parseInt(response.code))) {
        // Handle error-code 32127323 or 18197323
        ai4seo_handle_robhub_api_response_errors(response.error, response.code);
    }

    // check for the "content too short" error
    else if (response.code === 491320823 || response.code === "491320823" || response.code === 54155424 || response.code === "54155424") {
        ai4seo_show_notification_modal(wp.i18n.__("The provided content length insufficient for optimal SEO performance:", "ai-for-seo") + " " + response.error + ".");
    }

    // Check if error-code exists as key in ai4seo_error_codes_and_messages
    else if (ai4seo_error_codes_and_messages[response.code]) {
        ai4seo_show_notification_modal(ai4seo_error_codes_and_messages[response.code]);
    }

    else if (error_list[response.code]) {
        ai4seo_show_notification_modal(error_list[response.code]);
    }

    else if (show_generic_error) {
        ai4seo_show_notification_modal(wp.i18n.__("An error occurred! Please check your settings or contact the plugin developer.", "ai-for-seo") + " (" + wp.i18n.__("Error-code", "ai-for-seo") + ": #71816423 + #" + response.code + ")");
    }

    return false;
}

// =========================================================================================== \\

function ai4seo_handle_robhub_api_response_errors(error_message, error_code) {
    // Check if ai4seo_robhub_api_response_error_codes_and_messages-array contains key that contains the error-message
    for (var key in ai4seo_robhub_api_response_error_codes_and_messages) {
        if (error_message.includes(key)) {
            // Display error-message
            ai4seo_show_notification_modal(ai4seo_robhub_api_response_error_codes_and_messages[key]);
            return;
        }
    }

    // Display generic error-message if no error-message was found
    ai4seo_show_notification_modal(error_message + " (" + wp.i18n.__("API error-code", "ai-for-seo") + ": #" + error_code + "). " + wp.i18n.__("An error occurred. Please contact the plugin-developer or try again!", "ai-for-seo"));
}

// =========================================================================================== \\

// Function to fill the post details in the ai4seo_post_outputs variable
function ai4seo_fill_post_outputs( response, only_this_selector = false) {
    // todo: only un-commend for debugging (check prior to release)
    // console.log(response);

    ai4seo_post_outputs = response.data;
    ai4seo_consumed_credits = response.consumed_credits ? response.consumed_credits : 0

    if (response.new_credits_balance) {
        ai4seo_remaining_credits = response.new_credits_balance;
    }

    // go through the selector mapping and fill the values
    ai4seo_fill_post_outputs_from_mapping(only_this_selector);
}

// =========================================================================================== \\

// Function to go through the selector mapping and fill the values
function ai4seo_fill_post_outputs_from_mapping(only_this_selector = false) {
    // Define variable for all selectors
    var all_selectors = ai4seo_selector_mapping;

    if (only_this_selector) {
        if (!ai4seo_selector_mapping[only_this_selector]) {
            console.log("AI for SEO: Unknown selector: " + only_this_selector);
            return;
        }

        all_selectors = {};
        all_selectors[only_this_selector] = ai4seo_selector_mapping[only_this_selector];
    }

    // Go through the selector mapping and fill the values
    for (var selector in all_selectors) {
        var options = all_selectors[selector];

        var endpoint = options.endpoint;

        // Set selectors by options.additional_selectors if given, otherwise set to {}
        var selectors = options.additional_selectors ? options.additional_selectors : [];

        // Add the selector to the selectors
        selectors.push(selector);

        // Define variable for the value of the selector
        var value = ai4seo_post_outputs[endpoint];

        // Make sure that value-parameter could be defined
        if (typeof value === "undefined") {
            continue;
        }

        // Go through the selectors and fill the value
        for (var i = 0; i < selectors.length; i++) {
            var this_selector = selectors[i];

            if (!ai4seo_exists(this_selector)) {
                continue;
            }

            ai4seo_fill_text( this_selector, value, options );
        }
    }
}

// =========================================================================================== \\

// Function to fill the text with the element selected by the selector with the value
// the element can be a text field or a text area or a div
function ai4seo_fill_text( selector, value, options = {}) {
    var element = ai4seo_jQuery(selector);

    // Stop script if element could not be found
    if (element.length === 0) {
        return;
    }

    if (element.is('input')) {
        element.val(value).keypress().change();
    } else if (element.is('textarea')) {
        element.val(value).keypress().change();
    } else {
        var text_length = ai4seo_jQuery(selector).text().length;

        if (options.key_by_key && text_length > 0 && ai4seo_is_chrome_browser()) {
            ai4seo_add_text_to_editor_key_by_key(selector, value);
        } else {
            ai4seo_set_yoast_input_content(selector, value);

            //if (options.key_by_key && text_length > 0 && !ai4seo_is_chrome_browser()) {
            if (!ai4seo_is_chrome_browser()) {
                // disable input for the selector's element
                ai4seo_jQuery(selector).parent().parent().parent().attr("contenteditable", false);
                ai4seo_jQuery(selector).parent().parent().parent().attr("readonly", true);
                ai4seo_jQuery(selector).parent().parent().parent().css("pointer-events", "none");
                ai4seo_jQuery(selector).parent().parent().parent().parent().parent().parent().css("background-color", "rgba(155,155,155,.5)");
            }
        }
    }

    // Call function to set progress bar to success
    ai4seo_set_progress_bar_success(selector);
}

// =========================================================================================== \\

function ai4seo_set_progress_bar_success(selector) {
    // Define variable for selector-element
    var selector_element = ai4seo_jQuery(selector);

    // Make sure that selector-element exists
    if (selector_element.length === 0) {
        return;
    }

    // Define variable for the parent-element with class "yst-replacevar"
    var parent_element = selector_element.closest(".yst-replacevar");

    // Make sure that parent-element exists
    if (parent_element.length === 0) {
        return;
    }

    // Define variable for the progress-bar-element
    var progress_bar = parent_element.next("progress");

    // Make sure that progress-bar-element exists
    if (progress_bar.length === 0) {
        return;
    }

    // Read max-value of progress-bar-element
    var max_value = progress_bar.attr("max");

    // Add success-class to progress-bar-element
    progress_bar.addClass("ai4seo-progress-success");

    // Set porgress-bar-value to max-value
    progress_bar.attr("value", max_value);
}

// =========================================================================================== \\

function ai4seo_set_yoast_input_content( selector, value ) {
    const jquery_container = ai4seo_jQuery(selector);
    const data_offset_key = jquery_container.data("offset-key");
    const container = jquery_container.get(0);
    const inner_span = React.createElement('span', { 'data-text': 'true' }, value);
    const span_container = React.createElement('span', { 'data-offset-key': data_offset_key }, inner_span);
    ReactDOM.unmountComponentAtNode(container);
    ReactDOM.render(span_container, container);

    if (ai4seo_is_chrome_browser()) {
        // frozen input workaround: add empty character to editor
        var editor = ai4seo_jQuery(selector).parent().parent().parent().get(0);

        editor.focus();
        document.execCommand('insertText', false, "​");
    }
}

// =========================================================================================== \\

function ai4seo_add_text_to_editor_key_by_key( selector, value ) {
    var editor = ai4seo_jQuery(selector).parent().parent().parent().get(0);

    // delete all content in the editor
    ai4seo_delete_editor_content(editor);

    editor.focus();
    ai4seo_set_cursor_at_the_end(editor);

    // go through each character and add it to the editor
    for (var i = 0; i < value.length; i++) {
        document.execCommand('insertText', false, value[i]);
    }
}

// =========================================================================================== \\

function ai4seo_delete_editor_content(editor) {
    editor.focus();

    // place cursor at the beginning of the editor
    ai4seo_set_cursor_at_the_end(editor);

    // Remove the content one by one
    var text_length = ai4seo_jQuery(editor).text().length;

    for (var i = 0; i < text_length; i++) {
        document.execCommand('delete', false, null);
    }
}

// =========================================================================================== \\

function ai4seo_set_cursor_at_the_end(element) {
    const range = document.createRange();
    const selection = window.getSelection();
    range.selectNodeContents(element);
    range.collapse(false);
    selection.removeAllRanges();
    selection.addRange(range);
}


// ___________________________________________________________________________________________ \\
// === DASHBOARD ============================================================================= \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

function add_refresh_credits_balance_parameter_and_reload_page() {
    ai4seo_reload_page_with_parameter("ai4seo_refresh_credits_balance", "true");
}

// ___________________________________________________________________________________________ \\
// === GENERATE THROUGH AI - BUTTONS ========================================================= \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

function ai4seo_init_generate_all_button() {
    // Check if current page is attachment-page
    // workaround: we need to check if the attachment mime type is supported
    if (ai4seo_is_attachment_post_type()) {
        // Stop script if the current attachment doesn't contain supported mime type
        if (!ai4seo_is_attachment_mime_type_supported()) {
            return;
        }
    }

    // check if there is already a generate all button
    if (ai4seo_exists(".ai4seo-generate-all-button")) {
        ai4seo_jQuery(".ai4seo-generate-all-button").remove();
    }

    // Loop through selectors and add button to each selector
    for (let key in ai4seo_generate_all_button_selectors) {
        ai4seo_generate_all_button_selectors[key].forEach(function(selector) {
            if (ai4seo_exists(selector)) {
                ai4seo_add_generate_all_button(key, selector);
            }
        });
    }
}

// =========================================================================================== \\

function ai4seo_add_generate_all_button(processing_context, element_selector) {
    // Define variable for element
    var hook_element = ai4seo_jQuery(element_selector);

    // Stop script if element is not found
    if (hook_element.length === 0) {
        return;
    }

    // Define button variables
    let onclick = "";
    let button_title = "";
    let button_label = "<img src='" + ai4seo_get_ai4seo_plugin_directory_url() + "/assets/images/logos/ai-for-seo-logo-64x64.png' class='ai4seo-logo'  alt='AI'/>" + wp.i18n.__("Generate all SEO", "ai-for-seo");

    if (processing_context === "metadata") {
        onclick += "ai4seo_generate_with_ai(\"ai4seo_generate_metadata\", false, false, true);";
        button_title += wp.i18n.__("Generate metadata for all meta tags via AI.", "ai-for-seo");
    } else if (processing_context === "attachment-attributes") {
        onclick += "ai4seo_generate_with_ai(\"ai4seo_generate_attachment_attributes\",false, false);";
        button_title += wp.i18n.__("Generate content for all available media attributes via AI.", "ai-for-seo");
    }

    // put everything together
    let button_html = "<button type='button' onclick='" + onclick + "' title='" + button_title + "' class='ai4seo-generate-all-button'>" + button_label + "</button>";
    let wrapped_button_html = "<div class='ai4seo-generate-all-button-wrapper'>" + button_html + "</div>";

    // Add button-element after element
    hook_element.prepend(wrapped_button_html);
}

// =========================================================================================== \\

/**
 * Fetch the values of all input fields that are mapped to an endpoint
 * @returns {{}} - object with endpoint as key and input value as value
 */
function ai4seo_fetch_generation_input_values(only_this_selector = false) {
    let values = {};
    let selectors = [];

    if (only_this_selector) {
        selectors.push(only_this_selector);
    } else {
        // get objects field names
        selectors = Object.keys(ai4seo_selector_mapping);
    }

    for (let i = 0; i < selectors.length; i++) {
        let selector = selectors[i];

        // check if the selector exists and the element is visible
        if (!ai4seo_exists(selector)) {
            continue;
        }

        // check if the element is visible
        if (!ai4seo_jQuery(selector).is(":visible")) {
            continue;
        }

        let endpoint = ai4seo_selector_mapping[selector].endpoint;

        // check if we already have a value for this endpoint
        if (values[endpoint]) {
            continue;
        }

        values[endpoint] = ai4seo_get_input_val(selector);
    }

    return values;
}

// =========================================================================================== \\

function ai4seo_add_link_element_to_yoast_seo_input_label(editor_element_selector) {
    // Overwrite editor-element with jquery-element
    var editor_element = ai4seo_jQuery(editor_element_selector);

    // Stop script if editor-element is not found
    if (editor_element.length === 0) {
        return;
    }

    // Define variable for the parent-element
    var parent_element = editor_element.closest(".yst-replacevar__editor");

    // Stop script if parent-element is not found
    if (parent_element.length === 0) {
        return;
    }

    // Check if element after parent_element contains "ai4seo-generate-button"-class
    if (parent_element.next().hasClass("ai4seo-generate-button")) {
        // Remove button-element
        parent_element.next().remove();
    }

    // Add link element after parent-element
    parent_element.after(ai4seo_get_generate_button_output(editor_element_selector));
}

// =========================================================================================== \\

function ai4seo_get_generate_button_output(element_selector, button_label = "auto", button_title = "") {
    // Make sure that onclick-variable is defined
    let button_onclick = "";
    let try_read_page_content_via_js = "true"; // assuming I'm inside a WordPress editor

    if (ai4seo_exists("#ai4seo-read-page-content-via-js")) {
        try_read_page_content_via_js = ai4seo_jQuery("#ai4seo-read-page-content-via-js").val();
    }

    if (button_label === "auto") {
        // Generate with AI
        button_label = wp.i18n.__("Generate with AI", "ai-for-seo");
    }

    // Check if processing-entry exists in mapping-array
    if (ai4seo_selector_mapping[element_selector]['processing-context']) {
        // Prepare onclick for attachment-attributes-processing
        if (ai4seo_selector_mapping[element_selector]['processing-context'] === "attachment-attributes") {
            button_onclick = "ai4seo_generate_with_ai(\"ai4seo_generate_attachment_attributes\", false, \"" + element_selector + "\");";
        }

        // Prepare onclick for  -processing
        else if (ai4seo_selector_mapping[element_selector]['processing-context'] === "metadata") {
            button_onclick = "ai4seo_generate_with_ai(\"ai4seo_generate_metadata\", false, \"" + element_selector + "\", " + try_read_page_content_via_js + ");";
        }

        // Prepare fallback onclick
        else {
            console.log("AI for SEO: Unknown processing-context: " + ai4seo_selector_mapping[element_selector]['processing-context']);
        }
    } else {
        console.log("AI for SEO: No processing-context defined for element-selector: " + element_selector);
    }

    // Prepare additional css-class for button-output
    let additional_css_class = "";

    if (ai4seo_selector_mapping[element_selector]['css-class']) {
        additional_css_class = " " + ai4seo_selector_mapping[element_selector]['css-class'];
    }

    return "<button type='button' onclick='" + button_onclick + "' title='" + button_title + "' class='ai4seo-generate-button" + additional_css_class + "'><img src='" + ai4seo_get_ai4seo_plugin_directory_url() + "/assets/images/logos/ai-for-seo-logo-32x32.png' class='ai4seo-icon ai4seo-button-icon ai4seo-logo'> " + button_label + "</button>";
}

// =========================================================================================== \\

function ai4seo_add_generate_button_to_input(input_element_selector) {
    // Define variable for input-element
    var input_element = ai4seo_jQuery(input_element_selector);

    // Stop script if input-element is not found
    if (input_element.length === 0) {
        return;
    }

    // Check if element after input_element contains "ai4seo-generate-button"-class
    if (input_element.next().hasClass("ai4seo-generate-button")) {
        // Remove button-element
        input_element.next().remove();
    }

    // Add button-element after input-element
    input_element.after(ai4seo_get_generate_button_output(input_element_selector));
}

// === FUNCTION TO DETERMINE CONTENT FOR CURRENT PAGE ======================================== \\

function ai4seo_get_context() {
    // Define variable for the elementor-preview-iframe-element
    if (ai4seo_exists("#elementor-preview-iframe")) {
        return ai4seo_jQuery("#elementor-preview-iframe").contents();
    }

    // Define variable for the be-builder-iframe
    if (ai4seo_exists("#mfn-vb-ifr")) {
        return ai4seo_jQuery("#mfn-vb-ifr").contents();
    }

    // Return jQuery-document if no elementor-iframe exists
    return jQuery(document);
}

// === FUNCTIONS TO MANAGE LOADING-HTML TO ELEMENT =========================================== \\

function ai4seo_add_loading_html_to_element(element) {
    // Make sure that element is jquery-element
    element = ai4seo_jQuery(element);

    // Make sure that element can be found
    if (!element.length) {
        return;
    }

    element.each(function() {
        // Define variable for this element
        var this_element = ai4seo_jQuery(this);

        // Define variable for the original html-content
        var original_html_content = this_element.html();

        // Replace html-content with loading-elements
        this_element.html("<div class='ai4seo-loading-animation'><div></div><div></div><div></div><div></div></div>");

        // Add data-attribute to element with original html-content
        this_element.attr("data-ai-for-seo-original-html-content", original_html_content);

        // Add class to deactivate element to element
        this_element.addClass("ai4seo-element-inactive");
    });
}

// =========================================================================================== \\

function ai4seo_remove_loading_html_from_element(element) {
    // Make sure that element is jquery-element
    element = ai4seo_jQuery(element);

    // Make sure that element can be found
    if (!element.length) {
        return;
    }

    element.each(function() {
        // Define variable for this element
        var this_element = ai4seo_jQuery(this);

        // Define variable for the original html-content
        var original_html_content = this_element.attr("data-ai-for-seo-original-html-content");

        // Remove data-attribute from element
        this_element.removeAttr("data-ai-for-seo-original-html-content");

        // Replace html-content with original html-content
        this_element.html(original_html_content);

        // Remove class to deactivate element from element
        this_element.removeClass("ai4seo-element-inactive");
    });
}

// =========================================================================================== \\

function ai4seo_show_notification_modal(message, headline = "", buttons_row_html = "") {
    // INIT NOTIFICATION MODAL ELEMENT
    let notification_modal_element = ai4seo_jQuery(".ai4seo-notification-modal");

    // workaround: if the modal is not found, show an browser alert containing the message
    if (!ai4seo_exists(notification_modal_element)) {
        alert(message);
        return;
    }

    // INIT CONTENT ELEMENT
    let notification_modal_message_element = notification_modal_element.find(".ai4seo-notification-modal-content");

    // Make sure that error-message-element can be found
    if (!ai4seo_exists(notification_modal_message_element)) {
        alert(message);
        return;
    }

    // set message
    notification_modal_message_element.html(message);

    // INIT HEADLINE
    let notification_modal_headline_element = notification_modal_element.find(".ai4seo-notification-modal-headline");
    let notification_modal_default_headline_element = notification_modal_element.find(".ai4seo-notification-modal-default-headline");

    if (headline.length > 0) {
        notification_modal_headline_element.html(headline);
        notification_modal_headline_element.show();
        notification_modal_default_headline_element.hide();
    } else {
        notification_modal_headline_element.hide();
        notification_modal_default_headline_element.show();
    }

    // INIT BUTTONS
    let notification_modal_buttons_row_element = notification_modal_element.find(".ai4seo-notification-modal-buttons-row");
    let notification_modal_buttons_default_row_element = notification_modal_element.find(".ai4seo-notification-modal-default-buttons-row");

    if (buttons_row_html.length > 0) {
        notification_modal_buttons_row_element.html(buttons_row_html).show();
        notification_modal_buttons_default_row_element.hide();
    } else {
        notification_modal_buttons_default_row_element.show();
        notification_modal_buttons_row_element.hide();
    }

    // Show modal-element
    notification_modal_element.parent().show(); // wrapper
    notification_modal_element.show();
}

// =========================================================================================== \\

function ai4seo_hide_notification_modal() {
    let notification_modal_wrapper = jQuery(".ai4seo-notification-modal-wrapper", window.parent.document);

    notification_modal_wrapper.hide();
    notification_modal_wrapper.find(".ai4seo-notification-modal").hide();
    notification_modal_wrapper.find(".ai4seo-notification-modal-content").html("");
}

// =========================================================================================== \\

function ai4seo_hide_ajax_modal() {
    let ajax_modal_wrapper = jQuery("#ai4seo-ajax-modal-wrapper", window.parent.document);
    ajax_modal_wrapper.hide();
    ajax_modal_wrapper.find(".ai4seo-ajax-modal").hide();
    ajax_modal_wrapper.find(".ai4seo-ajax-modal-content").html("")
}

// =========================================================================================== \\

function ai4seo_hide_modal(element_within_modal) {
    element_within_modal = ai4seo_jQuery(element_within_modal);

    // find ai4seo-modal-wrapper parent
    let modal_wrapper = element_within_modal.closest(".ai4seo-modal-wrapper", window.parent.document);

    modal_wrapper.hide();
}

// =========================================================================================== \\

function ai4seo_close_modal_on_outside_click(event) {
    // Make sure that event-target exists
    if (!event.target) {
        return;
    }

    // Check if mouse-down-element has been saved
    if (ai4seo_just_clicked_modal_wrapper) {
        // Make sure that mouse-down-element has class "ai4seo-modal-wrapper"
        if (ai4seo_just_clicked_modal_wrapper.length && !ai4seo_just_clicked_modal_wrapper.hasClass("ai4seo-modal-wrapper")) {
            return;
        }
    }

    // Define variable for the target-element
    let target_element = ai4seo_jQuery(event.target);

    // Stop script if target-element doesn't have "ai4seo-ajax-modal-wrapper"-class
    if (target_element.hasClass("ai4seo-ajax-modal-wrapper")) {
        ai4seo_hide_ajax_modal();
    }
}

// =========================================================================================== \\

function ai4seo_open_metadata_editor_modal(post_id = false, read_page_content_via_js = false) {
    // Read post-id from hidden container if not defined
    if (!post_id) {
        post_id = ai4seo_get_post_id();
    }

    if (!post_id) {
        ai4seo_show_notification_modal(wp.i18n.__("An error occurred! Please check your settings or contact the plugin developer.", "ai-for-seo") + " (" + wp.i18n.__("Error-code", "ai-for-seo") + ": #26173424)");
        return;
    }

    // CURRENT POST'S CONTENT
    let post_content = "";

    // Define variable for the content based on ai4seo_get_post_content()
    if (read_page_content_via_js) {
        post_content = ai4seo_get_post_content();
    }

    let parameters = {
        post_id: post_id,
        read_page_content_via_js: read_page_content_via_js,
        content: post_content,
    }

    ai4seo_open_ajax_modal("ai4seo_show_metadata_editor", parameters);
}

// =========================================================================================== \\

function ai4seo_open_attachment_attributes_editor_modal(post_id = false) {
    // Read post-id from hidden container if not defined
    if (!post_id) {
        ai4seo_show_notification_modal(wp.i18n.__("An error occurred! Please check your settings or contact the plugin developer.", "ai-for-seo") + " (" + wp.i18n.__("Error-code", "ai-for-seo") + ": #241920824)");
        return;
    }

    // PARAMETERS
    let parameters = {
        post_id: post_id,
    }

    ai4seo_open_ajax_modal("ai4seo_show_attachment_attributes_editor", parameters);
}

// =========================================================================================== \\

function ai4seo_open_ajax_modal(ajax_action, parameters = {}) {
    // CHECK AJAX ACTION
    if (!ajax_action) {
        ai4seo_show_notification_modal(wp.i18n.__("Ajax action could not be found. Please contact support!", "ai-for-seo") + " #501220824");
        return;
    }

    // is allowed ajax_action?
    if (!ai4seo_allowed_ajax_actions.includes(ajax_action)) {
        ai4seo_show_notification_modal(wp.i18n.__("Ajax action not allowed. Please contact support!", "ai-for-seo") + " #521220824");
        return;
    }

    // INIT AJAX MODAL
    let ajax_modal_element = ai4seo_jQuery("#ai4seo-ajax-modal");

    if (!ai4seo_exists(ajax_modal_element)) {
        ai4seo_show_notification_modal(wp.i18n.__("Ajax modal could not be found. Please contact support!", "ai-for-seo") + " #25173424");
        return;
    }

    // INIT CONTENT ELEMENT
    let ajax_modal_content_element = ajax_modal_element.find(".ai4seo-ajax-modal-content");

    // empty and hide content element if it exists
    if (ai4seo_exists(ajax_modal_content_element)) {
        ajax_modal_content_element.html("").hide();
    }

    // INIT LOADER ICON
    let ajax_modal_loading_icon_element = ajax_modal_element.find(".ai4seo-ajax-modal-loading-icon");

    // show loader icon if it exists
    if (ai4seo_exists(ajax_modal_loading_icon_element)) {
        ajax_modal_loading_icon_element.show();
    }

    // INIT PARAMETERS
    let ajax_post_data = {
        action: ajax_action,
    }

    ajax_post_data = Object.assign(ajax_post_data, parameters);

    // SHOW MODAL (including the modal wrapper), SO THE USER CAN SEE THE LOADING ICON
    ajax_modal_element.parent().show(); // modal wrapper
    ajax_modal_element.show();

    // PERFORM AJAX CALL
    jQuery.post( ai4seo_admin_ajax_url, ajax_post_data, function( ajax_response ) {
        if (!ajax_response) {
            ai4seo_show_notification_modal(wp.i18n.__("An error occurred! Please check your settings or contact the plugin developer.", "ai-for-seo") + " (" + wp.i18n.__("Error-code", "ai-for-seo") + ": #2306230642)");
            return;
        }

        // Hide loading container
        ajax_modal_loading_icon_element.hide();

        // insert response into content element
        ajax_modal_content_element.fadeOut(200, function() {
            jQuery(this).html(ajax_response).fadeIn(200);
        });

        // Disable submit-button
        ai4seo_disable_modal_submit_button();

        // init html elements that may have been added
        ai4seo_init_html_elements();
    });
}

// =========================================================================================== \\

function ai4seo_enable_modal_submit_button() {
    // Define variable for the submit-button
    var submit_button = ai4seo_jQuery("#ai4seo-modal-submit");

    // Make sure that submit-button can be found
    if (!submit_button.length) {
        return;
    }

    // Remove disabled attribute from submit-button
    submit_button.removeAttr("disabled");

    // Add success-button-class to submit-button
    submit_button.removeClass("ai4seo-disabled-button");
}

// =========================================================================================== \\

function ai4seo_disable_modal_submit_button() {
    // Define variable for the submit-button
    var submit_button = ai4seo_jQuery("#ai4seo-modal-submit");

    // Make sure that submit-button can be found
    if (!submit_button.length) {
        return;
    }

    // Remove disabled attribute from submit-button
    submit_button.attr("disabled", "disabled");

    // Add success-button-class to submit-button
    submit_button.addClass("ai4seo-disabled-button");
}

// =========================================================================================== \\

function ai4seo_submit_ajax_modal(ajax_action) {
    let container_selector = "#ai4seo-ajax-modal";
    let success_callback = ai4seo_on_successful_ajax_modal_submit;
    
    ai4seo_submit_form_via_ajax(container_selector, ajax_action, success_callback);
}

// =========================================================================================== \\

function ai4seo_submit_form_via_ajax(container_selector, ajax_action, success_callback) {
    // check if action is allowed
    if (!ai4seo_allowed_ajax_actions.includes(ajax_action)) {
        ai4seo_show_notification_modal(wp.i18n.__("Ajax action not allowed. Please contact support!", "ai-for-seo") + " #511622824");
        return;
    }

    // Define variable for the form-data
    var all_input_values = ai4seo_get_all_input_values_in_container(container_selector);

    // Make sure that form-data could be read
    if (!all_input_values) {
        ai4seo_show_notification_modal(wp.i18n.__("An error occurred! Please check your settings or contact the plugin developer.", "ai-for-seo") + " (" + wp.i18n.__("Error-code", "ai-for-seo") + ": #1207230232)");
        return false;
    }

    // Prepare data for ajax call
    var data = {
        ai4seo_input_values: all_input_values,
        action: ajax_action,
    };

    // Make the ajax call and await the json response
    jQuery.post( ai4seo_admin_ajax_url, data, function( response ) {
        if (!response) {
            ai4seo_show_notification_modal(wp.i18n.__("An error occurred! Please check your settings or contact the plugin developer.", "ai-for-seo") + " (" + wp.i18n.__("Error-code", "ai-for-seo") + ": #1207230234)");
            return;
        }

        // Check if response is a json-string
        if (ai4seo_is_json_string(response)) {
            response = JSON.parse( response );
        } else if (ai4seo_is_object(response) && response.data) {
            // everything is fine already
        } else {
            if (response.code && response.error) {
                ai4seo_show_notification_modal(wp.i18n.__("An error occurred! Please check your settings or contact the plugin developer.", "ai-for-seo") + " (" + wp.i18n.__("Error-code", "ai-for-seo") + ": #" + response.code + ". " + wp.i18n.__("Error", "ai-for-seo") + ": " + response.error + ")");
            } else {
                ai4seo_show_notification_modal(wp.i18n.__("An error occurred! Please check your settings or contact the plugin developer.", "ai-for-seo") + " (" + wp.i18n.__("Error-code", "ai-for-seo") + ": #1207230235)");
            }
            return;
        }

        // Check if the response is valid
        if (!ai4seo_check_response(response)) {
            return;
        }

        success_callback(response);
    });
}

// =========================================================================================== \\

function ai4seo_get_all_input_values_in_container(form_container) {
    // Define variable for the form-holder-element based on the form-holder-selector
    var container_element = ai4seo_jQuery(form_container);

    // Stop script if form-holder-element could not be found
    if (!ai4seo_exists(container_element)) {
        ai4seo_show_notification_modal(wp.i18n.__("Form-holder-element could not be found.", "ai-for-seo") + " (" + wp.i18n.__("Error-code", "ai-for-seo") + ": #501622824");
        return false;
    }

    // Find form-elements within the form-holder-element
    var input_elements = container_element.find("input, select, textarea");
    var input_values = {};
    var this_input_selector;
    var this_input_element;
    var this_input_value;

    // Collect identifier (to prevent analysing the same checkbox or radio-name)
    for(var i = 0; i < input_elements.length; i++) {
        this_input_element = input_elements[i];
        this_input_selector = "#" + this_input_element.id;
        this_input_value = ai4seo_get_input_val(this_input_selector);

        if (typeof this_input_value !== "undefined") {
            input_values[this_input_element.id] = this_input_value;
        }
    }

    // Make sure that input_vals is not empty
    if (Object.keys(input_values).length === 0) {
        ai4seo_show_notification_modal(wp.i18n.__("An error occurred! Please check your settings or contact the plugin developer.", "ai-for-seo") + " (" + wp.i18n.__("Error-code", "ai-for-seo") + ": #1207230231)");
        return false;
    }

    return input_values;
}

// =========================================================================================== \\

function ai4seo_on_successful_ajax_modal_submit(action, form_container) {
    ai4seo_hide_ajax_modal();
    window.location.reload();
}

// =========================================================================================== \\

function ai4seo_toggle_automated_generation(context, checked) {
    // Prepare data for ajax call
    var data = {
        context: context,
        checked: checked,
        action: "ai4seo_toggle_automated_generation",
    };

    // Make the ajax call and await the json response
    jQuery.post( ai4seo_admin_ajax_url, data, function( response ) {
        ai4seo_check_response(response)

        // reload page
        window.location.reload();
    });

    if (checked) {
        // add ai4seo-green-animated-progress-bar class to all progress bars that are not finished
        ai4seo_jQuery(".ai4seo-seo-coverage-progress-bar:not(.ai4seo-progress-bar-finished):not(.ai4seo-green-animated-progress-bar)").addClass("ai4seo-gray-animated-progress-bar");
    } else {
        // remove ai4seo-green-animated-progress-bar class from all progress bars that are not started manually
        ai4seo_jQuery(".ai4seo-seo-coverage-progress-bar:not(.ai4seo-started-manually):not(.ai4seo-green-animated-progress-bar)").removeClass("ai4seo-gray-animated-progress-bar");
    }
}

// =========================================================================================== \\

function ai4seo_add_open_edit_metadata_modal_button_to_edit_page_header() {
    // Make sure the header_bar_buttons_container exists
    if (!ai4seo_exists(".edit-post-header .interface-pinned-items")) {
        return;
    }

    // remove old button
    if (ai4seo_exists(".ai4seo-header-builder-button")) {
        ai4seo_jQuery(".ai4seo-header-builder-button").remove();
    }

    // Define variable for the interface-pinned-items element within the edit-post-header-toolbar
    var header_bar_buttons_container = ai4seo_jQuery(".edit-post-header .interface-pinned-items");

    // Read post-id from hidden container if not defined
    var post_id = ai4seo_get_post_id();

    // Make sure post_id is defined
    if (!post_id) {
        return;
    }

    // Generate output
    var output = "";

    // Add button to output
    output += "<button type=\"button\" class=\"components-button has-icon ai4seo-header-builder-button\" aria-label=\"AI for SEO\" title=\"AI for SEO\" onclick='ai4seo_open_metadata_editor_modal(" + post_id + ", true);'>";
        output += "<img src='" + ai4seo_get_ai4seo_plugin_directory_url() + "/assets/images/logos/ai-for-seo-logo-32x32.png' class='ai4seo-icon ai4seo-24x24-icon'>";
    output += "</button>";

    // Add button to header_bar_buttons_container
    header_bar_buttons_container.append(output);
}

// =========================================================================================== \\

function ai4seo_add_open_edit_metadata_modal_button_to_be_builder_navigation() {
    // Make sure the seo_title_element_container exists
    if (!ai4seo_exists(".mfn-meta-seo-title")) {
        return
    }

    // Define variable for the seo-title-element within the be-builder-navigation
    var seo_title_element_container = ai4seo_jQuery(".mfn-meta-seo-title");

    // Read post-id from hidden container if not defined
    var post_id = ai4seo_get_post_id();

    // Make sure post_id is defined
    if (!post_id) {
        return;
    }

    // Generate output
    var output = "";

    // Add button to output
    output += "<button type=\"button\" class=\"ai4seo-generate-button\" aria-label=\"AI for SEO\" title=\"AI for SEO\" onclick='ai4seo_open_metadata_editor_modal(" + post_id + ", true);'>";
        output += "<img src='" + ai4seo_get_ai4seo_plugin_directory_url() + "/assets/images/logos/ai-for-seo-logo-32x32.png' class='ai4seo-icon ai4seo-button-icon ai4seo-logo'> ";
        output += wp.i18n.__("Show all SEO settings", "ai-for-seo");
    output += "</button>";

    // Add button to seo_title_element_container
    seo_title_element_container.before(output);
}

// =========================================================================================== \\

function ai4seo_add_open_edit_metadata_modal_button_to_elementor_navigation() {
    // Make sure the first_elementor_panel_menu_group_container exists
    if (!ai4seo_exists("#elementor-panel-page-menu-content .elementor-panel-menu-group:first-child .elementor-panel-menu-items")) {
        return
    }

    // Define variable for the first elementor-panel-menu-group-element within the elementor-navigation
    var first_elementor_panel_menu_group_container = ai4seo_jQuery("#elementor-panel-page-menu-content .elementor-panel-menu-group:first-child .elementor-panel-menu-items");

    // Read post-id from hidden container if not defined
    var post_id = ai4seo_get_post_id();

    // Make sure post_id is defined
    if (!post_id) {
        return;
    }

    // Generate output
    var output = "";

    // Add button to output
    output += "<button type=\"button\" class=\"ai4seo-generate-button\" aria-label=\"AI for SEO\" title=\"AI for SEO\" onclick='ai4seo_open_metadata_editor_modal(" + post_id + ", true);'>";
        output += "<img src='" + ai4seo_get_ai4seo_plugin_directory_url() + "/assets/images/logos/ai-for-seo-logo-32x32.png' class='ai4seo-icon ai4seo-button-icon ai4seo-logo'> ";
        output += wp.i18n.__("Show all SEO settings", "ai-for-seo");
    output += "</button>";

    // Add button to first_elementor_panel_menu_group_container
    first_elementor_panel_menu_group_container.append(output);
}


// ___________________________________________________________________________________________ \\
// === LICENCE HANDLING ====================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

function ai4seo_submit_new_licence_key() {
    let licence_key = ai4seo_jQuery("#ai4seo-licence-key-input").val();

    if (licence_key.length < 10) {
        ai4seo_show_notification_modal(wp.i18n.__("Please enter a valid licence key.", "ai-for-seo") + " #361222324");
        return;
    }

    // Prepare data for ajax call
    var data = {
        licence_key: licence_key,
        action: "ai4seo_submit_licence_key",
    };

    // Make the ajax call and await the json response
    jQuery.post( ai4seo_admin_ajax_url, data, function( response ) {
        let error_list = {
            371222324: wp.i18n.__("Please enter a valid licence key.", "ai-for-seo") + " #371222324",
            381222324: wp.i18n.__("Please enter a valid licence key.", "ai-for-seo") + " #381222324",
            391222324: wp.i18n.__("Please enter a valid licence key.", "ai-for-seo") + " #391222324",
        };

        if (ai4seo_check_response(response, error_list)) {
            ai4seo_show_notification_modal(wp.i18n.__("Licence key has been saved successfully. Reloading page now...", "ai-for-seo"), wp.i18n.__("Thank you!", "ai-for-seo"));

            // reload page after 2 seconds
            setTimeout(function() {
                window.location.reload();
            }, 2000);
        }
    });
}

// ___________________________________________________________________________________________ \\
// === NOTICES =============================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

// === ADD DISMISS CLICK ACTION TO NOTICE ELEMENT ============================================ \\

// class "ai4seo-notice > notice-dismiss"
jQuery(document).on("click", ".ai4seo-performance-notice > .notice-dismiss", function() {
    // call an ajax function
    let data = {
        action: "ai4seo_dismiss_performance_notice",
    };

    jQuery.post( ai4seo_admin_ajax_url, data, function( response ) {
        ai4seo_check_response(response);
    });
});


// ___________________________________________________________________________________________ \\
// === TERMS OF SERVICE ====================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Toggle the terms of service accept button based on the agreement checkbox state
 */
function ai4seo_toggle_tos_accept_button() {
    let accept_button = ai4seo_jQuery("#ai4seo-accept-tos-button");

    if (accept_button.length) {
        accept_button.prop("disabled", !accept_button.prop("disabled"));
    }
}

// =========================================================================================== \\

/**
 * Show confirmation notification modal to really decline tos
 */
function ai4seo_confirm_to_decline_tos() {
    let headline = wp.i18n.__("Please confirm", "ai-for-seo");
    let content = wp.i18n.__("Are you sure you want to decline the terms of service and uninstall AI for SEO?", "ai-for-seo");
    content += "<br><br>";
    content += wp.i18n.__("<strong>Attention:</strong><br>If you have already purchased a plan, you can cancel it by clicking <a href='https://aiforseo.ai/cancel-plan' target='_blank'>HERE</a>.", "ai-for-seo");
    let decline_button = "<button type='button' class='ai4seo-button ai4seo-abort-button' id='ai4seo-decline-tos-button' onclick='ai4seo_decline_tos();'>" + wp.i18n.__("Yes, please!", "ai-for-seo") + "</button>";
    let back_button = "<button type='button' class='ai4seo-button ai4seo-success-button' onclick='ai4seo_hide_notification_modal();'>" + wp.i18n.__("No, I changed my mind", "ai-for-seo") + "</button>";

    ai4seo_show_notification_modal(content, headline, decline_button + back_button);
}

// =========================================================================================== \\

/**
 * Let the user decline tos, using ajax
 */
function ai4seo_decline_tos() {
    // Prepare data for ajax call
    var data = {
        action: "ai4seo_decline_tos",
    };

    // Make the ajax call and await the json response
    jQuery.post( ai4seo_admin_ajax_url, data, function( response ) {
        ai4seo_check_response(response)

        // reload page
        window.location.reload();
    });

    ai4seo_add_loading_html_to_element(".ai4seo-button");
}

// =========================================================================================== \\

/**
 * Let the user accept tos, using ajax
 */
function ai4seo_accept_tos() {
    // check state of checkbox "ai4seo-enhanced-reporting-checkbox"
    let accepted_enhanced_reporting = ai4seo_jQuery("#ai4seo-enhanced-reporting-checkbox").prop("checked");

    // Prepare data for ajax call
    var data = {
        accepted_enhanced_reporting: accepted_enhanced_reporting,
        action: "ai4seo_accept_tos",
    };

    // Make the ajax call and await the json response
    jQuery.post( ai4seo_admin_ajax_url, data, function( response ) {
        ai4seo_check_response(response)

        // reload page
        window.location.reload();
    });

    ai4seo_add_loading_html_to_element(".ai4seo-button");
}

// =========================================================================================== \\

function ai4seo_does_user_need_to_accept_tos_toc_and_pp() {
    // check for element "ai4seo-user-accepted-tos"
    return !ai4seo_exists("#ai4seo-user-accepted-tos");
}