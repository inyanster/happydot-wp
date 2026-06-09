<div class="flexcore_notification-settings-wrapper hd-changes-settings">
    <div class="hd-changes-settings-block">
        <h3 class="flexcore_notification-title">CHANGE NOTIFICATION SETTINGS</h3>
        <p class="flexcore_notification-description hd-list-hedding">I wish to receive notifications by:</p>
     
        <form id="flexcore-notification-form" method="post" class="hd_notification-form">
            <input type="hidden" id="change_notification_settings_nonce" name="nonce" value="<?php echo wp_create_nonce('flexcore_notification_settings'); ?>">

            <div class="flexcore_notification-setting hd-changes-settings-list">
                <label class="hd-label"><strong>SMS Notification</strong> (Receives survey reminders & promotions via SMS.)</label>
                <div class="hd-satting-row">
                    <div class="hd-form-group radio-box">
                        <input class="hd-checkbox" type="radio" name="sms_notification" value="yes" id="notification_s_yes"  <?php echo $sms == true ? 'checked' : '';?>>
                        <label class="hd-label" for="notification_s_yes">Yes</label>
                    </div>
                    <div class="hd-form-group radio-box">
                        <input class="hd-checkbox" type="radio" name="sms_notification" value="no" id="notification_s_no" <?php echo $sms == false ? 'checked' : '';?>>
                        <label class="hd-label" for="notification_s_no">No</label>
                    </div>
                </div>
            </div>

            <div class="flexcore_notification-setting hd-changes-settings-list">
                <label class="hd-label"><strong>News and Update via Email</strong> (Receives the latest newsletters & campaign invites.)</label>
                <div class="hd-satting-row">
                    <div class="hd-form-group radio-box">
                        <input class="hd-checkbox" type="radio" name="email_notification" value="yes" id="notification_nu_yes" <?php echo $email == true ? 'checked' : '';?>>
                        <label class="hd-label" for="notification_nu_yes">Yes</label>
                    </div>
                    <div class="hd-form-group radio-box">
                        <input class="hd-checkbox" type="radio" name="email_notification" value="no" id="notification_nu_no" <?php echo $email == false ? 'checked' : '';?>>
                        <label class="hd-label" for="notification_nu_no" >No</label>
                    </div>
                </div>
            </div>

            <div class="flexcore_notification-actions hd-form-btn">
                <button type="submit" class="flexcore_save-changes-button hd-btn" >SAVE CHANGES</button>
            </div>

            <div id="notification-message" class="flexcore-message hd-ajax-response" style="display: none;"></div>
        </form>
    </div>
</div>
