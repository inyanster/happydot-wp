<!-- Current Avatar Display -->
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
<style>img{
    display: block !important;
} 
/* Prevent background from scrolling when modal is open */
body.modal-open {
    overflow: hidden;
}

/* Make sure the modal content scrolls if it's too tall */
#avatar-modal .hd-modal-inner {
    max-height: 100vh; /* You can adjust the height based on your needs */
    overflow-y: auto;
}

/* Optional: Add some padding to prevent text from touching edges */
#avatar-modal .hd-modal-ctn {
    padding: 20px;
}

</style>
<div id="current-avatar-box" class="hd-account-avtar">
  <div class="hd-account-avtar-block">
    <h3 class="hd-heading">Profile Avatar</h3>
    <div class="hd-avtar">
       <img id="current-avatar" src="<?php echo esc_url($avatar_src); ?>" width="100" alt="avatar" />
    </div>
    <div id="avatar-update-message" style="display:none;"></div>
    <div class="hd-form-btn">
      <button id="edit-avatar-btn" class="hd-btn " type="button">Edit</button>
    </div>
  </div>
</div>

<!-- Avatar Selection Modal (Inside a Form) -->
<div id="avatar-modal" class="hd-modal profile-img-edit-modal" style="display:none;">
  <div class="hd-modal-inner">
    <div class="hd-modal-ctn">

      <!-- Modal Header -->
      <div class="hd-modal-head">
        <h3>Choose Your Avatar</h3>
        <a href="javascript:void(0);" id="close-avatar-modal" class="close-btn">
          <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M7.21543 19.0518L5.81543 17.6518L11.4154 12.0518L5.81543 6.45176L7.21543 5.05176L12.8154 10.6518L18.4154 5.05176L19.8154 6.45176L14.2154 12.0518L19.8154 17.6518L18.4154 19.0518L12.8154 13.4518L7.21543 19.0518Z" fill="#1C1B1F" />
          </svg>
        </a>
      </div>

      <!-- Modal Body -->
      <div class="hd-modal-body">
        <form id="avatar-form">
          <?php wp_nonce_field('flexcore_change_avatar', 'change_avatar_nonce'); ?>
          <input type="hidden" id="avatarId" name="avatarId" value="" />

          <div class="hd-modal-image-list">
            <ul class="avatar-grid">
              <li>
                <div class="hd-modal-img hd_avatar_image_pic">
                  <img src="<?php echo site_url('/wp-content/uploads/avatar/23400.png')?>" data-id="23400" class="avatar-option" />
                </div>
              </li>
              <li>
                <div class="hd-modal-img hd_avatar_image_pic">
                  <img src="<?php echo site_url('/wp-content/uploads/avatar/23401.png')?>" data-id="23401" class="avatar-option" />
                </div>
              </li>
              <li>
                <div class="hd-modal-img hd_avatar_image_pic">
                  <img src="<?php echo site_url('/wp-content/uploads/avatar/23402.png')?>" data-id="23402" class="avatar-option" />
                </div>
              </li>
              <li>
                <div class="hd-modal-img hd_avatar_image_pic">
                  <img src="<?php echo site_url('/wp-content/uploads/avatar/23403.png')?>" data-id="23403" class="avatar-option" />
                </div>
              </li>
              <li>
                <div class="hd-modal-img hd_avatar_image_pic">
                  <img src="<?php echo site_url('/wp-content/uploads/avatar/23404.png')?>" data-id="23404" class="avatar-option" />
                </div>
              </li>
              <!-- More avatars can be added here -->
            </ul>
          </div>

          <!-- Save Changes Button -->
          <div class="hd-form-btn">
            <button type="submit" class="hd-btn">Save Changes</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
