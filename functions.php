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

// Settings
include_once get_stylesheet_directory() . "/settings/index.php";
$settings = new ThemeSettings('vuewp_settings');

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
        "language" => get_locale(),
        "settings" => get_vuewp_settings(),
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

function add_admin_js() {
    global $scriptsUrlProd;
    wp_enqueue_script("vuewp-admin-js", $scriptsUrlProd . "/admin.js");
}

function add_admin_css() {
    global $scriptsUrlProd;
    wp_enqueue_style("vuewp-admin-css", $scriptsUrlProd . "/admin.css");
}

function vuewp_admin_page() {
    $page_id = add_theme_page(
        __('Vue WP Theme options', 'vuewp'),
        __('Vue WP Theme', 'vuewp'),
        'manage_options',
        'vuewp-options',
        'admin_page'
    );
    add_action('admin_print_scripts-' . $page_id, 'add_admin_js');
    add_action('admin_print_styles-' . $page_id, 'add_admin_css' );
}
add_action('admin_menu', 'vuewp_admin_page');

function save_admin_page() {
    global $settings;
    $msg = '';
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'save-lang') {
            $ok = save_strings($_POST['lang'], $_POST['strings']);
            if ($ok) {
                $msg = sprintf(__('The file %s was successfully saved.', 'vuewp'), $_POST['lang'] . '.json');
            } else {
                $msg = sprintf(__('The file %s could not be saved, there was an error.', 'vuewp'), $_POST['lang'] . '.json');
            }
        }
        if ($_POST['action'] == 'create-lang') {
            create_file($_POST['lang']);
            $ok = save_strings($_POST['lang'], $_POST['strings']);
            if ($ok) {
                $msg = sprintf(__('The file %s was successfully create.', 'vuewp'), $_POST['lang'] . '.json');
            } else {
                $msg = sprintf(__('The file %s could not be create, there was an error.', 'vuewp'), $_POST['lang'] . '.json');
            }
        }
        if ($_POST['action'] == 'save-settings') {
            $settings->save($_POST['settings']);
            $msg = __('Settings updated successfully.', 'vuewp');
        }
    }
    return $msg;
}

function create_file($lang) {
    $path = get_template_directory() . '/vue-app/src/I18n/langs/' . $lang . '.json';
    if (!file_exists($path)) {
        $fh = fopen($path, "w");
        fclose($fh);
    }
}

