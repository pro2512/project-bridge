<?php
/**
 * Footer template.
 *
 * @package BridgeBootstrapMinimal
 */
?>
<footer class="site-footer" role="contentinfo">
    <div class="container footer-inner">
        <span>&copy; <?php echo esc_html(date_i18n('Y')); ?> <?php bloginfo('name'); ?></span>
        <span><?php bloginfo('description'); ?></span>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
