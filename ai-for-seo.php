<?php
/*
Plugin Name: AI for SEO
Plugin URI: https://aiforseo.ai
Description: One-Click SEO solution. "AI for SEO" helps your website to rank higher in Web Search results.
Version: 1.2.5
Author: spacecodes
Author URI: https://spa.ce.codes
Text Domain: ai-for-seo
Copyright 2024 spacecodes
License: GPLv2 or later
Requires PHP: 7.4
*/

if (!defined("ABSPATH")) {
    exit;
}

const AI4SEO_PLUGIN_VERSION_NUMBER = "1.2.5";
const AI4SEO_TOO_SHORT_CONTENT_LENGTH = 75;
const AI4SEO_MAX_TOTAL_CONTENT_SIZE = 10000;
const AI4SEO_SUPPORT_EMAIL = "info@aiforseo.ai";
const AI4SEO_OFFICIAL_WEBPAGE = "https://aiforseo.ai";
const AI4SEO_OFFICIAL_PRICING_WEBPAGE = "https://aiforseo.ai/pricing";
const AI4SEO_ROBHUB_AUTH_DATA_OPTION_NAME = "ai4seo_robhub_auth_data";
const AI4SEO_ROBHUB_CREDITS_BALANCE_OPTION_NAME = "_ai4seo_robhub_credits_balance";
const AI4SEO_ROBHUB_LAST_CREDITS_BALANCE_CHECK_OPTION_NAME = "_ai4seo_robhub_last_credit_balance_check";
const AI4SEO_POST_META_GENERATED_DATA_META_KEY = "ai4seo_generated_data";
const AI4SEO_POST_META_POST_CONTENT_SUMMARY_META_KEY = "ai4seo_content_summary";
const AI4SEO_APPROXIMATE_METADATA_GENERATION_SPEED = 10.0; // per minute
const AI4SEO_APPROXIMATE_ATTACHMENT_ATTRIBUTES_GENERATION_SPEED = 3.0; // per minute
const AI4SEO_NOTICE_DISMISSAL_TIME = WEEK_IN_SECONDS; // 1 week
const AI4SEO_VERY_LOW_CREDITS_THRESHOLD = 10;
const AI4SEO_LOW_CREDITS_THRESHOLD = 30;
const AI4SEO_MIN_CREDITS_BALANCE = 5; # todo: will be replaced by the users settings based on the quality of the ai generations
const AI4SEO_DAILY_FREE_CREDITS_AMOUNT = 5;
const AI4SEO_CREDITS_FLAT_COST = 5;
const AI4SEO_MONEY_BACK_GUARANTEE_DAYS = 14;
const AI4SEO_SVG_ICONS = array(
    "ai-for-seo-main-menu-icon" => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 500.000000 500.000000"><g transform="translate(0.000000,500.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none"><path d="M2145 4755 l7 -245 -843 -2 -844 -3 -3 -827 -2 -828 -230 0 -230 0 0 -345 0 -345 230 0 230 0 2 -22 c1 -13 2 -387 3 -833 l0 -810 838 -3 837 -2 0 -245 0 -245 350 0 350 0 0 245 0 245 840 0 840 0 2 308 c1 169 1 539 0 822 -1 283 1 522 3 530 4 13 38 15 240 12 l235 -3 0 345 0 346 -237 2 -238 3 -3 828 -2 827 -840 0 -840 0 0 245 0 245 -351 0 -351 0 7 -245z m344 -1143 c5 -10 21 -63 35 -118 36 -142 104 -375 110 -382 10 -10 18 6 31 60 8 29 32 125 55 213 24 88 45 174 47 190 3 17 10 36 15 43 8 9 124 12 519 12 401 0 509 -3 509 -12 -1 -7 1 -168 4 -358 4 -251 8 -1595 6 -1902 0 -17 -43 -18 -754 -18 l-754 0 -97 145 c-54 80 -101 145 -104 145 -4 0 -25 -24 -46 -52 -47 -63 -150 -196 -170 -220 -14 -17 -48 -18 -463 -18 -247 0 -451 3 -455 6 -3 3 2 29 13 58 10 28 34 103 55 166 124 393 661 2012 677 2043 8 16 37 17 383 17 351 0 375 -1 384 -18z"/><path d="M1907 3088 c-102 -299 -189 -553 -247 -718 -63 -179 -195 -563 -210 -613 l-9 -28 141 3 141 3 41 125 41 125 296 3 296 2 39 -130 38 -130 143 0 c79 0 143 2 143 4 0 6 -80 240 -115 336 -14 41 -51 143 -80 225 -29 83 -70 195 -90 250 -37 102 -235 667 -235 672 0 2 -65 3 -144 3 l-143 0 -46 -132z m203 -241 c0 -8 43 -143 95 -302 52 -158 95 -294 95 -301 0 -11 -38 -14 -205 -14 -136 0 -205 4 -205 10 0 25 202 620 211 620 5 0 9 -6 9 -13z"/><path d="M3126 2484 c-3 -404 -4 -740 -1 -745 4 -5 67 -9 141 -9 l134 0 0 745 0 745 -133 0 -134 0 -7 -736z"/></g></svg> ',
    "all-in-one-seo" => '<svg viewBox="0 0 20 20" width="16" height="16" fill="#a7aaad" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.98542 19.9708C15.5002 19.9708 19.9708 15.5002 19.9708 9.98542C19.9708 4.47063 15.5002 0 9.98542 0C4.47063 0 0 4.47063 0 9.98542C0 15.5002 4.47063 19.9708 9.98542 19.9708ZM8.39541 3.65464C8.26016 3.4485 8.0096 3.35211 7.77985 3.43327C7.51816 3.52572 7.26218 3.63445 7.01349 3.7588C6.79519 3.86796 6.68566 4.11731 6.73372 4.36049L6.90493 5.22694C6.949 5.44996 6.858 5.6763 6.68522 5.82009C6.41216 6.04734 6.16007 6.30426 5.93421 6.58864C5.79383 6.76539 5.57233 6.85907 5.35361 6.81489L4.50424 6.6433C4.26564 6.5951 4.02157 6.70788 3.91544 6.93121C3.85549 7.05738 3.79889 7.1862 3.74583 7.31758C3.69276 7.44896 3.64397 7.58105 3.59938 7.71369C3.52048 7.94847 3.61579 8.20398 3.81839 8.34133L4.53958 8.83027C4.72529 8.95617 4.81778 9.1819 4.79534 9.40826C4.75925 9.77244 4.76072 10.136 4.79756 10.4936C4.82087 10.7198 4.72915 10.9459 4.54388 11.0724L3.82408 11.5642C3.62205 11.7022 3.52759 11.9579 3.60713 12.1923C3.69774 12.4593 3.8043 12.7205 3.92615 12.9743C4.03313 13.1971 4.27749 13.3088 4.51581 13.2598L5.36495 13.0851C5.5835 13.0401 5.80533 13.133 5.94623 13.3093C6.16893 13.5879 6.42071 13.8451 6.6994 14.0756C6.87261 14.2188 6.96442 14.4448 6.92112 14.668L6.75296 15.5348C6.70572 15.7782 6.81625 16.0273 7.03511 16.1356C7.15876 16.1967 7.285 16.2545 7.41375 16.3086C7.54251 16.3628 7.67196 16.4126 7.80195 16.4581C8.18224 16.5912 8.71449 16.1147 9.108 15.7625C9.30205 15.5888 9.42174 15.343 9.42301 15.0798C9.42301 15.0784 9.42302 15.077 9.42302 15.0756L9.42301 13.6263C9.42301 13.6109 9.4236 13.5957 9.42476 13.5806C8.26248 13.2971 7.39838 12.2301 7.39838 10.9572V9.41823C7.39838 9.30125 7.49131 9.20642 7.60596 9.20642H8.32584V7.6922C8.32584 7.48312 8.49193 7.31364 8.69683 7.31364C8.90171 7.31364 9.06781 7.48312 9.06781 7.6922V9.20642H11.0155V7.6922C11.0155 7.48312 11.1816 7.31364 11.3865 7.31364C11.5914 7.31364 11.7575 7.48312 11.7575 7.6922V9.20642H12.4773C12.592 9.20642 12.6849 9.30125 12.6849 9.41823V10.9572C12.6849 12.2704 11.7653 13.3643 10.5474 13.6051C10.5477 13.6121 10.5478 13.6192 10.5478 13.6263L10.5478 15.0694C10.5478 15.3377 10.6711 15.5879 10.871 15.7622C11.2715 16.1115 11.8129 16.5837 12.191 16.4502C12.4527 16.3577 12.7086 16.249 12.9573 16.1246C13.1756 16.0155 13.2852 15.7661 13.2371 15.5229L13.0659 14.6565C13.0218 14.4334 13.1128 14.2071 13.2856 14.0633C13.5587 13.8361 13.8107 13.5792 14.0366 13.2948C14.177 13.118 14.3985 13.0244 14.6172 13.0685L15.4666 13.2401C15.7052 13.2883 15.9493 13.1756 16.0554 12.9522C16.1153 12.8261 16.1719 12.6972 16.225 12.5659C16.2781 12.4345 16.3269 12.3024 16.3714 12.1698C16.4503 11.935 16.355 11.6795 16.1524 11.5421L15.4312 11.0532C15.2455 10.9273 15.153 10.7015 15.1755 10.4752C15.2116 10.111 15.2101 9.74744 15.1733 9.38986C15.1499 9.16361 15.2417 8.93757 15.4269 8.811L16.1467 8.31927C16.3488 8.18126 16.4432 7.92558 16.3637 7.69115C16.2731 7.42411 16.1665 7.16292 16.0447 6.90915C15.9377 6.68638 15.6933 6.57462 15.455 6.62366L14.6059 6.79837C14.3873 6.84334 14.1655 6.75048 14.0246 6.57418C13.8019 6.29554 13.5501 6.03832 13.2714 5.80784C13.0982 5.6646 13.0064 5.43858 13.0497 5.2154L13.2179 4.34868C13.2651 4.10521 13.1546 3.85616 12.9357 3.74787C12.8121 3.68669 12.6858 3.62895 12.5571 3.5748C12.4283 3.52065 12.2989 3.47086 12.1689 3.42537C11.9388 3.34485 11.6884 3.44211 11.5538 3.64884L11.0746 4.38475C10.9513 4.57425 10.73 4.66862 10.5082 4.64573C10.1513 4.6089 9.79502 4.61039 9.44459 4.64799C9.22286 4.67177 9.00134 4.57818 8.87731 4.38913L8.39541 3.65464Z" fill="#a7aaad" /></svg>',
    "arrow-up-right-from-square" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32h82.7L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3V192c0 17.7 14.3 32 32 32s32-14.3 32-32V32c0-17.7-14.3-32-32-32H320zM80 32C35.8 32 0 67.8 0 112V432c0 44.2 35.8 80 80 80H400c44.2 0 80-35.8 80-80V320c0-17.7-14.3-32-32-32s-32 14.3-32 32V432c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16H192c17.7 0 32-14.3 32-32s-14.3-32-32-32H80z"/></svg>',
    "betheme" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 50"><text x="5" y="40" font-size="60" font-family="Arial Black" font-weight="bold">Be</text></svg>',
    "bolt" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M349.4 44.6c5.9-13.7 1.5-29.7-10.6-38.5s-28.6-8-39.9 1.8l-256 224c-10 8.8-13.6 22.9-8.9 35.3S50.7 288 64 288H175.5L98.6 467.4c-5.9 13.7-1.5 29.7 10.6 38.5s28.6 8 39.9-1.8l256-224c10-8.8 13.6-22.9 8.9-35.3s-16.6-20.7-30-20.7H272.5L349.4 44.6z"/></svg>',
    "circle-plus" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM232 344V280H168c-13.3 0-24-10.7-24-24s10.7-24 24-24h64V168c0-13.3 10.7-24 24-24s24 10.7 24 24v64h64c13.3 0 24 10.7 24 24s-10.7 24-24 24H280v64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/></svg>',
    "circle-question" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:.4}</style></defs><path class="fa-primary" d="M222.6 128c-23.7 0-44.8 14.9-52.8 37.3l-.4 1.2c-4.4 12.5 2.1 26.2 14.6 30.6s26.2-2.1 30.6-14.6l.4-1.2c1.1-3.2 4.2-5.3 7.5-5.3h58.3c8.4 0 15.1 6.8 15.1 15.1c0 5.4-2.9 10.4-7.6 13.1l-44.3 25.4c-7.5 4.3-12.1 12.2-12.1 20.8V264c0 13.3 10.7 24 24 24c13.1 0 23.8-10.5 24-23.6l32.3-18.5c19.6-11.3 31.7-32.2 31.7-54.8c0-34.9-28.3-63.1-63.1-63.1H222.6zM256 384a32 32 0 1 0 0-64 32 32 0 1 0 0 64z"/><path class="fa-secondary" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3h58.3c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24V250.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1H222.6c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>',
    "circle-up" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM135.1 217.4l107.1-99.9c3.8-3.5 8.7-5.5 13.8-5.5s10.1 2 13.8 5.5l107.1 99.9c4.5 4.2 7.1 10.1 7.1 16.3c0 12.3-10 22.3-22.3 22.3H304v96c0 17.7-14.3 32-32 32H240c-17.7 0-32-14.3-32-32V256H150.3C138 256 128 246 128 233.7c0-6.2 2.6-12.1 7.1-16.3z"/></svg>',
    'image' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/></svg>',
    'image-slash' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/><line x1="0" y1="0" x2="512" y2="512" stroke="black" stroke-width="32" /></svg>',
    "globe" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M352 256c0 22.2-1.2 43.6-3.3 64l-185.3 0c-2.2-20.4-3.3-41.8-3.3-64s1.2-43.6 3.3-64l185.3 0c2.2 20.4 3.3 41.8 3.3 64zm28.8-64l123.1 0c5.3 20.5 8.1 41.9 8.1 64s-2.8 43.5-8.1 64l-123.1 0c2.1-20.6 3.2-42 3.2-64s-1.1-43.4-3.2-64zm112.6-32l-116.7 0c-10-63.9-29.8-117.4-55.3-151.6c78.3 20.7 142 77.5 171.9 151.6zm-149.1 0l-176.6 0c6.1-36.4 15.5-68.6 27-94.7c10.5-23.6 22.2-40.7 33.5-51.5C239.4 3.2 248.7 0 256 0s16.6 3.2 27.8 13.8c11.3 10.8 23 27.9 33.5 51.5c11.6 26 20.9 58.2 27 94.7zm-209 0L18.6 160C48.6 85.9 112.2 29.1 190.6 8.4C165.1 42.6 145.3 96.1 135.3 160zM8.1 192l123.1 0c-2.1 20.6-3.2 42-3.2 64s1.1 43.4 3.2 64L8.1 320C2.8 299.5 0 278.1 0 256s2.8-43.5 8.1-64zM194.7 446.6c-11.6-26-20.9-58.2-27-94.6l176.6 0c-6.1 36.4-15.5 68.6-27 94.6c-10.5 23.6-22.2 40.7-33.5 51.5C272.6 508.8 263.3 512 256 512s-16.6-3.2-27.8-13.8c-11.3-10.8-23-27.9-33.5-51.5zM135.3 352c10 63.9 29.8 117.4 55.3 151.6C112.2 482.9 48.6 426.1 18.6 352l116.7 0zm358.1 0c-30 74.1-93.6 130.9-171.9 151.6c25.5-34.2 45.2-87.7 55.3-151.6l116.7 0z"/></svg>',
    "headline" => '<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" rx="15" ry="15"/><rect x="15" y="20" width="40" height="10" rx="5" ry="5" fill="#ffffff"/><rect x="60" y="20" width="20" height="10" rx="5" ry="5" fill="#ffffff"/></svg>',
    "magnifying-glass" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg> ',
    "rank-math" => '<svg viewBox="0 0 462.03 462.03" xmlns="http://www.w3.org/2000/svg" width="20"><g fill="#a7aaad"><path d="m462 234.84-76.17 3.43 13.43 21-127 81.18-126-52.93-146.26 60.97 10.14 24.34 136.1-56.71 128.57 54 138.69-88.61 13.43 21z"/><path d="m54.1 312.78 92.18-38.41 4.49 1.89v-54.58h-96.67zm210.9-223.57v235.05l7.26 3 89.43-57.05v-181zm-105.44 190.79 96.67 40.62v-165.19h-96.67z"/></g></svg>',
    "rotate" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:.4}</style></defs><path class="fa-primary" d="M105.1 202.6c7.7-21.8 20.2-42.3 37.8-59.8c62.2-62.2 162.7-62.5 225.3-1L327 183c-6.9 6.9-8.9 17.2-5.2 26.2s12.5 14.8 22.2 14.8H463.5c0 0 0 0 0 0H472c13.3 0 24-10.7 24-24V72c0-9.7-5.8-18.5-14.8-22.2s-19.3-1.7-26.2 5.2L413.4 96.6c-87.6-86.5-228.7-86.2-315.8 1C73.2 122 55.6 150.7 44.8 181.4c-5.9 16.7 2.9 34.9 19.5 40.8s34.9-2.9 40.8-19.5z"/><path class="fa-secondary" d="M16 319.6l0-7.6c0-13.3 10.7-24 24-24h7.6c.2 0 .5 0 .7 0H168c9.7 0 18.5 5.8 22.2 14.8s1.7 19.3-5.2 26.2l-41.1 41.1c62.6 61.5 163.1 61.2 225.3-1c17.5-17.5 30.1-38 37.8-59.8c5.9-16.7 24.2-25.4 40.8-19.5s25.4 24.2 19.5 40.8c-10.8 30.6-28.4 59.3-52.9 83.8c-87.2 87.2-228.3 87.5-315.8 1L57 457c-6.9 6.9-17.2 8.9-26.2 5.2S16 449.7 16 440l0-119.6c0-.2 0-.5 0-.7z"/></svg>',
    "pen-to-square" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:.4}</style></defs><path class="fa-primary" d="M392.4 21.7L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0zM339.7 74.3L172.4 241.7c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3z"/><path class="fa-secondary" d="M0 160c0-53 43-96 96-96h96c17.7 0 32 14.3 32 32s-14.3 32-32 32H96c-17.7 0-32 14.3-32 32V416c0 17.7 14.3 32 32 32H352c17.7 0 32-14.3 32-32V320c0-17.7 14.3-32 32-32s32 14.3 32 32v96c0 53-43 96-96 96H96c-53 0-96-43-96-96V160z"/></svg>',
    "rocket" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M156.6 384.9L125.7 354c-8.5-8.5-11.5-20.8-7.7-32.2c3-8.9 7-20.5 11.8-33.8L24 288c-8.6 0-16.6-4.6-20.9-12.1s-4.2-16.7 .2-24.1l52.5-88.5c13-21.9 36.5-35.3 61.9-35.3l82.3 0c2.4-4 4.8-7.7 7.2-11.3C289.1-4.1 411.1-8.1 483.9 5.3c11.6 2.1 20.6 11.2 22.8 22.8c13.4 72.9 9.3 194.8-111.4 276.7c-3.5 2.4-7.3 4.8-11.3 7.2l0 82.3c0 25.4-13.4 49-35.3 61.9l-88.5 52.5c-7.4 4.4-16.6 4.5-24.1 .2s-12.1-12.2-12.1-20.9l0-107.2c-14.1 4.9-26.4 8.9-35.7 11.9c-11.2 3.6-23.4 .5-31.8-7.8zM384 168a40 40 0 1 0 0-80 40 40 0 1 0 0 80z"/></svg>',
    "seopress" => '<svg id="uuid-4f6a8a41-18e3-4f77-b5a9-4b1b38aa2dc9" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 899.655 494.3094"><path id="uuid-a155c1ca-d868-4653-8477-8dd87240a765" d="M327.3849,435.128l-299.9999-.2497c-16.2735,1.1937-28.4981,15.3538-27.3044,31.6273,1.0719,14.6128,12.6916,26.2325,27.3044,27.3044l299.9999,.2497c16.2735-1.1937,28.4981-15.3538,27.3044-31.6273-1.0718-14.6128-12.6916-26.2325-27.3044-27.3044Z" style="fill:#fff"/><path id="uuid-e30ba4c6-4769-466b-a03a-e644c5198e56" d="M27.3849,58.9317l299.9999,.2497c16.2735-1.1937,28.4981-15.3537,27.3044-31.6273-1.0718-14.6128-12.6916-26.2325-27.3044-27.3044L27.3849,0C11.1114,1.1937-1.1132,15.3537,.0805,31.6273c1.0719,14.6128,12.6916,26.2325,27.3044,27.3044Z" style="fill:#fff"/><path id="uuid-2bbd52d6-aec1-4689-9d4c-23c35d4f22b8" d="M652.485,.2849c-124.9388,.064-230.1554,93.4132-245.1001,217.455H27.3849c-16.2735,1.1937-28.4981,15.3537-27.3044,31.6272,1.0719,14.6128,12.6916,26.2325,27.3044,27.3044H407.3849c16.2298,135.4454,139.187,232.0888,274.6323,215.8589,135.4455-16.2298,232.0888-139.1869,215.8589-274.6324C882.9921,93.6834,777.5884,.2112,652.485,.2849Zm0,433.4217c-102.9754,0-186.4533-83.478-186.4533-186.4533,0-102.9753,83.4781-186.4533,186.4533-186.4533,102.9754,0,186.4533,83.478,186.4533,186.4533,.0524,102.9753-83.383,186.4959-186.3583,186.5483-.0316,0-.0634,0-.0951,0v-.095Z" style="fill:#fff"/></svg>',
    "square-facebook" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64h98.2V334.2H109.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H255V480H384c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64z"/></svg>',
    "square-twitter-x" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm297.1 84L257.3 234.6 379.4 396H283.8L209 298.1 123.3 396H75.8l111-126.9L69.7 116h98l67.7 89.5L313.6 116h47.5zM323.3 367.6L153.4 142.9H125.1L296.9 367.6h26.3z"/></svg>',
    "square-xmark" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm79 143c9.4-9.4 24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-47 47 47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9z"/></svg>',
    "subtitle" => '<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" rx="15" ry="15"/><rect x="15" y="70" width="40" height="10" rx="5" ry="5" fill="#ffffff"/><rect x="60" y="70" width="20" height="10" rx="5" ry="5" fill="#ffffff"/></svg>',
    "subtitles" => '<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" rx="15" ry="15"/><rect x="15" y="50" width="30" height="10" rx="5" ry="5" fill="#ffffff"/><rect x="55" y="50" width="30" height="10" rx="5" ry="5" fill="#ffffff"/><rect x="15" y="70" width="40" height="10" rx="5" ry="5" fill="#ffffff"/><rect x="60" y="70" width="20" height="10" rx="5" ry="5" fill="#ffffff"/></svg>',
    "triangle-exclamation" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 32c14.2 0 27.3 7.5 34.5 19.8l216 368c7.3 12.4 7.3 27.7 .2 40.1S486.3 480 472 480H40c-14.3 0-27.6-7.7-34.7-20.1s-7-27.8 .2-40.1l216-368C228.7 39.5 241.8 32 256 32zm0 128c-13.3 0-24 10.7-24 24V296c0 13.3 10.7 24 24 24s24-10.7 24-24V184c0-13.3-10.7-24-24-24zm32 224a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/></svg>',
    "xmark" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><defs><style>.fa-secondary{opacity:.4}</style></defs><path class="fa-secondary" d="M297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6z"/></svg>',
    "yoast" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M91.3 76h186l-7 18.9h-179c-39.7 0-71.9 31.6-71.9 70.3v205.4c0 35.4 24.9 70.3 84 70.3V460H91.3C41.2 460 0 419.8 0 370.5V165.2C0 115.9 40.7 76 91.3 76zm229.1-56h66.5C243.1 398.1 241.2 418.9 202.2 459.3c-20.8 21.6-49.3 31.7-78.3 32.7v-51.1c49.2-7.7 64.6-49.9 64.6-75.3 0-20.1 .6-12.6-82.1-223.2h61.4L218.2 299 320.4 20zM448 161.5V460H234c6.6-9.6 10.7-16.3 12.1-19.4h182.5V161.5c0-32.5-17.1-51.9-48.2-62.9l6.7-17.6c41.7 13.6 60.9 43.1 60.9 80.5z"/></svg>',
);
const AI4SEO_STRIPE_BILLING_URL = "https://aiforseo.ai/manage-plan";
const AI4SEO_POST_TYPES_TAB_NAME = "post";

// the robhub api communicator is used to communicate with the robhub api which handles all the ai stuff
$ai4seo_robhub_api_communicator = null;

// Constants for the wp_options entries
const AI4SEO_FULLY_COVERED_METADATA_POST_IDS = "ai4seo_fully_covered_metadata_post_ids";
const AI4SEO_MISSING_METADATA_POST_IDS = "ai4seo_missing_metadata_post_ids";
const AI4SEO_PENDING_METADATA_POST_IDS = "ai4seo_pending_metadata_post_ids";
const AI4SEO_PROCESSING_METADATA_POST_IDS = "ai4seo_processing_metadata_post_ids";
const AI4SEO_GENERATED_METADATA_POST_IDS = "ai4seo_generated_metadata_post_ids";
const AI4SEO_FAILED_METADATA_POST_IDS = "ai4seo_failed_metadata_post_ids";

const AI4SEO_FULLY_COVERED_ATTACHMENT_ATTRIBUTES_POST_IDS = "ai4seo_fully_covered_attachment_attributes_post_ids";
const AI4SEO_MISSING_ATTACHMENT_ATTRIBUTES_POST_IDS = "ai4seo_missing_attachment_attributes_post_ids";
const AI4SEO_PENDING_ATTACHMENT_ATTRIBUTES_POST_IDS = "ai4seo_pending_attachment_attributes_post_ids";
const AI4SEO_PROCESSING_ATTACHMENT_ATTRIBUTES_POST_IDS = "ai4seo_processing_attachment_attributes_post_ids";
const AI4SEO_GENERATED_ATTACHMENT_ATTRIBUTES_POST_IDS = "ai4seo_generated_attachment_attributes_post_ids";
const AI4SEO_FAILED_ATTACHMENT_ATTRIBUTES_POST_IDS = "ai4seo_failed_attachment_attributes_post_ids";

// all wp_options that contain post ids
const AI4SEO_ALL_POST_ID_OPTIONS = array(
    AI4SEO_MISSING_METADATA_POST_IDS,
    AI4SEO_GENERATED_METADATA_POST_IDS,
    AI4SEO_FULLY_COVERED_METADATA_POST_IDS,
    AI4SEO_PENDING_METADATA_POST_IDS,
    AI4SEO_PROCESSING_METADATA_POST_IDS,
    AI4SEO_FAILED_METADATA_POST_IDS,

    AI4SEO_MISSING_ATTACHMENT_ATTRIBUTES_POST_IDS,
    AI4SEO_GENERATED_ATTACHMENT_ATTRIBUTES_POST_IDS,
    AI4SEO_FULLY_COVERED_ATTACHMENT_ATTRIBUTES_POST_IDS,
    AI4SEO_PENDING_ATTACHMENT_ATTRIBUTES_POST_IDS,
    AI4SEO_PROCESSING_ATTACHMENT_ATTRIBUTES_POST_IDS,
    AI4SEO_FAILED_ATTACHMENT_ATTRIBUTES_POST_IDS,
);

// all wp_options that define the seo coverage
// a post id cannot be in MISSING and one of the other options at the same time
const AI4SEO_SEO_COVERAGE_POST_ID_OPTIONS = array(
    AI4SEO_MISSING_METADATA_POST_IDS,
    AI4SEO_FULLY_COVERED_METADATA_POST_IDS,
    AI4SEO_GENERATED_METADATA_POST_IDS,

    AI4SEO_MISSING_ATTACHMENT_ATTRIBUTES_POST_IDS,
    AI4SEO_FULLY_COVERED_ATTACHMENT_ATTRIBUTES_POST_IDS,
    AI4SEO_GENERATED_ATTACHMENT_ATTRIBUTES_POST_IDS,
);

// all wp-Options that define the generation status of a given post
// a post id cannot be in PENDING and PROCESSING at the same time
const AI4SEO_GENERATION_STATUS_POST_ID_OPTIONS = array(
    AI4SEO_PENDING_METADATA_POST_IDS,
    AI4SEO_PROCESSING_METADATA_POST_IDS,
    AI4SEO_FAILED_METADATA_POST_IDS,

    AI4SEO_PENDING_ATTACHMENT_ATTRIBUTES_POST_IDS,
    AI4SEO_PROCESSING_ATTACHMENT_ATTRIBUTES_POST_IDS,
    AI4SEO_FAILED_ATTACHMENT_ATTRIBUTES_POST_IDS,
);

// Constants for third party plugin identifiers
// editors
const AI4SEO_THIRD_PARTY_PLUGIN_ELEMENTOR = "elementor";

// shops
const AI4SEO_THIRD_PARTY_PLUGIN_WOOCOMMERCE = "woocommerce";

// traditional seo plugins
const AI4SEO_THIRD_PARTY_PLUGIN_YOAST_SEO = "yoast-seo";
const AI4SEO_THIRD_PARTY_PLUGIN_ALL_IN_ONE_SEO = "all-in-one-seo-pack";
const AI4SEO_THIRD_PARTY_PLUGIN_THE_SEO_FRAMEWORK = "the-seo-framework";
const AI4SEO_THIRD_PARTY_PLUGIN_RANK_MATH = "rank-math";
const AI4SEO_THIRD_PARTY_PLUGIN_SEOPRESS = "seopress";
const AI4SEO_THIRD_PARTY_PLUGIN_SEO_SIMPLE_PACK = "seo-simple-pack";
const AI4SEO_THIRD_PARTY_PLUGIN_SLIM_SEO = "slim-seo";
const AI4SEO_THIRD_PARTY_PLUGIN_SQUIRRLY_SEO = "squirrly-seo";

// editors + seo plugins
const AI4SEO_THIRD_PARTY_PLUGIN_BETHEME = "betheme";

// social media plugins
const AI4SEO_THIRD_PARTY_PLUGIN_BLOG2SOCIAL = "blog2social";

const AI4SEO_THIRD_PARTY_SEO_PLUGIN_DETAILS = array(
    AI4SEO_THIRD_PARTY_PLUGIN_YOAST_SEO => array(
        'name' => 'Yoast SEO',
        'icon' => 'yoast',
        'icon-css-class' => 'ai4seo-purple-icon',
        'keyphrase-postmeta-key' => '_yoast_wpseo_focuskw',
        'metadata-postmeta-keys' => array(
            'meta-title' => '_yoast_wpseo_title',
            'meta-description' => '_yoast_wpseo_metadesc',
            'facebook-title' => '_yoast_wpseo_opengraph-title',
            'facebook-description' => '_yoast_wpseo_opengraph-description',
            'twitter-title' => '_yoast_wpseo_twitter-title',
            'twitter-description' => '_yoast_wpseo_twitter-description',
        ),
    ),
    AI4SEO_THIRD_PARTY_PLUGIN_BETHEME => array(
        'name' => 'BeTheme',
        'icon' => 'betheme',
        'icon-css-class' => 'ai4seo-blue-icon',
        'metadata-postmeta-keys' => array(
            'meta-title' => 'mfn-meta-seo-title',
            'meta-description' => 'mfn-meta-seo-description',
        ),
    ),
    AI4SEO_THIRD_PARTY_PLUGIN_ALL_IN_ONE_SEO => array(
        'name' => 'All in One SEO',
        'icon' => 'all-in-one-seo',
        'metadata-postmeta-keys' => array( # workaround: in addition, this plugin saves its data into wp_ai4seo_posts
            'meta-title' => '_aioseo_title',
            'meta-description' => '_aioseo_description',
            'facebook-title' => '_aioseo_og_title',
            'facebook-description' => '_aioseo_og_description',
            'twitter-title' => '_aioseo_twitter_title',
            'twitter-description' => '_aioseo_twitter_description',
        ),
    ),
    AI4SEO_THIRD_PARTY_PLUGIN_RANK_MATH => array(
        'name' => 'Rank Math',
        'icon' => 'rank-math',
        'icon-css-class' => 'ai4seo-purple-icon',
        'seo-score-postmeta-key' => 'rank_math_seo_score', # todo: make this dynamic
        'keyphrase-postmeta-key' => 'rank_math_focus_keyword',
        'metadata-postmeta-keys' => array(
            'meta-title' => 'rank_math_title',
            'meta-description' => 'rank_math_description',
            'facebook-title' => 'rank_math_facebook_title',
            'facebook-description' => 'rank_math_facebook_description',
            'twitter-title' => 'rank_math_twitter_title',
            'twitter-description' => 'rank_math_twitter_description',
        ),
    ),
    AI4SEO_THIRD_PARTY_PLUGIN_SEO_SIMPLE_PACK => array(
        'name' => 'SEO Simple Pack',
        'metadata-postmeta-keys' => array(
            'meta-title' => 'ssp_meta_title',
            'meta-description' => 'ssp_meta_description',
            'facebook-title' => 'ssp_meta_title',
            'facebook-description' => 'ssp_meta_description',
            'twitter-title' => 'ssp_meta_title',
            'twitter-description' => 'ssp_meta_description',
        ),
    ),
    AI4SEO_THIRD_PARTY_PLUGIN_SEOPRESS => array(
        'name' => 'SEOPress',
        'icon' => 'seopress',
        'metadata-postmeta-keys' => array(
            'meta-title' => '_seopress_titles_title',
            'meta-description' => '_seopress_titles_desc',
            'facebook-title' => '_seopress_social_fb_title',
            'facebook-description' => '_seopress_social_fb_desc',
            'twitter-title' => '_seopress_social_twitter_title',
            'twitter-description' => '_seopress_social_twitter_desc',
        ),
    ),
    AI4SEO_THIRD_PARTY_PLUGIN_SLIM_SEO => array(
        'name' => 'Slim SEO',
        'metadata-postmeta-keys' => array(
            'meta-title' => '_ai4seo_workaround',
            'meta-description' => '_ai4seo_workaround',
        ),
    ),
    AI4SEO_THIRD_PARTY_PLUGIN_SQUIRRLY_SEO => array(
        'name' => 'Squirrly SEO',
        'metadata-postmeta-keys' => array(
            'meta-title' => '_ai4seo_workaround',
            'meta-description' => '_ai4seo_workaround',
            'facebook-title' => '_ai4seo_workaround',
            'facebook-description' => '_ai4seo_workaround',
            'twitter-title' => '_ai4seo_workaround',
            'twitter-description' => '_ai4seo_workaround',
        ),
    ),
    AI4SEO_THIRD_PARTY_PLUGIN_THE_SEO_FRAMEWORK => array(
        'name' => 'The SEO Framework',
        'metadata-postmeta-keys' => array(
            'meta-title' => '_genesis_title',
            'meta-description' => '_genesis_description',
            'facebook-title' => '_open_graph_title',
            'facebook-description' => '_open_graph_description',
            'twitter-title' => '_twitter_title',
            'twitter-description' => '_twitter_description',
        ),
    ),
    AI4SEO_THIRD_PARTY_PLUGIN_BLOG2SOCIAL => array(
        'name' => 'Blog2Social',
        'metadata-postmeta-keys' => array(
            'facebook-title' => '_ai4seo_workaround',
            'facebook-description' => '_ai4seo_workaround',
            'twitter-title' => '_ai4seo_workaround',
            'twitter-description' => '_ai4seo_workaround',
        ),
    ),
);

// Variable for the plugin settings
/**
 * How to create new settings:
 * 1. Create a Constant below
 * 2. Define a default Setting in $ai4seo_default_settings
 * 3. Define a validation in ai4seo_validate_setting_value()
 * 4. If the setting is changeable in the Settings menu: Go to settings.php and
 *  - add the Constant to $ai4seo_all_known_changeable_settings_names and then
 *  - create the output of the setting at the bottom of the file
 */
const AI4SEO_SETTING_META_TAG_OUTPUT_MODE = 'meta_tags_output_method';
const AI4SEO_SETTING_APPLY_CHANGES_TO_THIRD_PARTY_SEO_PLUGINS = 'apply_changes_to_this_party_seo_plugins';
const AI4SEO_SETTING_ALLOWED_USER_ROLES = 'allowed_user_roles';
const AI4SEO_SETTING_ENABLED_AUTOMATED_GENERATIONS = 'enabled_automated_generations';
const AI4SEO_SETTING_METADATA_GENERATION_LANGUAGE = 'metadata_generation_language';
const AI4SEO_SETTING_ATTACHMENT_ATTRIBUTES_GENERATION_LANGUAGE = 'attachment_attributes_generation_language';
const AI4SEO_SETTING_VISIBLE_META_TAGS = 'visible_meta_tags';
const AI4SEO_SETTING_OVERWRITE_EXISTING_METADATA = 'overwrite_existing_metadata';
const AI4SEO_SETTING_OVERWRITE_EXISTING_ATTACHMENT_ATTRIBUTES = 'overwrite_existing_attachment_attributes';

