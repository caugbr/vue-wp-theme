<?php

// ===================== edit ===================== //

// Theme variables

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

// Aditional image sizes
$vuewp_image_sizes = [
    [
        "name" => "list-image",
        "width" => 220,
        "height" => 120,
        "crop" => true
    ]
];

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

// Internal
$vuewp_theme_dir = get_stylesheet_directory();
$vuewp_theme_url = get_template_directory_uri();
$vuewp_scripts = [
    'wp-vue-app-js' => 'js/app.js',
    'wp-vue-vendors-js' => 'js/chunk-vendors.js'
];

// Settings
include_once $vuewp_theme_dir . "/theme-settings.php";
$theme_options = [];
foreach ($theme_settings as $id => $info) {
    $theme_options[$id] = $info['default_value'] ?? '';
}
// If plugin Settings Page Manager is active
if (class_exists('SettingsPage')) {
    $config = new SettingsPage(
        [
            "page_title" => __('Vue WP Theme options', 'vuewp'),
            "menu_title" => __('Vue WP Theme', 'vuewp'),
            "menu_slug" => 'vuewp-options',
            "option_name" => 'vuewp_settings',
            "base_url" => get_stylesheet_directory_uri(),
            "tabs" => [
                "translations" => [
                    "label" => "Translations",
                    "callback" => "translations_tab",
                    "action" => "save-translations",
                    "hide_button" => true
                ],
                "routes" => [
                    "label" => "Routes",
                    "callback" => "routes_tab",
                    "action" => "save-routes"
                ]
            ],
            "beforeunload_msg" => '',
            "__scripts" => [
                "vuewp-admin-js" => $vuewp_theme_url . "/admin-assets/admin.js"
            ],
            "__styles" => [
                "vuewp-admin-css" => $vuewp_theme_url . "/admin-assets/admin.css"
            ]
        ],
        $theme_settings
    );
    $theme_options = $config->get_saved();
}

// If plugin Settings Page Manager is not active, show an error notice
// If it is not installed, download from github and install it.
function check_plugin_notice() {
    $downloaded = false;
    if (!file_exists(ABSPATH . "/wp-content/plugins/settings-page-manager")) {
        download_spm();
        $downloaded = true;
    }
    global $pagenow;
    if (!is_plugin_active('settings-page-manager/index.php')) {
        print '<div class="notice notice-error"><p>';
        print 'Vue WP Theme depends on <strong>Settings Page Manager</strong> plugin';
        if ($pagenow == 'plugins.php') {
            if ($downloaded) {
                print '.<br>We have downloaded <strong>Settings Page Manager</strong> for you, but you are in plugins page, so you must reload the page to see it.';
            } else {
                print ', please activate it.';
            }
        } else {
            if ($downloaded) {
                print '.<br>We have downloaded <strong>Settings Page Manager</strong> for you, just activate it in <a href="plugins.php">plugins page</a>.';
            } else {
                print ', please activate it in <a href="plugins.php">plugins page</a>.';
            }
        }
        print '</p></div>';
    }
}
add_action('admin_notices', 'check_plugin_notice');

function download_spm() {
    $zip_url = 'https://github.com/caugbr/settings-page-manager/archive/refs/heads/main.zip';
    $file = ABSPATH . "/wp-content/uploads/settings-page-manager.zip";
    $wp_filesystem = filesystem();

    $data = wp_remote_get($zip_url);
    $zip = $data['body'];
    $wp_filesystem->put_contents($file, $zip);

    if (unzip_file($file, ABSPATH . "/wp-content/uploads/")) {
        unlink($file);
        $wp_filesystem->move(
            ABSPATH . "/wp-content/uploads/settings-page-manager-main", 
            ABSPATH . "/wp-content/plugins/settings-page-manager"
        );
        return true;
    } else {
        return false;
    }
}

function filesystem() {
    global $wp_filesystem;
    if (is_null($wp_filesystem)) {
        require_once ABSPATH . '/wp-admin/includes/file.php';
        WP_Filesystem();
    }
    return $wp_filesystem;
}

// Add theme supports
vuewp_init();

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

function translations_tab() {
    global $translation;
    ?>
    <h2><?php _e('Frontend translations', 'vuewp'); ?></h2>
    <p>
        <?php _e('You can edit all translatable strings from here.', 'vuewp'); ?>
        <?php _e('If you created some new translatable strings, they will be present and marked in red. The strings displayed below will always be up to date with the application code.', 'vuewp'); ?>
    </p>
    <?php $translation->translations_form(); ?>
    <?php
}

function save_translations($msg) {
    global $translation;
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'save-lang') {
            $ok = $translation->save_strings($_POST['lang'], $_POST['strings']);
            if ($ok) {
                $msg = sprintf(__('The file %s was successfully saved.', 'vuewp'), $_POST['lang'] . '.json');
            } else {
                $msg = sprintf(__('The file %s could not be saved, there was an error.', 'vuewp'), $_POST['lang'] . '.json');
            }
        }
        if ($_POST['action'] == 'create-lang') {
            $translation->create_file($_POST['lang']);
            $ok = $translation->save_strings($_POST['lang'], $_POST['strings']);
            if ($ok) {
                $msg = sprintf(__('The file %s was successfully create.', 'vuewp'), $_POST['lang'] . '.json');
            } else {
                $msg = sprintf(__('The file %s could not be create, there was an error.', 'vuewp'), $_POST['lang'] . '.json');
            }
        }
    }
    return $msg;
}
add_filter('save_admin_page_message', 'save_translations');

function routes_tab() {
    global $vue_routes;
    ?>
    <h2><?php _e('Routes', 'vuewp'); ?></h2>
    <p>
        <?php printf(__('The site routes are defined by Vue app, ignoring the original WP routes. You can edit it here or directly in Vue Router file (%s).', 'vuewp'), '<code>src/router/index.js</code>'); ?>
    </p>
    <?php $vue_routes->render_form(); ?>
    <?php
}

function save_routes($msg) {
    global $vue_routes;
    if (!empty($_POST['action']) && $_POST['action'] == 'save-routes') {
        $routes = json_decode(stripslashes($_POST['routes']), true);
        $vue_routes->write_file($routes);
        $msg = __('Routes updated successfully.', 'vuewp');
    }
    return $msg;
}
add_filter('save_admin_page_message', 'save_routes');

// Theme initialization
function vuewp_init() {
    global $vuewp_image_sizes;
    global $vuewp_logo;
    global $theme_options;
    global $vuewp_theme_dir;

    // Translations
    load_theme_textdomain("vuewp", $vuewp_theme_dir . '/languages');

    // add aditional image sizes
    if (count($vuewp_image_sizes)) {
        foreach ($vuewp_image_sizes as $size) {
            add_image_size($size['name'], $size['width'], $size['height'], $size['crop']);
        }
    }
    
    // Add theme support
    $post_formats = $theme_options['post_formats'] ?? '';
    if (is_array($post_formats) && count($post_formats)) {
        add_theme_support('post-formats', $post_formats);
    }
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['style','script']);
    add_theme_support('widgets');
    add_theme_support('custom-logo', $vuewp_logo);

    // Remove redirects
    remove_action('template_redirect', 'redirect_canonical');
    
    // Disable 404 errors
    add_filter("pre_handle_404", "__return_false");
    
    // Hide admin bar
    if ($theme_options['hide_wp_bar']) {
        add_filter("show_admin_bar", "__return_false");
    }
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