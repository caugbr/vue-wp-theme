<?php

global $scriptsUrlDev;
global $themeDirUrl;
global $themeDir;
global $vueScripts;
global $appDir;
$scriptsUrlDev = 'http://127.0.0.1:8080';
$themeDirUrl = get_template_directory_uri();
$themeDir = get_stylesheet_directory();
$vueScripts = [
    'wp-vue-app-js' => 'js/app.js',
    'wp-vue-vendors-js' => 'js/chunk-vendors.js'
];
$appDir = 'vue-app';

global $vuewp_areas;
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

add_theme_support('post-thumbnails');
add_theme_support('html5', ['style','script']);
add_theme_support('automatic-feed-links');
add_theme_support('widgets');

// Remove redirects
remove_action('template_redirect', 'redirect_canonical');
// function remove_redirects() {
//     add_rewrite_rule('^/(.+)/?', 'index.php', 'top');
// }
// add_action('init', 'remove_redirects');

// Settings
include_once $themeDir . "/settings/index.php";
$settings = new ThemeSettings('vuewp_settings');

// REST API custom endpoints
include_once $themeDir . "/extend-rest-api.php";

/**
 * Register widget areas
 */
function vuewp_widget_areas() {
    global $vuewp_areas;
    foreach ($vuewp_areas as $area) {
        register_sidebar($area);
    }
}
add_action('widgets_init', 'vuewp_widget_areas');

/**
 * Print HTML for wp widget areas
 *
 * @return void
 */
function vuewp_add_areas() {
    global $vuewp_areas;
    ?>
    <div id="wp-sidebars" style="display: none;">
        <?php foreach ($vuewp_areas as $area) { ?>
            <?php if (is_active_sidebar($area['id'])) { ?>
            <div data-area-id="<?php print $area['id']; ?>">
                <?php dynamic_sidebar($area['id']); ?>
            </div>
            <?php } ?>
        <?php } ?>
    </div>
    <?php
}

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

function add_admin_js() {
    global $themeDirUrl;
    wp_enqueue_script("vuewp-admin-js", $themeDirUrl . "/admin.js");
}

function add_admin_css() {
    global $themeDirUrl;
    wp_enqueue_style("vuewp-admin-css", $themeDirUrl . "/admin.css");
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
    global $appDir;
    $path = get_template_directory() . $appDir . '/src/I18n/langs/' . $lang . '.json';
    if (!file_exists($path)) {
        $fh = fopen($path, "w");
        fclose($fh);
    }
}

function save_strings($lang, $json) {
    global $appDir;
    $path = get_template_directory() . '/' . $appDir . '/src/I18n/langs/' . $lang . '.json';
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
                                <?php printf(__('You can edit the file %s and add some new settings as per your theme\'s needs.', 'vuewp'), '<code>settings/theme-settings.php</code>'); ?>
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
    global $themeDir;
    global $appDir;
    $app_dir = $themeDir . '/' . $appDir;
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

/**
 * Adds Copyright widget.
 */
class Copyright extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'copyright',
			'Copyright',
			['description' => __('A Foo Widget', 'vuewp')]
		);
	}

	public function widget($args, $instance) {
		extract($args);
		$name = $instance['name'];
		print $before_widget;
		if (!empty($name)) {
            $year = date('Y');
			print "&copy;{$year} {$name}";
		}
		print $after_widget;
	}

	public function form($instance) {
		if (isset( $instance['name'])) {
			$name = $instance['name'];
		} else {
			$name = __('Owner name', 'vuewp');
		}
		?>
		<p>
			<label for="<?php print $this->get_field_name('name'); ?>"><?php _e('Name:'); ?></label>
			<input class="widefat" id="<?php print $this->get_field_id('name'); ?>" name="<?php print $this->get_field_name('name'); ?>" type="text" value="<?php print esc_attr($name); ?>" />
		 </p>
		<?php
	}

	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['name'] = (!empty( $new_instance['name'])) ? strip_tags($new_instance['name']) : '';
		return $instance;
	}
}
add_action('widgets_init', 'vuewp_register_widgets');
function vuewp_register_widgets() {
	register_widget('Copyright');
}