$ai4seo_default_settings = array(
    AI4SEO_SETTING_META_TAG_OUTPUT_MODE => "replace",
    AI4SEO_SETTING_APPLY_CHANGES_TO_THIRD_PARTY_SEO_PLUGINS => array(AI4SEO_THIRD_PARTY_PLUGIN_YOAST_SEO, AI4SEO_THIRD_PARTY_PLUGIN_RANK_MATH, AI4SEO_THIRD_PARTY_PLUGIN_SEOPRESS, AI4SEO_THIRD_PARTY_PLUGIN_THE_SEO_FRAMEWORK, AI4SEO_THIRD_PARTY_PLUGIN_SEO_SIMPLE_PACK),
    AI4SEO_SETTING_ALLOWED_USER_ROLES => array("administrator"),
    AI4SEO_SETTING_ENABLED_AUTOMATED_GENERATIONS => array(),
    AI4SEO_SETTING_METADATA_GENERATION_LANGUAGE => "auto",
    AI4SEO_SETTING_ATTACHMENT_ATTRIBUTES_GENERATION_LANGUAGE => "auto",
    AI4SEO_SETTING_VISIBLE_META_TAGS => array("meta-description", "facebook-title", "facebook-description", "twitter-title", "twitter-description"),
    AI4SEO_SETTING_OVERWRITE_EXISTING_METADATA => array(),
    AI4SEO_SETTING_OVERWRITE_EXISTING_ATTACHMENT_ATTRIBUTES => array("title"),
);

$ai4seo_settings = $ai4seo_default_settings;

$ai4seo_fallback_allowed_user_roles = array("administrator" => "Administrator");
$ai4seo_forbidden_allowed_user_roles = array("subscriber", "customer");
define("AI4SEO_SETTING_META_TAG_OUTPUT_MODE_ALLOWED_VALUES",
    array(
        "disable" => __("Disable 'AI for SEO' Meta Tags", "ai-for-seo"),
        "force" => __("Force 'AI for SEO' Meta Tags", "ai-for-seo"),
        "replace" => __("Replace Existing Meta Tags", "ai-for-seo"),
        "complement" => __("Complement Existing Meta Tags", "ai-for-seo"),
    )
);

// used to store various details about all supported metadata fields to use it on many places throughout the plugin
$ai4seo_metadata_details = array(
    "meta-title" => array(
        "name" => __("Meta Title", "ai-for-seo"),
        "icon" => "globe",
        "input" => "textfield",
        "hint" => __("<strong>Best Practice:</strong> A unique and concise title for this entry, which will be displayed on search engine results pages (SERPs) and in the browser tab. This helps users understand your content and enhances visibility.<br><br>The AI aims to generate a meta title with an optimal length of <strong>50 to 60</strong> characters.<br><br>The meta title is added to the <strong>title tag</strong> of your website.", "ai-for-seo"),
        "api-identifier" => "meta-title",
        "output-tag-type" => "title",
        "output-tag-identifier" => "",
        "meta-tag-regex" => '/<title>(.*?)<\/title>/is',
        "meta-tag-regex-match-index" => 1,
    ),
    "meta-description" => array(
        "name" => __("Meta Description", "ai-for-seo"),
        "icon" => "globe",
        "input" => "textarea",
        "hint" => __("<strong>Best Practice:</strong> A compelling and relevant meta description for your page or post, which will appear on search engine results pages (SERPs) beneath the meta title. This description provides a summary of your content, helping to attract clicks and improve visibility.<br><br>The AI aims to generate a meta description with an optimal length of <strong>135 to 150</strong> characters.<br><br>The meta description is added to the <strong>meta description tag</strong> of your website.", "ai-for-seo"),
        "api-identifier" => "meta-description",
        "output-tag-type" => "meta name",
        "output-tag-identifier" => "description",
        "meta-tag-regex" => '/<meta\s+[^>]*name=(["\'])description\1[^>]*content=(["\'])(.*?)\2[^>]*>/i',
        "meta-tag-regex-match-index" => 3,
    ),
    "facebook-title" => array(
        "name" => __("Facebook Title", "ai-for-seo"),
        "icon" => "square-facebook",
        "input" => "textfield",
        "hint" => __("<strong>Best Practice:</strong> This title will be displayed as the headline in the preview when your content is shared on Facebook, helping to capture attention and increase engagement.<br><br>The AI aims to generate a Facebook title with an optimal length of <strong>50 to 60</strong> characters.<br><br>The Facebook title is added to the <strong>og:title tag</strong> of your website.", "ai-for-seo"),
        "api-identifier" => "social-media-title",
        "output-tag-type" => "meta property",
        "output-tag-identifier" => "og:title",
        "meta-tag-regex" => '/<meta\s+[^>]*property=(["\'])og:title\1[^>]*content=(["\'])(.*?)\2[^>]*>/i',
        "meta-tag-regex-match-index" => 3,
    ),
    "facebook-description" => array(
        "name" => __("Facebook Description", "ai-for-seo"),
        "icon" => "square-facebook",
        "input" => "textarea",
        "hint" => __("<strong>Best Practice:</strong> This description will appear in the preview when your content is shared, providing a summary that encourages users to engage with your content.<br><br>The AI aims to generate a Facebook description with an optimal length of <strong>55 to 65</strong> characters.<br><br>The Facebook description is added to the <strong>og:description tag</strong> of your website.", "ai-for-seo"),
        "api-identifier" => "social-media-description",
        "output-tag-type" => "meta property",
        "output-tag-identifier" => "og:description",
        "meta-tag-regex" => '/<meta\s+[^>]*property=(["\'])og:description\1[^>]*content=(["\'])(.*?)\2[^>]*>/i',
        "meta-tag-regex-match-index" => 3,
    ),
    "twitter-title" => array(
        "name" => __("Twitter/X Title", "ai-for-seo"),
        "icon" => "square-twitter-x",
        "input" => "textfield",
        "hint" => __("<strong>Best Practice:</strong> An attention-grabbing title for your page or post, optimized for sharing on Twitter. This title will be displayed as the headline in the preview when your content is tweeted, helping to increase visibility and encourage clicks.<br><br>The AI aims to generate a Twitter/X title with an optimal length of <strong>50 to 60</strong> characters.<br><br>The Twitter/X title is added to the <strong>twitter:title tag</strong> of your website.", "ai-for-seo"),
        "api-identifier" => "social-media-title",
        "output-tag-type" => "meta name",
        "output-tag-identifier" => "twitter:title",
        "meta-tag-regex" => '/<meta\s+[^>]*name=(["\'])twitter:title\1[^>]*content=(["\'])(.*?)\2[^>]*>/i',
        "meta-tag-regex-match-index" => 3,
    ),
    "twitter-description" => array(
        "name" => __("Twitter/X Description", "ai-for-seo"),
        "icon" => "square-twitter-x",
        "input" => "textarea",
        "hint" => __("<strong>Best Practice:</strong> A concise and engaging description for your page or post, optimized for sharing on Twitter/X. This description will appear in the preview when your content is tweeted, providing a brief summary that encourages users to click and interact.<br><br>The AI aims to generate a Twitter/X description with an optimal length of <strong>55 to 65</strong> characters.<br><br>The Twitter/X description is added to the <strong>twitter:description tag</strong> of your website.", "ai-for-seo"),
        "api-identifier" => "social-media-description",
        "output-tag-type" => "meta name",
        "output-tag-identifier" => "twitter:description",
        "meta-tag-regex" => '/<meta\s+[^>]*name=(["\'])twitter:description\1[^>]*content=(["\'])(.*?)\2[^>]*>/i',
        "meta-tag-regex-match-index" => 3,
    ),
);

$ai4seo_attachments_attributes_details = array(
    "title" => array(
        "name" => __("Title", "ai-for-seo"),
        "icon" => "headline",
        "mime-type-restrictions" => array(),
        "input-type" => "textfield",
        "hint" => __("<strong>Best Practice:</strong> A descriptive and unique title for your image that helps users and search engines understand the content of the image. This title is displayed when the image is loaded in the browser and may be used as the default filename if someone downloads the image.<br><br>The AI aims to generate an image title with an optimal length of <strong>20 to 50</strong> characters.<br><br>The image title is not directly visible on your website but is stored in the <strong>image metadata</strong>.  A well-crafted title can aid in organizing your media library and improve searchability within WordPress.", "ai-for-seo"),
    ),
    "alt-text" => array(
        "name" => __("Alt Text", "ai-for-seo"),
        "icon" => "image-slash",
        "mime-type-restrictions" => array(
            "image/jpeg",
            "image/gif",
            "image/png",
            "image/bmp",
            "image/tiff",
            "image/webp",
            "image/avif",
            "image/x-icon",
            "image/heic",
        ),
        "input-type" => "textarea",
        "hint" => __("<strong>Best Practice:</strong> An informative and clear alt text for your image that describes its content and function. This text is used by screen readers to assist visually impaired users and is displayed in place of the image if it cannot be loaded. It also contributes to SEO by providing context to search engines.<br><br>The AI aims to generate alt text with an optimal length of <strong>145 to 155</strong> characters.<br><br>Alt text is added to the <strong>alt attribute</strong> of the image HTML tag.", "ai-for-seo"),
    ),
    "caption" => array(
        "name" => __("Caption", "ai-for-seo"),
        "icon" => "subtitle",
        "mime-type-restrictions" => array(),
        "input-type" => "textarea",
        "hint" => __("<strong>Best Practice:</strong> A brief and engaging caption for your image that provides additional context or credit information. Captions are typically displayed below the image on your website and can help enhance user engagement and provide useful information.<br><br>The AI aims to generate a caption with an optimal length of <strong>50 to 125</strong> characters.<br><br>The caption is added to the <strong>caption field</strong> in the WordPress Media Library and is displayed directly on the page where the image appears.", "ai-for-seo"),
    ),
    "description" => array(
        "name" => __("Description", "ai-for-seo"),
        "icon" => "subtitles",
        "mime-type-restrictions" => array(),
        "input-type" => "textarea",
        "hint" => __("<strong>Best Practice:</strong> A detailed and informative description of your image, which helps users understand the image's content and context. This description is particularly useful for internal reference and can aid in organizing and managing your media library.<br><br>The AI aims to generate a description with an optimal length of <strong>155 to 165</strong> characters.<br><br>The description is stored in the <strong>image metadata</strong> and is not directly visible to users on your website. A well-crafted description can aid in organizing your media library and improve searchability within WordPress.", "ai-for-seo"),
    ),
    #"file-name" => array(
    #    "name" => __("File Name", "ai-for-seo"),
    #    "mime-type-restrictions" => array(),
    #    "input-type" => "textfield",
    #    "hint" => __("The AI will generate a file name for your image based on its content. A descriptive file name can improve SEO and help search engines understand the image. Review the file name to ensure it accurately reflects the image.", "ai-for-seo"),
    #),
);

$ai4seo_translated_language_names = array (
    "en" => __("English", "ai-for-seo"),
    "de" => __("German", "ai-for-seo"),
    "fr" => __("French", "ai-for-seo"),
    "es" => __("Spanish", "ai-for-seo"),
    "it" => __("Italian", "ai-for-seo"),
    "nl" => __("Dutch", "ai-for-seo"),
    "pt" => __("Portuguese", "ai-for-seo"),
    "ru" => __("Russian", "ai-for-seo"),
    "zh" => __("Chinese", "ai-for-seo"),
    "ja" => __("Japanese", "ai-for-seo"),
    "ko" => __("Korean", "ai-for-seo"),
);

$ai4seo_allowed_html_tags_and_attributes = array(
    "div" => array(
        "id" => array(),
        "class" => array(),
        "onclick" => array(),
        "style" => array(),
        "title" => array(),
    ),
    "img" => array(
        "class" => array(),
        "src" => array(),
        "alt" => array(),
        "onclick" => array(),
        "style" => array(),
    ),
    'meta' => array(
        'name' => array(),
        'content' => array(),
        'property' => array(),
    ),
    'title' => array(),
    'svg' => array(
        'viewbox' => array(),
        'aria-label' => array(),
        'class' => array(),
        'xmlns' => array(),
    ),
    'rect' => array(
        'width' => array(),
        'height' => array(),
        'rx' => array(),
        'ry' => array(),
        'x' => array(),
        'y' => array(),
        'fill' => array(),
    ),
    'line' => array(
        'x1' => array(),
        'y1' => array(),
        'x2' => array(),
        'y2' => array(),
        'stroke' => array(),
        'stroke-width' => array(),
    ),
    'defs' => array(),
    'style' => array(),
    'path' => array(
        'class' => array(),
        'd' => array(),
        'fill-rule' => array(),
        'fill' => array(),
        'clip-rule' => array(),
    ),
    'g' => array(
        'class' => array(),
    ),
    'circle' => array(
        'cx' => array(),
        'cy' => array(),
        'r' => array(),
        'fill' => array(),
    ),
    'polygon' => array(
        'points' => array(),
        'fill' => array(),
    ),
    'text' => array(
        'x' => array(),
        'y' => array(),
        'font-size' => array(),
        'font-family' => array(),
        'font-weight' => array(),
        'fill' => array(),
    ),
    "button" => array(
        "type" => array(),
        "onclick" => array(),
        "class" => array(),
        "id" => array(),
        "disabled" => array(),
        "style" => array(),
    ),
    "span" => array(
        "class" => array(),
        "style" => array(),
        "data-trigger" => array(),
    ),
    "h1" => array(
        "class" => array(),
        "style" => array(),
    ),
    "h2" => array(
        "class" => array(),
        "style" => array(),
    ),
    "p" => array(
        "class" => array(),
        "style" => array(),
    ),
    "b" => array(),
    "u" => array(),
    "a" => array(
        "href" => array(),
        "target" => array(),
        "rel" => array(),
        "title" => array(),
        "class" => array(),
        "onclick" => array(),
    ),
    "i" => array(
        "onclick" => array(),
        "class" => array(),
        "id" => array(),
        "style" => array(),
    ),
    "select" => array(
        "id" => array(),
        "class" => array(),
        "style" => array(),
        "onchange" => array(),
    ),
    "option" => array(
        "value" => array(),
        "selected" => array(),
    ),
    "br" => array(),
    "strong" => array(),
    "input" => array(
        "type" => array(),
        "id" => array(),
        "class" => array(),
        "style" => array(),
        "value" => array(),
        "name" => array(),
        "placeholder" => array(),
        "onchange" => array(),
        "onclick" => array(),
        "disabled" => array(),
        "data-target" => array(),
    ),
    "textarea" => array(
        "id" => array(),
        "class" => array(),
        "style" => array(),
        "onchange" => array(),
        "onclick" => array(),
        "disabled" => array(),
    ),
    "label" => array(
        "for" => array(),
        "class" => array(),
        "style" => array(),
    ),
);

$ai4seo_cached_active_plugins_and_themes = array();
$ai4seo_cached_supported_post_types = array();
$ai4seo_allowed_attachment_mime_types = array("image/jpeg", "image/png", "image/gif", "image/bmp", "image/webp"); # IMPORTANT! Also apply changes to the api-service AND to ai4seo_supported_mime_types-variable in JS-file
$ai4seo_allowed_image_mime_types = array("image/jpeg", "image/png", "image/gif", "image/bmp", "image/webp");

// Define the constants for full and base language code mappings
const AI4SEO_FULL_LANGUAGE_CODE_MAPPING = array(
    'zh_cn' => 'simplified chinese',
    'zh_tw' => 'traditional chinese',
    'pt_br' => 'brazilian portuguese',
    'pt_pt' => 'european portuguese',
    'fr_ca' => 'canadian french',
    'en_us' => 'american english',
    'en_gb' => 'british english',
);

const AI4SEO_BASE_LANGUAGE_CODE_MAPPING = array(
    'sq' => 'albanian',
    'ar' => 'arabic',
    'bg' => 'bulgarian',
    'zh' => 'chinese',  // General Chinese fallback
    'hr' => 'croatian',
    'cs' => 'czech',
    'da' => 'danish',
    'nl' => 'dutch',
    'en' => 'english',  // General English fallback
    'et' => 'estonian',
    'fi' => 'finnish',
    'fr' => 'french',   // General French fallback
    'de' => 'german',
    'el' => 'greek',
    'he' => 'hebrew',
    'hi' => 'hindi',
    'hu' => 'hungarian',
    'is' => 'icelandic',
    'id' => 'indonesian',
    'it' => 'italian',
    'ja' => 'japanese',
    'ko' => 'korean',
    'lv' => 'latvian',
    'lt' => 'lithuanian',
    'mk' => 'macedonian',
    'mt' => 'maltese',
    'no' => 'norwegian',
    'pl' => 'polish',
    'pt' => 'portuguese',  // General Portuguese fallback
    'ro' => 'romanian',
    'ru' => 'russian',
    'sr' => 'serbian',
    'sk' => 'slovak',
    'sl' => 'slovenian',
    'es' => 'spanish',  // General Spanish fallback
    'sv' => 'swedish',
    'th' => 'thai',
    'tr' => 'turkish',
    'uk' => 'ukrainian',
    'vi' => 'vietnamese',
);

// allowed ajax function (also change in javascript file)
const AI4SEO_ALLOWED_AJAX_FUNCTIONS = array(
    "ai4seo_show_metadata_editor",
    "ai4seo_show_attachment_attributes_editor",
    "ai4seo_save_metadata_editor_values",
    "ai4seo_save_attachment_attributes_editor_values",
    "ai4seo_generate_metadata",
    "ai4seo_generate_attachment_attributes",
    "ai4seo_toggle_automated_generation",
    "ai4seo_submit_licence_key",
    "ai4seo_dismiss_performance_notice",
);


// === INITIALIZATION ======================================================================== \\

load_plugin_textdomain("ai-for-seo");

// init robhub-api-communicator
ai4seo_init_robhub_api_communicator();

// init cron jobs
ai4seo_init_cron_jobs();

// add meta tags to header
add_action("get_header", "ai4seo_init_meta_tags_output", 99999);

// init settings
add_action("init", "ai4seo_init_settings");

// init admin essentials after all plugins have been loaded
add_action("init", "ai4seo_init_admin_essentials");

// do some checks after all plugins have been loaded
add_action('plugins_loaded', 'ai4seo_check_version');

// on saving a post, check if the all ceo meta tags are filled
add_action("save_post", "ai4seo_mark_post_to_be_analyzed", 20, 3);

// analyze the post after it has been saved, call ai4seo_handle_posts_to_be_analyzed() at the end of the request
add_action("shutdown", "ai4seo_handle_posts_to_be_analyzed");

// on plugin activation / deactivation
register_activation_hook(__FILE__, "ai4seo_on_activation");
register_deactivation_hook(__FILE__, "ai4seo_on_deactivation");


// ___________________________________________________________________________________________ \\
// === INIT FUNCTIONS ======================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Things to do on plugin activation
 * @return void
 */
function ai4seo_on_activation() {
    // init robhub-api-communicator
    $successfully_initialized_api_communicator = ai4seo_init_robhub_api_communicator();

    if ($successfully_initialized_api_communicator) {
        ai4seo_robhub_api()->init_credentials();
    }

    ai4seo_init_cron_jobs();
}

// =========================================================================================== \\

/**
 * Things to do on plugin deactivation
 * @return void
 */
function ai4seo_on_deactivation() {
    // un schedule all cron jobs
    ai4seo_un_schedule_cron_jobs();
}

// =========================================================================================== \\

/**
 * Function to init plugin essentials for admins
 * @return void
 */
function ai4seo_init_admin_essentials() {
    // Make sure that the user is allowed to use this plugin
    if (!ai4seo_can_manage_this_plugin()) {
        return;
    }

    // Add menu-item to main menu
    add_action("admin_menu", "ai4seo_add_menu_entries");

    // enqueue scripts and styles
    add_action('wp_enqueue_scripts', 'ai4seo_enqueue_admin_scripts');
    add_action('admin_enqueue_scripts', 'ai4seo_enqueue_admin_scripts');

    // plugin action link use filter "plugin_action_links_ + plugin_basename"
    $this_plugin_basename = sanitize_text_field(ai4seo_get_plugin_basename());
    add_filter("plugin_action_links_{$this_plugin_basename}", 'ai4seo_add_links_to_the_plugin_directory');

    // put our code into the page header and footer and admin-bar
    add_action("wp_footer", "ai4seo_init_current_post_id");
    add_action("get_footer", "ai4seo_init_current_post_id");
    add_action("admin_footer", "ai4seo_init_current_post_id");
    
    add_action("wp_footer", "ai4seo_init_notification_modal");
    add_action("get_footer", "ai4seo_init_notification_modal");
    add_action("admin_footer", "ai4seo_init_notification_modal");
    
    add_action("wp_footer", "ai4seo_init_ajax_modal");
    add_action("get_footer", "ai4seo_init_ajax_modal");
    add_action("admin_footer", "ai4seo_init_ajax_modal");

    add_action("wp_footer", "ai4seo_init_plugin_version_number");
    add_action("get_footer", "ai4seo_init_plugin_version_number");
    add_action("admin_footer", "ai4seo_init_plugin_version_number");

    // admin bar menu item
    add_action("admin_bar_menu", "ai4seo_add_admin_menu_item", 999);

    // put our code into the post and page table
    add_filter("manage_post_posts_columns", "ai4seo_add_metadata_editor_column_to_posts_table");
    add_filter("manage_page_posts_columns", "ai4seo_add_metadata_editor_column_to_posts_table");
    #add_filter("manage_edit-product_columns", "ai4seo_add_metadata_editor_column_to_posts_table");
    add_action("manage_post_posts_custom_column", "ai4seo_add_metadata_editor_button_to_posts_table", 10, 2);
    add_action("manage_page_posts_custom_column", "ai4seo_add_metadata_editor_button_to_posts_table", 10, 2);
    #add_action("manage_product_posts_custom_column", "ai4seo_add_metadata_editor_button_to_posts_table", 10, 2);

    // notices
    add_action('admin_notices', 'ai4seo_add_admin_notices');

    // ajax functions
    foreach (AI4SEO_ALLOWED_AJAX_FUNCTIONS AS $this_ajax_function) {
        add_action("wp_ajax_{$this_ajax_function}", $this_ajax_function);
    }
}

// === FUNCTION TO INIT AI4SEO-PLUGIN-SETTINGS =============================================== \\

/**
 * Function to init the plugin-settings
 * @return void
 */
function ai4seo_init_settings() {
    global $ai4seo_settings;

    // Read settings from database
    $from_database_settings = get_option("ai4seo_settings");

    // Make sure that settings could be read from database
    if (!$from_database_settings) {
        return;
    }

    // Convert settings from database into array
    $from_database_settings = json_decode($from_database_settings, true);

    // Make sure that $settings is array
    if (!is_array($from_database_settings)) {
        return;
    }

    // Loop through settings and add the new values to $ai4seo_settings
    foreach ($from_database_settings as $setting_name => $setting_value) {
        // Make sure that this setting is valid
        if (!ai4seo_validate_setting_value($setting_name, $setting_value)) {
            continue;
        }

        // Save the new values to $ai4seo_settings
        $ai4seo_settings[$setting_name] = $setting_value;
    }
}

// =========================================================================================== \\

/**
 * Function to check if we have updated recently and do some actions accordingly
 * @return void
 */
function ai4seo_check_version() {
    $stored_version = get_option('_ai4seo_version');

    // on new version
    if ($stored_version != AI4SEO_PLUGIN_VERSION_NUMBER || isset($_GET["ai4seo-tidyup"])) {
        // update new version
        update_option('_ai4seo_version', AI4SEO_PLUGIN_VERSION_NUMBER);

        // tidy up some old version parameters, tables and options
        ai4seo_tidy_up();

        // do a full analysis of the plugin performance
        ai4seo_analyze_plugin_performance();
    }
}

// =========================================================================================== \\

function ai4seo_tidy_up() {
    // reestablish cron jobs
    ai4seo_un_schedule_cron_jobs();
    ai4seo_init_cron_jobs();

    // start cron jobs in 10 seconds
    ai4seo_inject_additional_cronjob_call("ai4seo_automated_generation_cron_job");
    ai4seo_inject_additional_cronjob_call("ai4seo_analyze_plugin_performance");

    // unset temporary options
    ai4seo_robhub_api()->reset_last_credit_balance_check();
    update_option("_ai4seo_last_cronjob_call", time() - 300);
    delete_option("_ai4seo_performance_notice_dismissed_timestamp");

    // remove old options (from older versions)
    // required after V1.1.1
    delete_option("_ai4seo_current_credits_balance");

    // if old option ai4seo_missing_seo_data_post_ids is set, rename it to ai4seo_processing_metadata_post_ids
    // required after V1.1.2
    if (get_option("ai4seo_missing_seo_data_post_ids")) {
        $missing_seo_data_post_ids = get_option("ai4seo_missing_seo_data_post_ids");
        update_option("ai4seo_processing_metadata_post_ids", $missing_seo_data_post_ids);
        delete_option("ai4seo_missing_seo_data_post_ids");
    }

    // if old option _ai4seo_num_existing_going_to_fill_this_post_ids_by_post_type is set, rename it to _ai4seo_num_processing_metadata_post_ids_by_post_type
    // required after V1.1.2
    if (get_option("_ai4seo_num_existing_going_to_fill_this_post_ids_by_post_type")) {
        $num_existing_going_to_fill_this_post_ids_by_post_type = get_option("_ai4seo_num_existing_going_to_fill_this_post_ids_by_post_type");
        update_option("_ai4seo_num_processing_metadata_post_ids_by_post_type", $num_existing_going_to_fill_this_post_ids_by_post_type);
        delete_option("_ai4seo_num_existing_going_to_fill_this_post_ids_by_post_type");
    }

    // clear schedule of old cronjobs, as of V1.1.2 we use new cronjobs
    wp_clear_scheduled_hook("ai4seo_search_missing_seo_data_posts");
    wp_clear_scheduled_hook("ai4seo_search_missing_metadata_posts");
    wp_clear_scheduled_hook("ai4seo_automated_seo_data_generation");

    // V1.1.8: clear schedule of old cronjob "ai4seo_automated_metadata_generation", it's now called "ai4seo_automated_generation_cron_job"
    wp_clear_scheduled_hook("ai4seo_automated_metadata_generation");

    // V1.1.8: check for option "ai4seo_is_automation_activated_for_posts", if set, delete it and call ai4seo_enable_automated_generation("post")
    if (get_option("ai4seo_is_automation_activated_for_posts")) {
        ai4seo_enable_automated_generation("post");
    }

    delete_option("ai4seo_is_automation_activated_for_posts");

    // V1.1.8: check for option "ai4seo_is_automation_activated_for_pages", if set, delete it and call ai4seo_enable_automated_generation("page")
    if (get_option("ai4seo_is_automation_activated_for_pages")) {
        ai4seo_enable_automated_generation("page");
    }

    delete_option("ai4seo_is_automation_activated_for_pages");

    // V1.1.8: check for option "ai4seo_is_automation_activated_for_products", if set, delete it and call ai4seo_enable_automated_generation("product")
    if (get_option("ai4seo_is_automation_activated_for_products")) {
        ai4seo_enable_automated_generation("product");
    }

    delete_option("ai4seo_is_automation_activated_for_products");

    // if old option ai4seo_already_filled_post_ids is set, rename it to ai4seo_already_filled_metadata_post_ids
    // required after V1.2
    if (get_option("ai4seo_already_filled_post_ids")) {
        $already_filled_metadata_post_ids = get_option("ai4seo_already_filled_post_ids");
        update_option("ai4seo_already_filled_metadata_post_ids", $already_filled_metadata_post_ids);
        delete_option("ai4seo_already_filled_post_ids");
    }

    // if old option ai4seo_failed_to_fill_post_ids is set, rename it to ai4seo_failed_to_fill_metadata_post_ids
    // required after V1.2
    if (get_option("ai4seo_failed_to_fill_post_ids")) {
        $failed_to_fill_metadata_post_ids = get_option("ai4seo_failed_to_fill_post_ids");
        update_option("ai4seo_failed_to_fill_metadata_post_ids", $failed_to_fill_metadata_post_ids);
        delete_option("ai4seo_failed_to_fill_post_ids");
    }

    // V1.2: check for table "wp_ai4seo_cache" (id, post_id, data), if available, save all it's "data" to the post_meta of the corresponding post_id, using ai4seo_save_generated_data()
    ai4seo_tidy_up_old_ai4seo_cache_table();

    // V1.2.1: Delete old summary options
    if (get_option("_ai4seo_num_processing_metadata_post_ids_by_post_type")) {
        delete_option("_ai4seo_num_processing_metadata_post_ids_by_post_type");
    }

    if (get_option("_ai4seo_num_failed_to_fill_post_ids_by_post_type")) {
        delete_option("_ai4seo_num_failed_to_fill_post_ids_by_post_type");
    }

    if (get_option("_ai4seo_num_already_filled_post_ids_by_post_type")) {
        delete_option("_ai4seo_num_already_filled_post_ids_by_post_type");
    }

    if (get_option("_ai4seo_num_posts_not_filled_by_post_type")) {
        delete_option("_ai4seo_num_posts_not_filled_by_post_type");
    }

    if (get_option("ai4seo_already_filled_metadata_post_ids")) {
        delete_option("ai4seo_already_filled_metadata_post_ids");
    }

    if (get_option("ai4seo_already_filled_attributes_attachment_post_ids")) {
        delete_option("ai4seo_already_filled_attributes_attachment_post_ids");
    }

    // V1.2.1: Rename some post ids options
    // (ai4seo_failed_to_fill_metadata_post_ids -> ai4seo_failed_metadata_post_ids)
    // (ai4seo_failed_to_fill_attributes_attachment_post_ids -> ai4seo_failed_attributes_attachment_post_ids)
    if (get_option("ai4seo_failed_to_fill_metadata_post_ids")) {
        $failed_metadata_post_ids = get_option("ai4seo_failed_to_fill_metadata_post_ids");
        update_option("ai4seo_failed_metadata_post_ids", $failed_metadata_post_ids);
        delete_option("ai4seo_failed_to_fill_metadata_post_ids");
    }

    if (get_option("ai4seo_failed_to_fill_attributes_attachment_post_ids")) {
        $failed_attributes_attachment_post_ids = get_option("ai4seo_failed_to_fill_attributes_attachment_post_ids");
        update_option("ai4seo_failed_attributes_attachment_post_ids", $failed_attributes_attachment_post_ids);
        delete_option("ai4seo_failed_to_fill_attributes_attachment_post_ids");
    }
}

// =========================================================================================== \\

/**
 * Function to tidy up old ai4seo_cache table
 * @return void
 */
function ai4seo_tidy_up_old_ai4seo_cache_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . "ai4seo_cache";

    // Check if the table exists
    $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name));

    if ($table_exists) {
        $sql = "SELECT * FROM $table_name";
        $results = $wpdb->get_results($sql, ARRAY_A);

        foreach ($results as $result) {
            $post_id = sanitize_key($result["post_id"]);
            $data = ai4seo_deep_sanitize(json_decode($result["data"], true));

            ai4seo_save_generated_data_to_postmeta($post_id, $data);
        }

        // Drop the table
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}

// =========================================================================================== \\

/**
 * Function to init cron jobs
 * @return void
 */
function ai4seo_init_cron_jobs() {
    // Add custom cron schedule
    add_filter("cron_schedules", "ai4seo_add_cron_job_intervals");

    // add cron jobs to automate content generation
    add_action("ai4seo_automated_generation_cron_job", "ai4seo_automated_generation_cron_job");

    // add cron jobs to analyze current state of the plugins performance
    add_action("ai4seo_analyze_plugin_performance", "ai4seo_analyze_plugin_performance");

    // schedule cron jobs if not already scheduled
    ai4seo_schedule_cron_jobs();
}

// =========================================================================================== \\

/**
 * Function to init robhub-api-communicator
 * @return bool true if the class is found, false if not
*/
function ai4seo_init_robhub_api_communicator(): bool {
    global $ai4seo_robhub_api_communicator;
    
    // Include RobHubApiCommunicator class
    include_once(ai4seo_get_includes_api_path("class-robhub-api-communicator.php"));

    // return false if the class is not found
    if (!class_exists("Ai4Seo_RobHubApiCommunicator")) {
        return false;
    }

    $ai4seo_robhub_api_communicator = new Ai4Seo_RobHubApiCommunicator();
    $ai4seo_robhub_api_communicator->set_option_names(
        AI4SEO_ROBHUB_AUTH_DATA_OPTION_NAME,
        AI4SEO_ROBHUB_CREDITS_BALANCE_OPTION_NAME,
        AI4SEO_ROBHUB_LAST_CREDITS_BALANCE_CHECK_OPTION_NAME
    );

    return true;
}

// === FUNCTION TO ADD MENU-ITEM TO MAIN MENU ================================================ \\

/**
 * Function to add menu-item to main menu
 * @return void
*/
function ai4seo_add_menu_entries(){
    $plugins_wordpress_identifier = ai4seo_get_plugins_wordpress_identifier();
    $plugins_official_name = ai4seo_get_plugins_official_name();
    $encoded_svg = 'data:image/svg+xml;base64,' . base64_encode(AI4SEO_SVG_ICONS["ai-for-seo-main-menu-icon"]);

    add_menu_page(
        $plugins_official_name,
        $plugins_official_name,
        "edit_posts",
        $plugins_wordpress_identifier,
        "ai4seo_require_menu_frame_file",
        $encoded_svg,
        99
    );
}

// === FUNCTION TO DISPLAY MENU FRAME ===================================================== \\

/**
 * Function to display the menu frame
 * @return void
*/
function ai4seo_require_menu_frame_file() {
    require_once(ai4seo_get_plugin_dir_path("includes/menu-frame.php"));
}

// === FUNCTION TO ENQUEUE JAVASCRIPT- AND CSS-FILES ========================================= \\

/**
 * Function to enqueue javascript- and css-files
 * @return void
*/
function ai4seo_enqueue_admin_scripts() {
    wp_enqueue_script("wp-i18n");

    // Register and enqueue stylesheet
    wp_register_style("ai-for-seo-styles", ai4seo_get_assets_css_path("ai-for-seo-styles.css"), "", AI4SEO_PLUGIN_VERSION_NUMBER);
    wp_enqueue_style("ai-for-seo-styles");

    // Enqueue javascript-file
    wp_enqueue_script("ai-for-seo-scripts", ai4seo_get_assets_js_path("ai-for-seo-scripts.js"), array("jquery", "wp-i18n"), AI4SEO_PLUGIN_VERSION_NUMBER, true);

    // Add urls for JavaScript use in ai-for-seo-scripts.js-file
    $urls = array(
        "ai4seo_site_url" => site_url(),
        "ai4seo_admin_url" => admin_url(),
        "ai4seo_includes_url" => includes_url(),
        "ai4seo_content_url" => content_url(),
        "ai4seo_plugin_url" => plugins_url(),
        "ai4seo_plugin_directory_url" => plugins_url("", __FILE__),
        "ai4seo_uploads_directory_url" => wp_upload_dir(),
    );

    wp_localize_script("ai-for-seo-scripts", "ai4seo_localization", $urls);

    wp_set_script_translations("ai-for-seo-scripts", "ai-for-seo");
}

// === CODE TO ADD ADMIN NOTICES ============================================================= \\

/**
 * Function to add admin notices
 * @return void
 */
function ai4seo_add_admin_notices() {
    // Make sure that the user is allowed to use this plugin
    if (!ai4seo_can_manage_this_plugin()) {
        return;
    }

    // PERFORMANCE NOTICE
    ai4seo_add_performance_notice();
}

/**
 * Function to add the performance notice
 * @return void
 */
