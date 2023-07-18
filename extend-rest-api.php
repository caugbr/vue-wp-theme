<?php

$vuewp_namespace = 'vuewp/v1';

add_action('rest_api_init', function() {
    global $vuewp_namespace;
    register_rest_route($vuewp_namespace, '/menu/(?P<slug>[a-z-]+)', [
        'methods' => 'GET',
        'callback' => 'rest_get_menu_items'
    ]);
    register_rest_route($vuewp_namespace, '/term/(?P<tax>[a-z-_]+)/(?P<term>\d+)', [
        'methods' => 'GET',
        'callback' => 'rest_get_term'
    ]);
    register_rest_route($vuewp_namespace, '/search/(?P<term>\w+)', [
        'methods' => 'GET',
        'callback' => 'rest_search'
    ]);
});

function rest_get_term(WP_REST_Request $request) {
    $tax = $request->get_param('tax');
    $term = $request->get_param('term');
    $obj = get_term($term, $tax);
    $tax = get_taxonomy($obj->taxonomy);
    $obj->post_type = $tax->object_type[0];
    return new WP_REST_Response($obj, 200);
}

function rest_search(WP_REST_Request $request) {
    $term = $request->get_param('term');
    $args = [
        "post_type" => "any",
        "s" => $term
    ];
    $posts = get_posts($args);
    $results = [];
    foreach ($posts as $p) {
        // print_r($p);
        $thumbnail_id = get_post_thumbnail_id($p);
        $thumbnail = wp_get_attachment_image_src($thumbnail_id);
        $results[] = [
            "id" => $p->ID,
            "title" => $p->post_title,
            "slug" => $p->post_name,
            "type" => $p->post_type,
            "thumbnail" => $thumbnail[0] ?? ''
        ];
    }
    return new WP_REST_Response($results, 200);
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
