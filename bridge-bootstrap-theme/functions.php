<?php
/**
 * Theme setup and performance-focused loading.
 *
 * @package BridgeBootstrapMinimal
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Set up theme defaults and supports.
 */
function bridge_bootstrap_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('responsive-embeds');
    add_theme_support('custom-logo');

    register_nav_menus([
        'primary' => __('Primary Menu', 'bridge-bootstrap-minimal'),
    ]);
}
add_action('after_setup_theme', 'bridge_bootstrap_setup');

/**
 * Remove unnecessary default front-end features for better performance.
 */
function bridge_bootstrap_cleanup_wp_head(): void
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'wp_generator');
}
add_action('init', 'bridge_bootstrap_cleanup_wp_head');

/**
 * Inline tiny critical CSS to avoid render blocking.
 */
function bridge_bootstrap_print_critical_css(): void
{
    echo '<style id="bridge-critical-css">:root{--bridge-content-max:860px;--bridge-border:#dfe3e8;--bridge-bg:#fff;--bridge-subtle:#f8f9fa;--bridge-text:#212529;--bridge-muted:#6c757d}*,:before,:after{box-sizing:border-box}html{font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;line-height:1.5}body{margin:0;color:var(--bridge-text);background:var(--bridge-bg)}.container{max-width:var(--bridge-content-max);margin-inline:auto;padding-inline:1rem}.site-header{border-bottom:1px solid var(--bridge-border);background:var(--bridge-subtle)}.header-inner{min-height:64px;display:flex;align-items:center;gap:1rem;justify-content:space-between}.brand{text-decoration:none;color:inherit;font-weight:700}.menu-toggle{font:inherit;border:1px solid var(--bridge-border);background:#fff;padding:.45rem .75rem;border-radius:.35rem;display:none}.primary-nav .menu-list{display:flex;list-style:none;gap:1rem;margin:0;padding:0}.primary-nav a{text-decoration:none;color:inherit}.site-main{padding-block:1.5rem}.entry-title{font-size:clamp(1.45rem,2vw,1.9rem);line-height:1.25;margin:0 0 .5rem}.entry-title a{text-decoration:none;color:inherit}.entry-meta{color:var(--bridge-muted);font-size:.875rem;margin:0 0 1rem}.entry-image{width:100%;height:auto;display:block;border-radius:.35rem}.post-card{margin-bottom:2.5rem}.site-footer{border-top:1px solid var(--bridge-border);padding:1rem 0;background:var(--bridge-subtle)}.footer-inner{display:flex;justify-content:space-between;gap:.5rem;flex-wrap:wrap;font-size:.875rem;color:var(--bridge-muted)}@media (max-width:768px){.menu-toggle{display:inline-block}.primary-nav{display:none;position:absolute;right:1rem;top:64px;background:#fff;border:1px solid var(--bridge-border);border-radius:.35rem;padding:.5rem;z-index:20}.primary-nav.is-open{display:block}.primary-nav .menu-list{display:block}.primary-nav li+li{margin-top:.5rem}}</style>';
}
add_action('wp_head', 'bridge_bootstrap_print_critical_css', 1);

/**
 * Enqueue non-critical assets only.
 */
function bridge_bootstrap_enqueue_assets(): void
{
    $theme = wp_get_theme();
    $version = (string) $theme->get('Version');

    wp_enqueue_style(
        'bridge-non-critical',
        get_template_directory_uri() . '/assets/css/non-critical.min.css',
        [],
        $version,
        'all'
    );

    wp_enqueue_script(
        'bridge-navigation',
        get_template_directory_uri() . '/assets/js/navigation.min.js',
        [],
        $version,
        ['in_footer' => true, 'strategy' => 'defer']
    );
}
add_action('wp_enqueue_scripts', 'bridge_bootstrap_enqueue_assets');

/**
 * Load non-critical CSS asynchronously.
 *
 * @param string $html Link tag html.
 * @param string $handle Registered handle.
 * @param string $href URL.
 * @param string $media Media attr.
 * @return string
 */
function bridge_bootstrap_async_css(string $html, string $handle, string $href, string $media): string
{
    if ('bridge-non-critical' !== $handle) {
        return $html;
    }

    $safe_href = esc_url($href);
    $safe_media = esc_attr($media ?: 'all');

    return "<link rel='preload' href='{$safe_href}' as='style' onload=\"this.onload=null;this.rel='stylesheet'\" media='{$safe_media}'>"
        . "<noscript><link rel='stylesheet' href='{$safe_href}' media='{$safe_media}'></noscript>";
}
add_filter('style_loader_tag', 'bridge_bootstrap_async_css', 10, 4);

/**
 * Add resource hints for assets.
 *
 * @param array<int, string|array<string, string>> $urls URLs.
 * @param string                                   $relation_type Type.
 * @return array<int, string|array<string, string>>
 */
function bridge_bootstrap_resource_hints(array $urls, string $relation_type): array
{
    if ('preload' === $relation_type) {
        $urls[] = [
            'href' => get_template_directory_uri() . '/assets/css/non-critical.min.css',
            'as'   => 'style',
        ];
    }

    return $urls;
}
add_filter('wp_resource_hints', 'bridge_bootstrap_resource_hints', 10, 2);

/**
 * Improve image loading behavior for better LCP/CLS.
 *
 * @param array<string, string|bool> $attr Attributes for image markup.
 * @return array<string, string|bool>
 */
function bridge_bootstrap_image_loading_optimization(array $attr): array
{
    if (empty($attr['decoding'])) {
        $attr['decoding'] = 'async';
    }

    if (empty($attr['loading'])) {
        $attr['loading'] = 'lazy';
    }

    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'bridge_bootstrap_image_loading_optimization');

/**
 * Preload likely LCP thumbnail on archive/home.
 */
function bridge_bootstrap_preload_lcp_image(): void
{
    if (! (is_home() || is_front_page())) {
        return;
    }

    $posts = get_posts([
        'numberposts'      => 1,
        'post_status'      => 'publish',
        'suppress_filters' => false,
    ]);

    if (empty($posts)) {
        return;
    }

    $thumb_id = get_post_thumbnail_id($posts[0]->ID);
    if (! $thumb_id) {
        return;
    }

    $image = wp_get_attachment_image_src($thumb_id, 'large');
    if (! is_array($image) || empty($image[0])) {
        return;
    }

    echo '<link rel="preload" as="image" fetchpriority="high" href="' . esc_url($image[0]) . '">';
}
add_action('wp_head', 'bridge_bootstrap_preload_lcp_image', 2);