function ai4seo_add_performance_notice() {
    // Make sure that the user is allowed to use this plugin
    if (!ai4seo_can_manage_this_plugin()) {
        return;
    }

    // NOTICE IS DISMISSED?
    // check for option "_ai4seo_performance_notice_dismissed_timestamp", if it's younger than one day, don't show the notice
    $dismissed_timestamp = get_option("_ai4seo_performance_notice_dismissed_timestamp", 0);

    if ($dismissed_timestamp > time() - AI4SEO_NOTICE_DISMISSAL_TIME) {
        return;
    }

    // GATHER PARAMETERS
    $is_dashboard_page_visible = ai4seo_is_tab_open("dashboard");
    $is_any_plugin_page_visible = ai4seo_is_user_inside_plugin_admin_pages();
    $dashboard_url = ai4seo_get_admin_url("dashboard");
    $plugin_official_name = ai4seo_get_plugins_official_name();

    $notice_messages = array();
    $notice_buttons = array();

    // ROBHUB API
    if (!ai4seo_robhub_api() instanceof Ai4Seo_RobHubApiCommunicator) {
        return;
    }

    // REFRESH GENERATION STATUS AND SEO COVERAGE
    // analyse the current state of the plugin's performance, but skip the refresh of the seo coverage, for performance reasons
    if (ai4seo_is_user_inside_plugin_admin_pages()) {
        ai4seo_analyze_plugin_performance();
    }

    // CREDITS BALANCE NOTICE
    // if we are on the dashboard -> reset credits balance check cache
    if ($is_dashboard_page_visible) {
        ai4seo_robhub_api()->reset_last_credit_balance_check();
    }

    // check current credits balance, if it's lower than AI4SEO_LOW_CREDITS_THRESHOLD, show notice-warning,
    // if it's lower than AI4SEO_VERY_LOW_CREDITS_THRESHOLD, show notice-error, otherwise show notice-info
    $current_credits_balance = ai4seo_robhub_api()->get_credits_balance();

    if ($current_credits_balance < AI4SEO_VERY_LOW_CREDITS_THRESHOLD) {
        $notice_class = "notice-error";
        $notice_messages[] = sprintf(
            "<span style='font-weight: bold; color: red;'>%s</span>",
            sprintf(
                esc_html__("Remaining credits: %u. Your credits for the \"AI for SEO\" plugin are running very low. Please purchase more credits to continue improving your remaining content.", "ai-for-seo"),
                $current_credits_balance
            )
        );
    } elseif ($current_credits_balance < AI4SEO_LOW_CREDITS_THRESHOLD) {
        $notice_class = "notice-warning";
        $notice_messages[] = sprintf(
            "<span style='font-weight: bold;'>%s</span>",
            sprintf(
                esc_html__("Remaining credits: %u. Your credits for the \"AI for SEO\" plugin are running low. Please consider purchasing more credits to continue improving your remaining content.", "ai-for-seo"),
                $current_credits_balance
            )
        );
    } else {
        $notice_class = "notice-info";
    }

    if ($current_credits_balance < AI4SEO_LOW_CREDITS_THRESHOLD && !$is_dashboard_page_visible) {
        $notice_buttons[] = ai4seo_get_button_text_link_tag($dashboard_url, "circle-plus", __("Purchase more credits", "ai-for-seo"));
    }

    // MISSING POSTS
    // do we even have missing posts? If not, we can skip the notice
    $num_missing_by_post_type = ai4seo_get_all_missing_posts_by_post_type();
    $num_failed_by_post_type = ai4seo_get_all_failed_posts_by_post_type();

    // remove all empty post types
    foreach ($num_missing_by_post_type as $post_type => $num_posts) {
        // check for failed entries (to substract them)
        if (isset($num_failed_by_post_type[$post_type]) && $num_failed_by_post_type[$post_type]) {
            $num_posts -= $num_failed_by_post_type[$post_type];
        }

        if ($num_posts <= 0) {
            unset($num_missing_by_post_type[$post_type]);
        }

        // also remove if this post type is auto generated, and we have enough credits left, as it will be fully generated soon
        if (ai4seo_is_automated_generation_enabled($post_type) && $current_credits_balance >= AI4SEO_VERY_LOW_CREDITS_THRESHOLD) {
            unset($num_missing_by_post_type[$post_type]);
        }
    }

    // if there are no missing posts, return
    if (empty($num_missing_by_post_type)) {
        return;
    }

    // GENERATED POSTS
    // check ai4seo_get_generation_status_summary_entry for generated posts
    $num_generated_by_post_type = ai4seo_get_all_generated_posts_by_post_type();

    // remove empty post types
    foreach ($num_generated_by_post_type as $post_type => $num_posts) {
        if ($num_posts == 0) {
            unset($num_generated_by_post_type[$post_type]);
        }
    }

    // YOU'RE DOING GREAT SO FAR! NOTICE
    if ($num_generated_by_post_type) {
        $generated_post_types_strings_parts = array();

        foreach ($num_generated_by_post_type AS $post_type => $num_posts) {
            # attachment -> media workaround
            if ($post_type == "attachment") {
                $post_type = "media file";
            }

            $generated_post_types_strings_parts[] = ai4seo_get_post_type_translation($post_type, $num_posts);
        }

        // build $post_types_to_mention_string by separating with commas and the last one with "and"
        if (count($generated_post_types_strings_parts) > 1) {
            $generated_post_types_complete_string = implode(", ", array_slice($generated_post_types_strings_parts, 0, -1)) . " " . __("and", "ai-for-seo") . " " . end($generated_post_types_strings_parts);
        } else {
            $generated_post_types_complete_string = $generated_post_types_strings_parts[0];
        }

        /* Translators: %1$s is replaced with bold text. */
        $notice_messages[] = sprintf(
            esc_html__('You\'re doing great so far! The "AI for SEO" plugin has already generated SEO-relevant data for %1$s.', 'ai-for-seo'),
            '<strong>' . esc_html($generated_post_types_complete_string) . '</strong>'
        );
    }

    // ROOM FOR IMPROVEMENT! NOTICE
    $missing_post_types_strings_parts = array();

    foreach ($num_missing_by_post_type AS $post_type => $num_posts) {
        # attachment -> media workaround
        if ($post_type == "attachment") {
            $post_type = "media file";
        }

        $missing_post_types_strings_parts[] = ai4seo_get_post_type_translation($post_type, $num_posts);
    }

    // build $post_types_to_mention_string by separating with commas and the last one with "and"
    // only, when we already have generated posts
    if ($missing_post_types_strings_parts && $num_generated_by_post_type) {
        if (count($missing_post_types_strings_parts) > 1) {
            $missing_post_types_complete_string = implode(", ", array_slice($missing_post_types_strings_parts, 0, -1)) . " " . __("and", "ai-for-seo") . " " . end($missing_post_types_strings_parts);
        } else {
            $missing_post_types_complete_string = $missing_post_types_strings_parts[0];
        }

        /* Translators: %1$s is replaced with bold text. */
        $notice_messages[] = sprintf(
            esc_html__("However, there is still room for improvement. \"AI for SEO\" can further assist by generating data for %s.", "ai-for-seo"),
            '<strong>' . esc_html($missing_post_types_complete_string) . '</strong>'
        );

        if ($current_credits_balance >= AI4SEO_LOW_CREDITS_THRESHOLD && !$is_any_plugin_page_visible) {
            $notice_buttons[] = ai4seo_get_button_text_link_tag($dashboard_url, "bolt", __("Let's optimize!", "ai-for-seo"), "ai4seo-success-button");
        }
    }

    // NO NOTICES COLLECTED SO FAR? RETURN
    if (!$notice_messages) {
        return;
    }

    // OUTPUT NOTICE
    echo '<div class="notice ' . esc_html($notice_class) . ' is-dismissible ai4seo-notice ai4seo-performance-notice">';

        // add "AI for SEO" logo
        echo '<img class="ai4seo-notice-icon" src="' . esc_url(ai4seo_get_ai_for_seo_logo_url("32x32")) . '" alt="' . esc_attr($plugin_official_name) . '" />';

        foreach ($notice_messages as $notice_message) {
            echo '<p>' . ai4seo_wp_kses($notice_message) . '</p>';
        }

        if ($notice_buttons) {
            echo '<p>' . ai4seo_wp_kses(implode(" ", $notice_buttons)) . '</p>';
        }

    echo '</div>';
}

// =========================================================================================== \\

/**
 * Function to output the ajax modal html code. Used to be implemented at the footer of the page.
 * @return void
*/
function ai4seo_init_ajax_modal() {
    // Make sure that this function is only called once
    if (!ai4seo_singleton(__FUNCTION__)) {
        return;
    }

    // Add layer-code to output-string
    echo "<div id='ai4seo-ajax-modal-wrapper' class='ai4seo-ajax-modal-wrapper ai4seo-modal-wrapper' style='display: none;'>";
        echo "<div id='ai4seo-ajax-modal' class='ai4seo-ajax-modal ai4seo-modal'>";
            // Code for close icon
            echo "<div class='ai4seo-ajax-modal-close-icon' onclick='ai4seo_hide_ajax_modal();' />";
            echo ai4seo_wp_kses(ai4seo_get_svg_tag("square-xmark", __("Close", "ai-for-seo")));
            echo "</div>";

            // Loading icon
            echo "<div id='ai4seo-ajax-modal-loading-icon' class='ai4seo-ajax-modal-loading-icon'>";
                echo ai4seo_wp_kses(ai4seo_get_svg_tag("rotate", __("Loading", "ai-for-seo"), "ai4seo-spinning-icon"));
            echo "</div>";

            // Content container
            echo "<div id='ai4seo-ajax-modal-content' class='ai4seo-ajax-modal-content'></div>";
        echo "</div>";
    echo "</div>";
}

// =========================================================================================== \\

/**
 * Function to add post-id to admin-footer
 * @return void
 */
function ai4seo_init_current_post_id() {
    // Make sure that this function is only called once
    if (!ai4seo_singleton(__FUNCTION__)) {
        return;
    }

    // Get post- or page-id
    $post_id = get_the_ID();

    // Stop script if post- or page-id could not be found
    if (!$post_id) {
        return;
    }

    echo "<div id='ai4seo-footer-post-id' style='display:none!important;'>" . esc_html($post_id) . "</div>";
}

// =========================================================================================== \\

/**
 * Function to add plugin version number to admin-footer
 * @return void
 */
function ai4seo_init_plugin_version_number() {
    // Make sure that this function is only called once
    if (!ai4seo_singleton(__FUNCTION__)) {
        return;
    }
    
    echo "<div id='ai4seo-plugin-version-number' class='ai4seo-plugin-version-number'>" . esc_html(AI4SEO_PLUGIN_VERSION_NUMBER) . "</div>";
}

// =========================================================================================== \\

/**
 * Function to add notification modal to footer
 * @return void
 */
function ai4seo_init_notification_modal() {
    // Make sure that this function is only called once
    if (!ai4seo_singleton(__FUNCTION__)) {
        return;
    }

    echo "<div class='ai4seo-notification-modal-wrapper ai4seo-modal-wrapper' style='display: none'>";
        echo "<div class='ai4seo-notification-modal ai4seo-modal' onclick='event.stopPropagation();'>";
            // headline
            echo "<h2 class='ai4seo-notification-modal-headline'></h2>";
            echo "<h2 class='ai4seo-notification-modal-default-headline'><img src='" . esc_url(ai4seo_get_ai_for_seo_logo_url("32x32")) . "' class='ai4seo-logo ai4seo-inline-logo' /> " . esc_html__("An error occurred", "ai-for-seo") . "</h2>";

            // content
            echo "<div class='ai4seo-notification-modal-content'></div>";

            // buttons
            echo "<div class='ai4seo-notification-modal-default-buttons-row'>";
                echo "<a onclick='ai4seo_hide_notification_modal();' class='button ai4seo-button ai4seo-success-button'>" . esc_html__("Close", "ai-for-seo") . "</a>";
            echo "</div>";

            echo "<div class='ai4seo-notification-modal-buttons-row'></div>";
        echo "</div>";
    echo "</div>";
}

// =========================================================================================== \\

/**
 * Function to add new column to page- and post-table
 * @param array $columns
 * @return array
*/
function ai4seo_add_metadata_editor_column_to_posts_table(array $columns): array {
    // Make sure that this function is only called once
    if (!ai4seo_singleton(__FUNCTION__)) {
        return $columns;
    }
    
    $ai4seo_plugin_name = ai4seo_get_plugins_official_name();
    $ai4seo_plugin_identifier = ai4seo_get_plugins_wordpress_identifier();
    $ai4seo_icon = "<img class='ai4seo-icon ai4seo-24x24-icon' src='" . esc_url(ai4seo_get_ai_for_seo_logo_url("32x32")) . "' alt='" . esc_attr($ai4seo_plugin_name) . "' /><span style='display: none'>" . esc_html($ai4seo_plugin_name) . "</span>";
    return array_merge($columns, [$ai4seo_plugin_identifier => $ai4seo_icon]);
}

// =========================================================================================== \\

/**
 * Function to add content to new page- and post-table column
 * @param string $column_name
 * @param int $post_id
 * @return void
*/
function ai4seo_add_metadata_editor_button_to_posts_table(string $column_name, int $post_id) {
    $ai4seo_plugin_identifier = ai4seo_get_plugins_wordpress_identifier();

    if ($column_name == $ai4seo_plugin_identifier) {
        echo ai4seo_wp_kses(ai4seo_get_edit_metadata_button($post_id));
    }
}

// =========================================================================================== \\

/**
 * Function to add plugin links (in the plugin directory)
 * @return array $links - array with links that will be displayed in the plugin directory near the plugin name
 */
function ai4seo_add_links_to_the_plugin_directory($links): array {
    // Make sure that this function is only called once
    if (!ai4seo_singleton(__FUNCTION__)) {
        return $links;
    }

    $successfully_initiated_api_communicator = ai4seo_init_robhub_api_communicator();

    if ($successfully_initiated_api_communicator) {
        $client_user_id = ai4seo_robhub_api()->get_client_id();
    } else {
        $client_user_id = false;
    }

    // add Help link
    $help_link_url = ai4seo_get_admin_url("help");

    if ($help_link_url) {
        $help_link_tag = "<a href='" . esc_url($help_link_url) . "'>" . esc_html__("Help", "ai-for-seo") . "</a>";
        array_unshift($links, $help_link_tag);
    }

    // add Upgrade link
    # todo: don't show if user is already on the highest plan (we wait for the subscription cache)
    if ($client_user_id) {
        $purchase_plan_url = ai4seo_get_purchase_plan_url($client_user_id);
        $purchase_plan_link = "<a href='" . esc_url($purchase_plan_url) . "' target='_blank' style='font-weight: bold; color: green;'>" . esc_html__("Upgrade", "ai-for-seo") . "</a>";
        array_unshift($links, $purchase_plan_link);
    }

    // add dashboard link at the front of the links
    $dashboard_link_url = ai4seo_get_admin_url("dashboard");
    $dashboard_link_tag = "<a href='" . esc_url($dashboard_link_url) . "'>" . esc_html__("Dashboard", "ai-for-seo") . "</a>";
    array_unshift($links, $dashboard_link_tag);

    return $links;
}

// =========================================================================================== \\

/**
 * Function to add menu-item to admin-bar
 * @return void
 */
function ai4seo_add_admin_menu_item($wp_admin_bar) {
    if (!ai4seo_singleton(__FUNCTION__)) {
        return;
    }

    // Stop function if called outside of page or post etc.
    if (!is_singular()) {
        return;
    }

    // Prepare arguments for admin-bar menu-item
    $args = array(
        "id" => "ai4seo-edit",
        "title" => "<div class='ai4seo-main-menu-icon'></div> " . esc_html__("Metadata Editor", "ai-for-seo"),
        "meta" => array(
            "onclick" => "ai4seo_open_metadata_editor_modal();return false;",
        ),
    );

    // Add node
    $wp_admin_bar->add_node($args);

    // Add node for mobile version
    $wp_admin_bar->add_menu( array(
        "parent" => "appearance",
        "id" => "ai4seo-edit-mobile",
        "title" => esc_html__("AI for SEO - Metadata Editor", "ai-for-seo"),
        "meta" => array(
            "onclick" => "ai4seo_open_metadata_editor_modal();return false;",
        ),
    ));
}

// =========================================================================================== \\

function ai4seo_init_meta_tags_output() {
    if (!ai4seo_singleton(__FUNCTION__)) {
        return;
    }

    // Stop function if called outside of page, post or product
    if (!is_singular()) {
        return;
    }

    // read setting AI4SEO_SETTING_META_TAG_OUTPUT_MODE
    $meta_tag_output_mode = ai4seo_get_setting(AI4SEO_SETTING_META_TAG_OUTPUT_MODE);

    // Stop function if meta tag output is disabled
    if ($meta_tag_output_mode == "disable") {
        return;
    }

    if (ai4seo_is_plugin_or_theme_active(AI4SEO_THIRD_PARTY_PLUGIN_SQUIRRLY_SEO)) {
        // Squirrly SEO workaround, as they use their own buffer
        add_action("sq_buffer", "ai4seo_modify_html_header_adding_our_meta_tags", 20);
    } else {
        ob_start('ai4seo_modify_html_header_adding_our_meta_tags');
    }
}

// =========================================================================================== \\

/**
 * Function modify and add meta tags to the html header
 * @param string $full_html_buffer - the full html buffer
 * @return string $full_html_buffer - the modified html buffer
 */
function ai4seo_modify_html_header_adding_our_meta_tags(string $full_html_buffer): string {
    global $ai4seo_metadata_details;

    // Define variable for the page- or post-id
    $post_id = get_the_ID();

    // Stop function if no page- or post-id is defined
    if (!$post_id) {
        return $full_html_buffer;
    }

    // read setting AI4SEO_SETTING_META_TAG_OUTPUT_MODE
    $meta_tag_output_mode = ai4seo_get_setting(AI4SEO_SETTING_META_TAG_OUTPUT_MODE);

    // read settings AI4SEO_SETTING_VISIBLE_META_TAGS
    $included_meta_tags = ai4seo_get_setting(AI4SEO_SETTING_VISIBLE_META_TAGS);

    if (!$included_meta_tags) {
        return $full_html_buffer;
    }

    // Extract the content between <head> and </head>
    $head_start_position = strpos($full_html_buffer, '<head>');

    if ($head_start_position === false) {
        return $full_html_buffer;
    }

    // start position right after <head>
    $head_start_position += 6;

    $head_end_position = strpos($full_html_buffer, '</head>');

    if ($head_end_position === false) {
        return $full_html_buffer;
    }

    $head_html = substr($full_html_buffer, $head_start_position, $head_end_position - $head_start_position);

    // analyse head html
    $found_third_party_meta_tags = ai4seo_get_meta_tags_from_html($head_html);

    // read OUR metadata values for this post
    $our_metadata = ai4seo_read_active_metadata_values_by_post_ids(array($post_id), false);

    if ($our_metadata) {
        $our_metadata = $our_metadata[$post_id] ?? array();
    }

    // go through each meta tag and decide what to do with it
    $add_this_metadata = array();
    $remove_this_third_party_meta_tags = array();

    foreach ($ai4seo_metadata_details as $this_metadata_identifier => $this_metadata_field_details) {
        $this_found_third_party_meta_tags = $found_third_party_meta_tags[$this_metadata_identifier] ?? array();
        $this_our_metadata = $our_metadata[$this_metadata_identifier] ?? "";

        // leave this meta tag alone if we do not have a value for it or we exclude this meta tag
        if (!$this_our_metadata) {
            continue;
        }

        if (!in_array($this_metadata_identifier, $included_meta_tags)) {
            continue;
        }

        switch ($meta_tag_output_mode) {
            case "force":
                $add_this_metadata[$this_metadata_identifier] = $this_our_metadata;
                break;
            case "replace":
                $add_this_metadata[$this_metadata_identifier] = $this_our_metadata;

                // remove found third party meta tags
                if ($this_found_third_party_meta_tags) {
                    foreach ($this_found_third_party_meta_tags AS $this_found_third_party_meta_tag) {
                        if ($this_found_third_party_meta_tag) {
                            $remove_this_third_party_meta_tags[] = $this_found_third_party_meta_tag["raw-html"];
                        }
                    }
                }
                break;
            case "complement":
                if (!$this_found_third_party_meta_tags) {
                    $add_this_metadata[$this_metadata_identifier] = $this_our_metadata;
                } else {
                    // workaround: if all the found meta tags are empty -> add ours anyway and remove their empty ones
                    $this_found_third_party_meta_tag_got_content = false;
                    $this_found_third_party_meta_tag_no_content_raw_html = array();
                    foreach ($this_found_third_party_meta_tags AS $this_found_third_party_meta_tag) {
                        if ($this_found_third_party_meta_tag["content"]) {
                            $this_found_third_party_meta_tag_got_content = true;
                            break;
                        } else {
                            $this_found_third_party_meta_tag_no_content_raw_html[] = $this_found_third_party_meta_tag["raw-html"];
                        }
                    }

                    if (!$this_found_third_party_meta_tag_got_content) {
                        $add_this_metadata[$this_metadata_identifier] = $this_our_metadata;
                        $remove_this_third_party_meta_tags = array_merge($remove_this_third_party_meta_tags, $this_found_third_party_meta_tag_no_content_raw_html);
                    }
                }
                break;
        }
    }

    // Remove any third-party meta tags and surrounding non-visible characters
    if ($remove_this_third_party_meta_tags) {
        foreach ($remove_this_third_party_meta_tags AS $this_remove_this_meta_tag) {
            // Use preg_replace to match the tag and any surrounding whitespace or line breaks
            $full_html_buffer = preg_replace(
                '/' . preg_quote($this_remove_this_meta_tag, '/') . '\s*/s',
                '',
                $full_html_buffer
            );
        }
    }

    // add our tags to the head, finding position first
    if ($add_this_metadata) {
        $add_this_meta_tags = array();

        // prepare our meta tags
        foreach ($add_this_metadata as $this_metadata_identifier => $this_metadata_content) {
            $this_metadata_field_details = $ai4seo_metadata_details[$this_metadata_identifier] ?? array();

            if (!$this_metadata_field_details) {
                continue;
            }

            // Prepare variables
            $this_output_tag_type = $this_metadata_field_details["output-tag-type"];
            $this_output_tag_identifier = $this_metadata_field_details["output-tag-identifier"];

            // Handle output for output-tag-type "title"
            if ($this_output_tag_type == "title") {
                $add_this_meta_tags[] = "<title>" . esc_attr($this_metadata_content) . "</title>";
            }

            // Handle output for output-tag-type "meta name"
            elseif ($this_output_tag_type == "meta name") {
                $add_this_meta_tags[] = "<meta name=\"" . esc_attr($this_output_tag_identifier) . "\" content=\"" . esc_attr($this_metadata_content) . "\" />";
            }

            // Handle output for output-tag-type "meta property"
            elseif ($this_output_tag_type == "meta property") {
                $add_this_meta_tags[] = "<meta property=\"" . esc_attr($this_output_tag_identifier) . "\" content=\"" . esc_attr($this_metadata_content) . "\" />";
            }
        }

        // output our meta tags
        if ($add_this_meta_tags) {
            // find a suitable position for our meta tags
            $our_meta_tags_position = $head_start_position;

            // consider the charset meta tag position, if it's near the head start
            if (isset($found_third_party_meta_tags["charset"])) {
                $charset_meta_tags_position = strpos($full_html_buffer, $found_third_party_meta_tags["charset"]["raw-html"]) + strlen($found_third_party_meta_tags["charset"]["raw-html"]);

                // set $charset_meta_tags_position as our meta tags position if it's not further away than 100 characters
                if ($charset_meta_tags_position - $head_start_position < 100) {
                    $our_meta_tags_position = $charset_meta_tags_position;
                }
            }

            // consider the viewport meta tag position, if it's near the head start
            if (isset($found_third_party_meta_tags["viewport"])) {
                $viewport_meta_tags_position = strpos($full_html_buffer, $found_third_party_meta_tags["viewport"]["raw-html"]) + strlen($found_third_party_meta_tags["viewport"]["raw-html"]);

                // set $viewport_meta_tags_position as our meta tags position if it's not further away than 200 characters
                if ($viewport_meta_tags_position - $head_start_position < 200) {
                    $our_meta_tags_position = $viewport_meta_tags_position;
                }
            }

            $plugins_official_name = ai4seo_get_plugins_official_name();

            // add plugin information to the meta tags block
            array_unshift($add_this_meta_tags, "\n\n\t<!-- [" . esc_html($plugins_official_name) . "] This site is optimized with the \"AI for SEO\" plugin v" . esc_html(AI4SEO_PLUGIN_VERSION_NUMBER) . " - " . esc_html(AI4SEO_OFFICIAL_WEBPAGE) . " -->");
            $add_this_meta_tags[] = "<!-- [" . esc_html($plugins_official_name) . "] End -->";

            $add_this_meta_tags = ai4seo_deep_sanitize($add_this_meta_tags, 'ai4seo_wp_kses');

            // add our meta tags to the head
            $full_html_buffer = substr_replace($full_html_buffer, implode("\n\t", $add_this_meta_tags) . "\n", $our_meta_tags_position, 0);
        }
    }

    return $full_html_buffer;
}

// =========================================================================================== \\

/**
 * Function to retrieve specific meta tags from html
 * @param string $head_html the html content of the head
 * @return array $found_meta_tags - an array with the found meta tags
 */
function ai4seo_get_meta_tags_from_html(string $head_html): array {
    global $ai4seo_metadata_details;

    // Remove <script>, <style>, and <link> tags and their content
    $head_html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $head_html);
    $head_html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $head_html);
    $head_html = preg_replace('/<link\b[^>]*>/i', '', $head_html);

    // Remove <![CDATA[ sections
    $head_html = preg_replace('/<!\[CDATA\[.*?\]\]>/s', '', $head_html);

    // Remove HTML comments
    $head_html = preg_replace('/<!--.*?-->/s', '', $head_html);

    // Trim
    $head_html = trim($head_html);

    // Workaround: Replace line breaks with placeholders
    $head_html = preg_replace('/\r\n/', '#AI4SEO#LBRN#', $head_html);
    $head_html = preg_replace('/\n/', '#AI4SEO#LBN#', $head_html);

    // add line breaks after each closing tag like </title>
    $head_html = preg_replace('/<\/[^>]+>/', "$0\n", $head_html);

    // add line breaks after each cosing single tag like <meta ... />
    $head_html = preg_replace('/<[^>]+\/>/', "$0\n", $head_html);

    // add line breaks between two tags
    $head_html = preg_replace('/>\s*</', ">\n<", $head_html);
    $head_html = preg_replace('/>(#AI4SEO#LBRN#|#AI4SEO#LBN#|\s)+</', ">\n<", $head_html);

    // generate array splitting by line breaks
    $head_tags = explode("\n", $head_html);

    // go through each and analyze it's content
    $found_meta_tags = array();

    foreach ($head_tags as $head_tag) {
        if (!$head_tag) {
            continue;
        }

        // trim
        $head_tag = trim($head_tag);

        // check for charset meta tag
        if (preg_match('/<meta\s+[^>]*charset\s*=\s*["\'][^"\']+["\'][^>]*>/i', $head_tag)) {
            $found_meta_tags["charset"] = array (
                "raw-html" => trim(ai4seo_remove_header_line_break_placeholders($head_tag)),
                "content" => "charset",
            );
        }

        // check for viewport meta tag
        if (preg_match('/<meta\s+[^>]*name\s*=\s*["\']viewport["\'][^>]*>/i', $head_tag)) {
            $found_meta_tags["viewport"] = array (
                "raw-html" => trim(ai4seo_remove_header_line_break_placeholders($head_tag)),
                "content" => "viewport",
            );
        }

        // go through each metadata field and check if the meta-tag-regex matches
        foreach ($ai4seo_metadata_details AS $this_metadata_identifier => $this_metadata_field_details) {
            $this_meta_tag_regex = $this_metadata_field_details["meta-tag-regex"] ?? "";
            $this_meta_tag_regex_match_index = $this_metadata_field_details["meta-tag-regex-match-index"] ?? 0;

            if (!$this_meta_tag_regex || !$this_meta_tag_regex_match_index) {
                continue;
            }

            if (!preg_match($this_meta_tag_regex, $head_tag, $this_meta_tag_regex_matches)) {
                continue;
            }

            if (!isset($this_meta_tag_regex_matches[$this_meta_tag_regex_match_index])) {
                continue;
            }

            // Workaround: replace line break placeholders back
            $this_meta_tag_regex_matches[0] = trim(ai4seo_remove_header_line_break_placeholders($this_meta_tag_regex_matches[0]));
            $this_meta_tag_regex_matches[$this_meta_tag_regex_match_index] = trim(ai4seo_remove_header_line_break_placeholders($this_meta_tag_regex_matches[$this_meta_tag_regex_match_index]));

            $found_meta_tags[$this_metadata_identifier][] = array (
                "raw-html" => $this_meta_tag_regex_matches[0],
                "content" => $this_meta_tag_regex_matches[$this_meta_tag_regex_match_index],
            );
        }
    }

    return $found_meta_tags;
}

// =========================================================================================== \\

/**
 * Removes line break placeholders from the given string
 * @param $string - the string to remove the line break placeholders from
 * @return string - the string without line break placeholders
 */
function ai4seo_remove_header_line_break_placeholders(string $string): string {
    return str_replace(array('#AI4SEO#LBRN#', '#AI4SEO#LBN#'), array("\r\n", "\n"), $string);
}

// =========================================================================================== \\

function ai4seo_handle_posts_to_be_analyzed() {
    // Make sure that the user is allowed to use this plugin
    if (!ai4seo_can_manage_this_plugin()) {
        return;
    }

    // get all posts that need to be analyzed
    $posts_to_be_analyzed = ai4seo_get_post_ids_from_option("_ai4seo_posts_to_be_analyzed");

    // if there are no posts to be analyzed, return
    if (!$posts_to_be_analyzed) {
        return;
    }

    // get the first post to be analyzed
    $post_id = array_shift($posts_to_be_analyzed);

    // check if the post id is numeric
    if (is_numeric($post_id)) {
        // analyze the post
        ai4seo_analyze_post($post_id);
    }

    // update the option
    ai4seo_remove_post_ids_from_option("_ai4seo_posts_to_be_analyzed", $post_id);
}


// ___________________________________________________________________________________________ \\
// === RIGHTS ================================================================================ \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Retrieve an array of all user-roles that are currently available
 * @return array An array of all user-roles
 */
function ai4seo_get_all_possible_user_roles(): array {
    global $ai4seo_fallback_allowed_user_roles;

    if (!function_exists('wp_roles')) {
        error_log("AI4SEO: wp_roles() does not exist. #49176824");
        return $ai4seo_fallback_allowed_user_roles;
    }

    // Attempt to get WordPress roles
    $wp_roles = wp_roles();

    // Check if wp_roles() returned a valid object
    if (!is_object($wp_roles) || !method_exists($wp_roles, 'get_names')) {
        error_log("AI4SEO: wp_roles() did not return a valid object. #50176824");
        return $ai4seo_fallback_allowed_user_roles;
    }

    // Get the array of role names
    $not_sanitized_user_roles = $wp_roles->get_names();

    // Check if roles array is not empty
    if (empty($not_sanitized_user_roles)) {
        error_log("AI4SEO: wp_roles() did not return any roles. #51176824");
        return $ai4seo_fallback_allowed_user_roles;
    }

    // sanitize and filter based on 'edit_post' capability
    $sanitized_user_roles = array();

    foreach ($not_sanitized_user_roles as $user_role_identifier => $user_role) {
        // Sanitize identifiers
        $user_role_identifier = sanitize_key($user_role_identifier);
        $user_role = sanitize_text_field($user_role);

        // Check if the role has the 'edit_post' capability
        $role_object = get_role($user_role_identifier);

        if ($role_object && $role_object->has_cap('edit_posts')) {
            $sanitized_user_roles[$user_role_identifier] = $user_role;
        }
    }

    ai4seo_remove_forbidden_allowed_user_roles($sanitized_user_roles);

    // add administrator role if it's not already in the array
    if (!isset($sanitized_user_roles["administrator"])) {
        $sanitized_user_roles["administrator"] = "Administrator";
    }

    return $sanitized_user_roles;
}

// =========================================================================================== \\

/**
 * Removes forbidden user roles from the given user roles array
 * @param $user_roles
 * @return void
 */
function ai4seo_remove_forbidden_allowed_user_roles(&$user_roles) {
    global $ai4seo_forbidden_allowed_user_roles;

    if (!is_array($user_roles)) {
        return;
    }

    foreach ($ai4seo_forbidden_allowed_user_roles as $user_role) {
        unset($user_roles[$user_role]);
    }
}


// ___________________________________________________________________________________________ \\
// === PLANS ================================================================================= \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Function to retrieve the given plans amount of credits
 * @param $plan
 * @return int
 */
function ai4seo_get_plan_credits($plan): int {
    $ai4seo_plans_credits = array(
        "free" => 100,
        "s" => 500,
        "m" => 1500,
        "l" => 5000,
    );

    return $ai4seo_plans_credits[$plan] ?? $ai4seo_plans_credits["free"];
}

// =========================================================================================== \\

/**
 * Return the name of the given plan
 * @param $plan
 * @return string
 */
function ai4seo_get_plan_name($plan): string {
    $ai4seo_plans_names = array(
        "free" => "Free",
        "s" => "Basic",
        "m" => "Pro",
        "l" => "Premium",
    );

    return $ai4seo_plans_names[$plan] ?? $ai4seo_plans_names["free"];
}


// ___________________________________________________________________________________________ \\
// === UTILITY FUNCTIONS ===================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Function to return the robhub api communicator
 * @return Ai4Seo_RobHubApiCommunicator
 */
function ai4seo_robhub_api(): Ai4Seo_RobHubApiCommunicator {
    global $ai4seo_robhub_api_communicator;
    return $ai4seo_robhub_api_communicator;
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
function ai4seo_deep_sanitize($data, string $sanitize_value_function_name = 'sanitize_text_field', string $sanitize_key_function_name = 'sanitize_key') {
    if (is_array($data)) {
        $sanitized_data = array();
        foreach ($data as $key => $value) {
            // Sanitize the key using the key sanitize function
            $sanitized_key = $sanitize_key_function_name($key);

            // Recursively sanitize the value if it's an array, or sanitize the value using the value sanitize function
            if (is_array($value)) {
                $sanitized_data[$sanitized_key] = ai4seo_deep_sanitize($value, $sanitize_value_function_name, $sanitize_key_function_name);
            } else {
                $sanitized_data[$sanitized_key] = $sanitize_value_function_name($value);
            }
        }
        return $sanitized_data;
    } else {
        // If it's not an array, sanitize the value directly
        return $sanitize_value_function_name($data);
    }
}

// =========================================================================================== \\

/**
 * Function to check whether the current user is allowed to use this plugin
 * @return bool
 */
function ai4seo_can_manage_this_plugin(): bool {
    // check if is_user_logged_in() is defined
    if (!function_exists('is_user_logged_in')) {
        return false;
    }

    // Check if the current user is logged in
    if (!is_user_logged_in()) {
        return false;
    }

    // Define variable for the allowed user-roles based on plugin-settings
    $allowed_user_roles = ai4seo_get_setting(AI4SEO_SETTING_ALLOWED_USER_ROLES);

    // Get the details of the current user
    $user = wp_get_current_user();

    // Stop script if the current user or the roles of the current user could not be read
    if (!$user || !isset($user->roles)) {
        return false;
    }

    // Loop through allowed roles and check if roles apply to current user
    foreach ($allowed_user_roles as $allowed_user_role) {
        // Check if the user has this allowed role
        if (in_array($allowed_user_role, (array) $user->roles)) {
            return true;
        }
    }

    return false;
}

// =========================================================================================== \\

/**
 * Given any text phrase that may not be suitable as a button or tab label, this function will return a nice label
 * @param $text string The text to be converted
 * @return string The nice label
 */
function ai4seo_get_nice_label(string $text, $separator = " "): string {
    // convert every _ to $separator
    $text = str_replace("_", $separator, $text);

    // explode by the separator
    $text_array = explode($separator, $text);

    // make every word start with a capital letter
    $text_array = array_map("ucfirst", $text_array);

    // put the words back together
    $text = implode($separator, $text_array);

    // make some manual adjustments
    $text = str_replace(array("Rss"), array("RSS"), $text);

    return $text;
}

// =========================================================================================== \\

/**
 * Function to simulate a singleton (only one call per function per id)
 * @param $id
 * @return bool
 */
function ai4seo_singleton($id): bool {
    $fullId = "Singleton-$id";
    if (isset($GLOBALS[$fullId])) {
        return false;
    } else {
        $GLOBALS[$fullId] = true;
        return true;
    }
}

// =========================================================================================== \\

/**
 * Return weather the given string is a valid json
 * @param $string
 * @return bool
 */
function ai4seo_is_json($string): bool {
    if (!is_string($string)) {
        return false;
    }

    // check if string starts with { or [
    if ($string[0] !== "{" && $string[0] !== "[") {
        return false;
    }

    json_decode($string);

    return (json_last_error() == JSON_ERROR_NONE);
}

// =========================================================================================== \\

/**
 * Returns the SVG tag for the given (fontawesome) icon name
 * @param string $icon_name The name of the icon. Check function for allowed icon names.
 * @param string $alt_text (optional)
 * @param string $icon_css_class (optional)
 * @return string The icon SVG tag
 */
function ai4seo_get_svg_tag(string $icon_name, string $alt_text = "", string $icon_css_class = ""): string {
    // Make sure that the icon-name is allowed
    if (!isset(AI4SEO_SVG_ICONS[$icon_name])) {
        return "";
    }

    $svg_tag = AI4SEO_SVG_ICONS[$icon_name];

    // add css class to svg tag
    if ($icon_css_class) {
        $icon_css_class = "ai4seo-icon " . $icon_css_class;
    } else {
        $icon_css_class = "ai4seo-icon";
    }

    $svg_tag = str_replace("<svg", "<svg class='" . esc_attr($icon_css_class) . "'", $svg_tag);

    // add alt text to svg tag
    if ($alt_text) {
        $svg_tag = str_replace("<svg", "<svg aria-label='" . esc_attr($alt_text) . "'", $svg_tag);
        $svg_tag = str_replace("</svg>", "<title>" . esc_html($alt_text) . "</title></svg>", $svg_tag);
    }

    return $svg_tag;
}

// =========================================================================================== \\

/**
 * Returns a question mark icon with tooltip
 * @param string $tooltip_text The tooltip text to be displayed
 * @param string $icon_css_class (optional) The css class for the icon
 * @param string $icon_name (optional) The name of the icon. Check function for allowed icon names.
 * @return string The icon SVG tag
 */
function ai4seo_get_icon_with_tooltip_tag(string $tooltip_text, string $icon_css_class = "", string $icon_name = "circle-question"): string {
    $icon = ai4seo_get_svg_tag($icon_name, "", $icon_css_class);
    $output = "<span class='ai4seo-icon-with-tooltip ai4seo-tooltip-holder'>";
    $output .= $icon;
    $output .= "<div class='ai4seo-tooltip'>{$tooltip_text}</div>";
    $output .= "</span>";
    return $output;
}

// =========================================================================================== \\

/**
 * Removes double sentences from the given string
 * @param $input_string
 * @return string
 */
function ai4seo_remove_double_sentences($input_string): string {
    // Split the input string into sentences using a regular expression
    $sentences = preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $input_string);

    // Create an empty array to store unique sentences
    $unique_sentences = array();

    // Loop through the sentences array and add unique sentences to the uniqueSentences array
    foreach ($sentences as $sentence) {
        $trimmed_sentence = trim($sentence);

        if (!in_array($trimmed_sentence, $unique_sentences)) {
            $unique_sentences[] = $trimmed_sentence;
        }
    }

    // Join the unique sentences back into a single string
    return implode(' ', $unique_sentences);
}

// =========================================================================================== \\

/**
 * Truncate a string after a specified soft cap length, considering the first end of sentence
 * as the end of the input, with a hard cap on the length.
 *
 * @param string $input   The input string to be truncated.
 * @param int $soft_cap The soft cap length after which to look for the end of a sentence.
 * @param int $hard_cap The hard cap length to truncate the string if no sentence end is found.
 * @return string         The truncated string.
 */
