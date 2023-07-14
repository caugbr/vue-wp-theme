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