function save_strings($lang, $json) {
    $path = get_template_directory() . '/vue-app/src/I18n/langs/' . $lang . '.json';
    $valid = json_decode(stripslashes($json), true);
    if (!is_array($valid)) {
        return false;
    }
    $str = json_encode($valid, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return !!file_put_contents($path, $str);
}

function admin_page() {
    global $settings;
    $msg = save_admin_page();
    $lang_info = readStrings();
    $strings = $lang_info['strings'];
    $lang_codes = array_keys($strings);
    $languages = [];
    foreach ($lang_codes as $code) {
        $languages[$code] = $code;
        if (!empty($strings[$code]['language_name'])) {
            $languages[$code] = $strings[$code]['language_name'];
        }
    }
    ?>
    <div class="wrap">
        <h1><?php _e('Vue WP Theme options', 'vuewp'); ?></h1>
        <?php if (!empty($msg)) { ?>
            <div id="message" class="notice notice-success settings-error is-dismissible">
                <p>
                    <strong><?php print $msg; ?></strong>
                </p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text">Dismiss this notice.</span>
                </button>
            </div>
        <?php } ?>
        <div class="settings">
            <form action="themes.php?page=vuewp-options" method="post" id="vuewp-form">
                <div class="tabs" data-tab="settings">
                    <div class="tab-links">
                        <a class="tab" href="#" data-tab="settings">
                            <?php _e('Options', 'vuewp'); ?>
                        </a>
                        <a class="tab" href="#" data-tab="translations">
                            <?php _e('Translations', 'vuewp'); ?>
                        </a>
                    </div>
                    <div class="tab-stage">
                        <div class="tab-content" data-tab="settings">
                            <h2><?php _e('Theme options', 'vuewp'); ?></h2>
                            <p>
                                <?php printf(__('These options will be available for all components in the Vue app as %s.', 'vuewp'), '<code>this.info.settings</code>'); ?>
                                <br />
                                <?php printf(__('You can edit the file %s and add some new settings as per your theme\'s needs.', 'vuewp'), '<em>settings/theme-settings.php</em>'); ?>
                            </p>
                            <?php $settings->render(); ?>
                            <div class="formline buttons">
                                <button id="save_settings" class="button button-primary">
                                    <?php _e('Save options', 'vuewp'); ?>
                                </button>
                            </div>
                        </div>
                        <div class="tab-content" data-tab="translations">
                            <h2><?php _e('Frontend translations', 'vuewp'); ?></h2>
                            <p>
                                <?php _e('You can edit all translatable strings from here.', 'vuewp'); ?>
                                <?php _e('If you created some new translatable strings, they will be present and marked in red. The strings displayed below will always be up to date with the application code.', 'vuewp'); ?>
                            </p>
                            <div class="formline">
                                <label for="langs"><?php _e('Select a language to edit', 'vuewp'); ?></label>
                                <select name="langs" id="langs" class="half">
                                    <?php foreach ($languages as $lcode => $lname) { ?>
                                        <option value="<?php print $lcode; ?>"><?php print $lname; ?></option>
                                    <?php } ?>
                                </select>
                                <button type="button" class="button" id="edit_button"><?php _e('Edit', 'vuewp'); ?></button>
                            </div>
            
                            <div class="formline">
                                <label for="new_code"><?php _e('Create a new language file', 'vuewp'); ?></label>
                                <input class="half" type="text" name="new_code" id="new_code" placeholder="<?php _e('Language code', 'vuewp'); ?>">
                                <input class="half" type="text" name="new_name" id="new_name" placeholder="<?php _e('Language name', 'vuewp'); ?>">
                                <button type="button" class="button" id="create_button"><?php _e('Create', 'vuewp'); ?></button>
                            </div>
            
                            <div class="translator" style="display: none;">
                                <div class="strings"></div>
                                <div class="editor">
                                    <div class="key-string">
                                        <textarea id="key-string" readonly></textarea>
                                    </div>
                                    <div class="value-string">
                                        <textarea id="value-string"></textarea>
                                    </div>
                                    <div class="buttons">
                                        <input type="hidden" id="action" name="action">
                                        <input type="hidden" id="lang" name="lang">
                                        <input type="hidden" id="strings" name="strings">
                                        <button class="button" type="button" id="cancel_saving">
                                            <?php _e('Cancel', 'vuewp'); ?>
                                        </button>
                                        <button class="button button-primary" type="button" id="save_language">
                                            <?php _e('Save', 'vuewp'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="strings-source" style="display: none;">
                    <?php foreach ($strings as $lng => $strs) { ?>
                        <div class="lang-set" data-lang="<?php print $lng; ?>">
                            <h3><?php printf(__('Editing language file &apos;%s.json&apos; (%s)', 'vuewp'), $lng, $languages[$lng]); ?></h3>
                            <?php foreach ($strs as $key => $val) { ?>
                                <?php $new_item = in_array($key, $lang_info['new_items'][$lng]); ?>
                                <div class="str-line<?php if ($new_item) print ' not-saved' ?>">
                                    <span class="key"><?php print $key; ?></span>
                                    <span class="val"><?php print $val; ?></span>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
    <?php
}

function rest_get_term(WP_REST_Request $request) {
    $tax = $request->get_param('tax');
    $term = $request->get_param('term');
    $obj = get_term($term, $tax);
    $tax = get_taxonomy($obj->taxonomy);
    $obj->post_type = $tax->object_type[0];
    return new WP_REST_Response($obj, 200);
}

function get_menus() {
    $menus = get_terms('nav_menu', array('hide_empty' => true));
    return array_map(function($a) { return (array) $a; }, $menus);
}

function rest_get_menu_items(WP_REST_Request $request) {
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
        'callback' => 'rest_get_menu_items'
    ]);
    register_rest_route('vuewp/v1', '/term/(?P<tax>[a-z-_]+)/(?P<term>\d+)', [
        'methods' => 'GET',
        'callback' => 'rest_get_term'
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

function readStrings() {
    $app_dir = get_stylesheet_directory() . '/vue-app';
    $lang_files = listFiles($app_dir . '/src/I18n/langs');
    $components = listFiles($app_dir . '/src/components');
    $views = listFiles($app_dir . '/src/views');
    $files = array_merge($components, $views);

    $code = ["language_name" => ""];
    foreach ($files as $file) {
        $content = file_get_contents($file);
        preg_match_all("/\bt[pl]?\([\'\"]([^\'\"]+)[\'\"][^)]*\)/", $content, $matches);
        foreach ($matches[1] as $str) {
            $code[$str] = "";
        }
    }

    $new_items = [];
    $all = [];
    foreach ($lang_files as $lfile) {
        $transl = (array) json_decode(file_get_contents($lfile));
        $lang = str_replace(".json", "", basename($lfile));
        $all[$lang] = $code;
        $new_items[$lang] = [];
        foreach ($transl as $key => $value) {
            if (isset($code[$key])) {
                $all[$lang][$key] = $value;
            }
        }
        if (count($transl) < count($code)) {
            foreach ($code as $key => $value) {
                if (!isset($transl[$key])) {
                    $new_items[$lang][] = $key;
                }
            }
        }
    }
    return [
        "strings" => $all,
        "new_items" => $new_items
    ];
}

// list the files on the givem directory
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