function ai4seo_truncate_sentence(string $input, int $soft_cap, int $hard_cap = 0 ): string {
    // Ensure the input length is within the limits.
    if ( mb_strlen( $input ) <= $soft_cap ) {
        return $input;
    }

    // if hard cap is less than soft cap, set hard cap to soft cap
    if ($hard_cap < $soft_cap) {
        $hard_cap = $soft_cap;
    }

    // Start truncation from soft cap onwards.
    $truncated_at_hard_cap = mb_substr( $input, 0, $hard_cap );
    $truncated_after_soft_cap = mb_substr( $truncated_at_hard_cap, $soft_cap );

    // Define sentence-ending punctuation marks.
    $punctuation_marks = array( '.', '!', '?', '…', '؟', '·', '。', '！', '？' );

    // Find the first sentence-ending punctuation after the soft cap.
    $first_sentence_after_soft_cap_end = PHP_INT_MAX;

    foreach ( $punctuation_marks as $mark ) {
        $position = mb_strpos( $truncated_after_soft_cap, $mark );

        if ( $position !== false ) {
            $first_sentence_after_soft_cap_end = min( $first_sentence_after_soft_cap_end, $position );
        }
    }

    // If an end of sentence is found, adjust the truncation to include it.
    if ( $first_sentence_after_soft_cap_end !== PHP_INT_MAX ) {
        $truncated_sentence = mb_substr( $truncated_at_hard_cap, 0, $soft_cap + $first_sentence_after_soft_cap_end + 1 );
    } else {
        // If no sentence end is found, ensure the truncation is at hard cap.
        $truncated_sentence = $truncated_at_hard_cap;
    }

    return $truncated_sentence;
}

// =========================================================================================== \\

/**
 * Returns the plugin basename
 * @return string The plugin basename
 */
function ai4seo_get_plugin_basename(): string {
    return sanitize_text_field(plugin_basename(__FILE__));
}

// =========================================================================================== \\

/**
 * Returns the plugin identifier (ai-for-seo) as known in WordPress plugin directory
 * @return string The plugin identifier
 */
function ai4seo_get_plugins_wordpress_identifier(): string {
    return "ai-for-seo";
}

// =========================================================================================== \\

/**
 * Returns the plugin's name
 * @return string The plugin name
 */
function ai4seo_get_plugins_official_name(): string {
    return "AI for SEO";
}

// =========================================================================================== \\

/**
 * Returns a url leading to a point within the plugin
 * @param string $tab The tab to navigate to
 * @param array $additional_parameter Additional parameters to add to the url
 * @return string The plugins admin sub page url
 */
function ai4seo_get_admin_url(string $tab = "", array $additional_parameter = array()): string {
    $tab = sanitize_key($tab);
    $plugins_wordpress_identifier = ai4seo_get_plugins_wordpress_identifier();
    $plugins_wordpress_identifier = sanitize_key($plugins_wordpress_identifier);
    $admin_sub_page_url = "/wp-admin/admin.php?page=" . $plugins_wordpress_identifier;

    // workaround: if tab is dashboard, remove it from the url
    if ($tab == "dashboard") {
        $tab = "";
    }

    // add tab if set
    if ($tab) {
        $admin_sub_page_url .= "&ai4seo-tab=" . $tab;
    }

    // add additional parameters if set
    if ($additional_parameter) {
        foreach ($additional_parameter as $key => $value) {
            $key = sanitize_key($key);
            $value = sanitize_text_field($value);
            $admin_sub_page_url = add_query_arg($key, $value, $admin_sub_page_url);
        }
    }

    return $admin_sub_page_url;
}

// =========================================================================================== \\

/**
 * Returns the url to a specific post type within the AI4SEO_POST_TYPES_TAB_NAME-Tab
 * @param string $post_type The post type to navigate to
 * @param int $current_page The current page to navigate to
 * @param array $additional_parameter Additional parameters to add to the url
 * @return string The url to the post type
 */
function ai4seo_get_post_type_url(string $post_type, int $current_page = 1, array $additional_parameter = array()): string {
    $additional_parameter["ai4seo-page"] = $current_page ?: "%#%"; # %#% = pagination workaround
    return ai4seo_get_admin_url(AI4SEO_POST_TYPES_TAB_NAME, array("ai4seo-post-type" => $post_type) + $additional_parameter);
}

// =========================================================================================== \\

/**
 * Returns whether the user is inside our plugin's admin pages
 * @return bool Whether the user is inside our plugin's admin pages
 */
function ai4seo_is_user_inside_plugin_admin_pages(): bool {
    $plugins_wordpress_identifier = ai4seo_get_plugins_wordpress_identifier();
    $plugins_wordpress_identifier = sanitize_key($plugins_wordpress_identifier);

    // check if the "page" parameter is set and if it is our plugin
    return isset($_GET["page"]) && sanitize_key($_GET["page"]) == $plugins_wordpress_identifier;
}

// =========================================================================================== \\

/**
 * Checks if the current tab is the given tab
 * @param string $tab The tab to check
 * @return bool Whether the current tab is the given tab
 */
function ai4seo_is_tab_open(string $tab = ""): bool {
    $tab = sanitize_key($tab);
    $request_uri = sanitize_text_field($_SERVER["REQUEST_URI"]);
    $admin_url = ai4seo_get_admin_url();
    $current_tab = ai4seo_get_current_tab();

    // check if we are inside the plugins admin pages
    if (strpos($request_uri, $admin_url) === false) {
        return false;
    }

    // Dashboard: both dashboard and empty tab are considered dashboard
    if (!$tab) {
        $tab = "dashboard";
    }

    if (!$current_tab) {
        $current_tab = "dashboard";
    }

    return $current_tab == $tab;
}

// =========================================================================================== \\

/**
 * Checks, if the current post type is the given post type
 * @param string $post_type The post type to check
 * @return bool Whether the current post type is the given post type
 */
function ai4seo_is_post_type_open(string $post_type): bool {
    $current_post_type = ai4seo_get_current_post_type();
    return $current_post_type == $post_type;
}

// =========================================================================================== \\

/**
 * Returns the current tab (admin url tab)
 * @return string The current tab
 */
function ai4seo_get_current_tab(): string {
    return sanitize_key($_GET["ai4seo-tab"] ?? ai4seo_get_default_tab());
}

// =========================================================================================== \\

/**
 * Returns the current tab (admin url tab)
 * @return string The current tab
 */
function ai4seo_get_current_post_type(): string {
    if (ai4seo_get_current_tab() != "post") {
        return "";
    }

    return sanitize_key($_GET["ai4seo-post-type"] ?? ai4seo_get_default_post_type());
}

// =========================================================================================== \\

/**
 * Returns the default tab (dashboard)
 * @return string The default tab
 */
function ai4seo_get_default_tab(): string {
    return "dashboard";
}

// =========================================================================================== \\

/**
 * Returns the default post type
 * @return string The default post type
 */
function ai4seo_get_default_post_type(): string {
    return "page";
}

// =========================================================================================== \\

/**
 * Returns the plugin directory path
 * @param string $sub_path The sub path to append to the plugin directory path (optional)
 * @return string The plugin directory path
 */
function ai4seo_get_plugin_dir_path(string $sub_path = ""): string {
    return plugin_dir_path(__FILE__) . $sub_path;
}

// =========================================================================================== \\

/**
 * Returns the plugins base urls
 * @param string $sub_path The sub path to append to the plugins base url (optional)
 * @return string The url to the file
 */
function ai4seo_get_plugins_url(string $sub_path = ""): string {
    return plugins_url($sub_path, __FILE__);
}

// =========================================================================================== \\

/**
 * Returns the path to includes/pages
 * @param string $sub_path The sub path to append to the includes/pages path (optional)
 * @return string The path to the file
 */
function ai4seo_get_includes_pages_path(string $sub_path = ""): string {
    return ai4seo_get_plugin_dir_path("includes/pages/{$sub_path}");
}

// =========================================================================================== \\

/**
 * Returns the path to includes/pages/content_types
 * @param string $sub_path The sub path to append to the includes/pages/content_types path (optional)
 * @return string The path to the file
 */
function ai4seo_get_includes_pages_content_types_path(string $sub_path = ""): string {
    return ai4seo_get_plugin_dir_path("includes/pages/content_types/{$sub_path}");
}

// =========================================================================================== \\

/**
 * Returns the path to includes/ajax/display
 * @param string $sub_path The sub path to append to the includes/ajax/display path (optional)
 * @return string The path to the file
 */
function ai4seo_get_includes_ajax_display_path(string $sub_path = ""): string {
    return ai4seo_get_plugin_dir_path("includes/ajax/display/{$sub_path}");
}

// =========================================================================================== \\

/**
 * Returns the path to includes/ajax/process
 * @param string $sub_path The sub path to append to the includes/ajax/process path (optional)
 * @return string The path to the file
 */
function ai4seo_get_includes_ajax_process_path(string $sub_path = ""): string {
    return ai4seo_get_plugin_dir_path("includes/ajax/process/{$sub_path}");
}

// =========================================================================================== \\

/**
 * Returns the path to includes/elements
 * @param string $sub_path The sub path to append to the includes/elements path (optional)
 * @return string The path to the file
 */
function ai4seo_get_includes_elements_path(string $sub_path = ""): string {
    return ai4seo_get_plugin_dir_path("includes/elements/{$sub_path}");
}

// =========================================================================================== \\

/**
 * Returns the path to includes/api
 * @param string $sub_path The sub path to append to the includes/api path (optional)
 * @return string The path to the file
 */
function ai4seo_get_includes_api_path(string $sub_path = ""): string {
    return ai4seo_get_plugin_dir_path("includes/api/{$sub_path}");
}

// =========================================================================================== \\

/**
 * Returns the url to assets/images
 * @param string $file_name The name of the file to get the path for
 * @return string The url to the file
 */
function ai4seo_get_assets_images_url($file_name): string {
    return ai4seo_get_plugins_url("assets/images/{$file_name}");
}

// =========================================================================================== \\

/**
 * Returns the url to assets/css
 * @param string $file_name The name of the file to get the path for
 * @return string The url to the file
 */
function ai4seo_get_assets_css_path(string $file_name): string {
    return ai4seo_get_plugins_url("assets/css/{$file_name}");
}

// =========================================================================================== \\

/**
 * Returns the url to assets/js
 * @param string $file_name The name of the file to get the path for
 * @return string The url to the file
 */
function ai4seo_get_assets_js_path(string $file_name): string {
    return ai4seo_get_plugins_url("assets/js/{$file_name}");
}

// =========================================================================================== \\

/**
 * Returns the url to the AI for SEO logo
 * @param string $variant The variant of the logo to get the url for
 * @return string The url to the file
 */
function ai4seo_get_ai_for_seo_logo_url(string $variant = "32x32"): string {
    switch ($variant) {
        case "svg":
            return ai4seo_get_assets_images_url("logos/ai-for-seo.svg");
        case "64x64":
            return ai4seo_get_assets_images_url("logos/ai-for-seo-logo-64x64.png");
        case "32x32":
        default:
            return ai4seo_get_assets_images_url("logos/ai-for-seo-logo-32x32.png");
    }
}

// =========================================================================================== \\

/**
 * Returns the purchase plan url
 * @param string $ai4seo_client_id
 * @return string The purchase plan url
 */
function ai4seo_get_purchase_plan_url(string $ai4seo_client_id): string {
    return AI4SEO_OFFICIAL_PRICING_WEBPAGE . "/?client-id={$ai4seo_client_id}";
}

// =========================================================================================== \\

/**
 * This function uses wp_kses with our collection of allowed html tags and attributes
 * @param $content string The content to sanitize
 * @return string The sanitized content
 */
function ai4seo_wp_kses(string $content): string {
    global $ai4seo_allowed_html_tags_and_attributes;
    return wp_kses($content, $ai4seo_allowed_html_tags_and_attributes);
}

// =========================================================================================== \\

function ai4seo_get_publicly_accessible_post_types(): array {
    $excluded_post_types = array(
        'attachment',
        'revision',
        'nav_menu_item',
        'custom_css',
        'customize_changeset',
        'oembed_cache',
        'user_request',
        'template',
        'wp_block',
    );

    $args = array(
        'public'   => true,
    );
    $post_types = get_post_types($args, 'objects');
    $publicly_accessible_post_types = array();

    foreach ($post_types as $post_type) {
        if (!$post_type->_builtin && !$post_type->publicly_queryable) {
            continue;
        }

        if (!$post_type->_builtin && !$post_type->rewrite) {
            continue;
        }

        if (in_array($post_type->name, $excluded_post_types)) {
            continue;
        }

        if ($post_type->has_archive || $post_type->capability_type === 'post' || !$post_type->exclude_from_search) {
            $publicly_accessible_post_types[$post_type->name] = $post_type->label;
        }
    }

    return $publicly_accessible_post_types;
}

// =========================================================================================== \\

/**
 * This function retrieves the language code of the WordPress installation as defined in the settings
 * @return string The language code of the WordPress installation
 */
function ai4seo_get_wordpress_language_code(): string {
    return get_bloginfo("language");
}

// =========================================================================================== \\

/**
 * This function retrieves the language of the WordPress installation as defined in the settings
 * @return string The language of the WordPress installation
 */
function ai4seo_get_wordpress_language(): string {
    $wordpress_language_code = ai4seo_get_wordpress_language_code();
    return ai4seo_get_language_long_version($wordpress_language_code);
}

// =========================================================================================== \\

/**
 * This functions returns the long version of a given language short version (de_DE -> german)
 * @param string $language_short_version The short version of the language
 * @return string The long version of the language
 */
function ai4seo_get_language_long_version(string $language_short_version): string {
    // Normalize the short code by converting it to lowercase
    $language_short_version = strtolower($language_short_version);

    // Check for a full language code match first
    if (isset(AI4SEO_FULL_LANGUAGE_CODE_MAPPING[$language_short_version])) {
        return AI4SEO_FULL_LANGUAGE_CODE_MAPPING[$language_short_version];
    }

    // Fall back to checking the base language code (first two letters)
    $language_base = substr($language_short_version, 0, 2);
    return AI4SEO_BASE_LANGUAGE_CODE_MAPPING[$language_base] ?? 'english';
}

// =========================================================================================== \\

/**
 * Check if a PHP function is usable (defined and not disabled).
 *
 * @param string $function_name The name of the function to check.
 * @return bool Returns true if the function is usable, false otherwise.
 */
function ai4seo_is_function_usable(string $function_name): bool {
    if (!function_exists($function_name)) {
        return false;
    }

    $disabled_functions = ini_get("disable_functions");

    if (!$disabled_functions) {
        return true;
    }

    return !in_array($function_name, explode(",", $disabled_functions));
}


// ___________________________________________________________________________________________ \\
// === POSTS ================================================================================= \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Returns all supported post types for this wordpress setup
 * @return array The supported post types
 */
function ai4seo_get_supported_post_types(): array {
    global $ai4seo_cached_supported_post_types;
    global $wpdb;

    if ($ai4seo_cached_supported_post_types) {
        return $ai4seo_cached_supported_post_types;
    }

    $publicly_accessible_post_types = ai4seo_get_publicly_accessible_post_types();
    $supported_post_types = array_keys($publicly_accessible_post_types);
    $supported_post_types = ai4seo_deep_sanitize($supported_post_types, "sanitize_key");

    // check in database for at least one post of each post type
    if ($supported_post_types) {
        $supported_post_types_from_database = $wpdb->get_col("SELECT DISTINCT post_type FROM {$wpdb->posts} WHERE post_type IN ('" . implode("', '", $supported_post_types) . "') AND post_status IN ('publish', 'future', 'private', 'pending') LIMIT 100");

        if ($supported_post_types_from_database) {
            $supported_post_types = $supported_post_types_from_database;
        }
    }

    // order the post types
    sort($supported_post_types);

    return $supported_post_types;
}

// =========================================================================================== \\

/**
 * @param $post_id int The ID of the post to get the pure text content for.
 * @param $max_length int The maximum length of the text content to return.
 * @return string The pure text content of the post.
 */
function ai4seo_get_post_content_summary(int $post_id, int $max_length = 1000): string {
    // Retrieve the post object
    $post = get_post($post_id);

    if (!$post) {
        return ''; // Return empty if post is not found
    }

    // the content parameter
    $content = '';

    // get post title
    $post_title = sanitize_text_field($post->post_title);

    // Return post-title if not empty
    if (!empty($post_title)) {
        $content .= trim($post_title) . ": ";
    }

    // get post excerpt
    $post_excerpt = sanitize_text_field($post->post_excerpt);

    // Return post-excerpt if not empty and not the same as the post-title
    if (!empty($post_excerpt) && $post_excerpt !== $post_title) {
        $content .= trim($post_excerpt) . " ";
    }

    // Get the post content
    $post_content = ai4seo_get_combined_post_content($post_id);

    // if not empty and differs from the post-title and post-excerpt
    if (!empty($post_content) && $post_content !== $post_title && $post_content !== $post_excerpt) {
        $content .= trim($post_content);
    }

    // Apply the 'the_content' filter to the post content
    $content = apply_filters('the_content', $content);

    // try to condense the post content
    ai4seo_condense_raw_post_content($content, $max_length);

    return $content;
}

// =========================================================================================== \\

/**
 * Returns the post content to a given post_id by also reading the content of the most common page builders and
 * combining them into one content
 * @param int $post_id The post or page id to read the content from
 * @param string $editor_identifier The identifier of the editor to read the content from
 * @return false|string The post or page content or false if the post_id is empty
 */
function ai4seo_get_combined_post_content(int $post_id = 0, string $editor_identifier = "") {
    // Define variables for the current theme and the parent theme
    $current_theme = wp_get_theme();
    $parent_theme = $current_theme->parent();

    // Read post-id if it is not numeric
    if (empty($post_id)) {
        // Get post- or page-id
        $post_id = get_the_ID();
    }

    if (empty($post_id)) {
        return false;
    }

    // Get post-object
    $post = get_post($post_id);

    // Define variable for the combined post- or page-content
    $combined_content = array();

    // Get post-content
    $post_content = sanitize_text_field($post->post_content);
    $post_content_length = mb_strlen($post_content);

    // Return post-content if not empty and not the same as the post-title or post-excerpt
    if (!empty($post_content)) {
        $combined_content[] = trim($post_content);
    }

    // Elementor: only if the post_content got less than 100 characters, as the post_content should contain even a clearer version of the content
    if ((!$editor_identifier || $editor_identifier == "elementor") && is_plugin_active("elementor/elementor.php")
        && $post_content_length < 100) {
        // Get elementor-content
        $elementor_content = sanitize_text_field(get_post_meta($post_id, "_elementor_data", true));

        // Return elementor-content if not empty
        if (!empty($elementor_content)) {
            $combined_content[] = trim($elementor_content);
        }
    }

    // Check if muffin-builder-plugin is active. If yes, only consider it's content as it's the content that is shown on the page
    if ((!$editor_identifier || $editor_identifier == "mfn-builder") && ($current_theme->get("Name") === "Betheme"
            || ($parent_theme && $parent_theme->get("Name") === "Betheme"))) {
        // Get muffin-builder-content
        $muffin_builder_content = sanitize_text_field(get_post_meta($post_id, "mfn-page-items-seo", true));

        // Return muffin-builder-content if not empty
        if (!empty($muffin_builder_content)) {
            $combined_content[] = trim($muffin_builder_content);
        }
    }

    // Check if beaver-builder-plugin is active
    if ((!$editor_identifier || $editor_identifier == "fl-builder") && is_plugin_active("beaver-builder-lite-version/fl-builder.php")) {
        // Get beaver-builder-content
        $beaver_builder_content = sanitize_text_field(get_post_meta($post_id, "_fl_builder_data", true));

        // Return beaver-builder-content if not empty
        if (!empty($beaver_builder_content)) {
            $combined_content[] = trim($beaver_builder_content);
        }
    }

    // Check if divi-builder-plugin is active
    if ((!$editor_identifier || $editor_identifier == "divi-builder") && is_plugin_active("divi-builder/divi-builder.php")) {
        // Get divi-builder-content
        $divi_builder_content = sanitize_text_field(get_post_meta($post_id, "_et_pb_use_builder", true));

        // Return divi-builder-content if not empty
        if (!empty($divi_builder_content)) {
            $combined_content[] = trim($divi_builder_content);
        }
    }

    // Check if oxygen-plugin is active
    if ((!$editor_identifier || $editor_identifier == "oxygen") && is_plugin_active("oxygen/functions.php")) {
        // Get oxygen-content
        $oxygen_content = sanitize_text_field(get_post_meta($post_id, "ct_builder_shortcodes", true));

        // Return oxygen-content if not empty
        if (!empty($oxygen_content)) {
            $combined_content[] = trim($oxygen_content);
        }
    }

    // Check if brizy-plugin is active
    if ((!$editor_identifier || $editor_identifier == "brizy") && is_plugin_active("brizy/brizy.php")) {
        // Get brizy-content
        $brizy_content = sanitize_text_field(get_post_meta($post_id, "brizy_post_uid", true));

        // Return brizy-content if not empty
        if (!empty($brizy_content)) {
            $combined_content[] = trim($brizy_content);
        }
    }

    return implode(" ", $combined_content);
}

// =========================================================================================== \\

/**
 * Condenses the raw content to a more readable and useful format for the api
 * @param $content string The raw content to condense
 * @param $soft_cap int Consider at least this many characters before truncating
 * @param $hard_cap int Truncate the content to this length if no sentence end is found
 */
function ai4seo_condense_raw_post_content(string &$content, int $soft_cap = 2000, int $hard_cap = 2250) {
    global $shortcode_tags;

    // Remove <style> and <script> tags and their content
    $content = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $content);
    $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);

    // Remove HTML comments
    $content = preg_replace('/<!--(.*?)-->/', '', $content);

    // Remove CSS/JS comments
    $content = preg_replace('/\/\*(.*?)\*\//', '', $content);

    // replace \/ with /
    $content = str_replace('\/', '/', $content);
    $content = str_replace('\'', "'", $content);

    // remove icons ("icon-lamp")
    $content = preg_replace('/icon-[a-z0-9-]+/', '', $content);

    // remove shortcodes like [vc_row1]
    $content = preg_replace('/\[[a-zA-Z0-9_]+(\]|$)/', '', $content);

    // Remove opening vc_ shortcodes
    $content = preg_replace('/\[vc_[^\]]+(\]|$)/', '', $content);

    // Remove closing vc_ shortcodes
    $content = preg_replace('/\[\/vc_[^\]]+(\]|$)/', '', $content);

    // handle $shortcode_tags
    $shortcodes = array_keys($shortcode_tags);

    if ($shortcodes) {
        foreach ($shortcodes as $shortcode) {
            $content = preg_replace('/\[' . $shortcode . '[^\]]*\]/', '', $content);
            $content = preg_replace('/\[\/' . $shortcode . '[^\]]*\]/', '', $content);
        }
    }

    // Remove all HTML tags
    $content = strip_tags($content);

    // remove all URLs
    $content = ai4seo_remove_urls_from_string($content);

    // Replace multiple spaces with a single space and trim whitespace
    $content = preg_replace('/\s+/', ' ', $content);
    $content = trim($content);

    // remove be-builder progress bar infos (50 10 #72a5d8)
    $content = preg_replace('/[0-9]+ [0-9]+ #[a-f0-9]+/', '', $content);
    $content = preg_replace('/[0-9]+ [0-9]+ (grey|gray|red|green|blue|yellow|orange|purple|pink|black|white)/', '', $content);

    // Decode HTML entities and handle common entities separately
    $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Handle common entities that might not be converted
    $content = str_replace(['&nbsp;', '&amp;', '&quot;', '&#39;', '&lt;', '&gt;', '&;', '\u2019', 'â€™', 'â€', 'â€³', '€™t', '\u201d'], [' ', '&', '"', "'", '<', '>', '\'', '\'', '\'', '"', '"', '\'', '"'], $content);

    // Replace multiple spaces with a single space and trim whitespace
    $content = preg_replace('/\s+/', ' ', $content);
    $content = trim($content);

    // remove remaining short tags with all kinds of [ - ] combinations,
    // but only apply the changes if we have at least AI4SEO_TOO_SHORT_CONTENT_LENGTH chars left
    $temp_content = preg_replace('/\[.*?\]/', '', $content);

    if ($content != $temp_content && mb_strlen($temp_content) >= AI4SEO_TOO_SHORT_CONTENT_LENGTH) {
        $content = $temp_content;

        // Replace multiple spaces with a single space and trim whitespace
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);
    }

    // remove double sentences
    $content = ai4seo_remove_double_sentences($content);

    // truncate sentence
    $content = ai4seo_truncate_sentence($content, $soft_cap, $hard_cap);
}

// =========================================================================================== \\

/**
 * Removes all URLs from a given string.
 *
 * @param string $content The input string from which URLs will be removed.
 * @return string The string with all URLs removed.
 */
function ai4seo_remove_urls_from_string(string $content): string {
    // Define the regex pattern to match URLs
    $pattern = '/\b(?:https?|ftp):\/\/\S+/i';

    // Use preg_replace to remove URLs
    $cleaned_content = preg_replace($pattern, '', $content);

    // Return the cleaned content
    return $cleaned_content;
}

// =========================================================================================== \\


/**
 * Is called when a post is updated or created, using the action hook "save_post". The function will add the post
 * id to the option "_ai4seo_posts_to_be_analyzed" to be analyzed by the plugin.
 * @param $post_id int the post id
 * @param $post WP_Post|null the post object
 * @param $update bool if the post is updated
 * @return void
 */
function ai4seo_mark_post_to_be_analyzed(int $post_id, WP_Post $post = null, bool $update = false) {
    // check if the post is a revision
    if (wp_is_post_revision($post_id)) {
        return;
    }

    // Check if this is an autosave routine. If it is, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Make sure that the user is allowed to use this plugin
    if (!ai4seo_can_manage_this_plugin()) {
        return;
    }

    // Verify this came from our screen and with proper authorization.
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Insert post id into option to be analyzed "_ai4seo_posts_to_be_analyzed"
    ai4seo_add_post_ids_to_option("_ai4seo_posts_to_be_analyzed", $post_id);
}

// =========================================================================================== \\

/**
 * Analyzes the post, currently updating the metadata coverage
 * @param $post_id int the post id
 * @return void
 */
function ai4seo_analyze_post(int $post_id) {
    if (!is_numeric($post_id)) {
        return;
    }

    // read post
    $post = get_post($post_id);

    // check if the post could be read
    if (!$post || is_wp_error($post) || !isset($post->post_type)) {
        return;
    }

    ai4seo_refresh_one_posts_metadata_coverage_status($post_id, $post);
}


// ___________________________________________________________________________________________ \\
// === OUTPUT FUNCTIONS ====================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Outputs an error notice
 * @param string $message
 * @param bool $is_dismissible
 * @return void
 */
function ai4seo_echo_error_notice(string $message, bool $is_dismissible = true ) {
    $plugins_official_name = ai4seo_get_plugins_official_name();
    echo '<div class="notice notice-error ai4seo-notice ' . ($is_dismissible ? "is-dismissible" : "") . '"><p>';
    echo '<img class="ai4seo-notice-icon" src="' . esc_url(ai4seo_get_ai_for_seo_logo_url("32x32")) . '" alt="' . esc_attr($plugins_official_name) . '" /> ';
    echo wp_kses_post($message);
    echo '</p></div>';
}

// =========================================================================================== \\

/**
 * Returns an error as JSON and quit the php execution.
 * @param $error_message
 * @param int $error_code
 * @return void
 */
function ai4seo_return_error_as_json($error_message, int $error_code = 100) {
    global $ai4seo_debug;

    // suppress output of errors or warnings or notices or whatever until this point
    if (!$ai4seo_debug) {
        ob_end_clean();
    }

    echo wp_json_encode(array(
        "success" => false,
        "error" => wp_kses_post($error_message),
        "code" => sanitize_key($error_code),
    ));

    die();
}

// ========================================================================================= \\

/**
 * Returns a success as JSON and quit the php execution.
 * @param $data
 * @return void
 */
function ai4seo_return_success_as_json($data) {
    global $ai4seo_debug;

    // suppress output of errors or warnings or notices or whatever until this point
    if (!$ai4seo_debug) {
        ob_end_clean();
    }

    $data = ai4seo_deep_sanitize($data, 'sanitize_post');

    echo wp_json_encode(
        array_merge(
            array("success" => true), $data
        )
    );

    die();
}

// =========================================================================================== \\

/**
 * Returns the HTML for the edit metadata button
 * @param $post_id int The post id to get the button for
 * @return string The HTML for the button
 */
function ai4seo_get_edit_metadata_button(int $post_id): string {
    $output = "<div class='button ai4seo-open-editor-modal-button' onclick='ai4seo_open_metadata_editor_modal(" . esc_js($post_id) . ");' title='" . esc_attr__("Edit metadata", "ai-for-seo") . "'>";
        $output .= ai4seo_get_svg_tag("pen-to-square", __("Edit metadata", "ai-for-seo"));
    $output .= "</div>";

    return $output;
}

// =========================================================================================== \\

/**
 * Returns the HTML for the edit attachment attributes button
 * @param $attachment_post_id int The post id to get the button for
 * @return string The HTML for the button
 */
function ai4seo_get_edit_attachment_attributes_button(int $attachment_post_id): string {
    $output = "<div class='button ai4seo-open-editor-modal-button' onclick='ai4seo_open_attachment_attributes_editor_modal(" . esc_js($attachment_post_id) . ");' title='" . esc_attr__("Edit media attributes", "ai-for-seo") . "'>";
    $output .= ai4seo_get_svg_tag("pen-to-square", __("Edit media attributes", "ai-for-seo"));
    $output .= "</div>";

    return $output;
}

// =========================================================================================== \\

/*function ai4seo_get_current_language() {
    // Read current language with weglot-plugin if it is installed and active
    if (function_exists("weglot_get_current_language")) {
        return weglot_get_current_language();
    }

    // Read current language with WPML-plugin if it is installed and active
    elseif (has_filter("wpml_current_language")) {
        return apply_filters("wpml_current_language", null);
    }

    // Read regular WordPress-language
    else {
        // Get language
        $language = get_locale();

        // Set default language if no language has been found
        if (empty($language)) {
            $language = "en_US";
        }

        // Convert language into simple language-code and return it
        return substr($language, 0, 2);
    }
}*/

// =========================================================================================== \\

/**
 * Generates the content for one accordion-element
 * @param string $headline
 * @param string $content
 * @return string
 */
function ai4seo_get_accordion_element(string $headline, string $content): string {
    // Generate output
    $output = "<div class='ai4seo-accordion-holder'>";
        // Add headline to output
        $output .= "<div class='card ai4seo-card ai4seo-accordion-headline' onclick='jQuery(\".ai4seo-accordion-content\").hide();jQuery(this).next().show();'>";
            $output .= $headline;
        $output .= "</div>";

        // Add content to output
        $output .= "<div class='card ai4seo-card ai4seo-accordion-content'>";
            $output .= $content;
        $output .= "</div>";
    $output .= "</div>";

    return $output;
}


// =========================================================================================== \\

function ai4seo_echo_half_donut_chart_with_headline_and_percentage($headline, $ai4seo_chart_values, $ai4seo_done, $ai4seo_total) {
    $ai4seo_percentage_done = round($ai4seo_done / $ai4seo_total * 100);

    // set $ai4seo_percentage_color
    if ($ai4seo_percentage_done < 99) {
        $ai4seo_percentage_color = "black";
    } else {
        $ai4seo_percentage_color = "#005500";
    }

    echo "<div class='ai4seo-chart-container'>";
        echo "<h4>" . esc_html($headline) . "</h4>";

        echo "<div class='ai4seo-half-donut-chart-container'>";
            ai4seo_echo_half_donut_chart($ai4seo_chart_values);

            echo "<div class='ai4seo-half-donut-chart-percentage' style='color: " . esc_attr($ai4seo_percentage_color) . ";'>";
                echo esc_html($ai4seo_percentage_done) . "%";
            echo "</div>";

            echo "<div class='ai4seo-half-donut-chart-done' style='color: " . esc_attr($ai4seo_percentage_color) . ";'>";
                echo sprintf(
                    esc_html__('%1$s/%2$s done', "ai-for-seo"),
                    esc_html($ai4seo_done),
                    esc_html($ai4seo_total)
                );
            echo "</div>";
        echo "</div>";
    echo "</div>";
}

// =========================================================================================== \\

/**
 * Function to output a half donut chart
 * @param $values array Example: [ "type1" => ["value" => 10, "color" => "#ff0000"], "type2" => ["value" => 20, "color" => "#00ff00"] ]
 * @return void
 */

function ai4seo_echo_half_donut_chart($values) {
    $total = array_sum(array_column($values, 'value'));

    echo '<svg width="200" height="100" xmlns="http://www.w3.org/2000/svg">';
    $startOffset = -235; // Adjust start position so that it begins to the left
    foreach ($values as $type => $info) {
        $percentage = ($info['value'] / $total) * 235;
        // Offset calculation needs to be adjusted
        echo "<circle class='ai4seo-circle' r='75' cx='100' cy='100' fill='transparent' stroke='" . esc_attr($info['color']) . "' ";
        echo "stroke-width='20' stroke-dasharray='" . esc_attr($percentage) . " 99999' stroke-dashoffset='" . esc_attr($startOffset) . "' />";
        $startOffset -= $percentage;
    }
    echo '</svg>';
}

// =========================================================================================== \\

/**
 * Function to output the legend for the half donut chart
 * @param $values array Example: [ "type1" => ["value" => 10, "color" => "#ff0000"], "type2" => ["value" => 20, "color" => "#00ff00"] ]
 * @return void
 */
function ai4seo_echo_chart_legend(array $values) {
    echo '<div class="ai4seo-chart-legend">';

    foreach ($values as $type => $info) {
        echo '<div class="ai4seo-chart-legend-item">';
            echo '<div class="ai4seo-chart-legend-color" style="background-color: ' . esc_attr($info['color']) . '"></div>';
            echo '<div class="ai4seo-chart-legend-text">' . esc_html(ai4seo_get_chart_legend_translation($type)) . '</div>';
        echo '</div>';
    }

    echo '</div>';
}

// =========================================================================================== \\

/**
 * Function to output a money-back-guarantee notice
 * @return void
 */
function ai4seo_output_money_back_guarantee_notice() {
    echo "<div class='card ai4seo-card ai4seo-money-back-guarantee-notice'>";

        // Portrait
        echo "<div class='ai4seo-andre-erbis-portrait'>";
            echo "<img src='" . esc_url(ai4seo_get_assets_images_url("andre-erbis-at-space-codes.webp")) . "' alt='André Erbis @ Space Codes - " . esc_attr__("SEO Expert and Full Stack Developer", "ai-for-seo") . "' />";
        echo "</div>";

        // Headline
        echo "<div class='ai4seo-money-back-guarantee-headline'>";
            echo esc_html__("We provide a 100% Risk-Free Money-Back Guarantee!", "ai-for-seo");
        echo "</div>";

        echo "<div class='ai4seo-money-back-guarantee-quote'>";
            echo sprintf(
                /* translators: %s is a clickable email address */
                esc_html__("We’re excited to have you experience \"AI for SEO\". During the first %u days after purchasing a plan (Basic, Pro or Premium), if \"AI for SEO\" isn’t the best fit, simply reach out at %s! We’ll happily refund %s of your money. No questions asked.", "ai-for-seo"),
                AI4SEO_MONEY_BACK_GUARANTEE_DAYS,
                '<a href="mailto:' . esc_attr(AI4SEO_SUPPORT_EMAIL) . '">' . esc_html(AI4SEO_SUPPORT_EMAIL) . '</a>',
                '100%',
            );
        echo "</div>";

        echo "<div class='ai4seo-money-back-guarantee-signature'>";
            echo "<img src='" . esc_url(ai4seo_get_assets_images_url("andre-erbis-signature.png")) . "' alt='André Erbis @ Space Codes - " . esc_attr__("SEO Expert and Full Stack Developer", "ai-for-seo") . "' /><br>";
            echo "André Erbis @ Space Codes - " . esc_html__("SEO Expert and Full Stack Developer", "ai-for-seo");
        echo "</div>";

    echo "</div>";
}

// =========================================================================================== \\

/**
 * Function to output a button text link tag
 * @param $href string The URL
 * @param $icon string The Font Awesome icon name
 * @param $text string The text to display
 * @param $css_class string The CSS class
 * @return string HTML
 */
function ai4seo_get_button_text_link_tag(string $href, string $icon, string $text, string $css_class = "", string $onclick = ""): string {
    $css_class = "ai4seo-button" . ($css_class ? " " . $css_class : "");

    $output = "<a href='" . esc_url($href) . "' class='" . esc_attr($css_class) . "' onclick='" . esc_attr($onclick) ."'>";
    $output .= ai4seo_get_svg_tag($icon) . $text;
    $output .= "</a>";

    return $output;
}


// =========================================================================================== \\

/**
 * Function to output a small button text link tag
 * @param $href string The URL
 * @param $icon string The Font Awesome icon name
 * @param $text string The text to display
 * @param $css_class string The CSS class
 * @return string HTML
 */
function ai4seo_get_small_button_tag(string $href, string $icon, string $text, string $css_class = "", string $onclick = ""): string {
    return ai4seo_get_button_text_link_tag($href, $icon, $text, "ai4seo-small-button" . ($css_class ? " " . $css_class : ""), $onclick);
}

// =========================================================================================== \\

/**
 * Retrieve the translation for the different content types
 * @return string The translation
 */
