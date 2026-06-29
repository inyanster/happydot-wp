<?php

/**
 * Dashboard template
 *
 * @package FlexCore_Server
 * @var array $profile User profile data
 */

if (!defined('ABSPATH')) {
    exit;
}

$profile_data = $profile['data'] ?? array();

?>
<?php
$profile_data = FlexCore_Server_Session::get_user_profile();
// $avatar_id=$avatar_id = isset($profile_data['avatarId']) ? $profile_data['avatarId'] : '23400';
if (isset($profile_data['userData']['metaData']['avatarId'])) {
    
    $avatar_id = $profile_data['userData']['metaData']['avatarId'];
} elseif (isset($profile_data['metaData']['avatarId'])) {
    error_log('Avatar ID found in metaData' );
   
  
    $avatar_id = $profile_data['metaData']['avatarId'];
} else {
    
    $avatar_id = '23400';
}
$avatar_src = site_url("/wp-content/uploads/avatar/{$avatar_id}.png");
?>
<style>
    img{
    display: block;
}
</style>
<div class="rewards-section"> <!-- changed from flexcore-dashboard -->

    <div class="profile-title-wrapper">
        <div class="rewards-balance-box">
            <div class="profile-balance-img">
                <img src="<?php echo $avatar_src ?>" alt="profile" />
            </div>
            <div class="rewards-balance-ctn">
                <h3><span><?php esc_html_e('Welcome', 'flexcore-server'); ?></span> <?php echo esc_html($profile_data['name'] ?? ''); ?></h3>
                <h5><?php esc_html_e('Your Dashboard', 'flexcore-server'); ?></h5>
            </div>
        </div>
    </div>

    <div class="dashboard-navigation hd-btn-grp">
        <a href="<?php echo esc_url(get_permalink(get_option('flexcore_profile_page'))); ?>" class="hd-btn">
            <?php esc_html_e('Edit Profile', 'flexcore-server'); ?>
        </a>
        <a href="<?php echo esc_url(get_permalink(get_option('flexcore_change_password_page'))); ?>" class="hd-btn">
            <?php esc_html_e('Change Password', 'flexcore-server'); ?>
        </a>
        <button class="flexcore-logout" class="hd-btn">
            <?php esc_html_e('Logout', 'flexcore-server'); ?>
        </button>
        <a href="<?php echo esc_url(site_url('contact-us')); ?>" class="hd-btn button-danger">
            <?php esc_html_e('Delete Account', 'flexcore-server'); ?>
        </a>
    </div>

    <div class="dashboard-content">
        <div class="profile-summary">
            <h3><?php esc_html_e('Profile Information', 'flexcore-server'); ?></h3>
            <table class="profile-details">
                <tr>
                    <th><?php esc_html_e('Name:', 'flexcore-server'); ?></th>
                    <td><?php echo esc_html($profile_data['name'] ?? ''); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Email:', 'flexcore-server'); ?></th>
                    <td><?php echo esc_html($profile_data['email'] ?? ''); ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div id="dashboard-message" class="flexcore-message" style="display: none;"></div>

</div>
