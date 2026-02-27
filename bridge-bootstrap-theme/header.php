<?php
/**
 * Header template.
 *
 * @package BridgeBootstrapMinimal
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header" role="banner">
    <div class="container header-inner">
        <a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
        <button class="menu-toggle" aria-expanded="false" aria-controls="primaryNav">
            <?php esc_html_e('Menu', 'bridge-bootstrap-minimal'); ?>
        </button>
        <?php
        wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => 'nav',
            'container_id'   => 'primaryNav',
            'container_class'=> 'primary-nav',
            'menu_class'     => 'menu-list',
            'fallback_cb'    => false,
        ]);
        ?>
    </div>
</header>