function ai4seo_get_post_type_translation($post_type, $count_or_plural = false): string {
    $post_type_original = $post_type;
    $post_type = strtolower($post_type);
    $translation = $post_type_original;

    switch ($post_type) {
        case "post":
        case "posts":
            // Plural
            if ($count_or_plural === true) {
                $translation = __("posts", "ai-for-seo");
            }
            // Singular
            else if ($count_or_plural === false) {
                $translation = __("post", "ai-for-seo");
            }
            // Singular or plural with count
            else {
                $translation = sprintf(_nx("%s post", "%s posts", $count_or_plural, "noun", "ai-for-seo"), $count_or_plural);
            }
            break;
        case "page":
        case "pages":
            // Plural
            if ($count_or_plural === true) {
                $translation = __("pages", "ai-for-seo");
            }
            // Singular
            else if ($count_or_plural === false) {
                $translation = __("page", "ai-for-seo");
            }
            // Singular or plural with count
            else {
                $translation = sprintf(_nx("%s page", "%s pages", $count_or_plural, "noun", "ai-for-seo"), $count_or_plural);
            }
            break;
        case "product":
        case "products":
            // Plural
            if ($count_or_plural === true) {
                $translation = __("products", "ai-for-seo");
            }
            // Singular
            else if ($count_or_plural === false) {
                $translation = __("product", "ai-for-seo");
            }
            // Singular or plural with count
            else {
                $translation = sprintf(_nx("%s product", "%s products", $count_or_plural, "noun", "ai-for-seo"), $count_or_plural);
            }
            break;
        case "portfolio":
        case "portfolios":
            // Plural
            if ($count_or_plural === true) {
                $translation = __("portfolios", "ai-for-seo");
            }
            // Singular
            else if ($count_or_plural === false) {
                $translation = __("portfolio", "ai-for-seo");
            }
            // Singular or plural with count
            else {
                $translation = sprintf(_nx("%s portfolio", "%s portfolios", $count_or_plural, "noun", "ai-for-seo"), $count_or_plural);
            }
            break;
        case "attachment":
        case "attachments":
            // Plural
            if ($count_or_plural === true) {
                $translation = __("attachments", "ai-for-seo");
            }
            // Singular
            else if ($count_or_plural === false) {
                $translation = __("attachment", "ai-for-seo");
            }
            // Singular or plural with count
            else {
                $translation = sprintf(_nx("%s attachment", "%s attachments", $count_or_plural, "noun", "ai-for-seo"), $count_or_plural);
            }
            break;
        case "media": # not a post type, but useful to have in some situations, as we describe attachments as media for the user
        case "medias":
            // Plural
            if ($count_or_plural === true) {
                $translation = __("media", "ai-for-seo");
            }
            // Singular
            else if ($count_or_plural === false) {
                $translation = __("media", "ai-for-seo");
            }
            // Singular or plural with count
            else {
                $translation = sprintf(_nx("%s media", "%s media", $count_or_plural, "noun", "ai-for-seo"), $count_or_plural);
            }
            break;
        case "media file": # not a post type, but useful to have in some situations, as we describe attachments as media for the user
        case "media files":
            // Plural
            if ($count_or_plural === true) {
                $translation = __("media files", "ai-for-seo");
            }
            // Singular
            else if ($count_or_plural === false) {
                $translation = __("media file", "ai-for-seo");
            }
            // Singular or plural with count
            else {
                $translation = sprintf(_nx("%s media file", "%s media files", $count_or_plural, "noun", "ai-for-seo"), $count_or_plural);
            }
            break;
        default:
            // plural
            if ($count_or_plural === true) {
                $translation .= "s";

            // singular
            } else if ($count_or_plural === false) {
                // nothing to do

            // singular / plural with a counter
            } else if (is_numeric($count_or_plural)) {
                $translation = $count_or_plural . " " . $post_type_original;

                if ($count_or_plural !== 1) {
                    $translation .= "s";
                }
            }
    }

    return $translation;
}

// =========================================================================================== \\

/**
 * Function that outputs the options for a language selection select field
 * @return string The html of the options for the select field
 */
function ai4seo_get_generation_language_select_options_html($selected = "auto"): string {
    $languages = ai4seo_get_translated_generation_language_options();
    $languages = array("auto" => "- " . __("Automatic", "ai-for-seo") . " -") + $languages;
    $options_html = "";

    foreach ($languages as $value => $text) {
        $selected_attribute = ($selected == $value) ? " selected" : "";
        $options_html .= "<option value='" . esc_attr($value) . "'" . esc_attr($selected_attribute) . ">" . esc_html($text) . "</option>";
    }

    return $options_html;
}

// =========================================================================================== \\

/**
 * Get all available language options for AI generation
 * @return array An array of all available language options this plugin supports for AI generation
 */
function ai4seo_get_translated_generation_language_options(): array {
    // Array of language codes and their corresponding names
    $languages = array(
        'albanian' => __('Albanian', 'ai-for-seo'),
        'arabic' => __('Arabic', 'ai-for-seo'),
        'bulgarian' => __('Bulgarian', 'ai-for-seo'),
        'chinese' => __('Chinese (General)', 'ai-for-seo'),
        'simplified chinese' => __('Chinese (Simplified)', 'ai-for-seo'),
        'traditional chinese' => __('Chinese (Traditional)', 'ai-for-seo'),
        'croatian' => __('Croatian', 'ai-for-seo'),
        'czech' => __('Czech', 'ai-for-seo'),
        'danish' => __('Danish', 'ai-for-seo'),
        'dutch' => __('Dutch', 'ai-for-seo'),
        'american english' => __('English (America)', 'ai-for-seo'),
        'british english' => __('English (Britain)', 'ai-for-seo'),
        'estonian' => __('Estonian', 'ai-for-seo'),
        'finnish' => __('Finnish', 'ai-for-seo'),
        'european french' => __('French (Europe)', 'ai-for-seo'),
        'canadian french' => __('French (Canada)', 'ai-for-seo'),
        'german' => __('German', 'ai-for-seo'),
        'greek' => __('Greek', 'ai-for-seo'),
        'hebrew' => __('Hebrew', 'ai-for-seo'),
        'hindi' => __('Hindi', 'ai-for-seo'),
        'hungarian' => __('Hungarian', 'ai-for-seo'),
        'icelandic' => __('Icelandic', 'ai-for-seo'),
        'indonesian' => __('Indonesian', 'ai-for-seo'),
        'italian' => __('Italian', 'ai-for-seo'),
        'japanese' => __('Japanese', 'ai-for-seo'),
        'korean' => __('Korean', 'ai-for-seo'),
        'latvian' => __('Latvian', 'ai-for-seo'),
        'lithuanian' => __('Lithuanian', 'ai-for-seo'),
        'macedonian' => __('Macedonian', 'ai-for-seo'),
        'maltese' => __('Maltese', 'ai-for-seo'),
        'norwegian' => __('Norwegian', 'ai-for-seo'),
        'polish' => __('Polish', 'ai-for-seo'),
        'european portuguese' => __('Portuguese (Europe)', 'ai-for-seo'),
        'brazilian portuguese' => __('Portuguese (Brazil)', 'ai-for-seo'),
        'romanian' => __('Romanian', 'ai-for-seo'),
        'russian' => __('Russian', 'ai-for-seo'),
        'serbian' => __('Serbian', 'ai-for-seo'),
        'slovak' => __('Slovak', 'ai-for-seo'),
        'slovenian' => __('Slovenian', 'ai-for-seo'),
        'spanish' => __('Spanish', 'ai-for-seo'),
        'swedish' => __('Swedish', 'ai-for-seo'),
        'thai' => __('Thai', 'ai-for-seo'),
        'turkish' => __('Turkish', 'ai-for-seo'),
        'ukrainian' => __('Ukrainian', 'ai-for-seo'),
        'vietnamese' => __('Vietnamese', 'ai-for-seo'),
    );

    return $languages;
}

// =========================================================================================== \\

/**
 * Retrieve the translation for the different chart-legend-types
 * @param string $legend_identifier
 * @return string
 */
function ai4seo_get_chart_legend_translation($legend_identifier): string {
    $legend_identifier_original = $legend_identifier;
    $legend_identifier = strtolower($legend_identifier);

    switch ($legend_identifier) {
        case "done":
            return __("Done", "ai-for-seo");
        case "processing":
            return __("Processing", "ai-for-seo");
        case "missing":
            return __("Missing SEO / Pending", "ai-for-seo");
        case "failed":
            return __("Failed (please check details)", "ai-for-seo");
        default:
            return $legend_identifier_original;
    }
}

// =========================================================================================== \\

function ai4seo_get_select_all_checkbox($target_checkbox_name, $label = "auto"): string {
    if ($label === "auto") {
        $label = __("Select All / Unselect All", "ai-for-seo");
    }

    $select_all_checkbox_id = "ai4seo-select-all-{$target_checkbox_name}";

    $output = "";

    if (!empty($label)) {
        $output .= "<label class='ai4seo-select-all-checkbox-label ai4seo-form-multiple-inputs' for='" . esc_attr($select_all_checkbox_id) . "'>";
    }

    $output .= "<input type='checkbox' class='ai4seo-select-all-checkbox' data-target='" . esc_attr($target_checkbox_name) . "' id='" . esc_attr($select_all_checkbox_id) . "'>";

    if (!empty($label)) {
        $output .= " " . esc_html($label);
        $output .= "</label>";
    }

    return $output;
}

// ___________________________________________________________________________________________ \\
// === THIRD PARTY SEO PLUGINS =============================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Collects all the currently supported and active third party SEO plugins
 * @return array The supported and currently active third party SEO plugins
 */
function ai4seo_get_active_third_party_seo_plugin_details(): array {
    $active_supported_third_party_seo_plugin_details = array();

    foreach (AI4SEO_THIRD_PARTY_SEO_PLUGIN_DETAILS AS $third_party_seo_plugin_identifier => $third_party_seo_plugin_details) {
        if (ai4seo_is_plugin_or_theme_active($third_party_seo_plugin_identifier)) {
            $active_supported_third_party_seo_plugin_details[$third_party_seo_plugin_identifier] = $third_party_seo_plugin_details;
        }
    }

    return $active_supported_third_party_seo_plugin_details;
}

// =========================================================================================== \\

/**
 * Returns the keyphrase of the currently active third party SEO plugin, if it exists
 * @param $post_id int The post id
 * @return string The keyphrase or an empty string
 */
function ai4seo_get_any_third_party_seo_plugin_keyphrase(int $post_id): string {
    $active_supported_third_party_seo_plugins = ai4seo_get_active_third_party_seo_plugin_details();

    foreach ($active_supported_third_party_seo_plugins AS $this_third_party_seo_plugin_identifier => $this_third_party_seo_plugin_details) {
        if (empty($this_third_party_seo_plugin_details['keyphrase-postmeta-key'])) {
            continue;
        }

        $keyphrase_postmeta_key = sanitize_text_field($this_third_party_seo_plugin_details['keyphrase-postmeta-key']);

        $this_keyphrase = get_post_meta($post_id, $keyphrase_postmeta_key, true);

        if (!empty($this_keyphrase) && is_string($this_keyphrase)) {
            return $this_keyphrase;
        }
    }

    return "";
}

// =========================================================================================== \\

/**
 * Returns the key phrases for the given post ids (based on the currently active third party seo plugin)
 * @param $post_ids array post ids
 * @return array key phrases by post id or null on error
 */
function ai4seo_read_third_party_seo_plugin_key_phrases(array $post_ids): ?array {
    global $wpdb;

    if (!$post_ids) {
        return array();
    }

    $postmeta_table = $wpdb->postmeta;
    $postmeta_table = sanitize_text_field($postmeta_table);

    // Sanitize and escape each post ID
    $sanitized_post_ids = array_map(function($id) use ($wpdb) {
        return intval($id);
    }, $post_ids);

    // Create a string of comma-separated post IDs
    $post_ids_string = implode(',', $sanitized_post_ids);

    // only consider the currently active third party seo plugins
    $active_supported_third_party_seo_plugins = ai4seo_get_active_third_party_seo_plugin_details();

    if (!$active_supported_third_party_seo_plugins) {
        return array();
    }

    // go through all active third party seo plugins and get the key phrases
    $key_phrases = array();

    foreach ($active_supported_third_party_seo_plugins AS $this_third_party_seo_plugin_identifier => $this_third_party_seo_plugin_details) {
        if (empty($this_third_party_seo_plugin_details['keyphrase-postmeta-key'])) {
            continue;
        }

        // if we found all key phrases, we can stop the loop
        if (count($key_phrases) == count($post_ids)) {
            break;
        }

        $this_keyphrase_postmeta_key = sanitize_text_field($this_third_party_seo_plugin_details['keyphrase-postmeta-key']);

        // Construct the SQL query
        $this_sql_query = "SELECT post_id, meta_value FROM " . esc_sql($postmeta_table) . " WHERE meta_key = '" . esc_sql($this_keyphrase_postmeta_key) . "' AND post_id IN ($post_ids_string)";

        $this_postmeta_entries = $wpdb->get_results($this_sql_query);

        // on error
        if ($wpdb->last_error) {
            return array();
        }

        if (!$this_postmeta_entries) {
            return array();
        }

        // loop through all key phrases and add them to the $ai4seo_this_page_post_ids array
        foreach ($this_postmeta_entries as $this_postmeta_entry) {
            $this_post_id = intval($this_postmeta_entry->post_id);
            $this_key_phrase_value = sanitize_text_field($this_postmeta_entry->meta_value);

            // Make sure that post id is numeric
            if (!$this_post_id) {
                continue;
            }

            // skip if we already have a key phrase for this post id
            if (isset($key_phrases[$this_post_id])) {
                continue;
            }

            // Add key phrase to the $ai4seo_this_page_post_ids array
            $key_phrases[$this_post_id] = $this_key_phrase_value;
        }
    }

    return $key_phrases;
}

// =========================================================================================== \\

/**
 * Returns the yoast seo scores for the given post ids
 * @param $post_ids array post ids
 * @return array yoast seo scores by post id or null on error
 */
function ai4seo_read_yoast_seo_scores(array $post_ids): ?array {
    global $wpdb;

    # todo: make this whole function dynamic

    // Make sure that yoast seo plugin is active
    if (!ai4seo_is_plugin_or_theme_active(AI4SEO_THIRD_PARTY_PLUGIN_YOAST_SEO)) {
        return array();
    }

    if (!$post_ids) {
        return array();
    }

    $postmeta_table = $wpdb->postmeta;
    $postmeta_table = sanitize_text_field($postmeta_table);

    // Sanitize and escape each post ID
    $sanitized_post_ids = array_map(function($id) use ($wpdb) {
        return intval($id);
    }, $post_ids);

    // Create a string of comma-separated post IDs
    $post_ids_string = implode(',', $sanitized_post_ids);

    // Construct the SQL query
    $sql = "SELECT post_id, meta_value FROM " . esc_sql($postmeta_table) .
        " WHERE meta_key = '_yoast_wpseo_linkdex' AND post_id IN ($post_ids_string)";

    $yoast_seo_scores = $wpdb->get_results($sql);

    // on error
    if ($wpdb->last_error) {
        return array();
    }

    if (!$yoast_seo_scores) {
        return array();
    }

    // loop through all yoast seo scores and add them to the $ai4seo_this_page_post_ids array
    $seo_scores = array();

    foreach ($yoast_seo_scores as $yoast_seo_score) {
        $post_id = $yoast_seo_score->post_id;
        $seo_score = $yoast_seo_score->meta_value;

        // Make sure that post id is numeric
        if (!is_numeric($post_id) || !$post_id) {
            continue;
        }

        // Add seo score to the $ai4seo_this_page_post_ids array
        $seo_scores[$post_id] = $seo_score;
    }

    return $seo_scores;
}

// ___________________________________________________________________________________________ \\
// === EXTERNAL PLUGINS ======================================================================= \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Returns weather a plugin or theme is active
 * @param $identifier
 * @return bool
 */
function ai4seo_is_plugin_or_theme_active($identifier): bool {
    global $ai4seo_cached_active_plugins_and_themes;

    // try use cache first
    if (isset($ai4seo_cached_active_plugins_and_themes[$identifier])) {
        return $ai4seo_cached_active_plugins_and_themes[$identifier];
    }

    // Make sure that plugin-file has been loaded
    if (!function_exists("is_plugin_active")) {
        include_once(ABSPATH . "wp-admin/includes/plugin.php");
    }

    if (!function_exists("is_plugin_active")) {
        return false;
    }

    $is_active = false;
    $check_this_theme_name = "";
    $check_this_file_path = "";
    $check_this_class_name = "";

    switch ($identifier) {
        // editors
        case AI4SEO_THIRD_PARTY_PLUGIN_BETHEME:
            $check_this_theme_name = "Betheme";
            break;
        case AI4SEO_THIRD_PARTY_PLUGIN_ELEMENTOR:
            $check_this_file_path = "elementor/elementor.php";
            $check_this_class_name = "Elementor\Plugin";
            break;

        // shops
        case AI4SEO_THIRD_PARTY_PLUGIN_WOOCOMMERCE:
            $check_this_file_path = "woocommerce/woocommerce.php";
            $check_this_class_name = "WooCommerce";
            break;

        // seo plugins
        case AI4SEO_THIRD_PARTY_PLUGIN_YOAST_SEO:
            $check_this_file_path = "wordpress-seo/wp-seo.php";
            $check_this_class_name = "WPSEO_Meta";
            break;

        case AI4SEO_THIRD_PARTY_PLUGIN_ALL_IN_ONE_SEO:
            $check_this_file_path = "all-in-one-seo-pack/all_in_one_seo_pack.php";
            $check_this_class_name = "AIOSEO\Plugin\AIOSEO";
            break;

        case AI4SEO_THIRD_PARTY_PLUGIN_RANK_MATH:
            $check_this_file_path = "seo-by-rank-math/rank-math.php";
            $check_this_class_name = "RankMath";
            break;

        case AI4SEO_THIRD_PARTY_PLUGIN_SEO_SIMPLE_PACK:
            $check_this_file_path = "seo-simple-pack/seo-simple-pack.php";
            $check_this_class_name = "SEO_SIMPLE_PACK";
            break;

        case AI4SEO_THIRD_PARTY_PLUGIN_SEOPRESS:
            $check_this_file_path = "wp-seopress/seopress.php";
            $check_this_class_name = "SEOPress\Core\Kernel";
            break;

        case AI4SEO_THIRD_PARTY_PLUGIN_SLIM_SEO:
            $check_this_file_path = "slim-seo/slim-seo.php";
            $check_this_class_name = "SlimSEO\\Core";
            break;

        case AI4SEO_THIRD_PARTY_PLUGIN_SQUIRRLY_SEO:
            $check_this_file_path = "squirrly-seo/squirrly.php";
            $check_this_class_name = "SQ_Classes_ObjController";
            break;

        case AI4SEO_THIRD_PARTY_PLUGIN_THE_SEO_FRAMEWORK:
            $check_this_file_path = "autodescription/autodescription.php";
            # do not check for class, as it is not unique, as the plugin uses a load system
            break;

        case AI4SEO_THIRD_PARTY_PLUGIN_BLOG2SOCIAL:
            $check_this_file_path = "blog2social/blog2social.php";
            $check_this_class_name = "B2S_System";
            break;
    }

    do {
        // check for a specific theme
        if ($check_this_theme_name) {
            $current_theme = wp_get_theme();
            $parent_theme = $current_theme->parent();

            // Check if betheme is active
            $is_active = $current_theme->get("Name") === $check_this_theme_name || ($parent_theme && $parent_theme->get("Name") === $check_this_theme_name);

            if (!$is_active) {
                break;
            }
        }

        //check for a specific plugin -> check path
        if ($check_this_file_path) {
            try {
                $is_active = is_plugin_active($check_this_file_path);
            } catch (Exception $e) {
                $is_active = false;
            }

            if (!$is_active) {
                break;
            }
        }

        //check for a specific plugin -> check class
        if ($check_this_class_name) {
            try {
                $is_active = class_exists($check_this_class_name);
            } catch (Exception $e) {
                $is_active = false;
            }

            if (!$is_active) {
                break;
            }
        }
    } while (false);

    // update cache
    $ai4seo_cached_active_plugins_and_themes[$identifier] = $is_active;

    return $is_active;
}


// ___________________________________________________________________________________________ \\
// === CRON JOBS ============================================================================= \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Function to schedule cron jobs
 * @return void
 */
function ai4seo_schedule_cron_jobs() {
    // add custom cron schedule for automated metadata generation
    if (!wp_next_scheduled("ai4seo_automated_generation_cron_job")) {
        wp_schedule_event(time(), "five_minutes", "ai4seo_automated_generation_cron_job");
    }

    // add custom cron schedule for analyzing the plugins performance
    if (!wp_next_scheduled("ai4seo_analyze_plugin_performance")) {
        wp_schedule_event(time(), "one_hour", "ai4seo_analyze_plugin_performance");
    }
}

// =========================================================================================== \\

/**
 * Function to un-schedule cron jobs
 * @return void
 */
function ai4seo_un_schedule_cron_jobs() {
    wp_clear_scheduled_hook('ai4seo_automated_generation_cron_job');
    wp_clear_scheduled_hook('ai4seo_analyze_plugin_performance');
}

// =========================================================================================== \\

/**
 * Function to add an additional cronjob call of a specific cronjob name, but only if there isn't already one scheduled within the next minute
 * @param $cronjob_name String the name of the cronjob
 * @return void
 */
function ai4seo_inject_additional_cronjob_call(string $cronjob_name, int $delay = 10) {
    // Current time
    $now = time();

    // Define a constant for the minimum interval in seconds
    $min_delay_for_looping_cron_jobs = 70;

    // workaround for cronjobs with a specific duration: check if the cron job has finished by now, if not, schedule
    // it right when it is finished
    if ($cronjob_name == "ai4seo_automated_generation_cron_job") {
        $last_execution_time = (int) ai4seo_get_last_cron_job_call_time("ai4seo_automated_generation_cron_job");

        if ($last_execution_time > $now - $min_delay_for_looping_cron_jobs) {
            $delay += $min_delay_for_looping_cron_jobs - ($now - $last_execution_time);
        }
    }

    // Get the next scheduled time for the event
    $next_scheduled = wp_next_scheduled($cronjob_name);

    // Schedule the event for ASAP only if there isn't already one scheduled within the $delay + 1 seconds
    if (!$next_scheduled || $next_scheduled > ($now + $delay + 1)) {
        // Clear the scheduled hook
        wp_unschedule_event($next_scheduled, $cronjob_name);

        // Schedule it to run ASAP (in $delay seconds)
        wp_schedule_single_event($now + $delay, $cronjob_name);
    }
}

// =========================================================================================== \\

/**
 * Function to add custom cron schedule
 * @param $schedules
 * @return mixed
 */
function ai4seo_add_cron_job_intervals($schedules) {
    $ai4seo_plugin_identifier = ai4seo_get_plugins_wordpress_identifier();

    $schedules["five_minutes"] = array(
        "interval" => 60 * 5, // Number of seconds, 5 minutes in seconds.
        "display"  => __("Every Five Minutes", $ai4seo_plugin_identifier),
    );

    $schedules["one_hour"] = array(
        "interval" => 60 * 60, // Number of seconds, 60 minutes in seconds.
        "display"  => __("Every Hour", $ai4seo_plugin_identifier),
    );

    return $schedules;
}

// =========================================================================================== \\

/**
 * Function to set the last execution time of a cronjob
 * @param $cron_job_name string the name of the cronjob
 * @param int $time the time of the last execution
 * @return bool true on success, false on failure
 */
function ai4seo_set_last_cron_job_call_time($cron_job_name, int $time = 0): bool {
    if (!is_numeric($time)) {
        return false;
    }

    $cron_job_name = sanitize_key($cron_job_name);
    $cron_job_name = preg_replace("/[^a-zA-Z0-9_]/", "", $cron_job_name);

    update_option("_ai4seo_last_cronjob_call", time());
    update_option("_ai4seo_last_cronjob_call_for_" . $cron_job_name, $time);
    return true;
}

// =========================================================================================== \\

/**
 * Function to get the last execution time of a cronjob
 * @param $cron_job_name string the name of the cronjob
 * @return int the last execution time of a cronjob
 */
function ai4seo_get_last_cron_job_call_time(string $cron_job_name = ""): int {
    if ($cron_job_name) {
        $cron_job_name = sanitize_key($cron_job_name);
        $cron_job_name = preg_replace("/[^a-zA-Z0-9_]/", "", $cron_job_name);

        return (int) get_option("_ai4seo_last_cronjob_call_for_" . $cron_job_name, 0);
    } else {
        return (int) get_option("_ai4seo_last_cronjob_call", 0);
    }
}

// === CRONJOB: ai4seo_automated_generation_cron_job() ============================================================== \\

/**
 * Function to automatically generate data for different kind of contexts
 * @return bool true on success, false on failure
 */
function ai4seo_automated_generation_cron_job($debug = false): bool {
    $max_execution_time = 55;
    $approximate_single_run_duration = 7;
    $max_tolerated_execution_time = 69;
    $max_runs = 20;

    // set the maximum execution time according to these functions needs
    set_time_limit($max_tolerated_execution_time + 5);

    // define the start time of this cron job function call
    $start_time = time();

    // if the last execution time is less than $max_execution_time + $approximate_single_run_duration,
    // we have to skip this run, to prevent multiple runs at the same time
    if (!$debug && $start_time - ai4seo_get_last_cron_job_call_time("ai4seo_automated_generation_cron_job") < $max_execution_time + $approximate_single_run_duration) {
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("skipped, because last execution was too close", true)) . "<</pre>";
        }
        return true;
    }

    // update the last execution time of this cron job
    ai4seo_set_last_cron_job_call_time("ai4seo_automated_generation_cron_job", $start_time);


    // === GET ROBHUB API COMMUNICATOR ========================================================= \\

    try {
        if (!ai4seo_robhub_api() instanceof Ai4Seo_RobHubApiCommunicator) {
            if ($debug) {
                echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("could not initiate robhub api communicator #1 -> skip", true)) . "<</pre>";
            }

            return false;
        }
    } catch (Exception $e) {
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("could not initiate robhub api communicator #2 -> skip", true)) . "<</pre>";
        }

        return false;
    }

    // check if credentials are set
    if (!ai4seo_robhub_api()->init_credentials()) {
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("auth failed -> skip", true)) . "<</pre>";
        }

        return false;
    }

    // check the current credits balance, compare it to AI4SEO_MIN_CREDITS_BALANCE and if it's lower, return true
    if (ai4seo_robhub_api()->get_credits_balance() < AI4SEO_MIN_CREDITS_BALANCE) {
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("skipped, because of low credits balance", true)) . "<</pre>";
        }
        return true;
    }

    $run_counter = 1;

    do {
        $did_something_good = false;

        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("start new run: #{$run_counter}", true)) . "<</pre>";
        }

        // check if there is any processing operations going on
        $processing_metadata_post_ids = ai4seo_get_post_ids_from_option(AI4SEO_PROCESSING_METADATA_POST_IDS);
        $processing_attachment_post_ids = ai4seo_get_post_ids_from_option(AI4SEO_PROCESSING_ATTACHMENT_ATTRIBUTES_POST_IDS);

        if ($processing_metadata_post_ids || $processing_attachment_post_ids) {
            if ($debug) {
                echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("skipped, because of processing operations", true)) . "<</pre>";
            }

            sleep(5);
            $run_counter++;
            continue;
        }


        // todo: keyphrase
        // --------------

        // metadata
        $success = ai4seo_automated_metadata_generation($debug);

        if ($success) {
            $did_something_good = true;
        }

        // attachments
        $success = ai4seo_automated_attachment_attributes_generation($debug);

        if ($success) {
            $did_something_good = true;
        }

        sleep(3);
        $run_counter++;
    } while (
        $did_something_good &&
        time() - $start_time < $max_execution_time - $approximate_single_run_duration &&
        $run_counter < $max_runs
    );

    // workaround: empty all leftover processing ids (only relevant if the generation was aborted for an unknown reason)
    update_option(AI4SEO_PROCESSING_METADATA_POST_IDS, array());
    update_option(AI4SEO_PROCESSING_ATTACHMENT_ATTRIBUTES_POST_IDS, array());

    // reschedule this cronjob asap, so that the next posts can be filled shortly
    if ($did_something_good) {
        ai4seo_inject_additional_cronjob_call("ai4seo_automated_generation_cron_job", 1);
    }

    return true;
}

// =========================================================================================== \\

/**
 * Function to automatically generate metadata for posts
 * @return bool true on success, false on failure
 */
function ai4seo_automated_metadata_generation($debug = false, $only_this_post_id = 0): bool {
    if ($only_this_post_id) {
        $post_id = $only_this_post_id;
    } else {
        // try to search for posts with missing metadata
        // todo: only if the user wants to generate new or yet unknown posts
        ai4seo_excavate_post_entries_with_missing_metadata($debug);

        $pending_post_ids = ai4seo_get_post_ids_from_option(AI4SEO_PENDING_METADATA_POST_IDS);

        if (!$pending_post_ids) {
            // skip here because we don't have any posts or pages
            if ($debug) {
                echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("No pending posts found", true)) . "<</pre>";
            }
            return false;
        }

        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("Found pending post(s): " . esc_html(implode(", ", $pending_post_ids)), true)) . "<</pre>";
        }

        // only take one post id
        $post_id = reset($pending_post_ids);
    }

    // make sure every entry is numeric
    if (!is_numeric($post_id)) {
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("post-id is not numeric", true)) . "<</pre>";
        }
        return false;
    }

    if ($debug) {
        echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("going to generate metadata for #" . esc_html($post_id), true)) . "<</pre>";
    }

    // check the seo coverage of the posts and pages
    // todo: make this depend on whether the user wants to overwrite existing data (hard generation reset)
    if (ai4seo_read_is_active_metadata_fully_covered($post_id)) {
        // add the new post ids to the option "ai4seo_already_filled_metadata_post_ids"
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("full metadata coverage found -> skip", true)) . "<</pre>";
        }

        ai4seo_remove_post_ids_from_all_generation_status_options($post_id);
        ai4seo_add_post_ids_to_option(AI4SEO_FULLY_COVERED_METADATA_POST_IDS, $post_id);
        return true;
    }

    // mark post as being processed
    ai4seo_add_post_ids_to_option(AI4SEO_PROCESSING_METADATA_POST_IDS, $post_id);

    // first, let's get a summary of the content
    $post_content_summary = ai4seo_get_post_content_summary($post_id);
    $post_content_summary = sanitize_text_field($post_content_summary);

    // if we have original content -> go ahead
    $content_length = mb_strlen($post_content_summary);

    // check if content is at least AI4SEO_TOO_SHORT_CONTENT_LENGTH characters long
    if ($content_length < AI4SEO_TOO_SHORT_CONTENT_LENGTH) {
        ai4seo_handle_failed_metadata_generation($post_id, __FUNCTION__, "Post content is too short for post ID: " . $post_id, $debug);
        return true;
    }

    // check if content is not larger than AI4SEO_MAX_TOTAL_CONTENT_SIZE characters
    if ($content_length > AI4SEO_MAX_TOTAL_CONTENT_SIZE) {
        ai4seo_handle_failed_metadata_generation($post_id, __FUNCTION__, "Post content is too long for post ID: " . $post_id, $debug);
        return true;
    }

    // here we put our new generated data
    $new_generated_metadata = array();

    // check, if we already have a post content summary and compare for similarities
    $existing_post_content_summary = ai4seo_read_post_content_summary_from_post_meta($post_id);

    if ($existing_post_content_summary && ai4seo_are_post_content_summaries_similar($post_content_summary, $existing_post_content_summary)) {
        // add the post ids to the option "ai4seo_already_filled_metadata_post_ids"
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("content summary is similar -> use existing generated data", true)) . "<</pre>";
        }

        $new_generated_metadata = ai4seo_read_generated_data_from_post_meta($post_id);
    }

    // if we don't have any generated data yet, we have to generate it
    if (!$new_generated_metadata) {
        $metadata_generation_language = ai4seo_get_setting(AI4SEO_SETTING_METADATA_GENERATION_LANGUAGE);
        $metadata_generation_language = sanitize_text_field($metadata_generation_language);

        $api_call_parameters = array(
            "input" => $post_content_summary,
            "language" => $metadata_generation_language,
        );

        // check for a key phrase
        $ai4seo_keyphrase = "";

        $ai4seo_keyphrase = sanitize_text_field(ai4seo_get_any_third_party_seo_plugin_keyphrase($post_id));

        if ($ai4seo_keyphrase) {
            $api_call_parameters["keyphrase"] = $ai4seo_keyphrase;
        }

        $robhub_api_endpoint = "ai4seo/generate-all-metadata";

        try {
            $results = ai4seo_robhub_api()->call($robhub_api_endpoint, $api_call_parameters, "POST");
        } catch (Exception $e) {
            ai4seo_handle_failed_metadata_generation($post_id, __FUNCTION__, "Generation with RobHub API endpoint failed for media post ID: " . $post_id, $debug);
            return false;
        }


        // === CHECK RESULTS ========================================================================== \\

        // sanitize data
        $results = ai4seo_deep_sanitize($results);

        if (empty($results) || !is_array($results) || empty($results["data"]) || !ai4seo_is_json($results["data"])) {
            ai4seo_handle_failed_metadata_generation($post_id, __FUNCTION__, "Could not interpret data for post ID: " . $post_id . ($debug ? ": " . print_r($results, true) : ""), $debug);
            return false;
        }

        if (!isset($results["success"]) || ($results["success"] !== true && $results["success"] !== "true" && $results["success"] !== "1" && $results["success"] !== 1)) {
            ai4seo_handle_failed_metadata_generation($post_id, __FUNCTION__, "Generation with RobHub API endpoint failed for post ID: " . $post_id . ($debug ? ": " . print_r($results, true) : ""), $debug);
            return false;
        }

        // === ALL GOOD -> PROCEED TO SAVE There RESULTS ============================================================ \\

        $new_generated_metadata = json_decode($results["data"], true);
    }

    // === UPDATE ================================================================================= \\

    // update metadata to be the new active metadata
    ai4seo_update_active_metadata($post_id, $new_generated_metadata, false);

    // save generated data to post meta table
    ai4seo_save_generated_data_to_postmeta($post_id, $new_generated_metadata);
    ai4seo_save_post_content_summary_to_postmeta($post_id, $post_content_summary);

    // set posts as fully covered and generated
    ai4seo_remove_post_ids_from_all_generation_status_options($post_id);
    ai4seo_add_post_ids_to_option(AI4SEO_FULLY_COVERED_METADATA_POST_IDS, $post_id);
    ai4seo_add_post_ids_to_option(AI4SEO_GENERATED_METADATA_POST_IDS, $post_id);

    if ($debug) {
        echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("metadata generated for post ID: " . $post_id . ": " . esc_html(print_r($new_generated_metadata, true)), true)) . "<</pre>";
    }

    return true;
}

// =========================================================================================== \\

/**
 * Updates the currently active metadata for a post. Also applies the changes to the third party seo plugins postmeta (table) meta keys
 * @param $post_id int the post id
 * @param $metadata_updates array the updates
 * @param $overwrite_existing_data bool if true, existing data will be overwritten, if false, we check the settings to identify the metadata fields that should be overwritten
 * @return void
 */
function ai4seo_update_active_metadata(int $post_id, array $metadata_updates, bool $overwrite_existing_data = false) {
    global $ai4seo_metadata_details;

    // sanitize everything
    $metadata_updates = ai4seo_deep_sanitize($metadata_updates);

    // handle specific overwrite existing data instruction
    $overwrite_existing_data_metadata_names = array();

    if (!$overwrite_existing_data) {
        $overwrite_existing_data_metadata_names = ai4seo_get_setting(AI4SEO_SETTING_OVERWRITE_EXISTING_METADATA);
    }

    // go through $ai4seo_metadata_fields_details, find corresponding api-identifier and add the data to the post meta
    foreach ($ai4seo_metadata_details as $this_metadata_identifier => $this_metadata_details) {
        $this_api_identifier = $this_metadata_details["api-identifier"];
        $this_postmeta_key = ai4seo_generate_postmeta_key_by_metadata_identifier($post_id, $this_metadata_identifier);

        if (isset($metadata_updates[$this_metadata_identifier])) {
            $this_new_metadata_content = $metadata_updates[$this_metadata_identifier];
        } else if (isset($metadata_updates[$this_api_identifier])) {
            # workaround: also check the api identifier for this metadata, as the api sends facebook-title as social-media-title etc.
            $this_new_metadata_content = $metadata_updates[$this_api_identifier];
        } else {
            continue;
        }

        // do we overwrite this particular metadata field?
        if ($overwrite_existing_data === true) {
            $overwrite_this_metadata_field = true;
        } else {
            $overwrite_this_metadata_field = in_array($this_metadata_identifier, $overwrite_existing_data_metadata_names);
        }

        // update third party seo plugins metadata and get a hint if we should skip to update our own metadata, when we
        // do not overwrite third-party seo plugins data AND there is existing data already
        $we_should_not_save_our_own_metadata = ai4seo_update_third_party_seo_plugins_metadata($post_id, $this_metadata_identifier, $this_new_metadata_content, $overwrite_this_metadata_field);

        if ($we_should_not_save_our_own_metadata) {
            continue;
        }

        // add value to our own postmeta (table) meta key entry, but only if not set yet (fill only empty fields)
        if ($overwrite_this_metadata_field) {
            update_post_meta($post_id, $this_postmeta_key, $this_new_metadata_content);
        } else {
            ai4seo_update_postmeta_if_empty($post_id, $this_postmeta_key, $this_new_metadata_content);
        }

    }
}

// =========================================================================================== \\

