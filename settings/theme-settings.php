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
    ],
    "post_formats" => [
        "type" => "checkbox-group",
        "options" => "all_post_formats",
        "description" => __('Select the post formats that will have support in your theme.', 'vuewp'),
        "label" =>  __('Post formats support', 'vuewp'),
        "default_value" => ''
    ],
    // Other possible types...
    // "text_field" => [
    //     "type" => "text", // one of text|email|url|password|tel|number|search
    //     "description" => __('Test field', 'vuewp'),
    //     "label" =>  __('Type something', 'vuewp'),
    //     "default_value" => ''
    // ],
    // "area_field" => [
    //     "type" => "textarea",
    //     "description" => __('Test area field', 'vuewp'),
    //     "label" =>  __('Type something', 'vuewp'),
    //     "default_value" => 'bah'
    // ],
    // "radio_list" => [
    //     "type" => "radio-group",
    //     "description" => __('Choose one', 'vuewp'),
    //     "label" =>  __('Radio group', 'vuewp'),
    //     "options" => [
    //         [ "label" => "left", "value" => "left" ],
    //         [ "label" => "right", "value" => "right" ],
    //         [ "label" => "none", "value" => "none" ]
    //     ],
    //     "default_value" => ''
    // ]
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
        [
            "label" => 'Aside',
            "value" => 'aside'
        ],
        [
            "label" => 'Gallery',
            "value" => 'gallery'
        ],
        [
            "label" => 'Link',
            "value" => 'link'
        ],
        [
            "label" => 'Image',
            "value" => 'image'
        ],
        [
            "label" => 'Quote', 
            "value" => 'quote'
        ],
        [
            "label" => 'Status',
            "value" => 'status'
        ],
        [
            "label" => 'Video',
            "value" => 'video'
        ],
        [
            "label" => 'Audio',
            "value" => 'audio'
        ],
        [
            "label" => 'Chat',
            "value" => 'chat'
        ],
    ];
}