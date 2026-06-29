<?php
/**
 * Delete account form template
 *
 * @package FlexCore_Server
 */

if (!defined('ABSPATH')) {
    exit;
}
?>


    <form id="flexcore-delete-account-form">
        <?php wp_nonce_field('flexcore_delete_account', 'flexcore_nonce'); ?>
        
        <div class="form-submit">
            <button type="submit" class="button hd-btn"><?php esc_html_e('Delete My Account', 'flexcore-server'); ?></button>
        </div>
        
        <div id="delete-account-message" class="flexcore-message" style="display: none;"></div>
    </form>
</div>
