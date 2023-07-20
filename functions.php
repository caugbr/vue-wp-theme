<?php

// Theme variables

// Dev URL - npm can change this. In this
// case you must edit the value bellow.
$scriptsUrlDev = 'http://127.0.0.1:8080';

// Theme sidebars - this variable will be used to
// register the sidebars and to render the WP part.
// But in Vue app, you must add an HTML element for
// each one with the same id, like this, on Sidebar:
//   <div id="sidebar_area" class="wp-widgets-area"></div>
$vuewp_areas = [
    [
		'name' => 'Sidebar widgets area',
		'id' => 'sidebar_area',
		'before_widget' => '<div>',
		'after_widget' => '</div>',
		'before_title' => '<h2>',
		'after_title' => '</h2>'
	],
    [
		'name' => 'Footer widgets area',
		'id' => 'footer_area',
		'before_widget' => '<div>',
		'after_widget' => '</div>',
		'before_title' => '<h2>',
		'after_title' => '</h2>'
	]
];

// App directory name
$appDir = 'vue-app';

// Image sizes
$vuewp_image_sizes = [

];

// Max width for site content in pixels
$content_width = 900;

// Logo image defaults
$vuewp_logo = [
    'height' => 40,
    'width' => 80,
    'flex-height' => false,
    'flex-width' => true
];

// Do not edit these ones
$themeDir = get_stylesheet_directory();
$themeDirUrl = get_template_directory_uri();
$vueScripts = [
    'wp-vue-app-js' => 'js/app.js',
    'wp-vue-vendors-js' => 'js/chunk-vendors.js'
];

// Translations
load_theme_textdomain("vuewp", $themeDir . '/languages');

// Theme add support
add_theme_support('post-thumbnails');
add_theme_support('html5', ['style','script']);
add_theme_support('widgets');
add_theme_support('custom-logo', $vuewp_logo);

// Remove redirects
remove_action('template_redirect', 'redirect_canonical');

// Settings
include_once $themeDir . "/settings/index.php";
$settings = new ThemeSettings('vuewp_settings');

// REST API custom endpoints
include_once $themeDir . "/scripts/extend-rest-api.php";

// Add sidebars and custom widgets
include_once $themeDir . "/scripts/widgets.php";

// Add translation functions
include_once $themeDir . "/scripts/translation-functions.php";
$translation = new TranslationFunctions($themeDir, $appDir);

// Add options page
include_once $themeDir . "/scripts/admin-page.php";

/**
 * Include the necessary files to run Vue, based on constant 
 * WP_ENVIRONMENT_TYPE located in wp-config.php.
 * If your development is done, run the build script and use 'production'.
 * During the development, you can use 'local', 'staging' or 'development',
 * in these cases, you must have the development server running.
 *
 * @return void
 */
function enqueue_scripts() {
    global $scriptsUrlDev;
    global $themeDirUrl;
    global $vueScripts;
    global $appDir;
    $envType = wp_get_environment_type();
    if ($envType == 'production') {
        foreach ($vueScripts as $sid => $script) {
            $url = "{$themeDirUrl}/{$appDir}/dist/{$script}";
            wp_enqueue_script($sid, $url);
        }
        $cssUrl = "{$themeDirUrl}/{$appDir}/dist/css/app.css";
        wp_enqueue_style("vp-vue-css-prod", $cssUrl);
    } else {
        foreach ($vueScripts as $sid => $script) {
            wp_enqueue_script($sid, $scriptsUrlDev . "/" . $script);
        }
    }
}
add_action('wp_footer', 'enqueue_scripts');

/**
 * Print variable vueWpThemeInfo
 *
 * @return void
 */
function get_vue_info() {
    global $themeDirUrl;
    global $settings;
	global $current_user;
	global $content_width;
    $user = false;
    if ($current_user->ID) {
        $user = [
            "id" => $current_user->data->ID,
            "name" => $current_user->data->display_name,
            "email" => $current_user->data->user_email,
            "login" => $current_user->data->user_login,
            "role" => $current_user->roles[0]
        ];
    }
    $url_info = parse_url(site_url());
    $ret = [
        "themeDirUrl" => $themeDirUrl,
        "siteUrl" => site_url(),
        "basePath" => $url_info['path'],
        "language" => get_locale(),
        "contentWidth" => $content_width,
        "settings" => $settings->get_saved(),
        "loggedUser" => $user,
        "wpApiSettings" => [
            "root" => esc_url_raw(rest_url()),
            "nonce" => wp_create_nonce('wp_rest')
        ]
    ];
    ?>
    <script>
        window.vueWpThemeInfo = <?php print json_encode($ret); ?>;
    </script>
    <?php
}

function vuewp_add_logo() {
    print '<div class="custom-logo move-to-app" data-to=".site-name">';
    if (function_exists('the_custom_logo')) {
        the_custom_logo();
    }
    print '</div>';
}