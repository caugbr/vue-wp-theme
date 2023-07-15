<?php

global $scriptsUrlDev;
global $scriptsUrlProd;
global $loadScripts;
$scriptsUrlDev = 'http://127.0.0.1:8080';
$scriptsUrlProd = get_template_directory_uri();
$loadScripts = [
    'wp-vue-app-js' => 'js/app.js',
    'wp-vue-vendors-js' => 'js/chunk-vendors.js'
];

// Remove redirects
function remove_redirects() {
    add_rewrite_rule('^/(.+)/?', 'index.php', 'top');
}
add_action('init', 'remove_redirects');
remove_action('template_redirect', 'redirect_canonical');

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
    global $scriptsUrlProd;
    global $loadScripts;
    $envType = wp_get_environment_type();
    if ($envType == 'production') {
        foreach ($loadScripts as $sid => $script) {
            wp_register_script($sid, $scriptsUrlProd . "/vue-app/dist/" . $script);
            wp_enqueue_script($sid);
        }
        wp_enqueue_style("vp-vue-css-prod", $scriptsUrlProd . "/vue-app/dist/css/app.css");
    } else {
        foreach ($loadScripts as $sid => $script) {
            wp_register_script($sid, $scriptsUrlDev . "/" . $script);
            wp_enqueue_script($sid);
        }
    }
}
add_action('wp_footer', 'enqueue_scripts');

/**
 * Add some values to wpVue variable
 *
 * @return string
 */
function get_vue_info() {
    global $scriptsUrlProd;
    $url_info = parse_url(site_url());
    $ret = [
        "themeDirUrl" => $scriptsUrlProd,
        "siteUrl" => site_url(),
        "basePath" => $url_info['path'],
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



function videos_admin_page() {
    add_options_page(
        __('Vue WP Theme options', 'vuewp'),
        __('Vue WP Theme', 'vuewp'),
        'manage_options',
        'vuewp-options',
        'admin_page'
    );
}
add_action('admin_menu', 'videos_admin_page');

function save_admin_page() {
    $msg = '';
    if (count($_POST) > 0) {
        if ($_POST['act'] == 'save-config') {
            update_option('vbr_providers', $_POST['providers'] ?? []);
            $msg = __('Configuration successfully saved', 'vuewp');
        }
    }
    return $msg;
}

function admin_page() {
    ?>
    <div class="wrap">
        <?php if (!empty($msg)) { ?>
            <div class="message"><?php print $msg; ?></div>
        <?php } ?>
        <h1><?php _e('Vue WP Theme options', 'vuewp') ?></h1>
        <?php
            $menus = get_menus();
            print_r($menus);
        ?>
    </div>
    <?php
}

function get_menus() {
    $menus = get_terms('nav_menu', array('hide_empty' => true));
    return array_map(function($a) { return (array) $a; }, $menus);
}

function get_menu_items(WP_REST_Request $request) {
    $menuID = $request->get_param('slug');
    $items = wp_get_nav_menu_items($menuID);
    $response = [];
    foreach ($items as $itm) {
        $arr = [
            "id" => $itm->object_id,
            "title" => $itm->title,
            "url" => $itm->url,
            "type" => $itm->type,
            "object_type" => $itm->object,
            "slug" => get_slug_by_id($itm)
        ];
        $response[] = $arr;
    }
    return new WP_REST_Response($response, 200);
}

add_action('rest_api_init', function() {
    register_rest_route('vuewp/v1', '/menu/(?P<slug>[a-z-]+)', [
        'methods' => 'GET',
        'callback' => 'get_menu_items'
    ]);
});

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