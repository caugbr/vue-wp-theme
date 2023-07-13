<?php
// Remove all default WP template redirects/lookups
remove_action('template_redirect', 'redirect_canonical');

// Redirect all requests to index.php so the Vue app is loaded and 404s aren't thrown
function remove_redirects() {
	add_rewrite_rule('^/(.+)/?', 'index.php', 'top');
}
add_action('init', 'remove_redirects');

global $scriptsUrlDev;
global $scriptsUrlProd;
global $loadScripts;
$scriptsUrlDev = 'http://127.0.0.1:8080';
$scriptsUrlProd = get_template_directory_uri();
$loadScripts = [
	'wp-vue-app-js' => 'js/app.js',
	'wp-vue-vendors-js' => 'js/chunk-vendors.js'
];

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
		window.apiInfo = <?php print json_encode($ret); ?>;
	</script>
	<?php
}


// Only logged in users
// include_once "only-loggedin/index.php";

// function videos_header($title = 'Rare connect', $subtitle = 'Plataforma de vídeos', $icon = 'fa-circle-info') {
	/* ?>
		<div class="videos-header">
			<div class="title-area">
				<h1><?php print $title; ?></h1>
				<strong><?php print $subtitle; ?></strong>
			</div>
			<div class="button-area">
				<button type="button" class="btn-primary header-button">
					<i class="fa-solid <?php print $icon; ?>" aria-hidden="true"></i>
				</button>
			</div>
		</div>
	<?php */
// }