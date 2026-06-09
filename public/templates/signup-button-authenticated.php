<?php
$profile_data = FlexCore_Server_Session::get_user_profile();
error_log('Profile Data: ' . print_r($profile_data, true)); // Debugging line
// $user_name = isset($profile_data['name']) ? $profile_data['name'] : 'WELCOME';
if (isset($profile_data['userData']['name'])) {
    $user_name = $profile_data['userData']['name'];
} elseif (isset($profile_data['name'])) {
    $user_name = $profile_data['name'];
} else {
    $user_name = 'WELCOME';
}
?>
<script>
    // console.log('Profile Data:', <?php echo json_encode($profile_data); ?>);
</script>
           
<style>
    .hd-btn2 {
    background-color: transparent;
    color: #D92632;
    border: none;
    cursor: pointer;
    padding: 0;
    font: inherit;
    font-weight: bold;
}
 .hd-btn2:hover {
    color:rgb(211, 9, 23);
    
    background-color: transparent;
 }

</style>
<div class="menu-container" style="position: relative; display: inline-block;">
    <button class="menu-item menu-item-type-custom hd-btn" style="cursor: pointer;" id="showListButton">
        <?php echo esc_html($user_name); ?>
    </button>
    <ul class="hd_sub_menu">
        <li><a href="<?php echo esc_url(home_url('/my-profile/')); ?>">My Profile</a></li>
        <li><a href="<?php echo esc_url(home_url('/my-account/')); ?>">My Account</a></li>
        <li><a href="<?php echo esc_url(home_url('/point-history/')); ?>">Point History</a></li>
        <li> <button class="flexcore-logout hd-btn2" >
            <?php esc_html_e('Logout', 'flexcore-server'); ?>
        </button></li>
    </ul>
</div>
<div id="logout-message" class="  vflexcore-message" style="display: none;"></div>
