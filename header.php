<?php status_header(200); ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">

    <head>
        <?php
        if (!function_exists('_wp_render_title_tag')) {
            function theme_slug_render_title() {
                ?><title><?php wp_title('|', true, 'right'); ?></title><?php
            }
            add_action('wp_head', 'theme_slug_render_title');
        }
        ?>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <?php get_vue_info(); ?>
        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>
        <?php wp_body_open(); ?>