/**
 * Updates the metadata for a post in the third party seo plugins postmeta (table) meta keys and if we do not overwrite
 * AND there is existing data already in one of the third party seo plugins, we return true to indicate that we should not save our own metadata for this field
 * @param $post_id int the post id
 * @param $metadata_identifier string the metadata identifier
 * @param $metadata_value string the metadata value
 * @param $overwrite_existing_data bool if true, existing data will be overwritten
 * @return bool if we do not overwrite AND there is existing data already
 **/
function ai4seo_update_third_party_seo_plugins_metadata(int $post_id, string $metadata_identifier, string $metadata_value, bool $overwrite_existing_data): bool {
    $we_should_not_save_our_own_metadata = false;

    // get the active third party seo plugins
    $active_supported_third_party_seo_plugins = ai4seo_get_active_third_party_seo_plugin_details();

    if (!$active_supported_third_party_seo_plugins) {
        return false;
    }

    $apply_changes_only_to_this_third_party_seo_plugin = ai4seo_get_setting(AI4SEO_SETTING_APPLY_CHANGES_TO_THIRD_PARTY_SEO_PLUGINS);

    if (!$apply_changes_only_to_this_third_party_seo_plugin || !is_array($apply_changes_only_to_this_third_party_seo_plugin)) {
        return false;
    }

    foreach ($active_supported_third_party_seo_plugins as $this_third_party_seo_plugin_identifier => $this_third_party_seo_plugin_details) {
        // check if we should only apply changes to a specific third party seo plugin
        if (!in_array($this_third_party_seo_plugin_identifier, $apply_changes_only_to_this_third_party_seo_plugin)) {
            continue;
        }

        // check if we got any meta keys for this third party seo plugin
        if (!isset($this_third_party_seo_plugin_details['metadata-postmeta-keys'][$metadata_identifier])) {
            continue;
        }

        // workaround: handle SLIM SEO (stores everything in a single serialized postmeta)
        if (ai4seo_is_plugin_or_theme_active(AI4SEO_THIRD_PARTY_PLUGIN_SLIM_SEO) && $this_third_party_seo_plugin_identifier === AI4SEO_THIRD_PARTY_PLUGIN_SLIM_SEO) {
            $this_was_updated = ai4seo_update_active_metadata_for_slim_seo($post_id, $metadata_identifier, $metadata_value, !$overwrite_existing_data);

            if (!$this_was_updated && !$overwrite_existing_data) {
                $we_should_not_save_our_own_metadata = true;
            }

            continue;
        }

        // workaround: handle Blog2Social (stores everything in a single serialized postmeta)
        if (ai4seo_is_plugin_or_theme_active(AI4SEO_THIRD_PARTY_PLUGIN_BLOG2SOCIAL) && $this_third_party_seo_plugin_identifier === AI4SEO_THIRD_PARTY_PLUGIN_BLOG2SOCIAL) {
            $this_was_updated = ai4seo_update_active_metadata_for_blog2social($post_id, $metadata_identifier, $metadata_value, !$overwrite_existing_data);

            if (!$this_was_updated && !$overwrite_existing_data) {
                $we_should_not_save_our_own_metadata = true;
            }

            continue;
        }

        // workaround: handle Squirrly SEO (stores everything in a single serialized column in own table)
        if (ai4seo_is_plugin_or_theme_active(AI4SEO_THIRD_PARTY_PLUGIN_SQUIRRLY_SEO) && $this_third_party_seo_plugin_identifier === AI4SEO_THIRD_PARTY_PLUGIN_SQUIRRLY_SEO) {
            $this_was_updated = ai4seo_update_active_metadata_for_squirrly_seo($post_id, $metadata_identifier, $metadata_value, !$overwrite_existing_data);

            if (!$this_was_updated && !$overwrite_existing_data) {
                $we_should_not_save_our_own_metadata = true;
            }

            continue;
        }

        $this_third_party_seo_plugin_postmeta_key = sanitize_text_field($this_third_party_seo_plugin_details['metadata-postmeta-keys'][$metadata_identifier]);

        if ($overwrite_existing_data) {
            update_post_meta($post_id, $this_third_party_seo_plugin_postmeta_key, $metadata_value);
        } else {
            $this_was_updated = ai4seo_update_postmeta_if_empty($post_id, $this_third_party_seo_plugin_postmeta_key, $metadata_value);

            if (!$this_was_updated) {
                $we_should_not_save_our_own_metadata = true;
            }
        }

        // handle specific third party seo plugin
        if (ai4seo_is_plugin_or_theme_active(AI4SEO_THIRD_PARTY_PLUGIN_ALL_IN_ONE_SEO) && $this_third_party_seo_plugin_identifier === AI4SEO_THIRD_PARTY_PLUGIN_ALL_IN_ONE_SEO) {
            ai4seo_update_active_metadata_for_all_in_one_seo($post_id, $metadata_identifier, $metadata_value, !$overwrite_existing_data);
            # we can ignore $this_was_updated as ALL in one SEO saves the values both in postmeta and in its own table
        }
    }

    return $we_should_not_save_our_own_metadata;
}

// =========================================================================================== \\

/**
 * Updates the metadata for a post for the Squirrly SEO plugin
 * @param int $post_id the post id
 * @param string $metadata_identifier the metadata identifier
 * @param string $metadata_value the metadata value
 * @param bool $only_if_empty if true, the metadata will only be updated if it is empty
 * @return bool true if we updated something, false if not
 */
function ai4seo_update_active_metadata_for_squirrly_seo(int $post_id, string $metadata_identifier, string $metadata_value, bool $only_if_empty = false): bool {
    // check table "wp_qss" -> column "seo". It's serialized with keys "title", "description", "og_title", "og_description", "tw_title", "tw_description"
    $metadata_identifier_mapping = array(
        "meta-title" => "title",
        "meta-description" => "description",
        "facebook-title" => "og_title",
        "facebook-description" => "og_description",
        "twitter-title" => "tw_title",
        "twitter-description" => "tw_description",
    );

    $this_slim_seo_json_key = $metadata_identifier_mapping[$metadata_identifier] ?? "";

    if (!$this_slim_seo_json_key) {
        return false;
    }

    // read entry
    global $wpdb;

    $squirrly_table = $wpdb->prefix . "qss";

    // Serialized key pattern for "ID"
    $pattern = '%s:2:"ID";i:' . esc_sql($post_id) . ';%';

    // Updated SQL query using LIKE to find the serialized post ID
    $wpdb_prepare = $wpdb->prepare(
        "SELECT seo FROM " . esc_sql($squirrly_table) . " WHERE post LIKE %s",
        $pattern
    );

    $current_squirrly_values = $wpdb->get_var($wpdb_prepare);
    $current_squirrly_values = maybe_unserialize($current_squirrly_values);

    if ($current_squirrly_values && is_string($current_squirrly_values)) {
        $current_squirrly_values = unserialize($current_squirrly_values);
    } else if ($current_squirrly_values && is_array($current_squirrly_values)) {
        // do nothing
    } else {
        $current_squirrly_values = array();
    }

    // something is wrong -> return false
    if (!is_array($current_squirrly_values) || empty($current_squirrly_values)) {
        return false;
    }

    // check the current value
    if ($only_if_empty) {
        if (isset($current_squirrly_values[$this_slim_seo_json_key]) && $current_squirrly_values[$this_slim_seo_json_key]) {
            return false;
        }
    }

    // update the value
    $current_squirrly_values[$this_slim_seo_json_key] = sanitize_text_field($metadata_value);

    $wpdb_prepare = $wpdb->prepare(
        "UPDATE " . esc_sql($squirrly_table) . " SET seo = %s WHERE post LIKE %s",
        maybe_serialize($current_squirrly_values),
        $pattern
    );

    $wpdb->query($wpdb_prepare);

    return true;
}

// =========================================================================================== \\

/**
 * Updates the metadata for a post for the Slim SEO plugin
 * @param int $post_id the post id
 * @param string $metadata_identifier the metadata identifier
 * @param string $metadata_value the metadata value
 * @param bool $only_if_empty if true, the metadata will only be updated if it is empty
 * @return bool true if we updated something, false if not
 */
function ai4seo_update_active_metadata_for_slim_seo(int $post_id, string $metadata_identifier, string $metadata_value, bool $only_if_empty = false): bool {
    // check postmeta "slim_seo". It's serialized with keys "title" and "description", nothing else
    $metadata_identifier_mapping = array(
        "meta-title" => "title",
        "meta-description" => "description",
    );

    $this_slim_seo_key = $metadata_identifier_mapping[$metadata_identifier] ?? "";

    if (!$this_slim_seo_key) {
        return false;
    }

    // read postmeta entry
    $current_slim_seo_values = get_post_meta($post_id, "slim_seo", true);
    $current_slim_seo_values = maybe_unserialize($current_slim_seo_values);

    // something is wrong -> return false
    if (!is_array($current_slim_seo_values) || !$current_slim_seo_values) {
        $current_slim_seo_values = array();
    }

    // check the current value
    if ($only_if_empty) {
        if (isset($current_slim_seo_values[$this_slim_seo_key]) && $current_slim_seo_values[$this_slim_seo_key]) {
            return false;
        }
    }

    // update the value
    $current_slim_seo_values[$this_slim_seo_key] = sanitize_text_field($metadata_value);

    update_post_meta($post_id, "slim_seo", $current_slim_seo_values);

    return true;
}

// =========================================================================================== \\

/**
 * Updates the metadata for a post for the Blog2Social plugin
 * @param int $post_id the post id
 * @param string $metadata_identifier the metadata identifier
 * @param string $metadata_value the metadata value
 * @param bool $only_if_empty if true, the metadata will only be updated if it is empty
 * @return bool true if we updated something, false if not
 */
function ai4seo_update_active_metadata_for_blog2social(int $post_id, string $metadata_identifier, string $metadata_value, bool $only_if_empty = false): bool {
    // check postmeta "_b2s_post_meta". It's serialized with keys "og_title", "og_desc", "card_title" and "card_desc"
    $metadata_identifier_mapping = array(
        "facebook-title" => "og_title",
        "facebook-description" => "og_desc",
        "twitter-title" => "card_title",
        "twitter-description" => "card_desc",
    );

    $this_mapped_key = $metadata_identifier_mapping[$metadata_identifier] ?? "";

    if (!$this_mapped_key) {
        return false;
    }

    // read postmeta entry
    $current_values = get_post_meta($post_id, "_b2s_post_meta", true);
    $current_values = maybe_unserialize($current_values);

    // something is wrong -> return false
    if (!is_array($current_values) || !$current_values) {
        $current_values = array();
    }

    // check the current value
    if ($only_if_empty) {
        if (isset($current_values[$this_mapped_key]) && $current_values[$this_mapped_key]) {
            return false;
        }
    }

    // update the value
    $current_values[$this_mapped_key] = sanitize_text_field($metadata_value);

    update_post_meta($post_id, "_b2s_post_meta", $current_values);

    return true;
}

// =========================================================================================== \\

/**
 * Updates the metadata for a post for the All in One SEO plugin
 * @param int $post_id the post id
 * @param string $metadata_identifier the metadata identifier
 * @param string $metadata_value the metadata value
 * @param bool $only_if_empty if true, the metadata will only be updated if it is empty
 * @return bool true if we updated something, false if not
 */
function ai4seo_update_active_metadata_for_all_in_one_seo(int $post_id, string $metadata_identifier, string $metadata_value, bool $only_if_empty = false): bool {
    // check table "wp_aioseo_posts" for the post id. Columns are "title", "description", "og_title", "og_description", "twitter_title", "twitter_description"
    $metadata_identifier_mapping = array(
        "meta-title" => "title",
        "meta-description" => "description",
        "facebook-title" => "og_title",
        "facebook-description" => "og_description",
        "twitter-title" => "twitter_title",
        "twitter-description" => "twitter_description",
    );

    $this_aioseo_column = $metadata_identifier_mapping[$metadata_identifier] ?? "";

    if (!$this_aioseo_column) {
        return false;
    }

    global $wpdb;

    $aioseo_table = $wpdb->prefix . "aioseo_posts";

    $sql = "SELECT post_id FROM " . esc_sql($aioseo_table) . " WHERE post_id = %d";

    $post_id_exists = $wpdb->get_var($wpdb->prepare($sql, $post_id));

    if (!$post_id_exists) {
        return false;
    }

    // check the current value
    if ($only_if_empty) {
        $sql = "SELECT " . esc_sql($this_aioseo_column) . " FROM " . esc_sql($aioseo_table) . " WHERE post_id = %d";
        $current_value = $wpdb->get_var($wpdb->prepare($sql, $post_id));

        if ($current_value) {
            return false;
        }
    }

    // update the value
    $sql = "UPDATE " . esc_sql($aioseo_table) . " SET " . esc_sql($this_aioseo_column) . " = %s WHERE post_id = %d";

    $wpdb->query($wpdb->prepare($sql, $metadata_value, $post_id));

    return true;
}

// =========================================================================================== \\

/**
 * Helps handle failed metadata generation by removing the post id from all generation status options and adding it to the failed ones
 * @param $post_id int the attachment post id
 * @param $function_name string the name of the function that failed
 * @param $error_message string the error message
 * @param $debug bool if true, debug information will be printed
 * @return void
 */
function ai4seo_handle_failed_metadata_generation(int $post_id, string $function_name = "", string $error_message = "", bool $debug = false) {
    if ($debug && $error_message && $function_name) {
        echo "<pre>" . esc_html($function_name) . " >" . esc_html(print_r($error_message, true)) . "<</pre>";
    }

    if ($error_message) {
        error_log("AI4SEO: " . $error_message);
    }

    ai4seo_remove_post_ids_from_all_generation_status_options($post_id);
    ai4seo_add_post_ids_to_option(AI4SEO_FAILED_METADATA_POST_IDS, $post_id);
}

// =========================================================================================== \\

/**
 * Function to automatically generate attributes for attachments
 * @param bool $debug debug mode yes or no
 * @param int $only_this_attachment_post_id care only this attachment post id
 * @return bool true on success, false on failure
 */
function ai4seo_automated_attachment_attributes_generation(bool $debug = false, int $only_this_attachment_post_id = 0): bool {
    global $ai4seo_allowed_attachment_mime_types;

    if ($only_this_attachment_post_id) {
        $attachment_post_id = $only_this_attachment_post_id;
    } else {
        // try to search for attachment posts with missing attributes
        // todo: only if the user wants to generate new or yet unknown posts
        ai4seo_excavate_attachments_with_missing_attributes($debug);

        $pending_attachment_post_ids = ai4seo_get_post_ids_from_option(AI4SEO_PENDING_ATTACHMENT_ATTRIBUTES_POST_IDS);

        if (!$pending_attachment_post_ids) {
            // skip here because we don't have any attachment posts
            if ($debug) {
                echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("No pending media posts found", true)) . "<</pre>";
            }
            return false;
        }

        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("Found pending media post(s): " . esc_html(implode(", ", $pending_attachment_post_ids)), true)) . "<</pre>";
        }

        // only take one post id
        $attachment_post_id = reset($pending_attachment_post_ids);
    }

    // make sure every entry is numeric
    if (!is_numeric($attachment_post_id)) {
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("media post-id is not numeric", true)) . "<</pre>";
        }
        return false;
    }

    if ($debug) {
        echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("going to generate media attributes for #" . esc_html($attachment_post_id), true)) . "<</pre>";
    }

    // check the seo coverage of the attachment
    // todo: make this depend on whether the user wants to overwrite existing data (hard generation reset)
    if (ai4seo_are_attachment_attributes_fully_covered($attachment_post_id)) {
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("full metadata coverage found -> skip", true)) . "<</pre>";
        }

        // add the new attachment post ids already filled ones
        ai4seo_remove_post_ids_from_all_generation_status_options($attachment_post_id);
        ai4seo_add_post_ids_to_option(AI4SEO_FULLY_COVERED_ATTACHMENT_ATTRIBUTES_POST_IDS, $attachment_post_id);
        return true;
    }

    // mark post as being processed
    ai4seo_add_post_ids_to_option(AI4SEO_PROCESSING_ATTACHMENT_ATTRIBUTES_POST_IDS, $attachment_post_id);

    // there are missing attachment attributes -> generate it
    // first, let's get the wp_post entry for more checks
    $attachment_post = get_post($attachment_post_id);

    // check if it's an attachment
    if (!$attachment_post || $attachment_post->post_type !== "attachment") {
        ai4seo_handle_failed_attachment_generation($attachment_post_id, __FUNCTION__, "Post is not a media for media post ID: " . $attachment_post_id, $debug);
        return true;
    }

    // check if it's one of the allowed mime types
    if (!in_array($attachment_post->post_mime_type, $ai4seo_allowed_attachment_mime_types)) {
        ai4seo_handle_failed_attachment_generation($attachment_post_id, __FUNCTION__, "Mime type not supported for media post ID: " . $attachment_post_id, $debug);
        return true;
    }

    // check url of the attachment
    $attachment_url = wp_get_attachment_url($attachment_post_id);
    $ai4seo_use_base64_image = false;

    if (!$attachment_url) {
        ai4seo_handle_failed_attachment_generation($attachment_post_id, __FUNCTION__, "Media URL not found for media post ID: " . $attachment_post_id, $debug);
        return true;
    }

    // check if url is valid
    if (!filter_var($attachment_url, FILTER_VALIDATE_URL)) {
        $ai4seo_use_base64_image = true;
    }

    if (ai4seo_robhub_api()->support_localhost_mode && $_SERVER["SERVER_NAME"] === "localhost") {
        $ai4seo_use_base64_image = true;
    }

    if (!$ai4seo_use_base64_image) {
        // check if the attachment url is accessible
        $attachment_url_headers = get_headers($attachment_url);

        if (!$attachment_url_headers || !is_array($attachment_url_headers) || !isset($attachment_url_headers[0])) {
            $ai4seo_use_base64_image = true;
        }

        if (strpos($attachment_url_headers[0], "200") === false) {
            $ai4seo_use_base64_image = true;
        }
    }

    if ($ai4seo_use_base64_image) {
        // Use wp_safe_remote_get instead of file_get_contents for fetching remote files
        $remote_get_response = wp_safe_remote_get($attachment_url);

        if (is_wp_error($remote_get_response)) {
            ai4seo_handle_failed_attachment_generation($attachment_post_id, __FUNCTION__, "Media URL not accessible for media post ID: " . $attachment_post_id, $debug);
            return true;
        }

        $this_attachment_contents = wp_remote_retrieve_body($remote_get_response);

        if (!$this_attachment_contents) {
            ai4seo_handle_failed_attachment_generation($attachment_post_id, __FUNCTION__, "Media content not accessible for media post ID: " . $attachment_post_id, $debug);
            return true;
        }

        $attachment_base64 = ai4seo_smart_image_base64_encode($this_attachment_contents);

        if (!$attachment_base64) {
            ai4seo_handle_failed_attachment_generation($attachment_post_id, __FUNCTION__, "Media content could not be base64 encoded for media post ID: " . $attachment_post_id, $debug);
            return true;
        }

        unset($this_attachment_contents);
    }

    // prepare robhub api call
    $robhub_endpoint = "ai4seo/generate-all-attachment-attributes";

    // determine target language
    $attachment_attributes_generation_language = sanitize_text_field(ai4seo_get_setting(AI4SEO_SETTING_ATTACHMENT_ATTRIBUTES_GENERATION_LANGUAGE));

    if ($attachment_attributes_generation_language == "auto") {
        // todo: determine language by context (attachment surroundings / usings)

        // fallback: WordPress language
        $attachment_attributes_generation_language = ai4seo_get_wordpress_language();
    }

    $ai4seo_api_call_parameters = array(
        "language" => $attachment_attributes_generation_language,
    );

    // localhost workaround -> send image as base64
    if ($ai4seo_use_base64_image) {
        $base64_image_encoded = sanitize_text_field("data:{$attachment_post->post_mime_type};base64,{$attachment_base64}");
        $ai4seo_api_call_parameters["input"] = $base64_image_encoded;
    } else {
        $ai4seo_api_call_parameters["attachment_url"] = $attachment_url;
    }

    try {
        $results = ai4seo_robhub_api()->call($robhub_endpoint, $ai4seo_api_call_parameters, "POST");
    } catch (Exception $e) {
        ai4seo_handle_failed_attachment_generation($attachment_post_id, __FUNCTION__, "Generation with RobHub API endpoint failed for media post ID: " . $attachment_post_id, $debug);
        return false;
    }


    // === CHECK RESULTS ========================================================================== \\

    // sanitize data
    $results = ai4seo_deep_sanitize($results);

    if (empty($results) || !is_array($results) || empty($results["data"]) || !ai4seo_is_json($results["data"])) {
        ai4seo_handle_failed_attachment_generation($attachment_post_id, __FUNCTION__, "RobHub API response is empty or malformed for media post ID: " . $attachment_post_id . ($debug ? ": " . print_r($results, true) : ""), $debug);
        return false;
    }

    if (!isset($results["success"]) || ($results["success"] !== true && $results["success"] !== "true" && $results["success"] !== "1" && $results["success"] !== 1)) {
        ai4seo_handle_failed_attachment_generation($attachment_post_id, __FUNCTION__, "Failed generation with error for media post ID: " . $attachment_post_id . ($debug ? ": " . print_r($results, true) : ""), $debug);
        return false;
    }

    
    // === ALL GOOD -> PROCEED TO SAVE There RESULTS ============================================================ \\

    $new_attachment_attributes = json_decode($results["data"], true);

    # todo: make overwriting variable depend on the user settings
    ai4seo_update_active_attachment_attributes($attachment_post_id, $new_attachment_attributes, false);

    // save generated data to post meta table
    ai4seo_save_generated_data_to_postmeta($attachment_post_id, $new_attachment_attributes);

    // add the attachment post id to the already filled ones
    ai4seo_remove_post_ids_from_all_generation_status_options($attachment_post_id);
    ai4seo_add_post_ids_to_option(AI4SEO_FULLY_COVERED_ATTACHMENT_ATTRIBUTES_POST_IDS, $attachment_post_id);
    ai4seo_add_post_ids_to_option(AI4SEO_GENERATED_ATTACHMENT_ATTRIBUTES_POST_IDS, $attachment_post_id);

    if ($debug) {
        echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("updated media attributes for #" . esc_html($attachment_post_id) . ":" . esc_html(print_r($new_attachment_attributes, true)), true)) . "<</pre>";
    }

    return true;
}

// =========================================================================================== \\

/**
 * Helps handle failed attachment generation by removing the post id from all generation status options and adding it to the failed ones
 * @param $attachment_post_id int the attachment post id
 * @param $function_name string the name of the function that failed
 * @param $error_message string the error message
 * @param $debug bool if true, debug information will be printed
 * @return void
 */
function ai4seo_handle_failed_attachment_generation(int $attachment_post_id, string $function_name = "", string $error_message = "", bool $debug = false) {
    if ($debug && $error_message && $function_name) {
        echo "<pre>" . esc_html($function_name) . " >" . esc_html(print_r($error_message, true)) . "<</pre>";
    }

    if ($error_message) {
        error_log("AI4SEO: " . $error_message);
    }

    ai4seo_remove_post_ids_from_all_generation_status_options($attachment_post_id);
    ai4seo_add_post_ids_to_option(AI4SEO_FAILED_ATTACHMENT_ATTRIBUTES_POST_IDS, $attachment_post_id);
}

// =========================================================================================== \\

/**
 * Updates the currently active attachment attributes for an attachment
 * @param int $attachment_post_id the attachment post id
 * @param array $updates the updates to apply with the keys title, caption, description, alt-text
 * @param bool $overwrite_existing_data if true, existing data will be overwritten, if false, we check the settings to identify the attachment attributes that should be overwritten
 * @return bool true on success, false on failure
 */
function ai4seo_update_active_attachment_attributes(int $attachment_post_id, array $updates = array(), bool $overwrite_existing_data = false): bool {
    // sanitize
    $updates = ai4seo_deep_sanitize($updates);

    // handle specific overwrite existing data instruction
    $overwrite_existing_data_attachment_attributes_names = array();

    if (!$overwrite_existing_data) {
        $overwrite_existing_data_attachment_attributes_names = ai4seo_get_setting(AI4SEO_SETTING_OVERWRITE_EXISTING_ATTACHMENT_ATTRIBUTES);
    }

    // read the attachment post
    $attachment_post = get_post($attachment_post_id);

    if (!$attachment_post) {
        return false;
    }

    // keep track if we made changes to the post
    $we_made_changes_to_the_post = false;

    // go through each update field and update the post or postmeta table
    foreach ($updates AS $this_attachment_attribute_name => $this_attachment_attribute_value) {
        // do we overwrite this particular attachment attribute?
        if ($overwrite_existing_data === true) {
            $overwrite_this_attachment_attribute = true;
        } else {
            $overwrite_this_attachment_attribute = in_array($this_attachment_attribute_name, $overwrite_existing_data_attachment_attributes_names);
        }

        // which table do we need to update? (title, caption, description => wp_posts, alt-text => wp_postmeta)
        if (in_array($this_attachment_attribute_name, array("title", "caption", "description"))) {
            // which column do we need to update? (title => post_title, caption => post_excerpt, description => post_content)
            switch ($this_attachment_attribute_name) {
                case "title":
                    $this_post_column = "post_title";
                    break;
                case "caption":
                    $this_post_column = "post_excerpt";
                    break;
                case "description":
                    $this_post_column = "post_content";
                    break;
                default:
                    continue 2;
            }

            // skip, if $overwrite_existing_data is false AND the previous value is not empty
            if (!$overwrite_this_attachment_attribute && !empty($attachment_post->$this_post_column)) {
                continue;
            }

            // update the post object
            $attachment_post->$this_post_column = $this_attachment_attribute_value;
            $we_made_changes_to_the_post = true;
        } else if ($this_attachment_attribute_name == "alt-text") {
            // update the postmeta table (mata_key = _wp_attachment_image_alt)
            if (!$overwrite_this_attachment_attribute) {
                // if not empty -> skip
                $existing_attachment_attribute_value = get_post_meta($attachment_post_id, "_wp_attachment_image_alt", true);

                if (!empty($existing_attachment_attribute_value)) {
                    continue;
                }
            }

            update_post_meta($attachment_post_id, "_wp_attachment_image_alt", $this_attachment_attribute_value);
        }
    }

    // only update the post if we made changes
    if ($we_made_changes_to_the_post) {
        wp_update_post($attachment_post);
    }

    return true;
}

// =========================================================================================== \\

/**
 * Function to excavate posts, pages, products etc. with missing metadata.
 * Is used by the cronjob "ai4seo_automated_generation_cron_job" to find posts and pages that are missing metadata
 * @param bool $debug if true, debug information will be printed
 * @return bool
 */
function ai4seo_excavate_post_entries_with_missing_metadata(bool $debug = false): bool {
    global $wpdb;

    // check the current credits balance, compare it to AI4SEO_MIN_CREDITS_BALANCE and if it's lower, return true
    if (ai4seo_robhub_api()->get_credits_balance() < AI4SEO_MIN_CREDITS_BALANCE) {
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("skipped, because of low credits balance", true)) . "<</pre>";
        }

        return true;
    }

    $supported_post_types = ai4seo_get_supported_post_types();

    // find out if the automation is enabled
    $enabled_generation_post_types = array();

    foreach ($supported_post_types as $this_post_type) {
        if (ai4seo_is_automated_generation_enabled($this_post_type)) {
            $enabled_generation_post_types[] = $this_post_type;
        }
    }

    // if automation is completely disabled -> return
    if (!$enabled_generation_post_types) {
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("No automation enabled", true)) . "<</pre>";
        }

        return true;
    }

    // check the number of already pending posts
    $pending_metadata_post_ids = ai4seo_get_post_ids_from_option(AI4SEO_PENDING_METADATA_POST_IDS);

    if ($pending_metadata_post_ids && count($pending_metadata_post_ids) >= 2) {
        // skip here because we already have two posts pending, that are going to be processed
        // better keep the amount of post ids low if the user suddenly stops the automation
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("Already >= 2 posts pending -> skip", true)) . "<</pre>";
        }

        return true;
    }

    // only these posts we have to look for
    $missing_metadata_post_ids = ai4seo_get_post_ids_from_option(AI4SEO_MISSING_METADATA_POST_IDS);

    if (!$missing_metadata_post_ids) {
        // skip here because we don't have any posts or pages
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("No posts found", true)) . "<</pre>";
        }

        return true;
    }

    $missing_metadata_post_ids = array_unique($missing_metadata_post_ids);

    $only_this_post_ids_term_string = implode(", ", $missing_metadata_post_ids);

    // additionally, these posts we have to ignore
    $failed_metadata_post_ids = ai4seo_get_post_ids_from_option(AI4SEO_FAILED_METADATA_POST_IDS);
    $processing_metadata_post_ids = ai4seo_get_post_ids_from_option(AI4SEO_PROCESSING_METADATA_POST_IDS);

    // exclude this post_ids (merge $processing_post_ids and $already_filled_post_ids and $failed_to_fill_post_ids)
    $exclude_this_post_ids = array_merge($pending_metadata_post_ids, $processing_metadata_post_ids, $failed_metadata_post_ids);

    // check if all values are numeric
    foreach ($exclude_this_post_ids as &$this_excluded_post_id) {
        $this_excluded_post_id = absint($this_excluded_post_id);
    }

    // make sure that $exclude_this_post_ids is an array and not empty (otherwise the query will fail)
    if (!$exclude_this_post_ids) {
        $exclude_this_post_ids = array(0);
    }

    $exclude_this_post_ids = array_unique($exclude_this_post_ids);

    // generate IN-term for query for the post_type based on the automation settings
    $post_type_sql_term = $enabled_generation_post_types;

    // escape each element
    foreach ($post_type_sql_term as $key => $value) {
        $post_type_sql_term[$key] = esc_sql($value);
    }

    $escaped_post_type_term_string = implode("', '", $post_type_sql_term);
    $not_this_ids_term_string = implode(", ", $exclude_this_post_ids);

    // look for two entries in wp_posts that are not in the option "ai4seo_already_filled_metadata_post_ids" and match the post_type
    $query = "SELECT ID FROM " . esc_sql($wpdb->prefix) . "posts WHERE post_type IN ('" . $escaped_post_type_term_string . "') AND ID IN (" . esc_sql($only_this_post_ids_term_string) . ") AND ID NOT IN (" . esc_sql($not_this_ids_term_string) . ") AND post_status IN ('publish', 'future', 'private', 'pending') ORDER BY RAND() LIMIT 2";

    $new_pending_post_ids = $wpdb->get_col($query);

    if (!$new_pending_post_ids) {
        // skip here because we don't have any posts or pages
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("No posts found", true)) . "<</pre>";
        }
        return true;
    }

    // add the new post ids to the option "ai4seo_processing_metadata_post_ids"
    ai4seo_add_post_ids_to_option(AI4SEO_PENDING_METADATA_POST_IDS, $new_pending_post_ids);

    if ($debug) {
        echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("New pending post(s): " . esc_textarea(implode(", ", $new_pending_post_ids)), true)) . "<</pre>";
    }

    return true;
}

// =========================================================================================== \\

/**
 * Function to excavate attachments with missing attributes.
 * Is used by the cronjob "ai4seo_automated_generation_cron_job"
 * @param bool $debug if true, debug information will be printed
 * @return bool
 */
function ai4seo_excavate_attachments_with_missing_attributes(bool $debug = false): bool {
    global $wpdb;
    global $ai4seo_allowed_attachment_mime_types;

    // check the current credits balance, compare it to AI4SEO_MIN_CREDITS_BALANCE and if it's lower, return true
    if (ai4seo_robhub_api()->get_credits_balance() < AI4SEO_MIN_CREDITS_BALANCE) {
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("skipped, because of low credits balance", true)) . "<</pre>";
        }
        return true;
    }

    // is automation disabled, skip
    if (!ai4seo_is_automated_generation_enabled("attachment")) {
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("No automation enabled", true)) . "<</pre>";
        }
        return true;
    }

    // check the number of already planned posts
    $pending_attributes_attachment_post_ids = ai4seo_get_post_ids_from_option(AI4SEO_PENDING_ATTACHMENT_ATTRIBUTES_POST_IDS);

    if ($pending_attributes_attachment_post_ids && count($pending_attributes_attachment_post_ids) >= 2) {
        // skip here because we already have two attachment posts that are going to be processed
        // better keep the amount of post ids low if the user suddenly stops the automation
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("Already >= 2 media posts to generate -> skip", true)) . "<</pre>";
        }

        return true;
    }

    // only consider this attachment posts with missing post ids
    $missing_attachment_attributes_post_ids = ai4seo_get_post_ids_from_option(AI4SEO_MISSING_ATTACHMENT_ATTRIBUTES_POST_IDS);

    if (!$missing_attachment_attributes_post_ids) {
        // skip here because we don't have any attachment posts with missing attributes
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("No media posts found", true)) . "<</pre>";
        }

        return true;
    }

    $missing_attachment_attributes_post_ids = array_unique($missing_attachment_attributes_post_ids);

    $only_this_post_ids_term_string = implode(", ", $missing_attachment_attributes_post_ids);

    // additionally, exclude these attachment posts
    $processing_attachment_attributes_post_ids = ai4seo_get_post_ids_from_option(AI4SEO_PROCESSING_ATTACHMENT_ATTRIBUTES_POST_IDS);
    $failed_attachment_attributes_post_ids = ai4seo_get_post_ids_from_option(AI4SEO_FAILED_ATTACHMENT_ATTRIBUTES_POST_IDS);

    // exclude this post_ids (merge $processing_post_ids and $already_filled_post_ids and $failed_to_fill_post_ids)
    $exclude_this_attachment_post_ids = array_merge($pending_attributes_attachment_post_ids, $processing_attachment_attributes_post_ids, $failed_attachment_attributes_post_ids);

    // check if all values are numeric
    foreach ($exclude_this_attachment_post_ids as &$this_excluded_attachment_post_id) {
        $this_excluded_attachment_post_id = absint($this_excluded_attachment_post_id);
    }

    // make sure that $exclude_this_post_ids is an array and not empty (otherwise the query will fail)
    if (!$exclude_this_attachment_post_ids) {
        $exclude_this_attachment_post_ids = array(0);
    }

    $exclude_this_attachment_post_ids = array_unique($exclude_this_attachment_post_ids);

    $not_this_ids_term_string = implode(", ", $exclude_this_attachment_post_ids);

    // perform esc_sql on every entry of $ai4seo_supported_attachment_mime_types
    $only_this_mime_types_sql_terms = array();

    foreach ($ai4seo_allowed_attachment_mime_types AS $this_mime_type) {
        $only_this_mime_types_sql_terms[] = esc_sql($this_mime_type);
    }

    $only_this_mime_types_term_string = implode("', '", $only_this_mime_types_sql_terms);

    // look for two entries in wp_posts that are not in the option "ai4seo_already_filled_attachment_attributes_post_ids" and match the post_type
    $query = "SELECT ID FROM " . esc_sql($wpdb->prefix) . "posts WHERE post_type = 'attachment' AND ID IN (" . esc_sql($only_this_post_ids_term_string) . ") AND ID NOT IN (" . esc_sql($not_this_ids_term_string) . ") AND post_status IN ('publish', 'future', 'private', 'pending', 'inherit') AND post_mime_type IN ('{$only_this_mime_types_term_string}') ORDER BY RAND() LIMIT 2";

    $new_pending_attachment_post_ids = $wpdb->get_col($query);

    if (!$new_pending_attachment_post_ids) {
        // skip here because we don't have any posts or pages
        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("No new media found", true)) . "<</pre>";
        }
        return true;
    }

    // add the new attachment post ids to be processed
    ai4seo_add_post_ids_to_option(AI4SEO_PENDING_ATTACHMENT_ATTRIBUTES_POST_IDS, $new_pending_attachment_post_ids);

    if ($debug) {
        echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("Added pending media: " . (implode(", ", $new_pending_attachment_post_ids)), true)) . "<</pre>";
    }

    return true;
}

// =========================================================================================== \\

/**
 * Function to analyse the performance of the plugin like getting the amount of content "AI for SEO" could
 * generate metadata for
 * @param bool $debug if true, debug information will be printed
 * @param int|bool $current_credits_balance this users current credits balance. if false, the current credits balance will be fetched
 * @return bool true on success, false on failure
 */
function ai4seo_analyze_plugin_performance(bool $debug = false, $refresh_all_posts_seo_coverage = true): bool {
    if ($refresh_all_posts_seo_coverage) {
        ai4seo_refresh_all_posts_seo_coverage($debug);
    }

    ai4seo_refresh_all_posts_generation_status_summary($debug);
    return true;
}

// =========================================================================================== \\

/**
 * Reads all posts and decides the corresponding post ids options
 * @param bool $debug if true, debug information will be printed
 * @return void
 */
