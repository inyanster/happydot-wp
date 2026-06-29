<?php
/**
 * Change Password Form Template
 *
 * @package FlexCore_Server
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Load WordPress text domain for translations
load_plugin_textdomain('flexcore-server', false, dirname(plugin_basename(__FILE__)) . '/languages/');
?>
<div class="profile-infoChange-wrapper">
			<div class="row">
				<div class="profile-infoChange-col">
					<div class="profile-infoChange-box">
						<h4>Change Password</h4>
						<a href="<?php echo home_url('/flexcore-change-password/'); ?>" class="hd-btn">New Password</a>
					</div>
				</div>
			</div>
</div>
