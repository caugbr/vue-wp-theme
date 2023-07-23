<?php

class RoutesVue {

    var $src;
    var $views;
    var $routes;

    public function __construct($theme_dir, $app_dir) {
        $this->src = "{$theme_dir}/{$app_dir}/src";
        $this->get_vue_views();
        $this->get_routes();
    }

    public function save_routes($routes) {
        return update_option('vuewp_routes', $routes);
    }

    public function get_routes() {
        $routes = get_option('vuewp_routes');
        if (!is_array($routes) || empty($routes)) {
            return [
                [
                    "path" => "/",
                    "component" => "Home"
                ],
                [
                    "path" => "/posts",
                    "component" => "PostArchive"
                ],
                [
                    "path" => "/posts/:slug",
                    "component" => "Post"
                ],
                [
                    "path" => "/pages",
                    "component" => "PageArchive"
                ],
                [
                    "path" => "/pages/:slug",
                    "component" => "Page"
                ],
                [
                    "path" => "/:postType/:taxonomy/:term",
                    "component" => "TaxonomyArchive"
                ],
                [
                    "path" => "/search/:term",
                    "component" => "SearchResults"
                ],
                [
                    "path" => "/:pathMatch(.*)*",
                    "component" => "NotFound"
                ]
            ];
        }
        return $this->routes = $routes;
    }

    public function get_vue_views() {
        $views = [];
        $view_files = listFiles("{$this->src}/views");
        foreach ($view_files as $view) {
            $code = file_get_contents($view);
            $name = basename($view);
            $slug = str_replace(".vue", "", $name);
            $views[$slug] = [
                "filePath" => $view,
                "fileName" => $name,
                "componentPath" => "../views/{$name}",
                "componentName" => $slug
            ];
            if (preg_match("/\broute_params:\s*[\"']([^\"']+)[\"']/", $code, $m)) {
                $views[$slug]["params"] = preg_split("/\s*,\s*/", $m[1]);
            }
        }
        return $this->views = $views;
    }

    public function render_form() {
        $components = array_map(function($e) {
            return [
                "label" => $e['componentName'],
                "value" => $e['componentName']
            ];
        }, $this->views);
        $routes = $this->get_routes();
        ?>
        <script>
            window.vuewpViews = <?php print json_encode($this->views); ?>;
            window.vuewpRoutes = <?php print json_encode($routes); ?>;
        </script>
        <h3><?php _e('Add route', 'vuewp'); ?></h3>
        <div class="formline">
            <label for="route-component">
                <?php _e('View to handle route', 'vuewp'); ?>
            </label>
            <select id="route-component">
                <option value=""><?php _e('Select the view', 'vuewp'); ?></option>
                <?php foreach($components as $comp) { ?>
                <option value="<?php print $comp['value'] ?>">
                    <?php print $comp['label']; ?>
                </option>
                <?php } ?>
            </select>
        </div>
        <div class="formline buttons">
            <label for="route-path">
                <?php _e('Path', 'vuewp'); ?>
            </label>
            <input class="" type="text" id="route-path" value="/">
            <div class="route-variables"></div>
        </div>
        <div class="formline buttons">
            <button class="button button-secondary" type="button" id="add-route">
                <?php _e('Add', 'vuewp'); ?>
            </button>
            <div class="route-error"></div>
        </div>

        <h3><?php _e('Current routes', 'vuewp'); ?></h3>
        <div class="current-routes"></div>
        <input type="hidden" name="routes" id="routes">
        <?php
    }

    public function write_file() {
        $routes = $this->get_routes();
        $views = $this->get_vue_views();
        $imports = [];
        $c = "import Vue from 'vue';\n";
        $c .= "import VueRouter from 'vue-router';\n\n";
        $c .= "%IMPORTS%\n\n";
        $c .= "Vue.use(VueRouter);\n\n";
        $c .= "const basePath = window.vueWpThemeInfo.basePath;\n";
        $c .= "const routes = [\n";
        foreach ($routes as $route) {
            $base = 'basePath + ';
            if ($route['path'] == '/') {
                $base = 'basePath ? basePath : ';
            }
            $c .= "    { path: {$base}'{$route['path']}', name: ";
            $c .= "'{$route['component']}', component: {$route['component']} },\n";
            $imports[] = "import {$views[$route['component']]['componentName']} " .
                         "from '{$views[$route['component']]['componentPath']}';";
        }
        $c .= "];\n\n";
        $c .= "const router = new VueRouter({ mode: 'history', routes });\n\n";
        $c .= "export default router;";

        $c = str_replace("%IMPORTS%", join("\n", $imports), $c);

        $file = "{$this->src}/router/index.js";
        file_put_contents($file, $c);
    }
}