function ai4seo_refresh_all_posts_seo_coverage(bool $debug = false) {
    global $wpdb;
    global $ai4seo_allowed_attachment_mime_types;


    // === PREPARE ================================================================================= \\

    $coverage_based_post_ids = array();

    foreach (AI4SEO_SEO_COVERAGE_POST_ID_OPTIONS AS $this_option_name) {
        $coverage_based_post_ids[$this_option_name] = array();
    }


    // === GENERATED POST IDS ================================================================================= \\

    $query = "SELECT post_id FROM " . esc_sql($wpdb->postmeta) . " WHERE meta_key = 'ai4seo_generated_data'";
    $generated_data_post_ids = $wpdb->get_col($query);

    if ($debug) {
        echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("Generated data post ids: " . esc_html(implode(", ", $generated_data_post_ids)), true)) . "<</pre>";
    }

    // === METADATA ================================================================================= \\

    // PREPARE POST TYPES
    $supported_post_types = ai4seo_get_supported_post_types();

    // generate IN-term for query for the post_type based on the automation settings
    // escape each element
    $post_type_sql_terms = array();
    foreach ($supported_post_types AS $this_supported_post_type) {
        $post_type_sql_terms[] = esc_sql($this_supported_post_type);
    }

    $escaped_post_type_term_string = implode("', '", $post_type_sql_terms);

    // READ
    $query = "SELECT ID FROM " . esc_sql($wpdb->prefix) . "posts WHERE post_type IN ('" . $escaped_post_type_term_string . "') AND post_status IN ('publish', 'future', 'private', 'pending')";

    $all_metadata_post_ids = $wpdb->get_col($query);

    // sanitize to int
    $all_metadata_post_ids = ai4seo_deep_sanitize($all_metadata_post_ids, "absint");

    if ($all_metadata_post_ids) {
        // read the percentage of active metadata by post ids
        $percentage_of_active_metadata_by_post_ids = ai4seo_read_percentage_of_active_metadata_by_post_ids($all_metadata_post_ids);

        // ADD ENTRIES TO THE GENERATION STATUS POST IDS
        foreach ($percentage_of_active_metadata_by_post_ids AS $this_post_id => $percentage) {
            if ($percentage == 100) {
                $coverage_based_post_ids[AI4SEO_FULLY_COVERED_METADATA_POST_IDS][] = (int) $this_post_id;
            } else {
                $coverage_based_post_ids[AI4SEO_MISSING_METADATA_POST_IDS][] = (int) $this_post_id;
            }

            if (in_array($this_post_id, $generated_data_post_ids)) {
                $coverage_based_post_ids[AI4SEO_GENERATED_METADATA_POST_IDS][] = (int) $this_post_id;
            }
        }

        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("Metadata coverage summary: " . esc_html(print_r($percentage_of_active_metadata_by_post_ids, true)), true)) . "<</pre>";
        }
    }


    // === ATTACHMENT ATTRIBUTES ================================================================================= \\

    // PREPARE MIME TYPES
    $only_this_mime_types_sql_terms = array();

    foreach ($ai4seo_allowed_attachment_mime_types AS $this_mime_type) {
        $only_this_mime_types_sql_terms[] = esc_sql($this_mime_type);
    }

    $only_this_mime_types_term_string = implode("', '", $only_this_mime_types_sql_terms);

    // READ
    $query = "SELECT ID FROM " . esc_sql($wpdb->posts) . " WHERE post_type = 'attachment' AND post_status IN ('publish', 'future', 'private', 'pending', 'inherit') AND post_mime_type IN ('{$only_this_mime_types_term_string}')";

    $all_attachment_attributes_post_ids = $wpdb->get_col($query);

    if ($all_attachment_attributes_post_ids) {
        // BUILD ATTACHMENT ATTRIBUTES COVERAGE ARRAY
        $attachment_attributes_coverage = ai4seo_read_and_analyse_attachment_attributes_coverage($all_attachment_attributes_post_ids);
        $num_total_attachment_attributes_fields = ai4seo_get_num_attachment_attributes();
        $attachment_attributes_coverage_summary = ai4seo_get_attachment_attributes_coverage_summary($attachment_attributes_coverage);
        unset($attachment_attributes_coverage);

        // ADD ENTRIES TO THE GENERATION STATUS POST IDS
        foreach ($attachment_attributes_coverage_summary AS $this_post_id => $num_fields_covered) {
            if ($num_fields_covered == $num_total_attachment_attributes_fields) {
                $coverage_based_post_ids[AI4SEO_FULLY_COVERED_ATTACHMENT_ATTRIBUTES_POST_IDS][] = (int) $this_post_id;
            } else {
                $coverage_based_post_ids[AI4SEO_MISSING_ATTACHMENT_ATTRIBUTES_POST_IDS][] = (int) $this_post_id;
            }

            if (in_array($this_post_id, $generated_data_post_ids)) {
                $coverage_based_post_ids[AI4SEO_GENERATED_ATTACHMENT_ATTRIBUTES_POST_IDS][] = (int) $this_post_id;
            }
        }

        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("Media attributes coverage summary: " . esc_html(print_r($attachment_attributes_coverage_summary, true)), true)) . "<</pre>";
        }
    }

    // === SAVE TO OPTIONS ================================================================================= \\

    // save the coverage based post ids
    foreach ($coverage_based_post_ids AS $option_name => $post_ids) {
        $post_ids = array_unique($post_ids);
        $post_ids = wp_json_encode($post_ids);
        update_option($option_name, $post_ids);
    }

    if ($debug) {
        echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("Coverage based post ids: " . esc_html(print_r($coverage_based_post_ids, true)), true)) . "<</pre>";
    }
}

// =========================================================================================== \\

/**
 * Function to refresh the generation status summary of all posts and attachments by using the current state of the post ids options
 * @param $debug bool if true, debug information will be printed
 * @return void
 */
function ai4seo_refresh_all_posts_generation_status_summary(bool $debug = false) {
    global $wpdb;

    // === PREPARE ================================================================================= \\

    $all_posts_generation_status_summary = array();

    foreach (AI4SEO_ALL_POST_ID_OPTIONS AS $this_option_name) {
        $all_posts_generation_status_summary[$this_option_name] = array();
    }

    // POST TYPES
    $supported_post_types = ai4seo_get_supported_post_types();

    // add attachment to the supported post types
    $supported_post_types[] = "attachment";

    // generate IN-term for query for the post_type based on the automation settings
    // escape each element
    $post_type_sql_terms = array();
    foreach ($supported_post_types AS $this_supported_post_type) {
        $post_type_sql_terms[] = esc_sql($this_supported_post_type);
    }

    $escaped_post_type_term_string = implode("', '", $post_type_sql_terms);


    // === GO THROUGH EACH OPTION ================================================================================= \\

    foreach ($all_posts_generation_status_summary AS $this_option_name => &$this_summary_entry) {
        $this_option_post_ids = ai4seo_get_post_ids_from_option($this_option_name);

        if(!$this_option_post_ids) {
            if ($debug) {
                echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("No post ids found for option: " . esc_html($this_option_name), true)) . "<</pre>";
            }
            continue;
        }

        $this_only_this_post_ids_term_string = implode(", ", $this_option_post_ids);

        // build query
        $this_query = "SELECT COUNT(*) AS 'num', post_type FROM " . esc_sql($wpdb->prefix) . "posts WHERE post_type IN ('$escaped_post_type_term_string') AND ID IN (" . esc_sql($this_only_this_post_ids_term_string) . ") GROUP BY post_type";
        $this_num_posts_by_post_type = $wpdb->get_results($this_query, ARRAY_A);

        if (!$this_num_posts_by_post_type) {
            if ($debug) {
                echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("No posts found for option: " . esc_html($this_option_name), true)) . "<</pre>";
            }
            continue;
        }

        foreach ($this_num_posts_by_post_type as $this_row) {
            $this_summary_entry[$this_row["post_type"]] = $this_row["num"];
        }

        if ($debug) {
            echo "<pre>" . esc_html(__FUNCTION__) . " >" . esc_html(print_r("Summary for option: " . esc_html($this_option_name) . ": " . esc_html(print_r($this_summary_entry, true)), true)) . "<</pre>";
        }
    }


    // === SAVE ================================================================================= \\

    update_option("_ai4seo_generation_status_summary", wp_json_encode($all_posts_generation_status_summary));
}


// ___________________________________________________________________________________________ \\
// === META DATA ============================================================================= \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Function to get the summary (amount of posts) of a specific options (generation status)
 * @param $option_name string the name of the option (generation status)
 * @return array the generation status summary entry or false if not found
 */
function ai4seo_get_generation_status_summary_entry(string $option_name): array {
    $generation_status_summary = get_option("_ai4seo_generation_status_summary");

    if (!$generation_status_summary) {
        return array();
    }

    $generation_status_summary = json_decode($generation_status_summary, true);

    if (!isset($generation_status_summary[$option_name])) {
        return array();
    }

    $generation_status_summary = ai4seo_deep_sanitize($generation_status_summary, "absint");

    return $generation_status_summary[$option_name];
}

// =========================================================================================== \\

/**
 * Function to get all missing posts by post type by using the generation status summary-cache
 * @return array the missing posts by post type
 */
function ai4seo_get_all_missing_posts_by_post_type(): array {
    $num_missing_metadata_by_post_type = ai4seo_get_generation_status_summary_entry(AI4SEO_MISSING_METADATA_POST_IDS);
    $num_missing_attachment_attributes = ai4seo_get_generation_status_summary_entry(AI4SEO_MISSING_ATTACHMENT_ATTRIBUTES_POST_IDS);
    return array_merge($num_missing_metadata_by_post_type, $num_missing_attachment_attributes);
}

// =========================================================================================== \\

/**
 * Function to get all fully covered posts by post type by using the generation status summary-cache
 * @return array the fully covered posts by post type
 */
function ai4seo_get_all_fully_covered_posts_by_post_type(): array {
    $num_fully_covered_metadata_by_post_type = ai4seo_get_generation_status_summary_entry(AI4SEO_FULLY_COVERED_METADATA_POST_IDS);
    $num_fully_covered_attachment_attributes = ai4seo_get_generation_status_summary_entry(AI4SEO_FULLY_COVERED_ATTACHMENT_ATTRIBUTES_POST_IDS);
    return array_merge($num_fully_covered_metadata_by_post_type, $num_fully_covered_attachment_attributes);
}

// =========================================================================================== \\

/**
 * Function to get all generated posts by post type by using the generation status summary-cache
 * @return array the generated posts by post type
 */
function ai4seo_get_all_generated_posts_by_post_type(): array {
    $num_generated_metadata_by_post_type = ai4seo_get_generation_status_summary_entry(AI4SEO_GENERATED_METADATA_POST_IDS);
    $num_generated_attachment_attributes = ai4seo_get_generation_status_summary_entry(AI4SEO_GENERATED_ATTACHMENT_ATTRIBUTES_POST_IDS);
    return array_merge($num_generated_metadata_by_post_type, $num_generated_attachment_attributes);
}

// =========================================================================================== \\

/**
 * Function to get all failed posts by post type by using the generation status summary-cache
 * @return array the failed posts by post type
 */
function ai4seo_get_all_failed_posts_by_post_type(): array {
    $num_failed_metadata_by_post_type = ai4seo_get_generation_status_summary_entry(AI4SEO_FAILED_METADATA_POST_IDS);
    $num_failed_attachment_attributes = ai4seo_get_generation_status_summary_entry(AI4SEO_FAILED_ATTACHMENT_ATTRIBUTES_POST_IDS);
    return array_merge($num_failed_metadata_by_post_type, $num_failed_attachment_attributes);
}

// =========================================================================================== \\

/**
 * Function to get all pending posts by post type by using the generation status summary-cache
 * @return array the pending posts by post type
 */
function ai4seo_get_all_pending_posts_by_post_type(): array {
    $num_pending_metadata_by_post_type = ai4seo_get_generation_status_summary_entry(AI4SEO_PENDING_METADATA_POST_IDS);
    $num_pending_attachment_attributes = ai4seo_get_generation_status_summary_entry(AI4SEO_PENDING_ATTACHMENT_ATTRIBUTES_POST_IDS);
    return array_merge($num_pending_metadata_by_post_type, $num_pending_attachment_attributes);
}

// =========================================================================================== \\

/**
 * Function to get all processing posts by post type by using the generation status summary-cache
 * @return array the processing posts by post type
 */
function ai4seo_get_all_processing_posts_by_post_type(): array {
    $num_processing_metadata_by_post_type = ai4seo_get_generation_status_summary_entry(AI4SEO_PROCESSING_METADATA_POST_IDS);
    $num_processing_attachment_attributes = ai4seo_get_generation_status_summary_entry(AI4SEO_PROCESSING_ATTACHMENT_ATTRIBUTES_POST_IDS);
    return array_merge($num_processing_metadata_by_post_type, $num_processing_attachment_attributes);
}

// =========================================================================================== \\

/**
 * Function to get the summary (amount of posts) of a specific options and post type
 * @param $option_name string the name of the option (generation status)
 * @param $post_type string the post type
 * @return int the amount of posts for this specific generation status and post type
 */
function ai4seo_get_num_generation_status_and_post_types_posts(string $option_name, string $post_type): int {
    $generation_status_summary = get_option("_ai4seo_generation_status_summary");

    if (!$generation_status_summary) {
        return 0;
    }

    $generation_status_summary = json_decode($generation_status_summary, true);

    if (!isset($generation_status_summary[$option_name])) {
        return 0;
    }

    if (!isset($generation_status_summary[$option_name][$post_type])) {
        return 0;
    }

    return (int) $generation_status_summary[$option_name][$post_type];
}

// =========================================================================================== \\

/**
 * Function to return a post meta key by the given post id and the name of the metadata field from the
 * $ai4seo_metadata_details array
 * @param $post_id int the post id
 * @param $metadata_identifier string the metadata identifier
 * @return string the post meta key
 */
function ai4seo_generate_postmeta_key_by_metadata_identifier($post_id, $metadata_identifier): string {
    return "_ai4seo_" . $post_id . "_" . $metadata_identifier;
}

// =========================================================================================== \\

/**
 * Function to get the metadata identifier out of a (our plugin's) postmeta key
 * @param $metadata_postmeta_key string the postmeta key
 * @return string the metadata identifier or an empty string if not found
 */
function ai4seo_get_metadata_identifier_by_postmeta_key(string $metadata_postmeta_key) {
    $matches = array();
    preg_match("/^_ai4seo_([0-9]+)_(.*)$/", $metadata_postmeta_key, $matches);

    if (empty($matches[2])) {
        return false;
    }

    return $matches[2];
}

// =========================================================================================== \\

/**
 * Function to read the post meta from specific posts by the given post ids
 * @param $post_ids array of post ids (all int)
 * @return array
 */
function ai4seo_read_our_plugins_metadata_by_post_ids(array $post_ids): array {
    global $wpdb;

    // make sure all entries of post_ids are numeric
    foreach ($post_ids as $key => $post_id) {
        if (!is_numeric($post_id)) {
            return array();
        }
    }

    // Make sure that all parameters are not empty
    if (empty($post_ids)) {
        return array();
    }

    // Make sure that all parameters are of the correct type
    $regexp = "^_ai4seo_[0-9]+_.*$";
    $post_ids_string = implode(",", $post_ids);
    $post_ids_string = sanitize_text_field($post_ids_string);
    $postmeta_table = $wpdb->postmeta;
    $postmeta_table = sanitize_text_field($postmeta_table);
    $query = "SELECT * FROM " . esc_sql($postmeta_table) . " WHERE meta_key REGEXP '" . esc_sql($regexp) . "' AND post_id IN (" . esc_sql($post_ids_string) . ")";

    // read directly from database by searching for entries in the postmeta table
    $query_results = $wpdb->get_results( $query, ARRAY_A );

    // rearrange the array to have post_id as 2d key and meta_key as 1d key and meta_value as the value
    // also remove entries with emtpy meta_value
    $reordered_results = array();

    foreach ($query_results as $query_result) {
        $this_post_id = $query_result["post_id"];
        $this_metadata_identifier = ai4seo_get_metadata_identifier_by_postmeta_key($query_result["meta_key"]);

        if (!$this_metadata_identifier) {
            continue;
        }

        $reordered_results[$this_post_id][$this_metadata_identifier] = strval($query_result["meta_value"]);
    }

    return $reordered_results;
}

// =========================================================================================== \\

/**
 * Function to read the post's metadata for a specific third party plugin from specific posts by the given post ids
 * @param $post_ids array of post ids (all int)
 * @return array the metadata by post-ids, using metadata-identifier keys
 */
function ai4seo_read_third_party_seo_plugin_metadata_by_post_ids($third_party_plugin_name, array $post_ids): array {
    global $wpdb;

    // make sure all entries of post_ids are numeric
    foreach ($post_ids as $key => $post_id) {
        if (!is_numeric($post_id)) {
            return array();
        }
    }

    // Make sure that all parameters are not empty
    if (empty($post_ids)) {
        return array();
    }

    // workaround for Slim SEO
    if ($third_party_plugin_name == AI4SEO_THIRD_PARTY_PLUGIN_SLIM_SEO) {
        return ai4seo_read_slim_seo_metadata_by_post_ids($post_ids);
    }

    // workaround for Blog2Social
    if ($third_party_plugin_name == AI4SEO_THIRD_PARTY_PLUGIN_BLOG2SOCIAL) {
        return ai4seo_read_blog2social_metadata_by_post_ids($post_ids);
    }

    // workaround for Squirrly SEO
    if ($third_party_plugin_name == AI4SEO_THIRD_PARTY_PLUGIN_SQUIRRLY_SEO) {
        return ai4seo_read_squirrly_seo_metadata_by_post_ids($post_ids);
    }

    // workaround for All in One SEO
    if ($third_party_plugin_name == AI4SEO_THIRD_PARTY_PLUGIN_ALL_IN_ONE_SEO) {
        return ai4seo_read_all_in_one_seo_metadata_by_post_ids($post_ids);
    }

    // Make sure that all parameters are of the correct type
    $metadata_postmeta_keys = AI4SEO_THIRD_PARTY_SEO_PLUGIN_DETAILS[$third_party_plugin_name]["metadata-postmeta-keys"] ?? array();

    if (!$metadata_postmeta_keys) {
        return array();
    }

    $post_ids_string = implode(",", $post_ids);
    $post_ids_string = sanitize_text_field($post_ids_string);
    $postmeta_table = $wpdb->postmeta;
    $postmeta_table = sanitize_text_field($postmeta_table);

    $metadata_postmeta_keys_string = implode(",", $metadata_postmeta_keys);
    $metadata_postmeta_keys_string = sanitize_text_field($metadata_postmeta_keys_string);

    // wrap all $meta_keys_string entries in single quotes
    $metadata_postmeta_keys_string = "'" . str_replace(",", "','", esc_sql($metadata_postmeta_keys_string)) . "'";

    $query = "SELECT * FROM " . esc_sql($postmeta_table) . " WHERE meta_key IN (" . $metadata_postmeta_keys_string . ") AND post_id IN (" . esc_sql($post_ids_string) . ")";

    // read directly from database by searching for entries in the postmeta table
    $query_results = $wpdb->get_results( $query, ARRAY_A );

    if (!$query_results) {
        return array();
    }

    // reorder results, to make post_id the 2d key, then the meta_keys the 1d key and meta_value the value
    // also skip entries with empty meta_value
    $third_party_seo_plugins_metadata = array();

    foreach ($query_results as $query_result) {
        $this_post_id = $query_result["post_id"];

        // find metadata identifier
        $this_metadata_identifier = array_search($query_result["meta_key"], $metadata_postmeta_keys);

        if (!$this_metadata_identifier) {
            continue;
        }

        $third_party_seo_plugins_metadata[$this_post_id][$this_metadata_identifier] = strval($query_result["meta_value"]);
    }

    return $third_party_seo_plugins_metadata;
}

// =========================================================================================== \\

/**
 * Function to read the post's metadata for the Slim SEO plugin from specific posts by the given post ids
 * @param $post_ids array of post ids (all int)
 * @return array the metadata by post-ids, using metadata-identifier keys
 */
function ai4seo_read_slim_seo_metadata_by_post_ids(array $post_ids): array {
    // check postmeta "slim_seo". It's serialized with keys "title" and "description", nothing else
    $metadata_identifier_mapping = array(
        "meta-title" => "title",
        "meta-description" => "description",
    );

    // read postmeta entries
    global $wpdb;

    $post_ids_string = implode(",", $post_ids);
    $post_ids_string = sanitize_text_field($post_ids_string);

    $query = "SELECT * FROM " . esc_sql($wpdb->postmeta) . " WHERE meta_key = 'slim_seo' AND post_id IN (" . esc_sql($post_ids_string) . ")";

    $query_results = $wpdb->get_results( $query, ARRAY_A );

    if (!$query_results) {
        return array();
    }

    // reorder results, to make post_id the 2d key, then the meta_keys the 1d key and meta_value the value
    // also skip entries with empty meta_value
    $third_party_plugins_metadata = array();

    foreach ($query_results as $query_result) {
        $this_post_id = (int) $query_result["post_id"];
        $this_metadata = maybe_unserialize($query_result["meta_value"]);

        if (!$this_metadata) {
            continue;
        }

        foreach ($metadata_identifier_mapping as $this_metadata_identifier => $this_third_party_plugin_key) {
            $third_party_plugins_metadata[$this_post_id][$this_metadata_identifier] = $this_metadata[$this_third_party_plugin_key] ?? "";
        }
    }

    return $third_party_plugins_metadata;
}

// =========================================================================================== \\

/**
 * Function to read the post's metadata for the Blog2Social plugin from specific posts by the given post ids
 * @param $post_ids array of post ids (all int)
 * @return array the metadata by post-ids, using metadata-identifier keys
 */
function ai4seo_read_blog2social_metadata_by_post_ids(array $post_ids): array {
    // check postmeta "_b2s_post_meta". It's serialized with keys "og_title", "og_desc", "card_title" and "card_desc"
    $metadata_identifier_mapping = array(
        "facebook-title" => "og_title",
        "facebook-description" => "og_desc",
        "twitter-title" => "card_title",
        "twitter-description" => "card_desc",
    );

    // read postmeta entries
    global $wpdb;

    $post_ids_string = implode(",", $post_ids);
    $post_ids_string = sanitize_text_field($post_ids_string);

    $query = "SELECT * FROM " . esc_sql($wpdb->postmeta) . " WHERE meta_key = '_b2s_post_meta' AND post_id IN (" . esc_sql($post_ids_string) . ")";

    $query_results = $wpdb->get_results( $query, ARRAY_A );

    if (!$query_results) {
        return array();
    }

    // reorder results, to make post_id the 2d key, then the meta_keys the 1d key and meta_value the value
    // also skip entries with empty meta_value
    $third_party_plugins_metadata = array();

    foreach ($query_results as $query_result) {
        $this_post_id = (int) $query_result["post_id"];
        $this_metadata = maybe_unserialize($query_result["meta_value"]);

        if (!$this_metadata) {
            continue;
        }

        foreach ($metadata_identifier_mapping as $this_metadata_identifier => $this_third_party_plugin_key) {
            $third_party_plugins_metadata[$this_post_id][$this_metadata_identifier] = $this_metadata[$this_third_party_plugin_key] ?? "";
        }
    }

    return $third_party_plugins_metadata;
}

// =========================================================================================== \\

/**
 * Function to read the post's metadata for the Squirrly SEO plugin from specific posts by the given post ids
 * @param $post_ids array of post ids (all int)
 * @return array the metadata by post-ids, using metadata-identifier keys
 */
function ai4seo_read_squirrly_seo_metadata_by_post_ids(array $post_ids): array {
    // check table "wp_qss" -> column "seo". It's serialized with keys "title", "description", "og_title", "og_description", "tw_title", "tw_description"
    $metadata_identifier_mapping = array(
        "meta-title" => "title",
        "meta-description" => "description",
        "facebook-title" => "og_title",
        "facebook-description" => "og_description",
        "twitter-title" => "tw_title",
        "twitter-description" => "tw_description",
    );

    // read column "seo" in table "wp_qss"
    global $wpdb;

    // Ensure post IDs are properly escaped and form the pattern for LIKE queries
    $patterns = array_map(function($post_id) {
        $post_id = intval($post_id);
        return '%s:2:"ID";i:' . esc_sql($post_id) . ';%';
    }, $post_ids);

    // Implode all patterns to use them in a single SQL query with multiple LIKE clauses
    $like_clauses = implode(" OR post LIKE ", array_fill(0, count($patterns), '%s'));

    // Prepare the query to get SEO data for all post IDs in one go
    $query = "
        SELECT post, seo
        FROM " . esc_sql($wpdb->prefix . "qss") . "
        WHERE post LIKE " . $like_clauses;

    // Prepare the arguments for the query
    $args = $patterns;

    // Execute the query
    $results = $wpdb->get_results($wpdb->prepare($query, ...$args), OBJECT);

    // Initialize the values array
    $all_squirrly_values = array();

    // Loop through the results and map them to the post IDs
    foreach ($results as $result) {
        $post_id = false;

        // Check if the post data contains a serialized "ID" field
        if (preg_match('/s:2:"ID";i:(\d+);/', $result->post, $matches)) {
            $post_id = intval($matches[1]);
        }

        if ($post_id) {
            // Deserialize the SEO value
            $this_posts_current_squirrly_values = maybe_unserialize($result->seo);
            if (is_string($this_posts_current_squirrly_values)) {
                $this_posts_current_squirrly_values = unserialize($this_posts_current_squirrly_values);
            }

            // Store the result for the post ID
            if (is_array($this_posts_current_squirrly_values) && !empty($this_posts_current_squirrly_values)) {
                $all_squirrly_values[$post_id] = $this_posts_current_squirrly_values;
            } else {
                $all_squirrly_values[$post_id] = array();
            }
        }
    }

    // reorder results, to make post_id the 2d key, then the meta_keys the 1d key and meta_value the value
    // also skip entries with empty meta_value
    $third_party_seo_plugins_metadata = array();

    foreach ($all_squirrly_values as $post_id => $this_metadata) {
        foreach ($metadata_identifier_mapping as $this_metadata_identifier => $this_squirrly_seo_key) {
            $third_party_seo_plugins_metadata[$post_id][$this_metadata_identifier] = $this_metadata[$this_squirrly_seo_key] ?? "";
        }
    }

    return $third_party_seo_plugins_metadata;
}

// =========================================================================================== \\

/**
 * Function to read the post's metadata for the All in One SEO plugin from specific posts by the given post ids
 * @param $post_ids array of post ids (all int)
 * @return array the metadata by post-ids, using metadata-identifier keys
 */
function ai4seo_read_all_in_one_seo_metadata_by_post_ids($post_ids): array {
    // check table "wp_aioseo_posts" for the post id. Columns are "title", "description", "og_title", "og_description", "twitter_title", "twitter_description"
    $metadata_identifier_mapping = array(
        "meta-title" => "title",
        "meta-description" => "description",
        "facebook-title" => "og_title",
        "facebook-description" => "og_description",
        "twitter-title" => "twitter_title",
        "twitter-description" => "twitter_description",
    );

    $post_ids = ai4seo_deep_sanitize($post_ids, "absint");

    // read entries
    global $wpdb;

    $aioseo_table = $wpdb->prefix . "aioseo_posts";

    $sql = "SELECT * FROM " . esc_sql($aioseo_table) . " WHERE post_id IN (" . esc_sql(implode(",", $post_ids)) . ")";

    $results = $wpdb->get_results($sql, ARRAY_A);

    if (!$results) {
        return array();
    }

    // reorder results, to make post_id the 2d key, then the meta_keys the 1d key and meta_value the value
    // also skip entries with empty meta_value
    $third_party_seo_plugins_metadata = array();

    foreach ($results as $result) {
        $this_post_id = (int) $result["post_id"];

        foreach ($metadata_identifier_mapping as $this_metadata_identifier => $this_aioseo_key) {
            $third_party_seo_plugins_metadata[$this_post_id][$this_metadata_identifier] = $result[$this_aioseo_key] ?? "";
        }
    }

    return $third_party_seo_plugins_metadata;
}

// =========================================================================================== \\

/**
 * Returns the number of metadata fields
 * @return int the number of metadata fields
 */
function ai4seo_get_num_metadata_fields(): int {
    global $ai4seo_metadata_details;
    return $ai4seo_metadata_details ? count($ai4seo_metadata_details) : 0;
}

// =========================================================================================== \\

/**
 * Function to read all the metadata, regardless of the source, for a specific post by the given post id
 * @param $post_ids array of post ids
 * @param $consider_third_party_seo_plugin_metadata bool if true, the own plugin's metadata will be preferred
 * @return array the post meta coverage by post ids
 */
function ai4seo_read_active_metadata_values_by_post_ids(array $post_ids, bool $consider_third_party_seo_plugin_metadata = true): array {
    global $ai4seo_metadata_details;

    // make sure post_ids is not empty
    if (empty($post_ids)) {
        return array();
    }

    // make sure all entries of post_ids are numeric
    foreach ($post_ids as $key => $post_id) {
        if (!is_numeric($post_id)) {
            error_log("AI4SEO: ai4seo_read_active_metadata_values_by_post_ids: post_id is not numeric");
            return array();
        }
    }

    $all_metadata = array();

    // 1. read our own plugin's metadata
    $our_plugins_metadata_by_post_ids = ai4seo_read_our_plugins_metadata_by_post_ids($post_ids);

    foreach ($post_ids AS $this_key => $this_post_id) {
        $this_posts_got_missing_metadata = false;

        foreach ($ai4seo_metadata_details as $this_metadata_identifier => $this_metadata_details) {
            $all_metadata[$this_post_id][$this_metadata_identifier] = $our_plugins_metadata_by_post_ids[$this_post_id][$this_metadata_identifier] ?? "";

            // still empty -> mark as missing
            if (empty($all_metadata[$this_post_id][$this_metadata_identifier])) {
                $this_posts_got_missing_metadata = true;
            }
        }

        // if we have every metadata field filled, remove the post id from the array
        if (!$this_posts_got_missing_metadata) {
            unset($post_ids[$this_key]);
        }
    }

    // should we consider third party seo plugins?
    if (!$consider_third_party_seo_plugin_metadata) {
        return $all_metadata;
    }

    // all posts are filled with our own metadata? return the metadata here
    if (count($post_ids) == 0) {
        return $all_metadata;
    }

    // if not, we...

    // 2. check third party seo plugins
    $active_third_party_seo_plugin_details = ai4seo_get_active_third_party_seo_plugin_details();

    foreach ($active_third_party_seo_plugin_details AS $this_third_party_seo_plugin_identifier => $this_third_party_seo_plugin_details) {
        $this_third_plugins_plugins_metadata_by_post_ids = ai4seo_read_third_party_seo_plugin_metadata_by_post_ids($this_third_party_seo_plugin_identifier, $post_ids);

        if (!$this_third_plugins_plugins_metadata_by_post_ids) {
            continue;
        }

        foreach ($post_ids AS $this_key => $this_post_id) {
            $this_posts_got_missing_metadata = false;

            foreach ($ai4seo_metadata_details as $this_metadata_identifier => $this_metadata_details) {
                // skip if we already have the meta value from our own plugin (or any other third party plugin)
                if ($all_metadata[$this_post_id][$this_metadata_identifier]) {
                    continue;
                }

                $all_metadata[$this_post_id][$this_metadata_identifier] = $this_third_plugins_plugins_metadata_by_post_ids[$this_post_id][$this_metadata_identifier] ?? "";

                // still empty -> mark as missing
                if (empty($all_metadata[$this_post_id][$this_metadata_identifier])) {
                    $this_posts_got_missing_metadata = true;
                }
            }

            // if we have every metadata field filled, remove the post id from the array
            if (!$this_posts_got_missing_metadata) {
                unset($post_ids[$this_key]);
            }
        }

        // all posts are filled with our own metadata? return the metadata here
        if (count($post_ids) == 0) {
            return $all_metadata;
        }
    }

    return $all_metadata;
}

// =========================================================================================== \\

/**
 * Function to return the amount of active metadata per post id
 * @param $post_ids array of post ids
 * @return array the amount of active metadata by post ids
 */
function ai4seo_read_num_active_metadata_by_post_ids(array $post_ids): array {
    global $ai4seo_metadata_details;

    $all_metadata = ai4seo_read_active_metadata_values_by_post_ids($post_ids);

    if (!$all_metadata) {
        return array();
    }

    // generate a summary of the post meta coverage array
    $num_active_metadata_by_post_ids = array();

    foreach ($all_metadata as $post_id => $this_metadata_entry) {
        $num_active_metadata_by_post_ids[$post_id] = 0;

        foreach ($ai4seo_metadata_details AS $this_metadata_identifier => $this_metadata_details) {
            if (isset($this_metadata_entry[$this_metadata_identifier]) && $this_metadata_entry[$this_metadata_identifier]) {
                $num_active_metadata_by_post_ids[$post_id]++;
            }
        }
    }

    return $num_active_metadata_by_post_ids;
}

// =========================================================================================== \\

/**
 * Function to return the percentage of active metadata per post id
 * @param $post_ids array of post ids
 * @param $round_precision int the precision to round the percentage to
 * @return array the amount of active metadata by post ids
 */
function ai4seo_read_percentage_of_active_metadata_by_post_ids(array $post_ids, int $round_precision = 0): array {
    $num_active_metadata_by_post_ids = ai4seo_read_num_active_metadata_by_post_ids($post_ids);
    $num_total_metadata_fields = ai4seo_get_num_metadata_fields();

    $percentage_of_active_metadata_by_post_ids = array();

    foreach ($num_active_metadata_by_post_ids as $this_post_id => $this_num_active_metadata) {
        $percentage_of_active_metadata_by_post_ids[$this_post_id] = round(($this_num_active_metadata / $num_total_metadata_fields) * 100, $round_precision);
    }

    return $percentage_of_active_metadata_by_post_ids;
}

// =========================================================================================== \\

/**
 * Refreshes the metadata coverage for the given post by putting the post id into the corresponding option
 * @param $post_id int The post id to refresh the metadata coverage for
 * @param null $post WP_Post|null The post object to refresh the metadata coverage for
 * @return void
 */
function ai4seo_refresh_one_posts_metadata_coverage_status(int $post_id, $post = null) {
    if (!is_numeric($post_id)) {
        return;
    }

    // remove post id if it's not a valid post
    if (!ai4seo_is_post_a_valid_content_post($post_id, $post)) {
        ai4seo_remove_post_ids_from_all_options($post_id);
        return;
    }

    // consider which option to put the post id into
    if (ai4seo_read_is_active_metadata_fully_covered($post_id)) {
        ai4seo_add_post_ids_to_option(AI4SEO_FULLY_COVERED_METADATA_POST_IDS, $post_id);
    } else {
        ai4seo_add_post_ids_to_option(AI4SEO_MISSING_METADATA_POST_IDS, $post_id);
    }

    // check if the post has generated data
    if (ai4seo_post_has_generated_data($post_id)) {
        ai4seo_add_post_ids_to_option(AI4SEO_GENERATED_METADATA_POST_IDS, $post_id);
    }
}

// =========================================================================================== \\

/**
 * Function to check if this post is a valid content post to be considered by our plugin
 * @param int $post_id The post id to check
 * @param $post WP_Post|null
 * @return bool Whether the post is a valid content post
 */
function ai4seo_is_post_a_valid_content_post(int $post_id, WP_Post $post = null): bool {
    if (!is_numeric($post_id)) {
        return false;
    }

    // read post
    if ($post === null) {
        $post = get_post($post_id);
    }

    // check if the post could be read
    if (!$post || is_wp_error($post) || !isset($post->post_type)) {
        return false;
    }

    // supported post types
    $supported_post_types = ai4seo_get_supported_post_types();

    // check if the post is supported
    if (!in_array($post->post_type, $supported_post_types)) {
        return false;
    }

    // check post status
    if (!in_array($post->post_status, array("publish", "future", "private", "pending"))) {
        return false;
    }

    return true;
}

// =========================================================================================== \\

/**
 * Checks if the metadata for a given post is fully covered
 * @param $post_id int The post id to check the metadata coverage for
 * @return bool Whether the metadata for a given post is fully covered
 */
function ai4seo_read_is_active_metadata_fully_covered(int $post_id): bool {
    $percentage_of_active_metadata_by_post_ids = ai4seo_read_percentage_of_active_metadata_by_post_ids(array($post_id));
    return (($percentage_of_active_metadata_by_post_ids[$post_id] ?? 0) == 100);
}

// =========================================================================================== \\

/**
 * Removes all failed to fill post ids for all or a specific post type. It's recommended to run
 * ai4seo_refresh_all_posts_generation_status_summary() after this function
 * @param string $post_type The post type to remove the failed to fill post ids for
 * @return void
 */
function ai4seo_remove_all_post_types_failed_metadata_generation_post_ids(string $post_type) {
    global $wpdb;

    $post_type = sanitize_text_field($post_type);

    // read all ids from ai4seo_failed_to_fill_metadata_post_ids and check which of them are of the given post_type
    $failed_post_ids = ai4seo_get_post_ids_from_option(AI4SEO_FAILED_METADATA_POST_IDS);

    // no failed posts? skip here
    if (!$failed_post_ids) {
        return;
    }

    $failed_post_ids_sql_term = implode(", ", $failed_post_ids);

    // nail down the post_type
    $query = "SELECT ID FROM " . esc_sql($wpdb->prefix) . "posts WHERE post_type = '" . esc_sql($post_type) . "' AND ID IN (" . esc_sql($failed_post_ids_sql_term) . ")";

    $failed_post_ids_of_post_type = $wpdb->get_col($query);

    if (!$failed_post_ids_of_post_type) {
        return;
    }

    // remove all post_ids of the given post_type from AI4SEO_FAILED_METADATA_POST_IDS
    ai4seo_remove_post_ids_from_option(AI4SEO_FAILED_METADATA_POST_IDS, $failed_post_ids_of_post_type);
}

// =========================================================================================== \\

/**
 * Reads the generated data for a given post, if it exists
 * @param $post_id int the post id
 * @return array
 */
function ai4seo_read_generated_data_from_post_meta(int $post_id): array {
    // reading in post meta, looking for the meta_key AI4SEO_POST_META_GENERATED_DATA_META_KEY
    $generate_data_json_string = get_post_meta($post_id, AI4SEO_POST_META_GENERATED_DATA_META_KEY, true);

    if (!$generate_data_json_string) {
        return array();
    }

    $generate_data = json_decode($generate_data_json_string, true);

    if (!$generate_data) {
        return array();
    }

    // sanitize all fields and then return
    return ai4seo_deep_sanitize($generate_data);
}

