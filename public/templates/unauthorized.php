<?php
/**
 * Unauthorized access template
 *
 * @package FlexCore_Server
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="flexcore-message error">
    <?php esc_html_e('Please login to continue.', 'flexcore-server'); ?>
    <a href="<?php echo esc_url(get_permalink(get_option('flexcore_login_page'))); ?>">
        <?php esc_html_e('Click here to login', 'flexcore-server'); ?>
    </a>
</div>
