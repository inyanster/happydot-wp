<?php
/**
 * Already logged in template
 *
 * @package FlexCore_Server
 * @var string $dashboard_url URL to the dashboard page
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="flexcore-message info">
    <?php esc_html_e('You are already logged in.', 'flexcore-server'); ?>
    <a href="<?php echo esc_url($dashboard_url); ?>">
        <?php esc_html_e('Go to Dashboard', 'flexcore-server'); ?>
    </a>
</div>
