<?php

// Theme variables
$scriptsUrlDev = 'http://127.0.0.1:8080';
$themeDirUrl = get_template_directory_uri();
$themeDir = get_stylesheet_directory();
$vueScripts = [
    'wp-vue-app-js' => 'js/app.js',
    'wp-vue-vendors-js' => 'js/chunk-vendors.js'
];
$appDir = 'vue-app';

// Theme add support
add_theme_support('post-thumbnails');
add_theme_support('html5', ['style','script']);
add_theme_support('automatic-feed-links');
add_theme_support('widgets');

// Remove redirects
remove_action('template_redirect', 'redirect_canonical');

// Settings
include_once $themeDir . "/settings/index.php";
$settings = new ThemeSettings('vuewp_settings');

// REST API custom endpoints
include_once $themeDir . "/scripts/extend-rest-api.php";

// Add custom widgets
include_once $themeDir . "/scripts/widgets.php";

// Add translation functions
include_once $themeDir . "/scripts/translation-functions.php";

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
 * Add some values to vueWpThemeInfo variable
 *
 * @return string
 */
function get_vue_info() {
    global $themeDirUrl;
    global $settings;
	global $current_user;
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

function get_slug_by_id($itm) {
    if ($itm->type == "post_type") {
        $post = get_post((int) $itm->object_id);
        return $post->post_name;
    }
    if ($itm->type == "taxonomy") {
        $tax = get_taxonomy($itm->object);
        $term = get_term((int) $itm->object_id, $itm->object);
        return [
            "post_type" => $tax->object_type[0],
            "tax_slug"  => $tax->rewrite['slug'],
            "tax_name"  => $tax->name,
            "term_slug" => $term->slug,
            "term_id"   => $term->term_id
        ];
    }
    if ($itm->type == "post_type_archive") {
        $slug = $itm->object;
        if (preg_match("/^(post|page|video)$/", $slug)) {
            $slug .= "s";
        }
        return $slug;
    }
    return '';
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
