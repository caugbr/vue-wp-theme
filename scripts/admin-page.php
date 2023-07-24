<?php

function add_admin_js() {
    global $vuewp_theme_url;
    wp_enqueue_script("vuewp-admin-js", $vuewp_theme_url . "/admin-assets/admin.js");
}

function add_admin_css() {
    global $vuewp_theme_url;
    wp_enqueue_style("vuewp-admin-css", $vuewp_theme_url . "/admin-assets/admin.css");
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
    global $translation;
    global $settings;
    global $vue_routes;
    $msg = '';
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
        if ($_POST['action'] == 'save-settings') {
            $settings->save($_POST['settings']);
            $msg = __('Settings updated successfully.', 'vuewp');
        }
        if ($_POST['action'] == 'save-routes') {
            $routes = json_decode(stripslashes($_POST['routes']), true);
            $vue_routes->write_file($routes);
            $msg = __('Routes updated successfully.', 'vuewp');
        }
    }
    return $msg;
}

function admin_page() {
    global $settings;
    global $translation;
    global $vue_routes;
    $msg = save_admin_page();
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
                        <a class="tab" href="#" data-tab="routes">
                            <?php _e('Routes', 'vuewp'); ?>
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
                            <?php $translation->translations_form(); ?>
                        </div>
                        <div class="tab-content" data-tab="routes">
                            <h2><?php _e('Routes', 'vuewp'); ?></h2>
                            <p>
                                <?php printf(__('The site routes are defined by Vue app, ignoring the original WP routes. You can edit it here or directly in Vue Router file (%s).', 'vuewp'), '<code>src/router/index.js</code>'); ?>
                            </p>
                            <?php $vue_routes->render_form(); ?>
                            <div class="formline buttons">
                                <button id="save-routes" class="button button-primary">
                                    <?php _e('Save routes', 'vuewp'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
}