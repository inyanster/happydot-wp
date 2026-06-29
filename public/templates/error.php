<?php
/**
 * Error message template
 *
 * @package FlexCore_Server
 * @var string $message Error message to display
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="flexcore-message error">
    <?php echo esc_html($message); ?>
</div>
