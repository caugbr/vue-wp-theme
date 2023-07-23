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

// Settings
include_once $themeDir . "/settings/index.php";
$settings = new ThemeSettings('vuewp_settings');

vuewp_add_support();

// Add translation functions
include_once $themeDir . "/scripts/translation-functions.php";
$translation = new TranslationFunctions($themeDir, $appDir);

// Add routes
include_once $themeDir . "/scripts/routes-vue.php";
$vue_routes = new RoutesVue($themeDir, $appDir);

// Remove redirects
remove_action('template_redirect', 'redirect_canonical');

// REST API custom endpoints
include_once $themeDir . "/scripts/extend-rest-api.php";

// Add sidebars and custom widgets
include_once $themeDir . "/scripts/widgets.php";

// Add options page
include_once $themeDir . "/scripts/admin-page.php";

// Theme add support
function vuewp_add_support() {
    global $vuewp_logo;
    global $settings;
    $options = $settings->get_saved();
    $post_formats = $options['post_formats'] ?? '';

    if (is_array($post_formats) && count($post_formats)) {
        add_theme_support('post-formats', $post_formats);
    }
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['style','script']);
    add_theme_support('widgets');
    add_theme_support('custom-logo', $vuewp_logo);
    add_theme_support('title-tag');
}

////////////////////////
function change_the_title($title) {
    if (is_404()) {
        return 'nao é';
    }
    return $title;
}
add_filter('document_title', 'change_the_title');

// Register theme hook
function register_vuewp() {
    $one = get_option('vuewp_theme');
    if (false == $one) {
        update_option('vuewp_theme', 'on');
        do_action('register_vuewp_theme');
    }
}
add_action('admin_init', 'register_vuewp');

// Unregister theme hook
function unregister_vuewp() {
    delete_option('vuewp_theme');
    do_action('unregister_vuewp_theme');
}
add_action('switch_theme', 'unregister_vuewp');


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
    $options = $settings->get_saved();
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
        "settings" => $options,
        "loggedUser" => $user,
        "wpApiSettings" => [
            "root" => esc_url_raw(rest_url()),
            "nonce" => wp_create_nonce('wp_rest'),
            "formats" => !empty($options['post_formats'])
        ]
    ];
    ?>
    <script>
        window.vueWpThemeInfo = <?php print json_encode($ret); ?>;
    </script>
    <?php
}

function vuewp_add_logo() {
    if (function_exists('the_custom_logo') && has_custom_logo()) {
        print '<div class="custom-logo move-to-app" data-to=".site-name">';
        the_custom_logo();
        print '</div>';
    }
}


// list the files on the given directory
function listFiles($directory) {
    $files = array();
    $rdi = new RecursiveDirectoryIterator($directory);
    $rii = new RecursiveIteratorIterator($rdi);
    foreach ($rii as $file) {
        if (!$file->isDir()) { 
            $files[] = str_replace("\\", "/", $file->getPathname()); 
        }
    }
    return $files;
}