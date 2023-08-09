<?php

// Theme variables


// ===================== edit ===================== //

// Dev URL - npm can change it.
// In this case you must edit the value bellow.
$vuewp_dev_url = 'http://127.0.0.1:8080';

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
$vuewp_app_dir = 'vue-app';

// Image sizes
// $vuewp_image_sizes = [
// ];

// Logo image defaults
$vuewp_logo = [
    'height' => 40,
    'width' => 80,
    'flex-height' => false,
    'flex-width' => true
];

// Max width for site content in pixels
$content_width = 900;

// ===================== /edit ===================== //

// Do not edit these ones
$vuewp_theme_dir = get_stylesheet_directory();
$vuewp_theme_url = get_template_directory_uri();
$vuewp_scripts = [
    'wp-vue-app-js' => 'js/app.js',
    'wp-vue-vendors-js' => 'js/chunk-vendors.js'
];

// Translations
load_theme_textdomain("vuewp", $vuewp_theme_dir . '/languages');

// Settings
include_once $vuewp_theme_dir . "/settings/index.php";
$settings = new ThemeSettings('vuewp_settings');
$theme_options = $settings->get_saved();

// Add theme supports
vuewp_add_support();

// Remove redirects
remove_action('template_redirect', 'redirect_canonical');

// Disable 404 errors
add_filter("pre_handle_404", "__return_false");

// Hide admin bar
if ($theme_options['hide_wp_bar']) {
    add_filter("show_admin_bar", "__return_false");
}

// Add translation functions
include_once $vuewp_theme_dir . "/scripts/translation-functions.php";
$translation = new TranslationFunctions($vuewp_theme_dir, $vuewp_app_dir);

// Add routes
include_once $vuewp_theme_dir . "/scripts/routes-vue.php";
$vue_routes = new RoutesVue($vuewp_theme_dir, $vuewp_app_dir);

// REST API custom endpoints
include_once $vuewp_theme_dir . "/scripts/extend-rest-api.php";

// Add sidebars and custom widgets
include_once $vuewp_theme_dir . "/scripts/widgets.php";

// Add options page
include_once $vuewp_theme_dir . "/scripts/admin-page.php";

// Theme add support
function vuewp_add_support() {
    global $vuewp_logo;
    global $theme_options;
    $post_formats = $theme_options['post_formats'] ?? '';

    if (is_array($post_formats) && count($post_formats)) {
        add_theme_support('post-formats', $post_formats);
    }
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['style','script']);
    add_theme_support('widgets');
    add_theme_support('custom-logo', $vuewp_logo);
}

// Register theme hook
function register_vuewp() {
    $on = get_option('vuewp_theme');
    if ('on' !== $on) {
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

// Add WP_ENVIRONMENT_TYPE to wp-config.php on theme
// activation, if it doesn't already exist.
function add_env_type() {
    $content = file_get_contents(ABSPATH . "wp-config.php");
    if (strstr($content, 'WP_ENVIRONMENT_TYPE')) {
        return;
    }
    $code = "\n\n/**\n * Vue WP Starter Theme\n * --------------------";
    $code .= "\n * Turn it to 'production' after build the Vue app\n */";
    $code .= "\ndefine( 'WP_ENVIRONMENT_TYPE', 'development' );";
    file_put_contents(ABSPATH . "/wp-config.php", $content . $code);
}
add_action('register_vuewp_theme', 'add_env_type');


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
    global $vuewp_dev_url;
    global $vuewp_theme_url;
    global $vuewp_scripts;
    global $vuewp_app_dir;
    $envType = wp_get_environment_type();
    if ($envType == 'production') {
        foreach ($vuewp_scripts as $sid => $script) {
            $url = "{$vuewp_theme_url}/{$vuewp_app_dir}/dist/{$script}";
            wp_enqueue_script($sid, $url);
        }
        $cssUrl = "{$vuewp_theme_url}/{$vuewp_app_dir}/dist/css/app.css";
        wp_enqueue_style("vp-vue-css-prod", $cssUrl);
    } else {
        foreach ($vuewp_scripts as $sid => $script) {
            wp_enqueue_script($sid, $vuewp_dev_url . "/" . $script);
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
    global $vuewp_theme_url;
    global $theme_options;
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
        "themeDirUrl" => $vuewp_theme_url,
        "siteUrl" => site_url(),
        "basePath" => $url_info['path'],
        "language" => get_locale(),
        "contentWidth" => $content_width,
        "settings" => $theme_options,
        "loggedUser" => $user,
        "wpApiSettings" => [
            "root" => esc_url_raw(rest_url()),
            "nonce" => wp_create_nonce('wp_rest'),
            "formats" => !empty($theme_options['post_formats'])
        ]
    ];
    ?>
    <script>
        window.vueWpThemeInfo = <?php print json_encode($ret); ?>;
    </script>
    <?php
}

/**
 * Custom logo support
 *
 * @return void
 */
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