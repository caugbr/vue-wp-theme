<?php

$theme_settings = [
    "use_wp_lang" => [
        "type" => "switch",
        "description" => __('Use the Wordpress language in your site (or define it directly in Vuex store)', 'vuewp'),
        "label" =>  __('Use WP language', 'vuewp'),
        "default_value" => '1'
    ],
    "hide_wp_bar" => [
        "type" => "switch",
        "description" => __('Hide Wordpress admin bar on frontend overriding user setting', 'vuewp'),
        "label" =>  __('Hide WP admin bar', 'vuewp'),
        "default_value" => '0'
    ],
    "header_menu" => [
        "type" => "select",
        "options" => "get_menus_as_options",
        "description" => __('Select the WP menu that appears in site header. Create a new one in Appearence &gt; Costumize &gt; Menus.', 'vuewp'),
        "label" =>  __('WP menu in header', 'vuewp'),
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
    ],
    "post_formats" => [
        "type" => "checkbox-group",
        "options" => "all_post_formats",
        "description" => __('Select the post formats that will have support in your theme.', 'vuewp'),
        "label" =>  __('Post formats support', 'vuewp'),
        "default_value" => ''
    ]
];

function get_menus() {
    $menus = get_terms('nav_menu', array('hide_empty' => true));
    return array_map(function($a) { return (array) $a; }, $menus);
}

function get_menus_as_options() {
    $ret = [ [ "label" => __("None", 'vuewp'), "value" => "" ] ];
    foreach (get_menus() as $opt) {
        $ret[] = [
            "label" => $opt['name'],
            "value" => $opt['slug']
        ];
    }
    return $ret;
}

function all_post_formats() {
    return [
        [ "label" => __('Aside'), "value" => 'aside' ],
        [ "label" => __('Gallery'), "value" => 'gallery' ],
        [ "label" => __('Link'), "value" => 'link' ],
        [ "label" => __('Image'), "value" => 'image' ],
        [ "label" => __('Quote'),  "value" => 'quote' ],
        [ "label" => __('Status'), "value" => 'status' ],
        [ "label" => __('Video'), "value" => 'video' ],
        [ "label" => __('Audio'), "value" => 'audio' ],
        [ "label" => __('Chat'), "value" => 'chat' ]
    ];
}