// =========================================================================================== \\

/**
 * Function to save the generated data for a given post
 * @param $post_id int the post id
 * @param $generated_data array the generated data
 * @return bool
 */
function ai4seo_save_generated_data_to_postmeta(int $post_id, array $generated_data): bool {
    // encode the data
    $generated_data_json_string = wp_json_encode($generated_data, JSON_UNESCAPED_UNICODE);

    // save the data
    return update_post_meta($post_id, AI4SEO_POST_META_GENERATED_DATA_META_KEY, $generated_data_json_string);
}

// =========================================================================================== \\

/**
 * Function to read the post content summary for a given post
 * @param $post_id int the post id
 * @return string
 */
function ai4seo_read_post_content_summary_from_post_meta(int $post_id): string {
    // reading in post meta, looking for the meta_key AI4SEO_POST_META_POST_CONTENT_SUMMARY_META_KEY
    $post_content_summary = get_post_meta($post_id, AI4SEO_POST_META_POST_CONTENT_SUMMARY_META_KEY, true);

    if (!$post_content_summary) {
        return "";
    }

    return sanitize_text_field($post_content_summary);
}

// =========================================================================================== \\

/**
 * Function to save the post content summary for a given post
 * @param $post_id int the post id
 * @param $post_content_summary string the content summary
 * @return bool
 */
function ai4seo_save_post_content_summary_to_postmeta(int $post_id, string $post_content_summary): bool {
    // sanitize the post content
    $post_content_summary = sanitize_text_field($post_content_summary);

    // save the data
    return update_post_meta($post_id, AI4SEO_POST_META_POST_CONTENT_SUMMARY_META_KEY, $post_content_summary);
}

// =========================================================================================== \\

/**
 * Compares two post content summaries. Returns true if they share a XX% similarity
 * @param $post_content_summary_1 string the first post content summary
 * @param $post_content_summary_2 string the second post content summary
 * @param $min_similarity_percentage int the percentage of similarity
 * @return bool
 */
function ai4seo_are_post_content_summaries_similar(string $post_content_summary_1, string $post_content_summary_2, int $min_similarity_percentage = 90): bool {
    // make sure that the similarity percentage is between 0 and 100
    $min_similarity_percentage = max(0, min(100, $min_similarity_percentage));

    // compare the two strings
    $similarity_percentage = 0;
    similar_text($post_content_summary_1, $post_content_summary_2, $similarity_percentage);

    // return true if the similarity is greater than the given percentage
    return ($similarity_percentage >= $min_similarity_percentage);
}


// ___________________________________________________________________________________________ \\
// === ATTACHMENTS / MEDIA =================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Function to read and analyse the attachment attributes coverage of the given attachment ids (post ids)
 * @param int|array $attachment_post_ids The post ids of the attachments we want to analyse
 * @return array
 */
function ai4seo_read_and_analyse_attachment_attributes_coverage($attachment_post_ids): array {
    global $wpdb;

    // to allow single post ids as parameter
    if (!is_array($attachment_post_ids)) {
        $attachment_post_ids = array($attachment_post_ids);
    }

    // build an array that holds track of which attachment attributes are covered by the given posts
    $attachment_attributes_coverage = ai4seo_create_empty_attachment_attributes_coverage_array($attachment_post_ids);

    // make sure $attachment_ids is not empty
    if (!$attachment_post_ids) {
        return $attachment_attributes_coverage;
    }

    // make sure all entries of post_ids are numeric
    foreach ($attachment_post_ids as $key => $attachment_post_id) {
        if (!is_numeric($attachment_post_id)) {
            return $attachment_attributes_coverage;
        }
    }

    // Make sure that all parameters are not empty
    if (empty($attachment_post_ids)) {
        return $attachment_attributes_coverage;
    }

    $attachment_post_ids_string = implode(",", $attachment_post_ids);
    $attachment_post_ids_string = sanitize_text_field($attachment_post_ids_string);

    // TITLE (post_title), CAPTION (post_excerpt), DESCRIPTION (post_content) AND FILE_NAME (guid) @ WP_POSTS TABLE
    $query = "SELECT ID, post_title, post_excerpt, post_content, guid FROM " . esc_sql($wpdb->posts) . " WHERE ID IN (" . esc_sql($attachment_post_ids_string) . ")";

    $attachment_posts = $wpdb->get_results($query, ARRAY_A);

    // go through them results and add the "title", "caption" and "description" attribute to the $attachment_attributes_coverage array
    foreach ($attachment_posts as $attachment_post) {
        if (empty($attachment_post["ID"])) {
            continue;
        }

        $attachment_attributes_coverage[$attachment_post["ID"]]["title"] = $attachment_post["post_title"];
        $attachment_attributes_coverage[$attachment_post["ID"]]["caption"] = $attachment_post["post_excerpt"];
        $attachment_attributes_coverage[$attachment_post["ID"]]["description"] = $attachment_post["post_content"];

        // file name: only consider everything after the last slash
        #$this_attachment_post_file_name = $attachment_post["guid"];
        #$attachment_attributes_coverage[$attachment_post["ID"]]["file-name"] = substr($this_attachment_post_file_name, strrpos($this_attachment_post_file_name, "/") + 1);
    }

    // ALT TEXT (_wp_attachment_image_alt) WP_POSTMETA TABLE
    $query = "SELECT * FROM " . esc_sql($wpdb->postmeta) . " WHERE meta_key = '_wp_attachment_image_alt' AND post_id IN (" . esc_sql($attachment_post_ids_string) . ")";

    $attachment_postmeta_entries = $wpdb->get_results($query, ARRAY_A);

    // go through the results and add the "alt-text" to the $attachment_attributes_coverage array
    foreach ($attachment_postmeta_entries as $attachment_postmeta_entry) {
        if (empty($attachment_postmeta_entry["post_id"])) {
            continue;
        }

        // we go through like this, in case we need to add more attributes in the future
        if ($attachment_postmeta_entry["meta_key"] == "_wp_attachment_image_alt") {
            $attachment_attributes_coverage[$attachment_postmeta_entry["post_id"]]["alt-text"] = $attachment_postmeta_entry["meta_value"];
        }
    }

    return $attachment_attributes_coverage;
}

// =========================================================================================== \\

/**
 * Function to return the summary of the attachment attributes coverage array
 * @param $attachment_attributes_coverage array The attachment attributes coverage array generated by ai4seo_read_and_analyse_attachment_attributes_coverage()
 * @return array The summary of the attachment attributes coverage array, basically the amount of filled attachment attributes per attachment
 */
function ai4seo_get_attachment_attributes_coverage_summary(array $attachment_attributes_coverage): array {
    // generate a summary of the attachment attributes coverage array
    $attachment_attributes_coverage_summary = array();

    if (!$attachment_attributes_coverage) {
        return $attachment_attributes_coverage_summary;
    }

    foreach ($attachment_attributes_coverage as $attachment_post_id => $attachment_attributes) {
        $attachment_attributes_coverage_summary[$attachment_post_id] = 0;

        foreach ($attachment_attributes as $this_attachment_attribute) {
            if ($this_attachment_attribute) {
                $attachment_attributes_coverage_summary[$attachment_post_id]++;
            }
        }
    }

    return $attachment_attributes_coverage_summary;
}

// =========================================================================================== \\

/**
 * Function to create an empty attachment attributes coverage array
 * @param $attachment_post_ids array The post ids of the attachments we want to analyse
 * @return array The empty attachment attributes coverage array
 */
function ai4seo_create_empty_attachment_attributes_coverage_array(array $attachment_post_ids): array {
    global $ai4seo_attachments_attributes_details;

    // make sure all entries of post_ids are numeric
    foreach ($attachment_post_ids as $attachment_post_id) {
        if (!is_numeric($attachment_post_id)) {
            return array();
        }
    }

    // Make sure that all parameters are not empty
    if (empty($attachment_post_ids)) {
        return array();
    }

    // build an array that holds track of which attachment_attributes are covered by the given posts
    $attachment_attributes_coverage = array();

    foreach ($attachment_post_ids as $post_id) {
        $attachment_attributes_coverage[$post_id] = array();

        foreach ($ai4seo_attachments_attributes_details as $this_attachment_attribute_name => $this_attachment_attribute_details) {
            $attachment_attributes_coverage[$post_id][$this_attachment_attribute_name] = "";
        }
    }

    return $attachment_attributes_coverage;
}

// =========================================================================================== \\

/**
 * Checks if the metadata for a given post is fully covered
 * @param $attachment_post_ids int The post id to check the metadata coverage for
 * @return bool Whether the metadata for a given post is fully covered
 */
function ai4seo_are_attachment_attributes_fully_covered(int $attachment_post_ids): bool {
    // get the total amount of attachment attributes
    $num_total_attachment_attributes = ai4seo_get_num_attachment_attributes();

    // get existing attributes coverage
    $attachment_attributes_coverage = ai4seo_read_and_analyse_attachment_attributes_coverage($attachment_post_ids);
    $attachment_attributes_coverage_summary = ai4seo_get_attachment_attributes_coverage_summary($attachment_attributes_coverage);
    $num_filled_attachment_attributes = $attachment_attributes_coverage_summary[$attachment_post_ids] ?? 0;
    $attachment_attributes_coverage_percentage = ($num_filled_attachment_attributes / $num_total_attachment_attributes) * 100;

    return ($attachment_attributes_coverage_percentage == 100);
}

// =========================================================================================== \\

/**
 * Returns the number of attachment attributes
 * @return int the number of attachment attributes
 */
function ai4seo_get_num_attachment_attributes(): int {
    global $ai4seo_attachments_attributes_details;
    return $ai4seo_attachments_attributes_details ? count($ai4seo_attachments_attributes_details) : 0;
}

// =========================================================================================== \\

/**
 * Returns the attachment attributes for a specific attachment post id
 * @param $attachment_post_id int The post id of the attachment
 * @return array The attachment attributes
 */
function ai4seo_read_attachment_attributes(int $attachment_post_id): array {
    // Read attachment title, caption, description, alt-text and file-path
    $ai4seo_this_attachment_post = get_post($attachment_post_id);
    $ai4seo_this_post_attachment_attributes_values["title"] = $ai4seo_this_attachment_post->post_title ?? "";
    $ai4seo_this_post_attachment_attributes_values["caption"] = $ai4seo_this_attachment_post->post_excerpt ?? "";
    $ai4seo_this_post_attachment_attributes_values["description"] = $ai4seo_this_attachment_post->post_content ?? "";
    $ai4seo_this_post_attachment_attributes_values["alt-text"] = get_post_meta($attachment_post_id, "_wp_attachment_image_alt", true) ?? "";
    //$ai4seo_this_attachment_post_details["file-name"] = basename(get_attached_file($attachment_post_id)) ?? "";

    return $ai4seo_this_post_attachment_attributes_values;
}

// =========================================================================================== \\

/**
 * Refreshes the attachment attributes coverage for the given post by putting the post id into the corresponding option
 * @param $post_id int The post id to refresh the attachment attributes coverage for
 * @param null $post WP_Post|null The post object to refresh the attachment attributes coverage for
 * @return void
 */
function ai4seo_refresh_one_posts_attachment_attributes_coverage(int $post_id, $post = null) {
    if (!is_numeric($post_id)) {
        return;
    }

    if (!ai4seo_is_post_a_valid_attachment($post_id, $post)) {
        ai4seo_remove_post_ids_from_all_options($post_id);
        return;
    }

    // consider which option to put the post id into
    if (ai4seo_are_attachment_attributes_fully_covered($post_id)) {
        ai4seo_add_post_ids_to_option(AI4SEO_FULLY_COVERED_ATTACHMENT_ATTRIBUTES_POST_IDS, $post_id);
    } else {
        ai4seo_add_post_ids_to_option(AI4SEO_MISSING_ATTACHMENT_ATTRIBUTES_POST_IDS, $post_id);
    }

    // check if the post has generated data
    if (ai4seo_post_has_generated_data($post_id)) {
        ai4seo_add_post_ids_to_option(AI4SEO_GENERATED_ATTACHMENT_ATTRIBUTES_POST_IDS, $post_id);
    }
}

// =========================================================================================== \\

/**
 * This function checks if an attachment is valid for our plugin to be considered
 * @param $post_id int The post id to check
 * @param $post WP_Post|null The post object to check
 * @return bool Whether the attachment is valid
 */
function ai4seo_is_post_a_valid_attachment(int $post_id, WP_Post $post = null): bool {
    global $ai4seo_allowed_attachment_mime_types;

    if (!is_numeric($post_id)) {
        return false;
    }

    // read post
    if ($post === null) {
        $post = get_post($post_id);
    }

    // check if the post could be read
    if (!$post || is_wp_error($post) || !isset($post->post_type)) {
        return false;
    }

    // check if the post type is an attachment
    if ($post->post_type != "attachment") {
        return false;
    }

    // check mime type
    if (!in_array($post->post_mime_type, $ai4seo_allowed_attachment_mime_types)) {
        return false;
    }

    // check post status
    if (!in_array($post->post_status, array("publish", "future", "private", "pending", "inherit"))) {
        return false;
    }

    return true;
}

// =========================================================================================== \\

/**
 * Creates a base64-encoded string of an image, downsizing it if necessary to fit within 3 MB.
 *
 * @param string $image_data The image data to encode.
 * @return string The base64-encoded image data, or false if there was an error.
 */
function ai4seo_smart_image_base64_encode( string $image_data ): string {
    // Set the file size limit to 1 MB.
    $max_file_size = 100000; // 1 MB in bytes.

    try {
        // Get the size of the decoded image data in bytes.
        $image_size = strlen( $image_data );

        // If the image size is less than or equal to the limit, return the original image as base64.
        if ( $image_size <= $max_file_size ) {
            return base64_encode( $image_data );
        }

        // Try to create an image from the string.
        $image = @imagecreatefromstring( $image_data );

        if ( $image === false ) {
            throw new Exception( 'Failed to create image from string.' );
        }

        // Get the original image dimensions.
        $width  = imagesx( $image );
        $height = imagesy( $image );

        // Calculate the scaling factor to downsize the image to fit within 1 MB.
        $scale      = sqrt( $max_file_size / $image_size );
        $new_width  = intval( $width * $scale );
        $new_height = intval( $height * $scale );

        // Create a new image with the new dimensions.
        $new_image = imagecreatetruecolor( $new_width, $new_height );
        if ( !imagecopyresampled( $new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height ) ) {
            throw new Exception( 'Failed to resample the image.' );
        }

        // Start output buffering to capture the downsized image data.
        ob_start();
        if ( !imagejpeg( $new_image, null, 75 ) ) { // 75 is the quality for the JPEG.
            ob_end_clean();
            throw new Exception( 'Failed to output the resized image.' );
        }
        $downsized_image_data = ob_get_contents();
        ob_end_clean();

        // Free memory.
        imagedestroy( $image );
        imagedestroy( $new_image );

        // Return the new base64-encoded image.
        return base64_encode( $downsized_image_data );

    } catch ( Exception $e ) {
        // Log the error message for debugging (WordPress style).
        error_log( 'AI4SEO: ai4seo_smart_image_base64_encode() error: ' . $e->getMessage() );

        // Free any allocated resources in case of an error.
        if ( isset( $image ) && is_resource( $image ) ) {
            imagedestroy( $image );
        }

        if ( isset( $new_image ) && is_resource( $new_image ) ) {
            imagedestroy( $new_image );
        }

        // Return false to indicate failure.
        return base64_encode( $image_data );
    }
}


// ___________________________________________________________________________________________ \\
// === POST META ============================================================================= \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Function to update a post meta if it is empty
 * @param $post_id int the post id
 * @param $meta_key string the meta key
 * @param $meta_value string the meta value
 * @return bool True if the post meta was updated, false if not
 */
function ai4seo_update_postmeta_if_empty($post_id, $meta_key, $meta_value): bool {
    $post_id = sanitize_key($post_id);
    $meta_key = sanitize_key($meta_key);
    $meta_value = sanitize_textarea_field($meta_value);

    $current_value = get_post_meta($post_id, $meta_key, true);

    if ($current_value) {
        return false;
    } else {
        update_post_meta($post_id, $meta_key, $meta_value);
        return true;
    }
}

// =========================================================================================== \\

/**
 * Returns weather a post got generated data
 * @param $post_id int the post id
 * @return bool
 */
function ai4seo_post_has_generated_data(int $post_id): bool {
    $generated_data = ai4seo_read_generated_data_from_post_meta($post_id);
    return !empty($generated_data);
}


// ___________________________________________________________________________________________ \\
// === WORDPRESS OPTIONS ===================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Function to get all post ids based on an option that is saved as json
 * @param string $ai4seo_option
 * @return array
 */
function ai4seo_get_post_ids_from_option(string $ai4seo_option): array {
    $ai4seo_option = sanitize_key($ai4seo_option);

    // get post ids
    $post_ids = get_option($ai4seo_option);

    // sanitize the post ids
    $post_ids = sanitize_text_field($post_ids);

    // create empty option if it does not exist
    if (!$post_ids) {
        add_option($ai4seo_option, wp_json_encode(array()));
        return array();
    }

    $post_ids = json_decode($post_ids);

    // on error -> return empty array
    if (!$post_ids || !is_array($post_ids)) {
        $post_ids = array();
    }

    // make sure every entry is numeric
    foreach ($post_ids as $key => $post_id) {
        if (!is_numeric($post_id)) {
            unset($post_ids[$key]);
        }
    }

    return array_unique($post_ids);
}

// =========================================================================================== \\

/**
 * Function to add post ids to an option that is saved as json
 * @param $ai4seo_option
 * @param $post_ids
 * @return bool
 */
function ai4seo_add_post_ids_to_option($ai4seo_option, $post_ids): bool {
    $ai4seo_option = sanitize_key($ai4seo_option);

    if (!is_array($post_ids)) {
        $post_ids = array($post_ids);
    }

    // make sure every entry is numeric
    foreach ($post_ids as $key => $post_id) {
        if (!is_numeric($post_id)) {
            unset($post_ids[$key]);
        }
    }

    // logic based removals
    ai4seo_remove_contradictory_post_ids($ai4seo_option, $post_ids);

    // get old post ids
    $old_post_ids = ai4seo_get_post_ids_from_option($ai4seo_option);

    // add the new post ids to the old ones
    $new_post_ids = array_merge($old_post_ids, $post_ids);
    $new_post_ids = array_unique($new_post_ids);
    $new_post_ids_json = sanitize_text_field(wp_json_encode($new_post_ids));

    return update_option($ai4seo_option, $new_post_ids_json);
}

// =========================================================================================== \\

/**
 * Function to remove post ids from options that are contrary to the option that got added to
 * @param $add_to_this_option string The option that got added to
 * @param $post_ids array The post ids that got added (and need to get removed)
 * @return void
 */
function ai4seo_remove_contradictory_post_ids(string $add_to_this_option, array $post_ids) {
    switch ($add_to_this_option) {
        // now missing -> remove from fully covered and generated
        case AI4SEO_MISSING_METADATA_POST_IDS:
            ai4seo_remove_post_ids_from_option(AI4SEO_FULLY_COVERED_METADATA_POST_IDS, $post_ids);
            ai4seo_remove_post_ids_from_option(AI4SEO_GENERATED_METADATA_POST_IDS, $post_ids);
            break;
        case AI4SEO_MISSING_ATTACHMENT_ATTRIBUTES_POST_IDS:
            ai4seo_remove_post_ids_from_option(AI4SEO_FULLY_COVERED_ATTACHMENT_ATTRIBUTES_POST_IDS, $post_ids);
            ai4seo_remove_post_ids_from_option(AI4SEO_GENERATED_ATTACHMENT_ATTRIBUTES_POST_IDS, $post_ids);
            break;

        // now fully covered and/or generated -> remove from missing
        case AI4SEO_FULLY_COVERED_METADATA_POST_IDS:
        case AI4SEO_GENERATED_METADATA_POST_IDS:
            ai4seo_remove_post_ids_from_option(AI4SEO_MISSING_METADATA_POST_IDS, $post_ids);
            break;
        case AI4SEO_FULLY_COVERED_ATTACHMENT_ATTRIBUTES_POST_IDS:
        case AI4SEO_GENERATED_ATTACHMENT_ATTRIBUTES_POST_IDS:
            ai4seo_remove_post_ids_from_option(AI4SEO_MISSING_ATTACHMENT_ATTRIBUTES_POST_IDS, $post_ids);
            break;

        // now processing -> remove from pending
        case AI4SEO_PROCESSING_METADATA_POST_IDS:
            ai4seo_remove_post_ids_from_option(AI4SEO_PENDING_METADATA_POST_IDS, $post_ids);
            break;
        case AI4SEO_PROCESSING_ATTACHMENT_ATTRIBUTES_POST_IDS:
            ai4seo_remove_post_ids_from_option(AI4SEO_PENDING_ATTACHMENT_ATTRIBUTES_POST_IDS, $post_ids);
            break;

        // now pending -> remove from processing
        case AI4SEO_PENDING_METADATA_POST_IDS:
            ai4seo_remove_post_ids_from_option(AI4SEO_PROCESSING_METADATA_POST_IDS, $post_ids);
            break;
        case AI4SEO_PENDING_ATTACHMENT_ATTRIBUTES_POST_IDS:
            ai4seo_remove_post_ids_from_option(AI4SEO_PROCESSING_ATTACHMENT_ATTRIBUTES_POST_IDS, $post_ids);
            break;
    }
}

// =========================================================================================== \\

/**
 * Remove post ids from an option that is saved as json
 * @param $ai4seo_option
 * @param $post_ids
 * @return bool
 */
function ai4seo_remove_post_ids_from_option($ai4seo_option, $post_ids): bool {
    $ai4seo_option = sanitize_key($ai4seo_option);

    if (!is_array($post_ids)) {
        $post_ids = array($post_ids);
    }

    // make sure every entry is numeric
    foreach ($post_ids as $key => $post_id) {
        if (!is_numeric($post_id)) {
            unset($post_ids[$key]);
        }
    }

    // get old post ids
    $old_post_ids = ai4seo_get_post_ids_from_option($ai4seo_option);

    // remove the new post ids from the old ones
    $new_post_ids = array_diff($old_post_ids, $post_ids);

    // rearrange the array keys to start at 0
    $new_post_ids = array_values($new_post_ids);
    $new_post_ids = array_unique($new_post_ids);

    // check if old and new post ids are the same
    if ($old_post_ids === $new_post_ids) {
        return false;
    }

    // update the option
    $new_post_ids_json = sanitize_text_field(wp_json_encode($new_post_ids));
    return update_option($ai4seo_option, $new_post_ids_json);
}

// =========================================================================================== \\

/**
 * Function to remove post ids from EVERY WP_OPTION
 * @param int|array $post_ids
 */
function ai4seo_remove_post_ids_from_all_options($post_ids) {
    foreach (AI4SEO_ALL_POST_ID_OPTIONS as $ai4seo_option) {
        ai4seo_remove_post_ids_from_option($ai4seo_option, $post_ids);
    }
}

// =========================================================================================== \\

/**
 * Function ro remove post ids from EVERY WP_OPTION that handles the SEO COVERAGE
 * @param int|array $post_ids
 */
function ai4seo_remove_post_ids_from_all_seo_coverage_options($post_ids) {
    foreach (AI4SEO_SEO_COVERAGE_POST_ID_OPTIONS as $ai4seo_option) {
        ai4seo_remove_post_ids_from_option($ai4seo_option, $post_ids);
    }
}

// =========================================================================================== \\

/**
 * Function to remove post ids from EVERY WP_OPTION that handles the GENERATION STATUS
 * @param int|array $post_ids
 */
function ai4seo_remove_post_ids_from_all_generation_status_options($post_ids) {
    foreach (AI4SEO_GENERATION_STATUS_POST_ID_OPTIONS as $ai4seo_option) {
        ai4seo_remove_post_ids_from_option($ai4seo_option, $post_ids);
    }
}


// ___________________________________________________________________________________________ \\
// === AJAX ================================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Called via AJAX - Requires the metadata editor to be displayed
 * @return void
 */
function ai4seo_show_metadata_editor() {
    require_once(ai4seo_get_includes_ajax_display_path("metadata-editor.php"));
    wp_die();
}


// =========================================================================================== \\

/**
 * Called via AJAX - Requires the attachment attributes editor to be displayed
 * @return void
 */
function ai4seo_show_attachment_attributes_editor() {
    require_once(ai4seo_get_includes_ajax_display_path("attachment-attributes-editor.php"));
    wp_die();
}


// =========================================================================================== \\

/**
 * Called via AJAX - Generates metadata after clicking on a generate metadata button
 * @return void
 */
function ai4seo_generate_metadata() {
    require_once(ai4seo_get_includes_ajax_process_path("generate-metadata.php"));
    wp_die();
}


// =========================================================================================== \\

/**
 * Called via AJAX - Generates attachment-attributes after clicking on a generate attachment-attributes button
 * @return void
 */
function ai4seo_generate_attachment_attributes() {
    require_once(ai4seo_get_includes_ajax_process_path("generate-attachment-attributes.php"));
    wp_die();
}


// =========================================================================================== \\

/**
 * Called via AJAX - Saves the user input from the metadata editor
 * @return void
 */
function ai4seo_save_metadata_editor_values() {
    require_once(ai4seo_get_includes_ajax_process_path("save-metadata-editor-values.php"));
    wp_die();
}

// =========================================================================================== \\

/**
 * Called via AJAX - Saves the user input from the attachment attributes editor
 * @return void
 */
function ai4seo_save_attachment_attributes_editor_values() {
    require_once(ai4seo_get_includes_ajax_process_path("save-attachment-attributes-editor-values.php"));
    wp_die();
}


// =========================================================================================== \\

/**
 * Called via AJAX - Toggles the automation of the metadata generation
 * @return void
 */
function ai4seo_toggle_automated_generation() {
    require_once(ai4seo_get_includes_ajax_process_path("toggle-automated-generation.php"));
    wp_die();
}


// =========================================================================================== \\

/**
 * Called via AJAX - Updates the licence key into the database
 * @return void
 */
function ai4seo_submit_licence_key() {
    require_once(ai4seo_get_includes_ajax_process_path("submit-licence-key.php"));
    wp_die();
}

// =========================================================================================== \\

/**
 * Called via AJAX - Updates the performance notice to be dismissed
 * @return void
 */
function ai4seo_dismiss_performance_notice() {
    // update option _ai4seo_performance_notice_dismissed_timestamp
    update_option("_ai4seo_performance_notice_dismissed_timestamp", time());

    $ai4seo_response = array(
        "success" => true,
    );

    ai4seo_return_success_as_json($ai4seo_response);

    wp_die();
}


// ___________________________________________________________________________________________ \\
// ==== SETTINGS ============================================================================= \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Retrieve all settings
 * @return array
 */
function ai4seo_get_all_settings(): array {
    global $ai4seo_settings;
    return $ai4seo_settings;
}

// =========================================================================================== \\

/**
 * Retrieve value of a setting
 * @param string $setting_name The name of the setting
 */
function ai4seo_get_setting(string $setting_name) {
    global $ai4seo_settings;

    // Make sure that $setting_name-parameter has content
    if (!$setting_name) {
        error_log("AI4SEO: Setting name is empty. #8122824");
        return "";
    }

    // Check if the $setting_name-parameter exists in settings-array
    if (!isset($ai4seo_settings[$setting_name])) {
        return "";
    }

    return $ai4seo_settings[$setting_name];
}

// =========================================================================================== \\

/**
 * Update value a setting
 * @return bool True if the setting was updated successfully, false if not
 */
function ai4seo_update_setting(string $setting_name, $new_setting_value): bool {
    global $ai4seo_settings;

    // Make sure that the new value of the setting is valid
    if (!ai4seo_validate_setting_value($setting_name, $new_setting_value)) {
        error_log("AI4SEO: Invalid setting value for setting '" . $setting_name . "'. #9122824");
        return false;
    }

    // Overwrite entry in $ai4seo_settings-array
    $ai4seo_settings[$setting_name] = $new_setting_value;

    // encode settings
    $ai4seo_settings_encoded = json_encode($ai4seo_settings);

    if (!$ai4seo_settings_encoded) {
        error_log("AI4SEO: Could not encode settings to JSON. #6122824");
        return false;
    }

    // Save updated settings to database
    return update_option("ai4seo_settings", $ai4seo_settings_encoded);
}

// =========================================================================================== \\

/**
 * Update values of given settings
 * @param $setting_changes array An array of settings to update
 * @return bool True if the setting was updated successfully, false if not
 */
function ai4seo_bulk_update_settings(array $setting_changes): bool {
    global $ai4seo_settings;

    $ai4seo_new_settings = $ai4seo_settings;

    foreach ($setting_changes AS $this_setting_name => $this_setting_value) {
        // Make sure that the new value of the setting is valid
        if (!ai4seo_validate_setting_value($this_setting_name, $this_setting_value)) {
            error_log("AI4SEO: Invalid setting value for setting '" . $this_setting_name . "'. #40146824");
            return false;
        }

        // Overwrite entry in $ai4seo_settings-array
        $ai4seo_new_settings[$this_setting_name] = $this_setting_value;
    }

    $ai4seo_settings = $ai4seo_new_settings;

    // encode settings
    $ai4seo_settings_encoded = json_encode($ai4seo_settings);

    if (!$ai4seo_settings_encoded) {
        error_log("AI4SEO: Could not encode settings to JSON. #41146824");
        return false;
    }

    // Save updated settings to database
    update_option("ai4seo_settings", $ai4seo_settings_encoded);

    return true;
}

// =========================================================================================== \\

/**
 * Validate value of a setting
 * @return bool True if the value is valid, false if not
 */
function ai4seo_validate_setting_value(string $setting_name, $setting_value): bool {
    global $ai4seo_metadata_details;
    global $ai4seo_attachments_attributes_details;

    switch ($setting_name) {
        case AI4SEO_SETTING_META_TAG_OUTPUT_MODE:
            return in_array($setting_value, array_keys(AI4SEO_SETTING_META_TAG_OUTPUT_MODE_ALLOWED_VALUES));

        case AI4SEO_SETTING_ALLOWED_USER_ROLES:
            // Make sure that the new setting-value is an array
            if (!is_array($setting_value)) {
                error_log("AI4SEO: Setting value for setting '" . $setting_name . "' is not an array. #45146824");
                return false;
            }

            $allowed_user_roles = ai4seo_get_all_possible_user_roles();
            $allowed_user_role_identifiers = array_keys($allowed_user_roles);

            // check if all values are proper user roles
            foreach ($setting_value as $user_role_identifier) {
                if (!in_array($user_role_identifier, $allowed_user_role_identifiers)) {
                    error_log("AI4SEO: Invalid user role in the allowed user roles. #44146824");
                    return false;
                }
            }

            // Make sure that the administrator-role exists in the array
            if (!in_array("administrator", $setting_value)) {
                error_log("AI4SEO: Administrator role is missing in the allowed user roles #43146824");
                return false;
            }

            return true;

        case AI4SEO_SETTING_ENABLED_AUTOMATED_GENERATIONS:
            // Make sure that the new setting-value is an array
            if (!is_array($setting_value)) {
                error_log("AI4SEO: Setting value for setting '" . $setting_name . "' is not an array. #1188824");
                return false;
            }

            // Make sure the keys consist of alphanumeric strings, with - and _ allowed and the values should be "1" or "0" only
            foreach ($setting_value as $key => $value) {
                if (!preg_match("/^[a-zA-Z0-9_-]+$/", $key)) {
                    error_log("AI4SEO: Invalid key in the enabled auto generations setting. #2188824");
                    return false;
                }

                if (!in_array($value, array("0", "1"))) {
                    error_log("AI4SEO: Invalid value in the enabled auto generations setting. #3188824");
                    return false;
                }
            }

            return true;

        case AI4SEO_SETTING_APPLY_CHANGES_TO_THIRD_PARTY_SEO_PLUGINS:
            if (!is_array($setting_value)) {
                error_log("AI4SEO: Setting value for setting '" . $setting_name . "' is not an array. #161523924");
                return false;
            }

            $allowed_third_party_seo_plugin_identifier = array_keys(AI4SEO_THIRD_PARTY_SEO_PLUGIN_DETAILS);

            foreach ($setting_value as $key => $value) {
                if (!is_string($value) || !preg_match("/^[a-zA-Z0-9_-]+$/", $value)) {
                    error_log("AI4SEO: Invalid value in the apply changes to third party seo plugin setting. #171523924");
                    return false;
                }

                if (!in_array($value, $allowed_third_party_seo_plugin_identifier)) {
                    error_log("AI4SEO: Invalid third party seo plugin name in the apply changes to third party seo plugin setting. #181523924");
                    return false;
                }
            }

            return true;

        case AI4SEO_SETTING_METADATA_GENERATION_LANGUAGE:
        case AI4SEO_SETTING_ATTACHMENT_ATTRIBUTES_GENERATION_LANGUAGE:
            // Make sure that the new setting-value is a string
            if (!is_string($setting_value)) {
                error_log("AI4SEO: Setting value for setting '" . $setting_name . "' is not a string. #261016824");
                return false;
            }

            $generation_language_options = array_keys(ai4seo_get_translated_generation_language_options());

            // Make sure that the new setting-value is a valid language
            if ($setting_value !== "auto" && !in_array($setting_value, $generation_language_options)) {
                error_log("AI4SEO: Invalid language in the generation language setting: " . $setting_name . ". #271016824");
                return false;
            }

            return true;

        case AI4SEO_SETTING_VISIBLE_META_TAGS:
        case AI4SEO_SETTING_OVERWRITE_EXISTING_METADATA:
            // Make sure that the new setting-value is an array
            if (!is_array($setting_value)) {
                error_log("AI4SEO: Setting value for setting '" . $setting_name . "' is not an array. #421728824");
                return false;
            }

            $all_meta_tags = array_keys($ai4seo_metadata_details);

            // Make sure that the new setting-value is a valid meta tag
            foreach ($setting_value as $meta_tag) {
                if (!in_array($meta_tag, $all_meta_tags)) {
                    error_log("AI4SEO: Invalid meta tag in the visible meta tags setting: " . $setting_name . ". #431728824");
                    return false;
                }
            }

            return true;

        case AI4SEO_SETTING_OVERWRITE_EXISTING_ATTACHMENT_ATTRIBUTES:
            // Make sure that the new setting-value is an array
            if (!is_array($setting_value)) {
                error_log("AI4SEO: Setting value for setting '" . $setting_name . "' is not an array. #101424924");
                return false;
            }

            $all_attachment_attributes = array_keys($ai4seo_attachments_attributes_details);

            // Make sure that the new setting-value is a valid attachment attribute
            foreach ($setting_value as $attachment_attribute) {
                if (!in_array($attachment_attribute, $all_attachment_attributes)) {
                    error_log("AI4SEO: Invalid attachment attribute in the overwrite existing attachment attributes setting: " . $setting_name . ". #111424924");
                    return false;
                }
            }

            return true;

        default:
            return false;
    }
}


// ___________________________________________________________________________________________ \\
// === AUTO GENERATIONS ====================================================================== \\
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ \\

/**
 * Check if the auto generation is enabled for a specific context
 * @param $context string The context of the auto generation (post, page, product, attachment, keyphrase etc.)
 * @return bool True if the auto generation is enabled, false if not
 */
function ai4seo_is_automated_generation_enabled(string $context): bool {
    $enabled_automated_generations = ai4seo_get_setting(AI4SEO_SETTING_ENABLED_AUTOMATED_GENERATIONS) ?: array();
    return isset($enabled_automated_generations[$context]) && $enabled_automated_generations[$context] == "1";
}

// =========================================================================================== \\

/**
 * Enable the auto generation for a specific context
 * @param string $context The context of the auto generation (post, page, product, attachment, keyphrase etc.)
 * @return bool True if the auto generation was enabled, false if not
 */
function ai4seo_enable_automated_generation(string $context): bool {
    $enabled_auto_generations = ai4seo_get_setting(AI4SEO_SETTING_ENABLED_AUTOMATED_GENERATIONS) ?: array();
    $enabled_auto_generations[$context] = "1";
    return ai4seo_update_setting(AI4SEO_SETTING_ENABLED_AUTOMATED_GENERATIONS, $enabled_auto_generations);
}

// =========================================================================================== \\

/**
 * Disable the auto generation for a specific context
 * @param string $context The context of the auto generation (post, page, product, attachment, keyphrase etc.)
 * @return bool True if the auto generation was disabled, false if not
 */
function ai4seo_disable_automated_generation(string $context): bool {
    $enabled_auto_generations = ai4seo_get_setting(AI4SEO_SETTING_ENABLED_AUTOMATED_GENERATIONS) ?: array();
    $enabled_auto_generations[$context] = "0";
    return ai4seo_update_setting(AI4SEO_SETTING_ENABLED_AUTOMATED_GENERATIONS, $enabled_auto_generations);
}

// =========================================================================================== \\

/**
 * Function to retrieve the (maximum) number of possible generations based on a given credits-amount
 * @param $credits_amount int The amount of credits
 * @return int The (maximum) number of possible generations
 */
function ai4seo_get_num_credits_amount_based_generations(int $credits_amount): int {
    // Make sure that credits-amount is numeric
    if (!is_numeric($credits_amount) || !is_numeric(AI4SEO_CREDITS_FLAT_COST) || AI4SEO_CREDITS_FLAT_COST === 0) {
        return 0;
    }

    return ($credits_amount / AI4SEO_CREDITS_FLAT_COST);
}