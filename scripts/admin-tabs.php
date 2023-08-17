<?php



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

function server_tab() {
    $build = get_build();
    ?>
    <h2><?php _e('Server info', 'vuewp'); ?></h2>
    <div class="infoline">
        <strong><?php _e('Environment', 'vuewp'); ?>:</strong>
        <span class="env-status"><?php print wp_get_environment_type(); ?></span>
    </div>
    <div class="infoline">
        <strong><?php _e('Development server', 'vuewp'); ?>:</strong>
        <span class="server-status"><?php _e('checking...', 'vuewp'); ?></span>
    </div>
    <div class="infoline">
        <strong><?php _e('Last build', 'vuewp'); ?>:</strong>
        <span class="last-build"><?php print $build ? $build : 'no build found'; ?></span>
    </div>
    <p class="server-msg"></p>
    <?php
}

function check_vue_server() {
    global $vuewp_dev_url;
    $ret = wp_remote_head($vuewp_dev_url);
    wp_send_json([ "status" => is_wp_error($ret) ? 'stoped' : 'running' ], 200);
    wp_die();
}
add_action('wp_ajax_check_server', 'check_vue_server');

function get_build() {
    global $vuewp_theme_dir;
    global $vuewp_app_dir;
    $path = $vuewp_theme_dir . "/" . $vuewp_app_dir . "/dist/index.html";
    if (file_exists($path)) {
        $time = filectime($path);
        return date("Y-m-d h:i:s", $time);
    }
    return false;
}