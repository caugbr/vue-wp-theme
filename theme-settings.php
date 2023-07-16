<?php

$theme_settings = [
    "use_wp_lang" => [
        "type" => "checkbox",
        "description" => __('Use the Wordpress language in your site (or define it directly in Vuex store)', 'vuewp'),
        "label" =>  __('Use WP language', 'vuewp'),
        "default_value" => '1'
    ],
    "header_menu" => [
        "type" => "select",
        "options" => "get_menus_as_options",
        "description" => __('Select the WP menu that appears in site header. Create a new one in Appearence &gt; Costumize &gt; Menus.', 'vuewp'),
        "label" =>  __('WP menu in site header', 'vuewp'),
        "default_value" => ''
    ],
    "sidebar_menu" => [
        "type" => "select",
        "options" => "get_menus_as_options",
        "description" => __('Select the WP menu that appears in sidebar. Create a new one in Appearence &gt; Costumize &gt; Menus.', 'vuewp'),
        "label" =>  __('WP menu in sidebar', 'vuewp'),
        "default_value" => ''
    ],
    "sidebar_location" => [
        "type" => "select",
        "options" => [
            [ "label" => __("On the left", "vuewp"), "value" => "left" ],
            [ "label" => __("On the right", "vuewp"), "value" => "right" ],
            [ "label" => __("Don't use sidebar", "vuewp"), "value" => "none" ]
        ],
        "description" => __('Define where to show sidebar', 'vuewp'),
        "label" =>  __('Sidebar position', 'vuewp'),
        "default_value" => 'left'
    ]
];

function get_menus_as_options() {
    $ret = [ [ "label" => "None", "value" => "" ] ];
    foreach (get_menus() as $opt) {
        $ret[] = [
            "label" => $opt['name'],
            "value" => $opt['slug']
        ];
    }
    return $ret;
}